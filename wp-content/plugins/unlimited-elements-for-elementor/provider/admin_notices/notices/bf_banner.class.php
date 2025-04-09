<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class UCAdminNoticeBFBanner extends UCAdminNoticeAbstract{

	/**
	 * get the notice identifier
	 */
	public function getId(){
		
		return 'black_friday_24';
	}
	
	/**
	 * get the css
	 */
	private function getCss(){
		
		$css = "
			.uc-black-friday-banner{
				height:120px;
				background-color:black;
				color:white;
			}
			.uc-notice-link{
				height:100%;
				text-decoration:none;
			}
			.uc-notice-link:hover{
				text-decoration:none;
			}
			.uc-bf-banner__inside{
				display:flex;
				justify-content: space-between;
				height:100%;
				padding-left:20px;
				padding-right:20px;
			}
			
			.uc-bf-banner__inner{
				display:flex;
				justify-content: flex-start;
				align-items:center;
				flex-wrap:nowrap;
				gap:25px;
				height:100%;
			}
			
			.uc-bf-banner__text{
				font-size:22px;
				font-weight:bold;
				color:white;
				padding-right:10px;
				line-height:26px;
				text-align:center;
			}
			.uc-bf-banner__text span{
				color: #FF0085;
			}
			.uc-bf-banner__header{
				margin-left:30px;
			}
			
			.uc-bf-banner__button{
				background-color:#FF0085;
			    color: #fff;
			    padding: 10px 10px;	//top left
			    border: none;
			    border-radius: 50px;
			    font-size: 16px;
			    font-weight: bold;
			    text-align: center;
			    cursor: pointer;
			    transition: background 0.3s ease;			
			}
			
			.uc-bf-banner__small{
				width:100%;
				display:none;
			}
			
			@media(max-width:1410px) {
				
				.uc-bf-banner__inside{
					display:none !important;
				} 
				
				.uc-bf-banner__small{
					display:block;
				}
			
				.uc-black-friday-banner{
					height:auto;
				}
				
			}
			
			
		";
		
		return($css);
	}
	
	/**
	 * get inside html
	 */
	private function getInsideHTML(){ 
		
		$urlLogoWhite = GlobalsUC::$urlPluginImages."logo-unlimited-white-image.png";
		
		$urlHeaderImage = GlobalsUC::$urlPluginImages."banners/bf-banner-header.png"; 
		
		$urlSmallImage = GlobalsUC::$urlPluginImages."banners/bf-notification.jpg"; 
		
		$htmlLogoWhite = "<img class=\"uc-bf-banner__logo\" height='64' src=\"{$urlLogoWhite}\"> ";
		
		$htmlHeaderImage = "<img class=\"uc-bf-banner__header\" height='86' src=\"{$urlHeaderImage}\"> ";
		
		$htmlCounter = '
			<img class="uc-bf-banner__counter" src="' . esc_url(GlobalsUC::$urlPluginImages . '2mgwko.gif') . '" style="display:inline-block!important;width:100%!important;max-width:272px!important;" border="0" alt="countdownmail.com"/>
		';
		
		$htmlText = "<div class='uc-bf-banner__text'>Give Your Elementor <br> Website <span>Superpowers</span></div>";
		
		$htmlButton = "<div class='uc-bf-banner__button'>Get Deal Now!</div>";
		
		$html = "
			<div class='uc-bf-banner__inside'>
				 <div class='uc-bf-banner__inner uc-bf-banner__left-group'>
					{$htmlLogoWhite}
					{$htmlHeaderImage}
					{$htmlCounter}
					{$htmlText}
				</div>
				 <div class='uc-bf-banner__inner uc-bf-banner__right-group'>
					{$htmlButton}
				 </div>
			</div>
			<img class='uc-bf-banner__small' src='{$urlSmallImage}'>
		";
		
		return($html);
	}
	
	/**
	 * get the notice html
	 */
	public function getHtml(){
		
		$linkUrl = 'https://unlimited-elements.com/pricing/';
		$linkTarget = '_blank';
		//$imageUrl = GlobalsUC::$urlPluginImages . 'bannerimage.jpg';
		$imageUrl = 'http://via.placeholder.com/1360x110';
		
		$css = $this->getCss();
		
		$id = $this->getId();
		
		$insideHTML = $this->getInsideHTML();
		
		$builder = new UCAdminNoticeBannerBuilder($id);
		
		$builder = $this->initBuilder($builder);
				
		$builder->dismissible();
		$builder->theme(UCAdminNoticeBannerBuilder::THEME_DARK);
		$builder->link($linkUrl, $linkTarget);
		
		//$builder->image($imageUrl);
		
		$builder->setAddHTML($insideHTML);
		
		$builder->setAddClass("uc-black-friday-banner");
		$builder->setCss($css);
		
		
		$html = $builder->build();
		
		return $html;
	}

	/**
	 * check if the notice condition is allowed
	 */
	protected function isConditionAllowed(){
		
		//don't show inside plugin
		
		if(GlobalsUC::$isInsidePlugin == true){
						
			return(false);
		}
		
		return true;
	}
	
	
	/**
	 * initialize the notice
	 */
	protected function init(){
		
		$this->freeOnly();		
		$this->setLocation(self::LOCATION_EVERYWHERE);
		$this->setDuration(720); // 30 days in hours
	}
	
}
