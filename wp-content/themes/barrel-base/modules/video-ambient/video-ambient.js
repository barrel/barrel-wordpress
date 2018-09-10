import { select, addClass, removeClass } from 'lib/dom'
import { throttle, doesSupportObjectFit, doesSupportObjectPosition } from 'lib/utils'

const Video = (el) => {
  const src = el.getAttribute('data-src')
  const $el = el
  const supportsObjectFit = doesSupportObjectFit()
  const supportsObjectPosition = doesSupportObjectPosition()
  const videoContainer = select('.video-container')
  const playTrigger = select('.play-btn svg')
  const playTriggerContainer = select('.play-btn')
  el.src = src

  if (!supportsObjectPosition) {
    addClass('no-object-position', document.body)
  }

  el.onplay = function () {
    addClass('video--playing', videoContainer)
  }

  window.addEventListener('load', function () {
    setTimeout(function () {
      if (el.currentTime === 0) {
        addClass('showing', playTriggerContainer)
      }
    }, 400)
  })

  playTrigger.onclick = function () {
    el.src = src
    el.play()
    removeClass('showing', playTriggerContainer)
  }

  if (!supportsObjectFit || !supportsObjectPosition) {
    objectFit(el)
    window.addEventListener('resize', throttle(function () {
      objectFit($el)
    }, 250))
  }
}

const objectFit = (el) => {
  const hero = select('.hero')
  const heroAspect = hero.clientHeight / hero.clientWidth
  const mediaAspect = el.clientHeight / el.clientWidth

  el.setAttribute('style', '')

  if (mediaAspect <= heroAspect) {
    el.style.maxHeight = '100%'
  }
  if (mediaAspect > heroAspect) {
    el.style.maxWidth = '100%'
  }
}

export default Video
