<template>
   <section class="content-part bg-blue compitior_padding">
      <div class="container"> 
        <div class="section-data comment-page-table">
            <div class="row">     

              <div class="col-sm-6">
                  <div class="table-heading" style="background-color: #FF4954;">
                    <h2>{{this.league.league_name}}</h2>
                  </div>
                  <div class="table-responsive league_page league-table league-height">


                    <table class="table" v-if="this.league.type == 'lms'">
                    <thead>
                      
                    </thead>
                    <tbody>  
                      <tr v-for="(scope,i) in list">
                        <td>{{i+=1}}.</td>
                         <td v-if="scope.nickname">{{ scope.nickname }}</td>
                        <td v-if="!scope.nickname">{{ scope.userName }}</td>
                        <td class="text-center league_type_lms">
                           <span v-for="(stats, j) in scope.last5roundResult">
                           <span class="round rund-green" v-if="stats=='w'" title="win"></span>
                           <span class="round rund-red" v-if="stats=='l'" title="loss"></span>
                           <span class="round rund-yellow" v-if="stats=='d'" title="draw"></span>
                           </span>
                         </td>
                      </tr>
                    </tbody>
                  </table>



                  <table class="table" v-if="this.league.type == 'lml'">
                    <thead>
                      <tr>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col" class="text-center">P</th>
                        <th scope="col" class="text-center">W</th>
                        <th scope="col" class="text-center">L</th>
                        <th scope="col" class="text-center">D</th>
                        <th scope="col" class="text-center">GD</th>
                        <th scope="col"></th> 
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(scope,i) in list">
                        <td>{{i+=1}}.</td>
                        <td v-if="scope.nickname">{{ scope.nickname }}</td>
                        <td v-if="!scope.nickname">{{ scope.userName }}</td>
                        <td class="text-center">{{ scope.played }}</td>
                        <td class="text-center">{{ scope.win }}</td>
                        <td class="text-center">{{ scope.loss }}</td>
                        <td class="text-center">{{ scope.draw }}</td>
                        <td class="text-center">{{ scope.goalDifference }}</td>
                        <td class="text-center roun-span">
                           <span v-for="(stats, j) in scope.last5roundResult">
                           <span class="round rund-green" v-if="stats=='w'" title="win"></span>
                           <span class="round rund-red" v-if="stats=='l'" title="loss"></span>
                           <span class="round rund-yellow" v-if="stats=='d'" title="draw"></span>
                           </span>
                         </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <Comments :leagueID="1" :roundID="1" v-if="this.league.is_banterboard == 'yes'"/>
          </div>
        </div>
      </div>
  </section>
</template>

<script>
import { viewLeague,leagueinfo } from '@/api/league';
import { MakeAdminOfLeague, acceptUserRequest } from '@/api/usersleague';
import Sortable from 'sortablejs';
import Resource from '@/api/resource';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import Comments from './components/comment';
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
      list: [],
      league: [],
      users: [],
      listLoading: true,
      loading: false,
      items: [],
    };
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.getList(id);
    this.leagueinfo(id);
  },
  methods: {
    async getList(id) {
      this.listLoading = true;
      const { data } = await viewLeague(id);
      this.list = data.items;
     
      this.listLoading = false;
    },
    async leagueinfo(id) {
      this.listLoading = true;
      const { data } = await leagueinfo(id);
      this.league = data.leagueinfo;

      if(this.league.league_member == 'no' && this.league.is_private == 'yes'){
        //alert();
        this.$router.push('/');
      }

      console.log(this.league);
      this.listLoading = false;
      
    },
  },
};
</script>
<style scoped>
.card{
        border-radius: 0px;
        height: 310px;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 22px;
        box-shadow: none;
        border: none;
      }
      .col-12 p{
        width: 95%
      }
</style>
