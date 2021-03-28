<template>
  <div class="app-container">
    <el-form ref="postForm" :model="postForm" :rules="sportRules" label-width="120px">

	  <el-form-item label="League Name" prop="league_name" >
	    <el-col :span="12">
	      <el-input v-model="postForm.league_name" name="leage_name" type="text" required />
	    </el-col>  
	  </el-form-item>
    <el-form-item label="League Town" prop="town" >
      <el-col :span="12">
        <el-input v-model="postForm.town" name="town" type="text" required />
      </el-col>  
    </el-form-item>
    <el-form-item label="League City" prop="city" >
      <el-col :span="12">
        <el-input v-model="postForm.city" name="city" type="text" required />
      </el-col>  
    </el-form-item>
    <el-form-item label="League State" prop="state" >
      <el-col :span="12">
        <el-input v-model="postForm.state" name="state" type="text" required />
      </el-col>  
    </el-form-item>
    <el-form-item label="League Country" prop="country" >
      <el-col :span="12">
        <el-input v-model="postForm.country" name="country" type="text" required />
      </el-col>  
    </el-form-item>
	  <el-form-item label="League Type" prop="type">
        <el-select v-model="postForm.type" placeholder="Please select a round">
          <el-option v-for="item in leaguetype" :label="item.name" :value="item.id" />
        </el-select>  
      </el-form-item> 

      <el-form-item label="Select Sport" prop="sport_id">
        <el-select v-model="postForm.sport_id" placeholder="Please select your sport" v-on:change="onSportChange($event)">
          <el-option v-for="item in list" :label="item.sport_name" :value="item.id" />
        </el-select>  
      </el-form-item>
      <el-form-item label="Round To Start" prop="round_id">
        <el-select v-model="postForm.round_id" placeholder="Please select a round">
          <el-option v-for="item in rounds" :label="item.round_name" :value="item.id" />
        </el-select>
      </el-form-item>  
	    <el-form-item label="Start Datetime">
	      <el-col :span="12">
	        <el-date-picker v-model="postForm.start_datetime" name="start_datetime" type="datetime" placeholder="Pick a date" style="width: 100%;" required/>
	      </el-col>
	    </el-form-item>
	    <el-form-item label="End Datetime">
	      <el-col :span="12">
	        <el-date-picker v-model="postForm.end_datetime" name="end_datetime" type="datetime" placeholder="Pick a date" style="width: 100%;" required/>
	      </el-col>
	    </el-form-item> 
	    <el-form-item label="Is Private" prop="is_private">
	        <el-select v-model="postForm.is_private" placeholder="Please select a round">
	          <el-option v-for="item in isprivate" :label="item.is_private" :value="item.id" />
	        </el-select>  
	      	</el-form-item>
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
import { fetchSportList } from '@/api/fixture';   
import { fetchroundsbysport } from '@/api/round';  
import { fetchLeague } from '@/api/league';  
import Resource from '@/api/resource';
const leagueResource = new Resource('league');

export default {
  data() {
    const validateName = (rule, value, callback) => {

      if (value.length < 1 ) {
        callback(new Error('This Field is required'));
      } else {
        callback();
      }
    };
    return {
      list:[],
      rounds:[],
      leaguetype:[
      {id:1,name:'LML'},
      {id:2,name:'LMS'}
      ],
      isprivate:[
      {id:'yes',name:'yes'},
      {id:'no',name:'no'}
      ],
      postForm: {
        sport_id:'',
        round_to_start:'',
        round_id:'',
        league_name:'',
        town:'',
        city:'',
        state:'',
        country:'',
        league_type:'',
        start_datetime:'',
        end_datetime:'',
        is_private:'',
      },
      sportRules: {
        league_name: [{ required: true, validator: validateName }],
        league_type: [{ required: true, validator: validateName }],
        sport_id: [{ required: true, validator: validateName }],
        round_id: [{ required: true, validator: validateName }],
        start_datetime: [{ required: true, validator: validateName }],
        end_datetime: [{ required: true, validator: validateName }],
        is_private: [{ required: true, validator: validateName }],
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
    async getList(id) {
      this.listLoading = true;
      fetchLeague(id)
        .then(response => {
          this.postForm = response.data[0];
          this.getRounds(this.postForm.sport_id);
          this.postForm.round_id = response.data[0].round.id
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
      const { data } = await fetchroundsbysport(id);
      this.rounds = data;

    },
    async onSportChange(event){
      // console.log(this.postForm.sport_id);
      this.listLoading = true;
      const { data } = await fetchroundsbysport(this.postForm.sport_id);
      this.rounds = data;
      console.log();
    },

    onSubmit() {  
      this.$refs.postForm.validate(valid => {
      	console.log(this.postForm);
        if (valid) {
          this.loading = true;
          leagueResource
            .update(this.postForm.id,this.postForm)
              .then(response => {
                this.$message({
                  message: 'League updated successfully',
                  type: 'success',
                  duration: 5 * 1000,
                });
          });
          this.loading = false;
          this.$router.push('/leagues');
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