<template>
  <div class="navbar">
    <hamburger id="hamburger-container" :is-active="sidebar.opened" class="hamburger-container" @toggleClick="toggleSideBar" />

    <breadcrumb id="breadcrumb-container" class="breadcrumb-container" />

    <div class="right-menu">
      <template v-if="device!=='mobile'">
        <search id="header-search" class="right-menu-item" />

        <screenfull id="screenfull" class="right-menu-item hover-effect" />

        <el-tooltip :content="$t('navbar.size')" effect="dark" placement="bottom">
          <size-select id="size-select" class="right-menu-item hover-effect" />
        </el-tooltip>

        <lang-select class="right-menu-item hover-effect" />

      </template>

      <el-dropdown class="avatar-container right-menu-item hover-effect admin_count" trigger="click">
        <div class="avatar-wrapper">
          <img src="images/icon-notification_Icons.png" class="topnotification" class-name="notification-icon" icon-class="notification" @click="markAll($event)"><span class="count">{{ count }}</span>
          <!-- <svg-icon  /> -->
        </div>
        <el-dropdown-menu slot="dropdown" class="dropdown-main">
          <!--  <div class="dropdown-header">
            <span class="notification-title">{{ count }} new Notification</span>
            <span class="markall" @click="markAll($event)">Mark all as read</span>
          </div> -->
          <el-dropdown-item v-for="item in notifications" :key="item.message">
            <li>{{ item.message }}</li>
          </el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>

      <el-dropdown class="avatar-container right-menu-item hover-effect admin_prfle" trigger="click">
        <div class="avatar-wrapper">
          <img v-if="avatar != 'user.png'" :src="'uploads/profile/'+avatar" class="user-avatar">
          <img v-else src="images/users.png" class="user-avatar">
          <i class="el-icon-caret-bottom" />
        </div>
        <el-dropdown-menu slot="dropdown">
          <router-link to="/">
            <el-dropdown-item>
              {{ $t('navbar.dashboard') }}
            </el-dropdown-item>
          </router-link>
          <el-dropdown-item divided>
            <span v-if="admin_permission === true" @click="addtodashboard()">Go to user dashboard</span>
          </el-dropdown-item>
          <el-dropdown-item divided>
            <span style="display:block;" @click="logout">{{ $t('navbar.logOut') }}</span>
          </el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import Breadcrumb from '@/components/Breadcrumb';
import Hamburger from '@/components/Hamburger';
import Screenfull from '@/components/Screenfull';
import SizeSelect from '@/components/SizeSelect';
import LangSelect from '@/components/LangSelect';
import Search from '@/components/HeaderSearch';
import { fetchNotifications } from '@/api/notification';
import { markasread } from '@/api/notification';
import { getunread } from '@/api/notification';
import Cookies from 'js-cookie';
var $ = require('jquery');

export default {
  components: {
    Breadcrumb,
    Hamburger,
    Screenfull,
    SizeSelect,
    LangSelect,
    Search,
  },
  data() {
    return {
      notifications: [
      ],
      count: '',
      admin_permission: false,
    };
  },
  computed: {
    ...mapGetters([
      'sidebar',
      'name',
      'avatar',
      'device',
      'userId',
      'permissions',
    ]),
  },
  mounted() {
    // alert(this.userId);

    $('body').removeClass('click');
  },
  created(){
    // alert();
    this.getList();
    this.getunread();
    if (this.permissions.includes('admin_permission')){
      this.admin_permission = true;
    }
  },
  methods: {
    addtodashboard(){
      Cookies.set('user_key', 0);
      this.$store.dispatch('user/setview', 'front');
      this.$router.push('/dashboard');
    },
    toggleSideBar() {
      this.$store.dispatch('app/toggleSideBar');
    },
    async logout() {
      await this.$store.dispatch('user/logout');
      this.$router.push(`/login?redirect=${this.$route.fullPath}`);
    },
    async getList() {
      this.listLoading = true;
      const { data } = await fetchNotifications();
      this.notifications = data.items.slice(0, 10);
    },
    async markAll(event) {
      this.listLoading = true;
      await markasread();
      if (this.count > 0){
        this.$message({
          message: 'Mark as read successfully',
          type: 'success',
          duration: 5 * 1000,
        });
      }
      this.getList();
      this.getunread();
    },
    async getunread(id){
      // alert(id);
      this.listLoading = true;
      const { data } = await getunread();
      this.count = data.total;
      // this.markAll();
    },
  },
};
</script>
<style type="text/css">
  img.topnotification {
    margin-top: 10px;
}
</style>
<style lang="scss" scoped>

.el-dropdown-menu__item li {
    margin: 10px 0px;
    padding: 5px 0px;
    border-bottom: 1px solid #40c9c6;
}
span.markall {
  float: right;
  cursor: pointer;
}

.dropdown-main {
  min-width: 300px;
}

span.markall:hover {
  font-size: 17px;
}

span.count {
  color: white;
  top: -20px;
  position: relative;
  border-radius: 2rem;
  background: red;
  padding: 3px;
  font-size: 10px;
  left: -6px;
}

.dropdown-header {
  background: #42b983;
  padding: 10px;
  color: #fff;
}

.dropdown-main {
  margin: 0px;
  padding: 0px;
}

.notification-main {
  display: inline-block;
}
.el-message {
    min-width: 290px !important;
    }
.right-menu .topnotification {
  vertical-align: 0.2em;
  cursor: pointer;
}
/************************/

.admin_count, .admin_prfle {
  margin-right: 0px!important;
}
.admin_prfle .avatar-wrapper{
  margin-top: 0px!important;
}
.admin_prfle{
  padding-left: 0px!important;
}
.admin_count{
  padding-right: 0px!important;
}
.admin_prfle img {
    width: 26px!important;
    height: 26px!important;
}
.admin_prfle i.el-icon-caret-bottom{
  right: -15px!important;
  top: 22px!important;
}
.admin_count .topnotification{
  vertical-align: 0.1em!important;
}
/************************/

.navbar {

  height: 65px;
  overflow: hidden;
  position: relative;
  background: #fff;
  box-shadow: 0 1px 4px rgba(0, 21, 41, .08);
  .hamburger-container {
    line-height: 46px;
    height: 100%;
    float: left;
    cursor: pointer;
    transition: background .3s;
    -webkit-tap-highlight-color: transparent;
    &:hover {
      background: rgba(0, 0, 0, .025)
    }
  }
  .breadcrumb-container {
    float: left;
  }
  .errLog-container {
    display: inline-block;
    vertical-align: top;
  }
  .right-menu {
    float: right;
    height: 100%;
    line-height: 50px;
    &:focus {
      outline: none;
    }
    .right-menu-item {
      display: inline-block;
      padding: 0 8px;
      height: 100%;
      font-size: 18px;
      color: #5a5e66;
      vertical-align: text-bottom;
      &.hover-effect {
        cursor: pointer;
        transition: background .3s;
        &:hover {
          background: rgba(0, 0, 0, .025)
        }
      }
    }
    .avatar-container {
      margin-right: 30px;
      .avatar-wrapper {
        margin-top: 5px;
        position: relative;
        .user-avatar {
          cursor: pointer;
          width: 40px;
          height: 40px;
          border-radius: 4px;
        }
        .el-icon-caret-bottom {
          cursor: pointer;
          position: absolute;
          right: -20px;
          top: 25px;
          font-size: 12px;
        }
      }
    }
  }
}
</style>
