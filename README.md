# Bloom Filter
布隆过滤器

### 安装
`composer require verdient/bloom-filter`

### 创建新的过滤器
```php
use Verdient\BloomFilter\BloomFilter;

/**
 * @var bool 是否自动重置
 */
$autoReset = false;

/**
 * @var float 误判率，最大为1，最小为0
 */
$misjudgmentRate = 0.001;

/**
 * @var int 集合大小，默认为false
 */
$setSize = false;

/**
 * @var int 哈希次数，默认为false
 */
$hashCount = false;

/**
 * @var int 预计最大元素数量，默认为1000000
 * autoReset设置为true时有效
 * 当已加入的元素数量大于maxEntries时，会自动重置过滤器
 */
$maxEntries = 1000000;

/**
 * @var string|array 存储组件配置
 * 可选Verdient\BloomFilter\BitMapStorage和Verdient\BloomFilter\RedisStorage
 * 默认为BitMapStorage
 * RedisStorage需配置参数，分别为
 *  'host' => '127.0.0.1', // 默认为127.0.0.1
 *  'password' => null, // 默认为null
 *  'db' => 0, // 默认为0
 *  'key' => null // 默认为null
 * 若需持久化存储二进制向量，需指定key的值，否则每个新创建的BloomFilter
 * 都是全新的过滤器，与以前的结果无关
 * 数组写法为：
 * $storage = [
 *  'class' => 'Verdient\BloomFilter\RedisStorage',
 *  'host' => '127.0.0.1',
 *  'password' => null,
 *  'db' => 0,
 *  'key' => null
 * ];
 */
$storage = 'Verdient\BloomFilter\BitMapStorage';

$filter = new BloomFilter([
	'autoReset' => $autoReset,
	'misjudgmentRate' => $misjudgmentRate,
	'setSize' => $setSize,
	'hashCount ' => $hashCount,
	'maxEntries' => $maxEntries,
	'storage' => $storage
]);
```
### 添加元素
```php
$filter->add($value);
```

### 判断元素是否在集合内
```php
$result = $filter->has($value);
```
如果result为`true`，则元素`可能`在集合内，否则元素`一定`不在集合内

### 判断元素总数
```php
$result = $filter->getCount();
```
如果autoReset设置为`true`，总数也会随着重置而重置为0

### 手动重置过滤器
```php
$result = $filter->reset();
```