<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class AuthToken
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", unique=true)
	 */
	protected $value;

	/**
	 * @ORM\Column(type="string", length=16)
	 */
	protected $ip_hash;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	protected $createdAt;

	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="authTokens")
	 * @var User
	 */
	protected $user;

	/**
	 * Triggered on insert
	 * @ORM\PrePersist
	 * @throws \Exception
	 */
	public function onPrePersist()
	{
		$this->setCreatedAt( new \DateTime("now") );
		$this->setValue( base64_encode(random_bytes(50)) );
	}


	public function getId()
	{
		return $this->id;
	}

	public function setId($id): self
	{
		$this->id = $id;
		return $this;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function setValue($value): self
	{
		$this->value = $value;
		return $this;
	}

	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	public function setCreatedAt(\DateTime $createdAt): self
	{
		$this->createdAt = $createdAt;
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

	public function getIpHash()
	{
		return $this->ip_hash;
	}

	public function setIpHash($ip_hash): self
	{
		$this->ip_hash = $ip_hash;
		return $this;
	}

	public static function anonymizeIp( $ip )
	{
		return substr(sha1($ip), 0, 16);
	}
}