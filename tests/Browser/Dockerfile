FROM mcr.microsoft.com/playwright:v1.65.0-noble

ENV DEBIAN_FRONTEND=noninteractive

# PHP 8.4 (from the ondrej PPA) because the locked vendor/ requires PHP >= 8.4.1;
# Ubuntu 24.04 only ships 8.3.
RUN apt-get update \
    && apt-get install -y --no-install-recommends software-properties-common ca-certificates \
    && add-apt-repository -y ppa:ondrej/php \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        php8.4-cli php8.4-sqlite3 php8.4-mbstring php8.4-xml php8.4-intl \
        php8.4-zip php8.4-curl unzip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1
