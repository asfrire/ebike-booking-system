POST   http://localhost:8001/api/auth/register
POST   http://localhost:8001/api/auth/login
POST   http://localhost:8001/api/auth/logout
GET    http://localhost:8001/api/auth/me
PUT    http://localhost:8001/api/auth/device-token

GET    http://localhost:8001/api/riders
GET    http://localhost:8001/api/riders/{id}
POST   http://localhost:8001/api/riders/go-online
POST   http://localhost:8001/api/riders/go-offline
PUT    http://localhost:8001/api/riders/capacity
GET    http://localhost:8001/api/riders/my-assignments
GET    http://localhost:8001/api/riders/queue-position
GET    http://localhost:8001/api/riders/stats
PUT    http://localhost:8001/api/riders/{id}

GET    http://localhost:8001/api/bookings
POST   http://localhost:8001/api/bookings
GET    http://localhost:8001/api/bookings/{id}
POST   http://localhost:8001/api/bookings/{id}/accept
POST   http://localhost:8001/api/bookings/{id}/reject
POST   http://localhost:8001/api/bookings/{id}/complete
POST   http://localhost:8001/api/bookings/{id}/cancel

GET    http://localhost:8001/api/health
GET    http://localhost:8001/api/check-timeouts