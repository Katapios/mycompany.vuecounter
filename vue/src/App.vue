<template>
  <div>
    <h1>{{ message }}</h1>
    <button @click="count++">Нажато {{ count }} раз</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      message: 'Привет из Vue + Bitrix!',
      count: 0
    }
  }
}
</script>