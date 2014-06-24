<?php
class CTA_DataObjectExtension extends DataExtension {
	
	/**
	 * @var DataObject
	 */
	protected $owner;
	
	/**
	 * @var array
	 */
	static $config;

	static function CallToActionFields_Init(){
	
		$config = array(
			// source class (instance of DataObject) => config array
			'Product' => array(
				array(
					'SourceValue' => 'PackingContent'		// db value of dataobject
					, 'GlobalSource' => 'SiteConfig'		// define where to store or get global value or dataobject
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
				}
			}
		}
		
	}
	
	static function ApplyCallToActionExtensions($ClassName, $config_array){
		
		foreach ($config_array as $array){
			//apply CTA_DataObjectExtension.php
			if( ! $ClassName::has_extension('CTA_DataObjectExtension') ){
				
				self::AddDataObjectExtension($ClassName, 'CTA_DataObjectExtension');
			}
			
			//apply CTA_GlobalDataObjectExtension.php
			if( isset($config_array['GlobalSource']) ){
				
				$NameOfGlobalDataObject = $config_array['GlobalSource'];
				
				if( ! $NameOfGlobalDataObject::has_extension('CTA_GlobalDataObjectExtension') ){
					self::AddDataObjectExtension($NameOfGlobalDataObject, 'CTA_GlobalDataObjectExtension');
				}
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
				
			
			
			
			
			
		}
		
	}
	
	
	public function onBeforeWrite(){
		
		if( ! $this->owner->ID && $this->owner->requireCTAConfig()){
			$this->owner->CTAFirstWrite = true;
		}
		
	}
	
	public function onAfterWrite(){
		
		if($this->owner->CTAFirstWrite === true){
			
			$this->owner->GetOrCreateCTAConfig();
			
			$this->owner->CTAFirstWrite = false;
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
	
	
	public function getCTAConfig(){
		return CTAConfig::get()
					->filter(
						array(
							'SourceClass' => $this->owner->ClassName
							, 'SourceID' => $this->owner->ID
						)
					)
					->first();		
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
		
		if( ! $configDO){
			return $this->owner->CreateCTAConfig();
		}
		
		return $configDO;
	}
	
	
	
	
	
}	
	