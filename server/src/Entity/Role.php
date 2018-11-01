<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 */
class Role
{
    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('participant', 'referent')"))
     */
    private $role;

	/**
	 * @ORM\Id()
	 * @ORM\ManyToOne(targetEntity="Militant", inversedBy="groups")
	 * @ORM\JoinColumn(name="militant_id", referencedColumnName="id", nullable=false)
	 */
    private $militant;

	/**
	 * @ORM\Id()
	 * @ORM\ManyToOne(targetEntity="Group", inversedBy="militants")
	 * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false)
	 */
    private $group;


	public function getRole(): ?string
	{
		return $this->role;
	}

	public function setRole(?string $role): self
	{
		$this->role = $role;

		return $this;
	}
}
