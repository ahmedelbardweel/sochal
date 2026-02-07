FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    nginx \
    supervisor

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Copy Nginx configuration
RUN echo 'server { \
    listen 8080; \
    root /var/www/public; \
    index index.php; \
    location / { \
    try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ \.php$ { \
    fastcgi_pass 127.0.0.1:9000; \
    fastcgi_index index.php; \
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
    include fastcgi_params; \
    } \
    }' > /etc/nginx/http.d/default.conf

# Supervisor configuration
RUN echo '[supervisord] \n\
    nodaemon=true \n\
    [program:php-fpm] \n\
    command=php-fpm \n\
    [program:nginx] \n\
    command=nginx -g "daemon off;"' > /etc/supervisord.conf

# Expose port
EXPOSE 8080

# Start services
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
