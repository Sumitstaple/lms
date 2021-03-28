import request from '@/utils/request';

export function fetchLeagueList(query) {
  return request({
    url: '/league',
    method: 'get',
    params: query,
  });
}

export function fetchLeague(id) {
  return request({
    url: '/league/' + id,
    method: 'get',
  });
}

export function viewLeague(id) {
  return request({
    url: '/league/view/' + id,
    method: 'get',
  });
}

export function leagueinfo(id) {
  return request({
    url: 'league/leagueinfo/' + id,
    method: 'get',
  });
}

export function getLeagueUser(id) {
  return request({
    url: '/league/get/user/' + id,
    method: 'get',
  });
}

export function createLeagues(query) {
  return request({
    url: '/league',
    method: 'post',
    params: query,
  });
}

export function updateLeagueStatus(id) {
  return request({
    url: '/updateLeagueStatus/' + id,
    method: 'get',
  });
}

export function sendInvitation(query) {
  return request({
    url: '/sendleagueinvite',
    method: 'post',
    params: query,
  });
}

export function getLeagueRounds(id) {
  return request({
    url: '/getleagueround/' + id,
    method: 'get',
  });
}

export function getTeamListByRound(r_id, s_id, l_id) {
  return request({
    url: '/getleagueroundteam/' + r_id + '/' + s_id + '/' + l_id,
    method: 'get',
  });
}

export function saveTeamSelection(r_id, l_id, t_id) {
  return request({
    url: '/saveteamselection/' + r_id + '/' + l_id + '/' + t_id,
    method: 'get',
  });
}

export function getLeagueComments(league_id, round_id) {
  return request({
    url: '/league/get/comment/' + league_id + '/' + round_id,
    method: 'get',
  });
}
export function manageteamlist(league_id) {
  return request({
    url: '/manageteamlist/' + league_id,
    method: 'get',
  });
}

export function saveteamlist(query) {
  return request({
    url: '/saveteamlist',
    method: 'post',
    params: query,
  });
}
