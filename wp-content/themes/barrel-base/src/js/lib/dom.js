/* global Element, getComputedStyle, CustomEvent */
import { makeArray, curry, getProp, parseOptions, setProp, partial, pipe, map, whileDo } from 'lib/utils'
import Tweezer from 'tweezer.js'
import { Promise } from 'es6-promise'

const _getElementRect = (el) => el.getBoundingClientRect()

/**
 * Add class to a list of elements. This is a curried function.
 *
 * @param {string} className
 * @param {Array<HTMLElement>} els
 * @return {Array<HTMLElement>} Same elements for chaining
 */
const addClass = curry((className, els) =>
  map(
    (el) => {
      el.classList.add(className)
      return el
    },
    els
  ))

const append = curry((parent, els) =>
  map((el) => parent.appendChild(el), els)
)

const prepend = curry((parent, els) =>
  map((el) => parent.insertBefore(el, parent.firstChild), Array.isArray(els) ? els.reverse() : els)
)

const appendHtml = (el, html) => {
  el.insertAdjacentHTML('beforeend', html)
  return el
}

const appendNode = (el, node) => {
  el.parentNode.insertBefore(node, el.nextSibling)
  return el
}

const createNodes = (html) => {
  const wrapper = document.createElement('div')
  appendHtml(wrapper, html)
  return getChildren(wrapper)
}

const preventDefault = (e) => { e.preventDefault(); return e }

/**
 * Set style for an element.
 *
 * This is a curried function.
 *
 * @param {string} k Style name
 * @param {string} v Style value
 * @param {HTMLElement} el Element
 * @return {HTMLElement}
 */
const setStyle = curry((k, v, el) => {
  el.style[ k ] = v
  return el
})

const setHeight = setStyle('height')

const hide = setStyle('display', 'none')

/**
 * Get a style for an element.
 *
 * This is a curried function.
 *
 * @param {string} k Style name
 * @param {HTMLElement} el
 * @return {string}
 */
const getStyle = curry((k, el) => getComputedStyle(el)[ k ])

/**
 * Get actual height of an element, including padding
 *
 * @param {HTMLElement} el
 * @return {number}
 */
const getHeight = getProp('clientHeight')

/**
 * Get scroll top position of an element.
 *
 * Default to getting scrolling position of the viewport
 *
 * @param {HTMLElement} el
 * @return {number}
 */
const getScrollTop = (el = document.documentElement) => el === document.documentElement ? (window.pageYOffset || el.scrollTop) : el.scrollTop

/**
 * Check to see if an element has a specified class.
 *
 * @param {string} className
 * @param {HTMLElement} el
 */
const hasClass = curry((className, el) => el.classList.contains(className))

/**
 * Get attribute value for an element.
 *
 * @param {string} name
 * @param {HTMLElement} el
 * @return {string}
 * @private
 */
const _getAttribute = (name, el) => el.getAttribute(name)

/**
 * Get attribute value for an element.
 *
 * This is a curried function
 *
 * @param {string} name
 * @param {HTMLElement} el
 * @return {string}
 */
const getAttribute = curry(_getAttribute)

const setAttribute = curry((name, value, el) => {
  el.setAttribute(name, value)
  return el
})

/**
 * Get data attribute.
 *
 * @param {string} name
 * @param {HTMLElement} el
 * @return {string} Parsed JSON value or object
 * @private
 */
const _getData = (name, el) => _getAttribute('data-' + name, el)

/**
 * Get data attribute.
 *
 * This is a curried function
 *
 * @param {string} name
 * @param {HTMLElement} el
 * @return {string}
 */
const getData = curry(_getData)

const setData = curry((name, value, el) => setAttribute('data-' + name, value, el))

/**
 * Attach event handler for a single event
 *
 * @param {string} event
 * @param {Function} handler
 * @param {Object} capture
 * @param {HTMLElement} el
 * @returns {HTMLElement}
 * @private
 */
const _on = (event, handler, capture, el) => {
  el.addEventListener(event, handler, capture, el)
  return el
}

const matches = (selector, el) => (
  Element.prototype.matches ||
  Element.prototype.matchesSelector ||
  Element.prototype.mozMatchesSelector ||
  Element.prototype.msMatchesSelector ||
  Element.prototype.oMatchesSelector ||
  Element.prototype.webkitMatchesSelector
).apply(el, [ selector ])

/**
 * Attach event handler for a list of events.
 *
 * This is a curried function
 *
 * @param {Array|Object} els Array or array-like object
 * @param {string} event
 * @param {Function} handler
 * @return {Array<HTMLElement>}
 */
const on = curry((event, handler, els) =>
  map(
    partial(_on, event, handler, {}),
    els
  ))

const onPassive = curry((event, handler, els) =>
  map(
    partial(_on, event, handler, { passive: true }),
    els
  ))

/**
 * Check to see if document has loaded
 *
 * @return {boolean}
 * @private
 */
const _domLoaded = () => document.attachEvent ? document.readyState === 'complete' : document.readyState !== 'loading'

/**
 * Call function when document is ready
 *
 * @param {Function} f
 */
const ready = (f) => _domLoaded() ? f() : on('DOMContentLoaded', f, document)

const closest = curry((selector, el) =>
  whileDo(
    (target) => target && !matches(selector, target) && target.parentNode,
    (target) => target.parentNode === document ? null : target.parentNode,
    el.parentNode
  )
)

const _delegate = (event, handler, selector, el) => _on(
  event,
  (e) => {
    const matchedElement = whileDo(
      (target) => target && target !== document && !matches(selector, target) && target.parentNode,
      (target) => target.parentNode,
      e.target
    )

    if (matchedElement !== document && matches(selector, matchedElement)) {
      handler.apply(el, [ e ])
    }
  },
  {},
  el
)

const delegate = curry((event, handler, selector, els) => map(
  partial(_delegate, event, handler, selector),
  els
))

const remove = (el) => el.parentNode.removeChild(el)

/**
 * Remove class from a list of elements. This is a curried function.
 *
 * @param {string} className
 * @param {Array<HTMLElement>} els
 * @return {Array<HTMLElement>} Same elements for chaining
 */
const removeClass = curry((className, els) =>
  map(
    (el) => { el.classList.remove(className); return el },
    els
  ))

const toggleClass = curry((className, els) =>
  map(
    (el) => { el.classList.toggle(className); return el },
    els
  ))

/**
 * Select one element matching a selector, which is also decendant of a parent element (defaults to document)
 * @param {string} selector
 * @param {HTMLElement|HTMLDocument=} parent
 * @return {HTMLElement}
 */
const select = (selector, parent = document) => parent.querySelector(selector)

/**
 * Select all elements matching a selector, which are also decendant of a parent element (defaults to document)
 * @param {string} selector
 * @param {HTMLElement|HTMLDocument=} parent
 * @return {Array<HTMLElement>}
 */
const selectAll = (selector, parent = document) => makeArray(parent.querySelectorAll(selector))

const getChildren = pipe(
  getProp('children'),
  makeArray
)

const getParent = getProp('parentNode')

const getTopOffset = (el) => _getElementRect(el).top + getScrollTop()

const getTopPosition = getProp('offsetTop')

const scrollTop = (offset, callback, el) => {
  new Tweezer({
    start: getScrollTop(el),
    end: offset
  })
    .on('tick', (v) => {
      (typeof el === 'undefined')
        ? window.scrollTo(0, v)
        : setProp('scrollTop', v, el)
    })
    .on('done', () => {
      if (typeof callback !== 'undefined') {
        callback()
      }
    })
    .begin()
}

/**
 * Trigger reflows so that transition happens after DOM insertion
 */
const triggerReflow = (els) => {
  map(getProp('offsetHeight'), els)
  return els
}

const doesSupportObjectFit = () => Object.keys(document.documentElement.style).reduce((support, prop) => support || /object(?:-f|F)it$/.test(prop), false)

const trigger = (eventName, el) => {
  let event
  let data = {}
  if (typeof eventName === 'object') {
    data = eventName.data
    eventName = eventName.event
  }
  if (typeof window.CustomEvent === 'function') {
    event = new CustomEvent(eventName, { detail: { some: 'data' } })
  } else {
    event = document.createEvent('CustomEvent')
    event.initCustomEvent(eventName, true, true, data)
  }
  el.dispatchEvent(event)
  return el
}

const createElement = (tag) => document.createElement(tag)

const createScriptTag = (src, async = true, defer = true) => (
  pipe(
    createElement,
    setProp('src', src),
    setProp('async', async),
    setProp('defer', defer)
  )('script')
)

const loadScript = (src, async = true, defer = true) => new Promise((resolve, reject) => {
  const tag = createScriptTag(src, async, defer)
  tag.onload = resolve
  tag.onerror = reject
  append(document.body, tag)
})

const getModuleOptions = (moduleName, el, def = {}) => parseOptions(getData(moduleName, el), def)

/**
 * Run method when element focused
 *
 * @param {element}
 * @param {function}
 * @return null
 */
const onFocus = (el, callback) => {
  el && el.addEventListener('focusin', function (e) {
    try {
      callback(el)
    } catch (e) {
      console.warn(e)
    }
  })
}

/**
 * Run method when element unfocused
 *
 * @param {element}
 * @param {function}
 * @return null
 */
const onBlur = (el, callback) => {
  el && el.addEventListener('focusout', function (e) {
    try {
      callback(el)
    } catch (e) {
      console.warn(e)
    }
  })
}

/**
 * Run method when escape key pressed
 *
 * @param {element}
 * @param {function}
 * @return null
 */
const onEscape = (el, callback) => {
  el.addEventListener('keyup', function (e) {
    let eventkey = false

    if (e.key !== undefined) {
      eventkey = e.key
    } else if (e.keyIdentifier !== undefined) {
      eventkey = e.keyIdentifier
    } else if (e.keyCode !== undefined) {
      eventkey = e.keyCode
    }

    if (eventkey === 'Escape' || eventkey === 'Esc' || eventkey === 27) {
      try {
        callback(el)
      } catch (e) {
        console.warn(e)
      }
    }
  })
}

/**
 * Run method when escape key pressed
 *
 * @param {element}
 * @param {function}
 * @return null
 */
const onEnter = (el, callback) => {
  el.addEventListener('keyup', function (e) {
    let eventkey = false

    if (e.key !== undefined) {
      eventkey = e.key
    } else if (e.keyIdentifier !== undefined) {
      eventkey = e.keyIdentifier
    } else if (e.keyCode !== undefined) {
      eventkey = e.keyCode
    }

    if (eventkey === '13') {
      try {
        callback(el)
      } catch (e) {
        console.warn(e)
      }
    }
  })
}

/**
 * We use <label> tags instead of input placeholders to better align with ADA standards
 * This function is used to toggle a class on label tags to control their visibilty
 *
 * @param {HTMLElement} e - The element fo which the label is being controlled
 * @param {String} parentSelector - The classname of the inputs parent conteiner to scope the event
 */
const toggleLabel = (e, parentSelector) => {
  var inputContainer = closest(parentSelector, e.target)

  if ((document.activeElement !== e.target && !e.target.value) || (e.target.localName === 'select' && e.target.value === 'all')) {
    removeClass('hide-label', inputContainer)
  } else {
    addClass('hide-label', inputContainer)
  }
}

export {
  addClass,
  append,
  appendHtml,
  appendNode,
  createElement,
  createNodes,
  closest,
  delegate,
  doesSupportObjectFit,
  getAttribute,
  getChildren,
  getHeight,
  getTopOffset,
  getTopPosition,
  getData,
  getModuleOptions,
  getParent,
  getScrollTop,
  getStyle,
  hasClass,
  hide,
  on,
  onEscape,
  onEnter,
  onPassive,
  onFocus,
  onBlur,
  loadScript,
  matches,
  prepend,
  preventDefault,
  ready,
  remove,
  removeClass,
  scrollTop,
  select,
  selectAll,
  setAttribute,
  setData,
  setHeight,
  setStyle,
  toggleClass,
  toggleLabel,
  triggerReflow,
  trigger
}
