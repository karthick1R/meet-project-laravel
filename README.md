# Meeting Room Booking System

A comprehensive meeting room booking web application built with Laravel 10+, MySQL, jQuery, HTML, and CSS (Bootstrap 5). Similar to Zoho Meeting Room, this application provides a complete solution for managing meeting room reservations with features like calendar views, availability checks, email notifications, and admin management.

## Features

- **Authentication System**: Register, login, and logout with role-based access (admin/user)
- **Room Management**: Create, edit, and delete meeting rooms with amenities and capacity tracking
- **Booking System**: 
  - Create bookings with date, time, title, description, and attendees
  - Prevent double-booking using database transactions and availability checks
  - Support for recurring bookings (daily, weekly, monthly)
  - Admin can block time slots for maintenance
  - Users can view and cancel their bookings
- **Calendar View**: FullCalendar integration showing room bookings color-coded by status
- **AJAX Endpoints**: 
  - Fetch bookings for calendar periods (JSON)
  - Check availability for proposed time slots
  - Create bookings with server validation
  - Cancel bookings
- **Email Notifications**: Send emails to attendees when bookings are created or cancelled (Mailtrap for dev)
- **Admin Features**: 
  - Manage rooms and amenities
  - View all bookings with filters (room/date/user)
  - Block time slots for maintenance
- **Responsive UI**: Bootstrap 5 layout with SweetAlert2 for notifications

## Requirements

- PHP >= 8.1
- MySQL >= 5.7
- Composer
- Node.js & NPM (for asset compilation, optional)

## Installation

1. **Clone the repository** (or navigate to the project directory)

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Configure environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Update `.env` file** with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=meeting_room
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Configure Mail (for Mailtrap in development)**:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_mailtrap_username
   MAIL_PASSWORD=your_mailtrap_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@meetingroom.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

6. **Run migrations**:
   ```bash
   php artisan migrate
   ```

7. **Seed the database** with sample data:
   ```bash
   php artisan db:seed
   ```

8. **Start the development server**:
   ```bash
   php artisan serve
   ```

9. **Access the application**:
   - Open your browser and navigate to `http://localhost:8000`
   - Login with:
     - **Admin**: `admin@meetingroom.com` / `password`
     - **User**: `john@example.com` / `password`

## Sample Seed Data

After running `php artisan db:seed`, you'll have:

- **Users**:
  - Admin: `admin@meetingroom.com` (password: `password`)
  - Regular users: `john@example.com`, `jane@example.com`, `bob@example.com` (all password: `password`)

- **Rooms**: 6 sample rooms including Conference Rooms, Boardroom, Workshop Room, etc.

## Key Code Snippets

### Migration for Bookings

The bookings table includes indexes for performance and conflict detection:

```php
Schema::create('bookings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('room_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('title');
    $table->text('description')->nullable();
    $table->date('date');
    $table->time('start_time');
    $table->time('end_time');
    $table->enum('recurrence', ['none', 'daily', 'weekly', 'monthly'])->default('none');
    $table->date('recurrence_end_date')->nullable();
    $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('confirmed');
    $table->timestamps();
    
    // Indexes for performance
    $table->index(['room_id', 'date', 'start_time', 'end_time']);
    $table->index(['user_id', 'date']);
});
```

### Booking Controller Logic (Transaction & Conflict Check)

The booking creation uses database transactions to prevent race conditions:

```php
return DB::transaction(function () use ($request, $room) {
    // Double-check availability within transaction
    $available = $room->isAvailable(
        $request->date,
        $request->start_time,
        $request->end_time
    );

    if (!$available) {
        return response()->json([
            'success' => false,
            'message' => 'This time slot is already booked or blocked.'
        ], 422);
    }

    // Create booking
    $booking = Booking::create([...]);
    
    // Create invitations and send emails
    ...
});
```

### jQuery AJAX for Booking

Client-side booking creation with validation:

```javascript
$('#bookingForm').submit(function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '{{ route("bookings.store") }}',
        method: 'POST',
        data: formData,
        success: function(response) {
            Swal.fire('Success!', response.message, 'success')
                .then(() => location.reload());
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                // Handle validation errors
            } else {
                Swal.fire('Error', xhr.responseJSON?.message, 'error');
            }
        }
    });
});
```

## Running Tests

Run the feature test to verify double-booking prevention:

```bash
php artisan test --filter BookingDoubleBookingTest
```

Or run all tests:

```bash
php artisan test
```

## Queue Configuration (Optional)

For better performance with email notifications, you can set up queues:

1. **Configure queue in `.env`**:
   ```env
   QUEUE_CONNECTION=database
   ```

2. **Create queue table**:
   ```bash
   php artisan queue:table
   php artisan migrate
   ```

3. **Run queue worker**:
   ```bash
   php artisan queue:work
   ```

## Mailtrap Setup

1. Sign up at [Mailtrap.io](https://mailtrap.io)
2. Create an inbox
3. Copy your SMTP credentials
4. Update `.env` with Mailtrap credentials (as shown in Installation step 5)
5. All emails will be captured in your Mailtrap inbox for testing

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginController.php
│   │   │   └── RegisterController.php
│   │   ├── BookingController.php
│   │   ├── RoomController.php
│   │   ├── AdminController.php
│   │   └── DashboardController.php
│   └── Middleware/
│       └── AdminMiddleware.php
├── Mail/
│   ├── BookingCreated.php
│   └── BookingCancelled.php
└── Models/
    ├── User.php
    ├── Room.php
    ├── Booking.php
    ├── Invitation.php
    └── RoomAvailability.php

database/
├── migrations/
│   ├── *_add_role_to_users_table.php
│   ├── *_create_rooms_table.php
│   ├── *_create_bookings_table.php
│   ├── *_create_invitations_table.php
│   └── *_create_room_availabilities_table.php
└── seeders/
    ├── UserSeeder.php
    └── RoomSeeder.php

resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── auth/
│   │   ├── login.blade.php
│   │   └── register.blade.php
│   ├── dashboard/
│   │   ├── user.blade.php
│   │   └── admin.blade.php
│   ├── rooms/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── bookings/
│   │   ├── index.blade.php
│   │   ├── show.blade.php
│   │   └── modal.blade.php
│   ├── admin/
│   │   ├── rooms/
│   │   │   └── index.blade.php
│   │   └── bookings/
│   │       └── index.blade.php
│   └── emails/
│       ├── booking-created.blade.php
│       └── booking-cancelled.blade.php

tests/
└── Feature/
    └── BookingDoubleBookingTest.php
```

## Security Features

- CSRF protection on all forms
- SQL injection prevention (using Eloquent ORM)
- Input validation on all endpoints
- Role-based access control (admin/user)
- Authentication middleware on protected routes
- Password hashing using bcrypt

## Common Commands

```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Seed database
php artisan db:seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Run tests
php artisan test

# Start queue worker
php artisan queue:work

# Create new migration
php artisan make:migration create_table_name

# Create new controller
php artisan make:controller ControllerName
```

## Troubleshooting

1. **Migration errors**: Make sure your database is created and credentials are correct in `.env`
2. **Email not sending**: Check Mailtrap credentials and ensure queue worker is running if using queues
3. **Calendar not loading**: Check browser console for JavaScript errors and ensure FullCalendar CDN is accessible
4. **Permission denied**: Ensure storage and cache directories are writable:
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
"# meet-project-laravel" 
