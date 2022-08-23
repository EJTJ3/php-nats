<?php

namespace EJTJ3\PhpNats\Jms\Serializer\Handler;

use DateTime;
use DateTimeImmutable;
use EJTJ3\PhpNats\Util\NatsDateUtil;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

final class NatsDateImmutableHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods(): array
    {
        return [
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'NatsDateTimeImmutable',
                'method' => 'deserializeDateTimeToJson',
            ],
        ];
    }
    public function deserializeDateTimeToJson(JsonDeserializationVisitor $visitor, string $dateString, array $type, Context $context): DateTimeImmutable
    {
        return new DateTimeImmutable(explode('.', $dateString)[0]);
    }
}
