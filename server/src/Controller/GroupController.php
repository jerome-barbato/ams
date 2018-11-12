<?php
namespace App\Controller;

use App\Entity\Group;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends ApiController
{
	/**
	 * @Route("/groups/{page}", methods={"GET"}, requirements={"page"="\d+"})
	 */
	public function list($page=0, GroupRepository $groupRepository)
	{
		$groups = $groupRepository->findBy([], ['creation'=>'ASC'], getenv('LIMIT'), $page);
		$groupsArray = [];

		foreach ($groups as $group) {
			$groupsArray[] = $groupRepository->transform($group);
		}

		return $this->respond($groupsArray);
	}


	/**
	 * @Route("/group/{id}", methods={"GET"})
	 */
	public function show($id, GroupRepository $groupRepository)
	{
		$group = $groupRepository->findOneBy(['uuid'=>$id]);

		if (! $group)
			return $this->respondNotFound();

		$group = $groupRepository->transform($group);

		return $this->respond($group);
	}


	/**
	 * @Route("/group", methods={"POST"})
	 */
	public function create(Request $request, GroupRepository $groupRepository, EntityManagerInterface $em)
	{
		// validate the fields
		$fields = ['title'];
		foreach ($fields as $field){
			if (!$request->get($field)) {
				return $this->respondValidationError('Please provide a '.str_replace('_', ' ', $field).'!');
			}
		}

		// persist the new group
		try{
			$group = new Group();
			$group->setUuid(uniqid());
			$group->setTitle($request->get('title'));
			$group->setDescription($request->get('description'));

			$em->persist($group);
			$em->flush();
		}
		catch(\Exception $e){
			return $this->respondWithErrors( $e->getMessage() );
		}

		return $this->respondCreated($groupRepository->transform($group));
	}


	/**
	 * @Route("/group/{id}", methods={"DELETE"})
	 */
	public function delete($id, GroupRepository $groupRepository, EntityManagerInterface $em)
	{
		$group = $groupRepository->findOneBy(['uuid'=>$id]);

		if (!$group)
			return $this->respondNotFound();

		$em->remove($group);
		$em->flush();

		return $this->respondGone();
	}
}