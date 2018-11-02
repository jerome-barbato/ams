<?php
namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\GroupRepository;
use App\Repository\MemberRepository;
use App\Repository\ParticipantRepository;
use App\Repository\MilitantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends ApiController
{
	/**
	 * @Route("/members/{group_id}/{page}", methods={"GET"}, requirements={"page"="\d+"}))
	 */
	public function list($group_id, $page=0, MemberRepository $memberRepository, MilitantRepository $militantRepository, GroupRepository $groupRepository)
	{
		$group = $groupRepository->findOneBy(['uuid'=>$group_id]);
		$members = $memberRepository->findBy(['group'=>$group->getId()], ['inscription'=>'ASC'], getenv('LIMIT'), $page);

		$membersArray = [];

		foreach ($members as $member){

			$membersArray[] = [
				'militant' => $militantRepository->transform($member->getMilitant()),
				'since' => $member->getInscription()->format(getenv('DATE_FORMAT')),
				'role' => $member->getRole()
			];
		}

		return $this->respond($membersArray);
	}
}