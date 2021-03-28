<template>
  <div class="dashboard-container">
    <userDashboard v-if="siteview === 'front'" />
    <adminDashboard v-if="siteview === 'admin'" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import adminDashboard from './admin';
import userDashboard from './user';

export default {
  name: 'Dashboard',
  components: { adminDashboard, userDashboard },
  data() {
    return {
      currentRole: 'adminDashboard',
    };
  },
  computed: {
    ...mapGetters([
      'roles',
      'siteview',
    ]),
  },
  created() {
    console.log('======963');
    if (this.siteview === 'admin') {
      this.currentRole = 'adminDashboard';
    } else {
      this.currentRole = 'userDashboard';
    }
  },
};
</script>
