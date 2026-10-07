<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>{{administratorSetting()->shop_name}}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'PP Eiko';
            src: url('path-to-your-font/PPEiko-Regular.woff2') format('woff2'),
            url('path-to-your-font/PPEiko-Regular.woff') format('woff');
            font-weight: normal;
            font-style: normal;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, 'Poppins', sans-serif;
            background-color: #808080;
        }

        h1, h2, h3, h4, h5, h6, p {
            margin: 0;
        }

        img {
            border: 0;
            display: block;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
    </style>
</head>
<body style="margin: 0; padding: 0;">
<table bgcolor="#808080" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width: 100%;">
    <tr>
        <td align="center" style="padding: 20px 0;">
            <table bgcolor="#f2f2f2" border="0" cellpadding="0" cellspacing="0" width="600"
                   style="max-width: 600px; width: 100%;">
                <!-- Logo Section -->
                <tr>
                    <td style="padding: 10px 0;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td align="center">
                                    <img alt="Logo" src="{{ @getFilePath(administratorSetting()->logo) }}"
                                         style="width: 120px; height: 48px; display: block;">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Main Content Section -->
                <tr>
                    <td style="background: #f2f2f2; padding: 0 40px 40px 40px;">
                        <table bgcolor="#ffffff"
                               border="0"
                               cellpadding="0"
                               cellspacing="0"
                               width="100%"
                               style="width: 100%;"

                        >
                            <tr>
                                <td style="padding: 40px; text-align: center;">
                                    <p style="color: #435B7A; font-family: Poppins, sans-serif; font-size: 18px; font-style: normal; font-weight: 400; line-height: 150%; margin: 0 0 10px 0;">
                                        Hi {{$user->name}}
                                    </p>

                                    <p style="color: #2D3E50; font-size: 16px; font-style: normal; font-weight: 500; line-height: 150%; margin: 10px 0;">
                                        Thank you for registering with us! To complete your registration, please verify
                                        your email address using the verification code provided below.
                                    </p>

                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td align="center" style="padding: 32px 0;">
                                                <a href=""
                                                   style="letter-spacing: 4px; display: inline-block; background-color: #D68B22; color: #ffffff; font-size: 20px; font-style: normal; line-height: 150%; padding: 12px 48px; text-decoration: none; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; border-radius: 4px;">{{$validation_code}}</a>
                                            </td>
                                        </tr>
                                    </table>

                                    <p style="margin: 0 0 10px 0; color: #D68B22; font-size: 14px;">
                                        Note: Please use this verification code within next 30 minutes.
                                    </p>

                                    <p style="margin: 0 0 10px 0; color: #2D3E50; font-size: 14px;">
                                        If the verification code is invalid, please click "Resend code" to get a new one.
                                    </p>

                                    <p style="margin: 20px 0 0 0; color: #2D3E50; font-size: 14px;">
                                        If you did not create an account, please ignore this email.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Banner Image -->
                <tr>
                    <td>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td>
                                    <img alt="Banner"
                                         src="{{asset('assets/img/icons/email/banner_image.png')}}"
                                         style="width: 100%; height: auto; display: block;">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Social Media Icons -->
                <tr>
                    <td align="center" style="padding: 20px 40px 0 40px;">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                @if (administratorSetting()->facebook_url != null)
                                    <td style="padding: 0 10px;">
                                        <a href="{{administratorSetting()->facebook_url}}">
                                            <img alt="Facebook" src="{{asset('assets/img/icons/email/facebook.png')}}"
                                                 style="width: 44px; height: 44px; display: block;">
                                        </a>
                                    </td>
                                @endif

                                @if (administratorSetting()->youtube_url != null)
                                    <td style="padding: 0 10px;">
                                        <a href="{{administratorSetting()->youtube_url}}">
                                            <img alt="YouTube" src="{{asset('assets/img/icons/email/youtube.png')}}"
                                                 style="width: 44px; height: 44px; display: block;">
                                        </a>
                                    </td>
                                @endif

                                @if (administratorSetting()->twitter_url != null)
                                    <td style="padding: 0 10px;">
                                        <a href="{{administratorSetting()->twitter_url}}">
                                            <img alt="Twitter" src="{{asset('assets/img/icons/email/twitter.png')}}"
                                                 style="width: 44px; height: 44px; display: block;">
                                        </a>
                                    </td>
                                @endif

                                @if (administratorSetting()->instagram_url != null)
                                    <td style="padding: 0 10px;">
                                        <a href="{{administratorSetting()->instagram_url}}">
                                            <img alt="Instagram" src="{{asset('assets/img/icons/email/instgram.png')}}"
                                                 style="width: 44px; height: 44px; display: block;">
                                        </a>
                                    </td>
                                @endif

                                @if (administratorSetting()->linkedin != null)
                                    <td style="padding: 0 10px;">
                                        <a href="{{administratorSetting()->linkedin}}">
                                            <img alt="Linkedin" src="{{asset('assets/img/icons/email/linkedin.png')}}"
                                                 style="width: 44px; height: 44px; display: block;">
                                        </a>
                                    </td>
                                @endif

                                @if (administratorSetting()->tiktok_url != null)
                                    <td style="padding: 0 10px;">
                                        <a href="{{administratorSetting()->tiktok_url}}">
                                            <img alt="Tiktok" src="{{asset('assets/img/icons/email/tiktok.png')}}"
                                                 style="width: 44px; height: 44px; display: block;">
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Footer Section -->
                <tr>
                    <td style="padding: 32px 40px; text-align: center;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td style="border-top: 1px solid #91949B; padding-top: 32px;">
                                    <p style="margin: 0 0 20px 0; color: #2D3E50; text-align: center; font-family: Poppins; font-size: 16px; font-style: normal; font-weight: 400; line-height: 24px;">
                                        If you have any questions, feel free message us at<br>
                                        <a href="mailto:{{administratorSetting()->email}}"
                                           style="color: #D68B22; text-decoration: none;">{{administratorSetting()->email}}</a>.<br>
                                        All rights reserved.
                                    </p>
                                    <p style="margin: 0 0 20px 0; color: #2D3E50; font-size: 16px; line-height: 24px;">
                                        {{administratorSetting()->location}}
                                    </p>
                                    <p style="margin: 0; font-size: 16px; line-height: 24px;">
                                        <a href="{{env('APP_FRONTEND_URL').'/page/terms-conditions'}}"
                                           style="color: #2D3E50; text-decoration: none;">Terms and Condition</a>
                                        <span style="color: #2D3E50; padding: 0 5px;">|</span>
                                        <a href="{{env('APP_FRONTEND_URL').'/page/privacy-policy'}}"
                                           style="color: #2D3E50; text-decoration: none;">Privacy Policy</a>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Copyright Section -->
                <tr>
                    <td style="background-color: #D68B22; color: white; text-align: center; padding: 16px; font-size: 16px;">
                        {!! generalSetting()->copyright_text !!}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
