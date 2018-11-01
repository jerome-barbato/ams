<?php

namespace App\Repository;

use App\Entity\Militant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Militant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Militant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Militant[]    findAll()
 * @method Militant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MilitantRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Militant::class);
    }

	public function transform(Militant $militant)
	{
		return [
			'id'    => (int) $militant->getId(),
			'name' => (string) $militant->getFirstName().' '.$militant->getLastName(),
			'groups' => $militant->getGroups()->count()
		];
	}

	public function transformAll()
	{
		$militants = $this->findAll();
		$militantsArray = [];

		foreach ($militants as $militant) {
			$militantsArray[] = $this->transform($militant);
		}

		return $militantsArray;
	}
}
