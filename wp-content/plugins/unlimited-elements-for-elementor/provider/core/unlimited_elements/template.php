<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class UCEmptyTemplate{
	
	const SHOW_DEBUG = false;
	
	private $templateID;
	private $isMultiple = false;
	
	
	
	/**
	 * construct
	 */
	public function __construct(){
		$this->init();
	}
	
	/** 
	 * put error message
	 */
	private function putErrorMessage($message = null){
		
		if(self::SHOW_DEBUG == true){
			
			//escape html for the error message
			
			echo esc_html($message);
		}
				
		dmp("no output");		
	}
	
	
	/**
	 * render header debug
	 */
	private function renderHeader(){
		?>
		<header class="site-header">
			<p class="site-title">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php bloginfo( 'name' ); ?>
				</a>
			</p>
			<p class="site-description"><?php bloginfo( 'description' ); ?></p>
		</header>
		<?php 
	}
	
	/**
	 * render regular post body
	 */
	private function renderRegularBody(){
		
  	$this->renderHeader();
  	
	if ( have_posts() ) :
			
				while ( have_posts() ) :
			
					the_post();
					the_content();
					
				endwhile;
		endif;
	}
	
	/**
	 * validate that template exists
	 */
	private function validateTemplateExists(){
		
		if(empty($this->templateID))
			UniteFunctionsUC::throwError("no template found");
		
		$template = get_post($this->templateID);
		if(empty($template))	
			UniteFunctionsUC::throwError("template not found");
		
		$postType = $template->post_type;
		
		if($postType != "elementor_library")
			UniteFunctionsUC::throwError("bad template");
			
	}
	
	/**
	 * render header part
	 */
	private function renderHeaderPart(){
		?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
    
    <style>
    html{
    	margin:0px !important;
    	padding:0px !important;
    }
    
    </style>
        
  </head>
  <body <?php body_class(); ?>>
		
		<?php 
	}
	
	/**
	 * render footer part
	 */
	private function renderFooter(){
		
		?>
		<!-- Start Footer! -->
		<?php 
				
		
		wp_footer();
				
		?>
			</body>
		</html>
		<?php 
	}
	
	/**
	 * render template
	 */
	private function renderTemplate(){

		if(is_singular() == false)
			UniteFunctionsUC::throwError("not singlular");
		
		UniteFunctionsUC::validateNumeric($this->templateID,"template id");
		
		$this->validateTemplateExists();
		
		$content = HelperProviderCoreUC_EL::getElementorTemplate($this->templateID, true);
		
		$this->renderHeaderPart();
		
		//$this->renderRegularBody();
		
		s_echo($content);
		
		$this->renderFooter();
		
}

	/**
	 * check and output debug
	 */
	private function outputDebugScript(){
		
		?>
		
		<style>
		
			.uc-debug-holder{
				display:flex;
				justify-content:center;
				padding:10px;
			}
			
			.uc-debug-holder button{
				margin-left:20px;
			}
			
			.uc-template-index{
				position:absolute;
				top:10px;
				left:10px;
			}
			
		</style>
		
		<div class="uc-debug-holder">
			
			<div id="debug_index" class="uc-template-index"></div>
			
			<button id="debug_button_prev">Prev</button>
			
			<button id="debug_button_next">Next</button>
			
		</div>
		
		
		<script>
		
			function trace(str){
				console.log(str);
			}

			jQuery(document).ready(function(){

				function setTemplateIndex(){

					var total = jQuery(".uc-template-holder").length;

					var active = jQuery(".uc-template-holder").not(".uc-template-hidden").index();

					active++;
					
					var text = active + " / " + total;
					
					jQuery("#debug_index").html(text);
					
				}
				
				
				//set some item active
				function setActive(dir){
					
					var objActiveTemplate = jQuery(".uc-template-holder").not(".uc-template-hidden");
					
					if(objActiveTemplate.length != 1){
						
						trace(objActiveTemplate);
						
						throw new Exception("Wrong active template");
					}

					if(dir == "prev")					
						var objNextTemplate = objActiveTemplate.prev();
					else
						var objNextTemplate = objActiveTemplate.next();

					if(objNextTemplate.length == 0)
						return(false);
					
					objActiveTemplate.hide().addClass("uc-template-hidden");

					objNextTemplate.show().removeClass("uc-template-hidden");

					
					//clone the template tag
					
					var nextTemplateElement = objNextTemplate.children("template");

					if(nextTemplateElement.length){
						
						objNextTemplate.removeClass("uc-not-inited");

			            if(objNextTemplate.length > 1){
				            
				            trace(objNextTemplate);
				            throw new Exception("wrong next template");
				            
				        }

			        	    
				        var clonedContent = nextTemplateElement[0].content.cloneNode(true);
				        objNextTemplate.append(clonedContent);
				      	
				        nextTemplateElement.remove();
				        
						setTimeout(function(){
					        
							jQuery("body").trigger("uc_dom_updated");
							
						}, 300);
						
					}

					setTemplateIndex();
				}

				jQuery("#debug_button_next").on("click",function(){

					setActive("next");
						
				});

				jQuery("#debug_button_prev").on("click",function(){

					setActive("prev");
						
				});

				setTemplateIndex();
				
			});
		
		</script>
		
		<?php 
		
		return(true);
	}
	
	
	
	
	/**
	 * render dynamic popup templates
	 */
	private function renderDynamicPopupTemplates(){
		
		$postIDs = UniteFunctionsUC::getGetVar("postids","",UniteFunctionsUC::SANITIZE_TEXT_FIELD);
		
		$isDebug = UniteFunctionsUC::getGetVar("debug","",UniteFunctionsUC::SANITIZE_TEXT_FIELD);
		$isDebug = UniteFunctionsUC::strToBool($isDebug);
		
		UniteFunctionsUC::validateNotEmpty($postIDs,"post ids");
		
		UniteFunctionsUC::validateIDsList($postIDs,"id's list");
		
		$arrPostIDs = explode(",",$postIDs);
		
		$templateID = $this->templateID;
		
		//sanitize and check the template ID
		
		UniteFunctionsUC::validateNumeric($templateID,"template");
		
		$templateID = (int)$templateID;
		
		$content = "";
		
		foreach($arrPostIDs as $postID){
			
			HelperProviderCoreUC_EL::savePostForDynamic($postID);
			
			$urlTemplate = UniteFunctionsWPUC::getPermalink($templateID);
			
			//render in hidden mode
			
			$isHidden = false;
				
			GlobalsProviderUC::$renderTemplateID = $templateID;
			GlobalsProviderUC::$renderJSForHiddenContent = true;
			GlobalsProviderUC::$isInsideHiddenTemplate = true;
						
			$output = HelperProviderCoreUC_EL::getElementorTemplate($templateID, true);
						
			//set hidden content
			
			$class = "";

			$tag = "template";
			if($isDebug == true)
				$tag = "div";
						
			$output = "<{$tag} id='uc_template_output_{$templateID}_{$postID}' class='uc-template-output' data-postid='$postID' data-templateid='$templateID'>$output</{$tag}>\n";
			
			if(empty($output))
				$output = "template $templateID not found";
						
			GlobalsProviderUC::$renderJSForHiddenContent = false;
			GlobalsProviderUC::$isInsideHiddenTemplate = false;
			GlobalsProviderUC::$renderTemplateID = null;
			
			$content .= $output;
			
		}
				
		//don't know why, but it's not working. need to remove this dependency
		
		$this->renderHeaderPart();
		
		//check debug
		
		s_echo($content);
		
		$this->renderFooter();
				
	}
	
	/**
	 * get tmeplate widgets html
	 */
	private function getTemplateWidgetsHTML($arrTempalteWidgets){
		
		if(empty($arrTempalteWidgets))
			return("");
		
		$html = "<div class='uc-template-widgets-list' style='display:none'>";
		
		foreach($arrTempalteWidgets as $widget){
			
			$title = UniteFunctionsUC::getVal($widget, "title");
			
			$link = UniteFunctionsUC::getVal($widget, "link");

			$html .= "<a href='{$link}' target='_blank' class='uc-template-widgets-list__link'>{$title}</a>";			
		}
		
		$html .= "</div>";
		
		return($html);		
	}
	
	
	/**
	 * render multiple template for templates widget output
	 */
	private function renderMultipleTemplates(){
		
		$this->isMultiple = true;
		
		$arrTemplates = explode(",", $this->templateID);
		
		UniteFunctionsUC::validateIDsList($this->templateID,"template ids");
		
		$cacheContent = true;
		
		//check debug
		
		$isDebug = UniteFunctionsUC::getGetVar("framedebug","",UniteFunctionsUC::SANITIZE_TEXT_FIELD);
		$isDebug = UniteFunctionsUC::strToBool($isDebug);
				
		if($isDebug == true)
			$cacheContent = false;
		
		//set the content
		$content = "";
		
		foreach($arrTemplates as $index => $templateID){
			
			//sanitize and check template ID
			
			UniteFunctionsUC::validateNumeric($templateID,"template id");
			
			$templateID = (int)$templateID;
			
			$urlTemplate = UniteFunctionsWPUC::getPermalink($templateID);
			
			//render in hidden mode
			
			$isHidden = false;
				
			GlobalsProviderUC::$renderTemplateID = $templateID;
			
			if($index > 0){
				
				GlobalsProviderUC::$renderJSForHiddenContent = true;
				$isHidden = true;
			}
			
			do_action("ue_template_render_start");
			
			$output = HelperProviderCoreUC_EL::getElementorTemplate($templateID, true);
			
			$arrTempalteWidgets = apply_filters("ue_get_template_widgets",array());
			
			$htmlTemplateWidgets = $this->getTemplateWidgetsHTML($arrTempalteWidgets);
			
			do_action("ue_template_render_end");
			
			//set hidden content
						
			$class = "";
			if($isHidden == true){
				
				$class = " uc-template-hidden uc-not-inited";
				
				$output = "\n\n<template>\n$output\n</template>\n\n";
			}
			
			if(empty($output))
				$output = "template $templateID not found";
			
			$urlTemplate = esc_attr($urlTemplate);
			
			$content .= "<div id='uc_template_$templateID' class='uc-template-holder{$class}' data-id='$templateID' data-link='$urlTemplate'>
				$output
				$htmlTemplateWidgets
			</div>";
			
			GlobalsProviderUC::$renderJSForHiddenContent = false;
			
			GlobalsProviderUC::$renderTemplateID = null;
			
		}
		
		//don't know why, but it's not working. need to remove this dependency
		
		UniteFunctionsWPUC::removeIncludeScriptDep("elementor-frontend");
		
		ob_start();
		
		$this->renderHeaderPart();
		
		//$this->renderRegularBody();
		if($isDebug == true)
			echo "<div class='uc-debug-templates-wrapper'>";
		
		s_echo($content);
		
		if($isDebug == true)
			echo "</div>";
		
		if($isDebug == true)
			$this->outputDebugScript();
		
		$this->renderFooter();
		
		$content = ob_get_contents();
		ob_end_clean();
		
		//cache the content without scripts
		if($cacheContent == true){
			
			$cacheKey = HelperProviderCoreUC_EL::getFrameCacheKey($arrTemplates);
			
			$success = wp_cache_set( $cacheKey, $content, '', GlobalsUnlimitedElements::FRAME_CACHE_EXPIRE_SECONDS );
		}
		
		s_echo($content);
	}
	
	
	/**
	 * init the template
	 */
	private function init(){
		
		try{
			
  			show_admin_bar(false);
			
			$renderTemplateID = UniteFunctionsUC::getGetVar("ucrendertemplate","",UniteFunctionsUC::SANITIZE_TEXT_FIELD);
			
			$isMultiple = UniteFunctionsUC::getGetVar("multiple","",UniteFunctionsUC::SANITIZE_TEXT_FIELD);
			$isMultiple = UniteFunctionsUC::strToBool($isMultiple);
			
			$isDynamicPopup = UniteFunctionsUC::getGetVar("dynamicpopup","",UniteFunctionsUC::SANITIZE_TEXT_FIELD);
			$isDynamicPopup = UniteFunctionsUC::strToBool($isDynamicPopup);
			
			
			$type = "single";
			if($isMultiple == true)
				$type = "multiple";
			else if ($isDynamicPopup == true)
				$type = "dynamic_popup";
			
			if(empty($renderTemplateID))
				UniteFunctionsUC::throwError("template id not found");
			
			$this->templateID = $renderTemplateID;
			
			switch($type){
				default:
				case "single":
					$this->renderTemplate();
				break;
				case "multiple":
					$this->renderMultipleTemplates();
				break;
				case "dynamic_popup":
					
					$this->renderDynamicPopupTemplates();
				break;
			}
						
			
		}catch(Exception $e){
			
			$message = $e->getMessage();
			
			$this->putErrorMessage($message);
			
		}
		
	}
	
}

new UCEmptyTemplate();