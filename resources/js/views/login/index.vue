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
        <div class="login-signup-content">
          <div class="login-box">
            <ul id="myTab" class="nav nav-tabs" role="tablist">
              <li class="nav-item">
                <a id="home-tab" class="nav-link active" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">LOGIN</a>
              </li>
              <li class="nav-item">
                <router-link v-if="this.lms" id="profile-tab" class="nav-link" data-toggle="tab" :to="'/register'+routeParm" role="tab" aria-controls="profile" aria-selected="false">SIGN UP</router-link>

                <router-link v-else id="profile-tab" class="nav-link" data-toggle="tab" to="/register" role="tab" aria-controls="profile" aria-selected="false">SIGN UP</router-link>
              </li>
            </ul>

            <div id="myTabContent" class="tab-content">
              <div id="home" class="tab-pane fade show active" role="tabpanel" aria-labelledby="home-tab">
                <div class="login-signup">
                  <div class="social-login">
                    <a href="javascript:void();" target="_blank" @click="AuthProvider('facebook')"><img src="images/fb.png"></a>
                    <a href="javascript:void();" target="_blank" @click="AuthProvider('twitter')"><img src="images/twitter.png"></a>
                    <a href="javascript:void();" target="_blank" @click="AuthProvider('google')"><img src="images/email.png"></a>
                  </div>
                  <div class="or-login"><span>OR</span></div>
                  <h4 v-if="lms" class="leagueTitle">
                    {{ lmsName }}
                  </h4>
                  <form class="login-form" @submit.prevent="handleLogin">
                    <div class="form-group">
                      <input v-model="loginForm.email" v-validate="'required|email'" name="email" :class="{'input form-control': true, 'is-danger form-control': errors.has('email') }" type="text" placeholder="Email">
                      <span v-show="errors.has('email')" class="help is-danger">{{ errors.first('email') }}</span>
                    </div>
                    <div class="form-group">
                      <input v-model="loginForm.password" v-validate="'required|min: 6'" name="password" :class="{'input form-control': true, 'is-danger form-control': errors.has('password') }" type="password" placeholder="Password">
                      <span v-show="errors.has('password')" class="help is-danger">{{ errors.first('password') }}</span>
                    </div>
                    <button type="submit" class="submit-btn"><span>LOGIN</span></button>
                    <div class="forgotten-password">
                      <router-link to="/password/reset">Forgotten password?</router-link>
                    </div>
                  </form>
                </div>
              </div>
              <div id="profile" class="tab-pane fade" role="tabpanel" aria-labelledby="profile-tab">
                <div class="login-signup">
                  <div class="social-login">
                    <a href="#" target="_blank"><img src="images/fb.png"></a>
                    <a href="#" target="_blank"><img src="images/twitter.png"></a>
                    <a href="#" target="_blank"><img src="images/insta.png"></a>
                  </div>
                  </form>
                </div>
              </div>
              <div id="profile" class="tab-pane fade" role="tabpanel" aria-labelledby="profile-tab">
                <div class="login-signup">
                  <div class="social-login">
                    <a href="#" target="_blank"><img src="images/fb.png"></a>
                    <a href="#" target="_blank"><img src="images/twitter.png"></a>
                    <a href="#" target="_blank"><img src="images/insta.png"></a>
                  </div>
                  <div class="or-login"><span>OR</span></div>
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

import Resource from '@/api/resource';
import Vue from 'vue';
import VeeValidate from 'vee-validate';
import Cookies from 'js-cookie';
import VueAxios from 'vue-axios';
import VueSocialauth from 'vue-social-auth';
import axios from 'axios';
var $ = require('jquery');
const getLeauge = new Resource('find/league');

Vue.use(VeeValidate);
Vue.use(VueAxios, axios);
Vue.use(VueSocialauth, {
  providers: {
    github: {
      clientId: '',
      redirectUri: 'https://app.lastman.com.au/api/auth/github/callback', // Your client app URL
    },
    google: {
      clientId: '146442162115-h011krau1e34o4g2jbbp9dscn1ku1s3m.apps.googleusercontent.com',
      redirectUri: 'https://app.lastman.com.au/auth/google/callback', // Your client app URL
    },
    facebook: {
      clientId: '3682803811770552',
      redirectUri: 'https://app.lastman.com.au/auth/facebook/callback', // Your client app URL
    },
    twitter: {
      clientId: '18717339',
      redirectUri: 'https://app.lastman.com.au/auth/twitter/callback', // Your client app URL
    },
  },
});
export default {

  name: 'Login',
  data() {
    return {
      loginForm: {
        email: '',
        password: '',
        leagueId: '',
        inviteId: '',
        mode: '',
      },
      loading: false,
      pwdType: 'password',
      redirect: undefined,
      lms: undefined,
      inviteId: undefined,
      mode: undefined,
      lmsUrl: '/',
      lmsName: '',
      routeParm: '',
    };
  },
  watch: {
    $route: {
      handler: function(route) {
        this.redirect = route.query && route.query.redirect;
        this.lms = route.query && route.query.lms;
        this.inviteId = route.query && route.query.id;
        this.mode = route.query && route.query.mode;
        this.routeParm = '?lms=' + this.lms + '&id=' + this.inviteId + '&mode=' + this.mode;
      },
      immediate: true,
    },
  },
  mounted() {
    // alert();

    $('body').addClass('click');
  },
  created() {
    if (this.lms) {
      this.getLeauge();
    }
  },
  methods: {
    AuthProvider(provider) {
      var self = this;
      this.$auth.authenticate(provider).then(response => {
        self.SocialLogin(provider, response);
      }).catch(err => {
        console.log({ err: err });
      });
    },

    SocialLogin(provider, response){
      if (provider === 'google'){
        this.$store.dispatch('user/googlelogin', response)
          .then((responsedata) => {
            if (responsedata.status === 'signup'){
              Cookies.set('user_data', JSON.stringify(responsedata.user));

              if (this.lms && this.inviteId) {
                this.lmsUrl = '/socialregister?lms=' + this.lms + '&id=' + this.inviteId + '&mode=' + this.mode;
                this.$router.push({ path: this.lmsUrl });
              } else {
                this.$router.push('/socialregister');
              }
            } else {
              if (this.lms && this.inviteId) {
                this.lmsUrl = '/?lms=' + this.lms + '&id=' + this.inviteId + '&mode=' + this.mode;
                this.$router.push({ path: this.lmsUrl });
              } else {
                this.$router.push({ path: this.redirect || '/' });
              }
              this.loading = false;
            }
          })
          .catch(() => {
            this.loading = false;
          });
      } else if (provider === 'twitter') {
        this.$store.dispatch('user/checklogin', response)
          .then((responsedata) => {
            if (this.lms && this.inviteId) {
              this.lmsUrl = '/?lms=' + this.lms + '&id=' + this.inviteId + '&mode=' + this.mode;
              this.$router.push({ path: this.lmsUrl });
            } else {
              this.$router.push({ path: this.redirect || '/' });
            }
            this.loading = false;
          })
          .catch(() => {
            this.loading = false;
          });
      } else {
        this.$store.dispatch('user/facebooklogin', response)
          .then((responsedata) => {
            if (this.lms && this.inviteId) {
              this.lmsUrl = '/?lms=' + this.lms + '&id=' + this.inviteId + '&mode=' + this.mode;
              this.$router.push({ path: this.lmsUrl });
            } else {
              this.$router.push({ path: this.redirect || '/' });
            }
            this.loading = false;
          })
          .catch(() => {
            this.loading = false;
          });
      }
    },

    showPwd() {
      if (this.pwdType === 'password') {
        this.pwdType = '';
      } else {
        this.pwdType = 'password';
      }
    },

    handleLogin() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.loading = true;
          if (this.lms && this.inviteId) {
            this.lmsUrl = '/?lms=' + this.lms + '&id=' + this.inviteId + '&mode=' + this.mode;

            this.loginForm.leagueId = this.lms;
            this.loginForm.inviteId = this.inviteId;
            this.loginForm.mode = this.mode;
          }
          this.$store.dispatch('user/login', this.loginForm)
            .then(() => {
              $('.el-message').css('display', 'none');
              if (this.lmsUrl) {
                this.$router.push({ path: this.lmsUrl });
              } else {
                this.$router.push({ path: this.redirect });
              }

              this.loading = false;
            })
            .catch(() => {
              this.loading = false;
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },

    getLeauge() {
      getLeauge
        .get(this.lms)
        .then(response => {
          if (response.data.status === 'success') {
            this.lmsName = 'You are signing up to join competition ' + response.data.leauge_name;
          } else {
            this.$message({
              message: 'Your url is invalid',
              type: 'error',
              duration: 5 * 1000,
            });
          }
        });
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss">

.or-login span:after {
    content: "";
    position: absolute;
    background-color: #333333;
    width: 50px;
    height: 1px;
    left: 24px;
    top: 7px;
}
.login-box .tab-content>.active {
    display: flex;
    height: auto;
    align-items: center;
}
.tab-content {
    background-color: #fff;
    border-radius: 0 0 11px 11px;
}
.or-login {
    margin: 28px 0 20px;
}

.field-text .is-danger{
text-align: left;
}
.login-signup .help.is-danger {
    text-align: left;
    display: block;
    font-size: 10px;
    margin-bottom: 10px;
    color: red;
}
.login-signup .input.is-danger {
    border-color: red;
}

.login-signup h4 {
    color: #333333;
    margin: 25px 0px 28px;
    display: block;
}
.el-message {
    min-width: 290px !important;
    }

/*** iPhone and iOS Form Input Zoom Fixes ***/
/* Fix Input Zoom on devices older than iPhone 5: */
@media screen and (device-aspect-ratio: 2/3) {
    select, textarea, input[type="text"], input[type="password"],
    input[type="datetime"], input[type="datetime-local"],
    input[type="date"], input[type="month"], input[type="time"],
    input[type="week"], input[type="number"], input[type="email"],
    input[type="url"]{ font-size: 14px!important; }
}

/* Fix Input Zoom on iPhone 5, 5C, 5S, iPod Touch 5g */
@media screen and (device-aspect-ratio: 40/71) {
    select, textarea, input[type="text"], input[type="password"],
    input[type="datetime"], input[type="datetime-local"],
    input[type="date"], input[type="month"], input[type="time"],
    input[type="week"], input[type="number"], input[type="email"],
    input[type="url"]{ font-size: 14px!important; }
}

/* Fix Input Zoom on iPhone 6, iPhone 6s, iPhone 7  */
@media screen and (device-aspect-ratio: 375/667) {
    select, textarea, input[type="text"], input[type="password"],
    input[type="datetime"], input[type="datetime-local"],
    input[type="date"], input[type="month"], input[type="time"],
    input[type="week"], input[type="number"], input[type="email"], 
    input[type="tel"], input[type="url"]{ font-size: 14px!important; }
}

/* Fix Input Zoom on iPhone 6 Plus, iPhone 6s Plus, iPhone 7 Plus, iPhone 8, iPhone X, XS, XS Max  */
@media screen and (device-aspect-ratio: 9/16) {
    select, textarea, input[type="text"], input[type="password"],
    input[type="datetime"], input[type="datetime-local"],
    input[type="date"], input[type="month"], input[type="time"],
    input[type="week"], input[type="number"], input[type="email"],
    input[type="tel"], input[type="url"]{ font-size: 14px!important; }
}
</style>
