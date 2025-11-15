<template>
    <div class="w-full sm:w-[90%] mx-auto pb-10">
        <el-form :model="uploadInfo" label-width="120px" ref="form" class="pr-4" :rules="rules">
            <el-form-item label="上传Excel文件" prop="file">
                <el-upload
                    ref="uploadRef"
                    class="upload-demo"
                    drag
                    :action="uploadAction"
                    :headers="uploadHeaders"
                    :name="'file'"
                    :before-upload="beforeUpload"
                    :on-success="handleSuccess"
                    :on-error="handleError"
                    :limit="1"
                    :file-list="fileList"
                    accept=".xlsx,.xls"
                >
                    <el-icon class="el-icon--upload"><upload-filled /></el-icon>
                    <div class="el-upload__text">
                        拖拽文件到此处或 <em>点击上传</em>
                    </div>
                    <template #tip>
                        <div class="el-upload__tip">
                            仅支持 .xlsx 或 .xls 格式的Excel文件，文件大小不超过 10MB
                        </div>
                    </template>
                </el-upload>
                <div v-if="uploadInfo.file" class="mt-2 text-sm text-green-600">
                    已上传文件: {{ fileName }}
                </div>
            </el-form-item>
        </el-form>
        <div class="flex justify-between w-full pl-24 mx-auto mt-4 sm:w-96">
            <el-button class="mt-5" @click="$emit('prev')">{{ $t('system.prev') }}</el-button>
            <el-button class="mt-5" type="primary" @click="submitCreateTable(form)" :loading="loading">{{ $t('system.next') }}</el-button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, unref, computed } from 'vue'
import { usePurchaseOrderStore } from '../store'
import type { FormInstance, UploadInstance, UploadProps, UploadRawFile } from 'element-plus'
import { getAuthToken, getBaseUrl } from '@/support/helper'
import { UploadFilled } from '@element-plus/icons-vue'
import Message from '@/support/message'
import { Code } from '@/enum/app'

const purchaseOrderStore = usePurchaseOrderStore()

const emits = defineEmits(['prev', 'next'])

const uploadInfo = ref(purchaseOrderStore.getUploadInfo)
const form = ref<FormInstance>()
const uploadRef = ref<UploadInstance>()
const loading = ref(false)
const fileList = ref<any[]>([])

const uploadAction = computed(() => {
    const baseUrl = getBaseUrl()
    return `${baseUrl}/upload/file`
})

const uploadHeaders = computed(() => {
    const token = getAuthToken()
    return {
        authorization: token || '',
        'Request-from': 'Dashboard'
    }
})

const fileName = computed(() => {
    if (uploadInfo.value.file) {
        const path = uploadInfo.value.file
        return path.substring(path.lastIndexOf('/') + 1)
    }
    return ''
})

const rules = {
    file: [
        { required: true, message: '请上传Excel文件', trigger: 'change' }
    ]
}

const beforeUpload = (file: UploadRawFile) => {
    const isExcel = file.name.endsWith('.xlsx') || file.name.endsWith('.xls')
    const isLt10M = file.size / 1024 / 1024 < 10

    if (!isExcel) {
        Message.error('只能上传 Excel 文件（.xlsx 或 .xls 格式）!')
        return false
    }
    if (!isLt10M) {
        Message.error('文件大小不能超过 10MB!')
        return false
    }
    return true
}

const handleSuccess = (response: any) => {
    if (response.code === Code.SUCCESS) {
        uploadInfo.value.file = response.data.path
        purchaseOrderStore.setUploadInfo(unref(uploadInfo))
        Message.success('文件上传成功')
    } else {
        Message.error(response.message || '文件上传失败')
    }
}

const handleError = () => {
    Message.error('文件上传失败，请重试')
}

const submitCreateTable = (formEl: FormInstance | undefined) => {
    if (!formEl) return
    formEl.validate((valid: any): any => {
        if (valid) {
            if (!uploadInfo.value.file) {
                Message.error('请先上传Excel文件')
                return false
            }
            emits('next')
            purchaseOrderStore.setUploadInfo(unref(uploadInfo))
        } else {
            return false
        }
    })
}
</script>

<style scoped>
</style>
