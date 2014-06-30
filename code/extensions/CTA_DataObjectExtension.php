<?php
class CTA_DataObjectExtension extends DataExtension {
	
	/**
	 * @var CTA_DataObjectExtension
	 */
	protected $owner;
	
	/**
	 * @var array
	 */
	static $config;
	
	/**
	 * @var string
	 */
	private static $DefaultGlobalSource = 'SiteConfig';

	/**
	 * @var array
	 */
	private static $GlobalStaticConfig = array();

	static function CallToActionFields_Init(){
		//TODO $config should be pulled from static config 
		$config = array(
			// source class (instance of DataObject) => config array
			'Product' => array(
				array(
					'SourceValue' => 'PackingContent'		// db value of dataobject
					, 'GlobalSource' => 'SiteConfig'		// define where to store or get global value or dataobject
					, 'DefaultSourceOption' => 'Global'		// if this is not set, 'Global' by default
					, 'UseOriginal' => false				// true  = if 'SourceValue' exists in 'GlobalSource' dataobject, 
															//         then use it as global source value. Otherwise, create a 
															//		   new value in 'CTAVGlobalSetings'
															// false = always create new value in 'CTAVGlobalSetings'
				)											
			)
// 			, 'Page' => array(
// 				array(
// 					'SourceValue' => 'ctatesting'		
// 					, 'GlobalSource' => 'SiteConfig'		
// 					, 'UseOriginal' => true			
// 				)
// 			)
		);
	
		self::$config = $config;
		
		if( ! empty($config)){
			foreach ($config as $ClassName => $ConfigArray){
				//make sure $ClassName is instance of DataObject
				if(is_subclass_of($ClassName, 'DataObject')){
					//add CTA config into SS Config
					$cta_config_array = Config::inst()->get($ClassName, 'cta_config');
					
					if($cta_config_array === null){
						//update 'cta_config' for this class
						$cta_config_array = $ConfigArray;
						Config::inst()->update($ClassName, 'cta_config', $cta_config_array);
					}
					
					self::ApplyCallToActionExtensions($ClassName, $cta_config_array);
					
					//push global setting
// 					$sourceValue = ['SourceValue'];
					$GlobalStaticConfig[$ClassName][] = array();
				}
			}
		}
		
	}
	
	static function ApplyCallToActionExtensions($ClassName, $config_array){
		
		$GlobalStaticConfig = self::$GlobalStaticConfig;
		
		foreach ($config_array as $array){
			//apply CTA_DataObjectExtension.php
			if( ! $ClassName::has_extension('CTA_DataObjectExtension') ){
				self::AddDataObjectExtension($ClassName, 'CTA_DataObjectExtension');
			}
			
			//apply CTA_GlobalDataObjectExtension.php
			if( isset($config_array['GlobalSource']) && (is_subclass_of($config_array['GlobalSource'], 'DataObject'))){
				//TODO throw user error if $config_array['GlobalSource'] is not an dataobject.
				$NameOfGlobalDataObject = $config_array['GlobalSource'];
			}else{
				$NameOfGlobalDataObject = self::$DefaultGlobalSource;
			}
			
// 			$GlobalStaticConfig[$NameOfGlobalDataObject][] = ;
			
			if( ! $NameOfGlobalDataObject::has_extension('CTA_GlobalDataObjectExtension') ){
				self::AddDataObjectExtension($NameOfGlobalDataObject, 'CTA_GlobalDataObjectExtension');
			}
		}		
		
	}
	
	static function CheckSourceValue($ClassName, $value_name){
		
		
		
	}
	
	
	static function AddDataObjectExtension($ClassName, $extensionName){
		//add to static config
		$StaticExtsData = Config::inst()->get($ClassName, 'extensions');
		$StaticExtsData[] = $extensionName;
		Config::inst()->update($ClassName, 'extensions', null);
		Config::inst()->update($ClassName, 'extensions', $StaticExtsData);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function updateCMSFields(FieldList $fields){
		
		//check the static config and see if it need call to action fields
		if($this->owner->requireCTAConfig()){
				
			$StaticConfigArrays = $this->owner->getStaticConfig();
			
			$ConfigDataDO = $this->owner->getCTAConfig();
			
			//check all the defined values
			foreach ($StaticConfigArrays as $StaticConfig){
				
				$valueName = $StaticConfig['SourceValue'];
				
				$SelectedField = $fields->dataFieldByName($valueName);

				if(is_object($SelectedField) && $SelectedField->Name == $valueName){
					//insert value source options field
					$SourceOptionField = $this->owner->PopulateSourceOptionField($fields, $SelectedField, $StaticConfig, $ConfigDataDO);
					
					//TODO setup readonly field for viewing global values.
					
					//setup display logic
					$SelectedField->displayIf($SourceOptionField->Name)->isEqualTo("Custom")->end();
				}
			}
		}
		
	}
	
	
	public function PopulateSourceOptionField(FieldList $fields, FormField $SelectedField, $StaticConfig, CTAConfig $ConfigDataDO){
		$optionsArray 	= array(
			'Custom' => 'Custom'
			, 'Global' => 'Global'
			, 'Parent' => 'Parent'	
			, 'Hide' => 'Hide'
		);
		
		if( ! $this->owner->hasExtension('Hierarchy') || $this->owner->ParentID === 0){
			unset($optionsArray['Parent']);
		}
		
		$sourceFieldName = $SelectedField->Name;
		
		$selectedValue 	= $this->owner->getCTAOptionValue($sourceFieldName, $ConfigDataDO);

		$optionsField = OptionsetField::create("CTASourceOption[{$sourceFieldName}]", "'{$SelectedField->Title()}' Source", $optionsArray);
		$optionsField->setValue($selectedValue);		
		
		$fields->insertBefore($optionsField, $sourceFieldName);
		
		return $optionsField;
	}
	
	
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
	
	/**
	 * @return Boolean
	 */
	public function requireCTAConfig(){
		$staticConfig = $this->owner->getStaticConfig();
		
		if($staticConfig !== null){
			foreach ($staticConfig as $array){
				if(isset($array['SourceValue']) && $array['SourceValue']){
					//return true if it's $db value of the owner DataObject
					$dbMapArray = DataObject::database_fields($this->owner->ClassName);
// 					Debug::show($dbMapArray);die;
					$dbValueName = $array['SourceValue'];
					if(isset($dbMapArray[$dbValueName])){
						return true;
					}
				}
			}
		}
		
		return false;
	}
	
	/**
	 * @return array | null
	 */
	public function getStaticConfig(){
		
		return $this->owner->stat('cta_config');
		
	}
	
	
	/**
	 * @return CTAConfig
	 */
	public function getCTAConfig(){
		$CTAConfigDO =  CTAConfig::get()
							->filter(
								array(
									'SourceClass' 	=> $this->owner->ClassName, 
									'SourceID' 		=> $this->owner->ID
								)
							)
							->first();		

		return ($CTAConfigDO && $CTAConfigDO->exists()) ? $CTAConfigDO : false;
	}
	
	
	public function CreateCTAConfig(){
		if($this->owner->ID){
			$config = new CTAConfig();
			$config->SourceClass	= $this->owner->ClassName;
			$config->SourceID		= $this->owner->ID;
			$config->Setting		= '';
			$config->write();
			
			return $config;
		}
		
		return false;
	}
	
	
	public function GetOrCreateCTAConfig(){
		
		$configDO = $this->owner->getCTAConfig();
		
		if($configDO === false){
			return $this->owner->CreateCTAConfig();
		}
		
		return $configDO;
	}
	
	
	public function getCTAOptionValue($SourceValue, $CTAConfigDO = false){
		
		$StaticConfig = $this->owner->getStaticConfig();
		
		if($CTAConfigDO === false){
			$CTAConfigDO = $this->owner->getCTAConfig();
		}
		
		//program default
		$OptionValue = 'Custom';
		$gotValue = true;
		
		if($CTAConfigDO !== false){
			//if user set the custom option value, return it.
			$setting = $CTAConfigDO->getSettingArrayByValue();

			if( isset($setting[$SourceValue])){
				
				$OptionValue = $setting[$SourceValue];
				
			}else{
				$getFromDefault = true; 
			}
		}
		
		if( ! $gotValue){
			//haven't get user defined option value. 
			//if 'DefaultSourceOption' is set, then use it.
			if(isset($StaticConfig['DefaultSourceOption']) && $StaticConfig['DefaultSourceOption']){
				$OptionValue = $StaticConfig['DefaultSourceOption'];
			}
		}
		
		return $OptionValue;
	}
	
	
	
	
}	
	