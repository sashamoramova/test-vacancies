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
  <div class="page">
    <div class="hero">
      <h1>Сервис управления вакансиями</h1>
      <p>
        Посмотрите список, добавьте новую вакансию,
        откройте детальную карточку
      </p>
      <div class="hero-actions">
        <NuxtLink to="/create">Создать вакансию</NuxtLink>
        <a href="http://localhost:8080/swagger/index.html" target="_blank">Документация API (Swagger)</a>
      </div>
    </div>

    <div class="list-header">
      <h2>Список вакансий</h2>
    </div>

    <div class="filters">
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

    <div v-if="error">
      <h3>Не удалось загрузить список</h3>
      <p>
        {{ error.message || 'Проверьте, что backend запущен' }}
      </p>
      <div>
        <button @click="refresh()">Попробовать снова</button>
        <NuxtLink to="/create">Создать первую вакансию</NuxtLink>
      </div>
    </div>

    <div v-else>
      <!-- <table> -->
      <table class="vacancy-table">
           <colgroup>
            <col style="width: 53%" />
            <col style="width: 14%" />
            <col style="width: 18%" />
            <col style="width: 15%" />
           </colgroup>
        <thead>
          <tr>
            <th>Название</th>
            <th>Зарплата</th>
            <th>Дата создания</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="pending">
            <td colspan="4" class="table-state">Загрузка списка вакансий…</td>
          </tr>
          <tr v-else-if="!data || !data.items.length">
            <td colspan="4" class="table-state">
              Пока вакансий нет. <NuxtLink to="/create">Создать первую вакансию</NuxtLink>
            </td>
          </tr>
          <tr v-else v-for="v in data.items" :key="v.id">
            <td>{{ v.title }}</td>
            <td>{{ formatSalary(v.salary) }}</td>
            <td>{{ formatDate(v.created_at) }}</td>
            <td>
              <NuxtLink :to="`/vacancies/${v.id}`">Подробнее</NuxtLink>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="pagination">
        <button :disabled="page <= 1" @click="goToPage(page - 1)">← Назад</button>
        <span v-for="p in data.meta.pageCount" :key="p">
          <button :class="{ active: p === page }" @click="goToPage(p)">{{ p }}</button>
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

<style scoped>
.page,
.page div {
  box-sizing: border-box;
}
.page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 16px;
}
h1, h2, h3 {
  margin: 0 0 12px;
}
p {
  margin: 0 0 12px;
  color: #444;
}
.hero,
.list-header,
.filters,
table,
.pagination {
  margin-bottom: 14px;
}
.hero {
  padding: 14px;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  background: #fff;
}
.hero-actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}
.list-header {
  padding-top: 4px;
}
.filters {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}
.filters > div {
  min-width: 220px;
}
label {
  display: block;
  margin-bottom: 6px;
}
a {
  color: #2563eb;
  text-decoration: none;
}
a:hover {
  text-decoration: underline;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 12px;
  background: #fff;
}
th, td {
  border: 1px solid #e5e7eb;
  padding: 10px;
  text-align: left;
}
th {
  background: #f3f4f6;
}
.vacancy-table {
  width: 100%;
  table-layout: fixed;
}
.vacancy-table th,
.vacancy-table td {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.table-state {
  text-align: center;
  color: #6b7280;
  padding: 18px;
}
button, select {
  padding: 6px 10px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: #fff;
}
button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.pagination {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  align-items: center;
}
.pagination .active {
  background: #2563eb;
  color: #fff;
  border-color: #2563eb;
}
</style>