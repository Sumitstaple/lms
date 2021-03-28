<template>
  <div>
<section class="content-part bg-blue compitior_padding">
            <div class="container">
              <div class="row" v-loading="loading">
                <div class="col-sm-12">
                  <div class="compition-title text-center border-radius_top">
                    <h2 class="mb-0"><span class="league_title">{{league_type_name}}</span></h2>
                    
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="last-stand-form">
                    <form ref="postForm" :model="postForm" @submit.prevent="onSubmit" label-width="120px">   
                      <div class="row">
                        <div class="col-sm-6"> 
                          <h4 class="transparent">gggg</h4>  
                          <!-- <div class="form-group">
                            <div class="btn-group custom-select_op">
                              <a class="btn btn-primary dropdown-toggle" @click="typeSelect=!typeSelect" data-toggle="dropdown">{{typeSelectText}}<span class="caret"></span></a>
                              <ul :class="typeSelect?'dropdown-menu':'dropdown-menu show'">
                                <li :class="{ 'active': postForm.league_type === 'lms' }" @click="setTypeActive('lms')"><a>Last Man Standing</a></li>
                                <li :class="{ 'active': postForm.league_type === 'lml' }" @click="setTypeActive('lml')"><a>Last Man League</a></li>
                              </ul>
                            </div>
                          </div> -->

                          <div class="form-group">
                            <input type="text" v-model="postForm.league_name" name="leage_name" v-validate="'required'" :class="{'input form-control': true, 'is-danger form-control': errors.has('leage_name') }" placeholder="League Name">
                            <!-- <i v-show="errors.has('leage_name')" class="fa fa-warning"></i> -->
                            <span v-show="errors.has('leage_name')" class="help is-danger"><i class="fa fa-exclamation-circle" style=""></i>League name is required</span>
                          </div>
                          
                          <div class="form-group">
                            <div class="btn-group custom-select_op">
                              <a class="btn btn-primary dropdown-toggle" @click="sportSelect=!sportSelect" data-toggle="dropdown">{{sportSelectText}}<span class="caret"></span></a>
                              <ul :class="sportSelect?'dropdown-menu':'dropdown-menu show'" >
                                <li v-for="(item,index) in list" :class="{ 'active': activeSport === index }" :key="item.id" @click="onSportChange(item, index)"><a>{{ item.sport_name }}</a></li>
                              </ul>
                            </div>

                            <input type="hidden" v-model="check_sport" name="check_sport" value="" v-validate="'required'">
                            <span v-show="errors.has('check_sport')" class="help is-danger"><i class="fa fa-exclamation-circle" style=""></i>Please select a sport</span>
                          </div>

                          <div class="form-group">
                            <div class="btn-group custom-select_op">
                              <a class="btn btn-primary dropdown-toggle" @click="roundSelect=!roundSelect" data-toggle="dropdown">{{roundSelectText}}<span class="caret"></span></a>
                              <ul :class="roundSelect?'dropdown-menu':'dropdown-menu show'" >
                                <li v-for="(item,index) in rounds" :class="{ 'active': activeRound === index }" :key="item.id" @click="onRoundSelect(item, index)"><a>{{ item.round_name }}</a></li>
                              </ul>
                            </div>
                             <input type="hidden" v-model="check_round" name="check_round" value="" v-validate="'required'">
                            <span v-show="errors.has('check_round')" class="help is-danger"><i class="fa fa-exclamation-circle" style=""></i>Please select a round</span>
                          </div>
                          <div class="mt-20 radiobtn-new">
                          <div class="form-group radio-part">
                              <input type="radio" v-model="postForm.if_forfeit" name="if_forfeit" value="knocked_out" id="knocked_out" v-validate="'required'">
                              <label for="knocked_out" style="width: 100%;" v-if="postForm.league_type== 'lms'"><span>Forfeit and they are knocked out of the competition</span></label>

                              <label for="knocked_out" style="width: 100%;" v-if="postForm.league_type== 'lml'"><span>Forfeit this round and they are awarded 0 points.</span></label>
                              
                            </div>
                            <div class="form-group radio-part">
                              <input type="radio" v-model="postForm.if_forfeit" name="if_forfeit" value="assign_lowest_team" id="assign_lowest_team">
                              <label for="assign_lowest_team" style="width: 100%;"><span>They are awarded the lowest team they can pick.</span></label>
                            
                            </div>
                          </div>
                            <span class="help is-danger" v-show="errors.has('if_forfeit')"><i class="fa fa-exclamation-circle" style=""></i>Please select above option.</span>
                         <!--  <div class="form-group">
                            <date-picker v-model="postForm.start_datetime" :config="options" placeholder="Start Date" name="start_datetime" v-validate="'required'" :class="{'input form-control': true, 'is-danger form-control': errors.has('start_datetime') }"></date-picker>
                            <i v-show="errors.has('start_datetime')" class="fa fa-warning"></i>
                            <span v-show="errors.has('start_datetime')" class="help is-danger">Please  enter start date!</span>
                          </div>
                          <div class="form-group">
                            <date-picker v-model="postForm.end_datetime" :config="options" placeholder="End Date" name="end_datetime" v-validate="'required'" :class="{'input form-control': true, 'is-danger form-control': errors.has('end_datetime') }"></date-picker>
                            <i v-show="errors.has('end_datetime')" class="fa fa-warning"></i>
                            <span v-show="errors.has('end_datetime')" class="help is-danger">Please  enter end date!</span>
                          </div> -->
                        </div>

                        <div class="col-sm-6 league_location_desk">
                          
                          <div class="form-group">
                           <h4 class="text-center">Location <i class="fa fa-question-circle-o" aria-hidden="true"></i></h4>
                          <vue-google-autocomplete
                              ref="address"
                              id="map"
                              classname="form-control mymap"
                              placeholder="Enter your location to help your friends find your league"
                              v-on:placechanged="getAddressData"
                              v-on:keyup="validatelocation($event)"
                              types="(cities)"
                              country="aus"
                              name="hidelocation"
                              value=""
                              v-model="postForm.hidelocation"
                          >
                          </vue-google-autocomplete>
                           <input type="hidden" v-validate="'required'" v-model="postForm.location" value="" name="location">
                          <span class="help is-danger" v-show="errors.has('location')"><i class="fa fa-exclamation-circle" style=""></i>Please select the league location</span>

                          </div>

                          <div class="checkbox-part chk_radio">
                          <br>
                            <h4 class="text-center">League Type</h4>
                            <div class="form-group radio-part">
                              <input type="radio" v-model="postForm.is_private" name="is_private" value="yes" id="private" v-validate="'required'">
                              <label for="private"><span>Private</span></label>
                              <p class="">Your leagues performance will not show up the search results and if someone wants to join your league you have to approve it.</p>
                            </div>
                            <div class="form-group radio-part">
                              <input type="radio" v-model="postForm.is_private" name="is_private" value="no" id="open">
                              <label for="open"><span>Open</span></label>
                              <p class="">Your league results will show up in search results and anyone can join your league using your leagues custom link.</p>
                            </div>
                            <span class="help is-danger" v-show="errors.has('is_private')"><i class="fa fa-exclamation-circle" style=""></i>Please select league type</span>
                            <div class="form-group check-new"> 
                              <b-form-checkbox
                              id="checkbox-1"
                              v-model="postForm.is_banterboard"
                              name="is_banterboard"
                              value="yes"
                              unchecked-value="no"
                            >
                            <label for="banterboard"><span>Banter Board</span></label>
                              <p>A message board for you and your league mates to discuss results, performance and who has no chance of winning!</p>
                            </b-form-checkbox>
                            </div>
                           
            
                        <div class="form-group league_location_mobile">
                           <h4 class="text-center">Location <i class="fa fa-question-circle-o" aria-hidden="true"></i></h4>
                           <div class="form-group">
                          <vue-google-autocomplete
                              ref="address"
                              id="mapmobile"
                              classname="form-control mymap2"
                              placeholder="Enter your location to help your friends find your league"
                              v-on:placechanged="getAddressData"
                              v-on:keyup="validatelocation($event)"
                              types="(cities)"
                              country="aus"
                              name="hidelocation"
                              value=""
                              v-model="postForm.hidelocation"
                          >
                          </vue-google-autocomplete>
                           <input type="hidden" v-validate="'required'" v-model="postForm.location" value="" name="location">
                          <span class="help is-danger" v-show="errors.has('location')">Please select the league location!</span>

                          </div>
                        </div>
                        </div>

                        </div>

                        

                        <!--<div class="col-sm-12 leuage-text d-sm-block d-md-none">
                          <br>
                          <h4 class="text-left">League Type</h4>
                          <p class="">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque iaculis nisl dolor, sed vulputate nisi varius vitae.</p>

                          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque iaculis nisl dolor.</p>

                          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque iaculis nisl dolor.</p>
                        </div>-->
                        <div class="col-sm-12 text-center">
                            <button v-show="!shareButton" type="submit" class="submit-btn" >CREATE</button>
                            <button v-show="shareButton" type="button" v-b-modal.my-modal aria-hidden="true" class="submit-btn" >Share</button>

                            <button v-show="shareButton" v-on:click="showLeague()" type="button" class="submit-btn" >Competitions</button>
                            
                            
                        </div>
                      </div>

                    </form>
                  </div>
                </div>
                <b-modal id="my-modal" v-model="modalShow">

              <form ref="form" :model="form" label-width="120px">
                 <div class="pop-share">
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
                  <div v-show="error_msg" class="success-msg" style="display: none;">
                    <p>Please enter the emails.</p>
                  </div>
                 <button type="primary" class="btn btn-primary" @click="onInvitationSubmit" :disabled="inviteLoader">
                   Send Invitation
                 </button>
                 
                 </div>
             </form>



              </b-modal>
              </div>
            </div>
        </section>
</div>
</template>

<script>
import { fetchSportList } from '@/api/fixture';
import { fetchroundsbysportleague } from '@/api/round';
import Vue from 'vue';
import VeeValidate from 'vee-validate';

import VueGoogleAutocomplete from 'vue-google-autocomplete'
// Import this component


// Import date picker css
// import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
import Resource from '@/api/resource';
const leagueResource = new Resource('league');
import { sendInvitation } from '@/api/league';

Vue.use(VeeValidate);

export default {
  components: { VueGoogleAutocomplete },
  data() {
    return { 
      message: {
        text: '',
        type: ''
      },
      date: new Date(),
      options: {
        format: 'YYYY-MM-DD h:m:s',
        useCurrent: false,
      },
      league_type_name:'',
      check_sport:'',
      check_round:'',
      typeSelect: true,
      type: undefined,
      typeSelectText: 'Last Man Standing',
      sportSelectText: 'Please select a sport',
      sportSelect: true,
      activeSport: undefined,
      roundSelectText: 'Please select a round',
      roundSelect: true,
      activeRound: undefined,
      radioValue:'',
      team_icon:'',
      team_icon2:'',
      list:[],
      rounds:[],
      teams:[],
      postForm: {
        location:'',
        hidelocation:'',
        is_banterboard:'yes',
        sport_id:'',
        round_to_start:'',
        league_name:'',
        league_type:'lms',
        start_datetime:'',
        end_datetime:'',
        is_private:'',
        league_city:'',
        league_state:'',
        league_country:'',
        league_town:'',
        field2:'',
        if_forfeit:'',
      },
      loading: false,
      tempRoute: {},
      address: '',
      inviteLeagueName:'',
      inviteLeagueID:'',
      inviteDialogVisible: false,
      inviteFbUrl:location.origin+"/#/register",
      inviteTwUrl:location.origin+"/#/register",
      modalShow:false,
      form: {
       desc: '',
       leagueid: ''
      },
      shareButton:false,
      inviteLoader:false,
      error_msg:false,
    };
  },
  created() {

    this.tempRoute = Object.assign({}, this.$route);
    const var_league_type = this.$route.params && this.$route.params.type;

    if(var_league_type == "lms"){
      this.league_type_name = 'LAST MAN STANDING';
    }
    else{
      this.typeSelectText = 'Last Man League';
      this.league_type_name = 'LAST MAN LEAGUE';
    }

    this.postForm.league_type=var_league_type;
    this.getList();
    // alert(this.postForm.league_type); 

  },
  mounted() {
            // To demonstrate functionality of exposed component functions
            // Here we make focus on the user input
            // this.$refs.address.focus();

        },
  methods: {

     async onInvitationSubmit () {
      this.inviteLoader = true;
      this.error_msg = false;
       sendInvitation(this.form).then(response => {
          

           if(response.status=='failed') {

            //this.message.type = 'success';
            //this.message.text = response.message;
            this.error_msg = true;

           }else{
            this.form.desc = '';
            Bus.$emit('flash-message', this.message);
            this.message.type = 'success';
            this.message.text = response.message;
           }
           this.inviteLoader = false;
           console.log(response);
          }).catch(err => {
            console.log(err);
          });
    },

    setTypeActive(type) { 
        this.type = type;
        if (type == 'lms') {
          this.typeSelectText = 'Last Man Standing';
        } else {
          this.typeSelectText = 'Last Man League';
        }
        this.activeType = type;
        // this.postForm.league_type=type;
        this.typeSelect = true;
    },
    validatelocation(event){
      // alert(document.getElementsByClassName("mymap")[0].value);
      // alert();
      if(document.getElementsByClassName("mymap")[0].value){

      this.postForm.location = document.getElementsByClassName("mymap")[0].value;
      }
      else{
        
      this.postForm.location = document.getElementsByClassName("mymap2")[0].value;
      }
    },
    getAddressData: function (addressData, placeResultData, id) {
        this.address = addressData;
        // this.postForm.league_city = addressData.locality;
        this.postForm.league_city = placeResultData.vicinity;
        this.postForm.league_state = addressData.administrative_area_level_1;
        this.postForm.league_country = addressData.country;
        this.postForm.league_town = addressData.route;

    },
    changeRadioHandler(data) {
      if(data === "Last Man Standing (LMS)"){
        this.postForm.league_type = 'lms';
      }
      else{
        this.postForm.league_type = 'lml';
      }
    },
    onCancel() {
      this.$message({
        message: 'cancel!',
        type: 'warning',
      });
    },
    async getList() {
      this.listLoading = true;
      const { data } = await fetchSportList(this.listQuery);
      this.list = data.items;
    },

    async onSportChange(sport,index){
      

      if (index == -1) {
        this.postForm.sport_id = undefined;
        this.sportSelectText = 'Sport';
      } else {
        this.postForm.sport_id = sport.id;
        this.sportSelectText = sport.sport_name;
      }
     
      this.check_sport = sport.id;
      this.activeSport = index;
      this.sportSelect = true;

      this.listLoading = true;
      const { data } = await fetchroundsbysportleague(sport.id);
      this.rounds = data;
    },

    async onRoundSelect(round,index){

      if (index == -1) {
        this.postForm.round_to_start = undefined;
        this.roundSelectText = 'Please select a round';
      } else {
        this.postForm.round_to_start = round.id;
        this.roundSelectText = round.round_name;
      }
      this.check_round = round.id;
      this.activeRound = index;
      this.roundSelect = true;

    },

    onSubmit() {
      
      this.$validator.validateAll().then((result) => {
        if (result) {
       
          this.loading = true;
          leagueResource
            .store(this.postForm)
              .then(response => {
                if(response.success) {
                //this.inviteDialogVisible = true;
                //this.inviteLeagueID = btoa(response.data.id);
                //this.form.leagueid = response.data.id;
                //this.inviteLeagueName = response.data.league_name;

                this.loading = false;
                //this.modalShow = true;
                //this.shareButton = true;                
                // Bus.$emit('flash-message', this.message);
                // this.message.text = 'New League added successfully';
                // this.message.type = 'success';

                this.$router.push('/league/myleagues?id='+response.data.id+'&name='+response.data.league_name);
                } else {
                    this.loading = false;
                }
                
          });
          
        } else {
          this.loading = false;
          this.message.text = 'Error while adding league';
          this.message.type = 'error';
          return false;
        }
      });
    },
    showLeague(){
      this.$router.push('/league/myleagues');
    },
  },
};
</script>
<style rel="stylesheet/scss" lang="scss">
.help.is-danger {
    text-align: left;
    display: block;
    font-size: 12px;
    margin-bottom: 10px;
    color: red;
    padding: 10px 5px;
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
div#focus {
  top: 0px !important;
}    
/*.last-stand-form .radio-part label:before, .last-stand-form .radio-part label:after {
    content: none !important;
}*/
.check-new .custom-control.custom-checkbox {
    padding-left: 1.5rem;
}
.check-new .custom-checkbox .custom-control-label::before {
    left: -1.5rem;
    top: 0.25rem
}
</style>        