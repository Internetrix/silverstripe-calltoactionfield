<?php
class CTAConfig extends DataObject {
	
	private static $db = array (
		'SourceClass' 	=> 'Varchar(255)',
		'SourceID' 		=> 'Varchar(255)',
		'ForGlobal'		=> 'Boolean',	
		'Setting' 		=> 'Text'			//serialized values
	);
	
	
	public function getSourceDataObject(){
		$ClassName = $this->SourceClass;

		return $ClassName::get()->byID($this->SourceID);
	}
	
	
	
	
	
	
	
}