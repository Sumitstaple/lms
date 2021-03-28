import request from '@/utils/request';

export function fetchNotificationTemplateList(query) {
  return request({
    url: '/notificationtemplates',
    method: 'get',
    params: query,
  });
}
