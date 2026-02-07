<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0A0A0B;
            margin: 0;
            padding: 0;
            color: #E2E8F0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #1A1A1C;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #2D3FE6, #FF2D55);
            padding: 40px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: -1px;
            text-transform: uppercase;
            font-style: italic;
            font-weight: 800;
        }
        .content {
            padding: 40px;
            text-align: center;
        }
        .otp-code {
            font-size: 48px;
            font-weight: 800;
            color: #2D3FE6;
            letter-spacing: 8px;
            margin: 30px 0;
            padding: 20px;
            background: #0A0A0B;
            border-radius: 16px;
            border: 1px dashed rgba(255, 255, 255, 0.1);
        }
        .footer {
            padding: 30px;
            text-align: center;
            font-size: 12px;
            color: #8A8D91;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>AbsScroll</h1>
        </div>
        <div class="content">
            <h2 style="color: #FFFFFF;">Establish Neural Connection</h2>
            <p style="color: #B0B3B8;">Use the security protocol code below to verify your access to the nexus.</p>
            
            <div class="otp-code">
                {{ $otp }}
            </div>
            
            <p style="color: #8A8D91; font-size: 14px;">This code will expire in 10 minutes for your security.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AbsScroll Dynamics. All rights reserved.<br>
            Secure Link Transmission System v2.0
        </div>
    </div>
</body>
</html>
