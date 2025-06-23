# S3 Bucket for Laravel File Storage
resource "aws_s3_bucket" "app_storage" {
  bucket = "${var.project_name}-app-storage-${random_string.bucket_suffix.result}"

  tags = {
    Name = "${var.project_name}-app-storage"
  }
}

# Random string for bucket name uniqueness
resource "random_string" "bucket_suffix" {
  length  = 8
  special = false
  upper   = false
}

# S3 Bucket Versioning
resource "aws_s3_bucket_versioning" "app_storage" {
  bucket = aws_s3_bucket.app_storage.id
  versioning_configuration {
    status = var.s3_enable_versioning ? "Enabled" : "Disabled"
  }
}

# S3 Bucket Server Side Encryption
resource "aws_s3_bucket_server_side_encryption_configuration" "app_storage" {
  bucket = aws_s3_bucket.app_storage.id

  rule {
    apply_server_side_encryption_by_default {
      sse_algorithm = "AES256"
    }
    bucket_key_enabled = true
  }
}

# S3 Bucket Public Access Block
resource "aws_s3_bucket_public_access_block" "app_storage" {
  bucket = aws_s3_bucket.app_storage.id

  block_public_acls       = !var.s3_allow_public_read
  block_public_policy     = !var.s3_allow_public_read
  ignore_public_acls      = !var.s3_allow_public_read
  restrict_public_buckets = !var.s3_allow_public_read
}

# S3 Bucket Policy for Public Read (if enabled)
resource "aws_s3_bucket_policy" "app_storage_public_read" {
  count  = var.s3_allow_public_read ? 1 : 0
  bucket = aws_s3_bucket.app_storage.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Sid       = "PublicReadGetObject"
        Effect    = "Allow"
        Principal = "*"
        Action    = ["s3:GetObject"]
        Resource  = "${aws_s3_bucket.app_storage.arn}/public/*"
      }
    ]
  })

  depends_on = [aws_s3_bucket_public_access_block.app_storage]
}

# S3 Bucket CORS Configuration
resource "aws_s3_bucket_cors_configuration" "app_storage" {
  bucket = aws_s3_bucket.app_storage.id

  cors_rule {
    allowed_headers = ["*"]
    allowed_methods = ["GET", "POST", "PUT", "DELETE", "HEAD"]
    allowed_origins = var.s3_cors_allowed_origins
    expose_headers  = ["ETag"]
    max_age_seconds = 3000
  }

  depends_on = [aws_s3_bucket_public_access_block.app_storage]
}

# S3 Bucket Lifecycle Configuration
resource "aws_s3_bucket_lifecycle_configuration" "app_storage" {
  count  = var.s3_enable_lifecycle ? 1 : 0
  bucket = aws_s3_bucket.app_storage.id

  rule {
    id     = "delete_incomplete_multipart_uploads"
    status = "Enabled"

    filter {
      prefix = ""
    }

    abort_incomplete_multipart_upload {
      days_after_initiation = 7
    }
  }

  rule {
    id     = "transition_to_ia"
    status = "Enabled"

    filter {
      prefix = ""
    }

    transition {
      days          = 30
      storage_class = "STANDARD_IA"
    }
  }

  rule {
    id     = "transition_to_glacier"
    status = "Enabled"

    filter {
      prefix = ""
    }

    transition {
      days          = 90
      storage_class = "GLACIER"
    }
  }
}

# IAM Policy for S3 Access
resource "aws_iam_policy" "s3_access" {
  name_prefix = "${var.project_name}-s3-access-"
  description = "Policy for Laravel app to access S3 bucket"

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "s3:GetObject",
          "s3:PutObject",
          "s3:DeleteObject",
          "s3:GetObjectAcl",
          "s3:PutObjectAcl"
        ]
        Resource = [
          "${aws_s3_bucket.app_storage.arn}/*"
        ]
      },
      {
        Effect = "Allow"
        Action = [
          "s3:ListBucket",
          "s3:GetBucketLocation"
        ]
        Resource = [
          aws_s3_bucket.app_storage.arn
        ]
      }
    ]
  })

  tags = {
    Name = "${var.project_name}-s3-access-policy"
  }
}

# Attach S3 policy to ECS task role
resource "aws_iam_role_policy_attachment" "ecs_task_s3_access" {
  role       = aws_iam_role.ecs_task_role.name
  policy_arn = aws_iam_policy.s3_access.arn
}

# IAM Policy for ECS Exec
resource "aws_iam_policy" "ecs_exec" {
  name        = "${var.project_name}-ecs-exec"
  description = "Policy for ECS Exec functionality"

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "ssmmessages:CreateControlChannel",
          "ssmmessages:CreateDataChannel",
          "ssmmessages:OpenControlChannel",
          "ssmmessages:OpenDataChannel"
        ]
        Resource = "*"
      }
    ]
  })

  tags = {
    Name = "${var.project_name}-ecs-exec-policy"
  }
}

# Attach ECS Exec policy to ECS task role
resource "aws_iam_role_policy_attachment" "ecs_task_exec_access" {
  role       = aws_iam_role.ecs_task_role.name
  policy_arn = aws_iam_policy.ecs_exec.arn
} 