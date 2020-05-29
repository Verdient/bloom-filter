<?php
namespace Verdient\BloomFilter;

use chorus\ObjectHelper;

/**
 * 布隆过滤器
 * @author Verdient。
 */
class BloomFilter extends \chorus\BaseObject
{
	/**
	 * @var bool 是否自动重置
	 * @author Verdient。
	 */
	protected $autoReset = false;

	/**
	 * @var float 误判率
	 * @author Verdient。
	 */
	protected $misjudgmentRate = 0.0001;

	/**
	 * @var int 集合大小
	 * @author Verdient。
	 */
	protected $setSize = false;

	/**
	 * @var int 哈希次数
	 * @author Verdient。
	 */
	protected $hashCount = false;

	/**
	 * @var int 最大元素数量
	 * @author Verdient。
	 */
	protected $maxEntries = 1000000;

	/**
	 * @var string|array 存储组件
	 * @author Verdient。
	 */
	protected $storage = 'Verdient\BloomFilter\BitMapStorage';

	/**
	 * @var StorageInterface 集合
	 * @author Verdient。
	 */
	protected $_set = null;

	/**
	 * @var array 哈希集合
	 * @author Verdient。
	 */
	protected $_hashes = [];

	/**
	 * @var int 元素总数
	 * @author Verdient。
	 */
	protected $_count = 0;

	/**
	 * @inheritdoc
	 * @author Verdient。
	 */
	public function init(){
		parent::init();
		$this->_set = ObjectHelper::create($this->storage);
		$this->reset();
	}

	/**
	 * 添加元素
	 * @param mixed $value 要添加的值
	 * @return BloomFilter
	 * @author Verdient。
	 */
	public function add($value){
		if($this->autoReset === true){
			if(is_int($this->maxEntries) && $this->_count >= $this->maxEntries){
				$this->reset();
			}
		}
		$this->_count ++;
		foreach($this->_hashes as $hash){
			$crc = $this->calculateCRC($hash->hash($this->serialize($value)));
			$this->_set->set($crc, 1);
		}
		return $this;
	}

	/**
	 * 计算校验位
	 * @param string 要计算校验位的内容
	 * @return int
	 * @author Verdient。
	 */
	protected function calculateCRC($value){
		return abs(crc32($value)) % $this->setSize;
	}

	/**
	 * 重置
	 * @return BloomFilter
	 * @author Verdient。
	 */
	public function reset(){
		if(!$this->setSize){
			$this->setSize = intval(-round(($this->maxEntries * log($this->misjudgmentRate)) / pow(log(2), 2)));
		}
		if(!$this->hashCount){
			$this->hashCount = intval(round($this->setSize * log(2) / $this->maxEntries)) ?: 1;
		}
		$this->_set->reset();
		$this->_count = 0;
		$this->_hashes = [];
		for($i = 0; $i < $this->hashCount; $i++){
			$this->_hashes[] = new Hash();
		}
		return $this;
	}

	/**
	 * 判断一个元素是否在集合内
	 * @param mixed $value 待验证的元素
	 * @return bool
	 * @author Verdient。
	 */
	public function has($value){
		foreach($this->_hashes as $hash){
			$crc = $this->calculateCRC($hash->hash($this->serialize($value)));
			if(!$this->_set->get($crc)){
				return false;
			}
		}
		return true;
	}

	/**
	 * 获取总数
	 * @return int
	 * @author Verdient。
	 */
	public function getCount(){
		return $this->_count;
	}

	/**
	 * 序列化
	 * @param mixed $value 待序列化的值
	 * @return string
	 * @author Verdient。
	 */
	protected function serialize($value){
		return serialize($value);
	}
}