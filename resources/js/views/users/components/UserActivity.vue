<template>
  <el-card v-if="user.name">
    <el-tabs v-model="activeActivity" @tab-click="handleClick">
      <el-tab-pane label="Personal Detail" name="first">
        <el-form-item label="Nick Name">
          <el-input v-model="user.nickname" />
        </el-form-item>
        <el-form-item prop="is_marketing_option_active">
          <el-checkbox v-model="user.is_marketing_option_active" label="Marketing option" />
        </el-form-item>
        <el-form-item prop="is_notifications_option_active">
          <el-checkbox v-model="user.is_notifications_option_active" label="Notifications option" />
        </el-form-item>
        <el-form-item label="Change Profile" prop="sport_icon">
          <div v-if="!sport_icon">
            <input type="file" required @change="onFileChange">
          </div>
          <div v-else>
            <img :src="sport_icon" height="200" width="200" style="padding: 20px;border-radius: 100px;">
            <br>
            <el-button v-loading="loading" type="primary" @click="removeImage">
              Remove Image
            </el-button>
          </div>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="onSubmit">
            Update
          </el-button>
        </el-form-item>
      </el-tab-pane>
      <el-tab-pane v-loading="updating" label="Reset Password" name="second">
        <el-form ref="newpaswords" :model="newpaswords" :rules="passwordRule">
          <p>To reset your password please enter your new password and confirm in the fields below.</p>
          <el-form-item label="Password" prop="new">
            <el-input v-model="newpaswords.new" name="new" required />
          </el-form-item>
          <el-form-item label="Re-Password" prop="re">
            <el-input v-model="newpaswords.re" name="re" type="password" required />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="onPasswordSubmit">
              Reset Password
            </el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>
    </el-tabs>
  </el-card>
</template>

<script>
import Resource from '@/api/resource';
const userResource = new Resource('users');

export default {
  props: {
    user: {
      type: Object,
      default: () => {
        return {
          nickname: '',
          email: '',
          avatar: '',
          is_marketing_option_active: '',
          is_notifications_option_active: '',
        };
      },
    },
  },
  data() {
    const validatePass = (rule, value, callback) => {
      if (value.length < 1) {
        callback(new Error('Password Name Field is required'));
      } else {
        callback();
      }
    };
    const validateRePass = (rule, value, callback) => {
      if (value.length < 1) {
        callback(new Error('Re-Password Field is required'));
      } else if (this.newpaswords.new !== this.newpaswords.re){
        callback(new Error('Password and Re-Password Field should be same'));
      } else {
        callback();
      }
    };
    return {
      message: {
        text: '',
        type: '',
      },
      activeActivity: 'first',
      sport_icon: '',
      updated: {
        image: '',
        nickName: '',
        is_marketing_option_active: '',
        is_notifications_option_active: '',
      },
      newpaswords: {
        new: '',
        re: '',
      },
      passwordRule: {
        new: [{ required: true, validator: validatePass }],
        re: [{ required: true, validator: validateRePass }],
      },
      updating: false,
    };
  },
  methods: {
    handleClick(tab, event) {
      //  console.log('Switching tab ', tab, event);
    },
    async logout() {
      await this.$store.dispatch('user/logout');
      this.$router.push(`/login?redirect=${this.$route.fullPath}`);
    },
    onSubmit() {
      this.updated.image = this.sport_icon;
      this.updated.nickName = this.user.nickname;
      this.updated.is_marketing_option_active = this.user.is_marketing_option_active;
      this.updated.is_notifications_option_active = this.user.is_notifications_option_active;
      this.updating = true;
      userResource
        .update(this.user.id, this.updated)
        .then(response => {
          Bus.$emit('flash-message', this.message);
          this.updating = false;
          this.sport_icon = '';
          // this.$message({
          //   message: 'User information has been updated successfully',
          //   type: 'success',
          //   duration: 5 * 1000,
          // });
          this.message.text = 'User information has been updated successfully';
          this.message.type = 'success';
          // this.$router.push('/dashboard');
        })
        .catch(error => {
          console.log(error);
          this.updating = false;
        });
    },
    onPasswordSubmit() {
      this.$refs.newpaswords.validate(valid => {
        if (valid) {
          this.updating = true;
          userResource
            .update(this.user.id, this.newpaswords)
            .then(response => {
              Bus.$emit('flash-message', this.message);
              this.updating = false;
              // this.$message({
              //   message: 'Your Password Has been updated successfully!, You need to Re-login',
              //   type: 'success',
              //   duration: 5 * 1000,
              // });

              this.message.text = 'Your password has been updated successfully, you will shortly be logged out. Please re-login with your new password.';
              this.message.type = 'success';

              setTimeout(() => this.logout(), 5500);
            })
            .catch(error => {
              console.log(error);
              this.updating = false;
            });
        } else {
          return false;
        }
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

<style lang="scss" scoped>
.el-form-item {
  width: 60%
}
@media only screen and (max-width: 767px) {
  .el-form-item {
    width: 100%
  }
}
</style>
