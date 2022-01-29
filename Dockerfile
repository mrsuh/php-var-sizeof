FROM php:8.1-cli

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

RUN install-php-extensions ffi mbstring

RUN mkdir /app

RUN groupadd -r ffi && useradd -m -g ffi ffi

RUN chown -R ffi /app

USER ffi

WORKDIR /app
