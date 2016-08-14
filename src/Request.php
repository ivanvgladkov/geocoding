<?php

namespace Ivanvgladkov\Geocoding;

/**
 * Class Request
 * @package Geocoding
 */
class Request
{
    private $language;

    private $address;
    private $components;
    private $region;
    private $bounds;

    private $longitude;
    private $latitude;
    private $latlng;
    private $placeId;
    private $resultType;
    private $locationType;

    const COMPONENT_ROUTE = 'route';
    const COMPONENT_LOCALITY = 'locality';
    const COMPONENT_ADMINISTRATIVE_AREA = 'administrative_area';
    const COMPONENT_POSTAL_CODE = 'postal_code';
    const COMPONENT_COUNTRY = 'country';

    const RESULT_TYPE_COUNTRY = 'country';
    const RESULT_TYPE_STREET_ADDRESS = 'street_address';
    const RESULT_TYPE_POSTAL_CODE = 'postal_code';
    //todo: add more result types

    /**
     * restricts the results to addresses for which we have location information
     * accurate down to street address precision
     */
    const LOCATION_TYPE_ROOFTOP = 'ROOFTOP';

    /**
     * restricts the results to those that reflect an approximation (usually on a road) interpolated between two
     * precise points (such as intersections). An interpolated range generally indicates that rooftop geocodes are
     * unavailable for a street address
     */
    const LOCATION_TYPE_RANGE_INTERPOLATED = 'RANGE_INTERPOLATED';

    /**
     * restricts the results to geometric centers of a location such as a polyline (for example, a street)
     * or polygon (region)
     */
    const LOCATION_TYPE_GEOMETRIC_CENTER = 'GEOMETRIC_CENTER';

    /**
     * restricts the results to those that are characterized as approximate
     */
    const LOCATION_TYPE_APPROXIMATE = 'APPROXIMATE';

    private static $frontParamsMap = [
        'language' => 'language',
        'address' => 'address',
        'components' => 'components',
        'region' => 'region',
        'bounds' => 'bounds',
    ];

    private static $reverseParamsMap = [
        'language' => 'language',
        'latlng' => 'latlng',
        'placeId' => 'place_id',
        'resultType' => 'result_type',
        'locationType' => 'location_type',
    ];

    public function setLanguage(string $language)
    {
        $this->language = $language;
        return $this;
    }

    public function setAddress(string $address)
    {
        $this->address = $address;
        return $this;
    }

    public function setComponents(array $components)
    {
        $requestComponents = [];
        foreach ($components as $name => $value) {
            $requestComponents = $name . ':' . $value;
        }

        $this->components = implode('|', $requestComponents);
        return $this;
    }

    public function setRegion(string $region)
    {
        $this->region = $region;
        return $this;
    }

    public function setBounds(
        string $southwestLatitude,
        string $southwestLongitude,
        string $northeastLatitude,
        string $northeastLongitude
    ) {
        $this->bounds = sprintf('%s,%s|%s,%s', $southwestLatitude, $southwestLongitude, $northeastLatitude, $northeastLongitude);
        return $this;
    }

    public function setLongitude(string $value)
    {
        $this->longitude = $value;
        $this->setLatlng();
        return $this;
    }

    public function setLatitude(string $value)
    {
        $this->latitude = $value;
        $this->setLatlng();
        return $this;
    }

    private function setLatlng()
    {
        $this->latlng = $this->latitude . ',' . $this->longitude;
        return $this;
    }

    public function setResultType(array $types)
    {
        $this->resultType = implode('|', $types);
        return $this;
    }

    public function setLocationType(array $types)
    {
        $this->locationType = implode('|', $types);
        return $this;
    }

    public function getMappedParams() : array
    {
        if ($this->address !== null || $this->components !== null) {
            $isReverse = false;
        } elseif ($this->longitude !== null || $this->latitude !== null || $this->placeId !== null) {
            $isReverse = true;
        } else {
            return [];
        }

        $paramsMap = $isReverse ? self::$reverseParamsMap : self::$frontParamsMap;
        $params = [];

        foreach ($paramsMap as $entityField => $apiField) {
            if ($this->$entityField !== null) {
                $params[$apiField] = $this->$entityField;
            }
        }

        return $params;
    }
}
