import request from '@/utils/request';

export function getRounds(query) {
  return request({
    url: '/getrounds',
    method: 'get',
    params: query,
  });
}
export function fetchFixture(id, s_id) {
  return request({
    url: '/getfixturebyround/' + id + '/' + s_id,
    method: 'get',
  });
}
export function makeAbondon(id) {
  return request({
    url: '/setasabondon/' + id,
    method: 'get',
  });
}

export function saveResultStats(query) {
  return request({
    url: '/saveresultstats',
    method: 'post',
    data: query,
  });
}
export function undoResult(query) {
  return request({
    url: '/undoresultround',
    method: 'post',
    data: query,
  });
}

export function getLeagueUser(query) {
  return request({
    url: '/getleagueuser',
    method: 'post',
    data: query,
  });
}
