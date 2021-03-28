<template>
  <div class="wrapper">
    <header>
      <nav class="navbar navbar-dark navbar-expand-md justify-content-between login-menu">
        <div class="container">
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".dual-nav">
            <span class="navbar-toggler-icon" />
          </button>
          <div class="navbar-collapse collapse dual-nav w-50 order-md-0">
                  &nbsp;
          </div>
          <a href="/" class="navbar-brand mx-auto d-block text-center order-md-1 w-25"><img src="images/logo.png"></a>
          <div class="w-50 order-2 nav-right">
                &nbsp;
          </div>
        </div>
      </nav>
    </header>

    <section class="content-part login_signup-page bg-blue">
      <div class="container">
        <!-----------------form start-------------------------------->
        <div class="login-signup-content forget_page">
          <div class="login-box">
            <div id="myTabContent" class="tab-content">
              <div id="home" class="tab-pane fade show active" role="tabpanel" aria-labelledby="home-tab">
                <div class="login-signup">
                  <h4 class="leagueTitle">
                    Forgot Password
                  </h4>
                  <p>To reset your password please enter your email with a new password and then confirm your new password.</p>
                  <ValidationObserver ref="observer">
                    <b-form slot-scope="{ validate }" class="login-form" @submit.prevent="validate().then(passwordReset)">

                      <ValidationProvider rules="required|email" name="Email">
                        <b-form-group
                          slot-scope="{ valid, errors }"
                        >
                          <b-form-input
                            v-model="loginForm.email"
                            type="email"
                            :state="errors[0] ? false : (valid ? true : null)"
                            placeholder="Email"
                          />
                          <b-form-invalid-feedback id="inputLiveFeedback">
                            {{ errors[0] }}
                          </b-form-invalid-feedback>
                        </b-form-group>
                      </ValidationProvider>
                      <ValidationProvider rules="required|min:6" name="Password" vid="password">
                        <b-form-group
                          slot-scope="{ valid, errors }"
                        >
                          <b-form-input
                            v-model="loginForm.password"
                            type="password"
                            :state="errors[0] ? false : (valid ? true : null)"
                            placeholder="Password"
                          />
                          <b-form-invalid-feedback id="inputLiveFeedback">
                            {{ errors[0] }}
                          </b-form-invalid-feedback>
                        </b-form-group>
                      </ValidationProvider>

                      <ValidationProvider rules="required|confirmed:password" name="Password confirmation">
                        <b-form-group
                          slot-scope="{ valid, errors }"
                          label-for="exampleInput1"
                        >
                          <b-form-input
                            v-model="loginForm.password_confirmation"
                            type="password"
                            :state="errors[0] ? false : (valid ? true : null)"
                            placeholder="Confirm Password"
                          />
                          <b-form-invalid-feedback id="inputLiveFeedback">
                            {{ errors[0] }}
                          </b-form-invalid-feedback>
                        </b-form-group>
                      </ValidationProvider>
                      <button type="submit" class="submit-btn"><span>Reset Password</span></button>

                    </b-form>
                  </ValidationObserver>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
</template>

<script>
import VeeValidate from 'vee-validate';
import Vue from 'vue';
import { validEmail } from '@/utils/validate';
import Resource from '@/api/resource';
const userResource = new Resource('auth/password/forgot');
import { ValidationProvider, ValidationObserver } from 'vee-validate';

Vue.use(VeeValidate);

export default {
  name: 'Login',
  components: {
    ValidationProvider,
    ValidationObserver,
  },
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
      if (value !== this.loginForm.password) {
        callback(new Error('Password must be match the confirm password'));
      } else {
        callback();
      }
    };

    return {
      loginForm: {
        email: '',
        password: '',
        password_confirmation: '',
        token: '',
      },
      loginRules: {
        email: [{ required: true, trigger: 'blur', validator: validateEmail }],
        password: [{ required: true, trigger: 'blur', validator: validatePass }],
        password_confirmation: [{ required: true, trigger: 'blur', validator: validateConfirmPass }],
      },
      loading: false,
      resetUrl: '',
      pwdType: 'password',
      pwdConfirmType: 'password',
      token: undefined,
    };
  },
  watch: {
    $route: {
      handler: function(route) {
        this.token = route.query && route.query.token;
      },
      immediate: true,
    },
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
    passwordReset() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.loginForm.token = this.token;
          userResource
            .store(this.loginForm)
            .then(response => {
              if (response.errors){
                this.sport_icon = '';
                this.$message({
                  message: error,
                  type: 'error',
                  duration: 5 * 1000,
                });
              } else {
                this.$message({
                  message: 'Your password has been reset, Please login with new password',
                  type: 'success',
                  duration: 5 * 1000,
                });
                this.$router.push('/login');
              }
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss">
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
      color: $light_gray;
      height: 47px;
      &:-webkit-autofill {
        -webkit-box-shadow: 0 0 0px 1000px $bg inset !important;
        -webkit-text-fill-color: #fff !important;
      }
    }
  }
  .el-form-item {
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    color: #454545;
  }
}

</style>

<style rel="stylesheet/scss" lang="scss" scoped>
$bg:#2d3a4b;
$dark_gray:#889aa4;
$light_gray:#eee;
.login-container {
  position: fixed;
  height: 100%;
  width: 100%;
  background-color: $bg;
  .login-form {
    position: absolute;
    left: 0;
    right: 0;
    width: 520px;
    max-width: 100%;
    padding: 35px 35px 15px 35px;
    margin: 80px auto;
  }
  .tips {
    font-size: 14px;
    color: #fff;
    margin-bottom: 10px;
    span {
      &:first-of-type {
        margin-right: 16px;
      }
    }
  }
  .svg-container {
    padding: 6px 5px 6px 15px;
    color: $dark_gray;
    vertical-align: middle;
    width: 30px;
    display: inline-block;
  }
  .title {
    font-size: 26px;
    font-weight: 400;
    color: $light_gray;
    margin: 0px auto 40px auto;
    text-align: center;
    font-weight: bold;
  }
  .leagueTitle {
    color: $light_gray;
  }
  .show-pwd {
    position: absolute;
    right: 10px;
    top: 7px;
    font-size: 16px;
    color: $dark_gray;
    cursor: pointer;
    user-select: none;
  }
  .set-language {
    color: #fff;
    position: absolute;
    top: 40px;
    right: 35px;
  }
}
.forget_page h4.leagueTitle {
    color: #fff;
    font-family: 'Titillium Web', sans-serif;
    font-weight: 700;
    margin-bottom: 10px;
    background: #FF4954;
    padding: 10px;
    border-radius: 6px 6px 0px 0px;
    margin-top: 0;
}
.forget_page .login-signup {
    padding: 0px;
    margin-top: 0px;
    border-radius: 6px 6px 0px 0px;
}
.forget_page .login-box .tab-content .tab-pane, .forget_page .tab-content{
    border-radius: 6px 6px 0px 0px;
}
.forget_page form.login-form{
  padding: 0 30px;
}
.forget_page #inputLiveFeedback{
  text-align: left;
}
</style>
