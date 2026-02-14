FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mysqli

# Set document root to /var/www/html/src/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/src/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Enable apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files and install dependencies first for better caching
WORKDIR /var/www/html
COPY composer.json composer.lock .
RUN composer install --no-dev --optimize-autoloader

# Copy application files
COPY . .

# Move vendor folder to the correct location
RUN rm -rf src/private/vendor && mv vendor src/private

# Set permissions
RUN chown -R www-data:www-data /var/www/html/src/private/uploads

# Set permissions for all files (not just uploads)
RUN chown -R www-data:www-data /var/www/html

# Update Apache config to allow .htaccess and access to public
RUN echo '<Directory /var/www/html/src/public>\n    AllowOverride All\n    Require all granted\n</Directory>' > /etc/apache2/conf-available/allow-public.conf \
    && a2enconf allow-public
