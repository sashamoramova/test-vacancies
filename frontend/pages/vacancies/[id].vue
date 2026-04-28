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
  updated_at: true,
})

const fieldLabels: Record<string, string> = {
  id: 'ID',
  title: 'Название',
  description: 'Описание',
  salary: 'Зарплата',
  created_at: 'Дата создания',
  updated_at: 'Дата обновления',
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
  if (key === 'created_at' || key === 'updated_at') return formatDate(value)
  return String(value)
}
</script>

<template>
  <div>
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
      <div>
        <strong>Показывать поля:</strong>
        <div>
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