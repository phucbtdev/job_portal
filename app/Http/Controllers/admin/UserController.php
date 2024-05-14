<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function list()
    {
        $users = User::orderBY('created_at','DESC')->paginate(4);
        return view('admin.user.list',[
            'users' => $users
        ]);
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit',[
            'user'=> $user
        ]);
    }
    public function updateUser(Request $request)
    {
        $id = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5|max:20',
            'email' => 'required|email|unique:users,email,'.$id.',id',
        ]);

        if ($validator->passes()) {
            $user = User::findOrFail($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->designation = $request->designation;
            $user->mobile = $request->mobile;
            $user->save();

            session()->flash('success', 'Update user successfully!');
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

    public function removeUser(Request $request)
    {
        $user = User::find($request->id);
        if ($user == null){
            session()->flash('error', 'User not found');
            return response()->json([
                'status' => false
            ]);
        }

        $user->delete();
        session()->flash('success', 'User deleted successfully.');
        return response()->json([
            'status' => true
        ]);
    }
}
