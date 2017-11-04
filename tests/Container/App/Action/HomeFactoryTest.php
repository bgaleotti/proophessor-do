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
use Prooph\ProophessorDo\App\Action\Home;
use Prooph\ProophessorDo\Container\App\Action\HomeFactory;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

final class HomeFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_new_home_action(): void
    {
        /** @var TemplateRendererInterface $templateRenderer */
        $templateRenderer = $this->prophesize(TemplateRendererInterface::class)->reveal();
        /** @var ContainerInterface $container */
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(TemplateRendererInterface::class)->willReturn($templateRenderer);

        $factory = new HomeFactory();
        $homeAction = $factory($container->reveal());

        self::assertInstanceOf(Home::class, $homeAction);
    }
}
