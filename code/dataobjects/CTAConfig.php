<?php
class CTAConfig extends DataObject {
	
	private static $db = array (
		'SourceClass' 	=> 'Varchar(255)',
		'SourceID' 		=> 'Varchar(255)',
		'Value'			=> 'Text'	
	);
	
	
}