# php-swoole
FROM php:7.1-cli
MAINTAINER Xinyu Jiang 251413215@qq.com

#调整时区
RUN /bin/cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime \
    && echo 'Asia/Shanghai' > /etc/timezone

# 构建swoole环境，在这里安装了php,swoole,protobuf,composer,安装扩展：phpredis,msgpack,inotify,igbinary,ds,zip opcache bcmath pdo_mysql
RUN apt-get update && apt-get install -y \
	curl \
	zlib1g-dev \
	vim \
	libssl-dev \
	libpcre3 \
	libpcre3-dev \
	libnghttp2-dev \
	unzip \
	wget \
	make \
	supervisor \
	--no-install-recommends \
	&& docker-php-ext-install zip opcache bcmath pdo_mysql \
	&& cd /home && rm -rf temp && mkdir temp && cd temp \
	&& wget https://github.com/swoole/swoole-src/archive/v4.2.6.tar.gz \
	https://github.com/redis/hiredis/archive/v0.13.3.tar.gz \
	https://github.com/phpredis/phpredis/archive/3.1.3.tar.gz \
	https://github.com/msgpack/msgpack-php/archive/msgpack-2.0.2.tar.gz \
	https://github.com/arnaud-lb/php-inotify/archive/2.0.0.tar.gz \
	http://pecl.php.net/get/igbinary-2.0.5.tgz \
	http://pecl.php.net/get/ds-1.2.5.tgz \
	#编译protobuf
	https://github.com/google/protobuf/releases/download/v2.6.1/protobuf-2.6.1.tar.gz \
	https://github.com/allegro/php-protobuf/archive/v0.12.3.tar.gz \	
	#解压安装包
	&& tar -xzvf 3.1.3.tar.gz \
	&& tar -xzvf v0.13.3.tar.gz \
	&& tar -xzvf v4.2.6.tar.gz \
	&& tar -xzvf msgpack-2.0.2.tar.gz \
	&& tar -xzvf 2.0.0.tar.gz \
	&& tar -xzvf igbinary-2.0.5.tgz \
	&& tar -xzvf ds-1.2.5.tgz \
	&& tar -xzvf protobuf-2.6.1.tar.gz \
	&& tar -xzvf v0.12.3.tar.gz \
	#源码编译protobuf
	&& cd /home/temp/protobuf-2.6.1 \
	&& ./configure --prefix=/usr/local/protobuf \
	&& make && make install \
	&& cp /usr/local/protobuf/bin/protoc /usr/local/bin/ \
	#源码编译hireids
	&& cd /home/temp/hiredis-0.13.3 \
	&& make -j && make install && ldconfig \
	#源码编译swoole, 注意先往环境请去除--enable-swoole-debug配置
	&& cd /home/temp/swoole-src-4.2.6 \
	&& phpize && ./configure --enable-mysqlnd --enable-openssl \
	&& make && make install \
	#源码编译安装inotify
	&& cd /home/temp/php-inotify-2.0.0 \
	&& phpize \
	&& ./configure \
	&& make &&  make install \
	#源码编译安装DS
	&& cd /home/temp/ds-1.2.5 \
	&& phpize \
	&& ./configure \
	&& make &&  make install \	
	#源码编译安装igbinary
	&& cd /home/temp/igbinary-2.0.5 \
	&& phpize \
	&& ./configure \
	&& make &&  make install \	
	#源码编译phpreids
	&& cd /home/temp/phpredis-3.1.3 \
	&& phpize \
	&& ./configure --enable-redis-igbinary \
	&& make &&  make install \
	#源码编译msgpack
	&& cd /home/temp/msgpack-php-msgpack-2.0.2 \
	&& phpize \
	&& ./configure \
	&& make &&  make install \	
	#源码编译protobuf
	&& cd /home/temp/php-protobuf-0.12.3 \
	&& phpize \
	&& ./configure \
	&& make &&  make install \
	&& cd /home/temp \
	&& php -r"copy('https://getcomposer.org/installer','composer-setup.php');" \
	&& php composer-setup.php --install-dir=/usr/bin --filename=composer \
	#保存protobuf目录，生成protobuf文件需要里面源码，进行composer install, 生成protobuff类文件命令：php /usr/local/bin/php-protobuf/protoc-gen-php.php /apps/protobuf/Person.proto
	&& mkdir /apps && cp -r /home/temp/php-protobuf-0.12.3 /apps/php-protobuf \
	&& cd /apps/php-protobuf && composer install \
	#删除编译源码包
	&& rm -rf /home/temp \
	&& cd /usr/local/etc/php/conf.d/ \
	&& echo extension=igbinary.so>igbinary.ini \
	&& echo extension=redis.so>redis.ini \
	&& echo extension=inotify.so>inotify.ini \
	&& echo extension=swoole.so>swoole.ini \
	&& echo extension=msgpack.so>msgpack.ini \
	&& echo extension=ds.so>ds.ini \
	&& echo extension=protobuf.so>ds.ini \
	#添加系统配置，例如php.ini,opcache-recommended.ini
	&& echo memory_limit = 1024 >> php.ini \ 
	&& echo Mdata.timezone = "Asia/Shanghai" >> php.ini \
	&& echo opcache.memory_consumption=128 >> opcache-recommended.ini \
	&& echo opcache.interned_strings_buffer=8 >> opcache-recommended.ini \
	&& echo opcache.max_accelerated_files=4000 >> opcache-recommended.ini \
	&& echo opcache.revalidate_freq=60 >> opcache-recommended.ini \
	&& echo opcache.fast_shutdown=4 >> opcache-recommended.ini \
	&& echo opcache.enable_cli=1 >> opcache-recommended.ini \
	&& composer config -g repo.packagist composer https://packagist.phpcomposer.com \
	&& mkdir -p /var/log/supervisor \
	&& apt-get autoclean \
    && apt-get autoremove \
    && rm -rf /var/lib/apt/lists/*

#WORKDIR /apps	
#添加服务器代码目录到容器里
#VOLUME /apps /apps
#暴露端口
#EXPOSE 80 9502
#启动代码
#CMD ["/bin/bash"]
