<template>
  <section class="content-part bg-blue compitior_padding">
    <div class="container">
    <h1>Joined Leagues</h1>
    <el-table ref="dragTable" v-loading="listLoading" :data="list" row-key="id" border fit highlight-current-row style="width: 100%">
      <el-table-column align="center" label="ID" width="65">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column width="180px" align="center" label="League Name">
        <template slot-scope="scope">
          <router-link :to="'/league/'+scope.row.league.id">
          <span>{{scope.row.league.league_name }}</span>
        </router-link>
        </template>
      </el-table-column>
 
      <el-table-column min-width="300px" label="Team Pickup">
       <template slot-scope="scope">
          <span>{{scope.row.is_team_pickup }}</span>
        </template>
      </el-table-column>
      <el-table-column min-width="300px" label="Status">
       <template slot-scope="{row}">
          <el-tag v-if="row.status === 'active'" :type="row.status | statusFilter">
            Active
          </el-tag>
          <el-tag v-else-if="row.status === 'rejected'" :type="row.status | statusFilter">
            Rejected
          </el-tag>
          <el-tag v-else="row.status === 'request_pending'" :type="row.status | statusFilter">
            Request Pending
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Action">
        <template slot-scope="scope" v-if="scope.row.is_admin == 'yes'">
          <router-link :to="'/league/user-league-detail/'+scope.row.id">Detail</router-link>
           <el-button type="primary" size="small" icon="el-icon-share" v-if="scope.row.is_admin == 'yes'" v-on:click="inviteDialog(scope.row.id)">
            </el-button>
        </template>
      </el-table-column>

    </el-table>
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
      <!-- <Comments :leagueID="1" :roundID="1" /> -->
  </div>
  </section>
</template>

<script>
import { fetchLeagueList } from '@/api/usersleague';
import { updateLeagueStatus,sendInvitation } from '@/api/league';
import Sortable from 'sortablejs';
import Resource from '@/api/resource';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import Comments from './components/comment';
const leagueResource = new Resource('usersleague');

export default {
  name: 'LeagueList',
  components: { Pagination, Comments },
  filters: {
    statusFilter(status) {
      const statusMap = {
        active: 'success',
        request_pending: 'info',
        rejected: 'danger',
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
      this.list = data.items;
      console.log(this.list);
      this.listLoading = false;
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