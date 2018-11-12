<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MemberRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Member
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

	/**
	 * @ORM\Column(type="date")
	 */
	private $inscription;

	/**
	 * Triggered on insert
	 * @ORM\PrePersist
	 */
	public function onPrePersist()
	{
		$this->setInscription( new \DateTime("now") );
	}

	public function getRole(): ?string
	{
		return $this->role;
	}

	public function setRole(?string $role): self
	{
		$this->role = $role;

		return $this;
	}

	public function getMilitant()
	{
		return $this->militant;
	}

	public function setMilitant(Militant $militant): self
	{
		$this->militant = $militant;

		return $this;
	}

	public function getGroup()
	{
		return $this->group;
	}

	public function setGroup(Group $group): self
	{
		$this->group = $group;

		return $this;
	}

	public function getInscription(): ?\DateTimeInterface
	{
		return $this->inscription;
	}

	public function setInscription(\DateTimeInterface $inscription): self
	{
		$this->inscription = $inscription;

		return $this;
	}
}
