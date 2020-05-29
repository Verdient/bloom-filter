<?php
namespace Verdient\BloomFilter;

use Verdient\BitMap\BitMap;

/**
 * 位图存储
 * @author Verdient。
 */
class BitMapStorage extends \chorus\BaseObject implements StorageInterface
{
	/**
	 * @var BitMap 位图
	 * @author Verdient。
	 */
	protected $_bitmap;

	/**
	 * @inheritdoc
	 * @author Verdient。
	 */
	public function init(){
		parent::init();
		$this->_bitmap = new BitMap();
	}

	/**
	 * @inheritdoc
	 * @author Verdient。
	 */
	public function set($offset, $value){
		return $this->_bitmap->set($offset, $value);
	}

	/**
	 * @inheritdoc
	 * @author Verdient。
	 */
	public function get($offset){
		return $this->_bitmap->get($offset);
	}

	/**
	 * @inheritdoc
	 * @author Verdient。
	 */
	public function reset(){
		$this->_bitmap = new BitMap();
	}
}