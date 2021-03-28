<template>
  <div>
    <section class="content-part bg-blue compitior_padding">
      <div class="container">
        <div class="row">
          <div class="col-sm-12">
            <div class="top-message">
              <div v-if="avatar === 'user.png'" class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fa fa-info-circle fa-lg" aria-hidden="true" /> You need to set your name and profile picture
                <router-link to="/account/settings" class="close">
                  <span aria-hidden="true">&#10230;</span>
                </router-link>
              </div>
              <div v-if="inviteMsg != ''" class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fa fa-info-circle fa-lg" aria-hidden="true" /> {{ inviteMsg }}
                <router-link to="/league/myleagues" class="close">
                  <span aria-hidden="true">&#10230;</span>
                </router-link>
              </div>
            </div>
          </div>

          <div class="col-sm-6">
            <div class="compition-part">
              <div class="compition-tag">
                <router-link to="/league/find"><img src="images/video1.jpg"></router-link>
              </div>
              <router-link to="/league/find"><div class="compition-caption white-bg">
                <p><strong>Find</strong> a competition <span>&#10230;</span></p>
              </div>
              </router-link>
            </div>
          </div>

          <div class="col-sm-6">
            <div class="compition-part">
              <div class="compition-tag">
                <router-link to="/league/home"><img src="images/video2.jpg"></router-link>
              </div>
              <router-link to="/league/home">
                <div class="compition-caption white-bg">
                  <p><strong>Create</strong> a competition <span>&#10230;</span></p>
                </div>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
<script>
import { mapGetters } from 'vuex';
import Resource from '@/api/resource';
import Cookies from 'js-cookie';
import store from '@/store';

const userResource = new Resource('join/leauge/user');

export default {
  name: 'DashboardEditor',
  props: {
    user: {
      type: Object,
      default: () => {
        return {
          permissions: [],
        };
      },
    },
  },
  data() {
    return {
      emptyGif: 'https://media.giphy.com/media/Ai8iZqHx2i0fK/giphy.gif',
      invite_id: undefined,
      lms: undefined,
      leaugeUserForm: {
        invite_id: '',
        lms: '',
      },
      admin_permission: false,
      inviteMsg: '',
    };
  },
  computed: {
    ...mapGetters([
      'nick_name',
      'avatar',
      'roles',
      'leagueId',
      'permissions',
      'siteview',
    ]),
  },
  watch: {
    $route: {
      handler: function(route) {
        this.leaugeUserForm.invite_id = route.query && route.query.id;
        this.leaugeUserForm.lms = route.query && route.query.lms;
        this.leaugeUserForm.mode = route.query && route.query.mode;
        if (!this.lms) {
          this.case = 2;
        }
      },
      immediate: true,
    },
  },
  created() {
    this.saveLeagueUser();
    if (this.permissions.includes('user_can_access_dashboard')){
      this.admin_permission = true;
    }
  },
  methods: {
    addtodashboard(){
      Cookies.set('user_key', 1);
      this.$store.dispatch('user/setview', 'admin');
    },
    saveLeagueUser() {
      if (this.leaugeUserForm.invite_id && this.leaugeUserForm.lms) {
        userResource
          .store(this.leaugeUserForm)
          .then(response => {
            if (response.data.status === 'active') {
              this.inviteMsg = 'You have joined the ' + response.data.league_name + ' competition';
            } else if (response.data.status === 'request_pending') {
              this.inviteMsg = 'Your request to join the competition ' + response.data.league_name + ' has been submitted';
            } else if (response.data.status === 'exist') {
              this.inviteMsg = 'You have already joined the ' + response.data.league_name + ' competition';
            } else if (response.data.status === 'emailWrong') {
              // remove token and go to login page to re-login
              this.$store.dispatch('user/resetToken');
              this.$router.push('login?lms=' + this.leaugeUserForm.lms + '&id=' + this.leaugeUserForm.invite_id + '&mode=system');
            } else {
              this.inviteMsg = 'Url is invalid';
            }

            setTimeout(() => {
              this.inviteMsg = '';
            }, 10000);

            // this.$router.push('/dashboard');
          });
      } else {
        console.log('error submitd!!');
        return false;
      }
    },
  },
};
</script>
<style type="text/css">
  .compition-caption p span{
    top: 46%;
  }
</style>
