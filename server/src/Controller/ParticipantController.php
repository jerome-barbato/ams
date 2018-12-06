<?php
namespace App\Controller;

use App\Entity\Participant;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends ApiController
{
	/**
	 * @Route("/participants/{event_id}/{page}", methods={"GET"}, requirements={"page"="\d+"})
	 */
	public function show($event_id, $page=0, ParticipantRepository $participantRepository, UserRepository $userRepository, EventRepository $eventRepository)
	{
		$event = $eventRepository->findOneBy(['uuid'=>$event_id]);
		$participants = $participantRepository->findBy(['event'=>$event], ['inscription'=>'ASC'], getenv('LIMIT'), $page);

		$participantsArray = [];

		foreach ($participants as $participant){

			$participantsArray[] = [
				'user' => $userRepository->transform($participant->getUser()),
				'role'  => $participant->getRole(),
				'inscription' => $participant->getInscription()->format(getenv('DATE_FORMAT'))
			];
		}

		return $this->respond($participantsArray);
	}

	/**
	 * @Route("/participant/{event_id}/{user_id}", methods={"POST"}))
	 */
	public function add($event_id, $user_id, Request $request, UserRepository $userRepository, EventRepository $eventRepository, EntityManagerInterface $em)
	{
		$role = $request->get('role', 'participant');

		if(!$user_id)
			return $this->respondValidationError('Please provide a user id');

		$user = $userRepository->findOneBy(['uuid'=>$user_id]);
		if(!$user)
			return $this->respondValidationError('The user does not exist');

		if(!$event_id)
			return $this->respondValidationError('Please provide a event id');

		$event = $eventRepository->findOneBy(['uuid'=>$event_id]);
		if(!$event)
			return $this->respondValidationError('The event does not exist');

		$participant = new Participant();
		$participant->setUser($user);
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
	 * @Route("/participant/{event_id}/{user_id}", methods={"DELETE"}))
	 */
	public function delete($event_id, $user_id, UserRepository $userRepository, EventRepository $eventRepository, ParticipantRepository $participantRepository, EntityManagerInterface $em)
	{
		if(!$event_id)
			return $this->respondValidationError('Please provide an event id');

		if(!$event = $eventRepository->findOneBy(['uuid'=>$event_id]))
			return $this->respondNotFound('Please provide a valid event id');

		if(!$user_id)
			return $this->respondValidationError('Please provide a user id');

		if(!$user = $userRepository->findOneBy(['uuid'=>$user_id]))
			return $this->respondNotFound('Please provide a valid user id');

		$participations = $participantRepository->findBy(['user'=>$user, 'event'=>$event]);

		if(!$participations || !count($participations))
			return $this->respondNotFound();

		foreach ($participations as $participation)
			$em->remove($participation);

		$em->flush();

		return $this->respondGone();
	}
}