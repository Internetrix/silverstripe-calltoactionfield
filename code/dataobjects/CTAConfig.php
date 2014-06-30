<?php
class CTAConfig extends DataObject {
	
	private static $db = array (
		'SourceClass' 	=> 'Varchar(255)',
		'SourceID' 		=> 'Varchar(255)',
		'Setting' 		=> 'Text'			//serialized values
	);
	
	/**
	 * @var array
	 */
	private $SettingDataArray = array();	//store unserialized 'Setting' value
	
	
	public function getSettingDataArray(){
	
		if(empty($this->SettingDataArray)){
			if($this->Setting){
				$this->SettingDataArray = unserialize($this->Setting);
			}
		}
	
		return $this->SettingDataArray;
	}
	

	public function onBeforeWrite() {
		parent::onBeforeWrite();
		
		$this->Setting = serialize($this->SettingDataArray);
	}

	
	public function getSourceDataObject(){
		$ClassName = $this->SourceClass;

		return $ClassName::get()->byID($this->SourceID);
	}
	
	
	public function getSettingArrayByValue($value = null){
		$setting = $this->getSettingDataArray();
		
		if($value === null){
			return $setting;
		}
		
		if( isset($setting[$value]) ){
			return $setting[$value];
		}
		
		return false;
	}
	
	
	public function updateSettingData($OptionValuesArray){
		if( ! is_array($OptionValuesArray)){
			return false;
		}
		
		$dataArray = $this->getSettingDataArray();
		
		$this->SettingDataArray = array_merge($dataArray, $OptionValuesArray);
	}
	
}