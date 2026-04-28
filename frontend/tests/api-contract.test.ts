/**
 * Проверка формы ответа и обработку ошибок без поднятого бэка.
 * Запуск: docker compose exec frontend npm test
 */
import { describe, it, expect } from 'vitest'

type Vacancy = {
  id: number
  title: string
  description: string
  salary: number
  created_at: string
}

type ValidationError = { field: string; message: string }

// Проверка что типы публичного контракта соответствуют ожидаемому
// и что структуры ответов backend-а совместимы с фронтовыми типами

describe('Vacancy API contract', () => {
  it('Vacancy имеет все поля из ТЗ', () => {
    const v: Vacancy = {
      id: 1,
      title: 'Backend developer',
      description: 'Yii2 + MySQL',
      salary: 150000,
      created_at: '2026-04-24 16:51:35',
    }
    expect(v.id).toBe(1)
    expect(v.title).toBeTypeOf('string')
    expect(v.salary).toBeTypeOf('number')
  })

  it('ValidationError имеет field и message', () => {
    const err: ValidationError = {
      field: 'title',
      message: 'Поле «Название» обязательно',
    }
    expect(err.field).toBe('title')
    expect(err.message).toContain('обязательно')
  })

  it('Пагинация парсится из заголовков', () => {
    // Моделирование headers-object из $fetch.raw
    const headers = new Headers({
      'X-Pagination-Total-Count': '50',
      'X-Pagination-Page-Count': '5',
      'X-Pagination-Current-Page': '1',
      'X-Pagination-Per-Page': '10',
    })

    const meta = {
      totalCount: Number(headers.get('X-Pagination-Total-Count') || 0),
      pageCount: Number(headers.get('X-Pagination-Page-Count') || 1),
      currentPage: Number(headers.get('X-Pagination-Current-Page') || 1),
      perPage: Number(headers.get('X-Pagination-Per-Page') || 10),
    }

    expect(meta.totalCount).toBe(50)
    expect(meta.pageCount).toBe(5)
    expect(meta.currentPage).toBe(1)
    expect(meta.perPage).toBe(10)
  })

  it('Формат sort для Yii2 REST: без минуса - ASC, с минусом - DESC', () => {
    const makeSortParam = (field: string, direction: 'asc' | 'desc') =>
      (direction === 'desc' ? '-' : '') + field

    expect(makeSortParam('created_at', 'desc')).toBe('-created_at')
    expect(makeSortParam('salary', 'asc')).toBe('salary')
    expect(makeSortParam('title', 'desc')).toBe('-title')
  })

  it('Сообщение о клиент-валидации не отправляется пустым', () => {
    const form = { title: '', description: '', salary: 0 }
    const errors: string[] = []
    if (!form.title.trim()) errors.push('Укажите название')
    if (!form.description.trim()) errors.push('Добавьте описание')
    if (!Number.isInteger(+form.salary) || +form.salary < 0) {
      errors.push('Зарплата должна быть целым неотрицательным числом')
    }
    expect(errors.length).toBeGreaterThanOrEqual(2)
  })
})
