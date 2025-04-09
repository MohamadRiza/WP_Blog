<?php

class UEOpenWeatherAPIForecastDaily extends UEOpenWeatherAPIForecastAbstract{

	use UEOpenWeatherAPIForecastHasSunTime;

	/**
	 * Get the minimum temperature.
	 *
	 * @return string
	 */
	public function getMinTemperature(){

		$temperature = $this->getTemperature("min");

		return $temperature;
	}

	/**
	 * Get the maximum temperature.
	 *
	 * @return string
	 */
	public function getMaxTemperature(){

		$temperature = $this->getTemperature("max");

		return $temperature;
	}

	/**
	 * Get the morning temperature.
	 *
	 * @return string
	 */
	public function getMorningTemperature(){

		$temperature = $this->getTemperature("morn");

		return $temperature;
	}

	/**
	 * Get the day temperature.
	 *
	 * @return string
	 */
	public function getDayTemperature(){

		$temperature = $this->getTemperature("day");

		return $temperature;
	}

	/**
	 * Get the evening temperature.
	 *
	 * @return string
	 */
	public function getEveningTemperature(){

		$temperature = $this->getTemperature("eve");

		return $temperature;
	}

	/**
	 * Get the night temperature.
	 *
	 * @return string
	 */
	public function getNightTemperature(){

		$temperature = $this->getTemperature("night");

		return $temperature;
	}

	/**
	 * Get the morning "feels like" temperature.
	 *
	 * @return string
	 */
	public function getMorningFeelsLike(){

		$temperature = $this->getFeelsLike("morn");

		return $temperature;
	}

	/**
	 * Get the day "feels like" temperature.
	 *
	 * @return string
	 */
	public function getDayFeelsLike(){

		$temperature = $this->getFeelsLike("day");

		return $temperature;
	}

	/**
	 * Get the evening "feels like" temperature.
	 *
	 * @return string
	 */
	public function getEveningFeelsLike(){

		$temperature = $this->getFeelsLike("eve");

		return $temperature;
	}

	/**
	 * Get the night "feels like" temperature.
	 *
	 * @return string
	 */
	public function getNightFeelsLike(){

		$temperature = $this->getFeelsLike("night");

		return $temperature;
	}

	/**
	 * Get the temperature.
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	private function getTemperature($key){

		$temperature = $this->getAttribute("temp", array());
		$temperature = UniteFunctionsUC::getVal($temperature, $key, 0);
		$temperature = $this->formatTemperature($temperature);

		return $temperature;
	}

	/**
	 * Get the "feels like" temperature.
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	private function getFeelsLike($key){

		$temperature = $this->getAttribute("feels_like", array());
		$temperature = UniteFunctionsUC::getVal($temperature, $key, 0);
		$temperature = $this->formatTemperature($temperature);

		return $temperature;
	}

}
