<template>
  <div class="app-container">
    <el-form ref="postForm" :model="postForm" :rules="sportRules" label-width="220px">
      <el-form-item label="Select Type 1 User Team" prop="type_1_team">
        <div class="slct custom_arrw el-col el-col-12">
          <select class="form-control positionTypes" v-model="postForm.type_1_team" name="type_1_team"  required>
            <option value="" disabled selected>Please select a team</option>
            <option v-for="(item, index) in list" :label="item.team_name" :value="item.id" :class="item.team_name"/>
          </select>
          </div>
      </el-form-item>
      <el-form-item label="Select Type 2 User Team" prop="type_2_team">
        <div class="slct custom_arrw el-col el-col-12">
          <select class="form-control positionTypes" v-model="postForm.type_2_team" name="type_2_team"  required>
            <option value="" disabled selected>Please select a team</option>
            <option v-for="(item, index) in list" :label="item.team_name" :value="item.id" :class="item.team_name"/>
          </select>
          </div>
      </el-form-item>
 <!--      <el-form-item label="Select Fixture 1" prop="fixture1">
        <div class="slct custom_arrw el-col el-col-12">
          <select class="form-control positionTypes" v-model="postForm.fixture1" name="fixture1"  required>
            <option value="" disabled selected>Please select a Fixture 1</option>
            <option v-for="(item, index) in list2" :label="item.fixture_name" :value="item.id" :class="item.team_name"/>
          </select>
          </div>
      </el-form-item>
      <el-form-item label="Select Fixture 2" prop="fixture1">
        <div class="slct custom_arrw el-col el-col-12">
          <select class="form-control positionTypes" v-model="postForm.fixture2" name="fixture2"  required>
            <option value="" disabled selected>Please select a Fixture 2</option>
            <option v-for="(item, index) in list2" :label="item.fixture_name" :value="item.id" :class="item.team_name"/>
          </select>
          </div>
      </el-form-item> -->
      <el-form-item>
        <el-button v-loading="loading" type="primary" @click.native.prevent="onSubmit">
          Assign
        </el-button>
      </el-form-item>
    </el-form>
  </div>
</template>
<script>

import { fetchTeamList, assignteams, fetchFixtureList } from '@/api/sport';
import Resource from '@/api/resource';
const userResource = new Resource('team');

export default {
  data() {
    const validateName = (rule, value, callback) => {
      if (value.length < 1) {
        callback(new Error('This Field is required'));
      } else {
        callback();
      }
    };

    return {
      team_icon: '',
      list: [],
      list2: [],
      postForm: {
        type_1_team: '',
        type_2_team: '',
      },
      sportRules: {
        type_1_team: [{ required: true, validator: validateName }],
        type_2_team: [{ required: true, validator: validateName }],
        fixture1: [{ required: true, validator: validateName }],
        fixture2: [{ required: true, validator: validateName }],
      },
      loading: false,
      tempRoute: {},
    };
  },
  created() {
    this.tempRoute = Object.assign({}, this.$route);
    this.getList();
    this.getList2();
  },
  methods: {
    async getList() {
      this.listLoading = true;
      const { data } = await fetchTeamList(this.listQuery);

      this.list = data.items.data;

    },
   async getList2() {
      this.listLoading = true;

      const { data } = await fetchFixtureList(this.listQuery);

      this.list2 = data.items.data;
    },
    async onSubmit() {
      this.listLoading = true;

        const { data } =  await assignteams(this.postForm.type_1_team,this.postForm.type_2_team);
        // console.log(data.status);
        // if(data == "success"){
          this.$message({
                  message: 'You have succesfully assigned the team',
                  type: 'success',
                  duration: 5 * 1000,
                });
          this.listLoading = false;
          //alert("You have succesfully assigned the team");
        // }
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.line{
  text-align: center;
}
img{
  width: 20%;
  height: 10%;
  display: block;
  margin-bottom: 10px;
}
</style>

