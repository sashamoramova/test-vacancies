## Стек

- **Backend**: Yii2, PHP 8.1, REST API
- **Frontend**: Nuxt 3, TypeScript
- **БД**: MySQL 8
- **Шлюз**: nginx (объединяет фронт и бек на одном порту)
- **Документация**: OpenAPI 3.0, Swagger UI
- **Тесты**: Smoke API (backend), Vitest (frontend)

## Запуск 

```bash
HTTPS:
git clone https://github.com/sashamoramova/test-vacancies.git
или SSH:
git clone git@github.com:sashamoramova/test-vacancies.git

cd test-vacancies
cp .env.example .env
docker compose up -d --build
```

## Адреса

- Backend API:  http://localhost:8080/api
- Frontend:     http://localhost:3000
- База данных:  localhost:3306
- Swagger:      http://localhost:8080/swagger/index.html

## Запуск тестов

Из корня `test-vacancies`:

1) Backend smoke API (проверка эндпоинтов)

```bash
docker compose exec backend sh tests/smoke.sh
```

2) Frontend (Vitest)
  
```bash
docker compose exec frontend npm test
```

## Примеры запросов и ответов, описание структуры проекта

см. readme_docs.md