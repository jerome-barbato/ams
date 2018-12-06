<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Group;
use App\Entity\Place;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Event::class);
    }

	public function transform(Event $event, $full=false)
	{
		$data = [
			'id'           => (string) $event->getUuid(),
			'title'        => (string) $event->getTitle(),
			'description'  => (string) $event->getDescription(),
			'begin'        => (string) $event->getBegin()->format(getenv('DATETIME_FORMAT')),
			'end'          => (string) $event->getEnd()->format(getenv('DATETIME_FORMAT')),
			'creation'     => (string) $event->getCreation()->format(getenv('DATE_FORMAT')),
			'image'        => (string) $event->getImage(),
			'type'         => (string) $event->getType(),
			'participants' => (int) $event->getUsers()->count(),
			'groups'       => (int) $event->getGroups()->count(),
		];

		if( $full ) {

			$data['groups'] = [];

			/* @var $groups Group[] */
			$groups = $event->getGroups();

			/* @var $groupRepository groupRepository */
			$groupRepository = $this->getEntityManager()->getRepository('App:Group');

			foreach ($groups as $group){
				$data['groups'][] = $groupRepository->transform($group);
			}

			/* @var $placeRepository placeRepository */
			$placeRepository = $this->getEntityManager()->getRepository('App:Place');

			/* @var $place Place */
			$place = $event->getPlace();
			$data['place'] = $placeRepository->transform($place);
		}
		return $data;
	}
}
