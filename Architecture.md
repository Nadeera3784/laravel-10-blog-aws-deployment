## Complete Directory Structure

```
project-root/
├── app/
│   ├── Foundation/               # Shared kernel, base classes, traits
│   │   ├── Entities/             # Base entities, models, value objects, shared domain logic
│   │   ├── UseCases/             # Base interactors, shared application services
│   │       ├── Repositories/     # Repository interfaces for shared entities
│   │   ├── Adapters/             # Base presenters, view models, framework-agnostic components
│   │   ├── IO/                   # Shared IO components
│   │       ├── Database/         # Common database concerns, repository implementations
│   │       ├── Http/             # Shared controllers, middleware, API resources
│   │       ├── Web/              # Common layouts, shared components, base templates
│   │       ├── GraphQL/          # Shared GraphQL components
│   │       └── ExternalServices/ # Shared service clients, integrations
│   │       ├── FoundationServiceProvider.php  # Core app-wide service provider
│   │   ├── Specs/                # Foundation specifications
│   │   └── Testing/              # Foundation testing support API
│   │
│   ├── Blog/                    # Blog domain module
│   │   ├── Entities/             # Domain entities, models, and business rules
│   │   ├── UseCases/             # Application business logic
│   │       ├── Repositories/     # Repository interfaces
│   │   ├── Adapters/             # Framework-agnostic interface adapters
│   │   ├── IO/                   # Frameworks, drivers, external services
│   │       ├── Database/         # Blog-specific database migrations, seeders, repositories
│   │       ├── Http/             # Blog-specific HTTP interfaces, controllers, resources
│   │       ├── Web/              # Blog-specific UI elements
│   │       ├── GraphQL/          # Blog-specific GraphQL components
│   │       └── ExternalServices/ # Blog-specific external services
│   │       ├── BlogServiceProvider.php      # Blog domain service provider
│   │   ├── Specs/                # Blog behavior specifications
│   │   └── Testing/              # Blog-specific testing utilities
│   │
│   └── User/                     # User domain module (same structure)
```

