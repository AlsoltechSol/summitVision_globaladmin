@extends('layouts.admin')

@section('page-title')
{{ __('Manage Plan Order') }}
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
<li class="breadcrumb-item">{{ __('Plan Order') }}</li>
@endsection

@php
$file = \App\Models\Utility::get_file('uploads/order/');
@endphp

@section('action-button')
<a id="exportbtn" href="{{ route('order.export') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Export') }}">
    <i class="ti ti-file-export"></i>
</a>
@endsection


@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="mt-2" id="multiCollapseExample1">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => ['order.index'], 'method' => 'get', 'id' => 'order_filter']) }}
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
                                        {{ Form::label('payment_status', __('Payment Status'), ['class' => 'form-label']) }}
                                        {{ Form::select('payment_status', ['Pending' => 'Pending', 'Approved' => 'Approved', 'Rejected' => 'Rejected'], isset($_GET['payment_status']) ? $_GET['payment_status'] : '', ['class' => 'form-control select', 'id' => 'payment_status_id']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="row">
                                <div class="col-auto mt-4">
                                    <a href="#" class="btn btn-sm btn-primary" onclick="document.getElementById('order_filter').submit(); return false;" data-bs-toggle="tooltip" title="" data-bs-original-title="apply">
                                        <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                    </a>
                                    <a href="{{ route('order.index') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="" data-bs-original-title="Reset">
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
                {{-- <h5></h5> --}}
                <div class="table-responsive">
                    <table class="table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th>{{ __('Order Id') }}</th>
                                <th>{{ __('Company ID')}}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Plan Name') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Coupon') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Invoice') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->order_id }}</td>
                                <td>{{ $order->company_id }}</td>
                                <td>{{ $order->user_name }}</td>
                                <td>{{ $order->plan_name }}</td>
                                <td>{{ (!empty(env('CURRENCY_SYMBOL')) ? env('CURRENCY_SYMBOL') : '$') . $order->price }}
                                </td>
                                <td>
                                    @if ($order->payment_status == 'succeeded')
                                    <i class="mdi mdi-circle text-success"></i>
                                    {{ ucfirst($order->payment_status) }}
                                    @else
                                    <i class="mdi mdi-circle text-danger"></i>
                                    {{ ucfirst($order->payment_status) }}
                                    @endif
                                </td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td>{{ !empty($order->total_coupon_used) ? (!empty($order->total_coupon_used->coupon_detail) ? $order->total_coupon_used->coupon_detail->code : '-') : '-' }}
                                </td>
                                <td>{{ $order->payment_type }}</td>
                                <td class="text-center Id">
                                    @if (!empty($order->receipt && !empty($order->payment_type == 'STRIPE')))
                                    <a href="{{ $order->receipt }}" class="btn btn-outline-primary" target="_blank"><i class="fas fa-file-invoice"></i></a>
                                    @elseif(!empty($order->receipt && !empty($order->payment_type == 'Bank Transfer')))
                                    <a href="{{ $file . '' . $order->receipt }}" class="btn btn-outline-primary" target="_blank"><i class="fas fa-file-invoice"></i></a>
                                    @else
                                    <p>-</p>
                                    @endif
                                </td>
                                <td>
                                    @if (\Auth::user()->type == 'super admin')
                                    @if ($order->payment_status == 'Pending')
                                    <div class="action-btn bg-success ms-2">
                                        <a href="#" class="mx-3 btn btn-sm align-items-center" data-size="lg" data-url="{{ URL::to('order/' . $order->id . '/action') }}" data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title="" data-title="{{ __('Order Action') }}" data-bs-original-title="{{ __('Manage Order') }}">
                                            <i class="text-white ti ti-caret-right"></i>
                                        </a>
                                    </div>
                                    @endif
                                    {{-- @elseif(\Auth::user()->type == 'company' && $order->payment_type == 'Bank Transfer')
                                                <div class="action-btn bg-success ms-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center"
                                                        data-size="lg"
                                                        data-url="{{ URL::to('order/' . $order->id . '/action') }}"
                                    data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                    title="" data-title="{{ __('Manage Order') }}"
                                    data-bs-original-title="{{ __('Manage Order') }}">
                                    <i class="text-white ti ti-caret-right"></i>
                                    </a>
                </div> --}}
                @else
                @endif

                @if (\Auth::user()->type == 'super admin')
                <div class="action-btn bg-danger ms-2">
                    {!! Form::open([
                    'method' => 'DELETE',
                    'route' => ['order.destroy', $order->id],
                    'id' => 'delete-form-' . $order->id,
                    ]) !!}
                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="" data-bs-original-title="Delete" aria-label="Delete"><i class="text-white ti ti-trash"></i></a>
                    </form>
                </div>
                @endif
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const exportbtn = document.getElementById('exportbtn');
        let urlParams = new URLSearchParams(window.location.search);
        let start_date = urlParams.get('start_date');
        let end_date = urlParams.get('end_date');
        
        let payment_status = urlParams.get('payment_status');
        // Use decodeURIComponent to properly decode URL parameters
        start_date = decodeURIComponent(start_date);
        end_date = decodeURIComponent(end_date);
        payment_status = decodeURIComponent(payment_status);
  
        var dynamicUrl = `/export/order?start_date=${start_date ? start_date : 'null '}&end_date=${end_date ? end_date : 'null'}&payment_status=${payment_status?payment_status:'null'}`;


        exportbtn.setAttribute('href', dynamicUrl);
    });
</script>
@endsection