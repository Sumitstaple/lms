<template>
  <div v-if="showComment" class="col-sm-6 comment-new">
    <div class="user-activity">
      <el-form ref="postForm" :model="postForm" :rules="commentRules" label-width="120px">
      <h1 style="color : #ffffff; text-align:center; border:1px;">Banter Board</h1>
      <div class="main-div">
      <div class="post comt-part" v-for="element in commentList" style="align-items: center;">
      <div class="user-block">
        <img v-if="element.profile_pic != 'user.png'" class="img-circle" :src="'/uploads/profile/'+element.profile_pic" alt="user image">
        <!-- <img v-if="element.profile_pic == 'user.png'" class="img-circle" :src="'/'+element.profile_pic" alt="user image"> -->
        <span v-if="element.profile_pic == 'user.png'" class="img-circle-name">{{ element.user.charAt(0) }}</span>
        <span class="username text-muted">
          <a >{{element.user}} <span class="dot_nm">{{element.time}}</span></a>
        </span>
        <span class="description">{{element.comment}}</span>
        <span class="description">
          <a href="javascript:void(0);" style="color:blue;" @click="element.reply=!element.reply">Reply</a>
          <a href="javascript:void(0);" @click="element.edit=!element.edit"><svg-icon icon-class="edit" /></a> 
          <a href="javascript:void(0);" @click="onDelete(element.id)"><i class="el-icon-delete" /></a>
        </span>
      </div>
      <div v-if="element.edit">
        <Mentionable :keys="['@']" :items="items" offset="6" class="botm_cmnt cmt_update" insert-space @open="onOpen" >
         
          <textarea style="width:70%; padding:8px 8px 8px 8px; height: 35px" v-model="postForm.pre_comment[element.id]" type="textarea" placeholder="Type a comment">
          </textarea>
          <img v-if="avatar != 'user.png'" class="cmmt_img" :src="'/uploads/profile/'+avatar" alt="user image">
          <!-- <img v-if="avatar == 'user.png'" class="cmmt_img" :src="'/'+avatar" alt="user image"> -->
          <span v-if="avatar == 'user.png'" class="cmmt_img cmmt_img-name">{{ avname }}</span>
          <el-button v-loading="loading" style="height: 25px" type="primary" @click.native.prevent="onUpdate(element.id)">
            Update
          </el-button>
          <template #no-result>
      <div class="dim">
        No result
      </div>
    </template>

    <template #item-@="{ item }">
      <div class="user" @mousedown="onKeyDown(item.id,'@'+item.value)" :data-id="item.id" onclick="alert()">
        {{ item.value }}
      </div>
    </template>
        </Mentionable>
      </div>
      <Mentionable v-if="element.reply"
        :keys="['@']"
        :items="items"
        offset="3"
        insert-space
        @open="onOpen"
        class="botm_cmnt cmt_update"
        >
        <textarea style="width:70%; padding:8px 8px 8px 8px; height: 35px" v-model="postForm.comment[element.id]" name="leage_name" type="textarea" placeholder="Write a reply...">
        </textarea>
        <img v-if="avatar != 'user.png'" class="cmmt_img" :src="'/uploads/profile/'+avatar" alt="user image">
        <!-- <img v-if="avatar == 'user.png'" class="cmmt_img" :src="'/'+avatar" alt="user image"> -->
        <span v-if="avatar == 'user.png'" class="cmmt_img cmmt_img-name">{{ avname }}</span>
        <el-button v-loading="loading" style="height: 25px"  type="primary" @click.native.prevent="onSubmit(element.id,0,0)">
          Reply
        </el-button>
        <template #no-result>
      <div class="dim">
        No result
      </div>
    </template>

    <template #item-@="{ item }">
      <div class="user" @mousedown="onKeyDown(item.id,'@'+item.value)" :data-id="item.id" onclick="alert()">
        {{ item.value }}
      </div>
    </template>
      </Mentionable>
      <div style="margin-top: 12px;" v-for="sub in element.other">
        <div style="margin-left: 30px;" class="user-block">
          <img v-if="sub.profile_pic != 'user.png'" class="img-circle" :src="'/uploads/profile/'+sub.profile_pic" alt="user image">
         <!--  <img v-if="sub.profile_pic == 'user.png'" class="img-circle" :src="'/'+sub.profile_pic" alt="user image"> -->
         <span v-if="sub.profile_pic == 'user.png'" class="img-circle-name">{{ sub.user.charAt(0) }}</span>
          <span class="username text-muted">
            <a >{{sub.user}} . {{sub.time}}</a>
          </span>
          <span class="description">{{sub.comment}}</span>
          <span class="description">
            <a href="javascript:void(0);" style="color:blue;" @click="sub.reply=!sub.reply">Reply</a>
            <a href="javascript:void(0);" @click="sub.edit=!sub.edit"><svg-icon icon-class="edit" /></a>
            <a href="javascript:void(0);" @click="onDelete(sub.id)"><i class="el-icon-delete" /></a>
          </span>
        </div>
        <div v-if="sub.edit">
        <Mentionable :keys="['@']" :items="items" offset="6" class="botm_cmnt cmt_update" insert-space @open="onOpen" >
          <textarea style="width:70%; padding:8px 8px 8px 8px; height: 35px" v-model="postForm.pre_comment[sub.id]" type="textarea" placeholder="Type a comment">
          </textarea>
          <img v-if="avatar != 'user.png'" class="cmmt_img" :src="'/uploads/profile/'+avatar" alt="user image">
          <!-- <img v-if="avatar == 'user.png'" class="cmmt_img" :src="'/'+avatar" alt="user image"> -->
          <span v-if="avatar == 'user.png'" class="cmmt_img cmmt_img-name">{{ avname }}</span>
          <el-button v-loading="loading" style="height: 25px" type="primary" @click.native.prevent="onUpdate(sub.id)">
            Update
          </el-button>
          <template #no-result>
      <div class="dim">
        No result
      </div>
    </template>

    <template #item-@="{ item }">
      <div class="user" @mousedown="onKeyDown(item.id,'@'+item.value)" :data-id="item.id" onclick="alert()">
        {{ item.value }}
      </div>
    </template>
        </Mentionable>
      </div>
        <Mentionable v-if="sub.reply"
            :keys="['@']"
            :items="items"
            offset="3"
            insert-space
            @open="onOpen"
            class="botm_cmnt cmt_update"
            >
            <textarea style="width:70%; padding:8px 8px 8px 8px; height: 35px" v-model="postForm.comment[sub.id]" name="leage_name" type="textarea" placeholder="Write a reply...">
            </textarea>
            <img v-if="avatar != 'user.png'" class="cmmt_img" :src="'/uploads/profile/'+avatar" alt="user image">
            <!-- <img v-if="avatar == 'user.png'" class="cmmt_img" :src="'/'+avatar" alt="user image"> -->
            <span v-if="avatar == 'user.png'" class="cmmt_img cmmt_img-name">{{ avname }}</span>
            <el-button v-loading="loading" style="height: 25px"  type="primary" @click.native.prevent="onSubmit(element.id,sub.id,0)">
              Reply
            </el-button>
            <template #no-result>
      <div class="dim">
        No result
      </div>
    </template>

    <template #item-@="{ item }">
      <div class="user" @mousedown="onKeyDown(item.id,'@'+item.value)" :data-id="item.id" onclick="alert()">
        {{ item.value }}
      </div>
    </template>
        </Mentionable>
      <!--  ======= add 2nd lavel comment =====-->
        <div style="margin-top: 12px;" v-for="rep in sub.replies">
          <div style="margin-left: 70px;" class="user-block">
            <img v-if="rep.profile_pic != 'user.png'" class="img-circle" :src="'/uploads/profile/'+rep.profile_pic" alt="user image">
           <!--  <img v-if="rep.profile_pic == 'user.png'" class="img-circle" :src="'/'+rep.profile_pic" alt="user image"> -->
           <span v-if="rep.profile_pic == 'user.png'" class="img-circle-name">{{ rep.user.charAt(0) }}</span>
            <span class="username text-muted">
              <a >{{rep.user}} . {{rep.time}}</a>
            </span>
            <span class="description">{{rep.comment}}</span>
            <span class="description">
              <a href="javascript:void(0);" style="color:blue;" @click="rep.reply=!rep.reply">Reply</a>
              <a href="javascript:void(0);" @click="rep.edit=!rep.edit"><svg-icon icon-class="edit" /></a>
              <a href="javascript:void(0);" @click="onDelete(rep.id)"><i class="el-icon-delete" /></a>
            </span>
          </div>
          <div v-if="rep.edit">
            <Mentionable :keys="['@']" :items="items" offset="6" class="botm_cmnt cmt_update" insert-space @open="onOpen" >
              <textarea style="width:70%; padding:8px 8px 8px 8px; height: 35px" v-model="postForm.pre_comment[rep.id]" type="textarea" placeholder="Type a comment">
              </textarea>
              <img v-if="avatar != 'user.png'" class="cmmt_img" :src="'/uploads/profile/'+avatar" alt="user image">
              <!-- <img v-if="avatar == 'user.png'" class="cmmt_img" :src="'/'+avatar" alt="user image"> -->
              <span v-if="avatar == 'user.png'" class="cmmt_img cmmt_img-name">{{ avname }}</span>
              <el-button v-loading="loading" style="height: 25px" type="primary" @click.native.prevent="onUpdate(rep.id)">
                Update
              </el-button>
              <template #no-result>
      <div class="dim">
        No result
      </div>
    </template>

    <template #item-@="{ item }">
      <div class="user" @mousedown="onKeyDown(item.id,'@'+item.value)" :data-id="item.id" onclick="alert()">
        {{ item.value }}
      </div>
    </template>
            </Mentionable>
          </div>
          <Mentionable v-if="rep.reply"
            :keys="['@']"
            :items="items"
            offset="3"
            insert-space
            @open="onOpen"
            class="botm_cmnt cmt_update"
            >
            <textarea style="width:70%; padding:8px 8px 8px 8px; height: 35px" v-model="postForm.comment[rep.id]" name="leage_name" type="textarea" placeholder="Write a reply...">
            </textarea>
            <img v-if="avatar != 'user.png'" class="cmmt_img" :src="'/uploads/profile/'+avatar" alt="user image">
            <!-- <img v-if="avatar == 'user.png'" class="cmmt_img" :src="'/'+avatar" alt="user image"> -->
            <span v-if="avatar == 'user.png'" class="cmmt_img cmmt_img-name">{{ avname }}</span>
            <el-button v-loading="loading" style="height: 25px"  type="primary" @click.native.prevent="onSubmit(element.id,sub.id,rep.id)">
              Reply
            </el-button>
            <template #no-result>
      <div class="dim">
        No result
      </div>
    </template>

    <template #item-@="{ item }">
      <div class="user" @mousedown="onKeyDown(item.id,'@'+item.value)" :data-id="item.id" onclick="alert()">
        {{ item.value }}
      </div>
    </template>
          </Mentionable>
          <!-- ======add 3rd lavel comment ===-->
            <div style="margin-top: 12px;" v-for="re in rep.subReply">
              <div style="margin-left: 120px;" class="user-block">
                <img v-if="re.profile_pic != 'user.png'" class="img-circle" :src="'/uploads/profile/'+re.profile_pic" alt="user image">
                <!-- <img v-if="re.profile_pic == 'user.png'" class="img-circle" :src="'/'+re.profile_pic" alt="user image"> -->
                <span v-if="re.profile_pic == 'user.png'" class="img-circle-name">{{ re.user.charAt(0) }}</span>
                <span class="username text-muted">
                  <a >{{re.user}} . {{re.time}}</a>
                </span>
                <span class="description">{{re.comment}}</span>
                <span class="description">
                  <a href="javascript:void(0);" @click="re.edit=!re.edit"><svg-icon icon-class="edit" /></a>
                  <a href="javascript:void(0);" @click="onDelete(re.id)"><i class="el-icon-delete" /></a>
                </span>
              </div>
              <div v-if="re.edit">
                <Mentionable :keys="['@']" :items="items" class="botm_cmnt cmt_update"  offset="3" insert-space @open="onOpen" >
                  <textarea style="width:70%; padding:8px 8px 8px 8px; height: 35px" v-model="postForm.pre_comment[re.id]" type="textarea" placeholder="Type a comment">
                  </textarea>
                  <img v-if="avatar != 'user.png'" class="cmmt_img" :src="'/uploads/profile/'+avatar" alt="user image">
                  <!-- <img v-if="avatar == 'user.png'" class="cmmt_img" :src="'/'+avatar" alt="user image"> -->
                  <span v-if="avatar == 'user.png'" class="cmmt_img cmmt_img-name">{{ avname }}</span>
                  <el-button v-loading="loading" style="height: 25px" type="primary" @click.native.prevent="onUpdate(re.id)">
                    Update
                  </el-button>
                  <template #no-result>
      <div class="dim">
        No result
      </div>
    </template>
    <!-- ======add onKeyDown events ===-->
    <template #item-@="{ item }">
      <div class="user" @mousedown="onKeyDown(item.id,'@'+item.value)" :data-id="item.id" onclick="alert()">
        {{ item.value }}
      </div>
    </template>
                </Mentionable>
              </div>
            </div>
          <!-- add 3rd lavel comment -->
        </div>
      <!-- add 2nd lavel comment -->
      </div>
    </div>
  </div>
    <div>
    <Mentionable
      :keys="['@']"
      :items="items"
      offset="3"
      insert-space
      @open="onOpen"
      class="botm_cmnt last_cmt"
      >
      <textarea style="width:70%; padding:8px 8px 8px 8px; height: 35px" v-model="postForm.comment[0]" name="leage_name" type="textarea" placeholder="Add a comment.." >

      </textarea>
      <img v-if="avatar != 'user.png'" class="cmmt_img" :src="'/uploads/profile/'+avatar" alt="user image">
      <!-- <img v-if="avatar == 'user.png'" class="cmmt_img" :src="'/'+avatar" alt="user image"> -->
      <span v-if="avatar == 'user.png'" class="cmmt_img cmmt_img-name">{{ avname }}</span>
      <el-button v-loading="loading" type="primary" @click.native.prevent="onSubmit(0,0,0)">
           <img src="images/send-img.png">
      </el-button>
          <template #no-result>
      <div class="dim">
        No result
      </div>
    </template>

    <template #item-@="{ item }">
      <div class="user" @mousedown="onKeyDown(item.id,'@'+item.value)" :data-id="item.id" onclick="alert()">
        {{ item.value }}
      </div>
    </template>
    </Mentionable>
    </div>

  </el-form>
  </div>
  </div>
</template>

<script>
import { getLeagueComments, getLeagueUser } from '@/api/league';
import Resource from '@/api/resource';
import { mapGetters } from 'vuex';
const leagueResource = new Resource('league/add/comment');
const commentUpdate = new Resource('league/update/comment');
const commentDelete = new Resource('league/delete/comment');
const commentReset = new Resource('league/reset/comment');

import { Mentionable } from 'vue-mention';
var $ = require( "jquery" );

export default {
  name: 'LeagueComment',
  components: {Mentionable },
  filters: {
    statusFilter(status) {
      const statusMap = {
        active: 'success',
        request_pending: 'info',
        rejected: 'danger',
      };
      return statusMap[status];
    },
  },
  data() {
    return {
      avname: '',
      commentList: [],
      users: [],
      listLoading: true,
      loading: false,
      postForm: {
        league_id: '1',
        round_id: '1',
        comment: [],
        pre_comment: [],
        comment_id: '',
        sub_parent_id: 0,
        sub_sub_parent_id: 0,
        userids: [],
        username: [],
      },
      items: [],
      commentRules: {
        comment: [{ required: true}],
      },
      showComment:false,
    };
  },
  computed: {
    ...mapGetters([
      'avatar',
      'name',
      'nickname',
    ]),
  },
  created() {
    this.avname = this.name.charAt(0);
    const id = this.$route.params && this.$route.params.id;
    this.postForm.league_id = id;
    this.getList(id);
    this.getUserList(id);
    this.resetCounter(id)
    this.interval = setInterval(() => this.getList(id), 15000);
    this.interval = setInterval(() => this.resetCounter(id), 10000);
  },
  methods: {
    async getList(id) {
      this.listLoading = true;
      const { data } = await getLeagueComments(id,1);
      if(data.status == 'success') {
        this.showComment = true;
      this.commentList = data.items;
      var pre_comment = [];
      var isEdit = [];
      data.items.forEach(function(value,key){
        pre_comment[value.id] = value.comment;
          value.other.forEach(function(val,key){
          pre_comment[val.id] = val.comment;
          val.replies.forEach(function(v,k){
              pre_comment[v.id] = v.comment;
              v.subReply.forEach(function(r,i){
                pre_comment[r.id] = r.comment;
              }); 
          });  
         });
       });
      this.postForm.pre_comment = pre_comment;
      this.postForm.isEdit = isEdit;
      this.listLoading = false;
    } else {
      this.showComment = false;
    }
    },
    async getUserList(id) {
      this.listLoading = true;
      const { data } = await getLeagueUser(id);
      var l = [];
      data.items.forEach(function(value,key){
        //var a = Vue.compile('<div><span>{{ value.user_id }}</span></div>')
        if(value.user.nickname){
          var u = {"value": value.user.nickname, "id" : value.user_id};
        l.push(u);
        }else{
          var u = {"value": value.user.first_name+' '+value.user.last_name, "id" : value.user_id};
        l.push(u);
        }
        

       });
      this.users = l;
      this.listLoading = false;3

      
    },
    onOpen (key) {
      this.items = key === '@' ? this.users : issues
      console.log(key);
    },
    onKeyDown (id,name) {
      this.postForm.userids.push(id);
      this.postForm.username.push(name);
    },
     onSubmit(id,subId,subSubId) {
      this.$refs.postForm.validate(valid => {
        if (valid) {
          this.postForm.comment_id = id;
          this.postForm.sub_parent_id = subId;
          this.postForm.sub_sub_parent_id = subSubId;
          this.loading = true;
          leagueResource
            .store(this.postForm)
              .then(response => {
                this.postForm.userids = [];
                this.postForm.username = [];
                this.getList(this.postForm.league_id);
                if(subId > 0 && subSubId == 0) {
                  this.postForm.comment[subId] = '';
                } else if (subId > 0 && subSubId > 0){
                  this.postForm.comment[subSubId] = '';
                } else {
                  this.postForm.comment[subId] = '';
                }
          });
          this.loading = false;
        } else {
          return false;
        }
      });
    },
    onUpdate(id) {
          this.postForm.comment_id = id;
          this.loading = true;
          commentUpdate
            .store(this.postForm)
              .then(response => {
                // this.$message({
                //   message: 'Comment update successfully',
                //   type: 'success',
                //   duration: 5 * 1000,
                // });
                this.getList(this.postForm.league_id);
          });
          this.loading = false;
    },
    onDelete(id) {
          this.postForm.comment_id = id;
          this.loading = true;
          commentDelete
            .destroy(id)
              .then(response => {
                // this.$message({
                //   message: 'Comment delete successfully',
                //   type: 'success',
                //   duration: 5 * 1000,
                // });
                this.getList(this.postForm.league_id);
          });
          this.loading = false;
    },
    resetCounter(id) {
          this.loading = true;
          commentReset
            .destroy(id)
              .then(response => {
          });
          this.loading = false;
    },
    editComment(id) {
      //this.postForm.pre_comment[id] = val;
      this.postForm.isEdit[id] = true;

      console.log(this.postForm);
    },
  },
};
</script>
<style lang="scss" scoped>
.mention-item {
  padding: 4px 10px;
  border-radius: 4px;
}

.mention-selected {
  background: rgb(192, 250, 153);
}
</style>
<style lang="scss" scoped>
.user-activity {
  .user-block {
    .username, .description {
      display: block;
      margin-left: 50px;
      padding: 2px 0;
    }
    img {
      width: 40px;
      height: 40px;
      float: left;
    }
    :after {
      clear: both;
    }
    .img-circle {
      border-radius: 50%;
      border: 2px solid #d2d6de;
      padding: 2px;
    }
    span {
      font-weight: 500;
      font-size: 12px;
    }
  }
  .post {
    font-size: 14px;
    border-bottom: 1px solid #d2d6de;
    margin-bottom: 15px;
    padding-bottom: 15px;
    color: #666;
    .image {
      width: 100%;
    }
    .user-images {
      padding-top: 20px;
    }
  }
  .list-inline {
    padding-left: 0;
    margin-left: -5px;
    list-style: none;
    li {
      display: inline-block;
      padding-right: 5px;
      padding-left: 5px;
      font-size: 13px;
    }
    .link-black {
      &:hover, &:focus {
        color: #999;
      }
    }
  }
  .el-carousel__item h3 {
    color: #475669;
    font-size: 14px;
    opacity: 0.75;
    line-height: 200px;
    margin: 0;
  }

  .el-carousel__item:nth-child(2n) {
    background-color: #99a9bf;
  }

  .el-carousel__item:nth-child(2n+1) {
    background-color: #d3dce6;
  }
}

.comment-new h1{
    background-color: #FF4954;
    border-radius: 6px 6px 0px 0px;
    height: 40px;
    display: block;
    align-items: center;
    vertical-align: middle;
    font-family: 'Titillium Web', sans-serif;
    font-size: 18px;
    color: #fff;
    letter-spacing: 0.5px;
    font-weight: 900;
    width: 100%;
    padding: 8px 0px;
    margin: 0px;
}
.comment-new span.username a {
    color: #999999;
    font-size: 12px;
}
.comment-new span.username a:hover, .comment-new span.username a:focus{
  text-decoration: none;
}
.comment-new span.username{
  padding: 0px!important;
}
.comment-new .comt-part{
  background: #ffffff;
  padding: 15px 14px;
  margin-bottom: 0px;
  border: none;
}
.comment-new span.description {
    color: #333333;
    font-size: 14px;
    padding: 0px!important;
}
.comment-new span.description a {
    color: #999999!important;
    font-size: 12px;
    margin-right: 6px;
    border-right: 1px solid #999;
    padding-right: 6px;
}
.comment-new span.description a:last-child{
  border-right: none;
}
.botm_cmnt textarea {
    width: 100% !important;
    border: none;
    height: 50px !important;
    background-color: #EFEFEF;
    padding: 14px 30px 14px 52px !important;
    border-radius: 0px 0px 6px 6px;
}
.user-activity .user-block img{
    width: 44px;
    height: 44px;
    float: left;
    border: none!important;
    margin-right: 5px;
}
.botm_cmnt button.el-button.el-button--primary.el-button--medium {
    position: absolute;
    right: 10px;
    padding: 0px;
    top: 45%;
    transform: translateY(-50%)!important;
    border: none;
    background: transparent;
    box-shadow: none;
}
.botm_cmnt button.el-button.el-button--primary.el-button--medium:hover{
    color: #FF4954;
}

.botm_cmnt button.el-button.el-button--primary.el-button--medium img {
    max-width: 30px;
}
.cmt_update button {
    color: #333;
}
.cmt_update button:hover, .cmt_update button:focus{
  color: #333;
  outline: none;
  box-shadow: none;
}
.main-div {
    height: 300px;
    overflow-y: scroll;
    background: #fff;
}
img.cmmt_img {
    position: absolute;
    left: 6px;
    max-width: 38px;
    width: 40px;
    top: 44%;
    transform: translateY(-50%);
    border-radius: 100px;
    background: #fff;
    width: 40px;
    height: 40px;
}
span.dot_nm {
    position: relative;
    padding-left: 10px;
}
span.dot_nm:before {
    content: '';
    height: 2px;
    width: 2px;
    border-radius: 100%;
    background: #999999;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    left: 4px;
}
.botm_cmnt textarea:focus, .botm_cmnt textarea:hover{
    box-shadow: none;
    border: none;
    outline: none;
}
.botm_cmnt.cmt_update textarea {
    padding-right: 56px!important;
}
.botm_cmnt textarea::-webkit-scrollbar {
   display: none;
 }

</style>
