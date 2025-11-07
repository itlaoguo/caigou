<template>
    <div class="w-full sm:w-[90%] mx-auto">
        <el-form :model="formData" label-width="120px" ref="form" v-loading="loading" class="pr-4">
            <el-form-item label="选择商品录入方式" prop="enter_way">
                
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
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import { computed, ref, unref } from 'vue'
import { useSchemaStore } from '../store'
import type { FormInstance } from 'element-plus'

const schemaStore = useSchemaStore()
schemaStore.start()

const emits = defineEmits(['prev', 'next'])

const schema = ref(schemaStore.getSchema)
const form = ref<FormInstance>()
const submitCreateTable = (formEl: FormInstance | undefined) => {
    if (!formEl) return
    formEl.validate((valid: any):any => {
        if (valid) {
            emits('next')
            schemaStore.setSchema(unref(schema))
        } else {
            return false
        }
    })
}
const engines = computed(() => {
    return [
        {
            value: 'InnoDB',
            label: 'InnoDB'
        },
        {
            value: 'MyISAM',
            label: 'MyISAM'
        },
        {
            value: 'Memory',
            label: 'Memory'
        }
    ]
})
</script>
