#!/bin/bash

# Setup IAM Roles for Laravel Blog ECS Deployment
echo "🔐 Setting up IAM roles for ECS deployment..."

# Get AWS Account ID
ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
echo "📋 AWS Account ID: $ACCOUNT_ID"

# 1. Create ECS Task Execution Role
echo "📝 Creating ECS Task Execution Role..."
aws iam create-role \
  --role-name ecsTaskExecutionRole \
  --assume-role-policy-document file://aws/iam-trust-policy.json \
  --description "ECS Task Execution Role for Laravel Blog"

# Attach the managed policy for ECS task execution
aws iam attach-role-policy \
  --role-name ecsTaskExecutionRole \
  --policy-arn arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy

echo "✅ ECS Task Execution Role created successfully"

# 2. Create ECS Task Role (for application permissions)
echo "📝 Creating ECS Task Role..."
aws iam create-role \
  --role-name ecsTaskRole \
  --assume-role-policy-document file://aws/iam-trust-policy.json \
  --description "ECS Task Role for Laravel Blog application"

# Create custom policy for S3 and Systems Manager access
cat > aws/ecs-task-policy.json << 'EOF'
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "s3:GetObject",
        "s3:PutObject",
        "s3:DeleteObject",
        "s3:GetObjectVersion"
      ],
      "Resource": "arn:aws:s3:::textract-aws-storage/*"
    },
    {
      "Effect": "Allow",
      "Action": [
        "s3:ListBucket"
      ],
      "Resource": "arn:aws:s3:::textract-aws-storage"
    },
    {
      "Effect": "Allow",
      "Action": [
        "ssm:GetParameter",
        "ssm:GetParameters",
        "ssm:GetParametersByPath"
      ],
      "Resource": [
        "arn:aws:ssm:*:${ACCOUNT_ID}:parameter/laravel-blog/*"
      ]
    }
  ]
}
EOF

# Replace account ID in the policy
sed -i.bak "s/\${ACCOUNT_ID}/$ACCOUNT_ID/g" aws/ecs-task-policy.json

# Create the custom policy
aws iam create-policy \
  --policy-name LaravelBlogECSTaskPolicy \
  --policy-document file://aws/ecs-task-policy.json \
  --description "Custom policy for Laravel Blog ECS tasks"

# Attach the custom policy to the task role
aws iam attach-role-policy \
  --role-name ecsTaskRole \
  --policy-arn "arn:aws:iam::$ACCOUNT_ID:policy/LaravelBlogECSTaskPolicy"

echo "✅ ECS Task Role created successfully"

# 3. Update task definition with correct ARNs
echo "📝 Updating task definition with correct role ARNs..."

# Create a backup of the original task definition
cp aws/task-definition.json aws/task-definition.json.bak

# Update the task definition with correct ARNs
sed -i.tmp "s/YOUR_ACCOUNT_ID/$ACCOUNT_ID/g" aws/task-definition.json

echo "✅ Task definition updated with correct ARNs"

# 4. Display the roles created
echo ""
echo "🎉 IAM Roles Setup Complete!"
echo ""
echo "📋 Created Roles:"
echo "   • ecsTaskExecutionRole: arn:aws:iam::$ACCOUNT_ID:role/ecsTaskExecutionRole"
echo "   • ecsTaskRole: arn:aws:iam::$ACCOUNT_ID:role/ecsTaskRole"
echo ""
echo "📋 Created Policy:"
echo "   • LaravelBlogECSTaskPolicy: arn:aws:iam::$ACCOUNT_ID:policy/LaravelBlogECSTaskPolicy"
echo ""
echo "✅ Your task definition has been updated with the correct ARNs"
echo ""
echo "🚀 Next steps:"
echo "   1. Deploy CloudFormation stack"
echo "   2. Set up application secrets"
echo "   3. Push code to GitHub for deployment"

# Clean up temporary files
rm -f aws/ecs-task-policy.json.bak aws/task-definition.json.tmp 