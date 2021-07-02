@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2> Show Organization</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('organizations.index') }}"> Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-4 col-sm-3 col-md-3">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $organization->name }}
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    @if(empty($organization->picture))
                        <img class="img-fluid" src="{{asset('storage/organization/default.jpg' )}}">
                    @else
                        <img class="img-fluid" src="{{asset('storage/organization/'.$organization->picture->url )}}">
                    @endif
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Phone:</strong>
                    {{ $organization->phone }}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Email:</strong>
                    {{ $organization->email }}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Website:</strong>
                    {{ $organization->website }}
                </div>
            </div>
            @can('org-edit')
                <a class="btn btn-primary" href="{{ route('organizations.edit',$organization->id) }}">Edit Organization</a>
            @endcan
        </div>
        <div class="col-xs-8 col-sm-8 col-md-8">
            @can('org-member-create')
            <div class="pull-left">
                <a class="btn btn-success"
                   href="{{ route('organizations.members.create', ['organization' => $organization]) }}"> Add New
                    Member</a>
            </div>
            @endcan
            <table class="table table-bordered">
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th width="280px">Action</th>
                </tr>
                @foreach ($members as $member)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->email }}</td>
                        <td>{{ $member->phone }}</td>
                        <td>
                            <form
                                action="{{ route('organizations.members.destroy', ['member' => $member, 'organization' => $organization]) }}"
                                method="POST">
                                <a class="btn btn-info"
                                   href="{{ route('organizations.members.show', ['member' => $member, 'organization' => $organization]) }}">Show</a>
                                @can('org-member-edit')
                                    <a class="btn btn-primary"
                                       href="{{ route('organizations.members.edit', ['member' => $member, 'organization' => $organization]) }}">Edit</a>
                                @endcan
                                @csrf
                                @method('DELETE')
                                @can('org-member-delete')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                @endcan
                            </form>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        @endsection
        <p class="text-center text-primary"><small>muhadif here <3</small></p>
