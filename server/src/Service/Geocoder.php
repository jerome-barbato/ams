<?php

namespace App\Service;

class Geocoder
{
	private $raw_address;

	private $postal_code;
	private $locality;
	private $country;
	private $country_code;
	private $street_number;
	private $street_name;
	private $sublocality;
	private $place_id;
	private $lat;
	private $lng;

	public function __construct( $raw_address )
	{
		$this->raw_address = $raw_address;
	}

	/**
	 * @throws \Exception
	 */
	public function geocode()
	{
		if(!getenv('GMAP_API_KEY'))
			throw new \Exception('GMAP_API_KEY is not defined');

		$context_options= [
			'http'=> [
				'timeout' => 5
			],
			'ssl'=>[
				'verify_peer'=>false,
				'verify_peer_name'=>false,
			]
		];
		$geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.urlencode($this->raw_address).'&sensor=false&key='.urlencode(getenv('GMAP_API_KEY')),false, stream_context_create($context_options));
		$geocode = json_decode($geocode);

		if( $geocode->status != 'OK' )
			throw new \Exception($geocode->error_message);

		$result = $geocode->results[0];

		foreach ($result->address_components as $component) {
			foreach ($component->types as $type) {
				$this->updateAddressComponent($type, $component);
			}
		}

		$this->setPlaceId($result->place_id);
		$this->setLat($result->geometry->location->lat);
		$this->setLng($result->geometry->location->lng);
	}

	private function updateAddressComponent($type, $values)
	{
		switch ($type) {
			case 'postal_code':
				$this->setPostalCode($values->long_name);
				break;
			case 'locality':
			case 'postal_town':
				$this->setLocality($values->long_name);
				break;
			case 'country':
				$this->setCountry($values->long_name);
				$this->setCountryCode($values->short_name);
				break;
			case 'street_number':
				$this->setStreetNumber($values->long_name);
				break;
			case 'route':
				$this->setStreetName($values->long_name);
				break;
			case 'sublocality':
				$this->setSublocality($values->long_name);
				break;
			default:
		}
	}

	/**
	 * @return mixed
	 */
	public function getRawAddress()
	{
		return $this->raw_address;
	}

	/**
	 * @param mixed $raw_address
	 */
	public function setRawAddress($raw_address): void
	{
		$this->raw_address = $raw_address;
	}

	/**
	 * @return mixed
	 */
	public function getPostalCode()
	{
		return $this->postal_code;
	}

	/**
	 * @param mixed $postal_code
	 */
	public function setPostalCode($postal_code): void
	{
		$this->postal_code = $postal_code;
	}

	/**
	 * @return mixed
	 */
	public function getLocality()
	{
		return $this->locality;
	}

	/**
	 * @param mixed $locality
	 */
	public function setLocality($locality): void
	{
		$this->locality = $locality;
	}

	/**
	 * @return mixed
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * @param mixed $country
	 */
	public function setCountry($country): void
	{
		$this->country = $country;
	}

	/**
	 * @return mixed
	 */
	public function getCountryCode()
	{
		return $this->country_code;
	}

	/**
	 * @param mixed $country_code
	 */
	public function setCountryCode($country_code): void
	{
		$this->country_code = $country_code;
	}

	/**
	 * @return mixed
	 */
	public function getStreetNumber()
	{
		return $this->street_number;
	}

	/**
	 * @param mixed $street_number
	 */
	public function setStreetNumber($street_number): void
	{
		$this->street_number = $street_number;
	}

	/**
	 * @return mixed
	 */
	public function getStreetName()
	{
		return $this->street_name;
	}

	/**
	 * @param mixed $street_name
	 */
	public function setStreetName($street_name): void
	{
		$this->street_name = $street_name;
	}

	/**
	 * @return mixed
	 */
	public function getSublocality()
	{
		return $this->sublocality;
	}

	/**
	 * @param mixed $sublocality
	 */
	public function setSublocality($sublocality): void
	{
		$this->sublocality = $sublocality;
	}


	/**
	 * @return mixed
	 */
	public function getPlaceId()
	{
		return $this->place_id;
	}

	/**
	 * @param mixed $place_id
	 */
	public function setPlaceId($place_id): void
	{
		$this->place_id = $place_id;
	}

	/**
	 * @return mixed
	 */
	public function getLat()
	{
		return $this->lat;
	}

	/**
	 * @param mixed $lat
	 */
	public function setLat($lat): void
	{
		$this->lat = $lat;
	}

	/**
	 * @return mixed
	 */
	public function getLng()
	{
		return $this->lng;
	}

	/**
	 * @param mixed $lng
	 */
	public function setLng($lng): void
	{
		$this->lng = $lng;
	}
}