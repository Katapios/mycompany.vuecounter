<template>
  <div class="crm-deals">
    <h2>CRM Сущности</h2>

    <div class="controls">
      <select v-model="entityType" @change="loadItems">
        <option value="deals">Сделки</option>
        <option value="leads">Лиды</option>
        <option value="contacts">Контакты</option>
        <option value="tasks">Задачи</option>
        <option value="products">Товары</option>
      </select>

      <input
          v-model="searchQuery"
          @input="applySearch"
          type="text"
          placeholder="Поиск..."
          class="search-input"
      />
      <button @click="deleteAllItemsBatch">🗑 Пакетное удаление всех</button>
    </div>

    <div v-if="loading" class="loader">Загрузка...</div>
    <div v-if="error" class="error">{{ error }}</div>

    <div v-if="!loading && !error && filteredItems.length" class="grid-wrapper">
      <table class="main-grid-table" id="vue-grid">
        <thead>
        <tr>
          <th
              v-for="column in columns"
              :key="column.id"
              @click="sortBy(column.id)"
              style="cursor: pointer;"
          >
            {{ column.name }}
            <span v-if="sortField === column.id">
          {{ sortDirection === 'asc' ? '▲' : '▼' }}
        </span>
          </th>
          <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="item in paginatedItems" :key="item.ID">
          <td v-for="column in columns" :key="column.id">
            {{ item[column.id] || '-' }}
          </td>
          <td>
            <button @click="deleteItem(item.ID)">🗑</button>
          </td>
        </tr>
        </tbody>
      </table>
      <div v-if="totalPages > 1" class="pagination">
        <button
            v-for="page in totalPages"
            :key="page"
            @click="goToPage(page)"
            :class="{ active: currentPage === page }"
        >
          {{ page }}
        </button>
      </div>
    </div>

    <div v-if="!loading && !error && !filteredItems.length">
      Нет данных для отображения
    </div>
  </div>
</template>


<script setup>
if (typeof BX === 'undefined') {
  console.error('BX не загружен. Проверь подключение main.core.');
}
import {ref, onMounted, watch, computed} from 'vue';

const offset = ref(0);
const limit = 20;
const total = ref(0);
const loadingMore = ref(false);


const currentPage = ref(1);        // текущая страница
const itemsPerPage = 10;            // сколько записей на одной странице
const entityType = ref('deals');
const items = ref([]);
const searchQuery = ref('');
const loading = ref(true);
const error = ref(null);
const columns = ref([]);

// Новое: состояние для сортировки
const sortField = ref('');
const sortDirection = ref('asc'); // 'asc' или 'desc'

const loadItems = async (reset = false) => {
  if (reset) {
    offset.value = 0;
    items.value = [];
    total.value = 0;
  }

  loading.value = offset.value === 0;
  loadingMore.value = offset.value > 0;
  error.value = null;

  let actionName = '';
  switch (entityType.value) {
    case 'deals':
      actionName = 'getDeals';
      break;
    case 'leads':
      actionName = 'getLeads';
      break;
    case 'contacts':
      actionName = 'getContacts';
      break;
    case 'tasks':
      actionName = 'getTasks';
      break;
    case 'products':
      actionName = 'getProducts';
      break;
  }

  try {
    const response = await BX.ajax.runComponentAction('mycompany:vuecounter', actionName, {
      mode: 'class',
      data: {
        offset: offset.value,
        limit: limit
      }
    });

    if (response.data.success) {
      const existingIds = new Set(items.value.map(item => item.ID));

      const newItems = response.data[entityType.value].filter(item => !existingIds.has(item.ID));

      items.value.push(...newItems);
      total.value = response.data.total;
      columns.value = getColumns(entityType.value);
      sortField.value = '';
      offset.value += limit; // можно оставить если хочешь авто-поднятие offset

    } else {
      error.value = 'Ошибка получения данных';
    }
  } catch (err) {
    console.error('Ошибка связи с сервером:', err);
    error.value = 'Ошибка связи с сервером';
  } finally {
    loading.value = false;
    loadingMore.value = false;
  }
};


// Live поиск + сортировка
const filteredItems = computed(() => {
  let filtered = items.value;

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(item =>
        Object.values(item).some(val =>
            String(val).toLowerCase().includes(query)
        )
    );
  }

  if (sortField.value) {
    filtered = [...filtered].sort((a, b) => {
      const aValue = a[sortField.value] || '';
      const bValue = b[sortField.value] || '';

      if (aValue < bValue) return sortDirection.value === 'asc' ? -1 : 1;
      if (aValue > bValue) return sortDirection.value === 'asc' ? 1 : -1;
      return 0;
    });
  }

  return filtered;
});

// Клик по заголовку для сортировки
const sortBy = (field) => {
  if (sortField.value === field) {
    // если кликаем по уже отсортированному столбцу — меняем порядок
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    // иначе сортируем по новому полю
    sortField.value = field;
    sortDirection.value = 'asc';
  }
};

// данные для текущей страницы
const paginatedItems = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredItems.value.slice(start, start + itemsPerPage);
});

// сколько всего страниц
const totalPages = computed(() => {
  return Math.ceil(total.value / itemsPerPage);
});

// смена страницы
const goToPage = async (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;

    const start = (page - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    if (items.value.length < end && items.value.length < total.value) {
      offset.value = items.value.length;
      await loadItems();
    }
  }
};

const getColumns = (type) => {
  switch (type) {
    case 'deals':
      return [
        {id: 'ID', name: 'ID'},
        {id: 'TITLE', name: 'Название сделки'},
        {id: 'STAGE_ID', name: 'Стадия'},
        {id: 'OPPORTUNITY', name: 'Сумма'},
        {id: 'CURRENCY_ID', name: 'Валюта'}
      ];
    case 'leads':
      return [
        {id: 'ID', name: 'ID'},
        {id: 'TITLE', name: 'Название лида'},
        {id: 'STATUS_ID', name: 'Статус'},
        {id: 'OPPORTUNITY', name: 'Сумма'},
        {id: 'CURRENCY_ID', name: 'Валюта'}
      ];
    case 'contacts':
      return [
        {id: 'ID', name: 'ID'},
        {id: 'NAME', name: 'Имя'},
        {id: 'LAST_NAME', name: 'Фамилия'},
        {id: 'POST', name: 'Должность'}
      ];
    case 'tasks':
      return [
        {id: 'ID', name: 'ID'},
        {id: 'TITLE', name: 'Название задачи'},
        {id: 'STATUS', name: 'Статус'},
        {id: 'RESPONSIBLE_ID', name: 'Ответственный'}
      ];
    case 'products':
      return [
        {id: 'ID', name: 'ID'},
        {id: 'NAME', name: 'Название товара'},
        {id: 'PRICE', name: 'Цена'},
        {id: 'CURRENCY_ID', name: 'Валюта'}
      ];
  }
};

// Добавим новые функции для удаления

async function deleteItem(id) {
  if (!confirm(`Удалить элемент №${id}? Это действие необратимо.`)) return;
  try {
    const

        response = await BX.ajax.runComponentAction('mycompany:vuecounter', 'deleteItem', {
          mode: 'class',
          data: {entityType: entityType.value, id}
        });

    if (response.data.success) {
      alert('Удалено успешно');
      await loadItems(true);
    } else {
      alert(`Ошибка: ${response.data.error || 'Неизвестно'}`);
    }
  } catch (e) {
    console.error(e);
    alert('Ошибка связи с сервером');
  }
}

async function deleteAllItemsBatch() {
  if (!confirm(`Удалить ВСЕ записи категории "${entityType.value}" пакетно? Это необратимо.`)) return;

  let offset = 0;
  const batchSize = 100; // сколько записей за один проход

  loading.value = true;

  try {
    while (true) {
      const response = await BX.ajax.runComponentAction('mycompany:vuecounter', 'deleteBatchItems', {
        mode: 'class',
        data: {entityType: entityType.value, batch: batchSize, offset}
      });

      if (!response.data.success) {
        alert(`Ошибка: ${response.data.error}`);
        break;
      }

      console.log(`Удалено ${response.data.processed} записей`);

      if (response.data.nextOffset === null) {
        alert('Все записи удалены успешно');
        break;
      }

      offset = response.data.nextOffset;
    }

    await loadItems(true); // перезагружаем таблицу

  } catch (e) {
    console.error('Ошибка удаления пакетами:', e);
    alert('Ошибка связи с сервером');
  }

  loading.value = false;
}

onMounted(loadItems);

watch(entityType, () => {
  currentPage.value = 1;
  loadItems(true);
});

watch(searchQuery, () => {
  currentPage.value = 1;
});
const applySearch = () => {
};
</script>


<style scoped>
.crm-deals {
  width: 100%;
  padding: 20px;
  box-sizing: border-box;
  font-family: Arial, sans-serif;
}

.controls {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
}

.search-input {
  flex: 1;
  padding: 8px;
  font-size: 14px;
}

.loader, .error {
  text-align: center;
  font-size: 16px;
  padding: 20px;
}

.grid-wrapper {
  width: 100%;
  overflow-x: auto;
}

.main-grid-table {
  width: 100%; /* Теперь на всю ширину контейнера */
  border-collapse: collapse;
  table-layout: fixed; /* ВАЖНО: фиксированная таблица */
}

.main-grid-table th, .main-grid-table td {
  border: 1px solid #ccc;
  padding: 6px 8px;
  text-align: left;
  font-size: 14px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.main-grid-table th {
  background-color: #f2f2f2;
  cursor: pointer;
}

.main-grid-table th:hover {
  background-color: #e8e8e8;
}

.main-grid-table tbody tr:hover {
  background-color: #f5f7fa;
}

.pagination {
  margin-top: 20px;
  text-align: center;
}

.pagination button {
  margin: 0 5px;
  padding: 6px 12px;
  font-size: 14px;
  cursor: pointer;
  background: #f2f2f2;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.pagination button.active {
  background-color: #4a90e2;
  color: white;
  border-color: #4a90e2;
}
</style>
