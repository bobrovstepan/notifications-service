<?php

declare(strict_types=1);

use App\Console\Commands\RetryStuckNotificationsCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(RetryStuckNotificationsCommand::class)->everyFiveMinutes();
