<?php

use Phone\Jobs\PhoneDataImportJob;
use Laravel\Horizon\Repositories\RedisJobRepository;

if (! function_exists('should_show_import_job_alert')) {
    function should_show_import_job_alert()
    {
        $isAdmin = auth()->user()->isAdmin();
        //count running jobs in reddis at the moment
        $hasRunningJob = env('QUEUE_CONNECTION') === 'redis' && app()->get(RedisJobRepository::class)->getRecent()->where('name', PhoneDataImportJob::class)->where('completed_at', null)->where('failed_at', null)->count() > 0;

        return $hasRunningJob && $isAdmin;
    }
}
