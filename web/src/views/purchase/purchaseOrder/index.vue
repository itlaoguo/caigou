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
      <div class="flex justify-between mb-3 pt-5 pl-5">
        <el-button type="primary" @click="open()">创建批量采购单</el-button>
      </div>
      <el-table :data="tableData" class="mt-3" v-loading="loading" >
        <el-table-column prop="number" label="批量采购单号" />
        <el-table-column prop="name" label="采购单名称" />
        <el-table-column prop="created_at" label="创建时间" />
        <el-table-column prop="updated_at" label="修改时间" />
        <el-table-column label="操作" width="200">
          <template #default="scope">
            <Update @click="open(scope.row.id)" />
            <Destroy @click="destroy(api, scope.row.id)" />
          </template>
        </el-table-column>
      </el-table>
      <Paginate />
    </div>

    <Dialog v-model="visible" :title="title" destroy-on-close width="80%">
      <Create @close="close(reset)" :primary="id" :api="api" />
    </Dialog>
  </div>
</template>

<script lang="ts" setup>
import { computed, onMounted } from 'vue'
import Create from './create.vue'
import { useGetList } from '@/composables/curd/useGetList'
import { useDestroy } from '@/composables/curd/useDestroy'
import { useOpen } from '@/composables/curd/useOpen'

const api = 'purchase/purchase/order'

const { data, query, search, reset, loading } = useGetList(api)
const { destroy, deleted } = useDestroy()
const { open, close, title, visible, id } = useOpen()

const tableData = computed(() => data.value?.data)

onMounted(() => {
  search()
  deleted(reset)
})
</script>
