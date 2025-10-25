<template>
  <div>
    <div class="flex flex-col justify-between bg-white rounded dark:bg-regal-dark sm:flex-row">
      <div class="flex items-center h-28 w-full justify-between pl-2 pr-2 sm:pr-3">
        <div class="flex flex-col w-[50%] text-center">
          <div class="text-lg text-gray-600 dark:text-gray-400">采购单总数</div>
          <div class="text-gray-400 text dark:text-gray-200">1000</div>
        </div>
        <el-divider direction="vertical" />
        <div class="flex flex-col w-[50%] text-center">
          <div class="text-lg text-gray-600 dark:text-gray-400">采购单总金额</div>
          <div class="text-gray-400 text dark:text-gray-200">1000</div>
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { computed } from 'vue'
import { useUserStore } from '@/stores/modules/user'

const userStore = useUserStore()

const nickname = computed(() => {
  return userStore.getNickname
})

const avatar = computed(() => {
  return userStore.getAvatar
})

function isInRange(compare: number, min: number, max: number) {
  return compare >= min && compare < max
}
</script>
