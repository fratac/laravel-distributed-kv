protected function schedule(Schedule $schedule)
{
$schedule->command('dkv:sync')->everyMinute();
}
