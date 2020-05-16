<?php
namespace Verdient\BloomFilter;

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
	 * @var int 集合大小
	 * @author Verdient。
	 */
	public $setSize = 1000000;

	/**
	 * @var int 哈希次数
	 * @author Verdient。
	 */
	public $hashCount = 10;

	/**
	 * @var int 最大元素数量
	 * @author Verdient。
	 */
	public $maxEntries = false;

	/**
	 * @var int 最大比率
	 * @author Verdient。
	 */
	public $maxRate = false;

	/**
	 * @var array 集合
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
			if(is_int($this->maxRate) && $this->getRate() >= $this->maxRate){
				$this->reset();
			}
		}
		$this->_count ++;
		foreach($this->_hashes as $hash){
			$crc = $this->calculateCRC($hash->hash($this->serialize($value)));
			$this->_set[$crc] = 1;
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
		$this->_set = str_repeat('0', $this->setSize);
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
			if(!$this->_set[$crc]){
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
	 * 获取比率
	 * @return float
	 * @author Verdient。
	 */
	public function getRate(){
		return substr_count($this->_set, 1) / $this->setSize * 100;
	}

	/**
	 * 序列化
	 * @param mixed $value 待序列化的值
	 * @return string
	 * @author Verdient。
	 */
	public function serialize($value){
		return serialize($value);
	}
}