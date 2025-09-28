# CSV Contact Importer

System for importing and managing contacts from CSV files.

## Features

- CSV file upload with drag & drop
- Automatic CSV column detection
- Asynchronous processing with Redis queues
- Real-time progress tracking
- Email and data validation
- Duplicate prevention by email
- Paginated contact listing
- Gravatar profile pictures
- Responsive interface

## Tech Stack

- Laravel 11 (API)
- Vue.js 3 (Frontend)
- MySQL (Database)
- Redis (Queues)
- Tailwind CSS (Styling)
- Docker via Laravel Sail

## Setup

### Requirements
- Docker and Docker Compose

### Installation
```bash
# Copy environment file
cp .env.example .env

# Install PHP dependencies
composer install

# Generate application key
php artisan key:generate

# Start containers
./vendor/bin/sail up -d

# Install Node dependencies
./vendor/bin/sail npm install

# Run migrations
./vendor/bin/sail artisan migrate

# Build assets
./vendor/bin/sail npm run build

# Start queue worker
./vendor/bin/sail artisan queue:work

# Access application
# http://localhost
```

## CSV Format

### Required columns
- `name` (or full_name, contact_name)
- `email` (or email_address, e-mail)

### Optional columns
- `phone` (or phone_number, telephone, mobile)
- `birthdate` (or birth_date, date_of_birth, dob)

### Supported separators
- Comma (`,`)
- Semicolon (`;`)
- Tab (`\t`)

### Date formats
- YYYY-MM-DD
- DD/MM/YYYY
- MM/DD/YYYY
- DD-MM-YYYY

### Example
```csv
name,email,phone,birthdate
John Doe,john@example.com,1234567890,1990-05-15
Jane Smith,jane@example.com,0987654321,15/03/1985
```

## API Endpoints

```bash
# Upload
POST /api/imports/upload

# Processing status
GET /api/imports/{id}/status

# Final summary
GET /api/imports/{id}/summary

# List contacts
GET /api/contacts?page=1&per_page=15
```

## Project Structure

```
app/
├── Http/Controllers/Api/     # API Controllers
├── Http/Requests/           # Validation
├── Http/Middleware/         # CORS
├── Jobs/                   # Processing job
└── Models/                 # Contact, ImportSummary

resources/
├── js/components/          # Vue components
├── js/services/           # API client
└── views/                 # Base template

database/migrations/        # Database migrations
```

## Useful Commands

```bash
# Development mode
./vendor/bin/sail npm run dev

# Clear cache
./vendor/bin/sail artisan cache:clear

# View failed jobs
./vendor/bin/sail artisan queue:failed

# Flush queues
./vendor/bin/sail artisan queue:flush
```