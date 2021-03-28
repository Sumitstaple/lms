<template>
  <div v-if="siteview === 'admin'" :class="classObj" class="app-wrapper">
    <div v-if="device==='mobile' && sidebar.opened" class="drawer-bg" @click="handleClickOutside" />
    <sidebar v-if="siteview === 'admin'" class="sidebar-container" />
    <div v-if="siteview === 'admin'" :class="{hasTagsView:needTagsView}" class="main-container">
      <div v-if="siteview === 'admin'" :class="{'fixed-header':fixedHeader}">
        <navbar />
      </div>
      <app-main />
    </div>
  </div>
  </div>
  <div v-else class="wrapper">
    <FrontNavbar />
    <app-main />
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
import RightPanel from '@/components/RightPanel';
import { Navbar, Sidebar, AppMain, TagsView, Settings, FrontNavbar, FrontMain } from './components';
import ResizeMixin from './mixin/resize-handler.js';
import { mapState } from 'vuex';
import checkRole from '@/utils/role';
import Cookies from 'js-cookie';
import { mapGetters } from 'vuex';

export default {
  name: 'Layout',
  components: {
    AppMain,
    Navbar,
    FrontNavbar,
    RightPanel,
    Settings,
    Sidebar,
    TagsView,
    FrontMain,
  },
  mixins: [ResizeMixin],
  computed: {
    ...mapState({
      sidebar: state => state.app.sidebar,
      device: state => state.app.device,
      showSettings: state => state.settings.showSettings,
      needTagsView: state => state.settings.tagsView,
      fixedHeader: state => state.settings.fixedHeader,
    }),
    ...mapGetters([
      'siteview',
    ]),
    classObj() {
      return {
        hideSidebar: !this.sidebar.opened,
        openSidebar: this.sidebar.opened,
        withoutAnimation: this.sidebar.withoutAnimation,
        mobile: this.device === 'mobile',
      };
    },
  },
  created() {
    console.log(this.siteview);
  },
  methods: {
    checkRole,
    handleClickOutside() {
      this.$store.dispatch('app/closeSideBar', { withoutAnimation: false });
    },
  },
};
</script>
