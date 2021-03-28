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
                <router-link id="home-tab" class="nav-link" data-toggle="tab" to="/login" role="tab" aria-controls="home" aria-selected="true">LOGIN</router-link>
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
                  <div class="or-login"><span>OR</span></div>
                  <form class="login-form" />
                </div>
              </div>
              <div id="profile" class="tab-pane fade show active" role="tabpanel" aria-labelledby="profile-tab">
                <div class="login-signup">
                  <!-- <div class="social-login">
                    <a href="javascript:void(0);" target="_blank"><img src="images/fb.png"></a>
                    <a href="javascript:void(0);" target="_blank"><img src="images/twitter.png"></a>
                    <a href="javascript:void();" @click="AuthProvider('google')" target="_blank"><img src="images/email.png"></a>
                  </div> -->
                  <!-- <div class="or-login"><span>OR</span></div> -->
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
                      <ValidationProvider rules="required" name="Display Name">
                        <b-form-group
                          slot-scope="{ valid, errors }"
                        >
                          <b-form-input
                            v-model="signUpForm.display_name"
                            type="text"
                            :state="errors[0] ? false : (valid ? true : null)"
                            placeholder="Display Name"
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
                          >
                            <span style="color:grey">I accept the term and condition of last man standing</span>
                          </b-form-checkbox>
                          <b-form-invalid-feedback id="inputLiveFeedback">
                            {{ errors[0] }}
                          </b-form-invalid-feedback>
                        </b-form-group>
                      </ValidationProvider>
                      <button type="submit" class="submit-btn"><span>SIGN UP</span></button>

                      <div class="forgotten-password">
                        <a href="javascript:void(0);">Forgotten password?</a>
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
// import { validEmail } from '@/utils/validate';
import { ValidationProvider, ValidationObserver } from 'vee-validate';
import Vue from 'vue';
import VeeValidate from 'vee-validate';
import Cookies from 'js-cookie';
import VueAxios from 'vue-axios';
import VueSocialauth from 'vue-social-auth';
import axios from 'axios';

Vue.use(VeeValidate);
Vue.use(VueAxios, axios);
Vue.use(VueSocialauth, {
  providers: {
    google: {
      clientId: '230620643298-b4ukmg3oqp9vcth18dblao6eqivt3moq.apps.googleusercontent.com',
      redirectUri: 'http://dev.lastman.com.au/api/auth/google/callback', // Your client app URL
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
      user_data: [],
      signUpForm: {
        email: '',
        first_name: '',
        last_name: '',
        display_name: '',
        termAndCondition: '',
      },
      loading: false,
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
        this.lms = route.query && route.query.lms;
        this.inviteId = route.query && route.query.id;
        this.mode = route.query && route.query.mode;
      },
      immediate: true,
    },
  },
  created() {
    if (Cookies.get('user_data')) {
      this.users_data = JSON.parse(Cookies.get('user_data'));
      this.signUpForm.email = this.users_data.user.email;
      this.signUpForm.first_name = this.users_data.user.given_name;
      this.signUpForm.last_name = this.users_data.user.family_name;
      this.signUpForm.display_name = this.users_data.user.given_name;
      console.log(this.users_data);
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
            console.log(responsedata.status);

            if (responsedata.status === 'signup'){
              Cookies.set('user_data', JSON.stringify(responsedata.user));

              this.$router.push('/socialregister');
            } else {
              this.$router.push({ path: this.redirect || '/' });
              this.loading = false;
            }
          })
          .catch(() => {
            this.loading = false;
          });
      } else {
        alert(provider);
      }
    },
    handleSignUp() {
      // this.$refs.signUpForm.validate(valid => {
      // if (valid) {
      this.loading = true;
      this.$store.dispatch('user/socialsignup', this.signUpForm)
        .then(() => {
          if (this.lms && this.inviteId) {
            this.lmsUrl = '/dashboard?lms=' + this.lms + '&id=' + this.inviteId + '&mode=' + this.mode;
            this.$router.push({ path: this.lmsUrl });
          } else {
            this.$router.push({ path: this.redirect || '/' });
          }

          this.loading = false;
        })
        .catch(() => {
          this.loading = false;
        });
      // } else {
      //   console.log('error submit!!');
      //   return false;
      // }
      // });
    },
  },
};
</script>
<style rel="stylesheet/scss" lang="scss">
html,
body,
div,
span,
applet,
object,
iframe,
h1,
h2,
h3,
h4,
h5,
h6,
p,
blockquote,
pre,
a,
abbr,
acronym,
address,
big,
cite,
code,
del,
dfn,
em,
img,
ins,
kbd,
q,
s,
samp,
small,
strike,
strong,
sub,
sup,
tt,
var,
b,
u,
i,
center,
dl,
dt,
dd,
ol,
ul,
li,
fieldset,
form,
label,
legend,
table,
caption,
tbody,
tfoot,
thead,
tr,
th,
td,
article,
aside,
canvas,
details,
embed,
figure,
figcaption,
footer,
header,
hgroup,
menu,
nav,
output,
ruby,
section,
summary,
time,
mark,
audio,
video {
  margin: 0;
  padding: 0;
  border: 0;
  font-size: 100%;
  font: inherit;
  vertical-align: baseline;
}

article,
aside,
details,
figcaption,
figure,
footer,
header,
hgroup,
menu,
nav,
section {
  display: block;
}

body {
  line-height: 1;
  overflow-x: hidden;
}

ol,
ul {
  list-style: none;
}

blockquote,
q {
  quotes: none;
}

blockquote:before,
blockquote:after,
q:before,
q:after {
  content: "";
  content: none;
}

a {
  text-decoration: none;
  cursor: pointer;
}

table {
  border-collapse: collapse;
  border-spacing: 0;
}

input,
button {
  outline: 0;
}

body{
  margin: 0 auto;
  font-family: 'DM Sans', sans-serif;
}
nav.navbar {
    background-color: #156DE6;
    padding: 0px;
    height: 140px;
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 1;
}
nav.navbar ul.navbar-nav li a.nav-link{
    font-size: 14px;
    color: #fff;
    display: flex;
    vertical-align: middle;
    align-items: center;
}
nav.navbar ul.navbar-nav li a.nav-link:hover, nav.navbar ul.navbar-nav li a.nav-link:focus{
  color: #fff;
}
a.navbar-brand img {
    max-width: 245px;
}
nav.navbar ul.navbar-nav li a.nav-link img{
    max-width: 45px;
    margin-left: 12px;
}
header .nav-right .navbar-nav{
  float: right;
}
/****/
.bg-blue{
  background: #0081FF;
}
section.content-part {
    padding: 40px 0px 70px;
    margin-top: 140px;
}
.video-tag img {
    width: 100%;
    border-radius: 0px 0px 6px 6px;
}
.video-part {
    text-align: center;
}
.video-part h2{
    color: #fff;
    font-family: 'Titillium Web', sans-serif;
    font-weight: 900;
    font-size: 18px;
    letter-spacing: 0.5px;
    margin: 0px;
    align-items: center;
    vertical-align: middle;
    height: 40px;
    padding: 9px 0;
    border-radius: 6px 6px 0px 0px;
}
.video-part p.description {
    color: #333333;
    background-color: #fff;
    margin: 0px;
    height: 70px;
    padding: 7px 10px;
    font-size: 11px;
    display: flex;
    align-items: center;
    line-height: 16px;
}
.video-tag {
    position: relative;
    text-align: center;
    margin: 0 auto;
}
.video-tag img.play-btn {
    position: absolute;
    max-width: 50px;
    top: 40%;
    text-align: center;
    margin: 0 auto;
    left: 50%;
    transform: translate(-50%);
}
.video-part .caption p {
    color: #fff;
    font-size: 12px;
    padding: 3px 33px;
    line-height: 15px;
    margin-bottom: 0px;
}
.video-part .caption {
    margin: 23px 0px 0px;
}
.video-part .caption a {
    width: 250px;
    border-radius: 6px;
    display: block;
    align-items: center;
    margin: 0 auto;
    vertical-align: middle;
    color: #FFFFFF;
    font-family: "Titillium Web";
    font-size: 21px;
    font-weight: 900;
    letter-spacing: 0.58px;
    line-height: 18px;
    text-align: center;
    padding: 15px 0px;
    margin-top: 30px;
    transition: all 0.5s;
}
button.submit-btn{
  transition: all 0.5s;
}
.video-part .caption a span, button.submit-btn span{
  transition: all 0.5s;
  position: relative;
}
.video-part .caption a span:after, button.submit-btn span:after {
     content: "\f101";
    font-family: FontAwesome;
    position: absolute;
    opacity: 0;
    top: 8px;
    right: -20px;
    transition: 0.5s;
}

.video-part .caption a:hover span, button.submit-btn:hover span {
  padding-right: 20px;
}

.video-part .caption a:hover span:after, button.submit-btn:hover span:after {
  opacity: 1;
  right: 0;
}
.video-part .caption a:hover, .video-part .caption a:focus{
  text-decoration: none;
  color: #fff;
}
/*******/
footer {
    background: #282855;
    padding: 30px 0px;
}
.footer-logo img {
    max-width: 90px;
}
.footer-menu ul li {
    list-style: none;
    width: 50%;
    display: inline-block;
    float: left;
}
.footer-menu ul li a{
  font-size: 12px;
    line-height: 24px;
    color: #fff;
}
.footer-menu ul li a:hover, .footer-menu ul li a:focus{
  text-decoration: none;
  color: #fff;
}
.footer-menu ul{
  margin: 0px;
}
.footer-social ul {
    margin: 70px 0px 0px;
    padding: 0px;
}
.footer-social ul li {
    list-style: none;
    display: inline-block;
}
.footer-social ul li a {
    color: #0081FF;
    border-radius: 100px;
    border: 1px solid #0081FF;
    width: 30px;
    height: 30px;
    display: inline-block;
    text-align: center;
    /*padding: 6px 0px;*/
}
/******************/
.compition-tag img {
    width: 100%;
    border-radius: 6px 6px 0px 0px;
}
.white-bg {
    background: #fff;
}
.compition-caption {
    height: 60px;
    display: flex;
    border-radius: 0px 0px 6px 6px;
    padding: 10px 20px;
    align-items: center;
    margin-top: -4px;
}
.compition-caption p strong {
    font-weight: 700;
}
.compition-caption p  {
    color: #282855;
    font-size: 18px;
    width: 100%;
    position: relative;
    cursor: pointer;
}
.compition-caption p a, .compition-caption p a:focus, .compition-caption p a:hover{
  color: #282855;
  text-decoration: none;
}

.compition-caption:hover span {
    right: 12px!important;
}
.top-message .alert-warning {
    color: #fff;
    font-size: 14px;
    background: #156DE6;
    border-color: #156DE6;
}
.top-message .alert-dismissible .close {
    position: absolute;
    top: 0;
    right: 0;
    padding: 6px 1.25rem;
    color: #fff;
    opacity: 0.9;
}
.top-message .alert-dismissible .close:hover{
  color: #fff;
    opacity: 0.9;
}
.top-message .alert-warning .fa-info-circle {
    margin-right: 4px;
}
.compition-part a{
  display: inline-block;
  width: 100%;
}
.compition-part a:hover, .compition-part a:focus{
  text-decoration: none;
}
/*******************/
/************************Bhiwani******************/
/*********************************************************************login css start******************************/
section.login_signup-page{
  padding: 40px 0px 240px
}
.login-signup {
    width: 100%;
    background-color: transparent;
    position: relative;
    margin: 35px auto 40px;
    text-align: center;
    padding: 0 30px;
}

.login-form .form-control {
  color: #333333;
  font-size: 14px;
  font-family: 'DM Sans', sans-serif;
  display: block;
  width: 100%;
  padding: 2px 28px;
  line-height: 1.5;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #DEDEDE;
  border-radius: 2px;
  height: 41px;
  margin-bottom: 4px;
}
.login-form .form-control::-webkit-input-placeholder { /* Chrome/Opera/Safari */
  color: #333333;
}
.login-form .form-control::-moz-placeholder { /* Firefox 19+ */
  color: #333333;
}
.login-form .form-control:-ms-input-placeholder { /* IE 10+ */
  color: #333333;
}
.login-form .form-control:-moz-placeholder { /* Firefox 18- */
  color: #333333;
}
.login-form .form-control:focus{
  outline: none;
  box-shadow: none;
}

.submit-btn {
  width: 280px;
  margin-top: 28px;
  text-align: center;
  padding: 13px;
  border-radius: 6px;
  background-color: #ff4954;
  color: #FFF;
  border: none;
  transition: 0.5s cubic-bezier(0.72, 0.15, 0.53, 0.84);
  font-size: 21px;
  font-weight: 900;
  line-height: 18px;
  font-family: "Titillium Web";
  letter-spacing: 0.58px;
}
.submit-btn:hover {
  background-color: #f9303c;
}
.forgotten-password a {
    text-align: center;
    color: #333333;
    text-decoration: underline;
    font-size: 10px;
}
.forgotten-password {
  height: 12px;
  width: 100%;
  color: #333333;
  font-size: 9px;
  letter-spacing: 0;
  line-height: 12px;
  text-align: center;
  margin-top: 24px;
}

.social-login {
    text-align: center;
}
.social-login img {
    width: 40px;
    height: 40px;
    margin-right: 5px;
}

.login-signup-content {
    max-width: 520px;
    position: relative;
    background-color: transparent;
    margin: 0 auto;
    border-radius: 0 0 11px 11px;
}
.login-box .nav-tabs {
    border-bottom: 1px solid #dee2e6;
    border-radius: 11px 11px 0 0;
}
.login-box {
    background-color: transparent;
    width: 520px;
}
.login-box .nav-link {
    display: block;
    padding: .5rem 1rem;
    background-color: #DEDEDE;
    border-radius: 11px 11px 0 0;
    color: #333333;
    font-size: 11px;
    font-weight: bold;
    letter-spacing: 0;
    line-height: 14px;
    text-align: center;
    height: 42px;
    padding: 13px;
    font-family: 'DM Sans', sans-serif;
}

.login-box  .nav-tabs .nav-link{
    border-radius: 11px 11px 0 0;
}
.login-box .nav-tabs .nav-item {
    margin-bottom: -1px;
    width: 50%;
    height: 40px;
    text-align: center;
}
.login-box{
    border-radius: 0 0 11px 11px;

}
.or-login span {
    position: relative;
    font-family: 'DM Sans', sans-serif;
    font-size: 11px;
    color: #333333;
}
.or-login span:before {
    content: "";
    position: absolute;
    width: 50px;
    height: 1px;
    background-color: #333333;
    left: -60px;
    top: 7px;
    font-family: 'DM Sans', sans-serif;
}

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
.invalid-feedback {
    text-align: left;
}
.was-validated .custom-control-input:valid:checked ~ .custom-control-label::before, .custom-control-input.is-valid:checked ~ .custom-control-label::before {
    border-color: #156DE6;
    background-color: #156DE6;
}
.custom-checkbox .was-validated .custom-control-input:valid ~ .custom-control-label::before, .custom-checkbox .custom-control-input.is-valid ~ .custom-control-label::before {
    border-color: #156DE6!important;
}
.custom-checkbox .custom-control-label::before {
    top: 0;
    left: -34px;
    border-radius: 0px;
}
.custom-checkbox .custom-control-label::after{
  background-size: 12px;
  top: 0;
  left: -34px;
}
.custom-control.custom-checkbox {
    padding-left: 0px;
}

.login-signup h4 {
    color: #333333;
    margin: 25px 0px 28px;
    display: block;
}
input[type="radio"]:focus, input[type="checkbox"]:focus{
  outline: none;
  box-shadow: none;
  outline-offset: 0px!important;
}
.login-form .custom-checkbox .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #156DE6!important;
}
.login-form .custom-checkbox .custom-control-label::after {
    background-size: 15px;
    top: 0;
    left: 3px;
}
</style>
