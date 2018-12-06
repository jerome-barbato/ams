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
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="events")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 */
	private $user;

	/**
	 * @ORM\Id()
	 * @ORM\ManyToOne(targetEntity="Event", inversedBy="users")
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

	public function getUser()
	{
		return $this->user;
	}

	public function setUser(User $user): self
	{
		$this->user = $user;

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
