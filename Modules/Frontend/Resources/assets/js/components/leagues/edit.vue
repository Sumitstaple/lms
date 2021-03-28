<template>
  <div>
<section class="content-part bg-blue compitior_padding">
            <div class="container">
              <div class="row">
                <div class="col-sm-12">
                  <div class="compition-title text-center border-radius_top">
                    <h2 class="mb-0"><span class="league_title">LAST MAN STANDING</span></h2>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="last-stand-form">
                    <form ref="postForm" :model="postForm" @submit.prevent="onSubmit" label-width="120px">
                      <div class="row"> 
                        <div class="col-sm-6">
                          <h4 class="transparent">gggg</h4> 
                        

                          <div class="form-group">
                            <div class="btn-group custom-select_op">
                              <a class="btn btn-primary dropdown-toggle disabled" @click="typeSelect=!typeSelect" data-toggle="dropdown">{{typeSelectText}}<span class="caret"></span></a>
                              <ul :class="typeSelect?'dropdown-menu':'dropdown-menu show'">
                                <li :class="{ 'active': postForm.league_type === 'lms' }" @click="setTypeActive('lms')"><a>Last Man Standing</a></li>
                                <li :class="{ 'active': postForm.league_type === 'lml' }" @click="setTypeActive('lml')"><a>Last Man League</a></li>
                              </ul>
                            </div>
                          </div>

                          <div class="form-group">
                            <input type="text" v-model="postForm.league_name" name="leage_name" v-validate="'required'" :class="{'input form-control': true, 'is-danger form-control': errors.has('leage_name') }" placeholder="League Name">
                          <!--   <i v-show="errors.has('leage_name')" class="fa fa-warning"></i> -->
                            <span v-show="errors.has('leage_name')" class="help is-danger"><i class="fa fa-exclamation-circle" style=""></i> League name is required!</span>
                          </div>

                          <div class="form-group">
                            <div class="btn-group custom-select_op">
                              <a class="btn btn-primary dropdown-toggle disabled" @click="sportSelect=!sportSelect" data-toggle="dropdown">{{sportSelectText}}<span class="caret"></span></a>
                              <ul :class="sportSelect?'dropdown-menu':'dropdown-menu show'" >
                                <li v-for="(item,index) in list" :class="{ 'active': activeSport === item.id }" :key="item.id" @click="onSportChange(item, index)"><a>{{ item.sport_name }}</a></li>
                              </ul>
                            </div>
                          </div>

                          <div class="form-group">
                            <div class="btn-group custom-select_op">
                              <a class="btn btn-primary dropdown-toggle disabled" @click="roundSelect=!roundSelect" data-toggle="dropdown">{{roundSelectText}}<span class="caret"></span></a>
                              <ul :class="roundSelect?'dropdown-menu':'dropdown-menu show'" disabled >
                                <li v-for="(item,index) in rounds" :class="{ 'active': activeRound == item.id }" :key="item.id" @click="onRoundSelect(item, index)"><a>{{ item.round_name }}</a></li>
                              </ul>
                             
                            </div> 
                          </div>

                            <div class="form-group radio-part">
                              <input type="radio" v-model="postForm.if_forfeit" name="if_forfeit" value="knocked_out" id="knocked_out" v-validate="'required'" disabled>
                               <label for="knocked_out" style="width: 100%;" v-if="postForm.league_type== 'lms'"><span>Forfeit and they are knocked out of the competition</span></label>

                              <label for="knocked_out" style="width: 100%;" v-if="postForm.league_type== 'lml'"><span>Forfeit this round and they are awarded 0 points.</span></label>
                              
                            </div>
                            <div class="form-group radio-part">
                              <input type="radio" v-model="postForm.if_forfeit" name="if_forfeit" value="assign_lowest_team" id="assign_lowest_team" disabled>
                              <label for="assign_lowest_team" style="width: 100%;"><span>They are awarded the lowest team they can pick.</span></label>
                            
                            </div>
                            <span class="help is-danger" v-show="errors.has('if_forfeit')"><i class="fa fa-exclamation-circle" style=""></i>Please select above option.</span>
                        
                        </div>

                        <div class="col-sm-6 league_location_desk">
                          
                          <div class="form-group">
                           <h4 class="text-center">Location <i class="fa fa-question-circle-o" aria-hidden="true"></i></h4>
                          <vue-google-autocomplete
                              ref="address"
                              id="map"
                              classname="form-control"
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
                            <h4 class="text-center d-none d-md-block">League Status</h4>
                            <div class="form-group radio-part">
                              <input type="radio" v-model="postForm.is_private" name="is_private" value="yes" id="private" v-validate="'required'" disabled>
                              <label for="private"><span>Private</span></label>
                              <p>Your leagues performance will not show up the search results and if someone wants to join your league you have to approve it.</p>
                            </div>
                            <div class="form-group radio-part">
                              <input type="radio" v-model="postForm.is_private" name="is_private" value="no" id="open" disabled>
                              <label for="open"><span>Open</span></label>
                              <p>Your league results will show up in search results and anyone can join your league using your leagues custom link.</p>
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


                            <!-- <span class="help is-danger" v-show="errors.has('is_banterboard')"><i class="fa fa-exclamation-circle" style=""></i>Please check Banter Board option</span> -->


                          <div class="form-group league_location_mobile">
                           <h4 class="text-center">Location <i class="fa fa-question-circle-o" aria-hidden="true"></i></h4>
                           <div class="form-group">
                          <vue-google-autocomplete
                              ref="address"
                              id="mapmobile"
                              classname="form-control"
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

                        <div class="col-sm-12 text-center">
                            <button type="submit" class="submit-btn" >UPDATE</button>
                        </div>
                      </div>

                    </form>
                  </div>
                </div>
              </div>
            </div>
        </section>
</div>
</template>

<script>
import { fetchSportList } from '@/api/fixture';
import { fetchroundsbysportleague } from '@/api/round';
import { fetchLeague } from '@/api/league';
import VueGoogleAutocomplete from 'vue-google-autocomplete'
import Resource from '@/api/resource';

// Import date picker css
// import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
import Vue from 'vue';
import VeeValidate from 'vee-validate';

const leagueResource = new Resource('league');

Vue.use(VeeValidate);

export default {
  components: { VueGoogleAutocomplete },
  data() {
    return {
       message: {
        text: '',
        type: ''
      },
      list:[],
      rounds:[],
      date: new Date(),
      options: {
        format: 'YYYY-MM-DD h:m:s',
        useCurrent: false,
      },
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
      postForm: {
        hidelocation:'',
        location:'',
        sport_id:'',
        round_to_start:'',
        round_id:'',
        league_name:'',
        league_type:'',
        is_banterboard:'',
        start_datetime:'',
        end_datetime:'',
        is_private:'',
        league_city:'',
        league_state:'',
        league_country:'',
        league_town:'',
        type:'',
        field2:'', 
        if_forfeit:'', 
      },
      loading: false,
      tempRoute: {},
    };
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.getList(id);
    this.getSportsList();

  },
  methods: {

    async onRoundSelect(round,index){

      if (index == -1) {
        this.postForm.round_to_start = undefined;
        this.roundSelectText = 'Please select a round';
      } else {
        this.postForm.round_to_start = round.id;
        this.roundSelectText = round.round_name;
      }
      this.check_round = round.id;
      this.activeRound = round.id;
      this.roundSelect = true;

    },

    setTypeActive(type) { 
        this.type = type;
        if (type == 'lms') {
          this.typeSelectText = 'Last Man Standing';
        } else {
          this.typeSelectText = 'Last Man League';
        }
        this.activeType = type;
        this.postForm.league_type=type;
        this.typeSelect = true;
    },

    validatelocation(event){
      // alert(document.getElementById("map").value);
      this.postForm.location = document.getElementById("map").value;
    },
    getAddressData: function (addressData, placeResultData, id) {
      // console.log(addressData);
      // console.log(placeResultData.vicinity);
      this.address = addressData;
      this.postForm.league_city = placeResultData.vicinity;
      this.postForm.league_state = addressData.administrative_area_level_1;
      this.postForm.league_country = addressData.country;
      this.postForm.league_town = addressData.route;

    },
    async getList(id) {
      this.listLoading = true;
      fetchLeague(id)
        .then(response => {
          this.postForm = response.data[0];
          this.getRounds(this.postForm.sport_id);
          this.postForm.round_id = response.data[0].round.id
          this.postForm.league_type = this.postForm.type;
          this.postForm.is_banterboard = this.postForm.is_banterboard;

          this.postForm.hidelocation = this.postForm.location.league_city+' '+this.postForm.location.league_state+', '+this.postForm.location.league_country;

          if(this.postForm.league_type == "lml"){
            this.typeSelectText = 'Last Man League';
          }
          this.activeSport = this.postForm.sport_id;
          this.activeRound = this.postForm.round_to_start;
          this.sportSelectText = response.data[0].sport.sport_name;
          this.roundSelectText = response.data[0].round.round_name;
          this.postForm.league_city = this.postForm.location.league_city;
          this.postForm.league_state = this.postForm.location.league_state;
          this.postForm.league_country = this.postForm.location.league_country;
          
          
        })
        .catch(err => {
          console.log(err);
        });
    },

    async getSportsList() {
      this.listLoading = true;
      const { data } = await fetchSportList(this.listQuery);
      this.list = data.items;
    },
    async getRounds(id){

      this.listLoading = true;
      const { data } = await fetchroundsbysportleague(id);
      this.rounds = data;

    },
    async onSportChange(sport,index){
      console.log(sport.id,index);
      if (index == -1) {
        this.postForm.sport_id = undefined;
        this.sportSelectText = 'Sport';
      } else {
        this.postForm.sport_id = sport.id;
        this.sportSelectText = sport.sport_name;
      }
     
      this.check_sport = sport.id;
      this.activeSport = sport.id;
      this.sportSelect = true;

      this.listLoading = true;
      const { data } = await fetchroundsbysportleague(sport.id);
      this.rounds = data;

 
    },

    onSubmit() {
    
    this.$validator.validateAll().then((result) => {
        if (result) {
          this.loading = true;
          leagueResource
            .update(this.postForm.id,this.postForm)
              .then(response => {
                 Bus.$emit('flash-message', this.message);

                this.message.text = 'League updated successfully';
                this.message.type = 'success';
          });
          this.loading = false;
          this.$router.push('/league/myleagues');
        } else {

          this.message.text = 'Error during league updation';
          this.message.type = 'error';
          return false;
        }
      });
    },
    onCancel() {
      this.$message({
        message: 'cancel!',
        type: 'warning',
      });
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="css" scoped>
.line{
  text-align: center;
}
img{
  width: 20%;
  height: 10%;
  display: block;
  margin-bottom: 10px;
}
.el-select {
    width: 50%;
}
input#map {
    width: 359px;
    height: 35px;
    border: 1px solid #dcdfe6;
    border-radius: 4px;
    padding: 0px 5px;
}

</style>
