<?php
/**
 * An interface for JSON encodable objects.
 *
 */
interface IAJSONEncodable {
	/**
	 * Returns an array of attributes and their values, these attributes will be
	 * @return array
	 */
	public function toJSON();
}