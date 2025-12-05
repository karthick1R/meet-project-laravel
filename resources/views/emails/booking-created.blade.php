<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9;">
        <div style="background-color: #3b82f6; color: white; padding: 20px; text-align: center;">
            <h1 style="margin: 0;">Meeting Room Booking Confirmed</h1>
        </div>
        
        <div style="background-color: white; padding: 20px; margin-top: 20px;">
            <p>Hello,</p>
            
            <p>Your meeting room booking has been confirmed:</p>
            
            <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #3b82f6; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #3b82f6;">{{ $booking->title }}</h3>
                <p style="margin: 5px 0;"><strong>Room:</strong> {{ $booking->room->name }}</p>
                <p style="margin: 5px 0;"><strong>Date:</strong> {{ $booking->date->format('F d, Y') }}</p>
                <p style="margin: 5px 0;"><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</p>
                @if($booking->description)
                    <p style="margin: 5px 0;"><strong>Description:</strong> {{ $booking->description }}</p>
                @endif
                @if($booking->room->location)
                    <p style="margin: 5px 0;"><strong>Location:</strong> {{ $booking->room->location }}</p>
                @endif
            </div>
            
            <p>If you need to cancel or modify this booking, please log in to the system.</p>
            
            <p>Best regards,<br>
            Meeting Room Booking System</p>
        </div>
        
        <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
            <p>This is an automated email. Please do not reply.</p>
        </div>
    </div>
</body>
</html>


