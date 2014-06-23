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
				'' => ''
				, '' => ''	
				, '' => ''	
				, '' => ''	
				, '' => ''	
			)
		);
	
		self::$config = $config;
	
	}
	
	
	
	
	
	public function updateCMSFields(FieldList $fields){
		$fields->removeByName('CTAVSetings');
		
		
		
		
		
		
	}
	
	
}	
	