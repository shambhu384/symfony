<?php

namespace Symfony\Component\Mailer\Bridge\Mailgun\Tests\Factory;

use Symfony\Component\Mailer\Bridge\Mailgun;
use Symfony\Component\Mailer\Bridge\Mailgun\Factory\MailgunTransportFactory;
use Symfony\Component\Mailer\Tests\TransportFactoryTestCase;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\TransportFactoryInterface;

class MailgunTransportFactoryTest extends TransportFactoryTestCase
{
    public function getFactory(): TransportFactoryInterface
    {
        return new MailgunTransportFactory($this->getDispatcher(), $this->getClient(), $this->getLogger());
    }

    public function supportsProvider(): iterable
    {
        yield [
            new Dsn('api', 'mailgun'),
            true,
        ];

        yield [
            new Dsn('http', 'mailgun'),
            true,
        ];

        yield [
            new Dsn('smtp', 'mailgun'),
            true,
        ];

        yield [
            new Dsn('smtp', 'example.com'),
            false,
        ];
    }

    public function createProvider(): iterable
    {
        $client = $this->getClient();
        $dispatcher = $this->getDispatcher();
        $logger = $this->getLogger();

        yield [
            new Dsn('api', 'mailgun', self::USER, self::PASSWORD),
            new Mailgun\Http\Api\MailgunTransport(self::USER, self::PASSWORD, null, $client, $dispatcher, $logger),
        ];

        yield [
            new Dsn('api', 'mailgun', self::USER, self::PASSWORD, null, ['region' => 'eu']),
            new Mailgun\Http\Api\MailgunTransport(self::USER, self::PASSWORD, 'eu', $client, $dispatcher, $logger),
        ];

        yield [
            new Dsn('http', 'mailgun', self::USER, self::PASSWORD),
            new Mailgun\Http\MailgunTransport(self::USER, self::PASSWORD, null, $client, $dispatcher, $logger),
        ];

        yield [
            new Dsn('smtp', 'mailgun', self::USER, self::PASSWORD),
            new Mailgun\Smtp\MailgunTransport(self::USER, self::PASSWORD, null, $dispatcher, $logger),
        ];
    }

    public function unsupportedSchemeProvider(): iterable
    {
        yield [new Dsn('foo', 'mailgun', self::USER, self::PASSWORD)];
    }

    public function incompleteDsnProvider(): iterable
    {
        yield [new Dsn('api', 'mailgun', self::USER)];

        yield [new Dsn('api', 'mailgun', null, self::PASSWORD)];
    }
}
