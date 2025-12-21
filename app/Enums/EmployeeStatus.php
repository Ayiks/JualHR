<?php

namespace App\Enums;

enum EmployeeStatus: string
{
    case ACTIVE = 'active';
    case ON_LEAVE = 'on_leave';
    case SUSPENDED = 'suspended';
    case TERMINATED = 'terminated';
    case RESIGNED = 'resigned';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::ON_LEAVE => 'On Leave',
            self::SUSPENDED => 'Suspended',
            self::TERMINATED => 'Terminated',
            self::RESIGNED => 'Resigned',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'green',
            self::ON_LEAVE => 'blue',
            self::SUSPENDED => 'yellow',
            self::TERMINATED => 'red',
            self::RESIGNED => 'gray',
        };
    }
}