<template>
  <div class="app-container">
    <!-- Note that row-key is necessary to get a correct row order. -->
    <el-table ref="dragTable" v-loading="listLoading" :data="list" row-key="id" border fit highlight-current-row style="width: 100%">
      <el-table-column align="center" label="ID" width="65">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column width="180px" align="center" label="Fixture">
        <template slot-scope="scope">
          <span>{{scope.row.league_name }}</span>
        </template>
      </el-table-column>
      <el-table-column min-width="300px" label="Sport">
       <template slot-scope="scope">
          <span>{{scope.row.sport.sport_name }}</span>
        </template>
      </el-table-column>
      <el-table-column min-width="300px" label="Round To Start">
       <template slot-scope="scope">
          <span>{{scope.row.round.round_name }}</span>
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
          <router-link :to="'/league/update/'+scope.row.id">
            <el-button type="primary" size="small" icon="el-icon-edit">

            </el-button>
          </router-link>
            <el-button type="primary" size="small" icon="el-icon-delete" v-on:click="onLeagueDelete(scope.row.id)">
            </el-button>
            <el-button type="primary" size="small" icon="el-icon-share" v-on:click="inviteDialog(scope.row.id)">
            </el-button>
        </template>
      </el-table-column>
    </el-table>
     <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
     <el-dialog :visible.sync="inviteDialogVisible" title="Invitation Portal">
    <el-form ref="form" :model="form" label-width="120px">
     <el-form-item label="Enter Email's">
       <el-input v-model="form.desc" type="textarea" rows="6" placeholder="You can add multiple Email Address, you can use  comma(,) for separation" />
     </el-form-item>
       <el-form-item>
         <el-button type="primary" @click="onInvitationSubmit" :disabled="inviteLoader">
           Send Invitation
         </el-button>
         <el-button @click="inviteDialogVisible = false">
           Cancel
         </el-button>
       </el-form-item>
     </el-form>
     </el-dialog>
  </div>
</template>

<script>
import { fetchLeagueList } from '@/api/league';
import { updateLeagueStatus,sendInvitation } from '@/api/league';
import Sortable from 'sortablejs';
import Resource from '@/api/resource';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
const leagueResource = new Resource('league');

export default {
  name: 'LeagueList',
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
      },
      sortable: null,
      oldList: [],
      newList: [],
      inviteDialogVisible: false,
      form: {
       desc: '',
       leagueid: ''
      },
      inviteLoader: false,
    };
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
      this.listLoading = true;
      const { data } = await fetchLeagueList(this.listQuery);
      this.list = data.items.data;
      this.total = data.items.total;
      this.listLoading = false;
      this.oldList = this.list.map(v => v.id);
      this.newList = this.oldList.slice();
      this.$nextTick(() => {
        this.setSort();
      });
    },
    async onLeagueDelete(id){

     this.listLoading = true;

      updateLeagueStatus(id)
        .then(response => {
          this.$message({
          message: 'League deleted successfully',
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
    inviteDialog ( id ) {
      this.form.leagueid = id;
      this.inviteDialogVisible = true;

    },
    async onInvitationSubmit () {
      this.inviteLoader = true;
       sendInvitation(this.form).then(response => {
           if(response.status=='failed') {
              this.$message({
                 message: response.message,
                 type: 'warning',
                 duration: 5 * 1000,
             });
           }else{
             this.$message({
                message: response.message,
                type: 'success',
                duration: 5 * 1000,
            });
           }
           this.inviteLoader = false;
           console.log(response);
          }).catch(err => {
            console.log(err);
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
