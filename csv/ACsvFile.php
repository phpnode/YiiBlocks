<?php
/**
 * Allows easy access to csv files
 * @package packages.csv
 * @author Charles Pick
 */
class ACsvFile extends CTypedList {
	/**
	 * The filename for this csv file
	 * @var string
	 */
	public $filename;
	/**
	 * Constructor.
	 * @param string $filename the csv filename
	 */
	public function __construct($filename = null)
	{
		parent::__construct("CAttributeCollection");
		if ($filename !== null) {
			$this->filename = $filename;
			$this->load();
		}
	}
	/**
	 * Loads data from a CSV file
	 * @return ACsvFile the csv file with the data loaded
	 */
	public function load() {
		$handle = fopen($this->filename,"r");
		if ($handle === false) {
			return $this;
		}
		$n = 0;
		$keys = array();
		while(($row = fgetcsv($handle)) !== false) {
			if ($n == 0) {
				$keys = array_values($row);
			}
			else {
				$this[] = new CAttributeCollection(array_combine($keys,array_values($row)));
			}
			$n++;
		}
		return $this;
	}
}