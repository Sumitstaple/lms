<template>
  <div class="app-container">
    <el-form ref="postForm" :model="postForm" :rules="sportRules" label-width="120px">
      <el-form-item label="Select Sport" prop="sport_id">
        <el-select v-model="postForm.sport_id" placeholder="Please select your sport" v-on:change="onSportChange($event)" disabled>
          <el-option v-for="item in list" :label="item.sport_name" :value="item.id" />
        </el-select>  
      </el-form-item> 
      <el-form-item label="Select Round" prop="round_id">
        <el-select v-model="postForm.round_id" placeholder="Please select a round" v-on:change="onRoundChange($event)" disabled>
          <el-option v-for="item in rounds" :label="item.round_name" :value="item.id" />
        </el-select>  
      </el-form-item> 
      <el-form-item label="Total Fixture" prop="fixture_number" >
        <el-col :span="12">
          <el-input v-model="postForm.fixture_number" name="fixture_number" type="number" required disabled/>
        </el-col>
      </el-form-item>
        <el-form-item label="Fixture Name" prop="fixture_name" >
          <el-col :span="12">
            <el-input v-model="postForm.fixture_name" name="fixture_name" type="text" required />
          </el-col>   
        </el-form-item>
        <el-form-item label="Select Home Team" prop="home_team_id">
          <el-select v-model="postForm.home_team_id" name="home_team_id" id="hometeam" placeholder="Please select a home team">
            <el-option v-for="item in teams" :label="item.team_name" :value="item.id" />
          </el-select>  
        </el-form-item>
        <el-form-item label="Select Away Team" prop="away_team_id">
          <el-select v-model="postForm.away_team_id" name="away_team_id" id="awayteam" placeholder="Please select a away team">
            <el-option v-for="item in teams" :label="item.team_name" :value="item.id" />
          </el-select>  
        </el-form-item>
        <el-form-item label="Match Day">
          <el-col :span="12">
            <!-- <el-date-picker v-model="postForm.match_datetime" name="match_datetime" id="matchdate" type="datetime" placeholder="Pick a date" style="width: 100%;" v-on:change="onDateChange($event)" /> -->
            <datetime format="YYYY-MM-DD H:i:s" width="300px" v-model="postForm.match_datetime" name="match_datetime" id="matchdate" placeholder="Pick a date" style="width: 100%;" required ></datetime>
          </el-col>
        </el-form-item>

        <br />
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
import { fetchFixture } from '@/api/fixture';
import { fetchSportList } from '@/api/fixture';  
import { fetchTeamList } from '@/api/fixture'; 
import { fetchRoundList } from '@/api/fixture'; 
import { fetchRound } from '@/api/round';  
import Resource from '@/api/resource';
const userResource = new Resource('fixture');


export default {
  components: { datetime },
  data() {
    const validateName = (rule, value, callback) => {
      if (value.length < 1) {
        callback(new Error('This Field is required'));
      } else {
        callback();
      }
    };
    const validateIcon = (rule, value, callback) => {
      if (value.length < 1) {
        callback(new Error('Team Icon Field is required'));
      } else {
        callback();
      }
    };
    return {
      team_icon:'',
      list:[],
      rounds:[],
      teams:[],
      fixtures:0,
      curruent_date:'',
      date_status:'',
      round_starttime:'',
      round_endtime:'',
      postForm: {
        sport_id:'',
        round_id:'',
        fixture_name:'',
        match_datetime:'',
        home_team_id:'',
        away_team_id:'',
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
    };
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.getList(id);

    this.getSport();
    
  },
  methods: {
    async getList(id) {
      this.listLoading = true;
      fetchFixture(id)
        .then(response => {
          this.postForm = response.data;
          // console.log(this.postForm);
          this.getTeams(this.postForm.sport_id);
          this.getRounds(this.postForm.sport_id);
          this.onRoundChange(this.postForm.round_id);
        })
        .catch(err => {
          console.log(err);
        });
    },

    async getSport() {
      this.listLoading = true;
      const { data } = await fetchSportList();
      this.list = data.items;
    },
    async getTeams(id) {
      this.listLoading = true;
       const { data } = await fetchTeamList(id);
       this.teams = data.items;
    },
    async getRounds(id){
      this.listLoading = true;
      const { data } = await fetchRoundList(id);
      this.rounds = data.items;

      // console.log(this.rounds);

      // this.round_starttime = data.start_datetime;
      // this.round_endtime = data.end_datetime;
      
    },
    async onRoundChange(id){

      this.listLoading = true;
      const { data } = await fetchRound(id);
      this.round_starttime = data.start_datetime;
      this.round_endtime = data.end_datetime;
      this.curruent_date = document.getElementById("matchdate").value;
      console.log(this.curruent_date,this.round_starttime,this.round_endtime);
    },
    async convert(str) {
      var date = new Date(str),
        mnth = ("0" + (date.getMonth() + 1)).slice(-2),
        day = ("0" + date.getDate()).slice(-2);
        this.curruent_date = date.getFullYear()+'-'+mnth+'-'+day+' 00:00:00';
    },
    async onDateChange(event){
      this.convert(event);
      // console.log(this.curruent_date,this.round_starttime,this.round_endtime);
     
    },
    onSubmit() { 
     //this.curruent_date = document.getElementById("matchdate").value;
      // console.log(this.curruent_date,this.round_starttime,this.round_endtime);
      //  if(this.curruent_date >= this.round_starttime && this.curruent_date <= this.round_endtime ){
      //      this.date_status = true;

      //   }
      //   else{
      //     this.date_status = false;
      //   }
 
      // console.log(this.date_status);
      // if(this.date_status == false){
      //     this.$message({
      //             message: 'Date Should be in range of round',
      //             type: 'error',
      //             duration: 5 * 1000,
      //           });
      //     return false;
      // }

      var hometeam = document.getElementById("hometeam").value;
        var awayteam = document.getElementById("awayteam").value;
        // alert(awayteam);
        if (hometeam == awayteam) {

          this.$message({
              message: 'Team should not be same!',
              type: 'fail',
              duration: 5 * 1000,
            });

         document.getElementById("awayteam").focus();
          return false;
        } 

      this.postForm.team_icon = this.team_icon;
      console.log(this.postForm);
      this.$refs.postForm.validate(valid => {
        if (valid) {
          this.loading = true;
          userResource
            .update(this.postForm.id,this.postForm)
              .then(response => {
                this.$message({
                  message: 'Fixture updated successfully',
                  type: 'success',
                  duration: 5 * 1000,
                });
          });

          this.loading = false;
          this.$router.push('/fixtures');
        } else {
          return false;
        }
      });
    },
    onCancel() {
      // this.$message({
      //   message: 'cancel!',
      //   type: 'warning',
      // });
      this.$router.push('/fixtures');
    },
  },
};
</script>

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
</style>