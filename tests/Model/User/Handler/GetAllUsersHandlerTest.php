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

namespace ProophTest\ProophessorDo\Model\User\Handler;

use Prooph\ProophessorDo\Model\User\Handler\GetAllUsersHandler;
use Prooph\ProophessorDo\Model\User\Query\GetAllUsers;
use Prooph\ProophessorDo\Projection\User\UserFinder;
use ProophTest\ProophessorDo\TestCase;
use React\Promise\Deferred;

final class GetAllUsersHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_users()
    {
        $userFinder = $this->prophesize(UserFinder::class);
        $userFinder->findAll()->willReturn([]);

        $getAllUsersHandler = new GetAllUsersHandler($userFinder->reveal());

        self::assertSame([], $getAllUsersHandler(new GetAllUsers()));
    }

    /**
     * @test
     */
    public function it_resolves_users_in_promise()
    {
        $userFinder = $this->prophesize(UserFinder::class);
        $userFinder->findAll()->willReturn([]);

        $deferred = $this->prophesize(Deferred::class);
        $deferred->resolve([])->shouldBeCalled();

        $getAllUsersHandler = new GetAllUsersHandler($userFinder->reveal());
        $getAllUsersHandler(new GetAllUsers(), $deferred->reveal());
    }
}
