<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
if ( ! defined( 'ABSPATH' ) ) exit;


	class UniteSettingsOutputInlineUC extends UniteCreatorSettingsOutput{
		
		
		/**
		 * constuct function
		 */
		public function __construct(){
			$this->isParent = true;
			self::$serial++;
			$this->wrapperID = "unite_settings_wide_output_".self::$serial;
			$this->settingsMainClass = "unite-settings-inline";
			$this->showDescAsTips = true;
		}
		
		
		/**
		 * draw settings row
		 * @param $setting
		 */
		protected function drawSettingRow($setting, $mode=""){
		
			//set cellstyle:
			$cellStyle = "";
			if(isset($setting[UniteSettingsUC::PARAM_CELLSTYLE])){
				$cellStyle .= $setting[UniteSettingsUC::PARAM_CELLSTYLE];
			}
			
			if($cellStyle != "")
				 $cellStyle = "style='".$cellStyle."'";
			
			$textStyle = $this->drawSettingRow_getTextStyle($setting);
						
			$rowClass = $this->drawSettingRow_getRowClass($setting, "unite-setting-row");
			
			$text = $this->drawSettingRow_getText($setting);
			
			$description = UniteFunctionsUC::getVal($setting,"description");
			
			$addField = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_ADDFIELD);
			
			?>
				
				<div id="<?php echo esc_attr($setting["id_row"])?>" <?php 
					s_echo($rowClass)?>>
					
					<div class="unite-setting-text" <?php 
						s_echo($textStyle)?> >
						<?php if($this->showDescAsTips == true): ?>
					    	<span class='setting_text' title="<?php echo esc_attr($description)?>"><?php echo esc_attr($text)?></span>
					    <?php else:?>
					    	<?php echo esc_attr($text)?>
					    <?php endif?>
					</div>
					<div class="unite-setting-content" <?php 
						s_echo($cellStyle)?>>
						<?php 
							$this->drawInputs($setting);
							$this->drawInputAdditions($setting);
						?>
					</div>
				</div>
			<?php
		}

		/**
		 * draw wrapper end after settings
		 */
		protected function drawSettingsAfter(){
		
			?><div class="unite-clear"></div><?php
		}
		
		
	
	}
?>