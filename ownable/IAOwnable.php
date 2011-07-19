<?php
/**
 * @author Charles Pick
 * @package blocks.ownable.interfaces 
 */
interface IAOwnable {
	/**
	 * Gets the id of the object that owns this item
	 * @return integer the id of the object that owns this item.
	 */
	public function getOwnerId();
	
	/**
	 * Gets the name of the class that owns this item
	 * @return string the owner model class name
	 */
	public function getOwnerClassName();
	
	
	
}
