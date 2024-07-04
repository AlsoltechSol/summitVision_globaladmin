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
<li class="breadcrumb-item">{{ __('Companies') }}</li>

@endsection

@section('action-button')

<a href="{{ route('companies.deleted') }}"  class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Deleted companies') }}">
    <i class="ti ti-trash"></i>
</a>

@if (\Auth::user()->type == 'super admin' || Gate::check('Create Companies'))
<a href="#" data-url="{{ route('companies.create') }}" data-ajax-popup="true" data-title="{{ __('Create New Company') }}" data-size="md" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
    <i class="ti ti-plus"></i>
</a>
@endif

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
                                    <th>{{ __('Server Config')}}</th>
                                    <th>{{ __('Server Config By Cron')}}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($companies as $company)
                                <tr>
                                    <td>{{ $company->id }}</td>
                                    <td>{{ $company->name }}</td>
                                    <td>
                                        @if($company->is_verified == 1)
                                        <a class=" text-success" title="verified">{{ $company->email }}</a>
                                        @else
                                        <a class="text-danger" title="not verified">{{ $company->email }}</a>
                                        @endif
                                    </td>
                                    <td>{{ substr($company->password, 0, 8) }}...</td>
                                    <td>{{ $company->mobile }}</td>
                                    <td>
                                        @if($company->server_config_status == 1)
                                        <i class="ti ti-check text-success" style="font-size: 16px; font-weight: 500;"></i>
                                        @else
                                        <i class="ti ti-square-x text-danger" style="font-size: 16px; font-weight: 500;"></i>
                                        @endif
                                    </td>
                                    <td>

                                        @if($company->setup_by_cron == null)
                                        Never Executed
                                        @elseif($company->setup_by_cron == 0)
                                        Faild
                                        @else
                                        Success
                                        @endif
                                    </td>
                                    <td>
                                        <div class="gap-2 d-flex align-items-start justify-content-start">
                                            <a href="#" data-url="{{ route('companies.edit', $company->id) }}" data-ajax-popup="true" data-title="{{ __('Edit Company') }}" data-size="md" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Edit') }}">
                                                <i class="ti ti-pencil"></i>
                                            </a>

                                            <a href="#" data-url="{{ route('companies.show', $company->id) }}" class="btn btn-sm btn-primary" data-ajax-popup="true" data-title="{{ __('Show Company data') }}" data-size="md" data-bs-toggle="tooltip" title="" data-bs-original-title="{{ __('Show') }}">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            @if($company->server_config_status == 1)
                                            <a href="{{ route('companies.settings', $company->id) }}" class="btn btn-sm btn-primary">
                                                <i class="ti ti-settings"></i>
                                            </a>
                                            @endif

                                            @if($company->server_config_status == 0)
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['companies.destroy', $company->id], 'id' => 'delete-form-' . $company->id]) !!}
                                            <a href="#" class=" btn btn-sm btn-primary bs-pass-para" data-bs-toggle="tooltip" title="Delete Company and server details" data-bs-original-title="Delete Company and server details" aria-label="Delete"><i class="text-white ti ti-trash"></i></a>
                                            </form>
                                            @elseif($company->server_config_status == 1 && $company->id != 5)
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['companies.destroy', $company->id], 'id' => 'delete-form-' . $company->id]) !!}
                                            <a href="#" class="btn btn-sm btn-primary" title="Delete Company and server details" data-bs-original-title="Delete Company and server details" aria-label="Delete" onclick="confirmDelete({{ $company->id }})">
                                                <i class="text-white ti ti-trash"></i>
                                            </a>
                                            {!! Form::close() !!}
                                            @endif

                                            <!-- Modal -->
                                            <div class="modal fade" id="deleteModal_{{$company->id}}" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><strong>Company name</strong> {{ $company->company_name }}</p>
                                                            <p style="text-wrap: wrap; font-weight: 600; color: red; font-size: 11px;">
                                                                Note: This is a live company panel. Deleting it will cause the panel to no longer function.
                                                                you can choose which resources to delete using the server resources options below.
                                                            </p>

                                                            <p>Choose Server Resources to delete:</p>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="1" id="deleteSubdomain_{{$company->id}}">
                                                                <label class="form-check-label" for="deleteSubdomain">
                                                                    Delete subdomain
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="1" id="deleteDatabase_{{$company->id}}">
                                                                <label class="form-check-label" for="deleteDatabase">
                                                                    Delete database and username
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="1" id="deleteProjectDirectory_{{$company->id}}">
                                                                <label class="form-check-label" for="deleteProjectDirectory">
                                                                    Delete project directory
                                                                </label>
                                                            </div>
                                                            <p class="mt-3"> Please type "delete" to confirm:</p>
                                                            <input type="text" id="deleteConfirmationInput_{{$company->id}}" class="form-control" placeholder="Type 'delete' to confirm">

                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['companies.destroy', $company->id], 'id' => 'delete-form-modal_' .$company->id]) !!}
                                                            <input type="hidden" name="delete_subdomain" id="hiddenDeleteSubdomain_{{$company->id}}">
                                                            <input type="hidden" name="delete_faild_server_setup_company" value="1">
                                                            <input type="hidden" name="delete_database" id="hiddenDeleteDatabase_{{$company->id}}">
                                                            <input type="hidden" name="delete_project_directory" id="hiddenDeleteProjectDirectory_{{$company->id}}">
                                                            {!! Form::close() !!}
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="button" class="btn btn-danger" id="confirmDeleteButton_{{$company->id}}">Delete</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function confirmDelete(companyId) {
                // Open the modal
                var deleteModal = new bootstrap.Modal(document.getElementById("deleteModal_" + companyId), {});
                deleteModal.show();
                document.getElementById("deleteConfirmationInput_" + companyId).value = '';
                document.getElementById("hiddenDeleteSubdomain_" + companyId).value = '';
                document.getElementById("hiddenDeleteDatabase_" + companyId).value = '';
                document.getElementById("hiddenDeleteProjectDirectory_" + companyId).value = '';

                document.getElementById("deleteSubdomain_" + companyId).checked = false;
                document.getElementById("deleteDatabase_" + companyId).checked = false;
                document.getElementById("deleteProjectDirectory_" + companyId).checked = false;
                // Handle the delete button click
                document.getElementById("confirmDeleteButton_" + companyId).onclick = function() {
                    var confirmationInput = document.getElementById("deleteConfirmationInput_" + companyId).value;
                    if (confirmationInput.toLowerCase() !== 'delete') {
                        alert('You must type "delete" to confirm.');
                        return;
                    }

                    // Set the hidden inputs for the form submission
                    document.getElementById("hiddenDeleteSubdomain_" + companyId).value = document.getElementById("deleteSubdomain_" + companyId).checked ? 1 : 0;
                    document.getElementById("hiddenDeleteDatabase_" + companyId).value = document.getElementById("deleteDatabase_" + companyId).checked ? 1 : 0;
                    document.getElementById("hiddenDeleteProjectDirectory_" + companyId).value = document.getElementById("deleteProjectDirectory_" + companyId).checked ? 1 : 0;

                    // Submit the form
                    document.getElementById("delete-form-modal_" + companyId).submit();
                }
            }
        </script>
        {{-- @if (\Auth::user()->type == 'super admin')
                @foreach ($users as $user)
                    <div class="col-xl-3">
                        <div class="text-center card">
                            <div class="pb-0 border-0 card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <div class="p-2 px-3 rounded badge bg-primary">{{ ucfirst($user->type) }}
    </div>
    </h6>
</div>
<div class="card-header-right">
    <div class="btn-group card-option">
        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="feather icon-more-vertical"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end">
            <a href="#" class="dropdown-item" data-url="{{ route('user.edit', $user->id) }}" data-size="md" data-ajax-popup="true" data-title="{{ __('Update User') }}"><i class="ti ti-edit "></i><span class="ms-2">{{ __('Edit') }}</span></a>

            <a href="#" class="dropdown-item" data-ajax-popup="true" data-size="md" data-title="{{ __('Change Password') }}" data-url="{{ route('user.reset', \Crypt::encrypt($user->id)) }}"><i class="ti ti-key"></i>
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
        <a href="{{ !empty($user->avatar) ? $profile . $user->avatar : $logo . 'avatar.png' }}" target="_blank">
            <img src="{{ !empty($user->avatar) ? $profile . $user->avatar : $logo . 'avatar.png' }}" class="rounded-circle" style="width: 10%">
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
                    <a href="#" data-url="{{ route('plan.upgrade', $user->id) }}" class="btn btn-sm btn-primary btn-icon" data-size="lg" data-ajax-popup="true" data-title="{{ __('Upgrade Plan') }}">{{ __('Upgrade Plan') }}
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
    <a href="#" class="my-4 btn-addnew-project " data-ajax-popup="true" data-url="{{ route('user.create') }}" data-title="{{ __('Create New Company') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
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
                    <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="feather icon-more-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        @can('Edit User')
                        <a href="#" class="dropdown-item" data-url="{{ route('user.edit', $user->id) }}" data-size="md" data-ajax-popup="true" data-title="{{ __('Update User') }}"><i class="ti ti-edit "></i><span class="ms-2">{{ __('Edit') }}</span></a>
                        @endcan



                        <a href="#" class="dropdown-item" data-ajax-popup="true" data-size="md" data-title="{{ __('Change Password') }}" data-url="{{ route('user.reset', \Crypt::encrypt($user->id)) }}"><i class="ti ti-key"></i>
                            <span class="ms-1">{{ __('Reset Password') }}</span></a>

                        @can('Delete User')
                        {!! Form::open([
                        'method' => 'DELETE',
                        'route' => ['user.destroy', $user->id],
                        'id' => 'delete-form-' . $user->id,
                        ]) !!}
                        <a href="#" class="bs-pass-para dropdown-item" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="delete-form-{{ $user->id }}" title="{{ __('Delete') }}" data-bs-toggle="tooltip" data-bs-placement="top"><i class="ti ti-trash"></i><span class="ms-2">{{ __('Delete') }}</span></a>
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
                <a href="{{ !empty($user->avatar) ? $profile . $user->avatar : $logo . 'avatar.png' }}" target="_blank">
                    <img src="{{ !empty($user->avatar) ? $profile . $user->avatar : $logo . 'avatar.png' }}" class="rounded-circle" style="width: 30%">
                </a>

            </div>
            <h4 class="mt-2 text-primary">{{ $user->name }}</h4>
            <small class="">{{ $user->email }}</small>

        </div>
    </div>
</div>
@endforeach
<div class="col-xl-3 col-lg-4 col-sm-6">
    <a href="#" class="btn-addnew-project " data-ajax-popup="true" data-url="{{ route('user.create') }}" data-title="{{ __('Create New User') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
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