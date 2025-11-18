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
                  :size="size"
              />
          </el-form-item>
      </template>
    </Search>
    <div class="table-default">
     
      <el-table :data="tableData" class="mt-3" v-loading="loading" >
        <el-table-column prop="name" label="采购单名称" />
        <el-table-column prop="created_at" label="创建时间" />
        <el-table-column prop="updated_at" label="修改时间" />
        <el-table-column prop="status" label="状态" />
        <el-table-column prop="detail" label="详细任务状态">
          <template #default="scope">
                      <p>采购商品数量：{{ scope.row.created_at }}</p>
                      <p>采购子任务：{{ scope.row.created_at }}</p>
                      <p>已执行子任务：{{ scope.row.created_at }}</p>
                      <p>失败：{{ scope.row.created_at }}</p>
                      <p>成功{{ scope.row.created_at }}</p>
                  </template>
        </el-table-column>
        <el-table-column label="操作" width="200">
          <template #default="scope">
            <el-button size="small" type="success" @click="view(scope.row.id)"><Icon name="eye" class="w-3 mr-1" /> 查看</el-button>
          </template>
        </el-table-column>
      </el-table>
      <Paginate />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, onMounted } from 'vue'
import { useGetList } from '@/composables/curd/useGetList'
import { useDestroy } from '@/composables/curd/useDestroy'
import { useOpen } from '@/composables/curd/useOpen'

const api = 'purchase/order'

const { data, query, search, reset, loading } = useGetList(api)
const { destroy, deleted } = useDestroy()
const { open, close, title, visible, id } = useOpen()

const tableData = computed(() => data.value?.data)

const view = primaryId => {
  schemaVisible.value = true

  id.value = primaryId
}

onMounted(() => {
  search()
  deleted(reset)
})
</script>
