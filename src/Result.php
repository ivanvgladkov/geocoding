<?php

namespace Geocoding;

/**
 * Class Result
 * @package Geocoding
 */
class Result
{

    private $addressComponents;
    private $formattedAddress;
    private $geometry;
    private $placeId;
    private $types;

    private static $map = [
        'address_components' => 'addressComponents',
        'formatted_address' => 'formattedAddress',
        'geometry' => 'geometry',
        'place_id' => 'placeId',
        'types' => 'types',
    ];

    public function getAddressComponent() { return $this->addressComponents; }
    public function getFormattedAddress() { return $this->formattedAddress; }
    public function getGeometry() { return $this->geometry; }
    public function getPlaceId() { return $this->placeId; }
    public function getTypes() { return $this->types; }

    /**
     * @param array $data
     * @return $this
     */
    public function populate(array $data)
    {
        foreach ($data as $key => $value) {
            if (!array_key_exists($key, self::$map)) {
                continue;
            }

            $this->{self::$map[$key]} = $value;
        }

        return $this;
    }

}

