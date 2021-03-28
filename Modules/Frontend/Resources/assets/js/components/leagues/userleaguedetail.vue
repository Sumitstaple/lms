<template>
  <div> 
<section class="content-part bg-blue">
            <div class="container">
              <div class="section-data round-page standing_table">
                <div class="table-heading" style="background-color: #FF4954;">
                  <h2>{{league_type}}</h2>
                 
                </div>
                <div class="table-responsive">
                <table class="table" v-loading="listLoading">

                  <tbody>
 
                    <tr v-for="item in list"> 
                      <!-- <td><img src="images/comment-blue.png"><span>{{ item.league.id }}</span></td> -->
                      <td v-b-tooltip.hover.right title="You have selected your team" class="success-bg" v-if="item.is_team_pickup == 'yes' && item.is_knockedout == 'no'"><img src="images/check-mark.png"></td>
                      <td v-b-tooltip.hover.right title="You need to pick a team" class="pending-bg" v-if="item.is_team_pickup == 'no' && item.is_knockedout == 'no'"><img src="images/question-mark.png"></td> 

                      <td v-b-tooltip.hover.right title="Your are out" class="success-danger" v-if="item.is_knockedout == 'yes'"><img src="images/close-bttn.png"></td> 
                      <td>
                        <div v-if="item.user.nickname">
                          {{ item.user.nickname }}
                        </div>
                        <div v-if="!item.user.nickname">
                          {{ item.user.first_name }} {{ item.user.last_name }} 
                        </div>
                        <span class="nameavatar" v-if="item.user.avatar == 'user.png'">{{ item.user.first_name.charAt(0) }}</span>
                        <span v-if="item.user.avatar != 'user.png'"><img class="detail-img" :src="'uploads/profile/'+item.user.avatar"></span>
                      </td> 
                      <td v-if="item.league.is_private =='no' && item.is_knockedout=='no'">{{ item.status }}</td> 
                      <td v-if="item.league.is_private =='no' && item.is_knockedout=='yes'">Knocked Out</td> 
                      <td v-if="item.league.is_private =='yes'">
                        <div :class="{disabled:item.league.process_status == 'progress'}">
                        <span v-if="item.status == 'request_pending'" class="accept btn btn-success" @click="handleModifyStatus(item.id,item.user.id,'active')" >Accept</span>
                        <span v-if="item.status == 'request_pending'" class="accept btn btn-danger" @click="handleModifyStatus(item.id,item.user.id,'rejected')">Reject</span>
                      </div>
                        <span v-if="item.status == 'rejected'">Rejected</span>
                        <span v-if="item.status == 'active'">Active</span>
                      </td>
                      <td v-if="item.is_team_pickup == 'yes'">{{item.teamname}}</td>
                      <td v-if="item.is_team_pickup == 'no'">Team Not Picked </td>
                      <td></td>
                      <td v-if="item.is_admin == 'no'"><span >League User</span> </td>

                      <td v-if="item.is_admin == 'yes'">League Admin </td>

                      <td v-if="is_admin == 'yes'"> 
                        <input type="checkbox" :value="item.user.id" :checked="item.is_admin == 'yes'" @change="makeadmin(item.is_admin,item.user.id)" v-if="checkadmin == 'no' || item.is_play == 'no'" disabled>

                        <input type="checkbox" :value="item.user.id" :checked="item.is_admin == 'yes'" @change="makeadmin(item.is_admin,item.user.id)" v-if="checkadmin == 'yes' && item.is_play == 'yes' && item.status == 'active'">
                      </td>

                      
                    </tr>
                  </tbody>
                </table>
              </div> 
              <b-modal id="my-modal">

              <form ref="form" :model="form" label-width="120px">
                 <div class="pop-share">
                  <h2>SHARE THE LEAGUE!</h2>
                  <p>Get your mates to join up</p>
                  <ul><li class="active"><a href="#"><i class="fa fa-envelope" aria-hidden="true"></i></a></li>
                  
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
                  <input type="email" v-model="form.desc" class="popupemail" placeholder="Email" />
                  
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
                      <!-- <router-link to="/league/find" class="comptiton-btn mr-2"><span>Find a competition</span></router-link>
                      <router-link to="/league/home" class="comptiton-btn mr-2"><span>Create a competition</span></router-link> -->

                      <a src="#" class="comptiton-btn mr-2" v-b-modal.my-modal aria-hidden="true" v-on:click="inviteDialog()"><span>Invite Users</span></a>
                      
                  </div>
              </div>

              </div>
            </div>
        </section>
      </div>
</template>        


<script>
import { fetchLeagueDetail, fetchUserLeagueDetail } from '@/api/usersleague';
import { sendInvitation } from '@/api/league';
import { MakeAdminOfLeague, acceptUserRequest } from '@/api/usersleague';
import Sortable from 'sortablejs';
import Resource from '@/api/resource';
import { mapGetters } from 'vuex';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
const leagueResource = new Resource('usersleague');

export default {
  name: 'LeagueList',
  components: { Pagination },
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
    computed: {
    ...mapGetters([
      'sidebar',
      'name',
      'avatar',
      'device',
      'userId',
      'siteview',
    ]),
  },
  data() {
    return {
       message: {
        text: '',
        type: ''
      },
      list: [],
      checkedCategories: [],
      isChecked:1,
      make_admin:'',
      listLoading: true,
      league_type:'',
      checkadmin:'',
      check_is_play:'',
      is_admin:'',
      inviteLoader: false,
      checktemplate:'',
      inviteFbUrl:location.origin+"/#/register",
      inviteTwUrl:location.origin+"/#/register",
      inviteLeagueID:'',
      inviteLeagueName:'',    
      form: {
       desc: '',
       leagueid: ''
      },
    };
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.getList(id);
    this.getuserLeague(this.userId,id);
  },
  methods: {

    async getuserLeague(user, id) {

      const { data } = await fetchUserLeagueDetail(user, id);
      this.checkadmin = data.items[0].is_admin;
      this.is_admin = data.items[0].is_admin;
      this.check_is_play = data.items[0].is_play;

  },
  inviteDialog () {
    const id = this.$route.params && this.$route.params.id;
     this.form.leagueid = id;
      this.inviteDialogVisible = true;
      this.inviteLeagueID = btoa(id);
      this.inviteLeagueName = this.league_type;
    },

    async onInvitationSubmit () {
      this.inviteLoader = true;
      //alert(this.form);
       sendInvitation(this.form).then(response => {
          Bus.$emit('flash-message', this.message);

           if(response.status=='failed') {
            this.message.type = 'error';
            this.message.text = response.message;

           }else{
            this.message.type = 'success';
            this.message.text = response.message;
           }
           this.inviteLoader = false;
           console.log(response);
          }).catch(err => {
            console.log(err);
          });
    },
    async getList(id) {


      this.listLoading = true;
      const { data } = await fetchLeagueDetail(id);
      this.list = data.items;

      console.log(this.list);

      this.league_type = this.list[0].league.league_name;
      this.listLoading = false;
    },
    
    async makeadmin(status,user_id) {

      const league = this.$route.params && this.$route.params.id;
      this.listLoading = true;
    //   if(this.check_is_play == 'yes' && status == "yes"){

    //      this.$message({
    //       message: 'You do not have the permission for this',
    //       type: 'fail',
    //       duration: 5 * 1000,
    //     });

    // //      const id = this.$route.params && this.$route.params.id;
    // this.getList(league);

    //      return false;

    //   }


      if (status == "no") {

        this.make_admin = "yes" 
      }
      if (status == "yes") {
        this.make_admin = "no"
      }
      
      const { data } =  await MakeAdminOfLeague(league,user_id,this.make_admin);
      if(data.status == "leagueadmin"){

        Bus.$emit('flash-message', this.message);

        this.message.text = 'You have make admin successfully';
        this.message.type = 'success';

      }
      else{

        Bus.$emit('flash-message', this.message);

        this.message.text = 'You have successfully remove the user as admin.';
        this.message.type = 'success';

      }
        this.listLoading = false;
        this.getList(league);
    },
    async handleModifyStatus(leagueid, userid, status) {
      
      this.listLoading = true;
      const { data } =  await acceptUserRequest(leagueid,userid,status);

      if (status === 'active' ) {
        const id = this.$route.params && this.$route.params.id;
        this.getList(id);

        Bus.$emit('flash-message', this.message);

        this.message.text = 'User request accepted';
        this.message.type = 'success';
        this.listLoading = false;

      } else {
         const id = this.$route.params && this.$route.params.id;
          this.getList(id);

        Bus.$emit('flash-message', this.message);

        this.message.text = 'User request rejected';
        this.message.type = 'error';
        this.listLoading = false;
      }

      row.status = status;
    },
  },
};
</script>

<style type="text/css">
  
  .disabled span {
    pointer-events: none;
}
a:not([href]):hover {
    color: #fff;
    text-decoration: none;
}
    background: #ccc;
    border-color: #ccc;
</style>