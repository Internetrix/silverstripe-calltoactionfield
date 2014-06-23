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
		);
	
		self::$config = $config;
		
		if( ! empty($config)){
			foreach ($config as $ClassName => $ConfigArray){
				//make sure $ClassName is instance of DataObject
				if(is_subclass_of($ClassName, 'DataObject')){
					//add CTA config into SS Config
					$cta_config = Config::inst()->get($ClassName, 'cta_config');
					
					if($cta_config === null){
						//update 'cta_config' for this class
						Config::inst()->update($ClassName, 'cta_config', $ConfigArray);
					}
					
					self::ApplyCallToActionExtensions($ClassName, $ConfigArray);
				}
			}
		}
		
	}
	
	static function ApplyCallToActionExtensions($ClassName, $ConfigArray){
		
		
		
		
	}
	
	
	
	public function updateCMSFields(FieldList $fields){
		$fields->removeByName('CTAVSetings');
		
		
		
		
		
		
	}
	
	
}	
	