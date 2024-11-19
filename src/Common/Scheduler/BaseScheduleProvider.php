<?php

declare(strict_types=1);

namespace App\Common\Scheduler;

use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('base')]
class BaseScheduleProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return new Schedule();
        //        return (new Schedule())->add(
        //            RecurringMessage::every('10 seconds', new YouMessage()),
        //        );
    }
}
