# fszek_figyelo
This webapplication is to set alert for books getting available in the Metropolitan Ervin Szabó Library Budapest (Fővárosi Szabó Ervin Könyvtár)

## Branches

I'm working on dev, pushing to staging for testing and the master branch is instantly pushed to prod.

## Development

The development can be done using Docker. First setup a mysql container then the laravel app itself.

### Create a docker network

```
docker network create fszekfigyelo_network
```

### Mysql container setup

Download the container:

```
docker pull mysql/mysql-server:5.6
```

Run the container:

```
docker run --name=fszekfigyelo_mysql --network fszekfigyelo_network -e MYSQL_ROOT_PASSWORD=fszekfigyelorootpass -e MYSQL_ROOT_HOST=% -e MYSQL_DATABASE=fszekfigyelo -d mysql/mysql-server:5.6
```

### Fszekfigyelo container setup

```
git clone https://github.com/markszabo/fszekfigyelo
cd fszekfigyelo
mv .env_dev .env
#Update SQL password in .env if you used something else
sudo sh -c 'echo "127.0.0.1 fszekfigyelo.dev" >> /etc/hosts'
docker build --network fszekfigyelo_network -t fszekfigyelo .
docker run --name=fszekfigyelo -p 8181:8181 -v ${PWD}:/app/fszekfigyelo -e USERID=$UID --network fszekfigyelo_network -d fszekfigyelo
```

And fszekfigyelo should be available on http://fszekfigyelo.dev:8181/.

### Stop the containers

```
docker stop fszekfigyelo_mysql fszekfigyelo
```

### Start the containers

```
docker start fszekfigyelo_mysql fszekfigyelo
```

### Shell on the laravel container

```
docker exec -i -t fszekfigyelo /bin/bash
```

## Links

Useful links for development:

* Testing: https://github.com/laravel/browser-kit-testing
* PHPUnit assertions: https://phpunit.readthedocs.io/en/7.1/assertions.html
* http-mock: https://github.com/InterNations/http-mock (used to setup a mock http server for testing to be used instead of the real http://saman.fszek.hu)
