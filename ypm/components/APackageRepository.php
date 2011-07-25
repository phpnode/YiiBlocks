<?php
/**
 * APackageRepository represents information about a Yii Package repository.
 * @author Charles Pick
 * @package packages.ypm.components
 */
class APackageRepository extends CComponent {
	/**
	 * The unique name of this repository
	 * @var string
	 */
	public $name;
	
	/**
	 * The package repository URL
	 * @var string
	 */
	public $url;
	
	/**
	 * Holds a list of packages provided by this repo
	 * @see getPackages()
	 * @var APackage[]
	 */
	protected $_packages;
	
	/**
	 * Refreshes the list of packages
	 */
	public function refreshPackages() {
		$curl = new ACurl;
		$curl->options->timeout = 3;
		$response = $curl->get($this->url)->exec()->fromJSON();
		$this->_packages = new CAttributeCollection();
		foreach($response['packages'] as $item) {
			$package = new APackage();
			$package->repository = $this;
			foreach($item as $attribute => $value) {
				$package->{$attribute} = $value;
			}
			$this->_packages[$package->name] = $package;
		}
		return $this->_packages;
	}
	/**
	 * Gets an array of packages provided by this repository.
	 * @param boolean $forceRefresh Whether to force a refresh or not, defaults to false.
	 * @return APackage[] an array of packages provided by this repository
	 */
	public function getPackages($forceRefresh = false) {
		if ($forceRefresh || $this->_packages === null) {
			$this->refreshPackages();
		}
		return $this->_packages;
	}
	
}
