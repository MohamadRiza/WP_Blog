<?php

abstract class UEOpenWeatherAPIForecastAbstract extends UEOpenWeatherAPIModel{

	const UNITS_STANDARD = "standard";
	const UNITS_METRIC = "metric";
	const UNITS_IMPERIAL = "imperial";

	/**
	 * Get the identifier.
	 *
	 * @return int
	 */
	public function getId(){

		$id = $this->getTime();

		return $id;
	}

	
	/**
	 * Get the date.
	 * @param string $format
	 * @return string
	 */
	public function getDate($format){
		
		$time = $this->getTime();
		
		$date = $this->formatTime($time, $format);

		return $date;
	}

	/**
	 * Get the description.
	 *
	 * @return string
	 */
	public function getDescription(){

		$description = $this->getWeather("description");

		return $description;
	}

	/**
	 * Get the state.
	 *
	 * @return string
	 */
	public function getState(){

		$state = $this->getWeather("main");

		return $state;
	}

	/**
	 * Get the icon name.
	 *
	 * @return string
	 */
	public function getIconName(){

		$name = $this->getWeather("icon");

		return $name;
	}

	/**
	 * Get the icon URL.
	 *
	 * @return string
	 */
	public function getIconUrl(){

		$name = $this->getIconName();
		$url = "https://openweathermap.org/img/wn/" . $name . "@2x.png";

		return $url;
	}

	/**
	 * Get the pressure.
	 *
	 * @return string
	 */
	public function getPressure(){

		$pressure = $this->getAttribute("pressure");
		// translators: %s is a string
		$pressure = sprintf(__("%s hPa", "unlimited-elements-for-elementor"), $pressure);

		return $pressure;
	}

	/**
	 * Get the humidity.
	 *
	 * @return string
	 */
	public function getHumidity(){

		$humidity = $this->getAttribute("humidity");
		$humidity = $this->formatPercentage($humidity);

		return $humidity;
	}

	/**
	 * Get the cloudiness.
	 *
	 * @return string
	 */
	public function getCloudiness(){

		$cloudiness = $this->getAttribute("clouds");
		$cloudiness = $this->formatPercentage($cloudiness);

		return $cloudiness;
	}

	/**
	 * Get the rain.
	 *
	 * @return string
	 */
	public function getRain(){

		$rain = $this->getAttribute("rain", 0);
		$rain = $this->formatPrecipitation($rain);

		return $rain;
	}

	/**
	 * Get the snow.
	 *
	 * @return string
	 */
	public function getSnow(){

		$snow = $this->getAttribute("snow", 0);
		$snow = $this->formatPrecipitation($snow);

		return $snow;
	}

	/**
	 * Get the UVI.
	 *
	 * @return float
	 */
	public function getUvi(){

		$uvi = $this->getAttribute("uvi");

		return $uvi;
	}

	/**
	 * Get the wind speed.
	 *
	 * @return string
	 */
	public function getWindSpeed(){

		$speed = $this->getAttribute("wind_speed");
		$speed = $this->formatSpeed($speed);

		return $speed;
	}

	/**
	 * Get the wind degrees.
	 *
	 * @return int
	 */
	public function getWindDegrees(){

		$degrees = $this->getAttribute("wind_deg");

		return $degrees;
	}

	/**
	 * Get the wind gust.
	 *
	 * @return string
	 */
	public function getWindGust(){

		$gust = $this->getAttribute("wind_gust");
		$gust = $this->formatSpeed($gust);

		return $gust;
	}

	/**
	 * Get the weather.
	 *
	 * @param string $key
	 * @param mixed $fallback
	 *
	 * @return string
	 */
	protected function getWeather($key, $fallback = null){

		$weather = $this->getAttribute("weather");
		$weather = UniteFunctionsUC::getVal($weather, 0, array()); // the first weather condition is primary
		$value = UniteFunctionsUC::getVal($weather, $key, $fallback);

		return $value;
	}

	/**
	 * Get the time.
	 *
	 * @return int
	 */
	protected function getTime(){

		$time = $this->getAttribute("dt");

		return $time;
	}

	/**
	 * Get the units.
	 *
	 * @return string
	 */
	protected function getUnits(){

		$units = $this->getParameter("units");

		return $units;
	}

	/**
	 * Format the percentage.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	protected function formatPercentage($value){
		// translators: %s is a string
		return sprintf(__("%s%%", "unlimited-elements-for-elementor"), $value);
	}

	/**
	 * Format the precipitation.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	protected function formatPrecipitation($value){

		if(is_array($value))
			$value = UniteFunctionsUC::getArrFirstValue($value);

		if(is_array($value))
			$value = 0;
		// translators: %s is a string
		return sprintf(__("%s mm", "unlimited-elements-for-elementor"), $value);
	}

	/**
	 * Format the speed.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	protected function formatSpeed($value){

		switch($this->getUnits()){
			case self::UNITS_IMPERIAL:
				// translators: %s is a string
				return sprintf(__("%s mph", "unlimited-elements-for-elementor"), $value);
			default:
				// translators: %s is a string
				return sprintf(__("%s m/s", "unlimited-elements-for-elementor"), $value);
		}
	}

	/**
	 * Format the temperature.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	protected function formatTemperature($value){

		if(is_numeric($value) === true)
			$value = round($value);

		// translators: %s is a string
		return sprintf(__("%s°", "unlimited-elements-for-elementor"), $value);

		//switch($this->getUnits()){
		//	case self::UNITS_METRIC:
		//		return sprintf(__("%s°C", "unlimited-elements-for-elementor"), $value);
		//	case self::UNITS_IMPERIAL:
		//		return sprintf(__("%s°F", "unlimited-elements-for-elementor"), $value);
		//	default:
		//		return sprintf(__("%sK", "unlimited-elements-for-elementor"), $value);
		//}
	}
	
	
	/**
	 * translate day of week
	 */
	protected function translateDayOfWeek($locale, $date, $timezone, $format){
		
		$isIntlLoaded = extension_loaded('intl');
		if($isIntlLoaded == false)
			return(null);
		
		if(class_exists("IntlDateFormatter") == false)
			return(null);
			
		$string = 'EEEE';
		
		if($format == "D")
			$string = 'EEE';
		
		
		$formatter = new IntlDateFormatter(
		    $locale,
		    IntlDateFormatter::NONE, // No date format
		    IntlDateFormatter::NONE, // No time format
		    $timezone,         // Timezone
		    IntlDateFormatter::GREGORIAN, // Calendar type
		    $string // Day of the week format
		);
		
		
		$translatedDayOfWeek = $formatter->format($date);
		
		
		return($translatedDayOfWeek);
	}
	
	
	
	/**
	 * Format the time.
	 *
	 * @param int $timestamp
	 * @param string $format
	 *
	 * @return string
	 */
	protected function formatTime($timestamp, $format){

		$timezone = $this->getParameter("timezone");
		
		$dateTimezone = new DateTimeZone($timezone);

		$date = new DateTime();
		$date->setTimezone($dateTimezone);
		$date->setTimestamp($timestamp);
		
		$strTime = $date->format($format);
		
		$locale = $this->getParameter("locale");
				
		if($format == "D" || $format == "l" && !empty($locale) && $locale !== "en"){
			
			$strTranslated = $this->translateDayOfWeek($locale, $date, $timezone, $format);
			
			if(!empty($strTranslated))
				$strTime = $strTranslated;
			
		}
		
		return $strTime;
	}

}
