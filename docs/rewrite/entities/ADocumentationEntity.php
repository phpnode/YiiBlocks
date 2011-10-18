<?php
/**
 * A base class for documentation entities.
 * Documentation entities are used by the documentation generator to store information
 * about a particular namespace, class, constant, method, property or function
 * @author Charles Pick
 * @package packages.docs.entities
 *
 * @property ADocumentationEntity $parent the parent, see {@link getParent()} / {@link setParent() }
 */
class ADocumentationEntity extends CComponent {
	/**
	 * The name of the entity
	 * @var string
	 */
	public $name;

	/**
	 * The name of the file that this entity is declared in
	 * @var string
	 */
	public $filename;

	/**
	 * A short description of this entity
	 * @var string
	 */
	public $introduction;

	/**
	 * A full description of this entity
	 * @var string
	 */
	public $description;

	/**
	 * The raw phpdoc comment
	 * @var string
	 */
	public $rawComment;

	/**
	 * The parent for this entity
	 * @var ADocumentationEntity
	 * @see getParent()
	 * @see setParent()
	 */
	protected $_parent;

	/**
	 * The namespace this entity belongs to
	 * @var ADocumentationNamespace
	 */
	protected $_namespace;


	/**
	 * Sets the namespace that this entity belongs to
	 * @param ADocumentationNamespace $namespace the namespace this entity belongs to
	 */
	public function setNamespace($namespace)
	{
		$this->_namespace = $namespace;
	}

	/**
	 * Gets the namespace entity that ths entity belongs to.
	 * If no namespace is provided, the global namespace will be returned
	 * @return ADocumentationNamespace the namespace this entity belongs to
	 */
	public function getNamespace()
	{
		return $this->_namespace;
	}

	/**
	 * Sets the parent entity
	 * @param ADocumentationEntity $parent the parent entity
	 */
	public function setParent($parent)
	{
		$this->_parent = $parent;
	}

	/**
	 * Gets the parent entity
	 * @return ADocumentationEntity the entity this one belongs to
	 */
	public function getParent()
	{
		return $this->_parent;
	}


	public function __toArray() {
		return array();
	}

}
