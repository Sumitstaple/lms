<template>
  <div class="app-container">
    <el-form ref="postForm" :model="postForm" :rules="roundRules" label-width="150px">
      <el-form-item label="Round Name" prop="round_name" >
        <el-col :span="12">
          <el-input v-model="postForm.round_name" name="round_name" type="text" required />
        </el-col> 
      </el-form-item>
      <el-form-item label="Select Sport" prop="sport_id" label-width="150px">
        <el-select v-model="postForm.sport_id" placeholder="Please select your sport" required :disabled="true" v-bind:style="{ 'width': '50%' }">
          <el-option v-for="item in sportList" :label="item.sport_name" :value="item.id" />
        </el-select>
      </el-form-item>
      <el-form-item label="Round Number" prop="round_number" >
        <el-col :span="12">
          <el-input v-model="postForm.round_number" name="round_number" type="text" required :disabled="true"/>
        </el-col> 
      </el-form-item>
      <!--el-form-item label="Round Description" prop="round_description" >
        <el-col :span="12">
          <el-input v-model="postForm.round_description" name="round_description" type="textarea" required />
        </el-col> 
      </el-form-item-->
      <el-form-item label="Start Datetime">
          <el-col :span="12">
          <!--   <el-date-picker v-model="postForm.start_datetime" name="start_datetime" type="datetime" placeholder="Pick a date" style="width: 100%;" /> -->
            <datetime format="YYYY-MM-DD H:i:s" width="300px" v-model="postForm.start_datetime" name="start_datetime" placeholder="Pick a date" style="width: 100%;" required ></datetime>
          </el-col>
        </el-form-item>
        <el-form-item label="End Datetime">
          <el-col :span="12">
            <!-- <el-date-picker v-model="postForm.end_datetime" name="end_datetime" type="datetime" placeholder="Pick a date" style="width: 100%;" /> -->

            <datetime format="YYYY-MM-DD H:i:s" width="300px" v-model="postForm.end_datetime" name="end_datetime" placeholder="Pick a date" style="width: 100%;" required ></datetime>
          </el-col>
        </el-form-item>
      <el-form-item>
        <el-button v-loading="loading" type="primary" @click.native.prevent="onSubmit">
          Update
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
import { fetchSportList } from '@/api/sport';
import Resource from '@/api/resource';
const userResource = new Resource('round');
import { fetchRound } from '@/api/round';

export default {
  components: { datetime },
  data() {
    const validateText = (rule, value, callback) => {
      if (value.length < 1) {
        callback(new Error('This ield is required'));
      } else {
        callback();
      }
    };
    return {
      postForm: {
        round_name: '',
        round_number:'',
        round_description:'',
        start_datetime:'',
        end_datetime:'',
        sport_id:''
      },
      roundRules: {
        round_name: [{ required: true, validator: validateText }],
        round_number: [{ required: true, validator: validateText }],
        //round_description: [{ required: true, validator: validateText }],
        start_datetime: [{ required: true, validator: validateText }],
        end_datetime: [{ required: true, validator: validateText }],
        sport_id: [{ required: true, validator: validateText }],
      },
      loading: false,
      tempRoute: {},
      sportList: [],
    };
  },
  created() {
      const id = this.$route.params && this.$route.params.id;
      this.fetchData(id);
      this.getSportList();
  },
  methods: {
    async getSportList() {
      this.listLoading = true;
      const { data } = await fetchSportList(this.listQuery);
      this.sportList = data.items;
    },
    fetchData(id) {
      fetchRound(id)
        .then(response => {
          this.postForm = response.data;
        })
        .catch(err => {
          console.log(err);
        });
    },
    onSubmit() {
      this.$refs.postForm.validate(valid => {
        if (valid) {
          this.loading = true;
          userResource
            .update(this.postForm.id,this.postForm)
              .then(response => {
                this.$message({
                  message: 'Round updated successfully',
                  type: 'success',
                  duration: 5 * 1000,
                });
          });
          this.postForm.status = 'published';
          this.loading = false;
          this.$router.push('/rounds');
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
      this.$router.push('/rounds');
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
</style>

