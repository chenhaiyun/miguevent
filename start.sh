#!/bin/bash

# 咪咕体育解析服务 - 启动脚本
# 使用此脚本快速启动Docker服务

echo "🚀 正在启动咪咕体育解析服务..."

# 检查Docker是否运行
if ! docker info > /dev/null 2>&1; then
    echo "❌ 错误：Docker未运行或未安装"
    echo "请先启动Docker服务"
    exit 1
fi

# 检查是否存在用户配置
if [ ! -f "config/user.env" ]; then
    echo "⚠️  警告：未找到用户配置文件"
    echo "请先复制并编辑配置文件："
    echo "  cp config/user.env.example config/user.env"
    echo "  然后编辑 config/user.env 填入您的咪咕会员信息"
    echo ""
    echo "或者您也可以直接修改 migu.php 和 miguevent_jpid.php 文件中的用户信息"
    echo ""
fi

# 创建必要的目录
echo "📁 创建日志和缓存目录..."
mkdir -p logs cache

# 停止现有服务（如果有）
echo "🛑 停止现有服务..."
docker compose down 2>/dev/null || true

# 构建并启动服务
echo "🏗️  构建Docker镜像..."
docker compose build

echo "🎬 启动服务容器..."
docker compose up -d

# 等待服务启动
echo "⏳ 等待服务启动..."
sleep 5

# 检查服务状态
if docker compose ps | grep -q "Up"; then
    echo ""
    echo "✅ 服务启动成功！"
    echo ""
    echo "📝 访问地址："
    echo "   主服务: http://localhost:8080/migu.php"
    echo "   精选赛事: http://localhost:8080/miguevent_jpid.php"
    echo ""
    echo "📋 管理命令："
    echo "   查看日志: docker compose logs -f"
    echo "   停止服务: docker compose down"
    echo "   重启服务: docker compose restart"
    echo ""
    echo "💡 更多信息请查看 DOCKER_README.md"
else
    echo ""
    echo "❌ 服务启动失败！"
    echo "请查看日志："
    echo "   docker compose logs"
fi
