<?php

namespace App\Http\Controllers\Admin\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:org-list|org-create|org-edit|org-delete|org-show', ['only' => ['index','show', 'fetch-data']]);
        $this->middleware('permission:org-create', ['only' => ['create','store']]);
        $this->middleware('permission:org-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:org-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {

//        $user = User::with('picture', 'organization')
//            ->whereHas("roles", function ($q){
//                $q->where("name", "Account Manager");
//            })
//            ->whereNotNull('organization_id')
//            ->get();
//            dd($user);

        $organizations = Organization::with('picture')->latest();
        if(!Auth::user()->hasRole("Admin") and !is_null(Auth::user()->organization_id)){
            $organizations_id = Auth::user()->organization_id;
            $organizations = $organizations->where('id', $organizations_id);
        }

        if(isset($request->search)){
            $organizations
                ->where('organizations.name', 'LIKE', '%'.$request->search. '%');
        }

        $data = $organizations
            ->paginate(5);

        return view('organizations.index',compact('data'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('organizations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'website' => 'required',
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();
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
            $data["picture_id"] = $picture->id;
        }

        Organization::create($data);

        return redirect()->route('organizations.index')
            ->with('success','organization created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(Organization $organization)
    {
        $members = User::with('organization')
            ->where('organization_id', $organization->id)
            ->orderBy('id','DESC')
            ->paginate(5);

        if(!Auth::user()->hasRole('Admin')){
            if($organization->id != Auth::user()->organization_id){
                abort(403, 'User Not Have Permission');
            }
        }

        return view('organizations.show',
            compact('organization', 'members'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(Organization $organization)
    {
        return view('organizations.edit',compact('organization'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Organization $organization)
    {
        request()->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'website' => 'required',
            'picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();
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
            $data["picture_id"] = $picture->id;
        }


        $organization->update($data);

        return redirect()->route('organizations.index')
            ->with('success','Organization updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\  $organization
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Organization $organization)
    {
        $organization->delete();

        return redirect()->route('organizations.index')
            ->with('success','Organization deleted successfully');
    }

    public function fetch_data(Request $request){
        if($request->ajax())
        {
            $query = $request->get('query');
            if(isset($query)){
                $query = str_replace(" ", "%", $query);
                $organizations = DB::table('organizations')
                    ->Where('name', 'like', '%'.$query.'%')
                    ->paginate(5);
            } else {
                $organizations = Organization::with('picture')->latest();
                if(!Auth::user()->hasRole("Admin") and !is_null(Auth::user()->organization_id)){
                    $organizations_id = Auth::user()->organization_id;
                    $organizations = $organizations->where('id', $organizations_id);
                }
                $organizations = $organizations
                    ->paginate(5);
            }


            return view('organizations.components.search_data', ['data'=>$organizations])->render();
        }
    }
}
