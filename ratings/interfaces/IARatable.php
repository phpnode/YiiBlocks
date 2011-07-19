<?php
/**
 * @author Charles Pick
 * @package blocks.reviews.interfaces 
 */
interface IARatable {
	/**
	 * Gets the id of the object being rated.
	 * @return integer the id of the object being rated.
	 */
	public function getId();
	
	/**
	 * Gets the name of the class that owns the ratings.
	 * @return string the owner model class name
	 */
	public function getClassName();
	
}
