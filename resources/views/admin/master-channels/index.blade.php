@extends('layouts.admin')

@section('title', 'Master Payment Channels')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Master Payment Channels</h6>
                    <a href="{{ route('admin.master-channels.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                        Add New Channel
                    </a>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success text-white mx-4 mt-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Channel</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Code</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Type</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($channels as $channel)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                @if($channel->logo_url)
                                                    <img src="{{ $channel->logo_url }}" class="avatar avatar-sm me-3 bg-white p-1" alt="{{ $channel->name }}">
                                                @else
                                                    <div class="avatar avatar-sm me-3 bg-secondary">
                                                        <span class="text-white text-xs">{{ substr($channel->name, 0, 2) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $channel->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $channel->code }}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-info">{{ strtoupper($channel->type) }}</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @if($channel->is_active)
                                            <span class="badge badge-sm bg-gradient-success">Active</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-end pe-4">
                                        <a href="{{ route('admin.master-channels.edit', $channel->id) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit channel">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.master-channels.destroy', $channel->id) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Are you sure you want to delete this channel?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger text-xs mb-0 p-0">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-xs font-weight-bold mb-0">No master channels found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
