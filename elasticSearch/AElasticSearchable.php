<?php
/**
 * Allows easy indexing and searching for Active Records
 * @package packages.elasticSearch
 * @author Charles Pick
 */
class AElasticSearchable extends CActiveRecordBehavior {
	/**
	 * The model attributes that should be indexed,
	 * defaults to null meaning index all attributes
	 * @var array
	 */
	public $indexAttributes;

	/**
	 * The mapping for this index.
	 * Defaults to null meaning elastic search will automatically create a mapping
	 * @var array
	 */
	public $mapping;

	/**
	 * The name of the elastic search application component,
	 * defaults to "elasticSearch"
	 * @var string
	 */
	public $componentID  = "elasticSearch";


	/**
	 * The name of the index to use for storing the items.
	 * Defaults to null meaning use the value of {@link AElasticSearch::$defaultIndex}
	 * @var string
	 */
	public $indexName;

	/**
	 * The type to use when storing the items.
	 * Defaults to null meaning use the class name of the behavior owner.
	 * @var string
	 */
	public $type;

	/**
	 * Triggered after the model saves, this is where we index the item
	 * @see CActiveRecordBehavior::afterSave()
	 */
	public function afterSave() {

		return true;
	}

	/**
	 * Triggered after the model is deleted, this is where we de-index the item
	 * @see CActiveRecordBehavior::afterDelete()
	 */
	public function afterDelete() {

		return true;
	}

	/**
	 * Sends the mapping for this index to the elastic search server
	 * @return boolean Whether the put succeeded or not
	 */
	public function putMapping() {
		if ($this->mapping === null) {
			$this->mapping = array();
		}
		if ($this->type === null) {
			$type = get_class($this->owner);
		}
		else {
			$type = $this->type;
		}
		$data = array(
				$type => array(
					"properties" => $this->mapping
				)
			);

		$response = Yii::app()->{$this->componentID}->putMapping($this->indexName,$type, $data);
		return $response;
		if (!is_array($response) || !$response['ok']) {
			return false;
		}
		return true;
	}

	/**
	 * Deletes the mapping for this index from the elastic search server
	 * @return boolean Whether the delete succeeded or not
	 */
	public function deleteMapping() {
		if ($this->type === null) {
			$type = get_class($this->owner);
		}
		else {
			$type = $this->type;
		}
		$response = Yii::app()->{$this->componentID}->deleteMapping($this->indexName,$type);
		if (!is_array($response) || !$response['ok']) {
			return false;
		}
		return true;
	}


	/**
	 * Adds the record to the elastic search index
	 */
	public function index() {
		if ($this->indexAttributes === null) {
			$attributes = array();
		}
		else {
			$attributes = $this->indexAttributes;
		}
		$data = array();
		foreach($attributes as $attribute) {
			$data[$attribute] = $this->owner->{$attribute};
		}
		if ($this->type === null) {
			$type = get_class($this->owner);
		}
		else {
			$type = $this->type;
		}
		$id = $this->owner->primaryKey;
		if (is_array($id)) {
			$id = implode("_",$id);
		}
		$response = Yii::app()->{$this->componentID}->index($this->indexName, $type, $id, $data);
		if (!is_object($response) || !$response->ok) {
			return false;
		}
		return true;
	}
	/**
	 * Searches for records using values from this model.
	 * @return array An array of active records matching the criteria
	 */
	public function search() {
		if ($this->indexAttributes === null) {
			$attributes = array();
		}
		else {
			$attributes = $this->indexAttributes;
		}
		$query = array();
		foreach($attributes as $attribute) {
			$query[$attribute] = $this->owner->{$attribute};
		}
		print_r($query);
	}
}
