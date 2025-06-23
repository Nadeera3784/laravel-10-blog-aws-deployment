# General Configuration
project_name = "laravel-blog"
aws_region   = "us-east-1"

# VPC Configuration
vpc_cidr               = "10.0.0.0/16"
public_subnet_cidrs    = ["10.0.1.0/24", "10.0.2.0/24"]
private_subnet_cidrs   = ["10.0.3.0/24", "10.0.4.0/24"]

# ECS Configuration
app_cpu    = 512
app_memory = 1024
app_count  = 2

# Laravel Application Configuration
app_env   = "Production"
app_debug = "false"
app_key   = "base64:GDGVQ4JRzZLhrDILXUsvX/EGcy4gip8jdnP0fhrt/Js="

# Database Configuration
db_name                     = "laravel_blog"
db_username                 = "laraveluser"
db_password                 = "MySecurePassword12345"
db_instance_class           = "db.t3.micro"
db_allocated_storage        = 20
db_backup_retention_period  = 7
db_skip_final_snapshot      = false
db_deletion_protection      = true

# Redis Configuration
redis_node_type        = "cache.t3.micro"
redis_num_cache_nodes  = 1

# OpenSearch Configuration
opensearch_instance_type        = "t3.small.search"
opensearch_instance_count       = 1
opensearch_master_instance_type = "t3.small.search"
opensearch_volume_size          = 20
opensearch_master_user          = "admin"
opensearch_master_password      = "YourSecureOpenSearchPassword123!"

# Load Balancer Configuration
alb_deletion_protection = false

# S3 Configuration
s3_enable_versioning    = false
s3_allow_public_read    = true
s3_cors_allowed_origins = ["*"]
s3_enable_lifecycle     = true

# Optional SSL Configuration (uncomment if using HTTPS)
# ssl_certificate_arn = "arn:aws:acm:us-west-2:123456789012:certificate/12345678-1234-1234-1234-123456789012" 