<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5ea00cc67502b\Symfony\Component\Cache\Tests\Simple;

use _PhpScoper5ea00cc67502b\Cache\IntegrationTests\SimpleCacheTest;
use _PhpScoper5ea00cc67502b\Psr\SimpleCache\CacheInterface;
use _PhpScoper5ea00cc67502b\Symfony\Component\Cache\PruneableInterface;
use DateInterval;
use Exception;
use Serializable;
use function array_key_exists;
use function array_merge;
use function defined;
use function method_exists;
use function serialize;
use function sleep;

abstract class CacheTestCase extends SimpleCacheTest
{
    protected function setUp()
    {
        parent::setUp();
        if (!array_key_exists('testPrune', $this->skippedTests) && !$this->createSimpleCache() instanceof PruneableInterface) {
            $this->skippedTests['testPrune'] = 'Not a pruneable cache pool.';
        }
    }
    public static function validKeys()
    {
        if (defined('HHVM_VERSION')) {
            return parent::validKeys();
        }
        return array_merge(parent::validKeys(), [["a\0b"]]);
    }
    public function testDefaultLifeTime()
    {
        if (isset($this->skippedTests[__FUNCTION__])) {
            $this->markTestSkipped($this->skippedTests[__FUNCTION__]);
        }
        $cache = $this->createSimpleCache(2);
        $cache->clear();
        $cache->set('key.dlt', 'value');
        sleep(1);
        $this->assertSame('value', $cache->get('key.dlt'));
        sleep(2);
        $this->assertNull($cache->get('key.dlt'));
        $cache->clear();
    }
    public function testNotUnserializable()
    {
        if (isset($this->skippedTests[__FUNCTION__])) {
            $this->markTestSkipped($this->skippedTests[__FUNCTION__]);
        }
        $cache = $this->createSimpleCache();
        $cache->clear();
        $cache->set('foo', new NotUnserializable());
        $this->assertNull($cache->get('foo'));
        $cache->setMultiple(['foo' => new NotUnserializable()]);
        foreach ($cache->getMultiple(['foo']) as $value) {
        }
        $this->assertNull($value);
        $cache->clear();
    }
    public function testPrune()
    {
        if (isset($this->skippedTests[__FUNCTION__])) {
            $this->markTestSkipped($this->skippedTests[__FUNCTION__]);
        }
        if (!method_exists($this, 'isPruned')) {
            $this->fail('Test classes for pruneable caches must implement `isPruned($cache, $name)` method.');
        }
        /** @var PruneableInterface|CacheInterface $cache */
        $cache = $this->createSimpleCache();
        $cache->clear();
        $cache->set('foo', 'foo-val', new DateInterval('PT05S'));
        $cache->set('bar', 'bar-val', new DateInterval('PT10S'));
        $cache->set('baz', 'baz-val', new DateInterval('PT15S'));
        $cache->set('qux', 'qux-val', new DateInterval('PT20S'));
        sleep(30);
        $cache->prune();
        $this->assertTrue($this->isPruned($cache, 'foo'));
        $this->assertTrue($this->isPruned($cache, 'bar'));
        $this->assertTrue($this->isPruned($cache, 'baz'));
        $this->assertTrue($this->isPruned($cache, 'qux'));
        $cache->set('foo', 'foo-val');
        $cache->set('bar', 'bar-val', new DateInterval('PT20S'));
        $cache->set('baz', 'baz-val', new DateInterval('PT40S'));
        $cache->set('qux', 'qux-val', new DateInterval('PT80S'));
        $cache->prune();
        $this->assertFalse($this->isPruned($cache, 'foo'));
        $this->assertFalse($this->isPruned($cache, 'bar'));
        $this->assertFalse($this->isPruned($cache, 'baz'));
        $this->assertFalse($this->isPruned($cache, 'qux'));
        sleep(30);
        $cache->prune();
        $this->assertFalse($this->isPruned($cache, 'foo'));
        $this->assertTrue($this->isPruned($cache, 'bar'));
        $this->assertFalse($this->isPruned($cache, 'baz'));
        $this->assertFalse($this->isPruned($cache, 'qux'));
        sleep(30);
        $cache->prune();
        $this->assertFalse($this->isPruned($cache, 'foo'));
        $this->assertTrue($this->isPruned($cache, 'baz'));
        $this->assertFalse($this->isPruned($cache, 'qux'));
        sleep(30);
        $cache->prune();
        $this->assertFalse($this->isPruned($cache, 'foo'));
        $this->assertTrue($this->isPruned($cache, 'qux'));
        $cache->clear();
    }
}
class NotUnserializable implements Serializable
{
    public function serialize()
    {
        return serialize(123);
    }
    public function unserialize($ser)
    {
        throw new Exception(__CLASS__);
    }
}