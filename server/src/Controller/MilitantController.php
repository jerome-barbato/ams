<?php
namespace App\Controller;

use App\Entity\Militant;
use App\Repository\MilitantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MilitantController extends ApiController
{
	/**
	 * @Route("/militants/{page}", methods={"GET"}, requirements={"page"="\d+"})
	 */
	public function list($page=0, MilitantRepository $militantRepository)
	{
		$militants      = $militantRepository->findBy([], ['inscription'=>'ASC'], getenv('LIMIT'), $page);
		$militantsArray = [];

		foreach ($militants as $militant) {
			$militantsArray[] = $militantRepository->transform($militant);
		}

		return $this->respond($militantsArray);
	}


	/**
	 * @Route("/militants/{id}", methods={"GET"})
	 */
	public function show($id, MilitantRepository $militantRepository)
	{
		$militant = $militantRepository->findOneBy(['uuid'=>$id]);

		if (!$militant)
			return $this->respondNotFound();

		$militant = $militantRepository->transform($militant, true);

		return $this->respond($militant);
	}


	/**
	 * @Route("/militants", methods={"POST"})
	 */
	public function create(Request $request, MilitantRepository $militantRepository, EntityManagerInterface $em)
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

		// persist the new militant
		try{
			$militant = new Militant();
			$militant->setUuid(uniqid());
			$militant->setFirstName($request->get('first_name'));
			$militant->setLastName($request->get('last_name'));
			$militant->setEmail($request->get('email'));
			$militant->setAddress($request->get('address'));
			$militant->setPostalCode($request->get('postal_code'));
			$militant->setCity($request->get('city'));
			$militant->setCountry($request->get('country'));
			$militant->setLat(0);
			$militant->setLng(0);

			$em->persist($militant);
			$em->flush();
		}
		catch(\Exception $e){
			return $this->respondWithErrors( $e->getMessage() );
		}

		return $this->respondCreated($militantRepository->transform($militant));
	}
}