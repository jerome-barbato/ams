<?php
namespace App\Controller;

use App\Entity\Event;
use App\Entity\Material;
use App\Entity\Place;
use App\Repository\EventRepository;
use App\Repository\MaterialRepository;
use App\Repository\UserRepository;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class MaterialController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class MaterialController extends ApiController
{
	/**
	 * @Route("/materials/{page}", methods={"GET"}, requirements={"page"="\d+"})
	 */
	public function list(MaterialRepository $materialRepository, $page=0)
	{
		$material = $materialRepository->findBy([], ['name'=>'ASC'], getenv('LIMIT'), $page);
		$materialArray = [];

		foreach ($material as $_material) {
			$materialArray[] = $materialRepository->transform($_material);
		}

		return $this->respond($materialArray);
	}


	/**
	 * @Route("/material/{id}", methods={"GET"})
	 */
	public function show($id, MaterialRepository $materialRepository)
	{
		$material = $materialRepository->findOneBy(['uuid'=>$id]);

		if (!$material)
			return $this->respondNotFound();

		$material = $materialRepository->transform($material, true);

		return $this->respond($material);
	}


	/**
	 * @Route("/material/{id}", methods={"DELETE"})
	 */
	public function delete($id, MaterialRepository $materialRepository, EntityManagerInterface $em)
	{
		$material = $materialRepository->findOneBy(['uuid'=>$id]);

		if (!$material)
			return $this->respondNotFound();

		$em->remove($material);
		$em->flush();

		return $this->respondGone();
	}


	/**
	 * @Route("/material", methods={"POST"})
	 */
	public function create(Request $request, MaterialRepository $materialRepository, PlaceRepository $placeRepository, UserRepository $userRepository, EntityManagerInterface $em)
	{
		// validate the fields
		$fields = ['quantity','name'];
		foreach ($fields as $field){
			if (!$request->get($field)) {
				return $this->respondValidationError('Please provide a '.str_replace('_', ' ', $field).'!');
			}
		}

		// persist the new material
		try{
			$material = new Material();
			$material->setName($request->get('name'))
				->setDescription($request->get('description'))
				->setType($request->get('type'))
				->setImage($request->get('image'))
				->setLocation($request->get('location'))
				->setQuantity($request->get('quantity'))
				->setSize($request->get('size'))
				->setTheme($request->get('theme'));

			if($user_id = $request->get('user_id')){

				$user = $userRepository->findOneBy(['uuid'=>$user_id]);
				if($user)
					$material->addOwner($user);
			}


			if($request->get('address') && $request->get('postal_code') && $request->get('city')){

				$place = new Place();
				$place->setAddress($request->get('address'))
					->setPostalCode($request->get('postal_code'))
					->setCity($request->get('city'))
					->setCountry($request->get('country'))
					->geocode();

				if(!$place->hasError() && $existingPlace = $placeRepository->findOneBy(['gid'=>$place->getGid()])){

					$material->setPlace($existingPlace);
				}
				else{

					$material->setPlace($place);
					$em->persist($place);
				}
			}

			$em->persist($material);
			$em->flush();
		}
		catch(\Exception $e){
			return $this->respondWithErrors( $e->getMessage() );
		}

		return $this->respondCreated($materialRepository->transform($material));
	}
}