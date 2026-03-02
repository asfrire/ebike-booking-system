# E-Bike Booking System

A complete E-Bike Booking System with React Native mobile app and Laravel API backend.

## Tech Stack

- **Frontend**: React Native (Expo + TypeScript)
- **Backend**: Laravel 11 (API only)
- **Database**: PostgreSQL (Supabase Free)
- **Hosting**: Render.com (Free)
- **Push Notifications**: Firebase Cloud Messaging

## Features

- Multi-role system (Admin, Rider, Customer)
- FIFO Rider Queue System
- Multi-passenger booking (2-5 passengers per e-bike)
- 3-minute expiration with auto reassignment
- Late acceptance handling
- Real-time push notifications
- Race condition prevention with DB transactions

## Quick Start

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- Expo CLI
- Git

### Backend Setup

1. **Clone and install dependencies**
```bash
git clone <your-repo>
cd ebike-booking
composer install
```

2. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configure .env**
```env
DB_CONNECTION=pgsql
DB_HOST=your-project.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-supabase-password

FCM_SERVER_KEY=your-fcm-server-key
```

4. **Run migrations**
```bash
php artisan migrate
```

5. **Start local server**
```bash
php artisan serve
```

### Frontend Setup (React Native)

1. **Install Expo CLI**
```bash
npm install -g expo-cli
```

2. **Create React Native app**
```bash
npx create-expo-app ebike-mobile --template typescript
cd ebike-mobile
```

3. **Install dependencies**
```bash
npm install @react-navigation/native @react-navigation/stack
npm install react-native-screens react-native-safe-area-context
npm install @react-native-async-storage/async-storage
npm install @react-native-firebase/app @react-native-firebase/messaging
```

## API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `GET /api/auth/me` - Get current user
- `PUT /api/auth/device-token` - Update FCM token

### Bookings
- `GET /api/bookings` - List bookings
- `POST /api/bookings` - Create booking (Customer only)
- `GET /api/bookings/{id}` - Get booking details
- `POST /api/bookings/{id}/accept` - Accept assignment (Rider only)
- `POST /api/bookings/{id}/reject` - Reject assignment (Rider only)
- `POST /api/bookings/{id}/complete` - Complete booking (Admin only)
- `POST /api/bookings/{id}/cancel` - Cancel booking

### Riders
- `GET /api/riders` - List riders
- `POST /api/riders/go-online` - Go online (Rider only)
- `POST /api/riders/go-offline` - Go offline (Rider only)
- `PUT /api/riders/capacity` - Update capacity (Rider only)
- `GET /api/riders/my-assignments` - Get my assignments (Rider only)
- `GET /api/riders/queue-position` - Get queue position (Rider only)
- `GET /api/riders/stats` - Get rider stats (Rider only)

## Database Schema

### Users Table
- id, name, email, password, role, phone, device_token

### Riders Table
- id, user_id, is_online, queue_position, capacity

### Bookings Table
- id, customer_id, pickup_location, dropoff_location, pax, remaining_pax, status

### Booking_Riders Table
- id, booking_id, rider_id, allocated_seats, status, expires_at

## Deployment Guide

### Supabase Setup (Free)

1. **Create Supabase Project**
   - Go to https://supabase.com
   - Create new project
   - Choose region closest to your users

2. **Get Database Credentials**
   - Settings → Database
   - Copy connection string

3. **Configure Laravel**
   - Update .env with Supabase credentials
   - Run migrations

### Render.com Deployment (Free)

1. **Prepare for Production**
```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. **Create render.yaml**
```yaml
services:
  - type: web
    name: ebike-api
    env: php
    plan: free
    buildCommand: composer install --no-dev --optimize-autoloader && php artisan config:cache && php artisan route:cache
    startCommand: php artisan serve --host=0.0.0.0 --port=$PORT
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false
      - key: DB_CONNECTION
        value: pgsql
      - key: DB_HOST
        sync: false # Set in Render dashboard
      - key: DB_PASSWORD
        sync: false # Set in Render dashboard
      - key: FCM_SERVER_KEY
        sync: false # Set in Render dashboard
```

3. **Deploy to Render**
   - Push code to GitHub
   - Connect GitHub repository to Render
   - Set environment variables in Render dashboard
   - Deploy

### Firebase Cloud Messaging Setup

1. **Create Firebase Project**
   - Go to https://console.firebase.google.com
   - Create new project
   - Add Android app (for React Native)

2. **Get Server Key**
   - Project Settings → Cloud Messaging
   - Copy Server Key

3. **Configure Laravel**
   - Add FCM_SERVER_KEY to .env
   - Add to Render environment variables

### React Native Deployment

1. **Configure Expo**
```json
{
  "expo": {
    "name": "E-Bike Booking",
    "slug": "ebike-booking",
    "version": "1.0.0",
    "orientation": "portrait",
    "icon": "./assets/icon.png",
    "splash": {
      "image": "./assets/splash.png",
      "resizeMode": "contain",
      "backgroundColor": "#ffffff"
    },
    "platforms": ["ios", "android"],
    "extra": {
      "eas": {
        "projectId": "your-project-id"
      }
    }
  }
}
```

2. **Build with EAS**
```bash
npm install -g eas-cli
eas build:configure
eas build --platform android
eas build --platform ios
```

## Business Logic

### Booking Assignment Algorithm
1. Customer creates booking with passenger count
2. System gets available riders (online + FIFO queue)
3. Assigns riders based on capacity
4. Sets 3-minute expiration for each assignment
5. Sends push notifications to assigned riders

### Acceptance Logic
- Uses DB transactions to prevent race conditions
- First valid acceptance wins
- Late acceptance allowed if seats not reassigned
- Auto-reassignment on timeout

### Queue Management
- FIFO ordering by queue_position
- Riders go online/offline affects queue
- Timeout moves rider to end of queue
- Automatic queue reordering

## Testing

### Backend Tests
```bash
php artisan test
```

### API Testing
Use Postman or curl to test endpoints:
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password","role":"customer"}'
```

## Monitoring

### Health Check
- `GET /api/health` - System health status

### Logs
- Check Render logs for deployment issues
- Use Laravel logs for application debugging

## Security Features

- Sanctum token-based authentication
- Role-based access control
- SQL injection prevention with Eloquent
- Race condition prevention with DB locks
- Input validation with Laravel validators

## Performance Optimizations

- Database indexing on frequently queried fields
- Eager loading to prevent N+1 queries
- Response caching where appropriate
- Optimized rider queue queries

## Troubleshooting

### Common Issues
1. **Database Connection**: Verify Supabase credentials
2. **Push Notifications**: Check FCM server key
3. **Timeout Issues**: Verify cron jobs or manual timeout checks
4. **Queue Position**: Check rider online status

### Debug Mode
Set `APP_DEBUG=true` in development for detailed error messages.

## Support

For issues and questions:
1. Check logs in Render dashboard
2. Verify database connections
3. Test API endpoints manually
4. Review Firebase console for FCM issues
