<?php

namespace App\Repository;

use App\Entity\Material;
use App\Entity\Militant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Material|null find($id, $lockMode = null, $lockVersion = null)
 * @method Material|null findOneBy(array $criteria, array $orderBy = null)
 * @method Material[]    findAll()
 * @method Material[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaterialRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Material::class);
    }
	public function transform(Material $material, $full=false)
	{
		$data = [
			'id'       => (string) $material->getUuid(),
			'name'     => (string) $material->getName(),
			'image'    => (string) $material->getImage(),
			'quantity' => (int) $material->getQuantity(),
			'type'     => (string) $material->getType(),
			'theme'    => (string) $material->getTheme(),
			'owners'   => (int) $material->getOwners()->count()
		];

		if( $full ) {

			$data['owners'] = [];

			/* @var $militant Militant[] */
			$militants = $material->getOwners();

			/* @var $militantRepository militantRepository */
			$militantRepository = $this->getEntityManager()->getRepository('App:Militant');

			foreach ($militants as $militant){
				$data['owners'][] = $militantRepository->transform($militant);
			}

			/* @var $placeRepository placeRepository */
			$placeRepository = $this->getEntityManager()->getRepository('App:Place');

			if($place = $material->getPlace())
				$data['place']       = $placeRepository->transform($material->getPlace());
			else
				$data['place']       = false;

			$data['description']   = $material->getDescription();
			$data['location']      = $material->getLocation();
			$data['size']          = $material->getSize();
			$data['quantity_left'] = $material->getQuantity() - $militants->count();
		}

		return $data;
	}
}
