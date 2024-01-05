@extends('layouts.admin')

@section('page-title')
{{ __('Manage Plan Request') }}
@endsection


@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
<li class="breadcrumb-item">{{ __('Plan Request') }}</li>
@endsection


@section('action-button')
<a id="exportbtn" href="{{ route('plan_request.export') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Export') }}">
    <i class="ti ti-file-export"></i>
</a>
@endsection


@section('content')

<div class="row">
    <div class="col-sm-12">
        <div class="mt-2" id="multiCollapseExample1">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => ['plan_request.index'], 'method' => 'get', 'id' => 'pr_filter']) }}
                    <div class="row align-items-center justify-content-end">
                        <div class="col-xl-10">
                            <div class="row">

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                    <div class="btn-box">
                                        {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                        {{ Form::date('start_date', isset($_GET['start_date']) ? $_GET['start_date'] : '', ['class' => 'month-btn form-control current_date', 'autocomplete' => 'off', 'id' => 'current_date']) }}
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                    <div class="btn-box">
                                        {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                        {{ Form::date('end_date', isset($_GET['end_date']) ? $_GET['end_date'] : '', ['class' => 'month-btn form-control current_date', 'autocomplete' => 'off', 'id' => 'current_date']) }}
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                    <div class="btn-box">
                                        {{ Form::label('company_id', __('Company Admin Name'), ['class' => 'form-label']) }}
                                        {{ Form::select('company_id', $companies, isset($_GET['company_id']) ? $_GET['company_id'] : '', ['class' => 'form-control select', 'id' => 'company_id']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="row">
                                <div class="col-auto mt-4">
                                    <a href="#" class="btn btn-sm btn-primary" onclick="document.getElementById('pr_filter').submit(); return false;" data-bs-toggle="tooltip" title="" data-bs-original-title="apply">
                                        <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                    </a>
                                    <a href="{{ route('plan_request.index') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="" data-bs-original-title="Reset">
                                        <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off "></i></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <div class="col-xl-12">
        <div class="card">
            <div class="card-header card-body table-border-style">
                <div class="table-responsive">

                    <table class="table header " width="100%">
                        <tbody>
                            @if ($plan_requests->count() > 0)
                            @foreach ($plan_requests as $prequest)
                            <thead>
                                <tr>
                                    <th>{{ __('Company ID')}}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Plan Name') }}</th>
                                    <th>{{ __('Total Users') }}</th>
                                    <th>{{ __('Total Employees') }}</th>
                                    <th>{{ __('Duration') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Action') }}</th>

                                </tr>
                            </thead>

                            <tr>
                                <td>
                                    <div class="font-style ">{{ $prequest->company_id }}</div>
                                </td>
                                <td>
                                    <div class="font-style ">{{ $prequest->user->name }}</div>
                                </td>
                                <td>
                                    <div class="font-style ">{{ $prequest->plan->name }}</div>
                                </td>
                                <td>
                                    <div class="">{{ $prequest->plan->max_users }}</div>
                                </td>
                                <td>
                                    <div class="">{{ $prequest->plan->max_employees }}</div>
                                </td>
                                <td>
                                    <div class="font-style ">
                                        {{ $prequest->duration == 'monthly' ? __('One Month') : __('One Year') }}
                                    </div>
                                </td>
                                <td>{{ Utility::getDateFormated($prequest->created_at, false) }}</td>
                                <td>
                                    <div>
                                        <a href="{{ route('response.request', [$prequest->id, 1]) }}" class="btn btn-success btn-sm">
                                            <i class="ti ti-check"></i>
                                        </a>
                                        <a href="{{ route('response.request', [$prequest->id, 0]) }}" class="btn btn-danger btn-sm">
                                            <i class="ti ti-x"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <th scope="col" colspan="7">
                                    <h6 class="text-center">{{ __('No Manually Plan Request Found.') }}</h6>
                                </th>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const exportbtn = document.getElementById('exportbtn');
        let urlParams = new URLSearchParams(window.location.search);
        let start_date = urlParams.get('start_date');
        let end_date = urlParams.get('end_date');
        
        let company_id = urlParams.get('company_id');
        // Use decodeURIComponent to properly decode URL parameters
        start_date = decodeURIComponent(start_date);
        end_date = decodeURIComponent(end_date);
        company_id = decodeURIComponent(company_id);
        console.log(start_date, end_date, employee)
        var dynamicUrl = `/export/plan_request?start_date=${start_date ? start_date : 'null '}&end_date=${end_date ? end_date : 'null'}&company_id=${company_id?company_id:'null'}`;


        exportbtn.setAttribute('href', dynamicUrl);
    });
</script>
@endsection