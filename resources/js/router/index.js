import Vue from 'vue';
import Router from 'vue-router';
import addSport from './../../../Modules/Sport/Resources/assets/js/components/sports/create.vue';
import editSport from './../../../Modules/Sport/Resources/assets/js/components/sports/edit.vue';
import allSport from './../../../Modules/Sport/Resources/assets/js/components/sports/index.vue';
import SportsTeams from './../../../Modules/Sport/Resources/assets/js/components/sports/sportsteams.vue';
import SportsRounds from './../../../Modules/Sport/Resources/assets/js/components/sports/sportsrounds.vue';
import addRound from './../../../Modules/Sport/Resources/assets/js/components/rounds/create.vue';
import allRound from './../../../Modules/Sport/Resources/assets/js/components/rounds/index.vue';
import editRound from './../../../Modules/Sport/Resources/assets/js/components/rounds/edit.vue';
import addTeam from './../../../Modules/Sport/Resources/assets/js/components/teams/create.vue';
import allTeam from './../../../Modules/Sport/Resources/assets/js/components/teams/index.vue';
import editTeam from './../../../Modules/Sport/Resources/assets/js/components/teams/edit.vue';
import allFixtures from './../../../Modules/Sport/Resources/assets/js/components/fixtures/index.vue';
import addFixtures from './../../../Modules/Sport/Resources/assets/js/components/fixtures/create.vue';
import editFixtures from './../../../Modules/Sport/Resources/assets/js/components/fixtures/edit.vue';
import RoundsFixtures from './../../../Modules/Sport/Resources/assets/js/components/fixtures/roundsfixtures.vue';

import allLeagueTeam from './../../../Modules/Sport/Resources/assets/js/components/leagueteams/index.vue';
import AssignTeams from './../../../Modules/Sport/Resources/assets/js/components/leagueteams/assignteams.vue';
import ManageTeams from './../../../Modules/Sport/Resources/assets/js/components/leagueteams/manageteams.vue';

import allTemplates from './../../../Modules/Sport/Resources/assets/js/components/notificationtemplate/index.vue';
import systemLogs from './../../../Modules/Sport/Resources/assets/js/components/systemlogs/index.vue';

import leaguecreatehome from './../../../Modules/Frontend/Resources/assets/js/components/leagues/index.vue';

import createLeage from './../../../Modules/Frontend/Resources/assets/js/components/leagues/createleague.vue';

import leageList from './../../../Modules/Frontend/Resources/assets/js/components/leagues/list.vue';
import leageEdit from './../../../Modules/Frontend/Resources/assets/js/components/leagues/edit.vue';
import leaugeAddTeam from './../../../Modules/Frontend/Resources/assets/js/components/leagues/addteam.vue';

import myLeagues from './../../../Modules/Frontend/Resources/assets/js/components/leagues/myleagues.vue';
import myOtherLeagues from './../../../Modules/Frontend/Resources/assets/js/components/leagues/yourleagues.vue';
import userLeagueDetail from './../../../Modules/Frontend/Resources/assets/js/components/leagues/userleaguedetail.vue';

import Autocomplete from './../../../Modules/Sport/Resources/assets/js/components/autocomplete/index.vue';
import searchLeague from './../../../Modules/Frontend/Resources/assets/js/components/leagues/findLeagues.vue';
import viewLeague from './../../../Modules/Frontend/Resources/assets/js/components/leagues/viewLeague.vue';

import resultIndex from './../../../Modules/Result/Resources/assets/js/components/result/index.vue';
import resultfixture from './../../../Modules/Result/Resources/assets/js/components/result/fixture.vue';

// Users
import viewUsers from './../../../Modules/Sport/Resources/assets/js/components/users/index.vue';
import addNewUser from './../../../Modules/Sport/Resources/assets/js/components/users/adduser.vue';

import lmsHomePage from './../../../Modules/Frontend/Resources/assets/js/components/lmshome/index.vue';

/**
 * Layzloading will create many files and slow on compiling, so best not to use lazyloading on devlopment.
 * The syntax is lazyloading, but we convert to proper require() with babel-plugin-syntax-dynamic-import
 * @see https://doc.laravue.dev/guide/advanced/lazy-loading.html
 */

Vue.use(Router);

/* Layout */
import Layout from '@/layout';
import errorRoutes from './modules/error';

export const asyncRoutes = [
  errorRoutes,
  {
    path: '/sports',
    component: Layout,
    redirect: '/sports',
    alwaysShow: true,
    meta: {
      permissions: ['admin_permission'],
      title: 'Sports',
      icon: 'sport',

    },
    children: [
      {
        path: '/sports',
        component: allSport,
        name: 'All Sports',
        meta: { title: 'All Sports', noCache: false },
      },
      {
        path: '/sports/add',
        component: addSport,
        name: 'Add Sports',
        meta: { title: 'Add Sport', noCache: false },
      },
      {
        path: '/sports/edit/:id(\\d+)',
        component: editSport,
        name: 'Edit Sport',
        meta: { title: 'Edit Sport', noCache: true },
        hidden: true,
      },
      {
        path: '/sports/teams/:id(\\d+)',
        component: SportsTeams,
        name: 'Sport Teams',
        meta: { title: 'Sport Teams', noCache: true },
        hidden: true,
      },
      {
        path: '/sports/rounds/:id(\\d+)',
        component: SportsRounds,
        name: 'Sport Rounds',
        meta: { title: 'Sport Rounds', noCache: true },
        hidden: true,
      },
    ],
  },
  {
    path: '/teams',
    component: Layout,
    redirect: '/teams',
    alwaysShow: true,
    meta: {
      title: 'Teams',
      icon: 'peoples',
      permissions: ['admin_permission'],

    },
    children: [
      {
        path: '/teams',
        component: allTeam,
        name: 'All Teams',
        meta: { title: 'All Teams', noCache: false },
      },
      {
        path: '/teams/add',
        component: addTeam,
        name: 'Add Teams',
        meta: { title: 'Add Team', noCache: false },
      },
      {
        path: '/teams/add/:id(\\d+)',
        component: addTeam,
        name: 'Add New Teams',
        meta: { title: 'Add Team', noCache: false },
        hidden: true,
      },
      {
        path: '/teams/edit/:id(\\d+)',
        component: editTeam,
        name: 'Edit Team',
        meta: { title: 'Edit Team', noCache: true },
        hidden: true,
      },
    ],
  },
  {
    path: '/rounds',
    component: Layout,
    redirect: '/rounds',
    alwaysShow: false,
    meta: {
      title: 'Rounds',
      icon: 'example',
      permissions: ['admin_permission'],

    },
    children: [
      {
        path: '/rounds',
        component: allRound,
        name: 'All Round',
        meta: { title: 'All Round', noCache: false },
      },
      {
        path: '/rounds/add/:id(\\d+)',
        component: addRound,
        name: 'Add New Round',
        meta: { title: 'Add Round', noCache: false },
        hidden: true,
      },
      {
        path: '/rounds/add',
        component: addRound,
        name: 'Add Round',
        meta: { title: 'Add Round', noCache: false },
      },
      {
        path: '/rounds/edit/:id(\\d+)',
        component: editRound,
        name: 'Edit Round',
        meta: { title: 'Edit Round', noCache: true },
        hidden: true,
      },
      {
        path: '/rounds/fixtures/:id(\\d+)',
        component: RoundsFixtures,
        name: 'Round Fixtures',
        meta: { title: 'Round Fixtures', noCache: true },
        hidden: true,
      },
    ],
  },
  {
    path: '/fixtures',
    component: Layout,
    redirect: '/fixtures',
    alwaysShow: true,
    meta: {
      title: 'Fixtures',
      icon: 'list',
      permissions: ['admin_permission'],

    },
    children: [
      {
        path: '/fixtures',
        component: allFixtures,
        name: 'All Fixtures',
        meta: { title: 'All Fixtures', noCache: false },
      },
      {
        path: '/fixtures/add',
        component: addFixtures,
        name: 'Add Fixtures',
        meta: { title: 'Add Fixture', noCache: false },
      },
      {
        path: '/fixtures/add/:sid(\\d+)/:rid(\\d+)',
        component: addFixtures,
        name: 'Add New Fixtures',
        meta: { title: 'Add New Fixture', noCache: false },
        hidden: true,

      },
      {
        path: '/fixtures/edit/:id(\\d+)',
        component: editFixtures,
        name: 'Edit Fixtures',
        meta: { title: 'Edit Fixture', noCache: true },
        hidden: true,
      },
      {
        path: '/rounds/fixtures/:sid(\\d+)/:rid(\\d+)',
        component: RoundsFixtures,
        name: 'Round Fixtures',
        meta: { title: 'Round Fixtures', noCache: true },
        hidden: true,
      },
    ],
  },
  {
    path: '/League Teams',
    component: Layout,
    redirect: '/leagueteams',
    alwaysShow: true,
    meta: {
      title: 'League Teams',

      icon: 'peoples',

    },
    children: [
      {
        path: '/leagueteams',
        component: allLeagueTeam,
        name: 'League Teams',
        meta: { title: 'League Teams', noCache: false },
      },
      {
        path: '/manageteams/:id(\\d+)',
        component: ManageTeams,
        hidden: true,
      },
    ],
  },
  {
    path: '/Assign Teams',
    component: Layout,
    redirect: '/assignteams',
    alwaysShow: true,
    meta: {
      title: 'Assign Teams',

      icon: 'peoples',

    },
    children: [
      {
        path: '/assignteams',
        component: AssignTeams,
        name: 'Assign Teams',
        meta: { title: 'Assign Teams', noCache: false },
      },
    ],
  },
  {
    path: '/users',
    component: Layout,
    alwaysShow: true,
    hidden: false,
    meta: {
      title: 'Users',
      icon: 'user',
      permissions: ['admin_permission'],
    },
    children: [
      {
        path: '/users',
        component: viewUsers,
        name: 'All Users',
        alwaysShow: true,
        meta: { title: 'All Users', noCache: false },

      },
      {
        path: '/users/addnew',
        component: addNewUser,
        name: 'Add New',
        alwaysShow: true,
        meta: { title: 'Add New', noCache: false },

      },
    ],
  },
  {
    path: '/result',
    component: Layout,
    redirect: '/result/view',
    alwaysShow: true,
    meta: {
      title: 'Result',
      icon: 'international',
      permissions: ['admin_permission'],
    },
    children: [
      {
        path: '/result/view',
        component: resultIndex,
        name: 'View Result',
        meta: { title: 'View Result', noCache: false },
      },
      {
        path: '/result/round/:id(\\d+)/:s_id(\\d+)',
        component: resultfixture,
        name: 'Result Detail',
        meta: { title: 'Result Detail', noCache: true },
        hidden: true,
      },
    ],
  },

  {
    path: '/notofication-template',
    component: Layout,
    redirect: '/notofication-template',
    alwaysShow: true,
    meta: {
      title: 'Notification Templates',
      icon: 'notification',

    },
    children: [
      {
        path: '/notofication-template',
        component: allTemplates,
        name: 'All Templates',
        meta: { title: 'All Templates', noCache: false },
      },
      {
        path: '/notofication-template/edit/:id(\\d+)',
        component: allTemplates,
        name: 'Edit Template',
        meta: { title: 'Edit Template', noCache: true },
        hidden: true,
      },

    ],
  },
  {
    path: '/system-logs',
    component: Layout,
    redirect: '/system-logs',
    alwaysShow: true,
    meta: {
      title: 'System Logs',
      icon: 'list',
    },
    children: [
      {
        path: '/system-logs',
        component: systemLogs,
        name: 'All Logs',
        meta: { title: 'All Logs', noCache: false },
      },
    ],
  },

  { path: '*', redirect: '/404', hidden: true },
];

export const constantRoutes = [
  {
    path: '/redirect',
    component: Layout,
    hidden: true,
    children: [
      {
        path: '/redirect/:path*',
        component: () => import('@/views/redirect/index'),
      },
    ],
  },
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true,
  },
  {
    path: '/welcome',
    component: () => import('@/views/dashboard/user/mobile'),
    hidden: true,
  },
  {
    path: '/auth/:provider/callback ',
    template: '<div class="auth-component"></div>',
    hidden: true,
  },
  {
    path: '/register',
    component: () => import('@/views/register/index'),
    hidden: true,
  },
  {
    path: '/socialregister',
    component: () => import('@/views/register/social'),
    hidden: true,
  },
  {
    path: '/password/reset',
    component: () => import('@/views/password/index'),
    hidden: true,
  },
  {
    path: '/password/forgot',
    component: () => import('@/views/password/create'),
    hidden: true,
  },
  {
    path: '/auth-redirect',
    component: () => import('@/views/login/AuthRedirect'),
    hidden: true,
  },
  {
    path: '/404',
    redirect: { name: 'Page404' },
    component: () => import('@/views/error-page/404'),
    hidden: true,
  },
  {
    path: '/401',
    component: () => import('@/views/error-page/401'),
    hidden: true,
  },
  {
    path: '',
    component: Layout,
    redirect: 'dashboard',
    children: [
      {
        path: 'dashboard',
        component: () => import('@/views/dashboard/index'),
        name: 'Dashboard',
        meta: { title: 'dashboard', icon: 'dashboard', noCache: false },
      },

    ],
  },
  {
    path: '/account',
    component: Layout,
    hidden: true,
    redirect: '/account/settings',
    children: [
      {
        path: 'settings',
        component: () => import('@/views/users/SelfProfile'),
        name: 'SelfProfile2',
        meta: { title: 'userProfile', icon: 'user', noCache: true },
      },
    ],
  },
  // {
  //   path: '/leagues',
  //   component: Layout,
  //   redirect: '/leagues',
  //   hidden: true,
  //   meta: {
  //     title: 'Leagues',
  //     icon: 'sport',

  //   },
  //   children: [
  //     {
  //       path: '/leagues',
  //       component: allLeagues,
  //       name: 'All leagues',
  //       meta: { title: 'All Leagues', noCache: false },
  //     },
  //     {
  //       path: '/leagues/add',
  //       component: addLeagues,
  //       name: 'Add Leageus',
  //       meta: { title: 'Add League', noCache: false },
  //     },
  //     {
  //       path: '/leagues/edit/:id(\\d+)',
  //       component: editLeagues,
  //       name: 'Edit leagues',
  //       meta: { title: 'Edit League', noCache: true },
  //       hidden: true,
  //     },
  //     {
  //       path: '/league/:id(\\d+)',
  //       component: viewLeague,
  //       name: 'View League',
  //       meta: { title: 'View League', noCache: false },
  //       hidden: true,
  //     },
  //   ],
  // },
  {
    path: '/home',
    component: Layout,
    redirect: '/home',
    alwaysShow: true,
    hidden: true,
    children: [
      {
        path: '/home',
        component: lmsHomePage,
        name: 'LMS Home',
        meta: { title: 'LMS Home', noCache: false, permissions: ['user_permission'] },
      },
    ],
  },
  {
    path: '/leauge/create',
    component: Layout,
    redirect: 'leauge/create',
    alwaysShow: true,
    hidden: true,
    children: [
      {
        path: '/league/home',
        component: leaguecreatehome,
        name: 'Create League',
        meta: { title: 'Create League', noCache: false, permissions: ['user_permission'] },
      },
      {
        path: '/league/create/:type',
        component: createLeage,
        name: 'League Create',
        meta: { title: 'League', icon: 'dashboard', noCache: true, permissions: ['user_permission'] },
      },
      {
        path: '/league/list',
        component: leageList,
        hidden: true,
        meta: { title: 'League List', icon: 'dashboard', noCache: true, permissions: ['user_permission'] },
      },
      {
        path: '/league/update/:id(\\d+)',
        component: leageEdit,
        hidden: true,
        meta: { title: 'Update League', icon: 'dashboard', noCache: true, permissions: ['user_permission'] },
      },
      {
        path: '/league/myleagues',
        component: myLeagues,
        name: 'My Leagues',
        meta: { title: 'My Leagues', icon: 'dashboard', noCache: true, permissions: ['user_permission'] },
      },
      {
        path: '/league/joinedleagues',
        component: myOtherLeagues,
        name: 'My Other Leagues',
        meta: { title: 'My Other Leagues', icon: 'dashboard', noCache: true, permissions: ['user_permission'] },
      },
      {
        path: '/league/user-league-detail/:id(\\d+)',
        component: userLeagueDetail,
        name: 'User League Detail',
        meta: { title: 'User League Detail', icon: 'dashboard', noCache: true, permissions: ['user_permission'] },
      },
      {
        path: '/league/find',
        component: searchLeague,
        name: 'Find Leagues',
        meta: { title: 'Find Leagues', icon: 'dashboard', noCache: true, permissions: ['user_permission'] },
      },
      {
        path: '/league/addteam/:id(\\d+)',
        component: leaugeAddTeam,
        name: 'User League Add Team',
        meta: { title: 'User League Add Team', icon: 'dashboard', noCache: true, permissions: ['user_permission'] },
      },
      {
        path: '/league/:id(\\d+)',
        component: viewLeague,
        name: 'View League',
        meta: { title: 'View League', noCache: false, permissions: ['user_permission'] },
        hidden: true,
      },
    ],
  },
];

const createRouter = () => new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({ y: 0 }),
  base: process.env.MIX_LARAVUE_PATH,
  routes: constantRoutes,
});

const router = createRouter();

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter();
  router.matcher = newRouter.matcher; // reset router
}

export default router;
