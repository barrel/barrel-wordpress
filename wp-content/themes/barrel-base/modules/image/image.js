import on from 'dom-event'
import { select, addClass, doesSupportObjectFit } from 'lib/dom'
import Layzr from 'layzr.js'
import throttle from 'lodash.throttle'

const wrapper = select('.wrapper')
const body = document.body
const LOADED_CLASS = 'image--loaded'

const instance = window.layzr = Layzr({
  threshold: 100
})

const objectFit = doesSupportObjectFit()
if (!objectFit) addClass('no-object-fit', body)

instance
  .on('src:before', image => {
    on(image, 'load', (event) => {
      const imageWrapper = image.parentNode
      addClass(LOADED_CLASS, imageWrapper)
    })
  })

instance
  .on('src:after', el => {
    const imageWrapper = el.parentNode
    if (!imageWrapper.classList.contains('js-wrap')) return

    if (!objectFit) {
      const src = el.getAttribute('data-normal')
      imageWrapper.style.backgroundImage = 'url("' + src + '")'
      addClass(LOADED_CLASS, imageWrapper)
    }
  })

const updateLazyLoad = () => instance.update().check()

updateLazyLoad().handlers(true)

if (wrapper) {
  on(wrapper, 'scroll', throttle(updateLazyLoad, 100))
}

export default (el) => {
}

export {
  updateLazyLoad
}
