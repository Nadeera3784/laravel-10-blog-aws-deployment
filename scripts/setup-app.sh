#!/bin/bash

# Laravel Blog Application Setup Script
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
TERRAFORM_DIR="terraform"
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo -e "${GREEN}⚙️  Laravel Blog Application Setup${NC}"
echo "========================================"

# Navigate to terraform directory
cd "$PROJECT_ROOT/$TERRAFORM_DIR"

# Check if terraform state exists
if [ ! -f "terraform.tfstate" ]; then
    echo -e "${RED}❌ Terraform state not found. Please deploy infrastructure first.${NC}"
    exit 1
fi

# Get infrastructure details
echo -e "${YELLOW}📋 Getting infrastructure details...${NC}"

PROJECT_NAME=$(terraform output -raw project_name 2>/dev/null || echo "laravel-blog")
CLUSTER_NAME=$(terraform output -raw ecs_cluster_name)
SERVICE_NAME=$(terraform output -raw ecs_service_name)
REGION=$(terraform output -raw aws_region 2>/dev/null || echo "us-west-2")

echo -e "${GREEN}📦 Project: $PROJECT_NAME${NC}"
echo -e "${GREEN}🏗️  Cluster: $CLUSTER_NAME${NC}"
echo -e "${GREEN}🚀 Service: $SERVICE_NAME${NC}"

# Function to get running task ARN
get_task_arn() {
    aws ecs list-tasks --cluster "$CLUSTER_NAME" --service-name "$SERVICE_NAME" --desired-status RUNNING --region "$REGION" --query 'taskArns[0]' --output text
}

# Function to execute command in ECS task
execute_in_task() {
    local task_arn="$1"
    local command="$2"
    
    echo -e "${YELLOW}🔧 Executing: $command${NC}"
    aws ecs execute-command \
        --cluster "$CLUSTER_NAME" \
        --task "$task_arn" \
        --container "laravel-app" \
        --region "$REGION" \
        --interactive \
        --command "$command"
}

# Wait for service to be stable
echo -e "${YELLOW}⏳ Waiting for ECS service to be stable...${NC}"
aws ecs wait services-stable --cluster "$CLUSTER_NAME" --services "$SERVICE_NAME" --region "$REGION"

# Get running task
echo -e "${YELLOW}🔍 Finding running task...${NC}"
TASK_ARN=$(get_task_arn)

if [ "$TASK_ARN" = "None" ] || [ -z "$TASK_ARN" ]; then
    echo -e "${RED}❌ No running tasks found. Please check ECS service status.${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Found running task: $TASK_ARN${NC}"

# Check if ECS Exec is enabled
echo -e "${YELLOW}🔧 Checking ECS Exec capability...${NC}"
EXEC_ENABLED=$(aws ecs describe-tasks --cluster "$CLUSTER_NAME" --tasks "$TASK_ARN" --region "$REGION" --query 'tasks[0].enableExecuteCommand' --output text)

if [ "$EXEC_ENABLED" != "True" ]; then
    echo -e "${RED}❌ ECS Exec is not enabled for this task.${NC}"
    echo -e "${YELLOW}💡 To enable ECS Exec, update your ECS service or task definition.${NC}"
    echo -e "${YELLOW}   Alternatively, you can run commands manually via AWS Console.${NC}"
    exit 1
fi

echo -e "${GREEN}✅ ECS Exec is enabled${NC}"

# Menu for setup options
echo -e "${YELLOW}📋 Setup Options:${NC}"
echo "1. Run database migrations"
echo "2. Seed database"
echo "3. Initialize Elasticsearch index"
echo "4. Refresh Elasticsearch index"
echo "5. Clear application cache"
echo "6. Generate application key"
echo "7. Run all setup tasks (1-5)"
echo "8. Interactive shell"
echo "0. Exit"

while true; do
    echo -e "${YELLOW}❓ Select an option (0-8):${NC}"
    read -r choice
    
    case $choice in
        1)
            echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
            execute_in_task "$TASK_ARN" "php artisan migrate --force"
            ;;
        2)
            echo -e "${YELLOW}🌱 Seeding database...${NC}"
            execute_in_task "$TASK_ARN" "php artisan db:seed --force"
            ;;
        3)
            echo -e "${YELLOW}🔍 Initializing Elasticsearch index...${NC}"
            execute_in_task "$TASK_ARN" "php artisan elasticsearch:recreate-index"
            ;;
        4)
            echo -e "${YELLOW}🔄 Refreshing Elasticsearch index...${NC}"
            execute_in_task "$TASK_ARN" "php artisan elasticsearch:refresh-index"
            ;;
        5)
            echo -e "${YELLOW}🧹 Clearing application cache...${NC}"
            execute_in_task "$TASK_ARN" "php artisan cache:clear"
            execute_in_task "$TASK_ARN" "php artisan config:cache"
            execute_in_task "$TASK_ARN" "php artisan route:cache"
            execute_in_task "$TASK_ARN" "php artisan view:cache"
            ;;
        6)
            echo -e "${YELLOW}🔑 Generating application key...${NC}"
            execute_in_task "$TASK_ARN" "php artisan key:generate --show"
            echo -e "${YELLOW}💡 Copy the generated key and update your terraform.tfvars${NC}"
            ;;
        7)
            echo -e "${YELLOW}🚀 Running all setup tasks...${NC}"
            execute_in_task "$TASK_ARN" "php artisan migrate --force"
            execute_in_task "$TASK_ARN" "php artisan db:seed --force"
            execute_in_task "$TASK_ARN" "php artisan elasticsearch:recreate-index"
            execute_in_task "$TASK_ARN" "php artisan elasticsearch:refresh-index"
            execute_in_task "$TASK_ARN" "php artisan cache:clear"
            execute_in_task "$TASK_ARN" "php artisan config:cache"
            echo -e "${GREEN}✅ All setup tasks completed${NC}"
            ;;
        8)
            echo -e "${YELLOW}🖥️  Opening interactive shell...${NC}"
            execute_in_task "$TASK_ARN" "/bin/bash"
            ;;
        0)
            echo -e "${GREEN}👋 Goodbye!${NC}"
            break
            ;;
        *)
            echo -e "${RED}❌ Invalid option. Please try again.${NC}"
            ;;
    esac
    
    echo ""
done

# Show application URL
APP_URL=$(terraform output -raw application_url)
echo -e "${GREEN}🌐 Application URL: $APP_URL${NC}"
echo -e "${GREEN}🎉 Setup completed successfully${NC}" 