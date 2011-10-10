<?php
/**
 * Allows iterating over large data sets without consuming ridiculous amounts of RAM
 * Based on ActiveRecordIteractor by allain@machete.ca see {@link https://github.com/allain/activerecorditerator/blob/master/ActiveRecordIterator.php}
 * @author Charles Pick
 * @package packages.dataProviderIterator
 */
class ADataProviderIterator implements Iterator, Countable {
	/**
	 * The data provider to iterate over
	 * @var CDataProvider
	 */
	public $dataProvider;

	/**
	 * The current index
	 * @var integer
	 */
	protected $_currentIndex = -1;

	/**
	 * The current page in the pagination
	 * @var integer
	 */
	protected $_currentPage = 0;

	/**
	 * The total number of items
	 * @var integer
	 */
	protected $_totalItems = -1;

	/**
	 * The current set of items
	 * @var array
	 */
	protected $_items;
	/**
	 * Constructor. Sets the data provider to iterator over
	 * @param CDataProvider $dataProvider the data provider to iterate over
	 */
	public function __construct(CDataProvider $dataProvider) {
		$this->dataProvider = $dataProvider;
		$this->_totalItems = $dataProvider->getTotalItemCount();
	}

	/**
	 * Sets the current index position
	 * @param integer $currentIndex the current index positon
	 */
	public function setCurrentIndex($currentIndex) {
		$this->_currentIndex = $currentIndex;
	}

	/**
	 * Gets the current index position
	 * @return integer index position
	 */
	public function getCurrentIndex() {
		return $this->_currentIndex;
	}

	/**
	 * Gets the current set of items to iterate over
	 * @return array the current set items to iterate over
	 */
	public function getItems(){
		return $this->_items;
	}

	/**
	 * Sets the total number of items to iterate over
	 * @param int $totalItems
	 */
	public function setTotalItems($totalItems) {
		$this->_totalItems = $totalItems;
	}

	/**
	 * Gets the total number of items to iterate over
	 * @return integer
	 */
	public function getTotalItems()	{
		return $this->_totalItems;
	}

	/**
	 * Loads the page of results
	 * @return array the items from the next page of results
	 */
	protected function loadPage() {
		$this->dataProvider->getPagination()->setCurrentPage($this->_currentPage);
		return $this->_items = $this->dataProvider->getData(true);
	}
	/**
	 * Gets the current item in the list.
	 * This method is required by the Iterator interface
	 * @return mixed the current item in the list
	 */
	public function current() {
		$items = $this->getItems();
		return $items[$this->getCurrentIndex()];
	}

	/**
	 * Gets the key of the current item.
	 * This method is required by the Iterator interface
	 * @return integer the key of the current item
	 */
	public function key() {
		$pagination = $this->dataProvider->getPagination();
		$currentPage = $this->_currentPage;
		$pageSize = $pagination->getPageSize();
		return $currentPage * $pageSize + $this->getCurrentIndex();
	}
	/**
	 * Moves the pointer to the next item in the list.
	 * This method is required by the Iterator interface
	 */
	public function next() {
		$pagination = $this->dataProvider->getPagination();
		$this->_currentIndex++;
		if ($this->getCurrentIndex() >= $pagination->getPageSize()) {
			$this->_currentPage++;
			$this->_currentIndex = 0;
			$this->loadPage();
		}
	}
	/**
	 * Rewinds the iterator to the start of the list.
	 * This method is required by the Iterator interface
	 */
	public function rewind() {
		$this->_currentIndex = 0;
		$this->_currentPage = 0;
		$this->loadPage();
	}
	/**
	 * Checks if the current position is valid or not.
	 * This method is required by the Iterator interface
	 * @return boolean true if this index is valid
	 */
	public function valid() {
		return $this->key() < $this->getTotalItems();
	}
	/**
	 * Gets the total number of items in the dataProvider
	 * This method is required by the Countable interface
	 * @return integer the total number of items
	 */
	public function count() {
		return $this->getTotalItems();
	}
}