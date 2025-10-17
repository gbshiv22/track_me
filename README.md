# Track Me - Location Tracking Web Application

A Laravel-based location tracking application that allows users to track their movements and view routes on maps. Perfect for personal use, tours, or tracking daily activities.

## Features

### User Features
- **Real-time Location Tracking**: Start/stop tracking with a single button
- **Route Visualization**: View tracked routes on interactive maps using Leaflet.js (FREE, no API key required)
- **Route History**: Browse all your past routes with detailed statistics
- **Distance & Duration**: Automatic calculation of distance traveled and time spent
- **Google Maps Integration**: Open routes in Google Maps for navigation
- **Mobile-Friendly**: Works on any device with a web browser

### Admin Features
- **Live Tracking Dashboard**: View all active users' locations on a single map in real-time
- **User Management**: See all users and their tracking activities
- **Statistics Dashboard**: Comprehensive analytics including:
  - Total users and routes
  - Active tracking sessions
  - Most active users
  - Route trends over time
  - Recent activity logs

## Technology Stack

- **Backend**: Laravel 12 (PHP 8.x)
- **Database**: SQLite (file-based, no server required)
- **Frontend**: Blade Templates + Tailwind CSS (via Breeze)
- **Maps**: Leaflet.js + OpenStreetMap (100% FREE)
- **Authentication**: Laravel Breeze

## Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- XAMPP (or any web server with PHP support)

### Step 1: Already Installed!
Your Laravel application is already installed at:
```
/Applications/XAMPP/xamppfiles/htdocs/track_me
```

### Step 2: Database Configuration
The SQLite database is already configured and migrated. The database file is located at:
```
/Applications/XAMPP/xamppfiles/htdocs/track_me/database/database.sqlite
```

### Step 3: Start XAMPP
1. Open XAMPP Control Panel
2. Start Apache
3. Access the application at: `http://localhost/track_me/public`

### Step 4: Create Your First User

#### Option A: Register via Web Interface
1. Go to `http://localhost/track_me/public`
2. Click "Register"
3. Fill in your details (name, email, password)
4. Submit the form

#### Option B: Create Admin User via Tinker
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/track_me
php artisan tinker
```

Then run:
```php
$user = App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@trackme.com',
    'password' => bcrypt('password123'),
    'role' => 'admin'
]);
```

## Configuration

### Tracking Interval
You can adjust how often location updates are sent to the server by editing `.env`:

```env
# Location Tracking Configuration
TRACKING_INTERVAL=10  # Seconds between location updates (default: 10)
```

Change this to:
- `5` for more frequent updates (more battery usage)
- `15` or `30` for less frequent updates (better battery life)

## Usage Guide

### For Regular Users

#### 1. Start Tracking
1. Log in to your account
2. Click on "Track Me" in the navigation
3. Click "Start Tracking" button
4. Allow location permissions when prompted
5. Your location will be recorded automatically every 10 seconds

#### 2. Stop Tracking
1. When you reach your destination, click "Stop Tracking"
2. Your route is automatically saved

#### 3. View Your Routes
1. Click "My Routes" in the navigation
2. Browse your tracking history
3. Click "View Route" to see the path on a map
4. Use "Open in Google Maps" for navigation

### For Admin Users

#### 1. Live Tracking Dashboard
- Access via "Admin" → "Live Tracking"
- See all active users on a single map
- Auto-refreshes every 10 seconds
- View user details and tracking duration

#### 2. View All User Routes
- Access via "Admin" → "All Routes"
- Browse routes from all users
- Filter and search through tracking history

#### 3. Statistics Dashboard
- Access via "Admin" → "Statistics"
- View system-wide analytics
- Track usage patterns
- Identify most active users

## File Structure

```
track_me/
├── app/
│   ├── Http/Controllers/
│   │   ├── TrackingController.php   # Tracking operations
│   │   ├── RouteController.php      # Route viewing
│   │   └── AdminController.php      # Admin features
│   └── Models/
│       ├── User.php                 # User model with roles
│       ├── TrackingSession.php      # Tracking sessions
│       └── LocationPoint.php        # GPS coordinates
├── database/
│   ├── database.sqlite              # SQLite database file
│   └── migrations/                  # Database schema
├── resources/
│   └── views/
│       ├── tracking/                # Tracking pages
│       ├── routes/                  # Route viewing pages
│       └── admin/                   # Admin dashboard
├── config/
│   └── tracking.php                 # Tracking configuration
└── .env                             # Environment variables
```

## API Endpoints

All API endpoints are protected by authentication:

- `POST /tracking/start` - Start a new tracking session
- `POST /tracking/stop` - Stop active tracking session
- `POST /tracking/location` - Store location point
- `GET /tracking/active` - Get active session info
- `GET /admin/active-sessions` - Get all active sessions (admin only)

## Security Features

- User authentication required for all features
- Role-based access control (admin/user)
- CSRF protection on all forms
- SQL injection protection via Eloquent ORM
- Location data private to each user
- Admin-only access to live tracking and statistics

## Troubleshooting

### Location Not Working
- Ensure you've allowed location permissions in your browser
- Use HTTPS in production (geolocation requires secure context)
- Check browser console for any JavaScript errors

### Database Errors
- Ensure `database/database.sqlite` exists
- Run migrations: `php artisan migrate:fresh`
- Check file permissions on the database file

### Apache Not Starting
- Check if port 80 is available
- Ensure no other web server is running
- Check XAMPP error logs

## Browser Compatibility

- ✅ Chrome/Edge (recommended)
- ✅ Firefox
- ✅ Safari (iOS 13.4+)
- ✅ Opera
- ⚠️ Internet Explorer (not supported)

## Mobile Usage

The application works on mobile browsers, but note:
- Tracking may pause if browser is minimized
- Battery usage increases during active tracking
- Use dedicated app (Flutter/React Native) for better reliability

## Performance Tips

1. **Increase tracking interval** for better battery life
2. **Clear old routes** regularly to maintain performance
3. **Use WiFi** when possible for better location accuracy
4. **Keep browser active** during tracking

## Future Enhancements

Planned features:
- [ ] Route sharing between users
- [ ] Geofencing and notifications
- [ ] Export routes as GPX/KML
- [ ] Native mobile apps (Flutter/React Native)
- [ ] Offline mode with sync
- [ ] Route comparison and analytics

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review Laravel documentation
3. Check browser console for errors

## License

This project is open-source software.

## Credits

- **Laravel Framework**: https://laravel.com
- **Leaflet.js**: https://leafletjs.com
- **OpenStreetMap**: https://www.openstreetmap.org
- **Tailwind CSS**: https://tailwindcss.com

---

**Built with ❤️ for personal location tracking needs**
