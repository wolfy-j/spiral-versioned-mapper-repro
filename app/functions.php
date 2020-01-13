<?php

declare(strict_types=1);

use Cake\Chronos\Chronos;
use Spiral\Database\DatabaseInterface;

function typecast_datetime($value, DatabaseInterface $db)
{
    return Chronos::parse($value, $db->getDriver()->getTimezone());
}
