<?php
/**
 * Represents an solr result
 * @author Charles Pick
 * @package packages.solr
 */
class ASolrResult extends ASolrDocument {
	/**
	 * The position in the search results
	 * @var integer
	 */
	protected $_position;
	/**
	 * The solr criteria
	 * @var ASolrCriteria
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
	 * Sets the solr criteria for this search result
	 * @param ASolrCriteria $criteria the solr criteria
	 */
	public function setCriteria($criteria) {
		$this->_criteria = $criteria;
	}

	/**
	 * Gets the solr criteria for this search result
	 * @return ASolrCriteria the solr criteria
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