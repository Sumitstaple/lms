<template>
  <div class="app-container">
    <el-form ref="postForm" :model="postForm" :rules="teamRules" label-width="120px">
      <el-form-item label="Team Name" prop="team_name" >
        <el-col :span="12">
          <el-input v-model="postForm.team_name" name="team_name" type="text" required />
        </el-col> 
      </el-form-item>
      <el-form-item label="Select Sport" prop="sport_id">
        <el-select v-model="postForm.sport_id" placeholder="Please select your sport" v-bind:style="{ 'width': '50%' }">
          <el-option v-for="item in sportList" :label="item.sport_name" :value="item.id" />
        </el-select>
      </el-form-item>
      <el-form-item label="Team Icon" prop="team_icon">
        <div v-if="!team_icon">
          <input type="file" @change="onFileChange" required />
        </div>
        <div v-else>
          <img v-if="is_team_icon" :src="iconPath+team_icon" />
          <img v-else :src="team_icon" />
          <el-button v-loading="loading" type="primary" @click="removeImage">
          Remove Team Icon
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
import { fetchSportList } from '@/api/sport';
import Resource from '@/api/resource';
const userResource = new Resource('team');
import { fetchTeam } from '@/api/sport';

export default {
  data() {
    const validateName = (rule, value, callback) => {
      if (value.length < 1) {
        callback(new Error('Team Name Field is required'));
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
      iconPath: '/uploads/team/',
      team_icon: '',
      is_team_icon: false,
      postForm: {
        team_name: '',
        team_icon:'',
      },
      teamRules: {
        team_name: [{ required: true, validator: validateName }],
        team_icon: [{ required: true, validator: validateIcon }],
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
      fetchTeam(id)
        .then(response => {
          this.postForm = response.data;
          this.team_icon = response.data.team_icon;
          if(response.data.team_icon) {
            this.is_team_icon = true;
          }
        })
        .catch(err => {
          console.log(err);
        });
    },
    onSubmit() {
      this.postForm.team_icon = this.team_icon;
      this.$refs.postForm.validate(valid => {
        if (valid) {
          this.loading = true;
          userResource
            .update(this.postForm.id,this.postForm)
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
                      message: 'Team update successfully',
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
      this.$router.push('/teams');
    },
    onFileChange(e) {
      this.is_team_icon = false;
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

