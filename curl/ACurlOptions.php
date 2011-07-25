<?php
/**
 * Holds a list of options for ACurl.
 * This allows easier access to CURLOPT constants, for example:
 * <pre>
 * $curl->options->ssl_verifypeer = true;
 * </pre>
 * is the same as:
 * <pre>
 * curl_setopt($curl->getHandle(),CURLOPT_SSL_VERIFYPEER,true);
 * </pre>
 * The properties are case insensitive and are automatically turned into the relevant
 * constant names.
 * The options are then applied to the ACurl object's curl handler by calling
 * the {@link applyTo()} method. 
 * @author Charles Pick
 * @package packages.curl
 */
class ACurlOptions extends CAttributeCollection {
	/**
	 * Applies the options to the specified ACurl object.
	 * @param ACurl $curl The ACurl object to apply the options to
	 */
	public function applyTo(ACurl $curl) {
		foreach($this as $key => $value) {
			$constant = constant($this->getConstantName($key));
			curl_setopt($curl->getHandle(), $constant, $value);
		}
	}
	/**
	 * Returns the curl constant name for the given property name.
	 * e.g.
	 * <pre>
	 * echo $curl->options->getConstantName("userAgent"); // outputs "CURLOPT_USERAGENT"
	 * echo $curl->options->getConstantName("ssl_verifypeer"); // outputs "CURLOPT_SSL_VERIFYPEER"
	 * </pre>
	 * @param string $name The name of the property
	 * @return string the name of the constant 
	 */
	public function getConstantName($name) {
		return "CURLOPT_".strtoupper($name);
	}
}
