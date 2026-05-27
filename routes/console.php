<?php

use App\Console\Commands\RetryStuckNotificationsCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(RetryStuckNotificationsCommand::class)->everyFiveMinutes();
