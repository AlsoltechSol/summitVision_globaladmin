@extends('layouts.admin')

@section('page-title')
{{ __('Deleted Companies') }}
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>

<li class="breadcrumb-item"><a href="{{ route('companies.index') }}">{{ __('Companies') }}</a></li>

@endsection

@section('action-button')

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
                                    <th>{{ __('Deleted at')}}</th>
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
                                    <td>{{ $company->deleted_at }}</td>
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

                                            {!! Form::open(['method' => 'post', 'route' => ['companies.restore', $company->id], 'id' => 'restore-form-' . $company->id]) !!}
                                            <a href="#" class=" btn btn-sm btn-primary bs-pass-para" data-bs-toggle="tooltip" data-bs-original-title="Restore this company" aria-label="Delete"><i class="text-white ti ti-recycle"></i></a>
                                            </form>
                                     

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


</div>
</div>
@endsection