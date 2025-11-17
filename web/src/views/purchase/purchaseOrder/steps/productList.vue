<template>
    <div class="w-full sm:w-[90%] mx-auto pb-10">
        <!-- 产品列表 -->
        <div class="mb-6">
            <!-- <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">采购产品列表</h3>
            </div> -->
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
            <div v-if="productList.length === 0" class="py-8 text-center text-gray-500">
                暂无产品数据，请先上传Excel文件
            </div>
            <!-- <div v-if="productList.length > 0" class="mt-4 text-right">
                <span class="text-lg font-semibold">
                    总计: <span class="text-red-600">¥{{ totalAmount.toFixed(2) }}</span>
                </span>
            </div> -->
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
import { useRouter } from 'vue-router'
import { usePurchaseOrderStore, type Product } from '../store'
import http from '@/support/http'
import Message from '@/support/message'
import { Code } from '@/enum/app'

const router = useRouter()
const purchaseOrderStore = usePurchaseOrderStore()

const emits = defineEmits(['prev', 'next', 'reset'])

const baseInfo = computed(() => purchaseOrderStore.getBaseInfo)
const uploadInfo = computed(() => purchaseOrderStore.getUploadInfo)
const productList = computed(() => purchaseOrderStore.getProductList)

const loading = ref(false)
const submitting = ref(false)


// 解析Excel文件，获取产品列表
const parseExcel = async () => {
    if (!uploadInfo.value.path) {
        Message.warning('请先上传Excel文件')
        return
    }

    if (!baseInfo.value.name) {
        Message.warning('请先填写采购单基本信息')
        return
    }

    loading.value = true
    try {
        // 调用后端接口解析Excel，传递第一步和第二步的信息
        const response = await http.get('purchase/prepare', {
            path: uploadInfo.value.path
        })
        console.log(response,'--------purchase/prepare------------');

        if (response.data.code === Code.SUCCESS) {
            const products = response.data.data || []
            // 将后端返回的数据转换为Product格式
            const formattedProducts: Product[] = products.map((item: any) => ({
                product_id: item.productId,
                product_title: item.productTitle,
                sku_id: item.skuId,
                sku_title: item.skuTitle,
                price: item.price,
                product_pic_url: item.productPicUrl,
                purchaser_id: item.purchaserId,
                quantity: item.quantity,
                can_sell: item.canSell,
                address_detail: item.addressDetail,
                receiver: item.receiver,
                receiver_phone: item.receiverPhone
            }))
            purchaseOrderStore.setProductList(formattedProducts)
            Message.success('Excel文件解析成功')
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

    if (!baseInfo.value.name) {
        Message.warning('请先填写采购单基本信息')
        return
    }

    submitting.value = true
    try {
        // 组装提交数据：第一步的基本信息 + 第二步的文件信息 + 第三步的产品列表
        const formData = {
            name: baseInfo.value.name,
            enter_way: baseInfo.value.enter_way,
            description: baseInfo.value.description,
            path: uploadInfo.value.path,
            products: productList.value
        }

        const response = await http.post('purchase/order', formData)

        if (response.data.code === Code.SUCCESS) {
            Message.success(response.data.message || '采购单创建成功')
            // 重置所有步骤的数据
            purchaseOrderStore.resetAll()
            // 重置步骤指示器
            emits('reset')
            // 延迟一下让用户看到成功消息
            setTimeout(() => {
                router.push('/purchase/purchase')
            }, 1500)
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
    // 进入第三步时，自动调用接口获取产品列表
    if (uploadInfo.value.path && productList.value.length === 0) {
        parseExcel()
    }
})
</script>

<style scoped>
</style>
