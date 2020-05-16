# Bloom Filter
布隆过滤器

### 创建新的过滤器
```php
use Verdient\BloomFilter\BloomFilter;

/**
 * @var bool 是否自动重置
 * @author Verdient。
 */
$autoReset = false;

/**
 * @var int 集合大小，默认为1000000
 * @author Verdient。
 */
$setSize = 1000000;

/**
 * @var int 哈希次数，默认为10
 * @author Verdient。
 */
$hashCount = 10;

/**
 * @var int 最大元素数量，默认为false，即不根据数量重置过滤器
 * autoReset设置为true时有效
 * 当已加入的元素数量大于maxEntries时，会自动重置过滤器
 * @author Verdient。
 */
$maxEntries = false;

/**
 * @var int 最大比率，默认为false，即不根据比率重置过滤器
 * autoReset设置为true时有效
 * 当已加入的比率大于maxRate时，会自动重置过滤器
 * @author Verdient。
 */
$maxRate = false;

$filter = new BloomFilter([
	'autoReset' => $autoReset,
	'setSize' => $setSize,
	'hashCount ' => $hashCount,
	'maxEntries' => $maxEntries,
	'maxRate ' => $maxRate
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

### 判断当前比率
```php
$result = $filter->getRate();
```
布隆过滤器集合中1的数量占整个集合的百分比。若百分比为`100`，则说明集合内所有位均为1，此时过滤器已完全失效。数值越`低`过滤器越可靠

### 手动重置过滤器
```php
$result = $filter->reset();
```