# Docker Development Guide - Skull King League

## 🚀 Quick Start

The fastest way to get Skull King League running locally is with Docker:

```bash
git clone https://github.com/Brice19/SkullKingLeague.git
cd SkullKingLeague
./docker-dev.sh up
```

Access the application:
- **Main App**: http://localhost:8080
- **Admin Panel**: http://localhost:8080/?page=admin (admin/admin123)
- **PHPMyAdmin**: http://localhost:8081 

## 🏗️ Architecture

### Services

- **app**: PHP 8.3 + Apache web server running the application
- **mysql**: MySQL 8.0 database with automatic initialization
- **phpmyadmin**: Web-based database management (optional)

### Docker Images

- **Application**: Custom PHP 8.3-apache image with required extensions
- **Database**: Official MySQL 8.0 image
- **Admin**: Official PHPMyAdmin image

## 🛠️ Development Tools

### Helper Script

The `docker-dev.sh` script provides convenient commands:

```bash
# Environment management
./docker-dev.sh up          # Start all services
./docker-dev.sh down        # Stop all services  
./docker-dev.sh restart     # Restart services
./docker-dev.sh status      # Show service status

# Development tools
./docker-dev.sh logs        # View real-time logs
./docker-dev.sh shell       # Open app container shell
./docker-dev.sh db-shell    # Open MySQL shell

# Maintenance
./docker-dev.sh init-db     # Reinitialize database
./docker-dev.sh test        # Run functionality tests
./docker-dev.sh clean       # Clean up containers and volumes
```

### VS Code Dev Containers

The project includes VS Code Dev Container configuration:

1. **Prerequisites**: Install "Dev Containers" extension in VS Code
2. **Usage**: Open project in VS Code → "Reopen in Container"
3. **Features**:
   - Full PHP development environment
   - Pre-configured debugging
   - Database access tools
   - Automatic port forwarding

## 🔧 Configuration

### Environment Variables

Copy `.env.example` to `.env` and customize:

```bash
# Database settings
DB_HOST=mysql
DB_NAME=skull_king_league
DB_USER=skullking_user
DB_PASS=SkullKing_2025!

# Application settings
APP_ENV=development
APP_DEBUG=true
```

### Custom Ports

If default ports conflict, modify `docker-compose.yml`:

```yaml
services:
  app:
    ports:
      - "8080:80"    # Change 8080 to your preferred port
  mysql:
    ports:
      - "3306:3306"  # Change 3306 to your preferred port
```

### Database Persistence

Database data is automatically persisted in a Docker volume named `mysql_data`. To reset:

```bash
./docker-dev.sh down
docker volume rm skullkingleague_mysql_data
./docker-dev.sh up
```

## 🧪 Testing & CI/CD

### Automated Testing

The project includes GitHub Actions workflows:

- **Container Build**: Tests Docker image build and basic functionality
- **Security Scan**: Vulnerability scanning with Trivy
- **PR Preview**: Automated preview environments for pull requests

### Local Testing

```bash
# Test container build
docker build -t skull-king-test .

# Test full stack
./docker-dev.sh up
./docker-dev.sh test

# Manual testing
curl http://localhost:8080/
```

### PR Preview Environments

When you open a pull request:

1. GitHub Actions automatically builds your changes
2. A preview environment is created
3. A comment on the PR provides access details
4. Environment is cleaned up when PR is closed

## 🔒 Security

### Best Practices Applied

- **Non-root user**: Application runs as `www-data`
- **Minimal image**: Only required packages installed
- **Health checks**: Container health monitoring
- **Secrets**: Database credentials via environment variables
- **Network isolation**: Services in dedicated Docker network

### Security Scanning

Trivy vulnerability scanner runs on every PR:

```bash
# Run security scan locally
docker run --rm -v /var/run/docker.sock:/var/run/docker.sock \
  aquasec/trivy image skull-king-league:latest
```

## 🚀 Production Deployment

### Building for Production

```bash
# Build optimized image
docker build -t skull-king-league:prod .

# Or use docker-compose
docker-compose -f docker-compose.prod.yml build
```

### Environment-Specific Configs

Create environment-specific compose files:

```yaml
# docker-compose.prod.yml
version: '3.8'
services:
  app:
    build: .
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    restart: unless-stopped
```

### Resource Limits

For production, add resource constraints:

```yaml
services:
  app:
    deploy:
      resources:
        limits:
          memory: 512M
          cpus: '0.5'
```

## 🐛 Troubleshooting

### Common Issues

**Port conflicts**:
```bash
# Check what's using the port
lsof -i :8080
# Change ports in docker-compose.yml
```

**Database connection errors**:
```bash
# Check MySQL logs
./docker-dev.sh logs mysql
# Verify database is healthy
docker-compose ps
```

**Permission issues**:
```bash
# Fix file permissions
sudo chown -R $USER:$USER .
```

**Container won't start**:
```bash
# Check detailed logs
docker-compose logs app
# Rebuild images
./docker-dev.sh build
```

### Debug Mode

Enable detailed debugging:

```bash
# Set in .env file
APP_DEBUG=true

# View PHP errors
./docker-dev.sh logs app | grep ERROR
```

### Database Access

Multiple ways to access the database:

```bash
# MySQL command line
./docker-dev.sh db-shell

# Access http://localhost:8081 # (PHPMyAdmin)

# Direct Docker exec
docker-compose exec mysql mysql -u root -p
```

## 📊 Performance

### Optimization Tips

- **Use volumes**: Mount source code for development hot-reload
- **Multi-stage builds**: For optimized production images
- **Health checks**: Ensure services are ready before dependent services start
- **Resource limits**: Prevent containers from consuming too many resources

### Monitoring

```bash
# Resource usage
docker stats

# Container health
docker-compose ps

# Application logs
./docker-dev.sh logs app
```

## 🤝 Contributing

When contributing to the Docker setup:

1. Test your changes locally with `./docker-dev.sh test`
2. Ensure containers build successfully on different platforms
3. Update documentation for any new features
4. Test dev container setup in VS Code
5. Verify GitHub Actions pass

### Adding New Services

To add a new service:

1. Add to `docker-compose.yml`
2. Update `.devcontainer/devcontainer.json` if needed
3. Add commands to `docker-dev.sh` if necessary
4. Update this documentation

## 📝 Additional Resources

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Reference](https://docs.docker.com/compose/)
- [VS Code Dev Containers](https://code.visualstudio.com/docs/remote/containers)
- [GitHub Actions Docker](https://docs.github.com/en/actions/publishing-packages/publishing-docker-images)