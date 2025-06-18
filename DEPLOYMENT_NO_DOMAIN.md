# Deploy Laravel Blog on AWS without a domain 

Yes, you can absolutely deploy your Laravel blog on AWS **without owning a domain**! AWS will provide you with a URL to access your application.

## 🌐 What You'll Get

After deployment, you'll receive an AWS-provided URL like:
```
http://laravel-blog-alb-1234567890.us-east-1.elb.amazonaws.com
```

This URL will work immediately and you can share it with anyone!

## 🚀 Quick Deployment Steps

### 1. Deploy AWS Infrastructure (Without Domain)

```bash
aws cloudformation create-stack \
  --stack-name laravel-blog-infrastructure \
  --template-body file://aws/cloudformation-template.yml \
  --parameters ParameterKey=DatabasePassword,ParameterValue=YourSecurePassword123! \
  --capabilities CAPABILITY_IAM
```

**Note:** We're not specifying a domain name, so it will use the default empty value.

### 2. Get Your Application URL

After the CloudFormation stack completes, get your Load Balancer URL:

```bash
aws cloudformation describe-stacks \
  --stack-name laravel-blog-infrastructure \
  --query 'Stacks[0].Outputs[?OutputKey==`LoadBalancer`].OutputValue' \
  --output text
```

This will return something like:
```
laravel-blog-alb-1234567890.us-east-1.elb.amazonaws.com
```

### 3. Update Task Definition with Real URL

Once you have the ALB DNS name, update the `aws/task-definition.json` file:

```json
{
  "name": "APP_URL",
  "value": "http://YOUR_ACTUAL_ALB_DNS_NAME_HERE"
}
```

Replace `YOUR_ACTUAL_ALB_DNS_NAME_HERE` with the actual DNS name from step 2.

### 4. Set Up GitHub Secrets

Add these secrets to your GitHub repository:

```
AWS_ACCESS_KEY_ID=your-access-key-id
AWS_SECRET_ACCESS_KEY=your-secret-access-key
AWS_ACCOUNT_ID=123456789012
```

### 5. Deploy Your Application

Push your code to GitHub, and the GitHub Actions pipeline will automatically:
1. Run tests
2. Build Docker image
3. Deploy to AWS

## 🔍 How to Access Your Blog

1. **Get the Load Balancer URL** (from step 2 above)
2. **Visit the URL** in your browser
3. **Done!** Your Laravel blog is live

Example URLs you might get:
- `http://laravel-blog-alb-1234567890.us-east-1.elb.amazonaws.com`
- `http://laravel-blog-alb-987654321.us-east-1.elb.amazonaws.com`

## 📝 Important Notes

### ✅ What Works Without a Domain:
- ✅ Complete Laravel application
- ✅ User authentication (admin@blog.com/password)
- ✅ Blog posts and categories
- ✅ Image uploads to S3
- ✅ Admin panel functionality
- ✅ Public blog interface

### ⚠️ Limitations (Without Custom Domain):
- ⚠️ URL is long and not branded
- ⚠️ No HTTPS by default (only HTTP)
- ⚠️ URL might change if you recreate the load balancer

## 🔧 Environment Configuration

Your application will use these settings automatically:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-alb-dns-name

# Database (AWS RDS)
DB_CONNECTION=mysql
DB_HOST=your-rds-endpoint
DB_PORT=3306
DB_DATABASE=laravel_blog

# Cache & Sessions (AWS ElastiCache)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=your-elasticache-endpoint

# File Storage (AWS S3 - already configured)
FILESYSTEM_DISK=s3
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=textract-aws-storage
```

## 🎯 Complete Deployment Commands

Here's the complete sequence to deploy without a domain:

```bash
# 1. Deploy infrastructure
aws cloudformation create-stack \
  --stack-name laravel-blog-infrastructure \
  --template-body file://aws/cloudformation-template.yml \
  --parameters ParameterKey=DatabasePassword,ParameterValue=YourSecurePassword123! \
  --capabilities CAPABILITY_IAM

# 2. Wait for completion
aws cloudformation wait stack-create-complete --stack-name laravel-blog-infrastructure

# 3. Get your app URL
aws cloudformation describe-stacks \
  --stack-name laravel-blog-infrastructure \
  --query 'Stacks[0].Outputs[?OutputKey==`LoadBalancer`].OutputValue' \
  --output text

# 4. Set up secrets (replace with your values)
aws ssm put-parameter --name "/laravel-blog/app-key" --value "base64:YOUR_APP_KEY" --type "SecureString"
aws ssm put-parameter --name "/laravel-blog/db-username" --value "admin" --type "SecureString"
aws ssm put-parameter --name "/laravel-blog/db-password" --value "YourSecurePassword123!" --type "SecureString"

# 5. Create IAM roles (automated script)
chmod +x aws/setup-iam-roles.sh
./aws/setup-iam-roles.sh

# 6. Push code to GitHub (triggers automatic deployment)
git add .
git commit -m "Deploy to AWS"
git push origin main
```


## 🚀 Live Example

After deployment, your blog will be accessible at a URL like:
```
http://laravel-blog-alb-1234567890.us-east-1.elb.amazonaws.com/
```

**Admin access:**
```
http://laravel-blog-alb-1234567890.us-east-1.elb.amazonaws.com/admin/posts
Email: admin@blog.com
Password: password
```

**Public blog:**
```
http://laravel-blog-alb-1234567890.us-east-1.elb.amazonaws.com/blog
```

