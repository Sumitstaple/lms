<template>
  <div class="app-container">
    <!-- Note that row-key is necessary to get a correct row order. -->
    <el-table v-loading="listLoading" :data="list" row-key="id" border fit highlight-current-row style="width: 100%">
      <el-table-column align="center" label="ID" width="65">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column width="180px" align="center" label="Name">
        <template slot-scope="scope">
          <span>{{scope.row.name }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Last Name">
        <template slot-scope="scope">
          <span>{{ scope.row.last_name }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Nick Name">
        <template slot-scope="scope">
          <span>{{ scope.row.nickname }}</span>
        </template>
      </el-table-column>
      <el-table-column width="180px" align="center" label="Email">
        <template slot-scope="scope">
          <span>{{scope.row.email }}</span>
        </template>
      </el-table-column>

       <el-table-column width="140px" align="center" label="Profile Image">
        <template slot-scope="scope">
          <span v-if="scope.row.avatar !== 'user.png'"><img :src="iconPath+scope.row.avatar"/></span>
          <span v-else><img :src="defaultIconPath"/></span>
        </template>
      </el-table-column>


      <el-table-column width="80px" align="center" label="Role">
        <template slot-scope="scope">
          <span>{{scope.row.role }}</span>
        </template>
      </el-table-column>

      <el-table-column width="80px" align="center" label="Notification">
        <template slot-scope="scope">
          <span>{{scope.row.is_notifications_option_active }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Make Admin">
        <template slot-scope="scope">
         <input type="checkbox" :value="scope.row.id" :checked="scope.row.is_mark_admin == 'yes'" @change="makeadmin(scope.row.is_mark_admin,scope.row.id)">
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
  </div>
</template>

<script>
import { fetchUserList,MakeUserAdmin  } from '@/api/user';
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
      iconPath: '/uploads/profile/',
      defaultIconPath: '/images/users.png',
      list: [],
      total: null,
      listLoading: true,
      make_admin:'',
      postData:[],
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
      const { data,meta } = await fetchUserList(this.listQuery);
      console.log(data);
      this.list = data;
      this.total = meta.total;

      this.listLoading = false;
      this.oldList = this.list.map(v => v.id);
      this.newList = this.oldList.slice();
      this.$nextTick(() => {
        this.setSort();
      });
    },
    async makeadmin(status,user_id) {
     

      this.postData=[status,user_id];

      const { data } =  await MakeUserAdmin(this.postData);

      if(data.status == "admin"){

        this.$message({
          message: 'You have make admin successfully',
          type: 'success',
          duration: 5 * 1000,
        });
      }
      else if(data.status == "user"){
        this.$message({
          message: 'You have successfully remove the admin permission of this user.',
          type: 'success',
          duration: 5 * 1000,
        });
      }
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
span.nameavatar {
height: 15%;
width: 20%;
text-align: center;
color: #00000;
border-radius: 50%;
line-height: 37px;
text-transform: uppercase;
}

img{
  width: 20%;
  height: 15%;
  border-radius: 50%;
}
</style>
