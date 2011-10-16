<?php
/**
 * Represents a stack with a maximum size.
 * When the stack reaches the maximum size,
 * further additions will cause the first element in the stack
 * to be shifted off the start before the new item is added to the end of the stack.
 * @author Charles Pick
 * @package packages.fixedStack
 */
class AFixedStack extends CStack {
	/**
	 * internal data storage
	 * @var array
	 */
	protected $_d=array();
	/**
	 * number of items
	 * @var integer
	 */
	protected $_c=0;

	/**
	 * The maximum size of the stack
	 * @var integer
	 */
	public $maxSize;

	/**
	 * Constructor.
	 * Initializes the stack with an array or an iterable object.
	 * @param integer $maxSize the maximum size of the stack
	 * @param array $data the initial data. Default is null, meaning no initialization.
	 */
	public function __construct($maxSize = null, $data=null) {
		$this->maxSize = $maxSize;
		if($data!==null) {
			$this->copyFrom($data);
		}
	}

	/**
	 * @return array the list of items in stack
	 */
	public function toArray()
	{
		return $this->_d;
	}

	/**
	 * Copies iterable data into the stack.
	 * Note, existing data in the list will be cleared first.
	 * @param mixed $data the data to be copied from, must be an array or object implementing Traversable
	 * @throws CException If data is neither an array nor a Traversable.
	 */
	public function copyFrom($data)
	{
		if(is_array($data) || ($data instanceof Traversable))
		{
			$this->clear();
			foreach($data as $item)
			{
				$this->_d[]=$item;
				++$this->_c;
			}
		}
		else if($data!==null)
			throw new CException(Yii::t('yii','Stack data must be an array or an object implementing Traversable.'));
	}

	/**
	 * Removes all items in the stack.
	 */
	public function clear()
	{
		$this->_c=0;
		$this->_d=array();
	}

	/**
	 * @param mixed $item the item
	 * @return boolean whether the stack contains the item
	 */
	public function contains($item)
	{
		return array_search($item,$this->_d,true)!==false;
	}

	/**
	 * Returns the item at the top of the stack.
	 * Unlike {@link pop()}, this method does not remove the item from the stack.
	 * @return mixed item at the top of the stack
	 * @throws CException if the stack is empty
	 */
	public function peek()
	{
		if($this->_c)
			return $this->_d[$this->_c-1];
		else
			throw new CException(Yii::t('yii','The stack is empty.'));
	}

	/**
	 * Pops up the item at the top of the stack.
	 * @return mixed the item at the top of the stack
	 * @throws CException if the stack is empty
	 */
	public function pop()
	{
		if($this->_c)
		{
			--$this->_c;
			return array_pop($this->_d);
		}
		else
			throw new CException(Yii::t('yii','The stack is empty.'));
	}

	/**
	 * Pushes an item into the stack.
	 * @param mixed $item the item to be pushed into the stack
	 */
	public function push($item)
	{
		if ($this->maxSize !== null && $this->_c == $this->maxSize) {
			$this->shift();
		}
		++$this->_c;
		array_push($this->_d,$item);
	}
	/**
	 * Shifts an item off the start of the stack
	 * @return mixed the item shifted off the start of the stack
	 */
	public function shift() {
		--$this->_c;
		return array_shift($this->_d);
	}

	/**
	 * Returns an iterator for traversing the items in the stack.
	 * This method is required by the interface IteratorAggregate.
	 * @return Iterator an iterator for traversing the items in the stack.
	 */
	public function getIterator()
	{
		return new CStackIterator($this->_d);
	}

	/**
	 * Returns the number of items in the stack.
	 * @return integer the number of items in the stack
	 */
	public function getCount()
	{
		return $this->_c;
	}

	/**
	 * Returns the number of items in the stack.
	 * This method is required by Countable interface.
	 * @return integer number of items in the stack.
	 */
	public function count()
	{
		return $this->getCount();
	}
}