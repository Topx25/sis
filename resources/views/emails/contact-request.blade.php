<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Request</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f7f6; padding: 30px 0;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 32px 40px; text-align: left;">
                            <h1 style="color: #ffffff; font-size: 22px; font-weight: 700; margin: 0; letter-spacing: -0.5px;">SiS Automation</h1>
                            <p style="color: #94a3b8; font-size: 14px; margin: 6px 0 0 0;">New Contact Form Submission</p>
                        </td>
                    </tr>

                    <!-- Status Badge Banner -->
                    <tr>
                        <td style="padding: 24px 40px 0 40px;">
                            <div style="background-color: #eff6ff; border-left: 4px solid #2563eb; padding: 14px 18px; border-radius: 4px;">
                                <p style="margin: 0; font-size: 14px; color: #1e40af; font-weight: 500;">
                                    📩 You have received a new inquiry from your website.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Content / Details -->
                    <tr>
                        <td style="padding: 30px 40px;">
                            <h3 style="color: #334155; font-size: 16px; font-weight: 600; margin: 0 0 16px 0; text-transform: uppercase; letter-spacing: 0.5px;">Client Information</h3>
                            
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 14px; font-weight: 600; width: 140px;">Full Name</td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-size: 14px; font-weight: 600;">{{ $contactRequest->name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 14px; font-weight: 600;">Email Address</td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #2563eb; font-size: 14px; font-weight: 500;">
                                        <a href="mailto:{{ $contactRequest->email }}" style="color: #2563eb; text-decoration: none;">{{ $contactRequest->email }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 14px; font-weight: 600;">Phone Number</td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-size: 14px;">{{ $contactRequest->phone ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 14px; font-weight: 600;">Company</td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-size: 14px;">{{ $contactRequest->company_name ?? 'Not provided' }}</td>
                                </tr>
                            </table>

                            <!-- Message Box -->
                            <div style="margin-top: 28px;">
                                <h3 style="color: #334155; font-size: 16px; font-weight: 600; margin: 0 0 12px 0; text-transform: uppercase; letter-spacing: 0.5px;">Message Details</h3>
                                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; color: #334155; font-size: 15px; line-height: 1.7; whitespace: pre-line;">
                                    {{ $contactRequest->message }}
                                </div>
                            </div>

                            <!-- Reply CTA Button -->
                            <div style="margin-top: 32px; text-align: center;">
                                <a href="mailto:{{ $contactRequest->email }}" style="background-color: #2563eb; color: #ffffff; padding: 12px 28px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px; display: inline-block; box-shadow: 0 2px 5px rgba(37, 99, 235, 0.3);">
                                    Reply to {{ $contactRequest->name }}
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 40px; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                Sent automatically by SiS Notification System • {{ now()->format('Y-m-d H:i') }}
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
