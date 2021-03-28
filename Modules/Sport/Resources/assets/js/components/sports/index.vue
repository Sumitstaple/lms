<template>
  <div class="app-container">
    <el-table v-loading="listLoading" :data="list" border fit highlight-current-row style="width: 100%">
      <el-table-column align="center" label="ID" width="140">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Sport Name" width="220">
        <template slot-scope="scope">
          <span>{{ scope.row.sport_name }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Sport Icon" width="180">
        <template slot-scope="scope">
          <span><img :src="iconPath+scope.row.sport_icon"/></span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Status" width="150">
        <template slot-scope="{row}">
          <el-tag :type="row.status | statusFilter">
            {{ row.is_active === 'yes'?'Active':'DeActive' }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Actions">
        <template slot-scope="scope">
          <router-link :to="'/sports/edit/'+scope.row.id">
            <el-button type="primary" size="small">
              Edit
            </el-button>
          </router-link>
          <router-link :to="'sports/teams/'+scope.row.id">
            <el-button type="info" size="small">
              Manage Teams
            </el-button>
          </router-link>
          <router-link :to="'sports/rounds/'+scope.row.id">
            <el-button type="default" size="small">
              Manage Rounds
            </el-button>
          </router-link>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
  </div>
</template>

<script>
import Resource from '@/api/resource';
const sportResource = new Resource('sport');
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination


export default {
  name: 'SportList',
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
      list: null,
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 20,
      },
    };
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
      this.listLoading = true;
      const { data } = await sportResource.list(this.listQuery);
      this.list = data.items.data;
      this.total = data.items.total;
      this.listLoading = false;
    },
  },
};
</script>

<style scoped>
.edit-input {
  padding-right: 100px;
}
.cancel-btn {
  position: absolute;
  right: 15px;
  top: 10px;
}
img{
  width: 20%;
  height: 15%;
}
</style>
