<?php
namespace App\Controller;

use App\Entity\Event;
use App\Entity\Material;
use App\Entity\Place;
use App\Repository\EventRepository;
use App\Repository\MaterialRepository;
use App\Repository\MilitantRepository;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MaterialController extends ApiController
{
	/**
	 * @Route("/materials/{page}", methods={"GET"}, requirements={"page"="\d+"})
	 */
	public function list($page=0, MaterialRepository $materialRepository)
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
	public function create(Request $request, MaterialRepository $materialRepository, PlaceRepository $placeRepository, MilitantRepository $militantRepository, EntityManagerInterface $em)
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
			$material->setName($request->get('name'));
			$material->setDescription($request->get('description'));
			$material->setType($request->get('type'));
			$material->setImage($request->get('image'));
			$material->setLocation($request->get('location'));
			$material->setQuantity($request->get('quantity'));
			$material->setSize($request->get('size'));
			$material->setTheme($request->get('theme'));

			if($militant_id = $request->get('militant_id')){

				$militant = $militantRepository->findOneBy(['uuid'=>$militant_id]);
				if($militant)
					$material->addOwner($militant);
			}


			if($request->get('address') && $request->get('postal_code') && $request->get('city')){

				$place = new Place();
				$place->setTitle($request->get('place_title', 'Local'));
				$place->setAddress($request->get('address'));
				$place->setPostalCode($request->get('postal_code'));
				$place->setCity($request->get('city'));
				$place->setCountry($request->get('country'));
				$place->geocode();

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