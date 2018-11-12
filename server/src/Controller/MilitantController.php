<?php
namespace App\Controller;

use App\Entity\Militant;
use App\Entity\Place;
use App\Repository\MilitantRepository;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class MilitantController extends ApiController
{
	//IsGranted("ROLE_ADMIN")
	/**
	 * @Route("/militants/{page}", methods={"GET"}, requirements={"page"="\d+"})
	 */
	public function list($page=0, MilitantRepository $militantRepository)
	{
		$militants      = $militantRepository->findBy([], ['inscription'=>'ASC'], getenv('LIMIT'), $page);
		$militantsArray = [];

		foreach ($militants as $militant) {
			$militantsArray[] = $militantRepository->transform($militant);
		}

		return $this->respond($militantsArray);
	}


	/**
	 * @Route("/militant/{id}", methods={"GET"})
	 */
	public function show($id, MilitantRepository $militantRepository)
	{
		$militant = $militantRepository->findOneBy(['uuid'=>$id]);

		if (!$militant)
			return $this->respondNotFound();

		$militant = $militantRepository->transform($militant, true);

		return $this->respond($militant);
	}


	/**
	 * @Route("/militant", methods={"POST"})
	 */
	public function create(Request $request, MilitantRepository $militantRepository, PlaceRepository $placeRepository, EntityManagerInterface $em)
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

		$militant = $militantRepository->findOneBy(['email'=>$request->get('email')]);

		if( $militant )
			return $this->respondValidationError('This email is already used.');

		try{
			// persist the new militant
			$militant = new Militant();

			$militant->setFirstName($request->get('first_name'));
			$militant->setLastName($request->get('last_name'));
			$militant->setEmail($request->get('email'));

			$password = $request->get('password', base64_encode(random_bytes(8)));
			$encoder = $this->get('security.password_encoder');
			$encoded = $encoder->encodePassword($militant, $password);
			$militant->setPassword($encoded);

			if($request->get('is_admin'))
				$militant->setRoles(['ROLE_ADMIN']);

			$place = new Place();
			$place->setTitle($request->get('place_title', 'Maison'));
			$place->setAddress($request->get('address'));
			$place->setPostalCode($request->get('postal_code'));
			$place->setCity($request->get('city'));
			$place->setCountry($request->get('country'));
			$place->geocode();

			if(!$place->hasError() && $existingPlace = $placeRepository->findOneBy(['gid'=>$place->getGid()])){

				$militant->setPlace($existingPlace);
			}
			else{

				$militant->setPlace($place);
				$em->persist($place);
			}

			$em->persist($militant);
			$em->flush();

			$data = $militantRepository->transform($militant);

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
	 * @Route("/militant/{id}", methods={"DELETE"})
	 */
	public function delete($id, MilitantRepository $militantRepository, EntityManagerInterface $em)
	{
		$militant = $militantRepository->findOneBy(['uuid'=>$id]);

		if (!$militant)
			return $this->respondNotFound();

		$em->remove($militant);
		$em->flush();

		return $this->respondGone();
	}
}