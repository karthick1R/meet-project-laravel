<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Cancelled</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9;">
        <div style="background-color: #dc3545; color: white; padding: 20px; text-align: center;">
            <h1 style="margin: 0;">Meeting Room Booking Cancelled</h1>
        </div>
        
        <div style="background-color: white; padding: 20px; margin-top: 20px;">
            <p>Hello,</p>
            
            <p>This is to notify you that the following meeting room booking has been cancelled:</p>
            
            <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #dc3545; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #dc3545;">{{ $booking->title }}</h3>
                <p style="margin: 5px 0;"><strong>Room:</strong> {{ $booking->room->name }}</p>
                <p style="margin: 5px 0;"><strong>Date:</strong> {{ $booking->date->format('F d, Y') }}</p>
                <p style="margin: 5px 0;"><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</p>
            </div>
            
            <p>If you have any questions, please contact the administrator.</p>
            
            <p>Best regards,<br>
            Meeting Room Booking System</p>
        </div>
        
        <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
            <p>This is an automated email. Please do not reply.</p>
        </div>
    </div>
</body>
</html>


