import on from 'dom-event'
import { select } from 'lib/dom'
import Layzr from 'layzr.js'
import throttle from 'lodash.throttle'

const wrapper = select('.wrapper')

const instance = window.layzr = Layzr({
  threshold: 100
})

const doesSupportObjectFit = () => {
  const i = document.createElement('img')
  return ('objectFit' in i.style)
}
const objectFit = doesSupportObjectFit()
if (!objectFit) document.body.classList.add('no-object-fit')

instance
  .on('src:before', image => {
    on(image, 'load', (event) => {
      const imageWrapper = image.parentNode
      imageWrapper.classList.add('image--loaded')
    })
  })

instance
  .on('src:after', el => {
    const imageWrapper = el.parentNode
    if (!imageWrapper.classList.contains('js-wrap')) return

    if (!objectFit) {
      const src = el.getAttribute('data-normal')
      imageWrapper.style.backgroundImage = 'url("' + src + '")'
      imageWrapper.classList.add('image--loaded')
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
