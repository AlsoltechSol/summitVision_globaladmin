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
@if (\Auth::user()->type == 'super admin' || Gate::check('Create Companies'))
<li class="breadcrumb-item">{{ __('Company Settings') }}</li>
@else
<li class="breadcrumb-item">{{ __('Users') }}</li>
@endif
@endsection

@section('action-button')

@endsection


@section('content')
<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8">
        <h5 class=""><strong>{{ __('Company Admin: ')}}</strong> {{ $company->name  }}</h5>
        <span>{{ $company->email }}</span>
        <span class="mb-2">( {{ $company->url??'N/A' }} )</span>

    </div>
</div>
<div class="mt-4 row">

    <div class="row">

        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">

                    <div class="p-3 table-responsive">
                        @php
                        $url = $company->url . '/api/get_settings_details';
                        $options = [
                        'http' => [
                        'method' => 'POST',
                        'header' => 'Content-type: application/x-www-form-urlencoded',
                        'ignore_errors' => true,
                        ],
                        ];
                        $context = stream_context_create($options);
                        $json_data = file_get_contents($url, false, $context);
                        $data = json_decode($json_data, true);

                        // Access the data as needed
                        $setting = $data['setting'];
                        $file_type = $data['file_type'];
                        $local_storage_validations = $data['local_storage_validations'];
                        $s3_storage_validations = $data['s3_storage_validations'];
                        $wasabi_storage_validations = $data['wasabi_storage_validations'];
                        @endphp

                        <!--storage Setting-->
                        <div id="storage-settings" class="">
                        {{ Form::open(['route' => ['company.storage.setting.store', 'company' => $company], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <h5 class="">{{ __('Storage Settings') }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="pe-2">
                                        <input type="radio" class="btn-check" name="storage_setting" id="local-outlined" autocomplete="off" {{ $setting['storage_setting'] == 'local' ? 'checked' : '' }} value="local" checked>
                                        <label class="btn btn-outline-success" for="local-outlined">{{ __('Local') }}</label>
                                    </div>
                                    <div class="pe-2">
                                        <input type="radio" class="btn-check" name="storage_setting" id="s3-outlined" autocomplete="off" {{ $setting['storage_setting'] == 's3' ? 'checked' : '' }} value="s3">
                                        <label class="btn btn-outline-success" for="s3-outlined">
                                            {{ __('AWS S3') }}</label>
                                    </div>

                                    <div class="pe-2">
                                        <input type="radio" class="btn-check" name="storage_setting" id="wasabi-outlined" autocomplete="off" {{ $setting['storage_setting'] == 'wasabi' ? 'checked' : '' }} value="wasabi">
                                        <label class="btn btn-outline-success" for="wasabi-outlined">{{ __('Wasabi') }}</label>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="local-setting row mb-4 {{ $setting['storage_setting'] == 'local' ? ' ' : 'd-none' }}">
                                        <!-- <h4 class="small-title">{{ __('Local Settings') }}</h4> -->
                                        <div class="form-group col-8 switch-width">
                                            {{ Form::label('local_storage_validation', __('Only Upload Files'), ['class' => ' form-label']) }}
                                            <select name="local_storage_validation[]" class="select2" id="local_storage_validation" multiple>
                                                @foreach ($file_type as $f)
                                                <option @if (in_array($f, $local_storage_validations)) selected @endif>
                                                    {{ $f }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label" for="local_storage_max_upload_size">{{ __('Max upload size ( In KB)') }}</label>
                                                <input type="number" name="local_storage_max_upload_size" class="form-control" value="{{ !isset($setting['local_storage_max_upload_size']) || is_null($setting['local_storage_max_upload_size']) ? '' : $setting['local_storage_max_upload_size'] }}" placeholder="{{ __('Max upload size') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="s3-setting row {{ $setting['storage_setting'] == 's3' ? ' ' : 'd-none' }}">

                                        <div class=" row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="s3_key">{{ __('S3 Key') }}</label>
                                                    <input type="text" name="s3_key" class="form-control" value="{{ !isset($setting['s3_key']) || is_null($setting['s3_key']) ? '' : $setting['s3_key'] }}" placeholder="{{ __('S3 Key') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="s3_secret">{{ __('S3 Secret') }}</label>
                                                    <input type="text" name="s3_secret" class="form-control" value="{{ !isset($setting['s3_secret']) || is_null($setting['s3_secret']) ? '' : $setting['s3_secret'] }}" placeholder="{{ __('S3 Secret') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="s3_region">{{ __('S3 Region') }}</label>
                                                    <input type="text" name="s3_region" class="form-control" value="{{ !isset($setting['s3_region']) || is_null($setting['s3_region']) ? '' : $setting['s3_region'] }}" placeholder="{{ __('S3 Region') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="s3_bucket">{{ __('S3 Bucket') }}</label>
                                                    <input type="text" name="s3_bucket" class="form-control" value="{{ !isset($setting['s3_bucket']) || is_null($setting['s3_bucket']) ? '' : $setting['s3_bucket'] }}" placeholder="{{ __('S3 Bucket') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="s3_url">{{ __('S3 URL') }}</label>
                                                    <input type="text" name="s3_url" class="form-control" value="{{ !isset($setting['s3_url']) || is_null($setting['s3_url']) ? '' : $setting['s3_url'] }}" placeholder="{{ __('S3 URL') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="s3_endpoint">{{ __('S3 Endpoint') }}</label>
                                                    <input type="text" name="s3_endpoint" class="form-control" value="{{ !isset($setting['s3_endpoint']) || is_null($setting['s3_endpoint']) ? '' : $setting['s3_endpoint'] }}" placeholder="{{ __('S3 Endpoint') }}">
                                                </div>
                                            </div>
                                            <div class="form-group col-8 switch-width">
                                                {{ Form::label('s3_storage_validation', __('Only Upload Files'), ['class' => ' form-label']) }}
                                                <select name="s3_storage_validation[]" class="select2" id="s3_storage_validation" multiple>
                                                    @foreach ($file_type as $f)
                                                    <option @if (in_array($f, $s3_storage_validations)) selected @endif>
                                                        {{ $f }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label class="form-label" for="s3_max_upload_size">{{ __('Max upload size ( In KB)') }}</label>
                                                    <input type="number" name="s3_max_upload_size" class="form-control" value="{{ !isset($setting['s3_max_upload_size']) || is_null($setting['s3_max_upload_size']) ? '' : $setting['s3_max_upload_size'] }}" placeholder="{{ __('Max upload size') }}">
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="wasabi-setting row {{ $setting['storage_setting'] == 'wasabi' ? ' ' : 'd-none' }}">
                                        <div class=" row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="s3_key">{{ __('Wasabi Key') }}</label>
                                                    <input type="text" name="wasabi_key" class="form-control" value="{{ !isset($setting['wasabi_key']) || is_null($setting['wasabi_key']) ? '' : $setting['wasabi_key'] }}" placeholder="{{ __('Wasabi Key') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="s3_secret">{{ __('Wasabi Secret') }}</label>
                                                    <input type="text" name="wasabi_secret" class="form-control" value="{{ !isset($setting['wasabi_secret']) || is_null($setting['wasabi_secret']) ? '' : $setting['wasabi_secret'] }}" placeholder="{{ __('Wasabi Secret') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="s3_region">{{ __('Wasabi Region') }}</label>
                                                    <input type="text" name="wasabi_region" class="form-control" value="{{ !isset($setting['wasabi_region']) || is_null($setting['wasabi_region']) ? '' : $setting['wasabi_region'] }}" placeholder="{{ __('Wasabi Region') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="wasabi_bucket">{{ __('Wasabi Bucket') }}</label>
                                                    <input type="text" name="wasabi_bucket" class="form-control" value="{{ !isset($setting['wasabi_bucket']) || is_null($setting['wasabi_bucket']) ? '' : $setting['wasabi_bucket'] }}" placeholder="{{ __('Wasabi Bucket') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="wasabi_url">{{ __('Wasabi URL') }}</label>
                                                    <input type="text" name="wasabi_url" class="form-control" value="{{ !isset($setting['wasabi_url']) || is_null($setting['wasabi_url']) ? '' : $setting['wasabi_url'] }}" placeholder="{{ __('Wasabi URL') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="wasabi_root">{{ __('Wasabi Root') }}</label>
                                                    <input type="text" name="wasabi_root" class="form-control" value="{{ !isset($setting['wasabi_root']) || is_null($setting['wasabi_root']) ? '' : $setting['wasabi_root'] }}" placeholder="{{ __('Wasabi Root') }}">
                                                </div>
                                            </div>
                                            <div class="form-group col-8 switch-width">
                                                {{ Form::label('wasabi_storage_validation', __('Only Upload Files'), ['class' => 'form-label']) }}

                                                <select name="wasabi_storage_validation[]" class="select2" id="wasabi_storage_validation" multiple>
                                                    @foreach ($file_type as $f)
                                                    <option @if (in_array($f, $wasabi_storage_validations)) selected @endif>
                                                        {{ $f }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label class="form-label" for="wasabi_root">{{ __('Max upload size ( In KB)') }}</label>
                                                    <input type="number" name="wasabi_max_upload_size" class="form-control" value="{{ !isset($setting['wasabi_max_upload_size']) || is_null($setting['wasabi_max_upload_size']) ? '' : $setting['wasabi_max_upload_size'] }}" placeholder="{{ __('Max upload size') }}">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-footer text-end">
                                        <input class="btn btn-print-invoice btn-primary m-r-10" type="submit" value="{{ __('Save Changes') }}">
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

@endsection