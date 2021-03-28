import { login, logout, getInfo, signup, passwordReset, googlelogin, socialsignup, signupuser, checklogin, facebooklogin } from '@/api/auth';
import { getToken, setToken, removeToken } from '@/utils/auth';
import router, { resetRouter } from '@/router';
import store from '@/store';
import Cookies from 'js-cookie';

const state = {
  id: null,
  token: getToken(),
  name: '',
  avatar: '',
  introduction: '',
  roles: [],
  permissions: [],
  leagueId: '',
  siteview: '',
  nickname: '',
};

const mutations = {
  SET_ID: (state, id) => {
    state.id = id;
  },
  SET_TOKEN: (state, token) => {
    state.token = token;
  },
  SET_INTRODUCTION: (state, introduction) => {
    state.introduction = introduction;
  },
  SET_NAME: (state, name) => {
    state.name = name;
  },
  SET_NICKNAME: (state, nickname) => {
    state.nickname = nickname;
  },
  SET_AVATAR: (state, avatar) => {
    state.avatar = avatar;
  },
  SET_ROLES: (state, roles) => {
    state.roles = roles;
  },
  SET_PERMISSIONS: (state, permissions) => {
    state.permissions = permissions;
  },
  SET_LEAUGEID: (state, leagueId) => {
    state.leagueId = leagueId;
  },

  SET_SITEVIEW: (state, siteview) => {
    state.siteview = siteview;
  },
};

const actions = {
  // user login
  login({ commit }, userInfo) {
    const { email, password } = userInfo;
    return new Promise((resolve, reject) => {
      login({ email: email.trim(), password: password, userInfo })
        .then(response => {
          Cookies.set('user_key', 2);
          commit('SET_TOKEN', response.token);
          commit('SET_SITEVIEW', 'front');
          setToken(response.token);
          resolve();
        })
        .catch(error => {
          reject(error);
        });
    });
  },

  googlelogin({ commit }, userInfo) {
    return new Promise((resolve, reject) => {
      googlelogin(userInfo)
        .then(response => {
          if (response.status === 'signup'){
            resolve(response);
          } else {
            commit('SET_TOKEN', response.token);
            commit('SET_SITEVIEW', 'front');
            setToken(response.token);
            resolve(response);
          }
        });
      // .catch(error => {
      //   reject(error);
      // });
    });
  },

  // Customer register
  signup({ commit }, userInfo) {
    const { email, password } = userInfo;
    return new Promise((resolve, reject) => {
      signup(userInfo)
        .then(response => {
          Cookies.set('user_key', 2);
          commit('SET_TOKEN', response.token);
          commit('SET_SITEVIEW', 'front');
          setToken(response.token);
          resolve();
        })
        .catch(error => {
          reject(error);
        });
    });
  },

  // Customer register
  signupuser({ commit }, userInfo) {
    const { email, password } = userInfo;
    return new Promise((resolve, reject) => {
      signupuser(userInfo)
        .then(response => {
          commit('SET_TOKEN', response.token);
          commit('SET_SITEVIEW', 'front');
          setToken(response.token);
          resolve();
        })
        .catch(error => {
          reject(error);
        });
    });
  },

  socialsignup({ commit }, userInfo) {
    return new Promise((resolve, reject) => {
      socialsignup(userInfo)
        .then(response => {
          commit('SET_TOKEN', response.token);
          commit('SET_SITEVIEW', 'front');
          setToken(response.token);
          resolve();
        })
        .catch(error => {
          reject(error);
        });
    });
  },

  // get user info
  getInfo({ commit, state }) {
    return new Promise((resolve, reject) => {
      getInfo(state.token)
        .then(response => {
          const { data } = response;

          if (!data) {
            reject('Verification failed, please Login again.');
          }

          const { roles, name, nickname, avatar, introduction, permissions, id, league_id } = data;
          // roles must be a non-empty array
          if (!roles || roles.length <= 0) {
            reject('getInfo: roles must be a non-null array!');
          }
          // alert(permissions);
          commit('SET_ROLES', roles);
          commit('SET_PERMISSIONS', permissions);
          commit('SET_NAME', name);
          commit('SET_NICKNAME', nickname);
          commit('SET_AVATAR', avatar);
          commit('SET_INTRODUCTION', introduction);
          commit('SET_ID', id);
          commit('SET_LEAUGEID', league_id);

          if (Cookies.get('user_key') === 2 || Cookies.get('user_key') === '2') {
            if (roles == 'admin') {
              Cookies.set('user_key', 1);
              commit('SET_SITEVIEW', 'admin');
            } else {
              Cookies.set('user_key', 0);
              commit('SET_SITEVIEW', 'front');
            }
          } else if (Cookies.get('user_key') === 1 || Cookies.get('user_key') === '1') {
            commit('SET_SITEVIEW', 'admin');
          } else {
            commit('SET_SITEVIEW', 'front');
          }
          resolve(data);
        })
        .catch(error => {
          reject(error);
        });
    });
  },

  // user logout
  logout({ commit, state }) {
    return new Promise((resolve, reject) => {
      logout(state.token)
        .then(() => {
          commit('SET_NICKNAME', '');
          Cookies.set('user_key', 0);
          commit('SET_TOKEN', '');
          commit('SET_ROLES', []);
          commit('SET_SITEVIEW', 'front');
          removeToken();
          resetRouter();
          resolve();
        })
        .catch(error => {
          reject(error);
        });
    });
  },

  // remove token
  resetToken({ commit }) {
    return new Promise(resolve => {
      commit('SET_TOKEN', '');
      commit('SET_ROLES', []);
      removeToken();
      resolve();
    });
  },

  // Dynamically modify permissions
  changeRoles({ commit, dispatch }, role) {
    return new Promise(async resolve => {
      // const token = role + '-token';

      // commit('SET_TOKEN', token);
      // setToken(token);

      // const { roles } = await dispatch('getInfo');

      const roles = [role.name];
      const permissions = role.permissions.map(permission => permission.name);
      commit('SET_ROLES', roles);
      commit('SET_PERMISSIONS', permissions);
      resetRouter();

      // generate accessible routes map based on roles
      const accessRoutes = await store.dispatch('permission/generateRoutes', { roles, permissions });

      // dynamically add accessible routes
      router.addRoutes(accessRoutes);

      resolve();
    });
  },

  // passwordReset
  passwordReset({ commit }, userInfo) {
    const { email } = userInfo;
    return new Promise((resolve, reject) => {
      passwordReset(userInfo)
        .then(response => {
          resolve(response);
        })
        .catch(error => {
          reject(error);
        });
    });
  },

  // user logout
  setview({ commit, state }, info) {
    return new Promise(resolve => {
      commit('SET_SITEVIEW', info);
    });
  },
  checklogin({ commit }, userInfo) {
    return new Promise((resolve, reject) => {
      checklogin(userInfo)
        .then(response => {
          commit('SET_TOKEN', response.token);
          commit('SET_SITEVIEW', 'front');
          setToken(response.token);
          resolve();
        })
        .catch(error => {
          reject(error);
        });
    });
  },

  facebooklogin({ commit }, userInfo) {
    return new Promise((resolve, reject) => {
      facebooklogin(userInfo)
        .then(response => {
          commit('SET_TOKEN', response.token);
          commit('SET_SITEVIEW', 'front');
          setToken(response.token);
          resolve();
        })
        .catch(error => {
          reject(error);
        });
    });
  },
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
};
