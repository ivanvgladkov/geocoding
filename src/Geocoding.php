<?php

namespace Ivanvgladkov\Geocoding;

use GuzzleHttp\Client;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Ivanvgladkov\Geocoding\Exceptions\BadRequestException;
use Ivanvgladkov\Geocoding\Exceptions\InvalidResponseException;

/**
 * Class Geocoding
 * @package Geocoding
 */
class Geocoding
{

    private $key;
    private $format;
    private $baseUrl;

    /**
     * @var CacheContract
     */
    private $cache;

    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';

    /**
     * Geocoding constructor.
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
        $this->format = self::FORMAT_JSON;
        $this->baseUrl = 'https://maps.googleapis.com/maps/api/geocode';
    }

    public function setCache(CacheContract $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    public function getRequest() : Request
    {
        return new Request();
    }

    /**
     * @param Request $request
     * @return Response
     * @throws BadRequestException
     * @throws InvalidResponseException
     */
    public function send(Request $request) : Response
    {
        $paramsQuery = http_build_query(array_merge(['key' => $this->key], $request->getMappedParams()));
        $url = sprintf('%s/%s?%s', $this->baseUrl, $this->format, $paramsQuery);

        if ($this->isResultCached($url)) {
            return $this->getCachedResult($url);
        }

        $httpClient = new Client();
        $httpResponse = $httpClient->request('get', $url);

        if ($httpResponse->getStatusCode() !== 200) {
            throw new BadRequestException();
        }

        $responseData = json_decode($httpResponse->getBody()->getContents(), true);

        if ($responseData === null) {
            throw new InvalidResponseException();
        }

        $response = new Response();
        $response->populate($responseData);

        $this->cacheResult($url, $response);

        return $response;
    }

    private function isResultCached(string $url) : bool
    {
        if ($this->cache) {
            return $this->cache->has($this->getCacheKey($url));
        }

        return false;
    }

    private function getCachedResult(string $url)
    {
        if ($this->cache) {
            return $this->cache->get($this->getCacheKey($url));
        }

        return null;
    }

    private function cacheResult(string $url, Response $response)
    {
        if ($this->cache) {
            $this->cache->add($this->getCacheKey($url), $response, 360);
        }
    }

    private function getCacheKey(string $url)
    {
        return base64_encode($url);
    }

}