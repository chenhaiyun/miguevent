# 咪咕体育解析服务 - AMD64 架构镜像使用指南

## 📦 镜像信息

- **文件名**: `miguevent-amd64.tar`
- **文件大小**: 523MB
- **镜像大小**: 538MB
- **架构**: linux/amd64 (x86_64)
- **基础镜像**: PHP 8.2 Apache
- **构建时间**: 2025-12-10

## 🔨 构建镜像

如果您想自己从源码构建AMD64架构的镜像，请按以下步骤操作：

### 前提条件
- 安装Docker Desktop或Docker Engine
- 确保Docker BuildKit可用
- 网络能够访问Docker Hub

### 构建步骤

#### 1. 克隆或下载项目源码
```bash
# 如果是Git仓库
git clone <your-repo-url>
cd miguevent

# 或者下载并解压源码包
```

#### 2. 验证Docker Buildx
```bash
# 检查buildx是否可用
docker buildx version

# 查看支持的平台
docker buildx ls
```

#### 3. 构建AMD64镜像

**方法一：构建并导出tar文件（推荐）**
```bash
# 构建并加载到本地Docker
docker buildx build --platform linux/amd64 -t miguevent:amd64 --load .

# 导出为tar文件
docker save miguevent:amd64 -o miguevent-amd64.tar

# 验证文件大小
ls -lh miguevent-amd64.tar
```

**方法二：直接使用docker build（传统方式）**
```bash
# 在AMD64系统上直接构建
docker build -t miguevent:amd64 .

# 导出镜像
docker save miguevent:amd64 -o miguevent-amd64.tar
```

**方法三：构建多架构镜像**
```bash
# 同时构建AMD64和ARM64
docker buildx build \
  --platform linux/amd64,linux/arm64 \
  -t miguevent:latest \
  --push .
```

#### 4. 验证构建结果
```bash
# 查看构建的镜像
docker images miguevent

# 测试运行
docker run --rm -p 8080:80 miguevent:amd64
```

### 构建时可能遇到的问题

#### 网络问题
```bash
# 如果网络连接有问题，可以使用国内镜像源
# 修改Dockerfile中的基础镜像源，或使用代理
docker buildx build --platform linux/amd64 -t miguevent:amd64 --load . --build-arg HTTP_PROXY=http://your-proxy:port
```

#### 平台问题
```bash
# 如果在ARM机器上构建AMD64镜像失败，确保启用了模拟
docker run --privileged --rm tonistiigi/binfmt --install all
```

#### 构建缓存清理
```bash
# 如果需要完全重新构建
docker buildx build --platform linux/amd64 -t miguevent:amd64 --load --no-cache .
```

### 自定义构建

#### 修改PHP版本
```dockerfile
# 在Dockerfile中修改基础镜像
FROM php:8.3-apache  # 改为其他版本
```

#### 添加自定义配置
```bash
# 修改docker/apache-config.conf文件
# 修改config/user.env.example文件
# 然后重新构建
docker buildx build --platform linux/amd64 -t miguevent:custom --load .
```

## 🚀 使用方法

### 1. 加载镜像到Docker

```bash
# 加载 tar 文件到 Docker
docker load < miguevent-amd64.tar

# 查看加载的镜像
docker images | grep miguevent
```

### 2. 运行容器

#### 方法一：使用 docker run 命令

```bash
# 基本运行
docker run -d \
  --name miguevent_app \
  -p 8080:80 \
  miguevent:amd64

# 完整运行（推荐）
docker run -d \
  --name miguevent_app \
  -p 8080:80 \
  -e TZ=Asia/Shanghai \
  -v ./logs:/var/www/html/logs \
  -v ./cache:/var/www/html/cache \
  -v ./config:/var/www/html/config:ro \
  --restart unless-stopped \
  miguevent:amd64
```

#### 方法二：使用现有的 docker-compose.yml

```bash
# 修改 docker-compose.yml 中的镜像名称
# 将 build: . 替换为 image: miguevent:amd64

# 然后运行
docker compose up -d
```

### 3. 配置用户信息

#### 选项A：使用配置文件（推荐）

```bash
# 创建配置目录
mkdir -p config

# 复制配置模板
cp config/user.env.example config/user.env

# 编辑配置文件
nano config/user.env
```

在 `config/user.env` 中填入：
```env
MIGU_USER_ID=你的真实userId
MIGU_USER_TOKEN=你的真实userToken
CACHE_EXPIRE_TIME=3600
RATE_LIMIT_MAX=30
LOG_LEVEL=1
```

#### 选项B：通过 URL 参数传递

直接在访问URL中添加参数：
```
http://localhost:8080/migu.php?userId=你的userId&userToken=你的userToken
```

### 4. 访问服务

```bash
# 主服务
curl http://localhost:8080/migu.php

# 精选赛事
curl http://localhost:8080/miguevent_jpid.php

# 健康检查
curl http://localhost:8080/docker/healthcheck.php
```

## 🔧 容器管理

### 查看状态
```bash
# 查看运行状态
docker ps

# 查看日志
docker logs miguevent_app

# 进入容器
docker exec -it miguevent_app bash
```

### 停止和删除
```bash
# 停止容器
docker stop miguevent_app

# 删除容器
docker rm miguevent_app

# 删除镜像
docker rmi miguevent:amd64
```

## 🌐 部署到生产环境

### Linux 服务器部署

1. **上传镜像文件**
```bash
scp miguevent-amd64.tar user@your-server:/path/to/directory/
```

2. **在服务器上加载镜像**
```bash
ssh user@your-server
docker load < /path/to/directory/miguevent-amd64.tar
```

3. **运行容器**
```bash
docker run -d \
  --name miguevent_production \
  -p 80:80 \
  -e TZ=Asia/Shanghai \
  -v /var/log/miguevent:/var/www/html/logs \
  -v /var/cache/miguevent:/var/www/html/cache \
  -v /etc/miguevent:/var/www/html/config:ro \
  --restart always \
  miguevent:amd64
```

### Docker Swarm 集群部署

```bash
# 创建 swarm 服务
docker service create \
  --name miguevent-service \
  --replicas 3 \
  -p 8080:80 \
  --mount type=volume,source=miguevent-logs,destination=/var/www/html/logs \
  --mount type=volume,source=miguevent-cache,destination=/var/www/html/cache \
  miguevent:amd64
```

### Kubernetes 部署

创建 `miguevent-deployment.yaml`:

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: miguevent
spec:
  replicas: 3
  selector:
    matchLabels:
      app: miguevent
  template:
    metadata:
      labels:
        app: miguevent
    spec:
      containers:
      - name: miguevent
        image: miguevent:amd64
        ports:
        - containerPort: 80
        env:
        - name: TZ
          value: "Asia/Shanghai"
        volumeMounts:
        - name: logs
          mountPath: /var/www/html/logs
        - name: cache
          mountPath: /var/www/html/cache
      volumes:
      - name: logs
        emptyDir: {}
      - name: cache
        emptyDir: {}
---
apiVersion: v1
kind: Service
metadata:
  name: miguevent-service
spec:
  selector:
    app: miguevent
  ports:
  - port: 80
    targetPort: 80
  type: LoadBalancer
```

应用部署：
```bash
kubectl apply -f miguevent-deployment.yaml
```

## 🔍 故障排除

### 常见问题

1. **容器无法启动**
```bash
# 查看详细错误
docker logs miguevent_app

# 检查端口占用
netstat -tulpn | grep :8080
```

2. **配置问题**
```bash
# 检查配置文件是否正确挂载
docker exec miguevent_app ls -la /var/www/html/config/

# 测试配置加载
docker exec miguevent_app php -c /var/www/html/config/config.php
```

3. **权限问题**
```bash
# 修复目录权限
sudo chown -R 82:82 logs cache
sudo chmod -R 755 logs cache
```

## 📊 性能监控

### 资源监控
```bash
# 查看容器资源使用情况
docker stats miguevent_app

# 查看镜像信息
docker inspect miguevent:amd64
```

### 日志监控
```bash
# 实时查看日志
docker logs -f miguevent_app

# 查看最近的日志
docker logs --tail 100 miguevent_app
```

## 🔒 安全建议

1. **网络安全**
   - 使用防火墙限制访问端口
   - 配置 HTTPS（使用 Nginx 反向代理）
   - 定期更新镜像

2. **配置安全**
   - 不要在镜像中硬编码敏感信息
   - 使用 Docker secrets 管理敏感配置
   - 定期轮换用户凭证

3. **运行时安全**
   - 使用非特权用户运行容器
   - 限制容器资源使用
   - 启用容器日志轮转

## 📝 版本信息

- **构建版本**: v1.0.0
- **PHP 版本**: 8.2.29
- **Apache 版本**: 2.4.65
- **支持架构**: linux/amd64
- **构建日期**: 2025-12-10

---

如果遇到任何问题，请参考主要的 `DOCKER_README.md` 文档或检查 Docker 容器日志进行排查。
