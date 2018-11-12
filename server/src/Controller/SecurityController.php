<?php

namespace App\Controller;

use App\Entity\AuthToken;
use App\Entity\Militant;
use App\Repository\AuthTokenRepository;
use App\Repository\MilitantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Security;

class SecurityController extends ApiController
{
	/**
	 * @Route("/login", methods={"POST"})
	 */
	public function login(Request $request, MilitantRepository $militantRepository, EntityManagerInterface $em)
	{
		if( !$email = $request->get('email') )
			return $this->respondWithErrors('Email is required');

		if( !$password = $request->get('password') )
			return $this->respondWithErrors('Password is required');

		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
			return $this->respondValidationError('This email is not valid.');

		$militant = $militantRepository->findOneBy(['email'=>$email]);

		if( !$militant )
			return $this->respondUnauthorized('Invalid email or password');

		$encoder = $this->container->get('security.password_encoder');
		$isPasswordValid = $encoder->isPasswordValid($militant, $password);

		if( !$isPasswordValid )
			return $this->respondUnauthorized('Invalid email or password');

		$authToken = new AuthToken();
		$authToken->setMilitant($militant);

		$em->persist($authToken);
		$em->flush();

		return $this->respond([
			'auth_token' => $authToken->getValue(),
			'militant' => $militantRepository->transform($militant)
		]);
	}

	/**
	 * @Route("/logout", methods={"GET"})
	 * @IsGranted("ROLE_USER")
	 */
	public function logout(AuthTokenRepository $authTokenRepository, EntityManagerInterface $em, Security $security)
	{
		/* @var $militant Militant */
		$militant = $security->getUser();

		$authTokens = $authTokenRepository->findBy(['militant'=>$militant]);

		foreach ($authTokens as $authToken)
			$em->remove($authToken);

		$em->flush();

		return $this->respondGone();
	}
}