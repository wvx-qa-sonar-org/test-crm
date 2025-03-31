# Vue CRM Application

A Customer Relationship Management (CRM) application built with Vue.js 2 and PHP/MongoDB backend. This application includes authentication, dashboard, and a ticketing system.

## Features

- User authentication (login/register)
- Dashboard with key metrics
- Ticket management system
- Client management
- Responsive design

## Prerequisites

- Node.js (v12 or higher)
- npm (v6 or higher)
- PHP (v7.4 or higher)
- Composer
- Docker (for MongoDB)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/test-crm.git
cd test-crm
```

2. Install Frontend Dependencies:
```bash
npm install
```

3. Install Backend Dependencies:
```bash
cd backend
composer install
```

4. Create Environment Files:

Frontend `.env`:
```ini
VUE_APP_API_URL=http://localhost:8001/api
```

Backend `backend/.env`:
```ini
MONGODB_URI=mongodb://localhost:27016
JWT_SECRET=your_generated_secret_key_here
JWT_EXPIRATION=3600
```

5. Generate JWT Secret:
```bash
php -r "echo bin2hex(random_bytes(32));"
```
Copy the output to your `backend/.env` JWT_SECRET.

## Running the Application

1. Start MongoDB (in Docker):
```bash
npm run start:db
```

2. Start PHP Backend:
```bash
npm run start:backend
# or
cd backend && php -S localhost:8001
```

3. Start Vue Frontend:
```bash
npm run serve
```

Or start everything at once:
```bash
npm run start:all
```

The application will be available at:
- Frontend: http://localhost:8080
- Backend API: http://localhost:8001
- MongoDB Express UI: http://localhost:8081

## Project Structure

```
test-crm/
├── src/                    # Vue frontend source
│   ├── components/
│   ├── router/
│   ├── store/
│   └── services/
├── backend/               # PHP backend
│   ├── api/              # API endpoints
│   ├── src/              # PHP source files
│   └── vendor/           # Composer dependencies
├── docker-compose.yml    # Docker configuration
└── package.json          # npm configuration
```

## API Endpoints

- `POST /api/login` - User authentication
- `GET /api/clients` - List all clients
- `POST /api/clients` - Create new client
- `GET /api/tickets` - List all tickets
- `POST /api/tickets` - Create new ticket

## Development

### Backend Development
```bash
cd backend
composer install
php -S localhost:8001
```

### Frontend Development
```bash
npm install
npm run serve
```

### Database Management
MongoDB runs in Docker for easy development. Access MongoDB Express UI at http://localhost:8081.

## Testing

### Frontend Tests
```bash
npm run test:unit
```

### Backend Tests
```bash
cd backend
vendor/bin/phpunit
```

## Production Deployment

1. Build the frontend:
```bash
npm run build
```

2. Configure your web server to serve:
   - The `dist/` directory for frontend static files
   - The `backend/` directory for PHP API endpoints

3. Update environment variables for production settings

## License

[MIT](LICENSE)
