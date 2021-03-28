<template>
  <div>
<section class="content-part bg-blue">
            <div class="container">
            <div class="row">
              
                <!-- <div class="col-sm-12">
                  <div class="top-message">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                      <img src="images/check-mark.png" class="check_mark"> Geelong Cats locked in
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">✕</span>
                      </button>
                    </div>
                  </div>
                </div> -->

                <div class="col-sm-12">
                  <div class="compition-title text-center border-radius_top d-flex align-items-center">
                    <span class="mr-auto ml-3"><router-link to="/league/myleagues" class="lef-arrow">←</router-link></span><h2 class="mb-0 text-center">{{this.list[0].round_name}}</h2><span class="ml-auto mr-3"><!-- <img src="images/afl-img.png" style="max-width: 40px;"> --></span>
                  </div>
                </div>
              </div>
              <div class="section-rnd deskteam">
                <p v-if="noFixture=='true'">Fixtures have not yet been uploaded for this round</p>
                <div class="inner-rnd team_formnew">
                <el-form ref="form" :model="form" label-width="150px">
                 <el-radio-group v-model="form.teamid" v-on:change="onTeamChange($event)">
                  <div class="row align-items-center mb-4" v-for="item in teamList">
                        <div class="col-md-5 part-view">
                           <el-radio :label="item.fixture_id+'-'+item.home_team_id" :disabled="item.home_is_selectable=='true' ? false : true">
                                <div class="round-sec-left" :class="item.home_is_selectable=='false' ? 'inactive' : ''">
                                  <span class="left-end" v-if="item.home_is_selectable=='false'">{{item.homemessage}}</span>
                                  <img :src="iconPath+item.home_team_icon">{{ item.home_team_name }} <small>(H)</small>
                                </div>
                            </el-radio>
                        </div>
                        <div class="col-md-2">
                          <div class="round-sec-cntr">
                            <p><strong>{{item.matchDate}}</strong><br>
                              {{item.matchTime}}</p>
                          </div>
                        </div>
                      <div class="col-md-5">
                        <el-radio :label="item.fixture_id+'-'+item.away_team_id" :disabled="item.away_is_selectable=='true' ? false : true">
                              <div class="round-sec-right" :class="item.away_is_selectable=='false' ? 'inactive' : ''">
                              <span class="right-end" v-if="item.away_is_selectable=='false'">{{item.awaymesage}}</span>{{ item.away_team_name }}  (A)<img :src="iconPath+item.away_team_icon">
                              </div>
                        </el-radio>
                      </div>
                    </div>
                    </el-radio-group>
                  </el-form>
                </div>
              </div>
              <div class="section-rnd mobileteam">
                <div class="inner-rnd team_formnew">
                <el-form ref="form" :model="form" label-width="150px">
                 <el-radio-group v-model="form.teamid" v-on:change="onTeamChange($event)">
                  <div class="row align-items-center" v-for="item in teamList">
                        <div class="col-6">
                           <el-radio :label="item.fixture_id+'-'+item.home_team_id" :disabled="item.home_is_selectable=='true' ? false : true">
                                <div class="round-sec-left" :class="item.home_is_selectable=='false' ? 'inactive' : ''">
                                  <img :src="iconPath+item.home_team_icon">{{ item.home_team_name }} <small>(H)</small>
                                  
                                </div>
                            </el-radio>

                        </div>
                        <div class="col-6">
                        <p class="texttt" v-if="item.home_is_selectable=='false'">{{item.homemessage}}</p>
                        <p class="texttt" v-if="item.home_is_selectable=='true'">v.{{item.away_team_name}}</p>
                        </div>
</div>
<div class="row align-items-center" v-for="item in teamList">
                      <div class="col-6">
                        <el-radio :label="item.fixture_id+'-'+item.away_team_id" :disabled="item.away_is_selectable=='true' ? false : true">
                              <div class="round-sec-left" :class="item.away_is_selectable=='false' ? 'inactive' : ''">
                              <img :src="iconPath+item.away_team_icon">{{ item.away_team_name }}  <small>(A)</small>
                              
                              </div>
                        </el-radio>
                      </div>
                      <div class="col-6">
                        <p class="texttt" v-if="item.away_is_selectable=='false'">{{item.awaymesage}}</p>
                        <p class="texttt" v-if="item.away_is_selectable=='true'">v.{{item.home_team_name}}</p>
                      </div>
                    </div>
                    </el-radio-group>
                  </el-form>
                </div>
              </div>
            </div>
        </section>
      </div>
</template>

<script>
import { getLeagueRounds, getTeamListByRound, saveTeamSelection } from '@/api/league';
import { MakeAdminOfLeague } from '@/api/usersleague';
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
      message: {
        text: '',
        type: 'success'
      },
      iconPath: '/uploads/team/',
      list: [],
      checkedCategories: [],
      addTeamDialogVisible: false,
      form:{
       teamid:''
      },
      teamList: [],
      isChecked:1,
      make_admin:'',
      listLoading: true,
      activeR_id:'',
      activeS_id:'',
      noFixture: 'false'

    };
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.getList(id);
  },
  methods: {
    async getList(id) {
      this.listLoading = true;
      const { data } = await getLeagueRounds(id);
      if( data.status=='success' ) {
        this.list = data.data;
        this.getTeams(data.data[0].id, data.data[0].sport_id);
      } else {
          this.$message({
            message: data.message,
            type: 'warning',
            duration: 5 * 1000,
          });
      }
      //this.listLoading = false;
    },
    async getTeams(r_id,s_id) {
      this.listLoading= true;
      this.activeR_id = r_id;
      this.activeS_id = s_id;
      const league = this.$route.params && this.$route.params.id;
      getTeamListByRound(r_id, s_id, league).then(response => {
         if(response.data.status=='success') {
            this.teamList = response.data.data;
            this.listLoading= false;
            this.addTeamDialogVisible= true;
            if( response.data.selectedTeam ) {
               this.form.teamid = response.data.selectedTeam;
            }
         } else if (response.data.status=='error') {
           this.noFixture='true';
           
         } else {
             this.$message({
               message: response.data.message,
               type: 'warning',
               duration: 5 * 1000,
             });
         }
        }).catch(err => {
          console.log(err);
        });

    },
    async onTeamChange(e) {
    this.listLoading= true;
     const league = this.$route.params && this.$route.params.id;
     const r_id = this.activeR_id;
     const s_id = this.activeS_id;
     Bus.$emit('flash-message', this.message);
     saveTeamSelection(r_id, league, this.form.teamid).then(response => {
       if(response.data.status=='success') {

           // this.$message({
             // message: response.data.message,
             // type: 'success',
             // duration: 5 * 1000,
           // });
           console.log(response.data);
           this.message.type = 'success';
           this.message.text = response.data.message;
       } else {
           // this.$message({
           //   message: 'Something went Wrong!',
           //   type: 'warning',
           //   duration: 5 * 1000,
           // });
           
           this.message.type = 'error';
           this.message.text = response.data.message;
            const id = this.$route.params && this.$route.params.id;
            this.getList(id);
       }
       console.log(response);
     }).catch(err => {
       console.log(err);
     });
    }
  },
};
</script>

<style rel="stylesheet/scss" lang="scss">
.round-sec-left img {
    align-items: center;
    float: none;
    margin-right: 20px;
    position: absolute;
    left: 14px;
    top: 50%;
    height: 38px;
    transform: translateY(-50%);
}
span.el-radio__label img {
  width: 50px;
}
.team_formnew span.el-radio__input{
position: absolute;
top: 50%;
transform: translateY(-50%);
width: 100%;
left: 0;
height: 100%;
opacity: 0;
}
.team_formnew span.el-radio__input .el-radio__inner{
top: 50%;
}
.team_formnew span.el-radio__input.is-checked ~ .el-radio__label .round-sec-left,
.team_formnew span.el-radio__input.is-checked ~ .el-radio__label .round-sec-right{
  background-color: #2196F3;
  color: #fff;
}
</style>
