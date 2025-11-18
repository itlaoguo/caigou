<template>
  <el-table :data="productList" border v-loading="loading" style="width: 100%">
      <el-table-column label="商品信息" >
          <template #default="scope">
              <div class="flex">
                  <div style="width:80px;height: 80px;margin:0 10px;">
                      <img :src="scope.row.product_pic_url" width="100" height="100">
                  </div>
                  <div class="">
                      <p class="title">{{ scope.row.product_title }}</p>
                      <p>{{ scope.row.product_id }}</p>
                  </div>
              </div>
          </template>
      </el-table-column>
      <el-table-column prop="sku_title" label="SKU名称" width="200" />
      <el-table-column prop="sku_id" label="SKU ID" width="200" />
      <el-table-column label="价格" width="100">
          <template #default="scope">
              ¥{{ (scope.row.price/100)?.toFixed(2) || '0.00' }}
          </template>
      </el-table-column>
      <el-table-column prop="quantity" label="数量"  width="200" />
      <el-table-column label="收货人信息" >
          <template #default="scope">
              <p>收货人：{{ scope.row.receiver }}</p>
              <p>手机号码：{{ scope.row.receiver_phone }}</p>
              <p>收货地址：{{ scope.row.address_detail }}</p>
              <!-- <p>用户ID:{{ scope.row.address_detail }}</p> -->
          </template>
      </el-table-column>
  </el-table>
</template>

<script lang="ts" setup>
// @ts-nocheck
import { useShow } from '@/composables/curd/useShow'
const props = defineProps({
  id: {
    type: Number,
    required: true
  }
})

// const data = ref<Array<object>>()
const { data, loading } = useShow('schema', props.id)
</script>

<style scoped></style>
