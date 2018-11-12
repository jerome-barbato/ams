<?php
namespace App\Controller;

use App\Entity\Participant;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use App\Repository\MilitantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends ApiController
{
	/**
	 * @Route("/participants/{event_id}/{page}", methods={"GET"}, requirements={"page"="\d+"})
	 */
	public function show($event_id, $page=0, ParticipantRepository $participantRepository, MilitantRepository $militantRepository, EventRepository $eventRepository)
	{
		$event = $eventRepository->findOneBy(['uuid'=>$event_id]);
		$participants = $participantRepository->findBy(['event'=>$event], ['inscription'=>'ASC'], getenv('LIMIT'), $page);

		$participantsArray = [];

		foreach ($participants as $participant){

			$participantsArray[] = [
				'militant' => $militantRepository->transform($participant->getMilitant()),
				'role'  => $participant->getRole(),
				'inscription' => $participant->getInscription()->format(getenv('DATE_FORMAT'))
			];
		}

		return $this->respond($participantsArray);
	}

	/**
	 * @Route("/participant/{event_id}", methods={"POST"}))
	 */
	public function add($event_id, Request $request, MilitantRepository $militantRepository, EventRepository $eventRepository, EntityManagerInterface $em)
	{
		$role = $request->get('role', 'participant');
		$militant_id = $request->get('militant_id');

		if(!$militant_id)
			return $this->respondValidationError('Please provide a militant id');

		$militant = $militantRepository->findOneBy(['uuid'=>$militant_id]);
		if(!$militant)
			return $this->respondValidationError('The militant does not exist');

		if(!$event_id)
			return $this->respondValidationError('Please provide a event id');

		$event = $eventRepository->findOneBy(['uuid'=>$event_id]);
		if(!$event)
			return $this->respondValidationError('The event does not exist');

		$participant = new Participant();
		$participant->setMilitant($militant);
		$participant->setEvent($event);
		$participant->setRole($role);

		try{

			$em->persist($participant);
			$em->flush();

			return $this->respondCreated();
		}
		catch(\Exception $e){
			return $this->respondWithErrors( $e->getMessage() );
		}
	}
	

	/**
	 * @Route("/participant/{event_id}/{militant_id}", methods={"DELETE"}))
	 */
	public function delete($event_id, $militant_id, MilitantRepository $militantRepository, EventRepository $eventRepository, ParticipantRepository $participantRepository, EntityManagerInterface $em)
	{
		if(!$event_id)
			return $this->respondValidationError('Please provide an event id');

		if(!$event = $eventRepository->findOneBy(['uuid'=>$event_id]))
			return $this->respondNotFound('Please provide a valid event id');

		if(!$militant_id)
			return $this->respondValidationError('Please provide a militant id');

		if(!$militant = $militantRepository->findOneBy(['uuid'=>$militant_id]))
			return $this->respondNotFound('Please provide a valid militant id');

		$participations = $participantRepository->findBy(['militant'=>$militant, 'event'=>$event]);

		if(!$participations || !count($participations))
			return $this->respondNotFound();

		foreach ($participations as $participation)
			$em->remove($participation);

		$em->flush();

		return $this->respondGone();
	}
}