<template>
  <div class="app-container">
    <el-form ref="postForm" :model="postForm" :rules="roundRules" label-width="150px">
      <el-form-item label="Select Sport" prop="sport_id" label-width="150px">
        <el-select v-model="postForm.sport_id" placeholder="Please select your sport" required v-bind:style="{ 'width': '50%' }" :disabled="!isSelectable">
          <el-option v-for="item in list" :label="item.sport_name" :value="item.id" />
        </el-select>
      </el-form-item>
      <el-form-item label="Total Round" prop="round_number" >
        <el-col :span="12">
          <el-input v-model="postForm.round_number" name="round_number" type="number" required @keyup.native="roundNumber" />
        </el-col>
      </el-form-item>
      <div v-for="(i, index) in rounds">
        <h4 :key="i"> Round {{i}}</h4> 
        <el-form-item label="Round Name" prop="round_name" >
          <el-col :span="12">
            <el-input v-model="postForm.round_name[i]" name="round_name[]" type="text" required :key="i"/>
          </el-col>  
        </el-form-item>
        <!--el-form-item label="Round Description" prop="round_description" >
          <el-col :span="12">
            <el-input v-model="postForm.round_description[i]" name="round_description[]" type="textarea" required />
          </el-col>  
        </el-form-item-->
        <el-form-item label="Start Datetime" prop="start_datetime">
          <el-col :span="12">
            <!-- <el-date-picker v-model="postForm.start_datetime[i]" name="start_datetime[]" type="datetime" placeholder="Pick a date" style="width: 100%;" required/> -->
             <datetime format="YYYY-MM-DD H:i:s" width="300px" v-model="postForm.start_datetime[i]" name="start_datetime[]" placeholder="Pick a date" style="width: 100%;" required ></datetime>
          </el-col>
        </el-form-item>
        <el-form-item label="End Datetime" prop="end_datetime">
          <el-col :span="12">
            <!-- <el-date-picker v-model="postForm.end_datetime[i]" name="end_datetime[]" type="datetime" placeholder="Pick a date" style="width: 100%;" required/> -->

            <datetime format="YYYY-MM-DD H:i:s" width="300px" v-model="postForm.end_datetime[i]" name="end_datetime[]" placeholder="Pick a date" style="width: 100%;" required ></datetime>
          </el-col>
        </el-form-item>
        <br />
      </div>
      <el-form-item>
        <el-button v-loading="loading" type="primary" @click.native.prevent="onSubmit">
          Submit
        </el-button>
        <el-button @c="onCancel">
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
const defaultForm = {
  sport_name: '',
};

export default {
  components: { datetime },
  data() {
    const validateText = (rule, value, callback) => {
      var name_error = 0;
      for (var i = 1; i <= this.rounds; i++) {
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
    const validateField = (rule, value, callback) => {
      if (!value) {
        callback(new Error('This Field is required'));
      } else {
        callback();
      }
    };
    return {
      postForm: {
        round_name: [],
        round_number:'',
        //round_description:[],
        start_datetime:[],
        end_datetime:[],
        sport_id:''
      },
      roundRules: {
        round_name: [{ required: true, validator: validateText }],
        //round_description: [{ required: true, validator: validateText }],
        start_datetime: [{ required: true, validator: validateText }],
        end_datetime: [{ required: true, validator: validateText }],
        round_number: [{ required: true, validator: validateField }],
        sport_id: [{ required: true, validator: validateField }],
      },
      loading: false,
      tempRoute: {},
      list: [],
      rounds: 0,
      isSelectable: true
    };
  },
  created() {
    this.tempRoute = Object.assign({}, this.$route);
    this.getSportList();
    const id = this.$route.params && this.$route.params.id;
     if(typeof id!=='undefined') {
           this.postForm.sport_id = parseInt(id)
           this.isSelectable= false  
        }
  },
  methods: {
    async getSportList() {
      this.listLoading = true;
      const { data } = await fetchSportList(this.listQuery);
      this.list = data.items;
    },
    onSubmit() {
      this.postForm.sport_icon = this.sport_icon;
      this.$refs.postForm.validate(valid => {
        if (valid) {
          this.loading = true;
          userResource
          .store(this.postForm)
          .then(response => {
              this.$message({
                message: 'New round added successfully',
                type: 'success',
                duration: 5 * 1000,
              });
          });
          this.postForm.status = 'published';
          this.loading = false;
          this.$router.push('/rounds');
        } else {
          console.log('error submit!!');
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
    onFileChange(e) {
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) {
        return;
      }
      this.createImage(files[0]);
    },
    createImage(file) {
      var sport_icon = new Image();
      var reader = new FileReader();
      var vm = this;
      reader.onload = (e) => {
        vm.sport_icon = e.target.result;
      };
      reader.readAsDataURL(file);
    },
    removeImage() {
      this.sport_icon = '';
    },
    roundNumber() {
      this.rounds = Number(this.postForm.round_number);
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
.el-select > .el-input {
    display: block;
    width: 600px;
}
h4{
  color: #606266;
}
</style>

