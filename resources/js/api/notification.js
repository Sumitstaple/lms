import request from '@/utils/request';

export function fetchNotifications(query) {
  return request({
    url: '/notifications',
    method: 'get',
    params: query,
  });
}

export function markasread(query) {
  return request({
    url: '/markasread',
    method: 'get',
    params: query,
  });
}

export function getunread(query) {
  return request({
    url: '/getunread',
    method: 'get',
    params: query,
  });
}
