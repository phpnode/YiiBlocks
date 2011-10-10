<?php
/**
 * A base class for member declarations
 * @author Charles Pick
 * @package packages.docs.states
 */
abstract class AMemberDeclarationState extends APHPTokenReaderState {
	/**
	 * Whether the member is abstract or not
	 * @var boolean
	 */
	public $isAbstract = false;

	/**
	 * Whether the member is final or not
	 * @var boolean
	 */
	public $isFinal = false;

	/**
	 * Whether the member is static or not
	 * @var boolean
	 */
	public $isStatic = false;

	/**
	 * Whether the member is public or not
	 * @var boolean
	 */
	public $isPublic = false;

	/**
	 * Whether the member is protected or not
	 * @var boolean
	 */
	public $isProtected = false;

	/**
	 * Whether the member is private or not
	 * @var boolean
	 */
	public $isPrivate = false;
}