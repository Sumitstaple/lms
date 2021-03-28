import request from '@/utils/request';
import Resource from '@/api/resource';

class UserResource extends Resource {
  constructor() {
    super('users');
  }

  permissions(id) {
    return request({
      url: '/' + this.uri + '/' + id + '/permissions',
      method: 'get',
    });
  }

  updatePermission(id, permissions) {
    return request({
      url: '/' + this.uri + '/' + id + '/permissions',
      method: 'put',
      data: permissions,
    });
  }
}

export function fetchUserList(query) {
  return request({
    url: '/allusers',
    method: 'get',
    params: query,
  });
}
export function signupuser(query) {
  return request({
    url: '/users/signupuser',
    method: 'post',
    data: query,
  });
}
export function MakeUserAdmin(query) {
  return request({
    url: '/users/makeadmin',
    method: 'post',
    data: query,
  });
}

export { UserResource as default };
