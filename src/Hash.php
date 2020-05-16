<?php
namespace Verdient\BloomFilter;

/**
 * 哈希
 * @author Verdient。
 */
class Hash extends \chorus\BaseObject
{
	/**
	* @var string 签名秘钥
	* @author
	*/
	public $key;

	/**
	 * @var string 签名方法
	 * @author Verdient。
	 */
	public $algo = 'SHA256';

	/**
	 * @inheritdoc
	 * @author Verdient。
	 */
	public function init(){
		$this->key = random_bytes(256);
	}

	/**
	 * 哈希
	 * @param string $string 待哈希的字符串
	 * @param string $algo 使用的哈希算法
	 * @return string
	 * @author Verdient。
	 */
	public function hash($string, $algo = null){
		$algo = $algo ?: $this->algo;
		return hash_hmac($algo, $string, $this->key);
	}
}