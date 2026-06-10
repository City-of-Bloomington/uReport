FROM php:8.5-apache

# Install system deps
RUN apt-get update && apt-get install -y \
    unzip \
    rsync \
    git \
    curl \
    default-mysql-client \
    gettext \
    locales \
    imagemagick \
    && docker-php-ext-install pdo pdo_mysql mysqli gettext \
    && sed -i 's/^# *en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/' /etc/locale.gen \
    && locale-gen \
    && update-locale LANG=en_US.UTF-8 \
    && rm -rf /var/lib/apt/lists/*

# Install pcov for test coverage analysis
RUN pecl install pcov \
    && docker-php-ext-enable pcov

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Install Sass globally
RUN npm install -g sass

# Enable Apache rewrite
RUN a2enmod rewrite

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Set working dir
WORKDIR /var/www/html
