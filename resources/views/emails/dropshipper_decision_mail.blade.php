<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Welcome to Our Platform</title>
    <style>
        /* Inline styles for simplicity, consider using CSS classes for larger templates */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f1f1f1;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 200px;
        }

        .message {
            padding: 20px;
            background-color: #ffffff;
        }

        .message p {
            margin-bottom: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <!-- Optional: Add your logo here -->
            <!-- <img src="logo.png" alt="Company Logo"> -->
        </div>

        <div class="message">

        @if ($mailData['decision'] == 'reject')
        <p>Dear {{ $mailData['full_name'] }},</p>
        <p>Thank you for applying to become a dropshipper with YourMart. After carefully reviewing your application, we regret to inform you that it has not been approved at this time.</p>
        <ul>
            <li><strong>We encourage you to review our requirements and consider reapplying in the future. If you have any questions or need clarification, please feel free to contact us at 0326 9810000.</strong></li>
            <li><strong>We appreciate your interest in partnering with us and wish you success in your future endeavors.</strong></li>
            <li><strong>Best regards,</strong></li>
            <li><strong>YourMart Team</strong></li>
        </ul>
        @else
            <p>Dear {{ $mailData['full_name'] }},</p>
            <p>Congratulations! Your dropshipping application with YourMart has been Approved.</p>
            <ul>
                <li><strong>Please Log in to start managing your orders. For assistance, contact us at 0326 9810000.</strong></li>
                <li><strong>Welcome aboard!</strong></li>
                <li><strong>Best regards,</strong></li>
                <li><strong>YourMart Team</strong></li>
            </ul>
        @endif
        </div>

        <div class="footer">
            <p>Thank you for choosing our platform.</p>
        </div>
    </div>
</body>

</html>
