<?php

/**
 * https://developers.google.com/calendar/api/v3/reference
 */
class UEGoogleAPICalendarService extends UEGoogleAPIClient{

	
	/**
	 * convert times
	 */
	private function convertItemTimes($arrTime, $targetTimezone){
		
		if(empty($arrTime))
			return($arrTime);
		
		if(class_exists("DateTimeZone") == false)
			return($arrTime);
			
		$time = UniteFunctionsUC::getVal($arrTime, "dateTime");
		
		if(empty($time))
			return($arrTime);
		
		$sourceTimezone = UniteFunctionsUC::getVal($arrTime, "timeZone");
		
		if(empty($sourceTimezone))
			return($arrTime);
		
		if($sourceTimezone == $targetTimezone)
			return($arrTime);
				
		try{
			
			$objSourceTimezone = new DateTimeZone($sourceTimezone);
		
		}catch(Exception $e){
					
			$message = $e->getMessage();
			
			dmp("Init source timezone error ($sourceTimezone): ".$message);
			dmp("target timezone: $targetTimezone");
			dmp($arrTime);
		}
		
		
		try{
			$objTargetTimezone = new DateTimeZone($targetTimezone);
		}catch(Exception $e){
					
			$message = $e->getMessage();
			
			dmp("Init target timezone error ($targetTimezone): ".$message);
			dmp("source timezone: $targetTimezone");
		}
		
		try{
			
			$objDate = new DateTime($time, $objSourceTimezone);
			
			$objDate->setTimezone($objTargetTimezone);
			
			$strDate = $objDate->format('Y-m-d\TH:i:s');
			
			$arrTime["dateTime"] = $strDate;
		
		}catch(Exception $e){
					
			$message = $e->getMessage();
			
			dmp("Convert time error: ".$message);
		}
		
		return($arrTime);
	}
	
	
	/**
	 * convert timezones, from given to target timezone
	 */
	private function convertTimezones($response, $targetTimezone){
		
		if(empty($targetTimezone))
			return($response);
		
		$items = UniteFunctionsUC::getVal($response, "items");
		
		if(empty($items))
			return($response);
		
		foreach($items as $index=>$item){
			
			$item["start"] = $this->convertItemTimes($item["start"],$targetTimezone);
			$item["end"] = $this->convertItemTimes($item["end"],$targetTimezone);
			
			$items[$index] = $item;
		}
		
		$response["items"] = $items;
		
		return($response);
	}
	
	
	/**
	 * Get the events.
	 *
	 * @param string $calendarId
	 * @param array $params
	 *
	 * @return UEGoogleAPICalendarEvent[]
	 */
	public function getEvents($calendarId, $params = array(),$timezone = null){
		
		$calendarId = urlencode($calendarId);
		
		if(empty($timezone))
			$timezone = wp_timezone_string();
		
		//$params["timeZone"] = $timezone;
		
		$response = $this->get("/calendars/$calendarId/events", $params);
		
		$response = $this->convertTimezones($response, $timezone);
		
		$response = UEGoogleAPICalendarEvent::transformAll($response["items"]);
				
		return $response;
	}
	
	
	/**
	 * Get the base URL for the API.
	 *
	 * @return string
	 */
	protected function getBaseUrl(){

		return "https://www.googleapis.com/calendar/v3";
	}

}
