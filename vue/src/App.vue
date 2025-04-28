<template>
  <div class="crm-deals">
    <h2>CRM Сущности</h2>

    <select v-model="entityType" @change="loadItems">
      <option value="deals">Сделки</option>
      <option value="leads">Лиды</option>
      <option value="contacts">Контакты</option>
      <option value="tasks">Задачи</option>
      <option value="products">Товары</option>
    </select>

    <div v-if="loading" class="loader">Загрузка...</div>
    <div v-if="error" class="error">{{ error }}</div>

    <div v-if="!loading && !error && items.length">
      <table class="main-grid-table" id="vue-grid">
        <thead>
          <tr>
            <th v-for="column in columns" :key="column.id">
              {{ column.name }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items" :key="item.ID">
            <td v-for="column in columns" :key="column.id">
              {{ item[column.id] || '-' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="!loading && !error && !items.length">
      Нет данных для отображения
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';

const entityType = ref('deals');
const items = ref([]);
const loading = ref(true);
const error = ref(null);
const columns = ref([]);

const loadItems = async () => {
  loading.value = true;
  error.value = null;

  let actionName = '';
  switch (entityType.value) {
    case 'deals': actionName = 'getDeals'; break;
    case 'leads': actionName = 'getLeads'; break;
    case 'contacts': actionName = 'getContacts'; break;
    case 'tasks': actionName = 'getTasks'; break;
    case 'products': actionName = 'getProducts'; break;
  }

  try {
    const response = await BX.ajax.runComponentAction('mycompany:vuecounter', actionName, {
      mode: 'class'
    });

    if (response.data.success) {
      items.value = response.data[entityType.value];
      columns.value = getColumns(entityType.value);
    } else {
      error.value = 'Ошибка получения данных';
    }
  } catch (err) {
    console.error('Ошибка связи с сервером:', err);
    error.value = 'Ошибка связи с сервером';
  } finally {
    loading.value = false;
  }
};

const getColumns = (type) => {
  switch (type) {
    case 'deals':
      return [
        { id: 'ID', name: 'ID' },
        { id: 'TITLE', name: 'Название сделки' },
        { id: 'STAGE_ID', name: 'Стадия' },
        { id: 'OPPORTUNITY', name: 'Сумма' },
        { id: 'CURRENCY_ID', name: 'Валюта' }
      ];
    case 'leads':
      return [
        { id: 'ID', name: 'ID' },
        { id: 'TITLE', name: 'Название лида' },
        { id: 'STATUS_ID', name: 'Статус' },
        { id: 'OPPORTUNITY', name: 'Сумма' },
        { id: 'CURRENCY_ID', name: 'Валюта' }
      ];
    case 'contacts':
      return [
        { id: 'ID', name: 'ID' },
        { id: 'NAME', name: 'Имя' },
        { id: 'LAST_NAME', name: 'Фамилия' },
        { id: 'POST', name: 'Должность' }
      ];
    case 'tasks':
      return [
        { id: 'ID', name: 'ID' },
        { id: 'TITLE', name: 'Название задачи' },
        { id: 'STATUS', name: 'Статус' },
        { id: 'RESPONSIBLE_ID', name: 'Ответственный' }
      ];
    case 'products':
      return [
        { id: 'ID', name: 'ID' },
        { id: 'NAME', name: 'Название товара' },
        { id: 'PRICE', name: 'Цена' },
        { id: 'CURRENCY_ID', name: 'Валюта' }
      ];
  }
};

onMounted(loadItems);

watch(entityType, () => {
  loadItems();
});
</script>

<style scoped>
.crm-deals {
  max-width: 1000px;
  margin: 20px auto;
  font-family: Arial, sans-serif;
}
.loader {
  text-align: center;
  font-size: 18px;
  padding: 20px;
}
.error {
  color: red;
  text-align: center;
  font-size: 18px;
  padding: 20px;
}
.main-grid-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}
.main-grid-table th, .main-grid-table td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: left;
}
.main-grid-table th {
  background-color: #f2f2f2;
}
</style>
