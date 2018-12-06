<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Repository\GroupRepository;
use App\Entity\Member;
use App\Entity\Place;
use App\Entity\Event;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

	public function transform(User $user, $full=false)
	{
		$data = [
			'id'          => $user->getUuid(),
			'inscription' => (string) $user->getInscription()->format(getenv('DATE_FORMAT')),
			'name'        => (string) $user->getFirstName().' '.$user->getLastName(),
			'image'       => (string) $user->getImage()
		];

		if( $full ){

			$data['email'] = $user->getEmail();

			/* @var $placeRepository placeRepository */
			$placeRepository = $this->getEntityManager()->getRepository(  'App:Place');

			/* @var $place Place */
			$place = $user->getPlace();
			$data['place'] = $placeRepository->transform($place);

			$data['first_name'] = (string) $user->getFirstName();
			$data['last_name']  = (string) $user->getLastName();
			$data['groups']     = (int) $user->getGroups()->count();
			$data['events']     = (int) $user->getEvents()->count();
			$data['news']       = (int) $user->getNews()->count();
			$data['materials']  = (int) $user->getMaterials()->count();
		}

		return $data;
	}
}
