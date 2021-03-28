import request from '@/utils/request';

export function fetchSystemLogsList(query) {
  return request({
    url: '/systemlogs',
    method: 'get',
    params: query,
  });
}
