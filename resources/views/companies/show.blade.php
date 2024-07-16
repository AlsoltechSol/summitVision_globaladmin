<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <p><strong>Name:</strong> {{ $company->name }}</p>
            <p><strong>Email:</strong> {{ $company->email }}</p>
            <p><strong>Mobile:</strong> {{ $company->mobile }}</p>
            {{-- <p><strong>Password:</strong> {{ $company->password }}</p> --}}
            <p><strong>URL:</strong> {{ $company->url }}</p>
            <p><strong>Is Verified:</strong> {{ $company->is_verified == 1 ? 'Verified' : 'Non verified'}}</p>
            <p><strong>Server Config Status:</strong> {{ $company->server_config_status == 1 ? 'Success' : 'Faild'}}</p>
            <p><strong>DB_DATABASE:</strong> {{ $company->DB_DATABASE }}</p>
            <p><strong>DB_USERNAME:</strong> {{ $company->DB_USERNAME }}</p>
            <p><strong>DB_HOST:</strong> {{ $company->DB_HOST }}</p>
            <p><strong>Sub Domain:</strong> {{ $company->sub_domain }}</p>
        </div>
        <div class="col-md-12">
            <p><strong>Create Domain and Dir:</strong> {{ $company->create_domain_and_dir == 1 ? 'Success' : 'Faild'  }}</p>
            <p><strong>Database Create and Config:</strong> {{ $company->database_create_and_config == 1 ? 'Success' : 'Faild' }}</p>
            <p><strong>Fileop:</strong> {{ $company->fileop == 1 ? 'Success' : 'Faild' }}</p>
            <p><strong>Modify Env:</strong> {{ $company->modify_env == 1 ? 'Success' : 'Faild'  }}</p>
            <p><strong>Plan:</strong> {{ $company->plan }}</p>
            <p><strong>Plan Expire Date:</strong> {{ $company->plan_expire_date }}</p>
            <p><strong>Company Name:</strong> {{ $company->company_name }}</p>
            <p><strong>Setup by Cron:</strong> {{ $company->setup_by_cron > 1 ? 'Success' : ( $company->setup_by_cron == null ? 'Never Executed' : 'Faild' )  }}</p>
            <p><strong>Server Setup Started At:</strong> {{ $company->server_setup_started_at }}</p>
        </div>
    </div>
</div>
