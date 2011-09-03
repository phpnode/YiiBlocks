<?php
/**
 * Holds information about a resource file.
 * Resources are files that can be attached to models.
 * 
 * @property integer $id the id of the resource
 * @property string $ownerModel the name of the model this resource belongs to
 * @property integer $ownerId the id of the owner model
 * @property string $ownerAttribute the attribute on the owner model that this resource represents
 * @property string $name the resource file name
 * @property string $description the resource description
 * @property string $path the path to the file
 * @property string $type the mime type of the resource
 * @property integer $size the size of the resource in bytes
 * @property integer $userId the id of the user who uploaded this resource
 * @property integer $timeAdded the time the resource was added
 * 
 * @package packages.resources
 * @author Charles Pick
 */
class AResource extends CActiveRecord {
	
	/**
	 * Holds the uploaded file
	 * @var CUploadedFile
	 */
	protected $_uploadedFile;
	
	/**
	 * The file content.
	 * @see getContent()
	 * @see setContent()
	 * @var string
	 */
	protected $_content;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name to instantiate
	 * @return AResource the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	/**
	 * Gets the name of the table to store resource info in.
	 * @see CActiveRecord::tableName()
	 * @return string the table name
	 */
	public function tableName() {
		return "resources";
	}
	
	/**
	 * Gets the full path to the resource file
	 * @return string the path to the resource file
	 */
	public function getFullPath() {
		return Yii::app()->getModule("resources")->resourceDir."/".$this->path;
	}
	
	/**
	 * Gets the content of the resource
	 * @return string the file contents
	 */
	public function getContent() {
		if ($this->_content === null) {
			if (is_object($this->_uploadedFile)) {
				$this->_content = file_get_contents($this->_uploadedFile->getTempName());
			}
			elseif (!$this->isNewRecord) {
				$this->_content = file_get_contents($this->path);
			}
		}
		return $this->_content;
	}
	/**
	 * Sets the content of the resource, but does not update the file.
	 * The file must be updated by calling {@link save()} after setting the content
	 * @param string|CUploadedFile $content the file contents or an uploaded file
	 * @return string the file contents
	 */
	public function setContent(C$content) {
		if (is_object($content) && $content instanceof CUploadedFile) {
			$this->name = $content->name;
			$this->size = $content->size;
			$this->type = $content->type;
			$this->_content = null;
			return $this->_uploadedFile = $content;
		}
		return $this->_content = $content;
	}
	
	/**
	 * Triggered before the resource is saved
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave() {
		if ($this->isNewRecord) {
			$this->timeAdded = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
			if (!Yii::app()->user->isGuest) {
				$this->userId = Yii::app()->user->id;
			}
			$path = Yii::app()->getModule("resources")->resourceDir."/";
			$path .= $this->ownerModel."/".$this->ownerId."/".$this->ownerAttribute."/";
			if (!file_exists($path)) {
				mkdir($path);
			}
			$path .= $this->name;
			$this->path = $path;
			if ($this->size === null) {
				$this->size = (is_object($this->_uploadedFile) ? $this->_uploadedFile->size : strlen($this->content));
			}
		}
		return parent::beforeSave();
	}
	/**
	 * Saves the resource file after the record is saved
	 * @see CActiveRecord::afterSave()
	 */
	public function afterSave() {
		if (is_object($this->_uploadedFile)) {
			$this->_uploadedFile->saveAs($this->fullPath);
		}
		elseif($this->_content !== null) {
			file_put_contents($this->fullPath,$this->_content);
		}
		parent::afterSave();
	}
	/**
	 * Deletes the file after a resource is deleted.
	 * @see CActiveRecord::afterDelete()
	 */
	public function afterDelete() {
		if (file_exists($this->path)) {
			unlink($this->path);
		}
		parent::afterDelete();
	}
	/**
	 * Creates a resource from an uploaded file
	 * @param CUploadedFile $file the uploaded file
	 * @return AResource the newly populated resource
	 */
	public static function fromUploadedFile(CUploadedFile $file) {
		$resource = new AResource;
		$resource->content = $file;
		
		return $resource;
	}
}
