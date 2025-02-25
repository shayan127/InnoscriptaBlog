<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command('news:fetch')->everyFiveMinutes()->withoutOverlapping();
