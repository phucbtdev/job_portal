<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Models\Category;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobType;
use App\Models\SavedJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AccountController extends Controller
{
    public function registration(){
        return view('clients.accounts.registration');
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5|same:confirm_password',
            'confirm_password' => 'required',
        ]);

        if($validator->passes()){
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            session()->flash('success', 'You have registered successfully.');
            return response()->json([
                'status' => true,
                'errors' => []
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function login(){
        return view('clients.accounts.login');
    }

    public function authenticate(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->passes()){
            if (Auth::attempt(['email'=> $request->email, 'password' => $request->password])) {
                return redirect()->route('account.profile');
            }else {
                return redirect()->route('account.login')->withInput($request->only('email'))->with('error', 'Email or password is incorrect!');
            }
        }else{
            return redirect()->route('account.login')
            ->withErrors($validator)
            ->withInput($request->only('email'));
        }
    }

    public function profile(){
        $id = Auth::user()->id;

        // $user = User::find($id);
        $user = User::where('id', $id)->first();

        return view('clients.accounts.profile',[
            'user'=> $user
        ]);
    }

    public function updateProfile(Request $request){
        $id = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5|max:20',
            'email' => 'required|email|unique:users,email,'.$id.',id',
        ]);

        if ($validator->passes()) {
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->designation = $request->designation;
            $user->mobile = $request->mobile;
            $user->save();

            session()->flash('success', 'Update profile successfully!');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);
        }else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function updateAvatar(Request $request){
        $validator = Validator::make($request->all(), [
            'image' => 'required|image',
        ]);
        $id = Auth::user()->id;
        if ($validator->passes()) {
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = $id . time() . '-' . $ext;
            $image->move(public_path("/profile_pic/"), $imageName);

            //Create small thumbnail
            $sourcePath = public_path('/profile_pic/'.$imageName);
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($sourcePath);

            $image->cover(150, 150);
            $image->toPng()->save(public_path("/profile_pic/thumb/".$imageName));

            File::delete(public_path("/profile_pic/thumb/" . Auth::user()->image));
            File::delete(public_path("/profile_pic/" . Auth::user()->image));

            User::where('id', $id)->update(['image' => $imageName]);

            session()->flash('success', 'Profile picture update successfully!');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);
        }else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('account.login');
    }

    public function createJob(){
        $categories =  Category::orderBy('name', 'ASC')->where('status',1)->get();
        $jobTypes =  JobType::orderBy('name', 'ASC')->where('status',1)->get();
        return view('clients.accounts.job.create',[
            'categories' =>$categories,
            'jobTypes' =>$jobTypes,
        ]);
    }
    public function saveJob(Request $request){
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'category' => 'required',
            'jobType' => 'required',
            'vacancy' => 'required|integer',
            'location' => 'required|max:50',
            'description' => 'required',
            'company_name' => 'required|min:3|max:75',
        ]);

        if ($validator->passes()){
            $job = new Job();
            $job->title = $request->title;
            $job->category_id = $request->category;
            $job->job_type_id = $request->jobType;
            $job->user_id = Auth::user()->id;
            $job->vacancy = $request->vacancy;
            $job->salary = $request->salary;
            $job->location = $request->location;
            $job->description = $request->description;
            $job->benefits = $request->benefits;
            $job->responsibility = $request->responsibility;
            $job->qualifications = $request->qualifications;
            $job->keywords = $request->keywords;
            $job->experience = $request->experience;
            $job->company_name = $request->company_name;
            $job->company_location = $request->company_location;
            $job->company_website = $request->company_website;
            $job->save();

            session()->flash('success', 'Create job successfully!');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function listJobs()
    {
        $jobs = Job::where('user_id', Auth::user()->id)->with('jobType')->orderby('created_at' ,'DESC')->paginate(3);
        return view('clients.accounts.job.my-jobs',[
            'jobs' => $jobs
        ]);
    }

    public function editJob(Request $request, $id){
        $categories =  Category::orderBy('name', 'ASC')->where('status',1)->get();
        $jobTypes =  JobType::orderBy('name', 'ASC')->where('status',1)->get();

        $job = Job::where([
            'user_id' => Auth::user()->id,
            'id' => $id,
        ])->first();

        if ($job == null){
            abort(404);
        }

        return view('clients.accounts.job.edit',[
            'categories'=> $categories,
            'jobTypes' => $jobTypes,
            'job'=>  $job,
        ]);
    }

    public function updateJob(Request $request, $id){
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'category' => 'required',
            'jobType' => 'required',
            'vacancy' => 'required|integer',
            'location' => 'required|max:50',
            'description' => 'required',
            'company_name' => 'required|min:3|max:75',
        ]);

        if ($validator->passes()){
            $job = Job::find($id);
            $job->title = $request->title;
            $job->category_id = $request->category;
            $job->job_type_id = $request->jobType;
            $job->user_id = Auth::user()->id;
            $job->vacancy = $request->vacancy;
            $job->salary = $request->salary;
            $job->location = $request->location;
            $job->description = $request->description;
            $job->benefits = $request->benefits;
            $job->responsibility = $request->responsibility;
            $job->qualifications = $request->qualifications;
            $job->keywords = $request->keywords;
            $job->experience = $request->experience;
            $job->company_name = $request->company_name;
            $job->company_location = $request->company_location;
            $job->company_website = $request->company_website;
            $job->save();

            session()->flash('success', 'Updated job successfully!');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function deleteJob(Request $request)
    {
        $job = Job::where([
            'user_id' => Auth::user()->id,
            'id' => $request->jobId
        ])->first();

        if ($job == null){
            session()->flash('error', 'Either job deleted or not found.');
            return response()->json([
                'status' => true
            ]);
        }

        Job::where('id', $request->jobId)->delete();
        session()->flash('success', 'Job deleted successfully.');
        return response()->json([
            'status' => true
        ]);
    }

    public function jobsApplied()
    {
        $jobApplications = JobApplication::where('user_id', Auth::user()->id)->with(['job','job.jobType','job.jobApplication'])->paginate(10);

        return view('clients.accounts.job.jobs-applied',[
            'jobApplications'=>$jobApplications
        ]);
    }

    public function removeJob(Request $request)
    {
        $jobApplication = JobApplication::where([
            'id'=> $request->id,
            'user_id'=>Auth::user()->id
        ])->first();

        if ($jobApplication == null){
            session()->flash('error', 'Job Application not found.');
            return response()->json([
                'status' =>false
            ]);
        }

        JobApplication::find($request->id)->delete();
        session()->flash('success', 'Job Application removed successfully.');
        return response()->json([
            'status' =>true
        ]);
    }

    public function savedJobs()
    {
        $savedJobs = SavedJob::where('user_id', Auth::user()->id)->with('job','job.jobApplication')->paginate();

        return view('clients.accounts.job.saved-jobs',[
            'savedJobs'=>$savedJobs
        ]);
    }

    public function removeSavedJob(Request $request)
    {
        $savedJob = SavedJob::where([
            'id'=> $request->id,
            'user_id'=>Auth::user()->id
        ])->first();

        if ($savedJob === null){
            session()->flash('error', 'Job not found.');
            return response()->json([
                'status' =>false
            ]);
        }

        SavedJob::find($request->id)->delete();
        session()->flash('success', 'Job removed successfully.');
        return response()->json([
            'status' =>true
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'old_password'=>'required',
            'new_password'=>'required|min:7',
            'password_confirm'=>'required|same:new_password'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status'=> false,
                'errors' => $validator->errors()
            ]);
        }

        if (Hash::check($request->old_password, Auth::user()->password)=== false){
            session()->flash('error','Old password is incorrect.');
            return response()->json([
                'status'=> true
            ]);
        }

        $user = User::find(Auth::user()->id);
        $user->password = Hash::make($request->new_password);
        $user->save();

        session()->flash('success','Change password successfully.');
        return response()->json([
            'status'=> true
        ]);
    }

    public function forgotPassword()
    {
        return view('clients.accounts.forgot-password');
    }

    public function processForgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()){
            return redirect()->route('account.forgotPassword')->withInput()->withErrors($validator);
        }
        \DB::table('password_reset_tokens')->where('email',$request->email)->delete();
        $token = Str::random(50);
        \DB::table('password_reset_tokens')->insert([
            'email' =>$request->email,
            'token' =>$token,
            'created_at' =>now()
        ] );

        $user = User::where('email', $request->email)->first();
        $dataMail = [
            'user' => $user,
            'token' => $token
        ];
        Mail::to($request->email)->send( new ResetPassword($dataMail));

        return redirect()->route('account.forgotPassword')->with('success','Please check your mail.');
    }

    public function resetPassword($token)
    {
        $checkToken = \DB::table('password_reset_tokens')->where('token',$token)->first();
        if ($checkToken == null){
            return redirect()->route('account.forgotPassword')->with('error','Invalid token.');
        }

        return view('clients.accounts.reset-password',[
            'token' => $token
        ]);
    }

    public function processResetPass(Request $request)
    {
        $checkToken = \DB::table('password_reset_tokens')->where('token',$request->token)->first();
        if ($checkToken == null){
            return redirect()->route('account.forgotPassword')->with('error','Invalid token.');
        }

        $validator = Validator::make($request->all(),[
            'password' => 'required|min:8',
            'password_confirm' => 'required|same:password',
        ]);

        if ($validator->fails()){
            return redirect()->route('account.resetPassword',$request->token)->withInput()->withErrors($validator);
        }

        User::where('email', $checkToken->email)->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('account.login')->with('success','Reset password successfully.');
    }
}
