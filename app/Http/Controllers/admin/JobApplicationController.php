<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function listJobApplications()
    {
        $jobsApplications = JobApplication::orderBy('applied_date', 'DESC')->with('job','job.jobType')->paginate(10);
        return view('admin.job-applications.list',[
            'jobsApplications'=>$jobsApplications
        ]);
    }
}
