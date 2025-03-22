FROM nginx:latest

# Установка Certbot и зависимостей
RUN apt-get update && \
    apt-get install -y certbot python3-certbot-nginx && \
    apt-get clean

# Команда для запуска Nginx и Certbot
CMD ["sh", "-c", "nginx -g 'daemon off;'"]
