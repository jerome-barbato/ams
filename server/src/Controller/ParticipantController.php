<?php
namespace App\Controller;

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
		$participants = $participantRepository->findBy(['event'=>$event->getId()], ['inscription'=>'ASC'], getenv('LIMIT'), $page);

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
}