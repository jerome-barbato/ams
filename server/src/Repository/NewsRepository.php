<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\User;
use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, News::class);
    }

	public function transform(News $news, $full=false)
	{
		$data = [
			'slug'    => (string) $news->getSlug(),
			'title'   => (string) $news->getTitle(),
			'image'   => (string) $news->getImage(),
			'excerpt' => (string) $news->getExcerpt(),
			'created' => (string) $news->getCreated()->format(getenv('DATETIME_FORMAT')),
			'groups'  => (int) $news->getGroups()->count()
		];

		if( $full ) {

			$data['groups'] = [];

			/* @var $groups Group[] */
			$groups = $news->getGroups();

			/* @var $groupRepository groupRepository */
			$groupRepository = $this->getEntityManager()->getRepository('App:Group');

			foreach ($groups as $group){
				$data['groups'][] = $groupRepository->transform($group);
			}

			/* @var $userRepository userRepository */
			$userRepository = $this->getEntityManager()->getRepository('App:User');

			/* @var $user User */
			$user = $news->getAuthor();

			$data['author']  = $userRepository->transform($user);
			$data['updated'] = $news->getUpdated()->format(getenv('DATETIME_FORMAT'));
			$data['text']    = $news->getText();
		}

		return $data;
	}
}
