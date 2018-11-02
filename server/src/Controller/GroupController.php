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
	 * @Route("/groups/{id}", methods={"GET"})
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
	 * @Route("/groups", methods={"POST"})
	 */
	public function create(Request $request, GroupRepository $groupRepository, EntityManagerInterface $em)
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

		// persist the new group
		try{
			$group = new Group();
			$group->setUuid(uniqid());
			$group->setFirstName($request->get('first_name'));
			$group->setLastName($request->get('last_name'));
			$group->setEmail($request->get('email'));
			$group->setAddress($request->get('address'));
			$group->setPostalCode($request->get('postal_code'));
			$group->setCity($request->get('city'));
			$group->setCountry($request->get('country'));
			$group->setLat(0);
			$group->setLng(0);

			$em->persist($group);
			$em->flush();
		}
		catch(\Exception $e){
			return $this->respondWithErrors( $e->getMessage() );
		}

		return $this->respondCreated($groupRepository->transform($group));
	}
}