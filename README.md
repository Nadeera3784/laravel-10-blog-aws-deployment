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
│   ├── Specs/               # Domain tests
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
- MySQL or SQLite database

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

## Usage

### Public Access
- **Home Page**: Browse all published posts
- **Category Filter**: Click category buttons to filter posts
- **Post Details**: Click on any post to read the full content

### Admin Access
1. **Register**: Create an account at `/register`
2. **Login**: Access admin at `/login`
3. **Dashboard**: Overview at `/admin/dashboard`
4. **Manage Posts**: Create, edit, delete posts at `/admin/posts`
5. **Manage Categories**: Organize content at `/admin/categories`

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

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

### Test Structure
- **Domain Tests**: `app/Blog/Specs/` - Tests for use cases and business logic
- **Feature Tests**: `tests/Feature/` - End-to-end HTTP tests
- **Unit Tests**: `tests/Unit/` - Individual component tests

## API Documentation

### Public Endpoints
- `GET /` - Blog home page
- `GET /blog` - Blog posts listing
- `GET /blog/{slug}` - Individual post view
- `GET /?category={id}` - Filter posts by category

### Admin Endpoints
- `GET /admin/dashboard` - Admin dashboard
- `GET|POST /admin/posts` - Posts management
- `GET|POST /admin/categories` - Categories management

## File Upload

The application supports image uploads for blog posts:
- **Supported formats**: JPEG, PNG, JPG, GIF
- **Max size**: 2MB
- **Storage**: `storage/app/public/posts/`
- **URL**: Accessible via `storage/posts/filename.jpg`

## Security Features

- **Authentication**: Laravel's built-in authentication
- **Authorization**: Route protection with middleware
- **CSRF Protection**: All forms include CSRF tokens
- **File Upload Validation**: Secure image upload handling
- **Input Validation**: Comprehensive form validation

## Performance Considerations

- **Eager Loading**: Related models loaded efficiently
- **Pagination**: Large post lists are paginated
- **Image Optimization**: Proper image handling and storage
- **Caching**: Ready for Redis/Memcached implementation

## Deployment

### Production Checklist
1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Configure production database
4. Run `composer install --optimize-autoloader --no-dev`
5. Run `php artisan config:cache`
6. Run `php artisan route:cache`
7. Run `php artisan view:cache`
8. Set up proper file permissions
9. Configure web server (Apache/Nginx)

### AWS Deployment
This project is configured for AWS deployment:
- **EC2**: Application server
- **RDS**: Database
- **S3**: File storage (configure for images)
- **CloudFront**: CDN for static assets

## Contributing

1. Fork the repository
2. Create a feature branch
3. Follow the clean architecture patterns
4. Add appropriate tests
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions, please create an issue in the repository.