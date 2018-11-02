<?php

namespace App\Repository;

use App\Entity\Place;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Place|null find($id, $lockMode = null, $lockVersion = null)
 * @method Place|null findOneBy(array $criteria, array $orderBy = null)
 * @method Place[]    findAll()
 * @method Place[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Place::class);
    }

	public function transform(Place $place)
	{
		$data = [
			'title'       => $place->getTitle(),
			'address'     => $place->getAddress(),
			'postal_code' => $place->getPostalCode(),
			'city'        => $place->getCity(),
			'country'     => $place->getCountry(),
			'latlng'      => $place->getLat().','.$place->getLng(),
		];

		return $data;
	}
}
