<?php

namespace App\Repository;

use App\Entity\Militant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Repository\GroupRepository;
use App\Entity\Member;
use App\Entity\Place;
use App\Entity\Event;

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

	public function transform(Militant $militant, $full=false)
	{
		$data = [
			'id'          => $militant->getUuid(),
			'inscription' => (string) $militant->getInscription()->format(getenv('DATE_FORMAT')),
			'name'        => (string) $militant->getFirstName().' '.$militant->getLastName(),
			'image'       => (string) $militant->getImage()
		];

		if( $full ){

			$data['email'] = $militant->getEmail();

			/* @var $placeRepository placeRepository */
			$placeRepository = $this->getEntityManager()->getRepository(  'App:Place');

			/* @var $place Place */
			$place = $militant->getPlace();
			$data['place'] = $placeRepository->transform($place);

			$data['first_name'] = (string) $militant->getFirstName();
			$data['last_name']  = (string) $militant->getLastName();
			$data['groups']     = (int) $militant->getGroups()->count();
			$data['events']     = (int) $militant->getEvents()->count();
			$data['news']       = (int) $militant->getNews()->count();
			$data['materials']  = (int) $militant->getMaterials()->count();
		}

		return $data;
	}
}
