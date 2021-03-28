<template>
  <div class="app-container">
    <el-form ref="postForm" :model="postForm" :rules="sportRules" label-width="120px">
      <el-form-item label="Team Name" prop="team_name">
        <el-col :span="12">
          <el-input v-model="postForm.team_name" name="team_name" type="text" required />
        </el-col>
      </el-form-item>
      <el-form-item label="Select Sport" prop="sport_id">
        <el-select v-model="postForm.sport_id" placeholder="Please select your sport" v-bind:style="{ 'width': '50%' }" :disabled="!isSelectable">
          <el-option v-for="item in list" :label="item.sport_name" :value="item.id" />
        </el-select>
      </el-form-item>
      <el-form-item label="Team Icon" prop="team_icon">
        <div v-if="!team_icon">
          <input type="file" required @change="onFileChange">
        </div>
        <div v-else>
          <img :src="team_icon">
          <el-button v-loading="loading" type="primary" @click="removeImage">
            Remove Team Icon
          </el-button>
        </div>
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

import { fetchSportList } from '@/api/sport';
import Resource from '@/api/resource';
const userResource = new Resource('team');

export default {
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
      team_icon: '',
      list: [],
      postForm: {
        team_icon: '',
        team_name: '',
        sport_id: '',
      },
      sportRules: {
        team_name: [{ required: true, validator: validateName }],
        sport_id: [{ required: true, validator: validateName }],
        team_icon: [{ required: true, validator: validateIcon }],
      },
      loading: false,
      tempRoute: {},
      isSelectable: true
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
      const id = this.$route.params && this.$route.params.id;
        if(typeof id!=='undefined') {
           this.postForm.sport_id = parseInt(id)
           this.isSelectable= false  
        }
    },
    onSubmit() {
      this.postForm.team_icon = this.team_icon;
      this.$refs.postForm.validate(valid => {
        if (valid) {
          this.loading = true;
          userResource
            .store(this.postForm)
              .then(response => {
                  if(response.errors){
                    this.loading = false;
                    this.team_icon = '';
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
                      message: 'New team added successfully',
                      type: 'success',
                      duration: 5 * 1000,
                    });
                    this.loading = false;
                    this.$router.push('/teams');
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
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) {
        return;
      }
      this.createImage(files[0]);
    },
    createImage(file) {
      // var team_icon = new Image();
      var reader = new FileReader();
      var vm = this;
      reader.onload = (e) => {
        vm.team_icon = e.target.result;
      };
      reader.readAsDataURL(file);
    },
    removeImage() {
      this.team_icon = '';
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

