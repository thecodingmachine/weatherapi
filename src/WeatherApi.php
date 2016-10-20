<?php
namespace TheCodingMachine\WeatherApi;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Http\Client\Exception;
use Http\Client\HttpClient;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

class WeatherApi
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var string
     */
    private $openWeatherMapApiKey;

    public function __construct(HttpClient $httpClient, LoggerInterface $logger, CacheItemPoolInterface $cache, string $openWeatherMapApiKey)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->openWeatherMapApiKey = $openWeatherMapApiKey;
    }

    public function getWeather(string $countryCode, string $cityName) : Weather
    {
        $cacheItem = $this->cache->getItem($cityName.','.$countryCode);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        // Let's build the query
        $uri = new Uri('http://api.openweathermap.org/data/2.5/weather');
        $uri = Uri::withQueryValue($uri, 'q', $cityName.','.$countryCode);
        $uri = Uri::withQueryValue($uri, 'appid', $this->openWeatherMapApiKey);
        $request = new Request('GET', $uri);

        // Let's run the query

        $response = $this->httpClient->sendRequest($request);

        $responseStr = (string) $response->getBody();

        $this->logger->debug("Answer received: ". $responseStr);

        $weatherArray = json_decode((string) $response->getBody(), true);

        $weather = new Weather($weatherArray);

        // Keep the weather in cache for 1 day.
        $cacheItem->set($weather)->expiresAfter(new \DateInterval('P1D'));

        return $weather;
    }
}
