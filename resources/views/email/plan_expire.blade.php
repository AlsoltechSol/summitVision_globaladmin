<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
@php
    // $logo=asset(Storage::url('uploads/logo/'));
$logo=\App\Models\Utility::get_file('uploads/logo/');

 $company_logo = Utility::getValByName('company_logo');
@endphp
<head>
    <title>
    </title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        #outlook a {
            padding: 0;
        }

        .ReadMsgBody {
            width: 100%;
        }

        .ExternalClass {
            width: 100%;
        }

        .ExternalClass * {
            line-height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        p {
            display: block;
            margin: 13px 0;
        }
    </style>
    <style type="text/css">
        @media only screen and (max-width: 480px) {
            @-ms-viewport {
                width: 320px;
            }

            @viewport {
                width: 320px;
            }
        }
    </style>
    <style type="text/css">
        .outlook-group-fix {
            width: 100% !important;
        }
    </style>
    <link href="https://fonts.googleapis.com/css?family=Open Sans" rel="stylesheet" type="text/css">
    <style type="text/css">
        @media only screen and (min-width: 480px) {
            .mj-column-per-100 {
                width: 100% !important;
                max-width: 100%;
            }
        }
    </style>
    <style type="text/css">
        [owa] .mj-column-per-100 {
            width: 100% !important;
            max-width: 100%;
        }
    </style>
    <style type="text/css">
        @media only screen and (max-width: 480px) {
            table.full-width-mobile {
                width: 100% !important;
            }

            td.full-width-mobile {
                width: auto !important;
            }
        }
    </style>
</head>
<body style="background-color:#f8f8f8;">
<div style="background-color:#f8f8f8;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600">
        <tr>
            <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
                <div style="background:#ffffff;background-color:#ffffff;Margin:0px auto;max-width:600px;">
                    <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
                        <tbody>
                        <tr>
                            <td style="direction:ltr;font-size:0px;padding:20px 0;padding-bottom:0px;padding-left:0px;padding-right:0px;padding-top:0px;text-align:center;vertical-align:top;">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="" style="vertical-align:top;width:600px;">
                                            <div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                                                    <tr>
                                                        <td style="font-size:0px;padding:10px 25px;padding-top:0px;padding-right:0px;padding-bottom:40px;padding-left:0px;word-break:break-word;">
                                                            <p style="border-top:solid 7px #6676EF;font-size:1;margin:0px auto;width:100%;">
                                                            </p>

                                                            <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-top:solid 7px #6676EF;font-size:1;margin:0px auto;width:600px;" role="presentation" width="600px">
                                                                <tr>
                                                                    <td style="height:0;line-height:0;">
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center" style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                                                            <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
                                                                <tbody>
                                                                <tr>
                                                                    <td style="width:110px;">
                                                                        <img alt="" height="auto" src="{{$logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png')}}" style="border:none;display:block;outline:none;text-decoration:none;height:auto;width:100%;" title="" width="110"/>
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </table>
    <table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600">
        <tr>
            <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
                <div style="background:#ffffff;background-color:#ffffff;Margin:0px auto;max-width:600px;">
                    <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
                        <tbody>
                        <tr>
                            <td style="direction:ltr;font-size:0px;padding:20px 0px 20px 0px;padding-bottom:70px;padding-top:30px;text-align:center;vertical-align:top;">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="" style="vertical-align:top;width:600px;">
                                            <div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                                                    <tr>
                                                        <td align="left" style="font-size:0px;padding:0px 25px 0px 25px;padding-top:0px;padding-right:50px;padding-bottom:0px;padding-left:50px;word-break:break-word;">
                                                            <div style="font-family:Open Sans, Helvetica, Arial, sans-serif;font-size:13px;line-height:22px;text-align:left;color:#797e82;">
                                                                <p style=" line-height:32px"><b style="font-weight:700">{{__('Subject : ').$company['plan'] ? 'Your Plan is set to expire soon': 'Your demo period is ending soon'}}</b></p>
                                                                <p style="line-height:32px"><b style="font-weight:700">{{__('Hi ').$company['name'].','}}</b></p>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td align="left" style="font-size:0px;padding:0px 25px 0px 25px;padding-top:0px;padding-right:50px;padding-bottom:0px;padding-left:50px;word-break:break-word;">
                                                            <div style="font-family:Open Sans, Helvetica, Arial, sans-serif;font-size:13px;line-height:22px;text-align:left;color:#797e82;">
                                                                <p style="margin: 10px 0;">{{__($company['plan'] ? 'We hope this email finds you well. We are reaching out to inform you that your subscription with '. env('APP_NAME').' is set to expire soon. As valued members of our community, we want to ensure that you continue to benefit from our services without interruption.':
                                                                    "We hope you've been enjoying your demo account with us at ". env('APP_NAME') .". We're reaching out to inform you that your demo account is set to expire soon. As your trial period comes to an end, we wanted to remind you of the steps you need to take to continue enjoying our services.")}}</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" style="font-size:0px;padding:0px 25px 0px 25px;padding-top:0px;padding-right:50px;padding-bottom:0px;padding-left:50px;word-break:break-word;">
                                                            <div style="font-family:Open Sans, Helvetica, Arial, sans-serif;font-size:13px;line-height:22px;text-align:left;color:#797e82;">
                                                            <p style="margin: 10px 0;">{{__($company['plan'] ? 'Your subscription is expiring on '.$company['plan_expire_date'].', and we wanted to remind you in advance to avoid any disruption in service. By renewing now, you can continue to enjoy uninterrupted access to our premium features and services.':
                                                              "Your demo account is scheduled to expire on ".$company['plan_expire_date'].". After this date, your account will be deleted, and your access to our platform will be restricted.
                                                              To continue using our services and prevent any disruption, we encourage you to purchase a subscription plan before the demo account deletion date. By upgrading to a paid plan, you'll unlock access to a more features and benefits tailored to meet your needs.")}}</p>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td align="left" style="font-size:0px;padding:0px 25px 0px 25px;padding-top:0px;padding-right:50px;padding-bottom:0px;padding-left:50px;word-break:break-word;">
                                                            <div style="font-family:Open Sans, Helvetica, Arial, sans-serif;font-size:13px;line-height:22px;text-align:left;color:#797e82;">
                                                                <p style="margin: 10px 0;"><i style="font-style:normal">{{__('If you encounter any issues or have questions, please don\'t hesitate to contact us.' )}}</i></p>
                                                                <p style="margin: 10px 0;"><i style="font-style:normal">{{__('Thank you for choosing our service.')}}</i></p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" style="font-size:0px;padding:0px 25px 0px 25px;padding-top:0px;padding-right:50px;padding-bottom:0px;padding-left:50px;word-break:break-word;">
                                                            <div style="font-family:Open Sans, Helvetica, Arial, sans-serif;font-size:13px;line-height:22px;text-align:left;color:#797e82;">
                                                                <p style="margin: 10px 0;"><i style="font-style:normal"><b style="font-weight:700">{{__('Regards,')}}</b></i></p>
                                                                <p style="margin: 10px 0;"><i style="font-style:normal"><b style="font-weight:700">{{env('APP_NAME') .' Team'}}</b></i></p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
