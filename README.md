# 🚀 Laravel Blog with Elasticsearch & AWS Deployment

A modern, full-featured blog application built with Laravel 10, featuring powerful Elasticsearch integration and AWS deployment capabilities. This project was developed to enhance my Laravel and AWS skills while building something practical and scalable.

As a developer looking to deepen my understanding of modern web technologies, I wanted to create a project that would challenge me across multiple areas:

- **Laravel Mastery**: Implementing clean architecture patterns, advanced features, and best practices
- **AWS Skills**: Learning cloud deployment, infrastructure as code, and scalable architectures  
- **Search Technology**: Integrating Elasticsearch for powerful, real-time search capabilities
- **Modern DevOps**: Using Docker, CI/CD pipelines, and automated deployments

What started as a learning exercise evolved into a robust, production-ready blog platform with some pretty cool features!

## ✨ Key Features

### 🔍 **Powerful Search with Elasticsearch**
- Lightning-fast full-text search across all blog posts
- Real-time search suggestions and filtering
- Category-based filtering combined with search
- Automatic index synchronization when content changes

### 👨‍💼 **Admin Dashboard**  
- Clean, intuitive admin interface for managing content
- Full CRUD operations for posts and categories
- Rich text editor for post creation and editing
- Image upload and management
- Category management with automatic URL slug generation

### 🔄 **Smart Content Synchronization**
- Automatic Elasticsearch index updates when posts are modified
- Background job processing for performance
- Real-time category updates that sync across all related posts
- Event-driven architecture for maintainable code

### 🏗️ **Clean Architecture**
- Domain-driven design with clear separation of concerns
- Use Cases pattern for business logic
- Repository pattern for data access
- Event/Listener system for loose coupling
- Comprehensive test coverage

### ☁️ **AWS-Ready Deployment**
- Docker containerization for consistent environments
- Production-ready with environment-specific configs

### 🎨 **Modern Frontend**
- Responsive design that works on all devices
- Tailwind CSS for beautiful, consistent styling
- Fast page loads with optimized asset pipeline
- SEO-friendly URLs and meta tags

## 🛠️ Tech Stack

**Backend:**
- Laravel 10 (PHP 8.2+)
- Elasticsearch 8.x for search
- MySQL for primary data storage  
- Redis for caching and queues

**Frontend:**
- Blade templates with Tailwind CSS
- Vite for asset compilation
- Alpine.js for interactive components

**Infrastructure:**
- Docker & Docker Compose for development
- AWS ECS for container orchestration
- AWS RDS for managed database
- AWS ElastiCache for Redis
- AWS Elasticsearch Service
- Nginx as reverse proxy

**DevOps:**
- GitHub Actions for CI/CD
- Automated testing and deployment pipelines


### Prerequisites
- Docker and Docker Compose
- Git

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/laravel-blog-aws.git
   cd laravel-blog-aws
   ```

2. **Start the development environment**
   ```bash
   docker-compose up -d
   ```

3. **Install dependencies and setup**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate --seed
   ```

4. **Create Elasticsearch index**
   ```bash
   docker-compose exec app php artisan elasticsearch:recreate-index
   ```

5. **Visit your blog**
   - Blog: http://localhost
   - Admin: http://localhost/admin (after registering an account)

That's it! You now have a fully functional blog with search capabilities running locally.


Building this project significantly improved my skills in:

**Laravel:**
- Advanced architecture patterns and dependency injection
- Event-driven programming and background job processing
- Testing strategies for complex applications
- Performance optimization and caching strategies

**AWS:**
- Container orchestration with ECS
- Managed services integration (RDS, ElastiCache, Elasticsearch)
- CI/CD pipeline design and implementation

**General:**
- Elasticsearch integration and search optimization
- Docker containerization best practices
- Modern PHP development workflows
- Clean code principles and maintainable architecture
