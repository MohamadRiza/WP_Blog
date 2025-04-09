<?php

trait UEOpenWeatherAPIForecastHasSunTime{

	/**
	 * Get the sunrise time.
	 *
	 * @return string
	 */
	public function getSunrise(){

		$sunrise = $this->getAttribute("sunrise");
		$sunrise = $this->formatTime($sunrise, "H:i");

		return $sunrise;
	}

	/**
	 * Get the sunset time.
	 *
	 * @return string
	 */
	public function getSunset(){

		$sunset = $this->getAttribute("sunset");
		$sunset = $this->formatTime($sunset, "H:i");

		return $sunset;
	}

}
