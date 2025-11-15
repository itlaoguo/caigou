import { defineStore } from 'pinia'

/**
 * 基礎信息
 */

export interface BaseInfo {
  enter_way: string
  name: string
  description: string
} 

export interface UploadInfo {
  file: string
}

export interface Product {
  product_id: string
  product_name: string
  product_price: number
  product_quantity: number
  product_total: number
  product_remark: string
}

interface CreatePurchaseOrder {
  baseInfo: BaseInfo
  uploadInfo: UploadInfo
  productList: Product[]
  is_finished: boolean
}

/**
 * useSchemaStore
 */
export const usePurchaseOrderStore= defineStore('purchaseOrderStore', {
  state(): CreatePurchaseOrder {
    return {
      // 基本信息
      baseInfo: Object.assign({
        enter_way: 'excel',
        name: '',
        description: ''
      }),
      // 上傳信息
      uploadInfo: Object.assign({
        file: '',
      }),
      // 商品信息
      productList: [] as Product[],

      // is finished
      is_finished: false,
    }
  },

  // store getters
  getters: {
    getBaseInfo(): BaseInfo {
      return this.baseInfo
    },
    getUploadInfo(): UploadInfo {
      return this.uploadInfo
    },

    getProductList(): Product[] {
      return this.productList
    },

    getFinished(): boolean {
      return this.is_finished
    },
  },

  // store actions
  actions: {
    // set schema
    setBaseInfo(baseInfo: BaseInfo): void {
      this.baseInfo = baseInfo
    },

    setUploadInfo(uploadInfo: UploadInfo): void {
      this.uploadInfo = uploadInfo
    },

    setProductList(productList: Array<Product>): void {
      this.productList = productList
    },
    // add structure
    addProduct(product: Product): void {
        
        this.productList.push(product)
    },
    
    /**
     * finished
     */
    finished(): void {
      this.$reset()
      this.is_finished = true
    },

    /**
     * unfinished
     */
    start(): void {
      this.is_finished = false
    },
  },
})
