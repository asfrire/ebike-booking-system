# 📝 E-Bike Booking System - Logging Guide

## **Log File Location**
```
C:\Users\Garry\OneDrive\Desktop\ebike-booking\storage\logs\laravel.log
```

## **Real-time Log Monitoring**

### **PowerShell (Recommended)**
```powershell
# Open new PowerShell window and run:
Get-Content "C:\Users\Garry\OneDrive\Desktop\ebike-booking\storage\logs\laravel.log" -Wait -Tail 20
```

### **Command Prompt**
```cmd
# Open new Command Prompt and run:
powershell "Get-Content 'C:\Users\Garry\OneDrive\Desktop\ebike-booking\storage\logs\laravel.log' -Wait -Tail 20"
```

## **Current Logging Configuration**

Your `.env` is already configured for maximum logging:
```env
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=debug
APP_DEBUG=true
```

## **Test API Calls to Generate Logs**

### **1. Test Health Endpoint**
```bash
curl http://localhost:8001/api/health
```

### **2. Test Registration (Will Generate Validation Logs)**
```bash
curl -X POST http://localhost:8001/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"","email":"","password":"","role":""}'
```

### **3. Test Login (Will Generate Auth Logs)**
```bash
curl -X POST http://localhost:8001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"wrong@email.com","password":"wrong"}'
```

## **Log Levels Explained**

- **DEBUG**: Detailed debugging information
- **INFO**: General information (API requests/responses)
- **WARNING**: Warning messages
- **ERROR**: Error messages and exceptions
- **CRITICAL**: Critical errors

## **Common Log Patterns to Watch**

### **API Requests**
```
[2024-01-01 12:00:00] local.INFO: API Request {
  "method": "POST",
  "url": "http://localhost:8001/api/auth/login",
  "ip": "127.0.0.1",
  "user_id": null,
  "request_data": {...}
}
```

### **Validation Errors**
```
[2024-01-01 12:00:00] local.WARNING: Validation failed for {
  "email": ["The email field is required."],
  "password": ["The password field is required."]
}
```

### **Database Errors**
```
[2024-01-01 12:00:00] local.ERROR: Database connection failed {
  "exception": "Illuminate\\Database\\QueryException",
  "message": "could not find driver"
}
```

## **Clear Logs**
```bash
# Clear current log file
echo "" > storage/logs/laravel.log

# Or delete and recreate
Remove-Item storage/logs/laravel.log -Force
```

## **Search Logs**

### **Search for Errors**
```powershell
Select-String -Path storage/logs/laravel.log -Pattern "ERROR|Exception|Fatal"
```

### **Search for Specific User**
```powershell
Select-String -Path storage/logs/laravel.log -Pattern "user_id\":5"
```

### **Search for API Endpoints**
```powershell
Select-String -Path storage/logs/laravel.log -Pattern "/api/auth/login"
```

## **Enable Detailed SQL Logging (Optional)**

Add to any controller method:
```php
\DB::enableQueryLog();
// Your code here
\Log::info('SQL Queries', \DB::getQueryLog());
```

## **Production Logging**

For production, change in `.env`:
```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=warning
```

## **Log Rotation**

Laravel automatically rotates logs when they exceed 10MB. To configure:

In `config/logging.php`:
```php
'daily' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'level' => 'debug',
    'days' => 14,
],
```

Your logging system is now fully configured and ready to monitor all API activity! 🚀
