<template>
  <div> 
<section class="content-part bg-blue">
            <div class="container">
              <div class="row">
                <div class="col-sm-12">
                  <div class="top-message">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                      <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i> You need to tip for AFL Round 5
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">⟶</span>
                      </button>
                    </div>

                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                      <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i> You need to tip for NRL Round 3
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">⟶</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="section-data round-page">
                <div class="table-heading" style="background-color: #FF4954;">
                  <h2>LAST MAN STANDING</h2>
                </div>
                <div class="table-responsive">
                <table class="table" v-loading="listLoading">

                  <tbody>

                    <tr v-for="item in lmsleagues" v-if="item.league.type == 'lms'">
                      <td><img src="images/comment-blue.png"><span>{{ item.league.id }}</span></td>
                      <td>{{ item.league.league_name }}<br><small>21 New Comment</small></td>
                      <td class="text-center"><span><img src="images/icon1.png">Melbourne Storm</span></td>
                      <td class="text-center"><span class="round">{{ item.sport.sport_name }}</span></td>
                      <td class="text-center">ROUND<br><strong>{{ item.round.round_name }}</strong></td>
                      <td class="text-center">{{ item.status }}</td>
                      <td class="text-center" v-if="item.league.is_private == 'no'">Public</td>
                      <td class="text-center" v-if="item.league.is_private == 'yes'">Private</td>
                      <td class="text-center">
                        
                      <router-link :to="'/league/update/'+item.league.id">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                      </router-link>
                      <i class="fa fa-trash" aria-hidden="true" v-on:click="onLeagueDelete(item.league.id)"></i>
                      <i class="fa fa-share-alt" aria-hidden="true" v-on:click="inviteDialog(item.league.id)"></i>
                      </td>
                      <td><router-link :to="'/league/user-league-detail/'+item.league.id">Detail</router-link></td>
                      <td><router-link :to="'/league/addteam/'+item.league.id">Add Team</router-link></td>
                      <td class="btn-image"><img src="images/icon3.png"></td>
                    </tr>


                  </tbody>
                </table>
              </div> 


              <div class="table-heading" style="background-color: #282855;">
                  <h2>LAST MAN LEAGUE</h2>
                </div>
                <div class="table-responsive">
                <table class="table">
                  <tbody>

                    <tr v-for="item in lmsleagues" v-if="item.league.type == 'lml'">
                      <td><img src="images/comment-blue.png"><span>{{ item.league.id }}</span></td>
                      <td>{{ item.league.league_name }}<br><small>21 New Comment</small></td>
                      <td class="text-center"><span><img src="images/icon1.png">Melbourne Storm</span></td>
                      <td class="text-center"><span class="round">{{ item.sport.sport_name }}</span></td>
                      <td class="text-center">ROUND<br><strong>{{ item.round.round_name }}</strong></td>
                      <td class="text-center">{{ item.status }}</td>
                      <td class="text-center" v-if="item.league.is_private == 'no'">Public</td>
                      <td class="text-center" v-if="item.league.is_private == 'yes'">Private</td>
                      <td class="text-center">
                        
                      <router-link :to="'/league/update/'+item.league.id">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                      </router-link>
                      <i class="fa fa-trash" aria-hidden="true" v-on:click="onLeagueDelete(item.league.id)"></i>
                      <i class="fa fa-share-alt" aria-hidden="true" v-on:click="inviteDialog(item.league.id)"></i>
                      </td>
                      <td><router-link :to="'/league/user-league-detail/'+item.league.id">Detail</router-link></td>
                      <td><router-link :to="'/league/addteam/'+item.league.id">Add Team</router-link></td>
                      <td class="btn-image"><img src="images/icon3.png"></td>
                    </tr>

                    <!-- <tr>
                      <td><img src="images/comment.png"><span>0</span></td>
                      <td>Bryan’s Rad Competition</td>
                      <td class="text-center"><a href="#" class="table-btn">Pick your team</a></td>
                      <td class="text-center"><span class="round rund-green"></span><span class="round rund-green"></span><span class="round rund-green"></span></td>
                      <td class="text-center">ROUND<br><strong>1</strong></td>
                      <td class="btn-image"><img src="images/icon3.png"></td>
                    </tr>
                    <tr>
                      <td><img src="images/comment.png"><span>0</span></td>
                      <td>Bower Work Competition</td>
                      <td class="text-center"><span><img src="images/icon2.png">Arsenal</span></td>                      
                      <td class="text-center"><span class="round rund-green"></span><span class="round rund-green"></span><span class="round rund-yellow"></span></td>                      
                      <td class="text-center">ROUND<br><strong>7</strong></td>
                      <td class="btn-image"><img src="images/icon4.png"></td>
                    </tr>
                    <tr>
                      <td><img src="images/comment-blue.png"><span>2</span></td>
                      <td>Another Team I Joined<br> <small>2 New Comment</small></td>
                      <td class="text-center"><span><img src="images/icon2.png">Arsenal</span></td>
                      <td class="text-center"><span class="round rund-yellow"></span><span class="round rund-green"></span><span class="round rund-red"></span></td>
                      <td class="text-center">ROUND<br><strong>22</strong></td>
                      <td class="btn-image"><img src="images/icon5.png"></td>
                    </tr> -->
                  </tbody>
                </table>
              </div>

              <div class="row mt-4">
                  <div class="col-sm-12 text-center">
                      <a href="#" class="comptiton-btn mr-2"><span>Find a competition</span></a>
                      <a href="#" class="comptiton-btn"><span>CREATE a competition</span></a>
                  </div>
              </div>

              </div>
            </div>
        </section>
      </div>
</template>        


<script>
import { fetchLeagueList } from '@/api/usersleague';
import { updateLeagueStatus,sendInvitation } from '@/api/league';
import Sortable from 'sortablejs';
import Resource from '@/api/resource';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
const leagueResource = new Resource('usersleague');

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
      lmsleagues:[],
      lmlleagues:[],
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
      this.lmsleagues = data.items;
      this.lmlleagues = data.items;
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

<style type="text/css">
  

</style>