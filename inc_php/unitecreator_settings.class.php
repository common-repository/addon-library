<?php
/**
 * @package Blox Page Builder
 * @author UniteCMS.net
 * @copyright (C) 2017 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');

class UniteCreatorSettingsWork extends UniteSettingsAdvancedUC{
	
	
	
	private $currentAddon;
	
	
	/**
	 * set current addon
	 */
	public function setCurrentAddon(UniteCreatorAddon $addon){
		
		$this->currentAddon = $addon;
		
	}
	
	/**
	 * if the source == "addon" add url base
	 */
	private function checkParamsSource($arrParams){
		
		$source = UniteFunctionsUC::getVal($arrParams, "source");
		
		if($source == "addon"){
		
			if(empty($this->currentAddon))
				UniteFunctionsUC::throwError("You must set current addon before init settings for addon related image select option");
		
			$urlAssets = $this->currentAddon->getUrlAssets();
		
			$arrParams["url_base"] = $urlAssets;
		}
		
		return($arrParams);
	}
	
	
	/**
	 * add base url for image settings if needed
	 */
	public function addImage($name,$defaultValue = "",$text = "",$arrParams = array()){
		
		$arrParams = $this->checkParamsSource($arrParams);
		
		parent::addImage($name, $defaultValue, $text, $arrParams);
		
	}
	
	
	/**
	 * add base url for image settings if needed
	 */
	public function addMp3($name,$defaultValue = "",$text = "",$arrParams = array()){
	
		$arrParams = $this->checkParamsSource($arrParams);
	
		parent::addMp3($name, $defaultValue, $text, $arrParams);
	
	}
	
	
	/**
	 * get settings types array
	 */
	public function getArrUCSettingTypes(){
		
		$arrTypes = array(
			"uc_textfield",
			"uc_number",
			"uc_textarea",
			"uc_editor",
			"uc_radioboolean",
			"uc_checkbox",
			"uc_dropdown",
			"uc_colorpicker",
			"uc_image",
			"uc_mp3",
			"uc_icon",
			"uc_hr",
			"uc_font_override",
			UniteCreatorDialogParam::PARAM_POST,
			UniteCreatorDialogParam::PARAM_POSTS_LIST,
			"uc_statictext"
		);
		
		return($arrTypes);
	}
	
	
	/**
	 * if in this type exists multiple settings
	 */
	public static function isMultipleUCSettingType($type){
		
		switch($type){
			case UniteCreatorDialogParam::PARAM_POSTS_LIST:
			case UniteCreatorDialogParam::PARAM_CONTENT:
			case UniteCreatorDialogParam::PARAM_INSTAGRAM:
				return(true);
			break;
		}
		
		return(false);
	}
	
	
	
	/**
	 * add image base settings
	 */
	public function addImageBaseSettings(){
		
		$extra = array("origtype"=>"uc_image");
		$this->addImage("image","","Image",$extra);
		
		$extra = array("origtype"=>"uc_textarea");
		$this->addTextArea("description", "", __("Description", ADDONLIBRARY_TEXTDOMAIN),$extra);
		
		/*
		$extra = array("origtype"=>"uc_radioboolean");
		$this->addRadioBoolean("enable_link", __("Enable Link", ADDONLIBRARY_TEXTDOMAIN),false, "Yes","No",$extra);
			
		$extra = array("class"=>"unite-input-link", "origtype"=>"uc_textfield");
		$this->addTextBox("link", "", __("Link", ADDONLIBRARY_TEXTDOMAIN),$extra);
		*/
		
	}

	
	
	/**
	 * get settings in creator format
	 */
	public function getSettingsCreatorFormat(){
		
		$arrParams = array();
		foreach($this->arrSettings as $setting){
			$param = array();
			$origType = UniteFunctionsUC::getVal($setting, "origtype");
			UniteFunctionsUC::validateNotEmpty($origType, "settings original type");
			
			$param["type"] = $origType;
			$param["title"] = UniteFunctionsUC::getVal($setting, "text");
			$param["name"] = UniteFunctionsUC::getVal($setting, "name");
			$param["description"] = UniteFunctionsUC::getVal($setting, "description");
			$param["default_value"] = UniteFunctionsUC::getVal($setting, "default_value");
			$arrParams[] = $param;
		}
		
		
		return($arrParams);
	}
	
		
	
	/**
	 * add settings provider types
	 */
	protected function addSettingsProvider($type, $name,$value,$title,$extra ){
		
		dmp("function for override: addSettingsProvider ");
		exit();

	}
	
	
	/**
	 * add post list picker
	 */
	protected function addPostsListPicker($name,$value,$title,$extra){
		dmp("addPostsListPicker - function for override");
		exit();
	}
	
	
	/**
	 * add instagram selector
	 */
	protected function addInstagramSelector($name,$value,$title,$extra){
		
		$description = __("Type instagram user (@username)", ADDONLIBRARY_TEXTDOMAIN);
		$params = array("description"=>$description);
		
		if(empty($value))
			$value = "@gianlucavacchi";
		
		$this->addTextBox($name, $value ,__("Instagram User", ADDONLIBRARY_TEXTDOMAIN), $params);
		
	}
	
	
	/**
	 * get setting as creator params, for mapping
	 */
	public function getSettingsAsCreatorParams(){
		
		$arrParams = array();
		
		//UniteFunctionsUC::showTrace();
		//dmp($this->arrSettings);exit();
		
		foreach($this->arrSettings as $setting){
			
			$param = array();
			
			$param["type"] = "uc_textfield";	//no metter what type, will be parsed anyway
			$param["name"] = UniteFunctionsUC::getVal($setting, "name");
			$param["title"] = UniteFunctionsUC::getVal($setting, "text");
			$param["default_value"] = UniteFunctionsUC::getVal($setting, "default_value");
			$param["description"] = UniteFunctionsUC::getVal($setting, "description");
			
			$param["uc_setting"] = $setting;
			
			$arrParams[] = $param;
		}
		
		
		return($arrParams);
	}
	
	
	/**
	 * add setting by creator param
	 */
	public function addByCreatorParam($param, $inputValue = null){
		
		//add ready setting if exists
		$arrReadySetting = UniteFunctionsUC::getVal($param, "uc_setting"); 
		if(!empty($arrReadySetting)){
			
			$classAdd = UniteFunctionsUC::getVal($arrReadySetting, UniteSettingsUC::PARAM_CLASSADD);
			if(!empty($classAdd))
				$classAdd .= " ";
			$classAdd .= "wpb_vc_param_value";
			
			$arrReadySetting[UniteSettingsUC::PARAM_CLASSADD] = $classAdd;
			
			if($inputValue !== null)
				$arrReadySetting["value"] = $inputValue;
			
			$this->addSettingByArray($arrReadySetting);
			
			return(false);
		}
		
		$type = UniteFunctionsUC::getVal($param, "type");
		$title = UniteFunctionsUC::getVal($param, "title");
		$name = UniteFunctionsUC::getVal($param, "name");
		$description = UniteFunctionsUC::getVal($param, "description");
		
		$defaultValue = UniteFunctionsUC::getVal($param, "default_value");
		$value = UniteFunctionsUC::getVal($param, "value", $defaultValue);
		
		
		$unit = UniteFunctionsUC::getVal($param, "unit");
		
		$extra = array();
		if(!empty($description))
			$extra["description"] = $description;
		
		if(!empty($unit))
			$extra["unit"] = $unit;
		
		$extra["origtype"] = $type;
		$extra[UniteSettingsUC::PARAM_CLASSADD] = "wpb_vc_param_value";
		
		$isUpdateValue = true;
		
		$isMultipleSettingType = self::isMultipleUCSettingType($type);
		if($isMultipleSettingType && !empty($inputValue)){
			$value = $inputValue;
			$isUpdateValue = false;
		}
		
		
		switch ($type){
			case "uc_editor":
				$this->addEditor($name, $value, $title, $extra);
			break;
			case "uc_textfield":
				$this->addTextBox($name, $value, $title, $extra);
			break;
			case "uc_number":
				$extra["class"] = UniteCreatorSettingsOutput::INPUT_CLASS_NUMBER;
				$this->addTextBox($name, $value, $title, $extra);
			break;
			case "uc_radioboolean":
				$arrItems = array();
				$arrItems[$param["true_name"]] = $param["true_value"];
				$arrItems[$param["false_name"]] = $param["false_value"];
				
				$this->addRadio($name, $arrItems, $title, $value, $extra);
			break;
			case "uc_textarea":
				$this->addTextArea($name, $value, $title, $extra);
			break;
			case "uc_checkbox":
				$textNear = UniteFunctionsUC::getVal($param, "text_near");
				$isChecked = UniteFunctionsUC::getVal($param, "is_checked");
				$isChecked = UniteFunctionsUC::strToBool($isChecked);
		
				$this->addCheckbox($name, $isChecked, $title, $textNear, $extra);
			break;
			case "uc_dropdown":
				$options = UniteFunctionsUC::getVal($param, "options");
				
				$this->addSelect($name, $options, $title, $value, $extra);
			break;
			case "uc_colorpicker":
				$this->addColorPicker($name, $value, $title, $extra);
			break;
			case "uc_image":
				$this->addImage($name,$value,$title,$extra);
			break;
			case "uc_mp3":
				$this->addMp3($name,$value,$title,$extra);
			break;
			case "uc_imagebase":
				$this->addImageBaseSettings();
			break;
			case "uc_statictext":
				$this->addStaticText($title, $name, $extra);
				$isUpdateValue = false;
			break;
			case "uc_icon":
				$this->addIconPicker($name,$value,$title,$extra);
			break;
			case "uc_hr":
				$this->addHr($name);
				$isUpdateValue = false;
			break;
			case "uc_font_override":
				//don't draw anything
			break;
			case UniteCreatorDialogParam::PARAM_INSTAGRAM:
				$this->addInstagramSelector($name, $value, $title, $extra);
			break;
			case UniteCreatorDialogParam::PARAM_POST:
				$this->addPostPicker($name,$value,$title,$extra);
			break;
			case UniteCreatorDialogParam::PARAM_POSTS_LIST:
				$this->addPostsListPicker($name,$value,$title,$extra);
			break;
			case UniteCreatorDialogParam::PARAM_CONTENT;
				$this->addContentSelector($name,$value,$title,$extra);
			break;
			default:

				$isAdded = $this->addSettingsProvider($type,$name,$value,$title,$extra);
				if($isAdded == false)
					UniteFunctionsUC::throwError("initByCreatorParams error: Wrong setting type: $type");
			
			break;
		}

		
		//set setting value
		if($inputValue !== null && $isUpdateValue == true){
			
			$this->updateSettingValue($name, $inputValue);
		}
		
	}
	
	
	/**
	 * add settings by creator params
	 */
	public function initByCreatorParams($arrParams){
		
		foreach($arrParams as $param){
			$this->addByCreatorParam($param);
		}
		
	}
	
	
}