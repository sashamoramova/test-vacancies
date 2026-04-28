<script setup lang="ts">

const api = useApi()

const route = useRoute()
const router = useRouter()

const sortField = ref<string>(String(route.query.sort || 'created_at'))
const sortDir = ref<'asc' | 'desc'>(
  (route.query.direction as 'asc' | 'desc') || 'desc'
)
const page = ref<number>(Number(route.query.page || 1))
const perPage = ref<number>(10)

const { data, pending, error, refresh } = await useAsyncData(
  'vacancy-list',
  () =>
    api.list({
      sort: sortField.value,
      direction: sortDir.value,
      page: page.value,
      perPage: perPage.value,
    }),
  {
    watch: [sortField, sortDir, page],
  }
)

watch([sortField, sortDir, page], () => {
  router.push({
    query: {
      sort: sortField.value,
      direction: sortDir.value,
      page: page.value,
    },
  })
})

function toggleDir() {
  sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
}

function goToPage(p: number) {
  if (p < 1) return
  if (data.value && p > data.value.meta.pageCount) return
  page.value = p
}

function formatDate(iso: string): string {
  try {
    return new Date(iso).toLocaleString('ru-RU', {
      day: '2-digit', month: '2-digit', year: 'numeric',
      hour: '2-digit', minute: '2-digit',
    })
  } catch { return iso }
}

function formatSalary(n: number): string {
  return new Intl.NumberFormat('ru-RU').format(n) + ' ₽'
}
</script>

<template>
  <div>
    <div>
      <h1>Сервис управления вакансиями</h1>
      <p>
        Посмотрите список, добавьте новую вакансию,
        откройте детальную карточку
      </p>
      <div>
        <NuxtLink to="/create">Создать вакансию</NuxtLink>
        <a href="/swagger" target="_blank">Документация API (Swagger)</a>
      </div>
    </div>

    <div>
      <h2>Список вакансий</h2>
    </div>

    <div>
      <div>
        <label>Сортировка</label>
        <select v-model="sortField">
          <option value="created_at">Дата создания</option>
          <option value="title">Название</option>
          <option value="salary">Зарплата</option>
        </select>
      </div>
      <div>
        <label>Направление</label>
        <button @click="toggleDir">
          {{ sortDir === 'asc' ? '↑ По возрастанию' : '↓ По убыванию' }}
        </button>
      </div>
    </div>

    <div v-if="pending">
      Загрузка списка вакансий…
    </div>

    <div v-else-if="error">
      <h3>Не удалось загрузить список</h3>
      <p>
        {{ error.message || 'Проверьте, что backend запущен' }}
      </p>
      <div>
        <button @click="refresh()">Попробовать снова</button>
        <NuxtLink to="/create">Создать первую вакансию</NuxtLink>
      </div>
    </div>

    <div v-else-if="!data || !data.items.length">
      <p>Пока вакансий нет.</p>
      <NuxtLink to="/create">Создать первую вакансию</NuxtLink>
    </div>

    <div v-else>
      <table>
        <thead>
          <tr>
            <th>Название</th>
            <th>Зарплата</th>
            <th>Дата создания</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="v in data.items" :key="v.id">
            <td>{{ v.title }}</td>
            <td>{{ formatSalary(v.salary) }}</td>
            <td>{{ formatDate(v.created_at) }}</td>
            <td>
              <NuxtLink :to="`/vacancies/${v.id}`">Подробнее</NuxtLink>
            </td>
          </tr>
        </tbody>
      </table>

      <div>
        <button :disabled="page <= 1" @click="goToPage(page - 1)">← Назад</button>
        <span v-for="p in data.meta.pageCount" :key="p">
          <button @click="goToPage(p)">{{ p }}</button>
        </span>
        <button
          :disabled="page >= data.meta.pageCount"
          @click="goToPage(page + 1)"
        >Вперёд →</button>
      </div>

      <p>
        Всего вакансий: {{ data.meta.totalCount }}
      </p>
    </div>
  </div>
</template>