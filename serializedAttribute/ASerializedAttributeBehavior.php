<?php

class ASerializedAttributeBehavior extends CActiveRecordBehavior {
	/**
	 * The attribute names on the owner that should be serialized and unserialized
	 * @var array
	 */
	public $attributes = array();
	/**
	 * Responds to {@link CActiveRecord::onBeforeSave} event, serializes the attributes.
	 * @param CModelEvent $event event parameter
	 */
	public function beforeSave($event) {
		foreach($this->attributes as $attribute) {
			$event->sender->{$attribute} = serialize($event->sender->{$attribute});
		}
	}

	/**
	 * Responds to {@link CActiveRecord::onAfterSave} event, unserializes the attributes.
	 * @param CModelEvent $event event parameter
	 */
	public function afterSave($event) {
		foreach($this->attributes as $attribute) {
			$event->sender->{$attribute} = unserialize($event->sender->{$attribute});
		}
	}

	/**
	 * Responds to {@link CActiveRecord::onAfterFind} event, unserializes the attributes.
	 * @param CModelEvent $event event parameter
	 */
	public function afterFind($event) {
		foreach($this->attributes as $attribute) {
			if ($event->sender->{$attribute} !== null) {
				$event->sender->{$attribute} = unserialize($event->sender->{$attribute});
			}
		}
	}
}