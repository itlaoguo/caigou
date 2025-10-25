import { createApp } from 'vue'
import type { App as app } from 'vue'
import App from '@/App.vue'
import router, { bootstrapRouter } from '@/router'
import ElementPlus from 'element-plus'
import zh from 'element-plus/es/locale/lang/zh-cn'
import en from 'element-plus/es/locale/lang/en'

import { bootstrapStore } from '@/stores'
import Cache from './cache'
import { bootstrapI18n } from '@/i18n'
import guard from '@/router/guard'
import { bootstrapDirectives } from '@/directives'
import { Language } from 'element-plus/es/locale'
import { bootstrapCatchForm } from '@/components/catchForm'
import http from '@/support/http'

/**
 * catchadmin
 */
export default class CatchAdmin {
  protected app: app

  protected element: string

  /**
   * construct
   *
   * @param ele
   */
  constructor(ele: string = '#app') {
    this.app = createApp(App)
    this.element = ele
  }

  /**
   * admin boot
   */
  bootstrap(): void {
    this.useElementPlus().usePinia().useI18n().installDirectives().bootstrapCatchForm().useRouter().mount()
  }

  /**
   * 挂载节点
   *
   * @returns
   */
  protected mount(): void {
    this.app.mount(this.element)
  }

  /**
   * 加载路由
   *
   * @returns
   */
  protected useRouter(): CatchAdmin {
    guard(router)

    bootstrapRouter(this.app)

    return this
  }

  /**
   * ui
   *
   * @returns
   */
  protected useElementPlus(): CatchAdmin {
    const languages: Record<string, Language> = {
      zh,
      en
    }

    const language = Cache.get('language') || 'zh'
    this.app.use(ElementPlus, {
      locale: languages[language]
    })
    return this
  }

  /**
   * use pinia
   */
  protected usePinia(): CatchAdmin {
    bootstrapStore(this.app)

    return this
  }

  /**
   * use i18n
   */
  protected useI18n(): CatchAdmin {
    bootstrapI18n(this.app)

    return this
  }

  /**
   * install directives
   *
   * @protected
   */
  protected installDirectives(): CatchAdmin {
    bootstrapDirectives(this.app)

    return this
  }

  /**
   *
   * @returns
   */
  protected bootstrapCatchForm(): CatchAdmin {
    bootstrapCatchForm(this.app, { http })

    return this
  }
}
