<script setup lang="ts">
const route = useRoute()
const api = useApi()

const id = computed(() => route.params.id as string)

// Загрузка вакансии по id
const { data: vacancy, pending, error } = await useAsyncData(
  `vacancy-${id.value}`,
  () => api.one(id.value)
)

const visibleFields = reactive<Record<string, boolean>>({
  id: true,
  title: true,
  description: true,
  salary: true,
  created_at: true,
})

const fieldLabels: Record<string, string> = {
  id: 'ID',
  title: 'Название',
  description: 'Описание',
  salary: 'Зарплата',
  created_at: 'Дата создания',
}

function formatDate(iso: string): string {
  try {
    return new Date(iso).toLocaleString('ru-RU', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    })
  } catch {
    return iso
  }
}

function formatValue(key: string, value: any): string {
  if (key === 'salary') return new Intl.NumberFormat('ru-RU').format(value) + ' ₽'
  if (key === 'created_at') return formatDate(value)
  return String(value)
}
</script>

<template>
  <div class="page">
    <nav>
      <NuxtLink to="/">Главная</NuxtLink>
      <span>›</span>
      <span>Вакансия #{{ id }}</span>
    </nav>

    <NuxtLink to="/">← Назад к списку</NuxtLink>

    <div v-if="pending">Загрузка…</div>

    <div v-else-if="error">
      <h1>Вакансия не найдена</h1>
      <p>Запрошенный ресурс не существует или был удалён.</p>
    </div>

    <div v-else-if="vacancy">
      <div class="controls">
        <strong>Показывать поля:</strong>
        <div class="checkboxes">
          <label v-for="(label, key) in fieldLabels" :key="key">
            <input type="checkbox" v-model="visibleFields[key]" />
            {{ label }}
          </label>
        </div>
      </div>

      <div>
        <h1 v-if="visibleFields.title">{{ vacancy.title }}</h1>

        <dl>
          <template v-for="(label, key) in fieldLabels" :key="key">
            <template v-if="visibleFields[key] && key !== 'title'">
              <dt>{{ label }}</dt>
              <dd>
                <template v-if="key === 'description'">
                  {{ vacancy[key] }}
                </template>
                <template v-else>
                  {{ formatValue(key, vacancy[key as keyof typeof vacancy]) }}
                </template>
              </dd>
            </template>
          </template>
        </dl>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page {
  max-width: 900px;
  margin: 0 auto;
  padding: 16px;
}
nav {
  margin-bottom: 10px;
  color: #6b7280;
}
a {
  color: #2563eb;
  text-decoration: none;
}
a:hover {
  text-decoration: underline;
}
h1 {
  margin: 10px 0 14px;
}
strong {
  display: block;
  margin-bottom: 8px;
}
.controls {
  margin-top: 12px;
  padding: 12px;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  background: #fff;
}
.checkboxes {
  display: flex;
  flex-wrap: wrap;
}
label {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin-right: 12px;
  margin-bottom: 8px;
}
dl {
  margin: 16px 0 0;
  padding: 14px;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  background: #fff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}
dt {
  font-weight: 700;
  margin-top: 10px;
}
dt:first-child {
  margin-top: 0;
}
dd {
  margin: 4px 0 0;
  color: #374151;
  white-space: pre-wrap;
}
</style>