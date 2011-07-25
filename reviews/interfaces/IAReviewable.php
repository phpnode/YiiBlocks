<?php
/**
 * @author Charles Pick
 * @package packages.reviews.interfaces 
 */
interface IAReviewable {
	/**
	 * Gets the id of the object being reviewed.
	 * @return integer the id of the object being reviewed.
	 */
	public function getId();
	
	/**
	 * Gets the name of the class that owns the reviews.
	 * @return string the owner model class name
	 */
	public function getClassName();
	
}
