<script setup lang="ts">
import type { ValidationErrors } from '~/composables/useApi'

const api = useApi()
const router = useRouter()

const form = reactive({
  title: '',
  description: '',
  salary: 0,
})

const errors = ref<ValidationErrors>([])
const submitting = ref(false)
const serverError = ref<string | null>(null)

function validateClient(): string[] {
  const msgs: string[] = []
  if (!form.title.trim()) msgs.push('Укажите название вакансии')
  if (form.title.length > 255) msgs.push('Название слишком длинное (макс. 255 символов)')
  if (!form.description.trim()) msgs.push('Добавьте описание')
  if (!Number.isInteger(+form.salary) || +form.salary < 0) msgs.push('Зарплата должна быть целым неотрицательным числом')
  return msgs
}

async function onSubmit() {
  errors.value = []
  serverError.value = null

  const clientIssues = validateClient()
  if (clientIssues.length) {
    errors.value = clientIssues.map((msg) => ({ field: '', message: msg }))
    return
  }

  submitting.value = true
  try {
    const result = await api.create({
      title: form.title.trim(),
      description: form.description.trim(),
      salary: Number(form.salary),
    })

    if (result.errors && result.errors.length) {
      errors.value = result.errors
      return
    }

    if (result.model) {
      // Успех - редирект на деталку новой вакансии
      await router.push(`/vacancies/${result.model.id}`)
    }
  } catch (e: any) {
    serverError.value = e?.message || 'Не удалось создать вакансию'
  } finally {
    submitting.value = false
  }
}

function cancel() {
  router.push('/')
}
</script>

<template>
  <div>
    <nav class="breadcrumbs">
      <NuxtLink to="/">Главная</NuxtLink>
      <span>›</span>
      <span>Новая вакансия</span>
    </nav>
    <h1>Новая вакансия</h1>

    <form class="card" @submit.prevent="onSubmit">
      <div class="field">
        <label for="title">Название *</label>
        <input id="title" v-model="form.title" type="text" required />
      </div>

      <div class="field">
        <label for="description">Описание *</label>
        <textarea id="description" v-model="form.description" rows="6" required></textarea>
      </div>

      <div class="field">
        <label for="salary">Зарплата, ₽ *</label>
        <input id="salary" v-model.number="form.salary" type="number" min="0" step="1000" required />
      </div>

      <div v-if="errors.length" class="error-list">
        <p v-for="(e, i) in errors" :key="i" class="error">
          {{ e.field ? e.field + ': ' : '' }}{{ e.message }}
        </p>
      </div>

      <p v-if="serverError" class="error">{{ serverError }}</p>

      <div class="actions">
        <button type="submit" :disabled="submitting">
          {{ submitting ? 'Создание…' : 'Создать' }}
        </button>
        <button type="button" class="btn btn--ghost" @click="cancel">Отмена</button>
      </div>
    </form>
  </div>
</template>