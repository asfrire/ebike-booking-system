# Installation Guide

## Step 1: Backend Setup (Laravel API)

### Prerequisites
- PHP 8.2+ installed
- Composer installed
- Git installed

### Installation Commands

```bash
# 1. Navigate to project directory
cd c:/Users/Garry/OneDrive/Desktop/ebike-booking

# 2. Install Laravel dependencies
composer install

# 3. Create environment file
copy .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure database (see Step 2)

# 6. Run database migrations
php artisan migrate

# 7. Start local development server
php artisan serve
```

## Step 2: Database Setup (Supabase)

### Create Supabase Project
1. Go to https://supabase.com
2. Click "Start your project"
3. Sign up/login with GitHub
4. Click "New Project"
5. Choose organization (or create new one)
6. Enter project details:
   - **Project Name**: ebike-booking
   - **Database Password**: Create strong password
   - **Region**: Choose nearest to your users
7. Click "Create new project"

### Get Database Credentials
1. In Supabase dashboard, go to Settings → Database
2. Copy the **Connection string**
3. Extract these values:
   - Host: `your-project.supabase.co`
   - Port: `5432`
   - Database: `postgres`
   - Username: `postgres`
   - Password: Your created password

### Update .env File
```env
DB_CONNECTION=pgsql
DB_HOST=your-project.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-supabase-password
```

## Step 3: Firebase Setup (Push Notifications)

### Create Firebase Project
1. Go to https://console.firebase.google.com
2. Click "Add project"
3. Enter project name: "E-Bike Booking"
4. Enable Google Analytics (optional)
5. Click "Create project"

### Get Server Key
1. In Firebase console, go to Project Settings
2. Click "Cloud Messaging" tab
3. Copy the **Server key**
4. Add to .env:
```env
FCM_SERVER_KEY=your-fcm-server-key-here
```

## Step 4: Test Local Setup

### Test API
```bash
# Test health endpoint
curl http://localhost:8000/api/health

# Register a test user
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Customer",
    "email": "customer@test.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "customer"
  }'

# Register a test rider
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Rider",
    "email": "rider@test.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "rider"
  }'
```

## Step 5: Frontend Setup (React Native)

### Install Expo CLI
```bash
npm install -g expo-cli
```

### Create React Native Project
```bash
# Navigate to parent directory
cd c:/Users/Garry/OneDrive/Desktop/

# Create Expo project
npx create-expo-app ebike-mobile --template typescript
cd ebike-mobile
```

### Install Required Packages
```bash
npm install @react-navigation/native @react-navigation/stack
npm install react-native-screens react-native-safe-area-context
npm install @react-native-async-storage/async-storage
npm install @react-native-firebase/app @react-native-firebase/messaging
npm install axios
```

### Basic App Structure
Create these folders in your React Native project:
```
ebike-mobile/
├── src/
│   ├── components/
│   ├── screens/
│   ├── services/
│   ├── types/
│   └── utils/
```

## Step 6: Deployment (Render.com)

### Prepare for Production
```bash
# In your Laravel project directory
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
```

### Deploy to Render
1. Push code to GitHub repository
2. Go to https://render.com
3. Click "New" → "Web Service"
4. Connect your GitHub repository
5. Configure:
   - **Name**: ebike-api
   - **Environment**: PHP
   - **Plan**: Free
   - **Build Command**: `composer install --no-dev --optimize-autoloader && php artisan config:cache && php artisan route:cache`
   - **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`

### Set Environment Variables in Render
In Render dashboard → Environment:
- `APP_ENV`: `production`
- `APP_DEBUG`: `false`
- `DB_CONNECTION`: `pgsql`
- `DB_HOST`: Your Supabase host
- `DB_PASSWORD`: Your Supabase password
- `FCM_SERVER_KEY`: Your Firebase server key

## Step 7: Mobile App Deployment

### Configure Expo App
In `app.json`:
```json
{
  "expo": {
    "name": "E-Bike Booking",
    "slug": "ebike-booking",
    "version": "1.0.0",
    "orientation": "portrait",
    "platforms": ["ios", "android"]
  }
}
```

### Build for Production
```bash
# Install EAS CLI
npm install -g eas-cli

# Login to Expo
eas login

# Configure build
eas build:configure

# Build Android
eas build --platform android

# Build iOS (requires Apple Developer account)
eas build --platform ios
```

## Verification Checklist

### Backend
- [ ] Laravel server runs locally
- [ ] Database migrations completed
- [ ] API endpoints respond correctly
- [ ] Authentication works
- [ ] Booking creation works

### Database
- [ ] Supabase project created
- [ ] Tables created via migrations
- [ ] Connection successful

### Frontend
- [ ] Expo project created
- [ ] Dependencies installed
- [ ] Can connect to API

### Deployment
- [ ] Code pushed to GitHub
- [ ] Render service created
- [ ] Environment variables set
- [ ] API accessible via Render URL

## Troubleshooting

### Common Issues
1. **"Class 'App\Helpers\BookingHelper' not found"**
   - Run: `composer dump-autoload`

2. **Database connection failed**
   - Verify Supabase credentials in .env
   - Check Supabase project is active

3. **Migration errors**
   - Ensure database is empty
   - Check table permissions

4. **API returns 500 error**
   - Check Laravel logs: `storage/logs/laravel.log`
   - Verify .env configuration

### Get Help
- Check Render logs for deployment issues
- Review Supabase logs for database issues
- Test API endpoints with Postman
- Check Firebase console for FCM issues
