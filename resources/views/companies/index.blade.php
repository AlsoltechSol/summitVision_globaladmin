@extends('layouts.admin')

@section('page-title')
    @if (\Auth::user()->type == 'super admin')
        {{ __('Manage Companies') }}
    @else
        {{ __('Manage Users') }}
    @endif
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @if (\Auth::user()->type == 'super admin')
        <li class="breadcrumb-item">{{ __('Companies') }}</li>
    @else
        <li class="breadcrumb-item">{{ __('Users') }}</li>
    @endif
@endsection

@section('action-button')
    @if (Gate::check('Manage Employee Last Login'))
        @can('Manage Employee Last Login')
            <a href="{{ route('lastlogin') }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ __('User Logs History') }}"><i class="ti ti-user-check"></i>
            </a>
        @endcan
    @endif

    @can('Create User')
        @if (\Auth::user()->type == 'super admin')
            <a href="#" data-url="{{ route('companies.create') }}" data-ajax-popup="true"
                data-title="{{ __('Create New Company') }}" data-size="md" data-bs-toggle="tooltip" title=""
                class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        {{-- @else
            <a href="#" data-url="{{ route('user.create') }}" data-ajax-popup="true"
                data-title="{{ __('Create New User') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
                data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>--}}
        @endif 
    @endcan


@endsection


@php
    $logo = \App\Models\Utility::get_file('uploads/avatar/');
    
    $profile = \App\Models\Utility::get_file('uploads/avatar/');
@endphp
@section('content')
    <div class="row">

        <div class="row">

        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">

                    <div class="p-3 table-responsive">
                        <table class="table" id="pc-dt-simple">
    
                            <thead>
                                <tr>
                                    <th>{{ __('Company Id')}}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Password') }}</th>
                                    <th>{{ __('Mobile') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
    
                            <tbody>
                            @foreach($companies as $company)
                                <tr>
                                    <td>{{ $company->id }}</td>
                                    <td>{{ $company->name }}</td>
                                    <td>{{ $company->email }}</td>
                                    <td>{{ substr($company->password, 0, 8) }}...</td>
                                    <td>{{ $company->mobile }}</td>
                                    <td>
                                     <a href="#" data-url="{{ route('companies.edit', $company->id) }}" data-ajax-popup="true"
                                          data-title="{{ __('Edit Company') }}" data-size="md" data-bs-toggle="tooltip" title=""
                                          class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Edit') }}">
                                          <i class="ti ti-pencil"></i>
                                     </a>
                                     <a href="{{ route('companies.show', $company->id) }}" class="btn btn-sm btn-primary">
                                          <i class="ti ti-eye"></i>
                                     </a>
                                     <a href="{{ route('notifications.show', $company->id) }}" class="btn btn-sm btn-primary">
                                          <i class="ti ti-message"></i>
                                     </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
            
            {{-- @if (\Auth::user()->type == 'super admin')
                @foreach ($users as $user)
                    <div class="col-xl-3">
                        <div class="text-center card">
                            <div class="pb-0 border-0 card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <div class="p-2 px-3 rounded badge bg-primary">{{ ucfirst($user->type) }}</div>
                                    </h6>
                                </div>
                                <div class="card-header-right">
                                    <div class="btn-group card-option">
                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="#" class="dropdown-item"
                                                data-url="{{ route('user.edit', $user->id) }}" data-size="md"
                                                data-ajax-popup="true" data-title="{{ __('Update User') }}"><i
                                                    class="ti ti-edit "></i><span
                                                    class="ms-2">{{ __('Edit') }}</span></a>

                                            <a href="#" class="dropdown-item" data-ajax-popup="true" data-size="md"
                                                data-title="{{ __('Change Password') }}"
                                                data-url="{{ route('user.reset', \Crypt::encrypt($user->id)) }}"><i
                                                    class="ti ti-key"></i>
                                                <span class="ms-1">{{ __('Reset Password') }}</span></a>


                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => ['user.destroy', $user->id],
                                                'id' => 'delete-form-' . $user->id,
                                            ]) !!}
                                            <a href="#!" class="dropdown-item bs-pass-para">
                                                <i class="ti ti-trash"></i><span class="ms-1">
                                                    @if ($user->delete_status == 0)
                                                        {{ __('Delete') }}
                                                    @else
                                                        {{ __('Restore') }}
                                                    @endif
                                                </span>
                                            </a>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="avatar">
                                    <a href="{{ !empty($user->avatar) ? $profile . $user->avatar : $logo . 'avatar.png' }}"
                                        target="_blank">
                                        <img src="{{ !empty($user->avatar) ? $profile . $user->avatar : $logo . 'avatar.png' }}"
                                            class="rounded-circle" style="width: 10%">
                                    </a>
                                </div>
                                <h4 class="mt-2">{{ $user->name }}</h4>
                                <small>{{ $user->email }}</small>
                                @if (\Auth::user()->type == 'super admin')
                                    <div class="mt-3 mb-0 ">
                                        <div class="p-3 ">
                                            <div class="row">
                                                <div class="col-5 text-start">
                                                    <h6 class="px-2 mt-1 mb-0">
                                                        {{ !empty($user->currentPlan) ? $user->currentPlan->name : '' }}
                                                    </h6>
                                                </div>
                                                <div class="col-7 text-end">
                                                    <a href="#" data-url="{{ route('plan.upgrade', $user->id) }}"
                                                        class="btn btn-sm btn-primary btn-icon" data-size="lg"
                                                        data-ajax-popup="true"
                                                        data-title="{{ __('Upgrade Plan') }}">{{ __('Upgrade Plan') }}
                                                    </a>
                                                </div>
                                                <!--  <div class="col-6 {{ Auth::user()->type == 'admin' ? 'text-end' : 'text-start' }}  ">
                                                                                                    <h6 class="px-3 mb-0">{{ __('Plan Expired : ') }} {{ !empty($user->plan_expire_date) ? \Auth::user()->dateFormat($user->plan_expire_date) : __('Lifetime') }}</h6>
                                                                                                </div> -->
                                                <div class="mt-4 col-6 text-start">
                                                    <h6 class="px-3 mb-0">{{ $user->countUsers() }}</h6>
                                                    <p class="mb-0 text-sm text-muted">{{ __('Users') }}</p>
                                                </div>
                                                <div class="mt-4 col-6 text-end">
                                                    <h6 class="px-4 mb-0">{{ $user->countEmployees() }}</h6>
                                                    <p class="mb-0 text-sm text-muted">{{ __('Employees') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-2 mb-0">
                                        <button class="mt-3 btn btn-sm btn-neutral font-weight-500">
                                            <a>{{ __('Plan Expire : ') }}
                                                {{ !empty($user->plan_expire_date) ? \Auth::user()->dateFormat($user->plan_expire_date) : 'Lifetime' }}</a>
                                        </button>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach 
                <div class="col-xl-3 col-lg-4 col-sm-6">
                    <a href="#" class="my-4 btn-addnew-project " data-ajax-popup="true"
                        data-url="{{ route('user.create') }}" data-title="{{ __('Create New Company') }}"
                        data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
                        data-bs-original-title="{{ __('Create') }}">
                        <div class="my-4 bg-primary proj-add-icon">
                            <i class="ti ti-plus"></i>
                        </div>
                        <h6 class="mt-4 mb-2">{{ __('New Company') }}</h6>
                        <p class="text-center text-muted">{{ __('Click here to add new company') }}</p>
                    </a>
                </div>
            @else 
                 @foreach ($users as $user)
                    <div class="col-xl-3">
                        <div class="text-center card">
                            <div class="pb-0 border-0 card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <div class="p-2 px-3 rounded badge bg-primary">{{ ucfirst($user->type) }}</div>
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
                        <p class="text-center text-muted">{{ __('Click here to add new user') }}</p>
                    </a>
                </div>
            @endif --}}
        </div>
    </div>
@endsection
