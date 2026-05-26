FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    gnupg2 \
    ca-certificates \
    apt-transport-https \
    unixodbc-dev \
    odbcinst \
 && docker-php-ext-install -j$(nproc) intl mbstring zip pcntl bcmath \
 && rm -rf /var/lib/apt/lists/*

# START SQLserver
RUN curl https://packages.microsoft.com/keys/microsoft-2025.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && curl https://packages.microsoft.com/config/debian/13/prod.list > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 \
    && ACCEPT_EULA=Y apt-get install -y mssql-tools18 \
    && echo 'export PATH="$PATH:/opt/mssql-tools18/bin"' >> ~/.bashrc \
    && rm -rf /var/lib/apt/lists/*

RUN pecl channel-update https://pecl.php.net/channel.xml \
    && pecl install sqlsrv pdo_sqlsrv pcov \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv pcov

RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

RUN update-ca-certificates
# END SQLserver

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /package
COPY composer.json composer.json
RUN composer install --prefer-dist --no-interaction --no-scripts --no-autoloader
COPY . .
RUN composer dump-autoload --optimize

COPY .docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["composer", "test"]
