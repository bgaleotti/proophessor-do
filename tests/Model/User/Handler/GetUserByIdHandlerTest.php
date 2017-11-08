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

use Prooph\ProophessorDo\Model\User\Handler\GetUserByIdHandler;
use Prooph\ProophessorDo\Model\User\Query\GetUserById;
use Prooph\ProophessorDo\Model\User\UserId;
use Prooph\ProophessorDo\Projection\User\UserFinder;
use ProophTest\ProophessorDo\TestCase;
use React\Promise\Deferred;

final class GetUserByIdHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_users()
    {
        $userId = UserId::generate()->toString();
        $user = new \stdClass();

        $userFinder = $this->prophesize(UserFinder::class);
        $userFinder->findById($userId)->willReturn($user);

        $getUserByIdHandler = new GetUserByIdHandler($userFinder->reveal());

        self::assertSame($user, $getUserByIdHandler(new GetUserById($userId)));
    }

    /**
     * @test
     */
    public function it_resolves_users_in_promise()
    {
        $userId = UserId::generate()->toString();
        $user = new \stdClass();

        $userFinder = $this->prophesize(UserFinder::class);
        $userFinder->findById($userId)->willReturn($user);

        $deferred = $this->prophesize(Deferred::class);
        $deferred->resolve($user)->shouldBeCalled();

        $getUserByIdHandler = new GetUserByIdHandler($userFinder->reveal());
        $getUserByIdHandler(new GetUserById($userId), $deferred->reveal());
    }
}
