<template>
  <div class="app-container">
    <el-form ref="postForm" :model="postForm" :rules="sportRules" label-width="120px">
      <el-form-item label="Sport Name" prop="sport_name" >
        <el-col :span="12">
          <el-input v-model="postForm.sport_name" name="sport_name" type="text" required />
        </el-col> 
      </el-form-item>
      <el-form-item label="Sport Icon" prop="sport_icon">
        <div v-if="!sport_icon">
          <input type="file" @change="onFileChange" required />
        </div>
        <div v-else>
          <img :src="sport_icon" />
          <el-button v-loading="loading" type="primary" @click="removeImage">
          Remove Sport Icon
          </el-button>
        </div>
      </el-form-item>
      <el-form-item label="Total Team" prop="team_number" class="mb-0">
        <el-col :span="12">
          <el-input v-model="postForm.team_number" name="team_number" type="number" @keyup.native="teamNumberHandler" :disabled="!sport_icon" />
        </el-col>
      </el-form-item>
      <div class="row">
      <div v-for="(i, index) in teamNumber" :class="activeKey== i ? 'col-sm-6 ': 'col-sm-6  box-disable'">
        <div class="shadow-bg">
          <h4> Team {{i}}</h4> 
          <el-form-item label="Team Name" prop="round_name" class="frmlabel">
            <el-col>
              <el-input v-model="postForm.team_name[i]" name="team_name[]" type="text" :key="i"/>
            </el-col>  
          </el-form-item>
          <el-form-item label="Team Icon" prop="team_icon">
            <div v-if="!postForm.team_icon[i]">
              <input type="file" @change="onFileChange" :attr="i"  />
            </div>
            <div v-else>
              <img :src="iconPath+'/'+postForm.team_icon[i]" />
            </div>
          </el-form-item>
          <br />
        </div>
        </div>
      </div>
      <el-form-item class="mt-4">
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
import Resource from '@/api/resource';
import { saveTeamIcon } from '@/api/sport';
const userResource = new Resource('sport');

export default {
  data() {
    const validateName = (rule, value, callback) => {
      if (value.length < 1) {
        callback(new Error('Sport Name Field is required'));
      } else {
        callback();
      }
    };
    const validateIcon = (rule, value, callback) => {
      if (value.length < 1) {
        callback(new Error('Sport Icon Field is required'));
      } else {
        callback();
      }
    };
    return {
      sport_icon: '',
      iconPath: '/uploads/team',
      postForm: {
        sport_name: '',
        sport_icon:'',
        team_number: '',
        team_name:[],
        team_icon:[] 
      },
      uploadAsset: {
        key: '',
        image: ''
      },
      sportRules: {
        sport_name: [{ required: true, validator: validateName }],
        sport_icon: [{ required: true, validator: validateIcon }],
      },
      loading: false,
      tempRoute: {},
      teamNumber: 0,
      activeKey: 1,
    };
  },
  created() {
    this.tempRoute = Object.assign({}, this.$route);
  },
  methods: {
    onSubmit() {
      this.postForm.sport_icon = this.sport_icon;
      this.$refs.postForm.validate(valid => {
        if (valid) {
          this.loading = true;
          userResource
            .store(this.postForm)
              .then(response => {
                if(response.errors){
                  this.loading = false;
                  this.sport_icon = '';
                  var error = '';
                  for (var i = 0; i <= response.errors.length; i++) {
                    if (typeof response.errors[i] !== 'undefined') {
                      error+= response.errors[i]+' ';
                    }
                  }
                  this.$message({
                        message: error,
                        type: 'error',
                        duration: 5 * 1000,
                  });
                } else {
                  this.$message({
                    message: 'New sport added successfully',
                    type: 'success',
                    duration: 5 * 1000,
                  });
                  this.loading = false;
                  this.$router.push('/sports');
                  
                }
          });
          
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
    onFileChange(e) {
      this.loading=  true;
 
      let key = null;
      let temp = e.target.getAttribute('attr');
      if(temp!=='') {
        key = temp;
      } 



      var files = e.target.files || e.dataTransfer.files;
      console.log(files)
      if (!files.length) {
        return;
      }
      if(key==null) {
       this.createImage(files[0], key);
       this.loading=  false;
       } else {
           this.createTeamImage(files[0], key);
           this.loading=  false;
       }
    },
    createTeamImage(file, index) {
      var sport_icon = new Image();
      var reader = new FileReader();
      var vm = this;
      reader.onload = (e) => {
        vm.postForm.team_icon[index] = e.target.result;
        this.uploadTeamIcon(e.target.result, index);
      };
      reader.readAsDataURL(file);
        
      
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
    removeImage(key=0) {
      this.sport_icon = '';
      this.postForm.team_icon[key] = '';
    },
    teamNumberHandler() {
     this.teamNumber = Number(this.postForm.team_number);
    },
    uploadTeamIcon(str, key) {
      this.uploadAsset.key = key;
      this.uploadAsset.image = str;
      this.uploadAsset.type = 'single';
       this.loading = true;
       userResource
            .store(this.uploadAsset)
              .then(response => {
                this.postForm.team_icon[response.data.data['key']]= response.data.data.image;
                this.loading = false;
                let k = parseInt(this.activeKey);
                this.activeKey = k + 1;
                //console.log(key)
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
.shadow-bg .frmlabel label {
    text-align: left!important;
}
.shadow-bg {
    background-color: rgba(0,0,0,0.1);
    padding: 20px;
    border-radius: 4px;
    margin-top: 30px;
    position: relative;
}
label{
  text-align: left!important
}
.box-disable .shadow-bg::before{
  content: '';
  background-color: rgba(48, 65, 86, 0.6);
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  z-index: 9;
}
</style>

