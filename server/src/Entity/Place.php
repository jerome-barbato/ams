<?php

namespace App\Entity;

use App\Service\Geocoder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlaceRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Place
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;


	/**
	 * @ORM\Column(type="string", length=200)
	 */
	private $address;

	/**
	 * @ORM\Column(type="string", length=6)
	 */
	private $postal_code;

	/**
	 * @ORM\Column(type="string", length=200)
	 */
	private $city;

	/**
	 * @ORM\Column(type="string", length=200)
	 */
	private $country;

	/**
	 * @ORM\Column(type="float")
	 */
	private $lat;

	/**
	 * @ORM\Column(type="float")
	 */
	private $lng;

	/**
	 * @ORM\Column(type="string", length=200, unique=true, nullable=true)
	 */
	private $gid;

	private $error = false;
	private $errorMessage = '';


	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function geocode(){

		$formatted_address = $this->getAddress().','.$this->getPostalCode().' '.$this->getCity().', '.$this->getCountry();

		try{
			$geocoder = new Geocoder($formatted_address);
			$geocoder->geocode();

			$this->setLat( $geocoder->getLat() );
			$this->setLng( $geocoder->getLng() );
			$this->setPostalCode( $geocoder->getPostalCode() );
			$this->setCity( $geocoder->getLocality() );
			$this->setCountry( $geocoder->getCountry() );
			$this->setGid( $geocoder->getPlaceId() );
			$this->setAddress( $geocoder->getStreetNumber().' '.$geocoder->getStreetName() );
		}
		catch (\Exception $e){

			$this->setLat( 0 );
			$this->setLng( 0 );
			$this->setError( $e->getMessage() );
		}

		return true;
	}


	public function hasError(): ?bool
	{
		return $this->error;
	}

	public function getError(): ?string
	{
		return $this->errorMessage;
	}

	public function setError($error): self
	{
		$this->error = true;
		$this->errorMessage = $error;

		return $this;
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getAddress(): ?string
	{
		return $this->address;
	}

	public function setAddress(string $address): self
	{
		$this->address = $address;

		return $this;
	}

	public function getPostalCode(): ?string
	{
		return $this->postal_code;
	}

	public function setPostalCode(string $postal_code): self
	{
		$this->postal_code = $postal_code;

		return $this;
	}

	public function getCity(): ?string
	{
		return $this->city;
	}

	public function setCity(string $city): self
	{
		$this->city = $city;

		return $this;
	}

	public function getCountry(): ?string
	{
		return $this->country;
	}

	public function setCountry(string $country): self
	{
		$this->country = $country;

		return $this;
	}

	public function getLat(): ?float
	{
		return $this->lat;
	}

	public function setLat(float $lat): self
	{
		$this->lat = $lat;

		return $this;
	}

	public function getLng(): ?float
	{
		return $this->lng;
	}

	public function setLng(float $lng): self
	{
		$this->lng = $lng;

		return $this;
	}

	public function getGid(): ?string
	{
		return $this->gid;
	}

	public function setGid(string $gid): self
	{
		$this->gid = $gid;

		return $this;
	}
}
