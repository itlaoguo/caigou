<template>
  <div>
    <Search :search="search" :reset="reset">
      <template v-slot:body>
          <el-form-item label="采购单号" prop="number">
              <el-input v-model="query.number"  placeholder="请输入" name="number" clearable />
          </el-form-item>
          <el-form-item label="采购名称" prop="name">
              <el-input v-model="query.name"  placeholder="请输入" name="name" clearable />
          </el-form-item>
          <el-form-item label="状态">
              <Select v-model="query.status" clearable api="status" />
          </el-form-item>
          <el-form-item label="创建时间">
              <el-date-picker
                  v-model="query.created_at"
                  type="daterange"
                  range-separator="至"
                  start-placeholder="起始日期"
                  end-placeholder="结束日期"
              />
          </el-form-item>
      </template>
    </Search>
    <div class="table-default">

      <el-table :data="tableData" class="mt-3" v-loading="loading" >
        <el-table-column prop="name" label="采购单名称" />
        <el-table-column prop="created_at" label="创建时间" />
        <el-table-column prop="updated_at" label="最后更新时间" />
        <el-table-column prop="status" label="状态" >
          <template #default="scope">
                      <p v-if="scope.row.status == 10">已作废</p>
                      <p v-if="scope.row.status == 0">创建完成</p>
                      <p v-if="scope.row.status == 1">运行中</p>
                      <p v-if="scope.row.status == 2">运行完成</p>
                  </template>
        </el-table-column>
        <el-table-column prop="detail" label="详细任务状态">
          <template #default="scope">
                      <p>采购商品数量：{{ scope.row.total_product }}</p>
                      <p>采购子任务：{{ scope.row.total_task }}</p>
                      <p>已执行子任务：{{ scope.row.total_success_task + scope.row.total_fail_task }}</p>
                      <p>成功：{{ scope.row.total_success_task }}</p>
                      <p>失败：{{ scope.row.total_fail_task }}</p>
                  </template>
        </el-table-column>
        <el-table-column label="操作" width="200">
          <template #default="scope">
            <p style="margin: 10px 0;"><el-button size="small" v-if=" scope.row.status === 0" type="primary" @click="updatePurchase(scope.row.id,'start')"><Icon name="eye" class="w-3 mr-1" /> 开始执行</el-button></p>
            <p style="margin: 10px 0;"><el-button size="small" v-if=" scope.row.status === 0" type="primary" @click="updatePurchase(scope.row.id,'cancel')"><Icon name="eye" class="w-3 mr-1" /> 作废</el-button></p>
            <p style="margin: 10px 0;"><el-button size="small" type="success" @click="view(scope.row.id)"><Icon name="eye" class="w-3 mr-1" /> 查看</el-button></p>
          </template>
        </el-table-column>
      </el-table>
      <Paginate />
    </div>
    <!-- 采购单详情 -->
    <Dialog v-model="purchaseOrdersVisible" title="采购单详情" width="1200px" destroy-on-close>
      <Show :id="id" />
    </Dialog>
  </div>
</template>

<script lang="ts" setup>
import { computed, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { useGetList } from '@/composables/curd/useGetList'
import { useDestroy } from '@/composables/curd/useDestroy'
import { useOpen } from '@/composables/curd/useOpen'
import Show from './show.vue'
import http from '@/support/http'

const api = 'purchase/order'


const { data, query, search, reset, loading } = useGetList(api)
const { destroy, deleted } = useDestroy()
const { open, close, title, visible, id } = useOpen()
console.log(query.value,typeof query.value,'------------------');


const purchaseOrdersVisible = ref<boolean>(false)

const tableData = computed(() => data.value?.data)

const view = (primaryId:number)=> {
  purchaseOrdersVisible.value = true
  id.value = primaryId
}

const updatePurchase = (primaryId:number, type:string) => {
  let message = ''
  if (type === 'start') {
    message = '确认开始执行该采购单？'
  } else if (type === 'cancel') {
    message = '确认作废该采购单？'
  }
  ElMessageBox.confirm(message, '提示', {
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    type: 'warning',
  })
    .then(() => {
      let data  = {}
      if (type === 'start') {
        data = { status: 1 }
      } else if (type === 'cancel') {
        data = { status: 10 }
      }
      try {
        loading.value = true
        http.put(`/purchase/order/${primaryId}`, data).then(() => {
          ElMessage({
            type: 'success',
            message: '操作成功!',
          })
        })
      } catch (error) {
        //
      }finally {
        loading.value = false
      }
     
      
    })
    .catch(() => {
      //
    })
}


onMounted(() => {
  search()
  deleted(reset)
})
</script>
