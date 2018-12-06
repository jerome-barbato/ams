<?php
namespace App\Controller;

use App\Entity\Event;
use App\Entity\Place;
use App\Repository\EventRepository;
use App\Repository\GroupRepository;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class EventController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class EventController extends ApiController
{
	/**
	 * @Route("/events/{page}", methods={"GET"}, requirements={"page"="\d+"})
	 */
	public function list(EventRepository $eventRepository, $page=0)
	{
		$events = $eventRepository->findBy([], ['begin'=>'ASC'], getenv('LIMIT'), $page);
		$eventsArray = [];

		foreach ($events as $event) {
			$eventsArray[] = $eventRepository->transform($event);
		}

		return $this->respond($eventsArray);
	}


	/**
	 * @Route("/event/{id}", methods={"GET"})
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
	 * @Route("/event", methods={"POST"})
	 * @throws \Exception
	 */
	public function create(Request $request, PlaceRepository $placeRepository, EventRepository $eventRepository, GroupRepository $groupRepository, EntityManagerInterface $em)
	{
		// validate the fields
		$fields = ['title','address','postal_code','city','country'];
		foreach ($fields as $field){
			if (!$request->get($field)) {
				return $this->respondValidationError('Please provide a '.str_replace('_', ' ', $field).'!');
			}
		}

		try{
			// persist the new event
			$event = new Event();

			$event->setUuid(uniqid())
				->setTitle($request->get('title'))
				->setDescription($request->get('description'))
				->setType($request->get('type'));

			if($request->get('begin')){

				$begin = \DateTime::createFromFormat(getenv('DATETIME_FORMAT'), $request->get('begin'));
				if(!$begin)
					return $this->respondValidationError('Invalid date format, require '.getenv('DATETIME_FORMAT'));

				$event->setBegin($begin);
			}

			if($request->get('end')){

				$end = \DateTime::createFromFormat(getenv('DATETIME_FORMAT'), $request->get('end'));
				if(!$end)
					return $this->respondValidationError('Invalid date format, require '.getenv('DATETIME_FORMAT'));

				$event->setEnd($end);
			}

			$group = false;

			if($group_id = $request->get('group_id')){

				if(!$group = $groupRepository->findOneBy(['uuid'=>$group_id]))
					return $this->respondValidationError('Please provide a valid group id');
			}

			if($group)
				$event->addGroup($group);

			$place = new Place();

			$place->setAddress($request->get('address'))
				->setPostalCode($request->get('postal_code'))
				->setCity($request->get('city'))
				->setCountry($request->get('country'));

			$place->geocode();

			if(!$place->hasError() && $existingPlace= $placeRepository->findOneBy(['gid'=>$place->getGid()])){

				$event->setPlace($existingPlace);
			}
			else{

				$event->setPlace($place);
				$em->persist($place);
			}

			$em->persist($event);
			$em->flush();

			$data = $eventRepository->transform($event);

			if( $place->hasError() )
				$data['error'] = $place->getError();

			return $this->respondCreated($data);
		}
		catch(\Exception $e){

			return $this->respondWithErrors($e->getMessage());
		}
	}


	/**
	 * @Route("/event/{id}", methods={"DELETE"})
	 */
	public function delete($id, EventRepository $eventRepository, EntityManagerInterface $em)
	{
		$event = $eventRepository->findOneBy(['uuid'=>$id]);

		if (!$event)
			return $this->respondNotFound();

		$em->remove($event);
		$em->flush();

		return $this->respondGone();
	}

}