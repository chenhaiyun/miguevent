# 使用官方 PHP Apache 镜像
FROM php:8.2-apache

# 设置时区
ENV TZ=Asia/Shanghai
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# 安装必要的系统包和 PHP 扩展
RUN apt-get update && apt-get install -y \
    curl \
    libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 启用 Apache 模块
RUN a2enmod rewrite headers

# 设置工作目录
WORKDIR /var/www/html

# 复制项目文件
COPY . .

# 设置文件权限
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/logs || mkdir -p /var/www/html/logs && chmod -R 777 /var/www/html/logs

# 创建必要的目录
RUN mkdir -p /var/www/html/logs /var/www/html/cache \
    && chown -R www-data:www-data /var/www/html/logs /var/www/html/cache \
    && chmod -R 777 /var/www/html/logs /var/www/html/cache

# 复制自定义 Apache 配置
COPY docker/apache-config.conf /etc/apache2/sites-available/000-default.conf

# 暴露端口
EXPOSE 80

# 启动 Apache
CMD ["apache2-foreground"]
