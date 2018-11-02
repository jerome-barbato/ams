<?php
namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends ApiController
{
	/**
	 * @Route("/events/{page}", methods={"GET"}, requirements={"page"="\d+"})
	 */
	public function list($page=0, EventRepository $eventRepository)
	{
		$events = $eventRepository->findBy([], ['begin'=>'ASC'], getenv('LIMIT'), $page);
		$eventsArray = [];

		foreach ($events as $event) {
			$eventsArray[] = $eventRepository->transform($event);
		}

		return $this->respond($eventsArray);
	}


	/**
	 * @Route("/events/{id}", methods={"GET"})
	 */
	public function show($id, EventRepository $eventRepository)
	{
		$event = $eventRepository->findOneBy(['uuid'=>$id]);

		if (! $event)
			return $this->respondNotFound();

		$event = $eventRepository->transform($event, true);

		return $this->respond($event);
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