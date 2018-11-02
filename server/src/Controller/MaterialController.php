<?php
namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\MaterialRepository;
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
	 * @Route("/materials/{id}", methods={"GET"})
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
	 * @Route("/events", methods={"POST"})
	 */
	public function create(Request $request, EventRepository $eventRepository, EntityManagerInterface $em)
	{
		$request = $this->transformJsonBody($request);

		if (!$request)
			return $this->respondValidationError('Please provide a valid request!');

		// validate the fields
		$fields = ['first_name','last_name','email','address','postal_code','city','country'];
		foreach ($fields as $field){
			if (!$request->get($field)) {
				return $this->respondValidationError('Please provide a '.str_replace('_', ' ', $field).'!');
			}
		}

		// persist the new event
		try{
			$event = new Event();
			$event->setUuid(uniqid());
			$event->setFirstName($request->get('first_name'));
			$event->setLastName($request->get('last_name'));
			$event->setEmail($request->get('email'));
			$event->setAddress($request->get('address'));
			$event->setPostalCode($request->get('postal_code'));
			$event->setCity($request->get('city'));
			$event->setCountry($request->get('country'));
			$event->setLat(0);
			$event->setLng(0);

			$em->persist($event);
			$em->flush();
		}
		catch(\Exception $e){
			return $this->respondWithErrors( $e->getMessage() );
		}

		return $this->respondCreated($eventRepository->transform($event));
	}
}