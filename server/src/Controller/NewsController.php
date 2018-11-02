<?php
namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends ApiController
{
	/**
	 * @Route("/news/{page}", methods={"GET"}, requirements={"page"="\d+"})
	 */
	public function list($page=0, NewsRepository $newsRepository)
	{
		$news = $newsRepository->findBy([], ['created'=>'ASC'], getenv('LIMIT'), $page);
		$newsArray = [];

		foreach ($news as $_news) {
			$newsArray[] = $newsRepository->transform($_news);
		}

		return $this->respond($newsArray);
	}


	/**
	 * @Route("/news/{slug}", methods={"GET"})
	 */
	public function show($slug, NewsRepository $newsRepository)
	{
		$news = $newsRepository->findOneBy(['slug'=>$slug]);

		if (!$news)
			return $this->respondNotFound();

		$event = $newsRepository->transform($news, true);

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