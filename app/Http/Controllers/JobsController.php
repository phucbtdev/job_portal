<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;
use App\Models\JobType;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('status', 1)->get();
        $jobTypes = JobType::where('status', 1)->get();

        $jobs = Job::where('status', 1);

        //Keyword
        if (!empty($request->keyword)){
            $jobs = $jobs->where(function ($query) use($request){
               $query->orWhere('title','like','%'.$request->keyword.'%');
               $query->orWhere('keywords','like','%'.$request->keyword.'%');
            });
        }

        //Keyword
        if (!empty($request->location)){
            $jobs = $jobs->where('location','like','%'.$request->location.'%');
        }

        if (!empty($request->category)){
            $jobs = $jobs->where('category_id','like','%'.$request->category.'%');
        }

        if (!empty($request->experience)){
            $jobs = $jobs->where('experience','like','%'.$request->experience.'%');
        }

        $jobTypeArray = [];
        if (!empty($request->jobType)){
            $jobTypeArray = explode(',', $request->jobType );
            $jobs = $jobs->whereIn('job_type_id',$jobTypeArray);
        }

        if ($request->sort == '1'){
            $jobs = $jobs->orderBy('created_at', 'DESC');
        }else{
            $jobs = $jobs->orderBy('created_at', 'ASC');
        }
        $jobs = $jobs->paginate(9);

        return view('clients.jobs',[
            'categories'=> $categories,
            'jobTypes'=>$jobTypes,
            'jobs'=>$jobs,
            'jobTypeArray'=>$jobTypeArray
        ]);
    }

    public function jobDetail($id){
        $jobDetail = Job::where([
            'id'=>$id,
            'status'=>'1'
        ])->first();

        if (is_null($jobDetail)){
            abort(404);
        }
        return view('clients.jobDetail',[
            'jobDetail' =>$jobDetail
        ]);
    }
}
