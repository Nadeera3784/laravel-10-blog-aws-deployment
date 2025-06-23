#!/bin/bash

set -e

echo "🚀 Laravel Blog Deployment Fix Script"
echo "======================================"

# Configuration
AWS_REGION="us-east-1"
ECR_REGISTRY="891377138894.dkr.ecr.us-east-1.amazonaws.com"
ECR_REPOSITORY="laravel-blog-repo"
ECS_CLUSTER="laravel-blog-cluster"
ECS_SERVICE="laravel-blog-service"
TIMESTAMP=$(date +%s)
IMAGE_TAG="fixed-${TIMESTAMP}"

echo "📦 Building Docker image..."
docker build -f docker/php/Dockerfile.fixed -t laravel-blog:${IMAGE_TAG} .

echo "🏷️ Tagging image for ECR..."
docker tag laravel-blog:${IMAGE_TAG} ${ECR_REGISTRY}/${ECR_REPOSITORY}:${IMAGE_TAG}

echo "🔐 Logging into ECR..."
aws ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${ECR_REGISTRY}

echo "📤 Pushing image to ECR..."
docker push ${ECR_REGISTRY}/${ECR_REPOSITORY}:${IMAGE_TAG}

echo "📋 Creating new task definition..."
TASK_DEFINITION=$(aws ecs describe-task-definition --task-definition laravel-blog-task --region ${AWS_REGION} --query 'taskDefinition')

# Update the image in the task definition
NEW_TASK_DEFINITION=$(echo $TASK_DEFINITION | jq --arg IMAGE "${ECR_REGISTRY}/${ECR_REPOSITORY}:${IMAGE_TAG}" '.containerDefinitions[0].image = $IMAGE' | jq 'del(.taskDefinitionArn, .revision, .status, .requiresAttributes, .placementConstraints, .compatibilities, .registeredAt, .registeredBy)')

echo "🔄 Registering new task definition..."
NEW_TASK_DEF_ARN=$(echo $NEW_TASK_DEFINITION | aws ecs register-task-definition --region ${AWS_REGION} --cli-input-json file:///dev/stdin --query 'taskDefinition.taskDefinitionArn' --output text)

echo "🚢 Updating ECS service..."
aws ecs update-service --cluster ${ECS_CLUSTER} --service ${ECS_SERVICE} --task-definition ${NEW_TASK_DEF_ARN} --region ${AWS_REGION}

echo "⏳ Waiting for deployment to complete..."
aws ecs wait services-stable --cluster ${ECS_CLUSTER} --services ${ECS_SERVICE} --region ${AWS_REGION}

echo "✅ Deployment completed successfully!"
echo "🌐 Application should be available at: http://laravel-blog-alb-941765149.us-east-1.elb.amazonaws.com"
echo "🏷️ Deployed image: ${ECR_REGISTRY}/${ECR_REPOSITORY}:${IMAGE_TAG}" 