<template>
  <div class="app-container">
    <!-- Note that row-key is necessary to get a correct row order. -->
      <div class="filter-container">
      <el-select v-model="filterStats.sport" :placeholder="$t('Select Sport')" @change="handleFilter(index, $event)" style="width: 190px" class="filter-item">
        <el-option v-for="item in importanceOptions" :key="item.key" :label="item.sport_name" :value="item.id" />
      </el-select>
      <el-button class="filter-item" type="primary" icon="el-icon-search" @click="clearFilter">
        {{ $t('Clear') }}
      </el-button> 
    </div>

    <el-table ref="" v-loading="listLoading" :data="list" row-key="id" border fit highlight-current-row style="width: 100%">
      <el-table-column align="center" label="ID" width="65">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span> 
        </template>
      </el-table-column>
      <el-table-column width="180px" align="center" label="Fixture">
        <template slot-scope="scope">
          <span>{{scope.row.fixture_name }}</span>
        </template>
      </el-table-column>
      <el-table-column min-width="200px" label="Sport">
       <template slot-scope="scope">
          <span>{{scope.row.sport.sport_name }}</span>
        </template>
      </el-table-column> 
      <el-table-column min-width="200px" label="Home Team">
       <template slot-scope="scope">
          <span>{{scope.row.hometeam.team_name }}</span>
        </template>
      </el-table-column>
      <el-table-column min-width="200px" label="Away Team">
       <template slot-scope="scope">
          <span>{{scope.row.awayteam.team_name }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Status">
        <template slot-scope="{row}">
          <el-tag :type="row.status | statusFilter">
            {{ row.status === 'active'?'Active':'DeActive' }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Actions">
        <template slot-scope="scope">
          <router-link :to="'/fixtures/edit/'+scope.row.id">
            <el-button type="primary" size="small" icon="el-icon-edit">

            </el-button>
          </router-link>
            <!-- <el-button type="primary" size="small" icon="el-icon-delete" v-on:click="onFixtureDelete(scope.row.id)">
            </el-button> -->
        </template>
      </el-table-column>
    </el-table>
     <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

  </div>
</template>

<script>
import { fetchFixtureList, updateFixtureStatus } from '@/api/fixture';
import Sortable from 'sortablejs';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination

import Resource from '@/api/resource';
const fixtureResource = new Resource('fixture');

export default {
  name: 'DragTable',
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
      tableKey: 0,
      iconPath: '/uploads/team/',
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
      const { data } = await fetchFixtureList(this.listQuery);
      this.list = data.items.data;
      this.tempList = data.items.data;
      this.total = data.items.total;
      var fileTest = []; 
      this.listLoading = false;
      
      if(this.filterStats.sport=='') {
        this.importanceOptions = data.sport;
      }

      this.oldList = this.list.map(v => v.id);
      this.newList = this.oldList.slice();
      this.$nextTick(() => {
        this.setSort();
      });
    },
    async onFixtureDelete(id) {
     this.listLoading = true;
      updateFixtureStatus(id)
        .then(response => {
          this.$message({
        message: 'Fixture deleted successfully',
        type: 'success',
        duration: 5 * 1000,
        });
        })
        .catch(err => {
          console.log(err);
        });

      this.listLoading = false;
      this.getList();
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
.el-button--small {
    padding: 5px 8px;
    font-size: 12px;
    border-radius: 3px;
    display: inline-block;
    float: left;
    width: 26px;
    margin-left: 3px;
}
</style>
