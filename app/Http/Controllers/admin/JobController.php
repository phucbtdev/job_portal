<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public  function listJob()
    {
        $jobs = Job::orderBy('created_at','DESC')->paginate(10);
        return view('admin.job.list',[
            'jobs'=>$jobs
        ]);
    }

    public function editJob()
    {

    }
}
