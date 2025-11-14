<template>
    <div class="w-full sm:w-[90%] mx-auto pb-10">
        <el-form :model="baseInfo" :rules="rules" label-width="120px" ref="form" class="pr-4">
            <el-form-item label="商品录入方式" prop="enter_way">
                <el-radio-group v-model="baseInfo.enter_way">
                    <el-radio-button value="excel">Excel录入</el-radio-button>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="名称" prop="name">
                <el-input v-model="baseInfo.name" name="name" placeholder="请输入采购单名称" clearable />
            </el-form-item>
            <el-form-item label="任务描述" prop="description">
                <el-input 
                    v-model="baseInfo.description" 
                    name="description" 
                    type="textarea" 
                    :rows="3"
                    placeholder="请输入任务描述" 
                    clearable 
                />
            </el-form-item>
            <el-form-item label="下单模式" prop="mode">
                <el-input v-model="baseInfo.mode" name="mode" placeholder="请输入下单模式" clearable />
            </el-form-item>
            <el-form-item label="下单账号" prop="account">
                <el-input v-model="baseInfo.account" name="account" placeholder="请输入下单账号" clearable />
            </el-form-item>
        </el-form>
        <div class="flex justify-end w-full pl-24 mx-auto mt-4 sm:w-96">
            <el-button class="mt-5" type="primary" @click="submitCreateTable(form)">{{ $t('system.next') }}</el-button>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { computed, ref, unref } from 'vue'
import { usePurchaseOrderStore } from '../store'
import type { FormInstance } from 'element-plus'

const purchaseOrderStore = usePurchaseOrderStore()
purchaseOrderStore.start()

const emits = defineEmits(['prev', 'next'])

const baseInfo = ref(purchaseOrderStore.getBaseInfo)
const form = ref<FormInstance>()

const rules = {
    enter_way: [
        { required: true, message: '请选择商品录入方式', trigger: 'change' }
    ],
    name: [
        { required: true, message: '请输入采购单名称', trigger: 'blur' }
    ],
    mode: [
        { required: true, message: '请输入下单模式', trigger: 'blur' }
    ],
    account: [
        { required: true, message: '请输入下单账号', trigger: 'blur' }
    ]
}

const submitCreateTable = (formEl: FormInstance | undefined) => {
    if (!formEl) return
    formEl.validate((valid: any):any => {
        if (valid) {
            emits('next')
            purchaseOrderStore.setBaseInfo(unref(baseInfo))
        } else {
            return false
        }
    })
}

</script>
