@extends('layouts.admin')

@section('page-title')

{{ __('Server Setup') }}

@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
<li class="breadcrumb-item">{{ __('Companies') }}</li>
<li class="breadcrumb-item">{{ __('Server Setup') }}</li>
@endsection

@section('action-button')

@endsection


@section('content')



<div class="mt-4 row">

    <div class="row">

        <div class="col-xl-12">
            @if(isset($company))
            <div class="p-5 pt-3 d-flex align-items-center flex-column">
                <input type="text" hidden id="verification_token" value="{{ isset($company) ? $company['verification_token'] : '' }}">
                <h5 id="show_bar">Please wait few minutes as scripts are configuring the database and application.</h5>
                <div class="mt-5 loader"></div>
        
            </div>
            @else
            <div class="p-5 pt-3 d-flex align-items-center flex-column">
                <h5>Sorry something went wrong company object not found..</h5>
            </div>
            @endif
        </div>


    </div>
</div>
@push('script-page')
<script>
    console.log('first')
    $(document).ready(function() {
        const company = @json($company);
        const companyString = encodeURIComponent(JSON.stringify(company));
        console.log(companyString)

        function httpRequests(data, url, company) {
           return new Promise((resolve, reject) => {
               $.ajax({
                   url: "https://summitconnect.sg"+url,
                   method: "POST",
                   async: true, 
                   data: {
                       "company": JSON.stringify(company),
                       "data": data,
                   },
                   success: function(response) {
                       console.log(response)
                       if (response.status == 200) {
                           show_toastr('Success', response?.message, 'success');
                           resolve(response); 
                       } else {
                           show_toastr('Error', response?.message, 'error');
                           reject(new Error(response?.message)); 
                       }
                   },
                   error: function(xhr, status, error) {
                       show_toastr('Error', error?.message, 'error');
                       reject(error); 
                   }
               });
           });
        }
        
        setTimeout(() => {
      
            $.ajax({
                url: "https://summitconnect.sg/create_subdomain_and_dir_globaladmin",
                method: "POST",
                async: false,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "company": {!! json_encode($company) !!},
                },
                success: function(data) {
                    console.log(data)
                    if (data.status == 200) {
                        show_toastr('Success', data?.message, 'success');
                        let obj = {
                            dbname: null
                        }
                        
                        let cpannel_create_database_put_data = httpRequests(obj, '/cpannel_create_database_put_data_gbadmin', company);
                  
                        cpannel_create_database_put_data.then(cpannel_create_database_put_data_response => {
                            if (cpannel_create_database_put_data_response.status === 200) {
                                show_toastr('Success', cpannel_create_database_put_data_response.message, 'success');
                                obj.dbname = cpannel_create_database_put_data_response.dbname;
                                console.log(obj)
                                return httpRequests(obj, '/create_db_user', cpannel_create_database_put_data_response.company);
                            } else {
                                show_toastr('Error', cpannel_create_database_put_data_response.message, 'error');
                                return Promise.reject(new Error("Create Database failed."));
                            }
                        }).then(create_user_response => {
                            if (create_user_response.status === 200) {
                                show_toastr('Success', create_user_response.message, 'success');
                                return httpRequests(obj, '/set_privileges_on_database', create_user_response.company);
                            } else {
                                show_toastr('Error', create_user_response.message, 'error');
                                return Promise.reject(new Error("Create user request failed."));
                            }
                        }).then(set_privileges_on_database_response => {
                            if (set_privileges_on_database_response.status === 200) {
                                show_toastr('Success', set_privileges_on_database_response.message, 'success');
                                return httpRequests(obj, '/importSQLFile', set_privileges_on_database_response.company);
                            } else {
                                show_toastr('Error', set_privileges_on_database_response.message, 'error');
                                return Promise.reject(new Error("Set privileges on database request failed."));
                            }
                        }).then(importSQLFile_response => {
                            
                            if (importSQLFile_response.status === 200) {
                                show_toastr('Success', importSQLFile_response.message, 'success');
                                return httpRequests(obj, '/fileop', importSQLFile_response.company);
                            }else{
                                show_toastr('Error', importSQLFile_response.message, 'error');
                                return Promise.reject(new Error("Failed to set default data on database."));
                            }
                        }).then(fileop_response => {
                            
                            if (fileop_response.status === 200) {
                                show_toastr('Success', fileop_response.message, 'success');
                                return httpRequests(obj, '/upload_env', fileop_response.company);
                            }else{
                                show_toastr('Error', fileop_response.message, 'error');
                                return Promise.reject(new Error("Failed to set default data on database."));
                            }
                        }).then(upload_env_response => {
                            console.log(upload_env_response);
                            if (upload_env_response.status === 200) {
                                show_toastr('Success', upload_env_response.message, 'success');
                                $('#show_bar').html(`This account has been created. Login credentials and login url will be sent shortly to the user email. Please note, Many times it will take time to SSL and others configeration to setup. <br> URL: <a href="${upload_env_response.company.url}">${upload_env_response.company.url}</a>`)
                                setTimeout(() => {
                                    console.log("Inside setTimeout. Redirecting...");
                                    // window.location.href = '/login';
                                }, 8000);
                            }else{
                                show_toastr('Error', upload_env_response.message, 'error');
                                return Promise.reject(new Error("Failed to Configure application envirement."));
                            }
                        }).catch(error => {
                            console.error("Error:", error);
                            show_toastr('Error', error.message, 'error');
                            setTimeout(() => {
                                    console.log("Inside setTimeout. Redirecting...");
                                    // window.location.href = '/register';
                                }, 6000);
                        });


                        
                    } else {
                        show_toastr('Error', data?.message, 'error');
                        setTimeout(() => {
                                  
                                    // window.location.href = '/register';
                                }, 6000);
                    }

                    
                },
                error: function(xhr, status, error) {
                    show_toastr('Error', 'Something went wrong try again later or contact out support team.', 'error');
                    console.log(error, xhr, status)

                }
            });
        }, 2500);

    });





</script>
@endpush
@endsection