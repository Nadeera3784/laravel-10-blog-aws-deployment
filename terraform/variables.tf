# General Configuration
variable "project_name" {
  description = "Name of the project"
  type        = string
  default     = "laravel-blog"
}

variable "aws_region" {
  description = "AWS region"
  type        = string
  default     = "us-west-2"
}

# VPC Configuration
variable "vpc_cidr" {
  description = "CIDR block for VPC"
  type        = string
  default     = "10.0.0.0/16"
}

variable "public_subnet_cidrs" {
  description = "CIDR blocks for public subnets"
  type        = list(string)
  default     = ["10.0.1.0/24", "10.0.2.0/24"]
}

variable "private_subnet_cidrs" {
  description = "CIDR blocks for private subnets"
  type        = list(string)
  default     = ["10.0.3.0/24", "10.0.4.0/24"]
}

# ECS Configuration
variable "app_cpu" {
  description = "CPU units for the application"
  type        = number
  default     = 512
}

variable "app_memory" {
  description = "Memory for the application"
  type        = number
  default     = 1024
}

variable "app_count" {
  description = "Number of application instances"
  type        = number
  default     = 2
}

# Laravel Application Configuration
variable "app_env" {
  description = "Laravel application environment"
  type        = string
  default     = "production"
}

variable "app_debug" {
  description = "Laravel application debug mode"
  type        = string
  default     = "false"
}

variable "app_key" {
  description = "Laravel application key"
  type        = string
  sensitive   = true
}

# Database Configuration
variable "db_name" {
  description = "Database name"
  type        = string
  default     = "laravel_blog"
}

variable "db_username" {
  description = "Database username"
  type        = string
  default     = "laravel"
}

variable "db_password" {
  description = "Database password"
  type        = string
  sensitive   = true
}

variable "db_instance_class" {
  description = "RDS instance class"
  type        = string
  default     = "db.t3.micro"
}

variable "db_allocated_storage" {
  description = "Allocated storage for RDS instance"
  type        = number
  default     = 20
}

variable "db_backup_retention_period" {
  description = "Backup retention period for RDS"
  type        = number
  default     = 7
}

variable "db_skip_final_snapshot" {
  description = "Skip final snapshot when deleting RDS"
  type        = bool
  default     = false
}

variable "db_deletion_protection" {
  description = "Enable deletion protection for RDS"
  type        = bool
  default     = true
}

# Redis Configuration
variable "redis_node_type" {
  description = "ElastiCache Redis node type"
  type        = string
  default     = "cache.t3.micro"
}

variable "redis_num_cache_nodes" {
  description = "Number of cache nodes in the Redis cluster"
  type        = number
  default     = 1
}

# OpenSearch Configuration
variable "opensearch_instance_type" {
  description = "OpenSearch instance type"
  type        = string
  default     = "t3.small.search"
}

variable "opensearch_instance_count" {
  description = "Number of OpenSearch instances"
  type        = number
  default     = 1
}

variable "opensearch_master_instance_type" {
  description = "OpenSearch master instance type"
  type        = string
  default     = "t3.small.search"
}

variable "opensearch_volume_size" {
  description = "EBS volume size for OpenSearch"
  type        = number
  default     = 20
}

variable "opensearch_master_user" {
  description = "OpenSearch master username"
  type        = string
  default     = "admin"
}

variable "opensearch_master_password" {
  description = "OpenSearch master password"
  type        = string
  sensitive   = true
}

# Load Balancer Configuration
variable "alb_deletion_protection" {
  description = "Enable deletion protection for ALB"
  type        = bool
  default     = false
}

# Optional SSL Configuration
variable "ssl_certificate_arn" {
  description = "ARN of SSL certificate for HTTPS"
  type        = string
  default     = ""
}

# S3 Configuration
variable "s3_enable_versioning" {
  description = "Enable versioning for S3 bucket"
  type        = bool
  default     = true
}

variable "s3_allow_public_read" {
  description = "Allow public read access to S3 bucket (for public assets)"
  type        = bool
  default     = false
}

variable "s3_cors_allowed_origins" {
  description = "List of allowed origins for CORS"
  type        = list(string)
  default     = ["*"]
}

variable "s3_enable_lifecycle" {
  description = "Enable lifecycle policies for S3 bucket"
  type        = bool
  default     = true
} 