import request from '@/utils/request';

export function login(data) {
  return request({
    url: '/auth/login',
    method: 'post',
    data: data,
  });
}

export function signup(data) {
  return request({
    url: '/auth/register',
    method: 'post',
    data: data,
  });
}
export function signupuser(data) {
  return request({
    url: '/auth/signupuser',
    method: 'post',
    data: data,
  });
}

export function getInfo(token) {
  return request({
    url: '/auth/user',
    method: 'get',
  });
}

export function logout() {
  return request({
    url: '/auth/logout',
    method: 'post',
  });
}

export function passwordReset(data) {
  return request({
    url: '/auth/password/reset',
    method: 'post',
    data: data,
  });
}

export function googlelogin(data) {
  return request({
    url: '/sociallogin/google',
    method: 'post',
    data: data,
  });
}

export function facebooklogin(data) {
  return request({
    url: '/sociallogin/facebook',
    method: 'post',
    data: data,
  });
}

export function socialsignup(data) {
  return request({
    url: '/socialsignup',
    method: 'post',
    data: data,
  });
}

export function checklogin(data) {
  return request({
    url: '/checklogin',
    method: 'post',
    data: data,
  });
}
