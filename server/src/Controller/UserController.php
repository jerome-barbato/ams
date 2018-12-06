<?php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Place;
use App\Repository\UserRepository;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends ApiController
{
	/**
	 * @Route("/users/{page}", methods={"GET"}, requirements={"page"="\d+"})
	 * @IsGranted("ROLE_USER")
	 */
	public function list( UserRepository $userRepository)
	{
		$users      = $userRepository->findBy([], ['inscription'=>'ASC'], getenv('LIMIT'), $page);
		$usersArray = [];

		foreach ($users as $user) {
			$usersArray[] = $userRepository->transform($user);
		}

		return $this->respond($usersArray);
	}


	/**
	 * @Route("/user/{id}", methods={"GET"})
	 * @IsGranted("ROLE_USER")
	 */
	public function show($id, UserRepository $userRepository)
	{
		$user = $userRepository->findOneBy(['uuid'=>$id]);

		if (!$user)
			return $this->respondNotFound();

		$user = $userRepository->transform($user, true);

		return $this->respond($user);
	}


	/**
	 * @Route("/user", methods={"POST"})
	 */
	public function create(Request $request, UserRepository $userRepository, PlaceRepository $placeRepository, EntityManagerInterface $em)
	{
		// validate the fields
		$fields = ['first_name','last_name','email','address','postal_code','city','country'];
		foreach ($fields as $field){
			if (!$request->get($field)) {
				return $this->respondValidationError('Please provide a '.str_replace('_', ' ', $field).'!');
			}
		}

		if(!filter_var($request->get('email'), FILTER_VALIDATE_EMAIL))
			return $this->respondValidationError('This email is not valid.');

		$user = $userRepository->findOneBy(['email'=>$request->get('email')]);

		if( $user )
			return $this->respondValidationError('This email is already used.');

		try{
			// persist the new user
			$user = new User();

			$user->setFirstName($request->get('first_name'))
				->setLastName($request->get('last_name'))
				->setEmail($request->get('email'));

			$password = $request->get('password', base64_encode(random_bytes(8)));
			$encoder = $this->get('security.password_encoder');
			$encoded = $encoder->encodePassword($user, $password);

			$user->setPassword($encoded);

			if($request->get('is_admin'))
				$user->setRoles(['ROLE_ADMIN']);

			$place = new Place();
			$place->setAddress($request->get('address'))
				->setPostalCode($request->get('postal_code'))
				->setCity($request->get('city'))
				->setCountry($request->get('country'))
				->geocode();

			if(!$place->hasError() && $existingPlace = $placeRepository->findOneBy(['gid'=>$place->getGid()])){

				$user->setPlace($existingPlace);
			}
			else{

				$user->setPlace($place);
				$em->persist($place);
			}

			$em->persist($user);
			$em->flush();

			$data = $userRepository->transform($user);

			if( !$request->get('password') )
				$data['password'] = $password;

			if( $place->hasError() )
				$data['error'] = $place->getError();

			return $this->respondCreated($data);
		}
		catch(\Exception $e){
			return $this->respondWithErrors( $e->getMessage() );
		}
	}


	/**
	 * @Route("/user/{id}", methods={"DELETE"})
	 * @IsGranted("ROLE_USER")
	 */
	public function delete($id, UserRepository $userRepository, EntityManagerInterface $em, UserInterface $user = null)
	{
		$targeted_user = $userRepository->findOneBy(['uuid'=>$id]);

		if( !in_array('ROLE_ADMIN', $user->getRoles()) && $user->getUsername() !== $targeted_user->getUsername() )
			return $this->respondUnauthorized();

		if (!$targeted_user)
			return $this->respondNotFound();

		$em->remove($user);
		$em->flush();

		return $this->respondGone();
	}
}