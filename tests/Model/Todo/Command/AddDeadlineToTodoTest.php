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

namespace ProophTest\ProophessorDo\Model\Todo\Command;

use Prooph\Common\Messaging\Message;
use Prooph\ProophessorDo\Model\Todo\Command\AddDeadlineToTodo;
use Prooph\ProophessorDo\Model\Todo\TodoId;
use Prooph\ProophessorDo\Model\User\UserId;
use ProophTest\ProophessorDo\TestCase;

final class AddDeadlineToTodoTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_deadline_from_payload()
    {
        $userId = 'dd5c5d21-2b9a-4106-89b6-0520bb95595f';
        $todoId = 'e9d459cd-e5c0-48aa-b575-308a0204322f';
        $deadline = '2017-11-04T00:00:00+00:00';

        $command = new AddDeadlineToTodo([
            'user_id' => $userId,
            'todo_id' => $todoId,
            'deadline' => $deadline,

        ]);

        self::assertSame(Message::TYPE_COMMAND, $command->messageType());
        self::assertTrue($command->userId()->sameValueAs(UserId::fromString($userId)));
        self::assertTrue($command->todoId()->sameValueAs(TodoId::fromString($todoId)));
        self::assertSame($deadline, $command->deadline()->toString());
    }
}
