<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    public function getMyJob() {
        $query_tag = \Request::query('tag');

        $query = Job::query()->select('jobs.*')
                ->where('user_id', '=', \Auth::id())
                ->whereNull('deleted_at')
                ->orderBy('updated_at', 'DESC');

        if(!empty($query_tag)) {
                $query->leftJoin('job_tags', 'job_tags.job_id', '=', 'jobs.id')
                ->where('job_tags.tag_id', '=', $query_tag);
        }

        $jobs = $query->get();

        return $jobs;
    }
}
