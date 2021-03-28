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
          <img v-if="is_sport_icon" :src="iconPath+sport_icon" />
          <img v-else :src="sport_icon" />
          <el-button v-loading="loading" type="primary" @click="removeImage">
          Remove Sport Icon
          </el-button>
        </div>
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
import Resource from '@/api/resource';
const userResource = new Resource('sport');
import { fetchSport } from '@/api/sport';

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
      iconPath: '/uploads/sport/',
      sport_icon: '',
      is_sport_icon: false,
      postForm: {
        sport_name: '',
        sport_icon:'',
      },
      sportRules: {
        sport_name: [{ required: true, validator: validateName }],
        sport_icon: [{ required: true, validator: validateIcon }],
      },
      loading: false,
      tempRoute: {},
    };
  },
  created() {
      const id = this.$route.params && this.$route.params.id;
      this.fetchData(id);
  },
  methods: {
    fetchData(id) {
      fetchSport(id)
        .then(response => {
          this.postForm = response.data;
          this.sport_icon = response.data.sport_icon;
          if(response.data.sport_icon) {
            this.is_sport_icon = true;
          }
          // Set tagsview title
          
          this.setTagsViewTitle();
        })
        .catch(err => {
          console.log(err);
        });
    },
    onSubmit() {
      this.postForm.sport_icon = this.sport_icon;
      this.$refs.postForm.validate(valid => {
        if (valid) {
          this.loading = true;
          userResource
            .update(this.postForm.id,this.postForm)
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
                    message: 'Sport update successfully',
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
      this.$router.push('/sports');
    },
    onFileChange(e) {
      this.is_sport_icon = false;
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) {
        return;
      }
      this.createImage(files[0]);
    },
    createImage(file) {
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
</style>

