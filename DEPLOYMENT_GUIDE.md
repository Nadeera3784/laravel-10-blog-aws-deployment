# Laravel Blog AWS Deployment Guide

This guide will help you deploy your Laravel blog application to AWS using GitHub Actions CI/CD pipeline.

## 🏗️ Architecture Overview

- **AWS ECS Fargate**: Container orchestration for running the Laravel application
- **AWS RDS MySQL**: Managed database service
- **AWS ElastiCache Redis**: Caching and session storage
- **AWS S3**: File storage (already configured)
- **Application Load Balancer**: Traffic distribution and SSL termination
- **AWS ECR**: Container registry for Docker images
- **GitHub Actions**: CI/CD pipeline

## 📋 Prerequisites

1. AWS Account with appropriate permissions
2. GitHub repository with your Laravel application
3. Domain name (optional, but recommended)
4. AWS CLI installed locally

## 🚀 Step-by-Step Deployment

### 1. Set up AWS Infrastructure

Deploy the CloudFormation template to create all necessary AWS resources:

```bash
aws cloudformation create-stack \
  --stack-name laravel-blog-infrastructure \
  --template-body file://aws/cloudformation-template.yml \
  --parameters ParameterKey=DatabasePassword,ParameterValue=YourSecurePassword123! \
               ParameterKey=DomainName,ParameterValue=your-domain.com \
  --capabilities CAPABILITY_IAM
```

Wait for the stack to complete:
```bash
aws cloudformation wait stack-create-complete --stack-name laravel-blog-infrastructure
```

### 2. Create IAM Roles for ECS

Create the ECS Task Execution Role:

```bash
aws iam create-role \
  --role-name ecsTaskExecutionRole \
  --assume-role-policy-document '{
    "Version": "2012-10-17",
    "Statement": [
      {
        "Effect": "Allow",
        "Principal": {
          "Service": "ecs-tasks.amazonaws.com"
        },
        "Action": "sts:AssumeRole"
      }
    ]
  }'

aws iam attach-role-policy \
  --role-name ecsTaskExecutionRole \
  --policy-arn arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy
```

Create the ECS Task Role:

```bash
aws iam create-role \
  --role-name ecsTaskRole \
  --assume-role-policy-document '{
    "Version": "2012-10-17",
    "Statement": [
      {
        "Effect": "Allow",
        "Principal": {
          "Service": "ecs-tasks.amazonaws.com"
        },
        "Action": "sts:AssumeRole"
      }
    ]
  }'

aws iam attach-role-policy \
  --role-name ecsTaskRole \
  --policy-arn arn:aws:iam::aws:policy/AmazonS3FullAccess
```

### 3. Store Secrets in AWS Systems Manager

Store sensitive configuration in Parameter Store:

```bash
# Generate Laravel application key
php artisan key:generate --show

# Store secrets
aws ssm put-parameter \
  --name "/laravel-blog/app-key" \
  --value "base64:YOUR_GENERATED_KEY_HERE" \
  --type "SecureString"

aws ssm put-parameter \
  --name "/laravel-blog/db-username" \
  --value "admin" \
  --type "SecureString"

aws ssm put-parameter \
  --name "/laravel-blog/db-password" \
  --value "YourSecurePassword123!" \
  --type "SecureString"
```

### 4. Update Task Definition

Update `aws/task-definition.json` with your AWS Account ID and actual values:

1. Replace `YOUR_ACCOUNT_ID` with your AWS Account ID
2. Update RDS endpoint from CloudFormation outputs
3. Update Redis endpoint from CloudFormation outputs

### 5. Configure GitHub Secrets

Add these secrets to your GitHub repository (`Settings > Secrets and variables > Actions`):

```
AWS_ACCESS_KEY_ID=your-access-key-id
AWS_SECRET_ACCESS_KEY=your-secret-access-key
AWS_ACCOUNT_ID=123456789012
```

### 6. Create ECS Service

After the first deployment, create the ECS service:

```bash
aws ecs create-service \
  --cluster laravel-blog-cluster \
  --service-name laravel-blog-service \
  --task-definition laravel-blog-task:1 \
  --desired-count 1 \
  --launch-type FARGATE \
  --network-configuration "awsvpcConfiguration={subnets=[subnet-xxx,subnet-yyy],securityGroups=[sg-xxx],assignPublicIp=ENABLED}" \
  --load-balancers targetGroupArn=arn:aws:elasticloadbalancing:us-east-1:123456789012:targetgroup/laravel-blog-targets/xxx,containerName=laravel-app,containerPort=80
```

### 7. Set up Domain and SSL (Optional)

If you have a domain:

1. Create an ACM certificate:
```bash
aws acm request-certificate \
  --domain-name your-domain.com \
  --domain-name www.your-domain.com \
  --validation-method DNS
```

2. Add HTTPS listener to ALB
3. Update Route 53 records to point to ALB

## 🔧 Environment Configuration

### Production Environment Variables

The following environment variables are configured in the ECS task definition:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your-rds-endpoint
DB_PORT=3306
DB_DATABASE=laravel_blog

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=your-elasticache-endpoint
REDIS_PORT=6379

FILESYSTEM_DISK=s3
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=textract-aws-storage
```

## 🚦 Deployment Process

### Automatic Deployment

1. Push code to `main` branch
2. GitHub Actions automatically:
   - Runs tests
   - Builds Docker image
   - Pushes to ECR
   - Updates ECS service

### Manual Deployment

To deploy manually:

```bash
# Build and push image
docker build -f docker/php/Dockerfile.prod -t laravel-blog .
docker tag laravel-blog:latest 123456789012.dkr.ecr.us-east-1.amazonaws.com/laravel-blog:latest
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin 123456789012.dkr.ecr.us-east-1.amazonaws.com
docker push 123456789012.dkr.ecr.us-east-1.amazonaws.com/laravel-blog:latest

# Update ECS service
aws ecs update-service --cluster laravel-blog-cluster --service laravel-blog-service --force-new-deployment
```

## 🗃️ Database Migration

Run migrations on first deployment:

```bash
# Connect to ECS container and run migrations
aws ecs execute-command \
  --cluster laravel-blog-cluster \
  --task task-id \
  --container laravel-app \
  --interactive \
  --command "/bin/sh"

# Inside container
php artisan migrate --force
php artisan db:seed --force
```

## 📊 Monitoring and Logs

### CloudWatch Logs
View application logs:
```bash
aws logs tail /ecs/laravel-blog --follow
```

### Health Checks
- Application Load Balancer performs health checks on `/`
- ECS tasks have health checks configured
- CloudWatch alarms can be set up for monitoring

## 💰 Cost Optimization

### Estimated Monthly Costs (us-east-1):
- **ECS Fargate (0.5 vCPU, 1GB)**: ~$15
- **RDS MySQL (db.t3.micro)**: ~$15
- **ElastiCache Redis (cache.t3.micro)**: ~$15
- **Application Load Balancer**: ~$20
- **Data Transfer**: Variable
- **Total**: ~$65-80/month

### Cost Optimization Tips:
1. Use Spot instances for non-production
2. Schedule stop/start for development environments
3. Use Reserved Instances for production workloads
4. Implement auto-scaling based on CPU/memory usage

## 🔒 Security Best Practices

1. **Secrets Management**: Use AWS Systems Manager Parameter Store
2. **Network Security**: Private subnets for database and cache
3. **IAM Roles**: Least privilege access
4. **SSL/TLS**: Use ACM certificates
5. **Security Groups**: Restrict access to necessary ports only
6. **Image Scanning**: ECR automatically scans images for vulnerabilities

## 🔄 Alternative Deployment Options

### 1. AWS App Runner (Simpler, Higher Cost)
- Easier to set up and manage
- Automatic scaling and load balancing
- Higher cost (~$100-150/month)

### 2. AWS Elastic Beanstalk
- Platform-as-a-Service approach
- Good for traditional applications
- Less container-native

### 3. AWS Lambda + API Gateway (Serverless)
- Pay per request
- Complex for full Laravel applications
- Good for APIs only

## 🚨 Troubleshooting

### Common Issues:

1. **ECS Task Fails to Start**
   - Check CloudWatch logs
   - Verify environment variables
   - Check IAM permissions

2. **Database Connection Issues**
   - Verify security groups
   - Check RDS endpoint
   - Validate credentials

3. **File Upload Issues**
   - Verify S3 bucket permissions
   - Check IAM role for ECS tasks

### Useful Commands:

```bash
# Check ECS service status
aws ecs describe-services --cluster laravel-blog-cluster --services laravel-blog-service

# View running tasks
aws ecs list-tasks --cluster laravel-blog-cluster --service-name laravel-blog-service

# Check ALB target health
aws elbv2 describe-target-health --target-group-arn YOUR_TARGET_GROUP_ARN

# View CloudFormation stack status
aws cloudformation describe-stacks --stack-name laravel-blog-infrastructure
```

## 📞 Support

For issues with this deployment:
1. Check CloudWatch logs first
2. Verify all AWS resources are properly configured
3. Ensure GitHub secrets are correctly set
4. Review the task definition for correct values

This deployment provides a production-ready, scalable, and secure environment for your Laravel blog application on AWS. 