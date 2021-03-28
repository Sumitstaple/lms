<template>
  <div class="app-container">
  <div class="row">
    <div class="col-12">
      <table class="table table-striped" v-loading="listLoading">
        <thead class="thead-light">
          <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Sport</th>
          </tr>
        </thead>
        <draggable v-model="list"  tag="tbody" @change="settable($event)">
          <tr v-for="item in list" :key="item.name">
            <td scope="row">{{item.sort}}</td>
            <td>{{ item.team_name }}</td>
            <td>{{ item.sportname }}</td>
          </tr>
        </draggable>
      </table>
    </div>

    <rawDisplayer class="col-3" :value="list" title="List" />
  </div>
</div>
</template>

<script>
import { manageteamlist,saveteamlist } from '@/api/league';
import Sortable from 'sortablejs';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import draggable from 'vuedraggable'
import $ from 'jquery'

export default {
  name: 'TeamList',
  components: { Pagination,draggable },
  filters: {
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'info',
        deleted: 'danger',
      };
      return statusMap[status];
    },
  },

  data() {
    return {
      iconPath: '/uploads/team/',
      list: [],
      tabledata: [
      ],
      total: null,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 10,
      },
      sortable: null,
      oldList: [],
      newList: [],
    };
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
    	const id = this.$route.params && this.$route.params.id;
      this.listLoading = true;
      const { data } = await manageteamlist(id);
      this.list = data.items;
      this.tabledata = data.items;
      // console.log(this.list);
      this.listLoading = false;
    },

  async settable(event){

   const id = this.$route.params && this.$route.params.id;

    this.array_move(this.tabledata,event.moved.oldIndex,event.moved.newIndex);
    console.log(this.tabledata);
    saveteamlist({sportid:id,data:this.tabledata}).then(response => {
      console.log(response)
     });

    },
  array_move(arr, old_index, new_index) {
    if (new_index >= arr.length) {
        var k = new_index - arr.length + 1;
        while (k--) {
            arr.push(undefined);
        }
    }
    arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
    return arr; // for testing
}
  },
};
</script>