## 01. Preparação do ambiente 

Estou utilizando **Docker** para criar um ambiente de containers de serviços em minha máquina local, é necessário seguir os comandos para que a aplicação funcione corretamente em seu computador.

1 - Execute os containers:
```
docker-compose up -d
```

2 - Execute o ```composer``` para criar a pasta ```vendor``` da aplicação:
```
docker-compose exec app composer install
```

3 - Crie o arquivo ```.env```:
```
docker-compose exec app cp .env.example .env  
```

4 - Crie a chave encriptada que vai preencher o ```APP_KEY=``` do arquivo ```.env```:
```
docker-compose exec app php artisan key:generate
```

5 - No arquivo ```.env``` copie o seguinte trecho de código para conectar a aplicação ao banco de dados **MySQL** que o Docker está executando em um de seus containers:

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=hotel-api-challenge
DB_USERNAME=user
DB_PASSWORD=password
```

6 - Para criar as ```migrations``` da aplicação, execute o seguinte comando:
```
docker-compose exec app php artisan migrate
```

7 - Para usar o ```phpMyAdmin``` executado pelo Docker, você pode acessar o seguinte link:
```
http://localhost:8081
```

8 - A dashboard vai estar disponível:

(Foto)

9 - Verifique o banco de dados criado ```hotel-api-challenge```.

(Foto)

10 - Não existe registros nas tabelas no banco de dados ```hotel-api-challenge```, a inserção de dados será feita no próximo módulo.

(Foto)

Caso queira desligar os container quando não estiver usando a aplicação, execute o comando:
1 - Execute os containers:
```
docker-compose down
```

## 02. Execução das Jobs & importação de dados

1 - Execute o comando para executar as ```Jobs``` da aplicação:
```
docker compose exec app php artisan queue:work
```

2 - Repare que o processo de ```Jobs``` está em andamento, agora é possível executar a importação de dados dos arquivos ```xml``` para o banco de dados.

(Foto)

3 - Para importar os dados dos arquivos ```xml``` para o ```MySQL```, execute a seguinte comando:

```
localhost:8000/api/start-import
```

4 - A seguinte mensagem será exibida

(Foto)

5 - Atualize o página do banco de dados ```hotel-api-challenge``` e perceba que agora as tabelas tem registros.

(Foto)
