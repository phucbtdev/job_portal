<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

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

}