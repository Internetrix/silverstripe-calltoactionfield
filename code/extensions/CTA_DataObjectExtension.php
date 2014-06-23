<?php
class CTA_DataObjectExtension extends DataExtension {
	
	private static $db = array (
		'CTAVSetings' 	=> 'Text'	//Call To Action Values Settings
	);
	
	/**
	 * @var array
	 */
	static $config;

	static function CallToActionFields_Init(){
	
		$config = array(
			// source class (instance of DataObject) => config array
			'Product' => array(
				array(
					'SourceValue' => 'PackingContent'		// db value of dataobject
					, 'GlobalSource' => 'SiteConfig'		// define where to store or get global value or dataobject
					, 'UseOriginal' => false				// true  = if 'SourceValue' exists in 'GlobalSource' dataobject, 
															//         then use it as global source value. Otherwise, create a 
															//		   new value in 'CTAVGlobalSetings'
															// false = always create new value in 'CTAVGlobalSetings'
				)											
			)
			, 'Page' => array(
				array(
					'SourceValue' => 'ctatesting'		
					, 'GlobalSource' => 'SiteConfig'		
					, 'UseOriginal' => true			
				)
			)
		);
	
		self::$config = $config;
		
		if( ! empty($config)){
			foreach ($config as $ClassName => $ConfigArray){
				//make sure $ClassName is instance of DataObject
				if(is_subclass_of($ClassName, 'DataObject')){
					//add CTA config into SS Config
					$cta_config_array = Config::inst()->get($ClassName, 'cta_config');
					
					if($cta_config_array === null){
						//update 'cta_config' for this class
						$cta_config_array = $ConfigArray;
						Config::inst()->update($ClassName, 'cta_config', $cta_config_array);
					}
					
					self::ApplyCallToActionExtensions($ClassName, $cta_config_array);
				}
			}
		}
		
	}
	
	static function ApplyCallToActionExtensions($ClassName, $config_array){
		
		foreach ($config_array as $array){
			//apply CTA_DataObjectExtension.php
			if( ! $ClassName::has_extension('CTA_DataObjectExtension') ){
				Object::add_extension($ClassName, 'CTA_DataObjectExtension');
			}
			
			//apply CTA_GlobalDataObjectExtension.php
			if( isset($config_array['GlobalSource']) ){
				
				$NameOfGlobalDataObject = $config_array['GlobalSource'];
				
				if( ! $NameOfGlobalDataObject::has_extension('CTA_GlobalDataObjectExtension') ){
					Object::add_extension($NameOfGlobalDataObject, 'CTA_GlobalDataObjectExtension');
				}
			}
		}		
		
	}
	
	static function CheckSourceValue($ClassName, $value_name){
		
		
		
	}
	
	
	public function updateCMSFields(FieldList $fields){

		$fields->removeByName('CTAVSetings');
		
		$cta_config = $this->owner->stat('cta_config');
		
		Debug::show(Config::inst()->get('Page', 'extensions'));
		Debug::show($this->owner->CTAVSetings);
		die;
		
		
	}
	
}	
	