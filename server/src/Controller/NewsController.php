<?php
namespace App\Controller;

use App\Entity\News;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
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

		$news = $newsRepository->transform($news, true);

		return $this->respond($news);
	}

	/**
	 * @Route("/news/{slug}", methods={"DELETE"})
	 */
	public function delete($slug, NewsRepository $newsRepository, EntityManagerInterface $em)
	{
		$news = $newsRepository->findOneBy(['slug'=>$slug]);

		if (!$news)
			return $this->respondNotFound();

		$em->remove($news);
		$em->flush();

		return $this->respondGone();
	}


	/**
	 * @Route("/news", methods={"POST"})
	 */
	public function create(Request $request, NewsRepository $newsRepository, UserRepository $userRepository, GroupRepository $groupRepository, EntityManagerInterface $em)
	{
		// validate the fields
		$fields = ['title', 'user_id'];
		foreach ($fields as $field){
			if (!$request->get($field)) {
				return $this->respondValidationError('Please provide a '.str_replace('_', ' ', $field).'!');
			}
		}

		if(!$user = $userRepository->findOneBy(['uuid'=>$request->get('user_id')]))
			return $this->respondValidationError('Please provide a valid user id');

		$group = false;

		if($group_id = $request->get('group_id')){

			if(!$group = $groupRepository->findOneBy(['uuid'=>$group_id]))
				return $this->respondValidationError('Please provide a valid group id');
		}

		// persist the new news
		try{
			$news = new News();
			$news->setTitle($request->get('title'));
			$news->setExcerpt($request->get('excerpt', ''));
			$news->setImage($request->get('image',''));
			$news->setText($request->get('text',''));
			$news->setAuthor($user);

			if($group)
				$news->addGroup($group);

			$em->persist($news);
			$em->flush();
		}
		catch(\Exception $e){
			return $this->respondWithErrors( $e->getMessage() );
		}

		return $this->respondCreated($newsRepository->transform($news));
	}
}