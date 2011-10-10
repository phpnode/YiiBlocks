<?php
/**
 * Returns information about the system the application is running on
 * @author Charles Pick
 * @package packages.sysinfo
 */
class ASystemInformation extends CApplicationComponent {
	/**
	 * Gets the current load average
	 * @return false|float the load average, or false if it could not be determined
	 */
	public function getCurrentLoad() {
		if (stristr(PHP_OS,"win")) {
			return $this->getCurrentLoadWindows();
		}
		return array_shift(sys_getloadavg());
	}
	/**
	 * Gets the current load average on windows
	 * @return float|false the the system load average, or false if it cannot be determined
	 */
	protected function getCurrentLoadWindows() {
		ob_start();
		passthru('typeperf -sc 1 "\processor(_total)\% processor time"',$status);
		$content = ob_get_contents();
		ob_end_clean();
		if ($status == 0 && preg_match("/\,\"([0-9]+\.[0-9]+)\"/",$content,$load)) {
			return $load[1];
		}
		return false;
	}

}