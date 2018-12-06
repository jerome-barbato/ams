<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface
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
	 * @ORM\Column(type="string", length=200)
	 */
	private $firstName;

	/**
	 * @ORM\Column(type="string", length=200)
	 */
	private $lastName;

	/**
	 * @ORM\Column(type="string", length=200, unique=true)
	 */
	private $email;

	/**
	 * @ORM\Column(type="json")
	 */
	private $roles = [];

	/**
	 * @var string The hashed password
	 * @ORM\Column(type="string")
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $image;

	/**
	 * Many Users can be in Many Groups.
	 * @OneToMany(targetEntity="Member", mappedBy="user")
	 */
	private $groups;

	/**
	 * Many Users can be in Many event.
	 * @OneToMany(targetEntity="Participant", mappedBy="user")
	 */
	private $events;

	/**
	 * @ORM\Column(type="date")
	 */
	private $inscription;

	/**
	 * @ORM\ManyToOne(targetEntity="Place")
	 */
	private $place;

	/**
	 * @ORM\OneToMany(targetEntity="News", mappedBy="author")
	 */
	private $news;

	/**
	 * @ORM\ManyToMany(targetEntity="Material", mappedBy="owners")
	 */
	private $materials;

	/**
	 * @ORM\OneToMany(targetEntity="AuthToken", mappedBy="user", orphanRemoval=true)
	 */
	private $authTokens;

	/**
	 * Triggered on insert
	 * @ORM\PrePersist
	 */
	public function onPrePersist()
	{
		$this->setUuid(uniqid());
		$this->setInscription( new \DateTime("now") );
	}

	public function __construct()
	{
		$this->groups = new ArrayCollection();
		$this->events = new ArrayCollection();
		$this->news = new ArrayCollection();
		$this->materials = new ArrayCollection();
		$this->authTokens = new ArrayCollection();
	}

	public function getGroups(): Collection
	{
		return $this->groups;
	}

	public function getEvents(): Collection
	{
		return $this->events;
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getFirstName(): ?string
	{
		return $this->firstName;
	}

	public function setFirstName(string $firstName): self
	{
		$this->firstName = $firstName;

		return $this;
	}

	public function getLastName(): ?string
	{
		return $this->lastName;
	}

	public function setLastName(string $lastName): self
	{
		$this->lastName = $lastName;

		return $this;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(string $email): self
	{
		$this->email = $email;

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

	public function getUuid(): ?string
	{
		return $this->uuid;
	}

	public function setUuid(string $uuid): self
	{
		$this->uuid = $uuid;

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

	public function getPlace(): ?Place
	{
		return $this->place;
	}

	public function setPlace(?Place $place): self
	{
		$this->place = $place;

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
			$news->setAuthor($this);
		}

		return $this;
	}

	public function removeNews(News $news): self
	{
		if ($this->news->contains($news)) {
			$this->news->removeElement($news);
			// set the owning side to null (unless already changed)
			if ($news->getAuthor() === $this) {
				$news->setAuthor(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|Material[]
	 */
	public function getMaterials(): Collection
	{
		return $this->materials;
	}

	public function addMaterial(Material $material): self
	{
		if (!$this->materials->contains($material)) {
			$this->materials[] = $material;
			$material->addOwner($this);
		}

		return $this;
	}

	public function removeMaterial(Material $material): self
	{
		if ($this->materials->contains($material)) {
			$this->materials->removeElement($material);
			$material->removeOwner($this);
		}

		return $this;
	}

	/**
	 * A visual identifier that represents this user.
	 *
	 * @see UserInterface
	 */
	public function getUsername(): string
	{
		return (string) $this->email;
	}

	/**
	 * @see UserInterface
	 */
	public function getRoles(): array
	{
		$roles = $this->roles;
		// guarantee every user at least has ROLE_USER
		$roles[] = 'ROLE_USER';

		return array_unique($roles);
	}

	public function setRoles(array $roles): self
	{
		$this->roles = $roles;

		return $this;
	}

	/**
	 * @see UserInterface
	 */
	public function getPassword(): string
	{
		return (string) $this->password;
	}

	public function setPassword(string $password): self
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * @see UserInterface
	 */
	public function getSalt()
	{
		// not needed when using the "bcrypt" algorithm in security.yaml
	}

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials()
	{
		// If you store any temporary, sensitive data on the user, clear it here
		// $this->plainPassword = null;
	}

	/**
	 * @return Collection|AuthToken[]
	 */
	public function getAuthTokens(): Collection
	{
		return $this->authTokens;
	}

	public function addAuthToken(authToken $authToken): self
	{
		if (!$this->authTokens->contains($authToken)) {
			$this->authTokens[] = $authToken;
			$authToken->setUser($this);
		}

		return $this;
	}

	public function removeAuthToken(authToken $authToken): self
	{
		if ($this->authTokens->contains($authToken)) {
			$this->authTokens->removeElement($authToken);
			// set the owning side to null (unless already changed)
			if ($authToken->getUser() === $this) {
				$authToken->setUser(null);
			}
		}

		return $this;
	}
}
