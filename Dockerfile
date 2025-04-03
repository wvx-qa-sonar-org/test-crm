# Use the latest PHP image with Apache
FROM php:cli

# Install system dependencies and MongoDB extension
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    zip \
    unzip \
    libssl-dev \
    pkg-config

RUN pecl install mongodb && \ 
    docker-php-ext-enable mongodb

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer



# Set working directory
WORKDIR /backend

# Expose port 80
EXPOSE 8001

# Copy application files
COPY . .

# Start Apache
CMD ["apache2-foreground"]