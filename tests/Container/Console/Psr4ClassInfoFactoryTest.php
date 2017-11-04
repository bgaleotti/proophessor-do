<?php
/**
 * This file is part of prooph/proophessor-do.
 * (c) 2014-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\ProophessorDo\Container\Mail;

use PHPUnit\Framework\TestCase;
use Prooph\Cli\Console\Helper\Psr4Info;
use Prooph\ProophessorDo\Container\Console\Psr4ClassInfoFactory;
use Psr\Container\ContainerInterface;

final class Psr4ClassInfoFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_new_Psr4Info(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->prophesize(ContainerInterface::class)->reveal();

        $factory = new Psr4ClassInfoFactory();
        $psr4Info = $factory($container);

        $docblock = <<<'PROOPH'
This file is part of prooph/proophessor.
(c) 2014-2017 prooph software GmbH <contact@prooph.de>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
PROOPH;

        self::assertInstanceOf(Psr4Info::class, $psr4Info);
        self::assertSame('src', $psr4Info->getSourceFolder());
        self::assertSame('Prooph\\ProophessorDo', $psr4Info->getPackagePrefix());
        self::assertSame($docblock, $psr4Info->getFileDocBlock());
    }
}
