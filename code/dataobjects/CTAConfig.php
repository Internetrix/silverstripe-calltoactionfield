<?php
class CTAConfig extends DataObject {
	
	private static $db = array (
		'SourceClass' 	=> 'Varchar(255)',
		'SourceID' 		=> 'Varchar(255)',
		'ForGlobal'		=> 'Boolean',	
		'Setting' 		=> 'Text'			//serialized values
	);
	
	/**
	 * @var array
	 */
	protected $SettingDataArray;	//store unserialized 'Setting' value
	
	public function __construct(){
		
		parent::__construct();
		
		if($this->Setting){
			$this->SettingDataArray = unserialize($this->Setting);
		}else{
			$this->SettingDataArray = array();
		}
	}
	
	public function onBeforeWrite() {
		parent::onBeforeWrite();
		
		$this->Setting = serialize($this->SettingDataArray);
	}

	public function getSourceDataObject(){
		$ClassName = $this->SourceClass;

		return $ClassName::get()->byID($this->SourceID);
	}
	
	
	public function getSettingDataArrayArray(){
		return $this->SettingDataArray;
	}
	
}