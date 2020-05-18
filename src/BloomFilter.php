<?php
namespace Verdient\BloomFilter;

use Verdient\BitMap\BitMap;

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
	public $autoReset = false;

	/**
	 * @var float 误判率
	 * @author Verdient。
	 */
	public $misjudgmentRate = 0.0001;

	/**
	 * @var int 集合大小
	 * @author Verdient。
	 */
	public $setSize = false;

	/**
	 * @var int 哈希次数
	 * @author Verdient。
	 */
	public $hashCount = false;

	/**
	 * @var int 最大元素数量
	 * @author Verdient。
	 */
	public $maxEntries = 1000000;

	/**
	 * @var BitMap 集合
	 * @author Verdient。
	 */
	protected $_set;

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
		$this->_set = new BitMap(['size' => $this->setSize]);
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