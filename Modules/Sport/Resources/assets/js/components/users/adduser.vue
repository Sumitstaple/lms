<template>
  <div v-if="lms && !checkEmail" class="login-container">
    <el-form ref="loginForm" :model="loginForm" :rules="loginRules" class="login-form" auto-complete="on" label-position="left">
      <h3 class="title">
        Sign up
      </h3>
      <h3 v-if="lms" class="leagueTitle">
        {{ lmsName }}
      </h3>
      <el-form-item prop="email">
        <span class="svg-container">
          <svg-icon icon-class="email" />
        </span>
        <el-input v-model="loginForm.email" name="email" type="text" auto-complete="on" :placeholder="$t('login.email')" />
        <span v-if="!isVerify" class="show-pwd" @click="changeEmail"> Change Email </span>
      </el-form-item>
      <el-form-item v-if="isVerify">
        <el-button :loading="loading" type="" style="width:100%;" @click.native.prevent="VerifyEmail">
          Verify Email
        </el-button>
      </el-form-item>
      <el-form-item v-if="showPass" prop="password">
        <span class="svg-container">
          <svg-icon icon-class="password" />
        </span>
        <el-input
          v-model="loginForm.password"
          :type="pwdType"
          name="password"
          auto-complete="on"
          placeholder="Password"
          @keyup.enter.native="handleLogin"
        />
        <span class="show-pwd" @click="showPwd">
          <svg-icon icon-class="eye" />
        </span>
      </el-form-item>
      <el-form-item v-if="showPass">
        <el-button :loading="loading" type="primary" style="width:100%;" @click.native.prevent="handleLogin">
          Login
        </el-button>
      </el-form-item>
    </el-form>
  </div>
  <div v-else-if="!lms || showAllField" class="login-container">
    <el-form ref="signUpForm" :model="signUpForm" :rules="signUpRules" class="login-form" auto-complete="on" label-position="left">
      <h3 class="title">
        Sign up
      </h3>
      <h3 v-if="lms" class="leagueTitle">
        {{ lmsName }}
      </h3>
      <el-form-item prop="email">
        <span class="svg-container">
          <svg-icon icon-class="email" />
        </span>
        <el-input v-model="signUpForm.email" name="email" type="text" auto-complete="on" :placeholder="$t('login.email')" />
        <span v-if="!isVerify" class="show-pwd" @click="changeEmail"> Change Email </span>
      </el-form-item>
      <el-form-item prop="first_name">
        <span class="svg-container">
          <svg-icon icon-class="user" />
        </span>
        <el-input v-model="signUpForm.first_name" name="first_name" type="text" auto-complete="on" placeholder="First name" />
      </el-form-item>
      <el-form-item prop="last_name">
        <span class="svg-container">
          <svg-icon icon-class="user" />
        </span>
        <el-input v-model="signUpForm.last_name" name="last_name" type="text" auto-complete="on" placeholder="Last name" />
      </el-form-item>
      <el-form-item prop="password">
        <span class="svg-container">
          <svg-icon icon-class="password" />
        </span>
        <el-input
          v-model="signUpForm.password"
          :type="pwdType"
          name="password"
          auto-complete="on"
          placeholder="Password"
          @keyup.enter.native="handleLogin"
        />
        <span class="show-pwd" @click="showPwd">
          <svg-icon icon-class="eye" />
        </span>
      </el-form-item>
      <el-form-item prop="password_confirmation">
        <span class="svg-container">
          <svg-icon icon-class="password" />
        </span>
        <el-input
          v-model="signUpForm.password_confirmation"
          :type="pwdConfirmType"
          name="password_confirmation"
          auto-complete="on"
          placeholder="Confirm Password"
          @keyup.enter.native="handleLogin"
        />
        <span class="show-pwd" @click="showConfirmPwd">
          <svg-icon icon-class="eye" />
        </span>
      </el-form-item>
      <el-form-item prop="termAndCondition">
        <el-checkbox v-model="signUpForm.termAndCondition" label="I accept the terms and conditions of last man standing" />
      </el-form-item>
      <el-form-item>
        <el-button :loading="loading" type="primary" style="width:100%;" @click.native.prevent="handleSignUp">
          Sign Up
        </el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import { validEmail } from '@/utils/validate';
import { signupuser } from '@/api/user';

import Resource from '@/api/resource';
const checkEmail = new Resource('find/user/email');
const getLeauge = new Resource('find/league');

import Cookies from 'js-cookie';

export default {
  name: 'Login',
  data() {
    const validateEmail = (rule, value, callback) => {
      if (!validEmail(value)) {
        callback(new Error('Please enter the correct email'));
      } else {
        callback();
      }
    };
    const validatePass = (rule, value, callback) => {
      if (value.length < 8) {
        callback(new Error('Password cannot be less than 8 digits'));
      } else {
        callback();
      }
    };
    const validateConfirmPass = (rule, value, callback) => {
      if (value !== this.signUpForm.password) {
        callback(new Error('Password must be match the confirm password'));
      } else {
        callback();
      }
    };

    return {

      signUpForm: {
        email: '',
        first_name: '',
        last_name: '',
        password: '',
        password_confirmation: '',
        termAndCondition: '',
        leagueId: '',
      },
      signUpRules: {
        email: [{ required: true, trigger: 'blur', validator: validateEmail }],
        first_name: [{ required: true, trigger: 'blur' }],
        last_name: [{ required: true, trigger: 'blur' }],
        password: [{ required: true, trigger: 'blur', validator: validatePass }],
        password_confirmation: [{ required: true, trigger: 'blur', validator: validateConfirmPass }],
        termAndCondition: [{ required: true }],
      },
      loading: false,
      pwdType: 'password',
      pwdConfirmType: 'password',
      redirect: undefined,
      checkEmail: undefined,
      showAllField: undefined,
      showPass: undefined,
      showLogin: undefined,
      isVerify: true,
      lmsName: '',
      lms: undefined,
      inviteId: undefined,
      mode: undefined,
      lmsUrl: '/',
    };
  },
  watch: {
    $route: {
      handler: function(route) {
        this.redirect = route.query && route.query.redirect;
        this.lms = route.query && route.query.lms;
        this.inviteId = route.query && route.query.id;
        this.mode = route.query && route.query.mode;
      },
      immediate: true,
    },
  },

  created() {
    if (this.lms) {
      this.getLeauge();
      if (localStorage.getItem('user_data')) {
        this.users_data = JSON.parse(localStorage.getItem('user_data'));
        this.signUpForm.email = this.users_data.user.email;
        this.signUpForm.first_name = this.users_data.user.given_name;
        this.signUpForm.last_name = this.users_data.user.family_name;
      }
    }
  },

  methods: {

    showPwd() {
      if (this.pwdType === 'password') {
        this.pwdType = '';
      } else {
        this.pwdType = 'password';
      }
    },
    showConfirmPwd() {
      if (this.pwdConfirmType === 'password') {
        this.pwdConfirmType = '';
      } else {
        this.pwdConfirmType = 'password';
      }
    },
    handleSignUp() {
      this.$refs.signUpForm.validate(valid => {
        if (valid) {
          this.loading = true;

           signupuser(this.signUpForm)
        .then(response => {
          if(response.data.status == "success"){
             this.$message({
                message: 'New user added successfully',
                type: 'success',
                duration: 5 * 1000,
              });
             this.$router.push('/users');
             this.loading = false;
          }
          else{
            this.$message({
                message: 'fail to add new user',
                type: 'fail',
                duration: 5 * 1000,
              });
             this.loading = false;
          }
        })
        .catch(err => {
          console.log(err);
        });

        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    VerifyEmail() {
      this.$refs.loginForm.validate(valid => {
        if (valid) {
          this.signUpForm.email = this.loginForm.email;
          this.loading = true;
          this.isVerify = undefined;
          this.checkEmailExist();
          this.loading = false;
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    checkEmailExist() {
      checkEmail
        .store(this.loginForm)
        .then(response => {
          if (response.data.status === 'yes') {
            this.showLogin = 1;
            this.showPass = 1;
            this.showAllField = undefined;
          } else {
            this.showAllField = 1;
            this.showPass = 1;
            this.checkEmail = 1;
          }
        });
    },

  },
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>

$bg:#2d3a4b;
$light_gray:#eee;

/* reset element-ui css */

.login-container {
  margin-top: 4px;
}
.login-container {
  .el-input {
    display: inline-block;
    height: 47px;
    width: 85%;
    input {
      background: transparent;
      border: 0px;
      -webkit-appearance: none;
      border-radius: 0px;
      padding: 12px 5px 12px 15px;
      height: 47px;
    }
  }
  .el-form-item {
    border: 1px solid rgba(255, 255, 255, 0.1);;
    border-radius: 5px;
    color: #454545;
  }
}
.el-input.el-input--medium {
    width: 50% !important;
}
button.el-button.el-button--primary.el-button--medium {
    width: auto !important;
}
</style>
