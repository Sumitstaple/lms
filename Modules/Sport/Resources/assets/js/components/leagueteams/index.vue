<template>
  <div class="app-container">
    <!-- Note that row-key is necessary to get a correct row order. -->
    <el-table v-loading="listLoading" :data="list" row-key="id" border fit highlight-current-row style="width: 100%">
      <el-table-column align="center" label="ID" width="65">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
<!--       <el-table-column align="center" label="League Name">
        <template slot-scope="scope">
          <span>{{scope.row.league_name }}</span>
        </template>
      </el-table-column> -->
      <el-table-column align="center" label="Sport Name">
        <template slot-scope="scope">
          <span>{{scope.row.sport_name }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Actions">
        <template slot-scope="scope">
          <router-link :to="'/manageteams/'+scope.row.id">
              Manage Teams
          </router-link>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
  </div>
</template>

<script>
import { fetchSportList } from '@/api/sport';
import Sortable from 'sortablejs';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination

export default {
  name: 'TeamList',
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
      iconPath: '/uploads/team/',
      list: [],
      total: null,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 10,
      },
      sortable: null,
      oldList: [],
      newList: [],
    };
  },
  created() {
    this.getList();
    
  },
  methods: {
    async getList() {
      this.listLoading = true;
      const { data } = await fetchSportList(this.listQuery);
      this.list = data.items;
      console.log(this.list);
      this.total = data.items.total;
      this.listLoading = false;
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
