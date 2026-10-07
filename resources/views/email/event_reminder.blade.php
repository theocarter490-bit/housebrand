<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>{{shopSetting()->shop_name}}</title>
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
                                    <img alt="Logo" src="{{ @getFilePath(shopSetting()->logo) }}"
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
                               style="width: 100%;">
                            <tr>
                                <td style="padding: 40px; text-align: center;">
                                    <h1 style="color: #2D3E50; font-size: 40px; font-family: 'PP Eiko', Georgia, serif; line-height: 150%;">
                                        Congratulations!</h1>
                                    <h2 style="color: #2D3E50; font-family: 'PP Eiko', Georgia, serif; font-size: 32px; font-style: normal; font-weight: 900; line-height: 150%;">
                                        {{shopSetting()->site_name}}</h2>
                                    <h4>Hi {{ $notifiable->name }},</h4>
                                    <p>You have an upcoming event on
                                        <strong>{{ dateFormatwithTime($event->start_date) }}</strong>.</p>
                                    <p><strong>Event Details:</strong></p>
                                    <p><strong>Event Name:</strong> {{ $event->name }}</p>
                                    <p><strong>Event Type:</strong> {{ $event->eventType->name }}</p>
                                    <p><strong>Start Date:</strong> {{ dateFormatwithTime($event->start_date) }}</p>
                                    <p><strong>End Date:</strong> {{ dateFormatwithTime($event->end_date) }}</p>
                                    @if($event->location)
                                        <p><strong>Location:</strong> {{ $event->location }}</p>
                                    @endif
                                    @if($event->description)
                                        <p><strong>Description:</strong> {{ $event->description }}</p>
                                    @endif
                                    @if(!empty($event->file))
                                        <p><strong>Event Attachment:</strong></p>
                                        <p>
                                            <a href="{{ getFilePath($event->file) }}"
                                               download="{{ getFilePath($event->file) }}" class="button">Download
                                                Attachment</a>
                                        </p>
                                    @endif
                                    @if(shopSetting()->seller->role_id == \App\Models\Role::SUPER_ADMIN)
                                        <a href="{{env('APP_FRONTEND_URL')}}" target="_blank"
                                           style="display: inline-block; background-color: #D68B22; color: #FFF; margin-top: 32px; font-size: 16px; font-style: normal; line-height: 150%; padding: 12px 48px; text-decoration: none; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">EXPLORE
                                            HOUSEBRANDS</a>
                                    @elseif(shopSetting()->seller->role_id == \App\Models\Role::DESIGNER)
                                        <a href="{{env('APP_FRONTEND_URL').'/designer/'.shopSetting()->slug }}"
                                           target="_blank"
                                           style="display: inline-block; background-color: #D68B22; color: #FFF; margin-top: 32px; font-size: 16px; font-style: normal; line-height: 150%; padding: 12px 48px; text-decoration: none; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">EXPLORE
                                            {{ shopSetting()->shop_name }}</a>
                                    @endif
                                    <p style="color: #2D3E50; font-size: 16px; font-style: normal; font-weight: 500; line-height: 150%; margin-top: 32px;">
                                        Don't share your account credentials with anyone.</p>
                                    <p style="color: #91949B;

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
                                @if (shopSetting()->facebook_url != null)
                                    <td style="padding: 0 10px;">
                                        <a href="{{shopSetting()->facebook_url}}">
                                            <img alt="Facebook" src="{{asset('assets/img/icons/email/facebook.png')}}"
                                                 style="width: 44px; height: 44px; display: block;">
                                        </a>
                                    </td>
                                @endif

                                @if (shopSetting()->youtube_url != null)
                                    <td style="padding: 0 10px;">
                                        <a href="{{shopSetting()->youtube_url}}">
                                            <img alt="YouTube" src="{{asset('assets/img/icons/email/youtube.png')}}"
                                                 style="width: 44px; height: 44px; display: block;">
                                        </a>
                                    </td>
                                @endif

                                @if (shopSetting()->twitter_url != null)
                                    <td style="padding: 0 10px;">
                                        <a href="{{shopSetting()->twitter_url}}">
                                            <img alt="Twitter" src="{{asset('assets/img/icons/email/twitter.png')}}"
                                                 style="width: 44px; height: 44px; display: block;">
                                        </a>
                                    </td>
                                @endif

                                @if (shopSetting()->instagram_url != null)
                                    <td style="padding: 0 10px;">
                                        <a href="{{shopSetting()->instagram_url}}">
                                            <img alt="Instagram" src="{{asset('assets/img/icons/email/instgram.png')}}"
                                                 style="width: 44px; height: 44px; display: block;">
                                        </a>
                                    </td>
                                @endif

                                @if (shopSetting()->linkedin != null)
                                    <td style="padding: 0 10px;">
                                        <a href="{{shopSetting()->linkedin}}">
                                            <img alt="Linkedin" src="{{asset('assets/img/icons/email/linkedin.png')}}"
                                                 style="width: 44px; height: 44px; display: block;">
                                        </a>
                                    </td>
                                @endif

                                @if (shopSetting()->tiktok_url != null)
                                    <td style="padding: 0 10px;">
                                        <a href="{{shopSetting()->tiktok_url}}">
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
                                        <a href="mailto:{{shopSetting()->email}}"
                                           style="color: #D68B22; text-decoration: none;">{{shopSetting()->email}}</a>
                                    </p>
                                    <p style="margin: 0 0 20px 0; color: #2D3E50; font-size: 16px; line-height: 24px;">
                                        {{shopSetting()->location}}
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


