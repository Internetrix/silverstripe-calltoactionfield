<?php
class CTA_DataObjectExtension extends DataExtension {
	
	static $setting;

	static function CallToActionFields_Init(){
	
		$config = array(
			// source class (instance of DataObject) => config array
			'' => array(
			
			)
		);
	
		self::$setting = $config;
	
	}
	
	
	
	
}	
	