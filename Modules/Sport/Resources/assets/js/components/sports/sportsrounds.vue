<template>
  <div class="app-container">

      <div class="filter-container">
          <router-link :to="'/rounds/add/'+sportId">
          <el-button class="el-button el-button--primary el-button--medium">
            Add Round
          </el-button>
        </router-link>
      </div>

    <!-- Note that row-key is necessary to get a correct row order. -->
    <el-table v-loading="listLoading" :data="list" row-key="id" border fit highlight-current-row style="width: 100%">
      <el-table-column align="center" label="ID" >
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column  align="center" label="Sport Name" >
        <template slot-scope="scope">
          <span>{{scope.row.sport.sport_name }}</span> 
        </template>
      </el-table-column>
      <el-table-column align="center" label="Round Name" >
        <template slot-scope="scope">
          <span>{{scope.row.round_name }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Round Number" >
        <template slot-scope="scope">
          <span>{{scope.row.round_number }}</span>
        </template>
      </el-table-column>
      <!--el-table-column width="180px" align="center" label="Round Description">
        <template slot-scope="scope">
          <span>{{scope.row.round_description }}</span>
        </template>
      </el-table-column-->
      <el-table-column width="180px" align="center" label="Start Date" >
        <template slot-scope="scope">
          <span>{{scope.row.start_datetime }}</span>
        </template>
      </el-table-column>
       <el-table-column width="180px" align="center" label="End Date">
        <template slot-scope="scope">
          <span>{{scope.row.end_datetime }}</span>
        </template>
      </el-table-column>
       <el-table-column align="center" label="Actions" width="220">
        <template slot-scope="scope">
          <router-link :to="'/rounds/edit/'+scope.row.id">
            <el-button type="primary" size="small" icon="el-icon-edit">
              Edit
            </el-button>
          </router-link>
          <router-link :to="'/fixtures/add/'+scope.row.sport.id+'/'+scope.row.id">
            <el-button type="info" size="small">
              Fixture
            </el-button>
          </router-link>
        </template>
      </el-table-column>   
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
  </div>
</template>

<script>
import { fetchRoundListBySport } from '@/api/round';
import Sortable from 'sortablejs';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination

export default {
  name: 'RoundList',
  components: { Pagination },
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
      sportId:'',
      total: null,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 10,
        string: ''
      },
      filterStats:{
        sport: ''
      },
      sortable: null,
      oldList: [],
      newList: [],
      tempList: [],
      importanceOptions: [],
    };
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
      this.listLoading = true;
      const sport_id = this.$route.params && this.$route.params.id;
      this.sportId = sport_id;
      const { data } = await fetchRoundListBySport(sport_id);
      this.list = data.items.data;
      this.tempList = data.items.data;
      this.total = data.items.total;
      var fileTest = []; 
      this.listLoading = false;
      
 if(this.filterStats.sport=='') {
    this.importanceOptions = data.sport;
  }

      this.oldList = this.list.map(v => v.id);
      this.newList = this.oldList.slice();
      this.$nextTick(() => {
        this.setSort();
      });
    },
    setSort() {
      const el = this.$refs.dragTable.$el.querySelectorAll('.el-table__body-wrapper > table > tbody')[0];
      this.sortable = Sortable.create(el, {
        ghostClass: 'sortable-ghost', // Class name for the drop placeholder,
        setData: function(dataTransfer) {
          // to avoid Firefox bug
          // Detail see : https://github.com/RubaXa/Sortable/issues/1012
          dataTransfer.setData('Text', '');
        },
        onEnd: evt => {
          const targetRow = this.list.splice(evt.oldIndex, 1)[0];
          this.list.splice(evt.newIndex, 0, targetRow);

          // for show the changes, you can delete in you code
          const tempIndex = this.newList.splice(evt.oldIndex, 1)[0];
          this.newList.splice(evt.newIndex, 0, tempIndex);
        },
      });
    },
    handleFilter (a, b) {
     this.listQuery.string = this.filterStats.sport;
     this.getList();
    },
    clearFilter () {
     this.listQuery.string = '';
     this.filterStats.sport = '';
     this.importanceOptions = [];
     this.getList();
    },
  },
};
</script>

<style>
.sortable-ghost{
  opacity: .8;
  color: #fff!important;
  background: #42b983!important;
}
</style>

<style scoped>
.icon-star {
  margin-right:2px;
}
.drag-handler {
  width: 20px;
  height: 20px;
  cursor: pointer;
}
.show-d {
  margin-top: 15px;
}
img{
  width: 5%;
  height: 3%;
  display: block;
  margin-bottom: 10px;
}
</style>
