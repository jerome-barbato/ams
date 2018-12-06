<?php
namespace App\Controller;

use App\Entity\Member;
use App\Repository\EventRepository;
use App\Repository\GroupRepository;
use App\Repository\MemberRepository;
use App\Repository\ParticipantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends ApiController
{
	/**
	 * @Route("/members/{group_id}/{page}", methods={"GET"}, requirements={"page"="\d+"}))
	 */
	public function list($group_id, $page=0, MemberRepository $memberRepository, UserRepository $userRepository, GroupRepository $groupRepository)
	{
		if(!$group_id)
			return $this->respondValidationError('Please provide a group id');

		$group = $groupRepository->findOneBy(['uuid'=>$group_id]);
		if(!$group)
			return $this->respondValidationError('The group does not exist');

		$members = $memberRepository->findBy(['group'=>$group->getId()], ['inscription'=>'ASC'], getenv('LIMIT'), $page);

		$membersArray = [];

		foreach ($members as $member){

			$membersArray[] = [
				'user' => $userRepository->transform($member->getUser()),
				'since' => $member->getInscription()->format(getenv('DATE_FORMAT')),
				'role' => $member->getRole()
			];
		}

		return $this->respond($membersArray);
	}

	/**
	 * @Route("/member/{group_id}/{user_id}", methods={"DELETE"}))
	 */
	public function delete($group_id, $user_id, GroupRepository $groupRepository, MemberRepository $memberRepository, UserRepository $userRepository, EntityManagerInterface $em)
	{
		if(!$group_id)
			return $this->respondValidationError('Please provide a group id');

		if(!$group = $groupRepository->findOneBy(['uuid'=>$group_id]))
			return $this->respondNotFound('Please provide a valid group id');

		if(!$user_id)
			return $this->respondValidationError('Please provide a user id');

		if(!$user = $userRepository->findOneBy(['uuid'=>$user_id]))
			return $this->respondNotFound('Please provide a valid user id');

		$memberships = $memberRepository->findBy(['user'=>$user, 'group'=>$group]);

		if(!$memberships || !count($memberships))
			return $this->respondNotFound();

		foreach ($memberships as $membership)
			$em->remove($membership);

		$em->flush();

		return $this->respondGone();
	}

	/**
	 * @Route("/member/{group_id}/{user_id}", methods={"POST"}))
	 */
	public function add($group_id, $user_id, Request $request, UserRepository $userRepository, GroupRepository $groupRepository, EntityManagerInterface $em)
	{
		$role = $request->get('role', 'participant');

		if(!$user_id)
			return $this->respondValidationError('Please provide a user id');

		$user = $userRepository->findOneBy(['uuid'=>$user_id]);
		if(!$user)
			return $this->respondValidationError('The user does not exist');

		if(!$group_id)
			return $this->respondValidationError('Please provide a group id');

		$group = $groupRepository->findOneBy(['uuid'=>$group_id]);
		if(!$group)
			return $this->respondValidationError('The group does not exist');

		$member = new Member();
		$member->setUser($user);
		$member->setGroup($group);
		$member->setRole($role);

		try{

			$em->persist($member);
			$em->flush();

			return $this->respondCreated();
		}
		catch(\Exception $e){

			return $this->respondWithErrors( $e->getMessage() );
		}
	}
}