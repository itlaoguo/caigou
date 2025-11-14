<template>
    <div class="w-full sm:w-[90%] mx-auto pb-10">
        <!-- 采购单基本信息展示 -->
        <div class="mb-6">
            <h3 class="mb-4 text-lg font-semibold">采购单基本信息</h3>
            <el-descriptions :column="2" border>
                <el-descriptions-item label="商品录入方式">
                    {{ baseInfo.enter_way === 'excel' ? 'Excel录入' : baseInfo.enter_way }}
                </el-descriptions-item>
                <el-descriptions-item label="名称">
                    {{ baseInfo.name || '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="任务描述">
                    {{ baseInfo.description || '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="下单模式">
                    {{ baseInfo.mode || '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="下单账号">
                    {{ baseInfo.account || '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="Excel文件">
                    {{ uploadInfo.file ? fileName : '-' }}
                </el-descriptions-item>
            </el-descriptions>
        </div>

        <!-- 产品列表 -->
        <div class="mb-6">
            <h3 class="mb-4 text-lg font-semibold">采购产品列表</h3>
            <el-table :data="productList" border v-loading="loading" style="width: 100%">
                <el-table-column type="index" label="序号" width="60" align="center" />
                <el-table-column prop="product_id" label="产品ID" width="120" />
                <el-table-column prop="product_name" label="产品名称" min-width="200" />
                <el-table-column prop="product_price" label="单价" width="120" align="right">
                    <template #default="scope">
                        ¥{{ scope.row.product_price?.toFixed(2) || '0.00' }}
                    </template>
                </el-table-column>
                <el-table-column prop="product_quantity" label="数量" width="100" align="right" />
                <el-table-column prop="product_total" label="小计" width="120" align="right">
                    <template #default="scope">
                        ¥{{ scope.row.product_total?.toFixed(2) || '0.00' }}
                    </template>
                </el-table-column>
                <el-table-column prop="product_remark" label="备注" min-width="150" />
            </el-table>
            <div v-if="productList.length === 0" class="py-8 text-center text-gray-500">
                暂无产品数据，请先上传Excel文件
            </div>
            <div v-if="productList.length > 0" class="mt-4 text-right">
                <span class="text-lg font-semibold">
                    总计: <span class="text-red-600">¥{{ totalAmount.toFixed(2) }}</span>
                </span>
            </div>
        </div>

        <!-- 操作按钮 -->
        <div class="flex justify-between w-full pl-24 mx-auto mt-4 sm:w-96">
            <el-button class="mt-5" @click="$emit('prev')">{{ $t('system.prev') }}</el-button>
            <el-button 
                class="mt-5" 
                type="primary" 
                @click="submitForm" 
                :loading="submitting"
                :disabled="productList.length === 0"
            >
                提交采购单
            </el-button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, unref } from 'vue'
import { usePurchaseOrderStore, type Product } from '../store'
import http from '@/support/http'
import Message from '@/support/message'
import { Code } from '@/enum/app'
import { getFilename } from '@/support/helper'

const purchaseOrderStore = usePurchaseOrderStore()

const emits = defineEmits(['prev', 'next'])

const baseInfo = computed(() => purchaseOrderStore.getBaseInfo)
const uploadInfo = computed(() => purchaseOrderStore.getUploadInfo)
const productList = computed(() => purchaseOrderStore.getProductList)

const loading = ref(false)
const submitting = ref(false)

const fileName = computed(() => {
    if (uploadInfo.value.file) {
        return getFilename(uploadInfo.value.file)
    }
    return ''
})

const totalAmount = computed(() => {
    return productList.value.reduce((sum, item) => {
        return sum + (item.product_total || 0)
    }, 0)
})

// 解析Excel文件，获取产品列表
const parseExcel = async () => {
    if (!uploadInfo.value.file) {
        return
    }

    loading.value = true
    try {
        // 调用后端接口解析Excel
        // 这里假设后端有一个接口可以解析Excel并返回产品列表
        // 如果后端没有这个接口，可能需要前端使用xlsx库解析
        const response = await http.post('purchase/prepare', {
            file: uploadInfo.value.file,
            baseInfo: baseInfo.value
        })

        if (response.data.code === Code.SUCCESS) {
            const products = response.data.data || []
            // 将后端返回的数据转换为Product格式
            const formattedProducts: Product[] = products.map((item: any) => ({
                product_id: item.product_id || item.id || '',
                product_name: item.product_name || item.name || '',
                product_price: parseFloat(item.product_price || item.price || 0),
                product_quantity: parseInt(item.product_quantity || item.quantity || 0),
                product_total: parseFloat(item.product_total || item.total || 0),
                product_remark: item.product_remark || item.remark || ''
            }))
            purchaseOrderStore.setProductList(formattedProducts)
        } else {
            Message.error(response.data.message || '解析Excel文件失败')
        }
    } catch (error: any) {
        console.error('解析Excel失败:', error)
        Message.error(error.message || '解析Excel文件失败，请检查文件格式')
    } finally {
        loading.value = false
    }
}

// 提交采购单
const submitForm = async () => {
    if (productList.value.length === 0) {
        Message.warning('请先上传并解析Excel文件')
        return
    }

    submitting.value = true
    try {
        const formData = {
            ...baseInfo.value,
            file: uploadInfo.value.file,
            products: productList.value
        }

        const response = await http.post('purchase/order', formData)

        if (response.data.code === Code.SUCCESS) {
            Message.success('采购单创建成功')
            purchaseOrderStore.finished()
            emits('next')
            // 可以在这里触发父组件关闭对话框或跳转
        } else {
            Message.error(response.data.message || '提交失败')
        }
    } catch (error: any) {
        console.error('提交失败:', error)
        Message.error(error.message || '提交失败，请重试')
    } finally {
        submitting.value = false
    }
}

onMounted(() => {
    // 如果已经有文件，自动解析
    if (uploadInfo.value.file && productList.value.length === 0) {
        parseExcel()
    }
})
</script>

<style scoped>
</style>
