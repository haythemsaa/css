# Backend Dockerfile - Laravel 12
FROM php:8.4-fpm

# Arguments
ARG user=css
ARG uid=1000

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev

# Nettoyage du cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Création de l'utilisateur système
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de l'application
COPY --chown=$user:$user . /var/www/html

# Installer les dépendances PHP
USER $user
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Permissions
USER root
RUN chown -R $user:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port
EXPOSE 8000

# Démarrer PHP-FPM
USER $user
CMD ["php-fpm"]
