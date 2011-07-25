<?php
/**
 * @author Charles Pick
 * @package packages.moderator.interfaces 
 */
interface IAModeratable {
	const PENDING = 'pending';
	const APPROVED = 'approved';
	const DENIED = 'denied';
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
	
	/**
	 * Whether this particular object should be moderated.
	 * @return boolean true if the object should be moderated
	 */
	public function isModeratable();
	
}
