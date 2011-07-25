<?php
/**
 * Allows the application to use redis as a cache component.
 * Depends on the phpredis php extension.
 * @author Charles Pick
 * @package packages.redis
 */
class ARedisCache extends CCache {

		
	private $_server;
	private $_redis;
	
	/**
	 * Initializes this application component.
	 * This method is required by the {@link IApplicationComponent} interface.
	 */
	public function init() {
		parent::init();
		$this->getRedis();
		
	}
	
	/**
	 * Gets the Redis instance
	 * @return Redis the redis instance
	 */
	public function getRedis() {
		if ($this->_redis === null)	{
			$this->_redis = new Redis;
			$this->_redis->connect($this->server->host,$this->server->port);
		}
		return $this->_redis;
	}
	
	/**
	 * Sets the Redis instance associated with this component
	 * @param Redis $value The redis instance
	 */
	public function setRedis(Redis $value) {
		$this->_redis = $value;
	}
	/**
	 * Gets the server configuration for this redis instance
	 * @return ARedisServerConfiguration The server config
	 */
	public function getServer() {
		if ($this->_server === null) {
			$this->_server = new ARedisServerConfiguration(array());
		}
		return $this->_server;
	}
	
	/**
	 * Sets the server configuration for this redis instance.
	 * @param array The server config, key => value
	 */
	public function setServer($config) {
		$this->_server=new ARedisServerConfiguration($config);
		
	}
	
	/**
	 * Retrieves a value from cache with a specified key.
	 * This is the implementation of the method declared in the parent class.
	 * @param string a unique key identifying the cached value
	 * @return string the value stored in cache, false if the value is not in the cache or expired.
	 */
	protected function getValue($key) {
		return $this->_redis->get($key);
	}

	/**
	 * Retrieves multiple values from cache with the specified keys.
	 * @param array a list of keys identifying the cached values
	 * @return array a list of cached values indexed by the keys
	 * @since 1.0.8
	 */
	protected function getValues($keys)	{
		return $this->_redis->mget($keys);
	}

	/**
	 * Stores a value identified by a key in cache.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string the key identifying the value to be cached
	 * @param string the value to be cached
	 * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	protected function setValue($key,$value,$expire = 0) {
			
		$this->_redis->set($key,$value);
		if ($expire) {
			$this->_redis->expire($key,$expire);
		}
	}

	/**
	 * Stores a value identified by a key into cache if the cache does not contain this key.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string the key identifying the value to be cached
	 * @param string the value to be cached
	 * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	protected function addValue($key,$value,$expire) {
		if($expire>0)
			$expire+=time();
		else
			$expire=0;

		return $this->_redis->add($key,$value,$expire);
	}

	/**
	 * Deletes a value with the specified key from cache
	 * This is the implementation of the method declared in the parent class.
	 * @param string the key of the value to be deleted
	 * @return boolean if no error happens during deletion
	 */
	protected function deleteValue($key) {
		return $this->_redis->delete($key);
	}

	/**
	 * Deletes all values from cache.
	 * Be careful of performing this operation if the cache is shared by multiple applications.
	 */
	public function flush() {
		return $this->_redis->flush();
	}
	
	
}
/**
 * Holds configuration information about a redis server
 * @package packages.redis
 * @author Charles Pick
 */
class ARedisServerConfiguration extends CConfiguration {
	/**
	 * @var string redis server hostname or IP address
	 */
	public $host = "localhost";
	/**
	 * @var integer redis server port
	 */
	public $port=6379;
	/**
	 * @var integer which database to use
	 */
	public $database=1;
	
	/**
	 * @var string the alias to use for this server
	 */
	public $alias="first";

	/**
	 * Constructor.
	 * @param array list of redis server configurations.
	 * @throws CException if the configuration is not an array
	 */
	public function __construct($config)
	{
		if(is_array($config)) {
			foreach($config as $key=>$value)
				$this->$key=$value;
			if($this->host===null)
				throw new CException(Yii::t('blocks','ARedis server configuration must have "host" value.'));
		}
		else
			throw new CException(Yii::t('blocks','ARedis server configuration must be an array.'));
	}
}