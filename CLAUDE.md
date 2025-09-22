# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

The "Japan24" project is a web application for authenticating visits to 24 famous Japanese castles. Users can verify their visits through GPS, photo authentication, and stamp collection, earning badges for their achievements.

## Technology Stack

- **Backend**: PHP 8.2 with Laravel 12
- **Database**: SQLite
- **Frontend**: Bootstrap 5 with Blade templates
- **Development Environment**: Docker with nginx and PHP-FPM
- **Language**: Korean UI with English code

## Project Structure

### Core Models
- `Castle`: Represents the 24 Japanese castles with GPS coordinates, descriptions, and visit information
- `User`: Standard Laravel authentication with visit tracking and badge relationships
- `VisitRecord`: Tracks user visits with GPS verification, photos, and approval status
- `Badge` & `UserBadge`: Achievement system based on visit count

### Key Controllers
- `DashboardController`: User dashboard with progress tracking
- `CastleController`: Castle listings and details
- `VisitRecordController`: Visit authentication and record management
- `AuthenticatedSessionController` & `RegisteredUserController`: User authentication

### Database Seeding
- 24 famous Japanese castles with accurate GPS coordinates and descriptions
- 6 achievement badges (초보자, 성 순례 입문, 성 애호가, 성 마스터, 성 박사, 성 컴플리트)

## Development Commands

### Docker Commands
- `docker compose up -d`: Start development environment on http://localhost:8000
- `docker compose build`: Build Docker containers
- `docker compose exec app php artisan [command]`: Run Laravel artisan commands

### Laravel Commands
- `php artisan migrate:fresh --seed`: Reset database with fresh data
- `php artisan config:clear && php artisan cache:clear`: Clear Laravel caches

## Key Features Implemented

### User Authentication
- Registration and login with Laravel's built-in auth
- Simple template system using Bootstrap CDN

### Castle Management
- Complete 24-castle database with Korean and Japanese names
- GPS coordinates for location verification
- Visit hours, entrance fees, and stamp locations

### Visit Authentication System
- GPS-based location verification (200m radius)
- Photo upload requirement (3 castle photos)
- Optional stamp booklet photo verification
- Automatic approval system (can be enhanced with manual review)

### Badge System
- Automatic badge awarding based on verified visit count
- 6 progressive achievement levels
- User dashboard showing badge progress

### Dashboard Features
- Visit progress tracking with percentage completion
- Recent visits display
- Statistics overview (visited/pending/badges)
- Quick action buttons

## Template System

The project uses a simple Blade template system:
- `layouts/simple.blade.php`: Basic Bootstrap layout
- `auth/simple-login.blade.php`: Login form
- Complex templates in `layouts/app.blade.php` and other views (note: may have rendering issues with certain Laravel packages)

## Known Issues

1. **Blade Template Rendering**: Complex templates with syntax highlighting may cause issues due to Phiki package conflicts
2. **Frontend Dependencies**: Currently uses CDN for Bootstrap and icons instead of NPM/Vite build process
3. **Development Environment**: Node.js not included in Docker container - use CDN resources for frontend assets

## Development Notes

- Korean language used for UI text and documentation
- All castle data includes both Korean and Japanese names
- GPS coordinates are real and accurate for authentication
- Database uses SQLite for simplicity but can be changed via Laravel config
- Bootstrap 5 provides responsive design without custom CSS compilation
- Simple authentication flow without email verification

## Future Enhancements

Areas identified for potential development:
- Photo validation using AI/ML for castle recognition
- OCR for stamp booklet verification
- Interactive map with castle locations
- Social features for sharing achievements
- Mobile app development
- Enhanced admin panel for manual visit approval