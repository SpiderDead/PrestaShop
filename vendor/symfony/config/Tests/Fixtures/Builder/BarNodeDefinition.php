<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5eddef0da618a\Symfony\Component\Config\Tests\Fixtures\Builder;

use _PhpScoper5eddef0da618a\Symfony\Component\Config\Definition\Builder\NodeDefinition;
use _PhpScoper5eddef0da618a\Symfony\Component\Config\Tests\Fixtures\BarNode;
class BarNodeDefinition extends \_PhpScoper5eddef0da618a\Symfony\Component\Config\Definition\Builder\NodeDefinition
{
    protected function createNode()
    {
        return new \_PhpScoper5eddef0da618a\Symfony\Component\Config\Tests\Fixtures\BarNode($this->name);
    }
}
