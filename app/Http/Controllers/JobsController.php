<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;
use App\Models\JobType;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 1)->get();
        $jobTypes = JobType::where('status', 1)->get();

        $jobs = Job::where('status', 1)->orderBy('created_at', 'DESC')->paginate(9);

        return view('clients.jobs',[
            'categories'=> $categories,
            'jobTypes'=>$jobTypes,
            'jobs'=>$jobs
        ]);
    }
}
