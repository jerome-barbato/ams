<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Participant
{
	/**
	 * @ORM\Column(type="string", columnDefinition="ENUM('participant', 'referent', 'peacekeeper')"))
	 */
	private $role;

	/**
	 * @ORM\Id()
	 * @ORM\ManyToOne(targetEntity="Militant", inversedBy="events")
	 * @ORM\JoinColumn(name="militant_id", referencedColumnName="id", nullable=false)
	 */
	private $militant;

	/**
	 * @ORM\Id()
	 * @ORM\ManyToOne(targetEntity="Event", inversedBy="militants")
	 * @ORM\JoinColumn(name="event_id", referencedColumnName="id", nullable=false)
	 */
	private $event;

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

	public function getEvent()
	{
		return $this->event;
	}

	public function setEvent(Event $event): self
	{
		$this->event = $event;

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
