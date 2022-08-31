#### Composer 管理我的 CSS、JS、Image等静态资源

> 安装： composer require longzy/test

### 默认安装目录为 /Plugin/test/static/ ,可以在 composer.json 添加以下配置来指定安装位置

````
 "extra": {
        "installer-paths": {
            "youPath/{$name}/": ["longzy/test"]
        }
},
//以下参数可以不添加，反正你在composer 的时候会提示是开启
"config": {
        "allow-plugins": {
            "composer/installers": true
        }
}
````

> installer-paths 安装参数具体查看文档：[installers类库](https://github.com/composer/installers)



