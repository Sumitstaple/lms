T<template>
  <section class="content-part bg-blue compitior_padding">
    <div class="container">
      <div class="row"> 
        
        <div class="last-stand-form search_box mt-4 bg-transparent col-sm-12">
          <form v-on:submit.prevent="onSubmit">
              <div class="row">
                <div class="col-sm-12">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon1"><i class="fa fa-search" aria-hidden="true"></i></span>
                    </div>
                    <input type="text" v-model="listQuery.title" placeholder="Search" name="search" class="form-control">
                    <button type="submit" @click="handleFilter"><span aria-hidden="true">&#10230;</span></button>
                  </div>
                </div> 
              </div>  
            </form>
        </div> 
        <div class="col-sm-12">
          <button class="compition-title border-radius_top text-left" type="button" data-toggle="collapse" data-target="#collapseExample" @click="collapse=!collapse" aria-expanded="false" aria-controls="collapseExample">
            <img src="images/filter-icon.png" class="mr-3">
            Filter
            <span><i :class="collapse?'fa fa-angle-up':'fa fa-angle-down'" aria-hidden="true"></i></span>
          </button>
        </div>
        
        <div class="col-sm-12"> 
          <form v-on:submit.prevent="onSubmit">
            <div :class="collapse?'collapse show':'collapse'" id="collapseExample">
            <div class="last-stand-form pb-40">
              <div class="row">
                <div class="col-sm-6"> 
                  <div class="form-group">
                    <div class="btn-group custom-select_op">
                      <a class="btn btn-primary dropdown-toggle" @click="sportSelect=!sportSelect" data-toggle="dropdown">{{sportSelectText}}<span class="caret"></span></a>
                      <ul :class="sportSelect?'dropdown-menu':'dropdown-menu show'" >
                        <li :class="{ 'active': activeSport === -1 }" @click="setSportActive('All', -1)"><a>All</a></li>
                        <li v-for="(item,index) in sportList" :class="{ 'active': activeSport === index }" :key="item.id" @click="setSportActive(item, index)"><a>{{ item.sport_name }}</a></li>
                      </ul>
                    </div>
                  </div>
                </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <div class="btn-group custom-select_op">
                        <a class="btn btn-primary dropdown-toggle" @click="typeSelect=!typeSelect" data-toggle="dropdown">{{typeSelectText}}<span class="caret"></span></a>
                        <ul :class="typeSelect?'dropdown-menu':'dropdown-menu show'">
                          <li :class="{ 'active': activeType === 'lms' }" @click="setTypeActive('lms')"><a>Last Man Standing</a></li>
                          <li :class="{ 'active': activeType === 'lml' }" @click="setTypeActive('lml')"><a>Last Man League</a></li>
                        </ul>
                      </div>
                    </div>
                </div>
                <div class="col-sm-12 mt-md-2">
                    <div class="form-group">
                      <vue-google-autocomplete
                        ref="address"
                        id="map"
                        classname="form-control"
                        placeholder="Location"
                        v-on:placechanged="getAddressData"
                        v-on:inputChange="inputLocationChange"
                        types="(cities)"
                        country="aus">
                      </vue-google-autocomplete>
                  </div>
                </div>
              </div>
            </div>
            </div>

            <div class="search-listing mt-3">
              <div class="table-responsive">
                <table class="table mblie_tble" v-loading="listLoading">
                  <tbody v-if="list.length > 0">
                    <tr v-for="scope in list" v-if="scope.status == 'active'">
                      <td>
                        <router-link v-if="(scope.process_status == 'progress' || scope.process_status == 'complete') && scope.is_private === 'no'" :to="'/league/'+scope.id">
                        {{scope.league_name}}<br/>
                        <strong>{{scope.location.league_city}} {{scope.location.league_state}}, {{scope.location.league_country}}</strong>
                      </router-link>
                      <router-link v-else to="">
                        {{scope.league_name}}<br/>
                        <strong>{{scope.location.league_city}} {{scope.location.league_state}}, {{scope.location.league_country}}</strong>
                      </router-link>
                      </td>
                      <!-- <td>{{ scope.status }}</td> -->
                      <td v-b-tooltip.hover.right :title="scope.sport.sport_name">
                        <img class="search_sport_icon" :src="'uploads/sport/'+scope.sport.sport_icon">
                      </td>
                      <td>
                        <span v-if="scope.process_status == 'progress' || scope.process_status == 'complete'" class="round dot"></span> 
                        <span v-if="scope.process_status == 'pending'" class="round dot success"></span> 
                        <span v-if="scope.process_status != 'pending' ">Unavailable</span>
                        <span v-if="scope.process_status == 'pending'">Available</span>
                      </td>
                      <td>

                      <img v-b-tooltip.hover.right :title="'LAST MAN STANDING'" v-if="scope.type == 'lms'" class="lms_image" src="/images/lms2.png">  
                      <img v-b-tooltip.hover.right :title="'LAST MAN LEAGUE'" v-if="scope.type == 'lml'" class="lml_image" src="/images/lml.png">  

                    </td>
                      <td>
                        <img src="/images/unlock.png" v-if="scope.is_private == 'no'">
                        <img src="/images/lock.png" v-if="scope.is_private == 'yes'">
                      {{scope.is_private == 'yes'? 'Private':'Public' }}</td>
                      <td>
                        <a v-if="scope.process_status == 'pending'" @click="joinLeague(scope.id)" class="btn_join">Join</a>
                        <span v-if="scope.joined == 'yes' && scope.is_private == 'no'">Already Joined</span>
                        <span v-if="scope.joined == 'yes' && scope.is_private == 'yes'">Request sent</span>
                      </td>
                    </tr>
                   
                  </tbody>
                  <tbody v-else>
                     <tr>
                      <td colspan="5">No Data Found</td>
                    </tr>
                  </tbody>
                </table>
                <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
              </div>
            </div>
          </form>
         </div>
      </div>
    </div>
  </section>
</template>

<script>
import Vue from 'vue';
import { findLeague } from '@/api/usersleague';
import { updateLeagueStatus,sendInvitation } from '@/api/league';
import Sortable from 'sortablejs';
import Resource from '@/api/resource';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
const leagueResource = new Resource('usersleague');
import { fetchSportList } from '@/api/sport';
import { fetchLeagueList } from '@/api/league';
import VueGoogleAutocomplete from 'vue-google-autocomplete';
const userResource = new Resource('join/leauge');
import VeeValidate from 'vee-validate';
Vue.use(VeeValidate);

export default {
  name: 'LeagueList',
  components: { Pagination, VueGoogleAutocomplete },

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
        type: ''
      },
      iconPath: '/uploads/sport/',
      list: [],
      activeType: 'lms',
      typeSelect: true,
      typeSelectText: 'Last Man Standing',
      sportSelectText: 'Sport',
      sportSelect: true,
      collapse: false,
      total: null,
      listLoading: true,
      activeSport: -1,
      listQuery: {
        page: 1,
        limit: 50,
        title: undefined,
        sport_id: undefined,
        location: undefined,
        type: undefined,
        league_city:'',
        league_state:'',
        league_country:'',
        league_town:'',
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
      sportList: [],
      leaugeUserForm: {
        id: '',
      },
      address: '',
    };
  },
  created() {
    this.listQuery.type="lms";
    this.listQuery.league_country="empty";
    this.getList();
    this.getSportList();
  },
  methods: {
    setSportActive(sport, index) {
      if (index == -1) {
        this.listQuery.sport_id = undefined;
        this.sportSelectText = 'Sport';
      } else {
        this.listQuery.sport_id = sport.id;
        this.sportSelectText = sport.sport_name;
      }

      this.activeSport = index;
      this.sportSelect = true;
      this.handleFilter();

    },

    setTypeActive(type) {
        this.listQuery.type = type;
        if (type == 'lms') {
          this.typeSelectText = 'Last Man Standing';
        } else {
          this.typeSelectText = 'Last Man League';
        }
        this.activeType = type;
        this.typeSelect = true;
        this.handleFilter();
    },
    getAddressData: function (addressData, placeResultData, id) {
        this.address = addressData;
        // this.postForm.league_city = addressData.locality;
        this.listQuery.league_city = placeResultData.vicinity;
        this.listQuery.league_state = addressData.administrative_area_level_1;
        this.listQuery.league_country = addressData.country;
        this.listQuery.league_town = addressData.route;
        this.handleFilter();

    },
    inputLocationChange: function (addressData, placeResultData, id) {
      if (addressData.newVal == '') {
        this.listQuery.league_city = '';
        this.listQuery.league_state = '';
        this.listQuery.league_country = '';
        this.listQuery.league_town = '';
      }
    },
    changeRadioHandler(data) {

    },
    async getList() {
      if (this.listQuery.location === '') {
         this.listQuery.league_city = '';
      }
      this.listLoading = true;
      const { data } = await findLeague(this.listQuery);
      this.list = data.items.data;
      this.listLoading = false;
    },
    async getSportList() {
      this.listLoading = true;
      const { data } = await fetchSportList(this.listQuery);
      this.sportList = data.items;
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
        setSort() {
      const el = this.$refs.dragTable.$el.querySelectorAll('.mblie_tble > tbody')[0];
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
    handleFilter() {
      this.listQuery.page = 1;
      this.getList();
    },

    joinLeague(id) {
       
        this.listLoading = true;
        if (id) {
          this.leaugeUserForm.id = id;
          userResource
            .store(this.leaugeUserForm)
              .then(response => {
                this.getList();
                this.listLoading = false;
                 Bus.$emit('flash-message', this.message);
                if (response.data.status === 'active') {

                  this.message.text = 'You have joined the ' + response.data.league_name + ' competition';
                  this.message.type = 'success';
                  // this.$message({
                  //   message: 'You have joined the ' + response.data.league_name + ' competition',
                  //   type: 'success',
                  //   duration: 5 * 1000,
                  // });
                }
                else if (response.data.status === 'request_pending') {

                  this.message.text = 'Your request to join the competition ' + response.data.league_name + ' has been submitted';
                  this.message.type = 'success';

                  // this.$message({
                  //   message: 'Your request to join the competition ' + response.data.league_name + ' has been submitted',
                  //   type: 'success',
                  //   duration: 5 * 1000,
                  // });
                }
                else if (response.data.status === 'exist') {

                  this.message.text = 'You have already joined the competition';
                  this.message.type = 'error';
                  document.getElementById("focus").focus();

                  // this.$message({
                  //   message: 'You have already joined the competition',
                  //   type: 'success',
                  //   duration: 5 * 1000,
                  // });
                } 

                else if (response.data.status === 'existprivate') {

                  this.message.text = 'You have already sent the request to join the competition.';
                  this.message.type = 'error';
                  document.getElementById("focus").focus();

                  // this.$message({
                  //   message: 'You have already joined the competition',
                  //   type: 'success',
                  //   duration: 5 * 1000,
                  // });
                }
                else {

                  this.message.text = 'Url is invalid';
                  this.message.type = 'error';

                  // this.$message({
                  //   message: 'Url is invalid',
                  //   type: 'error',
                  //   duration: 5 * 1000,
                  // });
                }
          });
        } else {
          console.log('error submitd!!');
          return false;
        }
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss">
img.search_sport_icon {
    width: 50px;
}
a.btn_join.display-none {
    cursor: not-allowed;
        display: none !important;
}
span.round.dot.success {
    background: #94db8c;
}
a.btn_join {
    background: #367ff7 !important;
}
.custom-select_op {
    width: 100%;
    border: 1px solid #DEDEDE;
    border-radius: 2px;
    background-image: url(/images/caret.png);
    background-repeat: no-repeat;
    background-position: right 13px center;
}
.custom-select_op .btn::after{
    display: none;
}
.custom-select_op .btn {
    background: transparent!important;
    border: none;
    border-radius: 0px;
    text-align: left;
    color: #333333!important;
    font-size: 14px;
    padding: 2px 26px;
    padding-top: 10px;
    padding-bottom: 10px;
}
.custom-select_op .btn:focus, .custom-select_op .btn:hover, .custom-select_op .btn:active{
    background: transparent!important;
    border: none;
    color: #333333!important;
    box-shadow: none!important;
    outline: none;
}
.custom-select_op ul.dropdown-menu {
    width: 100%;
    border-top: none;
    border-radius: 0px 0px 6px 6px;
    margin-top: 4px;
    z-index: 1;
}
.custom-select_op ul.dropdown-menu li {
    padding: 6px 10px 6px 26px;
}
.custom-select_op ul.dropdown-menu li a{
    color: #333333;
    font-size: 14px;
    position: relative;
}
.custom-select_op ul.dropdown-menu li a:hover, .custom-select_op ul.dropdown-menu li a:focus{
    text-decoration: none;
}
.custom-select_op ul.dropdown-menu li.active{
    background: transparent;
    color: #333333;
}
.custom-select_op ul.dropdown-menu li.active a:after {
    content: '';
    background-image: url(/images/check.png);
    background-repeat: no-repeat;
    height: 20px;
    width: 20px;
    position: absolute;
    background-size: 20px;
    background-position: center;
    overflow: visible;
    z-index: 999;
    right: -26px;
}
.pagination-container {
    display: block !important;
}
</style>
