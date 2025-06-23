# Network Outputs
output "vpc_id" {
  description = "ID of the VPC"
  value       = aws_vpc.main.id
}

output "public_subnet_ids" {
  description = "IDs of the public subnets"
  value       = aws_subnet.public[*].id
}

output "private_subnet_ids" {
  description = "IDs of the private subnets"
  value       = aws_subnet.private[*].id
}

# Load Balancer Outputs
output "load_balancer_dns_name" {
  description = "DNS name of the load balancer"
  value       = aws_lb.main.dns_name
}

output "load_balancer_zone_id" {
  description = "Zone ID of the load balancer"
  value       = aws_lb.main.zone_id
}

output "load_balancer_arn" {
  description = "ARN of the load balancer"
  value       = aws_lb.main.arn
}

# ECS Outputs
output "ecs_cluster_name" {
  description = "Name of the ECS cluster"
  value       = aws_ecs_cluster.main.name
}

output "ecs_service_name" {
  description = "Name of the ECS service"
  value       = aws_ecs_service.app.name
}

output "ecr_repository_url" {
  description = "URL of the ECR repository"
  value       = aws_ecr_repository.app.repository_url
}

# Database Outputs
output "database_endpoint" {
  description = "RDS instance endpoint"
  value       = aws_db_instance.main.endpoint
  sensitive   = true
}

output "database_port" {
  description = "RDS instance port"
  value       = aws_db_instance.main.port
}

# Redis Outputs
output "redis_endpoint" {
  description = "ElastiCache Redis endpoint"
  value       = aws_elasticache_replication_group.main.primary_endpoint_address
  sensitive   = true
}

output "redis_port" {
  description = "ElastiCache Redis port"
  value       = aws_elasticache_replication_group.main.port
}

# OpenSearch Outputs
output "opensearch_endpoint" {
  description = "OpenSearch domain endpoint"
  value       = aws_opensearch_domain.main.endpoint
  sensitive   = true
}

output "opensearch_dashboard_endpoint" {
  description = "OpenSearch dashboard endpoint"
  value       = aws_opensearch_domain.main.dashboard_endpoint
  sensitive   = true
}

# Security Group Outputs
output "alb_security_group_id" {
  description = "ID of the ALB security group"
  value       = aws_security_group.alb.id
}

output "ecs_security_group_id" {
  description = "ID of the ECS security group"
  value       = aws_security_group.ecs.id
}

output "rds_security_group_id" {
  description = "ID of the RDS security group"
  value       = aws_security_group.rds.id
}

output "redis_security_group_id" {
  description = "ID of the Redis security group"
  value       = aws_security_group.redis.id
}

output "opensearch_security_group_id" {
  description = "ID of the OpenSearch security group"
  value       = aws_security_group.opensearch.id
}

# S3 Outputs
output "s3_bucket_name" {
  description = "Name of the S3 bucket for file storage"
  value       = aws_s3_bucket.app_storage.bucket
}

output "s3_bucket_arn" {
  description = "ARN of the S3 bucket"
  value       = aws_s3_bucket.app_storage.arn
}

output "s3_bucket_domain_name" {
  description = "Domain name of the S3 bucket"
  value       = aws_s3_bucket.app_storage.bucket_domain_name
}

output "s3_bucket_regional_domain_name" {
  description = "Regional domain name of the S3 bucket"
  value       = aws_s3_bucket.app_storage.bucket_regional_domain_name
}

# Application URL
output "application_url" {
  description = "URL to access the Laravel application"
  value       = "http://${aws_lb.main.dns_name}"
}

# Project Configuration
output "project_name" {
  description = "Name of the project"
  value       = var.project_name
}

output "aws_region" {
  description = "AWS region where resources are deployed"
  value       = var.aws_region
}

# Generate task definition JSON for GitHub Actions
resource "local_file" "task_definition" {
  content = jsonencode({
    family                   = aws_ecs_task_definition.app.family
    networkMode             = "awsvpc"
    requiresCompatibilities = ["FARGATE"]
    cpu                     = tostring(aws_ecs_task_definition.app.cpu)
    memory                  = tostring(aws_ecs_task_definition.app.memory)
    executionRoleArn        = aws_ecs_task_definition.app.execution_role_arn
    taskRoleArn            = aws_ecs_task_definition.app.task_role_arn
    containerDefinitions = [
      {
        name  = "laravel-app"
        image = "${aws_ecr_repository.app.repository_url}:latest"
        
        portMappings = [
          {
            containerPort = 80
            hostPort      = 80
            protocol      = "tcp"
          }
        ]

        environment = [
          {
            name  = "APP_ENV"
            value = var.app_env
          },
          {
            name  = "APP_DEBUG"
            value = var.app_debug
          },
          {
            name  = "APP_KEY"
            value = var.app_key
          },
          {
            name  = "DB_CONNECTION"
            value = "mysql"
          },
          {
            name  = "DB_HOST"
            value = split(":", aws_db_instance.main.endpoint)[0]
          },
          {
            name  = "DB_PORT"
            value = "3306"
          },
          {
            name  = "DB_DATABASE"
            value = var.db_name
          },
          {
            name  = "DB_USERNAME"
            value = var.db_username
          },
          {
            name  = "DB_PASSWORD"
            value = var.db_password
          },
          {
            name  = "REDIS_HOST"
            value = aws_elasticache_replication_group.main.primary_endpoint_address
          },
          {
            name  = "REDIS_PORT"
            value = "6379"
          },
          {
            name  = "REDIS_PASSWORD"
            value = ""
          },
          {
            name  = "REDIS_SCHEME"
            value = "tcp"
          },
          {
            name  = "ELASTICSEARCH_HOST"
            value = "https://${aws_opensearch_domain.main.endpoint}"
          },
          {
            name  = "QUEUE_CONNECTION"
            value = "redis"
          },
          {
            name  = "FILESYSTEM_DISK"
            value = "s3"
          },
          {
            name  = "AWS_BUCKET"
            value = aws_s3_bucket.app_storage.bucket
          },
          {
            name  = "AWS_DEFAULT_REGION"
            value = var.aws_region
          },
          {
            name  = "AWS_USE_PATH_STYLE_ENDPOINT"
            value = "false"
          },
          {
            name  = "CACHE_DRIVER"
            value = "redis"
          },
          {
            name  = "SESSION_DRIVER"
            value = "redis"
          },
          {
            name  = "LOG_CHANNEL"
            value = "stderr"
          },
          {
            name  = "ELASTICSEARCH_USERNAME"
            value = var.opensearch_master_user
          },
          {
            name  = "ELASTICSEARCH_PASSWORD"
            value = var.opensearch_master_password
          }
        ]

        logConfiguration = {
          logDriver = "awslogs"
          options = {
            "awslogs-group"         = aws_cloudwatch_log_group.app.name
            "awslogs-region"        = var.aws_region
            "awslogs-stream-prefix" = "ecs"
          }
        }

        essential = true
      }
    ]
  })
  
  filename = "../aws/task-definition.json"
} 