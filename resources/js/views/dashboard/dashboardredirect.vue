<template>
  <div class="dashboard-container">
    <component :is="currentRole" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import adminDashboard from './admin';
import userDashboard from './user';
import Cookies from 'js-cookie';
import Layout from '@/layout';

export default {
  name: 'Dashboardredirect',
  components: { adminDashboard, userDashboard },
  data() {
    return {
      currentRole: 'adminDashboard',
    };
  },
  computed: {
    ...mapGetters([
      'roles',
      'user_key',
    ]),
  },
  created() {
    this.$store.state.user_key = Cookies.get('user_key');

    this.$router.push('/dashboard');

    if (Cookies.get('user_key') == 1){
      // alert(Cookies.get('user_key'));
      this.currentRole = 'adminDashboard';
    } else {
      if (!this.roles.includes('admin')) {
        this.currentRole = 'userDashboard';
      }
    }
  },
};
</script>
