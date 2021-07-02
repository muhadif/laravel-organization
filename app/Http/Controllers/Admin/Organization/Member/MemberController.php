<?php

namespace App\Http\Controllers\Admin\Organization\Member;

use App\Http\Controllers\Controller;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MemberController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:org-member-list|org-member-create|org-member-edit|org-member-delete', ['only' => ['index','show']]);
        $this->middleware('permission:org-member-create', ['only' => ['create','store']]);
        $this->middleware('permission:org-member-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:org-member-delete', ['only' => ['destroy']]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $organization = $request->organization;
        $roles = Role::where('id', '!=', 1)->pluck('name','name');
        return view('organizations.members.create', compact('organization', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users',
            'password' => 'required|same:confirm-password',
            'roles' => 'required',
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $organization = $request->organization;
        $input = $request->all();
        if ($request->hasFile('picture')) {

            $request->validate([
                'picture' => 'mimes:jpeg,bmp,png'
            ]);

            $request->picture->store('organization', 'public');

            $picture = new Picture([
                "name" => $request->picture->getClientOriginalName(),
                "url" => $request->picture->hashName()
            ]);
            $picture->save();
            $input["picture_id"] = $picture->id;
        }

        $input['password'] = Hash::make($input['password']);
        $input['organization_id'] = $organization;
        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        return redirect()->route('organizations.show', compact('organization'))
            ->with('success','User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $organization = $request->organization;
        $member = $request->member;
        $user = User::with('picture')->find($member);
        return view('organizations.members.show',compact('user', 'organization'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $organization = $request->organization;
        $member = $request->member;
        $user = User::find($member);
        $userRole = $user->roles->pluck('name','name')->all();
        $roles = Role::where('id', '!=', 1)->pluck('name','name');
        return view('organizations.members.edit',compact('user', 'userRole', 'roles', 'organization'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $organization = $request->organization;
        $member = $request->member;

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$member,
            'password' => 'same:confirm-password',
        ]);

        $input = $request->all();
        if ($request->hasFile('picture')) {

            $request->validate([
                'picture' => 'mimes:jpeg,bmp,png'
            ]);

            $request->picture->store('organization', 'public');

            $picture = new Picture([
                "name" => $request->picture->getClientOriginalName(),
                "url" => $request->picture->hashName()
            ]);
            $picture->save();
            $input["picture_id"] = $picture->id;
        }

        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));
        }

        $user = User::find($member);
        $user->update($input);

        DB::table('model_has_roles')->where('model_id',$member)->delete();
        $user->assignRole($request->input('roles'));

        return redirect()->route('organizations.show', $organization)
            ->with('success','User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $organization = $request->organization;
        $member = $request->member;

        User::find($member)->delete();
        return redirect()->route('organizations.show', $organization)
            ->with('success','User deleted successfully');
    }

    public function addToManagerRole(Request $request){
        $organization = $request->organization;
        $member = $request->member;

        $user = User::find($member);
        $user->assignRole(3);
        return redirect()->route('organizations.show', $organization)
            ->with('success','User updated successfully');
    }

    public function deleteManagerRole(Request $request){
        $organization = $request->organization;
        $member = $request->member;

        DB::table('model_has_roles')->where('model_id',$member)->delete();
        $user = User::find($member);
        $user->assignRole(2);
        return redirect()->route('organizations.show', $organization)
            ->with('success','User updated successfully');
    }
}
