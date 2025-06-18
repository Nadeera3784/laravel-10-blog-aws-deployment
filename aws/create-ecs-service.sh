#!/bin/bash

# Create ECS Service for Laravel Blog
echo "🚀 Creating ECS Service for Laravel Blog..."

# Get AWS Account ID
ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
echo "📋 AWS Account ID: $ACCOUNT_ID"

# Get CloudFormation outputs
echo "📋 Getting CloudFormation outputs..."

PRIVATE_SUBNET_1=$(aws cloudformation describe-stacks \
  --stack-name laravel-blog-infrastructure \
  --query 'Stacks[0].Outputs[?OutputKey==`PrivateSubnet1`].OutputValue' \
  --output text)

PRIVATE_SUBNET_2=$(aws cloudformation describe-stacks \
  --stack-name laravel-blog-infrastructure \
  --query 'Stacks[0].Outputs[?OutputKey==`PrivateSubnet2`].OutputValue' \
  --output text)

ECS_SECURITY_GROUP=$(aws ec2 describe-security-groups \
  --filters "Name=group-name,Values=laravel-blog-ecs-sg" \
  --query 'SecurityGroups[0].GroupId' \
  --output text)

TARGET_GROUP_ARN=$(aws cloudformation describe-stacks \
  --stack-name laravel-blog-infrastructure \
  --query 'Stacks[0].Outputs[?OutputKey==`TargetGroup`].OutputValue' \
  --output text)

echo "📋 Infrastructure Details:"
echo "   Private Subnet 1: $PRIVATE_SUBNET_1"
echo "   Private Subnet 2: $PRIVATE_SUBNET_2" 
echo "   Security Group: $ECS_SECURITY_GROUP"
echo "   Target Group: $TARGET_GROUP_ARN"

# First, register the task definition
echo "📝 Registering ECS Task Definition..."
aws ecs register-task-definition --cli-input-json file://aws/task-definition.json

# Create the ECS service
echo "🔧 Creating ECS Service..."
aws ecs create-service \
  --cluster laravel-blog-cluster \
  --service-name laravel-blog-service \
  --task-definition laravel-blog-task:1 \
  --desired-count 1 \
  --launch-type FARGATE \
  --platform-version LATEST \
  --network-configuration "awsvpcConfiguration={
    subnets=[$PRIVATE_SUBNET_1,$PRIVATE_SUBNET_2],
    securityGroups=[$ECS_SECURITY_GROUP],
    assignPublicIp=ENABLED
  }" \
  --load-balancers "targetGroupArn=$TARGET_GROUP_ARN,containerName=laravel-app,containerPort=80" \
  --health-check-grace-period-seconds 300

if [ $? -eq 0 ]; then
  echo "✅ ECS Service created successfully!"
  echo ""
  echo "🎉 Service Details:"
  echo "   Cluster: laravel-blog-cluster"
  echo "   Service: laravel-blog-service" 
  echo "   Task Definition: laravel-blog-task:1"
  echo "   Desired Count: 1"
  echo ""
  echo "⏱️  The service is now starting up..."
  echo "   You can monitor it in the AWS ECS Console"
  echo ""
  echo "🚀 Next steps:"
  echo "   1. Wait for the service to become stable (~5-10 minutes)"
  echo "   2. Get your application URL:"
  echo "      aws cloudformation describe-stacks --stack-name laravel-blog-infrastructure --query 'Stacks[0].Outputs[?OutputKey==\`LoadBalancer\`].OutputValue' --output text"
  echo "   3. Future deployments will work automatically via GitHub Actions"
else
  echo "❌ Failed to create ECS service"
  echo "Please check the AWS Console for more details"
fi 