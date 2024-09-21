{!! Form::open(['route' => isset($company) ? ['companies.update', $company->id] : 'companies.store', 'method' => isset($company) ? 'put' : 'post']) !!}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
            <div class="form-icon-user">
                {!! Form::text('name', isset($company) ? $company->name : null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter Name']) !!}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}
            <div class="form-icon-user">
                {!! Form::text('email', isset($company) ? $company->email : null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter Email']) !!}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('company_name', __('Company Name'), ['class' => 'form-label']) }}
            <div class="form-icon-user">
                {!! Form::text('company_name', isset($company) ? $company->company_name : null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter Company Name']) !!}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('mobile', __('Mobile'), ['class' => 'form-label']) }}
            <div class="form-icon-user">
                {!! Form::text('mobile', isset($company) ? $company->mobile : null, ['class' => 'form-control', 'placeholder' => 'Enter Mobile']) !!}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('password', __('Password'), ['class' => 'form-label']) }}
            <div class="form-icon-user">
                {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Enter Password', 'id' => 'password-input']) !!}
                <span class="cursor-pointer toggle-password" onclick="togglePasswordVisibility()">Show</span>
            </div>
        </div>
        @if(isset($company))
        <div class="form-group">
            {{ Form::label('url', __('Url'), ['class' => 'form-label']) }}
            <div class="form-icon-user">
                {!! Form::text('url', isset($company) ? $company->url : null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter Url']) !!}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('plan', __('Plan'), ['class' => 'form-label']) }}
            <div class="form-icon-user">
                {!! Form::number('plan', isset($company) ? $company->plan : null, ['class' => 'form-control', 'placeholder' => 'Enter Plan']) !!}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('plan_expire_date', __('Plan Expire Date'), ['class' => 'form-label']) }}
            <div class="form-icon-user">
                {!! Form::date('plan_expire_date', isset($company) ? $company->plan_expire_date : null, ['class' => 'form-control', 'placeholder' => 'Enter Plan Expire Date']) !!}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('DB_DATABASE', __('DB Database'), ['class' => 'form-label']) }}
            <div class="form-icon-user">
                {!! Form::text('DB_DATABASE', isset($company) ? $company->DB_DATABASE : null, ['class' => 'form-control', 'placeholder' => 'Enter DB Database']) !!}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('DB_USERNAME', __('DB Username'), ['class' => 'form-label']) }}
            <div class="form-icon-user">
                {!! Form::text('DB_USERNAME', isset($company) ? $company->DB_USERNAME : null, ['class' => 'form-control', 'placeholder' => 'Enter DB Username']) !!}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('DB_HOST', __('DB Host'), ['class' => 'form-label']) }}
            <div class="form-icon-user">
                {!! Form::text('DB_HOST', isset($company) ? $company->DB_HOST : 'localhost:3306', ['class' => 'form-control', 'placeholder' => 'Enter DB Host']) !!}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('DB_PASSWORD', __('DB Password'), ['class' => 'form-label']) }}
            <div class="form-icon-user">
                {!! Form::text('DB_PASSWORD', isset($company) ? $company->DB_PASSWORD : '', ['class' => 'form-control', 'placeholder' => 'Enter DB Password']) !!}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('sub_domain', __('Sub Domain'), ['class' => 'form-label']) }}
            <div class="form-icon-user">
                {!! Form::text('sub_domain', isset($company) ? $company->sub_domain : null, ['class' => 'form-control', 'placeholder' => 'Enter Sub Domain']) !!}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('is_verified', __('Is Verified'), ['class' => 'form-label']) }}
            <div class="form-icon-user">
                {!! Form::checkbox('is_verified', '1', isset($company) ? $company->is_verified == '1' : false) !!}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('server_config_status', __('Server Config Status'), ['class' => 'form-label']) }}
            <div class="form-icon-user">
                {!! Form::checkbox('server_config_status', '1', isset($company) ? $company->server_config_status == '1' : false) !!}
            </div>
        </div>
        @endif
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ isset($company) ? __('Update') : __('Create') }}" class="btn btn-primary">
</div>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password-input');
        const togglePassword = document.querySelector('.toggle-password');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            togglePassword.textContent = 'Hide';
        } else {
            passwordInput.type = 'password';
            togglePassword.textContent = 'Show';
        }
    }
</script>

{!! Form::close() !!}
