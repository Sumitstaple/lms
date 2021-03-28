import createLeage from './../components/leagues/createLeage.vue';

/* Layout */
import Layout from '@/layout';

/* Router for modules */
// import permissionRoutes from './modules/permission';

/**
 * Sub-menu only appear when children.length>=1
 * @see https://doc.laravue.dev/guide/essentials/router-and-nav.html
 **/

/**
* hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
* alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
*                                if not set alwaysShow, only more than one route under the children
*                                it will becomes nested mode, otherwise not show the root menu
* redirect: noredirect           if `redirect:noredirect` will no redirect in the breadcrumb
* name:'router-name'             the name is used by <keep-alive> (must set!!!)
* meta : {
    roles: ['admin', 'editor']   Visible for these roles only
    permissions: ['view menu zip', 'manage user'] Visible for these permissions only
    title: 'title'               the name show in sub-menu and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar
    noCache: true                if true, the page will no be cached(default is false)
    breadcrumb: false            if false, the item will hidden in breadcrumb (default is true)
    affix: true                  if true, the tag will affix in the tags-view
  }
**/

import Layout from '@/layout';

const frontendRouter = [
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
  {
    path: '',
    component: Layout,
    redirect: 'leauge/create',
    hidden: true,
    children: [
      {
        path: '/leauge/create',
        component: createLeage,
        name: 'League Create',
        meta: { title: 'League', icon: 'dashboard', noCache: true },
      },
    ],
  },
];

export default frontendRouter;