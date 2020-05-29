<?php
namespace Verdient\BloomFilter;

use Redis;

/**
 * Redis存储
 * @author Verdient。
 */
class RedisStorage extends \chorus\BaseObject implements StorageInterface
{
	/**
	 * @var string 主机
	 * @author Verdient。
	 */
	protected $host = '127.0.0.1';

	/**
	 * @var int 端口
	 * @author Verdient。
	 */
	protected $port = 6379;

	/**
	 * @var string 密码
	 * @author Verdient。
	 */
	protected $password = null;

	/**
	 * @var int 数据库名称
	 * @author Verdient。
	 */
	protected $db = 0;

	/**
	 * @var string 存储Key
	 * @author Verdient。
	 */
	protected $key = null;

	/**
	 * @var Redis Redis客户端
	 * @author Verdient。
	 */
	protected $_client = null;

	/**
	 * 获取存储Key
	 * @author Verdient。
	 */
	public function getKey(){
		if($this->key === null){
			$this->key = 'bloom_filter_' . bin2hex(random_bytes(32));
		}
		return $this->key;
	}

	/**
	 * 获取Redis客户端
	 * @return Object
	 * @author Verdient。
	 */
	public function getClient(){
		if($this->_client === null){
			$this->_client = new Redis();
			$this->_client->connect($this->host, $this->port);
			if($this->password){
				$this->_client->auth($this->password);
			}
			$this->_client->select($this->db);
		}
		return $this->_client;
	}

	/**
	 * 设置位
	 * @param int $offset 偏移量
	 * @param int $value 值
	 * @return bool
	 * @author Verdient。
	 */
	public function set($offset, $value){
		return $this->getClient()->setbit($this->getKey(), $offset, $value);
	}

	/**
	 * 获取位
	 * @param int $offset 偏移量
	 * @return int|false
	 * @author Verdient。
	 */
	public function get($offset){
		return $this->getClient()->getbit($this->getKey(), $offset);
	}

	/**
	 * @inheritdoc
	 * @author Verdient。
	 */
	public function reset(){
		$this->getClient()->del($this->getKey());
	}
}