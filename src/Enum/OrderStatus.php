<?php

namespace App\Enum;

final class OrderStatus
{
    public const NEW = 'new';
    public const PAID = 'paid';
    public const SHIPPED = 'shipped';
    public const CANCELED = 'canceled';
    
    public static function choices(): array
    {
        return [
            'New' => self::NEW,
            'Paid' => self::PAID,
            'Shipped' => self::SHIPPED,
            'Canceled' => self::CANCELED,
        ];
    }
}