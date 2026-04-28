<template>
  <div class="app">
    <header class="app__header">
      <span>Yii2 + Nuxt3</span>
      <a
        class="app__brand"
        href="http://localhost:8080/swagger/index.html"
        target="_blank"
        >Документация API (Swagger)</a
      >
    </header>
    <main class="app__main">
      <NuxtPage />
    </main>
    <footer class="app__footer">
      <NuxtLink
        to="https://github.com/sashamoramova/test-vacancies"
        target="_blank"
        class="app__brand"
        title="GitHub"
      >
        GitHub
      </NuxtLink>
    </footer>
  </div>
</template>

<style scoped>
.app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: #f8fafc;
  color: #111827;
}

.app__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-bottom: 1px solid #e5e7eb;
  background: #ffffff;
}

.app__brand {
  font-weight: 700;
  color: #111827;
  text-decoration: none;
}

.app__nav {
  display: flex;
  gap: 10px;
}

.app__nav a {
  color: #374151;
  text-decoration: none;
  padding: 6px 10px;
  border-radius: 6px;
}

.app__nav a:hover {
  background: #f3f4f6;
}

.app__nav-active {
  background: #2563eb;
  color: #ffffff !important;
}

.app__main {
  flex: 1;
  padding: 16px 0;
}

.app__footer {
  border-top: 1px solid #e5e7eb;
  background: #ffffff;
  color: #6b7280;
  font-size: 14px;
  padding: 10px 16px;
}

.app__footer a {
  color: #2563eb;
  text-decoration: none;
}

.app__footer a:hover {
  text-decoration: underline;
}
</style>
