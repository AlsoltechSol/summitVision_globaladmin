@extends('layouts.admin')

@section('page-title')
        {{ __('Manage Users') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item">{{ __('Users') }}</li>
@endsection

@section('action-button')
    @if (Gate::check('Manage Employee Last Login'))
        {{-- @can('Manage Employee Last Login')
            <a href="{{ route('lastlogin') }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ __('User Logs History') }}"><i class="ti ti-user-check"></i>
            </a>
        @endcan --}}
    @endif

    @can('Create User')
        <a href="#" data-url="{{ route('user.create') }}" data-ajax-popup="true"
            data-title="{{ __('Create New User') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
            data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endcan


@endsection


@php
    $logo = \App\Models\Utility::get_file('uploads/avatar/');
    
    $profile = \App\Models\Utility::get_file('uploads/avatar/');
@endphp
@section('content')
    <div class="row">

        <div class="row">
            @if (\Auth::user()->type == 'super admin')
                @foreach ($users as $user)
                    <div class="col-xl-3">
                        <div class="card  text-center">
                            <div class="card-header border-0 pb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <div class="badge p-2 px-3 rounded bg-primary">{{ ucfirst($user->type) }}</div>
                                    </h6>
                                </div>

                                @if (Gate::check('Edit User') || Gate::check('Delete User'))
                                    <div class="card-header-right">
                                        <div class="btn-group card-option">
                                            @if ($user->is_active == 1)
                                                <button type="button" class="btn dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="feather icon-more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    @can('Edit User')
                                                        <a href="#" class="dropdown-item"
                                                            data-url="{{ route('user.edit', $user->id) }}" data-size="md"
                                                            data-ajax-popup="true" data-title="{{ __('Update User') }}"><i
                                                                class="ti ti-edit "></i><span
                                                                class="ms-2">{{ __('Edit') }}</span></a>
                                                    @endcan



                                                    <a href="#" class="dropdown-item" data-ajax-popup="true"
                                                        data-size="md" data-title="{{ __('Change Password') }}"
                                                        data-url="{{ route('user.reset', \Crypt::encrypt($user->id)) }}"><i
                                                            class="ti ti-key"></i>
                                                        <span class="ms-1">{{ __('Reset Password') }}</span></a>

                                                    @can('Delete User')
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['user.destroy', $user->id],
                                                            'id' => 'delete-form-' . $user->id,
                                                        ]) !!}
                                                        <a href="#" class="bs-pass-para dropdown-item"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $user->id }}"
                                                            title="{{ __('Delete') }}" data-bs-toggle="tooltip"
                                                            data-bs-placement="top"><i class="ti ti-trash"></i><span
                                                                class="ms-2">{{ __('Delete') }}</span></a>
                                                        {!! Form::close() !!}
                                                    @endcan
                                                </div>
                                            @else
                                                <i class="ti ti-lock"></i>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                            </div>
                            <div class="card-body">
                                <div class="avatar">
                                    <a href="{{ !empty($user->avatar) ? $profile . $user->avatar : $logo . 'avatar.png' }}"
                                        target="_blank">
                                        <img src="{{ !empty($user->avatar) ? $profile . $user->avatar : $logo . 'avatar.png' }}"
                                            class="rounded-circle" style="width: 30%">
                                    </a>

                                </div>
                                <h4 class="mt-2 text-primary">{{ $user->name }}</h4>
                                <small class="">{{ $user->email }}</small>

                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="col-xl-3 col-lg-4 col-sm-6">
                    <a href="#" class="btn-addnew-project " data-ajax-popup="true"
                        data-url="{{ route('user.create') }}" data-title="{{ __('Create New User') }}"
                        data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
                        data-bs-original-title="{{ __('Create') }}">
                        <div class="bg-primary proj-add-icon">
                            <i class="ti ti-plus"></i>
                        </div>
                        <h6 class="mt-4 mb-2">{{ __('New User') }}</h6>
                        <p class="text-muted text-center">{{ __('Click here to add new user') }}</p>
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
