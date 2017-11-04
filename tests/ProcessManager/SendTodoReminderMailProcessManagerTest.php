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

namespace ProophTest\ProophessorDo\ProcessManager;

use PHPUnit\Framework\TestCase;
use Prooph\ProophessorDo\Model\Todo\Command\SendTodoReminderMail;
use Prooph\ProophessorDo\Model\Todo\Event\TodoAssigneeWasReminded;
use Prooph\ProophessorDo\Model\Todo\TodoId;
use Prooph\ProophessorDo\Model\Todo\TodoReminder;
use Prooph\ProophessorDo\Model\User\UserId;
use Prooph\ProophessorDo\ProcessManager\SendTodoReminderMailProcessManager;
use Prooph\ServiceBus\CommandBus;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class SendTodoReminderMailProcessManagerTest extends TestCase
{
    /**
     * @test
     */
    public function it_dispatches_email_to_the_assignee_command(): void
    {
        /** @var CommandBus|ObjectProphecy $commandBus */
        $commandBus = $this->prophesize(CommandBus::class);
        $commandBus->dispatch(Argument::type(SendTodoReminderMail::class))->shouldBeCalled();

        $event = TodoAssigneeWasReminded::forAssignee(
            TodoId::generate(),
            UserId::generate(),
            TodoReminder::from('2017-11-04T00:00:00-00:00', 'OPEN')
        );

        $processManager = new SendTodoReminderMailProcessManager($commandBus->reveal());
        $processManager($event);
    }
}
