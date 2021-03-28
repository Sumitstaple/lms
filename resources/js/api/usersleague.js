import request from '@/utils/request';

export function fetchLeagueList(query) {
  return request({
    url: '/joinedleagues',
    method: 'get',
    params: query,
  });
}

export function fetchLeagueDetail(query) {
  return request({
    url: '/userleaguedetail/' + query,
    method: 'get',
    params: query,
  });
}
export function fetchUserLeagueDetail(user, id) {
  return request({
    url: '/currentuserleague/' + user + '/' + id,
    method: 'get',
  });
}
export function fetchMyLeagueList(query) {
  return request({
    url: '/myleagues',
    method: 'get',
    params: query,
  });
}

export function findLeague(query) {
  return request({
    url: '/leauge/find',
    method: 'get',
    params: query,
  });
}

export function MakeAdminOfLeague(league_id, user_id, status) {
  return request({
    url: '/makeadmin/' + league_id + '/' + user_id + '/' + status,
    method: 'get',
  });
}

export function acceptUserRequest(id, user_id, status) {
  return request({
    url: '/league/join/request/accept/' + id + '/' + user_id + '/' + status,
    method: 'get',
  });
}
