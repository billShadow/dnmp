# dnmp

image：指定使用的镜像

container_name：给容器取名字【运行多个docker-compose不可重复取名】

volumes：卷挂载路径，定义宿主机的目录/文件和容器的目录/文件的映射  宿主机路径:容器路径

depend_on：规定service加载顺序，例如上面的配置nginx依赖php先启动

ports：映射端口  	[本机端口:容器端口]

environment：

privileged: true ：开启特权模式

networks：配置使用的网络


docker network ls #查看网络
docker network create --driver=bridge we_network
