<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #8B4513;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .credentials {
            background-color: #fff;
            padding: 15px;
            border: 1px solid #ddd;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bakery Registration Approved!</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $tenant->name }},</p>
            
            <p>We are pleased to inform you that your registration for {{ $tenant->bakery_name }} has been approved!</p>
            
            <div class="credentials">
                <h3>Your Login Credentials:</h3>
                <p>Your bakery account has been approved. Here are your login credentials:</p>
                <p><strong>Domain:</strong> {{ $tenant->domain_name }}</p>
                <p><strong>Login URL:</strong> <a href="http://{{ $tenant->domain_name }}/tenant/login">http://{{ $tenant->domain_name }}/tenant/login</a></p>
                <p><strong>Email:</strong> {{ $tenant->email }}</p>
                <p><strong>Password:</strong> {{ $password }}</p>
            </div>
            
            <p>Please keep these credentials safe and use them to log in to your bakery management system.</p>
            
            <p>If you have any questions or need assistance, please don't hesitate to contact us.</p>
            
            <p>Best regards,<br>
            The {{ config('app.name') }} Management Team</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html> 