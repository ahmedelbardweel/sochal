<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0A0A0B; margin: 0; padding: 0; color: #E2E8F0; }
        .container { max-width: 600px; margin: 40px auto; background: #1A1A1C; border-radius: 24px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.05); }
        .header { background: #FF2D55; padding: 40px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; letter-spacing: -1px; text-transform: uppercase; font-style: italic; font-weight: 800; color: white; }
        .content { padding: 40px; text-align: center; }
        .action-btn {
            display: inline-block;
            padding: 16px 32px;
            background: #FF2D55;
            color: #FFFFFF !important;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 30px 0;
            box-shadow: 0 10px 20px rgba(255, 45, 85, 0.3);
        }
        .footer { padding: 30px; text-align: center; font-size: 12px; color: #8A8D91; border-top: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Recovery</h1>
        </div>
        <div class="content">
            <h2 style="color: #FFFFFF;">Account Retrieval</h2>
            <p style="color: #B0B3B8;">A neural reset request was detected. If this was you, use the secure link below to re-establish your credentials.</p>
            
            <a href="{{ url('/password/reset?token=' . $token) }}" class="action-btn">
                Reset Credentials
            </a>
            
            <p style="color: #8A8D91; font-size: 12px;">This link will expire for your safety. If you did not request this, ignore this transmission.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AbsScroll Dynamics. All rights reserved.<br>
            Secure Recovery Protocol v1.4
        </div>
    </div>
</body>
</html>
