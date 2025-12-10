# 咪咕体育解析服务 - Docker 部署指南

本指南将帮助您使用 Docker 快速部署咪咕体育解析服务。

## 🚀 快速开始

### 前提条件
- 已安装 Docker 和 Docker Compose
- 拥有咪咕会员账户和相应的 userId、userToken
- 确保网络环境可以访问咪咕服务（建议使用国内服务器）

### 1. 克隆项目
```bash
git clone <your-repo-url>
cd miguevent
```

### 2. 配置用户信息

#### 方法一：使用环境配置文件（推荐）
```bash
# 复制配置模板
cp config/user.env.example config/user.env

# 编辑配置文件，填入您的咪咕会员信息
nano config/user.env
```

在 `config/user.env` 中填入：
```env
MIGU_USER_ID=你的真实userId
MIGU_USER_TOKEN=你的真实userToken
```

#### 方法二：直接修改 PHP 文件
编辑 `migu.php` 和 `miguevent_jpid.php` 文件中的以下行：
```php
$defaultUserId    = "你的真实userId";
$defaultUserToken = "你的真实userToken";
```

### 3. 启动服务

使用 Docker Compose 启动：
```bash
# 构建并启动服务
docker-compose up -d

# 查看运行状态
docker-compose ps

# 查看日志
docker-compose logs -f miguevent
```

### 4. 访问服务

服务启动后，可通过以下地址访问：

#### 基础访问地址
```
http://localhost:8080/migu.php
http://localhost:8080/miguevent_jpid.php
```

#### 带参数访问（如果未在配置文件中设置）
```
http://localhost:8080/migu.php?userId=你的userId&userToken=你的userToken
http://localhost:8080/miguevent_jpid.php?userId=你的userId&userToken=你的userToken
```

#### 获取具体节目播放链接
```
http://localhost:8080/migu.php?id=节目ID&userId=你的userId&userToken=你的userToken
```

## 📋 服务管理

### 常用命令
```bash
# 启动服务
docker-compose up -d

# 停止服务
docker-compose down

# 重启服务
docker-compose restart

# 查看日志
docker-compose logs -f

# 进入容器调试
docker-compose exec miguevent bash

# 更新镜像
docker-compose pull
docker-compose up -d
```

### 健康检查
Docker Compose 配置了健康检查，会定期检查服务状态：
```bash
# 查看健康状态
docker-compose ps
```

## 📁 目录结构

```
miguevent/
├── Dockerfile                 # Docker 镜像构建文件
├── docker-compose.yml         # Docker Compose 配置
├── .dockerignore             # Docker 忽略文件
├── docker/
│   └── apache-config.conf    # Apache 自定义配置
├── config/
│   └── user.env.example      # 用户配置模板
├── logs/                     # 日志目录（持久化）
├── cache/                    # 缓存目录（持久化）
├── migu.php                  # 主要解析脚本
├── miguevent_jpid.php        # 精选赛事解析脚本
└── DOCKER_README.md          # 本文档
```

## 🔧 高级配置

### 端口配置
默认服务运行在 8080 端口，可在 `docker-compose.yml` 中修改：
```yaml
ports:
  - "8080:80"  # 修改为其他端口，如 "9000:80"
```

### 环境变量配置
在 `docker-compose.yml` 中可以设置更多环境变量：
```yaml
environment:
  - TZ=Asia/Shanghai          # 时区设置
  - PHP_MEMORY_LIMIT=256M     # PHP 内存限制
  - PHP_MAX_EXECUTION_TIME=300 # PHP 执行时间限制
```

### 数据持久化
项目配置了以下目录的数据持久化：
- `./logs` - 访问日志和错误日志
- `./cache` - 缓存文件
- `./config` - 配置文件

## 🛡️ 安全建议

1. **修改默认端口**：避免使用默认的 8080 端口
2. **网络访问控制**：建议配置防火墙规则，限制访问来源
3. **定期更新**：定期更新 Docker 镜像和代码
4. **日志监控**：定期检查日志文件，发现异常访问

## 📊 监控和日志

### 查看访问日志
```bash
# 查看 Docker 日志
docker-compose logs -f miguevent

# 查看应用日志（如果挂载了日志目录）
tail -f logs/url_log.txt
```

### 性能监控
```bash
# 查看容器资源使用情况
docker stats miguevent_app
```

## 🐛 故障排除

### 常见问题

1. **服务无法启动**
   ```bash
   # 检查端口占用
   netstat -tulpn | grep :8080
   
   # 查看详细错误信息
   docker-compose logs miguevent
   ```

2. **无法访问咪咕服务**
   - 检查网络连接
   - 确认 userId 和 userToken 正确
   - 验证会员状态是否正常

3. **缓存问题**
   ```bash
   # 清理缓存
   rm -rf cache/*
   docker-compose restart
   ```

4. **权限问题**
   ```bash
   # 修复目录权限
   chmod -R 777 logs cache
   ```

### 调试模式
```bash
# 以调试模式运行容器
docker-compose exec miguevent bash

# 手动测试 PHP 脚本
curl "http://localhost/migu.php"
```

## 📝 更新日志

- **v1.0.0** - 初始 Docker 化版本
  - 支持 Docker Compose 一键部署
  - 配置数据持久化
  - 添加健康检查
  - 优化 Apache 和 PHP 配置

## 🤝 贡献

如果您在使用过程中遇到问题或有改进建议，欢迎提交 Issue 或 Pull Request。

## ⚠️ 免责声明

请遵守相关法律法规，仅用于学习和测试用途。使用本服务所产生的任何风险由使用者自行承担。
