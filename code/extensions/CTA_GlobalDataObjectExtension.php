<?php
class CTA_GlobalDataObjectExtension extends DataExtension {
	
	private static $db = array (
		'CTAGlobal'		=> 'Boolean',
	);
	
	public function onBeforeWrite(){
	
		if($this->owner->requireCTAConfig()){
			//update call to action values in onAfterWrite()
			$this->owner->CTAFirstWrite = true;
			$this->owner->CTAupdate = true;
		}
	
	}
	
	public function onAfterWrite(){
	
		if($this->owner->CTAupdate === true){
			//get or create record
			$configDO = $this->owner->GetOrCreateCTAConfig();
				
			//save settings
			$CTA_post_data = Controller::curr()->request->postVar('CTASourceOption');
				
			$configDO->updateSettingData($CTA_post_data);
			$configDO->write();
				
			$this->owner->CTAupdate = false;
		}
	
	}
	
	
	
	
	
	
	public function updateCMSFields(FieldList $fields){
		$fields->removeByName('CTAGlobal');
	
	
	
	
	}
	
	
	
	
	
	
	
}	
	