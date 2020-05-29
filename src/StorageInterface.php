<?php
namespace Verdient\BloomFilter;

/**
 * 存储接口
 * @author Verdient。
 */
interface StorageInterface
{
	/**
	 * 设置位
	 * @param int $offset 偏移量
	 * @param int $value 值
	 * @return bool
	 * @author Verdient。
	 */
	public function set($offset, $value);

	/**
	 * 获取位
	 * @param int $offset 偏移量
	 * @return int|false
	 * @author Verdient。
	 */
	public function get($offset);

	/**
	 * 重置
	 * @return bool
	 * @author Verdient。
	 */
	public function reset();
}