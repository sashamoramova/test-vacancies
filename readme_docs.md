## Примеры запросов и ответов

### 1) Получение списка вакансий

Запрос:

```bash
curl -X GET "http://localhost:8080/api/vacancies?sort=-created_at&page=1&per-page=10"
```

Ответ (200 OK):

```json
[
  {
    "id": 1,
    "title": "Backend developer",
    "description": "Yii2 + MySQL",
    "salary": 150000,
    "created_at": "2026-04-24 16:51:35",
    "updated_at": "2026-04-24 16:51:35"
  }
]
```

Заголовки пагинации:

- `X-Pagination-Total-Count`
- `X-Pagination-Page-Count`
- `X-Pagination-Current-Page`
- `X-Pagination-Per-Page`

### 2) Получение вакансии по ID

Запрос:

```bash
curl -X GET "http://localhost:8080/api/vacancies/1"
```

Ответ (200 OK):

```json
{
  "id": 1,
  "title": "Backend developer",
  "description": "Yii2 + MySQL",
  "salary": 150000,
  "created_at": "2026-04-24 16:51:35",
  "updated_at": "2026-04-24 16:51:35"
}
```

Ответ при отсутствии записи (404 Not Found):

```json
{
  "name": "Not Found",
  "message": "Object not found: 1",
  "code": 0,
  "status": 404
}
```

### 3) Создание вакансии

Запрос:

```bash
curl -X POST "http://localhost:8080/api/vacancies" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Frontend developer",
    "description": "Nuxt 3 + TypeScript",
    "salary": 180000
  }'
```

Ответ (201 Created):

```json
{
  "id": 2,
  "title": "Frontend developer",
  "description": "Nuxt 3 + TypeScript",
  "salary": 180000,
  "created_at": "2026-04-24 17:05:10",
  "updated_at": "2026-04-24 17:05:10"
}
```

Ответ при ошибке валидации (422 Unprocessable Entity):

```json
[
  {
    "field": "salary",
    "message": "Зарплата не должна превышать 10 000 000"
  }
]
```

## Описание структуры проекта

```text
test-vacancies/
├─ backend/                 # Yii2 REST API
│  ├─ config/               # Конфигурация приложения
│  ├─ controllers/          # REST-контроллеры (VacancyController и др.)
│  ├─ models/               # Модели ActiveRecord и валидация
│  ├─ migrations/           # Миграции БД
│  ├─ tests/                # Smoke-тесты API
│  └─ web/swagger/          # OpenAPI спецификация и Swagger UI
│
├─ frontend/                # Nuxt 3 приложение
│  ├─ pages/                # Страницы: список, создание, деталка
│  ├─ composables/          # API-клиент и типы
│  ├─ tests/                # Vitest тесты frontend
│  └─ nuxt.config.ts        # Конфигурация Nuxt
│
├─ nginx.conf               # Роутинг фронта, API и /swagger
├─ docker-compose.yml       # Единый запуск всех сервисов
├─ .env.example             # Пример переменных окружения
└─ README.md                # Инструкция по запуску и описания
```
