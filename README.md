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

8 - O ```dashboard``` vai estar disponível:

![](https://raw.githubusercontent.com/CryptedSnow/hotel-api-challenge/refs/heads/main/public/img/01.png)

9 - Verifique o banco de dados criado ```hotel-api-challenge```.

![](https://raw.githubusercontent.com/CryptedSnow/hotel-api-challenge/refs/heads/main/public/img/02.png)

10 - Não existe registros nas tabelas no banco de dados ```hotel-api-challenge```, a inserção de dados será feita no próximo módulo.

![](https://raw.githubusercontent.com/CryptedSnow/hotel-api-challenge/refs/heads/main/public/img/03.png)

Caso queira desligar os container quando não estiver usando a aplicação, execute o comando:
```
docker-compose down
```

## 02. Execução das Jobs & importação de dados

1 - Execute o comando para executar as ```Jobs``` da aplicação:
```
docker compose exec app php artisan queue:work
```

2 - Repare que o processo de ```Jobs``` está em andamento, agora é possível executar a importação de dados dos arquivos ```xml``` para o banco de dados.

![](https://raw.githubusercontent.com/CryptedSnow/hotel-api-challenge/refs/heads/main/public/img/04.png)


3 - Para importar os dados dos arquivos ```xml``` para o ```MySQL```, execute a seguinte comando:

```
localhost:8000/api/start-import
```

4 - A seguinte mensagem será exibida

![](https://raw.githubusercontent.com/CryptedSnow/hotel-api-challenge/refs/heads/main/public/img/05.png)

5 - Atualize o página do banco de dados ```hotel-api-challenge``` e perceba que agora as tabelas tem registros.

![](https://raw.githubusercontent.com/CryptedSnow/hotel-api-challenge/refs/heads/main/public/img/06.png)

## 03. Testes de API's

Os principais endpoints (Endereços ```HTTP```) conhecidos são:

* ```GET```: Listagem de registros.
    - Em alguns casos, é necessário definir o ```id``` nesse endpoints para apontar o registro específico a ser listado.
* ```POST```: Inserção de registros.
    - É necessária uma estrutura ```json``` para definir os campos para criar o registro.
* ```PUT/PATCH```: Atualização de registros.
    - É necessária uma estrutura ```json``` completa (caso use ```PUT```) ou parcial (caso utilize ```PATCH```) para definir os campos a serem atualizados no registro.
    - Necessário definir o ```id``` nesse endpoints para apontar o registro específico para atualização.
* ```DELETE```: Exclusão de registros.
    - Necessário definir o ```id``` nesse endpoints para apontar o registro específico para exclusão.

Um exemplo **prático** desse endpoints será feito com uma das tabelas da aplicação, nesse exemplo vou utilizar a tabela ```customers```.

**GET: localhost:8000/api/customer**
```
// Response - Status: 200 OK
{
    "data": [
        {
            "id": 1,
            "first_name": "Bruno",
            "last_name": "Nascimento"
        },
        {
            "id": 2,
            "first_name": "Carla",
            "last_name": "Mendes"
        },
        {
            "id": 3,
            "first_name": "Daniel",
            "last_name": "Oliveira"
        },
        {
            "id": 4,
            "first_name": "Elena",
            "last_name": "Rocha"
        },
        {
            "id": 5,
            "first_name": "Fabio",
            "last_name": "Lins"
        },
        {
            "id": 6,
            "first_name": "Gisele",
            "last_name": "Souza"
        }
    ]
}
```
**POST: localhost:8000/api/customer**
```
// JSON body
{
    "first_name": "Keanu",
    "last_name": "Reeves"
}
```

```
// Response - Status: 201 Created
{
    "data": {
        "id": 7,
        "first_name": "Keanu",
        "last_name": "Reeves"
    }
}
```

**GET: localhost:8000/api/customer/id**
- Altere **id** para **7**.
```
// Response - Status: 200 OK
{
    "data": {
        "id": 7,
        "first_name": "Keanu",
        "last_name": "Reeves"
    }
}
```

**PUT: localhost:8000/api/customer/id**
- Altere **id** para **7**.
```
// JSON body
{
    "first_name": "John",
    "last_name": "Wick"
}
```

```
// Response - Status: 202 Accepted
{
    "data": {
        "id": 7,
        "first_name": "John",
        "last_name": "Wick"
    }
}
```

Ou atualizar parcialmente, use ```PATCH```:

**PATCH: localhost:8000/api/customer/id**
- Altere **id** para **7**.
```
// JSON body
{
    "last_name": "Wick"
}
```

// Response - Status: 202 Accepted
```
{
    "data": {
        "id": 7,
        "first_name": "John",
        "last_name": "Wick"
    }
}
```

**DELETE: localhost:8000/api/customer/id**
- Altere **id** para **7**.
```
{
    "message": "John Wick was deleted."
}
```

Todos os endpoints disponíveis para testes:

- Customers:
    - **GET**: ```localhost:8000/api/customer```
    - **POST**: ```localhost:8000/api/customer```
    - **GET**: ```localhost:8000/api/customer/id```
    - **PUT/PATCH**: ```localhost:8000/api/customer/id```
    - **DELETE**: ```localhost:8000/api/customer/id```

- Hotels:
    - **GET**: ```localhost:8000/api/hotel```
    - **POST**: ```localhost:8000/api/hotel```
    - **GET**: ```localhost:8000/api/hotel/id```
    - **PUT/PATCH**: ```localhost:8000/api/hotel/id```
    - **DELETE**: ```localhost:8000/api/hotel/id```

- Rooms:
    - **GET**: ```localhost:8000/api/room```
    - **POST**: ```localhost:8000/api/room```
    - **GET**: ```localhost:8000/api/room/id```
    - **PUT/PATCH**: ```localhost:8000/api/room/id```
    - **DELETE**: ```localhost:8000/api/room/id```

- Rates:
    - **GET**: ```localhost:8000/api/rate```
    - **POST**: ```localhost:8000/api/rate```
    - **GET**: ```localhost:8000/api/rate/id```
    - **PUT/PATCH**: ```localhost:8000/api/rate/id```
    - **DELETE**: ```localhost:8000/api/rate/id```

- Reservations:
    - **GET**: ```localhost:8000/api/reservation```
    - **POST**: ```localhost:8000/api/reservation```
    - **GET**: ```localhost:8000/api/reservation/id```
    - **PUT/PATCH**: ```localhost:8000/api/reservation/id```
    - **DELETE**: ```localhost:8000/api/reservation/id```

- RoomReservations:
    - **GET**: ```localhost:8000/api/room-reservation```
    - **POST**: ```localhost:8000/api/room-reservation```
    - **GET**: ```localhost:8000/api/room-reservation/id```
    - **PUT/PATCH**: ```localhost:8000/api/room-reservation/id```
    - **DELETE**: ```localhost:8000/api/room-reservation/id```

- DailyPrices:
    - **GET**: ```localhost:8000/api/daily-price```
    - **POST**: ```localhost:8000/api/daily-price```
    - **GET**: ```localhost:8000/api/daily-price/id```
    - **PUT/PATCH**: ```localhost:8000/api/daily-price/id```
    - **DELETE**: ```localhost:8000/api/daily-price/id```


## 04. Regra de disponibilidade

Foi pedido no teste que o sistema não deve permitir que um quarto seja reservado se ele já possuir uma reserva ativa no período solicitado (check-in/check-out).

Então criei alguns registros em algumas tabelas para executar testes na tabela ```room_reservations```. Uma forma didática de entender como funciona a aplicação funciona

- customers 

| id | first_name | last_name |
|----|------------|-----------|
| 7  | Al Pacino  | Reeves    |

- hotels

| id      | name              |
|---------|-------------------|
| 1375991 | Emporio Acapulco  |

- rooms

| id        | hotel_id  | name        | inventory_count |
|-----------|-----------|-------------|-----------------|
| 137598825 | 1375991   | First class | 15              |

- rates

| id       | hotel_id  | name         | active | price |
|----------|-----------|--------------|--------|-------|
| 62740215 | 1375991   | Classic Rate | 1      | 150   |

- reservations

| id         | hotel_id | customer_id | date       | time     |
|------------|----------|-------------|------------|----------|
| 3820212530 | 1375991  |      7      | 18-03-2026 | 11:15:00 |


**POST: localhost:8000/api/room-reservation**
```
// JSON body
{
    "reservation_id": 3820212530,
    "room_id": 137598825,
    "arrival_date": "2025-04-07",
    "departure_date": "2025-04-24",
    "currencycode": "BRL",
    "meal_plan": "Breakfast included.",
    "guest_counts": [
        {
            "type": "adult",
            "count": 1
        }
    ],
    "totalprice": "1500.00"
}
```

```
// Response - Status: 201 Created
{
    "data": {
        "id": 3641632093,
        "reservation_id": 3820212530,
        "room_id": "137598825",
        "arrival_date": "07-04-2025",
        "departure_date": "24-04-2025",
        "currencycode": "BRL",
        "meal_plan": "Breakfast included.",
        "guest_counts": [
            {
                "type": "adult",
                "count": 1
            }
        ],
        "totalprice": "1500.00"
    }
}
```

A pergunta é: **Quando essa mensagem de disponibilidade do quarto deve ser disparada**?
```
{
    "errors": {
        "period": [
            "O quarto já está reservado para o período solicitado."
        ]
    }
}
```
