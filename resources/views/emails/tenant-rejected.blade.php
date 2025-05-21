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
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
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
            <h1>Bakery Registration Rejected</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $tenant->name }},</p>
            
            <p>We regret to inform you that your registration for {{ $tenant->bakery_name }} has been rejected.</p>
            
            @if($reason)
                <p><strong>Reason for rejection:</strong> {{ $reason }}</p>
            @endif
            
            <p>If you believe this decision was made in error or would like to appeal, please contact our support team.</p>
            
            <p>Best regards,<br>
            The {{ config('app.name') }} Management Team</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html> 