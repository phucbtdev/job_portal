<?php

namespace App\Http\Controllers;

use App\Mail\JobNotificationEmail;
use App\Models\Category;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobType;
use App\Models\SavedJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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

        $jobSaveCount = 0;
        if (Auth::user()){
            $jobSaveCount = SavedJob::where(['user_id'=>Auth::user()->id, 'job_id' => $id])->count();

        }

        $applicants = JobApplication::where('job_id',$id)->with('user')->get();
        return view('clients.jobDetail',[
            'jobDetail' =>$jobDetail,
            'jobSaveCount'=>$jobSaveCount,
            'applicants' => $applicants
        ]);
    }

    public function applyJob(Request $request){
        $id = $request->id;
        $job = Job::where('id',$id)->first();

        //Check job's exist in database
        if ( $job == null){
            session()->flash('error',"Jod does not exist.");
            return response()->json([
                'status' => false,
                'message'=> "Jod does not exist."
            ]);
        }

        //You can not apply in your own job.
        $employer_id = $job->user_id;
        if ( $employer_id  == Auth::user()->id){
            session()->flash('error',"You can not apply in your own job.");
            return response()->json([
                'status' => false,
                'message'=> "You can not apply in your own job."
            ]);
        }

        //You can not apply on a job twise
        $jobApplied = JobApplication::where([
            'job_id'=> $id,
            'user_id'=> Auth::user()->id
        ])->count();

        if ( $jobApplied > 0){
            session()->flash('error',"You already applied on this job.");
            return response()->json([
                'status' => false,
                'message'=> "You already applied on this job."
            ]);
        }

        $application = new JobApplication();
        $application->job_id = $id;
        $application->user_id = Auth::user()->id;
        $application->employer_id = $employer_id;
        $application->applied_date = now();
        $application->save();

        //Send notifications to employer
        $employer = User::where('id', $employer_id)->first();
        $mailData = [
            'employer' =>  $employer,
            'user' => Auth::user(),
            'job' => $job
        ];

        Mail::to($employer->email )->send(new JobNotificationEmail($mailData));

        session()->flash('success',"You have successfully applied.");
        return response()->json([
            'status' => true,
            'message'=> "You have successfully applied."
        ]);
    }

    public function saveJob(Request $request)
    {
        $id = $request->id;
        $job = Job::find($id);
        if ($job === null){
            session()->flash('error', 'Job not found.');
            return response()->json([
                    'status'=> false
                ]
            );
        }

        $jobSave = SavedJob::where(['user_id'=>Auth::user()->id, 'job_id' => $id])->count();
        if ($jobSave > 0){
            session()->flash('error', 'Job already saved.');
            return response()->json([
                    'status'=> false
                ]
            );
        }

        $jobSeved = new SavedJob();
        $jobSeved->user_id = Auth::user()->id;
        $jobSeved->job_id = $id;
        $jobSeved->save();
        session()->flash('success', 'Job have successfully saved.');
        return response()->json([
                'status'=> true
            ]
        );
    }
}
