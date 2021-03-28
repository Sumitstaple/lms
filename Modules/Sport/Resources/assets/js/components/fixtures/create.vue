<template>
  <div class="app-container">
    <el-form ref="postForm" :model="postForm" :rules="sportRules" label-width="120px">
      <el-form-item label="Select Sport" prop="sport_id">
        <el-select v-model="postForm.sport_id" placeholder="Please select your sport" v-on:change="onSportChange($event)" :disabled="!isSportSelectable">
          <el-option v-for="item in list" :label="item.sport_name" :value="item.id" />
        </el-select>
      </el-form-item>
      <el-form-item label="Select Round" prop="round_id">
        <el-select v-model="postForm.round_id" placeholder="Please select a round" v-on:change="onRoundChange($event)" :disabled="!isRoundSelectable">
          <el-option v-for="item in rounds" :label="item.round_name" :value="item.id" />
        </el-select>
      </el-form-item>
      <el-form-item label="Total Fixture" prop="fixture_number" >
        <el-col :span="12"> 
          <el-input v-model="postForm.fixture_number" name="fixture_number" type="number" required @keyup.native="fixtureNumber" />
        </el-col> 
      </el-form-item>       
      <div v-for="(i, index) in fixtures"> 
        <h4> Fixture {{i}}</h4>
        <el-form-item label="Fixture Name" prop="fixture_name" >
          <el-col :span="12">
            <el-input v-model="postForm.fixture_name[i]" name="fixture_name[]" type="text" required :key="i" />
          </el-col>
        </el-form-item>
        <!-- <el-form-item label="Select Home Team" prop="home_team_id">
          <el-select v-model="postForm.home_team_id[i]" name="home_team_id[]" placeholder="Please select a home team" :id="'hometeam'+i" v-on:change="onTeamChange($event)" required :key="i">
            <el-option v-for="(item) in teams" :label="item.team_name" :value="item.id"  v-if="selected != item.id" :class="item.team_name" />
          </el-select>
        </el-form-item> -->

        <el-form-item label="Select Home Team" prop="home_team_id">
          <div class="slct custom_arrw">
          <select class="form-control positionTypes" v-model="postForm.home_team_id[i]" name="home_team_id[]" :id="'awayteam'+i" v-on:change="onTeamChange($event)" required :key="i">
            <option value="" disabled selected>Please select home team</option>
            <option v-for="(item, index) in teams" :label="item.team_name" :value="item.id" v-if="selected != item.id" :class="item.team_name"/>
          </select>
          </div>
        </el-form-item>

        <el-form-item label="Select Away Team" prop="away_team_id">
          <div class="slct custom_arrw">
          <select class="form-control positionTypes" v-model="postForm.away_team_id[i]" name="away_team_id[]" :id="'awayteam'+i" placeholder="Please select a away team" v-on:change="onTeamChange($event)" required :key="i" >
            <option value="" disabled selected>Please select away team</option>
            <option v-for="(item, index) in teams" :label="item.team_name" :value="item.id" v-if="selected != item.id" :class="item.team_name"/>
          </select>
          </div>
        </el-form-item>

        <!-- <el-form-item label="Select Away Team" prop="away_team_id">
          <el-select v-model="postForm.away_team_id[i]" name="away_team_id[]" :id="'awayteam'+i" placeholder="Please select a away team" v-on:change="onTeamChange($event)" required :key="i">
            <el-option v-for="(item, index) in teams" :label="item.team_name" :value="item.id" v-if="selected != item.id" :class="item.team_name"/>
          </el-select>
        </el-form-item> -->

        <el-form-item label="Match Day">
          <el-col :span="12">
            <!-- <el-date-picker v-model="postForm.match_datetime[i]" name="match_datetime[]" type="datetime" placeholder="Pick a date" :id="'currentdate'+i" style="width: 100%;" v-on:change="onDateChange(i,$event)" required :key="i"/> -->
            <datetime format="YYYY-MM-DD H:i:s" width="300px" v-model="postForm.match_datetime[i]" name="match_datetime[]" placeholder="Pick a date" style="width: 100%;" required :key="i"></datetime>
          </el-col>
        </el-form-item>        <br />

      </div>     
      <el-form-item>
        <el-button v-loading="loading" type="primary" @click.native.prevent="onSubmit">
          Submit
        </el-button>
        <el-button @click="onCancel">
          Cancel
        </el-button>
      </el-form-item>
    </el-form>
  </div>
</template> 
<script>
import datetime from 'vuejs-datetimepicker';
import { fetchSportList } from '@/api/fixture';
import { fetchTeamList } from '@/api/fixture';
import { fetchroundsbysport } from '@/api/round';
import { fetchRound } from '@/api/round';
import Resource from '@/api/resource';
const userResource = new Resource('fixture');
var $ = require( "jquery" );

export default {
  components: { datetime },
  mounted () {
    //alert();
 
$(document).on("change","select.positionTypes",function () {
  // alert();
    $("select.positionTypes option[value='" + $(this).data('index') + "']").prop('disabled', false);
    $(this).data('index', this.value);
    $("select.positionTypes option[value='" + this.value + "']:not([value=''])").prop('disabled', true);
    $(this).find("option[value='" + this.value + "']:not([value=''])").prop('disabled', false);
    });

},
  data() {    
    const validateName = (rule, value, callback) => {      if (value.length < 1) {
        callback(new Error('This Field is required'));
      }
      else {
        callback();
      }
    };
    const validateText = (rule, value, callback) => {
      var name_error = 0;
      for (var i = 1; i <= this.fixtures; i++) {
        if (typeof value[i] === 'undefined') {
          name_error = 1;
          // callback(new Error('All '+rule.field[0].toUpperCase() + rule.field.slice(1) +' field is required'));
          callback(new Error('This field is required'));
        } 
      }

      if(name_error === 0) {
          callback();
      }
    };
    return {
      selected:[2,3],
      team_icon:'',
      list:[],
      rounds:[],
      teams:[],
      fixtures:0,
      selected:'',
      curruent_date:'',
      date_status:'',
      round_starttime:'',
      round_endtime:'',
      hometeam:'hometeam',
      awayteam:'awayteam',
      postForm: {
        sport_id:'',
        round_id:'',
        fixture_name:[],
        match_datetime:[],
        home_team_id:[],
        away_team_id:[],
        added_by:'',
      },
      sportRules: {
        fixture_name: [{ required: true, validator: validateName }],
        sport_id: [{ required: true, validator: validateName }],
        round_id: [{ required: true, validator: validateName }],
        home_team_id: [{ required: true, validator: validateName }],
        away_team_id: [{ required: true, validator: validateName }],
      },
      loading: false,
      tempRoute: {},
      fixtures:0,
      isSportSelectable: true,
      isRoundSelectable: true

    };
  },
  created() {
    this.tempRoute = Object.assign({}, this.$route);
    this.getList();
    
   

  },
  methods: {
    async getList() {
      this.listLoading = true;
      const { data } = await fetchSportList(this.listQuery);
      this.list = data.items;
      const sid = this.$route.params && this.$route.params.sid;
      if(typeof sid!=='undefined') {
           this.postForm.sport_id = parseInt(sid)
           this.onSportChange(sid)
           this.isSportSelectable= false  
        }
    },
    async getteams() {
      this.listLoading = true;
       const { data } = await fetchTeamList(this.postForm.sport_id);
      this.teams = data.items;


       
    },
    async onSportChange(event){
      // console.log(this.postForm.sport_id);
      this.listLoading = true;
      const { data } = await fetchroundsbysport(this.postForm.sport_id);
      this.rounds = data;    
      this.getteams();
      const self = this;
       let rids = this.$route.params && this.$route.params.rid
     if(typeof rids!=='undefined') {
         self.postForm.round_id = parseInt(rids);
         self.onRoundChange(rids);
         this.isRoundSelectable= false;
         const { data } = await fetchRound(this.postForm.round_id);

      }

    },
    myFunction(event,item, index) {

      if(event == item.id){
       return index;

      }
 
    },
    async onTeamChange(event){
      
     var dataid = this.teams.forEach(this.myFunction.bind(null,event));

    },
    async onRoundChange(event){
      this.listLoading = true;
      const { data } = await fetchRound(this.postForm.round_id);
      this.round_starttime = data.start_datetime;
      this.round_endtime = data.end_datetime;
    },
    // async convert(str) {
    //   var date = new Date(str),
    //     mnth = ("0" + (date.getMonth() + 1)).slice(-2),
    //     day = ("0" + date.getDate()).slice(-2),
    //     time = ("0" + date.getTime()).slice(-2);
    //     this.curruent_date = date.getFullYear()+'-'+mnth+'-'+day+' '+time;
    // }, 
    async onDateChange(i,event){
      let s = event;
let d = new Date(Date.parse(s));
      // console.log(d.toUTCString());
    },
    fixtureNumber() {
      this.listLoading = true;
      this.fixtures = Number(this.postForm.fixture_number);
    },
    gfg_Run(a1,a2,a3) { 
            var D1 = new Date(a1), 
            D2 = new Date(a2), 
            D3 = new Date(a3); 
              
            if (D3.getTime() <= D2.getTime() 
                && D3.getTime() >= D1.getTime()) { 
               return true;
            } else { 
                return ;
            } 
        } ,
    onSubmit() {

      //console.log();
      // if(this.fixtures >= 1){
      //     for (var i = 1; i <= this.fixtures; i++) {

      //       var currentdate = document.getElementById("currentdate"+i).value;
      //       var checkate = this.gfg_Run(this.round_starttime,this.round_endtime,currentdate);
      //       if(checkate != true){
      //           this.$message({
      //                   message: 'Match Date Should be in range of round',
      //                   type: 'error',
      //                   duration: 5 * 1000,
      //                 });
      //           return false; 
      //       }

      //     }
      //   }

      // for (var i = 1; i <= this.fixtures; i++) {

      //   var hometeam = document.getElementById("hometeam"+i).value;
      //   var awayteam = document.getElementById("awayteam"+i).value;
      //   // alert(awayteam);
      //   if (hometeam == awayteam) {

      //     this.$message({
      //         message: 'Team should not be same!',
      //         type: 'fail',
      //         duration: 5 * 1000,
      //       });

      //    document.getElementById("awayteam"+i).focus();
      //     return false;
      //   } 

      // }


      this.postForm.team_icon = this.team_icon;
      this.$refs.postForm.validate(valid => {
        if (valid) {
          this.loading = true;
          userResource
            .store(this.postForm)
              .then(response => {
                this.$message({
                  message: 'New Fixture added successfully',
                  type: 'success',
                  duration: 5 * 1000,
                });
          });
          this.postForm.status = 'published';
          this.loading = false;
          this.$router.push('/fixtures');
        } else {
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
<style type="text/css">
  .calender-div {
    top: -210px !important;
}
.month-setter {
    background: #64a9ea;
}
span.port.activePort, span.port.activePort:hover, button.nav-l, button.nav-r, .year-month-wrapper {
    background-color: #64a9ea !important;
}
</style>
<style rel="stylesheet/scss" lang="scss" scoped>

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
.custom_arrw select{
    -webkit-appearance: none;
    -moz-appearance: none;
    text-indent: 1px;
    text-overflow: '';
    position: relative;
}
.slct.custom_arrw {
    position: relative;
    transition: 0.8s;
    width: 50%;
}
.custom_arrw::after{
    content: '';
    background-image: url(/images/arrow.png);
    background-repeat: no-repeat;
    position: absolute;
    right: 7px;
    height: 10px;
    width: 20px;
    top: 14px;
    transform: rotateZ(180deg);
    transition: 0.8s;
    background-size: 18px;
}
.arrw_up::after{
   transform: rotateZ(0deg);
    transition: 0.8s;
}
</style>
