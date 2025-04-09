<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;


class UniteCreatorRSS{
	
	public static $showDebug = false;
	
    private $rssAutoDetectKeys = array(
        'title_key' => 'title',
        'description_key' => 'description',
        'content_key' => 'content',
        'author_key' =>'dc_creator|author_name|author|dc-creator|credit',
        'link_key' => 'link|atom_link_href|link_href',
        'image_key' => 'media_thumbnail_url|media_group_media_content_url|media_content_media_thumbnail_url|media_content_url|image_url|enclosure_url',
        'date_key' => 'publish_date|published|publish_date_original'
    );
	
    /**
     * constructor
     */
    public function __construct(){
    	
    	if(self::$showDebug == false){
    		$rssDebug = HelperUC::hasPermissionsFromQuery("ucrssdebug");
    		
    		if($rssDebug == true)
    			self::$showDebug = true;
    	}
    	    	
    }
    
    /**
     * get rss fields
     */
    public function getRssFields($name = null) {
    	
        $fields = array(
            array(
                "type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
                "id" => "rss_url",
                "text" => __("RSS URL", "unlimited-elements-for-elementor"),
                "desc" => __("Enter some RSS service url. Example: https://wired.com/feed/rss", "unlimited-elements-for-elementor"),
                "placeholder" => "Example: https://wired.com/feed/rss",
                "label_block" => true,
            	"default" => "https://wired.com/feed/rss"
            ),
            array(
                "type" => UniteCreatorDialogParam::PARAM_RADIOBOOLEAN,
                "id" => "rss_show_filter_by_date",
                "text" => "Filter By Date",
                "default" => "false"
            ),
            array(
                "type" => UniteCreatorDialogParam::PARAM_DROPDOWN,
                "id" => "rss_filter_by_date_option",
                "text" => "Select value",
                "conditions" => array("rss_show_filter_by_date"=>"true"),
                "options" => array(
                    "all" => __("All", "unlimited-elements-for-elementor"),
                    "today" => __("Today", "unlimited-elements-for-elementor"),
                    "yesterday" => __("Yesterday", "unlimited-elements-for-elementor"),
                    "last_2_days" => __("Last 2 Days", "unlimited-elements-for-elementor"),
                    "last_3_days" => __("Last 3 Days", "unlimited-elements-for-elementor")
                ),
                "default" => "all"
            ),
            array(
                "type" => UniteCreatorDialogParam::PARAM_RADIOBOOLEAN,
                "id" => "rss_show_date_formatted",
                "text" => "Format Date",
                "default" => "true"
            ),
            array(
                "type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
                "id" => "rss_date_format",
                "conditions" => array("rss_show_date_formatted"=>"true"),
                "text" => "",
                "desc" => __("Specify the <a target='_blank' href='https://www.php.net/manual/en/datetime.format.php'>date format</a>, default is 'd/m/Y, H:i'", "unlimited-elements-for-elementor"),
                "placeholder" => "d/m/Y, H:i",
                "default" => "d/m/Y, H:i"
            ),
            array(
                "type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
                "id" => "rss_items_limit",
                "text" => __("Items Limit", "unlimited-elements-for-elementor"),
                "desc" => __("Optional. You can specify the maximum number of items: from 1 to 50., Use 0 for all", "unlimited-elements-for-elementor"),
                "placeholder" => "0",
                "default" => "0"
            ),
        );

        return $fields;
    }
	
	
	/**
	 * get Rss Feed data
	 */
	public function getRssFeedData($data, $arrValues, $name, $showDebug = false){
		
		if(self::$showDebug == true)
			$showDebug = true;
		
		$data[$name] = array();
		
		if($showDebug == true){
			dmp("---- the debug is ON, please turn it off before release --- ");
		}
		
		if(empty($arrValues)){
			
			if($showDebug)
				dmp("no data found");
			
			return($data);
		}
			
		$rss_url = UniteFunctionsUC::getVal($arrValues, $name.'_rss_url');

		if(empty($rss_url)){
			if($showDebug)
				dmp("no url found for rss");

			return($data);
		}
		
		$getUrlDebug = false;
		
		if(self::$showDebug == true)
			$getUrlDebug = true;
		
		if($showDebug){
			dmp("Get rss data from: $rss_url");			
		}
		try{
	        
			$rssContent = HelperUC::$operations->getUrlContents($rss_url, $getUrlDebug, true);
				        
		}catch(Exception $e){
			
            if($showDebug){
                dmp("Failed to fetch rss: ".$e->getMessage());
            }
            
            return($data);
		}
        
        
        //dmp($response);exit();
        
        if(empty($rssContent)) {
            if($showDebug)
                dmp("no content found for rss");

            return($data);
        }
		
        $arrData = UniteFunctionsUC::maybeXmlDecode($rssContent);
		
        if(empty($arrData)) {
            if($showDebug)
                dmp('no data found for rss');

            return($data);
        }

        if($showDebug == true && is_array($arrData)) {
        	
            dmp("Original rss data");
			            
			HelperHtmlUC::putHtmlDataDebugBox($arrData);						
        }

        if(is_array($arrData) == false){

            if($showDebug == true){
                dmp("No RSS data found. The input is: ");
                echo "<div style='background-color:lightgray'>";
                dmp(htmlspecialchars($rssContent));
                echo "</div>";
            }
			
            return($data);
        }

        if(!empty($arrData)) {
            (bool) $showDateFormated = UniteFunctionsUC::getVal($arrValues, $name."_rss_show_date_formatted");
            $dateFormat = null;
            if ($showDateFormated) {
                $dateFormat = UniteFunctionsUC::getVal($arrValues, $name."_rss_date_format");
                if (empty($dateFormat)) {
                    $dateFormat = 'd/m/Y, H:i';
                }
            }

            $arrData = $this->simplifyRssDataArray($arrData, $dateFormat);
			
            // trim by date limits
            (bool) $showFilterByDate = UniteFunctionsUC::getVal($arrValues, $name."_rss_show_filter_by_date");
            if($showFilterByDate && !empty($arrData)) {
            	
                $filterByDateOption = UniteFunctionsUC::getVal($arrValues, $name . "_rss_filter_by_date_option");
                                
                if (!empty($filterByDateOption) && $filterByDateOption != 'all') {
                    $arrData = $this->limitArrayByPublishDate($arrData, $filterByDateOption);
                }
            }
	
            // trim by items limit
            (int) $dataItemsLimit = UniteFunctionsUC::getVal($arrValues, $name."_rss_items_limit");
            if($dataItemsLimit > 0 && !empty($arrData))
                $arrData = array_slice($arrData, 0, $dataItemsLimit, true);

            $data[$name] = $arrData;
        }
		
        
		return $data;
	}
	
	/**
	 * get the date
	 */
	private function getDateString($arrItem){
		
		if (array_key_exists('publish_date_original', $arrItem))
			return($arrItem["publish_date_original"]);
		
		if (array_key_exists('publish_date', $arrItem))
			return($arrItem["publish_date"]);
		
		return(null);
	}
	
	/**
	 * limit the array (filter) by publish date
	 */
	private function limitArrayByPublishDate($arrData, $option){
		
		// calculate period
		$from_time = 0;
		$to_time = 0;

		switch($option){
			case 'today':
				$from_time = strtotime('today midnight');
				$to_time = strtotime('tomorrow midnight');
				break;
			case 'yesterday':
				$from_time = strtotime('yesterday midnight');
				$to_time = strtotime('today midnight');
				break;
			case 'last_2_days':
				$from_time = strtotime("-2 days");;
				$to_time = time();
				break;
			case 'last_3_days':
				$from_time = strtotime("-3 days");
				$to_time = time();
				break;
			default:
				break;
		}
		
		if ($from_time > 0 && $to_time > 0) {
			$newArray = array();
 			
			foreach ($arrData as $key => $value){
				
				if (array_key_exists('publish_date', $value)) {
					
					$dateString = $this->getDateString($value);
					
					$timestamp = UniteFunctionsUC::date2Timestamp($dateString);
					
					if ($timestamp >= $from_time && $timestamp < $to_time) {
						$newArray[] = $arrData[$key];
					}
				} else {
					$newArray[] = $arrData[$key];
				}
			}

			return $newArray;
		}

		return $arrData;
	}
	
	
	/**
	 * modify rss array to simplify the use
	 */
	private function simplifyRssDataArray($arrRss, $dateFormat = null){
		
		if($items = $this->findRssItems($arrRss)) {
			$items = $this->createNiceKeys($items, $dateFormat);

            if (!empty($items))
                $arrRss = $items;
            
		} else
            $arrRss = $this->createNiceKeys($arrRss, $dateFormat);
		
        if(empty($arrRss))
        	return($arrRss);
        
        //detect image url
        
        $firstRssElement = UniteFunctionsUC::getArrFirstValue($arrRss);
		
        $hasImageKey = $this->hasImageKey($firstRssElement);
        
        if($hasImageKey == false)
        	return($arrRss);
        	
        foreach ($arrRss as $rssKey => $rssItem) {
        	
             $imageLink = $this->getFirstImageLinkFromContent($rssItem);

           	 if(!empty($imageLink))
                  $arrRss[$rssKey]['image_url'] = $imageLink;
             
		}

		return($arrRss);
	}

	/**
	 * create nice keys
	 */
	private function createNiceKeys($arrRss, $dateFormat, $prefix = '') {
		
		$niceArr = array();

		foreach ($arrRss as $key => $value) {
            $key = strtolower($key);

			// Replace colons with underscores and append to prefix
			$newKey = $prefix . ($prefix ? '_' : '') . str_replace(':', '_', $key);

			if (is_array($value)) {
				// If the key is numeric, keep it as part of the array
				if (is_numeric($key)) {
					$niceArr[$key] = $this->createNiceKeys($value, $dateFormat);
				} else {
					$niceArr = array_merge($niceArr, $this->createNiceKeys($value, $dateFormat, $newKey));
				}
			} else {
				if (is_numeric($key)) {
					$niceArr[$prefix][$key] = $value;
				} else {
					$niceArr[$newKey] = $value;

					if (!empty($dateFormat) && ($newKey == 'publish_date' || $newKey == 'published')) {
						$date_time = UniteFunctionsUC::date2Timestamp($value);

						if (!empty($date_time)){
							
							$niceArr['publish_date_original'] = $value;
							$niceArr[$key] = s_date($dateFormat, $date_time);							
						}
					}
				}
			}
		}

		return $niceArr;
	}

	
	/**
	 * create data for rss
	 */
	private function createDate($arrRss, $dateFormat) {
				
		$niceArr = array();

		foreach ($arrRss as $key => $value) {
			if (is_array($value)) {
				$niceArr[$key] = $this->createDate($value, $dateFormat);
			} else {
				$timestamp = UniteFunctionsUC::date2Timestamp($value);
				if (!empty($timestamp)) {
					$formatedDate = s_date($dateFormat, $timestamp);
					if (empty($formatedDate)) {
						$formatedDate = s_date('d/m/Y H:i:s', $timestamp);
					}

					$niceArr[$key] = $formatedDate;
				} else {
					$niceArr[$key] = $value;
				}
			}
		}

		return $niceArr;
	}
	
	/**
	 * find rss items in rss array
	 */
	private function findRssItems($arrRss) {
		if (array_key_exists('item', $arrRss)) {
			return $arrRss['item'];
		} elseif (array_key_exists('entry', $arrRss)) {
			return $arrRss['entry'];
		} else {
			foreach ($arrRss as $value) {
				if (is_array($value)) {
					if ($items = $this->findRssItems($value)) {
						return $items;
					}
				}
			}
		}

		return false;
	}

    /**
     * Filter widget params to get only a specific sublist by catid
     */
    public function filterByTypeAndCatId($array, $type, $catIdValue) {
        
    	$result = array();

        foreach ($array as $key => $subArray) {
            if ($subArray['type'] != $type) {
                continue;
            }

            if ($subArray['__attr_catid__'] != $catIdValue) {
                continue;
            }

            $result[] = $subArray;
        }

        return $result;
    }

    /**
     * Get all rss widget keys, if any key is empty use default key for it
     */
    public function getRssFeedKeys(UniteCreatorAddonWork $addon, $data){
    	
        $params = $addon->getParams();
		
        $keys = array();
		
        $rssFeedArr = UniteFunctionsUC::getVal($data, "rss_feed");
        if (empty($rssFeedArr)) {
            return;
        }

        $firstRssElement = UniteFunctionsUC::getArrFirstValue($rssFeedArr);
        if (empty($firstRssElement)) {
            return;
        }

        $catValue = $addon->getParamByName("auto_detect_keys");
        if (empty($catValue)) {
            return;
        }
		
        $rssKeyArr = $this->filterByTypeAndCatId($params, UniteCreatorDialogParam::PARAM_TEXTFIELD, $catValue['__attr_catid__']);
		
        
        $isAutoDetect = UniteFunctionsUC::getVal($data, "auto_detect_keys");
        $isAutoDetect = UniteFunctionsUC::strToBool($isAutoDetect);
        
        $isShowDebug = UniteFunctionsUC::getVal($data, "show_debug");
        $isShowDebug = UniteFunctionsUC::strToBool($isShowDebug);
		
        if ($isShowDebug) {
            dmp('--- Fields Keys ---');
        }

        foreach ($rssKeyArr as $keyValue) {
        	
            $keyName = $keyValue['name'];
            $defaultKey = null;

            if ($isAutoDetect == false) {
                $defaultKey = UniteFunctionsUC::getVal($keyValue, 'value');
            }

            $isAutoKey = false;
            
            if (!empty($defaultKey)) {
            	
                $keys[$keyName] = $defaultKey;
            	                
            } else {
            	            	
                $autoKey = $this->autoDetectKeys($keyName, $firstRssElement);
				
                if (!empty($autoKey)) {
            		            		
                	$isAutoKey = true;
                	
                    $keys[$keyName] = $autoKey;

                } else if($keyName == 'image_key') {
                    
                	$searchImageKey = $this->searchForImageKey($firstRssElement);

                    if (!empty($searchImageKey))
                        $keys[$keyName] = $searchImageKey;
                       
                }
            }

            if ($isShowDebug == true) {
            	
            	$title = UniteFunctionsUC::getVal($keyValue, "title");
            	$key = UniteFunctionsUC::getVal($keys, $keyName);
            	
            	$autoText = $isAutoKey?" <span style='color:grey;'>[auto detect]</span>":"";
            	
                if(!array_key_exists($keyName, $keys))
                    dmp("{$title}: <span style='color:darkred;'>not detected, please select a custom one</span>");
                 else if (array_key_exists($keys[$keyName], $firstRssElement))
                    dmp("{$title}: <b>$key</b> {$autoText}");
                 else 
                    dmp("{$title}: <span style='color:darkred;'> key not found, please select another one</span>");
                
            }
            
        }

        if ($isShowDebug) {
            dmp('--- first RSS item structure ---');
            $firstRssShow = UniteFunctionsUC::modifyDataArrayForShow($firstRssElement);
            dmp($firstRssShow);
        }

        
        //set original date key
        
        if(isset($firstRssElement["publish_date_original"]))
        	$keys["date_key_original"] = "publish_date_original";
        else
        if(isset($firstRssElement["publish"]))
        	$keys["date_key_original"] = "publish";
        else
        	$keys["date_key_original"] = "publish_date";
        
        
        return $keys;
    }

    /**
     * Auto detect keys for rss widget
     */
    private function autoDetectKeys($keyName, $rssElement) {
    	
        if(array_key_exists($keyName, $this->rssAutoDetectKeys) == false)
        	return(null);
        	
            $autoDetectKey = $this->rssAutoDetectKeys[$keyName];
				
            $arrKeys = explode('|', $autoDetectKey);

            foreach ($arrKeys as $arrKey) {
            	
                $foundValue = UniteFunctionsUC::getVal($rssElement, $arrKey);

                if (!empty($foundValue)) {
                    return $arrKey;
                }
            }
            
            return(null);
            
    }

    /**
     * Search for image key in an array of values
     */
    public function searchForImageKey($rssItem) {
    	
        $pattern = '/^https?:\/\/.*\.(jpg|jpeg|png|gif|bmp|webp|svg)$/i';

        foreach ($rssItem as $key => $value) {
            if (is_array($value)) {
                $newKey = $this->searchForImageKey($value);
                if (!empty($newKey)) {
                    return $key . "." . $newKey;
                }
            } else if(preg_match($pattern, $value)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Check if exist any possible image keys or a key with image link
     */
    private function hasImageKey($rssItem) {
    	
        $possibleImageKeys = UniteFunctionsUC::getVal($this->rssAutoDetectKeys, "image_key");    
		
        if(empty($possibleImageKeys))
        	return(false);
        
        $rssKeys = explode('|', $possibleImageKeys);

        if(empty($rssKeys) == false)
        	return(false);
        
        foreach ($rssKeys as $rssKey) {
            if(array_key_exists($rssKey, $rssItem)) {
                return true;
            }
        }
		
        $otherImageKey = $this->searchForImageKey($rssItem);
        
        if (!empty($otherImageKey)) {
            return true;
        }

        return false;
    }

    /**
     * Find first image in possible content keys or any other image
     */
    private function getFirstImageLinkFromContent($rssItem) {
    	
        $possibleContentKeys = $this->rssAutoDetectKeys['content_key'];
        $possibleDescKeys = $this->rssAutoDetectKeys['description_key'];

        $contentKeys = explode('|', $possibleContentKeys);
        $descKeys = explode('|', $possibleDescKeys);
        $rssKeys = array_merge($contentKeys, $descKeys);

        $pattern = '/<img[^>]+src=["\']([^"\']+)["\']/i';

        foreach ($rssKeys as $rssKey) {
            if(array_key_exists($rssKey, $rssItem)) {
                if (!is_array($rssItem[$rssKey])) {
                    if (preg_match($pattern, $rssItem[$rssKey], $matches)) {
                        if (!empty($matches[1])) {
                            return $matches[1];
                        }
                    }
                } else {
                    $firstImage = $this->getFirstImageLinkFromContent($rssItem[$rssKey]);

                    if (!empty($firstImage)) {
                        return $firstImage;
                    }
                }
            }
        }

        return null;
    }
}
