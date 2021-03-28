<template>
  <div> 
<section class="content-part bg-blue">
            <div class="container">

            <!--   <div class="row" v-if="checktemplate == 0">
                <div class="col-sm-12">
                  <div class="compition-title text-center">
                    <h2>CREATE A COMPETITION</h2>
                  </div>
                </div>
                 <div class="col-sm-6"> 
                  <div class="compition-part">
                    <div class="compition-tag">
                      <img src="images/video1.jpg">
                    </div>
                    <router-link to="/league/find"><div class="compition-caption white-bg">
                      <p><strong>Find</strong> a competition <span>&#10230;</span></p>
                    </div>
                    </router-link> 
                  </div>
                </div>     
  
                <div class="col-sm-6">
                  <div class="compition-part">
                    <div class="compition-tag">
                      <img src="images/video2.jpg">
                    </div>
                    <router-link to="/league/home">
                      <div class="compition-caption white-bg">
                        <p><strong>Create</strong> a competition <span>&#10230;</span></p>
                      </div>
                    </router-link>
                  </div>
                </div>
 
              </div> -->


              <div class="row" v-if="checktemplate >= 1">
                <div class="col-sm-12">
                  <div class="top-message">
                    <div v-for="item in lmsleagues" v-if="item.is_team_pickup == 'no' && item.league.status == 'active' && item.league.get_round.process_status == 'pending' && item.is_knockedout == 'no'" class="alert alert-warning alert-dismissible fade show crossw_alrt" role="alert" :id="'remove'+item.id">
                      <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i> You need to choose {{ item.sport.sport_name }} {{ item.league.get_round.round_name }} for {{ item.league.league_name }}  <span aria-hidden="true" class="hori_arrow"><router-link class="" :to="'/league/addteam/'+item.league.id">⟶</router-link></span>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span class="crs_alrt"><img src="images/cross-n.png" v-on:click="removeRow(item.id)"></span>
                      </button> 
                    </div>

                    <!-- <div class="alert alert-warning alert-dismissible fade show" role="alert">
                      <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i> You need to tip for NRL Round 3
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">⟶</span>
                      </button>
                    </div> -->
                  </div>
                </div>
              </div>

              <div class="section-data round-page standing_table" v-if="checktemplate >= 1">
                <div class="table-heading" style="background-color: #FF4954;" v-if="this.lmscount.length >= 1">
                  <h2>LAST MAN STANDING</h2>
                </div>
                <div class="table-responsive">
                <table class="table">
                  <!--<thead>
                    <tr>
                      <th scope="col"></th>
                      <th scope="col"></th>
                      <th scope="col">LAST MAN STANDING</th>
                      <th scope="col"></th>
                      <th scope="col"></th>
                    </tr>
                  </thead>--->
                  <tbody>
                    <tr v-for="item in lmsleagues" v-if="item.league.type == 'lms' && item.league.status == 'active'" :key="item.id">
                      <td v-b-tooltip.hover.right title="You have selected your team" class="success-bg" v-if="item.is_team_pickup == 'yes' && item.is_knockedout == 'no'"><img src="images/check-mark.png"></td>
                      <td v-b-tooltip.hover.right title="You need to pick a team" class="pending-bg" v-if="item.is_team_pickup == 'no' && item.is_knockedout == 'no'"><img src="images/question-mark.png"></td>

                      <td v-b-tooltip.hover.right title="Your are out" class="success-danger" v-if="item.is_knockedout == 'yes'"><img src="images/close-bttn.png"></td> 

                      <td><div class="cmnnt-div"><router-link v-b-tooltip.hover.right title="League comments" :to="'/league/'+item.league.id"><img src="images/comment-blue.png"><span v-if="item.league.commentcount != null">{{item.league.commentcount.comment_count}}</span></router-link></div> <router-link :to="'/league/'+item.league.id">{{ item.league.league_name }}</router-link></td>
                       <td v-if="item.league.process_status === 'pending' && getStartDate(item.league.get_start_round.start_datetime) > 0">
                        <div v-if="item.league.process_status === 'pending' && getStartDate(item.league.get_start_round.start_datetime) > 0" class="time-part">
                        <countdown :time="getStartDate(item.league.get_start_round.start_datetime)">
                        <template slot-scope="props">{{ props.days?props.days+'d':'' }} {{ props.hours }}h {{ props.minutes }}m</template>
                      </countdown>
                      </div>
                        <div v-if="item.league.process_status !== 'pending' && getStartDate(item.league.get_start_round.start_datetime) > 0" class="time-part">
                         <countdown :time="getStartDate(item.league.get_round.start_datetime)">
                        <template slot-scope="props">{{ props.days?props.days+'d':'' }} {{ props.hours }}h {{ props.minutes }}m</template>
                      </countdown> 
                      </div>
                      </td>
                      <td>
                        <div style="display: -webkit-inline-box;padding-right: 3px;" v-b-tooltip.hover.right :title="item.sport.sport_name"><img :src="'uploads/sport/'+item.sport.sport_icon" style="width: 25px;  height: 25px;"></div><div v-b-tooltip.hover.right title="League Round" style="display: -webkit-inline-box;">{{ item.league.get_round.round_name }}</div></td>

                      <td class="text-center">
                        <span v-if="item.is_team_pickup == 'yes' && item.is_knockedout == 'no'"><router-link class="teamspan" :to="'/league/addteam/'+item.league.id"><div class="teamim"><img :src="'uploads/team/'+item.league.team.team_icon"></div><div class="teamtext">{{ item.league.team.team_name }}</div></router-link></span>

                        <span v-if="item.is_team_pickup == 'no' && item.is_knockedout == 'no'"><router-link class="table-btn" :to="'/league/addteam/'+item.league.id">Pick your team</router-link></span>

                        <span v-if="item.is_knockedout == 'yes'">Out</span>

                      </td>

                      <td v-bind:class="{ 'emptytd' : item.result_stats == null}"> 
                        <div v-if="item.result_stats != null">

                        <span v-for="result in item.result_stats.split(',').reverse().slice(Math.max(item.result_stats.split(',').length - 3, 0))" :class="'round dot success-'+result">
                         
                        </span> 
                      </div>
                      </td>
                     
                      <td class="btn-image text-center">
                        <router-link :to="'/league/update/'+item.league.id"><img v-if="item.is_admin == 'yes'" src="images/edit-icon.png" v-b-tooltip.hover.right title="Edit league"></router-link>

                       <a href="javascript:void(0);"><img v-if="item.is_admin == 'yes'" v-on:click="onLeagueDelete(item.league.id)" src="images/del-icon.png" v-b-tooltip.hover.right title="Delete league"></a>

                       <a href="javascript:void(0);"><img v-if="item.is_admin == 'yes'" v-b-modal.my-modal aria-hidden="true" v-on:click="inviteDialog(item.league.id,item.league.league_name)" src="images/shre-icon.png" v-b-tooltip.hover.right title="Share your league"></a> 
                       <router-link v-b-tooltip.hover.right title="Competition users" :to="'/league/user-league-detail/'+item.league.id"><img class="leagueusers" src="images/users.png"></router-link>

                     </td>
                    </tr>
                    
                  </tbody>
                </table>
              </div> 


              <div class="table-heading" style="background-color: #282855;" v-if="this.lmlcount.length >= 1">
                  <h2>LAST MAN LEAGUE</h2>
                </div>
                <div class="table-responsive">
                <table class="table">
                  <!--<thead>
                    <tr>
                      <th scope="col"></th>
                      <th scope="col"></th>
                      <th scope="col">LAST MAN STANDING</th>
                      <th scope="col"></th>
                      <th scope="col"></th>
                    </tr>
                  </thead>--->
                  <tbody>
                    <tr v-for="item in lmsleagues" v-if="item.league.type == 'lml' && item.league.status == 'active'">
                      <td v-b-tooltip.hover.right title="You have selected your team" class="success-bg" v-if="item.is_team_pickup == 'yes' && item.is_knockedout == 'no'"><img src="images/check-mark.png"></td>
                      <td v-b-tooltip.hover.right title="You need to pick a team" class="pending-bg" v-if="item.is_team_pickup == 'no' && item.is_knockedout == 'no'"><img src="images/question-mark.png"></td>

                      <td v-b-tooltip.hover.right title="Your are out" class="success-danger" v-if="item.is_knockedout == 'yes'"><img src="images/close-bttn.png"></td> 

                      <td><div class="cmnnt-div"><router-link v-b-tooltip.hover.right title="League comments" :to="'/league/'+item.league.id"><img src="images/comment-blue.png"><span v-if="item.league.commentcount != null">{{item.league.commentcount.comment_count}}</span></router-link></div> <router-link :to="'/league/'+item.league.id">{{ item.league.league_name }}</router-link></td>
                        <td v-if="item.league.process_status === 'pending' && getStartDate(item.league.get_start_round.start_datetime) > 0">
                        <div v-if="item.league.process_status === 'pending' && getStartDate(item.league.get_start_round.start_datetime) > 0" class="time-part">
                        <countdown :time="getStartDate(item.league.get_start_round.start_datetime)">
                        <template slot-scope="props">{{ props.days?props.days+'d':'' }} {{ props.hours }}h {{ props.minutes }}m</template>
                      </countdown>
                      </div>
                        <div v-if="item.league.process_status !== 'pending' && getStartDate(item.league.get_start_round.start_datetime) > 0" class="time-part">
                         <countdown :time="getStartDate(item.league.get_round.start_datetime)">
                        <template slot-scope="props">{{ props.days?props.days+'d':'' }} {{ props.hours }}h {{ props.minutes }}m </template>
                      </countdown> 
                      </div>
                      </td>
                      <td><div style="display: -webkit-inline-box;" v-b-tooltip.hover.right :title="item.sport.sport_name"><img :src="'uploads/sport/'+item.sport.sport_icon"></div><div v-b-tooltip.hover.right title="League Round" style="display: -webkit-inline-box;">{{ item.league.get_round.round_name }}</div></td>

                      <td class="text-center">

                        <span v-if="item.is_team_pickup == 'yes' && item.is_knockedout == 'no'"><router-link class="" :to="'/league/addteam/'+item.league.id"><img :src="'uploads/team/'+item.league.team.team_icon">{{ item.league.team.team_name }}</router-link></span>

                        <span v-if="item.is_team_pickup == 'no' && item.is_knockedout == 'no'"><router-link class="table-btn" :to="'/league/addteam/'+item.league.id">Pick your team</router-link></span>

                        <span v-if="item.is_knockedout == 'yes'">Out</span>

                      </td>
                      
                      <td v-bind:class="{ 'emptytd' : item.result_stats == null}">
                        <div v-if="item.result_stats != null">

                        <span v-for="result in item.result_stats.split(',').reverse().slice(Math.max(item.result_stats.split(',').length - 3, 0))" :class="'round dot success-'+result">
                         
                        </span> 
                      </div>

                      </td>

                      <td class="btn-image text-center">
                     <router-link :to="'/league/update/'+item.league.id"><img v-if="item.is_admin == 'yes'" src="images/edit-icon.png" v-b-tooltip.hover.right title="Edit league"></router-link>

                       <a href="javascript:void(0);"><img v-if="item.is_admin == 'yes'" v-on:click="onLeagueDelete(item.league.id)" src="images/del-icon.png" v-b-tooltip.hover.right title="Delete league"></a>

                       <a href="javascript:void(0);"><img v-if="item.is_admin == 'yes'" v-b-modal.my-modal aria-hidden="true" v-on:click="inviteDialog(item.league.id,item.league.league_name)" src="images/shre-icon.png" v-b-tooltip.hover.right title="Share your league"></a> 

                       <router-link v-b-tooltip.hover.right title="Competition users" :to="'/league/user-league-detail/'+item.league.id"><img class="leagueusers" src="images/users.png"></router-link>
                     </td>
                    </tr>
                    
                    
                  </tbody>
                </table>
              </div>
              <b-modal id="my-modal" v-model="modalShow">

              <form ref="form" :model="form" label-width="120px">
                 <div class="pop-share">
                  <h2 class="msg-ppup" v-if="newLeagueID > 0"> <img src="images/check-mark.png"> League {{this.inviteLeagueName}} has been created</h2>
                  <h2>SHARE THE LEAGUE!</h2>
                  <p>Get your mates to join up</p>
                  <ul>

                  <li class="">
                    <ShareNetwork
                      network="facebook"
                      :title="inviteLeagueName"
                      :url="inviteFbUrl+'?lms='+inviteLeagueID+'&id=0&mode=fb'"
                      :quote="inviteLeagueName+' league now opened If you want join then use this url \n '+inviteFbUrl+'?lms='+inviteLeagueID+'&id=0&mode=fb'"
                    >
                    <i class="fa fa-facebook" aria-hidden="true"></i>
                  </ShareNetwork>
                  </li>
                 <li class="">
                  <ShareNetwork
                    network="twitter"
                    :title="inviteLeagueName+' league now opened If you want join then use this url \n'"
                    text="here"
                    :url="inviteFbUrl+'?lms='+inviteLeagueID+'&id=0&mode=tw'"
                  >
                  <i class="fa fa-twitter" aria-hidden="true"></i>
                </ShareNetwork>
                </li>
                 </ul>
                
                  <p>You can add the multiple email addresses.<br/>
                  Use commas for seperation.</p>

                  <input type="text" v-model="form.desc" class="popupemail" placeholder="Email" />
                  
                 <button type="primary" class="btn btn-primary" @click="onInvitationSubmit" :disabled="inviteLoader">
                   Send Invitation
                 </button>
                 <div class="success-msg" style="display: none;">
                    <p>Form Submit Sucessfully.</p>
                  </div>
                 </div>
             </form>



              </b-modal>
              <div class="row mt-4">
                  <div class="col-sm-12 text-center">
                      <router-link to="/league/find" class="comptiton-btn mr-2"><span>Find a competition</span></router-link>
                      <router-link to="/league/home" class="comptiton-btn mr-2"><span>Create a competition</span></router-link>
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
import Vue from 'vue';
import VueSocialSharing from 'vue-social-sharing';
Vue.use(VueSocialSharing);
import VueCountdown from '@chenfengyuan/vue-countdown';
 
Vue.component(VueCountdown.name, VueCountdown);

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
      iconPath: '/uploads/sport/',
      list: [],
      total: null,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 10,
      },
      lmsleagues:[],
      lmscount:[],
      lmlleagues:[],
      lmlcount:[],
      sortable: null,
      oldList: [],
      newList: [],
      inviteDialogVisible: false,
      form: {
       desc: '',
       leagueid: ''
      },
      inviteLoader: false,
      checktemplate:'',
      inviteFbUrl:location.origin+"/#/register",
      inviteTwUrl:location.origin+"/#/register",
      inviteLeagueID:'',
      inviteLeagueName:'',
      inviteLoader: false,
      newLeagueID:0,
      newLeagueName:'',
      modalShow:false,
    };
  },
  watch: {
    $route: {
      handler: function(route) {
        this.newLeagueID = route.query && route.query.id;
        this.newLeagueName = route.query && route.query.name;
      },
      immediate: true,
    },
  },
  created() {
    this.getList();
    if(this.newLeagueID){
      this.inviteDialogNew(this.newLeagueID,this.newLeagueName);
    }
  },
  methods: {
    async getList() {
      this.listLoading = true;
      const { data } = await fetchLeagueList(this.listQuery);
      this.lmsleagues = data.items;
      this.lmlleagues = data.items;
      this.list = data.items;

      this.lmsleagues.map((contact) => {
        if(contact.league.type == "lms"){
          if(contact.league.status =='active'){

            this.lmscount.push(contact.league.type);
          }
        }
        else{
          if(contact.league.status =='active'){

            this.lmlcount.push(contact.league.type);
          }
        }
      });

      // console.log(this.lmscount,this.lmlcount);

      if(this.list.length == 0){
        // alert();
        this.checktemplate = this.list.length;
      }
      else{
          this.checktemplate = this.list.length;
      }

      if(this.checktemplate == 0){
         this.$router.push('/league/home');
      }
      this.listLoading = false;
    },
    async onLeagueDelete(id){ 

     this.listLoading = true;

      updateLeagueStatus(id)
        .then(response => {
        //   this.$message({
        //   message: 'League deleted successfully',
        //   type: 'success',
        //   duration: 5 * 1000,
        // });

        Bus.$emit('flash-message', this.message);
        this.message.type = 'success';
        this.message.text = 'League deleted successfully';

        })
        .catch(err => {
          console.log(err);
        });

      this.listLoading = false;
      this.getList();
    },
    inviteDialog ( id, name ) {
      this.newLeagueID = 0;
      this.newLeagueName = '';
      this.form.leagueid = id;
      this.inviteDialogVisible = true;
      this.inviteLeagueID = btoa(id);
      this.inviteLeagueName = name;
      this.form.desc = '';
    },
    inviteDialogNew ( id, name ) {
      this.form.desc = '';
      this.form.leagueid = id;
      this.inviteDialogVisible = true;
      this.inviteLeagueID = btoa(id);
      this.inviteLeagueName = name;
      if(this.newLeagueID){
        this.modalShow = true;
      }
    },
    getteamname(leagueid){
      alert();
    },
    removeRow(id){
      document.getElementById("remove"+id).style.display = "none";
    },
    async onInvitationSubmit () {
      this.inviteLoader = true;
       sendInvitation(this.form).then(response => {
          Bus.$emit('flash-message', this.message);

           if(response.status=='failed') {
             //  this.$message({
             //     message: response.message,
             //     type: 'warning',
             //     duration: 5 * 1000,
             // });

            this.message.type = 'success';
            this.message.text = response.message;

           }else{
            //  this.$message({
            //     message: response.message,
            //     type: 'success',
            //     duration: 5 * 1000,
            // });
            this.form.desc = '';
            this.message.type = 'success';
            this.message.text = response.message;
           }
           this.inviteLoader = false;
           console.log(response);
          }).catch(err => {
            console.log(err);
          });
    },

      transform(props) {
      Object.entries(props).forEach(([key, value]) => {
        // Adds leading zero
        const digits = value < 10 ? `0${value}` : value;
 
        // uses singular form when the value is less than 2
        const word = value < 2 ? key.replace(/s$/, '') : key;
 
        props[key] = `${digits} ${word}`;
      });
 
      return props;
    },
    getStartDate(date1) {
    let fulldate = new Date().toLocaleString("en", {timeZone: "Australia/Sydney",hour12:false});
    let date = new Date().toISOString().split('T')[0];
    let time = fulldate.split('.')[0];
    var date2 = date+' '+time;

    date1 = new Date(date1);  
    var date2 = new Date(fulldate);

    var diff = Math.floor(date1.getTime() - date2.getTime());
    return diff;
    },

  },
};
</script>

<style type="text/css">
  
 /* .round-page table.table tr td:nth-child(3) {
    width: auto !important;
}*/
.round-page table.table tbody tr td:nth-child(5) {
    font-size: 100% !important;
}
textarea {
    width: 100%;
}
/*.round-page table.table tr td:nth-child(2) {
     width: auto !important; 
}*/
table.table td {
    text-transform: capitalize;
}
a.table-btn {
    line-height: 18px;
}
.round-page table.table tr td:nth-child(1) span {
    line-height: 28px;
}
span.round img {
    width: 40px;
}
.pop-share {
    text-align: center;
}
.pop-share li.active {
    background: #ec585a
;
}
.pop-share ul li {
    display: inline-block;
    background: #ec585a;
    border-radius: 50px;
    width: 40px;
    height: 40px;
    text-align: center;
  padding: 6px 0px;
}
.pop-share ul li a {
   color: #282852;
    font-size: 18px;
}
.pop-share h2{
  color: #fff;      
  font-family: 'Titillium Web', sans-serif;
  font-weight: 900;
  font-size: 30px;
  letter-spacing: 0.5px;
}
.pop-share p{
  color: #fff;
  text-align: center;
  font-size: 18px;
  line-height: 1.4;
}
h2.msg-ppup img {
    width: 21px;
    padding-right: 5px;
}
div#my-modal___BV_modal_content_ .btn-primary {
    background-color: #ec585a;
    border-color: #ec585a;
    width: 250px;
    border-radius: 6px;
    display: block;
    align-items: center;
    margin: 0 auto;
    vertical-align: middle;
    color: #FFFFFF;
    font-family: "Titillium Web";
    font-size: 18px;
    font-weight: 900;
    letter-spacing: 0.58px;
    line-height: 18px;
    text-align: center;
    padding: 15px 0px;
    transition: all 0.5s;
    text-transform: uppercase;
}
div#my-modal___BV_modal_content_ .btn-primary:focus, div#my-modal___BV_modal_content_ .btn-primary:hover{
  background-color: #ec585a;
  border-color: #ec585a;
}
.pop-share input.popupemail {
    width: 88%;
    display: block;
    margin: 0 auto;
    height: 40px;
    padding: 7px 10px 10px;
    margin: 30px auto 40px;
}
.pop-share ul {
    margin: 30px 0px 40px;
}
div#my-modal___BV_modal_content_ {
    margin-top: 50px;
    background-color: #282852;
    padding-bottom: 40px;
}
div#my-modal___BV_modal_content_ button.close {
    opacity: 0.9;
    color: #fff;
    z-index: 999;
    margin-right: 0px;
    margin-top: 0;
    font-size: 43px;
    padding-top: 0px;
    padding-bottom: 0px;
}
.success-msg p{
  color: #fff;
  margin-top: 15px;
}
.msg-ppup{
font-size: 13px!important;
    background-color: #0081FF;
    border-radius: 5px;
    padding: 8px 10px;
    width: 270px;
    margin: 0 auto 23px;
    font-weight: normal !important;
}
.msg-ppup i {
    margin-right: 5px;
    font-size: 17px;
}.msg-ppup i {
    margin-right: 5px;
    font-size: 17px;
}
</style>