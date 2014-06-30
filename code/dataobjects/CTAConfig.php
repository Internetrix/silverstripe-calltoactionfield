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
	

	public function onBeforeWrite() {
		parent::onBeforeWrite();
		
		$this->Setting = serialize($this->SettingDataArray);
	}

	public function getSourceDataObject(){
		$ClassName = $this->SourceClass;

		return $ClassName::get()->byID($this->SourceID);
	}
	
	
	public function getSettingDataArray(){
		
		if($this->Setting){
			$this->SettingDataArray = unserialize($this->Setting);
		}else{
			$this->SettingDataArray = array();
		}
		
		return $this->SettingDataArray;
	}
	
	
	public function getSettingArrayByValue($value){
		$setting = $this->getSettingDataArray();
		
		if( isset($setting[$value]) ){
			return $setting[$value];
		}
		
		return false;
	}
	
}