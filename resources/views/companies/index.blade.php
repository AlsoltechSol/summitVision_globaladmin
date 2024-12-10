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
                                        Failed
                                        @elseif($company->server_config_status == 1)
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
                                            {!! Form::open(['method' => "POST", 'route' => ['companies.destroy_company', $company->id], 'id' => 'delete-form-' . $company->id]) !!}
                                            <a href="#" class=" btn btn-sm btn-primary bs-pass-para" data-bs-toggle="tooltip" title="Delete Company and server details" data-bs-original-title="Delete Company and server details" aria-label="Delete"><i class="text-white ti ti-trash"></i></a>
                                            <input hidden type="checkbox" name="delete_permanent" id="delete_permanent_{{$company->id}}" value="1" checked> <small hidden>Skip trash and delete this company permanently.</small>

                                            </form>
                                            @elseif($company->server_config_status == 1 && $company->id != 5)
                                            {{-- {!! Form::open(['method' => "POST", 'route' => ['companies.destroy_company', $company->id], 'id' => 'delete-form-' . $company->id]) !!} --}}
                                            <a href="#" class="btn btn-sm btn-primary" title="Delete Company and server details" data-bs-original-title="Delete Company and server details" aria-label="Delete" onclick="event.preventDefault(); confirmDelete({{ $company->id }})">
                                                <i class="text-white ti ti-trash"></i>
                                            </a>
                                            {{-- {!! Form::close() !!} --}}
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
                                                            <form action="{{ route('companies.destroy_company', $company->id) }}" method="post" id="delete-form-modal_{{$company->id}}">
                                                            @csrf
                                                            @method('POST')
                                                            <p><strong>Company name</strong> {{ $company->company_name }}</p>
                                                            <p style="text-wrap: wrap; font-weight: 600; color: red; font-size: 11px;">
                                                                Note: This is a live company panel. Deleting it will cause the panel to no longer function.
                                                                you can choose which resources to delete using the server resources options below.
                                                            </p>

                                                            <p>Choose Server Resources to delete:</p>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="delete_subdomain" value="0" onchange="this.value = this.checked ? 1 : 0;" id="deleteSubdomain_{{$company->id}}">
                                                                <label class="form-check-label" for="deleteSubdomain">
                                                                    Delete subdomain
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"  name="delete_database" value="0" id="hiddenDeleteDatabase_{{$company->id}}" onchange="this.value = this.checked ? 1 : 0;">
                                                                <label class="form-check-label" for="deleteDatabase">
                                                                    Delete database and username
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"  name="delete_project_directory" value="0" id="hiddenDeleteProjectDirectory_{{$company->id}}" onchange="this.value = this.checked ? 1 : 0;">
                                                                <label class="form-check-label" for="deleteProjectDirectory">
                                                                    Delete project directory
                                                                </label>
                                                            </div>
                                                            <p class="mt-3"> Please type "delete" to confirm:</p>
                                                            <input type="text" id="deleteConfirmationInput_{{$company->id}}" class="form-control" placeholder="Type 'delete' to confirm">
                                                           
                                                                {{-- {!! Form::open(['method' => "POST", 'route' => ['companies.destroy_company', $company->id], 'id' => 'delete-form-modal_' .$company->id]) !!} --}}
                                                                <input type="hidden"  id="hiddenDeleteSubdomain_{{$company->id}}">
                                                                <input type="hidden" name="delete_faild_server_setup_company" value="1">
                                                                {{-- <input type="hidden" name="delete_database" value="0" id="hiddenDeleteDatabase_{{$company->id}}" onchange="this.value = this.checked ? 1 : 0;"> --}}
                                                                {{-- <input type="hidden" name="delete_project_directory" value="0" id="hiddenDeleteProjectDirectory_{{$company->id}}" onchange="this.value = this.checked ? 1 : 0;"> --}}
                                                                <input type="checkbox" name="delete_permanent" value="0" id="delete_permanent_{{$company->id}}" onchange="this.value = this.checked ? 1 : 0;"> <small>Skip trash and delete this company permanently.</small>
                                                                {{-- {!! Form::close() !!} --}}
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger" form="delete-form-modal_{{$company->id}}" id="confirmDeleteButton_{{$company->id}}">Delete</button>
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
                // document.getElementById("confirmDeleteButton_" + companyId).onclick = function() {
                //     var confirmationInput = document.getElementById("deleteConfirmationInput_" + companyId).value;
                //     if (confirmationInput.toLowerCase() !== 'delete') {
                //         alert('You must type "delete" to confirm.');
                //         return;
                //     }

                //     // Set the hidden inputs for the form submission
                //     document.getElementById("hiddenDeleteSubdomain_" + companyId).value = document.getElementById("deleteSubdomain_" + companyId).checked ? 1 : 0;
                //     document.getElementById("hiddenDeleteDatabase_" + companyId).value = document.getElementById("deleteDatabase_" + companyId).checked ? 1 : 0;
                //     document.getElementById("hiddenDeleteProjectDirectory_" + companyId).value = document.getElementById("deleteProjectDirectory_" + companyId).checked ? 1 : 0;

                //     // Submit the form
                //     document.getElementById("delete-form-modal_" + companyId).submit();
                // }
            }
        </script>
      
</div>
</div>
@endsection