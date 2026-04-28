<script setup lang="ts">
const api = useApi();

const route = useRoute();
const router = useRouter();

const sortField = ref<string>(String(route.query.sort || "created_at"));
const sortDir = ref<"asc" | "desc">(
  (route.query.direction as "asc" | "desc") || "desc",
);
const page = ref<number>(Number(route.query.page || 1));
const perPage = ref<number>(10);

const { data, pending, error, refresh } = await useAsyncData(
  "vacancy-list",
  () =>
    api.list({
      sort: sortField.value,
      direction: sortDir.value,
      page: page.value,
      perPage: perPage.value,
    }),
  {
    watch: [sortField, sortDir, page],
  },
);

watch([sortField, sortDir, page], () => {
  router.push({
    query: {
      sort: sortField.value,
      direction: sortDir.value,
      page: page.value,
    },
  });
});

function toggleDir() {
  sortDir.value = sortDir.value === "asc" ? "desc" : "asc";
}

function goToPage(p: number) {
  if (p < 1) return;
  if (data.value && p > data.value.meta.pageCount) return;
  page.value = p;
}

function formatDate(iso: string): string {
  try {
    return new Date(iso).toLocaleString("ru-RU", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  } catch {
    return iso;
  }
}

function formatSalary(n: number): string {
  return new Intl.NumberFormat("ru-RU").format(n) + " ₽";
}
</script>

<template>
  <div class="page">
    <div class="hero">
      <h1 class="hero__title">Сервис управления вакансиями</h1>
      <p class="hero__text">
        Посмотрите список, добавьте новую вакансию, откройте детальную карточку
      </p>
      <div class="hero__actions">
        <button class="btn btn--primary" @click="router.push('/create')">
          Создать вакансию
        </button>
        <!-- <a class="link" href="http://localhost:8080/swagger/index.html" target="_blank">Документация API (Swagger)</a> -->
      </div>
    </div>

    <div class="list-header">
      <h2 class="section-title">Список вакансий</h2>
    </div>

    <div class="filters">
      <div class="filters__group">
        <label class="filters__label">Сортировка</label>
        <select v-model="sortField">
          <option value="created_at">Дата создания</option>
          <option value="title">Название</option>
          <option value="salary">Зарплата</option>
        </select>
      </div>
      <div class="filters__group">
        <label class="filters__label">Направление</label>
        <button @click="toggleDir">
          {{ sortDir === "asc" ? "↑ По возрастанию" : "↓ По убыванию" }}
        </button>
      </div>
    </div>

    <div v-if="error">
      <h3 class="section-title">Не удалось загрузить список</h3>
      <p class="muted-text">
        {{ error.message || "Проверьте, что backend запущен" }}
      </p>
      <div>
        <button @click="refresh()">Попробовать снова</button>
        <NuxtLink class="link" to="/create">Создать первую вакансию</NuxtLink>
      </div>
    </div>

    <div v-else>
      <!-- <table> -->
      <table class="vacancy-table">
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
              Пока вакансий нет.
              <NuxtLink class="link" to="/create"
                >Создать первую вакансию</NuxtLink
              >
            </td>
          </tr>
          <tr v-else v-for="v in data.items" :key="v.id">
            <td>{{ v.title }}</td>
            <td>{{ formatSalary(v.salary) }}</td>
            <td>{{ formatDate(v.created_at) }}</td>
            <td>
              <NuxtLink class="link" :to="`/vacancies/${v.id}`"
                >Подробнее</NuxtLink
              >
            </td>
          </tr>
        </tbody>
      </table>

      <div class="pagination">
        <button :disabled="page <= 1" @click="goToPage(page - 1)">
          ← Назад
        </button>
        <span v-for="p in data.meta.pageCount" :key="p">
          <button :class="{ active: p === page }" @click="goToPage(p)">
            {{ p }}
          </button>
        </span>
        <button
          :disabled="page >= data.meta.pageCount"
          @click="goToPage(page + 1)"
        >
          Вперёд →
        </button>
      </div>

      <p class="muted-text">Всего вакансий: {{ data.meta.totalCount }}</p>
    </div>
  </div>
</template>

<style scoped>
.page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 16px;
}
.page * {
  box-sizing: border-box;
}
.hero {
  margin-bottom: 14px;
  padding: 14px;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  background: #fff;
}
.hero__title {
  margin: 0 0 12px;
}
.hero__text {
  margin: 0 0 12px;
  color: #444;
}
.hero__actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}
.list-header {
  margin-bottom: 14px;
  padding-top: 4px;
}
.section-title {
  margin: 0 0 12px;
}
.filters {
  margin-bottom: 14px;
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}
.filters__group {
  min-width: 220px;
}
.filters__label {
  display: block;
  margin-bottom: 6px;
}
.link {
  color: #2563eb;
  text-decoration: none;
}
.link:hover {
  text-decoration: underline;
}
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 8px 14px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  border: 1px solid transparent;
}
.btn--primary {
  background: #2563eb;
  color: #fff;
}
.btn--primary:hover {
  background: #1d4ed8;
  text-decoration: none;
}
.muted-text {
  margin: 0 0 12px;
  color: #444;
}
.vacancy-table {
  margin-top: 12px;
  margin-bottom: 14px;
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  table-layout: fixed;
}
.vacancy-table th,
.vacancy-table td {
  border: 1px solid #e5e7eb;
  height: 40px;
  padding: 0 10px;
  line-height: 40px;
  text-align: left;
}
.vacancy-table th {
  background: #f3f4f6;
}
.vacancy-table thead,
.vacancy-table tbody tr {
  display: table;
  width: 100%;
  table-layout: fixed;
}
.vacancy-table tbody {
  display: block;
  height: 420px;
  overflow-y: auto;
  overflow-x: hidden;
}
.vacancy-table th:nth-child(1),
.vacancy-table td:nth-child(1) {
  width: 53%;
}
.vacancy-table th:nth-child(2),
.vacancy-table td:nth-child(2) {
  width: 14%;
}
.vacancy-table th:nth-child(3),
.vacancy-table td:nth-child(3) {
  width: 18%;
}
.vacancy-table th:nth-child(4),
.vacancy-table td:nth-child(4) {
  width: 15%;
}
.vacancy-table th,
.vacancy-table td {
  box-sizing: border-box;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.table-state {
  height: 40px;
  line-height: 40px;
  text-align: center;
  color: #6b7280;
  padding: 0 10px;
}
button,
select {
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
  margin-bottom: 14px;
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
