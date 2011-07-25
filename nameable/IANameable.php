<?php
/**
 * An interface for nameable objects.
 * Nameable objects implement the getName() and setName() methods to
 * allow consistent access to the "name" of objects.
 * @author Charles Pick
 * @package packages.namable
 */
interface IANameable {
	/**
	 * Gets the name of this item
	 * @return string the name of the item
	 */
	public function getName();
	
	/**
	 * Sets the name of this item
	 * @param string $value the new name for this item
	 */
	public function setName($value);
}
