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

use Prooph\ProophessorDo\Model\User\Command\RegisterUser;
use Prooph\ProophessorDo\Model\User\EmailAddress;
use Prooph\ProophessorDo\Model\User\Event\UserWasRegisteredAgain;
use Prooph\ProophessorDo\Model\User\Exception\UserAlreadyExists;
use Prooph\ProophessorDo\Model\User\Exception\UserNotFound;
use Prooph\ProophessorDo\Model\User\Handler\RegisterUserHandler;
use Prooph\ProophessorDo\Model\User\Service\ChecksUniqueUsersEmailAddress;
use Prooph\ProophessorDo\Model\User\User;
use Prooph\ProophessorDo\Model\User\UserCollection;
use Prooph\ProophessorDo\Model\User\UserId;
use Prooph\ProophessorDo\Model\User\UserName;
use ProophTest\ProophessorDo\TestCase;
use Prophecy\Argument;

final class RegisterUserHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function it_registers_a_user()
    {
        $command = RegisterUser::withData('7fedbd2f-fea2-4bae-9796-6c5f52bcbd43', 'John Doe', 'john.doe@example.com');

        $userCollection = $this->prophesize(UserCollection::class);
        $userCollection->get($command->userId())->willReturn(null);
        $userCollection->save(Argument::type(User::class))->shouldBeCalled();

        $checksUniqueUsersEmailAddress = new class() implements ChecksUniqueUsersEmailAddress
        {
            public function __invoke(EmailAddress $emailAddress): ?UserId
            {
                return null;
            }
        };

        $registerUserHandler = new RegisterUserHandler($userCollection->reveal(), $checksUniqueUsersEmailAddress);
        $registerUserHandler($command);
    }

    /**
     * @test
     */
    public function it_does_not_register_if_email_already_exists_and_user_is_not_found()
    {
        $command = RegisterUser::withData('7fedbd2f-fea2-4bae-9796-6c5f52bcbd43', 'John Doe', 'john.doe@example.com');

        $userCollection = $this->prophesize(UserCollection::class);
        $userCollection->get($command->userId())->willReturn(null);
        $userCollection->save(Argument::type(User::class))->shouldNotBeCalled();

        $checksUniqueUsersEmailAddress = new class($command->userId()) implements ChecksUniqueUsersEmailAddress
        {
            private $userId;

            public function __construct(UserId $userId)
            {
                $this->userId = $userId;
            }

            public function __invoke(EmailAddress $emailAddress): ?UserId
            {
                return $this->userId;
            }
        };

        $this->expectException(UserNotFound::class);
        $this->expectExceptionMessage('User with id 7fedbd2f-fea2-4bae-9796-6c5f52bcbd43 cannot be found.');

        $registerUserHandler = new RegisterUserHandler($userCollection->reveal(), $checksUniqueUsersEmailAddress);
        $registerUserHandler($command);
    }

    /**
     * @test
     */
    public function it_does_not_register_if_email_is_registered_and_user_already_exists()
    {
        $command = RegisterUser::withData('7fedbd2f-fea2-4bae-9796-6c5f52bcbd43', 'John Doe', 'john.doe@example.com');
        $user = User::registerWithData($command->userId(), $command->name(), $command->emailAddress());

        $userCollection = $this->prophesize(UserCollection::class);
        $userCollection->get($command->userId())->willReturn($user);
        $userCollection->save(Argument::type(User::class))->shouldNotBeCalled();

        $checksUniqueUsersEmailAddress = new class() implements ChecksUniqueUsersEmailAddress
        {
            public function __invoke(EmailAddress $emailAddress): ?UserId
            {
                return null;
            }
        };

        $this->expectException(UserAlreadyExists::class);
        $this->expectExceptionMessage('User with id 7fedbd2f-fea2-4bae-9796-6c5f52bcbd43 already exists.');

        $registerUserHandler = new RegisterUserHandler($userCollection->reveal(), $checksUniqueUsersEmailAddress);
        $registerUserHandler($command);
    }

    /**
     * @test
     */
    public function it_registers_a_user_again()
    {
        $command = RegisterUser::withData('7fedbd2f-fea2-4bae-9796-6c5f52bcbd43', 'John Doe', 'john.doe@example.com');
        $user = User::registerWithData($command->userId(), UserName::fromString('John'), $command->emailAddress());

        $userCollection = $this->prophesize(UserCollection::class);
        $userCollection->get($command->userId())->willReturn($user);
        $userCollection->save(Argument::type(User::class))->shouldBeCalled();

        $checksUniqueUsersEmailAddress = new class($command->userId()) implements ChecksUniqueUsersEmailAddress
        {
            private $userId;

            public function __construct(UserId $userId)
            {
                $this->userId = $userId;
            }

            public function __invoke(EmailAddress $emailAddress): ?UserId
            {
                return $this->userId;
            }
        };

        $registerUserHandler = new RegisterUserHandler($userCollection->reveal(), $checksUniqueUsersEmailAddress);
        $registerUserHandler($command);

        $events = $this->popRecordedEvent($user);
        self::assertInstanceOf(UserWasRegisteredAgain::class, end($events));
    }
}
