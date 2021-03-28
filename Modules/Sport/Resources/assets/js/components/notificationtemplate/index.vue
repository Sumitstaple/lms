<template>
  <div class="app-container">
    <!-- Note that row-key is necessary to get a correct row order. -->
    <el-table v-loading="listLoading" :data="list" row-key="id" border fit highlight-current-row style="width: 100%">
      <el-table-column align="center" label="ID" width="65">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Template Name">
        <template slot-scope="scope">
          <span>{{scope.row.name }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Template Slug">
        <template slot-scope="scope">
          <span>{{scope.row.slug }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Template Type">
        <template slot-scope="scope">
          <span>{{scope.row.type }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Template Mode">
        <template slot-scope="scope">
          <span>{{scope.row.mode }}</span>
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
import { fetchNotificationTemplateList } from '@/api/notificationtemplate';
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
      const { data } = await fetchNotificationTemplateList(this.listQuery);
      this.list = data.items.data;
      this.total = data.items.total;
      this.listLoading = false;
      this.oldList = this.list.map(v => v.id);
      this.newList = this.oldList.slice();
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
