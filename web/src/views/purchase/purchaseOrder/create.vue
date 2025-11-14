<template>
    <div class="w-full min-h-0 pt-5 pl-5 pr-5 bg-white rounded-lg dark:bg-regal-dark">
      <el-steps style="width: 100%" :active="active" align-center class="pd-10">
          <el-step title="创建采购单" description="选择导入方式和基本配置" />
          <el-step title="导入excel" description="按照模板导入表单" />
          <el-step title="确认采购详情" description="确认下单" />
      </el-steps>
      <br/>
      <BasicInfo v-if="active === 1" @next="next" @prev="prev" />
      <UploadInfo v-if="active === 2" @next="next" @prev="prev" />
      <ProductList v-if="active === 3" @next="next" @prev="prev" />
    </div>

</template>

<script lang="ts" setup>
import { ref, watch } from 'vue'
import BasicInfo from './steps/basicInfo.vue'
import UploadInfo from './steps/uploadInfo.vue'
import ProductList from './steps/productList.vue'
import { usePurchaseOrderStore } from './store'

const purchaseOrderStore = usePurchaseOrderStore()

const active = ref(1)

const next = () => {
  if (active.value < 3) {
    active.value++
  }
}
const prev = () => {
  if (active.value > 1) {
    active.value--
  }
}

</script>
