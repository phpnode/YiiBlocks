<?php
/**
 * @author Charles Pick
 * @package packages.moderator.interfaces 
 */
interface IAVotable {

	/**
	 * Gets the id of the object being moderated.
	 * @return integer the id of the object being moderated.
	 */
	public function getId();
	
	/**
	 * Gets the name of the class that is being moderated.
	 * @return string the owner model class name
	 */
	public function getClassName();
	
	
}
