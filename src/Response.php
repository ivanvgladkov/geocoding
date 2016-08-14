<?php

namespace Geocoding;

/**
 * Class GeocodingResponse
 * @package Geocoding
 */
class Response
{
    /**
     * @var Result[]
     */
    private $results = [];
    private $status;

    public function getResults() { return $this->results; }
    public function getStatus() { return $this->status; }

    /**
     * @param array $data
     * @return $this
     */
    public function populate(array $data)
    {
        if (array_key_exists('status', $data)) {
            $this->status = $data['status'];
        }

        if (array_key_exists('results', $data)) {

            foreach ($data['results'] as $resultData) {

                $result = new Result();
                $result->populate($resultData);
                $this->results[] = $result;
            }
        }

        return $this;
    }

}
