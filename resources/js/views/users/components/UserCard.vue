<template>
  <el-card v-if="user.name">
    <div class="user-profile">

      <div v-if="user.avatar != 'user.png'" class="user-avatar box-center">
        <pan-thumb v-if="user.avatar != 'user.png'" :image="iconPath+user.avatar" :height="'100px'" :width="'100px'" :hoverable="false" />
      </div>

      <div v-else class="user-avatar-name box-center">
        <span class="nameavatar1">{{ user.name.charAt(0) }}</span>
      </div>
      <div class="box-center">
        <div class="user-name text-center">
          {{ user.name }}
        </div>
        <div class="user-role text-center text-muted">
          {{ user.role.toUpperCase() }}
        </div>
      </div>
      <div class="box-social">
        <div class="user-role text-center">
          {{ user.email }}
        </div>
      </div>
    </div>
  </el-card>
</template>

<script>
import PanThumb from '@/components/PanThumb';

export default {
  components: { PanThumb },
  props: {
    user: {
      type: Object,
      default: () => {
        return {
          name: '',
          email: '',
          avatar: '',
          roles: [],
        };
      },
    },
  },
  data() {
    return {
      iconPath: '/uploads/profile/',
      social: [
        {
          'name': 'Followers',
          'count': 1235,
        },
        {
          'name': 'Following',
          'count': 23512,
        },
        {
          'name': 'Friends',
          'count': 7242,
        },
      ],
    };
  },
  methods: {
    getRole() {
      const roles = this.user.roles.map(value => this.$options.filters.uppercaseFirst(value));
      return roles.join(' | ');
    },
  },
};
</script>

<style lang="scss" scoped>
.user-profile {
  .user-name {
    font-weight: bold;
  }
  .box-center {
    padding-top: 10px;
  }
  .user-role {
    padding-top: 10px;
    font-weight: 400;
    font-size: 14px;
  }
  .box-social {
    padding-top: 50px;
    .el-table {
      border-top: 1px solid #dfe6ec;
    }
  }
  .user-follow {
    padding-top: 20px;
  }
}
span.nameavatar1 {
height: 40px;
width: 40px;
text-align: center;
color: #FFFFFF;
border-radius: 50%;
line-height: 37px;
text-transform: uppercase;
}

.user-avatar-name {
    height: 60px;
    width: 60px;
    text-align: center;
    background: #282855;
    border-radius: 50%;
    line-height: 37px;
    text-transform:uppercase;
}
</style>
