<template>
  <div class="app-container">

    <div class="filter-container">
      <el-select v-model="filterStats.user" :placeholder="$t('Select User')" @change="handleFilter(index, $event)" style="width: 190px" class="filter-item">
        <el-option v-for="item in importanceOptions" :key="item.key" :label="item.first_name" :value="item.id" />
      </el-select>
      <el-button class="filter-item" type="primary" icon="el-icon-search" @click="clearFilter">
        {{ $t('Clear') }}
      </el-button> 
    </div>

    <!-- Note that row-key is necessary to get a correct row order. -->
    <el-table v-loading="listLoading" :data="list" row-key="id" border fit highlight-current-row style="width: 100%">
      <el-table-column align="center" label="ID" width="65">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="User Name">
        <template slot-scope="scope">
          <span>{{scope.row.user.first_name }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Log Type">
        <template slot-scope="scope">
          <span>{{scope.row.logtype.type }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Log Action">
        <template slot-scope="scope">
          <span>{{scope.row.log_action }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Description">
        <template slot-scope="scope">
          <span>{{scope.row.log_text }}</span>
        </template>
      </el-table-column>
<!--       <el-table-column align="center" label="Actions">
        <template slot-scope="scope">
          <router-link :to="'/teams/edit/'+scope.row.id">
            <el-button type="primary" size="small" icon="el-icon-edit">
              Edit
            </el-button>
          </router-link>
        </template>
      </el-table-column> -->
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
  </div>
</template>

<script>
import { fetchSystemLogsList } from '@/api/systemlogs';
import Sortable from 'sortablejs';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination

export default {
  name: '',
  components: { Pagination },
  filters: {
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'info',
        deleted: 'danger',
      };
      return statusMap[status];
    },
  },
  data() {
    return {
      list: [],
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 10, 
        string: '',
      },
      filterStats:{
        user: ''
      },
      sortable: null,
      oldList: [],
      newList: [],
      tempList: [],
      importanceOptions: [],
    };
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {

      this.listLoading = true;
      const { data } = await fetchSystemLogsList(this.listQuery);
      this.list = data.items.data;
      this.total = data.items.total;
      this.listLoading = false;
       if(this.filterStats.user=='') {
          this.importanceOptions = data.user;
        }

            this.oldList = this.list.map(v => v.id);
            this.newList = this.oldList.slice();
            this.$nextTick(() => {
              this.setSort();
            });
    },
    setSort() {
      const el = this.$refs.dragTable.$el.querySelectorAll('.el-table__body-wrapper > table > tbody')[0];
      this.sortable = Sortable.create(el, {
        ghostClass: 'sortable-ghost', // Class name for the drop placeholder,
        setData: function(dataTransfer) {
          // to avoid Firefox bug
          // Detail see : https://github.com/RubaXa/Sortable/issues/1012
          dataTransfer.setData('Text', '');
        },
        onEnd: evt => {
          const targetRow = this.list.splice(evt.oldIndex, 1)[0];
          this.list.splice(evt.newIndex, 0, targetRow);

          // for show the changes, you can delete in you code
          const tempIndex = this.newList.splice(evt.oldIndex, 1)[0];
          this.newList.splice(evt.newIndex, 0, tempIndex);
        },
      });
    },
    handleFilter (a, b) {
     this.listQuery.string = this.filterStats.user;
     this.getList();
    },
    clearFilter () {
     this.listQuery.string = '';
     this.filterStats.sport = '';
     this.importanceOptions = [];
     this.getList();
    },
  },
};
</script>

<style>
.sortable-ghost{
  opacity: .8;
  color: #fff!important;
  background: #42b983!important;
}
</style>

<style scoped>
.icon-star {
  margin-right:2px;
}
.drag-handler {
  width: 20px;
  height: 20px;
  cursor: pointer;
}
.show-d {
  margin-top: 15px;
}
img{
  width: 20%;
  height: 15%;
}
</style>
