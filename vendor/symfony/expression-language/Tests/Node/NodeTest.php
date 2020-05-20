<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5ea00cc67502b\Symfony\Component\ExpressionLanguage\Tests\Node;

use _PhpScoper5ea00cc67502b\PHPUnit\Framework\TestCase;
use _PhpScoper5ea00cc67502b\Symfony\Component\ExpressionLanguage\Node\ConstantNode;
use _PhpScoper5ea00cc67502b\Symfony\Component\ExpressionLanguage\Node\Node;
use function serialize;
use function unserialize;

class NodeTest extends TestCase
{
    public function testToString()
    {
        $node = new Node([new ConstantNode('foo')]);
        $this->assertEquals(<<<'EOF'
Node(
    ConstantNode(value: 'foo')
)
EOF
, (string) $node);
    }
    public function testSerialization()
    {
        $node = new Node(['foo' => 'bar'], ['bar' => 'foo']);
        $serializedNode = serialize($node);
        $unserializedNode = unserialize($serializedNode);
        $this->assertEquals($node, $unserializedNode);
    }
}