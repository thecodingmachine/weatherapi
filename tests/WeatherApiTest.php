<?php


namespace TheCodingMachine\WeatherApi;

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Monolog\Logger;
use Stash\Driver\FileSystem;
use Stash\Pool;

class WeatherApiTest extends \PHPUnit_Framework_TestCase
{
    public function testApi()
    {
        global $openweathermapapikey;

        $guzzle = new GuzzleClient([]);
        $httplugAdapter = new GuzzleAdapter($guzzle);

        $logger = new Logger('my_logger');

        $driver = new FileSystem();
        $cache = new Pool($driver);
        $weatherApi = new WeatherApi($httplugAdapter, $logger, $cache, $openweathermapapikey);

        $weather = $weatherApi->getWeather('fr', 'Paris');

        $this->assertInstanceOf(Weather::class, $weather);
        $this->assertNotEmpty($weather->getMainWeather());
        var_dump($weather);
    }
}
