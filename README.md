# My Laravel Blog AWS Journey

Hey there! 👋 I wanted to learn Terraform and how to create infrastructure with Terraform in AWS, then deploy a Laravel app with different services like Redis, RDS, and Nginx. This project is the result of that learning adventure!

I developed this simple blog system following clean code architecture principles. To improve the search functionality, I integrated Elasticsearch, and I'm using Redis for queuing background jobs.

This is my Terraform module to create AWS infrastructure from scratch - it's been quite a journey! 🚀

## 🎯 What I Built

I created a production-ready Laravel blog application that runs on AWS with:

- **Laravel 10** - My choice for the backend framework
- **AWS ECS Fargate** - Because I wanted to learn containerized deployments
- **RDS MySQL** - Managed database (no more server maintenance headaches!)
- **ElastiCache Redis** - For caching and my background job queues
- **Elasticsearch** - This was tricky but so worth it for advanced search
- **S3** - File storage made simple
- **Application Load Balancer** - High availability was important to me
- **ECR** - Container registry for my Docker images
- **CloudWatch** - Monitoring everything (learned this the hard way!)

## ✨ Features I'm Proud Of

- **Clean Architecture** - I spent time learning domain-driven design patterns
- **Elasticsearch Integration** - Took me a while to get this right, but the search is amazing now
- **Background Jobs** - Queue processing with Redis (no more slow page loads!)
- **Self-Healing Database** - The app automatically runs migrations if tables are missing
- **CI/CD Pipeline** - GitHub Actions deployment (this was a game-changer for me)
- **Infrastructure as Code** - Everything in Terraform (scary at first, but so powerful)
- **Container Security** - Multi-stage Docker builds with security best practices
- **Auto-scaling** - ECS Fargate scales based on demand

## 🛠 What You'll Need

Before you start this journey, make sure you have:

- **AWS CLI** (v2.0 or later) - You'll need AWS credentials
- **Terraform** (v1.0 or later) - The star of the show!
- **Docker** (v20.0 or later) - For containerization
- **Git** - Version control is essential

## 🚀 Let's Get Started!

### 1. Clone My Project

```bash
git clone git@github.com:Nadeera3784/laravel-10-blog-aws-deployment.git
cd laravel-10-blog-aws-deployment
```

### 2. Set Up Your AWS Credentials

```bash
aws configure
# You'll need your AWS Access Key ID, Secret Access Key, and choose us-east-1 as region
```

### 3. Configure Terraform Variables

```bash
cd terraform
cp terraform.tfvars.example terraform.tfvars
```

Now edit `terraform.tfvars` with your values. Here's what I used:

```hcl
# AWS Configuration
aws_region = "us-east-1"

# Project Configuration  
project_name = "laravel-blog"

# Application Configuration
app_env = "production"
app_debug = "false"
app_key = "base64:YOUR_GENERATED_APP_KEY_HERE"  # Generate this with: php artisan key:generate --show

# Database Configuration
db_name = "laravel_blog"
db_username = "laraveluser"
db_password = "YourSecurePassword123!"  # Make this strong!

# Elasticsearch Configuration
opensearch_master_user = "admin"
opensearch_master_password = "YourSecureElasticsearchPassword123!"

# ECS Configuration
app_cpu = "512"
app_memory = "1024"
app_count = 1
```

### 4. Deploy the Infrastructure

This is where the magic happens:

```bash
# Initialize Terraform (first time only)
terraform init

# See what Terraform will create
terraform plan

# Deploy everything to AWS
terraform apply
```

### 5. Build and Deploy Your Application

```bash
# Build the Docker image (make sure it's for the right architecture!)
docker build --platform linux/amd64 -f docker/php/Dockerfile.prod -t laravel-blog-app:latest .

# Push to AWS ECR
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin $(terraform output -raw ecr_repository_url)

docker tag laravel-blog-app:latest $(terraform output -raw ecr_repository_url):latest
docker push $(terraform output -raw ecr_repository_url):latest

# Update the ECS service
aws ecs update-service --cluster laravel-blog-cluster --service laravel-blog-service --force-new-deployment --region us-east-1
```

## 🔧 Running Database Migrations

Here's something I learned the hard way - you need to run migrations separately. Create a new ECS task with the same task definition and override the command in Container overrides:

```bash
php artisan migrate 
```

Launch the task, wait for it to complete, then stop it. Your database will be ready!

## To generate local task_definition

```bash
terraform apply -auto-approve -target=local_file.task_definition
```


Here's something I learned the hard way - you need to run migrations separately. Create a new ECS task with the same task definition and override the command in Container overrides:

```bash
php artisan migrate 
```

## 🤖 Setting Up CI/CD (The Cool Part!)

I set up GitHub Actions to automatically deploy when I push code. Here's what you need to do:

### Add These Secrets to Your GitHub Repository

Go to **Settings** → **Secrets and variables** → **Actions** → **New repository secret**

| Secret Name | What to Put | Why You Need It |
|------------|-------------|-----------------|
| `AWS_ACCESS_KEY_ID` | Your AWS Access Key | For GitHub to access AWS |
| `AWS_SECRET_ACCESS_KEY` | Your AWS Secret Key | Authentication |
| `AWS_REGION` | `us-east-1` | Which AWS region |
| `ECR_REPOSITORY` | `laravel-blog-app` | Your container repository |
| `ECS_SERVICE` | `laravel-blog-service` | Your ECS service name |
| `ECS_CLUSTER` | `laravel-blog-cluster` | Your ECS cluster name |
| `CONTAINER_NAME` | `laravel-app` | Container name in task definition |

### What Happens Automatically

Once you set this up, every time you push to the main branch:
- ✅ Tests run automatically
- ✅ Docker image builds
- ✅ Pushes to ECR
- ✅ Updates ECS service
- ✅ Zero downtime deployment!

## 🏗️ What Gets Created in AWS

When you run Terraform, here's what you'll get:

**Core Infrastructure:**
- VPC with public/private subnets (security first!)
- ECS Fargate cluster (no servers to manage)
- RDS MySQL database with automated backups
- ElastiCache Redis for caching and queues
- Elasticsearch domain for search functionality
- Application Load Balancer with health checks
- S3 bucket for file storage
- ECR repository for your Docker images

**Security & Monitoring:**
- CloudWatch log groups (you'll thank me later)
- IAM roles with minimal permissions
- Security groups that actually secure things
- Encryption everywhere (RDS, Redis, Elasticsearch)

## 🎨 What the Blog Can Do

**For Users:**
- Read blog posts with beautiful, responsive design
- Search through posts (thanks to Elasticsearch!)
- Browse by categories
- Fast loading (Redis caching works wonders)

**For Admins:**
- Create, edit, and delete posts
- Manage categories
- User authentication system
- Admin dashboard

**Under the Hood:**
- Background job processing
- Automatic database migrations
- Comprehensive error logging
- Performance optimizations

## 🐳 Docker Development

Want to develop locally? I've got you covered:

```bash
# Start everything with Docker Compose
docker-compose up -d

# Run migrations in the container
docker-compose exec app php artisan migrate
```

## 🔍 My Learning Journey

### Challenges I Faced

1. **Docker Architecture Issues** - Learned the hard way about ARM64 vs x86_64 compatibility
2. **Database Connectivity** - Security groups were confusing at first
3. **Elasticsearch Setup** - The configuration took several attempts
4. **ECS Task Definitions** - Understanding CPU/memory allocation was tricky


## 🚨 When Things Go Wrong

**ECS Task Failing?**
```bash
aws logs get-log-events --log-group-name "/ecs/laravel-blog" --log-stream-name <stream-name>
```

**Database Issues?**
- Check security group rules
- Verify RDS endpoint in environment variables
- Make sure your password is correct!

**Docker Build Problems?**
- Always use `--platform linux/amd64` for AWS
- Check if Docker daemon is running

## 🎯 What's Next?

Some ideas for improvements:
1. Add CloudFront for faster static assets
2. Implement auto-scaling based on traffic
3. Set up proper SSL certificates
4. Add more comprehensive monitoring
5. Create staging environment

## 💝 Why I'm Sharing This

I spent weeks figuring this out, reading documentation, debugging issues, and learning from mistakes. If this helps someone else on their AWS/Terraform journey, it was all worth it!
