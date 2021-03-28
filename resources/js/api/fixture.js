import request from '@/utils/request';

export function fetchFixtureList(query) {
  return request({
    url: '/fixture',
    method: 'get',
    params: query,
  });
}
export function fetchFixture(id) {
  return request({
    url: '/fixture/' + id,
    method: 'get',
  });
}

export function createfixtures(query) {
  return request({
    url: '/fixture',
    method: 'post',
    params: query,
  });
}

export function updateFixtureStatus(id) {
  return request({
    url: '/updateFixtureStatus/' + id,
    method: 'get',
  });
}

export function fetchSportList(query) {
  return request({
    url: '/sportsforfixture',
    method: 'get',
    params: query,
  });
}

export function fetchTeamList(id) {
  return request({
    url: '/teamsforfixture/' + id,
    method: 'get',
  });
}

export function fetchRoundList(id) {
  return request({
    url: '/roundsforfixture',
    method: 'get',
  });
}

export function fetchFixtureListBySportAndRound(sid,rid) {
  return request({
    url: '/fixturebysportandround/'+sid+'/'+rid,
    method: 'get',
  });
}
