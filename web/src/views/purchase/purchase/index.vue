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
          </template>
      </Search>

      <div class="table-default">
          <div class="pt-5 pl-2">
              <Add :size="'default'" :text="'新增采购单'" />
          </div>
          <el-table :data="tableData" class="mt-3" v-loading="loading" row-key="id" default-expand-all :tree-props="{ children: 'children' }">
              <el-table-column prop="number" label="批量采购单号" />
              <el-table-column prop="name" label="采购单名称" />
              <el-table-column prop="bizId" label="业务ID" />
              <el-table-column prop="created_at" label="创建时间" />
              <el-table-column prop="updated_at" label="修改时间" />
              <el-table-column prop="status" label="状态" />
              <el-table-column prop="task_detail" label="详细任务" />
              <el-table-column label="操作" width="200">
                  <template #default="scope">
                      <Show @click="openPurchaseForm(scope.row.id, scope.row.permissions)" />
                  </template>
              </el-table-column>
          </el-table>
      </div>
      <Dialog v-model="visible" :title="title" destroy-on-close>
          <Create @close="close(reset)" :primary="id" :api="api" :has-permissions="rolePermissions" />
      </Dialog>
  </div>
</template>

<script lang="ts" setup>
// @ts-nocheck
import { computed, onMounted, ref } from 'vue'
import Create from './form/create.vue'
import { useGetList } from '@/composables/curd/useGetList'
import { useDestroy } from '@/composables/curd/useDestroy'
import { useOpen } from '@/composables/curd/useOpen'

const api = 'purchase/purchase'

const { data, query, search, reset, loading } = useGetList(api, false)
const { destroy, deleted } = useDestroy()
const { open, close, title, visible, id } = useOpen()

const tableData = computed(() => data.value?.data)

const rolePermissions = ref<Array<number>>([])
const openPurchaseForm = (id, permissions) => {
  
}
onMounted(() => {
  search()

  deleted(reset)
})
</script>
