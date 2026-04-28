<template>
    <div class="app">
      <header class="app__header">
        <NuxtLink to="/" class="app__brand" title="На главную">
          Вакансии
        </NuxtLink>
        <nav class="app__nav">
          <NuxtLink to="/" exact-active-class="app__nav-active">Список</NuxtLink>
          <NuxtLink to="/create" active-class="app__nav-active">Создать</NuxtLink>
        </nav>
      </header>
      <main class="app__main">
        <NuxtPage />
      </main>
      <footer class="app__footer">
        <span>Yii2 + Nuxt3 · <a href="/swagger" target="_blank">Документация API</a></span>
      </footer>
    </div>
  </template>
  

  