<template>
  <div>
    <div class="table-default">
      <el-table :data="tableData" class="mt-3" v-loading="loading" >
        <el-table-column prop="productList" label="商品基本信息" />
        <el-table-column prop="orderInfo" label="订单信息" />
        <el-table-column prop="amountInfo" label="订单金额" />
        <el-table-column prop="purchaser" label="采购方" />
        <el-table-column prop="supplier" label="供给方" />
        <el-table-column prop="pay_way" label="支付方式" />
        <el-table-column prop="created_at" label="下单时间" />
        <el-table-column prop="afersale_status" label="订单状态" />
        <el-table-column label="操作" width="200">
          <template #default="scope">
            <Update @click="open(scope.row.id)" />
            <Destroy @click="destroy(api, scope.row.id)" />
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

const api = 'order/afterSale'

const { data, query, search, reset, loading } = useGetList(api)
const { destroy, deleted } = useDestroy()
const { open, close, title, visible, id } = useOpen()

const tableData = computed(() => data.value?.data)

onMounted(() => {
  search()
  deleted(reset)
})
</script>

<style scoped>
.time-filter {
  display: flex;
  gap: 12px;
  align-items: center;
}
.select-inline {
  flex: 0 0 160px;
}
.time-range-picker {
  flex: 1;
  min-width: 220px;
}
</style>
