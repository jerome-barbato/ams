<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 * @ORM\Table(name="`group`")
 * @ORM\HasLifecycleCallbacks()
 */
class Group
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", length=13)
	 */
	private $uuid;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $title;

	/**
	 * @ORM\Column(type="string", length=200, nullable=true)
	 */
	private $image;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $description;

	/**
	 * Many Groups have Many Users.
	 * @OneToMany(targetEntity="Member", mappedBy="group", cascade={"remove"})
	 */
	private $militants;

	/**
	 * @ORM\Column(type="date")
	 */
	private $creation;

	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\News", mappedBy="groups")
	 */
	private $news;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Event", mappedBy="groups")
     */
    private $events;


	public function __construct()
               	{
               		$this->militants = new ArrayCollection();
               		$this->news = new ArrayCollection();
                 $this->events = new ArrayCollection();
               	}

	/**
	 * Triggered on insert
	 * @ORM\PrePersist
	 */
	public function onPrePersist()
               	{
               		$this->setCreation( new \DateTime("now") );
               	}

	public function getMilitants(): Collection
               	{
               		return $this->militants;
               	}

	public function getId(): ?int
               	{
               		return $this->id;
               	}

	public function getTitle(): ?string
               	{
               		return $this->title;
               	}

	public function setTitle(string $title): self
               	{
               		$this->title = $title;
               
               		return $this;
               	}

	public function getImage(): ?string
               	{
               		return $this->image;
               	}

	public function setImage(?string $image): self
               	{
               		$this->image = $image;
               
               		return $this;
               	}

	public function getDescription(): ?string
               	{
               		return $this->description;
               	}

	public function setDescription(?string $description): self
               	{
               		$this->description = $description;
               
               		return $this;
               	}

	public function getCreation(): ?\DateTimeInterface
               	{
               		return $this->creation;
               	}

	public function setCreation(\DateTimeInterface $creation): self
               	{
               		$this->creation = $creation;
               
               		return $this;
               	}

	public function getUuid(): ?string
               	{
               		return $this->uuid;
               	}

	public function setUuid(string $uuid): self
               	{
               		$this->uuid = $uuid;
               
               		return $this;
               	}

	/**
	 * @return Collection|News[]
	 */
	public function getNews(): Collection
               	{
               		return $this->news;
               	}

	public function addNews(News $news): self
               	{
               		if (!$this->news->contains($news)) {
               			$this->news[] = $news;
               			$news->addGroup($this);
               		}
               
               		return $this;
               	}

	public function removeNews(News $news): self
               	{
               		if ($this->news->contains($news)) {
               			$this->news->removeElement($news);
               			$news->removeGroup($this);
               		}
               
               		return $this;
               	}

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->addGroup($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            $event->removeGroup($this);
        }

        return $this;
    }
}
