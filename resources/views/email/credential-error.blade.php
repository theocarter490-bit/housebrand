<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>{{ generalSetting()->site_name }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f4f4;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table {
            border-spacing: 0;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f4f4;
            padding-bottom: 40px;
        }

        .main-table {
            background-color: #1C395F;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-radius: 8px;
            overflow: hidden;
        }

        .content-card {
            background-color: #ffffff;
            border-radius: 12px;
            margin: 0 20px;
            padding: 40px 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #D68B22;
            color: #ffffff !important;
            padding: 14px 30px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
            margin: 20px 0;
        }

        @media screen and (max-width: 600px) {
            .content-card {
                margin: 0 10px !important;
                padding: 30px 20px !important;
            }
        }
    </style>
</head>

<body>
<div class="wrapper">
    <table class="main-table" align="center" border="0" cellpadding="0" cellspacing="0">

        <!-- HEADER / LOGO -->
        <tr>
            <td align="center" style="padding: 20px 0 25px 0;">
                <img
                    src="{{ @getFilePath(generalSetting()->logo) }}"
                    alt="Logo"
                    style="width:110px; max-width:110px; display:block;"
                >
            </td>
        </tr>

        <!-- BODY BACKGROUND -->
        <tr>
            <td style="background-color: #435B7A; padding: 0 0 35px 0;">
                <div class="content-card" style="margin-top:-15px;">

                    <h2 style="color:#d9534f; font-size:22px; margin-bottom:15px; text-align:center;">
                        Urgent: Email Configuration Error
                    </h2>

                    <p style="color:#444; font-size:16px; margin-bottom:20px;">
                        Hello, <strong>{{ $data['name'] }}</strong>
                    </p>

                    <p style="color:#666; font-size:14px; line-height:1.6; margin-bottom:25px;">
                        Our system was unable to send an email on your behalf using the SMTP credentials currently saved
                        in your settings.
                    </p>

                    <!-- ERROR BOX -->
                    <div style="background-color:#FDF2F2; border-left:4px solid #d9534f; padding:15px; margin-bottom:25px;">
                        <strong style="color:#d9534f; font-size:14px; display:block; margin-bottom:8px;">
                            Technical Details:
                        </strong>
                        <p style="margin:0; font-size:13px; color:#555;">
                            <strong>Host:</strong> {{ $data['host'] }}<br>
                            <strong>Port:</strong> {{ $data['port'] }}<br>
                            <strong>Status:</strong> Connection Failed (Auth Error)
                        </p>
                    </div>

                    <!-- WARNING -->
                    <p style="background-color:#FFF9E6; color:#856404; padding:12px; border-radius:4px; font-size:13px; line-height:1.4;">
                        <strong>Warning:</strong> Customers are <strong>not</strong> receiving order confirmations or
                        automated notifications from your store until this is fixed.
                    </p>

                    <!-- CTA -->
                    <div style="text-align:center; padding-top:20px;">
                        <a href="{{ url('/setting/email-setting') }}" class="btn-primary">
                            UPDATE SETTINGS
                        </a>
                    </div>

                    <p style="font-size:12px; color:#999; margin-top:25px; line-height:1.4;">
                        If you recently enabled 2FA, please use an <strong>App Password</strong> instead of your regular
                        account password.
                    </p>

                </div>
            </td>
        </tr>

        <!-- BANNER -->
        <tr>
            <td>
                <img src="{{ asset('assets/img/icons/email/banner_image.png') }}" alt="Banner"
                     style="width:100%; display:block;">
            </td>
        </tr>

        <!-- SOCIAL ICONS -->
        <tr>
            <td align="center" style="padding:35px 20px 20px 20px;">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        @php
                            $socials = [
                                'facebook' => generalSetting()->facebook_url,
                                'youtube' => generalSetting()->youtube_url,
                                'twitter' => generalSetting()->twitter_url,
                                'instgram' => generalSetting()->instagram_url,
                                'linkedin' => generalSetting()->linkedin,
                                'tiktok' => generalSetting()->tiktok_url,
                            ];
                        @endphp

                        @foreach($socials as $key => $url)
                            @if($url)
                                <td style="padding:0 8px;">
                                    <a href="{{ $url }}">
                                        <img src="{{ asset('assets/img/icons/email/'.$key.'.png') }}"
                                             width="28" alt="{{ $key }}">
                                    </a>
                                </td>
                            @endif
                        @endforeach
                    </tr>
                </table>
            </td>
        </tr>

        <!-- FOOTER -->
        <tr>
            <td style="padding:0 40px 40px 40px; text-align:center;">
                <hr style="border:none; border-top:1px solid #335075; margin-bottom:30px;">
                <p style="color:#ffffff; font-size:14px; line-height:1.5; margin-bottom:15px;">
                    Questions? Email us at<br>
                    <a href="mailto:{{ generalSetting()->email }}"
                       style="color:#D68B22; text-decoration:none;">
                        {{ generalSetting()->email }}
                    </a>
                </p>

                <p style="color:#91949B; font-size:13px; line-height:1.5;">
                    {{ generalSetting()->address }}<br>
                    <a href="{{ url('/page/terms-conditions') }}" style="color:#91949B; text-decoration:underline;">
                        Terms
                    </a>
                    |
                    <a href="{{ url('/page/privacy-policy') }}" style="color:#91949B; text-decoration:underline;">
                        Privacy
                    </a>
                </p>
            </td>
        </tr>

        <!-- COPYRIGHT -->
        <tr>
            <td style="background-color:#D68B22; color:#ffffff; text-align:center; padding:15px; font-size:12px;">
                {!! generalSetting()->copyright_text !!}
            </td>
        </tr>

    </table>
</div>
</body>
</html>
