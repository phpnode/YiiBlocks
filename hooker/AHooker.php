<?php
/**
 * Allow modules and packages to register extra functionality in the form of hooks.
 * These hooks can then be called later on by user interface widgets etc
 * @package packages.hooker
 * @author Charles Pick
 */
class AHooker extends CApplicationComponent {
	/**
	 * Holds the hooks
	 * @var AHook[]
	 */
	protected $_hooks = array();
	/**
	 * Gets the registered hooks
	 * @return AHook[] an array of registered hooks
	 */
	public function getHooks() {
		return $this->_hooks;
	}
	/**
	 * Registers the given hooks with the hooker
	 * @param AHook[]|array $hooks An array of hooks or an array of configuration items
	 */
	public function setHooks($hooks) {
		$this->_hooks = array();
		foreach($hooks as $hook) {
			$this->attachHook($hook);
		}
		return $this->_hooks;
	}
	/**
	 * Attaches a hook to the hooker
	 * @param AHook|array $hook the hook to attach, either an instance of AHook or the configuration for one
	 */
	public function attachHook($hook) {
		if (!($hook instanceof AHook)) {
			$config = $hook;
			$hook = new AHook;
			foreach($config as $key => $value) {
				$hook->{$key} = $value;
			}
		}
		$this->_hooks[] = $hook;
	}
	
}
