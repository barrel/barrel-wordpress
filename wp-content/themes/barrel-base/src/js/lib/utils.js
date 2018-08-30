/**
 * Function that always returns True
 */
const returnTrue = () => true

/**
 * Function that always returns false
 */
const returnFalse = () => false

/**
 * Identity function
 * @param {*} val
 * @return {*}
 */
const identity = (val) => val

const not = (val) => !val

/**
 * Returns a curried function of the provided function, so that:
 *
 * f(a, b, c) = f(a, b)(c) = f(a)(b)(c) = f(a)(b, c)
 *
 * @param {Function} f
 * @param {..*} Initial parameters
 * @return {Function} The curried function
 */
const curry = (f, ...args) => args.length >= f.length ? f(...args) : curry.bind(this, f, ...args)

const allPass = (fs) => (...args) => {
  for (let i = 0; i < fs.length; i++) {
    if (!fs[ i ].apply(this, args)) {
      return false
    }
  }
  return true
}

/**
 * Takes in a list of predicates and return a function that will
 * pass its arguments through each of the predicates, returning
 * true if any predicate is satisfied.
 *
 * @param {Array<Function>}
 * @return {Function}
 */
const anyPass = (fs) => (...args) => {
  for (let i = 0; i < fs.length; i++) {
    if (fs[ i ].apply(this, args)) {
      return true
    }
  }

  return false
}

/**
 * Encapsulates switch/case or if/else logic.
 *
 * Takes a list of [predicate, transformer] pairs.
 *
 * The returned function passes its arguments to predicates, evaluates them, and execute the matched transformer (passing the
 * same arguments). If there's no matched predicate, return undefined.
 *
 * @param pairs Pairs of [predicate, transformer]
 * @return {Function} Encapsulated function
 */
const cond = (pairs) => (...args) => {
  for (let i = 0; i < pairs.length; i++) {
    if (pairs[ i ][ 0 ].apply(this, args)) {
      return pairs[ i ][ 1 ].apply(this, args)
    }
  }

  return undefined
}

/**
 * Encapsulate if/else logic. Basically a wrapper of `cond`.
 *
 * @param p Predicate
 * @param {Function} fT Function to call when predicate is true
 * @param {Function} fF Function to call when predicate is false
 * @return {Function} Encapsulated function
 */
const ifElse = (p, fT, fF) => cond([ [ p, fT ], [ returnTrue, fF ] ])

const when = (p, f) => cond([ [ p, f ], [ returnTrue, identity ] ])

/**
 * Functional wrapper for array map function.
 *
 * @param {Function} f
 * @param {*} arr
 */
const map = curry((f, arr) => Array.isArray(arr) ? arr.map(f) : f(arr))

const filter = curry((f, arr) => Array.isArray(arr) ? arr.filter(f) : (f(arr) ? arr : undefined))

/**
 * Partial application
 * @param {Function} f
 * @param {..*} args Initial parameters
 */
const partial = (f, ...args) => f.bind(this, ...args)

/**
 * Transform a value by chaining a list of function from left to right
 *
 * @param val
 * @param {..Function} funcs
 * @return {*}
 */
const pipe = (...funcs) => function (val) { return funcs.reduce((acc, f) => f.apply(this, [ acc ]), val) }

const always = (val) => partial(val)

/**
 * Get property of an object.
 *
 * This is a curried function.
 *
 * @param {string} prop
 * @param {Object} obj
 * @return {*}
 */
const getProp = curry((prop, obj) => {
  return obj[prop]
})

const lt = curry((b, a) => a < b)

const setProp = curry((prop, value, obj) => {
  obj[ prop ] = value
  return obj
})

/**
 * Delay execution of callback used on individual items of a list.
 *
 * This function is curried.
 *
 * @param {number} initial Initial timeout
 * @param {number} step Delay per execution
 * @param {Function} callback Callback
 * @param {Array} items
 * @private
 */
const _staggerCallback = (initial, step, callback, items) => (
  items.reduce((delay, item) => {
    setTimeout(() => callback(item), delay)
    return delay + step
  }, initial)
)

/**
 * Delay execution of callback used on individual items of a list.
 *
 * This function is curried.
 *
 * @param {number} initial Initial timeout
 * @param {number} step Delay per execution
 * @param {Function} callback Callback
 * @param {Array} items
 */
const staggerCallback = curry(_staggerCallback)

/**
 * Encapsulate try/catch logic.
 *
 * Takes a pair of tryer and catcher functions. User must ensure tryer and catcher return the same
 * type so that chaining / composing works.
 *
 * Returns a function that can take arguments, which will be passed to both tryer and catcher.
 *
 * @param {Function} tryer
 * @param {Function} catcher
 * @return {Function} Encapsulated function
 */
const tryCatch = (tryer, catcher) => (...args) => {
  try {
    return tryer(...args)
  } catch (e) {
    return catcher(e, ...args)
  }
}

const whileDo = (pred, fn, initial) => pred(initial) ? whileDo(pred, fn, fn(initial)) : initial

const divide = curry((b, a) => a / b)
const multiply = curry((b, a) => a * b)

/**
 * Check if an array contains an item.
 *
 * This is a curried wrapper for Array.prototype.indexOf
 *
 * @param {Array}
 * @param {*}
 * @return {boolean}
 */
const inArray = curry((array, item) => array.indexOf(item) !== -1)

/**
 * Get the nth element of an array
 *
 * @param {number}
 * @param {Array}
 * @return {*}
 */
const nth = curry((index, array) => index < 0 ? array[ array.length + index ] : array[ index ])

/**
 * Get the nth element of an array
 *
 * @param {Array}
 * @return {*}
 */
const first = nth(1)

const last = nth(-1)
const add = curry((b, a) => a + b)
const _add1ToLast = pipe(last, add(1))

const range = (from, to) => whileDo(
  pipe(_add1ToLast, lt(to)),
  (array) => [ ...array, _add1ToLast(array) ],
  [ from ]
)

const flipArgs = (f) => curry((arg1, arg2) => f.apply(this, [ arg2, arg1 ]))

const count = (arr) => arr.length

/**
 * Create an array out of an array-like object
 *
 * @param {Object} Array-like object
 * @return {Array} Array
 */
const makeArray = (arrayLike) => Array.prototype.slice.call(arrayLike)

/**
 * Log a value to console
 *
 * @param {*} val
 * @returns {*}
 */
const log = (val) => {
  console.log(val)
  return val
}

const logWrap = (fn) => (...args) => log(fn.apply(this, args))

const logArgs = (fn) => (...args) => {
  log(args)
  return fn.apply(this, args)
}

const debounce = (callback, wait, context = this) => {
  let timeout = null
  let callbackArgs = null

  const later = () => callback.apply(context, callbackArgs)

  return function () {
    callbackArgs = arguments
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

const then = curry((f, promise) => promise.then(f))
const reject = curry((f, promise) => promise.catch(f))

const parseOptions = (string, def = {}) => {
  let options = {}
  try {
    options = JSON.parse(string)
  } catch (e) {
    console.warn('Invalid option JSON string.')
    console.trace()

    return def
  }

  return Object.assign({}, def, options)
}

/**
 * Throttle a function so that it fires once every { threshold }
 * @param {Function} fn
 * @param {Integer} threshold
 * @param {FunctionContext} scope
 */

const throttle = (fn, threshold, scope) => {
  if (!threshold) {
    threshold = 250
  }

  var last, deferTimer

  return function () {
    var context = scope || this
    var now = +new Date()
    var args = arguments

    if (last && now < last + threshold) {
      // hold on to it
      clearTimeout(deferTimer)
      deferTimer = setTimeout(function () {
        last = now
        fn.apply(context, args)
      }, threshold)
    } else {
      last = now
      fn.apply(context, args)
    }
  }
}

const doesSupportObjectFit = () => {
  const i = document.createElement('img')
  return ('objectFit' in i.style)
}

const doesSupportObjectPosition = () => {
  const i = document.createElement('img')
  return ('objectPosition' in i.style)
}

export {
  add,
  allPass,
  always,
  anyPass,
  cond,
  count,
  debounce,
  doesSupportObjectFit,
  doesSupportObjectPosition,
  log,
  logArgs,
  logWrap,
  makeArray,
  curry,
  divide,
  filter,
  first,
  flipArgs,
  getProp,
  identity,
  ifElse,
  inArray,
  parseOptions,
  last,
  lt,
  map,
  multiply,
  not,
  nth,
  partial,
  pipe,
  range,
  returnTrue,
  returnFalse,
  setProp,
  staggerCallback,
  then,
  throttle,
  reject,
  tryCatch,
  when,
  whileDo
}
