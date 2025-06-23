# OpenSearch Access Policy for VPC (no IP restrictions needed with VPC security groups)
data "aws_iam_policy_document" "opensearch_access_policy" {
  statement {
    effect = "Allow"
    
    principals {
      type        = "*"
      identifiers = ["*"]
    }
    
    actions = [
      "es:*"
    ]
    
    resources = [
      "arn:aws:es:${var.aws_region}:*:domain/${var.project_name}-opensearch/*"
    ]
  }
}

# OpenSearch Domain
resource "aws_opensearch_domain" "main" {
  domain_name    = "${var.project_name}-opensearch"
  engine_version = "OpenSearch_2.3"

  cluster_config {
    instance_type            = var.opensearch_instance_type
    instance_count           = var.opensearch_instance_count
    dedicated_master_enabled = var.opensearch_instance_count >= 3 ? true : false
    zone_awareness_enabled   = var.opensearch_instance_count > 1 ? true : false
    
    dynamic "zone_awareness_config" {
      for_each = var.opensearch_instance_count > 1 ? [1] : []
      content {
        availability_zone_count = min(var.opensearch_instance_count, length(aws_subnet.private))
      }
    }
  }

  ebs_options {
    ebs_enabled = true
    volume_type = "gp3"
    volume_size = var.opensearch_volume_size
  }

  vpc_options {
    security_group_ids = [aws_security_group.opensearch.id]
    subnet_ids         = slice(aws_subnet.private[*].id, 0, min(var.opensearch_instance_count, length(aws_subnet.private)))
  }

  encrypt_at_rest {
    enabled = true
  }

  node_to_node_encryption {
    enabled = true
  }

  domain_endpoint_options {
    enforce_https       = true
    tls_security_policy = "Policy-Min-TLS-1-2-2019-07"
  }

  advanced_security_options {
    enabled                        = true
    anonymous_auth_enabled         = false
    internal_user_database_enabled = true
    
    master_user_options {
      master_user_name     = var.opensearch_master_user
      master_user_password = var.opensearch_master_password
    }
  }

  log_publishing_options {
    cloudwatch_log_group_arn = aws_cloudwatch_log_group.opensearch.arn
    log_type                 = "INDEX_SLOW_LOGS"
  }

  log_publishing_options {
    cloudwatch_log_group_arn = aws_cloudwatch_log_group.opensearch.arn
    log_type                 = "SEARCH_SLOW_LOGS"
  }

  log_publishing_options {
    cloudwatch_log_group_arn = aws_cloudwatch_log_group.opensearch_application.arn
    log_type                 = "ES_APPLICATION_LOGS"
  }

  access_policies = data.aws_iam_policy_document.opensearch_access_policy.json

  tags = {
    Name = "${var.project_name}-opensearch"
  }

  depends_on = [
    aws_iam_service_linked_role.opensearch,
    aws_cloudwatch_log_resource_policy.opensearch
  ]
}

# CloudWatch Log Groups for OpenSearch
resource "aws_cloudwatch_log_group" "opensearch" {
  name              = "/aws/opensearch/domains/${var.project_name}-opensearch/index-slow-logs"
  retention_in_days = 7

  tags = {
    Name = "${var.project_name}-opensearch-slow-logs"
  }
}

resource "aws_cloudwatch_log_group" "opensearch_application" {
  name              = "/aws/opensearch/domains/${var.project_name}-opensearch/application-logs"
  retention_in_days = 7

  tags = {
    Name = "${var.project_name}-opensearch-app-logs"
  }
}

# CloudWatch Logs Resource Policy for OpenSearch
resource "aws_cloudwatch_log_resource_policy" "opensearch" {
  policy_name = "${var.project_name}-opensearch-logs-policy"

  policy_document = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Principal = {
          Service = "es.amazonaws.com"
        }
        Action = [
          "logs:PutLogEvents",
          "logs:CreateLogGroup",
          "logs:CreateLogStream",
          "logs:PutRetentionPolicy"
        ]
        Resource = [
          "arn:aws:logs:${var.aws_region}:*:log-group:/aws/opensearch/domains/${var.project_name}-opensearch/*"
        ]
      }
    ]
  })
}

# OpenSearch Service Linked Role
resource "aws_iam_service_linked_role" "opensearch" {
  aws_service_name = "es.amazonaws.com"
  description      = "Service linked role for OpenSearch"
} 