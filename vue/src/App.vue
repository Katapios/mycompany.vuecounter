<template>
  <div class="crm-deals">
    <h2>Сделки CRM</h2>

    <div v-if="loading">Загрузка сделок...</div>
    <div v-if="error" class="error">{{ error }}</div>

    <ul v-if="!loading && !error">
      <li v-for="deal in deals" :key="deal.ID">
        <strong>{{ deal.TITLE }}</strong><br>
        Сумма: {{ deal.OPPORTUNITY }} {{ deal.CURRENCY_ID }}<br>
        Этап: {{ deal.STAGE_ID }}
      </li>
    </ul>

    <p v-if="!loading && !error && deals.length === 0">Нет сделок.</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const deals = ref([]);
const loading = ref(true);
const error = ref(null);

onMounted(() => {
  BX.ajax.runComponentAction('mycompany:vuecounter', 'getDeals', {
    mode: 'class'
  }).then(response => {
    if (response.data.success) {
      deals.value = response.data.deals;
    } else {
      error.value = 'Ошибка получения сделок';
    }
  }, err => {
    console.error('Ошибка связи:', err);
    error.value = 'Ошибка связи с сервером';
  }).then(() => {
    loading.value = false;
  });
});
</script>


<style scoped>
.crm-deals {
  max-width: 600px;
  margin: 20px auto;
  font-family: Arial, sans-serif;
}
.error {
  color: red;
  text-align: center;
  font-size: 18px;
  padding: 20px;
}
</style>
