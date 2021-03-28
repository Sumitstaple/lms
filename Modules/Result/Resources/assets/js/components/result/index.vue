<template>
  <div class="app-container">
  <div class="filter-container">
      <el-select v-model="filterStats.sport" :placeholder="$t('Select Sport')" @change="handleFilter(index, $event)" style="width: 190px" class="filter-item">
        <el-option v-for="item in importanceOptions" :key="item.id" :label="item.sport_name" :value="item.id" />
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
      <el-table-column width="220px" align="center" label="Sport Name">
        <template slot-scope="scope">
          <span>{{scope.row.sport.sport_name }} </span>
        </template>
      </el-table-column>
      <el-table-column width="183px" align="center" label="Round Name">
        <template slot-scope="scope">
          <span>{{scope.row.round_name }}</span>
        </template>
      </el-table-column>
      <el-table-column width="180px" align="center" label="Round Number">
        <template slot-scope="scope">
          <span>{{scope.row.round_number }}</span>
        </template>
      </el-table-column>
       <el-table-column width="330px" align="center" label="Date">
        <template slot-scope="scope">
          <span>{{scope.row.start_datetime}} - {{scope.row.end_datetime }}</span>
        </template>
      </el-table-column>
       <el-table-column align="center" label="Actions" width="120">
        <template slot-scope="scope">
          <router-link :to="'/result/round/'+scope.row.id+'/'+scope.row.sport.id">
            <el-button type="primary" size="small" icon="el-icon-edit">
              Edit
            </el-button>
          </router-link>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
  </div>
</template>

<script>
import { getRounds } from '@/api/result';
import Sortable from 'sortablejs';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination

export default {
  name: 'RoundList',
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
      iconPath: '/uploads/sport/',
      list: [],
      total: null,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 10,
        string: ''
      },
      filterStats:{
        sport: ''
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
      const { data } = await getRounds(this.listQuery);
      this.list = data.items.data;
      this.tempList = data.items.data;
      this.listLoading = false;
      this.total = data.items.total;
      //var fileTest = []; 
      //this.importanceOptions = [];
      if(this.filterStats.sport=='') {
	      this.importanceOptions = data.sport;
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
     this.listQuery.string = this.filterStats.sport;
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
  width: 5%;
  height: 3%;
  display: block;
  margin-bottom: 10px;
}
</style>
