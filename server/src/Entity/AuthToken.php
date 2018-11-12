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
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	protected $createdAt;

	/**
	 * @ORM\ManyToOne(targetEntity="Militant")
	 * @var Militant
	 */
	protected $militant;

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

	public function getMilitant()
	{
		return $this->militant;
	}

	public function setMilitant(Militant $militant)
	{
		$this->militant = $militant;
	}
}