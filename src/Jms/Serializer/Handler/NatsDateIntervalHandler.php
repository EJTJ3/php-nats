<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Jms\Serializer\Handler;

use EJTJ3\PhpNats\Util\NatsDateUtil;
use Exception;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;

final class NatsDateIntervalHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods(): array
    {
        return [
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'NatsDateInterval',
                'method' => 'deserializeDateTimeToJson',
            ],
        ];
    }

    /**
     * @throws Exception
     */
    public function deserializeDateTimeToJson(JsonDeserializationVisitor $visitor, string $interval, array $type, Context $context)
    {
        return NatsDateUtil::createDateInterval($interval);
    }
}
