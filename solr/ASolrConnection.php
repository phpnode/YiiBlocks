<?php
/**
 * A simple wrapper for Solr
 * @author Charles Pick
 * @package packages.solr
 */
class ASolrConnection extends CApplicationComponent {
	/**
	 * The solr client object
	 * @var SolrClient
	 */
	protected $_client;
	/**
	 * The options used to initialize the client class
	 * @var CAttributeCollection
	 */
	protected $_clientOptions;
	/**
	 * Sets the solr client
	 * @param SolrClient $client the solr client
	 */
	public function setClient($client) {
		$this->_client = $client;
	}

	/**
	 * Gets the solr client instance
	 * @return SolrClient the solr client
	 */
	public function getClient() {
		if ($this->_client === null) {
			$this->_client = new SolrClient($this->getClientOptions()->toArray());
		}
		return $this->_client;
	}

	/**
	 * Sets the solr client options
	 * @param array|CAttributeCollection $clientOptions the client options
	 */
	public function setClientOptions($clientOptions) {
		if (!($clientOptions instanceof CAttributeCollection)) {
			$data = $clientOptions;
			$clientOptions = new CAttributeCollection();
			$clientOptions->caseSensitive = true;
			foreach($data as $key => $value) {
				$clientOptions->add($key,$value);
			}
		}
		else {
			$clientOptions->caseSensitive = true;
		}
		$this->_clientOptions = $clientOptions;
	}

	/**
	 * Gets the solr client options
	 * @return CAttributeCollection the client options
	 */
	public function getClientOptions() {
		if ($this->_clientOptions === null) {
			$clientOptions = new CAttributeCollection();
			$clientOptions->caseSensitive = true;
			if ($this->_client !== null) {
				foreach($this->_client->getOptions() as $key => $value) {
					$clientOptions->add($key,$value);
				}
			}
			$this->_clientOptions = $clientOptions;
		}
		return $this->_clientOptions;
	}
	/**
	 * Adds a document to the solr index
	 * @param ASolrDocument|SolrInputDocument $document the document to add to the index
	 * @return boolean true if the document was indexed successfully
	 */
	public function index($document) {
		if ($document instanceof ASolrDocument) {
			$document = $document->getInputDocument();
		}
		elseif (is_array($document) || $document instanceof Traversable) {
			$document = (array) $document;
			foreach($document as $key => $value) {
				if ($value instanceof ASolrDocument) {
					$document[$key] = $value->getInputDocument();
				}
			}
			return $this->getClient()->addDocuments($document)->success();
		}
		$response = $this->getClient()->addDocument($document);
		return $response->success();
	}


	/**
	 * Deletes a document from the solr index
	 * @param mixed $document the document to remove from the index, this can be the an id or an instance of ASoldDocument, an array of multiple values can also be used
	 * @return boolean true if the document was deleted successfully
	 */
	public function delete($document) {
		if ($document instanceof ASolrDocument) {
			$document = $document->getPrimaryKey();
		}
		elseif (is_array($document) || $document instanceof Traversable) {
			$document = (array) $document;
			foreach($document as $key => $value) {
				if ($value instanceof ASolrDocument) {
					$document[$key] = $value->getPrimaryKey();
				}
			}
			return $this->getClient()->deleteByIds($document)->success();
		}
		return $this->getClient()->deleteById($document)->success();
	}
	/**
	 * Sends a commit command to solr.
	 * @return boolean true if the commit was successful
	 */
	public function commit() {
		return $this->getClient()->commit()->success();
	}

	public function search(ASolrCriteria $criteria) {
		return new ASolrQueryResponse($this->getClient()->query($criteria)->getResponse(),$criteria);
	}
}
