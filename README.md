# JWT Аутентификационный Микросервис на Symfony

Микросервис для аутентификации пользователей с использованием JSON Web Tokens (JWT), на фреймворке Symfony.

## Особенности

*   Реализация REST API для аутентификации.
*   Использование JWT
*   Хранение в SQLite.
*   Автоматическая генерация JWT ключей и применение миграций базы данных при первом запуске контейнера.
*   Разработано с использованием PHP 8.2, Symfony, Doctrine, LexikJWTAuthenticationBundle.
*   Готово к запуску в Docker-окружении

## Технологический стек

*   PHP 8.2+
*   Symfony 7.x
*   Doctrine ORM
*   LexikJWTAuthenticationBundle
*   SQLite
*   Docker, Docker Compose
*   Nginx (в качестве веб-сервера в Docker)

## Быстрый старт / Запуск

1. **Соберите Docker-образы:**

    ```bash
    docker-compose build
    ```

2. **Запустите контейнеры:**
    Эта команда запустит PHP-приложение (с PHP-FPM) и Nginx.
    ```bash
    docker-compose up 
    ```
*   При первом запуске `docker-entrypoint.sh` автоматически:
    *   Сгенерирует пару JWT ключей (приватный и публичный) в директории `config/jwt/` (они будут видны на хост-машине благодаря монтированию).
    Эти ключи будут использоваться для подписи и проверки JWT токенов.
    *   Применит миграции базы данных.

3. **Доступ к приложению:**
    Ммикросервис будет доступен по адресу `http://localhost:8080`

## Консольные команды (CLI)

Команды выполняются внутри PHP-контейнера.

### 1. Создание нового пользователя

*   **Команда:**
    ```bash
    docker-compose exec app php bin/console app:create-user <email> <password>
    ```
*   **Пример:**
    ```bash
    docker-compose exec app php bin/console app:create-user user@test.com password
    ```
    Эта команда создаст нового пользователя в базе данных.

## API Эндпоинты

### 1. Аутентификация пользователя и получение JWT

*   **URL:** `/api/login`
*   **Метод:** `POST`
*   **Тело запроса (Request Body - `application/json`):**
    ```json
    {
        "email": "user@test.com",
        "password": "password"
    }
    ```
*   **Успешный ответ (Success Response - `200 OK`):**
    ```json
    {
        "token": ""
    }
    ```
*   **Ответ при ошибке (Error Responses):**
    *   `400 Bad Request`
    *   `401 Unauthorized`

    **Пример использования с `curl`:**
    ```bash
    curl -X POST http://localhost:8080/api/login \
         -H "Content-Type: application/json" \
         -d '{"email":"user@test.com","password":"password"}'
    ```

### 2. Проверка валидности JWT токена

*   **URL:** `/api/verify`
*   **Метод:** `GET`
*   **Заголовки (Headers):**
    *   `Authorization: Bearer <jwt_token>`
*   **Успешный ответ (Success Response - `200 OK`):**
    ```json
    {
        "message": "Token is valid"
    }
    ```
*   **Ответ при ошибке (Error Responses):**
    *   `401 Unauthorized`

    **Пример использования с `curl`:**
    ```bash
    curl -X GET http://localhost:8080/api/verify \
         -H "Authorization: Bearer <TOKEN>"
    ```