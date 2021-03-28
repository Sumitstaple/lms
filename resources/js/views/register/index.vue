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
        <div class="login-signup-content">
          <div class="login-box">
            <ul id="myTab" class="nav nav-tabs" role="tablist">
              <li class="nav-item">

                <router-link v-if="this.lms" id="home-tab" class="nav-link" data-toggle="tab" :to="'/login'+routeParm" role="tab" aria-controls="home" aria-selected="true">LOGIN</router-link>

                <router-link v-else id="home-tab" class="nav-link" data-toggle="tab" to="/login" role="tab" aria-controls="home" aria-selected="true">LOGIN</router-link>

              </li>
              <li class="nav-item">
                <a id="profile-tab" class="nav-link active" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">SIGN UP</a>
              </li>
            </ul>
            <div id="myTabContent" class="tab-content">
              <div id="home" class="tab-pane fade" role="tabpanel" aria-labelledby="home-tab">
                <div class="login-signup">
                  <div class="social-login">
                    <a href="" target="_blank"><img src="images/fb.png"></a>
                    <a href="" target="_blank"><img src="images/twitter.png"></a>
                    <a href="javascript:void();" target="_blank" @click="AuthProvider('google')"><img src="images/email.png"></a>
                  </div>
                  <div class="or-login"><span>OR 0000000</span></div> 

                  <form class="login-form" />
                  <div class="or-login"><span>OR</span></div>
                  <form class="login-form" />

                </div> 
              </div>
              <div id="profile" class="tab-pane fade show active" role="tabpanel" aria-labelledby="profile-tab">
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

                  <ValidationObserver ref="observer">
                    <b-form slot-scope="{ validate }" class="login-form" @submit.prevent="validate().then(handleSignUp)">
                      <ValidationProvider rules="required|email" name="Email">
                        <b-form-group
                          slot-scope="{ valid, errors }"
                        >
                          <b-form-input
                            v-model="signUpForm.email"
                            type="email"
                            :state="errors[0] ? false : (valid ? true : null)"
                            placeholder="Email"
                          />
                          <b-form-invalid-feedback id="inputLiveFeedback">
                            {{ errors[0] }}
                          </b-form-invalid-feedback>
                        </b-form-group>
                      </ValidationProvider>

                      <ValidationProvider rules="required" name="First Name">
                        <b-form-group
                          slot-scope="{ valid, errors }"
                        >
                          <b-form-input
                            v-model="signUpForm.first_name"
                            type="text"
                            :state="errors[0] ? false : (valid ? true : null)"
                            placeholder="First Name"
                          />
                          <b-form-invalid-feedback id="inputLiveFeedback">
                            {{ errors[0] }}
                          </b-form-invalid-feedback>
                        </b-form-group>
                      </ValidationProvider>

                      <ValidationProvider rules="required" name="Last Name">
                        <b-form-group
                          slot-scope="{ valid, errors }"
                        >
                          <b-form-input
                            v-model="signUpForm.last_name"
                            type="text"
                            :state="errors[0] ? false : (valid ? true : null)"
                            placeholder="Last Name"
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
                            v-model="signUpForm.password"
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
                            v-model="signUpForm.password_confirmation"
                            type="password"
                            :state="errors[0] ? false : (valid ? true : null)"
                            placeholder="Confirm Password"
                          />
                          <b-form-invalid-feedback id="inputLiveFeedback">
                            {{ errors[0] }}
                          </b-form-invalid-feedback>
                        </b-form-group>
                      </ValidationProvider>

                      <ValidationProvider rules="required" name="Password confirmation">
                        <b-form-group
                          slot-scope="{ valid, errors }"
                          label-for="exampleInput1"
                        >
                          <b-form-checkbox
                            v-model="signUpForm.termAndCondition"
                            type="checkbox"
                            :state="errors[0] ? false : (valid ? true : null)"
                            value="true"
                            unchecked-value="false"
                            @change="checkterm()"
                          > 
                            <span style="color:grey">I accept the term and condition of last man standing</span>
                          </b-form-checkbox>
                          <div v-if="termerror == true" id="inputLiveFeedback" class="invalid-feedback" style="display: block;">
                          Please check the term and condition box.
                        </div>
                        </b-form-group>
                      </ValidationProvider>

                      <button type="submit" class="submit-btn"><span>SIGN UP</span></button>

                      <div class="forgotten-password">
                         <router-link to="/password/reset">Forgotten password?</router-link>
                      </div>
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
            <div class="footer-social">this.lms
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
import VeeValidate from 'vee-validate';

import Resource from '@/api/resource';
const checkEmail = new Resource('find/user/email');
const getLeauge = new Resource('find/league');

import Vue from 'vue';
import Cookies from 'js-cookie';
import VueAxios from 'vue-axios';
import VueSocialauth from 'vue-social-auth';
import axios from 'axios';
import { ValidationProvider, ValidationObserver } from 'vee-validate';
Vue.use(VueAxios, axios);
Vue.use(VeeValidate);
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
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  data() {
    return {
      password: '',
      password_confirmation: '',
      loginForm: {
        email: '',
        password: '',
      },
      termerror:false,
      signUpForm: {
        email: '',
        first_name: '',
        last_name: '',
        password: '',
        password_confirmation: '',
        termAndCondition: 'true',
        leagueId: '',
        inviteId: '',
        mode: '',
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
    showConfirmPwd() {
      if (this.pwdConfirmType === 'password') {
        this.pwdConfirmType = '';
      } else {
        this.pwdConfirmType = 'password';
      }
    },
    handleLogin() {
      this.$refs.loginForm.validate(valid => {
        if (valid) {
          this.loading = true;
          if (this.lms && this.inviteId) {
            this.lmsUrl = '/?lms=' + this.lms + '&id=' + this.inviteId + '&mode=' + this.mode;
            this.$router.push({ path: this.redirect || this.lmsUrl });
          }
          this.$store.dispatch('user/login', this.loginForm)
            .then(() => {
              this.$router.push({ path: this.redirect || this.lmsUrl });
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
    checkterm(){
       if(this.signUpForm.termAndCondition == "true"){
        this.termerror= true;
       }
       else{
        this.termerror= false;
       }
    },
    handleSignUp() {

      if(this.termerror == true){
        return false;
      }
     
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.loading = true;
          this.signUpForm.leagueId = this.lms;
          this.signUpForm.inviteId = this.inviteId;
          this.signUpForm.mode = this.mode;
          if (this.lms && this.inviteId) {
            this.lmsUrl = '/?lms=' + this.lms + '&id=' + this.inviteId + '&mode=' + this.mode;
            this.$router.push({ path: this.redirect || this.lmsUrl });
          }
          this.$store.dispatch('user/signup', this.signUpForm)
            .then(() => {
              this.$router.push({ path: this.redirect || this.lmsUrl });
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
    changeEmail() {
      this.checkEmail = undefined;
      this.showAllField = undefined;
      this.showPass = undefined;
      this.showLogin = undefined;
      this.isVerify = true;
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss">
html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,embed,figure,figcaption,footer,header,hgroup,menu,nav,output,ruby,section,summary,time,mark,audio,video{margin:0;padding:0;border:0;font-size:100%;font:inherit;vertical-align:baseline}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block}body{line-height:1;overflow-x:hidden}ol,ul{list-style:none}blockquote,q{quotes:none}blockquote:before,blockquote:after,q:before,q:after{content:"";content:none}a{text-decoration:none;cursor:pointer}table{border-collapse:collapse;border-spacing:0}input,button{outline:0}body{margin:0 auto;font-family:'DM Sans',sans-serif}nav.navbar ul.navbar-nav li a.nav-link{font-size:14px;color:#fff;display:flex;vertical-align:middle;align-items:center}nav.navbar ul.navbar-nav li a.nav-link:hover,nav.navbar ul.navbar-nav li a.nav-link:focus{color:#fff}nav.navbar ul.navbar-nav li a.nav-link img{max-width:45px;margin-left:12px}header .nav-right .navbar-nav{float:right}.bg-blue{background:#0081FF}.video-tag img{width:100%;border-radius:0 0 6px 6px}.video-part{text-align:center}.video-part h2{color:#fff;font-family:'Titillium Web',sans-serif;font-weight:900;font-size:18px;letter-spacing:.5px;margin:0;align-items:center;vertical-align:middle;height:40px;padding:9px 0;border-radius:6px 6px 0 0}.video-part p.description{color:#333;background-color:#fff;margin:0;height:70px;padding:7px 10px;font-size:11px;display:flex;align-items:center;line-height:16px}.video-tag{position:relative;text-align:center;margin:0 auto}.video-tag img.play-btn{position:absolute;max-width:50px;top:40%;text-align:center;margin:0 auto;left:50%;transform:translate(-50%)}.video-part .caption p{color:#fff;font-size:12px;padding:3px 33px;line-height:15px;margin-bottom:0}.video-part .caption{margin:23px 0 0}.video-part .caption a{width:250px;border-radius:6px;display:block;align-items:center;margin:0 auto;vertical-align:middle;color:#FFF;font-family:"Titillium Web";font-size:21px;font-weight:900;letter-spacing:.58px;line-height:18px;text-align:center;padding:15px 0;margin-top:30px;transition:all 0.5s}button.submit-btn{transition:all 0.5s}.video-part .caption a span,button.submit-btn span{transition:all 0.5s;position:relative}.video-part .caption a span:after,button.submit-btn span:after{content:"\f101";font-family:FontAwesome;position:absolute;opacity:0;top:8px;right:-20px;transition:0.5s}.video-part .caption a:hover span,button.submit-btn:hover span{padding-right:20px}.video-part .caption a:hover span:after,button.submit-btn:hover span:after{opacity:1;right:0}.video-part .caption a:hover,.video-part .caption a:focus{text-decoration:none;color:#fff}footer{background:#282855;padding:30px 0}.footer-logo img{max-width:90px}.footer-menu ul li{list-style:none;width:50%;display:inline-block;float:left}.footer-menu ul li a{font-size:12px;line-height:24px;color:#fff}.footer-menu ul li a:hover,.footer-menu ul li a:focus{text-decoration:none;color:#fff}.footer-menu ul{margin:0}.footer-social ul{margin:70px 0 0;padding:0}.footer-social ul li{list-style:none;display:inline-block}.footer-social ul li a{color:#0081FF;border-radius:100px;border:1px solid #0081FF;width:30px;height:30px;display:inline-block;text-align:center}.compition-tag img{width:100%;border-radius:6px 6px 0 0}.white-bg{background:#fff}.compition-caption p strong{font-weight:700}.compition-caption p{color:#282855;font-size:18px;width:100%;position:relative;cursor:pointer}.compition-caption p a,.compition-caption p a:focus,.compition-caption p a:hover{color:#282855;text-decoration:none}.compition-caption:hover span{right:12px!important}.top-message .alert-warning{color:#fff;font-size:14px;background:#156DE6;border-color:#156DE6}.top-message .alert-dismissible .close{position:absolute;top:0;right:0;padding:6px 1.25rem;color:#fff;opacity:.9}.top-message .alert-dismissible .close:hover{color:#fff;opacity:.9}.top-message .alert-warning .fa-info-circle{margin-right:4px}.compition-part a{display:inline-block;width:100%}.compition-part a:hover,.compition-part a:focus{text-decoration:none}section.login_signup-page{padding:40px 0 240px}.login-signup{width:100%;background-color:transparent;position:relative;margin:35px auto 40px;text-align:center;padding:0 30px}.login-form .form-control{color:#333;font-size:14px;font-family:'DM Sans',sans-serif;display:block;width:100%;padding:2px 28px;line-height:1.5;background-color:#fff;background-clip:padding-box;border:1px solid #DEDEDE;border-radius:2px;height:41px;margin-bottom:4px}.login-form .form-control::-webkit-input-placeholder{color:#333}.login-form .form-control::-moz-placeholder{color:#333}.login-form .form-control:-ms-input-placeholder{color:#333}.login-form .form-control:-moz-placeholder{color:#333}.login-form .form-control:focus{outline:none;box-shadow:none}.submit-btn{width:280px;margin-top:28px;text-align:center;padding:13px;border-radius:6px;background-color:#ff4954;color:#FFF;border:none;transition:0.5s cubic-bezier(.72,.15,.53,.84);font-size:21px;font-weight:900;line-height:18px;font-family:"Titillium Web";letter-spacing:.58px}.submit-btn:hover{background-color:#f9303c}.forgotten-password a{text-align:center;color:#333;text-decoration:underline;font-size:10px}.forgotten-password{height:12px;width:100%;color:#333;font-size:9px;letter-spacing:0;line-height:12px;text-align:center;margin-top:24px}.social-login{text-align:center}.social-login img{width:40px;height:40px;margin-right:5px}.login-signup-content{max-width:520px;position:relative;background-color:transparent;margin:0 auto;border-radius:0 0 11px 11px}.login-box .nav-tabs{border-bottom:1px solid #dee2e6;border-radius:11px 11px 0 0}.login-box{background-color:transparent;width:520px}.login-box .nav-link{display:block;padding:.5rem 1rem;background-color:#DEDEDE;border-radius:11px 11px 0 0;color:#333;font-size:11px;font-weight:700;letter-spacing:0;line-height:14px;text-align:center;height:42px;padding:13px;font-family:'DM Sans',sans-serif}.login-box .nav-tabs .nav-link{border-radius:11px 11px 0 0}.login-box .nav-tabs .nav-item{margin-bottom:-1px;width:50%;height:40px;text-align:center}.login-box{border-radius:0 0 11px 11px}.or-login span{position:relative;font-family:'DM Sans',sans-serif;font-size:11px;color:#333}.or-login span:before{content:"";position:absolute;width:50px;height:1px;background-color:#333;left:-60px;top:7px;font-family:'DM Sans',sans-serif}.or-login span:after{content:"";position:absolute;background-color:#333;width:50px;height:1px;left:24px;top:7px}.login-box .tab-content>.active{display:flex;height:auto;align-items:center}.tab-content{background-color:#fff;border-radius:0 0 11px 11px}.or-login{margin:28px 0 20px}.field-text .is-danger{text-align:left}.login-signup .help.is-danger{text-align:left;display:block;font-size:10px;margin-bottom:10px;color:red}.login-signup .input.is-danger{border-color:red}.invalid-feedback{text-align:left}.was-validated .custom-control-input:valid:checked~.custom-control-label::before,.custom-control-input.is-valid:checked~.custom-control-label::before{border-color:#156DE6;background-color:#156DE6}.custom-checkbox .was-validated .custom-control-input:valid~.custom-control-label::before,.custom-checkbox .custom-control-input.is-valid~.custom-control-label::before{border-color:#156DE6!important}.custom-checkbox .custom-control-label::before{top:0;left:-34px;border-radius:0}.custom-checkbox .custom-control-label::after{background-size:12px;top:0;left:-34px}.custom-control.custom-checkbox{padding-left:0}.login-signup h4{color:#333;margin:25px 0 28px;display:block}input[type="radio"]:focus,input[type="checkbox"]:focus{outline:none;box-shadow:none;outline-offset:0px!important}.login-form .custom-checkbox .custom-control-input:checked~.custom-control-label::before{background-color:#156DE6!important}.login-form .custom-checkbox .custom-control-label::after{background-size:15px;top:6px;left:3px}.el-message{min-width:290px!important}@media screen and (device-aspect-ratio:2/3){select,textarea,input[type="text"],input[type="password"],input[type="datetime"],input[type="datetime-local"],input[type="date"],input[type="month"],input[type="time"],input[type="week"],input[type="number"],input[type="email"],input[type="url"]{font-size:14px!important}}@media screen and (device-aspect-ratio:40/71){select,textarea,input[type="text"],input[type="password"],input[type="datetime"],input[type="datetime-local"],input[type="date"],input[type="month"],input[type="time"],input[type="week"],input[type="number"],input[type="email"],input[type="url"]{font-size:14px!important}}@media screen and (device-aspect-ratio:375/667){select,textarea,input[type="text"],input[type="password"],input[type="datetime"],input[type="datetime-local"],input[type="date"],input[type="month"],input[type="time"],input[type="week"],input[type="number"],input[type="email"],input[type="tel"],input[type="url"]{font-size:14px!important}}@media screen and (device-aspect-ratio:9/16){select,textarea,input[type="text"],input[type="password"],input[type="datetime"],input[type="datetime-local"],input[type="date"],input[type="month"],input[type="time"],input[type="week"],input[type="number"],input[type="email"],input[type="tel"],input[type="url"]{font-size:14px!important}}
</style>

