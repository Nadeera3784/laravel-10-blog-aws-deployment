# ElastiCache Subnet Group
resource "aws_elasticache_subnet_group" "main" {
  name       = "${var.project_name}-cache-subnet"
  subnet_ids = aws_subnet.private[*].id

  tags = {
    Name = "${var.project_name}-cache-subnet-group"
  }
}

# ElastiCache Parameter Group
resource "aws_elasticache_parameter_group" "main" {
  family = "redis7"
  name   = "${var.project_name}-cache-params"

  parameter {
    name  = "maxmemory-policy"
    value = "allkeys-lru"
  }

  tags = {
    Name = "${var.project_name}-cache-parameter-group"
  }
}

# ElastiCache Replication Group
resource "aws_elasticache_replication_group" "main" {
  replication_group_id         = "${var.project_name}-redis"
  description                  = "Redis cluster for ${var.project_name}"
  
  node_type                    = var.redis_node_type
  port                        = 6379
  parameter_group_name        = aws_elasticache_parameter_group.main.name
  
  num_cache_clusters          = var.redis_num_cache_nodes
  
  engine_version              = "7.0"
  
  subnet_group_name           = aws_elasticache_subnet_group.main.name
  security_group_ids          = [aws_security_group.redis.id]
  
  at_rest_encryption_enabled  = true
  transit_encryption_enabled  = true
  transit_encryption_mode     = "preferred"
  apply_immediately           = true
  
  snapshot_retention_limit    = 5
  snapshot_window            = "03:00-05:00"
  
  maintenance_window         = "sun:05:00-sun:07:00"
  
  automatic_failover_enabled = var.redis_num_cache_nodes > 1 ? true : false
  multi_az_enabled          = var.redis_num_cache_nodes > 1 ? true : false
  
  tags = {
    Name = "${var.project_name}-redis-cluster"
  }
} 