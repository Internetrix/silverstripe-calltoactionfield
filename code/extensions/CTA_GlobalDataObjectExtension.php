<?php
class CTA_GlobalDataObjectExtension extends DataExtension {
	
	private static $db = array (
		'CTAVGlobalSetings' 	=> 'Text'	//Call To Action Values Global Settings
	);
	
	public function updateCMSFields(FieldList $fields){
		$fields->removeByName('CTAVGlobalSetings');
	
	
	
	
	
	
	}
	
	
	
	
	
	
	
}	
	