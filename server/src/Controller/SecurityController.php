<?php

namespace App\Controller;

use App\Entity\AuthToken;
use App\Entity\User;
use App\Repository\AuthTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Security;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends ApiController
{
	/**
	 * @Route("/login", methods={"POST"})
	 */
	public function login(Request $request, UserRepository $userRepository, AuthTokenRepository $authTokenRepository, EntityManagerInterface $em)
	{
		if( !$email = $request->get('email') )
			return $this->respondWithErrors('Email is required');

		if( !$password = $request->get('password') )
			return $this->respondWithErrors('Password is required');

		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
			return $this->respondValidationError('This email is not valid.');

		$user = $userRepository->findOneBy(['email'=>$email]);

		if( !$user )
			return $this->respondUnauthorized('Invalid email or password');

		$encoder = $this->container->get('security.password_encoder');
		$isPasswordValid = $encoder->isPasswordValid($user, $password);

		if( !$isPasswordValid )
			return $this->respondUnauthorized('Invalid email or password');

		$client_ip_hash = AuthToken::anonymizeIp($request->getClientIp());

		// get previous token if it exists
		$authToken = $authTokenRepository->findOneBy(['user'=>$user, 'ip_hash'=>$client_ip_hash], ['createdAt'=>'DESC']);

		//todo: check expiration
		if( !$authToken ){

			$authToken = new AuthToken();
			$authToken->setUser($user)
				->setIpHash($client_ip_hash);

			$em->persist($authToken);
			$em->flush();
		}

		return $this->respond([
			'bearer_token' => $authToken->getValue(),
			'user' => $userRepository->transform($user)
		]);
	}
}