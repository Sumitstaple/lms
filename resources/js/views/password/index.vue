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

    <footer>
      <div class="container">
        <div class="row">
          <div class="col-sm-6">
            <div class="footer-logo">
              <img src="images/lms.png">
            </div>
            <div class="footer-social">
              <ul>
                <li><a href="#" target="_blank"><i class="fa fa-instagram" aria-hidden="true" /></a></li>
                <li><a href="#" target="_blank"><i class="fa fa-facebook" aria-hidden="true" /></a></li>
                <li><a href="#" target="_blank"><i class="fa fa-envelope" aria-hidden="true" /></a></li>
              </ul>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="footer-menu">
              <ul>
                <li><a href="#">Lorem ipsum</a></li>
                <li><a href="#">Dolor sit</a></li>
                <li><a href="#">Dolor sit</a></li>
                <li><a href="#">Consectetur adipiscing</a></li>
                <li><a href="#">Consectetur adipiscing</a></li>
                <li><a href="#">Elit quisque</a></li>
                <li><a href="#">Elit quisque</a></li>
                <li><a href="#">Si varius vitae</a></li>
                <li><a href="#">Si varius vitae</a></li>
              </ul>
            </div>
          </div>

        </div>
      </div>
    </footer>

  </div>
</template>

<script>
import Vue from 'vue';
import { validEmail } from '@/utils/validate';
import VeeValidate from 'vee-validate';
import Resource from '@/api/resource';
const userResource = new Resource('auth/password/reset');
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

    return {
      loginForm: {
        email: '',
      },
      loginRules: {
        email: [{ required: true, trigger: 'blur', validator: validateEmail }],
      },
      loading: false,
      resetUrl: '',
      lmsName: '',
    };
  },
  methods: {
    passwordReset() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          userResource
            .store(this.loginForm)
            .then(response => {
              if (response.errors){
                this.sport_icon = '';
                var error = '';
                for (var i = 0; i <= response.errors.length; i++) {
                  if (typeof response.errors[i] !== 'undefined') {
                    error += response.errors[i] + ' ';
                  }
                }
                this.$message({
                  message: error,
                  type: 'error',
                  duration: 5 * 1000,
                });
              } else {
                // console.log(this.loginForm);
                this.$message({
                  message: 'We have e-mailed your password reset link',
                  type: 'success',
                  duration: 5 * 1000,
                });
                this.resetUrl = response;
                // this.loginForm.email='';
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
    margin-bottom: 60px;
    background: #FF4954;
    padding: 10px;
    margin-top: 0;
    border-radius: 6px 6px 0px 0px
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
