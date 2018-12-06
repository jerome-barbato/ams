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

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function setValue($value)
	{
		$this->value = $value;
	}

	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	public function setCreatedAt(\DateTime $createdAt)
	{
		$this->createdAt = $createdAt;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function setUser(User $user)
	{
		$this->user = $user;
	}

	public function getIpHash()
	{
		return $this->ip_hash;
	}

	public function setIpHash($ip_hash): void
	{
		$this->ip_hash = $ip_hash;
	}

	public static function anonymizeIp( $ip )
	{
		return substr(sha1($ip), 0, 16);
	}
}