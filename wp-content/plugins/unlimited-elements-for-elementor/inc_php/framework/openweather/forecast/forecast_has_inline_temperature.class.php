<?php

trait UEOpenWeatherAPIForecastHasInlineTemperature{

	/**
	 * Get the temperature.
	 *
	 * @return string
	 */
	public function getTemperature(){

		$temperature = $this->getTemperatureAttribute("temp");

		return $temperature;
	}

	/**
	 * Get the "feels like" temperature.
	 *
	 * @return string
	 */
	public function getFeelsLike(){

		$temperature = $this->getTemperatureAttribute("feels_like");

		return $temperature;
	}

	/**
	 * Get the temperature attribute.
	 */
	private function getTemperatureAttribute($key){

		$temperature = $this->getAttribute($key);
		$temperature = $this->formatTemperature($temperature);

		return $temperature;
	}

}
