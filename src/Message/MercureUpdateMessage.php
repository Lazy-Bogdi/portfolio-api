<?php

namespace App\Message;

class MercureUpdateMessage
{
    public function __construct(
        public readonly int $entityId,
        public readonly string $entityClass,
        public readonly string $action,
    ) {
    }
}
