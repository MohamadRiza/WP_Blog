<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class UniteCreatorGutenbergIntegrate{

	private static $initialized = false;
	private static $instance = null;
	private static $cacheBlocksRegular = array();
	private static $cacheBlocksBG = array();
	private $categoryName;
	private $categoryNameBG;
	private $categorySlug;
	private $categorySlugBG;
	
	
	
	/**
	 * Create a new instance.
	 */
	public function __construct(){

		$this->categorySlug = GlobalsUnlimitedElements::PLUGIN_NAME;
		$this->categoryName = GlobalsUnlimitedElements::PLUGIN_TITLE_GUTENBERG;

		$this->categorySlugBG = GlobalsUnlimitedElements::PLUGIN_NAME."_backgrounds";
		$this->categoryNameBG = GlobalsUnlimitedElements::PLUGIN_TITLE_GUTENBERG." Backgrounds";
		
	}

	/**
	 * Get the class instance.
	 *
	 * @return self
	 */
	public static function getInstance(){

		if(self::$instance === null)
			self::$instance = new self();

		return self::$instance;
	}

	/**
	 * Initialize the integration.
	 *
	 * @return void
	 */
	public function init(){
		
		$shouldInitialize = $this->shouldInitialize();
				
		if($shouldInitialize === false)
			return;

		$this->registerHooks();
		
		self::$initialized = true;
	}

	/**
	 * Determine if the integration should be initialized.
	 *
	 * @return bool
	 */
	private function shouldInitialize(){
		
		if(self::$initialized === true)
			return false;

		if(GlobalsUnlimitedElements::$enableGutenbergSupport === false)
			return false;
	
		if(function_exists('register_block_type') === false)
			return false;
		
		//if inside ajax action output data - don't register any blocks
		$isAjaxAction = HelperUC::isAjaxAction();
				
		if($isAjaxAction == true)
			return(false);
		
		return true;
	}

	/**
	 * Register the integration hooks.
	 *
	 * @return void
	 */
	private function registerHooks(){
		
		if(GlobalsUC::$is_admin == false)		
			UniteProviderFunctionsUC::addAction('init', array($this, 'registerBlocks'));
		else
			UniteProviderFunctionsUC::addAction('enqueue_block_editor_assets', array($this, 'registerBlocks'));
		
		UniteProviderFunctionsUC::addFilter('block_categories_all', array($this, 'registerCategories'));
				
		UniteProviderFunctionsUC::addAction('enqueue_block_editor_assets', array($this, 'enqueueAssets'));
		
	}
	
	
	/**
	 * Register the Gutenberg categories.
	 *
	 * @param array $categories
	 *
	 * @return array
	 */
	public function registerCategories($categories){
				
		$categories[] = array(
			'slug' => $this->categorySlug,
			'title' => $this->categoryName
		);
		
		$categories[] = array(
			'slug' => $this->categorySlugBG,
			'title' => $this->categoryNameBG
		);
		
		return $categories;
	}

	/**
	 * Register the Gutenberg blocks.
	 *
	 * @return void
	 */
	public function registerBlocks(){
		
		$blocks = $this->getBlocks();

		foreach($blocks as $name => $block){
			register_block_type($name, $block);
		}
		
		//register backgrouns
		
		$backgrounds = $this->getBlocks(true);
		
		foreach($backgrounds as $name => $block){
			register_block_type($name, $block);
		}
		
	}
	
	/**
	 * get all blocks - regular and backgrounds
	 */
	public function getAllBlocks(){
		
		$arrBlocks = $this->getBlocks();
		$arrBlocksBG = $this->getBlocks(true);
		
		$arrBlocks = array_merge($arrBlocks,$arrBlocksBG);
		
		return($arrBlocks);
	}
	

	/**
	 * Render the Gutenberg block on the frontend.
	 *
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function renderBlock($attributes){
		
		GlobalsProviderUC::$renderPlatform = GlobalsProviderUC::RENDER_PLATFORM_GUTENBERG;
		
		$data = array(
			'id' => $attributes['_id'],
			'root_id' => $attributes['_rootId'],
			'settings' => json_decode($attributes['data'], true),
			'selectors' => true,
		);
		
		$addonsManager = new UniteCreatorAddons();
		$addonData = $addonsManager->getAddonOutputData($data);
		
		$conflictingStyles = array('font-awesome');
		$conflictingScripts = array();
		
		foreach($addonData['includes'] as $include){
			$handle = UniteFunctionsUC::getVal($include, 'handle');
			$type = UniteFunctionsUC::getVal($include, 'type');
			$url = UniteFunctionsUC::getVal($include, 'url');

			if($type === 'css'){
				if(in_array($handle, $conflictingStyles) === true)
					wp_deregister_style($handle);

				HelperUC::addStyleAbsoluteUrl($url, $handle);
			}else{
				if(in_array($handle, $conflictingScripts) === true)
					wp_deregister_script($handle);

				HelperUC::addScriptAbsoluteUrl($url, $handle);
			}
		}

		$html = UniteFunctionsUC::getVal($addonData, "html");
		
		return $html;
	}

	/**
	 * Enqueue the Gutenberg assets.
	 *
	 * @return void
	 */
	public function enqueueAssets(){
				
		UniteCreatorAdmin::setView('testaddonnew');
		UniteCreatorAdmin::onAddScripts();
		
		$handle = 'uc_gutenberg_integrate';
		$styleUrl = GlobalsUnlimitedElements::$urlPluginGutenberg. 'assets/gutenberg_integrate.css';
		$scriptUrl = GlobalsUnlimitedElements::$urlPluginGutenberg . 'assets/gutenberg_integrate.js';
		$scriptDeps = array('jquery', 'wp-block-editor', 'wp-blocks', 'wp-components', 'wp-data', 'wp-element');
		
		HelperUC::addStyleAbsoluteUrl($styleUrl, $handle);
		HelperUC::addScriptAbsoluteUrl($scriptUrl, $handle, false, $scriptDeps);
		
		$arrBlocks = $this->getAllBlocks();
		$arrParsedBlocks = $this->getParsedBlocks();
		$globalJSOutput = HelperHtmlUC::getGlobalJsOutput();
		
		wp_localize_script($handle, 'g_gutenbergBlocks', $arrBlocks);
		wp_localize_script($handle, 'g_gutenbergParsedBlocks', $arrParsedBlocks);
		wp_add_inline_script($handle, $globalJSOutput, 'before');
	}

	/**
	 * Get the Gutenberg blocks.
	 *
	 * @return array
	 */
	private function getBlocks($isBG = false){
				
		//get from cache
		
		if($isBG == false)
			$arrCached = self::$cacheBlocksRegular;
		else
			$arrCached = self::$cacheBlocksBG;
		
		if(!empty($arrCached))
			return($arrCached);
		
		$arrBlocks = array();

		$isOutputPage = (GlobalsProviderUC::$isInsideEditor == false);
		
		$arrData = HelperProviderCoreUC_EL::getPreloadDBData($isOutputPage);
		
		if($isBG == false)
			$arrRecords = UniteFunctionsUC::getVal($arrData, "addons");
		else 
			$arrRecords = UniteFunctionsUC::getVal($arrData, "bg_addons");
		
		foreach($arrRecords as $addonName => $record){
			
			$addon = new UniteCreatorAddon();
			$addon->initByDBRecord($record);
			
			$name = $addon->getBlockName();
			
			$category = $this->categorySlug;
			if($isBG)
				$category = $this->categorySlugBG;
			
			$arrBlocks[$name] = array(
				'name' => $name,
				'title' => $addon->getTitle(),
				'icon' => $addon->getPreviewIconContents(),
				'category' => $category,
				'render_callback' => array($this, 'renderBlock'),
				'attributes' => array(
					'_id' => array(
						'type' => 'string',
						'default' => $addon->getID(),
					),
					'_rootId' => array(
						'type' => 'string',
						'default' => '',
					),
					'_preview' => array(
						'type' => 'string',
						'default' => '',
					),
					'data' => array(
						'type' => 'string',
						'default' => '',
					),
				),
				'example' => array(
					'attributes' => array(
						'_preview' => $addon->getPreviewImageUrl(),
					),
				),
				'supports' => array(
					'customClassName' => false,
					'html' => false,
					'renaming' => false,
					'reusable' => false,
				),
				'editor_style_handles' => array('uc_gutenberg_integrate'),
				'script_handles' => array('jquery'),
			);
		}
		
		if($isBG == false)
			self::$cacheBlocksRegular = $arrBlocks;
		else
			self::$cacheBlocksBG = $arrBlocks;
		
		
		return($arrBlocks);
	}

	/**
	 * Get the parsed Gutenberg blocks.
	 *
	 * @param int|null $postId
	 *
	 * @return array
	 */
	public function getPostBlocks($postId = null){

		$post = get_post($postId);
		$blocks = parse_blocks($post->post_content);

		return $blocks;
	}

	/**
	 * Get the existing parsed Gutenberg blocks.
	 *
	 * @return array
	 */
	public function getParsedBlocks(){
		
		$parsedBlocks = $this->getPostBlocks();
		$existingBlocks = $this->getAllBlocks();
				
		$blocks = $this->extractParsedBlocks($parsedBlocks, $existingBlocks);
		
		return $blocks;
	}

	/**
	 * Get block by root identifier.
	 *
	 * @param array $content
	 * @param int $rootId
	 *
	 * @return array|null
	 */
	public function getBlockByRootId($content, $rootId){

		if(empty($content) === true)
			return null;

		if(is_array($content) === false)
			return null;

		if(empty($rootId) === true)
			return null;

		foreach($content as $block){
			if(isset($block['blockName']) === false)
				continue;

			$blockAttributes = UniteFunctionsUC::getVal($block, 'attrs');
			$blockRootId = UniteFunctionsUC::getVal($blockAttributes, '_rootId');

			if($rootId === $blockRootId)
				return $block;

			$innerBlocks = UniteFunctionsUC::getVal($block, 'innerBlocks');

			if(empty($innerBlocks) === false && is_array($innerBlocks) === true){
				$innerBlock = $this->getBlockByRootId($innerBlocks, $rootId);

				if(empty($innerBlock) === false)
					return $innerBlock;
			}
		}

		return null;
	}

	/**
	 * Get settings from the given block.
	 *
	 * @param array $block
	 *
	 * @return array
	 */
	public function getSettingsFromBlock($block){

		$attributes = UniteFunctionsUC::getVal($block, 'attrs', array());
		$data = UniteFunctionsUC::getVal($attributes, 'data', null);

		if(empty($data) === true)
			return array();

		$settings = UniteFunctionsUC::jsonDecode($data);

		return $settings;
	}

	/**
	 * Extract the existing parsed Gutenberg blocks.
	 *
	 * @param array $parsedBlocks
	 * @param array $existingBlocks
	 *
	 * @return array
	 */
	private function extractParsedBlocks($parsedBlocks, $existingBlocks){
		
		$blocks = array();
		
		foreach($parsedBlocks as $block){
			$name = $block['blockName'];

			if(empty($existingBlocks[$name]) === false){
				$blocks[] = array(
					'name' => $name,
					'html' => render_block($block),
				);
			}

			$innerBlocks = UniteFunctionsUC::getVal($block, 'innerBlocks');

			if(empty($innerBlocks) === false && is_array($innerBlocks) === true){
				$blocks = array_merge($blocks, $this->extractParsedBlocks($innerBlocks, $existingBlocks));
			}
		}

		return $blocks;
	}

}
