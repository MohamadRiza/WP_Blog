<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class UCAdminNoticeBannerBuilder extends UCAdminNoticeBuilderAbstract{

	const THEME_DARK = 'dark';
	const THEME_LIGHT = 'light';

	private $theme = self::THEME_LIGHT;
	private $linkUrl;
	private $linkTarget;
	private $imageUrl;
	private $addClass = "";
	private $css = "";
	private $addHtml = "";
	
	
	/**
	 * set the notice theme
	 */
	public function theme($theme){

		$this->theme = $theme;

		return $this;
	}
	
	
	/**
	 * set add class
	 */
	public function setAddClass($class){
		
		$this->addClass = $class;
	}
	

	/**
	 * set the notice link URL
	 */
	public function link($url, $target = ''){

		$this->linkUrl = $url;
		$this->linkTarget = $target;

		return $this;
	}

	/**
	 * set the notice image URL
	 */
	public function image($url){

		$this->imageUrl = $url;

		return $this;
	}
	
	
	/**
	 * get css
	 */
	public function setCss($css){
		
		$this->css = $css;
	}
	
	/**
	 * set add html
	 */
	public function setAddHtml($html){
		
		$this->addHtml = $html;
	}
	
	
	/**
	 * get the notice html
	 */
	public function build(){

		$class = implode(' ', array(
			'notice',
			'uc-admin-notice',
			'uc-admin-notice--banner',
			'uc-admin-notice--theme-' . $this->theme,
			'uc-admin-notice--' . $this->getId(),
		));
		
		if(!empty($this->addClass))
			$class .= " ".$this->addClass;
		
		$html = "";
		
		//add css
		if(!empty($this->css))
			$html .= "
			<style>
				".$this->css."
			</style>
		";
			
		$html .= '<div class="' . esc_attr($class) . '">';
		$html .= '<a class="uc-notice-link" href="' . esc_url($this->linkUrl) . '" target="' . esc_attr($this->linkTarget) . '" >';
		$html .= $this->getImageHtml();
		$html .= $this->getAddHTML();
		$html .= '</a>';
		$html .= $this->getDebugHtml();
		$html .= $this->getDismissHtml();
		$html .= '</div>';

		return $html;
	}
	
	/**
	 * get custom html
	 */
	private function getAddHTML(){
		
		if(empty($this->addHtml))
			return("");
			
		return($this->addHtml);
	}


	/**
	 * get the image html
	 */
	private function getImageHtml(){
		
		if(empty($this->imageUrl))
			return '';

		return '<img class="uc-notice-image" src="' . esc_attr($this->imageUrl) . '" alt="" />';
	}

}
