import request from '@/utils/request';

export function fetchRoundList(query) {
  return request({
    url: '/round',
    method: 'get',
    params: query,
  });
}

export function fetchTeamList(query) {
  return request({
    url: '/team',
    method: 'get',
    params: query,
  });
}

export function fetchSport(id) {
  return request({
    url: '/sport/' + id,
    method: 'get',
  });
}

export function fetchRound(id) {
  return request({
    url: '/round/' + id,
    method: 'get',
  });
}

export function fetchroundsbysport(id) {
  return request({
    url: '/fetchroundsbysport/' + id,
    method: 'get',
  });
}
export function fetchroundsbysportleague(id) {
  return request({
    url: '/fetchroundsbysportleague/' + id,
    method: 'get',
  });
}

export function fetchRoundListBySport(id) {
  return request({
    url: '/sportsrounds/' +id,
    method: 'get',
  });
}
