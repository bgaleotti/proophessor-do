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
use Prooph\ProophessorDo\App\Action\UserList;
use Prooph\ProophessorDo\Container\App\Action\UserListFactory;
use Prooph\ServiceBus\QueryBus;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

final class UserListFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_new_user_list_action(): void
    {
        /** @var TemplateRendererInterface $templateRenderer */
        $templateRenderer = $this->prophesize(TemplateRendererInterface::class)->reveal();
        /** @var QueryBus $queryBus */
        $queryBus = $this->prophesize(QueryBus::class)->reveal();
        /** @var ContainerInterface $container */
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(TemplateRendererInterface::class)->willReturn($templateRenderer);
        $container->get(QueryBus::class)->willReturn($queryBus);

        $factory = new UserListFactory();
        $userListAction = $factory($container->reveal());

        self::assertInstanceOf(UserList::class, $userListAction);
    }
}