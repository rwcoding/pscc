# 改造 Laravel，适配 Api 开发

## 测试样例 （examples）
### 配置
```ini
# 配置 pscc.ini, swoole是可选的
[swoole]
host = 0.0.0.0
port = 9090
enable_coroutine = off
worker_num = *2
task_worker_num = 2
max_request = 10000
max_connection = 100000

[db]
host = 127.0.0.1
database = blog
username = root
password = root
```

### Swoole 下允许
```shell
php swoole.php
```

### 内置 WebServer 环境运行
```shell
cd public
php -S 127.0.0.1:8080 web.php
```

### 控制台运行
```shell
php cli.php
```