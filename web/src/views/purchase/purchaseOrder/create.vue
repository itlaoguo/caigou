<template>
    <el-steps style="width: 100%" :active="2" align-center>
        <el-step title="创建采购单" description="选择导入方式和基本配置" />
        <el-step title="导入excel" description="按照模板导入表单" />
        <el-step title="确认采购详情" description="确认下单" />
    </el-steps>
  <el-form :model="formData" label-width="120px" ref="form" v-loading="loading" class="pr-4">
    <div class="section">
      <el-form-item label="选择商品录入方式" prop="enter_way">
        <div class="flex">
          <div class="flex flex-col item-center text-center mr-4 pt-2 border-2 border-[#8a8a8a] cursor-pointer">
            <p><i class="iconfont icon-edit"></i></p>
            <p>人工录入</p>
          </div>
          <div class="flex flex-col item-center text-center mr-4 pt-2 border-2 border-[#8a8a8a] cursor-pointer">
            <p><i class="iconfont icon-excel"></i></p>
            <p>excel录入</p>
          </div>
        </div>
      </el-form-item>
      <el-form-item label="业务ID" prop="biz_id">
          <el-input v-model="formData.biz_id" name="biz_id" clearable />
      </el-form-item>
      <el-form-item label="名称" prop="name">
          <el-input v-model="formData.name" name="name" clearable />
      </el-form-item>
      <el-form-item label="下单模式" prop="mode">
          <el-input v-model="formData.mode" name="mode" clearable />
      </el-form-item>
      <el-form-item label="下单账号" prop="account">
          <el-input v-model="formData.account" name="mode" clearable />
      </el-form-item>

    </div>
    
    <div class="flex justify-end">
        <el-button type="primary" @click="submitForm(form)">{{ $t('system.confirm') }}</el-button>
    </div>
  </el-form>
</template>

<script lang="ts" setup>
import { useCreate } from '@/composables/curd/useCreate'
import { useShow } from '@/composables/curd/useShow'
import { onMounted } from 'vue'

const props = defineProps({
  primary: [String, Number],
  api: String,
})

const { formData, form, loading, submitForm, close } = useCreate(props.api, props.primary)

if (props.primary) {
  useShow(props.api, props.primary, formData)
}

const emit = defineEmits(['close'])
onMounted(() => {
  close(() => emit('close'))
})
</script>
