<?php


namespace TheCodingMachine\WeatherApi;


class Weather
{
    /**
     * @var array|mixed
     */
    private $weatherArray;

    /**
     * Weather constructor.
     * @param mixed $weatherArray
     */
    public function __construct(array $weatherArray)
    {
        $this->weatherArray = $weatherArray;
    }

    public function getMainWeather(): string
    {
        return $this->weatherArray['weather'][0]['main'];
    }

    public function getDescription(): string
    {
        return $this->weatherArray['weather'][0]['description'];
    }

    public function getTemperatureInCelsius() : float
    {
        return round(($this->weatherArray['main']['temp'] - 273.15));
    }
}
