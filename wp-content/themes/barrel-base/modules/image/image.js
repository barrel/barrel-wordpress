import on from 'dom-event'
import Layzr from 'layzr.js'

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
      let wrapper = image.parentNode
      wrapper.classList.add('image--loaded')
    })
  })

instance
  .on('src:after', el => {
    const wrapper = el.parentNode
    if (!wrapper.classList.contains('js-wrap')) return

    if (!objectFit) {
      const src = el.getAttribute('data-normal')
      wrapper.style.backgroundImage = 'url("' + src + '")'
    }
  })

instance.update().check().handlers(true)

export default (el) => {
  instance
    .update()
    .check()
    .handlers(true)
}
