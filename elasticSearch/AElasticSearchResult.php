<?php
/**
 * Represents an elastic search result
 * @author Charles Pick
 * @package packages.elasticSearch
 */
class AElasticSearchResult extends AElasticSearchDocument {
	/**
	 * The position in the search results
	 * @var integer
	 */
	protected $_position;
	/**
	 * The elastic search criteria
	 * @var AElasticSearchCriteria
	 */
	protected $_criteria;

	/**
	 * Holds the document score
	 * @var integer
	 */
	protected $_score;

	/**
	 * Sets the document score
	 * @param integer $score
	 */
	public function setScore($score) {
		$this->_score = $score;
	}

	/**
	 * Gets the score for this document
	 * @return integer
	 */
	public function getScore() {
		return $this->_score;
	}

	/**
	 * Sets the elastic search criteria for this search result
	 * @param AElasticSearchCriteria $criteria the elastic search criteria
	 */
	public function setCriteria($criteria) {
		$this->_criteria = $criteria;
	}

	/**
	 * Gets the elastic search criteria for this search result
	 * @return AElasticSearchCriteria the elastic search criteria
	 */
	public function getCriteria() {
		return $this->_criteria;
	}

	/**
	 * Sets the position in the search results
	 * @param integer $position
	 */
	public function setPosition($position) {
		$this->_position = $position;
	}

	/**
	 * Gets the position in the search results
	 * @return integer the position in the search results
	 */
	public function getPosition() {
		return $this->_position;
	}


}