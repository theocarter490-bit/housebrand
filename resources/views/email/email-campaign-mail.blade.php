<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>{{$shopSetting['shop_name']}}</title>
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
            font-family: -apple-system, 'Poppins', sans-serif;;
            background-color: gray;
        }

        h1, h2, h3, h4, h5, h6, p {
            margin: 0;
        }
    </style>
</head>
<body>
<table bgcolor="gray" border="0" cellpadding="0" cellspacing="0" width="100%" style="overflow: auto">
    <tr>
        <td align="center">
            <table bgcolor="#1C395F" border="0" cellpadding="0" cellspacing="0" width="600">
                <tr>
                    <td style="padding: 24px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td align="center">
                                    <img alt="logo" src="{{ @getFilePath($shopSetting['logo']) }}"
                                         style="width: 120px; height: 48px; display: block;">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr style="   height: auto;
                      overflow: hidden">
                    <td style="background: #435B7A;  ">
                        <table
                            border="0"
                            cellpadding="0"
                            cellspacing="0"
                            width="100%"
                            style="margin-top: -150px; ">
                            <tr>
                                <td style="padding: 0 24px 24px 24px; text-align: start; ">
                                    <table>
                                        <tr>
                                            <td style="color: #f4e9e9; padding-top: 24px; font-family: Poppins, sans-serif; font-size: 18px; font-style: normal; font-weight: 600; line-height: 150%; margin-top: 32px; text-align: start">
                                                Dear Sir/Madam,
                                            </td>
                                        </tr>
                                    </table>
                                    <table>
                                        <tr>
                                            <td style=" margin-top: 16px; text-align: start; color: #d5d3d3; font-family: Arial, Helvetica, sans-serif; font-size: 18px; line-height: 1.5;">
                                                {!! $Campmessages !!}
                                            </td>
                                        </tr>
                                    </table>
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td align="center">
                                                @if(shopSetting()->seller->role_id === \App\Models\Role::SUPER_ADMIN)
                                                    <a href="{{env('APP_FRONTEND_URL')}}" target="_blank"
                                                       style="display: inline-block; background-color: #D68B22; color: #FFF; margin-top: 32px; font-size: 16px; font-style: normal; line-height: 150%; padding: 12px 48px; text-decoration: none; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">EXPLORE
                                                        HOUSEBRANDS</a>
                                                @elseif(shopSetting()->seller->role_id === \App\Models\Role::DESIGNER)
                                                    <a href="{{env('APP_FRONTEND_URL').'/designer/'.shopSetting()->slug }}"
                                                       target="_blank"
                                                       style="display: inline-block; background-color: #D68B22; color: #FFF; margin-top: 32px; font-size: 16px; font-style: normal; line-height: 150%; padding: 12px 48px; text-decoration: none; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">EXPLORE
                                                        {{ shopSetting()->shop_name }}</a>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                    <p style="color: #a8b8c6; font-size: 16px; font-style: normal; font-weight: 500; line-height: 150%; margin-top: 16px; text-align: center">
                                        Don't share your account credentials with anyone.</p>
                                    <p style="color: #b4b7bc;

                                                            text-align: center;

                                                            font-size: 16px;
                                                            font-style: normal;
                                                            font-weight: 400;
                                                            line-height: 21px;
                                                            margin-top: 32px;">
                                        If you received this email by mistake, simply delete it. You won't
                                        be subscribed if you don't click the confirmation link above.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td>
                                    <img alt="Cozy living room"
                                         src="{{asset('assets/img/icons/email/banner_image.png')}}"
                                         style="width: 100%; ">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding: 40px; padding-bottom: 0;">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                @if (shopSetting()->facebook_url != null)
                                    <td style="padding: 0 10px;"><a href="{{shopSetting()->facebook_url}}"><img
                                                alt="Facebook" src="{{asset('assets/img/icons/email/facebook.png')}}"
                                                style="width: 44px;"></a></td>
                                @endif

                                @if (shopSetting()->youtube_url != null)
                                    <td style="padding: 0 10px;"><a href="{{shopSetting()->youtube_url}}"><img
                                                alt="YouTube" src="{{asset('assets/img/icons/email/youtube.png')}}"
                                                style="width: 44px;"></a></td>
                                @endif

                                @if (shopSetting()->twitter_url != null)
                                    <td style="padding: 0 10px;"><a href="{{shopSetting()->twitter_url}}"><img
                                                alt="Twitter" src="{{asset('assets/img/icons/email/twitter.png')}}"
                                                style="width: 44px;"></a></td>
                                @endif

                                @if (shopSetting()->instagram_url != null)
                                    <td style="padding: 0 10px;"><a href="{{shopSetting()->instagram_url}}"><img
                                                alt="Instgram" src="{{asset('assets/img/icons/email/instgram.png')}}"
                                                style="width: 44px;"></a></td>
                                @endif

                                @if (shopSetting()->linkedin != null)
                                    <td style="padding: 0 10px;"><a href="{{shopSetting()->linkedin}}"><img
                                                alt="Linkedin" src="{{asset('assets/img/icons/email/linkedin.png')}}"
                                                style="width: 44px;"></a></td>
                                @endif

                                @if (shopSetting()->tiktok_url != null)
                                    <td style="padding: 0 10px;"><a href="{{shopSetting()->tiktok_url}}"><img
                                                alt="Tiktok" src="{{asset('assets/img/icons/email/tiktok.png')}}"
                                                style="width: 44px;"></a></td>
                                @endif
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 32px 40px; color: white; text-align: center;">
                        <hr style="margin-bottom: 32px; background-color: #91949B;">
                        <p style="margin: 0 0 20px 0; color: #FFF;

                            text-align: center;
                            font-family: Poppins;
                            font-size: 16px;
                            font-style: normal;
                            font-weight: 400;
                            line-height: 24px">
                            If you have any questions, feel free message us at<br>
                            <a href="mailto:{{shopSetting()->email}}"
                               style="color: #D68B22; text-decoration: none;">{{shopSetting()->email}}</a>
                        </p>
                        <p style="margin: 0 0 20px 0; color: #ffff;font-size: 16px;">
                            {{ shopSetting()->location }}
                        </p>
                        <p style="margin: 0; font-size: 16px;">
                            <a href="{{env('APP_FRONTEND_URL').'/page/terms-conditions'}}"
                               style="color: white; text-decoration: none; margin: 0 10px;">Terms and Condition</a>
                            <span style="color: #ffff">|</span>
                            <a href="{{env('APP_FRONTEND_URL').'/page/privacy-policy'}}"
                               style="color: white; text-decoration: none; margin: 0 10px;">Privacy Policy</a>
                        </p>
                    </td>
                </tr>
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

