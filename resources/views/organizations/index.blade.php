@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Organization</h2>
            </div>
            <div class="pull-right">
                @can('org-create')
                    <a class="btn btn-success" href="{{ route('organizations.create') }}"> Create New Organization</a>
                @endcan
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Logo</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Website</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($organizations as $organization)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $organization->name }}</td>
                @if(empty($organization->picture))
                    <td width="20%"><img class="img-fluid" src="{{asset('storage/organization/default.jpg' )}}"></td>
                @else
                    <td width="20%"><img class="img-fluid" src="{{asset('storage/organization/'.$organization->picture->url )}}"></td>
                @endif
                <td>{{ $organization->email }}</td>
                <td>{{ $organization->phone }}</td>
                <td>{{ $organization->website }}</td>
                <td>
                    <form action="{{ route('organizations.destroy',$organization->id) }}" method="POST">
                        <a class="btn btn-info" href="{{ route('organizations.show',$organization->id) }}">Show</a>
                        @can('org-edit')
                            <a class="btn btn-primary" href="{{ route('organizations.edit',$organization->id) }}">Edit</a>
                        @endcan

                        @csrf
                        @method('DELETE')
                        @can('org-delete')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        @endcan
                    </form>
                </td>
            </tr>
        @endforeach
    </table>

    {!! $organizations->links() !!}

    <p class="text-center text-primary"><small>muhadif here <3</small></p>
@endsection
