#!/bin/bash

# Laravel Blog AWS Deployment Script
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
TERRAFORM_DIR="terraform"
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
REGION="us-west-2"

echo -e "${GREEN}🚀 Laravel Blog AWS Deployment Script${NC}"
echo "================================================"

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Check prerequisites
echo -e "${YELLOW}📋 Checking prerequisites...${NC}"

if ! command_exists terraform; then
    echo -e "${RED}❌ Terraform is not installed${NC}"
    exit 1
fi

if ! command_exists aws; then
    echo -e "${RED}❌ AWS CLI is not installed${NC}"
    exit 1
fi

if ! command_exists docker; then
    echo -e "${RED}❌ Docker is not installed${NC}"
    exit 1
fi

# Check AWS credentials
if ! aws sts get-caller-identity >/dev/null 2>&1; then
    echo -e "${RED}❌ AWS credentials not configured${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Prerequisites check passed${NC}"

# Navigate to terraform directory
cd "$PROJECT_ROOT/$TERRAFORM_DIR"

# Check if terraform.tfvars exists
if [ ! -f "terraform.tfvars" ]; then
    echo -e "${YELLOW}⚠️  terraform.tfvars not found. Please create it from terraform.tfvars.example${NC}"
    echo -e "${YELLOW}   cp terraform.tfvars.example terraform.tfvars${NC}"
    echo -e "${YELLOW}   Then edit terraform.tfvars with your values${NC}"
    exit 1
fi

# Initialize Terraform
echo -e "${YELLOW}🔧 Initializing Terraform...${NC}"
terraform init

# Plan Terraform
echo -e "${YELLOW}📋 Planning Terraform deployment...${NC}"
terraform plan -out=tfplan

# Ask for confirmation
echo -e "${YELLOW}❓ Do you want to apply the Terraform plan? (y/N)${NC}"
read -r response
if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
    echo -e "${YELLOW}🚀 Applying Terraform...${NC}"
    terraform apply tfplan
    
    # Get ECR repository URL
    ECR_REPO=$(terraform output -raw ecr_repository_url)
    echo -e "${GREEN}✅ Infrastructure deployed successfully${NC}"
    echo -e "${GREEN}📦 ECR Repository: $ECR_REPO${NC}"
    
    # Ask if user wants to build and push Docker image
    echo -e "${YELLOW}❓ Do you want to build and push the Docker image? (y/N)${NC}"
    read -r docker_response
    if [[ "$docker_response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
        echo -e "${YELLOW}🐳 Building and pushing Docker image...${NC}"
        
        # Navigate back to project root
        cd "$PROJECT_ROOT"
        
        # Get ECR login
        aws ecr get-login-password --region $REGION | docker login --username AWS --password-stdin $ECR_REPO
        
        # Build Docker image
        echo -e "${YELLOW}🔨 Building Docker image...${NC}"
        docker build -f docker/php/Dockerfile.prod -t laravel-blog:latest .
        
        # Tag and push image
        echo -e "${YELLOW}📤 Pushing Docker image...${NC}"
        docker tag laravel-blog:latest $ECR_REPO:latest
        docker push $ECR_REPO:latest
        
        echo -e "${GREEN}✅ Docker image pushed successfully${NC}"
        
        # Navigate back to terraform directory
        cd "$PROJECT_ROOT/$TERRAFORM_DIR"
        
        # Get application URL
        APP_URL=$(terraform output -raw application_url)
        echo -e "${GREEN}🌐 Application URL: $APP_URL${NC}"
        
        echo -e "${YELLOW}📝 Next steps:${NC}"
        echo -e "   1. Wait for ECS service to become stable (5-10 minutes)"
        echo -e "   2. Run database migrations in ECS task"
        echo -e "   3. Initialize Elasticsearch index"
        echo -e "   4. Access your application at: $APP_URL"
    fi
else
    echo -e "${YELLOW}⏸️  Deployment cancelled${NC}"
    rm -f tfplan
fi

echo -e "${GREEN}🎉 Deployment script completed${NC}" 