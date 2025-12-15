<template>
  <div>
    <Search :search="search" :reset="reset">
      <template v-slot:body>
            <el-form-item label="时间筛选">
                <div class="time-filter">
                <Select v-model="query.timeType" clearable api="status" class="select-inline" />
                <el-date-picker
                    v-model="query.timeRange"
                    type="daterange"
                    range-separator="至"
                    start-placeholder="起始日期"
                    end-placeholder="结束日期"
                    class="time-range-picker"
                />
                </div>
            </el-form-item>
            <el-form-item label="采购单号" prop="number">
                <el-input v-model="query.number"  placeholder="请输入" name="number" clearable />
            </el-form-item>
            <el-form-item label="采购方" prop="purchaser">
                <el-input v-model="query.purchaser"  placeholder="请输入" name="purchaser" clearable />
            </el-form-item>
            <el-form-item label="收件人" prop="receiver">
                <el-input v-model="query.receiver"  placeholder="请输入" name="receiver" clearable />
            </el-form-item>
            <el-form-item label="收件手机" prop="telephone">
                <el-input v-model="query.telephone"  placeholder="请输入" name="telephone" clearable />
            </el-form-item>
      </template>
    </Search>
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
