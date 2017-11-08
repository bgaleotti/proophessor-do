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

namespace ProophTest\ProophessorDo\Model\User;

use Prooph\ProophessorDo\Model\User\EmailAddress;
use Prooph\ProophessorDo\Model\ValueObject;
use ProophTest\ProophessorDo\TestCase;

final class EmailAddressTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_email_from_string()
    {
        $emailAddress = EmailAddress::fromString('john.doe@example.com');

        self::assertSame('john.doe@example.com', $emailAddress->toString());
    }

    /**
     * @test
     */
    public function it_does_not_create_email_from_not_valid_address()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address');

        EmailAddress::fromString('john.doe-example.com');
    }

    /**
     * @test
     * @depends it_creates_email_from_string
     */
    public function it_can_be_compared()
    {
        $first = EmailAddress::fromString('john.doe@example.com');
        $second = EmailAddress::fromString('john.doe@example.com');
        $third = EmailAddress::fromString('jane.doe@example.com');
        $fourth = new class() implements ValueObject {
            public function sameValueAs(ValueObject $object): bool
            {
                return false;
            }
        };

        self::assertTrue($first->sameValueAs($second));
        self::assertFalse($first->sameValueAs($third));
        self::assertFalse($first->sameValueAs($fourth));
    }
}
