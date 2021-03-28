<template>
  <div class="app-container">
    <!-- Note that row-key is necessary to get a correct row order. -->
    <el-table v-loading="listLoading" :data="list" row-key="id" border fit highlight-current-row style="width: 100%">
      <el-table-column align="center" label="ID" width="65">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column width="180px" align="center" label="Fixture Name">
        <template slot-scope="scope">
          <span>{{scope.row.fixture_name }} </span>
        </template>
      </el-table-column>
      <el-table-column width="180px" align="center" label="Match Time">
        <template slot-scope="scope">
          <span>{{scope.row.match_datetime }}</span>
        </template>
      </el-table-column>
      <el-table-column width="255px" align="center" label="Home Team">
        <template slot-scope="scope">
          <span>{{scope.row.hometeam.team_name }}</span>
          <el-input v-if="scope.row.fixure_result_status!='pending'" type="text" :disabled="true" :value="scope.row.fixure_result_status=='abandon' ? '' : scope.row.home_team_score" />
          <el-input v-else v-model="listValue['home_'+scope.row.id]" type="text" :disabled="switchList['list_'+scope.row.id]"  />
        </template>
      </el-table-column>
      <el-table-column width="255px" align="center" label="Away Team">
        <template slot-scope="scope">
          <span>{{scope.row.awayteam.team_name }}</span>
          <el-input v-if="scope.row.fixure_result_status!='pending'" type="text" :disabled="true" :value="scope.row.fixure_result_status=='abandon' ? '' : scope.row.away_team_score" />
          <el-input v-else v-model="listValue['away_'+scope.row.id]" type="text" :disabled="switchList['list_'+scope.row.id]" />
        </template>
      </el-table-column>
       <el-table-column align="center" label="Abandon" width="120">
        <template slot-scope="scope">
          <el-switch @change="handleSwitch(scope.row.id)" :disabled="scope.row.fixure_result_status!='pending'" :value="switchList['list_'+scope.row.id]" />
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
    <el-button v-loading="listLoading" type="primary" @click="saveResult" v-if="roundUndu=='resultcanProcess'">
      Submit
    </el-button>
    <el-button v-loading="listLoading" type="primary" @click="undoHandler" v-if="roundUndu=='unducanProcess'">
      Undo Result
    </el-button>
    <el-button v-loading="processing" type="primary" @click="saveResult" v-if="roundUndu!='resultcanProcess' && roundUndu!='unducanProcess'">
      Processing...
    </el-button>
    <el-button>
      Cancel
    </el-button>
    <el-dialog :visible.sync="resultDialogVisible" title="Result Confirmation">
    <el-form label-width="120px">
     <p>Your Highest Total is: <b>{{resultStatus.largest}}</b></p>
     <p>Result Status would be: <b v-if="resultStatus.status!=''">{{resultStatus.status}}</b><b v-else> Abondoned </b></p>
     <el-form-item>
       <el-button v-loading="resultLoader" type="primary" @click="onResultSubmit">
         Save Result
       </el-button>
       <el-button v-loading="resultLoader" @click="resultDialogVisible = false">
         Cancel
       </el-button>
     </el-form-item>
   </el-form>
   </el-dialog>
  </div>
</template>

<script>
import { fetchFixture, makeAbondon, saveResultStats, undoResult } from '@/api/result';
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
      iconPath: '/uploads/sport/',
      list: [],
      isTR:false,
      trueList:[],
      total: 0,
      listLoading: true,
      processing: true,
      resultDialogVisible: false,
      resultLoader: false,
      roundUndu: 'no',
      listQuery: {
        page: 1,
        limit: 10,
      },
      undoId: false,
      undoRequest: {
        id: false
      },
      sortable: null,
      oldList: [],
      newList: [],
      switchList: [],
      listValue: [],
      resultStatus: [],
      totalfixture:''
    };
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
      this.listLoading = true;
      const id = this.$route.params && this.$route.params.id;
      const s_id = this.$route.params && this.$route.params.s_id;      
      console.log(this.$route.params.id);
      const { data } = await fetchFixture( id, s_id );
      this.list = data.items;
      this.undoId = data.undoStats;
      this.roundUndu = data.roundStatus;
      this.listLoading = false;
      this.oldList = this.list.map(v => v.id);
      const keys = Object.keys(this.list);
      for (const key of keys) {
      let temp = {};
       if(this.list[key].fixure_result_status=="abandon") {
         this.switchList['list_'+this.list[key].id] = true;
       } else  {
         this.switchList['list_'+this.list[key].id] = false;
       }
      }
      this.newList = this.oldList.slice();
      this.listValue= [];
      this.$nextTick(() => {
        this.setSort();
      });
    },
    handleSwitch(e) {
     this.switchList['list_'+e] = !this.switchList['list_'+e];
    },
    saveResult() {
      let tes = Object.keys(this.list).length;
      let len = Object.keys(this.switchList);
      
      var swtchCounut = 0;
       for (const key of len) {
        if(this.switchList[key]) {
          swtchCounut = swtchCounut + 1;
        }
       }
     
    console.log(Object.keys(this.listValue).length);
     this.resultStatus = this.getWinner();
     this.resultStatus.fixture = this.getUniqueListBy(this.resultStatus.fixture, 'fixture_id');
     //console.log(this.resultStatus.fixture[0]);
     if(this.resultStatus.largest) {
       if(Object.keys(this.listValue).length % 2 == 0){
         this.resultDialogVisible = true;
       } else {
          this.$message({
	         message: 'Input field is blank',
	         type: 'error',
	         duration: 5 * 1000,
	       });
       }
       
     } else {
          if( tes === swtchCounut ) {

            this.resultDialogVisible = true;
          } else {
           this.totalfixture = tes*2;
           console.log(this.totalfixture);
          if(Object.keys(this.listValue).length != this.totalfixture){
             this.$message({
  	         message: 'Input field is blank',
  	         type: 'error',
  	         duration: 5 * 1000,
  	       }); 
          } 
          else{
            this.resultDialogVisible = true;
          }

          }
	       
     }
     //console.log(this.resultStatus);
     //console.log(this.switchList);
    },
    getWinner() {
    const keys2 = Object.keys(this.listValue);
    var largest = 0;
    var status = '';
    var largestKey = '';
    let fixture = [];
    for (const ke of keys2) {
     let value = parseInt(this.listValue[ke]);
     if(value === largest) {
       largest = value;
       status = 'draw';
       largestKey = ke;
     }
      if(value > largest) {
        largest = value;
        status = 'completed';
        largestKey = ke;
      }
      fixture.push( this.calculateFixtureResult( ke ) );
    }
    return { largest, status, largestKey, fixture };
    },
    getUniqueListBy(arr, key) {
      return [...new Map(arr.map(item => [item[key], item])).values()]
    },
    calculateFixtureResult( input ) {
        var res = input.split("_");
        let homeValue = parseInt(this.listValue['home_'+res[1]]);
        let awayValue = parseInt(this.listValue['away_'+res[1]]);
        let temp = {};
        if( homeValue > awayValue ) {
           temp['fixture_id'] = res[1];
           temp['winner'] = 'home';
           temp['losser'] = 'away';
           temp['winnerPoint'] = homeValue;
           temp['looserPoint'] = awayValue;
           temp['status'] = 'completed';
        }
        if( homeValue < awayValue && homeValue !== awayValue ) {
           temp['fixture_id'] = res[1];
           temp['winner'] = 'away';
           temp['losser'] = 'home';
           temp['winnerPoint'] = awayValue;
           temp['looserPoint'] = homeValue;
           temp['status'] = 'completed';
        }
        if( homeValue == awayValue ) {
           temp['fixture_id'] = res[1];
           temp['winner'] = null;
           temp['losser'] = null;
           temp['winnerPoint'] = homeValue;
           temp['looserPoint'] = awayValue;
           temp['status'] = 'draw';
        }
        return temp;

    },
    async onResultSubmit() {
    this.resultLoader = true;
    this.resultStatus['round_id'] = this.$route.params && this.$route.params.id;
    this.resultStatus['abondonList'] = Object.assign({}, this.switchList);
    const { data } = await saveResultStats( this.resultStatus );
     if( data.status=='success' ) {
       this.$message({
         message: 'Result has been Saved, Successfully',
         type: 'success',
         duration: 5 * 1000,
       });
     } else {
       this.$message({
         message: data.message,
         type: 'error',
         duration: 5 * 1000,
       });
     }
     this.resultLoader= false;
     this.resultDialogVisible = false;
     this.getList();
    },
    async undoHandler() {
    this.undoRequest.id = this.undoId;
     const { data } = await undoResult( this.undoRequest );
     if( data ) {
       this.$message({
         message: 'Result has been Saved, Successfully',
         type: 'success',
         duration: 5 * 1000,
       });
     } else {
       this.$message({
         message: 'opps!, something went wrong',
         type: 'error',
         duration: 5 * 1000,
       });
      }
      this.getList();
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
