# Vue CRM Application

A Customer Relationship Management (CRM) application built with Vue.js 2 and designed to work with a PHP backend. This application includes authentication, dashboard, and a ticketing system.

## Features

- User authentication (login/register)
- Dashboard with key metrics
- Ticket management system
- Client management
- Responsive design

## Project Setup

### Prerequisites

- Node.js (v12 or higher)
- npm (v6 or higher)

### Dependencies

This project uses the following main dependencies:
- Vue.js 2.6
- Vue Router 3.x
- Vuex 3.x
- Axios
- Font Awesome

### Installation

1. Clone the repository:
```
git clone https://github.com/yourusername/test-crm.git
cd test-crm
```

2. Install dependencies:
```
npm install
```

3. Create a `.env` file in the project root (optional):
```
VUE_APP_API_URL=http://localhost:3000
```

### Development

#### Running the mock API server

For development and testing without a backend, use the included JSON Server:

```
npm run mock-api
```

This will start a mock API server at http://localhost:3000 using the data in `db.json`.

#### Running the development server

```
npm run serve
```

This will start the development server, typically at http://localhost:8080.

#### Building for production

```
npm run build
```

This creates a `dist` folder with optimized files that you can deploy to your web server.

#### Linting

```
npm run lint
```

### Connecting to a PHP Backend

To connect this application to a PHP backend:

1. Create API endpoints in your PHP application that match the ones expected by the frontend
2. Update the `.env` file to point to your PHP API:
```
VUE_APP_API_URL=http://your-php-api.com/api
```

## Project Structure

- `src/components/auth/` - Authentication components (Login, Register)
- `src/components/dashboard/` - Dashboard components
- `src/components/tickets/` - Ticket management components
- `src/components/layout/` - Layout components (Header, Sidebar)
- `src/router/` - Vue Router configuration
- `src/store/` - Vuex store
- `src/services/` - API services

## Testing with SonarQube Cloud

This project is configured for testing with SonarQube Cloud:

1. Set up a SonarQube Cloud account
2. Configure your project in SonarQube
3. Add SonarQube analysis to your CI/CD pipeline

## License

[MIT](LICENSE)
