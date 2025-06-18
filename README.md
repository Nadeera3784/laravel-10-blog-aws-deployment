# Laravel Blog Application

A modern blog application built with Laravel 10 following Clean Architecture principles. Features include user authentication, post management, category organization, and a beautiful responsive UI with Tailwind CSS 3.

## Features

### Public Features
- 📖 Browse published blog posts
- 🏷️ Filter posts by category
- 📱 Responsive design with Tailwind CSS 3
- 🎨 Beautiful, modern UI with proper UX
- 📄 Individual post detail pages

### Admin Features
- 🔐 User authentication (login/register)
- ✍️ Create, edit, and delete blog posts
- 📁 Manage categories
- 🖼️ Image upload for featured images
- 📝 Draft and publish posts
- 📊 Admin dashboard with statistics
- 🔒 Protected admin routes

## Architecture

This project follows **Clean Architecture** principles with domain-driven design:

```
app/
├── Blog/                     # Blog domain module
│   ├── Entities/            # Domain entities (Post, Category)
│   ├── UseCases/            # Business logic (CreatePost, GetPosts, etc.)
│   ├── IO/Http/             # Controllers and HTTP layer
│   ├── Testing/               # Domain tests
│   └── BlogServiceProvider.php
```

### Key Architectural Benefits
- **Domain-focused organization**: Business logic is clearly separated
- **Testable code**: Use cases can be tested independently
- **Clean separation**: IO layer is separate from business logic
- **Maintainable**: Easy to understand and modify

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js and npm (for asset compilation)
- MySQL database

### Setup Steps

1. **Clone the repository**
```bash
git clone <repository-url>
cd laravel-10-blog-aws-deployment
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node.js dependencies**
```bash
npm install
```

4. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure Database**
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_blog
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. **Run Migrations**
```bash
php artisan migrate
```

7. **Seed Database (Optional)**
```bash
php artisan db:seed
```
This creates sample data including:
- Admin user (admin@blog.com / password)
- Sample categories and posts

8. **Storage Link**
```bash
php artisan storage:link
```

9. **Compile Assets**
```bash
npm run dev
# or for production
npm run build
```

10. **Start Development Server**
```bash
php artisan serve
```

Visit `http://localhost:8000` to see your blog!


### Default Admin Account
If you ran the seeder:
- **Email**: admin@blog.com
- **Password**: password

## Testing

The application includes comprehensive tests following the clean architecture:

### Run Tests
```bash
# Run all tests
php artisan test
