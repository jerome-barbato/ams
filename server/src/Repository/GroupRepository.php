<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Group::class);
    }

	public function transform(Group $group)
	{
		return [
			'id'           => (string) $group->getUuid(),
			'title'        => (string) $group->getTitle(),
			'description'  => (string) $group->getDescription(),
			'creation'     => (string) $group->getCreation()->format(getenv('DATE_FORMAT')),
			'participants' => (int) $group->getMilitants()->count(),
			'news'         => (int) $group->getNews()->count()
		];
	}
}
