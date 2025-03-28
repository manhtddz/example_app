<?php
namespace App\Const;

class TaskStatus
{
    public const LIST = [
        1 => 'On working',
        2 => 'Done',
    ];

    public static function getName($id)
    {
        return self::LIST [$id] ?? "UNKNOWN";
    }
}