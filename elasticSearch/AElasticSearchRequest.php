<?php
/**
 * Represents an elastic search request.
 * @author Charles Pick
 * @package packages.elasticSearch
 */
class AElasticSearchRequest extends ACurl {
	/**
	 * Gets the elastic search response
	 * @return AElasticSearchResponse the elastic search response
	 */
	public function exec() {
		if (YII_DEBUG) {
			$summary = "Making Elastic Search Request to ".$this->options->url;
			if (isset($this->options->postfields)) {
				$summary .= " with Post Data: ".print_r($this->options->postfields,true);

			}
			Yii::beginProfile($summary,"elasticSearch");
		}

		$response = new AElasticSearchResponse(parent::exec()->fromJSON());
		if (YII_DEBUG) {
			Yii::endProfile($summary,"elasticSearch");
		}
		return $response;
	}
}