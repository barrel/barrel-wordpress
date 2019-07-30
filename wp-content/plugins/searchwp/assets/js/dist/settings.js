'use strict';var _typeof2=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};function _defineProperty(obj,key,value){if(key in obj){Object.defineProperty(obj,key,{value:value,enumerable:true,configurable:true,writable:true});}else{obj[key]=value;}return obj;}/******/(function(modules){// webpackBootstrap
/******/// The module cache
/******/var installedModules={};/******//******/// The require function
/******/function __webpack_require__(moduleId){/******//******/// Check if module is in cache
/******/if(installedModules[moduleId]){/******/return installedModules[moduleId].exports;/******/}/******/// Create a new module (and put it into the cache)
/******/var module=installedModules[moduleId]={/******/i:moduleId,/******/l:false,/******/exports:{}/******/};/******//******/// Execute the module function
/******/modules[moduleId].call(module.exports,module,module.exports,__webpack_require__);/******//******/// Flag the module as loaded
/******/module.l=true;/******//******/// Return the exports of the module
/******/return module.exports;/******/}/******//******//******/// expose the modules object (__webpack_modules__)
/******/__webpack_require__.m=modules;/******//******/// expose the module cache
/******/__webpack_require__.c=installedModules;/******//******/// define getter function for harmony exports
/******/__webpack_require__.d=function(exports,name,getter){/******/if(!__webpack_require__.o(exports,name)){/******/Object.defineProperty(exports,name,{/******/configurable:false,/******/enumerable:true,/******/get:getter/******/});/******/}/******/};/******//******/// getDefaultExport function for compatibility with non-harmony modules
/******/__webpack_require__.n=function(module){/******/var getter=module&&module.__esModule?/******/function getDefault(){return module['default'];}:/******/function getModuleExports(){return module;};/******/__webpack_require__.d(getter,'a',getter);/******/return getter;/******/};/******//******/// Object.prototype.hasOwnProperty.call
/******/__webpack_require__.o=function(object,property){return Object.prototype.hasOwnProperty.call(object,property);};/******//******/// __webpack_public_path__
/******/__webpack_require__.p="";/******//******/// Load entry module and return exports
/******/return __webpack_require__(__webpack_require__.s=28);/******/})(/************************************************************************//******/[/* 0 *//***/function(module,exports){/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/// css base code, injected by the css-loader
module.exports=function(useSourceMap){var list=[];// return the list of modules as css string
list.toString=function toString(){return this.map(function(item){var content=cssWithMappingToString(item,useSourceMap);if(item[2]){return"@media "+item[2]+"{"+content+"}";}else{return content;}}).join("");};// import a list of modules into the list
list.i=function(modules,mediaQuery){if(typeof modules==="string")modules=[[null,modules,""]];var alreadyImportedModules={};for(var i=0;i<this.length;i++){var id=this[i][0];if(typeof id==="number")alreadyImportedModules[id]=true;}for(i=0;i<modules.length;i++){var item=modules[i];// skip already imported module
// this implementation is not 100% perfect for weird media query combinations
//  when a module is imported multiple times with different media queries.
//  I hope this will never occur (Hey this way we have smaller bundles)
if(typeof item[0]!=="number"||!alreadyImportedModules[item[0]]){if(mediaQuery&&!item[2]){item[2]=mediaQuery;}else if(mediaQuery){item[2]="("+item[2]+") and ("+mediaQuery+")";}list.push(item);}}};return list;};function cssWithMappingToString(item,useSourceMap){var content=item[1]||'';var cssMapping=item[3];if(!cssMapping){return content;}if(useSourceMap&&typeof btoa==='function'){var sourceMapping=toComment(cssMapping);var sourceURLs=cssMapping.sources.map(function(source){return'/*# sourceURL='+cssMapping.sourceRoot+source+' */';});return[content].concat(sourceURLs).concat([sourceMapping]).join('\n');}return[content].join('\n');}// Adapted from convert-source-map (MIT)
function toComment(sourceMap){// eslint-disable-next-line no-undef
var base64=btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap))));var data='sourceMappingURL=data:application/json;charset=utf-8;base64,'+base64;return'/*# '+data+' */';}/***/},/* 1 *//***/function(module,exports,__webpack_require__){/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
  Modified by Evan You @yyx990803
*/var hasDocument=typeof document!=='undefined';if(typeof DEBUG!=='undefined'&&DEBUG){if(!hasDocument){throw new Error('vue-style-loader cannot be used in a non-browser environment. '+"Use { target: 'node' } in your Webpack config to indicate a server-rendering environment.");}}var listToStyles=__webpack_require__(38);/*
type StyleObject = {
  id: number;
  parts: Array<StyleObjectPart>
}

type StyleObjectPart = {
  css: string;
  media: string;
  sourceMap: ?string
}
*/var stylesInDom={/*
  [id: number]: {
    id: number,
    refs: number,
    parts: Array<(obj?: StyleObjectPart) => void>
  }
*/};var head=hasDocument&&(document.head||document.getElementsByTagName('head')[0]);var singletonElement=null;var singletonCounter=0;var isProduction=false;var noop=function noop(){};var options=null;var ssrIdKey='data-vue-ssr-id';// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
// tags it will allow on a page
var isOldIE=typeof navigator!=='undefined'&&/msie [6-9]\b/.test(navigator.userAgent.toLowerCase());module.exports=function(parentId,list,_isProduction,_options){isProduction=_isProduction;options=_options||{};var styles=listToStyles(parentId,list);addStylesToDom(styles);return function update(newList){var mayRemove=[];for(var i=0;i<styles.length;i++){var item=styles[i];var domStyle=stylesInDom[item.id];domStyle.refs--;mayRemove.push(domStyle);}if(newList){styles=listToStyles(parentId,newList);addStylesToDom(styles);}else{styles=[];}for(var i=0;i<mayRemove.length;i++){var domStyle=mayRemove[i];if(domStyle.refs===0){for(var j=0;j<domStyle.parts.length;j++){domStyle.parts[j]();}delete stylesInDom[domStyle.id];}}};};function addStylesToDom(styles/* Array<StyleObject> */){for(var i=0;i<styles.length;i++){var item=styles[i];var domStyle=stylesInDom[item.id];if(domStyle){domStyle.refs++;for(var j=0;j<domStyle.parts.length;j++){domStyle.parts[j](item.parts[j]);}for(;j<item.parts.length;j++){domStyle.parts.push(addStyle(item.parts[j]));}if(domStyle.parts.length>item.parts.length){domStyle.parts.length=item.parts.length;}}else{var parts=[];for(var j=0;j<item.parts.length;j++){parts.push(addStyle(item.parts[j]));}stylesInDom[item.id]={id:item.id,refs:1,parts:parts};}}}function createStyleElement(){var styleElement=document.createElement('style');styleElement.type='text/css';head.appendChild(styleElement);return styleElement;}function addStyle(obj/* StyleObjectPart */){var update,remove;var styleElement=document.querySelector('style['+ssrIdKey+'~="'+obj.id+'"]');if(styleElement){if(isProduction){// has SSR styles and in production mode.
// simply do nothing.
return noop;}else{// has SSR styles but in dev mode.
// for some reason Chrome can't handle source map in server-rendered
// style tags - source maps in <style> only works if the style tag is
// created and inserted dynamically. So we remove the server rendered
// styles and inject new ones.
styleElement.parentNode.removeChild(styleElement);}}if(isOldIE){// use singleton mode for IE9.
var styleIndex=singletonCounter++;styleElement=singletonElement||(singletonElement=createStyleElement());update=applyToSingletonTag.bind(null,styleElement,styleIndex,false);remove=applyToSingletonTag.bind(null,styleElement,styleIndex,true);}else{// use multi-style-tag mode in all other cases
styleElement=createStyleElement();update=applyToTag.bind(null,styleElement);remove=function remove(){styleElement.parentNode.removeChild(styleElement);};}update(obj);return function updateStyle(newObj/* StyleObjectPart */){if(newObj){if(newObj.css===obj.css&&newObj.media===obj.media&&newObj.sourceMap===obj.sourceMap){return;}update(obj=newObj);}else{remove();}};}var replaceText=function(){var textStore=[];return function(index,replacement){textStore[index]=replacement;return textStore.filter(Boolean).join('\n');};}();function applyToSingletonTag(styleElement,index,remove,obj){var css=remove?'':obj.css;if(styleElement.styleSheet){styleElement.styleSheet.cssText=replaceText(index,css);}else{var cssNode=document.createTextNode(css);var childNodes=styleElement.childNodes;if(childNodes[index])styleElement.removeChild(childNodes[index]);if(childNodes.length){styleElement.insertBefore(cssNode,childNodes[index]);}else{styleElement.appendChild(cssNode);}}}function applyToTag(styleElement,obj){var css=obj.css;var media=obj.media;var sourceMap=obj.sourceMap;if(media){styleElement.setAttribute('media',media);}if(options.ssrId){styleElement.setAttribute(ssrIdKey,obj.id);}if(sourceMap){// https://developer.chrome.com/devtools/docs/javascript-debugging
// this makes source maps inside style tags work properly in Chrome
css+='\n/*# sourceURL='+sourceMap.sources[0]+' */';// http://stackoverflow.com/a/26603875
css+='\n/*# sourceMappingURL=data:application/json;base64,'+btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap))))+' */';}if(styleElement.styleSheet){styleElement.styleSheet.cssText=css;}else{while(styleElement.firstChild){styleElement.removeChild(styleElement.firstChild);}styleElement.appendChild(document.createTextNode(css));}}/***/},/* 2 *//***/function(module,exports){/* globals __VUE_SSR_CONTEXT__ */// IMPORTANT: Do NOT use ES2015 features in this file.
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.
module.exports=function normalizeComponent(rawScriptExports,compiledTemplate,functionalTemplate,injectStyles,scopeId,moduleIdentifier/* server only */){var esModule;var scriptExports=rawScriptExports=rawScriptExports||{};// ES6 modules interop
var type=_typeof2(rawScriptExports.default);if(type==='object'||type==='function'){esModule=rawScriptExports;scriptExports=rawScriptExports.default;}// Vue.extend constructor export interop
var options=typeof scriptExports==='function'?scriptExports.options:scriptExports;// render functions
if(compiledTemplate){options.render=compiledTemplate.render;options.staticRenderFns=compiledTemplate.staticRenderFns;options._compiled=true;}// functional template
if(functionalTemplate){options.functional=true;}// scopedId
if(scopeId){options._scopeId=scopeId;}var hook;if(moduleIdentifier){// server build
hook=function hook(context){// 2.3 injection
context=context||// cached call
this.$vnode&&this.$vnode.ssrContext||// stateful
this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext;// functional
// 2.2 with runInNewContext: true
if(!context&&typeof __VUE_SSR_CONTEXT__!=='undefined'){context=__VUE_SSR_CONTEXT__;}// inject component styles
if(injectStyles){injectStyles.call(this,context);}// register component module identifier for async chunk inferrence
if(context&&context._registeredComponents){context._registeredComponents.add(moduleIdentifier);}};// used by ssr in case component is cached and beforeCreate
// never gets called
options._ssrRegister=hook;}else if(injectStyles){hook=injectStyles;}if(hook){var functional=options.functional;var existing=functional?options.render:options.beforeCreate;if(!functional){// inject component registration as beforeCreate hook
options.beforeCreate=existing?[].concat(existing,hook):[hook];}else{// for template-only hot-reload because in that case the render fn doesn't
// go through the normalizer
options._injectStyles=hook;// register for functioal component in vue file
options.render=function renderWithStyleInjection(h,context){hook.call(context);return existing(h,context);};}}return{esModule:esModule,exports:scriptExports,options:options};};/***/},/* 3 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* WEBPACK VAR INJECTION */(function(process,global,setImmediate){/*!
 * Vue.js v2.5.22
 * (c) 2014-2019 Evan You
 * Released under the MIT License.
 *//*  */var emptyObject=Object.freeze({});// These helpers produce better VM code in JS engines due to their
// explicitness and function inlining.
function isUndef(v){return v===undefined||v===null;}function isDef(v){return v!==undefined&&v!==null;}function isTrue(v){return v===true;}function isFalse(v){return v===false;}/**
 * Check if value is primitive.
 */function isPrimitive(value){return typeof value==='string'||typeof value==='number'||// $flow-disable-line
(typeof value==='undefined'?'undefined':_typeof2(value))==='symbol'||typeof value==='boolean';}/**
 * Quick object check - this is primarily used to tell
 * Objects from primitive values when we know the value
 * is a JSON-compliant type.
 */function isObject(obj){return obj!==null&&(typeof obj==='undefined'?'undefined':_typeof2(obj))==='object';}/**
 * Get the raw type string of a value, e.g., [object Object].
 */var _toString=Object.prototype.toString;function toRawType(value){return _toString.call(value).slice(8,-1);}/**
 * Strict object type check. Only returns true
 * for plain JavaScript objects.
 */function isPlainObject(obj){return _toString.call(obj)==='[object Object]';}function isRegExp(v){return _toString.call(v)==='[object RegExp]';}/**
 * Check if val is a valid array index.
 */function isValidArrayIndex(val){var n=parseFloat(String(val));return n>=0&&Math.floor(n)===n&&isFinite(val);}/**
 * Convert a value to a string that is actually rendered.
 */function toString(val){return val==null?'':(typeof val==='undefined'?'undefined':_typeof2(val))==='object'?JSON.stringify(val,null,2):String(val);}/**
 * Convert an input value to a number for persistence.
 * If the conversion fails, return original string.
 */function toNumber(val){var n=parseFloat(val);return isNaN(n)?val:n;}/**
 * Make a map and return a function for checking if a key
 * is in that map.
 */function makeMap(str,expectsLowerCase){var map=Object.create(null);var list=str.split(',');for(var i=0;i<list.length;i++){map[list[i]]=true;}return expectsLowerCase?function(val){return map[val.toLowerCase()];}:function(val){return map[val];};}/**
 * Check if a tag is a built-in tag.
 */var isBuiltInTag=makeMap('slot,component',true);/**
 * Check if an attribute is a reserved attribute.
 */var isReservedAttribute=makeMap('key,ref,slot,slot-scope,is');/**
 * Remove an item from an array.
 */function remove(arr,item){if(arr.length){var index=arr.indexOf(item);if(index>-1){return arr.splice(index,1);}}}/**
 * Check whether an object has the property.
 */var hasOwnProperty=Object.prototype.hasOwnProperty;function hasOwn(obj,key){return hasOwnProperty.call(obj,key);}/**
 * Create a cached version of a pure function.
 */function cached(fn){var cache=Object.create(null);return function cachedFn(str){var hit=cache[str];return hit||(cache[str]=fn(str));};}/**
 * Camelize a hyphen-delimited string.
 */var camelizeRE=/-(\w)/g;var camelize=cached(function(str){return str.replace(camelizeRE,function(_,c){return c?c.toUpperCase():'';});});/**
 * Capitalize a string.
 */var capitalize=cached(function(str){return str.charAt(0).toUpperCase()+str.slice(1);});/**
 * Hyphenate a camelCase string.
 */var hyphenateRE=/\B([A-Z])/g;var hyphenate=cached(function(str){return str.replace(hyphenateRE,'-$1').toLowerCase();});/**
 * Simple bind polyfill for environments that do not support it,
 * e.g., PhantomJS 1.x. Technically, we don't need this anymore
 * since native bind is now performant enough in most browsers.
 * But removing it would mean breaking code that was able to run in
 * PhantomJS 1.x, so this must be kept for backward compatibility.
 *//* istanbul ignore next */function polyfillBind(fn,ctx){function boundFn(a){var l=arguments.length;return l?l>1?fn.apply(ctx,arguments):fn.call(ctx,a):fn.call(ctx);}boundFn._length=fn.length;return boundFn;}function nativeBind(fn,ctx){return fn.bind(ctx);}var bind=Function.prototype.bind?nativeBind:polyfillBind;/**
 * Convert an Array-like object to a real Array.
 */function toArray(list,start){start=start||0;var i=list.length-start;var ret=new Array(i);while(i--){ret[i]=list[i+start];}return ret;}/**
 * Mix properties into target object.
 */function extend(to,_from){for(var key in _from){to[key]=_from[key];}return to;}/**
 * Merge an Array of Objects into a single Object.
 */function toObject(arr){var res={};for(var i=0;i<arr.length;i++){if(arr[i]){extend(res,arr[i]);}}return res;}/* eslint-disable no-unused-vars *//**
 * Perform no operation.
 * Stubbing args to make Flow happy without leaving useless transpiled code
 * with ...rest (https://flow.org/blog/2017/05/07/Strict-Function-Call-Arity/).
 */function noop(a,b,c){}/**
 * Always return false.
 */var no=function no(a,b,c){return false;};/* eslint-enable no-unused-vars *//**
 * Return the same value.
 */var identity=function identity(_){return _;};/**
 * Generate a string containing static keys from compiler modules.
 */function genStaticKeys(modules){return modules.reduce(function(keys,m){return keys.concat(m.staticKeys||[]);},[]).join(',');}/**
 * Check if two values are loosely equal - that is,
 * if they are plain objects, do they have the same shape?
 */function looseEqual(a,b){if(a===b){return true;}var isObjectA=isObject(a);var isObjectB=isObject(b);if(isObjectA&&isObjectB){try{var isArrayA=Array.isArray(a);var isArrayB=Array.isArray(b);if(isArrayA&&isArrayB){return a.length===b.length&&a.every(function(e,i){return looseEqual(e,b[i]);});}else if(a instanceof Date&&b instanceof Date){return a.getTime()===b.getTime();}else if(!isArrayA&&!isArrayB){var keysA=Object.keys(a);var keysB=Object.keys(b);return keysA.length===keysB.length&&keysA.every(function(key){return looseEqual(a[key],b[key]);});}else{/* istanbul ignore next */return false;}}catch(e){/* istanbul ignore next */return false;}}else if(!isObjectA&&!isObjectB){return String(a)===String(b);}else{return false;}}/**
 * Return the first index at which a loosely equal value can be
 * found in the array (if value is a plain object, the array must
 * contain an object of the same shape), or -1 if it is not present.
 */function looseIndexOf(arr,val){for(var i=0;i<arr.length;i++){if(looseEqual(arr[i],val)){return i;}}return-1;}/**
 * Ensure a function is called only once.
 */function once(fn){var called=false;return function(){if(!called){called=true;fn.apply(this,arguments);}};}var SSR_ATTR='data-server-rendered';var ASSET_TYPES=['component','directive','filter'];var LIFECYCLE_HOOKS=['beforeCreate','created','beforeMount','mounted','beforeUpdate','updated','beforeDestroy','destroyed','activated','deactivated','errorCaptured'];/*  */var config={/**
   * Option merge strategies (used in core/util/options)
   */// $flow-disable-line
optionMergeStrategies:Object.create(null),/**
   * Whether to suppress warnings.
   */silent:false,/**
   * Show production mode tip message on boot?
   */productionTip:process.env.NODE_ENV!=='production',/**
   * Whether to enable devtools
   */devtools:process.env.NODE_ENV!=='production',/**
   * Whether to record perf
   */performance:false,/**
   * Error handler for watcher errors
   */errorHandler:null,/**
   * Warn handler for watcher warns
   */warnHandler:null,/**
   * Ignore certain custom elements
   */ignoredElements:[],/**
   * Custom user key aliases for v-on
   */// $flow-disable-line
keyCodes:Object.create(null),/**
   * Check if a tag is reserved so that it cannot be registered as a
   * component. This is platform-dependent and may be overwritten.
   */isReservedTag:no,/**
   * Check if an attribute is reserved so that it cannot be used as a component
   * prop. This is platform-dependent and may be overwritten.
   */isReservedAttr:no,/**
   * Check if a tag is an unknown element.
   * Platform-dependent.
   */isUnknownElement:no,/**
   * Get the namespace of an element
   */getTagNamespace:noop,/**
   * Parse the real tag name for the specific platform.
   */parsePlatformTagName:identity,/**
   * Check if an attribute must be bound using property, e.g. value
   * Platform-dependent.
   */mustUseProp:no,/**
   * Perform updates asynchronously. Intended to be used by Vue Test Utils
   * This will significantly reduce performance if set to false.
   */async:true,/**
   * Exposed for legacy reasons
   */_lifecycleHooks:LIFECYCLE_HOOKS};/*  *//**
 * Check if a string starts with $ or _
 */function isReserved(str){var c=(str+'').charCodeAt(0);return c===0x24||c===0x5F;}/**
 * Define a property.
 */function def(obj,key,val,enumerable){Object.defineProperty(obj,key,{value:val,enumerable:!!enumerable,writable:true,configurable:true});}/**
 * Parse simple path.
 */var bailRE=/[^\w.$]/;function parsePath(path){if(bailRE.test(path)){return;}var segments=path.split('.');return function(obj){for(var i=0;i<segments.length;i++){if(!obj){return;}obj=obj[segments[i]];}return obj;};}/*  */// can we use __proto__?
var hasProto='__proto__'in{};// Browser environment sniffing
var inBrowser=typeof window!=='undefined';var inWeex=typeof WXEnvironment!=='undefined'&&!!WXEnvironment.platform;var weexPlatform=inWeex&&WXEnvironment.platform.toLowerCase();var UA=inBrowser&&window.navigator.userAgent.toLowerCase();var isIE=UA&&/msie|trident/.test(UA);var isIE9=UA&&UA.indexOf('msie 9.0')>0;var isEdge=UA&&UA.indexOf('edge/')>0;var isAndroid=UA&&UA.indexOf('android')>0||weexPlatform==='android';var isIOS=UA&&/iphone|ipad|ipod|ios/.test(UA)||weexPlatform==='ios';var isChrome=UA&&/chrome\/\d+/.test(UA)&&!isEdge;// Firefox has a "watch" function on Object.prototype...
var nativeWatch={}.watch;var supportsPassive=false;if(inBrowser){try{var opts={};Object.defineProperty(opts,'passive',{get:function get(){/* istanbul ignore next */supportsPassive=true;}});// https://github.com/facebook/flow/issues/285
window.addEventListener('test-passive',null,opts);}catch(e){}}// this needs to be lazy-evaled because vue may be required before
// vue-server-renderer can set VUE_ENV
var _isServer;var isServerRendering=function isServerRendering(){if(_isServer===undefined){/* istanbul ignore if */if(!inBrowser&&!inWeex&&typeof global!=='undefined'){// detect presence of vue-server-renderer and avoid
// Webpack shimming the process
_isServer=global['process']&&global['process'].env.VUE_ENV==='server';}else{_isServer=false;}}return _isServer;};// detect devtools
var devtools=inBrowser&&window.__VUE_DEVTOOLS_GLOBAL_HOOK__;/* istanbul ignore next */function isNative(Ctor){return typeof Ctor==='function'&&/native code/.test(Ctor.toString());}var hasSymbol=typeof Symbol!=='undefined'&&isNative(Symbol)&&typeof Reflect!=='undefined'&&isNative(Reflect.ownKeys);var _Set;/* istanbul ignore if */// $flow-disable-line
if(typeof Set!=='undefined'&&isNative(Set)){// use native Set when available.
_Set=Set;}else{// a non-standard Set polyfill that only works with primitive keys.
_Set=/*@__PURE__*/function(){function Set(){this.set=Object.create(null);}Set.prototype.has=function has(key){return this.set[key]===true;};Set.prototype.add=function add(key){this.set[key]=true;};Set.prototype.clear=function clear(){this.set=Object.create(null);};return Set;}();}/*  */var warn=noop;var tip=noop;var generateComponentTrace=noop;// work around flow check
var formatComponentName=noop;if(process.env.NODE_ENV!=='production'){var hasConsole=typeof console!=='undefined';var classifyRE=/(?:^|[-_])(\w)/g;var classify=function classify(str){return str.replace(classifyRE,function(c){return c.toUpperCase();}).replace(/[-_]/g,'');};warn=function warn(msg,vm){var trace=vm?generateComponentTrace(vm):'';if(config.warnHandler){config.warnHandler.call(null,msg,vm,trace);}else if(hasConsole&&!config.silent){console.error("[Vue warn]: "+msg+trace);}};tip=function tip(msg,vm){if(hasConsole&&!config.silent){console.warn("[Vue tip]: "+msg+(vm?generateComponentTrace(vm):''));}};formatComponentName=function formatComponentName(vm,includeFile){if(vm.$root===vm){return'<Root>';}var options=typeof vm==='function'&&vm.cid!=null?vm.options:vm._isVue?vm.$options||vm.constructor.options:vm;var name=options.name||options._componentTag;var file=options.__file;if(!name&&file){var match=file.match(/([^/\\]+)\.vue$/);name=match&&match[1];}return(name?"<"+classify(name)+">":"<Anonymous>")+(file&&includeFile!==false?" at "+file:'');};var repeat=function repeat(str,n){var res='';while(n){if(n%2===1){res+=str;}if(n>1){str+=str;}n>>=1;}return res;};generateComponentTrace=function generateComponentTrace(vm){if(vm._isVue&&vm.$parent){var tree=[];var currentRecursiveSequence=0;while(vm){if(tree.length>0){var last=tree[tree.length-1];if(last.constructor===vm.constructor){currentRecursiveSequence++;vm=vm.$parent;continue;}else if(currentRecursiveSequence>0){tree[tree.length-1]=[last,currentRecursiveSequence];currentRecursiveSequence=0;}}tree.push(vm);vm=vm.$parent;}return'\n\nfound in\n\n'+tree.map(function(vm,i){return""+(i===0?'---> ':repeat(' ',5+i*2))+(Array.isArray(vm)?formatComponentName(vm[0])+"... ("+vm[1]+" recursive calls)":formatComponentName(vm));}).join('\n');}else{return"\n\n(found in "+formatComponentName(vm)+")";}};}/*  */var uid=0;/**
 * A dep is an observable that can have multiple
 * directives subscribing to it.
 */var Dep=function Dep(){this.id=uid++;this.subs=[];};Dep.prototype.addSub=function addSub(sub){this.subs.push(sub);};Dep.prototype.removeSub=function removeSub(sub){remove(this.subs,sub);};Dep.prototype.depend=function depend(){if(Dep.target){Dep.target.addDep(this);}};Dep.prototype.notify=function notify(){// stabilize the subscriber list first
var subs=this.subs.slice();if(process.env.NODE_ENV!=='production'&&!config.async){// subs aren't sorted in scheduler if not running async
// we need to sort them now to make sure they fire in correct
// order
subs.sort(function(a,b){return a.id-b.id;});}for(var i=0,l=subs.length;i<l;i++){subs[i].update();}};// The current target watcher being evaluated.
// This is globally unique because only one watcher
// can be evaluated at a time.
Dep.target=null;var targetStack=[];function pushTarget(target){targetStack.push(target);Dep.target=target;}function popTarget(){targetStack.pop();Dep.target=targetStack[targetStack.length-1];}/*  */var VNode=function VNode(tag,data,children,text,elm,context,componentOptions,asyncFactory){this.tag=tag;this.data=data;this.children=children;this.text=text;this.elm=elm;this.ns=undefined;this.context=context;this.fnContext=undefined;this.fnOptions=undefined;this.fnScopeId=undefined;this.key=data&&data.key;this.componentOptions=componentOptions;this.componentInstance=undefined;this.parent=undefined;this.raw=false;this.isStatic=false;this.isRootInsert=true;this.isComment=false;this.isCloned=false;this.isOnce=false;this.asyncFactory=asyncFactory;this.asyncMeta=undefined;this.isAsyncPlaceholder=false;};var prototypeAccessors={child:{configurable:true}};// DEPRECATED: alias for componentInstance for backwards compat.
/* istanbul ignore next */prototypeAccessors.child.get=function(){return this.componentInstance;};Object.defineProperties(VNode.prototype,prototypeAccessors);var createEmptyVNode=function createEmptyVNode(text){if(text===void 0)text='';var node=new VNode();node.text=text;node.isComment=true;return node;};function createTextVNode(val){return new VNode(undefined,undefined,undefined,String(val));}// optimized shallow clone
// used for static nodes and slot nodes because they may be reused across
// multiple renders, cloning them avoids errors when DOM manipulations rely
// on their elm reference.
function cloneVNode(vnode){var cloned=new VNode(vnode.tag,vnode.data,// #7975
// clone children array to avoid mutating original in case of cloning
// a child.
vnode.children&&vnode.children.slice(),vnode.text,vnode.elm,vnode.context,vnode.componentOptions,vnode.asyncFactory);cloned.ns=vnode.ns;cloned.isStatic=vnode.isStatic;cloned.key=vnode.key;cloned.isComment=vnode.isComment;cloned.fnContext=vnode.fnContext;cloned.fnOptions=vnode.fnOptions;cloned.fnScopeId=vnode.fnScopeId;cloned.asyncMeta=vnode.asyncMeta;cloned.isCloned=true;return cloned;}/*
 * not type checking this file because flow doesn't play well with
 * dynamically accessing methods on Array prototype
 */var arrayProto=Array.prototype;var arrayMethods=Object.create(arrayProto);var methodsToPatch=['push','pop','shift','unshift','splice','sort','reverse'];/**
 * Intercept mutating methods and emit events
 */methodsToPatch.forEach(function(method){// cache original method
var original=arrayProto[method];def(arrayMethods,method,function mutator(){var args=[],len=arguments.length;while(len--){args[len]=arguments[len];}var result=original.apply(this,args);var ob=this.__ob__;var inserted;switch(method){case'push':case'unshift':inserted=args;break;case'splice':inserted=args.slice(2);break;}if(inserted){ob.observeArray(inserted);}// notify change
ob.dep.notify();return result;});});/*  */var arrayKeys=Object.getOwnPropertyNames(arrayMethods);/**
 * In some cases we may want to disable observation inside a component's
 * update computation.
 */var shouldObserve=true;function toggleObserving(value){shouldObserve=value;}/**
 * Observer class that is attached to each observed
 * object. Once attached, the observer converts the target
 * object's property keys into getter/setters that
 * collect dependencies and dispatch updates.
 */var Observer=function Observer(value){this.value=value;this.dep=new Dep();this.vmCount=0;def(value,'__ob__',this);if(Array.isArray(value)){if(hasProto){protoAugment(value,arrayMethods);}else{copyAugment(value,arrayMethods,arrayKeys);}this.observeArray(value);}else{this.walk(value);}};/**
 * Walk through all properties and convert them into
 * getter/setters. This method should only be called when
 * value type is Object.
 */Observer.prototype.walk=function walk(obj){var keys=Object.keys(obj);for(var i=0;i<keys.length;i++){defineReactive$$1(obj,keys[i]);}};/**
 * Observe a list of Array items.
 */Observer.prototype.observeArray=function observeArray(items){for(var i=0,l=items.length;i<l;i++){observe(items[i]);}};// helpers
/**
 * Augment a target Object or Array by intercepting
 * the prototype chain using __proto__
 */function protoAugment(target,src){/* eslint-disable no-proto */target.__proto__=src;/* eslint-enable no-proto */}/**
 * Augment a target Object or Array by defining
 * hidden properties.
 *//* istanbul ignore next */function copyAugment(target,src,keys){for(var i=0,l=keys.length;i<l;i++){var key=keys[i];def(target,key,src[key]);}}/**
 * Attempt to create an observer instance for a value,
 * returns the new observer if successfully observed,
 * or the existing observer if the value already has one.
 */function observe(value,asRootData){if(!isObject(value)||value instanceof VNode){return;}var ob;if(hasOwn(value,'__ob__')&&value.__ob__ instanceof Observer){ob=value.__ob__;}else if(shouldObserve&&!isServerRendering()&&(Array.isArray(value)||isPlainObject(value))&&Object.isExtensible(value)&&!value._isVue){ob=new Observer(value);}if(asRootData&&ob){ob.vmCount++;}return ob;}/**
 * Define a reactive property on an Object.
 */function defineReactive$$1(obj,key,val,customSetter,shallow){var dep=new Dep();var property=Object.getOwnPropertyDescriptor(obj,key);if(property&&property.configurable===false){return;}// cater for pre-defined getter/setters
var getter=property&&property.get;var setter=property&&property.set;if((!getter||setter)&&arguments.length===2){val=obj[key];}var childOb=!shallow&&observe(val);Object.defineProperty(obj,key,{enumerable:true,configurable:true,get:function reactiveGetter(){var value=getter?getter.call(obj):val;if(Dep.target){dep.depend();if(childOb){childOb.dep.depend();if(Array.isArray(value)){dependArray(value);}}}return value;},set:function reactiveSetter(newVal){var value=getter?getter.call(obj):val;/* eslint-disable no-self-compare */if(newVal===value||newVal!==newVal&&value!==value){return;}/* eslint-enable no-self-compare */if(process.env.NODE_ENV!=='production'&&customSetter){customSetter();}// #7981: for accessor properties without setter
if(getter&&!setter){return;}if(setter){setter.call(obj,newVal);}else{val=newVal;}childOb=!shallow&&observe(newVal);dep.notify();}});}/**
 * Set a property on an object. Adds the new property and
 * triggers change notification if the property doesn't
 * already exist.
 */function set(target,key,val){if(process.env.NODE_ENV!=='production'&&(isUndef(target)||isPrimitive(target))){warn("Cannot set reactive property on undefined, null, or primitive value: "+target);}if(Array.isArray(target)&&isValidArrayIndex(key)){target.length=Math.max(target.length,key);target.splice(key,1,val);return val;}if(key in target&&!(key in Object.prototype)){target[key]=val;return val;}var ob=target.__ob__;if(target._isVue||ob&&ob.vmCount){process.env.NODE_ENV!=='production'&&warn('Avoid adding reactive properties to a Vue instance or its root $data '+'at runtime - declare it upfront in the data option.');return val;}if(!ob){target[key]=val;return val;}defineReactive$$1(ob.value,key,val);ob.dep.notify();return val;}/**
 * Delete a property and trigger change if necessary.
 */function del(target,key){if(process.env.NODE_ENV!=='production'&&(isUndef(target)||isPrimitive(target))){warn("Cannot delete reactive property on undefined, null, or primitive value: "+target);}if(Array.isArray(target)&&isValidArrayIndex(key)){target.splice(key,1);return;}var ob=target.__ob__;if(target._isVue||ob&&ob.vmCount){process.env.NODE_ENV!=='production'&&warn('Avoid deleting properties on a Vue instance or its root $data '+'- just set it to null.');return;}if(!hasOwn(target,key)){return;}delete target[key];if(!ob){return;}ob.dep.notify();}/**
 * Collect dependencies on array elements when the array is touched, since
 * we cannot intercept array element access like property getters.
 */function dependArray(value){for(var e=void 0,i=0,l=value.length;i<l;i++){e=value[i];e&&e.__ob__&&e.__ob__.dep.depend();if(Array.isArray(e)){dependArray(e);}}}/*  *//**
 * Option overwriting strategies are functions that handle
 * how to merge a parent option value and a child option
 * value into the final value.
 */var strats=config.optionMergeStrategies;/**
 * Options with restrictions
 */if(process.env.NODE_ENV!=='production'){strats.el=strats.propsData=function(parent,child,vm,key){if(!vm){warn("option \""+key+"\" can only be used during instance "+'creation with the `new` keyword.');}return defaultStrat(parent,child);};}/**
 * Helper that recursively merges two data objects together.
 */function mergeData(to,from){if(!from){return to;}var key,toVal,fromVal;var keys=Object.keys(from);for(var i=0;i<keys.length;i++){key=keys[i];toVal=to[key];fromVal=from[key];if(!hasOwn(to,key)){set(to,key,fromVal);}else if(toVal!==fromVal&&isPlainObject(toVal)&&isPlainObject(fromVal)){mergeData(toVal,fromVal);}}return to;}/**
 * Data
 */function mergeDataOrFn(parentVal,childVal,vm){if(!vm){// in a Vue.extend merge, both should be functions
if(!childVal){return parentVal;}if(!parentVal){return childVal;}// when parentVal & childVal are both present,
// we need to return a function that returns the
// merged result of both functions... no need to
// check if parentVal is a function here because
// it has to be a function to pass previous merges.
return function mergedDataFn(){return mergeData(typeof childVal==='function'?childVal.call(this,this):childVal,typeof parentVal==='function'?parentVal.call(this,this):parentVal);};}else{return function mergedInstanceDataFn(){// instance merge
var instanceData=typeof childVal==='function'?childVal.call(vm,vm):childVal;var defaultData=typeof parentVal==='function'?parentVal.call(vm,vm):parentVal;if(instanceData){return mergeData(instanceData,defaultData);}else{return defaultData;}};}}strats.data=function(parentVal,childVal,vm){if(!vm){if(childVal&&typeof childVal!=='function'){process.env.NODE_ENV!=='production'&&warn('The "data" option should be a function '+'that returns a per-instance value in component '+'definitions.',vm);return parentVal;}return mergeDataOrFn(parentVal,childVal);}return mergeDataOrFn(parentVal,childVal,vm);};/**
 * Hooks and props are merged as arrays.
 */function mergeHook(parentVal,childVal){var res=childVal?parentVal?parentVal.concat(childVal):Array.isArray(childVal)?childVal:[childVal]:parentVal;return res?dedupeHooks(res):res;}function dedupeHooks(hooks){var res=[];for(var i=0;i<hooks.length;i++){if(res.indexOf(hooks[i])===-1){res.push(hooks[i]);}}return res;}LIFECYCLE_HOOKS.forEach(function(hook){strats[hook]=mergeHook;});/**
 * Assets
 *
 * When a vm is present (instance creation), we need to do
 * a three-way merge between constructor options, instance
 * options and parent options.
 */function mergeAssets(parentVal,childVal,vm,key){var res=Object.create(parentVal||null);if(childVal){process.env.NODE_ENV!=='production'&&assertObjectType(key,childVal,vm);return extend(res,childVal);}else{return res;}}ASSET_TYPES.forEach(function(type){strats[type+'s']=mergeAssets;});/**
 * Watchers.
 *
 * Watchers hashes should not overwrite one
 * another, so we merge them as arrays.
 */strats.watch=function(parentVal,childVal,vm,key){// work around Firefox's Object.prototype.watch...
if(parentVal===nativeWatch){parentVal=undefined;}if(childVal===nativeWatch){childVal=undefined;}/* istanbul ignore if */if(!childVal){return Object.create(parentVal||null);}if(process.env.NODE_ENV!=='production'){assertObjectType(key,childVal,vm);}if(!parentVal){return childVal;}var ret={};extend(ret,parentVal);for(var key$1 in childVal){var parent=ret[key$1];var child=childVal[key$1];if(parent&&!Array.isArray(parent)){parent=[parent];}ret[key$1]=parent?parent.concat(child):Array.isArray(child)?child:[child];}return ret;};/**
 * Other object hashes.
 */strats.props=strats.methods=strats.inject=strats.computed=function(parentVal,childVal,vm,key){if(childVal&&process.env.NODE_ENV!=='production'){assertObjectType(key,childVal,vm);}if(!parentVal){return childVal;}var ret=Object.create(null);extend(ret,parentVal);if(childVal){extend(ret,childVal);}return ret;};strats.provide=mergeDataOrFn;/**
 * Default strategy.
 */var defaultStrat=function defaultStrat(parentVal,childVal){return childVal===undefined?parentVal:childVal;};/**
 * Validate component names
 */function checkComponents(options){for(var key in options.components){validateComponentName(key);}}function validateComponentName(name){if(!/^[a-zA-Z][\w-]*$/.test(name)){warn('Invalid component name: "'+name+'". Component names '+'can only contain alphanumeric characters and the hyphen, '+'and must start with a letter.');}if(isBuiltInTag(name)||config.isReservedTag(name)){warn('Do not use built-in or reserved HTML elements as component '+'id: '+name);}}/**
 * Ensure all props option syntax are normalized into the
 * Object-based format.
 */function normalizeProps(options,vm){var props=options.props;if(!props){return;}var res={};var i,val,name;if(Array.isArray(props)){i=props.length;while(i--){val=props[i];if(typeof val==='string'){name=camelize(val);res[name]={type:null};}else if(process.env.NODE_ENV!=='production'){warn('props must be strings when using array syntax.');}}}else if(isPlainObject(props)){for(var key in props){val=props[key];name=camelize(key);res[name]=isPlainObject(val)?val:{type:val};}}else if(process.env.NODE_ENV!=='production'){warn("Invalid value for option \"props\": expected an Array or an Object, "+"but got "+toRawType(props)+".",vm);}options.props=res;}/**
 * Normalize all injections into Object-based format
 */function normalizeInject(options,vm){var inject=options.inject;if(!inject){return;}var normalized=options.inject={};if(Array.isArray(inject)){for(var i=0;i<inject.length;i++){normalized[inject[i]]={from:inject[i]};}}else if(isPlainObject(inject)){for(var key in inject){var val=inject[key];normalized[key]=isPlainObject(val)?extend({from:key},val):{from:val};}}else if(process.env.NODE_ENV!=='production'){warn("Invalid value for option \"inject\": expected an Array or an Object, "+"but got "+toRawType(inject)+".",vm);}}/**
 * Normalize raw function directives into object format.
 */function normalizeDirectives(options){var dirs=options.directives;if(dirs){for(var key in dirs){var def=dirs[key];if(typeof def==='function'){dirs[key]={bind:def,update:def};}}}}function assertObjectType(name,value,vm){if(!isPlainObject(value)){warn("Invalid value for option \""+name+"\": expected an Object, "+"but got "+toRawType(value)+".",vm);}}/**
 * Merge two option objects into a new one.
 * Core utility used in both instantiation and inheritance.
 */function mergeOptions(parent,child,vm){if(process.env.NODE_ENV!=='production'){checkComponents(child);}if(typeof child==='function'){child=child.options;}normalizeProps(child,vm);normalizeInject(child,vm);normalizeDirectives(child);// Apply extends and mixins on the child options,
// but only if it is a raw options object that isn't
// the result of another mergeOptions call.
// Only merged options has the _base property.
if(!child._base){if(child.extends){parent=mergeOptions(parent,child.extends,vm);}if(child.mixins){for(var i=0,l=child.mixins.length;i<l;i++){parent=mergeOptions(parent,child.mixins[i],vm);}}}var options={};var key;for(key in parent){mergeField(key);}for(key in child){if(!hasOwn(parent,key)){mergeField(key);}}function mergeField(key){var strat=strats[key]||defaultStrat;options[key]=strat(parent[key],child[key],vm,key);}return options;}/**
 * Resolve an asset.
 * This function is used because child instances need access
 * to assets defined in its ancestor chain.
 */function resolveAsset(options,type,id,warnMissing){/* istanbul ignore if */if(typeof id!=='string'){return;}var assets=options[type];// check local registration variations first
if(hasOwn(assets,id)){return assets[id];}var camelizedId=camelize(id);if(hasOwn(assets,camelizedId)){return assets[camelizedId];}var PascalCaseId=capitalize(camelizedId);if(hasOwn(assets,PascalCaseId)){return assets[PascalCaseId];}// fallback to prototype chain
var res=assets[id]||assets[camelizedId]||assets[PascalCaseId];if(process.env.NODE_ENV!=='production'&&warnMissing&&!res){warn('Failed to resolve '+type.slice(0,-1)+': '+id,options);}return res;}/*  */function validateProp(key,propOptions,propsData,vm){var prop=propOptions[key];var absent=!hasOwn(propsData,key);var value=propsData[key];// boolean casting
var booleanIndex=getTypeIndex(Boolean,prop.type);if(booleanIndex>-1){if(absent&&!hasOwn(prop,'default')){value=false;}else if(value===''||value===hyphenate(key)){// only cast empty string / same name to boolean if
// boolean has higher priority
var stringIndex=getTypeIndex(String,prop.type);if(stringIndex<0||booleanIndex<stringIndex){value=true;}}}// check default value
if(value===undefined){value=getPropDefaultValue(vm,prop,key);// since the default value is a fresh copy,
// make sure to observe it.
var prevShouldObserve=shouldObserve;toggleObserving(true);observe(value);toggleObserving(prevShouldObserve);}if(process.env.NODE_ENV!=='production'&&// skip validation for weex recycle-list child component props
!false){assertProp(prop,key,value,vm,absent);}return value;}/**
 * Get the default value of a prop.
 */function getPropDefaultValue(vm,prop,key){// no default, return undefined
if(!hasOwn(prop,'default')){return undefined;}var def=prop.default;// warn against non-factory defaults for Object & Array
if(process.env.NODE_ENV!=='production'&&isObject(def)){warn('Invalid default value for prop "'+key+'": '+'Props with type Object/Array must use a factory function '+'to return the default value.',vm);}// the raw prop value was also undefined from previous render,
// return previous default value to avoid unnecessary watcher trigger
if(vm&&vm.$options.propsData&&vm.$options.propsData[key]===undefined&&vm._props[key]!==undefined){return vm._props[key];}// call factory function for non-Function types
// a value is Function if its prototype is function even across different execution context
return typeof def==='function'&&getType(prop.type)!=='Function'?def.call(vm):def;}/**
 * Assert whether a prop is valid.
 */function assertProp(prop,name,value,vm,absent){if(prop.required&&absent){warn('Missing required prop: "'+name+'"',vm);return;}if(value==null&&!prop.required){return;}var type=prop.type;var valid=!type||type===true;var expectedTypes=[];if(type){if(!Array.isArray(type)){type=[type];}for(var i=0;i<type.length&&!valid;i++){var assertedType=assertType(value,type[i]);expectedTypes.push(assertedType.expectedType||'');valid=assertedType.valid;}}if(!valid){warn(getInvalidTypeMessage(name,value,expectedTypes),vm);return;}var validator=prop.validator;if(validator){if(!validator(value)){warn('Invalid prop: custom validator check failed for prop "'+name+'".',vm);}}}var simpleCheckRE=/^(String|Number|Boolean|Function|Symbol)$/;function assertType(value,type){var valid;var expectedType=getType(type);if(simpleCheckRE.test(expectedType)){var t=typeof value==='undefined'?'undefined':_typeof2(value);valid=t===expectedType.toLowerCase();// for primitive wrapper objects
if(!valid&&t==='object'){valid=value instanceof type;}}else if(expectedType==='Object'){valid=isPlainObject(value);}else if(expectedType==='Array'){valid=Array.isArray(value);}else{valid=value instanceof type;}return{valid:valid,expectedType:expectedType};}/**
 * Use function string name to check built-in types,
 * because a simple equality check will fail when running
 * across different vms / iframes.
 */function getType(fn){var match=fn&&fn.toString().match(/^\s*function (\w+)/);return match?match[1]:'';}function isSameType(a,b){return getType(a)===getType(b);}function getTypeIndex(type,expectedTypes){if(!Array.isArray(expectedTypes)){return isSameType(expectedTypes,type)?0:-1;}for(var i=0,len=expectedTypes.length;i<len;i++){if(isSameType(expectedTypes[i],type)){return i;}}return-1;}function getInvalidTypeMessage(name,value,expectedTypes){var message="Invalid prop: type check failed for prop \""+name+"\"."+" Expected "+expectedTypes.map(capitalize).join(', ');var expectedType=expectedTypes[0];var receivedType=toRawType(value);var expectedValue=styleValue(value,expectedType);var receivedValue=styleValue(value,receivedType);// check if we need to specify expected value
if(expectedTypes.length===1&&isExplicable(expectedType)&&!isBoolean(expectedType,receivedType)){message+=" with value "+expectedValue;}message+=", got "+receivedType+" ";// check if we need to specify received value
if(isExplicable(receivedType)){message+="with value "+receivedValue+".";}return message;}function styleValue(value,type){if(type==='String'){return"\""+value+"\"";}else if(type==='Number'){return""+Number(value);}else{return""+value;}}function isExplicable(value){var explicitTypes=['string','number','boolean'];return explicitTypes.some(function(elem){return value.toLowerCase()===elem;});}function isBoolean(){var args=[],len=arguments.length;while(len--){args[len]=arguments[len];}return args.some(function(elem){return elem.toLowerCase()==='boolean';});}/*  */function handleError(err,vm,info){if(vm){var cur=vm;while(cur=cur.$parent){var hooks=cur.$options.errorCaptured;if(hooks){for(var i=0;i<hooks.length;i++){try{var capture=hooks[i].call(cur,err,vm,info)===false;if(capture){return;}}catch(e){globalHandleError(e,cur,'errorCaptured hook');}}}}}globalHandleError(err,vm,info);}function globalHandleError(err,vm,info){if(config.errorHandler){try{return config.errorHandler.call(null,err,vm,info);}catch(e){logError(e,null,'config.errorHandler');}}logError(err,vm,info);}function logError(err,vm,info){if(process.env.NODE_ENV!=='production'){warn("Error in "+info+": \""+err.toString()+"\"",vm);}/* istanbul ignore else */if((inBrowser||inWeex)&&typeof console!=='undefined'){console.error(err);}else{throw err;}}/*  */var callbacks=[];var pending=false;function flushCallbacks(){pending=false;var copies=callbacks.slice(0);callbacks.length=0;for(var i=0;i<copies.length;i++){copies[i]();}}// Here we have async deferring wrappers using both microtasks and (macro) tasks.
// In < 2.4 we used microtasks everywhere, but there are some scenarios where
// microtasks have too high a priority and fire in between supposedly
// sequential events (e.g. #4521, #6690) or even between bubbling of the same
// event (#6566). However, using (macro) tasks everywhere also has subtle problems
// when state is changed right before repaint (e.g. #6813, out-in transitions).
// Here we use microtask by default, but expose a way to force (macro) task when
// needed (e.g. in event handlers attached by v-on).
var microTimerFunc;var macroTimerFunc;var useMacroTask=false;// Determine (macro) task defer implementation.
// Technically setImmediate should be the ideal choice, but it's only available
// in IE. The only polyfill that consistently queues the callback after all DOM
// events triggered in the same loop is by using MessageChannel.
/* istanbul ignore if */if(typeof setImmediate!=='undefined'&&isNative(setImmediate)){macroTimerFunc=function macroTimerFunc(){setImmediate(flushCallbacks);};}else if(typeof MessageChannel!=='undefined'&&(isNative(MessageChannel)||// PhantomJS
MessageChannel.toString()==='[object MessageChannelConstructor]')){var channel=new MessageChannel();var port=channel.port2;channel.port1.onmessage=flushCallbacks;macroTimerFunc=function macroTimerFunc(){port.postMessage(1);};}else{/* istanbul ignore next */macroTimerFunc=function macroTimerFunc(){setTimeout(flushCallbacks,0);};}// Determine microtask defer implementation.
/* istanbul ignore next, $flow-disable-line */if(typeof Promise!=='undefined'&&isNative(Promise)){var p=Promise.resolve();microTimerFunc=function microTimerFunc(){p.then(flushCallbacks);// in problematic UIWebViews, Promise.then doesn't completely break, but
// it can get stuck in a weird state where callbacks are pushed into the
// microtask queue but the queue isn't being flushed, until the browser
// needs to do some other work, e.g. handle a timer. Therefore we can
// "force" the microtask queue to be flushed by adding an empty timer.
if(isIOS){setTimeout(noop);}};}else{// fallback to macro
microTimerFunc=macroTimerFunc;}/**
 * Wrap a function so that if any code inside triggers state change,
 * the changes are queued using a (macro) task instead of a microtask.
 */function withMacroTask(fn){return fn._withTask||(fn._withTask=function(){useMacroTask=true;try{return fn.apply(null,arguments);}finally{useMacroTask=false;}});}function nextTick(cb,ctx){var _resolve;callbacks.push(function(){if(cb){try{cb.call(ctx);}catch(e){handleError(e,ctx,'nextTick');}}else if(_resolve){_resolve(ctx);}});if(!pending){pending=true;if(useMacroTask){macroTimerFunc();}else{microTimerFunc();}}// $flow-disable-line
if(!cb&&typeof Promise!=='undefined'){return new Promise(function(resolve){_resolve=resolve;});}}/*  */var mark;var measure;if(process.env.NODE_ENV!=='production'){var perf=inBrowser&&window.performance;/* istanbul ignore if */if(perf&&perf.mark&&perf.measure&&perf.clearMarks&&perf.clearMeasures){mark=function mark(tag){return perf.mark(tag);};measure=function measure(name,startTag,endTag){perf.measure(name,startTag,endTag);perf.clearMarks(startTag);perf.clearMarks(endTag);perf.clearMeasures(name);};}}/* not type checking this file because flow doesn't play well with Proxy */var initProxy;if(process.env.NODE_ENV!=='production'){var allowedGlobals=makeMap('Infinity,undefined,NaN,isFinite,isNaN,'+'parseFloat,parseInt,decodeURI,decodeURIComponent,encodeURI,encodeURIComponent,'+'Math,Number,Date,Array,Object,Boolean,String,RegExp,Map,Set,JSON,Intl,'+'require'// for Webpack/Browserify
);var warnNonPresent=function warnNonPresent(target,key){warn("Property or method \""+key+"\" is not defined on the instance but "+'referenced during render. Make sure that this property is reactive, '+'either in the data option, or for class-based components, by '+'initializing the property. '+'See: https://vuejs.org/v2/guide/reactivity.html#Declaring-Reactive-Properties.',target);};var warnReservedPrefix=function warnReservedPrefix(target,key){warn("Property \""+key+"\" must be accessed with \"$data."+key+"\" because "+'properties starting with "$" or "_" are not proxied in the Vue instance to '+'prevent conflicts with Vue internals'+'See: https://vuejs.org/v2/api/#data',target);};var hasProxy=typeof Proxy!=='undefined'&&isNative(Proxy);if(hasProxy){var isBuiltInModifier=makeMap('stop,prevent,self,ctrl,shift,alt,meta,exact');config.keyCodes=new Proxy(config.keyCodes,{set:function set(target,key,value){if(isBuiltInModifier(key)){warn("Avoid overwriting built-in modifier in config.keyCodes: ."+key);return false;}else{target[key]=value;return true;}}});}var hasHandler={has:function has(target,key){var has=key in target;var isAllowed=allowedGlobals(key)||typeof key==='string'&&key.charAt(0)==='_'&&!(key in target.$data);if(!has&&!isAllowed){if(key in target.$data){warnReservedPrefix(target,key);}else{warnNonPresent(target,key);}}return has||!isAllowed;}};var getHandler={get:function get(target,key){if(typeof key==='string'&&!(key in target)){if(key in target.$data){warnReservedPrefix(target,key);}else{warnNonPresent(target,key);}}return target[key];}};initProxy=function initProxy(vm){if(hasProxy){// determine which proxy handler to use
var options=vm.$options;var handlers=options.render&&options.render._withStripped?getHandler:hasHandler;vm._renderProxy=new Proxy(vm,handlers);}else{vm._renderProxy=vm;}};}/*  */var seenObjects=new _Set();/**
 * Recursively traverse an object to evoke all converted
 * getters, so that every nested property inside the object
 * is collected as a "deep" dependency.
 */function traverse(val){_traverse(val,seenObjects);seenObjects.clear();}function _traverse(val,seen){var i,keys;var isA=Array.isArray(val);if(!isA&&!isObject(val)||Object.isFrozen(val)||val instanceof VNode){return;}if(val.__ob__){var depId=val.__ob__.dep.id;if(seen.has(depId)){return;}seen.add(depId);}if(isA){i=val.length;while(i--){_traverse(val[i],seen);}}else{keys=Object.keys(val);i=keys.length;while(i--){_traverse(val[keys[i]],seen);}}}/*  */var normalizeEvent=cached(function(name){var passive=name.charAt(0)==='&';name=passive?name.slice(1):name;var once$$1=name.charAt(0)==='~';// Prefixed last, checked first
name=once$$1?name.slice(1):name;var capture=name.charAt(0)==='!';name=capture?name.slice(1):name;return{name:name,once:once$$1,capture:capture,passive:passive};});function createFnInvoker(fns){function invoker(){var arguments$1=arguments;var fns=invoker.fns;if(Array.isArray(fns)){var cloned=fns.slice();for(var i=0;i<cloned.length;i++){cloned[i].apply(null,arguments$1);}}else{// return handler return value for single handlers
return fns.apply(null,arguments);}}invoker.fns=fns;return invoker;}function updateListeners(on,oldOn,add,remove$$1,createOnceHandler,vm){var name,def$$1,cur,old,event;for(name in on){def$$1=cur=on[name];old=oldOn[name];event=normalizeEvent(name);if(isUndef(cur)){process.env.NODE_ENV!=='production'&&warn("Invalid handler for event \""+event.name+"\": got "+String(cur),vm);}else if(isUndef(old)){if(isUndef(cur.fns)){cur=on[name]=createFnInvoker(cur);}if(isTrue(event.once)){cur=on[name]=createOnceHandler(event.name,cur,event.capture);}add(event.name,cur,event.capture,event.passive,event.params);}else if(cur!==old){old.fns=cur;on[name]=old;}}for(name in oldOn){if(isUndef(on[name])){event=normalizeEvent(name);remove$$1(event.name,oldOn[name],event.capture);}}}/*  */function mergeVNodeHook(def,hookKey,hook){if(def instanceof VNode){def=def.data.hook||(def.data.hook={});}var invoker;var oldHook=def[hookKey];function wrappedHook(){hook.apply(this,arguments);// important: remove merged hook to ensure it's called only once
// and prevent memory leak
remove(invoker.fns,wrappedHook);}if(isUndef(oldHook)){// no existing hook
invoker=createFnInvoker([wrappedHook]);}else{/* istanbul ignore if */if(isDef(oldHook.fns)&&isTrue(oldHook.merged)){// already a merged invoker
invoker=oldHook;invoker.fns.push(wrappedHook);}else{// existing plain hook
invoker=createFnInvoker([oldHook,wrappedHook]);}}invoker.merged=true;def[hookKey]=invoker;}/*  */function extractPropsFromVNodeData(data,Ctor,tag){// we are only extracting raw values here.
// validation and default values are handled in the child
// component itself.
var propOptions=Ctor.options.props;if(isUndef(propOptions)){return;}var res={};var attrs=data.attrs;var props=data.props;if(isDef(attrs)||isDef(props)){for(var key in propOptions){var altKey=hyphenate(key);if(process.env.NODE_ENV!=='production'){var keyInLowerCase=key.toLowerCase();if(key!==keyInLowerCase&&attrs&&hasOwn(attrs,keyInLowerCase)){tip("Prop \""+keyInLowerCase+"\" is passed to component "+formatComponentName(tag||Ctor)+", but the declared prop name is"+" \""+key+"\". "+"Note that HTML attributes are case-insensitive and camelCased "+"props need to use their kebab-case equivalents when using in-DOM "+"templates. You should probably use \""+altKey+"\" instead of \""+key+"\".");}}checkProp(res,props,key,altKey,true)||checkProp(res,attrs,key,altKey,false);}}return res;}function checkProp(res,hash,key,altKey,preserve){if(isDef(hash)){if(hasOwn(hash,key)){res[key]=hash[key];if(!preserve){delete hash[key];}return true;}else if(hasOwn(hash,altKey)){res[key]=hash[altKey];if(!preserve){delete hash[altKey];}return true;}}return false;}/*  */// The template compiler attempts to minimize the need for normalization by
// statically analyzing the template at compile time.
//
// For plain HTML markup, normalization can be completely skipped because the
// generated render function is guaranteed to return Array<VNode>. There are
// two cases where extra normalization is needed:
// 1. When the children contains components - because a functional component
// may return an Array instead of a single root. In this case, just a simple
// normalization is needed - if any child is an Array, we flatten the whole
// thing with Array.prototype.concat. It is guaranteed to be only 1-level deep
// because functional components already normalize their own children.
function simpleNormalizeChildren(children){for(var i=0;i<children.length;i++){if(Array.isArray(children[i])){return Array.prototype.concat.apply([],children);}}return children;}// 2. When the children contains constructs that always generated nested Arrays,
// e.g. <template>, <slot>, v-for, or when the children is provided by user
// with hand-written render functions / JSX. In such cases a full normalization
// is needed to cater to all possible types of children values.
function normalizeChildren(children){return isPrimitive(children)?[createTextVNode(children)]:Array.isArray(children)?normalizeArrayChildren(children):undefined;}function isTextNode(node){return isDef(node)&&isDef(node.text)&&isFalse(node.isComment);}function normalizeArrayChildren(children,nestedIndex){var res=[];var i,c,lastIndex,last;for(i=0;i<children.length;i++){c=children[i];if(isUndef(c)||typeof c==='boolean'){continue;}lastIndex=res.length-1;last=res[lastIndex];//  nested
if(Array.isArray(c)){if(c.length>0){c=normalizeArrayChildren(c,(nestedIndex||'')+"_"+i);// merge adjacent text nodes
if(isTextNode(c[0])&&isTextNode(last)){res[lastIndex]=createTextVNode(last.text+c[0].text);c.shift();}res.push.apply(res,c);}}else if(isPrimitive(c)){if(isTextNode(last)){// merge adjacent text nodes
// this is necessary for SSR hydration because text nodes are
// essentially merged when rendered to HTML strings
res[lastIndex]=createTextVNode(last.text+c);}else if(c!==''){// convert primitive to vnode
res.push(createTextVNode(c));}}else{if(isTextNode(c)&&isTextNode(last)){// merge adjacent text nodes
res[lastIndex]=createTextVNode(last.text+c.text);}else{// default key for nested array children (likely generated by v-for)
if(isTrue(children._isVList)&&isDef(c.tag)&&isUndef(c.key)&&isDef(nestedIndex)){c.key="__vlist"+nestedIndex+"_"+i+"__";}res.push(c);}}}return res;}/*  */function ensureCtor(comp,base){if(comp.__esModule||hasSymbol&&comp[Symbol.toStringTag]==='Module'){comp=comp.default;}return isObject(comp)?base.extend(comp):comp;}function createAsyncPlaceholder(factory,data,context,children,tag){var node=createEmptyVNode();node.asyncFactory=factory;node.asyncMeta={data:data,context:context,children:children,tag:tag};return node;}function resolveAsyncComponent(factory,baseCtor,context){if(isTrue(factory.error)&&isDef(factory.errorComp)){return factory.errorComp;}if(isDef(factory.resolved)){return factory.resolved;}if(isTrue(factory.loading)&&isDef(factory.loadingComp)){return factory.loadingComp;}if(isDef(factory.contexts)){// already pending
factory.contexts.push(context);}else{var contexts=factory.contexts=[context];var sync=true;var forceRender=function forceRender(renderCompleted){for(var i=0,l=contexts.length;i<l;i++){contexts[i].$forceUpdate();}if(renderCompleted){contexts.length=0;}};var resolve=once(function(res){// cache resolved
factory.resolved=ensureCtor(res,baseCtor);// invoke callbacks only if this is not a synchronous resolve
// (async resolves are shimmed as synchronous during SSR)
if(!sync){forceRender(true);}else{contexts.length=0;}});var reject=once(function(reason){process.env.NODE_ENV!=='production'&&warn("Failed to resolve async component: "+String(factory)+(reason?"\nReason: "+reason:''));if(isDef(factory.errorComp)){factory.error=true;forceRender(true);}});var res=factory(resolve,reject);if(isObject(res)){if(typeof res.then==='function'){// () => Promise
if(isUndef(factory.resolved)){res.then(resolve,reject);}}else if(isDef(res.component)&&typeof res.component.then==='function'){res.component.then(resolve,reject);if(isDef(res.error)){factory.errorComp=ensureCtor(res.error,baseCtor);}if(isDef(res.loading)){factory.loadingComp=ensureCtor(res.loading,baseCtor);if(res.delay===0){factory.loading=true;}else{setTimeout(function(){if(isUndef(factory.resolved)&&isUndef(factory.error)){factory.loading=true;forceRender(false);}},res.delay||200);}}if(isDef(res.timeout)){setTimeout(function(){if(isUndef(factory.resolved)){reject(process.env.NODE_ENV!=='production'?"timeout ("+res.timeout+"ms)":null);}},res.timeout);}}}sync=false;// return in case resolved synchronously
return factory.loading?factory.loadingComp:factory.resolved;}}/*  */function isAsyncPlaceholder(node){return node.isComment&&node.asyncFactory;}/*  */function getFirstComponentChild(children){if(Array.isArray(children)){for(var i=0;i<children.length;i++){var c=children[i];if(isDef(c)&&(isDef(c.componentOptions)||isAsyncPlaceholder(c))){return c;}}}}/*  *//*  */function initEvents(vm){vm._events=Object.create(null);vm._hasHookEvent=false;// init parent attached events
var listeners=vm.$options._parentListeners;if(listeners){updateComponentListeners(vm,listeners);}}var target;function add(event,fn){target.$on(event,fn);}function remove$1(event,fn){target.$off(event,fn);}function createOnceHandler(event,fn){var _target=target;return function onceHandler(){var res=fn.apply(null,arguments);if(res!==null){_target.$off(event,onceHandler);}};}function updateComponentListeners(vm,listeners,oldListeners){target=vm;updateListeners(listeners,oldListeners||{},add,remove$1,createOnceHandler,vm);target=undefined;}function eventsMixin(Vue){var hookRE=/^hook:/;Vue.prototype.$on=function(event,fn){var vm=this;if(Array.isArray(event)){for(var i=0,l=event.length;i<l;i++){vm.$on(event[i],fn);}}else{(vm._events[event]||(vm._events[event]=[])).push(fn);// optimize hook:event cost by using a boolean flag marked at registration
// instead of a hash lookup
if(hookRE.test(event)){vm._hasHookEvent=true;}}return vm;};Vue.prototype.$once=function(event,fn){var vm=this;function on(){vm.$off(event,on);fn.apply(vm,arguments);}on.fn=fn;vm.$on(event,on);return vm;};Vue.prototype.$off=function(event,fn){var vm=this;// all
if(!arguments.length){vm._events=Object.create(null);return vm;}// array of events
if(Array.isArray(event)){for(var i$1=0,l=event.length;i$1<l;i$1++){vm.$off(event[i$1],fn);}return vm;}// specific event
var cbs=vm._events[event];if(!cbs){return vm;}if(!fn){vm._events[event]=null;return vm;}// specific handler
var cb;var i=cbs.length;while(i--){cb=cbs[i];if(cb===fn||cb.fn===fn){cbs.splice(i,1);break;}}return vm;};Vue.prototype.$emit=function(event){var vm=this;if(process.env.NODE_ENV!=='production'){var lowerCaseEvent=event.toLowerCase();if(lowerCaseEvent!==event&&vm._events[lowerCaseEvent]){tip("Event \""+lowerCaseEvent+"\" is emitted in component "+formatComponentName(vm)+" but the handler is registered for \""+event+"\". "+"Note that HTML attributes are case-insensitive and you cannot use "+"v-on to listen to camelCase events when using in-DOM templates. "+"You should probably use \""+hyphenate(event)+"\" instead of \""+event+"\".");}}var cbs=vm._events[event];if(cbs){cbs=cbs.length>1?toArray(cbs):cbs;var args=toArray(arguments,1);for(var i=0,l=cbs.length;i<l;i++){try{cbs[i].apply(vm,args);}catch(e){handleError(e,vm,"event handler for \""+event+"\"");}}}return vm;};}/*  *//**
 * Runtime helper for resolving raw children VNodes into a slot object.
 */function resolveSlots(children,context){var slots={};if(!children){return slots;}for(var i=0,l=children.length;i<l;i++){var child=children[i];var data=child.data;// remove slot attribute if the node is resolved as a Vue slot node
if(data&&data.attrs&&data.attrs.slot){delete data.attrs.slot;}// named slots should only be respected if the vnode was rendered in the
// same context.
if((child.context===context||child.fnContext===context)&&data&&data.slot!=null){var name=data.slot;var slot=slots[name]||(slots[name]=[]);if(child.tag==='template'){slot.push.apply(slot,child.children||[]);}else{slot.push(child);}}else{(slots.default||(slots.default=[])).push(child);}}// ignore slots that contains only whitespace
for(var name$1 in slots){if(slots[name$1].every(isWhitespace)){delete slots[name$1];}}return slots;}function isWhitespace(node){return node.isComment&&!node.asyncFactory||node.text===' ';}function resolveScopedSlots(fns,// see flow/vnode
res){res=res||{};for(var i=0;i<fns.length;i++){if(Array.isArray(fns[i])){resolveScopedSlots(fns[i],res);}else{res[fns[i].key]=fns[i].fn;}}return res;}/*  */var activeInstance=null;var isUpdatingChildComponent=false;function setActiveInstance(vm){var prevActiveInstance=activeInstance;activeInstance=vm;return function(){activeInstance=prevActiveInstance;};}function initLifecycle(vm){var options=vm.$options;// locate first non-abstract parent
var parent=options.parent;if(parent&&!options.abstract){while(parent.$options.abstract&&parent.$parent){parent=parent.$parent;}parent.$children.push(vm);}vm.$parent=parent;vm.$root=parent?parent.$root:vm;vm.$children=[];vm.$refs={};vm._watcher=null;vm._inactive=null;vm._directInactive=false;vm._isMounted=false;vm._isDestroyed=false;vm._isBeingDestroyed=false;}function lifecycleMixin(Vue){Vue.prototype._update=function(vnode,hydrating){var vm=this;var prevEl=vm.$el;var prevVnode=vm._vnode;var restoreActiveInstance=setActiveInstance(vm);vm._vnode=vnode;// Vue.prototype.__patch__ is injected in entry points
// based on the rendering backend used.
if(!prevVnode){// initial render
vm.$el=vm.__patch__(vm.$el,vnode,hydrating,false/* removeOnly */);}else{// updates
vm.$el=vm.__patch__(prevVnode,vnode);}restoreActiveInstance();// update __vue__ reference
if(prevEl){prevEl.__vue__=null;}if(vm.$el){vm.$el.__vue__=vm;}// if parent is an HOC, update its $el as well
if(vm.$vnode&&vm.$parent&&vm.$vnode===vm.$parent._vnode){vm.$parent.$el=vm.$el;}// updated hook is called by the scheduler to ensure that children are
// updated in a parent's updated hook.
};Vue.prototype.$forceUpdate=function(){var vm=this;if(vm._watcher){vm._watcher.update();}};Vue.prototype.$destroy=function(){var vm=this;if(vm._isBeingDestroyed){return;}callHook(vm,'beforeDestroy');vm._isBeingDestroyed=true;// remove self from parent
var parent=vm.$parent;if(parent&&!parent._isBeingDestroyed&&!vm.$options.abstract){remove(parent.$children,vm);}// teardown watchers
if(vm._watcher){vm._watcher.teardown();}var i=vm._watchers.length;while(i--){vm._watchers[i].teardown();}// remove reference from data ob
// frozen object may not have observer.
if(vm._data.__ob__){vm._data.__ob__.vmCount--;}// call the last hook...
vm._isDestroyed=true;// invoke destroy hooks on current rendered tree
vm.__patch__(vm._vnode,null);// fire destroyed hook
callHook(vm,'destroyed');// turn off all instance listeners.
vm.$off();// remove __vue__ reference
if(vm.$el){vm.$el.__vue__=null;}// release circular reference (#6759)
if(vm.$vnode){vm.$vnode.parent=null;}};}function mountComponent(vm,el,hydrating){vm.$el=el;if(!vm.$options.render){vm.$options.render=createEmptyVNode;if(process.env.NODE_ENV!=='production'){/* istanbul ignore if */if(vm.$options.template&&vm.$options.template.charAt(0)!=='#'||vm.$options.el||el){warn('You are using the runtime-only build of Vue where the template '+'compiler is not available. Either pre-compile the templates into '+'render functions, or use the compiler-included build.',vm);}else{warn('Failed to mount component: template or render function not defined.',vm);}}}callHook(vm,'beforeMount');var updateComponent;/* istanbul ignore if */if(process.env.NODE_ENV!=='production'&&config.performance&&mark){updateComponent=function updateComponent(){var name=vm._name;var id=vm._uid;var startTag="vue-perf-start:"+id;var endTag="vue-perf-end:"+id;mark(startTag);var vnode=vm._render();mark(endTag);measure("vue "+name+" render",startTag,endTag);mark(startTag);vm._update(vnode,hydrating);mark(endTag);measure("vue "+name+" patch",startTag,endTag);};}else{updateComponent=function updateComponent(){vm._update(vm._render(),hydrating);};}// we set this to vm._watcher inside the watcher's constructor
// since the watcher's initial patch may call $forceUpdate (e.g. inside child
// component's mounted hook), which relies on vm._watcher being already defined
new Watcher(vm,updateComponent,noop,{before:function before(){if(vm._isMounted&&!vm._isDestroyed){callHook(vm,'beforeUpdate');}}},true/* isRenderWatcher */);hydrating=false;// manually mounted instance, call mounted on self
// mounted is called for render-created child components in its inserted hook
if(vm.$vnode==null){vm._isMounted=true;callHook(vm,'mounted');}return vm;}function updateChildComponent(vm,propsData,listeners,parentVnode,renderChildren){if(process.env.NODE_ENV!=='production'){isUpdatingChildComponent=true;}// determine whether component has slot children
// we need to do this before overwriting $options._renderChildren
var hasChildren=!!(renderChildren||// has new static slots
vm.$options._renderChildren||// has old static slots
parentVnode.data.scopedSlots||// has new scoped slots
vm.$scopedSlots!==emptyObject// has old scoped slots
);vm.$options._parentVnode=parentVnode;vm.$vnode=parentVnode;// update vm's placeholder node without re-render
if(vm._vnode){// update child tree's parent
vm._vnode.parent=parentVnode;}vm.$options._renderChildren=renderChildren;// update $attrs and $listeners hash
// these are also reactive so they may trigger child update if the child
// used them during render
vm.$attrs=parentVnode.data.attrs||emptyObject;vm.$listeners=listeners||emptyObject;// update props
if(propsData&&vm.$options.props){toggleObserving(false);var props=vm._props;var propKeys=vm.$options._propKeys||[];for(var i=0;i<propKeys.length;i++){var key=propKeys[i];var propOptions=vm.$options.props;// wtf flow?
props[key]=validateProp(key,propOptions,propsData,vm);}toggleObserving(true);// keep a copy of raw propsData
vm.$options.propsData=propsData;}// update listeners
listeners=listeners||emptyObject;var oldListeners=vm.$options._parentListeners;vm.$options._parentListeners=listeners;updateComponentListeners(vm,listeners,oldListeners);// resolve slots + force update if has children
if(hasChildren){vm.$slots=resolveSlots(renderChildren,parentVnode.context);vm.$forceUpdate();}if(process.env.NODE_ENV!=='production'){isUpdatingChildComponent=false;}}function isInInactiveTree(vm){while(vm&&(vm=vm.$parent)){if(vm._inactive){return true;}}return false;}function activateChildComponent(vm,direct){if(direct){vm._directInactive=false;if(isInInactiveTree(vm)){return;}}else if(vm._directInactive){return;}if(vm._inactive||vm._inactive===null){vm._inactive=false;for(var i=0;i<vm.$children.length;i++){activateChildComponent(vm.$children[i]);}callHook(vm,'activated');}}function deactivateChildComponent(vm,direct){if(direct){vm._directInactive=true;if(isInInactiveTree(vm)){return;}}if(!vm._inactive){vm._inactive=true;for(var i=0;i<vm.$children.length;i++){deactivateChildComponent(vm.$children[i]);}callHook(vm,'deactivated');}}function callHook(vm,hook){// #7573 disable dep collection when invoking lifecycle hooks
pushTarget();var handlers=vm.$options[hook];if(handlers){for(var i=0,j=handlers.length;i<j;i++){try{handlers[i].call(vm);}catch(e){handleError(e,vm,hook+" hook");}}}if(vm._hasHookEvent){vm.$emit('hook:'+hook);}popTarget();}/*  */var MAX_UPDATE_COUNT=100;var queue=[];var activatedChildren=[];var has={};var circular={};var waiting=false;var flushing=false;var index=0;/**
 * Reset the scheduler's state.
 */function resetSchedulerState(){index=queue.length=activatedChildren.length=0;has={};if(process.env.NODE_ENV!=='production'){circular={};}waiting=flushing=false;}/**
 * Flush both queues and run the watchers.
 */function flushSchedulerQueue(){flushing=true;var watcher,id;// Sort queue before flush.
// This ensures that:
// 1. Components are updated from parent to child. (because parent is always
//    created before the child)
// 2. A component's user watchers are run before its render watcher (because
//    user watchers are created before the render watcher)
// 3. If a component is destroyed during a parent component's watcher run,
//    its watchers can be skipped.
queue.sort(function(a,b){return a.id-b.id;});// do not cache length because more watchers might be pushed
// as we run existing watchers
for(index=0;index<queue.length;index++){watcher=queue[index];if(watcher.before){watcher.before();}id=watcher.id;has[id]=null;watcher.run();// in dev build, check and stop circular updates.
if(process.env.NODE_ENV!=='production'&&has[id]!=null){circular[id]=(circular[id]||0)+1;if(circular[id]>MAX_UPDATE_COUNT){warn('You may have an infinite update loop '+(watcher.user?"in watcher with expression \""+watcher.expression+"\"":"in a component render function."),watcher.vm);break;}}}// keep copies of post queues before resetting state
var activatedQueue=activatedChildren.slice();var updatedQueue=queue.slice();resetSchedulerState();// call component updated and activated hooks
callActivatedHooks(activatedQueue);callUpdatedHooks(updatedQueue);// devtool hook
/* istanbul ignore if */if(devtools&&config.devtools){devtools.emit('flush');}}function callUpdatedHooks(queue){var i=queue.length;while(i--){var watcher=queue[i];var vm=watcher.vm;if(vm._watcher===watcher&&vm._isMounted&&!vm._isDestroyed){callHook(vm,'updated');}}}/**
 * Queue a kept-alive component that was activated during patch.
 * The queue will be processed after the entire tree has been patched.
 */function queueActivatedComponent(vm){// setting _inactive to false here so that a render function can
// rely on checking whether it's in an inactive tree (e.g. router-view)
vm._inactive=false;activatedChildren.push(vm);}function callActivatedHooks(queue){for(var i=0;i<queue.length;i++){queue[i]._inactive=true;activateChildComponent(queue[i],true/* true */);}}/**
 * Push a watcher into the watcher queue.
 * Jobs with duplicate IDs will be skipped unless it's
 * pushed when the queue is being flushed.
 */function queueWatcher(watcher){var id=watcher.id;if(has[id]==null){has[id]=true;if(!flushing){queue.push(watcher);}else{// if already flushing, splice the watcher based on its id
// if already past its id, it will be run next immediately.
var i=queue.length-1;while(i>index&&queue[i].id>watcher.id){i--;}queue.splice(i+1,0,watcher);}// queue the flush
if(!waiting){waiting=true;if(process.env.NODE_ENV!=='production'&&!config.async){flushSchedulerQueue();return;}nextTick(flushSchedulerQueue);}}}/*  */var uid$1=0;/**
 * A watcher parses an expression, collects dependencies,
 * and fires callback when the expression value changes.
 * This is used for both the $watch() api and directives.
 */var Watcher=function Watcher(vm,expOrFn,cb,options,isRenderWatcher){this.vm=vm;if(isRenderWatcher){vm._watcher=this;}vm._watchers.push(this);// options
if(options){this.deep=!!options.deep;this.user=!!options.user;this.lazy=!!options.lazy;this.sync=!!options.sync;this.before=options.before;}else{this.deep=this.user=this.lazy=this.sync=false;}this.cb=cb;this.id=++uid$1;// uid for batching
this.active=true;this.dirty=this.lazy;// for lazy watchers
this.deps=[];this.newDeps=[];this.depIds=new _Set();this.newDepIds=new _Set();this.expression=process.env.NODE_ENV!=='production'?expOrFn.toString():'';// parse expression for getter
if(typeof expOrFn==='function'){this.getter=expOrFn;}else{this.getter=parsePath(expOrFn);if(!this.getter){this.getter=noop;process.env.NODE_ENV!=='production'&&warn("Failed watching path: \""+expOrFn+"\" "+'Watcher only accepts simple dot-delimited paths. '+'For full control, use a function instead.',vm);}}this.value=this.lazy?undefined:this.get();};/**
 * Evaluate the getter, and re-collect dependencies.
 */Watcher.prototype.get=function get(){pushTarget(this);var value;var vm=this.vm;try{value=this.getter.call(vm,vm);}catch(e){if(this.user){handleError(e,vm,"getter for watcher \""+this.expression+"\"");}else{throw e;}}finally{// "touch" every property so they are all tracked as
// dependencies for deep watching
if(this.deep){traverse(value);}popTarget();this.cleanupDeps();}return value;};/**
 * Add a dependency to this directive.
 */Watcher.prototype.addDep=function addDep(dep){var id=dep.id;if(!this.newDepIds.has(id)){this.newDepIds.add(id);this.newDeps.push(dep);if(!this.depIds.has(id)){dep.addSub(this);}}};/**
 * Clean up for dependency collection.
 */Watcher.prototype.cleanupDeps=function cleanupDeps(){var i=this.deps.length;while(i--){var dep=this.deps[i];if(!this.newDepIds.has(dep.id)){dep.removeSub(this);}}var tmp=this.depIds;this.depIds=this.newDepIds;this.newDepIds=tmp;this.newDepIds.clear();tmp=this.deps;this.deps=this.newDeps;this.newDeps=tmp;this.newDeps.length=0;};/**
 * Subscriber interface.
 * Will be called when a dependency changes.
 */Watcher.prototype.update=function update(){/* istanbul ignore else */if(this.lazy){this.dirty=true;}else if(this.sync){this.run();}else{queueWatcher(this);}};/**
 * Scheduler job interface.
 * Will be called by the scheduler.
 */Watcher.prototype.run=function run(){if(this.active){var value=this.get();if(value!==this.value||// Deep watchers and watchers on Object/Arrays should fire even
// when the value is the same, because the value may
// have mutated.
isObject(value)||this.deep){// set new value
var oldValue=this.value;this.value=value;if(this.user){try{this.cb.call(this.vm,value,oldValue);}catch(e){handleError(e,this.vm,"callback for watcher \""+this.expression+"\"");}}else{this.cb.call(this.vm,value,oldValue);}}}};/**
 * Evaluate the value of the watcher.
 * This only gets called for lazy watchers.
 */Watcher.prototype.evaluate=function evaluate(){this.value=this.get();this.dirty=false;};/**
 * Depend on all deps collected by this watcher.
 */Watcher.prototype.depend=function depend(){var i=this.deps.length;while(i--){this.deps[i].depend();}};/**
 * Remove self from all dependencies' subscriber list.
 */Watcher.prototype.teardown=function teardown(){if(this.active){// remove self from vm's watcher list
// this is a somewhat expensive operation so we skip it
// if the vm is being destroyed.
if(!this.vm._isBeingDestroyed){remove(this.vm._watchers,this);}var i=this.deps.length;while(i--){this.deps[i].removeSub(this);}this.active=false;}};/*  */var sharedPropertyDefinition={enumerable:true,configurable:true,get:noop,set:noop};function proxy(target,sourceKey,key){sharedPropertyDefinition.get=function proxyGetter(){return this[sourceKey][key];};sharedPropertyDefinition.set=function proxySetter(val){this[sourceKey][key]=val;};Object.defineProperty(target,key,sharedPropertyDefinition);}function initState(vm){vm._watchers=[];var opts=vm.$options;if(opts.props){initProps(vm,opts.props);}if(opts.methods){initMethods(vm,opts.methods);}if(opts.data){initData(vm);}else{observe(vm._data={},true/* asRootData */);}if(opts.computed){initComputed(vm,opts.computed);}if(opts.watch&&opts.watch!==nativeWatch){initWatch(vm,opts.watch);}}function initProps(vm,propsOptions){var propsData=vm.$options.propsData||{};var props=vm._props={};// cache prop keys so that future props updates can iterate using Array
// instead of dynamic object key enumeration.
var keys=vm.$options._propKeys=[];var isRoot=!vm.$parent;// root instance props should be converted
if(!isRoot){toggleObserving(false);}var loop=function loop(key){keys.push(key);var value=validateProp(key,propsOptions,propsData,vm);/* istanbul ignore else */if(process.env.NODE_ENV!=='production'){var hyphenatedKey=hyphenate(key);if(isReservedAttribute(hyphenatedKey)||config.isReservedAttr(hyphenatedKey)){warn("\""+hyphenatedKey+"\" is a reserved attribute and cannot be used as component prop.",vm);}defineReactive$$1(props,key,value,function(){if(!isRoot&&!isUpdatingChildComponent){warn("Avoid mutating a prop directly since the value will be "+"overwritten whenever the parent component re-renders. "+"Instead, use a data or computed property based on the prop's "+"value. Prop being mutated: \""+key+"\"",vm);}});}else{defineReactive$$1(props,key,value);}// static props are already proxied on the component's prototype
// during Vue.extend(). We only need to proxy props defined at
// instantiation here.
if(!(key in vm)){proxy(vm,"_props",key);}};for(var key in propsOptions){loop(key);}toggleObserving(true);}function initData(vm){var data=vm.$options.data;data=vm._data=typeof data==='function'?getData(data,vm):data||{};if(!isPlainObject(data)){data={};process.env.NODE_ENV!=='production'&&warn('data functions should return an object:\n'+'https://vuejs.org/v2/guide/components.html#data-Must-Be-a-Function',vm);}// proxy data on instance
var keys=Object.keys(data);var props=vm.$options.props;var methods=vm.$options.methods;var i=keys.length;while(i--){var key=keys[i];if(process.env.NODE_ENV!=='production'){if(methods&&hasOwn(methods,key)){warn("Method \""+key+"\" has already been defined as a data property.",vm);}}if(props&&hasOwn(props,key)){process.env.NODE_ENV!=='production'&&warn("The data property \""+key+"\" is already declared as a prop. "+"Use prop default value instead.",vm);}else if(!isReserved(key)){proxy(vm,"_data",key);}}// observe data
observe(data,true/* asRootData */);}function getData(data,vm){// #7573 disable dep collection when invoking data getters
pushTarget();try{return data.call(vm,vm);}catch(e){handleError(e,vm,"data()");return{};}finally{popTarget();}}var computedWatcherOptions={lazy:true};function initComputed(vm,computed){// $flow-disable-line
var watchers=vm._computedWatchers=Object.create(null);// computed properties are just getters during SSR
var isSSR=isServerRendering();for(var key in computed){var userDef=computed[key];var getter=typeof userDef==='function'?userDef:userDef.get;if(process.env.NODE_ENV!=='production'&&getter==null){warn("Getter is missing for computed property \""+key+"\".",vm);}if(!isSSR){// create internal watcher for the computed property.
watchers[key]=new Watcher(vm,getter||noop,noop,computedWatcherOptions);}// component-defined computed properties are already defined on the
// component prototype. We only need to define computed properties defined
// at instantiation here.
if(!(key in vm)){defineComputed(vm,key,userDef);}else if(process.env.NODE_ENV!=='production'){if(key in vm.$data){warn("The computed property \""+key+"\" is already defined in data.",vm);}else if(vm.$options.props&&key in vm.$options.props){warn("The computed property \""+key+"\" is already defined as a prop.",vm);}}}}function defineComputed(target,key,userDef){var shouldCache=!isServerRendering();if(typeof userDef==='function'){sharedPropertyDefinition.get=shouldCache?createComputedGetter(key):createGetterInvoker(userDef);sharedPropertyDefinition.set=noop;}else{sharedPropertyDefinition.get=userDef.get?shouldCache&&userDef.cache!==false?createComputedGetter(key):createGetterInvoker(userDef.get):noop;sharedPropertyDefinition.set=userDef.set||noop;}if(process.env.NODE_ENV!=='production'&&sharedPropertyDefinition.set===noop){sharedPropertyDefinition.set=function(){warn("Computed property \""+key+"\" was assigned to but it has no setter.",this);};}Object.defineProperty(target,key,sharedPropertyDefinition);}function createComputedGetter(key){return function computedGetter(){var watcher=this._computedWatchers&&this._computedWatchers[key];if(watcher){if(watcher.dirty){watcher.evaluate();}if(Dep.target){watcher.depend();}return watcher.value;}};}function createGetterInvoker(fn){return function computedGetter(){return fn.call(this,this);};}function initMethods(vm,methods){var props=vm.$options.props;for(var key in methods){if(process.env.NODE_ENV!=='production'){if(typeof methods[key]!=='function'){warn("Method \""+key+"\" has type \""+_typeof2(methods[key])+"\" in the component definition. "+"Did you reference the function correctly?",vm);}if(props&&hasOwn(props,key)){warn("Method \""+key+"\" has already been defined as a prop.",vm);}if(key in vm&&isReserved(key)){warn("Method \""+key+"\" conflicts with an existing Vue instance method. "+"Avoid defining component methods that start with _ or $.");}}vm[key]=typeof methods[key]!=='function'?noop:bind(methods[key],vm);}}function initWatch(vm,watch){for(var key in watch){var handler=watch[key];if(Array.isArray(handler)){for(var i=0;i<handler.length;i++){createWatcher(vm,key,handler[i]);}}else{createWatcher(vm,key,handler);}}}function createWatcher(vm,expOrFn,handler,options){if(isPlainObject(handler)){options=handler;handler=handler.handler;}if(typeof handler==='string'){handler=vm[handler];}return vm.$watch(expOrFn,handler,options);}function stateMixin(Vue){// flow somehow has problems with directly declared definition object
// when using Object.defineProperty, so we have to procedurally build up
// the object here.
var dataDef={};dataDef.get=function(){return this._data;};var propsDef={};propsDef.get=function(){return this._props;};if(process.env.NODE_ENV!=='production'){dataDef.set=function(){warn('Avoid replacing instance root $data. '+'Use nested data properties instead.',this);};propsDef.set=function(){warn("$props is readonly.",this);};}Object.defineProperty(Vue.prototype,'$data',dataDef);Object.defineProperty(Vue.prototype,'$props',propsDef);Vue.prototype.$set=set;Vue.prototype.$delete=del;Vue.prototype.$watch=function(expOrFn,cb,options){var vm=this;if(isPlainObject(cb)){return createWatcher(vm,expOrFn,cb,options);}options=options||{};options.user=true;var watcher=new Watcher(vm,expOrFn,cb,options);if(options.immediate){try{cb.call(vm,watcher.value);}catch(error){handleError(error,vm,"callback for immediate watcher \""+watcher.expression+"\"");}}return function unwatchFn(){watcher.teardown();};};}/*  */function initProvide(vm){var provide=vm.$options.provide;if(provide){vm._provided=typeof provide==='function'?provide.call(vm):provide;}}function initInjections(vm){var result=resolveInject(vm.$options.inject,vm);if(result){toggleObserving(false);Object.keys(result).forEach(function(key){/* istanbul ignore else */if(process.env.NODE_ENV!=='production'){defineReactive$$1(vm,key,result[key],function(){warn("Avoid mutating an injected value directly since the changes will be "+"overwritten whenever the provided component re-renders. "+"injection being mutated: \""+key+"\"",vm);});}else{defineReactive$$1(vm,key,result[key]);}});toggleObserving(true);}}function resolveInject(inject,vm){if(inject){// inject is :any because flow is not smart enough to figure out cached
var result=Object.create(null);var keys=hasSymbol?Reflect.ownKeys(inject).filter(function(key){/* istanbul ignore next */return Object.getOwnPropertyDescriptor(inject,key).enumerable;}):Object.keys(inject);for(var i=0;i<keys.length;i++){var key=keys[i];var provideKey=inject[key].from;var source=vm;while(source){if(source._provided&&hasOwn(source._provided,provideKey)){result[key]=source._provided[provideKey];break;}source=source.$parent;}if(!source){if('default'in inject[key]){var provideDefault=inject[key].default;result[key]=typeof provideDefault==='function'?provideDefault.call(vm):provideDefault;}else if(process.env.NODE_ENV!=='production'){warn("Injection \""+key+"\" not found",vm);}}}return result;}}/*  *//**
 * Runtime helper for rendering v-for lists.
 */function renderList(val,render){var ret,i,l,keys,key;if(Array.isArray(val)||typeof val==='string'){ret=new Array(val.length);for(i=0,l=val.length;i<l;i++){ret[i]=render(val[i],i);}}else if(typeof val==='number'){ret=new Array(val);for(i=0;i<val;i++){ret[i]=render(i+1,i);}}else if(isObject(val)){keys=Object.keys(val);ret=new Array(keys.length);for(i=0,l=keys.length;i<l;i++){key=keys[i];ret[i]=render(val[key],key,i);}}if(!isDef(ret)){ret=[];}ret._isVList=true;return ret;}/*  *//**
 * Runtime helper for rendering <slot>
 */function renderSlot(name,fallback,props,bindObject){var scopedSlotFn=this.$scopedSlots[name];var nodes;if(scopedSlotFn){// scoped slot
props=props||{};if(bindObject){if(process.env.NODE_ENV!=='production'&&!isObject(bindObject)){warn('slot v-bind without argument expects an Object',this);}props=extend(extend({},bindObject),props);}nodes=scopedSlotFn(props)||fallback;}else{nodes=this.$slots[name]||fallback;}var target=props&&props.slot;if(target){return this.$createElement('template',{slot:target},nodes);}else{return nodes;}}/*  *//**
 * Runtime helper for resolving filters
 */function resolveFilter(id){return resolveAsset(this.$options,'filters',id,true)||identity;}/*  */function isKeyNotMatch(expect,actual){if(Array.isArray(expect)){return expect.indexOf(actual)===-1;}else{return expect!==actual;}}/**
 * Runtime helper for checking keyCodes from config.
 * exposed as Vue.prototype._k
 * passing in eventKeyName as last argument separately for backwards compat
 */function checkKeyCodes(eventKeyCode,key,builtInKeyCode,eventKeyName,builtInKeyName){var mappedKeyCode=config.keyCodes[key]||builtInKeyCode;if(builtInKeyName&&eventKeyName&&!config.keyCodes[key]){return isKeyNotMatch(builtInKeyName,eventKeyName);}else if(mappedKeyCode){return isKeyNotMatch(mappedKeyCode,eventKeyCode);}else if(eventKeyName){return hyphenate(eventKeyName)!==key;}}/*  *//**
 * Runtime helper for merging v-bind="object" into a VNode's data.
 */function bindObjectProps(data,tag,value,asProp,isSync){if(value){if(!isObject(value)){process.env.NODE_ENV!=='production'&&warn('v-bind without argument expects an Object or Array value',this);}else{if(Array.isArray(value)){value=toObject(value);}var hash;var loop=function loop(key){if(key==='class'||key==='style'||isReservedAttribute(key)){hash=data;}else{var type=data.attrs&&data.attrs.type;hash=asProp||config.mustUseProp(tag,type,key)?data.domProps||(data.domProps={}):data.attrs||(data.attrs={});}var camelizedKey=camelize(key);if(!(key in hash)&&!(camelizedKey in hash)){hash[key]=value[key];if(isSync){var on=data.on||(data.on={});on["update:"+camelizedKey]=function($event){value[key]=$event;};}}};for(var key in value){loop(key);}}}return data;}/*  *//**
 * Runtime helper for rendering static trees.
 */function renderStatic(index,isInFor){var cached=this._staticTrees||(this._staticTrees=[]);var tree=cached[index];// if has already-rendered static tree and not inside v-for,
// we can reuse the same tree.
if(tree&&!isInFor){return tree;}// otherwise, render a fresh tree.
tree=cached[index]=this.$options.staticRenderFns[index].call(this._renderProxy,null,this// for render fns generated for functional component templates
);markStatic(tree,"__static__"+index,false);return tree;}/**
 * Runtime helper for v-once.
 * Effectively it means marking the node as static with a unique key.
 */function markOnce(tree,index,key){markStatic(tree,"__once__"+index+(key?"_"+key:""),true);return tree;}function markStatic(tree,key,isOnce){if(Array.isArray(tree)){for(var i=0;i<tree.length;i++){if(tree[i]&&typeof tree[i]!=='string'){markStaticNode(tree[i],key+"_"+i,isOnce);}}}else{markStaticNode(tree,key,isOnce);}}function markStaticNode(node,key,isOnce){node.isStatic=true;node.key=key;node.isOnce=isOnce;}/*  */function bindObjectListeners(data,value){if(value){if(!isPlainObject(value)){process.env.NODE_ENV!=='production'&&warn('v-on without argument expects an Object value',this);}else{var on=data.on=data.on?extend({},data.on):{};for(var key in value){var existing=on[key];var ours=value[key];on[key]=existing?[].concat(existing,ours):ours;}}}return data;}/*  */function installRenderHelpers(target){target._o=markOnce;target._n=toNumber;target._s=toString;target._l=renderList;target._t=renderSlot;target._q=looseEqual;target._i=looseIndexOf;target._m=renderStatic;target._f=resolveFilter;target._k=checkKeyCodes;target._b=bindObjectProps;target._v=createTextVNode;target._e=createEmptyVNode;target._u=resolveScopedSlots;target._g=bindObjectListeners;}/*  */function FunctionalRenderContext(data,props,children,parent,Ctor){var options=Ctor.options;// ensure the createElement function in functional components
// gets a unique context - this is necessary for correct named slot check
var contextVm;if(hasOwn(parent,'_uid')){contextVm=Object.create(parent);// $flow-disable-line
contextVm._original=parent;}else{// the context vm passed in is a functional context as well.
// in this case we want to make sure we are able to get a hold to the
// real context instance.
contextVm=parent;// $flow-disable-line
parent=parent._original;}var isCompiled=isTrue(options._compiled);var needNormalization=!isCompiled;this.data=data;this.props=props;this.children=children;this.parent=parent;this.listeners=data.on||emptyObject;this.injections=resolveInject(options.inject,parent);this.slots=function(){return resolveSlots(children,parent);};// support for compiled functional template
if(isCompiled){// exposing $options for renderStatic()
this.$options=options;// pre-resolve slots for renderSlot()
this.$slots=this.slots();this.$scopedSlots=data.scopedSlots||emptyObject;}if(options._scopeId){this._c=function(a,b,c,d){var vnode=createElement(contextVm,a,b,c,d,needNormalization);if(vnode&&!Array.isArray(vnode)){vnode.fnScopeId=options._scopeId;vnode.fnContext=parent;}return vnode;};}else{this._c=function(a,b,c,d){return createElement(contextVm,a,b,c,d,needNormalization);};}}installRenderHelpers(FunctionalRenderContext.prototype);function createFunctionalComponent(Ctor,propsData,data,contextVm,children){var options=Ctor.options;var props={};var propOptions=options.props;if(isDef(propOptions)){for(var key in propOptions){props[key]=validateProp(key,propOptions,propsData||emptyObject);}}else{if(isDef(data.attrs)){mergeProps(props,data.attrs);}if(isDef(data.props)){mergeProps(props,data.props);}}var renderContext=new FunctionalRenderContext(data,props,children,contextVm,Ctor);var vnode=options.render.call(null,renderContext._c,renderContext);if(vnode instanceof VNode){return cloneAndMarkFunctionalResult(vnode,data,renderContext.parent,options,renderContext);}else if(Array.isArray(vnode)){var vnodes=normalizeChildren(vnode)||[];var res=new Array(vnodes.length);for(var i=0;i<vnodes.length;i++){res[i]=cloneAndMarkFunctionalResult(vnodes[i],data,renderContext.parent,options,renderContext);}return res;}}function cloneAndMarkFunctionalResult(vnode,data,contextVm,options,renderContext){// #7817 clone node before setting fnContext, otherwise if the node is reused
// (e.g. it was from a cached normal slot) the fnContext causes named slots
// that should not be matched to match.
var clone=cloneVNode(vnode);clone.fnContext=contextVm;clone.fnOptions=options;if(process.env.NODE_ENV!=='production'){(clone.devtoolsMeta=clone.devtoolsMeta||{}).renderContext=renderContext;}if(data.slot){(clone.data||(clone.data={})).slot=data.slot;}return clone;}function mergeProps(to,from){for(var key in from){to[camelize(key)]=from[key];}}/*  *//*  *//*  *//*  */// inline hooks to be invoked on component VNodes during patch
var componentVNodeHooks={init:function init(vnode,hydrating){if(vnode.componentInstance&&!vnode.componentInstance._isDestroyed&&vnode.data.keepAlive){// kept-alive components, treat as a patch
var mountedNode=vnode;// work around flow
componentVNodeHooks.prepatch(mountedNode,mountedNode);}else{var child=vnode.componentInstance=createComponentInstanceForVnode(vnode,activeInstance);child.$mount(hydrating?vnode.elm:undefined,hydrating);}},prepatch:function prepatch(oldVnode,vnode){var options=vnode.componentOptions;var child=vnode.componentInstance=oldVnode.componentInstance;updateChildComponent(child,options.propsData,// updated props
options.listeners,// updated listeners
vnode,// new parent vnode
options.children// new children
);},insert:function insert(vnode){var context=vnode.context;var componentInstance=vnode.componentInstance;if(!componentInstance._isMounted){componentInstance._isMounted=true;callHook(componentInstance,'mounted');}if(vnode.data.keepAlive){if(context._isMounted){// vue-router#1212
// During updates, a kept-alive component's child components may
// change, so directly walking the tree here may call activated hooks
// on incorrect children. Instead we push them into a queue which will
// be processed after the whole patch process ended.
queueActivatedComponent(componentInstance);}else{activateChildComponent(componentInstance,true/* direct */);}}},destroy:function destroy(vnode){var componentInstance=vnode.componentInstance;if(!componentInstance._isDestroyed){if(!vnode.data.keepAlive){componentInstance.$destroy();}else{deactivateChildComponent(componentInstance,true/* direct */);}}}};var hooksToMerge=Object.keys(componentVNodeHooks);function createComponent(Ctor,data,context,children,tag){if(isUndef(Ctor)){return;}var baseCtor=context.$options._base;// plain options object: turn it into a constructor
if(isObject(Ctor)){Ctor=baseCtor.extend(Ctor);}// if at this stage it's not a constructor or an async component factory,
// reject.
if(typeof Ctor!=='function'){if(process.env.NODE_ENV!=='production'){warn("Invalid Component definition: "+String(Ctor),context);}return;}// async component
var asyncFactory;if(isUndef(Ctor.cid)){asyncFactory=Ctor;Ctor=resolveAsyncComponent(asyncFactory,baseCtor,context);if(Ctor===undefined){// return a placeholder node for async component, which is rendered
// as a comment node but preserves all the raw information for the node.
// the information will be used for async server-rendering and hydration.
return createAsyncPlaceholder(asyncFactory,data,context,children,tag);}}data=data||{};// resolve constructor options in case global mixins are applied after
// component constructor creation
resolveConstructorOptions(Ctor);// transform component v-model data into props & events
if(isDef(data.model)){transformModel(Ctor.options,data);}// extract props
var propsData=extractPropsFromVNodeData(data,Ctor,tag);// functional component
if(isTrue(Ctor.options.functional)){return createFunctionalComponent(Ctor,propsData,data,context,children);}// extract listeners, since these needs to be treated as
// child component listeners instead of DOM listeners
var listeners=data.on;// replace with listeners with .native modifier
// so it gets processed during parent component patch.
data.on=data.nativeOn;if(isTrue(Ctor.options.abstract)){// abstract components do not keep anything
// other than props & listeners & slot
// work around flow
var slot=data.slot;data={};if(slot){data.slot=slot;}}// install component management hooks onto the placeholder node
installComponentHooks(data);// return a placeholder vnode
var name=Ctor.options.name||tag;var vnode=new VNode("vue-component-"+Ctor.cid+(name?"-"+name:''),data,undefined,undefined,undefined,context,{Ctor:Ctor,propsData:propsData,listeners:listeners,tag:tag,children:children},asyncFactory);return vnode;}function createComponentInstanceForVnode(vnode,// we know it's MountedComponentVNode but flow doesn't
parent// activeInstance in lifecycle state
){var options={_isComponent:true,_parentVnode:vnode,parent:parent};// check inline-template render functions
var inlineTemplate=vnode.data.inlineTemplate;if(isDef(inlineTemplate)){options.render=inlineTemplate.render;options.staticRenderFns=inlineTemplate.staticRenderFns;}return new vnode.componentOptions.Ctor(options);}function installComponentHooks(data){var hooks=data.hook||(data.hook={});for(var i=0;i<hooksToMerge.length;i++){var key=hooksToMerge[i];var existing=hooks[key];var toMerge=componentVNodeHooks[key];if(existing!==toMerge&&!(existing&&existing._merged)){hooks[key]=existing?mergeHook$1(toMerge,existing):toMerge;}}}function mergeHook$1(f1,f2){var merged=function merged(a,b){// flow complains about extra args which is why we use any
f1(a,b);f2(a,b);};merged._merged=true;return merged;}// transform component v-model info (value and callback) into
// prop and event handler respectively.
function transformModel(options,data){var prop=options.model&&options.model.prop||'value';var event=options.model&&options.model.event||'input';(data.props||(data.props={}))[prop]=data.model.value;var on=data.on||(data.on={});var existing=on[event];var callback=data.model.callback;if(isDef(existing)){if(Array.isArray(existing)?existing.indexOf(callback)===-1:existing!==callback){on[event]=[callback].concat(existing);}}else{on[event]=callback;}}/*  */var SIMPLE_NORMALIZE=1;var ALWAYS_NORMALIZE=2;// wrapper function for providing a more flexible interface
// without getting yelled at by flow
function createElement(context,tag,data,children,normalizationType,alwaysNormalize){if(Array.isArray(data)||isPrimitive(data)){normalizationType=children;children=data;data=undefined;}if(isTrue(alwaysNormalize)){normalizationType=ALWAYS_NORMALIZE;}return _createElement(context,tag,data,children,normalizationType);}function _createElement(context,tag,data,children,normalizationType){if(isDef(data)&&isDef(data.__ob__)){process.env.NODE_ENV!=='production'&&warn("Avoid using observed data object as vnode data: "+JSON.stringify(data)+"\n"+'Always create fresh vnode data objects in each render!',context);return createEmptyVNode();}// object syntax in v-bind
if(isDef(data)&&isDef(data.is)){tag=data.is;}if(!tag){// in case of component :is set to falsy value
return createEmptyVNode();}// warn against non-primitive key
if(process.env.NODE_ENV!=='production'&&isDef(data)&&isDef(data.key)&&!isPrimitive(data.key)){{warn('Avoid using non-primitive value as key, '+'use string/number value instead.',context);}}// support single function children as default scoped slot
if(Array.isArray(children)&&typeof children[0]==='function'){data=data||{};data.scopedSlots={default:children[0]};children.length=0;}if(normalizationType===ALWAYS_NORMALIZE){children=normalizeChildren(children);}else if(normalizationType===SIMPLE_NORMALIZE){children=simpleNormalizeChildren(children);}var vnode,ns;if(typeof tag==='string'){var Ctor;ns=context.$vnode&&context.$vnode.ns||config.getTagNamespace(tag);if(config.isReservedTag(tag)){// platform built-in elements
vnode=new VNode(config.parsePlatformTagName(tag),data,children,undefined,undefined,context);}else if((!data||!data.pre)&&isDef(Ctor=resolveAsset(context.$options,'components',tag))){// component
vnode=createComponent(Ctor,data,context,children,tag);}else{// unknown or unlisted namespaced elements
// check at runtime because it may get assigned a namespace when its
// parent normalizes children
vnode=new VNode(tag,data,children,undefined,undefined,context);}}else{// direct component options / constructor
vnode=createComponent(tag,data,context,children);}if(Array.isArray(vnode)){return vnode;}else if(isDef(vnode)){if(isDef(ns)){applyNS(vnode,ns);}if(isDef(data)){registerDeepBindings(data);}return vnode;}else{return createEmptyVNode();}}function applyNS(vnode,ns,force){vnode.ns=ns;if(vnode.tag==='foreignObject'){// use default namespace inside foreignObject
ns=undefined;force=true;}if(isDef(vnode.children)){for(var i=0,l=vnode.children.length;i<l;i++){var child=vnode.children[i];if(isDef(child.tag)&&(isUndef(child.ns)||isTrue(force)&&child.tag!=='svg')){applyNS(child,ns,force);}}}}// ref #5318
// necessary to ensure parent re-render when deep bindings like :style and
// :class are used on slot nodes
function registerDeepBindings(data){if(isObject(data.style)){traverse(data.style);}if(isObject(data.class)){traverse(data.class);}}/*  */function initRender(vm){vm._vnode=null;// the root of the child tree
vm._staticTrees=null;// v-once cached trees
var options=vm.$options;var parentVnode=vm.$vnode=options._parentVnode;// the placeholder node in parent tree
var renderContext=parentVnode&&parentVnode.context;vm.$slots=resolveSlots(options._renderChildren,renderContext);vm.$scopedSlots=emptyObject;// bind the createElement fn to this instance
// so that we get proper render context inside it.
// args order: tag, data, children, normalizationType, alwaysNormalize
// internal version is used by render functions compiled from templates
vm._c=function(a,b,c,d){return createElement(vm,a,b,c,d,false);};// normalization is always applied for the public version, used in
// user-written render functions.
vm.$createElement=function(a,b,c,d){return createElement(vm,a,b,c,d,true);};// $attrs & $listeners are exposed for easier HOC creation.
// they need to be reactive so that HOCs using them are always updated
var parentData=parentVnode&&parentVnode.data;/* istanbul ignore else */if(process.env.NODE_ENV!=='production'){defineReactive$$1(vm,'$attrs',parentData&&parentData.attrs||emptyObject,function(){!isUpdatingChildComponent&&warn("$attrs is readonly.",vm);},true);defineReactive$$1(vm,'$listeners',options._parentListeners||emptyObject,function(){!isUpdatingChildComponent&&warn("$listeners is readonly.",vm);},true);}else{defineReactive$$1(vm,'$attrs',parentData&&parentData.attrs||emptyObject,null,true);defineReactive$$1(vm,'$listeners',options._parentListeners||emptyObject,null,true);}}function renderMixin(Vue){// install runtime convenience helpers
installRenderHelpers(Vue.prototype);Vue.prototype.$nextTick=function(fn){return nextTick(fn,this);};Vue.prototype._render=function(){var vm=this;var ref=vm.$options;var render=ref.render;var _parentVnode=ref._parentVnode;if(_parentVnode){vm.$scopedSlots=_parentVnode.data.scopedSlots||emptyObject;}// set parent vnode. this allows render functions to have access
// to the data on the placeholder node.
vm.$vnode=_parentVnode;// render self
var vnode;try{vnode=render.call(vm._renderProxy,vm.$createElement);}catch(e){handleError(e,vm,"render");// return error render result,
// or previous vnode to prevent render error causing blank component
/* istanbul ignore else */if(process.env.NODE_ENV!=='production'&&vm.$options.renderError){try{vnode=vm.$options.renderError.call(vm._renderProxy,vm.$createElement,e);}catch(e){handleError(e,vm,"renderError");vnode=vm._vnode;}}else{vnode=vm._vnode;}}// return empty vnode in case the render function errored out
if(!(vnode instanceof VNode)){if(process.env.NODE_ENV!=='production'&&Array.isArray(vnode)){warn('Multiple root nodes returned from render function. Render function '+'should return a single root node.',vm);}vnode=createEmptyVNode();}// set parent
vnode.parent=_parentVnode;return vnode;};}/*  */var uid$3=0;function initMixin(Vue){Vue.prototype._init=function(options){var vm=this;// a uid
vm._uid=uid$3++;var startTag,endTag;/* istanbul ignore if */if(process.env.NODE_ENV!=='production'&&config.performance&&mark){startTag="vue-perf-start:"+vm._uid;endTag="vue-perf-end:"+vm._uid;mark(startTag);}// a flag to avoid this being observed
vm._isVue=true;// merge options
if(options&&options._isComponent){// optimize internal component instantiation
// since dynamic options merging is pretty slow, and none of the
// internal component options needs special treatment.
initInternalComponent(vm,options);}else{vm.$options=mergeOptions(resolveConstructorOptions(vm.constructor),options||{},vm);}/* istanbul ignore else */if(process.env.NODE_ENV!=='production'){initProxy(vm);}else{vm._renderProxy=vm;}// expose real self
vm._self=vm;initLifecycle(vm);initEvents(vm);initRender(vm);callHook(vm,'beforeCreate');initInjections(vm);// resolve injections before data/props
initState(vm);initProvide(vm);// resolve provide after data/props
callHook(vm,'created');/* istanbul ignore if */if(process.env.NODE_ENV!=='production'&&config.performance&&mark){vm._name=formatComponentName(vm,false);mark(endTag);measure("vue "+vm._name+" init",startTag,endTag);}if(vm.$options.el){vm.$mount(vm.$options.el);}};}function initInternalComponent(vm,options){var opts=vm.$options=Object.create(vm.constructor.options);// doing this because it's faster than dynamic enumeration.
var parentVnode=options._parentVnode;opts.parent=options.parent;opts._parentVnode=parentVnode;var vnodeComponentOptions=parentVnode.componentOptions;opts.propsData=vnodeComponentOptions.propsData;opts._parentListeners=vnodeComponentOptions.listeners;opts._renderChildren=vnodeComponentOptions.children;opts._componentTag=vnodeComponentOptions.tag;if(options.render){opts.render=options.render;opts.staticRenderFns=options.staticRenderFns;}}function resolveConstructorOptions(Ctor){var options=Ctor.options;if(Ctor.super){var superOptions=resolveConstructorOptions(Ctor.super);var cachedSuperOptions=Ctor.superOptions;if(superOptions!==cachedSuperOptions){// super option changed,
// need to resolve new options.
Ctor.superOptions=superOptions;// check if there are any late-modified/attached options (#4976)
var modifiedOptions=resolveModifiedOptions(Ctor);// update base extend options
if(modifiedOptions){extend(Ctor.extendOptions,modifiedOptions);}options=Ctor.options=mergeOptions(superOptions,Ctor.extendOptions);if(options.name){options.components[options.name]=Ctor;}}}return options;}function resolveModifiedOptions(Ctor){var modified;var latest=Ctor.options;var sealed=Ctor.sealedOptions;for(var key in latest){if(latest[key]!==sealed[key]){if(!modified){modified={};}modified[key]=latest[key];}}return modified;}function Vue(options){if(process.env.NODE_ENV!=='production'&&!(this instanceof Vue)){warn('Vue is a constructor and should be called with the `new` keyword');}this._init(options);}initMixin(Vue);stateMixin(Vue);eventsMixin(Vue);lifecycleMixin(Vue);renderMixin(Vue);/*  */function initUse(Vue){Vue.use=function(plugin){var installedPlugins=this._installedPlugins||(this._installedPlugins=[]);if(installedPlugins.indexOf(plugin)>-1){return this;}// additional parameters
var args=toArray(arguments,1);args.unshift(this);if(typeof plugin.install==='function'){plugin.install.apply(plugin,args);}else if(typeof plugin==='function'){plugin.apply(null,args);}installedPlugins.push(plugin);return this;};}/*  */function initMixin$1(Vue){Vue.mixin=function(mixin){this.options=mergeOptions(this.options,mixin);return this;};}/*  */function initExtend(Vue){/**
   * Each instance constructor, including Vue, has a unique
   * cid. This enables us to create wrapped "child
   * constructors" for prototypal inheritance and cache them.
   */Vue.cid=0;var cid=1;/**
   * Class inheritance
   */Vue.extend=function(extendOptions){extendOptions=extendOptions||{};var Super=this;var SuperId=Super.cid;var cachedCtors=extendOptions._Ctor||(extendOptions._Ctor={});if(cachedCtors[SuperId]){return cachedCtors[SuperId];}var name=extendOptions.name||Super.options.name;if(process.env.NODE_ENV!=='production'&&name){validateComponentName(name);}var Sub=function VueComponent(options){this._init(options);};Sub.prototype=Object.create(Super.prototype);Sub.prototype.constructor=Sub;Sub.cid=cid++;Sub.options=mergeOptions(Super.options,extendOptions);Sub['super']=Super;// For props and computed properties, we define the proxy getters on
// the Vue instances at extension time, on the extended prototype. This
// avoids Object.defineProperty calls for each instance created.
if(Sub.options.props){initProps$1(Sub);}if(Sub.options.computed){initComputed$1(Sub);}// allow further extension/mixin/plugin usage
Sub.extend=Super.extend;Sub.mixin=Super.mixin;Sub.use=Super.use;// create asset registers, so extended classes
// can have their private assets too.
ASSET_TYPES.forEach(function(type){Sub[type]=Super[type];});// enable recursive self-lookup
if(name){Sub.options.components[name]=Sub;}// keep a reference to the super options at extension time.
// later at instantiation we can check if Super's options have
// been updated.
Sub.superOptions=Super.options;Sub.extendOptions=extendOptions;Sub.sealedOptions=extend({},Sub.options);// cache constructor
cachedCtors[SuperId]=Sub;return Sub;};}function initProps$1(Comp){var props=Comp.options.props;for(var key in props){proxy(Comp.prototype,"_props",key);}}function initComputed$1(Comp){var computed=Comp.options.computed;for(var key in computed){defineComputed(Comp.prototype,key,computed[key]);}}/*  */function initAssetRegisters(Vue){/**
   * Create asset registration methods.
   */ASSET_TYPES.forEach(function(type){Vue[type]=function(id,definition){if(!definition){return this.options[type+'s'][id];}else{/* istanbul ignore if */if(process.env.NODE_ENV!=='production'&&type==='component'){validateComponentName(id);}if(type==='component'&&isPlainObject(definition)){definition.name=definition.name||id;definition=this.options._base.extend(definition);}if(type==='directive'&&typeof definition==='function'){definition={bind:definition,update:definition};}this.options[type+'s'][id]=definition;return definition;}};});}/*  */function getComponentName(opts){return opts&&(opts.Ctor.options.name||opts.tag);}function matches(pattern,name){if(Array.isArray(pattern)){return pattern.indexOf(name)>-1;}else if(typeof pattern==='string'){return pattern.split(',').indexOf(name)>-1;}else if(isRegExp(pattern)){return pattern.test(name);}/* istanbul ignore next */return false;}function pruneCache(keepAliveInstance,filter){var cache=keepAliveInstance.cache;var keys=keepAliveInstance.keys;var _vnode=keepAliveInstance._vnode;for(var key in cache){var cachedNode=cache[key];if(cachedNode){var name=getComponentName(cachedNode.componentOptions);if(name&&!filter(name)){pruneCacheEntry(cache,key,keys,_vnode);}}}}function pruneCacheEntry(cache,key,keys,current){var cached$$1=cache[key];if(cached$$1&&(!current||cached$$1.tag!==current.tag)){cached$$1.componentInstance.$destroy();}cache[key]=null;remove(keys,key);}var patternTypes=[String,RegExp,Array];var KeepAlive={name:'keep-alive',abstract:true,props:{include:patternTypes,exclude:patternTypes,max:[String,Number]},created:function created(){this.cache=Object.create(null);this.keys=[];},destroyed:function destroyed(){for(var key in this.cache){pruneCacheEntry(this.cache,key,this.keys);}},mounted:function mounted(){var this$1=this;this.$watch('include',function(val){pruneCache(this$1,function(name){return matches(val,name);});});this.$watch('exclude',function(val){pruneCache(this$1,function(name){return!matches(val,name);});});},render:function render(){var slot=this.$slots.default;var vnode=getFirstComponentChild(slot);var componentOptions=vnode&&vnode.componentOptions;if(componentOptions){// check pattern
var name=getComponentName(componentOptions);var ref=this;var include=ref.include;var exclude=ref.exclude;if(// not included
include&&(!name||!matches(include,name))||// excluded
exclude&&name&&matches(exclude,name)){return vnode;}var ref$1=this;var cache=ref$1.cache;var keys=ref$1.keys;var key=vnode.key==null// same constructor may get registered as different local components
// so cid alone is not enough (#3269)
?componentOptions.Ctor.cid+(componentOptions.tag?"::"+componentOptions.tag:''):vnode.key;if(cache[key]){vnode.componentInstance=cache[key].componentInstance;// make current key freshest
remove(keys,key);keys.push(key);}else{cache[key]=vnode;keys.push(key);// prune oldest entry
if(this.max&&keys.length>parseInt(this.max)){pruneCacheEntry(cache,keys[0],keys,this._vnode);}}vnode.data.keepAlive=true;}return vnode||slot&&slot[0];}};var builtInComponents={KeepAlive:KeepAlive};/*  */function initGlobalAPI(Vue){// config
var configDef={};configDef.get=function(){return config;};if(process.env.NODE_ENV!=='production'){configDef.set=function(){warn('Do not replace the Vue.config object, set individual fields instead.');};}Object.defineProperty(Vue,'config',configDef);// exposed util methods.
// NOTE: these are not considered part of the public API - avoid relying on
// them unless you are aware of the risk.
Vue.util={warn:warn,extend:extend,mergeOptions:mergeOptions,defineReactive:defineReactive$$1};Vue.set=set;Vue.delete=del;Vue.nextTick=nextTick;Vue.options=Object.create(null);ASSET_TYPES.forEach(function(type){Vue.options[type+'s']=Object.create(null);});// this is used to identify the "base" constructor to extend all plain-object
// components with in Weex's multi-instance scenarios.
Vue.options._base=Vue;extend(Vue.options.components,builtInComponents);initUse(Vue);initMixin$1(Vue);initExtend(Vue);initAssetRegisters(Vue);}initGlobalAPI(Vue);Object.defineProperty(Vue.prototype,'$isServer',{get:isServerRendering});Object.defineProperty(Vue.prototype,'$ssrContext',{get:function get(){/* istanbul ignore next */return this.$vnode&&this.$vnode.ssrContext;}});// expose FunctionalRenderContext for ssr runtime helper installation
Object.defineProperty(Vue,'FunctionalRenderContext',{value:FunctionalRenderContext});Vue.version='2.5.22';/*  */// these are reserved for web because they are directly compiled away
// during template compilation
var isReservedAttr=makeMap('style,class');// attributes that should be using props for binding
var acceptValue=makeMap('input,textarea,option,select,progress');var mustUseProp=function mustUseProp(tag,type,attr){return attr==='value'&&acceptValue(tag)&&type!=='button'||attr==='selected'&&tag==='option'||attr==='checked'&&tag==='input'||attr==='muted'&&tag==='video';};var isEnumeratedAttr=makeMap('contenteditable,draggable,spellcheck');var isBooleanAttr=makeMap('allowfullscreen,async,autofocus,autoplay,checked,compact,controls,declare,'+'default,defaultchecked,defaultmuted,defaultselected,defer,disabled,'+'enabled,formnovalidate,hidden,indeterminate,inert,ismap,itemscope,loop,multiple,'+'muted,nohref,noresize,noshade,novalidate,nowrap,open,pauseonexit,readonly,'+'required,reversed,scoped,seamless,selected,sortable,translate,'+'truespeed,typemustmatch,visible');var xlinkNS='http://www.w3.org/1999/xlink';var isXlink=function isXlink(name){return name.charAt(5)===':'&&name.slice(0,5)==='xlink';};var getXlinkProp=function getXlinkProp(name){return isXlink(name)?name.slice(6,name.length):'';};var isFalsyAttrValue=function isFalsyAttrValue(val){return val==null||val===false;};/*  */function genClassForVnode(vnode){var data=vnode.data;var parentNode=vnode;var childNode=vnode;while(isDef(childNode.componentInstance)){childNode=childNode.componentInstance._vnode;if(childNode&&childNode.data){data=mergeClassData(childNode.data,data);}}while(isDef(parentNode=parentNode.parent)){if(parentNode&&parentNode.data){data=mergeClassData(data,parentNode.data);}}return renderClass(data.staticClass,data.class);}function mergeClassData(child,parent){return{staticClass:concat(child.staticClass,parent.staticClass),class:isDef(child.class)?[child.class,parent.class]:parent.class};}function renderClass(staticClass,dynamicClass){if(isDef(staticClass)||isDef(dynamicClass)){return concat(staticClass,stringifyClass(dynamicClass));}/* istanbul ignore next */return'';}function concat(a,b){return a?b?a+' '+b:a:b||'';}function stringifyClass(value){if(Array.isArray(value)){return stringifyArray(value);}if(isObject(value)){return stringifyObject(value);}if(typeof value==='string'){return value;}/* istanbul ignore next */return'';}function stringifyArray(value){var res='';var stringified;for(var i=0,l=value.length;i<l;i++){if(isDef(stringified=stringifyClass(value[i]))&&stringified!==''){if(res){res+=' ';}res+=stringified;}}return res;}function stringifyObject(value){var res='';for(var key in value){if(value[key]){if(res){res+=' ';}res+=key;}}return res;}/*  */var namespaceMap={svg:'http://www.w3.org/2000/svg',math:'http://www.w3.org/1998/Math/MathML'};var isHTMLTag=makeMap('html,body,base,head,link,meta,style,title,'+'address,article,aside,footer,header,h1,h2,h3,h4,h5,h6,hgroup,nav,section,'+'div,dd,dl,dt,figcaption,figure,picture,hr,img,li,main,ol,p,pre,ul,'+'a,b,abbr,bdi,bdo,br,cite,code,data,dfn,em,i,kbd,mark,q,rp,rt,rtc,ruby,'+'s,samp,small,span,strong,sub,sup,time,u,var,wbr,area,audio,map,track,video,'+'embed,object,param,source,canvas,script,noscript,del,ins,'+'caption,col,colgroup,table,thead,tbody,td,th,tr,'+'button,datalist,fieldset,form,input,label,legend,meter,optgroup,option,'+'output,progress,select,textarea,'+'details,dialog,menu,menuitem,summary,'+'content,element,shadow,template,blockquote,iframe,tfoot');// this map is intentionally selective, only covering SVG elements that may
// contain child elements.
var isSVG=makeMap('svg,animate,circle,clippath,cursor,defs,desc,ellipse,filter,font-face,'+'foreignObject,g,glyph,image,line,marker,mask,missing-glyph,path,pattern,'+'polygon,polyline,rect,switch,symbol,text,textpath,tspan,use,view',true);var isPreTag=function isPreTag(tag){return tag==='pre';};var isReservedTag=function isReservedTag(tag){return isHTMLTag(tag)||isSVG(tag);};function getTagNamespace(tag){if(isSVG(tag)){return'svg';}// basic support for MathML
// note it doesn't support other MathML elements being component roots
if(tag==='math'){return'math';}}var unknownElementCache=Object.create(null);function isUnknownElement(tag){/* istanbul ignore if */if(!inBrowser){return true;}if(isReservedTag(tag)){return false;}tag=tag.toLowerCase();/* istanbul ignore if */if(unknownElementCache[tag]!=null){return unknownElementCache[tag];}var el=document.createElement(tag);if(tag.indexOf('-')>-1){// http://stackoverflow.com/a/28210364/1070244
return unknownElementCache[tag]=el.constructor===window.HTMLUnknownElement||el.constructor===window.HTMLElement;}else{return unknownElementCache[tag]=/HTMLUnknownElement/.test(el.toString());}}var isTextInputType=makeMap('text,number,password,search,email,tel,url');/*  *//**
 * Query an element selector if it's not an element already.
 */function query(el){if(typeof el==='string'){var selected=document.querySelector(el);if(!selected){process.env.NODE_ENV!=='production'&&warn('Cannot find element: '+el);return document.createElement('div');}return selected;}else{return el;}}/*  */function createElement$1(tagName,vnode){var elm=document.createElement(tagName);if(tagName!=='select'){return elm;}// false or null will remove the attribute but undefined will not
if(vnode.data&&vnode.data.attrs&&vnode.data.attrs.multiple!==undefined){elm.setAttribute('multiple','multiple');}return elm;}function createElementNS(namespace,tagName){return document.createElementNS(namespaceMap[namespace],tagName);}function createTextNode(text){return document.createTextNode(text);}function createComment(text){return document.createComment(text);}function insertBefore(parentNode,newNode,referenceNode){parentNode.insertBefore(newNode,referenceNode);}function removeChild(node,child){node.removeChild(child);}function appendChild(node,child){node.appendChild(child);}function parentNode(node){return node.parentNode;}function nextSibling(node){return node.nextSibling;}function tagName(node){return node.tagName;}function setTextContent(node,text){node.textContent=text;}function setStyleScope(node,scopeId){node.setAttribute(scopeId,'');}var nodeOps=/*#__PURE__*/Object.freeze({createElement:createElement$1,createElementNS:createElementNS,createTextNode:createTextNode,createComment:createComment,insertBefore:insertBefore,removeChild:removeChild,appendChild:appendChild,parentNode:parentNode,nextSibling:nextSibling,tagName:tagName,setTextContent:setTextContent,setStyleScope:setStyleScope});/*  */var ref={create:function create(_,vnode){registerRef(vnode);},update:function update(oldVnode,vnode){if(oldVnode.data.ref!==vnode.data.ref){registerRef(oldVnode,true);registerRef(vnode);}},destroy:function destroy(vnode){registerRef(vnode,true);}};function registerRef(vnode,isRemoval){var key=vnode.data.ref;if(!isDef(key)){return;}var vm=vnode.context;var ref=vnode.componentInstance||vnode.elm;var refs=vm.$refs;if(isRemoval){if(Array.isArray(refs[key])){remove(refs[key],ref);}else if(refs[key]===ref){refs[key]=undefined;}}else{if(vnode.data.refInFor){if(!Array.isArray(refs[key])){refs[key]=[ref];}else if(refs[key].indexOf(ref)<0){// $flow-disable-line
refs[key].push(ref);}}else{refs[key]=ref;}}}/**
 * Virtual DOM patching algorithm based on Snabbdom by
 * Simon Friis Vindum (@paldepind)
 * Licensed under the MIT License
 * https://github.com/paldepind/snabbdom/blob/master/LICENSE
 *
 * modified by Evan You (@yyx990803)
 *
 * Not type-checking this because this file is perf-critical and the cost
 * of making flow understand it is not worth it.
 */var emptyNode=new VNode('',{},[]);var hooks=['create','activate','update','remove','destroy'];function sameVnode(a,b){return a.key===b.key&&(a.tag===b.tag&&a.isComment===b.isComment&&isDef(a.data)===isDef(b.data)&&sameInputType(a,b)||isTrue(a.isAsyncPlaceholder)&&a.asyncFactory===b.asyncFactory&&isUndef(b.asyncFactory.error));}function sameInputType(a,b){if(a.tag!=='input'){return true;}var i;var typeA=isDef(i=a.data)&&isDef(i=i.attrs)&&i.type;var typeB=isDef(i=b.data)&&isDef(i=i.attrs)&&i.type;return typeA===typeB||isTextInputType(typeA)&&isTextInputType(typeB);}function createKeyToOldIdx(children,beginIdx,endIdx){var i,key;var map={};for(i=beginIdx;i<=endIdx;++i){key=children[i].key;if(isDef(key)){map[key]=i;}}return map;}function createPatchFunction(backend){var i,j;var cbs={};var modules=backend.modules;var nodeOps=backend.nodeOps;for(i=0;i<hooks.length;++i){cbs[hooks[i]]=[];for(j=0;j<modules.length;++j){if(isDef(modules[j][hooks[i]])){cbs[hooks[i]].push(modules[j][hooks[i]]);}}}function emptyNodeAt(elm){return new VNode(nodeOps.tagName(elm).toLowerCase(),{},[],undefined,elm);}function createRmCb(childElm,listeners){function remove$$1(){if(--remove$$1.listeners===0){removeNode(childElm);}}remove$$1.listeners=listeners;return remove$$1;}function removeNode(el){var parent=nodeOps.parentNode(el);// element may have already been removed due to v-html / v-text
if(isDef(parent)){nodeOps.removeChild(parent,el);}}function isUnknownElement$$1(vnode,inVPre){return!inVPre&&!vnode.ns&&!(config.ignoredElements.length&&config.ignoredElements.some(function(ignore){return isRegExp(ignore)?ignore.test(vnode.tag):ignore===vnode.tag;}))&&config.isUnknownElement(vnode.tag);}var creatingElmInVPre=0;function createElm(vnode,insertedVnodeQueue,parentElm,refElm,nested,ownerArray,index){if(isDef(vnode.elm)&&isDef(ownerArray)){// This vnode was used in a previous render!
// now it's used as a new node, overwriting its elm would cause
// potential patch errors down the road when it's used as an insertion
// reference node. Instead, we clone the node on-demand before creating
// associated DOM element for it.
vnode=ownerArray[index]=cloneVNode(vnode);}vnode.isRootInsert=!nested;// for transition enter check
if(createComponent(vnode,insertedVnodeQueue,parentElm,refElm)){return;}var data=vnode.data;var children=vnode.children;var tag=vnode.tag;if(isDef(tag)){if(process.env.NODE_ENV!=='production'){if(data&&data.pre){creatingElmInVPre++;}if(isUnknownElement$$1(vnode,creatingElmInVPre)){warn('Unknown custom element: <'+tag+'> - did you '+'register the component correctly? For recursive components, '+'make sure to provide the "name" option.',vnode.context);}}vnode.elm=vnode.ns?nodeOps.createElementNS(vnode.ns,tag):nodeOps.createElement(tag,vnode);setScope(vnode);/* istanbul ignore if */{createChildren(vnode,children,insertedVnodeQueue);if(isDef(data)){invokeCreateHooks(vnode,insertedVnodeQueue);}insert(parentElm,vnode.elm,refElm);}if(process.env.NODE_ENV!=='production'&&data&&data.pre){creatingElmInVPre--;}}else if(isTrue(vnode.isComment)){vnode.elm=nodeOps.createComment(vnode.text);insert(parentElm,vnode.elm,refElm);}else{vnode.elm=nodeOps.createTextNode(vnode.text);insert(parentElm,vnode.elm,refElm);}}function createComponent(vnode,insertedVnodeQueue,parentElm,refElm){var i=vnode.data;if(isDef(i)){var isReactivated=isDef(vnode.componentInstance)&&i.keepAlive;if(isDef(i=i.hook)&&isDef(i=i.init)){i(vnode,false/* hydrating */);}// after calling the init hook, if the vnode is a child component
// it should've created a child instance and mounted it. the child
// component also has set the placeholder vnode's elm.
// in that case we can just return the element and be done.
if(isDef(vnode.componentInstance)){initComponent(vnode,insertedVnodeQueue);insert(parentElm,vnode.elm,refElm);if(isTrue(isReactivated)){reactivateComponent(vnode,insertedVnodeQueue,parentElm,refElm);}return true;}}}function initComponent(vnode,insertedVnodeQueue){if(isDef(vnode.data.pendingInsert)){insertedVnodeQueue.push.apply(insertedVnodeQueue,vnode.data.pendingInsert);vnode.data.pendingInsert=null;}vnode.elm=vnode.componentInstance.$el;if(isPatchable(vnode)){invokeCreateHooks(vnode,insertedVnodeQueue);setScope(vnode);}else{// empty component root.
// skip all element-related modules except for ref (#3455)
registerRef(vnode);// make sure to invoke the insert hook
insertedVnodeQueue.push(vnode);}}function reactivateComponent(vnode,insertedVnodeQueue,parentElm,refElm){var i;// hack for #4339: a reactivated component with inner transition
// does not trigger because the inner node's created hooks are not called
// again. It's not ideal to involve module-specific logic in here but
// there doesn't seem to be a better way to do it.
var innerNode=vnode;while(innerNode.componentInstance){innerNode=innerNode.componentInstance._vnode;if(isDef(i=innerNode.data)&&isDef(i=i.transition)){for(i=0;i<cbs.activate.length;++i){cbs.activate[i](emptyNode,innerNode);}insertedVnodeQueue.push(innerNode);break;}}// unlike a newly created component,
// a reactivated keep-alive component doesn't insert itself
insert(parentElm,vnode.elm,refElm);}function insert(parent,elm,ref$$1){if(isDef(parent)){if(isDef(ref$$1)){if(nodeOps.parentNode(ref$$1)===parent){nodeOps.insertBefore(parent,elm,ref$$1);}}else{nodeOps.appendChild(parent,elm);}}}function createChildren(vnode,children,insertedVnodeQueue){if(Array.isArray(children)){if(process.env.NODE_ENV!=='production'){checkDuplicateKeys(children);}for(var i=0;i<children.length;++i){createElm(children[i],insertedVnodeQueue,vnode.elm,null,true,children,i);}}else if(isPrimitive(vnode.text)){nodeOps.appendChild(vnode.elm,nodeOps.createTextNode(String(vnode.text)));}}function isPatchable(vnode){while(vnode.componentInstance){vnode=vnode.componentInstance._vnode;}return isDef(vnode.tag);}function invokeCreateHooks(vnode,insertedVnodeQueue){for(var i$1=0;i$1<cbs.create.length;++i$1){cbs.create[i$1](emptyNode,vnode);}i=vnode.data.hook;// Reuse variable
if(isDef(i)){if(isDef(i.create)){i.create(emptyNode,vnode);}if(isDef(i.insert)){insertedVnodeQueue.push(vnode);}}}// set scope id attribute for scoped CSS.
// this is implemented as a special case to avoid the overhead
// of going through the normal attribute patching process.
function setScope(vnode){var i;if(isDef(i=vnode.fnScopeId)){nodeOps.setStyleScope(vnode.elm,i);}else{var ancestor=vnode;while(ancestor){if(isDef(i=ancestor.context)&&isDef(i=i.$options._scopeId)){nodeOps.setStyleScope(vnode.elm,i);}ancestor=ancestor.parent;}}// for slot content they should also get the scopeId from the host instance.
if(isDef(i=activeInstance)&&i!==vnode.context&&i!==vnode.fnContext&&isDef(i=i.$options._scopeId)){nodeOps.setStyleScope(vnode.elm,i);}}function addVnodes(parentElm,refElm,vnodes,startIdx,endIdx,insertedVnodeQueue){for(;startIdx<=endIdx;++startIdx){createElm(vnodes[startIdx],insertedVnodeQueue,parentElm,refElm,false,vnodes,startIdx);}}function invokeDestroyHook(vnode){var i,j;var data=vnode.data;if(isDef(data)){if(isDef(i=data.hook)&&isDef(i=i.destroy)){i(vnode);}for(i=0;i<cbs.destroy.length;++i){cbs.destroy[i](vnode);}}if(isDef(i=vnode.children)){for(j=0;j<vnode.children.length;++j){invokeDestroyHook(vnode.children[j]);}}}function removeVnodes(parentElm,vnodes,startIdx,endIdx){for(;startIdx<=endIdx;++startIdx){var ch=vnodes[startIdx];if(isDef(ch)){if(isDef(ch.tag)){removeAndInvokeRemoveHook(ch);invokeDestroyHook(ch);}else{// Text node
removeNode(ch.elm);}}}}function removeAndInvokeRemoveHook(vnode,rm){if(isDef(rm)||isDef(vnode.data)){var i;var listeners=cbs.remove.length+1;if(isDef(rm)){// we have a recursively passed down rm callback
// increase the listeners count
rm.listeners+=listeners;}else{// directly removing
rm=createRmCb(vnode.elm,listeners);}// recursively invoke hooks on child component root node
if(isDef(i=vnode.componentInstance)&&isDef(i=i._vnode)&&isDef(i.data)){removeAndInvokeRemoveHook(i,rm);}for(i=0;i<cbs.remove.length;++i){cbs.remove[i](vnode,rm);}if(isDef(i=vnode.data.hook)&&isDef(i=i.remove)){i(vnode,rm);}else{rm();}}else{removeNode(vnode.elm);}}function updateChildren(parentElm,oldCh,newCh,insertedVnodeQueue,removeOnly){var oldStartIdx=0;var newStartIdx=0;var oldEndIdx=oldCh.length-1;var oldStartVnode=oldCh[0];var oldEndVnode=oldCh[oldEndIdx];var newEndIdx=newCh.length-1;var newStartVnode=newCh[0];var newEndVnode=newCh[newEndIdx];var oldKeyToIdx,idxInOld,vnodeToMove,refElm;// removeOnly is a special flag used only by <transition-group>
// to ensure removed elements stay in correct relative positions
// during leaving transitions
var canMove=!removeOnly;if(process.env.NODE_ENV!=='production'){checkDuplicateKeys(newCh);}while(oldStartIdx<=oldEndIdx&&newStartIdx<=newEndIdx){if(isUndef(oldStartVnode)){oldStartVnode=oldCh[++oldStartIdx];// Vnode has been moved left
}else if(isUndef(oldEndVnode)){oldEndVnode=oldCh[--oldEndIdx];}else if(sameVnode(oldStartVnode,newStartVnode)){patchVnode(oldStartVnode,newStartVnode,insertedVnodeQueue,newCh,newStartIdx);oldStartVnode=oldCh[++oldStartIdx];newStartVnode=newCh[++newStartIdx];}else if(sameVnode(oldEndVnode,newEndVnode)){patchVnode(oldEndVnode,newEndVnode,insertedVnodeQueue,newCh,newEndIdx);oldEndVnode=oldCh[--oldEndIdx];newEndVnode=newCh[--newEndIdx];}else if(sameVnode(oldStartVnode,newEndVnode)){// Vnode moved right
patchVnode(oldStartVnode,newEndVnode,insertedVnodeQueue,newCh,newEndIdx);canMove&&nodeOps.insertBefore(parentElm,oldStartVnode.elm,nodeOps.nextSibling(oldEndVnode.elm));oldStartVnode=oldCh[++oldStartIdx];newEndVnode=newCh[--newEndIdx];}else if(sameVnode(oldEndVnode,newStartVnode)){// Vnode moved left
patchVnode(oldEndVnode,newStartVnode,insertedVnodeQueue,newCh,newStartIdx);canMove&&nodeOps.insertBefore(parentElm,oldEndVnode.elm,oldStartVnode.elm);oldEndVnode=oldCh[--oldEndIdx];newStartVnode=newCh[++newStartIdx];}else{if(isUndef(oldKeyToIdx)){oldKeyToIdx=createKeyToOldIdx(oldCh,oldStartIdx,oldEndIdx);}idxInOld=isDef(newStartVnode.key)?oldKeyToIdx[newStartVnode.key]:findIdxInOld(newStartVnode,oldCh,oldStartIdx,oldEndIdx);if(isUndef(idxInOld)){// New element
createElm(newStartVnode,insertedVnodeQueue,parentElm,oldStartVnode.elm,false,newCh,newStartIdx);}else{vnodeToMove=oldCh[idxInOld];if(sameVnode(vnodeToMove,newStartVnode)){patchVnode(vnodeToMove,newStartVnode,insertedVnodeQueue,newCh,newStartIdx);oldCh[idxInOld]=undefined;canMove&&nodeOps.insertBefore(parentElm,vnodeToMove.elm,oldStartVnode.elm);}else{// same key but different element. treat as new element
createElm(newStartVnode,insertedVnodeQueue,parentElm,oldStartVnode.elm,false,newCh,newStartIdx);}}newStartVnode=newCh[++newStartIdx];}}if(oldStartIdx>oldEndIdx){refElm=isUndef(newCh[newEndIdx+1])?null:newCh[newEndIdx+1].elm;addVnodes(parentElm,refElm,newCh,newStartIdx,newEndIdx,insertedVnodeQueue);}else if(newStartIdx>newEndIdx){removeVnodes(parentElm,oldCh,oldStartIdx,oldEndIdx);}}function checkDuplicateKeys(children){var seenKeys={};for(var i=0;i<children.length;i++){var vnode=children[i];var key=vnode.key;if(isDef(key)){if(seenKeys[key]){warn("Duplicate keys detected: '"+key+"'. This may cause an update error.",vnode.context);}else{seenKeys[key]=true;}}}}function findIdxInOld(node,oldCh,start,end){for(var i=start;i<end;i++){var c=oldCh[i];if(isDef(c)&&sameVnode(node,c)){return i;}}}function patchVnode(oldVnode,vnode,insertedVnodeQueue,ownerArray,index,removeOnly){if(oldVnode===vnode){return;}if(isDef(vnode.elm)&&isDef(ownerArray)){// clone reused vnode
vnode=ownerArray[index]=cloneVNode(vnode);}var elm=vnode.elm=oldVnode.elm;if(isTrue(oldVnode.isAsyncPlaceholder)){if(isDef(vnode.asyncFactory.resolved)){hydrate(oldVnode.elm,vnode,insertedVnodeQueue);}else{vnode.isAsyncPlaceholder=true;}return;}// reuse element for static trees.
// note we only do this if the vnode is cloned -
// if the new node is not cloned it means the render functions have been
// reset by the hot-reload-api and we need to do a proper re-render.
if(isTrue(vnode.isStatic)&&isTrue(oldVnode.isStatic)&&vnode.key===oldVnode.key&&(isTrue(vnode.isCloned)||isTrue(vnode.isOnce))){vnode.componentInstance=oldVnode.componentInstance;return;}var i;var data=vnode.data;if(isDef(data)&&isDef(i=data.hook)&&isDef(i=i.prepatch)){i(oldVnode,vnode);}var oldCh=oldVnode.children;var ch=vnode.children;if(isDef(data)&&isPatchable(vnode)){for(i=0;i<cbs.update.length;++i){cbs.update[i](oldVnode,vnode);}if(isDef(i=data.hook)&&isDef(i=i.update)){i(oldVnode,vnode);}}if(isUndef(vnode.text)){if(isDef(oldCh)&&isDef(ch)){if(oldCh!==ch){updateChildren(elm,oldCh,ch,insertedVnodeQueue,removeOnly);}}else if(isDef(ch)){if(process.env.NODE_ENV!=='production'){checkDuplicateKeys(ch);}if(isDef(oldVnode.text)){nodeOps.setTextContent(elm,'');}addVnodes(elm,null,ch,0,ch.length-1,insertedVnodeQueue);}else if(isDef(oldCh)){removeVnodes(elm,oldCh,0,oldCh.length-1);}else if(isDef(oldVnode.text)){nodeOps.setTextContent(elm,'');}}else if(oldVnode.text!==vnode.text){nodeOps.setTextContent(elm,vnode.text);}if(isDef(data)){if(isDef(i=data.hook)&&isDef(i=i.postpatch)){i(oldVnode,vnode);}}}function invokeInsertHook(vnode,queue,initial){// delay insert hooks for component root nodes, invoke them after the
// element is really inserted
if(isTrue(initial)&&isDef(vnode.parent)){vnode.parent.data.pendingInsert=queue;}else{for(var i=0;i<queue.length;++i){queue[i].data.hook.insert(queue[i]);}}}var hydrationBailed=false;// list of modules that can skip create hook during hydration because they
// are already rendered on the client or has no need for initialization
// Note: style is excluded because it relies on initial clone for future
// deep updates (#7063).
var isRenderedModule=makeMap('attrs,class,staticClass,staticStyle,key');// Note: this is a browser-only function so we can assume elms are DOM nodes.
function hydrate(elm,vnode,insertedVnodeQueue,inVPre){var i;var tag=vnode.tag;var data=vnode.data;var children=vnode.children;inVPre=inVPre||data&&data.pre;vnode.elm=elm;if(isTrue(vnode.isComment)&&isDef(vnode.asyncFactory)){vnode.isAsyncPlaceholder=true;return true;}// assert node match
if(process.env.NODE_ENV!=='production'){if(!assertNodeMatch(elm,vnode,inVPre)){return false;}}if(isDef(data)){if(isDef(i=data.hook)&&isDef(i=i.init)){i(vnode,true/* hydrating */);}if(isDef(i=vnode.componentInstance)){// child component. it should have hydrated its own tree.
initComponent(vnode,insertedVnodeQueue);return true;}}if(isDef(tag)){if(isDef(children)){// empty element, allow client to pick up and populate children
if(!elm.hasChildNodes()){createChildren(vnode,children,insertedVnodeQueue);}else{// v-html and domProps: innerHTML
if(isDef(i=data)&&isDef(i=i.domProps)&&isDef(i=i.innerHTML)){if(i!==elm.innerHTML){/* istanbul ignore if */if(process.env.NODE_ENV!=='production'&&typeof console!=='undefined'&&!hydrationBailed){hydrationBailed=true;console.warn('Parent: ',elm);console.warn('server innerHTML: ',i);console.warn('client innerHTML: ',elm.innerHTML);}return false;}}else{// iterate and compare children lists
var childrenMatch=true;var childNode=elm.firstChild;for(var i$1=0;i$1<children.length;i$1++){if(!childNode||!hydrate(childNode,children[i$1],insertedVnodeQueue,inVPre)){childrenMatch=false;break;}childNode=childNode.nextSibling;}// if childNode is not null, it means the actual childNodes list is
// longer than the virtual children list.
if(!childrenMatch||childNode){/* istanbul ignore if */if(process.env.NODE_ENV!=='production'&&typeof console!=='undefined'&&!hydrationBailed){hydrationBailed=true;console.warn('Parent: ',elm);console.warn('Mismatching childNodes vs. VNodes: ',elm.childNodes,children);}return false;}}}}if(isDef(data)){var fullInvoke=false;for(var key in data){if(!isRenderedModule(key)){fullInvoke=true;invokeCreateHooks(vnode,insertedVnodeQueue);break;}}if(!fullInvoke&&data['class']){// ensure collecting deps for deep class bindings for future updates
traverse(data['class']);}}}else if(elm.data!==vnode.text){elm.data=vnode.text;}return true;}function assertNodeMatch(node,vnode,inVPre){if(isDef(vnode.tag)){return vnode.tag.indexOf('vue-component')===0||!isUnknownElement$$1(vnode,inVPre)&&vnode.tag.toLowerCase()===(node.tagName&&node.tagName.toLowerCase());}else{return node.nodeType===(vnode.isComment?8:3);}}return function patch(oldVnode,vnode,hydrating,removeOnly){if(isUndef(vnode)){if(isDef(oldVnode)){invokeDestroyHook(oldVnode);}return;}var isInitialPatch=false;var insertedVnodeQueue=[];if(isUndef(oldVnode)){// empty mount (likely as component), create new root element
isInitialPatch=true;createElm(vnode,insertedVnodeQueue);}else{var isRealElement=isDef(oldVnode.nodeType);if(!isRealElement&&sameVnode(oldVnode,vnode)){// patch existing root node
patchVnode(oldVnode,vnode,insertedVnodeQueue,null,null,removeOnly);}else{if(isRealElement){// mounting to a real element
// check if this is server-rendered content and if we can perform
// a successful hydration.
if(oldVnode.nodeType===1&&oldVnode.hasAttribute(SSR_ATTR)){oldVnode.removeAttribute(SSR_ATTR);hydrating=true;}if(isTrue(hydrating)){if(hydrate(oldVnode,vnode,insertedVnodeQueue)){invokeInsertHook(vnode,insertedVnodeQueue,true);return oldVnode;}else if(process.env.NODE_ENV!=='production'){warn('The client-side rendered virtual DOM tree is not matching '+'server-rendered content. This is likely caused by incorrect '+'HTML markup, for example nesting block-level elements inside '+'<p>, or missing <tbody>. Bailing hydration and performing '+'full client-side render.');}}// either not server-rendered, or hydration failed.
// create an empty node and replace it
oldVnode=emptyNodeAt(oldVnode);}// replacing existing element
var oldElm=oldVnode.elm;var parentElm=nodeOps.parentNode(oldElm);// create new node
createElm(vnode,insertedVnodeQueue,// extremely rare edge case: do not insert if old element is in a
// leaving transition. Only happens when combining transition +
// keep-alive + HOCs. (#4590)
oldElm._leaveCb?null:parentElm,nodeOps.nextSibling(oldElm));// update parent placeholder node element, recursively
if(isDef(vnode.parent)){var ancestor=vnode.parent;var patchable=isPatchable(vnode);while(ancestor){for(var i=0;i<cbs.destroy.length;++i){cbs.destroy[i](ancestor);}ancestor.elm=vnode.elm;if(patchable){for(var i$1=0;i$1<cbs.create.length;++i$1){cbs.create[i$1](emptyNode,ancestor);}// #6513
// invoke insert hooks that may have been merged by create hooks.
// e.g. for directives that uses the "inserted" hook.
var insert=ancestor.data.hook.insert;if(insert.merged){// start at index 1 to avoid re-invoking component mounted hook
for(var i$2=1;i$2<insert.fns.length;i$2++){insert.fns[i$2]();}}}else{registerRef(ancestor);}ancestor=ancestor.parent;}}// destroy old node
if(isDef(parentElm)){removeVnodes(parentElm,[oldVnode],0,0);}else if(isDef(oldVnode.tag)){invokeDestroyHook(oldVnode);}}}invokeInsertHook(vnode,insertedVnodeQueue,isInitialPatch);return vnode.elm;};}/*  */var directives={create:updateDirectives,update:updateDirectives,destroy:function unbindDirectives(vnode){updateDirectives(vnode,emptyNode);}};function updateDirectives(oldVnode,vnode){if(oldVnode.data.directives||vnode.data.directives){_update(oldVnode,vnode);}}function _update(oldVnode,vnode){var isCreate=oldVnode===emptyNode;var isDestroy=vnode===emptyNode;var oldDirs=normalizeDirectives$1(oldVnode.data.directives,oldVnode.context);var newDirs=normalizeDirectives$1(vnode.data.directives,vnode.context);var dirsWithInsert=[];var dirsWithPostpatch=[];var key,oldDir,dir;for(key in newDirs){oldDir=oldDirs[key];dir=newDirs[key];if(!oldDir){// new directive, bind
callHook$1(dir,'bind',vnode,oldVnode);if(dir.def&&dir.def.inserted){dirsWithInsert.push(dir);}}else{// existing directive, update
dir.oldValue=oldDir.value;callHook$1(dir,'update',vnode,oldVnode);if(dir.def&&dir.def.componentUpdated){dirsWithPostpatch.push(dir);}}}if(dirsWithInsert.length){var callInsert=function callInsert(){for(var i=0;i<dirsWithInsert.length;i++){callHook$1(dirsWithInsert[i],'inserted',vnode,oldVnode);}};if(isCreate){mergeVNodeHook(vnode,'insert',callInsert);}else{callInsert();}}if(dirsWithPostpatch.length){mergeVNodeHook(vnode,'postpatch',function(){for(var i=0;i<dirsWithPostpatch.length;i++){callHook$1(dirsWithPostpatch[i],'componentUpdated',vnode,oldVnode);}});}if(!isCreate){for(key in oldDirs){if(!newDirs[key]){// no longer present, unbind
callHook$1(oldDirs[key],'unbind',oldVnode,oldVnode,isDestroy);}}}}var emptyModifiers=Object.create(null);function normalizeDirectives$1(dirs,vm){var res=Object.create(null);if(!dirs){// $flow-disable-line
return res;}var i,dir;for(i=0;i<dirs.length;i++){dir=dirs[i];if(!dir.modifiers){// $flow-disable-line
dir.modifiers=emptyModifiers;}res[getRawDirName(dir)]=dir;dir.def=resolveAsset(vm.$options,'directives',dir.name,true);}// $flow-disable-line
return res;}function getRawDirName(dir){return dir.rawName||dir.name+"."+Object.keys(dir.modifiers||{}).join('.');}function callHook$1(dir,hook,vnode,oldVnode,isDestroy){var fn=dir.def&&dir.def[hook];if(fn){try{fn(vnode.elm,dir,vnode,oldVnode,isDestroy);}catch(e){handleError(e,vnode.context,"directive "+dir.name+" "+hook+" hook");}}}var baseModules=[ref,directives];/*  */function updateAttrs(oldVnode,vnode){var opts=vnode.componentOptions;if(isDef(opts)&&opts.Ctor.options.inheritAttrs===false){return;}if(isUndef(oldVnode.data.attrs)&&isUndef(vnode.data.attrs)){return;}var key,cur,old;var elm=vnode.elm;var oldAttrs=oldVnode.data.attrs||{};var attrs=vnode.data.attrs||{};// clone observed objects, as the user probably wants to mutate it
if(isDef(attrs.__ob__)){attrs=vnode.data.attrs=extend({},attrs);}for(key in attrs){cur=attrs[key];old=oldAttrs[key];if(old!==cur){setAttr(elm,key,cur);}}// #4391: in IE9, setting type can reset value for input[type=radio]
// #6666: IE/Edge forces progress value down to 1 before setting a max
/* istanbul ignore if */if((isIE||isEdge)&&attrs.value!==oldAttrs.value){setAttr(elm,'value',attrs.value);}for(key in oldAttrs){if(isUndef(attrs[key])){if(isXlink(key)){elm.removeAttributeNS(xlinkNS,getXlinkProp(key));}else if(!isEnumeratedAttr(key)){elm.removeAttribute(key);}}}}function setAttr(el,key,value){if(el.tagName.indexOf('-')>-1){baseSetAttr(el,key,value);}else if(isBooleanAttr(key)){// set attribute for blank value
// e.g. <option disabled>Select one</option>
if(isFalsyAttrValue(value)){el.removeAttribute(key);}else{// technically allowfullscreen is a boolean attribute for <iframe>,
// but Flash expects a value of "true" when used on <embed> tag
value=key==='allowfullscreen'&&el.tagName==='EMBED'?'true':key;el.setAttribute(key,value);}}else if(isEnumeratedAttr(key)){el.setAttribute(key,isFalsyAttrValue(value)||value==='false'?'false':'true');}else if(isXlink(key)){if(isFalsyAttrValue(value)){el.removeAttributeNS(xlinkNS,getXlinkProp(key));}else{el.setAttributeNS(xlinkNS,key,value);}}else{baseSetAttr(el,key,value);}}function baseSetAttr(el,key,value){if(isFalsyAttrValue(value)){el.removeAttribute(key);}else{// #7138: IE10 & 11 fires input event when setting placeholder on
// <textarea>... block the first input event and remove the blocker
// immediately.
/* istanbul ignore if */if(isIE&&!isIE9&&(el.tagName==='TEXTAREA'||el.tagName==='INPUT')&&key==='placeholder'&&!el.__ieph){var blocker=function blocker(e){e.stopImmediatePropagation();el.removeEventListener('input',blocker);};el.addEventListener('input',blocker);// $flow-disable-line
el.__ieph=true;/* IE placeholder patched */}el.setAttribute(key,value);}}var attrs={create:updateAttrs,update:updateAttrs};/*  */function updateClass(oldVnode,vnode){var el=vnode.elm;var data=vnode.data;var oldData=oldVnode.data;if(isUndef(data.staticClass)&&isUndef(data.class)&&(isUndef(oldData)||isUndef(oldData.staticClass)&&isUndef(oldData.class))){return;}var cls=genClassForVnode(vnode);// handle transition classes
var transitionClass=el._transitionClasses;if(isDef(transitionClass)){cls=concat(cls,stringifyClass(transitionClass));}// set the class
if(cls!==el._prevClass){el.setAttribute('class',cls);el._prevClass=cls;}}var klass={create:updateClass,update:updateClass};/*  */var validDivisionCharRE=/[\w).+\-_$\]]/;function parseFilters(exp){var inSingle=false;var inDouble=false;var inTemplateString=false;var inRegex=false;var curly=0;var square=0;var paren=0;var lastFilterIndex=0;var c,prev,i,expression,filters;for(i=0;i<exp.length;i++){prev=c;c=exp.charCodeAt(i);if(inSingle){if(c===0x27&&prev!==0x5C){inSingle=false;}}else if(inDouble){if(c===0x22&&prev!==0x5C){inDouble=false;}}else if(inTemplateString){if(c===0x60&&prev!==0x5C){inTemplateString=false;}}else if(inRegex){if(c===0x2f&&prev!==0x5C){inRegex=false;}}else if(c===0x7C&&// pipe
exp.charCodeAt(i+1)!==0x7C&&exp.charCodeAt(i-1)!==0x7C&&!curly&&!square&&!paren){if(expression===undefined){// first filter, end of expression
lastFilterIndex=i+1;expression=exp.slice(0,i).trim();}else{pushFilter();}}else{switch(c){case 0x22:inDouble=true;break;// "
case 0x27:inSingle=true;break;// '
case 0x60:inTemplateString=true;break;// `
case 0x28:paren++;break;// (
case 0x29:paren--;break;// )
case 0x5B:square++;break;// [
case 0x5D:square--;break;// ]
case 0x7B:curly++;break;// {
case 0x7D:curly--;break;// }
}if(c===0x2f){// /
var j=i-1;var p=void 0;// find first non-whitespace prev char
for(;j>=0;j--){p=exp.charAt(j);if(p!==' '){break;}}if(!p||!validDivisionCharRE.test(p)){inRegex=true;}}}}if(expression===undefined){expression=exp.slice(0,i).trim();}else if(lastFilterIndex!==0){pushFilter();}function pushFilter(){(filters||(filters=[])).push(exp.slice(lastFilterIndex,i).trim());lastFilterIndex=i+1;}if(filters){for(i=0;i<filters.length;i++){expression=wrapFilter(expression,filters[i]);}}return expression;}function wrapFilter(exp,filter){var i=filter.indexOf('(');if(i<0){// _f: resolveFilter
return"_f(\""+filter+"\")("+exp+")";}else{var name=filter.slice(0,i);var args=filter.slice(i+1);return"_f(\""+name+"\")("+exp+(args!==')'?','+args:args);}}/*  */function baseWarn(msg){console.error("[Vue compiler]: "+msg);}function pluckModuleFunction(modules,key){return modules?modules.map(function(m){return m[key];}).filter(function(_){return _;}):[];}function addProp(el,name,value){(el.props||(el.props=[])).push({name:name,value:value});el.plain=false;}function addAttr(el,name,value){(el.attrs||(el.attrs=[])).push({name:name,value:value});el.plain=false;}// add a raw attr (use this in preTransforms)
function addRawAttr(el,name,value){el.attrsMap[name]=value;el.attrsList.push({name:name,value:value});}function addDirective(el,name,rawName,value,arg,modifiers){(el.directives||(el.directives=[])).push({name:name,rawName:rawName,value:value,arg:arg,modifiers:modifiers});el.plain=false;}function addHandler(el,name,value,modifiers,important,warn){modifiers=modifiers||emptyObject;// warn prevent and passive modifier
/* istanbul ignore if */if(process.env.NODE_ENV!=='production'&&warn&&modifiers.prevent&&modifiers.passive){warn('passive and prevent can\'t be used together. '+'Passive handler can\'t prevent default event.');}// normalize click.right and click.middle since they don't actually fire
// this is technically browser-specific, but at least for now browsers are
// the only target envs that have right/middle clicks.
if(name==='click'){if(modifiers.right){name='contextmenu';delete modifiers.right;}else if(modifiers.middle){name='mouseup';}}// check capture modifier
if(modifiers.capture){delete modifiers.capture;name='!'+name;// mark the event as captured
}if(modifiers.once){delete modifiers.once;name='~'+name;// mark the event as once
}/* istanbul ignore if */if(modifiers.passive){delete modifiers.passive;name='&'+name;// mark the event as passive
}var events;if(modifiers.native){delete modifiers.native;events=el.nativeEvents||(el.nativeEvents={});}else{events=el.events||(el.events={});}var newHandler={value:value.trim()};if(modifiers!==emptyObject){newHandler.modifiers=modifiers;}var handlers=events[name];/* istanbul ignore if */if(Array.isArray(handlers)){important?handlers.unshift(newHandler):handlers.push(newHandler);}else if(handlers){events[name]=important?[newHandler,handlers]:[handlers,newHandler];}else{events[name]=newHandler;}el.plain=false;}function getBindingAttr(el,name,getStatic){var dynamicValue=getAndRemoveAttr(el,':'+name)||getAndRemoveAttr(el,'v-bind:'+name);if(dynamicValue!=null){return parseFilters(dynamicValue);}else if(getStatic!==false){var staticValue=getAndRemoveAttr(el,name);if(staticValue!=null){return JSON.stringify(staticValue);}}}// note: this only removes the attr from the Array (attrsList) so that it
// doesn't get processed by processAttrs.
// By default it does NOT remove it from the map (attrsMap) because the map is
// needed during codegen.
function getAndRemoveAttr(el,name,removeFromMap){var val;if((val=el.attrsMap[name])!=null){var list=el.attrsList;for(var i=0,l=list.length;i<l;i++){if(list[i].name===name){list.splice(i,1);break;}}}if(removeFromMap){delete el.attrsMap[name];}return val;}/*  *//**
 * Cross-platform code generation for component v-model
 */function genComponentModel(el,value,modifiers){var ref=modifiers||{};var number=ref.number;var trim=ref.trim;var baseValueExpression='$$v';var valueExpression=baseValueExpression;if(trim){valueExpression="(typeof "+baseValueExpression+" === 'string'"+"? "+baseValueExpression+".trim()"+": "+baseValueExpression+")";}if(number){valueExpression="_n("+valueExpression+")";}var assignment=genAssignmentCode(value,valueExpression);el.model={value:"("+value+")",expression:JSON.stringify(value),callback:"function ("+baseValueExpression+") {"+assignment+"}"};}/**
 * Cross-platform codegen helper for generating v-model value assignment code.
 */function genAssignmentCode(value,assignment){var res=parseModel(value);if(res.key===null){return value+"="+assignment;}else{return"$set("+res.exp+", "+res.key+", "+assignment+")";}}/**
 * Parse a v-model expression into a base path and a final key segment.
 * Handles both dot-path and possible square brackets.
 *
 * Possible cases:
 *
 * - test
 * - test[key]
 * - test[test1[key]]
 * - test["a"][key]
 * - xxx.test[a[a].test1[key]]
 * - test.xxx.a["asa"][test1[key]]
 *
 */var len,str,chr,index$1,expressionPos,expressionEndPos;function parseModel(val){// Fix https://github.com/vuejs/vue/pull/7730
// allow v-model="obj.val " (trailing whitespace)
val=val.trim();len=val.length;if(val.indexOf('[')<0||val.lastIndexOf(']')<len-1){index$1=val.lastIndexOf('.');if(index$1>-1){return{exp:val.slice(0,index$1),key:'"'+val.slice(index$1+1)+'"'};}else{return{exp:val,key:null};}}str=val;index$1=expressionPos=expressionEndPos=0;while(!eof()){chr=next();/* istanbul ignore if */if(isStringStart(chr)){parseString(chr);}else if(chr===0x5B){parseBracket(chr);}}return{exp:val.slice(0,expressionPos),key:val.slice(expressionPos+1,expressionEndPos)};}function next(){return str.charCodeAt(++index$1);}function eof(){return index$1>=len;}function isStringStart(chr){return chr===0x22||chr===0x27;}function parseBracket(chr){var inBracket=1;expressionPos=index$1;while(!eof()){chr=next();if(isStringStart(chr)){parseString(chr);continue;}if(chr===0x5B){inBracket++;}if(chr===0x5D){inBracket--;}if(inBracket===0){expressionEndPos=index$1;break;}}}function parseString(chr){var stringQuote=chr;while(!eof()){chr=next();if(chr===stringQuote){break;}}}/*  */var warn$1;// in some cases, the event used has to be determined at runtime
// so we used some reserved tokens during compile.
var RANGE_TOKEN='__r';var CHECKBOX_RADIO_TOKEN='__c';function model(el,dir,_warn){warn$1=_warn;var value=dir.value;var modifiers=dir.modifiers;var tag=el.tag;var type=el.attrsMap.type;if(process.env.NODE_ENV!=='production'){// inputs with type="file" are read only and setting the input's
// value will throw an error.
if(tag==='input'&&type==='file'){warn$1("<"+el.tag+" v-model=\""+value+"\" type=\"file\">:\n"+"File inputs are read only. Use a v-on:change listener instead.");}}if(el.component){genComponentModel(el,value,modifiers);// component v-model doesn't need extra runtime
return false;}else if(tag==='select'){genSelect(el,value,modifiers);}else if(tag==='input'&&type==='checkbox'){genCheckboxModel(el,value,modifiers);}else if(tag==='input'&&type==='radio'){genRadioModel(el,value,modifiers);}else if(tag==='input'||tag==='textarea'){genDefaultModel(el,value,modifiers);}else if(!config.isReservedTag(tag)){genComponentModel(el,value,modifiers);// component v-model doesn't need extra runtime
return false;}else if(process.env.NODE_ENV!=='production'){warn$1("<"+el.tag+" v-model=\""+value+"\">: "+"v-model is not supported on this element type. "+'If you are working with contenteditable, it\'s recommended to '+'wrap a library dedicated for that purpose inside a custom component.');}// ensure runtime directive metadata
return true;}function genCheckboxModel(el,value,modifiers){var number=modifiers&&modifiers.number;var valueBinding=getBindingAttr(el,'value')||'null';var trueValueBinding=getBindingAttr(el,'true-value')||'true';var falseValueBinding=getBindingAttr(el,'false-value')||'false';addProp(el,'checked',"Array.isArray("+value+")"+"?_i("+value+","+valueBinding+")>-1"+(trueValueBinding==='true'?":("+value+")":":_q("+value+","+trueValueBinding+")"));addHandler(el,'change',"var $$a="+value+","+'$$el=$event.target,'+"$$c=$$el.checked?("+trueValueBinding+"):("+falseValueBinding+");"+'if(Array.isArray($$a)){'+"var $$v="+(number?'_n('+valueBinding+')':valueBinding)+","+'$$i=_i($$a,$$v);'+"if($$el.checked){$$i<0&&("+genAssignmentCode(value,'$$a.concat([$$v])')+")}"+"else{$$i>-1&&("+genAssignmentCode(value,'$$a.slice(0,$$i).concat($$a.slice($$i+1))')+")}"+"}else{"+genAssignmentCode(value,'$$c')+"}",null,true);}function genRadioModel(el,value,modifiers){var number=modifiers&&modifiers.number;var valueBinding=getBindingAttr(el,'value')||'null';valueBinding=number?"_n("+valueBinding+")":valueBinding;addProp(el,'checked',"_q("+value+","+valueBinding+")");addHandler(el,'change',genAssignmentCode(value,valueBinding),null,true);}function genSelect(el,value,modifiers){var number=modifiers&&modifiers.number;var selectedVal="Array.prototype.filter"+".call($event.target.options,function(o){return o.selected})"+".map(function(o){var val = \"_value\" in o ? o._value : o.value;"+"return "+(number?'_n(val)':'val')+"})";var assignment='$event.target.multiple ? $$selectedVal : $$selectedVal[0]';var code="var $$selectedVal = "+selectedVal+";";code=code+" "+genAssignmentCode(value,assignment);addHandler(el,'change',code,null,true);}function genDefaultModel(el,value,modifiers){var type=el.attrsMap.type;// warn if v-bind:value conflicts with v-model
// except for inputs with v-bind:type
if(process.env.NODE_ENV!=='production'){var value$1=el.attrsMap['v-bind:value']||el.attrsMap[':value'];var typeBinding=el.attrsMap['v-bind:type']||el.attrsMap[':type'];if(value$1&&!typeBinding){var binding=el.attrsMap['v-bind:value']?'v-bind:value':':value';warn$1(binding+"=\""+value$1+"\" conflicts with v-model on the same element "+'because the latter already expands to a value binding internally');}}var ref=modifiers||{};var lazy=ref.lazy;var number=ref.number;var trim=ref.trim;var needCompositionGuard=!lazy&&type!=='range';var event=lazy?'change':type==='range'?RANGE_TOKEN:'input';var valueExpression='$event.target.value';if(trim){valueExpression="$event.target.value.trim()";}if(number){valueExpression="_n("+valueExpression+")";}var code=genAssignmentCode(value,valueExpression);if(needCompositionGuard){code="if($event.target.composing)return;"+code;}addProp(el,'value',"("+value+")");addHandler(el,event,code,null,true);if(trim||number){addHandler(el,'blur','$forceUpdate()');}}/*  */// normalize v-model event tokens that can only be determined at runtime.
// it's important to place the event as the first in the array because
// the whole point is ensuring the v-model callback gets called before
// user-attached handlers.
function normalizeEvents(on){/* istanbul ignore if */if(isDef(on[RANGE_TOKEN])){// IE input[type=range] only supports `change` event
var event=isIE?'change':'input';on[event]=[].concat(on[RANGE_TOKEN],on[event]||[]);delete on[RANGE_TOKEN];}// This was originally intended to fix #4521 but no longer necessary
// after 2.5. Keeping it for backwards compat with generated code from < 2.4
/* istanbul ignore if */if(isDef(on[CHECKBOX_RADIO_TOKEN])){on.change=[].concat(on[CHECKBOX_RADIO_TOKEN],on.change||[]);delete on[CHECKBOX_RADIO_TOKEN];}}var target$1;function createOnceHandler$1(event,handler,capture){var _target=target$1;// save current target element in closure
return function onceHandler(){var res=handler.apply(null,arguments);if(res!==null){remove$2(event,onceHandler,capture,_target);}};}function add$1(event,handler,capture,passive){handler=withMacroTask(handler);target$1.addEventListener(event,handler,supportsPassive?{capture:capture,passive:passive}:capture);}function remove$2(event,handler,capture,_target){(_target||target$1).removeEventListener(event,handler._withTask||handler,capture);}function updateDOMListeners(oldVnode,vnode){if(isUndef(oldVnode.data.on)&&isUndef(vnode.data.on)){return;}var on=vnode.data.on||{};var oldOn=oldVnode.data.on||{};target$1=vnode.elm;normalizeEvents(on);updateListeners(on,oldOn,add$1,remove$2,createOnceHandler$1,vnode.context);target$1=undefined;}var events={create:updateDOMListeners,update:updateDOMListeners};/*  */function updateDOMProps(oldVnode,vnode){if(isUndef(oldVnode.data.domProps)&&isUndef(vnode.data.domProps)){return;}var key,cur;var elm=vnode.elm;var oldProps=oldVnode.data.domProps||{};var props=vnode.data.domProps||{};// clone observed objects, as the user probably wants to mutate it
if(isDef(props.__ob__)){props=vnode.data.domProps=extend({},props);}for(key in oldProps){if(isUndef(props[key])){elm[key]='';}}for(key in props){cur=props[key];// ignore children if the node has textContent or innerHTML,
// as these will throw away existing DOM nodes and cause removal errors
// on subsequent patches (#3360)
if(key==='textContent'||key==='innerHTML'){if(vnode.children){vnode.children.length=0;}if(cur===oldProps[key]){continue;}// #6601 work around Chrome version <= 55 bug where single textNode
// replaced by innerHTML/textContent retains its parentNode property
if(elm.childNodes.length===1){elm.removeChild(elm.childNodes[0]);}}if(key==='value'){// store value as _value as well since
// non-string values will be stringified
elm._value=cur;// avoid resetting cursor position when value is the same
var strCur=isUndef(cur)?'':String(cur);if(shouldUpdateValue(elm,strCur)){elm.value=strCur;}}else{elm[key]=cur;}}}// check platforms/web/util/attrs.js acceptValue
function shouldUpdateValue(elm,checkVal){return!elm.composing&&(elm.tagName==='OPTION'||isNotInFocusAndDirty(elm,checkVal)||isDirtyWithModifiers(elm,checkVal));}function isNotInFocusAndDirty(elm,checkVal){// return true when textbox (.number and .trim) loses focus and its value is
// not equal to the updated value
var notInFocus=true;// #6157
// work around IE bug when accessing document.activeElement in an iframe
try{notInFocus=document.activeElement!==elm;}catch(e){}return notInFocus&&elm.value!==checkVal;}function isDirtyWithModifiers(elm,newVal){var value=elm.value;var modifiers=elm._vModifiers;// injected by v-model runtime
if(isDef(modifiers)){if(modifiers.lazy){// inputs with lazy should only be updated when not in focus
return false;}if(modifiers.number){return toNumber(value)!==toNumber(newVal);}if(modifiers.trim){return value.trim()!==newVal.trim();}}return value!==newVal;}var domProps={create:updateDOMProps,update:updateDOMProps};/*  */var parseStyleText=cached(function(cssText){var res={};var listDelimiter=/;(?![^(]*\))/g;var propertyDelimiter=/:(.+)/;cssText.split(listDelimiter).forEach(function(item){if(item){var tmp=item.split(propertyDelimiter);tmp.length>1&&(res[tmp[0].trim()]=tmp[1].trim());}});return res;});// merge static and dynamic style data on the same vnode
function normalizeStyleData(data){var style=normalizeStyleBinding(data.style);// static style is pre-processed into an object during compilation
// and is always a fresh object, so it's safe to merge into it
return data.staticStyle?extend(data.staticStyle,style):style;}// normalize possible array / string values into Object
function normalizeStyleBinding(bindingStyle){if(Array.isArray(bindingStyle)){return toObject(bindingStyle);}if(typeof bindingStyle==='string'){return parseStyleText(bindingStyle);}return bindingStyle;}/**
 * parent component style should be after child's
 * so that parent component's style could override it
 */function getStyle(vnode,checkChild){var res={};var styleData;if(checkChild){var childNode=vnode;while(childNode.componentInstance){childNode=childNode.componentInstance._vnode;if(childNode&&childNode.data&&(styleData=normalizeStyleData(childNode.data))){extend(res,styleData);}}}if(styleData=normalizeStyleData(vnode.data)){extend(res,styleData);}var parentNode=vnode;while(parentNode=parentNode.parent){if(parentNode.data&&(styleData=normalizeStyleData(parentNode.data))){extend(res,styleData);}}return res;}/*  */var cssVarRE=/^--/;var importantRE=/\s*!important$/;var setProp=function setProp(el,name,val){/* istanbul ignore if */if(cssVarRE.test(name)){el.style.setProperty(name,val);}else if(importantRE.test(val)){el.style.setProperty(name,val.replace(importantRE,''),'important');}else{var normalizedName=normalize(name);if(Array.isArray(val)){// Support values array created by autoprefixer, e.g.
// {display: ["-webkit-box", "-ms-flexbox", "flex"]}
// Set them one by one, and the browser will only set those it can recognize
for(var i=0,len=val.length;i<len;i++){el.style[normalizedName]=val[i];}}else{el.style[normalizedName]=val;}}};var vendorNames=['Webkit','Moz','ms'];var emptyStyle;var normalize=cached(function(prop){emptyStyle=emptyStyle||document.createElement('div').style;prop=camelize(prop);if(prop!=='filter'&&prop in emptyStyle){return prop;}var capName=prop.charAt(0).toUpperCase()+prop.slice(1);for(var i=0;i<vendorNames.length;i++){var name=vendorNames[i]+capName;if(name in emptyStyle){return name;}}});function updateStyle(oldVnode,vnode){var data=vnode.data;var oldData=oldVnode.data;if(isUndef(data.staticStyle)&&isUndef(data.style)&&isUndef(oldData.staticStyle)&&isUndef(oldData.style)){return;}var cur,name;var el=vnode.elm;var oldStaticStyle=oldData.staticStyle;var oldStyleBinding=oldData.normalizedStyle||oldData.style||{};// if static style exists, stylebinding already merged into it when doing normalizeStyleData
var oldStyle=oldStaticStyle||oldStyleBinding;var style=normalizeStyleBinding(vnode.data.style)||{};// store normalized style under a different key for next diff
// make sure to clone it if it's reactive, since the user likely wants
// to mutate it.
vnode.data.normalizedStyle=isDef(style.__ob__)?extend({},style):style;var newStyle=getStyle(vnode,true);for(name in oldStyle){if(isUndef(newStyle[name])){setProp(el,name,'');}}for(name in newStyle){cur=newStyle[name];if(cur!==oldStyle[name]){// ie9 setting to null has no effect, must use empty string
setProp(el,name,cur==null?'':cur);}}}var style={create:updateStyle,update:updateStyle};/*  */var whitespaceRE=/\s+/;/**
 * Add class with compatibility for SVG since classList is not supported on
 * SVG elements in IE
 */function addClass(el,cls){/* istanbul ignore if */if(!cls||!(cls=cls.trim())){return;}/* istanbul ignore else */if(el.classList){if(cls.indexOf(' ')>-1){cls.split(whitespaceRE).forEach(function(c){return el.classList.add(c);});}else{el.classList.add(cls);}}else{var cur=" "+(el.getAttribute('class')||'')+" ";if(cur.indexOf(' '+cls+' ')<0){el.setAttribute('class',(cur+cls).trim());}}}/**
 * Remove class with compatibility for SVG since classList is not supported on
 * SVG elements in IE
 */function removeClass(el,cls){/* istanbul ignore if */if(!cls||!(cls=cls.trim())){return;}/* istanbul ignore else */if(el.classList){if(cls.indexOf(' ')>-1){cls.split(whitespaceRE).forEach(function(c){return el.classList.remove(c);});}else{el.classList.remove(cls);}if(!el.classList.length){el.removeAttribute('class');}}else{var cur=" "+(el.getAttribute('class')||'')+" ";var tar=' '+cls+' ';while(cur.indexOf(tar)>=0){cur=cur.replace(tar,' ');}cur=cur.trim();if(cur){el.setAttribute('class',cur);}else{el.removeAttribute('class');}}}/*  */function resolveTransition(def$$1){if(!def$$1){return;}/* istanbul ignore else */if((typeof def$$1==='undefined'?'undefined':_typeof2(def$$1))==='object'){var res={};if(def$$1.css!==false){extend(res,autoCssTransition(def$$1.name||'v'));}extend(res,def$$1);return res;}else if(typeof def$$1==='string'){return autoCssTransition(def$$1);}}var autoCssTransition=cached(function(name){return{enterClass:name+"-enter",enterToClass:name+"-enter-to",enterActiveClass:name+"-enter-active",leaveClass:name+"-leave",leaveToClass:name+"-leave-to",leaveActiveClass:name+"-leave-active"};});var hasTransition=inBrowser&&!isIE9;var TRANSITION='transition';var ANIMATION='animation';// Transition property/event sniffing
var transitionProp='transition';var transitionEndEvent='transitionend';var animationProp='animation';var animationEndEvent='animationend';if(hasTransition){/* istanbul ignore if */if(window.ontransitionend===undefined&&window.onwebkittransitionend!==undefined){transitionProp='WebkitTransition';transitionEndEvent='webkitTransitionEnd';}if(window.onanimationend===undefined&&window.onwebkitanimationend!==undefined){animationProp='WebkitAnimation';animationEndEvent='webkitAnimationEnd';}}// binding to window is necessary to make hot reload work in IE in strict mode
var raf=inBrowser?window.requestAnimationFrame?window.requestAnimationFrame.bind(window):setTimeout:/* istanbul ignore next */function(fn){return fn();};function nextFrame(fn){raf(function(){raf(fn);});}function addTransitionClass(el,cls){var transitionClasses=el._transitionClasses||(el._transitionClasses=[]);if(transitionClasses.indexOf(cls)<0){transitionClasses.push(cls);addClass(el,cls);}}function removeTransitionClass(el,cls){if(el._transitionClasses){remove(el._transitionClasses,cls);}removeClass(el,cls);}function whenTransitionEnds(el,expectedType,cb){var ref=getTransitionInfo(el,expectedType);var type=ref.type;var timeout=ref.timeout;var propCount=ref.propCount;if(!type){return cb();}var event=type===TRANSITION?transitionEndEvent:animationEndEvent;var ended=0;var end=function end(){el.removeEventListener(event,onEnd);cb();};var onEnd=function onEnd(e){if(e.target===el){if(++ended>=propCount){end();}}};setTimeout(function(){if(ended<propCount){end();}},timeout+1);el.addEventListener(event,onEnd);}var transformRE=/\b(transform|all)(,|$)/;function getTransitionInfo(el,expectedType){var styles=window.getComputedStyle(el);// JSDOM may return undefined for transition properties
var transitionDelays=(styles[transitionProp+'Delay']||'').split(', ');var transitionDurations=(styles[transitionProp+'Duration']||'').split(', ');var transitionTimeout=getTimeout(transitionDelays,transitionDurations);var animationDelays=(styles[animationProp+'Delay']||'').split(', ');var animationDurations=(styles[animationProp+'Duration']||'').split(', ');var animationTimeout=getTimeout(animationDelays,animationDurations);var type;var timeout=0;var propCount=0;/* istanbul ignore if */if(expectedType===TRANSITION){if(transitionTimeout>0){type=TRANSITION;timeout=transitionTimeout;propCount=transitionDurations.length;}}else if(expectedType===ANIMATION){if(animationTimeout>0){type=ANIMATION;timeout=animationTimeout;propCount=animationDurations.length;}}else{timeout=Math.max(transitionTimeout,animationTimeout);type=timeout>0?transitionTimeout>animationTimeout?TRANSITION:ANIMATION:null;propCount=type?type===TRANSITION?transitionDurations.length:animationDurations.length:0;}var hasTransform=type===TRANSITION&&transformRE.test(styles[transitionProp+'Property']);return{type:type,timeout:timeout,propCount:propCount,hasTransform:hasTransform};}function getTimeout(delays,durations){/* istanbul ignore next */while(delays.length<durations.length){delays=delays.concat(delays);}return Math.max.apply(null,durations.map(function(d,i){return toMs(d)+toMs(delays[i]);}));}// Old versions of Chromium (below 61.0.3163.100) formats floating pointer numbers
// in a locale-dependent way, using a comma instead of a dot.
// If comma is not replaced with a dot, the input will be rounded down (i.e. acting
// as a floor function) causing unexpected behaviors
function toMs(s){return Number(s.slice(0,-1).replace(',','.'))*1000;}/*  */function enter(vnode,toggleDisplay){var el=vnode.elm;// call leave callback now
if(isDef(el._leaveCb)){el._leaveCb.cancelled=true;el._leaveCb();}var data=resolveTransition(vnode.data.transition);if(isUndef(data)){return;}/* istanbul ignore if */if(isDef(el._enterCb)||el.nodeType!==1){return;}var css=data.css;var type=data.type;var enterClass=data.enterClass;var enterToClass=data.enterToClass;var enterActiveClass=data.enterActiveClass;var appearClass=data.appearClass;var appearToClass=data.appearToClass;var appearActiveClass=data.appearActiveClass;var beforeEnter=data.beforeEnter;var enter=data.enter;var afterEnter=data.afterEnter;var enterCancelled=data.enterCancelled;var beforeAppear=data.beforeAppear;var appear=data.appear;var afterAppear=data.afterAppear;var appearCancelled=data.appearCancelled;var duration=data.duration;// activeInstance will always be the <transition> component managing this
// transition. One edge case to check is when the <transition> is placed
// as the root node of a child component. In that case we need to check
// <transition>'s parent for appear check.
var context=activeInstance;var transitionNode=activeInstance.$vnode;while(transitionNode&&transitionNode.parent){transitionNode=transitionNode.parent;context=transitionNode.context;}var isAppear=!context._isMounted||!vnode.isRootInsert;if(isAppear&&!appear&&appear!==''){return;}var startClass=isAppear&&appearClass?appearClass:enterClass;var activeClass=isAppear&&appearActiveClass?appearActiveClass:enterActiveClass;var toClass=isAppear&&appearToClass?appearToClass:enterToClass;var beforeEnterHook=isAppear?beforeAppear||beforeEnter:beforeEnter;var enterHook=isAppear?typeof appear==='function'?appear:enter:enter;var afterEnterHook=isAppear?afterAppear||afterEnter:afterEnter;var enterCancelledHook=isAppear?appearCancelled||enterCancelled:enterCancelled;var explicitEnterDuration=toNumber(isObject(duration)?duration.enter:duration);if(process.env.NODE_ENV!=='production'&&explicitEnterDuration!=null){checkDuration(explicitEnterDuration,'enter',vnode);}var expectsCSS=css!==false&&!isIE9;var userWantsControl=getHookArgumentsLength(enterHook);var cb=el._enterCb=once(function(){if(expectsCSS){removeTransitionClass(el,toClass);removeTransitionClass(el,activeClass);}if(cb.cancelled){if(expectsCSS){removeTransitionClass(el,startClass);}enterCancelledHook&&enterCancelledHook(el);}else{afterEnterHook&&afterEnterHook(el);}el._enterCb=null;});if(!vnode.data.show){// remove pending leave element on enter by injecting an insert hook
mergeVNodeHook(vnode,'insert',function(){var parent=el.parentNode;var pendingNode=parent&&parent._pending&&parent._pending[vnode.key];if(pendingNode&&pendingNode.tag===vnode.tag&&pendingNode.elm._leaveCb){pendingNode.elm._leaveCb();}enterHook&&enterHook(el,cb);});}// start enter transition
beforeEnterHook&&beforeEnterHook(el);if(expectsCSS){addTransitionClass(el,startClass);addTransitionClass(el,activeClass);nextFrame(function(){removeTransitionClass(el,startClass);if(!cb.cancelled){addTransitionClass(el,toClass);if(!userWantsControl){if(isValidDuration(explicitEnterDuration)){setTimeout(cb,explicitEnterDuration);}else{whenTransitionEnds(el,type,cb);}}}});}if(vnode.data.show){toggleDisplay&&toggleDisplay();enterHook&&enterHook(el,cb);}if(!expectsCSS&&!userWantsControl){cb();}}function leave(vnode,rm){var el=vnode.elm;// call enter callback now
if(isDef(el._enterCb)){el._enterCb.cancelled=true;el._enterCb();}var data=resolveTransition(vnode.data.transition);if(isUndef(data)||el.nodeType!==1){return rm();}/* istanbul ignore if */if(isDef(el._leaveCb)){return;}var css=data.css;var type=data.type;var leaveClass=data.leaveClass;var leaveToClass=data.leaveToClass;var leaveActiveClass=data.leaveActiveClass;var beforeLeave=data.beforeLeave;var leave=data.leave;var afterLeave=data.afterLeave;var leaveCancelled=data.leaveCancelled;var delayLeave=data.delayLeave;var duration=data.duration;var expectsCSS=css!==false&&!isIE9;var userWantsControl=getHookArgumentsLength(leave);var explicitLeaveDuration=toNumber(isObject(duration)?duration.leave:duration);if(process.env.NODE_ENV!=='production'&&isDef(explicitLeaveDuration)){checkDuration(explicitLeaveDuration,'leave',vnode);}var cb=el._leaveCb=once(function(){if(el.parentNode&&el.parentNode._pending){el.parentNode._pending[vnode.key]=null;}if(expectsCSS){removeTransitionClass(el,leaveToClass);removeTransitionClass(el,leaveActiveClass);}if(cb.cancelled){if(expectsCSS){removeTransitionClass(el,leaveClass);}leaveCancelled&&leaveCancelled(el);}else{rm();afterLeave&&afterLeave(el);}el._leaveCb=null;});if(delayLeave){delayLeave(performLeave);}else{performLeave();}function performLeave(){// the delayed leave may have already been cancelled
if(cb.cancelled){return;}// record leaving element
if(!vnode.data.show&&el.parentNode){(el.parentNode._pending||(el.parentNode._pending={}))[vnode.key]=vnode;}beforeLeave&&beforeLeave(el);if(expectsCSS){addTransitionClass(el,leaveClass);addTransitionClass(el,leaveActiveClass);nextFrame(function(){removeTransitionClass(el,leaveClass);if(!cb.cancelled){addTransitionClass(el,leaveToClass);if(!userWantsControl){if(isValidDuration(explicitLeaveDuration)){setTimeout(cb,explicitLeaveDuration);}else{whenTransitionEnds(el,type,cb);}}}});}leave&&leave(el,cb);if(!expectsCSS&&!userWantsControl){cb();}}}// only used in dev mode
function checkDuration(val,name,vnode){if(typeof val!=='number'){warn("<transition> explicit "+name+" duration is not a valid number - "+"got "+JSON.stringify(val)+".",vnode.context);}else if(isNaN(val)){warn("<transition> explicit "+name+" duration is NaN - "+'the duration expression might be incorrect.',vnode.context);}}function isValidDuration(val){return typeof val==='number'&&!isNaN(val);}/**
 * Normalize a transition hook's argument length. The hook may be:
 * - a merged hook (invoker) with the original in .fns
 * - a wrapped component method (check ._length)
 * - a plain function (.length)
 */function getHookArgumentsLength(fn){if(isUndef(fn)){return false;}var invokerFns=fn.fns;if(isDef(invokerFns)){// invoker
return getHookArgumentsLength(Array.isArray(invokerFns)?invokerFns[0]:invokerFns);}else{return(fn._length||fn.length)>1;}}function _enter(_,vnode){if(vnode.data.show!==true){enter(vnode);}}var transition=inBrowser?{create:_enter,activate:_enter,remove:function remove$$1(vnode,rm){/* istanbul ignore else */if(vnode.data.show!==true){leave(vnode,rm);}else{rm();}}}:{};var platformModules=[attrs,klass,events,domProps,style,transition];/*  */// the directive module should be applied last, after all
// built-in modules have been applied.
var modules=platformModules.concat(baseModules);var patch=createPatchFunction({nodeOps:nodeOps,modules:modules});/**
 * Not type checking this file because flow doesn't like attaching
 * properties to Elements.
 *//* istanbul ignore if */if(isIE9){// http://www.matts411.com/post/internet-explorer-9-oninput/
document.addEventListener('selectionchange',function(){var el=document.activeElement;if(el&&el.vmodel){trigger(el,'input');}});}var directive={inserted:function inserted(el,binding,vnode,oldVnode){if(vnode.tag==='select'){// #6903
if(oldVnode.elm&&!oldVnode.elm._vOptions){mergeVNodeHook(vnode,'postpatch',function(){directive.componentUpdated(el,binding,vnode);});}else{setSelected(el,binding,vnode.context);}el._vOptions=[].map.call(el.options,getValue);}else if(vnode.tag==='textarea'||isTextInputType(el.type)){el._vModifiers=binding.modifiers;if(!binding.modifiers.lazy){el.addEventListener('compositionstart',onCompositionStart);el.addEventListener('compositionend',onCompositionEnd);// Safari < 10.2 & UIWebView doesn't fire compositionend when
// switching focus before confirming composition choice
// this also fixes the issue where some browsers e.g. iOS Chrome
// fires "change" instead of "input" on autocomplete.
el.addEventListener('change',onCompositionEnd);/* istanbul ignore if */if(isIE9){el.vmodel=true;}}}},componentUpdated:function componentUpdated(el,binding,vnode){if(vnode.tag==='select'){setSelected(el,binding,vnode.context);// in case the options rendered by v-for have changed,
// it's possible that the value is out-of-sync with the rendered options.
// detect such cases and filter out values that no longer has a matching
// option in the DOM.
var prevOptions=el._vOptions;var curOptions=el._vOptions=[].map.call(el.options,getValue);if(curOptions.some(function(o,i){return!looseEqual(o,prevOptions[i]);})){// trigger change event if
// no matching option found for at least one value
var needReset=el.multiple?binding.value.some(function(v){return hasNoMatchingOption(v,curOptions);}):binding.value!==binding.oldValue&&hasNoMatchingOption(binding.value,curOptions);if(needReset){trigger(el,'change');}}}}};function setSelected(el,binding,vm){actuallySetSelected(el,binding,vm);/* istanbul ignore if */if(isIE||isEdge){setTimeout(function(){actuallySetSelected(el,binding,vm);},0);}}function actuallySetSelected(el,binding,vm){var value=binding.value;var isMultiple=el.multiple;if(isMultiple&&!Array.isArray(value)){process.env.NODE_ENV!=='production'&&warn("<select multiple v-model=\""+binding.expression+"\"> "+"expects an Array value for its binding, but got "+Object.prototype.toString.call(value).slice(8,-1),vm);return;}var selected,option;for(var i=0,l=el.options.length;i<l;i++){option=el.options[i];if(isMultiple){selected=looseIndexOf(value,getValue(option))>-1;if(option.selected!==selected){option.selected=selected;}}else{if(looseEqual(getValue(option),value)){if(el.selectedIndex!==i){el.selectedIndex=i;}return;}}}if(!isMultiple){el.selectedIndex=-1;}}function hasNoMatchingOption(value,options){return options.every(function(o){return!looseEqual(o,value);});}function getValue(option){return'_value'in option?option._value:option.value;}function onCompositionStart(e){e.target.composing=true;}function onCompositionEnd(e){// prevent triggering an input event for no reason
if(!e.target.composing){return;}e.target.composing=false;trigger(e.target,'input');}function trigger(el,type){var e=document.createEvent('HTMLEvents');e.initEvent(type,true,true);el.dispatchEvent(e);}/*  */// recursively search for possible transition defined inside the component root
function locateNode(vnode){return vnode.componentInstance&&(!vnode.data||!vnode.data.transition)?locateNode(vnode.componentInstance._vnode):vnode;}var show={bind:function bind(el,ref,vnode){var value=ref.value;vnode=locateNode(vnode);var transition$$1=vnode.data&&vnode.data.transition;var originalDisplay=el.__vOriginalDisplay=el.style.display==='none'?'':el.style.display;if(value&&transition$$1){vnode.data.show=true;enter(vnode,function(){el.style.display=originalDisplay;});}else{el.style.display=value?originalDisplay:'none';}},update:function update(el,ref,vnode){var value=ref.value;var oldValue=ref.oldValue;/* istanbul ignore if */if(!value===!oldValue){return;}vnode=locateNode(vnode);var transition$$1=vnode.data&&vnode.data.transition;if(transition$$1){vnode.data.show=true;if(value){enter(vnode,function(){el.style.display=el.__vOriginalDisplay;});}else{leave(vnode,function(){el.style.display='none';});}}else{el.style.display=value?el.__vOriginalDisplay:'none';}},unbind:function unbind(el,binding,vnode,oldVnode,isDestroy){if(!isDestroy){el.style.display=el.__vOriginalDisplay;}}};var platformDirectives={model:directive,show:show};/*  */var transitionProps={name:String,appear:Boolean,css:Boolean,mode:String,type:String,enterClass:String,leaveClass:String,enterToClass:String,leaveToClass:String,enterActiveClass:String,leaveActiveClass:String,appearClass:String,appearActiveClass:String,appearToClass:String,duration:[Number,String,Object]};// in case the child is also an abstract component, e.g. <keep-alive>
// we want to recursively retrieve the real component to be rendered
function getRealChild(vnode){var compOptions=vnode&&vnode.componentOptions;if(compOptions&&compOptions.Ctor.options.abstract){return getRealChild(getFirstComponentChild(compOptions.children));}else{return vnode;}}function extractTransitionData(comp){var data={};var options=comp.$options;// props
for(var key in options.propsData){data[key]=comp[key];}// events.
// extract listeners and pass them directly to the transition methods
var listeners=options._parentListeners;for(var key$1 in listeners){data[camelize(key$1)]=listeners[key$1];}return data;}function placeholder(h,rawChild){if(/\d-keep-alive$/.test(rawChild.tag)){return h('keep-alive',{props:rawChild.componentOptions.propsData});}}function hasParentTransition(vnode){while(vnode=vnode.parent){if(vnode.data.transition){return true;}}}function isSameChild(child,oldChild){return oldChild.key===child.key&&oldChild.tag===child.tag;}var isNotTextNode=function isNotTextNode(c){return c.tag||isAsyncPlaceholder(c);};var isVShowDirective=function isVShowDirective(d){return d.name==='show';};var Transition={name:'transition',props:transitionProps,abstract:true,render:function render(h){var this$1=this;var children=this.$slots.default;if(!children){return;}// filter out text nodes (possible whitespaces)
children=children.filter(isNotTextNode);/* istanbul ignore if */if(!children.length){return;}// warn multiple elements
if(process.env.NODE_ENV!=='production'&&children.length>1){warn('<transition> can only be used on a single element. Use '+'<transition-group> for lists.',this.$parent);}var mode=this.mode;// warn invalid mode
if(process.env.NODE_ENV!=='production'&&mode&&mode!=='in-out'&&mode!=='out-in'){warn('invalid <transition> mode: '+mode,this.$parent);}var rawChild=children[0];// if this is a component root node and the component's
// parent container node also has transition, skip.
if(hasParentTransition(this.$vnode)){return rawChild;}// apply transition data to child
// use getRealChild() to ignore abstract components e.g. keep-alive
var child=getRealChild(rawChild);/* istanbul ignore if */if(!child){return rawChild;}if(this._leaving){return placeholder(h,rawChild);}// ensure a key that is unique to the vnode type and to this transition
// component instance. This key will be used to remove pending leaving nodes
// during entering.
var id="__transition-"+this._uid+"-";child.key=child.key==null?child.isComment?id+'comment':id+child.tag:isPrimitive(child.key)?String(child.key).indexOf(id)===0?child.key:id+child.key:child.key;var data=(child.data||(child.data={})).transition=extractTransitionData(this);var oldRawChild=this._vnode;var oldChild=getRealChild(oldRawChild);// mark v-show
// so that the transition module can hand over the control to the directive
if(child.data.directives&&child.data.directives.some(isVShowDirective)){child.data.show=true;}if(oldChild&&oldChild.data&&!isSameChild(child,oldChild)&&!isAsyncPlaceholder(oldChild)&&// #6687 component root is a comment node
!(oldChild.componentInstance&&oldChild.componentInstance._vnode.isComment)){// replace old child transition data with fresh one
// important for dynamic transitions!
var oldData=oldChild.data.transition=extend({},data);// handle transition mode
if(mode==='out-in'){// return placeholder node and queue update when leave finishes
this._leaving=true;mergeVNodeHook(oldData,'afterLeave',function(){this$1._leaving=false;this$1.$forceUpdate();});return placeholder(h,rawChild);}else if(mode==='in-out'){if(isAsyncPlaceholder(child)){return oldRawChild;}var delayedLeave;var performLeave=function performLeave(){delayedLeave();};mergeVNodeHook(data,'afterEnter',performLeave);mergeVNodeHook(data,'enterCancelled',performLeave);mergeVNodeHook(oldData,'delayLeave',function(leave){delayedLeave=leave;});}}return rawChild;}};/*  */var props=extend({tag:String,moveClass:String},transitionProps);delete props.mode;var TransitionGroup={props:props,beforeMount:function beforeMount(){var this$1=this;var update=this._update;this._update=function(vnode,hydrating){var restoreActiveInstance=setActiveInstance(this$1);// force removing pass
this$1.__patch__(this$1._vnode,this$1.kept,false,// hydrating
true// removeOnly (!important, avoids unnecessary moves)
);this$1._vnode=this$1.kept;restoreActiveInstance();update.call(this$1,vnode,hydrating);};},render:function render(h){var tag=this.tag||this.$vnode.data.tag||'span';var map=Object.create(null);var prevChildren=this.prevChildren=this.children;var rawChildren=this.$slots.default||[];var children=this.children=[];var transitionData=extractTransitionData(this);for(var i=0;i<rawChildren.length;i++){var c=rawChildren[i];if(c.tag){if(c.key!=null&&String(c.key).indexOf('__vlist')!==0){children.push(c);map[c.key]=c;(c.data||(c.data={})).transition=transitionData;}else if(process.env.NODE_ENV!=='production'){var opts=c.componentOptions;var name=opts?opts.Ctor.options.name||opts.tag||'':c.tag;warn("<transition-group> children must be keyed: <"+name+">");}}}if(prevChildren){var kept=[];var removed=[];for(var i$1=0;i$1<prevChildren.length;i$1++){var c$1=prevChildren[i$1];c$1.data.transition=transitionData;c$1.data.pos=c$1.elm.getBoundingClientRect();if(map[c$1.key]){kept.push(c$1);}else{removed.push(c$1);}}this.kept=h(tag,null,kept);this.removed=removed;}return h(tag,null,children);},updated:function updated(){var children=this.prevChildren;var moveClass=this.moveClass||(this.name||'v')+'-move';if(!children.length||!this.hasMove(children[0].elm,moveClass)){return;}// we divide the work into three loops to avoid mixing DOM reads and writes
// in each iteration - which helps prevent layout thrashing.
children.forEach(callPendingCbs);children.forEach(recordPosition);children.forEach(applyTranslation);// force reflow to put everything in position
// assign to this to avoid being removed in tree-shaking
// $flow-disable-line
this._reflow=document.body.offsetHeight;children.forEach(function(c){if(c.data.moved){var el=c.elm;var s=el.style;addTransitionClass(el,moveClass);s.transform=s.WebkitTransform=s.transitionDuration='';el.addEventListener(transitionEndEvent,el._moveCb=function cb(e){if(e&&e.target!==el){return;}if(!e||/transform$/.test(e.propertyName)){el.removeEventListener(transitionEndEvent,cb);el._moveCb=null;removeTransitionClass(el,moveClass);}});}});},methods:{hasMove:function hasMove(el,moveClass){/* istanbul ignore if */if(!hasTransition){return false;}/* istanbul ignore if */if(this._hasMove){return this._hasMove;}// Detect whether an element with the move class applied has
// CSS transitions. Since the element may be inside an entering
// transition at this very moment, we make a clone of it and remove
// all other transition classes applied to ensure only the move class
// is applied.
var clone=el.cloneNode();if(el._transitionClasses){el._transitionClasses.forEach(function(cls){removeClass(clone,cls);});}addClass(clone,moveClass);clone.style.display='none';this.$el.appendChild(clone);var info=getTransitionInfo(clone);this.$el.removeChild(clone);return this._hasMove=info.hasTransform;}}};function callPendingCbs(c){/* istanbul ignore if */if(c.elm._moveCb){c.elm._moveCb();}/* istanbul ignore if */if(c.elm._enterCb){c.elm._enterCb();}}function recordPosition(c){c.data.newPos=c.elm.getBoundingClientRect();}function applyTranslation(c){var oldPos=c.data.pos;var newPos=c.data.newPos;var dx=oldPos.left-newPos.left;var dy=oldPos.top-newPos.top;if(dx||dy){c.data.moved=true;var s=c.elm.style;s.transform=s.WebkitTransform="translate("+dx+"px,"+dy+"px)";s.transitionDuration='0s';}}var platformComponents={Transition:Transition,TransitionGroup:TransitionGroup};/*  */// install platform specific utils
Vue.config.mustUseProp=mustUseProp;Vue.config.isReservedTag=isReservedTag;Vue.config.isReservedAttr=isReservedAttr;Vue.config.getTagNamespace=getTagNamespace;Vue.config.isUnknownElement=isUnknownElement;// install platform runtime directives & components
extend(Vue.options.directives,platformDirectives);extend(Vue.options.components,platformComponents);// install platform patch function
Vue.prototype.__patch__=inBrowser?patch:noop;// public mount method
Vue.prototype.$mount=function(el,hydrating){el=el&&inBrowser?query(el):undefined;return mountComponent(this,el,hydrating);};// devtools global hook
/* istanbul ignore next */if(inBrowser){setTimeout(function(){if(config.devtools){if(devtools){devtools.emit('init',Vue);}else if(process.env.NODE_ENV!=='production'&&process.env.NODE_ENV!=='test'&&isChrome){console[console.info?'info':'log']('Download the Vue Devtools extension for a better development experience:\n'+'https://github.com/vuejs/vue-devtools');}}if(process.env.NODE_ENV!=='production'&&process.env.NODE_ENV!=='test'&&config.productionTip!==false&&typeof console!=='undefined'){console[console.info?'info':'log']("You are running Vue in development mode.\n"+"Make sure to turn on production mode when deploying for production.\n"+"See more tips at https://vuejs.org/guide/deployment.html");}},0);}/*  */var defaultTagRE=/\{\{((?:.|\r?\n)+?)\}\}/g;var regexEscapeRE=/[-.*+?^${}()|[\]\/\\]/g;var buildRegex=cached(function(delimiters){var open=delimiters[0].replace(regexEscapeRE,'\\$&');var close=delimiters[1].replace(regexEscapeRE,'\\$&');return new RegExp(open+'((?:.|\\n)+?)'+close,'g');});function parseText(text,delimiters){var tagRE=delimiters?buildRegex(delimiters):defaultTagRE;if(!tagRE.test(text)){return;}var tokens=[];var rawTokens=[];var lastIndex=tagRE.lastIndex=0;var match,index,tokenValue;while(match=tagRE.exec(text)){index=match.index;// push text token
if(index>lastIndex){rawTokens.push(tokenValue=text.slice(lastIndex,index));tokens.push(JSON.stringify(tokenValue));}// tag token
var exp=parseFilters(match[1].trim());tokens.push("_s("+exp+")");rawTokens.push({'@binding':exp});lastIndex=index+match[0].length;}if(lastIndex<text.length){rawTokens.push(tokenValue=text.slice(lastIndex));tokens.push(JSON.stringify(tokenValue));}return{expression:tokens.join('+'),tokens:rawTokens};}/*  */function transformNode(el,options){var warn=options.warn||baseWarn;var staticClass=getAndRemoveAttr(el,'class');if(process.env.NODE_ENV!=='production'&&staticClass){var res=parseText(staticClass,options.delimiters);if(res){warn("class=\""+staticClass+"\": "+'Interpolation inside attributes has been removed. '+'Use v-bind or the colon shorthand instead. For example, '+'instead of <div class="{{ val }}">, use <div :class="val">.');}}if(staticClass){el.staticClass=JSON.stringify(staticClass);}var classBinding=getBindingAttr(el,'class',false/* getStatic */);if(classBinding){el.classBinding=classBinding;}}function genData(el){var data='';if(el.staticClass){data+="staticClass:"+el.staticClass+",";}if(el.classBinding){data+="class:"+el.classBinding+",";}return data;}var klass$1={staticKeys:['staticClass'],transformNode:transformNode,genData:genData};/*  */function transformNode$1(el,options){var warn=options.warn||baseWarn;var staticStyle=getAndRemoveAttr(el,'style');if(staticStyle){/* istanbul ignore if */if(process.env.NODE_ENV!=='production'){var res=parseText(staticStyle,options.delimiters);if(res){warn("style=\""+staticStyle+"\": "+'Interpolation inside attributes has been removed. '+'Use v-bind or the colon shorthand instead. For example, '+'instead of <div style="{{ val }}">, use <div :style="val">.');}}el.staticStyle=JSON.stringify(parseStyleText(staticStyle));}var styleBinding=getBindingAttr(el,'style',false/* getStatic */);if(styleBinding){el.styleBinding=styleBinding;}}function genData$1(el){var data='';if(el.staticStyle){data+="staticStyle:"+el.staticStyle+",";}if(el.styleBinding){data+="style:("+el.styleBinding+"),";}return data;}var style$1={staticKeys:['staticStyle'],transformNode:transformNode$1,genData:genData$1};/*  */var decoder;var he={decode:function decode(html){decoder=decoder||document.createElement('div');decoder.innerHTML=html;return decoder.textContent;}};/*  */var isUnaryTag=makeMap('area,base,br,col,embed,frame,hr,img,input,isindex,keygen,'+'link,meta,param,source,track,wbr');// Elements that you can, intentionally, leave open
// (and which close themselves)
var canBeLeftOpenTag=makeMap('colgroup,dd,dt,li,options,p,td,tfoot,th,thead,tr,source');// HTML5 tags https://html.spec.whatwg.org/multipage/indices.html#elements-3
// Phrasing Content https://html.spec.whatwg.org/multipage/dom.html#phrasing-content
var isNonPhrasingTag=makeMap('address,article,aside,base,blockquote,body,caption,col,colgroup,dd,'+'details,dialog,div,dl,dt,fieldset,figcaption,figure,footer,form,'+'h1,h2,h3,h4,h5,h6,head,header,hgroup,hr,html,legend,li,menuitem,meta,'+'optgroup,option,param,rp,rt,source,style,summary,tbody,td,tfoot,th,thead,'+'title,tr,track');/**
 * Not type-checking this file because it's mostly vendor code.
 */// Regular Expressions for parsing tags and attributes
var attribute=/^\s*([^\s"'<>\/=]+)(?:\s*(=)\s*(?:"([^"]*)"+|'([^']*)'+|([^\s"'=<>`]+)))?/;// could use https://www.w3.org/TR/1999/REC-xml-names-19990114/#NT-QName
// but for Vue templates we can enforce a simple charset
var ncname='[a-zA-Z_][\\w\\-\\.]*';var qnameCapture="((?:"+ncname+"\\:)?"+ncname+")";var startTagOpen=new RegExp("^<"+qnameCapture);var startTagClose=/^\s*(\/?)>/;var endTag=new RegExp("^<\\/"+qnameCapture+"[^>]*>");var doctype=/^<!DOCTYPE [^>]+>/i;// #7298: escape - to avoid being pased as HTML comment when inlined in page
var comment=/^<!\--/;var conditionalComment=/^<!\[/;// Special Elements (can contain anything)
var isPlainTextElement=makeMap('script,style,textarea',true);var reCache={};var decodingMap={'&lt;':'<','&gt;':'>','&quot;':'"','&amp;':'&','&#10;':'\n','&#9;':'\t'};var encodedAttr=/&(?:lt|gt|quot|amp);/g;var encodedAttrWithNewLines=/&(?:lt|gt|quot|amp|#10|#9);/g;// #5992
var isIgnoreNewlineTag=makeMap('pre,textarea',true);var shouldIgnoreFirstNewline=function shouldIgnoreFirstNewline(tag,html){return tag&&isIgnoreNewlineTag(tag)&&html[0]==='\n';};function decodeAttr(value,shouldDecodeNewlines){var re=shouldDecodeNewlines?encodedAttrWithNewLines:encodedAttr;return value.replace(re,function(match){return decodingMap[match];});}function parseHTML(html,options){var stack=[];var expectHTML=options.expectHTML;var isUnaryTag$$1=options.isUnaryTag||no;var canBeLeftOpenTag$$1=options.canBeLeftOpenTag||no;var index=0;var last,lastTag;while(html){last=html;// Make sure we're not in a plaintext content element like script/style
if(!lastTag||!isPlainTextElement(lastTag)){var textEnd=html.indexOf('<');if(textEnd===0){// Comment:
if(comment.test(html)){var commentEnd=html.indexOf('-->');if(commentEnd>=0){if(options.shouldKeepComment){options.comment(html.substring(4,commentEnd));}advance(commentEnd+3);continue;}}// http://en.wikipedia.org/wiki/Conditional_comment#Downlevel-revealed_conditional_comment
if(conditionalComment.test(html)){var conditionalEnd=html.indexOf(']>');if(conditionalEnd>=0){advance(conditionalEnd+2);continue;}}// Doctype:
var doctypeMatch=html.match(doctype);if(doctypeMatch){advance(doctypeMatch[0].length);continue;}// End tag:
var endTagMatch=html.match(endTag);if(endTagMatch){var curIndex=index;advance(endTagMatch[0].length);parseEndTag(endTagMatch[1],curIndex,index);continue;}// Start tag:
var startTagMatch=parseStartTag();if(startTagMatch){handleStartTag(startTagMatch);if(shouldIgnoreFirstNewline(startTagMatch.tagName,html)){advance(1);}continue;}}var text=void 0,rest=void 0,next=void 0;if(textEnd>=0){rest=html.slice(textEnd);while(!endTag.test(rest)&&!startTagOpen.test(rest)&&!comment.test(rest)&&!conditionalComment.test(rest)){// < in plain text, be forgiving and treat it as text
next=rest.indexOf('<',1);if(next<0){break;}textEnd+=next;rest=html.slice(textEnd);}text=html.substring(0,textEnd);advance(textEnd);}if(textEnd<0){text=html;html='';}if(options.chars&&text){options.chars(text);}}else{var endTagLength=0;var stackedTag=lastTag.toLowerCase();var reStackedTag=reCache[stackedTag]||(reCache[stackedTag]=new RegExp('([\\s\\S]*?)(</'+stackedTag+'[^>]*>)','i'));var rest$1=html.replace(reStackedTag,function(all,text,endTag){endTagLength=endTag.length;if(!isPlainTextElement(stackedTag)&&stackedTag!=='noscript'){text=text.replace(/<!\--([\s\S]*?)-->/g,'$1')// #7298
.replace(/<!\[CDATA\[([\s\S]*?)]]>/g,'$1');}if(shouldIgnoreFirstNewline(stackedTag,text)){text=text.slice(1);}if(options.chars){options.chars(text);}return'';});index+=html.length-rest$1.length;html=rest$1;parseEndTag(stackedTag,index-endTagLength,index);}if(html===last){options.chars&&options.chars(html);if(process.env.NODE_ENV!=='production'&&!stack.length&&options.warn){options.warn("Mal-formatted tag at end of template: \""+html+"\"");}break;}}// Clean up any remaining tags
parseEndTag();function advance(n){index+=n;html=html.substring(n);}function parseStartTag(){var start=html.match(startTagOpen);if(start){var match={tagName:start[1],attrs:[],start:index};advance(start[0].length);var end,attr;while(!(end=html.match(startTagClose))&&(attr=html.match(attribute))){advance(attr[0].length);match.attrs.push(attr);}if(end){match.unarySlash=end[1];advance(end[0].length);match.end=index;return match;}}}function handleStartTag(match){var tagName=match.tagName;var unarySlash=match.unarySlash;if(expectHTML){if(lastTag==='p'&&isNonPhrasingTag(tagName)){parseEndTag(lastTag);}if(canBeLeftOpenTag$$1(tagName)&&lastTag===tagName){parseEndTag(tagName);}}var unary=isUnaryTag$$1(tagName)||!!unarySlash;var l=match.attrs.length;var attrs=new Array(l);for(var i=0;i<l;i++){var args=match.attrs[i];var value=args[3]||args[4]||args[5]||'';var shouldDecodeNewlines=tagName==='a'&&args[1]==='href'?options.shouldDecodeNewlinesForHref:options.shouldDecodeNewlines;attrs[i]={name:args[1],value:decodeAttr(value,shouldDecodeNewlines)};}if(!unary){stack.push({tag:tagName,lowerCasedTag:tagName.toLowerCase(),attrs:attrs});lastTag=tagName;}if(options.start){options.start(tagName,attrs,unary,match.start,match.end);}}function parseEndTag(tagName,start,end){var pos,lowerCasedTagName;if(start==null){start=index;}if(end==null){end=index;}// Find the closest opened tag of the same type
if(tagName){lowerCasedTagName=tagName.toLowerCase();for(pos=stack.length-1;pos>=0;pos--){if(stack[pos].lowerCasedTag===lowerCasedTagName){break;}}}else{// If no tag name is provided, clean shop
pos=0;}if(pos>=0){// Close all the open elements, up the stack
for(var i=stack.length-1;i>=pos;i--){if(process.env.NODE_ENV!=='production'&&(i>pos||!tagName)&&options.warn){options.warn("tag <"+stack[i].tag+"> has no matching end tag.");}if(options.end){options.end(stack[i].tag,start,end);}}// Remove the open elements from the stack
stack.length=pos;lastTag=pos&&stack[pos-1].tag;}else if(lowerCasedTagName==='br'){if(options.start){options.start(tagName,[],true,start,end);}}else if(lowerCasedTagName==='p'){if(options.start){options.start(tagName,[],false,start,end);}if(options.end){options.end(tagName,start,end);}}}}/*  */var onRE=/^@|^v-on:/;var dirRE=/^v-|^@|^:/;var forAliasRE=/([\s\S]*?)\s+(?:in|of)\s+([\s\S]*)/;var forIteratorRE=/,([^,\}\]]*)(?:,([^,\}\]]*))?$/;var stripParensRE=/^\(|\)$/g;var argRE=/:(.*)$/;var bindRE=/^:|^v-bind:/;var modifierRE=/\.[^.]+/g;var decodeHTMLCached=cached(he.decode);// configurable state
var warn$2;var delimiters;var transforms;var preTransforms;var postTransforms;var platformIsPreTag;var platformMustUseProp;var platformGetTagNamespace;function createASTElement(tag,attrs,parent){return{type:1,tag:tag,attrsList:attrs,attrsMap:makeAttrsMap(attrs),parent:parent,children:[]};}/**
 * Convert HTML string to AST.
 */function parse(template,options){warn$2=options.warn||baseWarn;platformIsPreTag=options.isPreTag||no;platformMustUseProp=options.mustUseProp||no;platformGetTagNamespace=options.getTagNamespace||no;transforms=pluckModuleFunction(options.modules,'transformNode');preTransforms=pluckModuleFunction(options.modules,'preTransformNode');postTransforms=pluckModuleFunction(options.modules,'postTransformNode');delimiters=options.delimiters;var stack=[];var preserveWhitespace=options.preserveWhitespace!==false;var root;var currentParent;var inVPre=false;var inPre=false;var warned=false;function warnOnce(msg){if(!warned){warned=true;warn$2(msg);}}function closeElement(element){// check pre state
if(element.pre){inVPre=false;}if(platformIsPreTag(element.tag)){inPre=false;}// apply post-transforms
for(var i=0;i<postTransforms.length;i++){postTransforms[i](element,options);}}parseHTML(template,{warn:warn$2,expectHTML:options.expectHTML,isUnaryTag:options.isUnaryTag,canBeLeftOpenTag:options.canBeLeftOpenTag,shouldDecodeNewlines:options.shouldDecodeNewlines,shouldDecodeNewlinesForHref:options.shouldDecodeNewlinesForHref,shouldKeepComment:options.comments,start:function start(tag,attrs,unary){// check namespace.
// inherit parent ns if there is one
var ns=currentParent&&currentParent.ns||platformGetTagNamespace(tag);// handle IE svg bug
/* istanbul ignore if */if(isIE&&ns==='svg'){attrs=guardIESVGBug(attrs);}var element=createASTElement(tag,attrs,currentParent);if(ns){element.ns=ns;}if(isForbiddenTag(element)&&!isServerRendering()){element.forbidden=true;process.env.NODE_ENV!=='production'&&warn$2('Templates should only be responsible for mapping the state to the '+'UI. Avoid placing tags with side-effects in your templates, such as '+"<"+tag+">"+', as they will not be parsed.');}// apply pre-transforms
for(var i=0;i<preTransforms.length;i++){element=preTransforms[i](element,options)||element;}if(!inVPre){processPre(element);if(element.pre){inVPre=true;}}if(platformIsPreTag(element.tag)){inPre=true;}if(inVPre){processRawAttrs(element);}else if(!element.processed){// structural directives
processFor(element);processIf(element);processOnce(element);// element-scope stuff
processElement(element,options);}function checkRootConstraints(el){if(process.env.NODE_ENV!=='production'){if(el.tag==='slot'||el.tag==='template'){warnOnce("Cannot use <"+el.tag+"> as component root element because it may "+'contain multiple nodes.');}if(el.attrsMap.hasOwnProperty('v-for')){warnOnce('Cannot use v-for on stateful component root element because '+'it renders multiple elements.');}}}// tree management
if(!root){root=element;checkRootConstraints(root);}else if(!stack.length){// allow root elements with v-if, v-else-if and v-else
if(root.if&&(element.elseif||element.else)){checkRootConstraints(element);addIfCondition(root,{exp:element.elseif,block:element});}else if(process.env.NODE_ENV!=='production'){warnOnce("Component template should contain exactly one root element. "+"If you are using v-if on multiple elements, "+"use v-else-if to chain them instead.");}}if(currentParent&&!element.forbidden){if(element.elseif||element.else){processIfConditions(element,currentParent);}else if(element.slotScope){// scoped slot
currentParent.plain=false;var name=element.slotTarget||'"default"';(currentParent.scopedSlots||(currentParent.scopedSlots={}))[name]=element;}else{currentParent.children.push(element);element.parent=currentParent;}}if(!unary){currentParent=element;stack.push(element);}else{closeElement(element);}},end:function end(){// remove trailing whitespace
var element=stack[stack.length-1];var lastNode=element.children[element.children.length-1];if(lastNode&&lastNode.type===3&&lastNode.text===' '&&!inPre){element.children.pop();}// pop stack
stack.length-=1;currentParent=stack[stack.length-1];closeElement(element);},chars:function chars(text){if(!currentParent){if(process.env.NODE_ENV!=='production'){if(text===template){warnOnce('Component template requires a root element, rather than just text.');}else if(text=text.trim()){warnOnce("text \""+text+"\" outside root element will be ignored.");}}return;}// IE textarea placeholder bug
/* istanbul ignore if */if(isIE&&currentParent.tag==='textarea'&&currentParent.attrsMap.placeholder===text){return;}var children=currentParent.children;text=inPre||text.trim()?isTextTag(currentParent)?text:decodeHTMLCached(text)// only preserve whitespace if its not right after a starting tag
:preserveWhitespace&&children.length?' ':'';if(text){var res;if(!inVPre&&text!==' '&&(res=parseText(text,delimiters))){children.push({type:2,expression:res.expression,tokens:res.tokens,text:text});}else if(text!==' '||!children.length||children[children.length-1].text!==' '){children.push({type:3,text:text});}}},comment:function comment(text){currentParent.children.push({type:3,text:text,isComment:true});}});return root;}function processPre(el){if(getAndRemoveAttr(el,'v-pre')!=null){el.pre=true;}}function processRawAttrs(el){var l=el.attrsList.length;if(l){var attrs=el.attrs=new Array(l);for(var i=0;i<l;i++){attrs[i]={name:el.attrsList[i].name,value:JSON.stringify(el.attrsList[i].value)};}}else if(!el.pre){// non root node in pre blocks with no attributes
el.plain=true;}}function processElement(element,options){processKey(element);// determine whether this is a plain element after
// removing structural attributes
element.plain=!element.key&&!element.attrsList.length;processRef(element);processSlot(element);processComponent(element);for(var i=0;i<transforms.length;i++){element=transforms[i](element,options)||element;}processAttrs(element);}function processKey(el){var exp=getBindingAttr(el,'key');if(exp){if(process.env.NODE_ENV!=='production'){if(el.tag==='template'){warn$2("<template> cannot be keyed. Place the key on real elements instead.");}if(el.for){var iterator=el.iterator2||el.iterator1;var parent=el.parent;if(iterator&&iterator===exp&&parent&&parent.tag==='transition-group'){warn$2("Do not use v-for index as key on <transition-group> children, "+"this is the same as not using keys.");}}}el.key=exp;}}function processRef(el){var ref=getBindingAttr(el,'ref');if(ref){el.ref=ref;el.refInFor=checkInFor(el);}}function processFor(el){var exp;if(exp=getAndRemoveAttr(el,'v-for')){var res=parseFor(exp);if(res){extend(el,res);}else if(process.env.NODE_ENV!=='production'){warn$2("Invalid v-for expression: "+exp);}}}function parseFor(exp){var inMatch=exp.match(forAliasRE);if(!inMatch){return;}var res={};res.for=inMatch[2].trim();var alias=inMatch[1].trim().replace(stripParensRE,'');var iteratorMatch=alias.match(forIteratorRE);if(iteratorMatch){res.alias=alias.replace(forIteratorRE,'').trim();res.iterator1=iteratorMatch[1].trim();if(iteratorMatch[2]){res.iterator2=iteratorMatch[2].trim();}}else{res.alias=alias;}return res;}function processIf(el){var exp=getAndRemoveAttr(el,'v-if');if(exp){el.if=exp;addIfCondition(el,{exp:exp,block:el});}else{if(getAndRemoveAttr(el,'v-else')!=null){el.else=true;}var elseif=getAndRemoveAttr(el,'v-else-if');if(elseif){el.elseif=elseif;}}}function processIfConditions(el,parent){var prev=findPrevElement(parent.children);if(prev&&prev.if){addIfCondition(prev,{exp:el.elseif,block:el});}else if(process.env.NODE_ENV!=='production'){warn$2("v-"+(el.elseif?'else-if="'+el.elseif+'"':'else')+" "+"used on element <"+el.tag+"> without corresponding v-if.");}}function findPrevElement(children){var i=children.length;while(i--){if(children[i].type===1){return children[i];}else{if(process.env.NODE_ENV!=='production'&&children[i].text!==' '){warn$2("text \""+children[i].text.trim()+"\" between v-if and v-else(-if) "+"will be ignored.");}children.pop();}}}function addIfCondition(el,condition){if(!el.ifConditions){el.ifConditions=[];}el.ifConditions.push(condition);}function processOnce(el){var once$$1=getAndRemoveAttr(el,'v-once');if(once$$1!=null){el.once=true;}}function processSlot(el){if(el.tag==='slot'){el.slotName=getBindingAttr(el,'name');if(process.env.NODE_ENV!=='production'&&el.key){warn$2("`key` does not work on <slot> because slots are abstract outlets "+"and can possibly expand into multiple elements. "+"Use the key on a wrapping element instead.");}}else{var slotScope;if(el.tag==='template'){slotScope=getAndRemoveAttr(el,'scope');/* istanbul ignore if */if(process.env.NODE_ENV!=='production'&&slotScope){warn$2("the \"scope\" attribute for scoped slots have been deprecated and "+"replaced by \"slot-scope\" since 2.5. The new \"slot-scope\" attribute "+"can also be used on plain elements in addition to <template> to "+"denote scoped slots.",true);}el.slotScope=slotScope||getAndRemoveAttr(el,'slot-scope');}else if(slotScope=getAndRemoveAttr(el,'slot-scope')){/* istanbul ignore if */if(process.env.NODE_ENV!=='production'&&el.attrsMap['v-for']){warn$2("Ambiguous combined usage of slot-scope and v-for on <"+el.tag+"> "+"(v-for takes higher priority). Use a wrapper <template> for the "+"scoped slot to make it clearer.",true);}el.slotScope=slotScope;}var slotTarget=getBindingAttr(el,'slot');if(slotTarget){el.slotTarget=slotTarget==='""'?'"default"':slotTarget;// preserve slot as an attribute for native shadow DOM compat
// only for non-scoped slots.
if(el.tag!=='template'&&!el.slotScope){addAttr(el,'slot',slotTarget);}}}}function processComponent(el){var binding;if(binding=getBindingAttr(el,'is')){el.component=binding;}if(getAndRemoveAttr(el,'inline-template')!=null){el.inlineTemplate=true;}}function processAttrs(el){var list=el.attrsList;var i,l,name,rawName,value,modifiers,isProp;for(i=0,l=list.length;i<l;i++){name=rawName=list[i].name;value=list[i].value;if(dirRE.test(name)){// mark element as dynamic
el.hasBindings=true;// modifiers
modifiers=parseModifiers(name);if(modifiers){name=name.replace(modifierRE,'');}if(bindRE.test(name)){// v-bind
name=name.replace(bindRE,'');value=parseFilters(value);isProp=false;if(process.env.NODE_ENV!=='production'&&value.trim().length===0){warn$2("The value for a v-bind expression cannot be empty. Found in \"v-bind:"+name+"\"");}if(modifiers){if(modifiers.prop){isProp=true;name=camelize(name);if(name==='innerHtml'){name='innerHTML';}}if(modifiers.camel){name=camelize(name);}if(modifiers.sync){addHandler(el,"update:"+camelize(name),genAssignmentCode(value,"$event"));}}if(isProp||!el.component&&platformMustUseProp(el.tag,el.attrsMap.type,name)){addProp(el,name,value);}else{addAttr(el,name,value);}}else if(onRE.test(name)){// v-on
name=name.replace(onRE,'');addHandler(el,name,value,modifiers,false,warn$2);}else{// normal directives
name=name.replace(dirRE,'');// parse arg
var argMatch=name.match(argRE);var arg=argMatch&&argMatch[1];if(arg){name=name.slice(0,-(arg.length+1));}addDirective(el,name,rawName,value,arg,modifiers);if(process.env.NODE_ENV!=='production'&&name==='model'){checkForAliasModel(el,value);}}}else{// literal attribute
if(process.env.NODE_ENV!=='production'){var res=parseText(value,delimiters);if(res){warn$2(name+"=\""+value+"\": "+'Interpolation inside attributes has been removed. '+'Use v-bind or the colon shorthand instead. For example, '+'instead of <div id="{{ val }}">, use <div :id="val">.');}}addAttr(el,name,JSON.stringify(value));// #6887 firefox doesn't update muted state if set via attribute
// even immediately after element creation
if(!el.component&&name==='muted'&&platformMustUseProp(el.tag,el.attrsMap.type,name)){addProp(el,name,'true');}}}}function checkInFor(el){var parent=el;while(parent){if(parent.for!==undefined){return true;}parent=parent.parent;}return false;}function parseModifiers(name){var match=name.match(modifierRE);if(match){var ret={};match.forEach(function(m){ret[m.slice(1)]=true;});return ret;}}function makeAttrsMap(attrs){var map={};for(var i=0,l=attrs.length;i<l;i++){if(process.env.NODE_ENV!=='production'&&map[attrs[i].name]&&!isIE&&!isEdge){warn$2('duplicate attribute: '+attrs[i].name);}map[attrs[i].name]=attrs[i].value;}return map;}// for script (e.g. type="x/template") or style, do not decode content
function isTextTag(el){return el.tag==='script'||el.tag==='style';}function isForbiddenTag(el){return el.tag==='style'||el.tag==='script'&&(!el.attrsMap.type||el.attrsMap.type==='text/javascript');}var ieNSBug=/^xmlns:NS\d+/;var ieNSPrefix=/^NS\d+:/;/* istanbul ignore next */function guardIESVGBug(attrs){var res=[];for(var i=0;i<attrs.length;i++){var attr=attrs[i];if(!ieNSBug.test(attr.name)){attr.name=attr.name.replace(ieNSPrefix,'');res.push(attr);}}return res;}function checkForAliasModel(el,value){var _el=el;while(_el){if(_el.for&&_el.alias===value){warn$2("<"+el.tag+" v-model=\""+value+"\">: "+"You are binding v-model directly to a v-for iteration alias. "+"This will not be able to modify the v-for source array because "+"writing to the alias is like modifying a function local variable. "+"Consider using an array of objects and use v-model on an object property instead.");}_el=_el.parent;}}/*  */function preTransformNode(el,options){if(el.tag==='input'){var map=el.attrsMap;if(!map['v-model']){return;}var typeBinding;if(map[':type']||map['v-bind:type']){typeBinding=getBindingAttr(el,'type');}if(!map.type&&!typeBinding&&map['v-bind']){typeBinding="("+map['v-bind']+").type";}if(typeBinding){var ifCondition=getAndRemoveAttr(el,'v-if',true);var ifConditionExtra=ifCondition?"&&("+ifCondition+")":"";var hasElse=getAndRemoveAttr(el,'v-else',true)!=null;var elseIfCondition=getAndRemoveAttr(el,'v-else-if',true);// 1. checkbox
var branch0=cloneASTElement(el);// process for on the main node
processFor(branch0);addRawAttr(branch0,'type','checkbox');processElement(branch0,options);branch0.processed=true;// prevent it from double-processed
branch0.if="("+typeBinding+")==='checkbox'"+ifConditionExtra;addIfCondition(branch0,{exp:branch0.if,block:branch0});// 2. add radio else-if condition
var branch1=cloneASTElement(el);getAndRemoveAttr(branch1,'v-for',true);addRawAttr(branch1,'type','radio');processElement(branch1,options);addIfCondition(branch0,{exp:"("+typeBinding+")==='radio'"+ifConditionExtra,block:branch1});// 3. other
var branch2=cloneASTElement(el);getAndRemoveAttr(branch2,'v-for',true);addRawAttr(branch2,':type',typeBinding);processElement(branch2,options);addIfCondition(branch0,{exp:ifCondition,block:branch2});if(hasElse){branch0.else=true;}else if(elseIfCondition){branch0.elseif=elseIfCondition;}return branch0;}}}function cloneASTElement(el){return createASTElement(el.tag,el.attrsList.slice(),el.parent);}var model$1={preTransformNode:preTransformNode};var modules$1=[klass$1,style$1,model$1];/*  */function text(el,dir){if(dir.value){addProp(el,'textContent',"_s("+dir.value+")");}}/*  */function html(el,dir){if(dir.value){addProp(el,'innerHTML',"_s("+dir.value+")");}}var directives$1={model:model,text:text,html:html};/*  */var baseOptions={expectHTML:true,modules:modules$1,directives:directives$1,isPreTag:isPreTag,isUnaryTag:isUnaryTag,mustUseProp:mustUseProp,canBeLeftOpenTag:canBeLeftOpenTag,isReservedTag:isReservedTag,getTagNamespace:getTagNamespace,staticKeys:genStaticKeys(modules$1)};/*  */var isStaticKey;var isPlatformReservedTag;var genStaticKeysCached=cached(genStaticKeys$1);/**
 * Goal of the optimizer: walk the generated template AST tree
 * and detect sub-trees that are purely static, i.e. parts of
 * the DOM that never needs to change.
 *
 * Once we detect these sub-trees, we can:
 *
 * 1. Hoist them into constants, so that we no longer need to
 *    create fresh nodes for them on each re-render;
 * 2. Completely skip them in the patching process.
 */function optimize(root,options){if(!root){return;}isStaticKey=genStaticKeysCached(options.staticKeys||'');isPlatformReservedTag=options.isReservedTag||no;// first pass: mark all non-static nodes.
markStatic$1(root);// second pass: mark static roots.
markStaticRoots(root,false);}function genStaticKeys$1(keys){return makeMap('type,tag,attrsList,attrsMap,plain,parent,children,attrs'+(keys?','+keys:''));}function markStatic$1(node){node.static=isStatic(node);if(node.type===1){// do not make component slot content static. this avoids
// 1. components not able to mutate slot nodes
// 2. static slot content fails for hot-reloading
if(!isPlatformReservedTag(node.tag)&&node.tag!=='slot'&&node.attrsMap['inline-template']==null){return;}for(var i=0,l=node.children.length;i<l;i++){var child=node.children[i];markStatic$1(child);if(!child.static){node.static=false;}}if(node.ifConditions){for(var i$1=1,l$1=node.ifConditions.length;i$1<l$1;i$1++){var block=node.ifConditions[i$1].block;markStatic$1(block);if(!block.static){node.static=false;}}}}}function markStaticRoots(node,isInFor){if(node.type===1){if(node.static||node.once){node.staticInFor=isInFor;}// For a node to qualify as a static root, it should have children that
// are not just static text. Otherwise the cost of hoisting out will
// outweigh the benefits and it's better off to just always render it fresh.
if(node.static&&node.children.length&&!(node.children.length===1&&node.children[0].type===3)){node.staticRoot=true;return;}else{node.staticRoot=false;}if(node.children){for(var i=0,l=node.children.length;i<l;i++){markStaticRoots(node.children[i],isInFor||!!node.for);}}if(node.ifConditions){for(var i$1=1,l$1=node.ifConditions.length;i$1<l$1;i$1++){markStaticRoots(node.ifConditions[i$1].block,isInFor);}}}}function isStatic(node){if(node.type===2){// expression
return false;}if(node.type===3){// text
return true;}return!!(node.pre||!node.hasBindings&&// no dynamic bindings
!node.if&&!node.for&&// not v-if or v-for or v-else
!isBuiltInTag(node.tag)&&// not a built-in
isPlatformReservedTag(node.tag)&&// not a component
!isDirectChildOfTemplateFor(node)&&Object.keys(node).every(isStaticKey));}function isDirectChildOfTemplateFor(node){while(node.parent){node=node.parent;if(node.tag!=='template'){return false;}if(node.for){return true;}}return false;}/*  */var fnExpRE=/^([\w$_]+|\([^)]*?\))\s*=>|^function\s*\(/;var simplePathRE=/^[A-Za-z_$][\w$]*(?:\.[A-Za-z_$][\w$]*|\['[^']*?']|\["[^"]*?"]|\[\d+]|\[[A-Za-z_$][\w$]*])*$/;// KeyboardEvent.keyCode aliases
var keyCodes={esc:27,tab:9,enter:13,space:32,up:38,left:37,right:39,down:40,'delete':[8,46]};// KeyboardEvent.key aliases
var keyNames={// #7880: IE11 and Edge use `Esc` for Escape key name.
esc:['Esc','Escape'],tab:'Tab',enter:'Enter',// #9112: IE11 uses `Spacebar` for Space key name.
space:[' ','Spacebar'],// #7806: IE11 uses key names without `Arrow` prefix for arrow keys.
up:['Up','ArrowUp'],left:['Left','ArrowLeft'],right:['Right','ArrowRight'],down:['Down','ArrowDown'],// #9112: IE11 uses `Del` for Delete key name.
'delete':['Backspace','Delete','Del']};// #4868: modifiers that prevent the execution of the listener
// need to explicitly return null so that we can determine whether to remove
// the listener for .once
var genGuard=function genGuard(condition){return"if("+condition+")return null;";};var modifierCode={stop:'$event.stopPropagation();',prevent:'$event.preventDefault();',self:genGuard("$event.target !== $event.currentTarget"),ctrl:genGuard("!$event.ctrlKey"),shift:genGuard("!$event.shiftKey"),alt:genGuard("!$event.altKey"),meta:genGuard("!$event.metaKey"),left:genGuard("'button' in $event && $event.button !== 0"),middle:genGuard("'button' in $event && $event.button !== 1"),right:genGuard("'button' in $event && $event.button !== 2")};function genHandlers(events,isNative){var res=isNative?'nativeOn:{':'on:{';for(var name in events){res+="\""+name+"\":"+genHandler(name,events[name])+",";}return res.slice(0,-1)+'}';}function genHandler(name,handler){if(!handler){return'function(){}';}if(Array.isArray(handler)){return"["+handler.map(function(handler){return genHandler(name,handler);}).join(',')+"]";}var isMethodPath=simplePathRE.test(handler.value);var isFunctionExpression=fnExpRE.test(handler.value);if(!handler.modifiers){if(isMethodPath||isFunctionExpression){return handler.value;}return"function($event){"+handler.value+"}";// inline statement
}else{var code='';var genModifierCode='';var keys=[];for(var key in handler.modifiers){if(modifierCode[key]){genModifierCode+=modifierCode[key];// left/right
if(keyCodes[key]){keys.push(key);}}else if(key==='exact'){var modifiers=handler.modifiers;genModifierCode+=genGuard(['ctrl','shift','alt','meta'].filter(function(keyModifier){return!modifiers[keyModifier];}).map(function(keyModifier){return"$event."+keyModifier+"Key";}).join('||'));}else{keys.push(key);}}if(keys.length){code+=genKeyFilter(keys);}// Make sure modifiers like prevent and stop get executed after key filtering
if(genModifierCode){code+=genModifierCode;}var handlerCode=isMethodPath?"return "+handler.value+"($event)":isFunctionExpression?"return ("+handler.value+")($event)":handler.value;return"function($event){"+code+handlerCode+"}";}}function genKeyFilter(keys){return"if(!('button' in $event)&&"+keys.map(genFilterCode).join('&&')+")return null;";}function genFilterCode(key){var keyVal=parseInt(key,10);if(keyVal){return"$event.keyCode!=="+keyVal;}var keyCode=keyCodes[key];var keyName=keyNames[key];return"_k($event.keyCode,"+JSON.stringify(key)+","+JSON.stringify(keyCode)+","+"$event.key,"+""+JSON.stringify(keyName)+")";}/*  */function on(el,dir){if(process.env.NODE_ENV!=='production'&&dir.modifiers){warn("v-on without argument does not support modifiers.");}el.wrapListeners=function(code){return"_g("+code+","+dir.value+")";};}/*  */function bind$1(el,dir){el.wrapData=function(code){return"_b("+code+",'"+el.tag+"',"+dir.value+","+(dir.modifiers&&dir.modifiers.prop?'true':'false')+(dir.modifiers&&dir.modifiers.sync?',true':'')+")";};}/*  */var baseDirectives={on:on,bind:bind$1,cloak:noop};/*  */var CodegenState=function CodegenState(options){this.options=options;this.warn=options.warn||baseWarn;this.transforms=pluckModuleFunction(options.modules,'transformCode');this.dataGenFns=pluckModuleFunction(options.modules,'genData');this.directives=extend(extend({},baseDirectives),options.directives);var isReservedTag=options.isReservedTag||no;this.maybeComponent=function(el){return!(isReservedTag(el.tag)&&!el.component);};this.onceId=0;this.staticRenderFns=[];this.pre=false;};function generate(ast,options){var state=new CodegenState(options);var code=ast?genElement(ast,state):'_c("div")';return{render:"with(this){return "+code+"}",staticRenderFns:state.staticRenderFns};}function genElement(el,state){if(el.parent){el.pre=el.pre||el.parent.pre;}if(el.staticRoot&&!el.staticProcessed){return genStatic(el,state);}else if(el.once&&!el.onceProcessed){return genOnce(el,state);}else if(el.for&&!el.forProcessed){return genFor(el,state);}else if(el.if&&!el.ifProcessed){return genIf(el,state);}else if(el.tag==='template'&&!el.slotTarget&&!state.pre){return genChildren(el,state)||'void 0';}else if(el.tag==='slot'){return genSlot(el,state);}else{// component or element
var code;if(el.component){code=genComponent(el.component,el,state);}else{var data;if(!el.plain||el.pre&&state.maybeComponent(el)){data=genData$2(el,state);}var children=el.inlineTemplate?null:genChildren(el,state,true);code="_c('"+el.tag+"'"+(data?","+data:'')+(children?","+children:'')+")";}// module transforms
for(var i=0;i<state.transforms.length;i++){code=state.transforms[i](el,code);}return code;}}// hoist static sub-trees out
function genStatic(el,state){el.staticProcessed=true;// Some elements (templates) need to behave differently inside of a v-pre
// node.  All pre nodes are static roots, so we can use this as a location to
// wrap a state change and reset it upon exiting the pre node.
var originalPreState=state.pre;if(el.pre){state.pre=el.pre;}state.staticRenderFns.push("with(this){return "+genElement(el,state)+"}");state.pre=originalPreState;return"_m("+(state.staticRenderFns.length-1)+(el.staticInFor?',true':'')+")";}// v-once
function genOnce(el,state){el.onceProcessed=true;if(el.if&&!el.ifProcessed){return genIf(el,state);}else if(el.staticInFor){var key='';var parent=el.parent;while(parent){if(parent.for){key=parent.key;break;}parent=parent.parent;}if(!key){process.env.NODE_ENV!=='production'&&state.warn("v-once can only be used inside v-for that is keyed. ");return genElement(el,state);}return"_o("+genElement(el,state)+","+state.onceId++ +","+key+")";}else{return genStatic(el,state);}}function genIf(el,state,altGen,altEmpty){el.ifProcessed=true;// avoid recursion
return genIfConditions(el.ifConditions.slice(),state,altGen,altEmpty);}function genIfConditions(conditions,state,altGen,altEmpty){if(!conditions.length){return altEmpty||'_e()';}var condition=conditions.shift();if(condition.exp){return"("+condition.exp+")?"+genTernaryExp(condition.block)+":"+genIfConditions(conditions,state,altGen,altEmpty);}else{return""+genTernaryExp(condition.block);}// v-if with v-once should generate code like (a)?_m(0):_m(1)
function genTernaryExp(el){return altGen?altGen(el,state):el.once?genOnce(el,state):genElement(el,state);}}function genFor(el,state,altGen,altHelper){var exp=el.for;var alias=el.alias;var iterator1=el.iterator1?","+el.iterator1:'';var iterator2=el.iterator2?","+el.iterator2:'';if(process.env.NODE_ENV!=='production'&&state.maybeComponent(el)&&el.tag!=='slot'&&el.tag!=='template'&&!el.key){state.warn("<"+el.tag+" v-for=\""+alias+" in "+exp+"\">: component lists rendered with "+"v-for should have explicit keys. "+"See https://vuejs.org/guide/list.html#key for more info.",true/* tip */);}el.forProcessed=true;// avoid recursion
return(altHelper||'_l')+"(("+exp+"),"+"function("+alias+iterator1+iterator2+"){"+"return "+(altGen||genElement)(el,state)+'})';}function genData$2(el,state){var data='{';// directives first.
// directives may mutate the el's other properties before they are generated.
var dirs=genDirectives(el,state);if(dirs){data+=dirs+',';}// key
if(el.key){data+="key:"+el.key+",";}// ref
if(el.ref){data+="ref:"+el.ref+",";}if(el.refInFor){data+="refInFor:true,";}// pre
if(el.pre){data+="pre:true,";}// record original tag name for components using "is" attribute
if(el.component){data+="tag:\""+el.tag+"\",";}// module data generation functions
for(var i=0;i<state.dataGenFns.length;i++){data+=state.dataGenFns[i](el);}// attributes
if(el.attrs){data+="attrs:{"+genProps(el.attrs)+"},";}// DOM props
if(el.props){data+="domProps:{"+genProps(el.props)+"},";}// event handlers
if(el.events){data+=genHandlers(el.events,false)+",";}if(el.nativeEvents){data+=genHandlers(el.nativeEvents,true)+",";}// slot target
// only for non-scoped slots
if(el.slotTarget&&!el.slotScope){data+="slot:"+el.slotTarget+",";}// scoped slots
if(el.scopedSlots){data+=genScopedSlots(el.scopedSlots,state)+",";}// component v-model
if(el.model){data+="model:{value:"+el.model.value+",callback:"+el.model.callback+",expression:"+el.model.expression+"},";}// inline-template
if(el.inlineTemplate){var inlineTemplate=genInlineTemplate(el,state);if(inlineTemplate){data+=inlineTemplate+",";}}data=data.replace(/,$/,'')+'}';// v-bind data wrap
if(el.wrapData){data=el.wrapData(data);}// v-on data wrap
if(el.wrapListeners){data=el.wrapListeners(data);}return data;}function genDirectives(el,state){var dirs=el.directives;if(!dirs){return;}var res='directives:[';var hasRuntime=false;var i,l,dir,needRuntime;for(i=0,l=dirs.length;i<l;i++){dir=dirs[i];needRuntime=true;var gen=state.directives[dir.name];if(gen){// compile-time directive that manipulates AST.
// returns true if it also needs a runtime counterpart.
needRuntime=!!gen(el,dir,state.warn);}if(needRuntime){hasRuntime=true;res+="{name:\""+dir.name+"\",rawName:\""+dir.rawName+"\""+(dir.value?",value:("+dir.value+"),expression:"+JSON.stringify(dir.value):'')+(dir.arg?",arg:\""+dir.arg+"\"":'')+(dir.modifiers?",modifiers:"+JSON.stringify(dir.modifiers):'')+"},";}}if(hasRuntime){return res.slice(0,-1)+']';}}function genInlineTemplate(el,state){var ast=el.children[0];if(process.env.NODE_ENV!=='production'&&(el.children.length!==1||ast.type!==1)){state.warn('Inline-template components must have exactly one child element.');}if(ast.type===1){var inlineRenderFns=generate(ast,state.options);return"inlineTemplate:{render:function(){"+inlineRenderFns.render+"},staticRenderFns:["+inlineRenderFns.staticRenderFns.map(function(code){return"function(){"+code+"}";}).join(',')+"]}";}}function genScopedSlots(slots,state){return"scopedSlots:_u(["+Object.keys(slots).map(function(key){return genScopedSlot(key,slots[key],state);}).join(',')+"])";}function genScopedSlot(key,el,state){if(el.for&&!el.forProcessed){return genForScopedSlot(key,el,state);}var fn="function("+String(el.slotScope)+"){"+"return "+(el.tag==='template'?el.if?"("+el.if+")?"+(genChildren(el,state)||'undefined')+":undefined":genChildren(el,state)||'undefined':genElement(el,state))+"}";return"{key:"+key+",fn:"+fn+"}";}function genForScopedSlot(key,el,state){var exp=el.for;var alias=el.alias;var iterator1=el.iterator1?","+el.iterator1:'';var iterator2=el.iterator2?","+el.iterator2:'';el.forProcessed=true;// avoid recursion
return"_l(("+exp+"),"+"function("+alias+iterator1+iterator2+"){"+"return "+genScopedSlot(key,el,state)+'})';}function genChildren(el,state,checkSkip,altGenElement,altGenNode){var children=el.children;if(children.length){var el$1=children[0];// optimize single v-for
if(children.length===1&&el$1.for&&el$1.tag!=='template'&&el$1.tag!=='slot'){var normalizationType=checkSkip?state.maybeComponent(el$1)?",1":",0":"";return""+(altGenElement||genElement)(el$1,state)+normalizationType;}var normalizationType$1=checkSkip?getNormalizationType(children,state.maybeComponent):0;var gen=altGenNode||genNode;return"["+children.map(function(c){return gen(c,state);}).join(',')+"]"+(normalizationType$1?","+normalizationType$1:'');}}// determine the normalization needed for the children array.
// 0: no normalization needed
// 1: simple normalization needed (possible 1-level deep nested array)
// 2: full normalization needed
function getNormalizationType(children,maybeComponent){var res=0;for(var i=0;i<children.length;i++){var el=children[i];if(el.type!==1){continue;}if(needsNormalization(el)||el.ifConditions&&el.ifConditions.some(function(c){return needsNormalization(c.block);})){res=2;break;}if(maybeComponent(el)||el.ifConditions&&el.ifConditions.some(function(c){return maybeComponent(c.block);})){res=1;}}return res;}function needsNormalization(el){return el.for!==undefined||el.tag==='template'||el.tag==='slot';}function genNode(node,state){if(node.type===1){return genElement(node,state);}else if(node.type===3&&node.isComment){return genComment(node);}else{return genText(node);}}function genText(text){return"_v("+(text.type===2?text.expression// no need for () because already wrapped in _s()
:transformSpecialNewlines(JSON.stringify(text.text)))+")";}function genComment(comment){return"_e("+JSON.stringify(comment.text)+")";}function genSlot(el,state){var slotName=el.slotName||'"default"';var children=genChildren(el,state);var res="_t("+slotName+(children?","+children:'');var attrs=el.attrs&&"{"+el.attrs.map(function(a){return camelize(a.name)+":"+a.value;}).join(',')+"}";var bind$$1=el.attrsMap['v-bind'];if((attrs||bind$$1)&&!children){res+=",null";}if(attrs){res+=","+attrs;}if(bind$$1){res+=(attrs?'':',null')+","+bind$$1;}return res+')';}// componentName is el.component, take it as argument to shun flow's pessimistic refinement
function genComponent(componentName,el,state){var children=el.inlineTemplate?null:genChildren(el,state,true);return"_c("+componentName+","+genData$2(el,state)+(children?","+children:'')+")";}function genProps(props){var res='';for(var i=0;i<props.length;i++){var prop=props[i];/* istanbul ignore if */{res+="\""+prop.name+"\":"+transformSpecialNewlines(prop.value)+",";}}return res.slice(0,-1);}// #3895, #4268
function transformSpecialNewlines(text){return text.replace(/\u2028/g,'\\u2028').replace(/\u2029/g,'\\u2029');}/*  */// these keywords should not appear inside expressions, but operators like
// typeof, instanceof and in are allowed
var prohibitedKeywordRE=new RegExp('\\b'+('do,if,for,let,new,try,var,case,else,with,await,break,catch,class,const,'+'super,throw,while,yield,delete,export,import,return,switch,default,'+'extends,finally,continue,debugger,function,arguments').split(',').join('\\b|\\b')+'\\b');// these unary operators should not be used as property/method names
var unaryOperatorsRE=new RegExp('\\b'+'delete,typeof,void'.split(',').join('\\s*\\([^\\)]*\\)|\\b')+'\\s*\\([^\\)]*\\)');// strip strings in expressions
var stripStringRE=/'(?:[^'\\]|\\.)*'|"(?:[^"\\]|\\.)*"|`(?:[^`\\]|\\.)*\$\{|\}(?:[^`\\]|\\.)*`|`(?:[^`\\]|\\.)*`/g;// detect problematic expressions in a template
function detectErrors(ast){var errors=[];if(ast){checkNode(ast,errors);}return errors;}function checkNode(node,errors){if(node.type===1){for(var name in node.attrsMap){if(dirRE.test(name)){var value=node.attrsMap[name];if(value){if(name==='v-for'){checkFor(node,"v-for=\""+value+"\"",errors);}else if(onRE.test(name)){checkEvent(value,name+"=\""+value+"\"",errors);}else{checkExpression(value,name+"=\""+value+"\"",errors);}}}}if(node.children){for(var i=0;i<node.children.length;i++){checkNode(node.children[i],errors);}}}else if(node.type===2){checkExpression(node.expression,node.text,errors);}}function checkEvent(exp,text,errors){var stipped=exp.replace(stripStringRE,'');var keywordMatch=stipped.match(unaryOperatorsRE);if(keywordMatch&&stipped.charAt(keywordMatch.index-1)!=='$'){errors.push("avoid using JavaScript unary operator as property name: "+"\""+keywordMatch[0]+"\" in expression "+text.trim());}checkExpression(exp,text,errors);}function checkFor(node,text,errors){checkExpression(node.for||'',text,errors);checkIdentifier(node.alias,'v-for alias',text,errors);checkIdentifier(node.iterator1,'v-for iterator',text,errors);checkIdentifier(node.iterator2,'v-for iterator',text,errors);}function checkIdentifier(ident,type,text,errors){if(typeof ident==='string'){try{new Function("var "+ident+"=_");}catch(e){errors.push("invalid "+type+" \""+ident+"\" in expression: "+text.trim());}}}function checkExpression(exp,text,errors){try{new Function("return "+exp);}catch(e){var keywordMatch=exp.replace(stripStringRE,'').match(prohibitedKeywordRE);if(keywordMatch){errors.push("avoid using JavaScript keyword as property name: "+"\""+keywordMatch[0]+"\"\n  Raw expression: "+text.trim());}else{errors.push("invalid expression: "+e.message+" in\n\n"+"    "+exp+"\n\n"+"  Raw expression: "+text.trim()+"\n");}}}/*  */function createFunction(code,errors){try{return new Function(code);}catch(err){errors.push({err:err,code:code});return noop;}}function createCompileToFunctionFn(compile){var cache=Object.create(null);return function compileToFunctions(template,options,vm){options=extend({},options);var warn$$1=options.warn||warn;delete options.warn;/* istanbul ignore if */if(process.env.NODE_ENV!=='production'){// detect possible CSP restriction
try{new Function('return 1');}catch(e){if(e.toString().match(/unsafe-eval|CSP/)){warn$$1('It seems you are using the standalone build of Vue.js in an '+'environment with Content Security Policy that prohibits unsafe-eval. '+'The template compiler cannot work in this environment. Consider '+'relaxing the policy to allow unsafe-eval or pre-compiling your '+'templates into render functions.');}}}// check cache
var key=options.delimiters?String(options.delimiters)+template:template;if(cache[key]){return cache[key];}// compile
var compiled=compile(template,options);// check compilation errors/tips
if(process.env.NODE_ENV!=='production'){if(compiled.errors&&compiled.errors.length){warn$$1("Error compiling template:\n\n"+template+"\n\n"+compiled.errors.map(function(e){return"- "+e;}).join('\n')+'\n',vm);}if(compiled.tips&&compiled.tips.length){compiled.tips.forEach(function(msg){return tip(msg,vm);});}}// turn code into functions
var res={};var fnGenErrors=[];res.render=createFunction(compiled.render,fnGenErrors);res.staticRenderFns=compiled.staticRenderFns.map(function(code){return createFunction(code,fnGenErrors);});// check function generation errors.
// this should only happen if there is a bug in the compiler itself.
// mostly for codegen development use
/* istanbul ignore if */if(process.env.NODE_ENV!=='production'){if((!compiled.errors||!compiled.errors.length)&&fnGenErrors.length){warn$$1("Failed to generate render function:\n\n"+fnGenErrors.map(function(ref){var err=ref.err;var code=ref.code;return err.toString()+" in\n\n"+code+"\n";}).join('\n'),vm);}}return cache[key]=res;};}/*  */function createCompilerCreator(baseCompile){return function createCompiler(baseOptions){function compile(template,options){var finalOptions=Object.create(baseOptions);var errors=[];var tips=[];finalOptions.warn=function(msg,tip){(tip?tips:errors).push(msg);};if(options){// merge custom modules
if(options.modules){finalOptions.modules=(baseOptions.modules||[]).concat(options.modules);}// merge custom directives
if(options.directives){finalOptions.directives=extend(Object.create(baseOptions.directives||null),options.directives);}// copy other options
for(var key in options){if(key!=='modules'&&key!=='directives'){finalOptions[key]=options[key];}}}var compiled=baseCompile(template,finalOptions);if(process.env.NODE_ENV!=='production'){errors.push.apply(errors,detectErrors(compiled.ast));}compiled.errors=errors;compiled.tips=tips;return compiled;}return{compile:compile,compileToFunctions:createCompileToFunctionFn(compile)};};}/*  */// `createCompilerCreator` allows creating compilers that use alternative
// parser/optimizer/codegen, e.g the SSR optimizing compiler.
// Here we just export a default compiler using the default parts.
var createCompiler=createCompilerCreator(function baseCompile(template,options){var ast=parse(template.trim(),options);if(options.optimize!==false){optimize(ast,options);}var code=generate(ast,options);return{ast:ast,render:code.render,staticRenderFns:code.staticRenderFns};});/*  */var ref$1=createCompiler(baseOptions);var compile=ref$1.compile;var compileToFunctions=ref$1.compileToFunctions;/*  */// check whether current browser encodes a char inside attribute values
var div;function getShouldDecode(href){div=div||document.createElement('div');div.innerHTML=href?"<a href=\"\n\"/>":"<div a=\"\n\"/>";return div.innerHTML.indexOf('&#10;')>0;}// #3663: IE encodes newlines inside attribute values while other browsers don't
var shouldDecodeNewlines=inBrowser?getShouldDecode(false):false;// #6828: chrome encodes content in a[href]
var shouldDecodeNewlinesForHref=inBrowser?getShouldDecode(true):false;/*  */var idToTemplate=cached(function(id){var el=query(id);return el&&el.innerHTML;});var mount=Vue.prototype.$mount;Vue.prototype.$mount=function(el,hydrating){el=el&&query(el);/* istanbul ignore if */if(el===document.body||el===document.documentElement){process.env.NODE_ENV!=='production'&&warn("Do not mount Vue to <html> or <body> - mount to normal elements instead.");return this;}var options=this.$options;// resolve template/el and convert to render function
if(!options.render){var template=options.template;if(template){if(typeof template==='string'){if(template.charAt(0)==='#'){template=idToTemplate(template);/* istanbul ignore if */if(process.env.NODE_ENV!=='production'&&!template){warn("Template element not found or is empty: "+options.template,this);}}}else if(template.nodeType){template=template.innerHTML;}else{if(process.env.NODE_ENV!=='production'){warn('invalid template option:'+template,this);}return this;}}else if(el){template=getOuterHTML(el);}if(template){/* istanbul ignore if */if(process.env.NODE_ENV!=='production'&&config.performance&&mark){mark('compile');}var ref=compileToFunctions(template,{shouldDecodeNewlines:shouldDecodeNewlines,shouldDecodeNewlinesForHref:shouldDecodeNewlinesForHref,delimiters:options.delimiters,comments:options.comments},this);var render=ref.render;var staticRenderFns=ref.staticRenderFns;options.render=render;options.staticRenderFns=staticRenderFns;/* istanbul ignore if */if(process.env.NODE_ENV!=='production'&&config.performance&&mark){mark('compile end');measure("vue "+this._name+" compile",'compile','compile end');}}}return mount.call(this,el,hydrating);};/**
 * Get outerHTML of elements, taking care
 * of SVG elements in IE as well.
 */function getOuterHTML(el){if(el.outerHTML){return el.outerHTML;}else{var container=document.createElement('div');container.appendChild(el.cloneNode(true));return container.innerHTML;}}Vue.compile=compileToFunctions;/* harmony default export */__webpack_exports__["a"]=Vue;/* WEBPACK VAR INJECTION */}).call(__webpack_exports__,__webpack_require__(9),__webpack_require__(5),__webpack_require__(30).setImmediate);/***/},/* 4 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0_vue__=__webpack_require__(3);var EventBus=new __WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */]();/* harmony export (immutable) */__webpack_exports__["a"]=EventBus;/***/},/* 5 *//***/function(module,exports){var g;// This works in non-strict mode
g=function(){return this;}();try{// This works if eval is allowed (see CSP)
g=g||Function("return this")()||(1,eval)("this");}catch(e){// This works if the window reference is available
if((typeof window==='undefined'?'undefined':_typeof2(window))==="object")g=window;}// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}
module.exports=g;/***/},/* 6 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Message_vue__=__webpack_require__(15);/* unused harmony namespace reexport *//* harmony import */var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_39246a01_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Message_vue__=__webpack_require__(55);var disposed=false;function injectStyle(ssrContext){if(disposed)return;__webpack_require__(53);}var normalizeComponent=__webpack_require__(2);/* script *//* template *//* template functional */var __vue_template_functional__=false;/* styles */var __vue_styles__=injectStyle;/* scopeId */var __vue_scopeId__=null;/* moduleIdentifier (server only) */var __vue_module_identifier__=null;var Component=normalizeComponent(__WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Message_vue__["a"/* default */],__WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_39246a01_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Message_vue__["a"/* default */],__vue_template_functional__,__vue_styles__,__vue_scopeId__,__vue_module_identifier__);Component.options.__file="assets/js/src/components/Message.vue";/* hot reload */if(false){(function(){var hotAPI=require("vue-hot-reload-api");hotAPI.install(require("vue"),false);if(!hotAPI.compatible)return;module.hot.accept();if(!module.hot.data){hotAPI.createRecord("data-v-39246a01",Component.options);}else{hotAPI.reload("data-v-39246a01",Component.options);}module.hot.dispose(function(data){disposed=true;});})();}/* harmony default export */__webpack_exports__["a"]=Component.exports;/***/},/* 7 *//***/function(module,exports,__webpack_require__){(function(){var crypt=__webpack_require__(44),utf8=__webpack_require__(12).utf8,isBuffer=__webpack_require__(45),bin=__webpack_require__(12).bin,// The core
md5=function md5(message,options){// Convert to byte array
if(message.constructor==String){if(options&&options.encoding==='binary')message=bin.stringToBytes(message);else message=utf8.stringToBytes(message);}else if(isBuffer(message))message=Array.prototype.slice.call(message,0);else if(!Array.isArray(message))message=message.toString();// else, assume byte array already
var m=crypt.bytesToWords(message),l=message.length*8,a=1732584193,b=-271733879,c=-1732584194,d=271733878;// Swap endian
for(var i=0;i<m.length;i++){m[i]=(m[i]<<8|m[i]>>>24)&0x00FF00FF|(m[i]<<24|m[i]>>>8)&0xFF00FF00;}// Padding
m[l>>>5]|=0x80<<l%32;m[(l+64>>>9<<4)+14]=l;// Method shortcuts
var FF=md5._ff,GG=md5._gg,HH=md5._hh,II=md5._ii;for(var i=0;i<m.length;i+=16){var aa=a,bb=b,cc=c,dd=d;a=FF(a,b,c,d,m[i+0],7,-680876936);d=FF(d,a,b,c,m[i+1],12,-389564586);c=FF(c,d,a,b,m[i+2],17,606105819);b=FF(b,c,d,a,m[i+3],22,-1044525330);a=FF(a,b,c,d,m[i+4],7,-176418897);d=FF(d,a,b,c,m[i+5],12,1200080426);c=FF(c,d,a,b,m[i+6],17,-1473231341);b=FF(b,c,d,a,m[i+7],22,-45705983);a=FF(a,b,c,d,m[i+8],7,1770035416);d=FF(d,a,b,c,m[i+9],12,-1958414417);c=FF(c,d,a,b,m[i+10],17,-42063);b=FF(b,c,d,a,m[i+11],22,-1990404162);a=FF(a,b,c,d,m[i+12],7,1804603682);d=FF(d,a,b,c,m[i+13],12,-40341101);c=FF(c,d,a,b,m[i+14],17,-1502002290);b=FF(b,c,d,a,m[i+15],22,1236535329);a=GG(a,b,c,d,m[i+1],5,-165796510);d=GG(d,a,b,c,m[i+6],9,-1069501632);c=GG(c,d,a,b,m[i+11],14,643717713);b=GG(b,c,d,a,m[i+0],20,-373897302);a=GG(a,b,c,d,m[i+5],5,-701558691);d=GG(d,a,b,c,m[i+10],9,38016083);c=GG(c,d,a,b,m[i+15],14,-660478335);b=GG(b,c,d,a,m[i+4],20,-405537848);a=GG(a,b,c,d,m[i+9],5,568446438);d=GG(d,a,b,c,m[i+14],9,-1019803690);c=GG(c,d,a,b,m[i+3],14,-187363961);b=GG(b,c,d,a,m[i+8],20,1163531501);a=GG(a,b,c,d,m[i+13],5,-1444681467);d=GG(d,a,b,c,m[i+2],9,-51403784);c=GG(c,d,a,b,m[i+7],14,1735328473);b=GG(b,c,d,a,m[i+12],20,-1926607734);a=HH(a,b,c,d,m[i+5],4,-378558);d=HH(d,a,b,c,m[i+8],11,-2022574463);c=HH(c,d,a,b,m[i+11],16,1839030562);b=HH(b,c,d,a,m[i+14],23,-35309556);a=HH(a,b,c,d,m[i+1],4,-1530992060);d=HH(d,a,b,c,m[i+4],11,1272893353);c=HH(c,d,a,b,m[i+7],16,-155497632);b=HH(b,c,d,a,m[i+10],23,-1094730640);a=HH(a,b,c,d,m[i+13],4,681279174);d=HH(d,a,b,c,m[i+0],11,-358537222);c=HH(c,d,a,b,m[i+3],16,-722521979);b=HH(b,c,d,a,m[i+6],23,76029189);a=HH(a,b,c,d,m[i+9],4,-640364487);d=HH(d,a,b,c,m[i+12],11,-421815835);c=HH(c,d,a,b,m[i+15],16,530742520);b=HH(b,c,d,a,m[i+2],23,-995338651);a=II(a,b,c,d,m[i+0],6,-198630844);d=II(d,a,b,c,m[i+7],10,1126891415);c=II(c,d,a,b,m[i+14],15,-1416354905);b=II(b,c,d,a,m[i+5],21,-57434055);a=II(a,b,c,d,m[i+12],6,1700485571);d=II(d,a,b,c,m[i+3],10,-1894986606);c=II(c,d,a,b,m[i+10],15,-1051523);b=II(b,c,d,a,m[i+1],21,-2054922799);a=II(a,b,c,d,m[i+8],6,1873313359);d=II(d,a,b,c,m[i+15],10,-30611744);c=II(c,d,a,b,m[i+6],15,-1560198380);b=II(b,c,d,a,m[i+13],21,1309151649);a=II(a,b,c,d,m[i+4],6,-145523070);d=II(d,a,b,c,m[i+11],10,-1120210379);c=II(c,d,a,b,m[i+2],15,718787259);b=II(b,c,d,a,m[i+9],21,-343485551);a=a+aa>>>0;b=b+bb>>>0;c=c+cc>>>0;d=d+dd>>>0;}return crypt.endian([a,b,c,d]);};// Auxiliary functions
md5._ff=function(a,b,c,d,x,s,t){var n=a+(b&c|~b&d)+(x>>>0)+t;return(n<<s|n>>>32-s)+b;};md5._gg=function(a,b,c,d,x,s,t){var n=a+(b&d|c&~d)+(x>>>0)+t;return(n<<s|n>>>32-s)+b;};md5._hh=function(a,b,c,d,x,s,t){var n=a+(b^c^d)+(x>>>0)+t;return(n<<s|n>>>32-s)+b;};md5._ii=function(a,b,c,d,x,s,t){var n=a+(c^(b|~d))+(x>>>0)+t;return(n<<s|n>>>32-s)+b;};// Package private blocksize
md5._blocksize=16;md5._digestsize=16;module.exports=function(message,options){if(message===undefined||message===null)throw new Error('Illegal argument '+message);var digestbytes=crypt.wordsToBytes(md5(message,options));return options&&options.asBytes?digestbytes:options&&options.asString?bin.bytesToString(digestbytes):crypt.bytesToHex(digestbytes);};})();/***/},/* 8 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Button_vue__=__webpack_require__(18);/* unused harmony namespace reexport *//* harmony import */var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_f7a20cb0_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Button_vue__=__webpack_require__(68);var disposed=false;function injectStyle(ssrContext){if(disposed)return;__webpack_require__(66);}var normalizeComponent=__webpack_require__(2);/* script *//* template *//* template functional */var __vue_template_functional__=false;/* styles */var __vue_styles__=injectStyle;/* scopeId */var __vue_scopeId__=null;/* moduleIdentifier (server only) */var __vue_module_identifier__=null;var Component=normalizeComponent(__WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Button_vue__["a"/* default */],__WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_f7a20cb0_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Button_vue__["a"/* default */],__vue_template_functional__,__vue_styles__,__vue_scopeId__,__vue_module_identifier__);Component.options.__file="assets/js/src/components/Button.vue";/* hot reload */if(false){(function(){var hotAPI=require("vue-hot-reload-api");hotAPI.install(require("vue"),false);if(!hotAPI.compatible)return;module.hot.accept();if(!module.hot.data){hotAPI.createRecord("data-v-f7a20cb0",Component.options);}else{hotAPI.reload("data-v-f7a20cb0",Component.options);}module.hot.dispose(function(data){disposed=true;});})();}/* harmony default export */__webpack_exports__["a"]=Component.exports;/***/},/* 9 *//***/function(module,exports){// shim for using process in browser
var process=module.exports={};// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.
var cachedSetTimeout;var cachedClearTimeout;function defaultSetTimout(){throw new Error('setTimeout has not been defined');}function defaultClearTimeout(){throw new Error('clearTimeout has not been defined');}(function(){try{if(typeof setTimeout==='function'){cachedSetTimeout=setTimeout;}else{cachedSetTimeout=defaultSetTimout;}}catch(e){cachedSetTimeout=defaultSetTimout;}try{if(typeof clearTimeout==='function'){cachedClearTimeout=clearTimeout;}else{cachedClearTimeout=defaultClearTimeout;}}catch(e){cachedClearTimeout=defaultClearTimeout;}})();function runTimeout(fun){if(cachedSetTimeout===setTimeout){//normal enviroments in sane situations
return setTimeout(fun,0);}// if setTimeout wasn't available but was latter defined
if((cachedSetTimeout===defaultSetTimout||!cachedSetTimeout)&&setTimeout){cachedSetTimeout=setTimeout;return setTimeout(fun,0);}try{// when when somebody has screwed with setTimeout but no I.E. maddness
return cachedSetTimeout(fun,0);}catch(e){try{// When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
return cachedSetTimeout.call(null,fun,0);}catch(e){// same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
return cachedSetTimeout.call(this,fun,0);}}}function runClearTimeout(marker){if(cachedClearTimeout===clearTimeout){//normal enviroments in sane situations
return clearTimeout(marker);}// if clearTimeout wasn't available but was latter defined
if((cachedClearTimeout===defaultClearTimeout||!cachedClearTimeout)&&clearTimeout){cachedClearTimeout=clearTimeout;return clearTimeout(marker);}try{// when when somebody has screwed with setTimeout but no I.E. maddness
return cachedClearTimeout(marker);}catch(e){try{// When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
return cachedClearTimeout.call(null,marker);}catch(e){// same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
// Some versions of I.E. have different rules for clearTimeout vs setTimeout
return cachedClearTimeout.call(this,marker);}}}var queue=[];var draining=false;var currentQueue;var queueIndex=-1;function cleanUpNextTick(){if(!draining||!currentQueue){return;}draining=false;if(currentQueue.length){queue=currentQueue.concat(queue);}else{queueIndex=-1;}if(queue.length){drainQueue();}}function drainQueue(){if(draining){return;}var timeout=runTimeout(cleanUpNextTick);draining=true;var len=queue.length;while(len){currentQueue=queue;queue=[];while(++queueIndex<len){if(currentQueue){currentQueue[queueIndex].run();}}queueIndex=-1;len=queue.length;}currentQueue=null;draining=false;runClearTimeout(timeout);}process.nextTick=function(fun){var args=new Array(arguments.length-1);if(arguments.length>1){for(var i=1;i<arguments.length;i++){args[i-1]=arguments[i];}}queue.push(new Item(fun,args));if(queue.length===1&&!draining){runTimeout(drainQueue);}};// v8 likes predictible objects
function Item(fun,array){this.fun=fun;this.array=array;}Item.prototype.run=function(){this.fun.apply(null,this.array);};process.title='browser';process.browser=true;process.env={};process.argv=[];process.version='';// empty string to avoid regexp issues
process.versions={};function noop(){}process.on=noop;process.addListener=noop;process.once=noop;process.off=noop;process.removeListener=noop;process.removeAllListeners=noop;process.emit=noop;process.prependListener=noop;process.prependOnceListener=noop;process.listeners=function(name){return[];};process.binding=function(name){throw new Error('process.binding is not supported');};process.cwd=function(){return'/';};process.chdir=function(dir){throw new Error('process.chdir is not supported');};process.umask=function(){return 0;};/***/},/* 10 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__components_Engines_vue__=__webpack_require__(41);/* harmony import */var __WEBPACK_IMPORTED_MODULE_1__components_IndexStats_vue__=__webpack_require__(92);//
//
//
//
//
//
//
//
//
/* harmony default export */__webpack_exports__["a"]={name:'SearchwpSettings',components:{'searchwp-engines':__WEBPACK_IMPORTED_MODULE_0__components_Engines_vue__["a"/* default */],'searchwp-index-stats':__WEBPACK_IMPORTED_MODULE_1__components_IndexStats_vue__["a"/* default */]}};/***/},/* 11 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0_vue__=__webpack_require__(3);/* harmony import */var __WEBPACK_IMPORTED_MODULE_1__EventBus_js__=__webpack_require__(4);/* harmony import */var __WEBPACK_IMPORTED_MODULE_2_md5__=__webpack_require__(7);/* harmony import */var __WEBPACK_IMPORTED_MODULE_2_md5___default=__webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_md5__);/* harmony import */var __WEBPACK_IMPORTED_MODULE_3_keyfinder__=__webpack_require__(13);/* harmony import */var __WEBPACK_IMPORTED_MODULE_3_keyfinder___default=__webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_keyfinder__);/* harmony import */var __WEBPACK_IMPORTED_MODULE_4_array_unique__=__webpack_require__(49);/* harmony import */var __WEBPACK_IMPORTED_MODULE_4_array_unique___default=__webpack_require__.n(__WEBPACK_IMPORTED_MODULE_4_array_unique__);/* harmony import */var __WEBPACK_IMPORTED_MODULE_5__EnvironmentCheck_vue__=__webpack_require__(50);/* harmony import */var __WEBPACK_IMPORTED_MODULE_6__Engine_vue__=__webpack_require__(57);/* harmony import */var __WEBPACK_IMPORTED_MODULE_7__Message_vue__=__webpack_require__(6);//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */__webpack_exports__["a"]={name:'SearchwpEngines',components:{'searchwp-environment-check':__WEBPACK_IMPORTED_MODULE_5__EnvironmentCheck_vue__["a"/* default */],'searchwp-engine':__WEBPACK_IMPORTED_MODULE_6__Engine_vue__["a"/* default */],'searchwp-message':__WEBPACK_IMPORTED_MODULE_7__Message_vue__["a"/* default */]},data:function data(){return{adminSearchEnabled:_SEARCHWP_VARS.data.misc.admin_search,adminSearchEngine:_SEARCHWP_VARS.data.misc.admin_engine,waiting:false,engines:_SEARCHWP_VARS.data.engines,initialFingerprint:'',fingerprint:'',dirtyIndex:false,saving:false,doingSave:false,saved:false,initialSettingsSaved:_SEARCHWP_VARS.data.misc.initial_settings_saved,legacySettings:_SEARCHWP_VARS.data.misc.legacy_settings,i18n:{addEngine:_SEARCHWP_VARS.i18n.add_engine,indexDirtyFromEnginesSave:_SEARCHWP_VARS.i18n.index_dirty_from_engines_save,problemSavingEngineSettings:_SEARCHWP_VARS.i18n.problem_saving_engine_settings,rebuildIndex:_SEARCHWP_VARS.i18n.rebuild_index,saveEngines:_SEARCHWP_VARS.i18n.save_engines,saved:_SEARCHWP_VARS.i18n.saved,dismiss:_SEARCHWP_VARS.i18n.dismiss,legacySettingsNotice:_SEARCHWP_VARS.i18n.legacy_settings_notice,initialSettingsNotice:_SEARCHWP_VARS.i18n.initial_settings_notice,moreInfo:_SEARCHWP_VARS.i18n.more_info,useDefaults:_SEARCHWP_VARS.i18n.use_defaults}};},methods:{dismissLegacySettings:function dismissLegacySettings(){this.legacySettings=false;__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].SearchwpSetSetting('legacy_engines',JSON.stringify(false)).then(function(response){});},resetIndex:function resetIndex(){var self=this;self.dirtyIndex=false;__WEBPACK_IMPORTED_MODULE_1__EventBus_js__["a"/* EventBus */].$emit('indexReset');self.waiting=true;__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].SearchwpResetIndex().then(function(response){// Since we saved, we need to update the initial fingerprint
self.initialFingerprint=JSON.parse(JSON.stringify(self.fingerprint));// If the alternate indexer is in use, we need to reload so the alternate indexer can be triggered
if(_SEARCHWP_VARS.data.misc.alternate_indexer){window.location.reload();}else{self.waiting=false;}}).catch(function(response){alert('ERROR SEARCHWPINDEXRESET');});},addEngine:function addEngine(){var d=new Date();var engineHash='searchwp_engine_hash_'+__WEBPACK_IMPORTED_MODULE_2_md5___default()('searchwp_engine_hash_'+d.getTime());__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].set(this.engines,engineHash,JSON.parse(JSON.stringify(this.$root.engine_model)));},saveEngines:function saveEngines(){var self=this;var hadLegacySettings=false;self.saving=true;self.doingSave=true;// Update current fingerprint
self.fingerprint=self.generateFingerprint();// If engines are being saved, it means that settings are no longer legacy
if(self.legacySettings){self.legacySettings=false;hadLegacySettings=true;}__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].SearchwpSetSetting('engines',JSON.stringify(self.engines)).then(function(response){self.saving=false;// If engines are being saved, it means that settings are no longer legacy
if(hadLegacySettings){self.dismissLegacySettings();}self.afterSaveEngines();}).catch(function(response){alert(self.i18n.problemSavingEngineSettings);});},afterSaveEngines:function afterSaveEngines(){var self=this;// Since we're saving settings, initial settings must be true
if(!this.initialSettingsSaved){this.initialSettingsSaved=true;// Kick off the indexer
this.resetIndex();}else{// Signal that the index is dirty after save and needs to be rebuilt
self.dirtyIndex=self.isIndexDirty();}// Emit the model to components watching for engine saves
__WEBPACK_IMPORTED_MODULE_1__EventBus_js__["a"/* EventBus */].$emit('enginesSaved',{initialFingerprint:JSON.stringify(self.initialFingerprint),currentFingerprint:JSON.stringify(self.fingerprint),engines:self.engines,dirtyIndex:self.dirtyIndex});__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].SearchwpSetSetting('index_dirty',JSON.stringify(self.dirtyIndex)).then(function(response){});// Show confirmation, then restore save button
self.saved=true;setTimeout(function(){self.doingSave=false;self.saved=false;},1600);},isIndexDirty:function isIndexDirty(){var indexDirty=false;// The index is dirty if any of the following is true:
// - A post type that was indexed has been removed
// - Change in taxonomies
// - Change in metadata
// - Change in rules
// To determine whether a post type has been removed from the settings we can
// compare the initial fingerprint post types with the current post types
// We cannot simply count the enabled post types because if Posts were removed
// and Pages added at the same time, we'd still have 1 post type, but it's different
// Let's loop through the initial fingerprint and compare each value to the current
// fingerprint; if the post type is missing it has been removed
for(var i=0;i<this.initialFingerprint.postTypes.length;i++){if(this.fingerprint.postTypes.indexOf(this.initialFingerprint.postTypes[i])==-1){indexDirty=true;}}// If the post types didn't change, we need to check the other criteria
// This is more straightforward because any change means an unoptimized index
// ******************
// !!!! NOT TRUE !!!!
// ******************
// There is a false positive returned if *new* Custom Field(s) or Taxonomy Term(s)
// are added to a new Post Type — this should not happen because we are only adding
// to the index, so the index is technically not dirty. See Issue #33.
if(!indexDirty){// We want a copy of the fingerprints to manipulate without consequence
var initialFingerprint=JSON.parse(JSON.stringify(this.initialFingerprint));var fingerprint=JSON.parse(JSON.stringify(this.fingerprint));// Post types are a special circumstance previously handled, they no longer apply
delete initialFingerprint.postTypes;delete fingerprint.postTypes;// At this point it's a simple comparison of the two fingerprints
indexDirty=JSON.stringify(initialFingerprint)!==JSON.stringify(fingerprint);}return indexDirty;},generateFingerprint:function generateFingerprint(){// A fingerprint identifies the index-influencing engine attributes in play.
// Context: if a fingerprint differs from the fingerprint of the existing
// index, the index is no longer accurate and must be rebuilt.
// At the base, a fingerprint is defined by the enabled custom post types.
// Only enabled custom post types are indexed, because there's no reason
// to index anything else.
// The two attributes we're checking are taxonomies and custom fields,
// so we're going to get a list across all engines, make the list unique
// and sort the list, and then we have our fingerprint for comparison
// This works because SearchWP uses a single index for all engines, they all
// utilize different data stored in the index. That said, it's almost as though
// we have one big giant engine with a bunch of attributes, we need to consider
// the big picture across all engines, and then whatever each individual engine
// uses will be integrated within the fingerprint.
var fingerprint={// The fingerprint is affected if admin searching is enabled; all post types must be indexed
adminSearch:_SEARCHWP_VARS.data.misc.admin_search,// When a post type is removed it means there's overhead in the index
postTypes:[],// Only enabled taxonomies are indexed
taxonomies:[],// Only chosen meta keys are indexed
metadata:[],// Rules affect what's in the index
rules:[]};// Find all enabled post types
var engines=this.engines;for(var engine in engines){if(engines.hasOwnProperty(engine)){var _loop=function _loop(postType){if(engines[engine].hasOwnProperty(postType)){if(engines[engine][postType].hasOwnProperty('enabled')){var enginePostType=engines[engine][postType];if(enginePostType.enabled){fingerprint.postTypes.push(postType);// Find all taxonomies with a weight > 0
__WEBPACK_IMPORTED_MODULE_3_keyfinder___default()(enginePostType,'tax').forEach(function(postTypeTaxonomies){for(var taxonomy in postTypeTaxonomies){if(postTypeTaxonomies.hasOwnProperty(taxonomy)){// If the weight is zero, it's not part of the fingerprint because it won't be indexed
if(postTypeTaxonomies[taxonomy]>0){fingerprint.taxonomies.push(postType+'_'+taxonomy);}}}});// Find all metakeys
__WEBPACK_IMPORTED_MODULE_3_keyfinder___default()(enginePostType,'cf').forEach(function(postTypeMetakeyPairs){for(var metakeyPair in postTypeMetakeyPairs){if(postTypeMetakeyPairs.hasOwnProperty(metakeyPair)){fingerprint.metadata.push(postType+'_'+postTypeMetakeyPairs[metakeyPair].metakey);}}});// Find all rules
__WEBPACK_IMPORTED_MODULE_3_keyfinder___default()(enginePostType,'options').forEach(function(postTypeOptions){for(var option in postTypeOptions){if(postTypeOptions.hasOwnProperty(option)){if(0===option.indexOf('limit_to_')||0===option.indexOf('exclude_')){fingerprint.rules.push(postType+'_'+option);}}}});}}}};for(var postType in engines[engine]){_loop(postType);}}}// There will be redundancies here, but duplicates interfere with the accuracy of the fingerprint
['postTypes','taxonomies','metadata','rules'].forEach(function(criteria){// We need each criteria to be index-wide across all engines
fingerprint[criteria]=__WEBPACK_IMPORTED_MODULE_4_array_unique___default()(fingerprint[criteria]);fingerprint[criteria].sort(function(a,b){if(a<b){return-1;}else if(a>b){return 1;}return 0;});});return fingerprint;}},created:function created(){// When a taxonomy or custom field (across all engines) has been added
// or removed we need to rebuild the index, because only those taxonomies
// or custom fields are indexed so we're going to listen for those events
// and determine whether the index needs to be rebuilt for accuracy.
// Given that, every engine needs to listen to this event and when observed
// all engines must report their engine configuration for further evaluation.
var self=this;// We need an initial fingerprint for comparison
self.initialFingerprint=this.generateFingerprint();self.fingerprint=this.initialFingerprint;__WEBPACK_IMPORTED_MODULE_1__EventBus_js__["a"/* EventBus */].$on('indexReset',function(){self.dirtyIndex=false;// We also need to tell the root that the index is no longer dirty
__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].set(self.$root.$data.misc,'index_dirty',false);});// Rebuild the index after database table recreation.
__WEBPACK_IMPORTED_MODULE_1__EventBus_js__["a"/* EventBus */].$on('databaseTablesRecreated',function(){self.resetIndex();});}};/***/},/* 12 *//***/function(module,exports){var charenc={// UTF-8 encoding
utf8:{// Convert a string to a byte array
stringToBytes:function stringToBytes(str){return charenc.bin.stringToBytes(unescape(encodeURIComponent(str)));},// Convert a byte array to a string
bytesToString:function bytesToString(bytes){return decodeURIComponent(escape(charenc.bin.bytesToString(bytes)));}},// Binary encoding
bin:{// Convert a string to a byte array
stringToBytes:function stringToBytes(str){for(var bytes=[],i=0;i<str.length;i++){bytes.push(str.charCodeAt(i)&0xFF);}return bytes;},// Convert a byte array to a string
bytesToString:function bytesToString(bytes){for(var str=[],i=0;i<bytes.length;i++){str.push(String.fromCharCode(bytes[i]));}return str.join('');}}};module.exports=charenc;/***/},/* 13 *//***/function(module,exports,__webpack_require__){/*!
 *   keyfinder - v1.0.0
 *   Deep search for keys in objects and arrays and pluck their respective values.
 *   https://github.com/simon-johansson/keyfinder
 *   by Simon Johansson <mail@simon-johansson.com>
 *   MIT License
 */(function(){var arrayify,_find,isArray,isFunction,isObjectOrArray;isObjectOrArray=__webpack_require__(46);isArray=__webpack_require__(47);isFunction=__webpack_require__(48);arrayify=function arrayify(obj){var key,_results;_results=[];for(key in obj){_results.push(key);}return _results;};_find=function find(haystack,needle,memo){var key,parent,val,_i,_len,_ref;if(memo==null){memo=[];}if(needle&&isObjectOrArray(haystack)){if(needle in haystack){memo.push(haystack[needle]);}_ref=arrayify(haystack);for(_i=0,_len=_ref.length;_i<_len;_i++){key=_ref[_i];val=haystack[key];if(isFunction(needle)){parent=isArray(haystack)?"array":"object";needle(key,val,parent);}if(isObjectOrArray(val)){_find(val,needle,memo);}}}return memo;};module.exports=function(obj,key){return _find(obj,key);};}).call(this);/***/},/* 14 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0_vue__=__webpack_require__(3);/* harmony import */var __WEBPACK_IMPORTED_MODULE_1__EventBus_js__=__webpack_require__(4);/* harmony import */var __WEBPACK_IMPORTED_MODULE_2__Message_vue__=__webpack_require__(6);//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */__webpack_exports__["a"]={name:'SearchwpEnvironmentCheck',components:{'searchwp-message':__WEBPACK_IMPORTED_MODULE_2__Message_vue__["a"/* default */]},methods:{recreateDatabaseTables:function recreateDatabaseTables(){var _this10=this;__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].SearchwpRecreateDatabaseTables().then(function(response){_this10.validDatabase=true;// EventBus.$emit('databaseTablesRecreated');
window.location.reload();}).catch(function(response){this.validDatabase=false;alert('ERROR SEARCHWPRECREATEDBTABLES');});}},data:function data(){return{basicAuth:false,validDatabase:_SEARCHWP_VARS.data.misc.valid_db,i18n:{basicAuth:_SEARCHWP_VARS.i18n.basic_auth_note,databaseTablesMissing:_SEARCHWP_VARS.i18n.database_tables_missing,recreateTables:_SEARCHWP_VARS.i18n.recreate_tables}};},created:function created(){var _this11=this;__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].SearchwpCheckBasicAuth().then(function(response){_this11.basicAuth=response;}).catch(function(response){alert('Error HTTPBASICAUTHCHECK');});}};/***/},/* 15 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */__webpack_exports__["a"]={name:'SearchwpMessage',data:function data(){return{i18n:{moreInfo:_SEARCHWP_VARS.i18n.more_info}};},props:{type:{type:String,default:'notice',required:false},moreInfo:{type:String,default:'',required:false},action:{type:Object,default:function _default(){return{target:'#',text:''};},required:false}}};/***/},/* 16 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0_vue__=__webpack_require__(3);/* harmony import */var __WEBPACK_IMPORTED_MODULE_1__EventBus_js__=__webpack_require__(4);/* harmony import */var __WEBPACK_IMPORTED_MODULE_2_md5__=__webpack_require__(7);/* harmony import */var __WEBPACK_IMPORTED_MODULE_2_md5___default=__webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_md5__);/* harmony import */var __WEBPACK_IMPORTED_MODULE_3_slugify__=__webpack_require__(60);/* harmony import */var __WEBPACK_IMPORTED_MODULE_3_slugify___default=__webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_slugify__);/* harmony import */var __WEBPACK_IMPORTED_MODULE_4_vue_slider_component__=__webpack_require__(61);/* harmony import */var __WEBPACK_IMPORTED_MODULE_4_vue_slider_component___default=__webpack_require__.n(__WEBPACK_IMPORTED_MODULE_4_vue_slider_component__);/* harmony import */var __WEBPACK_IMPORTED_MODULE_5__Remove_vue__=__webpack_require__(62);/* harmony import */var __WEBPACK_IMPORTED_MODULE_6__Button_vue__=__webpack_require__(8);/* harmony import */var __WEBPACK_IMPORTED_MODULE_7__InputText_vue__=__webpack_require__(69);/* harmony import */var __WEBPACK_IMPORTED_MODULE_8__InputCheckbox_vue__=__webpack_require__(73);/* harmony import */var __WEBPACK_IMPORTED_MODULE_9__Dropdown_vue__=__webpack_require__(81);/* harmony import */var __WEBPACK_IMPORTED_MODULE_10__InputTaxonomyTerms_vue__=__webpack_require__(87);/* harmony import */var __WEBPACK_IMPORTED_MODULE_11__Message_vue__=__webpack_require__(6);//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */__webpack_exports__["a"]={name:'SearchwpEngine',components:{'searchwp-slider':__WEBPACK_IMPORTED_MODULE_4_vue_slider_component___default.a,'searchwp-remove':__WEBPACK_IMPORTED_MODULE_5__Remove_vue__["a"/* default */],'searchwp-button':__WEBPACK_IMPORTED_MODULE_6__Button_vue__["a"/* default */],'searchwp-dropdown':__WEBPACK_IMPORTED_MODULE_9__Dropdown_vue__["a"/* default */],'searchwp-input-text':__WEBPACK_IMPORTED_MODULE_7__InputText_vue__["a"/* default */],'searchwp-input-checkbox':__WEBPACK_IMPORTED_MODULE_8__InputCheckbox_vue__["a"/* default */],'searchwp-input-taxonomy-terms':__WEBPACK_IMPORTED_MODULE_10__InputTaxonomyTerms_vue__["a"/* default */],'searchwp-message':__WEBPACK_IMPORTED_MODULE_11__Message_vue__["a"/* default */]},computed:{getName:function getName(){return this.model.name;},hasDocumentProperties:function hasDocumentProperties(){// If the meta key is present in the unusedMetakeys then it is not part of the engine
// Therefore, if the index is missing from the unusedMetakeys, then we have it here
var hasDocumentContent=-1===this.indexOfObjectPropertyValue(this.unusedMetakeys('attachment',true),'name','searchwp_content');var hasPdfMetadata=-1===this.indexOfObjectPropertyValue(this.unusedMetakeys('attachment',true),'name','searchwp_pdf_metadata');// If we have document content OR PDF metadata
return hasDocumentContent||hasPdfMetadata;},hasPostTypes:function hasPostTypes(){var postTypes=[];for(var postType in this.model.objects){if('searchwp_engine_label'!==postType&&this.model.objects[postType].enabled){postTypes.push(postType);}}return postTypes.length?true:false;}},methods:{excludedTermsUpdated:function excludedTermsUpdated(taxPostType,taxTaxonomy,taxSelectedTermsCount){if(taxSelectedTermsCount<1){// This taxonomy no longer applies
__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].delete(this.model.objects[taxPostType].options,'exclude_'+taxTaxonomy);}},limitedToTermsUpdated:function limitedToTermsUpdated(taxPostType,taxTaxonomy,taxSelectedTermsCount){if(taxSelectedTermsCount<1){// This taxonomy no longer applies
__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].delete(this.model.objects[taxPostType].options,'limit_to_'+taxTaxonomy);}},removeTaxonomy:function removeTaxonomy(postTypeName,taxonomyKey){// Reset the weight to zero (effectively removes it)
this.model.objects[postTypeName].weights.tax[this.attributes[postTypeName].taxonomies[taxonomyKey].name]=0;// This change influences the index
this.indexInfluenced();},indexInfluenced:function indexInfluenced(){// All engines are listening for this emit. When received each will report
// its model to listeners, which in turn can further process for analysis.
// This is just the trigger that kicks off the event overall, no data here.
__WEBPACK_IMPORTED_MODULE_1__EventBus_js__["a"/* EventBus */].$emit('indexInfluenced');},updateName:function updateName(){var newName=__WEBPACK_IMPORTED_MODULE_3_slugify___default()(this.label.replace(/ /gi,'_'),{replacement:'_',remove:/[^a-z0-9_]/gi,lower:true});// TODO: We should only need to update one model...
__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].set(this.model,'name',newName);__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].set(this.$parent.engines[this.ref],'searchwp_engine_label',this.label);},hasAttributes:function hasAttributes(postTypeName){var attributes=[];var model=this.model.objects[postTypeName];// Native
for(var supported in this.attributes[postTypeName].supports){// let supportsKey = this.attributes[ postTypeName ].supports[ supported ];
if(model.weights[supported]){attributes.push(supported);}}// Taxonomies
for(var taxonomyKey in this.attributes[postTypeName].taxonomies){var taxonomy=this.attributes[postTypeName].taxonomies[taxonomyKey].name;// We need to check if this taxonomy was added since the last save...
if(!this.model.objects[postTypeName].weights.tax||Array.isArray(this.model.objects[postTypeName].weights.tax)){__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].set(this.model.objects[postTypeName].weights,'tax',{});}if(model.weights.tax[taxonomy]){attributes.push(taxonomy);}}// Custom Fields
for(var metakeyKey in model.weights.cf){var metakey=model.weights.cf[metakeyKey].metakey;attributes.push(metakey);}return attributes.length?true:false;},removeEngine:function removeEngine(){__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].delete(this.$root.engines,this.ref);},removePostType:function removePostType(postTypeName){// Disable the post type
this.model.objects[postTypeName].enabled=false;// Remove custom fields to prevent index dirt
__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].set(this.model.objects[postTypeName].weights,'cf',{});// Remove taxonomies to prevent index dirt
if(this.model.objects[postTypeName].weights.tax){for(var taxonomyKey in this.attributes[postTypeName].taxonomies){var taxonomy=this.attributes[postTypeName].taxonomies[taxonomyKey].name;if(this.model.objects[postTypeName].weights.tax[taxonomy]){this.model.objects[postTypeName].weights.tax[taxonomy]=0;}}}// TODO: Rules are not removed (maybe leave them for convenience? In a way allows for 'undo')
// Also remove the flag that these details are being shown
if(this.details.indexOf(postTypeName)>-1){this.details.splice(this.details.indexOf(postTypeName),1);}},indexOfObjectPropertyValue:function indexOfObjectPropertyValue(array,property,value){for(var i=0;i<array.length;i+=1){if(array[i][property]===value){return i;}}return-1;},hasCustomFields:function hasCustomFields(postTypeName){// Attachments are a special use case because they have Custom Fields for
// document content and PDF metadata. If those are the only two Custom Fields
// added to the engine, there's technically no Custom Fields added
var theseCustomFields=this.model.objects[postTypeName].weights.cf&&Object.keys(this.model.objects[postTypeName].weights.cf).length>0;if(theseCustomFields&&'attachment'==postTypeName){// Check to see if the only Custom Fields are reserved SearchWP meta keys
var customFields=this.model.objects[postTypeName].weights.cf;var reserved=0;for(var customField in customFields){if(customFields[customField].metakey=='searchwp_content'){reserved++;}if(customFields[customField].metakey=='searchwp_pdf_metadata'){reserved++;}}if(Object.keys(this.model.objects[postTypeName].weights.cf).length<=reserved){theseCustomFields=false;}}return theseCustomFields;},removeMetakey:function removeMetakey(postType,metakey){__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].delete(this.model.objects[postType].weights.cf,metakey);},unusedPostTypes:function unusedPostTypes(){var unusedPostTypes=[];var enginePostTypes=this.model.objects;for(var enginePostType in enginePostTypes){if('searchwp_engine_label'!==enginePostType&&!enginePostTypes[enginePostType].enabled){unusedPostTypes.push({name:enginePostType,label:this.$root.$data.objects[enginePostType].label,value:enginePostType});}}return unusedPostTypes;},unusedNativeAttributes:function unusedNativeAttributes(postType){var attributes=[];var supports=this.$root.$data.objects[postType].supports;for(var key in supports){attributes.push(key);}var attributeWeights=this.model.objects[postType].weights;// A native attribute is unused if it has a weight of zero
return attributes.filter(function(attribute){return attributeWeights[attribute]==0;});},unusedTaxonomies:function unusedTaxonomies(postType){var taxonomies=this.$root.$data.objects[postType].taxonomies;var taxonomyWeights=this.model.objects[postType].weights.tax;// A taxonomy is unused if it has a weight of zero
return taxonomies.filter(function(taxonomy){return taxonomyWeights[taxonomy.name]==0;});},unusedMetakeys:function unusedMetakeys(postType,unfiltered){var metakeys=[];var availableMetakeys=this.$root.$data.objects[postType].meta_keys;var metakeyWeights=this.model.objects[postType].weights.cf;for(var i in availableMetakeys){var metakey=availableMetakeys[i];var applicable=true;for(var key in metakeyWeights){if(metakeyWeights[key].metakey==metakey){applicable=false;break;}}if(applicable){if(unfiltered||'searchwp_content'!==metakey&&'searchwp_pdf_metadata'!==metakey){metakeys.push({name:metakey,label:metakey,value:metakey});}}}return metakeys;},unusedAttributes:function unusedAttributes(postType){return this.unusedNativeAttributes(postType).length||this.unusedTaxonomies(postType).length||this.unusedMetakeys(postType,true).length;},// addContentType(selectedOption) {
//     // console.log(selectedOption);
// },
toggleDetails:function toggleDetails(postType){if(this.details.includes(postType)){this.details=this.details.filter(function(item){return item!=postType;});}else{this.details.push(postType);}},getTooltipText:function getTooltipText(value){// The maximum value can be filtered, so we need to define 'zones'
if(value<Math.ceil(this.weightMax*0.02)){return _SEARCHWP_VARS.i18n.minimum+' ('+value+')';}if(value<Math.ceil(this.weightMax*0.3)){return _SEARCHWP_VARS.i18n.a_little+' ('+value+')';}if(value<Math.ceil(this.weightMax*0.65)){return _SEARCHWP_VARS.i18n.average+' ('+value+')';}if(value<Math.ceil(this.weightMax*0.99)){return _SEARCHWP_VARS.i18n.a_lot+' ('+value+')';}return _SEARCHWP_VARS.i18n.maximum+' ('+value+')';}},data:function data(){return{model:{name:this.name,objects:{}},dirtyIndex:false,ref:this.name,label:_SEARCHWP_VARS.i18n.default,editingLabel:false,attributes:this.$root.$data.objects,stemming_supported:this.$root.$data.stemming_supported,weightMax:parseInt(_SEARCHWP_VARS.data.misc.max_weight,10),i18n:{addAttribute:_SEARCHWP_VARS.i18n.add_attribute,addContentType:_SEARCHWP_VARS.i18n.add_content_type,addLimitExcludeRule:_SEARCHWP_VARS.i18n.add_limit_exclude_rule,addPostType:_SEARCHWP_VARS.i18n.add_post_type,adminEngineNote:_SEARCHWP_VARS.i18n.admin_engine_note,anyCustomField:_SEARCHWP_VARS.i18n.any_custom_field,assignWeightTo:_SEARCHWP_VARS.i18n.assign_weight_to,attribute:_SEARCHWP_VARS.i18n.attribute,attributeResultsTo:_SEARCHWP_VARS.i18n.attribute_results_to,attribution:_SEARCHWP_VARS.i18n.attribution,choose:_SEARCHWP_VARS.i18n.choose,commaSeparatedIds:_SEARCHWP_VARS.i18n.comma_separated_ids,contentType:_SEARCHWP_VARS.i18n.content_type,customField:_SEARCHWP_VARS.i18n.custom_field,customFields:_SEARCHWP_VARS.i18n.custom_fields,default:_SEARCHWP_VARS.i18n.default,defaultEngineNote:_SEARCHWP_VARS.i18n.default_engine_note,deleteEngine:_SEARCHWP_VARS.i18n.delete_engine,documentContent:_SEARCHWP_VARS.i18n.document_content,documentProperties:_SEARCHWP_VARS.i18n.document_properties,done:_SEARCHWP_VARS.i18n.done,engineNote:_SEARCHWP_VARS.i18n.engine_note,engineNoteNone:_SEARCHWP_VARS.i18n.engine_note_none,exclude:_SEARCHWP_VARS.i18n.exclude,excluded:_SEARCHWP_VARS.i18n.excluded,excludedIds:_SEARCHWP_VARS.i18n.excluded_ids,limitTo:_SEARCHWP_VARS.i18n.limit_to,limitedTo:_SEARCHWP_VARS.i18n.limited_to,note:_SEARCHWP_VARS.i18n.note,notAvailableNoIndex:_SEARCHWP_VARS.i18n.not_available_no_index,options:_SEARCHWP_VARS.i18n.options,pdfMetadata:_SEARCHWP_VARS.i18n.pdf_metadata,rules:_SEARCHWP_VARS.i18n.rules,singlePostId:_SEARCHWP_VARS.i18n.single_post_id,statistics:_SEARCHWP_VARS.i18n.statistics,taxonomy:_SEARCHWP_VARS.i18n.taxonomy,taxonomies:_SEARCHWP_VARS.i18n.taxonomies,transferWeightTo:_SEARCHWP_VARS.i18n.transfer_weight_to,transferWeightToParent:_SEARCHWP_VARS.i18n.transfer_weight_to_parent,useKeywordStem:_SEARCHWP_VARS.i18n.use_keyword_stem,weight:_SEARCHWP_VARS.i18n.weight,weightMultiplier:_SEARCHWP_VARS.i18n.weight_multiplier,weightAssignment:_SEARCHWP_VARS.i18n.weight_assignment,weightTransfer:_SEARCHWP_VARS.i18n.weight_transfer,withoutAttributesNoResults:_SEARCHWP_VARS.i18n.without_attributes_no_results},details:[]};},props:{settings:{type:Object,default:{},required:false},name:{type:String,default:'default',required:true},isAdminEngine:{type:Boolean,default:false}},created:function created(){// We need to prep the model to match the existing format
for(var key in this.settings){if('searchwp_engine_label'==key){this.label=this.settings[key];continue;}this.model.objects[key]=this.settings[key];}// Because of the way the legacy engine config is stored, if certain
// options were not set, they're missing, but we need them in place
for(var key in this.model.objects){if(this.model.objects[key].options&&!this.model.objects[key].options.hasOwnProperty('stem')){this.model.objects[key].options.stem=false;}}// In other cases the value is "0" (string) when we in fact want an empty string
for(var key in this.model.objects){if(!this.model.objects[key].options){break;}if(!this.model.objects[key].options.hasOwnProperty('stem')){this.model.objects[key].options.stem=false;}else{if('0'===this.model.objects[key].options.stem){this.model.objects[key].options.stem=false;}else{this.model.objects[key].options.stem=!!this.model.objects[key].options.stem;}}for(var option in this.model.objects[key].options){if(this.model.objects[key].options[option]==='0'){this.model.objects[key].options[option]='';}}}if(!this.label){this.label=this.i18n.default;}// If we're creating a new supplemental engine, the name is going
// to be a meaningless hash, so let's make that better
if(this.model.name.substring(0,21)=='searchwp_engine_hash_'){this.updateName();}}};/***/},/* 17 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";//
//
//
//
//
//
//
/* harmony default export */__webpack_exports__["a"]={name:'SearchwpRemove',data:function data(){return{};},props:{text:{type:String,default:_SEARCHWP_VARS.i18n.remove,required:false},icon:{type:Boolean,default:false}}};/***/},/* 18 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0_vue__=__webpack_require__(3);//
//
//
//
//
//
//
//
//
/* harmony default export */__webpack_exports__["a"]={name:'SearchwpButton',props:{label:{type:String,required:true},icon:{type:String,required:false}},methods:{buttonClick:function buttonClick(){this.$emit('buttonClick');}}};/***/},/* 19 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */__webpack_exports__["a"]={name:'SearchwpInputText',props:{value:{type:[String,Number],default:''},label:{type:String,default:''},placeholder:{type:String,default:''}}};/***/},/* 20 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__Tooltip_vue__=__webpack_require__(76);//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */__webpack_exports__["a"]={name:'SearchwpInputCheckbox',components:{'searchwp-tooltip':__WEBPACK_IMPORTED_MODULE_0__Tooltip_vue__["a"/* default */]},data:function data(){return{val:this['checked']};},model:{prop:'checked',event:'change'},props:{checked:{type:Boolean},label:{type:String,default:''},tooltip:{type:String,default:''},disabled:{type:Boolean,default:false}},watch:{val:function val(value){this.$emit('change',value);}}};/***/},/* 21 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0_vue__=__webpack_require__(3);//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */__webpack_exports__["a"]={props:{content:String}};/***/},/* 22 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0_vue__=__webpack_require__(3);/* harmony import */var __WEBPACK_IMPORTED_MODULE_1_md5__=__webpack_require__(7);/* harmony import */var __WEBPACK_IMPORTED_MODULE_1_md5___default=__webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_md5__);/* harmony import */var __WEBPACK_IMPORTED_MODULE_2__EventBus_js__=__webpack_require__(4);/* harmony import */var __WEBPACK_IMPORTED_MODULE_3__Button_vue__=__webpack_require__(8);//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */__webpack_exports__["a"]={name:'SearchwpDropdown',components:{'searchwp-button':__WEBPACK_IMPORTED_MODULE_3__Button_vue__["a"/* default */]},methods:{numberOfExcludedPostTypes:function numberOfExcludedPostTypes(){return Object.keys(this.$root.misc.excluded_from_search).length;},isWithoutCustomField:function isWithoutCustomField(metakey){return-1!==this.$parent.indexOfObjectPropertyValue(this.getUnusedMetakeys(true),'name',metakey);},getUnusedPostTypes:function getUnusedPostTypes(){var unusedPostTypes=[];var enginePostTypes=this.$parent.model.objects;for(var enginePostType in enginePostTypes){if('searchwp_engine_label'!==enginePostType&&!enginePostTypes[enginePostType].enabled){unusedPostTypes.push({name:enginePostType,label:this.$root.$data.objects[enginePostType].label,value:enginePostType});}}return unusedPostTypes;},getUnusedNativeAttributes:function getUnusedNativeAttributes(){// The native attributes for this post type are what the post type supports as defined in the root
// but multiselect expects a specifically formated array of objects, so we'll need to adapt
var attributes=[];var supports=this.$root.$data.objects[this.postType].supports;for(var key in supports){attributes.push({name:key,label:supports[key],value:key});}var attributeWeights=this.$parent.model.objects[this.postType].weights;// A native attribute is unused if it has a weight of zero
return attributes.filter(function(attribute){return attributeWeights[attribute.name]==0;});},getUnusedTaxonomies:function getUnusedTaxonomies(){var taxonomies=this.$root.$data.objects[this.postType].taxonomies;var taxonomyWeights=this.$parent.model.objects[this.postType].weights.tax;// A taxonomy is unused if it has a weight of zero *or if it is missing (meaning it was added since the last engine save)*
return taxonomies.filter(function(taxonomy){return!taxonomyWeights.hasOwnProperty(taxonomy.name)||taxonomyWeights[taxonomy.name]==0;});},getUnusedRuleTaxonomies:function getUnusedRuleTaxonomies(ruleType){var taxonomies=this.$root.$data.objects[this.postType].taxonomies;var engineOptions=this.$parent.model.objects[this.postType].options;// A taxonomy is unused if there is no {ruleType} property for it
return taxonomies.filter(function(taxonomy){return!engineOptions.hasOwnProperty(ruleType+'_'+taxonomy.name);});},getUnusedMetakeys:function getUnusedMetakeys(unfiltered,grouped){var metakeys=[];var availableMetakeys=this.$root.$data.objects[this.postType].meta_keys;var metakeyWeights=this.$parent.model.objects[this.postType].weights.cf;for(var i in availableMetakeys){var metakey=availableMetakeys[i];var applicable=true;for(var key in metakeyWeights){if(metakeyWeights[key].metakey==metakey){applicable=false;break;}}if(applicable){if(unfiltered||'searchwp_content'!==metakey&&'searchwp_pdf_metadata'!==metakey){metakeys.push({name:metakey,label:metakey==='searchwpcfdefault'?this.i18n.anyCustomField+' ★':metakey,value:metakey});}}}if(metakeys.length&&grouped){metakeys=this.groupMetakeys(metakeys);}return metakeys;},groupMetakeys:function groupMetakeys(metakeys){// This method formats meta keys into the appropriate groups. Added in 3.0.
if(!metakeys.length){return metakeys;}var globalAnyCustomFieldInUse=false;var metakeyWeights=this.$parent.model.objects[this.postType].weights.cf;if(metakeyWeights){for(var weight in metakeyWeights){if(metakeyWeights.hasOwnProperty(weight)){if('searchwpcfdefault'===metakeyWeights[weight].metakey){globalAnyCustomFieldInUse=true;}}}}// We're giving 'Any Custom Field' preferential treatment if it's not in use.
var groupedKeys=globalAnyCustomFieldInUse?[]:['searchwpcfdefault'];// The default is going to be the meta groups themselves.
// Again we're giving 'Any Custom Field' preferential treatment if it's not in use.
var grouped=globalAnyCustomFieldInUse?[]:[{label:'Global',metakeys:[_defineProperty({name:'searchwpcfdefault',label:this.i18n.anyCustomField+' ★'},'name','searchwpcfdefault')]}];for(var metaGroupIndex in this.metaGroups){if(this.metaGroups.hasOwnProperty(metaGroupIndex)){var metaGroup=this.metaGroups[metaGroupIndex];grouped.push({label:metaGroup.label,metakeys:metaGroup.metakeys.map(function(metakey){return{name:metakey,label:metakey,value:metakey};})});// Mark these keys as grouped/used.
groupedKeys=groupedKeys.concat(metaGroup.metakeys);}}// We're going to put into the Core group every key that has NOT been used in a custom group.
grouped.push({label:'Core',metakeys:metakeys.filter(function(metakey){return groupedKeys.indexOf(metakey.name)<0;})});return grouped;},showDetails:function showDetails(details){// Show the multiselect
this.showing=details;},addNativeContentType:function addNativeContentType(attribute){this.visible=!this.visible;this.$parent.model.objects[this.postType].weights[attribute.name]=1;// If there are no unused attributes left, there's no reason to continue showing anything
if(this.getUnusedNativeAttributes().length==0){this.showing='';}},addTaxonomy:function addTaxonomy(taxonomy){this.visible=!this.visible;// There's a chance that this is the first taxonomy, so we need to make sure the model is set up
if(!this.$parent.model.objects[this.postType].weights.tax||Array.isArray(this.$parent.model.objects[this.postType].weights.tax)){__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].set(this.$parent.model.objects[this.postType].weights,'tax',{});}// There's a chance that the taxnomy doesn't exist in the model yet (e.g. when
// a taxonomy has been added since the last engine save)
if(!this.$parent.model.objects[this.postType].weights.tax.hasOwnProperty(taxonomy.name)){__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].set(this.$parent.model.objects[this.postType].weights.tax,taxonomy.name,1);}else{this.$parent.model.objects[this.postType].weights.tax[taxonomy.name]=1;}// If there are no unused taxonomies left, there's no reason to continue showing anything
if(this.getUnusedTaxonomies().length==0){this.showing='';}// This change influences the index
this.indexInfluenced();},indexInfluenced:function indexInfluenced(){// All engines are listening for this emit. When received each will report
// its model to listeners, which in turn can further process for analysis.
// This is just the trigger that kicks off the event overall, no data here.
__WEBPACK_IMPORTED_MODULE_2__EventBus_js__["a"/* EventBus */].$emit('indexInfluenced');},addExcludedTaxonomy:function addExcludedTaxonomy(taxonomy){this.visible=!this.visible;__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].set(this.$parent.model.objects[this.postType].options,'exclude_'+taxonomy.name,[]);if(this.getUnusedRuleTaxonomies('exclude').length==0){this.showing='';}},addLimitedToTaxonomy:function addLimitedToTaxonomy(taxonomy){this.visible=!this.visible;__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].set(this.$parent.model.objects[this.postType].options,'limit_to_'+taxonomy.name,[]);if(this.getUnusedRuleTaxonomies('limit_to').length==0){this.showing='';}},addPostType:function addPostType(postType){__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].set(this.$parent.model.objects[postType],'enabled',true);},addMetakeyRecord:function addMetakeyRecord(metakey,passive){var modelKeyHash='swppv'+__WEBPACK_IMPORTED_MODULE_1_md5___default()(this.postType+metakey);if(!this.$parent.model.objects[this.postType].weights.cf){__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].set(this.$parent.model.objects[this.postType].weights,'cf',{});}// Due to limitations of JavaScript, Vue cannot observe object property changes, so this:
__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].set(this.$parent.model.objects[this.postType].weights.cf,modelKeyHash,{metakey:metakey,weight:1});// This change influences the index
this.indexInfluenced();},addMetakey:function addMetakey(metakey,id){this.visible=!this.visible;this.addMetakeyRecord(metakey.name);// If there are no unused Custom Fields left, there's no reason to continue showing anything
if(this.getUnusedMetakeys().length==0){this.showing='';}}},computed:{excludedPostTypes:function excludedPostTypes(){return this.$root.misc.excluded_from_search;},metaGroups:function metaGroups(){var source=this.$root.objects[this.postType].meta_groups;var metakeyWeights=this.$parent.model.objects[this.postType].weights.cf;if(!metakeyWeights){return source;}var keysInUse=[];for(var weight in metakeyWeights){if(metakeyWeights.hasOwnProperty(weight)){keysInUse.push(metakeyWeights[weight].metakey);}}// Filter out any meta group key that's been used.
var metaGroups={};for(var groupIndex in source){if(source.hasOwnProperty(groupIndex)){var group=source[groupIndex];metaGroups[groupIndex]={label:group.label,metakeys:group.metakeys.filter(function(metakey){return keysInUse.indexOf(metakey)<0;})};}}return metaGroups;}},data:function data(){return{showing:'',i18n:{addExclusion:_SEARCHWP_VARS.i18n.add_exclusion,addLimiter:_SEARCHWP_VARS.i18n.add_limiter,anyCustomField:_SEARCHWP_VARS.i18n.any_custom_field,chooseNativeAttribute:_SEARCHWP_VARS.i18n.choose_native_attribute,chooseCustomField:_SEARCHWP_VARS.i18n.choose_custom_field,chooseTaxonomy:_SEARCHWP_VARS.i18n.choose_taxonomy,customField:_SEARCHWP_VARS.i18n.custom_field,documentContent:_SEARCHWP_VARS.i18n.document_content,documentProperties:_SEARCHWP_VARS.i18n.document_properties,done:_SEARCHWP_VARS.i18n.done,excludeByTaxonomy:_SEARCHWP_VARS.i18n.exclude_by_taxonomy,excluded:_SEARCHWP_VARS.i18n.excluded,excludedFromSearch:_SEARCHWP_VARS.i18n.excluded_from_search,limitByTaxonomy:_SEARCHWP_VARS.i18n.limit_by_taxonomy,pdfMetadata:_SEARCHWP_VARS.i18n.pdf_metadata,nativeAttribute:_SEARCHWP_VARS.i18n.native_attribute,search:_SEARCHWP_VARS.i18n.search,taxonomy:_SEARCHWP_VARS.i18n.taxonomy}};},props:{postType:{type:String,default:'post',required:false},type:{type:String,default:'contentTypes',required:true},buttonText:{type:String,required:true},position:{type:String,default:'above',required:false}}};/***/},/* 23 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0_debounce__=__webpack_require__(88);/* harmony import */var __WEBPACK_IMPORTED_MODULE_0_debounce___default=__webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_debounce__);/* harmony import */var __WEBPACK_IMPORTED_MODULE_1_vue__=__webpack_require__(3);//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */__webpack_exports__["a"]={name:'SearchwpInputTaxonomyTerms',data:function data(){return{selectedTerms:this.$parent.model.objects[this.postType].options[this.mode+'_'+this.taxonomy],terms:[],isLoading:false,open:false,closed:true,i18n:{findTerms:_SEARCHWP_VARS.i18n.find_terms,noTermsFound:_SEARCHWP_VARS.i18n.no_terms_found}};},methods:{updateTerms:function updateTerms(){__WEBPACK_IMPORTED_MODULE_1_vue__["a"/* default */].set(this.$parent.model.objects[this.postType].options,this.mode+'_'+this.taxonomy,this.selectedTerms);this.$emit('termsUpdated',this.postType,this.taxonomy,this.selectedTerms.length);},asyncFind:__WEBPACK_IMPORTED_MODULE_0_debounce___default()(function(query){var _this12=this;this.isLoading=true;if(''==query){this.isLoading=false;return;}__WEBPACK_IMPORTED_MODULE_1_vue__["a"/* default */].SearchwpSearchTaxonomyTerms(query,this.taxonomy,this.postType).then(function(response){_this12.terms=response;_this12.isLoading=false;}).catch(function(response){this.isLoading=false;});},500),clearAll:function clearAll(){this.selectedTerms=[];}},created:function created(){if(this.terms.length){this.selectedTerms=this.terms;}},props:{label:{type:String,default:'',required:true},taxonomy:{type:String,required:true},postType:{type:String,required:true},mode:{type:String,default:'exclude',required:true},value:{type:Object,default:function _default(){return{};},required:false}}};/***/},/* 24 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0_vue__=__webpack_require__(3);/* harmony import */var __WEBPACK_IMPORTED_MODULE_1_keyfinder__=__webpack_require__(13);/* harmony import */var __WEBPACK_IMPORTED_MODULE_1_keyfinder___default=__webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_keyfinder__);/* harmony import */var __WEBPACK_IMPORTED_MODULE_2__EventBus_js__=__webpack_require__(4);/* harmony import */var __WEBPACK_IMPORTED_MODULE_3__ProgressBar_vue__=__webpack_require__(95);/* harmony import */var __WEBPACK_IMPORTED_MODULE_4__Message_vue__=__webpack_require__(6);/* harmony import */var __WEBPACK_IMPORTED_MODULE_5__Confirm_vue__=__webpack_require__(99);/* harmony import */var __WEBPACK_IMPORTED_MODULE_6__Notice_vue__=__webpack_require__(103);/* harmony import */var __WEBPACK_IMPORTED_MODULE_7_vue_simple_spinner__=__webpack_require__(107);/* harmony import */var __WEBPACK_IMPORTED_MODULE_7_vue_simple_spinner___default=__webpack_require__.n(__WEBPACK_IMPORTED_MODULE_7_vue_simple_spinner__);//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */__webpack_exports__["a"]={name:'SearchwpIndexStats',components:{'searchwp-progress-bar':__WEBPACK_IMPORTED_MODULE_3__ProgressBar_vue__["a"/* default */],'searchwp-message':__WEBPACK_IMPORTED_MODULE_4__Message_vue__["a"/* default */],Confirm:__WEBPACK_IMPORTED_MODULE_5__Confirm_vue__["a"/* default */],Spinner:__WEBPACK_IMPORTED_MODULE_7_vue_simple_spinner___default.a,'searchwp-notice':__WEBPACK_IMPORTED_MODULE_6__Notice_vue__["a"/* default */]},methods:{resetIndex:function resetIndex(){var _this13=this;this.dirtyIndex=false;this.progress=0;this.indexed=0;this.unindexed='-';this.mainRowCount=0;this.lastActivity=this.i18n.rightNow;this.indexIsResetting=true;__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].SearchwpResetIndex().then(function(response){_this13.indexIsResetting=false;_this13.indexIsReset=true;__WEBPACK_IMPORTED_MODULE_2__EventBus_js__["a"/* EventBus */].$emit('indexReset');}).catch(function(response){alert('ERROR SEARCHWPINDEXRESET');});},updateIndexStats:function updateIndexStats(){var jumpStart=false;var self=this;if(self.timeSinceChange>30&&!self.waiting){jumpStart=true;}__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].SearchwpGetIndexStats(jumpStart).then(function(response){if(self.progress<100&&self.progress==response.progress){self.timeSinceChange=self.timeSinceChange+5;}else{self.timeSinceChange=0;}self.progress=response.progress;self.lastActivity=response.last_activity;self.indexed=response.done;self.unindexed=response.remaining;self.mainRowCount=response.main_row_count;self.waiting=!!response.waiting;// Daisychain this call
setTimeout(function(){self.updateIndexStats();},5000);}).catch(function(response){// Logged out?
});}},created:function created(){var self=this;if(!_SEARCHWP_VARS.data.misc.alternate_indexer){self.updateIndexStats();}__WEBPACK_IMPORTED_MODULE_2__EventBus_js__["a"/* EventBus */].$on('enginesSaved',function(fingerprints){self.dirtyIndex=fingerprints.dirtyIndex;self.initialSettingsSaved=true;// Determine whether there are no enabled post types
var emptyEngines=true;__WEBPACK_IMPORTED_MODULE_1_keyfinder___default()(fingerprints.engines,'enabled').forEach(function(postTypeEnabled){if(postTypeEnabled){emptyEngines=false;}});self.emptyEngines=emptyEngines;});__WEBPACK_IMPORTED_MODULE_2__EventBus_js__["a"/* EventBus */].$on('indexReset',function(){self.dirtyIndex=false;self.progress=0;self.indexed=0;self.unindexed='-';self.mainRowCount=0;self.lastActivity=self.i18n.rightNow;});},data:function data(){return{i18n:{autoScale:_SEARCHWP_VARS.i18n.auto_scale,indexProgress:_SEARCHWP_VARS.i18n.index_progress,lastActivity:_SEARCHWP_VARS.i18n.last_activity,indexDirty:_SEARCHWP_VARS.i18n.index_dirty,indexed:_SEARCHWP_VARS.i18n.indexed,unindexed:_SEARCHWP_VARS.i18n.unindexed,mainRowCount:_SEARCHWP_VARS.i18n.main_row_count,entries:_SEARCHWP_VARS.i18n.entries,rightNow:_SEARCHWP_VARS.i18n.right_now,rows:_SEARCHWP_VARS.i18n.rows,rebuildIndex:_SEARCHWP_VARS.i18n.rebuild_index,indexNote:_SEARCHWP_VARS.i18n.index_note},progress:_SEARCHWP_VARS.data.index_stats.progress,lastActivity:_SEARCHWP_VARS.data.index_stats.last_activity,timeSinceChange:0,indexed:_SEARCHWP_VARS.data.index_stats.done,unindexed:_SEARCHWP_VARS.data.index_stats.remaining,mainRowCount:_SEARCHWP_VARS.data.index_stats.main_row_count,waiting:_SEARCHWP_VARS.data.index_stats.waiting,dirtyIndex:false,emptyEngines:_SEARCHWP_VARS.data.misc.empty_engines,initialSettingsSaved:_SEARCHWP_VARS.data.misc.initial_settings_saved,indexIsReset:false,indexIsResetting:false};}};/***/},/* 25 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */__webpack_exports__["a"]={name:'SearchwpProgressBar',data:function data(){return{alternateIndexer:_SEARCHWP_VARS.data.misc.alternate_indexer};},props:{progress:{type:Number,default:0,required:true},disabled:{type:Boolean,default:false,required:true}}};/***/},/* 26 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0_vue__=__webpack_require__(3);/* harmony import */var __WEBPACK_IMPORTED_MODULE_1__Button_vue__=__webpack_require__(8);//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */__webpack_exports__["a"]={name:'SearchwpConfirm',components:{SearchwpButton:__WEBPACK_IMPORTED_MODULE_1__Button_vue__["a"/* default */]},props:{buttonLabel:{type:String,required:true},icon:{type:String,required:false},question:{type:String,default:'Are you sure?',required:false},confirm:{type:String,default:'Yes',required:false},placement:{type:String,default:'bottom',required:false}},methods:{confirmed:function confirmed(){this.$emit('confirmed');}},data:function data(){return{};}};/***/},/* 27 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0_vue__=__webpack_require__(3);//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */__webpack_exports__["a"]={name:'SearchwpNotice',props:{type:{type:String,default:'success',required:false},link:{type:String,required:false},linkText:{type:String,required:false},buttonText:{type:String,required:false},dismissable:{type:Boolean,required:false,default:false}},methods:{emitDismissed:function emitDismissed(){this.$emit('dismissed');},emitButtonClick:function emitButtonClick(){this.$emit('buttonClick');}},data:function data(){return{i18n:{dismiss:_SEARCHWP_VARS.i18n.dismiss}};}};/***/},/* 28 *//***/function(module,exports,__webpack_require__){module.exports=__webpack_require__(29);/***/},/* 29 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";Object.defineProperty(__webpack_exports__,"__esModule",{value:true});/* harmony import */var __WEBPACK_IMPORTED_MODULE_0_vue__=__webpack_require__(3);/* harmony import */var __WEBPACK_IMPORTED_MODULE_1_v_tooltip__=__webpack_require__(32);/* harmony import */var __WEBPACK_IMPORTED_MODULE_2_vue_multiselect__=__webpack_require__(33);/* harmony import */var __WEBPACK_IMPORTED_MODULE_2_vue_multiselect___default=__webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_vue_multiselect__);/* harmony import */var __WEBPACK_IMPORTED_MODULE_3__inc_Ajax__=__webpack_require__(34);/* harmony import */var __WEBPACK_IMPORTED_MODULE_4__SearchwpSettings_vue__=__webpack_require__(35);__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].use(__WEBPACK_IMPORTED_MODULE_3__inc_Ajax__["a"/* default */]);__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].use(__WEBPACK_IMPORTED_MODULE_1_v_tooltip__["a"/* default */]);__WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */].component("multiselect",__WEBPACK_IMPORTED_MODULE_2_vue_multiselect___default.a);new __WEBPACK_IMPORTED_MODULE_0_vue__["a"/* default */]({el:"#searchwp-settings",data:_SEARCHWP_VARS.data?_SEARCHWP_VARS.data:{},template:"<searchwp-settings/>",components:{"searchwp-settings":__WEBPACK_IMPORTED_MODULE_4__SearchwpSettings_vue__["a"/* default */]}});/***/},/* 30 *//***/function(module,exports,__webpack_require__){/* WEBPACK VAR INJECTION */(function(global){var scope=typeof global!=="undefined"&&global||typeof self!=="undefined"&&self||window;var apply=Function.prototype.apply;// DOM APIs, for completeness
exports.setTimeout=function(){return new Timeout(apply.call(setTimeout,scope,arguments),clearTimeout);};exports.setInterval=function(){return new Timeout(apply.call(setInterval,scope,arguments),clearInterval);};exports.clearTimeout=exports.clearInterval=function(timeout){if(timeout){timeout.close();}};function Timeout(id,clearFn){this._id=id;this._clearFn=clearFn;}Timeout.prototype.unref=Timeout.prototype.ref=function(){};Timeout.prototype.close=function(){this._clearFn.call(scope,this._id);};// Does not start the time, just sets up the members needed.
exports.enroll=function(item,msecs){clearTimeout(item._idleTimeoutId);item._idleTimeout=msecs;};exports.unenroll=function(item){clearTimeout(item._idleTimeoutId);item._idleTimeout=-1;};exports._unrefActive=exports.active=function(item){clearTimeout(item._idleTimeoutId);var msecs=item._idleTimeout;if(msecs>=0){item._idleTimeoutId=setTimeout(function onTimeout(){if(item._onTimeout)item._onTimeout();},msecs);}};// setimmediate attaches itself to the global object
__webpack_require__(31);// On some exotic environments, it's not clear which object `setimmediate` was
// able to install onto.  Search each possibility in the same order as the
// `setimmediate` library.
exports.setImmediate=typeof self!=="undefined"&&self.setImmediate||typeof global!=="undefined"&&global.setImmediate||this&&this.setImmediate;exports.clearImmediate=typeof self!=="undefined"&&self.clearImmediate||typeof global!=="undefined"&&global.clearImmediate||this&&this.clearImmediate;/* WEBPACK VAR INJECTION */}).call(exports,__webpack_require__(5));/***/},/* 31 *//***/function(module,exports,__webpack_require__){/* WEBPACK VAR INJECTION */(function(global,process){(function(global,undefined){"use strict";if(global.setImmediate){return;}var nextHandle=1;// Spec says greater than zero
var tasksByHandle={};var currentlyRunningATask=false;var doc=global.document;var registerImmediate;function setImmediate(callback){// Callback can either be a function or a string
if(typeof callback!=="function"){callback=new Function(""+callback);}// Copy function arguments
var args=new Array(arguments.length-1);for(var i=0;i<args.length;i++){args[i]=arguments[i+1];}// Store and register the task
var task={callback:callback,args:args};tasksByHandle[nextHandle]=task;registerImmediate(nextHandle);return nextHandle++;}function clearImmediate(handle){delete tasksByHandle[handle];}function run(task){var callback=task.callback;var args=task.args;switch(args.length){case 0:callback();break;case 1:callback(args[0]);break;case 2:callback(args[0],args[1]);break;case 3:callback(args[0],args[1],args[2]);break;default:callback.apply(undefined,args);break;}}function runIfPresent(handle){// From the spec: "Wait until any invocations of this algorithm started before this one have completed."
// So if we're currently running a task, we'll need to delay this invocation.
if(currentlyRunningATask){// Delay by doing a setTimeout. setImmediate was tried instead, but in Firefox 7 it generated a
// "too much recursion" error.
setTimeout(runIfPresent,0,handle);}else{var task=tasksByHandle[handle];if(task){currentlyRunningATask=true;try{run(task);}finally{clearImmediate(handle);currentlyRunningATask=false;}}}}function installNextTickImplementation(){registerImmediate=function registerImmediate(handle){process.nextTick(function(){runIfPresent(handle);});};}function canUsePostMessage(){// The test against `importScripts` prevents this implementation from being installed inside a web worker,
// where `global.postMessage` means something completely different and can't be used for this purpose.
if(global.postMessage&&!global.importScripts){var postMessageIsAsynchronous=true;var oldOnMessage=global.onmessage;global.onmessage=function(){postMessageIsAsynchronous=false;};global.postMessage("","*");global.onmessage=oldOnMessage;return postMessageIsAsynchronous;}}function installPostMessageImplementation(){// Installs an event handler on `global` for the `message` event: see
// * https://developer.mozilla.org/en/DOM/window.postMessage
// * http://www.whatwg.org/specs/web-apps/current-work/multipage/comms.html#crossDocumentMessages
var messagePrefix="setImmediate$"+Math.random()+"$";var onGlobalMessage=function onGlobalMessage(event){if(event.source===global&&typeof event.data==="string"&&event.data.indexOf(messagePrefix)===0){runIfPresent(+event.data.slice(messagePrefix.length));}};if(global.addEventListener){global.addEventListener("message",onGlobalMessage,false);}else{global.attachEvent("onmessage",onGlobalMessage);}registerImmediate=function registerImmediate(handle){global.postMessage(messagePrefix+handle,"*");};}function installMessageChannelImplementation(){var channel=new MessageChannel();channel.port1.onmessage=function(event){var handle=event.data;runIfPresent(handle);};registerImmediate=function registerImmediate(handle){channel.port2.postMessage(handle);};}function installReadyStateChangeImplementation(){var html=doc.documentElement;registerImmediate=function registerImmediate(handle){// Create a <script> element; its readystatechange event will be fired asynchronously once it is inserted
// into the document. Do so, thus queuing up the task. Remember to clean up once it's been called.
var script=doc.createElement("script");script.onreadystatechange=function(){runIfPresent(handle);script.onreadystatechange=null;html.removeChild(script);script=null;};html.appendChild(script);};}function installSetTimeoutImplementation(){registerImmediate=function registerImmediate(handle){setTimeout(runIfPresent,0,handle);};}// If supported, we should attach to the prototype of global, since that is where setTimeout et al. live.
var attachTo=Object.getPrototypeOf&&Object.getPrototypeOf(global);attachTo=attachTo&&attachTo.setTimeout?attachTo:global;// Don't get fooled by e.g. browserify environments.
if({}.toString.call(global.process)==="[object process]"){// For Node.js before 0.9
installNextTickImplementation();}else if(canUsePostMessage()){// For non-IE10 modern browsers
installPostMessageImplementation();}else if(global.MessageChannel){// For web workers, where supported
installMessageChannelImplementation();}else if(doc&&"onreadystatechange"in doc.createElement("script")){// For IE 6–8
installReadyStateChangeImplementation();}else{// For older browsers
installSetTimeoutImplementation();}attachTo.setImmediate=setImmediate;attachTo.clearImmediate=clearImmediate;})(typeof self==="undefined"?typeof global==="undefined"?this:global:self);/* WEBPACK VAR INJECTION */}).call(exports,__webpack_require__(5),__webpack_require__(9));/***/},/* 32 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* WEBPACK VAR INJECTION */(function(global){/* unused harmony export install *//* unused harmony export VTooltip *//* unused harmony export VClosePopover *//* unused harmony export VPopover *//* unused harmony export createTooltip *//* unused harmony export destroyTooltip *//**!
 * @fileOverview Kickass library to create and place poppers near their reference elements.
 * @version 1.14.3
 * @license
 * Copyright (c) 2016 Federico Zivolo and contributors
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */var isBrowser=typeof window!=='undefined'&&typeof document!=='undefined';var longerTimeoutBrowsers=['Edge','Trident','Firefox'];var timeoutDuration=0;for(var i=0;i<longerTimeoutBrowsers.length;i+=1){if(isBrowser&&navigator.userAgent.indexOf(longerTimeoutBrowsers[i])>=0){timeoutDuration=1;break;}}function microtaskDebounce(fn){var called=false;return function(){if(called){return;}called=true;window.Promise.resolve().then(function(){called=false;fn();});};}function taskDebounce(fn){var scheduled=false;return function(){if(!scheduled){scheduled=true;setTimeout(function(){scheduled=false;fn();},timeoutDuration);}};}var supportsMicroTasks=isBrowser&&window.Promise;/**
* Create a debounced version of a method, that's asynchronously deferred
* but called in the minimum time possible.
*
* @method
* @memberof Popper.Utils
* @argument {Function} fn
* @returns {Function}
*/var debounce=supportsMicroTasks?microtaskDebounce:taskDebounce;/**
 * Check if the given variable is a function
 * @method
 * @memberof Popper.Utils
 * @argument {Any} functionToCheck - variable to check
 * @returns {Boolean} answer to: is a function?
 */function isFunction(functionToCheck){var getType={};return functionToCheck&&getType.toString.call(functionToCheck)==='[object Function]';}/**
 * Get CSS computed property of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Eement} element
 * @argument {String} property
 */function getStyleComputedProperty(element,property){if(element.nodeType!==1){return[];}// NOTE: 1 DOM access here
var css=getComputedStyle(element,null);return property?css[property]:css;}/**
 * Returns the parentNode or the host of the element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} parent
 */function getParentNode(element){if(element.nodeName==='HTML'){return element;}return element.parentNode||element.host;}/**
 * Returns the scrolling parent of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} scroll parent
 */function getScrollParent(element){// Return body, `getScroll` will take care to get the correct `scrollTop` from it
if(!element){return document.body;}switch(element.nodeName){case'HTML':case'BODY':return element.ownerDocument.body;case'#document':return element.body;}// Firefox want us to check `-x` and `-y` variations as well
var _getStyleComputedProp=getStyleComputedProperty(element),overflow=_getStyleComputedProp.overflow,overflowX=_getStyleComputedProp.overflowX,overflowY=_getStyleComputedProp.overflowY;if(/(auto|scroll|overlay)/.test(overflow+overflowY+overflowX)){return element;}return getScrollParent(getParentNode(element));}var isIE11=isBrowser&&!!(window.MSInputMethodContext&&document.documentMode);var isIE10=isBrowser&&/MSIE 10/.test(navigator.userAgent);/**
 * Determines if the browser is Internet Explorer
 * @method
 * @memberof Popper.Utils
 * @param {Number} version to check
 * @returns {Boolean} isIE
 */function isIE(version){if(version===11){return isIE11;}if(version===10){return isIE10;}return isIE11||isIE10;}/**
 * Returns the offset parent of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} offset parent
 */function getOffsetParent(element){if(!element){return document.documentElement;}var noOffsetParent=isIE(10)?document.body:null;// NOTE: 1 DOM access here
var offsetParent=element.offsetParent;// Skip hidden elements which don't have an offsetParent
while(offsetParent===noOffsetParent&&element.nextElementSibling){offsetParent=(element=element.nextElementSibling).offsetParent;}var nodeName=offsetParent&&offsetParent.nodeName;if(!nodeName||nodeName==='BODY'||nodeName==='HTML'){return element?element.ownerDocument.documentElement:document.documentElement;}// .offsetParent will return the closest TD or TABLE in case
// no offsetParent is present, I hate this job...
if(['TD','TABLE'].indexOf(offsetParent.nodeName)!==-1&&getStyleComputedProperty(offsetParent,'position')==='static'){return getOffsetParent(offsetParent);}return offsetParent;}function isOffsetContainer(element){var nodeName=element.nodeName;if(nodeName==='BODY'){return false;}return nodeName==='HTML'||getOffsetParent(element.firstElementChild)===element;}/**
 * Finds the root node (document, shadowDOM root) of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} node
 * @returns {Element} root node
 */function getRoot(node){if(node.parentNode!==null){return getRoot(node.parentNode);}return node;}/**
 * Finds the offset parent common to the two provided nodes
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element1
 * @argument {Element} element2
 * @returns {Element} common offset parent
 */function findCommonOffsetParent(element1,element2){// This check is needed to avoid errors in case one of the elements isn't defined for any reason
if(!element1||!element1.nodeType||!element2||!element2.nodeType){return document.documentElement;}// Here we make sure to give as "start" the element that comes first in the DOM
var order=element1.compareDocumentPosition(element2)&Node.DOCUMENT_POSITION_FOLLOWING;var start=order?element1:element2;var end=order?element2:element1;// Get common ancestor container
var range=document.createRange();range.setStart(start,0);range.setEnd(end,0);var commonAncestorContainer=range.commonAncestorContainer;// Both nodes are inside #document
if(element1!==commonAncestorContainer&&element2!==commonAncestorContainer||start.contains(end)){if(isOffsetContainer(commonAncestorContainer)){return commonAncestorContainer;}return getOffsetParent(commonAncestorContainer);}// one of the nodes is inside shadowDOM, find which one
var element1root=getRoot(element1);if(element1root.host){return findCommonOffsetParent(element1root.host,element2);}else{return findCommonOffsetParent(element1,getRoot(element2).host);}}/**
 * Gets the scroll value of the given element in the given side (top and left)
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @argument {String} side `top` or `left`
 * @returns {number} amount of scrolled pixels
 */function getScroll(element){var side=arguments.length>1&&arguments[1]!==undefined?arguments[1]:'top';var upperSide=side==='top'?'scrollTop':'scrollLeft';var nodeName=element.nodeName;if(nodeName==='BODY'||nodeName==='HTML'){var html=element.ownerDocument.documentElement;var scrollingElement=element.ownerDocument.scrollingElement||html;return scrollingElement[upperSide];}return element[upperSide];}/*
 * Sum or subtract the element scroll values (left and top) from a given rect object
 * @method
 * @memberof Popper.Utils
 * @param {Object} rect - Rect object you want to change
 * @param {HTMLElement} element - The element from the function reads the scroll values
 * @param {Boolean} subtract - set to true if you want to subtract the scroll values
 * @return {Object} rect - The modifier rect object
 */function includeScroll(rect,element){var subtract=arguments.length>2&&arguments[2]!==undefined?arguments[2]:false;var scrollTop=getScroll(element,'top');var scrollLeft=getScroll(element,'left');var modifier=subtract?-1:1;rect.top+=scrollTop*modifier;rect.bottom+=scrollTop*modifier;rect.left+=scrollLeft*modifier;rect.right+=scrollLeft*modifier;return rect;}/*
 * Helper to detect borders of a given element
 * @method
 * @memberof Popper.Utils
 * @param {CSSStyleDeclaration} styles
 * Result of `getStyleComputedProperty` on the given element
 * @param {String} axis - `x` or `y`
 * @return {number} borders - The borders size of the given axis
 */function getBordersSize(styles,axis){var sideA=axis==='x'?'Left':'Top';var sideB=sideA==='Left'?'Right':'Bottom';return parseFloat(styles['border'+sideA+'Width'],10)+parseFloat(styles['border'+sideB+'Width'],10);}function getSize(axis,body,html,computedStyle){return Math.max(body['offset'+axis],body['scroll'+axis],html['client'+axis],html['offset'+axis],html['scroll'+axis],isIE(10)?html['offset'+axis]+computedStyle['margin'+(axis==='Height'?'Top':'Left')]+computedStyle['margin'+(axis==='Height'?'Bottom':'Right')]:0);}function getWindowSizes(){var body=document.body;var html=document.documentElement;var computedStyle=isIE(10)&&getComputedStyle(html);return{height:getSize('Height',body,html,computedStyle),width:getSize('Width',body,html,computedStyle)};}var classCallCheck=function classCallCheck(instance,Constructor){if(!(instance instanceof Constructor)){throw new TypeError("Cannot call a class as a function");}};var createClass=function(){function defineProperties(target,props){for(var i=0;i<props.length;i++){var descriptor=props[i];descriptor.enumerable=descriptor.enumerable||false;descriptor.configurable=true;if("value"in descriptor)descriptor.writable=true;Object.defineProperty(target,descriptor.key,descriptor);}}return function(Constructor,protoProps,staticProps){if(protoProps)defineProperties(Constructor.prototype,protoProps);if(staticProps)defineProperties(Constructor,staticProps);return Constructor;};}();var defineProperty=function defineProperty(obj,key,value){if(key in obj){Object.defineProperty(obj,key,{value:value,enumerable:true,configurable:true,writable:true});}else{obj[key]=value;}return obj;};var _extends=Object.assign||function(target){for(var i=1;i<arguments.length;i++){var source=arguments[i];for(var key in source){if(Object.prototype.hasOwnProperty.call(source,key)){target[key]=source[key];}}}return target;};/**
 * Given element offsets, generate an output similar to getBoundingClientRect
 * @method
 * @memberof Popper.Utils
 * @argument {Object} offsets
 * @returns {Object} ClientRect like output
 */function getClientRect(offsets){return _extends({},offsets,{right:offsets.left+offsets.width,bottom:offsets.top+offsets.height});}/**
 * Get bounding client rect of given element
 * @method
 * @memberof Popper.Utils
 * @param {HTMLElement} element
 * @return {Object} client rect
 */function getBoundingClientRect(element){var rect={};// IE10 10 FIX: Please, don't ask, the element isn't
// considered in DOM in some circumstances...
// This isn't reproducible in IE10 compatibility mode of IE11
try{if(isIE(10)){rect=element.getBoundingClientRect();var scrollTop=getScroll(element,'top');var scrollLeft=getScroll(element,'left');rect.top+=scrollTop;rect.left+=scrollLeft;rect.bottom+=scrollTop;rect.right+=scrollLeft;}else{rect=element.getBoundingClientRect();}}catch(e){}var result={left:rect.left,top:rect.top,width:rect.right-rect.left,height:rect.bottom-rect.top};// subtract scrollbar size from sizes
var sizes=element.nodeName==='HTML'?getWindowSizes():{};var width=sizes.width||element.clientWidth||result.right-result.left;var height=sizes.height||element.clientHeight||result.bottom-result.top;var horizScrollbar=element.offsetWidth-width;var vertScrollbar=element.offsetHeight-height;// if an hypothetical scrollbar is detected, we must be sure it's not a `border`
// we make this check conditional for performance reasons
if(horizScrollbar||vertScrollbar){var styles=getStyleComputedProperty(element);horizScrollbar-=getBordersSize(styles,'x');vertScrollbar-=getBordersSize(styles,'y');result.width-=horizScrollbar;result.height-=vertScrollbar;}return getClientRect(result);}function getOffsetRectRelativeToArbitraryNode(children,parent){var fixedPosition=arguments.length>2&&arguments[2]!==undefined?arguments[2]:false;var isIE10=isIE(10);var isHTML=parent.nodeName==='HTML';var childrenRect=getBoundingClientRect(children);var parentRect=getBoundingClientRect(parent);var scrollParent=getScrollParent(children);var styles=getStyleComputedProperty(parent);var borderTopWidth=parseFloat(styles.borderTopWidth,10);var borderLeftWidth=parseFloat(styles.borderLeftWidth,10);// In cases where the parent is fixed, we must ignore negative scroll in offset calc
if(fixedPosition&&parent.nodeName==='HTML'){parentRect.top=Math.max(parentRect.top,0);parentRect.left=Math.max(parentRect.left,0);}var offsets=getClientRect({top:childrenRect.top-parentRect.top-borderTopWidth,left:childrenRect.left-parentRect.left-borderLeftWidth,width:childrenRect.width,height:childrenRect.height});offsets.marginTop=0;offsets.marginLeft=0;// Subtract margins of documentElement in case it's being used as parent
// we do this only on HTML because it's the only element that behaves
// differently when margins are applied to it. The margins are included in
// the box of the documentElement, in the other cases not.
if(!isIE10&&isHTML){var marginTop=parseFloat(styles.marginTop,10);var marginLeft=parseFloat(styles.marginLeft,10);offsets.top-=borderTopWidth-marginTop;offsets.bottom-=borderTopWidth-marginTop;offsets.left-=borderLeftWidth-marginLeft;offsets.right-=borderLeftWidth-marginLeft;// Attach marginTop and marginLeft because in some circumstances we may need them
offsets.marginTop=marginTop;offsets.marginLeft=marginLeft;}if(isIE10&&!fixedPosition?parent.contains(scrollParent):parent===scrollParent&&scrollParent.nodeName!=='BODY'){offsets=includeScroll(offsets,parent);}return offsets;}function getViewportOffsetRectRelativeToArtbitraryNode(element){var excludeScroll=arguments.length>1&&arguments[1]!==undefined?arguments[1]:false;var html=element.ownerDocument.documentElement;var relativeOffset=getOffsetRectRelativeToArbitraryNode(element,html);var width=Math.max(html.clientWidth,window.innerWidth||0);var height=Math.max(html.clientHeight,window.innerHeight||0);var scrollTop=!excludeScroll?getScroll(html):0;var scrollLeft=!excludeScroll?getScroll(html,'left'):0;var offset={top:scrollTop-relativeOffset.top+relativeOffset.marginTop,left:scrollLeft-relativeOffset.left+relativeOffset.marginLeft,width:width,height:height};return getClientRect(offset);}/**
 * Check if the given element is fixed or is inside a fixed parent
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @argument {Element} customContainer
 * @returns {Boolean} answer to "isFixed?"
 */function isFixed(element){var nodeName=element.nodeName;if(nodeName==='BODY'||nodeName==='HTML'){return false;}if(getStyleComputedProperty(element,'position')==='fixed'){return true;}return isFixed(getParentNode(element));}/**
 * Finds the first parent of an element that has a transformed property defined
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} first transformed parent or documentElement
 */function getFixedPositionOffsetParent(element){// This check is needed to avoid errors in case one of the elements isn't defined for any reason
if(!element||!element.parentElement||isIE()){return document.documentElement;}var el=element.parentElement;while(el&&getStyleComputedProperty(el,'transform')==='none'){el=el.parentElement;}return el||document.documentElement;}/**
 * Computed the boundaries limits and return them
 * @method
 * @memberof Popper.Utils
 * @param {HTMLElement} popper
 * @param {HTMLElement} reference
 * @param {number} padding
 * @param {HTMLElement} boundariesElement - Element used to define the boundaries
 * @param {Boolean} fixedPosition - Is in fixed position mode
 * @returns {Object} Coordinates of the boundaries
 */function getBoundaries(popper,reference,padding,boundariesElement){var fixedPosition=arguments.length>4&&arguments[4]!==undefined?arguments[4]:false;// NOTE: 1 DOM access here
var boundaries={top:0,left:0};var offsetParent=fixedPosition?getFixedPositionOffsetParent(popper):findCommonOffsetParent(popper,reference);// Handle viewport case
if(boundariesElement==='viewport'){boundaries=getViewportOffsetRectRelativeToArtbitraryNode(offsetParent,fixedPosition);}else{// Handle other cases based on DOM element used as boundaries
var boundariesNode=void 0;if(boundariesElement==='scrollParent'){boundariesNode=getScrollParent(getParentNode(reference));if(boundariesNode.nodeName==='BODY'){boundariesNode=popper.ownerDocument.documentElement;}}else if(boundariesElement==='window'){boundariesNode=popper.ownerDocument.documentElement;}else{boundariesNode=boundariesElement;}var offsets=getOffsetRectRelativeToArbitraryNode(boundariesNode,offsetParent,fixedPosition);// In case of HTML, we need a different computation
if(boundariesNode.nodeName==='HTML'&&!isFixed(offsetParent)){var _getWindowSizes=getWindowSizes(),height=_getWindowSizes.height,width=_getWindowSizes.width;boundaries.top+=offsets.top-offsets.marginTop;boundaries.bottom=height+offsets.top;boundaries.left+=offsets.left-offsets.marginLeft;boundaries.right=width+offsets.left;}else{// for all the other DOM elements, this one is good
boundaries=offsets;}}// Add paddings
boundaries.left+=padding;boundaries.top+=padding;boundaries.right-=padding;boundaries.bottom-=padding;return boundaries;}function getArea(_ref){var width=_ref.width,height=_ref.height;return width*height;}/**
 * Utility used to transform the `auto` placement to the placement with more
 * available space.
 * @method
 * @memberof Popper.Utils
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */function computeAutoPlacement(placement,refRect,popper,reference,boundariesElement){var padding=arguments.length>5&&arguments[5]!==undefined?arguments[5]:0;if(placement.indexOf('auto')===-1){return placement;}var boundaries=getBoundaries(popper,reference,padding,boundariesElement);var rects={top:{width:boundaries.width,height:refRect.top-boundaries.top},right:{width:boundaries.right-refRect.right,height:boundaries.height},bottom:{width:boundaries.width,height:boundaries.bottom-refRect.bottom},left:{width:refRect.left-boundaries.left,height:boundaries.height}};var sortedAreas=Object.keys(rects).map(function(key){return _extends({key:key},rects[key],{area:getArea(rects[key])});}).sort(function(a,b){return b.area-a.area;});var filteredAreas=sortedAreas.filter(function(_ref2){var width=_ref2.width,height=_ref2.height;return width>=popper.clientWidth&&height>=popper.clientHeight;});var computedPlacement=filteredAreas.length>0?filteredAreas[0].key:sortedAreas[0].key;var variation=placement.split('-')[1];return computedPlacement+(variation?'-'+variation:'');}/**
 * Get offsets to the reference element
 * @method
 * @memberof Popper.Utils
 * @param {Object} state
 * @param {Element} popper - the popper element
 * @param {Element} reference - the reference element (the popper will be relative to this)
 * @param {Element} fixedPosition - is in fixed position mode
 * @returns {Object} An object containing the offsets which will be applied to the popper
 */function getReferenceOffsets(state,popper,reference){var fixedPosition=arguments.length>3&&arguments[3]!==undefined?arguments[3]:null;var commonOffsetParent=fixedPosition?getFixedPositionOffsetParent(popper):findCommonOffsetParent(popper,reference);return getOffsetRectRelativeToArbitraryNode(reference,commonOffsetParent,fixedPosition);}/**
 * Get the outer sizes of the given element (offset size + margins)
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Object} object containing width and height properties
 */function getOuterSizes(element){var styles=getComputedStyle(element);var x=parseFloat(styles.marginTop)+parseFloat(styles.marginBottom);var y=parseFloat(styles.marginLeft)+parseFloat(styles.marginRight);var result={width:element.offsetWidth+y,height:element.offsetHeight+x};return result;}/**
 * Get the opposite placement of the given one
 * @method
 * @memberof Popper.Utils
 * @argument {String} placement
 * @returns {String} flipped placement
 */function getOppositePlacement(placement){var hash={left:'right',right:'left',bottom:'top',top:'bottom'};return placement.replace(/left|right|bottom|top/g,function(matched){return hash[matched];});}/**
 * Get offsets to the popper
 * @method
 * @memberof Popper.Utils
 * @param {Object} position - CSS position the Popper will get applied
 * @param {HTMLElement} popper - the popper element
 * @param {Object} referenceOffsets - the reference offsets (the popper will be relative to this)
 * @param {String} placement - one of the valid placement options
 * @returns {Object} popperOffsets - An object containing the offsets which will be applied to the popper
 */function getPopperOffsets(popper,referenceOffsets,placement){placement=placement.split('-')[0];// Get popper node sizes
var popperRect=getOuterSizes(popper);// Add position, width and height to our offsets object
var popperOffsets={width:popperRect.width,height:popperRect.height};// depending by the popper placement we have to compute its offsets slightly differently
var isHoriz=['right','left'].indexOf(placement)!==-1;var mainSide=isHoriz?'top':'left';var secondarySide=isHoriz?'left':'top';var measurement=isHoriz?'height':'width';var secondaryMeasurement=!isHoriz?'height':'width';popperOffsets[mainSide]=referenceOffsets[mainSide]+referenceOffsets[measurement]/2-popperRect[measurement]/2;if(placement===secondarySide){popperOffsets[secondarySide]=referenceOffsets[secondarySide]-popperRect[secondaryMeasurement];}else{popperOffsets[secondarySide]=referenceOffsets[getOppositePlacement(secondarySide)];}return popperOffsets;}/**
 * Mimics the `find` method of Array
 * @method
 * @memberof Popper.Utils
 * @argument {Array} arr
 * @argument prop
 * @argument value
 * @returns index or -1
 */function find(arr,check){// use native find if supported
if(Array.prototype.find){return arr.find(check);}// use `filter` to obtain the same behavior of `find`
return arr.filter(check)[0];}/**
 * Return the index of the matching object
 * @method
 * @memberof Popper.Utils
 * @argument {Array} arr
 * @argument prop
 * @argument value
 * @returns index or -1
 */function findIndex(arr,prop,value){// use native findIndex if supported
if(Array.prototype.findIndex){return arr.findIndex(function(cur){return cur[prop]===value;});}// use `find` + `indexOf` if `findIndex` isn't supported
var match=find(arr,function(obj){return obj[prop]===value;});return arr.indexOf(match);}/**
 * Loop trough the list of modifiers and run them in order,
 * each of them will then edit the data object.
 * @method
 * @memberof Popper.Utils
 * @param {dataObject} data
 * @param {Array} modifiers
 * @param {String} ends - Optional modifier name used as stopper
 * @returns {dataObject}
 */function runModifiers(modifiers,data,ends){var modifiersToRun=ends===undefined?modifiers:modifiers.slice(0,findIndex(modifiers,'name',ends));modifiersToRun.forEach(function(modifier){if(modifier['function']){// eslint-disable-line dot-notation
console.warn('`modifier.function` is deprecated, use `modifier.fn`!');}var fn=modifier['function']||modifier.fn;// eslint-disable-line dot-notation
if(modifier.enabled&&isFunction(fn)){// Add properties to offsets to make them a complete clientRect object
// we do this before each modifier to make sure the previous one doesn't
// mess with these values
data.offsets.popper=getClientRect(data.offsets.popper);data.offsets.reference=getClientRect(data.offsets.reference);data=fn(data,modifier);}});return data;}/**
 * Updates the position of the popper, computing the new offsets and applying
 * the new style.<br />
 * Prefer `scheduleUpdate` over `update` because of performance reasons.
 * @method
 * @memberof Popper
 */function update(){// if popper is destroyed, don't perform any further update
if(this.state.isDestroyed){return;}var data={instance:this,styles:{},arrowStyles:{},attributes:{},flipped:false,offsets:{}};// compute reference element offsets
data.offsets.reference=getReferenceOffsets(this.state,this.popper,this.reference,this.options.positionFixed);// compute auto placement, store placement inside the data object,
// modifiers will be able to edit `placement` if needed
// and refer to originalPlacement to know the original value
data.placement=computeAutoPlacement(this.options.placement,data.offsets.reference,this.popper,this.reference,this.options.modifiers.flip.boundariesElement,this.options.modifiers.flip.padding);// store the computed placement inside `originalPlacement`
data.originalPlacement=data.placement;data.positionFixed=this.options.positionFixed;// compute the popper offsets
data.offsets.popper=getPopperOffsets(this.popper,data.offsets.reference,data.placement);data.offsets.popper.position=this.options.positionFixed?'fixed':'absolute';// run the modifiers
data=runModifiers(this.modifiers,data);// the first `update` will call `onCreate` callback
// the other ones will call `onUpdate` callback
if(!this.state.isCreated){this.state.isCreated=true;this.options.onCreate(data);}else{this.options.onUpdate(data);}}/**
 * Helper used to know if the given modifier is enabled.
 * @method
 * @memberof Popper.Utils
 * @returns {Boolean}
 */function isModifierEnabled(modifiers,modifierName){return modifiers.some(function(_ref){var name=_ref.name,enabled=_ref.enabled;return enabled&&name===modifierName;});}/**
 * Get the prefixed supported property name
 * @method
 * @memberof Popper.Utils
 * @argument {String} property (camelCase)
 * @returns {String} prefixed property (camelCase or PascalCase, depending on the vendor prefix)
 */function getSupportedPropertyName(property){var prefixes=[false,'ms','Webkit','Moz','O'];var upperProp=property.charAt(0).toUpperCase()+property.slice(1);for(var i=0;i<prefixes.length;i++){var prefix=prefixes[i];var toCheck=prefix?''+prefix+upperProp:property;if(typeof document.body.style[toCheck]!=='undefined'){return toCheck;}}return null;}/**
 * Destroy the popper
 * @method
 * @memberof Popper
 */function destroy(){this.state.isDestroyed=true;// touch DOM only if `applyStyle` modifier is enabled
if(isModifierEnabled(this.modifiers,'applyStyle')){this.popper.removeAttribute('x-placement');this.popper.style.position='';this.popper.style.top='';this.popper.style.left='';this.popper.style.right='';this.popper.style.bottom='';this.popper.style.willChange='';this.popper.style[getSupportedPropertyName('transform')]='';}this.disableEventListeners();// remove the popper if user explicity asked for the deletion on destroy
// do not use `remove` because IE11 doesn't support it
if(this.options.removeOnDestroy){this.popper.parentNode.removeChild(this.popper);}return this;}/**
 * Get the window associated with the element
 * @argument {Element} element
 * @returns {Window}
 */function getWindow(element){var ownerDocument=element.ownerDocument;return ownerDocument?ownerDocument.defaultView:window;}function attachToScrollParents(scrollParent,event,callback,scrollParents){var isBody=scrollParent.nodeName==='BODY';var target=isBody?scrollParent.ownerDocument.defaultView:scrollParent;target.addEventListener(event,callback,{passive:true});if(!isBody){attachToScrollParents(getScrollParent(target.parentNode),event,callback,scrollParents);}scrollParents.push(target);}/**
 * Setup needed event listeners used to update the popper position
 * @method
 * @memberof Popper.Utils
 * @private
 */function setupEventListeners(reference,options,state,updateBound){// Resize event listener on window
state.updateBound=updateBound;getWindow(reference).addEventListener('resize',state.updateBound,{passive:true});// Scroll event listener on scroll parents
var scrollElement=getScrollParent(reference);attachToScrollParents(scrollElement,'scroll',state.updateBound,state.scrollParents);state.scrollElement=scrollElement;state.eventsEnabled=true;return state;}/**
 * It will add resize/scroll events and start recalculating
 * position of the popper element when they are triggered.
 * @method
 * @memberof Popper
 */function enableEventListeners(){if(!this.state.eventsEnabled){this.state=setupEventListeners(this.reference,this.options,this.state,this.scheduleUpdate);}}/**
 * Remove event listeners used to update the popper position
 * @method
 * @memberof Popper.Utils
 * @private
 */function removeEventListeners(reference,state){// Remove resize event listener on window
getWindow(reference).removeEventListener('resize',state.updateBound);// Remove scroll event listener on scroll parents
state.scrollParents.forEach(function(target){target.removeEventListener('scroll',state.updateBound);});// Reset state
state.updateBound=null;state.scrollParents=[];state.scrollElement=null;state.eventsEnabled=false;return state;}/**
 * It will remove resize/scroll events and won't recalculate popper position
 * when they are triggered. It also won't trigger onUpdate callback anymore,
 * unless you call `update` method manually.
 * @method
 * @memberof Popper
 */function disableEventListeners(){if(this.state.eventsEnabled){cancelAnimationFrame(this.scheduleUpdate);this.state=removeEventListeners(this.reference,this.state);}}/**
 * Tells if a given input is a number
 * @method
 * @memberof Popper.Utils
 * @param {*} input to check
 * @return {Boolean}
 */function isNumeric(n){return n!==''&&!isNaN(parseFloat(n))&&isFinite(n);}/**
 * Set the style to the given popper
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element - Element to apply the style to
 * @argument {Object} styles
 * Object with a list of properties and values which will be applied to the element
 */function setStyles(element,styles){Object.keys(styles).forEach(function(prop){var unit='';// add unit if the value is numeric and is one of the following
if(['width','height','top','right','bottom','left'].indexOf(prop)!==-1&&isNumeric(styles[prop])){unit='px';}element.style[prop]=styles[prop]+unit;});}/**
 * Set the attributes to the given popper
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element - Element to apply the attributes to
 * @argument {Object} styles
 * Object with a list of properties and values which will be applied to the element
 */function setAttributes(element,attributes){Object.keys(attributes).forEach(function(prop){var value=attributes[prop];if(value!==false){element.setAttribute(prop,attributes[prop]);}else{element.removeAttribute(prop);}});}/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} data.styles - List of style properties - values to apply to popper element
 * @argument {Object} data.attributes - List of attribute properties - values to apply to popper element
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The same data object
 */function applyStyle(data){// any property present in `data.styles` will be applied to the popper,
// in this way we can make the 3rd party modifiers add custom styles to it
// Be aware, modifiers could override the properties defined in the previous
// lines of this modifier!
setStyles(data.instance.popper,data.styles);// any property present in `data.attributes` will be applied to the popper,
// they will be set as HTML attributes of the element
setAttributes(data.instance.popper,data.attributes);// if arrowElement is defined and arrowStyles has some properties
if(data.arrowElement&&Object.keys(data.arrowStyles).length){setStyles(data.arrowElement,data.arrowStyles);}return data;}/**
 * Set the x-placement attribute before everything else because it could be used
 * to add margins to the popper margins needs to be calculated to get the
 * correct popper offsets.
 * @method
 * @memberof Popper.modifiers
 * @param {HTMLElement} reference - The reference element used to position the popper
 * @param {HTMLElement} popper - The HTML element used as popper
 * @param {Object} options - Popper.js options
 */function applyStyleOnLoad(reference,popper,options,modifierOptions,state){// compute reference element offsets
var referenceOffsets=getReferenceOffsets(state,popper,reference,options.positionFixed);// compute auto placement, store placement inside the data object,
// modifiers will be able to edit `placement` if needed
// and refer to originalPlacement to know the original value
var placement=computeAutoPlacement(options.placement,referenceOffsets,popper,reference,options.modifiers.flip.boundariesElement,options.modifiers.flip.padding);popper.setAttribute('x-placement',placement);// Apply `position` to popper before anything else because
// without the position applied we can't guarantee correct computations
setStyles(popper,{position:options.positionFixed?'fixed':'absolute'});return options;}/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */function computeStyle(data,options){var x=options.x,y=options.y;var popper=data.offsets.popper;// Remove this legacy support in Popper.js v2
var legacyGpuAccelerationOption=find(data.instance.modifiers,function(modifier){return modifier.name==='applyStyle';}).gpuAcceleration;if(legacyGpuAccelerationOption!==undefined){console.warn('WARNING: `gpuAcceleration` option moved to `computeStyle` modifier and will not be supported in future versions of Popper.js!');}var gpuAcceleration=legacyGpuAccelerationOption!==undefined?legacyGpuAccelerationOption:options.gpuAcceleration;var offsetParent=getOffsetParent(data.instance.popper);var offsetParentRect=getBoundingClientRect(offsetParent);// Styles
var styles={position:popper.position};// Avoid blurry text by using full pixel integers.
// For pixel-perfect positioning, top/bottom prefers rounded
// values, while left/right prefers floored values.
var offsets={left:Math.floor(popper.left),top:Math.round(popper.top),bottom:Math.round(popper.bottom),right:Math.floor(popper.right)};var sideA=x==='bottom'?'top':'bottom';var sideB=y==='right'?'left':'right';// if gpuAcceleration is set to `true` and transform is supported,
//  we use `translate3d` to apply the position to the popper we
// automatically use the supported prefixed version if needed
var prefixedProperty=getSupportedPropertyName('transform');// now, let's make a step back and look at this code closely (wtf?)
// If the content of the popper grows once it's been positioned, it
// may happen that the popper gets misplaced because of the new content
// overflowing its reference element
// To avoid this problem, we provide two options (x and y), which allow
// the consumer to define the offset origin.
// If we position a popper on top of a reference element, we can set
// `x` to `top` to make the popper grow towards its top instead of
// its bottom.
var left=void 0,top=void 0;if(sideA==='bottom'){top=-offsetParentRect.height+offsets.bottom;}else{top=offsets.top;}if(sideB==='right'){left=-offsetParentRect.width+offsets.right;}else{left=offsets.left;}if(gpuAcceleration&&prefixedProperty){styles[prefixedProperty]='translate3d('+left+'px, '+top+'px, 0)';styles[sideA]=0;styles[sideB]=0;styles.willChange='transform';}else{// othwerise, we use the standard `top`, `left`, `bottom` and `right` properties
var invertTop=sideA==='bottom'?-1:1;var invertLeft=sideB==='right'?-1:1;styles[sideA]=top*invertTop;styles[sideB]=left*invertLeft;styles.willChange=sideA+', '+sideB;}// Attributes
var attributes={'x-placement':data.placement};// Update `data` attributes, styles and arrowStyles
data.attributes=_extends({},attributes,data.attributes);data.styles=_extends({},styles,data.styles);data.arrowStyles=_extends({},data.offsets.arrow,data.arrowStyles);return data;}/**
 * Helper used to know if the given modifier depends from another one.<br />
 * It checks if the needed modifier is listed and enabled.
 * @method
 * @memberof Popper.Utils
 * @param {Array} modifiers - list of modifiers
 * @param {String} requestingName - name of requesting modifier
 * @param {String} requestedName - name of requested modifier
 * @returns {Boolean}
 */function isModifierRequired(modifiers,requestingName,requestedName){var requesting=find(modifiers,function(_ref){var name=_ref.name;return name===requestingName;});var isRequired=!!requesting&&modifiers.some(function(modifier){return modifier.name===requestedName&&modifier.enabled&&modifier.order<requesting.order;});if(!isRequired){var _requesting='`'+requestingName+'`';var requested='`'+requestedName+'`';console.warn(requested+' modifier is required by '+_requesting+' modifier in order to work, be sure to include it before '+_requesting+'!');}return isRequired;}/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */function arrow(data,options){var _data$offsets$arrow;// arrow depends on keepTogether in order to work
if(!isModifierRequired(data.instance.modifiers,'arrow','keepTogether')){return data;}var arrowElement=options.element;// if arrowElement is a string, suppose it's a CSS selector
if(typeof arrowElement==='string'){arrowElement=data.instance.popper.querySelector(arrowElement);// if arrowElement is not found, don't run the modifier
if(!arrowElement){return data;}}else{// if the arrowElement isn't a query selector we must check that the
// provided DOM node is child of its popper node
if(!data.instance.popper.contains(arrowElement)){console.warn('WARNING: `arrow.element` must be child of its popper element!');return data;}}var placement=data.placement.split('-')[0];var _data$offsets=data.offsets,popper=_data$offsets.popper,reference=_data$offsets.reference;var isVertical=['left','right'].indexOf(placement)!==-1;var len=isVertical?'height':'width';var sideCapitalized=isVertical?'Top':'Left';var side=sideCapitalized.toLowerCase();var altSide=isVertical?'left':'top';var opSide=isVertical?'bottom':'right';var arrowElementSize=getOuterSizes(arrowElement)[len];//
// extends keepTogether behavior making sure the popper and its
// reference have enough pixels in conjuction
//
// top/left side
if(reference[opSide]-arrowElementSize<popper[side]){data.offsets.popper[side]-=popper[side]-(reference[opSide]-arrowElementSize);}// bottom/right side
if(reference[side]+arrowElementSize>popper[opSide]){data.offsets.popper[side]+=reference[side]+arrowElementSize-popper[opSide];}data.offsets.popper=getClientRect(data.offsets.popper);// compute center of the popper
var center=reference[side]+reference[len]/2-arrowElementSize/2;// Compute the sideValue using the updated popper offsets
// take popper margin in account because we don't have this info available
var css=getStyleComputedProperty(data.instance.popper);var popperMarginSide=parseFloat(css['margin'+sideCapitalized],10);var popperBorderSide=parseFloat(css['border'+sideCapitalized+'Width'],10);var sideValue=center-data.offsets.popper[side]-popperMarginSide-popperBorderSide;// prevent arrowElement from being placed not contiguously to its popper
sideValue=Math.max(Math.min(popper[len]-arrowElementSize,sideValue),0);data.arrowElement=arrowElement;data.offsets.arrow=(_data$offsets$arrow={},defineProperty(_data$offsets$arrow,side,Math.round(sideValue)),defineProperty(_data$offsets$arrow,altSide,''),_data$offsets$arrow);return data;}/**
 * Get the opposite placement variation of the given one
 * @method
 * @memberof Popper.Utils
 * @argument {String} placement variation
 * @returns {String} flipped placement variation
 */function getOppositeVariation(variation){if(variation==='end'){return'start';}else if(variation==='start'){return'end';}return variation;}/**
 * List of accepted placements to use as values of the `placement` option.<br />
 * Valid placements are:
 * - `auto`
 * - `top`
 * - `right`
 * - `bottom`
 * - `left`
 *
 * Each placement can have a variation from this list:
 * - `-start`
 * - `-end`
 *
 * Variations are interpreted easily if you think of them as the left to right
 * written languages. Horizontally (`top` and `bottom`), `start` is left and `end`
 * is right.<br />
 * Vertically (`left` and `right`), `start` is top and `end` is bottom.
 *
 * Some valid examples are:
 * - `top-end` (on top of reference, right aligned)
 * - `right-start` (on right of reference, top aligned)
 * - `bottom` (on bottom, centered)
 * - `auto-right` (on the side with more space available, alignment depends by placement)
 *
 * @static
 * @type {Array}
 * @enum {String}
 * @readonly
 * @method placements
 * @memberof Popper
 */var placements=['auto-start','auto','auto-end','top-start','top','top-end','right-start','right','right-end','bottom-end','bottom','bottom-start','left-end','left','left-start'];// Get rid of `auto` `auto-start` and `auto-end`
var validPlacements=placements.slice(3);/**
 * Given an initial placement, returns all the subsequent placements
 * clockwise (or counter-clockwise).
 *
 * @method
 * @memberof Popper.Utils
 * @argument {String} placement - A valid placement (it accepts variations)
 * @argument {Boolean} counter - Set to true to walk the placements counterclockwise
 * @returns {Array} placements including their variations
 */function clockwise(placement){var counter=arguments.length>1&&arguments[1]!==undefined?arguments[1]:false;var index=validPlacements.indexOf(placement);var arr=validPlacements.slice(index+1).concat(validPlacements.slice(0,index));return counter?arr.reverse():arr;}var BEHAVIORS={FLIP:'flip',CLOCKWISE:'clockwise',COUNTERCLOCKWISE:'counterclockwise'};/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */function flip(data,options){// if `inner` modifier is enabled, we can't use the `flip` modifier
if(isModifierEnabled(data.instance.modifiers,'inner')){return data;}if(data.flipped&&data.placement===data.originalPlacement){// seems like flip is trying to loop, probably there's not enough space on any of the flippable sides
return data;}var boundaries=getBoundaries(data.instance.popper,data.instance.reference,options.padding,options.boundariesElement,data.positionFixed);var placement=data.placement.split('-')[0];var placementOpposite=getOppositePlacement(placement);var variation=data.placement.split('-')[1]||'';var flipOrder=[];switch(options.behavior){case BEHAVIORS.FLIP:flipOrder=[placement,placementOpposite];break;case BEHAVIORS.CLOCKWISE:flipOrder=clockwise(placement);break;case BEHAVIORS.COUNTERCLOCKWISE:flipOrder=clockwise(placement,true);break;default:flipOrder=options.behavior;}flipOrder.forEach(function(step,index){if(placement!==step||flipOrder.length===index+1){return data;}placement=data.placement.split('-')[0];placementOpposite=getOppositePlacement(placement);var popperOffsets=data.offsets.popper;var refOffsets=data.offsets.reference;// using floor because the reference offsets may contain decimals we are not going to consider here
var floor=Math.floor;var overlapsRef=placement==='left'&&floor(popperOffsets.right)>floor(refOffsets.left)||placement==='right'&&floor(popperOffsets.left)<floor(refOffsets.right)||placement==='top'&&floor(popperOffsets.bottom)>floor(refOffsets.top)||placement==='bottom'&&floor(popperOffsets.top)<floor(refOffsets.bottom);var overflowsLeft=floor(popperOffsets.left)<floor(boundaries.left);var overflowsRight=floor(popperOffsets.right)>floor(boundaries.right);var overflowsTop=floor(popperOffsets.top)<floor(boundaries.top);var overflowsBottom=floor(popperOffsets.bottom)>floor(boundaries.bottom);var overflowsBoundaries=placement==='left'&&overflowsLeft||placement==='right'&&overflowsRight||placement==='top'&&overflowsTop||placement==='bottom'&&overflowsBottom;// flip the variation if required
var isVertical=['top','bottom'].indexOf(placement)!==-1;var flippedVariation=!!options.flipVariations&&(isVertical&&variation==='start'&&overflowsLeft||isVertical&&variation==='end'&&overflowsRight||!isVertical&&variation==='start'&&overflowsTop||!isVertical&&variation==='end'&&overflowsBottom);if(overlapsRef||overflowsBoundaries||flippedVariation){// this boolean to detect any flip loop
data.flipped=true;if(overlapsRef||overflowsBoundaries){placement=flipOrder[index+1];}if(flippedVariation){variation=getOppositeVariation(variation);}data.placement=placement+(variation?'-'+variation:'');// this object contains `position`, we want to preserve it along with
// any additional property we may add in the future
data.offsets.popper=_extends({},data.offsets.popper,getPopperOffsets(data.instance.popper,data.offsets.reference,data.placement));data=runModifiers(data.instance.modifiers,data,'flip');}});return data;}/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */function keepTogether(data){var _data$offsets=data.offsets,popper=_data$offsets.popper,reference=_data$offsets.reference;var placement=data.placement.split('-')[0];var floor=Math.floor;var isVertical=['top','bottom'].indexOf(placement)!==-1;var side=isVertical?'right':'bottom';var opSide=isVertical?'left':'top';var measurement=isVertical?'width':'height';if(popper[side]<floor(reference[opSide])){data.offsets.popper[opSide]=floor(reference[opSide])-popper[measurement];}if(popper[opSide]>floor(reference[side])){data.offsets.popper[opSide]=floor(reference[side]);}return data;}/**
 * Converts a string containing value + unit into a px value number
 * @function
 * @memberof {modifiers~offset}
 * @private
 * @argument {String} str - Value + unit string
 * @argument {String} measurement - `height` or `width`
 * @argument {Object} popperOffsets
 * @argument {Object} referenceOffsets
 * @returns {Number|String}
 * Value in pixels, or original string if no values were extracted
 */function toValue(str,measurement,popperOffsets,referenceOffsets){// separate value from unit
var split=str.match(/((?:\-|\+)?\d*\.?\d*)(.*)/);var value=+split[1];var unit=split[2];// If it's not a number it's an operator, I guess
if(!value){return str;}if(unit.indexOf('%')===0){var element=void 0;switch(unit){case'%p':element=popperOffsets;break;case'%':case'%r':default:element=referenceOffsets;}var rect=getClientRect(element);return rect[measurement]/100*value;}else if(unit==='vh'||unit==='vw'){// if is a vh or vw, we calculate the size based on the viewport
var size=void 0;if(unit==='vh'){size=Math.max(document.documentElement.clientHeight,window.innerHeight||0);}else{size=Math.max(document.documentElement.clientWidth,window.innerWidth||0);}return size/100*value;}else{// if is an explicit pixel unit, we get rid of the unit and keep the value
// if is an implicit unit, it's px, and we return just the value
return value;}}/**
 * Parse an `offset` string to extrapolate `x` and `y` numeric offsets.
 * @function
 * @memberof {modifiers~offset}
 * @private
 * @argument {String} offset
 * @argument {Object} popperOffsets
 * @argument {Object} referenceOffsets
 * @argument {String} basePlacement
 * @returns {Array} a two cells array with x and y offsets in numbers
 */function parseOffset(offset,popperOffsets,referenceOffsets,basePlacement){var offsets=[0,0];// Use height if placement is left or right and index is 0 otherwise use width
// in this way the first offset will use an axis and the second one
// will use the other one
var useHeight=['right','left'].indexOf(basePlacement)!==-1;// Split the offset string to obtain a list of values and operands
// The regex addresses values with the plus or minus sign in front (+10, -20, etc)
var fragments=offset.split(/(\+|\-)/).map(function(frag){return frag.trim();});// Detect if the offset string contains a pair of values or a single one
// they could be separated by comma or space
var divider=fragments.indexOf(find(fragments,function(frag){return frag.search(/,|\s/)!==-1;}));if(fragments[divider]&&fragments[divider].indexOf(',')===-1){console.warn('Offsets separated by white space(s) are deprecated, use a comma (,) instead.');}// If divider is found, we divide the list of values and operands to divide
// them by ofset X and Y.
var splitRegex=/\s*,\s*|\s+/;var ops=divider!==-1?[fragments.slice(0,divider).concat([fragments[divider].split(splitRegex)[0]]),[fragments[divider].split(splitRegex)[1]].concat(fragments.slice(divider+1))]:[fragments];// Convert the values with units to absolute pixels to allow our computations
ops=ops.map(function(op,index){// Most of the units rely on the orientation of the popper
var measurement=(index===1?!useHeight:useHeight)?'height':'width';var mergeWithPrevious=false;return op// This aggregates any `+` or `-` sign that aren't considered operators
// e.g.: 10 + +5 => [10, +, +5]
.reduce(function(a,b){if(a[a.length-1]===''&&['+','-'].indexOf(b)!==-1){a[a.length-1]=b;mergeWithPrevious=true;return a;}else if(mergeWithPrevious){a[a.length-1]+=b;mergeWithPrevious=false;return a;}else{return a.concat(b);}},[])// Here we convert the string values into number values (in px)
.map(function(str){return toValue(str,measurement,popperOffsets,referenceOffsets);});});// Loop trough the offsets arrays and execute the operations
ops.forEach(function(op,index){op.forEach(function(frag,index2){if(isNumeric(frag)){offsets[index]+=frag*(op[index2-1]==='-'?-1:1);}});});return offsets;}/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @argument {Number|String} options.offset=0
 * The offset value as described in the modifier description
 * @returns {Object} The data object, properly modified
 */function offset(data,_ref){var offset=_ref.offset;var placement=data.placement,_data$offsets=data.offsets,popper=_data$offsets.popper,reference=_data$offsets.reference;var basePlacement=placement.split('-')[0];var offsets=void 0;if(isNumeric(+offset)){offsets=[+offset,0];}else{offsets=parseOffset(offset,popper,reference,basePlacement);}if(basePlacement==='left'){popper.top+=offsets[0];popper.left-=offsets[1];}else if(basePlacement==='right'){popper.top+=offsets[0];popper.left+=offsets[1];}else if(basePlacement==='top'){popper.left+=offsets[0];popper.top-=offsets[1];}else if(basePlacement==='bottom'){popper.left+=offsets[0];popper.top+=offsets[1];}data.popper=popper;return data;}/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */function preventOverflow(data,options){var boundariesElement=options.boundariesElement||getOffsetParent(data.instance.popper);// If offsetParent is the reference element, we really want to
// go one step up and use the next offsetParent as reference to
// avoid to make this modifier completely useless and look like broken
if(data.instance.reference===boundariesElement){boundariesElement=getOffsetParent(boundariesElement);}// NOTE: DOM access here
// resets the popper's position so that the document size can be calculated excluding
// the size of the popper element itself
var transformProp=getSupportedPropertyName('transform');var popperStyles=data.instance.popper.style;// assignment to help minification
var top=popperStyles.top,left=popperStyles.left,transform=popperStyles[transformProp];popperStyles.top='';popperStyles.left='';popperStyles[transformProp]='';var boundaries=getBoundaries(data.instance.popper,data.instance.reference,options.padding,boundariesElement,data.positionFixed);// NOTE: DOM access here
// restores the original style properties after the offsets have been computed
popperStyles.top=top;popperStyles.left=left;popperStyles[transformProp]=transform;options.boundaries=boundaries;var order=options.priority;var popper=data.offsets.popper;var check={primary:function primary(placement){var value=popper[placement];if(popper[placement]<boundaries[placement]&&!options.escapeWithReference){value=Math.max(popper[placement],boundaries[placement]);}return defineProperty({},placement,value);},secondary:function secondary(placement){var mainSide=placement==='right'?'left':'top';var value=popper[mainSide];if(popper[placement]>boundaries[placement]&&!options.escapeWithReference){value=Math.min(popper[mainSide],boundaries[placement]-(placement==='right'?popper.width:popper.height));}return defineProperty({},mainSide,value);}};order.forEach(function(placement){var side=['left','top'].indexOf(placement)!==-1?'primary':'secondary';popper=_extends({},popper,check[side](placement));});data.offsets.popper=popper;return data;}/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */function shift(data){var placement=data.placement;var basePlacement=placement.split('-')[0];var shiftvariation=placement.split('-')[1];// if shift shiftvariation is specified, run the modifier
if(shiftvariation){var _data$offsets=data.offsets,reference=_data$offsets.reference,popper=_data$offsets.popper;var isVertical=['bottom','top'].indexOf(basePlacement)!==-1;var side=isVertical?'left':'top';var measurement=isVertical?'width':'height';var shiftOffsets={start:defineProperty({},side,reference[side]),end:defineProperty({},side,reference[side]+reference[measurement]-popper[measurement])};data.offsets.popper=_extends({},popper,shiftOffsets[shiftvariation]);}return data;}/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */function hide(data){if(!isModifierRequired(data.instance.modifiers,'hide','preventOverflow')){return data;}var refRect=data.offsets.reference;var bound=find(data.instance.modifiers,function(modifier){return modifier.name==='preventOverflow';}).boundaries;if(refRect.bottom<bound.top||refRect.left>bound.right||refRect.top>bound.bottom||refRect.right<bound.left){// Avoid unnecessary DOM access if visibility hasn't changed
if(data.hide===true){return data;}data.hide=true;data.attributes['x-out-of-boundaries']='';}else{// Avoid unnecessary DOM access if visibility hasn't changed
if(data.hide===false){return data;}data.hide=false;data.attributes['x-out-of-boundaries']=false;}return data;}/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */function inner(data){var placement=data.placement;var basePlacement=placement.split('-')[0];var _data$offsets=data.offsets,popper=_data$offsets.popper,reference=_data$offsets.reference;var isHoriz=['left','right'].indexOf(basePlacement)!==-1;var subtractLength=['top','left'].indexOf(basePlacement)===-1;popper[isHoriz?'left':'top']=reference[basePlacement]-(subtractLength?popper[isHoriz?'width':'height']:0);data.placement=getOppositePlacement(placement);data.offsets.popper=getClientRect(popper);return data;}/**
 * Modifier function, each modifier can have a function of this type assigned
 * to its `fn` property.<br />
 * These functions will be called on each update, this means that you must
 * make sure they are performant enough to avoid performance bottlenecks.
 *
 * @function ModifierFn
 * @argument {dataObject} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {dataObject} The data object, properly modified
 *//**
 * Modifiers are plugins used to alter the behavior of your poppers.<br />
 * Popper.js uses a set of 9 modifiers to provide all the basic functionalities
 * needed by the library.
 *
 * Usually you don't want to override the `order`, `fn` and `onLoad` props.
 * All the other properties are configurations that could be tweaked.
 * @namespace modifiers
 */var modifiers={/**
   * Modifier used to shift the popper on the start or end of its reference
   * element.<br />
   * It will read the variation of the `placement` property.<br />
   * It can be one either `-end` or `-start`.
   * @memberof modifiers
   * @inner
   */shift:{/** @prop {number} order=100 - Index used to define the order of execution */order:100,/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */enabled:true,/** @prop {ModifierFn} */fn:shift},/**
   * The `offset` modifier can shift your popper on both its axis.
   *
   * It accepts the following units:
   * - `px` or unitless, interpreted as pixels
   * - `%` or `%r`, percentage relative to the length of the reference element
   * - `%p`, percentage relative to the length of the popper element
   * - `vw`, CSS viewport width unit
   * - `vh`, CSS viewport height unit
   *
   * For length is intended the main axis relative to the placement of the popper.<br />
   * This means that if the placement is `top` or `bottom`, the length will be the
   * `width`. In case of `left` or `right`, it will be the height.
   *
   * You can provide a single value (as `Number` or `String`), or a pair of values
   * as `String` divided by a comma or one (or more) white spaces.<br />
   * The latter is a deprecated method because it leads to confusion and will be
   * removed in v2.<br />
   * Additionally, it accepts additions and subtractions between different units.
   * Note that multiplications and divisions aren't supported.
   *
   * Valid examples are:
   * ```
   * 10
   * '10%'
   * '10, 10'
   * '10%, 10'
   * '10 + 10%'
   * '10 - 5vh + 3%'
   * '-10px + 5vh, 5px - 6%'
   * ```
   * > **NB**: If you desire to apply offsets to your poppers in a way that may make them overlap
   * > with their reference element, unfortunately, you will have to disable the `flip` modifier.
   * > More on this [reading this issue](https://github.com/FezVrasta/popper.js/issues/373)
   *
   * @memberof modifiers
   * @inner
   */offset:{/** @prop {number} order=200 - Index used to define the order of execution */order:200,/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */enabled:true,/** @prop {ModifierFn} */fn:offset,/** @prop {Number|String} offset=0
     * The offset value as described in the modifier description
     */offset:0},/**
   * Modifier used to prevent the popper from being positioned outside the boundary.
   *
   * An scenario exists where the reference itself is not within the boundaries.<br />
   * We can say it has "escaped the boundaries" — or just "escaped".<br />
   * In this case we need to decide whether the popper should either:
   *
   * - detach from the reference and remain "trapped" in the boundaries, or
   * - if it should ignore the boundary and "escape with its reference"
   *
   * When `escapeWithReference` is set to`true` and reference is completely
   * outside its boundaries, the popper will overflow (or completely leave)
   * the boundaries in order to remain attached to the edge of the reference.
   *
   * @memberof modifiers
   * @inner
   */preventOverflow:{/** @prop {number} order=300 - Index used to define the order of execution */order:300,/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */enabled:true,/** @prop {ModifierFn} */fn:preventOverflow,/**
     * @prop {Array} [priority=['left','right','top','bottom']]
     * Popper will try to prevent overflow following these priorities by default,
     * then, it could overflow on the left and on top of the `boundariesElement`
     */priority:['left','right','top','bottom'],/**
     * @prop {number} padding=5
     * Amount of pixel used to define a minimum distance between the boundaries
     * and the popper this makes sure the popper has always a little padding
     * between the edges of its container
     */padding:5,/**
     * @prop {String|HTMLElement} boundariesElement='scrollParent'
     * Boundaries used by the modifier, can be `scrollParent`, `window`,
     * `viewport` or any DOM element.
     */boundariesElement:'scrollParent'},/**
   * Modifier used to make sure the reference and its popper stay near eachothers
   * without leaving any gap between the two. Expecially useful when the arrow is
   * enabled and you want to assure it to point to its reference element.
   * It cares only about the first axis, you can still have poppers with margin
   * between the popper and its reference element.
   * @memberof modifiers
   * @inner
   */keepTogether:{/** @prop {number} order=400 - Index used to define the order of execution */order:400,/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */enabled:true,/** @prop {ModifierFn} */fn:keepTogether},/**
   * This modifier is used to move the `arrowElement` of the popper to make
   * sure it is positioned between the reference element and its popper element.
   * It will read the outer size of the `arrowElement` node to detect how many
   * pixels of conjuction are needed.
   *
   * It has no effect if no `arrowElement` is provided.
   * @memberof modifiers
   * @inner
   */arrow:{/** @prop {number} order=500 - Index used to define the order of execution */order:500,/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */enabled:true,/** @prop {ModifierFn} */fn:arrow,/** @prop {String|HTMLElement} element='[x-arrow]' - Selector or node used as arrow */element:'[x-arrow]'},/**
   * Modifier used to flip the popper's placement when it starts to overlap its
   * reference element.
   *
   * Requires the `preventOverflow` modifier before it in order to work.
   *
   * **NOTE:** this modifier will interrupt the current update cycle and will
   * restart it if it detects the need to flip the placement.
   * @memberof modifiers
   * @inner
   */flip:{/** @prop {number} order=600 - Index used to define the order of execution */order:600,/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */enabled:true,/** @prop {ModifierFn} */fn:flip,/**
     * @prop {String|Array} behavior='flip'
     * The behavior used to change the popper's placement. It can be one of
     * `flip`, `clockwise`, `counterclockwise` or an array with a list of valid
     * placements (with optional variations).
     */behavior:'flip',/**
     * @prop {number} padding=5
     * The popper will flip if it hits the edges of the `boundariesElement`
     */padding:5,/**
     * @prop {String|HTMLElement} boundariesElement='viewport'
     * The element which will define the boundaries of the popper position,
     * the popper will never be placed outside of the defined boundaries
     * (except if keepTogether is enabled)
     */boundariesElement:'viewport'},/**
   * Modifier used to make the popper flow toward the inner of the reference element.
   * By default, when this modifier is disabled, the popper will be placed outside
   * the reference element.
   * @memberof modifiers
   * @inner
   */inner:{/** @prop {number} order=700 - Index used to define the order of execution */order:700,/** @prop {Boolean} enabled=false - Whether the modifier is enabled or not */enabled:false,/** @prop {ModifierFn} */fn:inner},/**
   * Modifier used to hide the popper when its reference element is outside of the
   * popper boundaries. It will set a `x-out-of-boundaries` attribute which can
   * be used to hide with a CSS selector the popper when its reference is
   * out of boundaries.
   *
   * Requires the `preventOverflow` modifier before it in order to work.
   * @memberof modifiers
   * @inner
   */hide:{/** @prop {number} order=800 - Index used to define the order of execution */order:800,/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */enabled:true,/** @prop {ModifierFn} */fn:hide},/**
   * Computes the style that will be applied to the popper element to gets
   * properly positioned.
   *
   * Note that this modifier will not touch the DOM, it just prepares the styles
   * so that `applyStyle` modifier can apply it. This separation is useful
   * in case you need to replace `applyStyle` with a custom implementation.
   *
   * This modifier has `850` as `order` value to maintain backward compatibility
   * with previous versions of Popper.js. Expect the modifiers ordering method
   * to change in future major versions of the library.
   *
   * @memberof modifiers
   * @inner
   */computeStyle:{/** @prop {number} order=850 - Index used to define the order of execution */order:850,/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */enabled:true,/** @prop {ModifierFn} */fn:computeStyle,/**
     * @prop {Boolean} gpuAcceleration=true
     * If true, it uses the CSS 3d transformation to position the popper.
     * Otherwise, it will use the `top` and `left` properties.
     */gpuAcceleration:true,/**
     * @prop {string} [x='bottom']
     * Where to anchor the X axis (`bottom` or `top`). AKA X offset origin.
     * Change this if your popper should grow in a direction different from `bottom`
     */x:'bottom',/**
     * @prop {string} [x='left']
     * Where to anchor the Y axis (`left` or `right`). AKA Y offset origin.
     * Change this if your popper should grow in a direction different from `right`
     */y:'right'},/**
   * Applies the computed styles to the popper element.
   *
   * All the DOM manipulations are limited to this modifier. This is useful in case
   * you want to integrate Popper.js inside a framework or view library and you
   * want to delegate all the DOM manipulations to it.
   *
   * Note that if you disable this modifier, you must make sure the popper element
   * has its position set to `absolute` before Popper.js can do its work!
   *
   * Just disable this modifier and define you own to achieve the desired effect.
   *
   * @memberof modifiers
   * @inner
   */applyStyle:{/** @prop {number} order=900 - Index used to define the order of execution */order:900,/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */enabled:true,/** @prop {ModifierFn} */fn:applyStyle,/** @prop {Function} */onLoad:applyStyleOnLoad,/**
     * @deprecated since version 1.10.0, the property moved to `computeStyle` modifier
     * @prop {Boolean} gpuAcceleration=true
     * If true, it uses the CSS 3d transformation to position the popper.
     * Otherwise, it will use the `top` and `left` properties.
     */gpuAcceleration:undefined}};/**
 * The `dataObject` is an object containing all the informations used by Popper.js
 * this object get passed to modifiers and to the `onCreate` and `onUpdate` callbacks.
 * @name dataObject
 * @property {Object} data.instance The Popper.js instance
 * @property {String} data.placement Placement applied to popper
 * @property {String} data.originalPlacement Placement originally defined on init
 * @property {Boolean} data.flipped True if popper has been flipped by flip modifier
 * @property {Boolean} data.hide True if the reference element is out of boundaries, useful to know when to hide the popper.
 * @property {HTMLElement} data.arrowElement Node used as arrow by arrow modifier
 * @property {Object} data.styles Any CSS property defined here will be applied to the popper, it expects the JavaScript nomenclature (eg. `marginBottom`)
 * @property {Object} data.arrowStyles Any CSS property defined here will be applied to the popper arrow, it expects the JavaScript nomenclature (eg. `marginBottom`)
 * @property {Object} data.boundaries Offsets of the popper boundaries
 * @property {Object} data.offsets The measurements of popper, reference and arrow elements.
 * @property {Object} data.offsets.popper `top`, `left`, `width`, `height` values
 * @property {Object} data.offsets.reference `top`, `left`, `width`, `height` values
 * @property {Object} data.offsets.arrow] `top` and `left` offsets, only one of them will be different from 0
 *//**
 * Default options provided to Popper.js constructor.<br />
 * These can be overriden using the `options` argument of Popper.js.<br />
 * To override an option, simply pass as 3rd argument an object with the same
 * structure of this object, example:
 * ```
 * new Popper(ref, pop, {
 *   modifiers: {
 *     preventOverflow: { enabled: false }
 *   }
 * })
 * ```
 * @type {Object}
 * @static
 * @memberof Popper
 */var Defaults={/**
   * Popper's placement
   * @prop {Popper.placements} placement='bottom'
   */placement:'bottom',/**
   * Set this to true if you want popper to position it self in 'fixed' mode
   * @prop {Boolean} positionFixed=false
   */positionFixed:false,/**
   * Whether events (resize, scroll) are initially enabled
   * @prop {Boolean} eventsEnabled=true
   */eventsEnabled:true,/**
   * Set to true if you want to automatically remove the popper when
   * you call the `destroy` method.
   * @prop {Boolean} removeOnDestroy=false
   */removeOnDestroy:false,/**
   * Callback called when the popper is created.<br />
   * By default, is set to no-op.<br />
   * Access Popper.js instance with `data.instance`.
   * @prop {onCreate}
   */onCreate:function onCreate(){},/**
   * Callback called when the popper is updated, this callback is not called
   * on the initialization/creation of the popper, but only on subsequent
   * updates.<br />
   * By default, is set to no-op.<br />
   * Access Popper.js instance with `data.instance`.
   * @prop {onUpdate}
   */onUpdate:function onUpdate(){},/**
   * List of modifiers used to modify the offsets before they are applied to the popper.
   * They provide most of the functionalities of Popper.js
   * @prop {modifiers}
   */modifiers:modifiers};/**
 * @callback onCreate
 * @param {dataObject} data
 *//**
 * @callback onUpdate
 * @param {dataObject} data
 */// Utils
// Methods
var Popper=function(){/**
   * Create a new Popper.js instance
   * @class Popper
   * @param {HTMLElement|referenceObject} reference - The reference element used to position the popper
   * @param {HTMLElement} popper - The HTML element used as popper.
   * @param {Object} options - Your custom options to override the ones defined in [Defaults](#defaults)
   * @return {Object} instance - The generated Popper.js instance
   */function Popper(reference,popper){var _this=this;var options=arguments.length>2&&arguments[2]!==undefined?arguments[2]:{};classCallCheck(this,Popper);this.scheduleUpdate=function(){return requestAnimationFrame(_this.update);};// make update() debounced, so that it only runs at most once-per-tick
this.update=debounce(this.update.bind(this));// with {} we create a new object with the options inside it
this.options=_extends({},Popper.Defaults,options);// init state
this.state={isDestroyed:false,isCreated:false,scrollParents:[]};// get reference and popper elements (allow jQuery wrappers)
this.reference=reference&&reference.jquery?reference[0]:reference;this.popper=popper&&popper.jquery?popper[0]:popper;// Deep merge modifiers options
this.options.modifiers={};Object.keys(_extends({},Popper.Defaults.modifiers,options.modifiers)).forEach(function(name){_this.options.modifiers[name]=_extends({},Popper.Defaults.modifiers[name]||{},options.modifiers?options.modifiers[name]:{});});// Refactoring modifiers' list (Object => Array)
this.modifiers=Object.keys(this.options.modifiers).map(function(name){return _extends({name:name},_this.options.modifiers[name]);})// sort the modifiers by order
.sort(function(a,b){return a.order-b.order;});// modifiers have the ability to execute arbitrary code when Popper.js get inited
// such code is executed in the same order of its modifier
// they could add new properties to their options configuration
// BE AWARE: don't add options to `options.modifiers.name` but to `modifierOptions`!
this.modifiers.forEach(function(modifierOptions){if(modifierOptions.enabled&&isFunction(modifierOptions.onLoad)){modifierOptions.onLoad(_this.reference,_this.popper,_this.options,modifierOptions,_this.state);}});// fire the first update to position the popper in the right place
this.update();var eventsEnabled=this.options.eventsEnabled;if(eventsEnabled){// setup event listeners, they will take care of update the position in specific situations
this.enableEventListeners();}this.state.eventsEnabled=eventsEnabled;}// We can't use class properties because they don't get listed in the
// class prototype and break stuff like Sinon stubs
createClass(Popper,[{key:'update',value:function update$$1(){return update.call(this);}},{key:'destroy',value:function destroy$$1(){return destroy.call(this);}},{key:'enableEventListeners',value:function enableEventListeners$$1(){return enableEventListeners.call(this);}},{key:'disableEventListeners',value:function disableEventListeners$$1(){return disableEventListeners.call(this);}/**
     * Schedule an update, it will run on the next UI update available
     * @method scheduleUpdate
     * @memberof Popper
     *//**
     * Collection of utilities useful when writing custom modifiers.
     * Starting from version 1.7, this method is available only if you
     * include `popper-utils.js` before `popper.js`.
     *
     * **DEPRECATION**: This way to access PopperUtils is deprecated
     * and will be removed in v2! Use the PopperUtils module directly instead.
     * Due to the high instability of the methods contained in Utils, we can't
     * guarantee them to follow semver. Use them at your own risk!
     * @static
     * @private
     * @type {Object}
     * @deprecated since version 1.8
     * @member Utils
     * @memberof Popper
     */}]);return Popper;}();/**
 * The `referenceObject` is an object that provides an interface compatible with Popper.js
 * and lets you use it as replacement of a real DOM node.<br />
 * You can use this method to position a popper relatively to a set of coordinates
 * in case you don't have a DOM node to use as reference.
 *
 * ```
 * new Popper(referenceObject, popperNode);
 * ```
 *
 * NB: This feature isn't supported in Internet Explorer 10
 * @name referenceObject
 * @property {Function} data.getBoundingClientRect
 * A function that returns a set of coordinates compatible with the native `getBoundingClientRect` method.
 * @property {number} data.clientWidth
 * An ES6 getter that will return the width of the virtual reference element.
 * @property {number} data.clientHeight
 * An ES6 getter that will return the height of the virtual reference element.
 */Popper.Utils=(typeof window!=='undefined'?window:global).PopperUtils;Popper.placements=placements;Popper.Defaults=Defaults;var SVGAnimatedString=function SVGAnimatedString(){};if(typeof window!=='undefined'){SVGAnimatedString=window.SVGAnimatedString;}function convertToArray(value){if(typeof value==='string'){value=value.split(' ');}return value;}/**
 * Add classes to an element.
 * This method checks to ensure that the classes don't already exist before adding them.
 * It uses el.className rather than classList in order to be IE friendly.
 * @param {object} el - The element to add the classes to.
 * @param {classes} string - List of space separated classes to be added to the element.
 */function addClasses(el,classes){var newClasses=convertToArray(classes);var classList=void 0;if(el.className instanceof SVGAnimatedString){classList=convertToArray(el.className.baseVal);}else{classList=convertToArray(el.className);}newClasses.forEach(function(newClass){if(classList.indexOf(newClass)===-1){classList.push(newClass);}});if(el instanceof SVGElement){el.setAttribute('class',classList.join(' '));}else{el.className=classList.join(' ');}}/**
 * Remove classes from an element.
 * It uses el.className rather than classList in order to be IE friendly.
 * @export
 * @param {any} el The element to remove the classes from.
 * @param {any} classes List of space separated classes to be removed from the element.
 */function removeClasses(el,classes){var newClasses=convertToArray(classes);var classList=void 0;if(el.className instanceof SVGAnimatedString){classList=convertToArray(el.className.baseVal);}else{classList=convertToArray(el.className);}newClasses.forEach(function(newClass){var index=classList.indexOf(newClass);if(index!==-1){classList.splice(index,1);}});if(el instanceof SVGElement){el.setAttribute('class',classList.join(' '));}else{el.className=classList.join(' ');}}var supportsPassive=false;if(typeof window!=='undefined'){supportsPassive=false;try{var opts=Object.defineProperty({},'passive',{get:function get(){supportsPassive=true;}});window.addEventListener('test',null,opts);}catch(e){}}var _typeof=typeof Symbol==="function"&&_typeof2(Symbol.iterator)==="symbol"?function(obj){return typeof obj==='undefined'?'undefined':_typeof2(obj);}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj==='undefined'?'undefined':_typeof2(obj);};var classCallCheck$1=function classCallCheck$1(instance,Constructor){if(!(instance instanceof Constructor)){throw new TypeError("Cannot call a class as a function");}};var createClass$1=function(){function defineProperties(target,props){for(var i=0;i<props.length;i++){var descriptor=props[i];descriptor.enumerable=descriptor.enumerable||false;descriptor.configurable=true;if("value"in descriptor)descriptor.writable=true;Object.defineProperty(target,descriptor.key,descriptor);}}return function(Constructor,protoProps,staticProps){if(protoProps)defineProperties(Constructor.prototype,protoProps);if(staticProps)defineProperties(Constructor,staticProps);return Constructor;};}();var _extends$1=Object.assign||function(target){for(var i=1;i<arguments.length;i++){var source=arguments[i];for(var key in source){if(Object.prototype.hasOwnProperty.call(source,key)){target[key]=source[key];}}}return target;};/* Forked from https://github.com/FezVrasta/popper.js/blob/master/packages/tooltip/src/index.js */var DEFAULT_OPTIONS={container:false,delay:0,html:false,placement:'top',title:'',template:'<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:'hover focus',offset:0};var openTooltips=[];var Tooltip=function(){/**
  * Create a new Tooltip.js instance
  * @class Tooltip
  * @param {HTMLElement} reference - The DOM node used as reference of the tooltip (it can be a jQuery element).
  * @param {Object} options
  * @param {String} options.placement=bottom
  *			Placement of the popper accepted values: `top(-start, -end), right(-start, -end), bottom(-start, -end),
  *			left(-start, -end)`
  * @param {HTMLElement|String|false} options.container=false - Append the tooltip to a specific element.
  * @param {Number|Object} options.delay=0
  *			Delay showing and hiding the tooltip (ms) - does not apply to manual trigger type.
  *			If a number is supplied, delay is applied to both hide/show.
  *			Object structure is: `{ show: 500, hide: 100 }`
  * @param {Boolean} options.html=false - Insert HTML into the tooltip. If false, the content will inserted with `innerText`.
  * @param {String|PlacementFunction} options.placement='top' - One of the allowed placements, or a function returning one of them.
  * @param {String} [options.template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>']
  *			Base HTML to used when creating the tooltip.
  *			The tooltip's `title` will be injected into the `.tooltip-inner` or `.tooltip__inner`.
  *			`.tooltip-arrow` or `.tooltip__arrow` will become the tooltip's arrow.
  *			The outermost wrapper element should have the `.tooltip` class.
  * @param {String|HTMLElement|TitleFunction} options.title='' - Default title value if `title` attribute isn't present.
  * @param {String} [options.trigger='hover focus']
  *			How tooltip is triggered - click, hover, focus, manual.
  *			You may pass multiple triggers; separate them with a space. `manual` cannot be combined with any other trigger.
  * @param {HTMLElement} options.boundariesElement
  *			The element used as boundaries for the tooltip. For more information refer to Popper.js'
  *			[boundariesElement docs](https://popper.js.org/popper-documentation.html)
  * @param {Number|String} options.offset=0 - Offset of the tooltip relative to its reference. For more information refer to Popper.js'
  *			[offset docs](https://popper.js.org/popper-documentation.html)
  * @param {Object} options.popperOptions={} - Popper options, will be passed directly to popper instance. For more information refer to Popper.js'
  *			[options docs](https://popper.js.org/popper-documentation.html)
  * @return {Object} instance - The generated tooltip instance
  */function Tooltip(reference,options){classCallCheck$1(this,Tooltip);_initialiseProps.call(this);// apply user options over default ones
options=_extends$1({},DEFAULT_OPTIONS,options);reference.jquery&&(reference=reference[0]);// cache reference and options
this.reference=reference;this.options=options;// set initial state
this._isOpen=false;this._init();}//
// Public methods
//
/**
  * Reveals an element's tooltip. This is considered a "manual" triggering of the tooltip.
  * Tooltips with zero-length titles are never displayed.
  * @method Tooltip#show
  * @memberof Tooltip
  *//**
  * Hides an element’s tooltip. This is considered a “manual” triggering of the tooltip.
  * @method Tooltip#hide
  * @memberof Tooltip
  *//**
  * Hides and destroys an element’s tooltip.
  * @method Tooltip#dispose
  * @memberof Tooltip
  *//**
  * Toggles an element’s tooltip. This is considered a “manual” triggering of the tooltip.
  * @method Tooltip#toggle
  * @memberof Tooltip
  */createClass$1(Tooltip,[{key:'setClasses',value:function setClasses(classes){this._classes=classes;}},{key:'setContent',value:function setContent(content){this.options.title=content;if(this._tooltipNode){this._setContent(content,this.options);}}},{key:'setOptions',value:function setOptions(options){var classesUpdated=false;var classes=options&&options.classes||directive.options.defaultClass;if(this._classes!==classes){this.setClasses(classes);classesUpdated=true;}options=getOptions(options);var needPopperUpdate=false;var needRestart=false;if(this.options.offset!==options.offset||this.options.placement!==options.placement){needPopperUpdate=true;}if(this.options.template!==options.template||this.options.trigger!==options.trigger||this.options.container!==options.container||classesUpdated){needRestart=true;}for(var key in options){this.options[key]=options[key];}if(this._tooltipNode){if(needRestart){var isOpen=this._isOpen;this.dispose();this._init();if(isOpen){this.show();}}else if(needPopperUpdate){this.popperInstance.update();}}}//
// Private methods
//
},{key:'_init',value:function _init(){// get events list
var events=typeof this.options.trigger==='string'?this.options.trigger.split(' ').filter(function(trigger){return['click','hover','focus'].indexOf(trigger)!==-1;}):[];this._isDisposed=false;this._enableDocumentTouch=events.indexOf('manual')===-1;// set event listeners
this._setEventListeners(this.reference,events,this.options);}/**
   * Creates a new tooltip node
   * @memberof Tooltip
   * @private
   * @param {HTMLElement} reference
   * @param {String} template
   * @param {String|HTMLElement|TitleFunction} title
   * @param {Boolean} allowHtml
   * @return {HTMLelement} tooltipNode
   */},{key:'_create',value:function _create(reference,template){// create tooltip element
var tooltipGenerator=window.document.createElement('div');tooltipGenerator.innerHTML=template.trim();var tooltipNode=tooltipGenerator.childNodes[0];// add unique ID to our tooltip (needed for accessibility reasons)
tooltipNode.id='tooltip_'+Math.random().toString(36).substr(2,10);// Initially hide the tooltip
// The attribute will be switched in a next frame so
// CSS transitions can play
tooltipNode.setAttribute('aria-hidden','true');if(this.options.autoHide&&this.options.trigger.indexOf('hover')!==-1){tooltipNode.addEventListener('mouseenter',this.hide);tooltipNode.addEventListener('click',this.hide);}// return the generated tooltip node
return tooltipNode;}},{key:'_setContent',value:function _setContent(content,options){var _this=this;this.asyncContent=false;this._applyContent(content,options).then(function(){_this.popperInstance.update();});}},{key:'_applyContent',value:function _applyContent(title,options){var _this2=this;return new Promise(function(resolve,reject){var allowHtml=options.html;var rootNode=_this2._tooltipNode;if(!rootNode)return;var titleNode=rootNode.querySelector(_this2.options.innerSelector);if(title.nodeType===1){// if title is a node, append it only if allowHtml is true
if(allowHtml){while(titleNode.firstChild){titleNode.removeChild(titleNode.firstChild);}titleNode.appendChild(title);}}else if(typeof title==='function'){// if title is a function, call it and set innerText or innerHtml depending by `allowHtml` value
var result=title();if(result&&typeof result.then==='function'){_this2.asyncContent=true;options.loadingClass&&addClasses(rootNode,options.loadingClass);if(options.loadingContent){_this2._applyContent(options.loadingContent,options);}result.then(function(asyncResult){options.loadingClass&&removeClasses(rootNode,options.loadingClass);return _this2._applyContent(asyncResult,options);}).then(resolve).catch(reject);}else{_this2._applyContent(result,options).then(resolve).catch(reject);}return;}else{// if it's just a simple text, set innerText or innerHtml depending by `allowHtml` value
allowHtml?titleNode.innerHTML=title:titleNode.innerText=title;}resolve();});}},{key:'_show',value:function _show(reference,options){if(options&&typeof options.container==='string'){var container=document.querySelector(options.container);if(!container)return;}clearTimeout(this._disposeTimer);options=Object.assign({},options);delete options.offset;var updateClasses=true;if(this._tooltipNode){addClasses(this._tooltipNode,this._classes);updateClasses=false;}var result=this._ensureShown(reference,options);if(updateClasses&&this._tooltipNode){addClasses(this._tooltipNode,this._classes);}addClasses(reference,['v-tooltip-open']);return result;}},{key:'_ensureShown',value:function _ensureShown(reference,options){var _this3=this;// don't show if it's already visible
if(this._isOpen){return this;}this._isOpen=true;openTooltips.push(this);// if the tooltipNode already exists, just show it
if(this._tooltipNode){this._tooltipNode.style.display='';this._tooltipNode.setAttribute('aria-hidden','false');this.popperInstance.enableEventListeners();this.popperInstance.update();if(this.asyncContent){this._setContent(options.title,options);}return this;}// get title
var title=reference.getAttribute('title')||options.title;// don't show tooltip if no title is defined
if(!title){return this;}// create tooltip node
var tooltipNode=this._create(reference,options.template);this._tooltipNode=tooltipNode;this._setContent(title,options);// Add `aria-describedby` to our reference element for accessibility reasons
reference.setAttribute('aria-describedby',tooltipNode.id);// append tooltip to container
var container=this._findContainer(options.container,reference);this._append(tooltipNode,container);var popperOptions=_extends$1({},options.popperOptions,{placement:options.placement});popperOptions.modifiers=_extends$1({},popperOptions.modifiers,{arrow:{element:this.options.arrowSelector}});if(options.boundariesElement){popperOptions.modifiers.preventOverflow={boundariesElement:options.boundariesElement};}this.popperInstance=new Popper(reference,tooltipNode,popperOptions);// Fix position
requestAnimationFrame(function(){if(!_this3._isDisposed&&_this3.popperInstance){_this3.popperInstance.update();// Show the tooltip
requestAnimationFrame(function(){if(!_this3._isDisposed){_this3._isOpen&&tooltipNode.setAttribute('aria-hidden','false');}else{_this3.dispose();}});}else{_this3.dispose();}});return this;}},{key:'_noLongerOpen',value:function _noLongerOpen(){var index=openTooltips.indexOf(this);if(index!==-1){openTooltips.splice(index,1);}}},{key:'_hide',value:function _hide()/* reference, options */{var _this4=this;// don't hide if it's already hidden
if(!this._isOpen){return this;}this._isOpen=false;this._noLongerOpen();// hide tooltipNode
this._tooltipNode.style.display='none';this._tooltipNode.setAttribute('aria-hidden','true');this.popperInstance.disableEventListeners();clearTimeout(this._disposeTimer);var disposeTime=directive.options.disposeTimeout;if(disposeTime!==null){this._disposeTimer=setTimeout(function(){if(_this4._tooltipNode){_this4._tooltipNode.removeEventListener('mouseenter',_this4.hide);_this4._tooltipNode.removeEventListener('click',_this4.hide);// Don't remove popper instance, just the HTML element
_this4._tooltipNode.parentNode.removeChild(_this4._tooltipNode);_this4._tooltipNode=null;}},disposeTime);}removeClasses(this.reference,['v-tooltip-open']);return this;}},{key:'_dispose',value:function _dispose(){var _this5=this;this._isDisposed=true;// remove event listeners first to prevent any unexpected behaviour
this._events.forEach(function(_ref){var func=_ref.func,event=_ref.event;_this5.reference.removeEventListener(event,func);});this._events=[];if(this._tooltipNode){this._hide();this._tooltipNode.removeEventListener('mouseenter',this.hide);this._tooltipNode.removeEventListener('click',this.hide);// destroy instance
this.popperInstance.destroy();// destroy tooltipNode if removeOnDestroy is not set, as popperInstance.destroy() already removes the element
if(!this.popperInstance.options.removeOnDestroy){this._tooltipNode.parentNode.removeChild(this._tooltipNode);this._tooltipNode=null;}}else{this._noLongerOpen();}return this;}},{key:'_findContainer',value:function _findContainer(container,reference){// if container is a query, get the relative element
if(typeof container==='string'){container=window.document.querySelector(container);}else if(container===false){// if container is `false`, set it to reference parent
container=reference.parentNode;}return container;}/**
   * Append tooltip to container
   * @memberof Tooltip
   * @private
   * @param {HTMLElement} tooltip
   * @param {HTMLElement|String|false} container
   */},{key:'_append',value:function _append(tooltipNode,container){container.appendChild(tooltipNode);}},{key:'_setEventListeners',value:function _setEventListeners(reference,events,options){var _this6=this;var directEvents=[];var oppositeEvents=[];events.forEach(function(event){switch(event){case'hover':directEvents.push('mouseenter');oppositeEvents.push('mouseleave');if(_this6.options.hideOnTargetClick)oppositeEvents.push('click');break;case'focus':directEvents.push('focus');oppositeEvents.push('blur');if(_this6.options.hideOnTargetClick)oppositeEvents.push('click');break;case'click':directEvents.push('click');oppositeEvents.push('click');break;}});// schedule show tooltip
directEvents.forEach(function(event){var func=function func(evt){if(_this6._isOpen===true){return;}evt.usedByTooltip=true;_this6._scheduleShow(reference,options.delay,options,evt);};_this6._events.push({event:event,func:func});reference.addEventListener(event,func);});// schedule hide tooltip
oppositeEvents.forEach(function(event){var func=function func(evt){if(evt.usedByTooltip===true){return;}_this6._scheduleHide(reference,options.delay,options,evt);};_this6._events.push({event:event,func:func});reference.addEventListener(event,func);});}},{key:'_onDocumentTouch',value:function _onDocumentTouch(event){if(this._enableDocumentTouch){this._scheduleHide(this.reference,this.options.delay,this.options,event);}}},{key:'_scheduleShow',value:function _scheduleShow(reference,delay,options/*, evt */){var _this7=this;// defaults to 0
var computedDelay=delay&&delay.show||delay||0;clearTimeout(this._scheduleTimer);this._scheduleTimer=window.setTimeout(function(){return _this7._show(reference,options);},computedDelay);}},{key:'_scheduleHide',value:function _scheduleHide(reference,delay,options,evt){var _this8=this;// defaults to 0
var computedDelay=delay&&delay.hide||delay||0;clearTimeout(this._scheduleTimer);this._scheduleTimer=window.setTimeout(function(){if(_this8._isOpen===false){return;}if(!document.body.contains(_this8._tooltipNode)){return;}// if we are hiding because of a mouseleave, we must check that the new
// reference isn't the tooltip, because in this case we don't want to hide it
if(evt.type==='mouseleave'){var isSet=_this8._setTooltipNodeEvent(evt,reference,delay,options);// if we set the new event, don't hide the tooltip yet
// the new event will take care to hide it if necessary
if(isSet){return;}}_this8._hide(reference,options);},computedDelay);}}]);return Tooltip;}();// Hide tooltips on touch devices
var _initialiseProps=function _initialiseProps(){var _this9=this;this.show=function(){_this9._show(_this9.reference,_this9.options);};this.hide=function(){_this9._hide();};this.dispose=function(){_this9._dispose();};this.toggle=function(){if(_this9._isOpen){return _this9.hide();}else{return _this9.show();}};this._events=[];this._setTooltipNodeEvent=function(evt,reference,delay,options){var relatedreference=evt.relatedreference||evt.toElement||evt.relatedTarget;var callback=function callback(evt2){var relatedreference2=evt2.relatedreference||evt2.toElement||evt2.relatedTarget;// Remove event listener after call
_this9._tooltipNode.removeEventListener(evt.type,callback);// If the new reference is not the reference element
if(!reference.contains(relatedreference2)){// Schedule to hide tooltip
_this9._scheduleHide(reference,options.delay,options,evt2);}};if(_this9._tooltipNode.contains(relatedreference)){// listen to mouseleave on the tooltip element to be able to hide the tooltip
_this9._tooltipNode.addEventListener(evt.type,callback);return true;}return false;};};if(typeof document!=='undefined'){document.addEventListener('touchstart',function(event){for(var i=0;i<openTooltips.length;i++){openTooltips[i]._onDocumentTouch(event);}},supportsPassive?{passive:true,capture:true}:true);}/**
 * Placement function, its context is the Tooltip instance.
 * @memberof Tooltip
 * @callback PlacementFunction
 * @param {HTMLElement} tooltip - tooltip DOM node.
 * @param {HTMLElement} reference - reference DOM node.
 * @return {String} placement - One of the allowed placement options.
 *//**
 * Title function, its context is the Tooltip instance.
 * @memberof Tooltip
 * @callback TitleFunction
 * @return {String} placement - The desired title.
 */var state={enabled:true};var positions=['top','top-start','top-end','right','right-start','right-end','bottom','bottom-start','bottom-end','left','left-start','left-end'];var defaultOptions={// Default tooltip placement relative to target element
defaultPlacement:'top',// Default CSS classes applied to the tooltip element
defaultClass:'vue-tooltip-theme',// Default CSS classes applied to the target element of the tooltip
defaultTargetClass:'has-tooltip',// Is the content HTML by default?
defaultHtml:true,// Default HTML template of the tooltip element
// It must include `tooltip-arrow` & `tooltip-inner` CSS classes (can be configured, see below)
// Change if the classes conflict with other libraries (for example bootstrap)
defaultTemplate:'<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',// Selector used to get the arrow element in the tooltip template
defaultArrowSelector:'.tooltip-arrow, .tooltip__arrow',// Selector used to get the inner content element in the tooltip template
defaultInnerSelector:'.tooltip-inner, .tooltip__inner',// Delay (ms)
defaultDelay:0,// Default events that trigger the tooltip
defaultTrigger:'hover focus',// Default position offset (px)
defaultOffset:0,// Default container where the tooltip will be appended
defaultContainer:'body',defaultBoundariesElement:undefined,defaultPopperOptions:{},// Class added when content is loading
defaultLoadingClass:'tooltip-loading',// Displayed when tooltip content is loading
defaultLoadingContent:'...',// Hide on mouseover tooltip
autoHide:true,// Close tooltip on click on tooltip target?
defaultHideOnTargetClick:true,// Auto destroy tooltip DOM nodes (ms)
disposeTimeout:5000,// Options for popover
popover:{defaultPlacement:'bottom',// Use the `popoverClass` prop for theming
defaultClass:'vue-popover-theme',// Base class (change if conflicts with other libraries)
defaultBaseClass:'tooltip popover',// Wrapper class (contains arrow and inner)
defaultWrapperClass:'wrapper',// Inner content class
defaultInnerClass:'tooltip-inner popover-inner',// Arrow class
defaultArrowClass:'tooltip-arrow popover-arrow',defaultDelay:0,defaultTrigger:'click',defaultOffset:0,defaultContainer:'body',defaultBoundariesElement:undefined,defaultPopperOptions:{},// Hides if clicked outside of popover
defaultAutoHide:true,// Update popper on content resize
defaultHandleResize:true}};function getOptions(options){var result={placement:typeof options.placement!=='undefined'?options.placement:directive.options.defaultPlacement,delay:typeof options.delay!=='undefined'?options.delay:directive.options.defaultDelay,html:typeof options.html!=='undefined'?options.html:directive.options.defaultHtml,template:typeof options.template!=='undefined'?options.template:directive.options.defaultTemplate,arrowSelector:typeof options.arrowSelector!=='undefined'?options.arrowSelector:directive.options.defaultArrowSelector,innerSelector:typeof options.innerSelector!=='undefined'?options.innerSelector:directive.options.defaultInnerSelector,trigger:typeof options.trigger!=='undefined'?options.trigger:directive.options.defaultTrigger,offset:typeof options.offset!=='undefined'?options.offset:directive.options.defaultOffset,container:typeof options.container!=='undefined'?options.container:directive.options.defaultContainer,boundariesElement:typeof options.boundariesElement!=='undefined'?options.boundariesElement:directive.options.defaultBoundariesElement,autoHide:typeof options.autoHide!=='undefined'?options.autoHide:directive.options.autoHide,hideOnTargetClick:typeof options.hideOnTargetClick!=='undefined'?options.hideOnTargetClick:directive.options.defaultHideOnTargetClick,loadingClass:typeof options.loadingClass!=='undefined'?options.loadingClass:directive.options.defaultLoadingClass,loadingContent:typeof options.loadingContent!=='undefined'?options.loadingContent:directive.options.defaultLoadingContent,popperOptions:_extends$1({},typeof options.popperOptions!=='undefined'?options.popperOptions:directive.options.defaultPopperOptions)};if(result.offset){var typeofOffset=_typeof(result.offset);var offset=result.offset;// One value -> switch
if(typeofOffset==='number'||typeofOffset==='string'&&offset.indexOf(',')===-1){offset='0, '+offset;}if(!result.popperOptions.modifiers){result.popperOptions.modifiers={};}result.popperOptions.modifiers.offset={offset:offset};}if(result.trigger&&result.trigger.indexOf('click')!==-1){result.hideOnTargetClick=false;}return result;}function getPlacement(value,modifiers){var placement=value.placement;for(var i=0;i<positions.length;i++){var pos=positions[i];if(modifiers[pos]){placement=pos;}}return placement;}function getContent(value){var type=typeof value==='undefined'?'undefined':_typeof(value);if(type==='string'){return value;}else if(value&&type==='object'){return value.content;}else{return false;}}function createTooltip(el,value){var modifiers=arguments.length>2&&arguments[2]!==undefined?arguments[2]:{};var content=getContent(value);var classes=typeof value.classes!=='undefined'?value.classes:directive.options.defaultClass;var opts=_extends$1({title:content},getOptions(_extends$1({},value,{placement:getPlacement(value,modifiers)})));var tooltip=el._tooltip=new Tooltip(el,opts);tooltip.setClasses(classes);tooltip._vueEl=el;// Class on target
var targetClasses=typeof value.targetClasses!=='undefined'?value.targetClasses:directive.options.defaultTargetClass;el._tooltipTargetClasses=targetClasses;addClasses(el,targetClasses);return tooltip;}function destroyTooltip(el){if(el._tooltip){el._tooltip.dispose();delete el._tooltip;delete el._tooltipOldShow;}if(el._tooltipTargetClasses){removeClasses(el,el._tooltipTargetClasses);delete el._tooltipTargetClasses;}}function bind(el,_ref){var value=_ref.value,oldValue=_ref.oldValue,modifiers=_ref.modifiers;var content=getContent(value);if(!content||!state.enabled){destroyTooltip(el);}else{var tooltip=void 0;if(el._tooltip){tooltip=el._tooltip;// Content
tooltip.setContent(content);// Options
tooltip.setOptions(_extends$1({},value,{placement:getPlacement(value,modifiers)}));}else{tooltip=createTooltip(el,value,modifiers);}// Manual show
if(typeof value.show!=='undefined'&&value.show!==el._tooltipOldShow){el._tooltipOldShow=value.show;value.show?tooltip.show():tooltip.hide();}}}var directive={options:defaultOptions,bind:bind,update:bind,unbind:function unbind(el){destroyTooltip(el);}};function addListeners(el){el.addEventListener('click',onClick);el.addEventListener('touchstart',onTouchStart,supportsPassive?{passive:true}:false);}function removeListeners(el){el.removeEventListener('click',onClick);el.removeEventListener('touchstart',onTouchStart);el.removeEventListener('touchend',onTouchEnd);el.removeEventListener('touchcancel',onTouchCancel);}function onClick(event){var el=event.currentTarget;event.closePopover=!el.$_vclosepopover_touch;event.closeAllPopover=el.$_closePopoverModifiers&&!!el.$_closePopoverModifiers.all;}function onTouchStart(event){if(event.changedTouches.length===1){var el=event.currentTarget;el.$_vclosepopover_touch=true;var touch=event.changedTouches[0];el.$_vclosepopover_touchPoint=touch;el.addEventListener('touchend',onTouchEnd);el.addEventListener('touchcancel',onTouchCancel);}}function onTouchEnd(event){var el=event.currentTarget;el.$_vclosepopover_touch=false;if(event.changedTouches.length===1){var touch=event.changedTouches[0];var firstTouch=el.$_vclosepopover_touchPoint;event.closePopover=Math.abs(touch.screenY-firstTouch.screenY)<20&&Math.abs(touch.screenX-firstTouch.screenX)<20;event.closeAllPopover=el.$_closePopoverModifiers&&!!el.$_closePopoverModifiers.all;}}function onTouchCancel(event){var el=event.currentTarget;el.$_vclosepopover_touch=false;}var vclosepopover={bind:function bind(el,_ref){var value=_ref.value,modifiers=_ref.modifiers;el.$_closePopoverModifiers=modifiers;if(typeof value==='undefined'||value){addListeners(el);}},update:function update(el,_ref2){var value=_ref2.value,oldValue=_ref2.oldValue,modifiers=_ref2.modifiers;el.$_closePopoverModifiers=modifiers;if(value!==oldValue){if(typeof value==='undefined'||value){addListeners(el);}else{removeListeners(el);}}},unbind:function unbind(el){removeListeners(el);}};function getInternetExplorerVersion(){var ua=window.navigator.userAgent;var msie=ua.indexOf('MSIE ');if(msie>0){// IE 10 or older => return version number
return parseInt(ua.substring(msie+5,ua.indexOf('.',msie)),10);}var trident=ua.indexOf('Trident/');if(trident>0){// IE 11 => return version number
var rv=ua.indexOf('rv:');return parseInt(ua.substring(rv+3,ua.indexOf('.',rv)),10);}var edge=ua.indexOf('Edge/');if(edge>0){// Edge (IE 12+) => return version number
return parseInt(ua.substring(edge+5,ua.indexOf('.',edge)),10);}// other browser
return-1;}var isIE$1=void 0;function initCompat(){if(!initCompat.init){initCompat.init=true;isIE$1=getInternetExplorerVersion()!==-1;}}var ResizeObserver={render:function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"resize-observer",attrs:{"tabindex":"-1"}});},staticRenderFns:[],_scopeId:'data-v-b329ee4c',name:'resize-observer',methods:{notify:function notify(){this.$emit('notify');},addResizeHandlers:function addResizeHandlers(){this._resizeObject.contentDocument.defaultView.addEventListener('resize',this.notify);if(this._w!==this.$el.offsetWidth||this._h!==this.$el.offsetHeight){this.notify();}},removeResizeHandlers:function removeResizeHandlers(){if(this._resizeObject&&this._resizeObject.onload){if(!isIE$1&&this._resizeObject.contentDocument){this._resizeObject.contentDocument.defaultView.removeEventListener('resize',this.notify);}delete this._resizeObject.onload;}}},mounted:function mounted(){var _this=this;initCompat();this.$nextTick(function(){_this._w=_this.$el.offsetWidth;_this._h=_this.$el.offsetHeight;});var object=document.createElement('object');this._resizeObject=object;object.setAttribute('style','display: block; position: absolute; top: 0; left: 0; height: 100%; width: 100%; overflow: hidden; pointer-events: none; z-index: -1;');object.setAttribute('aria-hidden','true');object.setAttribute('tabindex',-1);object.onload=this.addResizeHandlers;object.type='text/html';if(isIE$1){this.$el.appendChild(object);}object.data='about:blank';if(!isIE$1){this.$el.appendChild(object);}},beforeDestroy:function beforeDestroy(){this.removeResizeHandlers();}};// Install the components
function install$1(Vue){Vue.component('resize-observer',ResizeObserver);/* -- Add more components here -- */}/* -- Plugin definition & Auto-install -- *//* You shouldn't have to modify the code below */// Plugin
var plugin$2={// eslint-disable-next-line no-undef
version:"0.4.4",install:install$1};// Auto-install
var GlobalVue$1=null;if(typeof window!=='undefined'){GlobalVue$1=window.Vue;}else if(typeof global!=='undefined'){GlobalVue$1=global.Vue;}if(GlobalVue$1){GlobalVue$1.use(plugin$2);}function getDefault(key){var value=directive.options.popover[key];if(typeof value==='undefined'){return directive.options[key];}return value;}var isIOS=false;if(typeof window!=='undefined'&&typeof navigator!=='undefined'){isIOS=/iPad|iPhone|iPod/.test(navigator.userAgent)&&!window.MSStream;}var openPopovers=[];var Element=function Element(){};if(typeof window!=='undefined'){Element=window.Element;}var Popover={render:function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"v-popover",class:_vm.cssClass},[_c('span',{ref:"trigger",staticClass:"trigger",staticStyle:{"display":"inline-block"},attrs:{"aria-describedby":_vm.popoverId,"tabindex":_vm.trigger.indexOf('focus')!==-1?0:-1}},[_vm._t("default")],2),_vm._v(" "),_c('div',{ref:"popover",class:[_vm.popoverBaseClass,_vm.popoverClass,_vm.cssClass],style:{visibility:_vm.isOpen?'visible':'hidden'},attrs:{"id":_vm.popoverId,"aria-hidden":_vm.isOpen?'false':'true'}},[_c('div',{class:_vm.popoverWrapperClass},[_c('div',{ref:"inner",class:_vm.popoverInnerClass,staticStyle:{"position":"relative"}},[_c('div',[_vm._t("popover")],2),_vm._v(" "),_vm.handleResize?_c('ResizeObserver',{on:{"notify":_vm.$_handleResize}}):_vm._e()],1),_vm._v(" "),_c('div',{ref:"arrow",class:_vm.popoverArrowClass})])])]);},staticRenderFns:[],name:'VPopover',components:{ResizeObserver:ResizeObserver},props:{open:{type:Boolean,default:false},disabled:{type:Boolean,default:false},placement:{type:String,default:function _default(){return getDefault('defaultPlacement');}},delay:{type:[String,Number,Object],default:function _default(){return getDefault('defaultDelay');}},offset:{type:[String,Number],default:function _default(){return getDefault('defaultOffset');}},trigger:{type:String,default:function _default(){return getDefault('defaultTrigger');}},container:{type:[String,Object,Element,Boolean],default:function _default(){return getDefault('defaultContainer');}},boundariesElement:{type:[String,Element],default:function _default(){return getDefault('defaultBoundariesElement');}},popperOptions:{type:Object,default:function _default(){return getDefault('defaultPopperOptions');}},popoverClass:{type:[String,Array],default:function _default(){return getDefault('defaultClass');}},popoverBaseClass:{type:[String,Array],default:function _default(){return directive.options.popover.defaultBaseClass;}},popoverInnerClass:{type:[String,Array],default:function _default(){return directive.options.popover.defaultInnerClass;}},popoverWrapperClass:{type:[String,Array],default:function _default(){return directive.options.popover.defaultWrapperClass;}},popoverArrowClass:{type:[String,Array],default:function _default(){return directive.options.popover.defaultArrowClass;}},autoHide:{type:Boolean,default:function _default(){return directive.options.popover.defaultAutoHide;}},handleResize:{type:Boolean,default:function _default(){return directive.options.popover.defaultHandleResize;}},openGroup:{type:String,default:null}},data:function data(){return{isOpen:false,id:Math.random().toString(36).substr(2,10)};},computed:{cssClass:function cssClass(){return{'open':this.isOpen};},popoverId:function popoverId(){return'popover_'+this.id;}},watch:{open:function open(val){if(val){this.show();}else{this.hide();}},disabled:function disabled(val,oldVal){if(val!==oldVal){if(val){this.hide();}else if(this.open){this.show();}}},container:function container(val){if(this.isOpen&&this.popperInstance){var popoverNode=this.$refs.popover;var reference=this.$refs.trigger;var container=this.$_findContainer(this.container,reference);if(!container){console.warn('No container for popover',this);return;}container.appendChild(popoverNode);this.popperInstance.scheduleUpdate();}},trigger:function trigger(val){this.$_removeEventListeners();this.$_addEventListeners();},placement:function placement(val){var _this=this;this.$_updatePopper(function(){_this.popperInstance.options.placement=val;});},offset:'$_restartPopper',boundariesElement:'$_restartPopper',popperOptions:{handler:'$_restartPopper',deep:true}},created:function created(){this.$_isDisposed=false;this.$_mounted=false;this.$_events=[];this.$_preventOpen=false;},mounted:function mounted(){var popoverNode=this.$refs.popover;popoverNode.parentNode&&popoverNode.parentNode.removeChild(popoverNode);this.$_init();if(this.open){this.show();}},beforeDestroy:function beforeDestroy(){this.dispose();},methods:{show:function show(){var _this2=this;var _ref=arguments.length>0&&arguments[0]!==undefined?arguments[0]:{},event=_ref.event,_ref$skipDelay=_ref.skipDelay,skipDelay=_ref$skipDelay===undefined?false:_ref$skipDelay,_ref$force=_ref.force,force=_ref$force===undefined?false:_ref$force;if(force||!this.disabled){this.$_scheduleShow(event);this.$emit('show');}this.$emit('update:open',true);this.$_beingShowed=true;requestAnimationFrame(function(){_this2.$_beingShowed=false;});},hide:function hide(){var _ref2=arguments.length>0&&arguments[0]!==undefined?arguments[0]:{},event=_ref2.event,_ref2$skipDelay=_ref2.skipDelay;this.$_scheduleHide(event);this.$emit('hide');this.$emit('update:open',false);},dispose:function dispose(){this.$_isDisposed=true;this.$_removeEventListeners();this.hide({skipDelay:true});if(this.popperInstance){this.popperInstance.destroy();// destroy tooltipNode if removeOnDestroy is not set, as popperInstance.destroy() already removes the element
if(!this.popperInstance.options.removeOnDestroy){var popoverNode=this.$refs.popover;popoverNode.parentNode&&popoverNode.parentNode.removeChild(popoverNode);}}this.$_mounted=false;this.popperInstance=null;this.isOpen=false;this.$emit('dispose');},$_init:function $_init(){if(this.trigger.indexOf('manual')===-1){this.$_addEventListeners();}},$_show:function $_show(){var _this3=this;var reference=this.$refs.trigger;var popoverNode=this.$refs.popover;clearTimeout(this.$_disposeTimer);// Already open
if(this.isOpen){return;}// Popper is already initialized
if(this.popperInstance){this.isOpen=true;this.popperInstance.enableEventListeners();this.popperInstance.scheduleUpdate();}if(!this.$_mounted){var container=this.$_findContainer(this.container,reference);if(!container){console.warn('No container for popover',this);return;}container.appendChild(popoverNode);this.$_mounted=true;}if(!this.popperInstance){var popperOptions=_extends$1({},this.popperOptions,{placement:this.placement});popperOptions.modifiers=_extends$1({},popperOptions.modifiers,{arrow:_extends$1({},popperOptions.modifiers&&popperOptions.modifiers.arrow,{element:this.$refs.arrow})});if(this.offset){var offset=this.$_getOffset();popperOptions.modifiers.offset=_extends$1({},popperOptions.modifiers&&popperOptions.modifiers.offset,{offset:offset});}if(this.boundariesElement){popperOptions.modifiers.preventOverflow=_extends$1({},popperOptions.modifiers&&popperOptions.modifiers.preventOverflow,{boundariesElement:this.boundariesElement});}this.popperInstance=new Popper(reference,popoverNode,popperOptions);// Fix position
requestAnimationFrame(function(){if(!_this3.$_isDisposed&&_this3.popperInstance){_this3.popperInstance.scheduleUpdate();// Show the tooltip
requestAnimationFrame(function(){if(!_this3.$_isDisposed){_this3.isOpen=true;}else{_this3.dispose();}});}else{_this3.dispose();}});}var openGroup=this.openGroup;if(openGroup){var popover=void 0;for(var i=0;i<openPopovers.length;i++){popover=openPopovers[i];if(popover.openGroup!==openGroup){popover.hide();popover.$emit('close-group');}}}openPopovers.push(this);this.$emit('apply-show');},$_hide:function $_hide(){var _this4=this;// Already hidden
if(!this.isOpen){return;}var index=openPopovers.indexOf(this);if(index!==-1){openPopovers.splice(index,1);}this.isOpen=false;if(this.popperInstance){this.popperInstance.disableEventListeners();}clearTimeout(this.$_disposeTimer);var disposeTime=directive.options.popover.disposeTimeout||directive.options.disposeTimeout;if(disposeTime!==null){this.$_disposeTimer=setTimeout(function(){var popoverNode=_this4.$refs.popover;if(popoverNode){// Don't remove popper instance, just the HTML element
popoverNode.parentNode&&popoverNode.parentNode.removeChild(popoverNode);_this4.$_mounted=false;}},disposeTime);}this.$emit('apply-hide');},$_findContainer:function $_findContainer(container,reference){// if container is a query, get the relative element
if(typeof container==='string'){container=window.document.querySelector(container);}else if(container===false){// if container is `false`, set it to reference parent
container=reference.parentNode;}return container;},$_getOffset:function $_getOffset(){var typeofOffset=_typeof(this.offset);var offset=this.offset;// One value -> switch
if(typeofOffset==='number'||typeofOffset==='string'&&offset.indexOf(',')===-1){offset='0, '+offset;}return offset;},$_addEventListeners:function $_addEventListeners(){var _this5=this;var reference=this.$refs.trigger;var directEvents=[];var oppositeEvents=[];var events=typeof this.trigger==='string'?this.trigger.split(' ').filter(function(trigger){return['click','hover','focus'].indexOf(trigger)!==-1;}):[];events.forEach(function(event){switch(event){case'hover':directEvents.push('mouseenter');oppositeEvents.push('mouseleave');break;case'focus':directEvents.push('focus');oppositeEvents.push('blur');break;case'click':directEvents.push('click');oppositeEvents.push('click');break;}});// schedule show tooltip
directEvents.forEach(function(event){var func=function func(event){if(_this5.isOpen){return;}event.usedByTooltip=true;!_this5.$_preventOpen&&_this5.show({event:event});};_this5.$_events.push({event:event,func:func});reference.addEventListener(event,func);});// schedule hide tooltip
oppositeEvents.forEach(function(event){var func=function func(event){if(event.usedByTooltip){return;}_this5.hide({event:event});};_this5.$_events.push({event:event,func:func});reference.addEventListener(event,func);});},$_scheduleShow:function $_scheduleShow(){var skipDelay=arguments.length>1&&arguments[1]!==undefined?arguments[1]:false;clearTimeout(this.$_scheduleTimer);if(skipDelay){this.$_show();}else{// defaults to 0
var computedDelay=parseInt(this.delay&&this.delay.show||this.delay||0);this.$_scheduleTimer=setTimeout(this.$_show.bind(this),computedDelay);}},$_scheduleHide:function $_scheduleHide(){var _this6=this;var event=arguments.length>0&&arguments[0]!==undefined?arguments[0]:null;var skipDelay=arguments.length>1&&arguments[1]!==undefined?arguments[1]:false;clearTimeout(this.$_scheduleTimer);if(skipDelay){this.$_hide();}else{// defaults to 0
var computedDelay=parseInt(this.delay&&this.delay.hide||this.delay||0);this.$_scheduleTimer=setTimeout(function(){if(!_this6.isOpen){return;}// if we are hiding because of a mouseleave, we must check that the new
// reference isn't the tooltip, because in this case we don't want to hide it
if(event&&event.type==='mouseleave'){var isSet=_this6.$_setTooltipNodeEvent(event);// if we set the new event, don't hide the tooltip yet
// the new event will take care to hide it if necessary
if(isSet){return;}}_this6.$_hide();},computedDelay);}},$_setTooltipNodeEvent:function $_setTooltipNodeEvent(event){var _this7=this;var reference=this.$refs.trigger;var popoverNode=this.$refs.popover;var relatedreference=event.relatedreference||event.toElement||event.relatedTarget;var callback=function callback(event2){var relatedreference2=event2.relatedreference||event2.toElement||event2.relatedTarget;// Remove event listener after call
popoverNode.removeEventListener(event.type,callback);// If the new reference is not the reference element
if(!reference.contains(relatedreference2)){// Schedule to hide tooltip
_this7.hide({event:event2});}};if(popoverNode.contains(relatedreference)){// listen to mouseleave on the tooltip element to be able to hide the tooltip
popoverNode.addEventListener(event.type,callback);return true;}return false;},$_removeEventListeners:function $_removeEventListeners(){var reference=this.$refs.trigger;this.$_events.forEach(function(_ref3){var func=_ref3.func,event=_ref3.event;reference.removeEventListener(event,func);});this.$_events=[];},$_updatePopper:function $_updatePopper(cb){if(this.popperInstance){cb();if(this.isOpen)this.popperInstance.scheduleUpdate();}},$_restartPopper:function $_restartPopper(){if(this.popperInstance){var isOpen=this.isOpen;this.dispose();this.$_isDisposed=false;this.$_init();if(isOpen){this.show({skipDelay:true,force:true});}}},$_handleGlobalClose:function $_handleGlobalClose(event){var _this8=this;var touch=arguments.length>1&&arguments[1]!==undefined?arguments[1]:false;if(this.$_beingShowed)return;this.hide({event:event});if(event.closePopover){this.$emit('close-directive');}else{this.$emit('auto-hide');}if(touch){this.$_preventOpen=true;setTimeout(function(){_this8.$_preventOpen=false;},300);}},$_handleResize:function $_handleResize(){if(this.isOpen&&this.popperInstance){this.popperInstance.scheduleUpdate();this.$emit('resize');}}}};if(typeof document!=='undefined'&&typeof window!=='undefined'){if(isIOS){document.addEventListener('touchend',handleGlobalTouchend,supportsPassive?{passive:true,capture:true}:true);}else{window.addEventListener('click',handleGlobalClick,true);}}function handleGlobalClick(event){handleGlobalClose(event);}function handleGlobalTouchend(event){handleGlobalClose(event,true);}function handleGlobalClose(event){var touch=arguments.length>1&&arguments[1]!==undefined?arguments[1]:false;// Delay so that close directive has time to set values
requestAnimationFrame(function(){var popover=void 0;for(var i=0;i<openPopovers.length;i++){popover=openPopovers[i];if(popover.$refs.popover){var contains=popover.$refs.popover.contains(event.target);if(event.closeAllPopover||event.closePopover&&contains||popover.autoHide&&!contains){popover.$_handleGlobalClose(event,touch);}}}});}var commonjsGlobal=typeof window!=='undefined'?window:typeof global!=='undefined'?global:typeof self!=='undefined'?self:{};function createCommonjsModule(fn,module){return module={exports:{}},fn(module,module.exports),module.exports;}var lodash_merge=createCommonjsModule(function(module,exports){/**
 * Lodash (Custom Build) <https://lodash.com/>
 * Build: `lodash modularize exports="npm" -o ./`
 * Copyright JS Foundation and other contributors <https://js.foundation/>
 * Released under MIT license <https://lodash.com/license>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 *//** Used as the size to enable large array optimizations. */var LARGE_ARRAY_SIZE=200;/** Used to stand-in for `undefined` hash values. */var HASH_UNDEFINED='__lodash_hash_undefined__';/** Used to detect hot functions by number of calls within a span of milliseconds. */var HOT_COUNT=800,HOT_SPAN=16;/** Used as references for various `Number` constants. */var MAX_SAFE_INTEGER=9007199254740991;/** `Object#toString` result references. */var argsTag='[object Arguments]',arrayTag='[object Array]',asyncTag='[object AsyncFunction]',boolTag='[object Boolean]',dateTag='[object Date]',errorTag='[object Error]',funcTag='[object Function]',genTag='[object GeneratorFunction]',mapTag='[object Map]',numberTag='[object Number]',nullTag='[object Null]',objectTag='[object Object]',proxyTag='[object Proxy]',regexpTag='[object RegExp]',setTag='[object Set]',stringTag='[object String]',undefinedTag='[object Undefined]',weakMapTag='[object WeakMap]';var arrayBufferTag='[object ArrayBuffer]',dataViewTag='[object DataView]',float32Tag='[object Float32Array]',float64Tag='[object Float64Array]',int8Tag='[object Int8Array]',int16Tag='[object Int16Array]',int32Tag='[object Int32Array]',uint8Tag='[object Uint8Array]',uint8ClampedTag='[object Uint8ClampedArray]',uint16Tag='[object Uint16Array]',uint32Tag='[object Uint32Array]';/**
 * Used to match `RegExp`
 * [syntax characters](http://ecma-international.org/ecma-262/7.0/#sec-patterns).
 */var reRegExpChar=/[\\^$.*+?()[\]{}|]/g;/** Used to detect host constructors (Safari). */var reIsHostCtor=/^\[object .+?Constructor\]$/;/** Used to detect unsigned integer values. */var reIsUint=/^(?:0|[1-9]\d*)$/;/** Used to identify `toStringTag` values of typed arrays. */var typedArrayTags={};typedArrayTags[float32Tag]=typedArrayTags[float64Tag]=typedArrayTags[int8Tag]=typedArrayTags[int16Tag]=typedArrayTags[int32Tag]=typedArrayTags[uint8Tag]=typedArrayTags[uint8ClampedTag]=typedArrayTags[uint16Tag]=typedArrayTags[uint32Tag]=true;typedArrayTags[argsTag]=typedArrayTags[arrayTag]=typedArrayTags[arrayBufferTag]=typedArrayTags[boolTag]=typedArrayTags[dataViewTag]=typedArrayTags[dateTag]=typedArrayTags[errorTag]=typedArrayTags[funcTag]=typedArrayTags[mapTag]=typedArrayTags[numberTag]=typedArrayTags[objectTag]=typedArrayTags[regexpTag]=typedArrayTags[setTag]=typedArrayTags[stringTag]=typedArrayTags[weakMapTag]=false;/** Detect free variable `global` from Node.js. */var freeGlobal=(typeof commonjsGlobal==='undefined'?'undefined':_typeof2(commonjsGlobal))=='object'&&commonjsGlobal&&commonjsGlobal.Object===Object&&commonjsGlobal;/** Detect free variable `self`. */var freeSelf=(typeof self==='undefined'?'undefined':_typeof2(self))=='object'&&self&&self.Object===Object&&self;/** Used as a reference to the global object. */var root=freeGlobal||freeSelf||Function('return this')();/** Detect free variable `exports`. */var freeExports='object'=='object'&&exports&&!exports.nodeType&&exports;/** Detect free variable `module`. */var freeModule=freeExports&&'object'=='object'&&module&&!module.nodeType&&module;/** Detect the popular CommonJS extension `module.exports`. */var moduleExports=freeModule&&freeModule.exports===freeExports;/** Detect free variable `process` from Node.js. */var freeProcess=moduleExports&&freeGlobal.process;/** Used to access faster Node.js helpers. */var nodeUtil=function(){try{return freeProcess&&freeProcess.binding&&freeProcess.binding('util');}catch(e){}}();/* Node.js helper references. */var nodeIsTypedArray=nodeUtil&&nodeUtil.isTypedArray;/**
 * A faster alternative to `Function#apply`, this function invokes `func`
 * with the `this` binding of `thisArg` and the arguments of `args`.
 *
 * @private
 * @param {Function} func The function to invoke.
 * @param {*} thisArg The `this` binding of `func`.
 * @param {Array} args The arguments to invoke `func` with.
 * @returns {*} Returns the result of `func`.
 */function apply(func,thisArg,args){switch(args.length){case 0:return func.call(thisArg);case 1:return func.call(thisArg,args[0]);case 2:return func.call(thisArg,args[0],args[1]);case 3:return func.call(thisArg,args[0],args[1],args[2]);}return func.apply(thisArg,args);}/**
 * The base implementation of `_.times` without support for iteratee shorthands
 * or max array length checks.
 *
 * @private
 * @param {number} n The number of times to invoke `iteratee`.
 * @param {Function} iteratee The function invoked per iteration.
 * @returns {Array} Returns the array of results.
 */function baseTimes(n,iteratee){var index=-1,result=Array(n);while(++index<n){result[index]=iteratee(index);}return result;}/**
 * The base implementation of `_.unary` without support for storing metadata.
 *
 * @private
 * @param {Function} func The function to cap arguments for.
 * @returns {Function} Returns the new capped function.
 */function baseUnary(func){return function(value){return func(value);};}/**
 * Gets the value at `key` of `object`.
 *
 * @private
 * @param {Object} [object] The object to query.
 * @param {string} key The key of the property to get.
 * @returns {*} Returns the property value.
 */function getValue(object,key){return object==null?undefined:object[key];}/**
 * Creates a unary function that invokes `func` with its argument transformed.
 *
 * @private
 * @param {Function} func The function to wrap.
 * @param {Function} transform The argument transform.
 * @returns {Function} Returns the new function.
 */function overArg(func,transform){return function(arg){return func(transform(arg));};}/**
 * Gets the value at `key`, unless `key` is "__proto__".
 *
 * @private
 * @param {Object} object The object to query.
 * @param {string} key The key of the property to get.
 * @returns {*} Returns the property value.
 */function safeGet(object,key){return key=='__proto__'?undefined:object[key];}/** Used for built-in method references. */var arrayProto=Array.prototype,funcProto=Function.prototype,objectProto=Object.prototype;/** Used to detect overreaching core-js shims. */var coreJsData=root['__core-js_shared__'];/** Used to resolve the decompiled source of functions. */var funcToString=funcProto.toString;/** Used to check objects for own properties. */var hasOwnProperty=objectProto.hasOwnProperty;/** Used to detect methods masquerading as native. */var maskSrcKey=function(){var uid=/[^.]+$/.exec(coreJsData&&coreJsData.keys&&coreJsData.keys.IE_PROTO||'');return uid?'Symbol(src)_1.'+uid:'';}();/**
 * Used to resolve the
 * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
 * of values.
 */var nativeObjectToString=objectProto.toString;/** Used to infer the `Object` constructor. */var objectCtorString=funcToString.call(Object);/** Used to detect if a method is native. */var reIsNative=RegExp('^'+funcToString.call(hasOwnProperty).replace(reRegExpChar,'\\$&').replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g,'$1.*?')+'$');/** Built-in value references. */var Buffer=moduleExports?root.Buffer:undefined,_Symbol=root.Symbol,Uint8Array=root.Uint8Array,allocUnsafe=Buffer?Buffer.allocUnsafe:undefined,getPrototype=overArg(Object.getPrototypeOf,Object),objectCreate=Object.create,propertyIsEnumerable=objectProto.propertyIsEnumerable,splice=arrayProto.splice,symToStringTag=_Symbol?_Symbol.toStringTag:undefined;var defineProperty=function(){try{var func=getNative(Object,'defineProperty');func({},'',{});return func;}catch(e){}}();/* Built-in method references for those with the same name as other `lodash` methods. */var nativeIsBuffer=Buffer?Buffer.isBuffer:undefined,nativeMax=Math.max,nativeNow=Date.now;/* Built-in method references that are verified to be native. */var Map=getNative(root,'Map'),nativeCreate=getNative(Object,'create');/**
 * The base implementation of `_.create` without support for assigning
 * properties to the created object.
 *
 * @private
 * @param {Object} proto The object to inherit from.
 * @returns {Object} Returns the new object.
 */var baseCreate=function(){function object(){}return function(proto){if(!isObject(proto)){return{};}if(objectCreate){return objectCreate(proto);}object.prototype=proto;var result=new object();object.prototype=undefined;return result;};}();/**
 * Creates a hash object.
 *
 * @private
 * @constructor
 * @param {Array} [entries] The key-value pairs to cache.
 */function Hash(entries){var index=-1,length=entries==null?0:entries.length;this.clear();while(++index<length){var entry=entries[index];this.set(entry[0],entry[1]);}}/**
 * Removes all key-value entries from the hash.
 *
 * @private
 * @name clear
 * @memberOf Hash
 */function hashClear(){this.__data__=nativeCreate?nativeCreate(null):{};this.size=0;}/**
 * Removes `key` and its value from the hash.
 *
 * @private
 * @name delete
 * @memberOf Hash
 * @param {Object} hash The hash to modify.
 * @param {string} key The key of the value to remove.
 * @returns {boolean} Returns `true` if the entry was removed, else `false`.
 */function hashDelete(key){var result=this.has(key)&&delete this.__data__[key];this.size-=result?1:0;return result;}/**
 * Gets the hash value for `key`.
 *
 * @private
 * @name get
 * @memberOf Hash
 * @param {string} key The key of the value to get.
 * @returns {*} Returns the entry value.
 */function hashGet(key){var data=this.__data__;if(nativeCreate){var result=data[key];return result===HASH_UNDEFINED?undefined:result;}return hasOwnProperty.call(data,key)?data[key]:undefined;}/**
 * Checks if a hash value for `key` exists.
 *
 * @private
 * @name has
 * @memberOf Hash
 * @param {string} key The key of the entry to check.
 * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
 */function hashHas(key){var data=this.__data__;return nativeCreate?data[key]!==undefined:hasOwnProperty.call(data,key);}/**
 * Sets the hash `key` to `value`.
 *
 * @private
 * @name set
 * @memberOf Hash
 * @param {string} key The key of the value to set.
 * @param {*} value The value to set.
 * @returns {Object} Returns the hash instance.
 */function hashSet(key,value){var data=this.__data__;this.size+=this.has(key)?0:1;data[key]=nativeCreate&&value===undefined?HASH_UNDEFINED:value;return this;}// Add methods to `Hash`.
Hash.prototype.clear=hashClear;Hash.prototype['delete']=hashDelete;Hash.prototype.get=hashGet;Hash.prototype.has=hashHas;Hash.prototype.set=hashSet;/**
 * Creates an list cache object.
 *
 * @private
 * @constructor
 * @param {Array} [entries] The key-value pairs to cache.
 */function ListCache(entries){var index=-1,length=entries==null?0:entries.length;this.clear();while(++index<length){var entry=entries[index];this.set(entry[0],entry[1]);}}/**
 * Removes all key-value entries from the list cache.
 *
 * @private
 * @name clear
 * @memberOf ListCache
 */function listCacheClear(){this.__data__=[];this.size=0;}/**
 * Removes `key` and its value from the list cache.
 *
 * @private
 * @name delete
 * @memberOf ListCache
 * @param {string} key The key of the value to remove.
 * @returns {boolean} Returns `true` if the entry was removed, else `false`.
 */function listCacheDelete(key){var data=this.__data__,index=assocIndexOf(data,key);if(index<0){return false;}var lastIndex=data.length-1;if(index==lastIndex){data.pop();}else{splice.call(data,index,1);}--this.size;return true;}/**
 * Gets the list cache value for `key`.
 *
 * @private
 * @name get
 * @memberOf ListCache
 * @param {string} key The key of the value to get.
 * @returns {*} Returns the entry value.
 */function listCacheGet(key){var data=this.__data__,index=assocIndexOf(data,key);return index<0?undefined:data[index][1];}/**
 * Checks if a list cache value for `key` exists.
 *
 * @private
 * @name has
 * @memberOf ListCache
 * @param {string} key The key of the entry to check.
 * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
 */function listCacheHas(key){return assocIndexOf(this.__data__,key)>-1;}/**
 * Sets the list cache `key` to `value`.
 *
 * @private
 * @name set
 * @memberOf ListCache
 * @param {string} key The key of the value to set.
 * @param {*} value The value to set.
 * @returns {Object} Returns the list cache instance.
 */function listCacheSet(key,value){var data=this.__data__,index=assocIndexOf(data,key);if(index<0){++this.size;data.push([key,value]);}else{data[index][1]=value;}return this;}// Add methods to `ListCache`.
ListCache.prototype.clear=listCacheClear;ListCache.prototype['delete']=listCacheDelete;ListCache.prototype.get=listCacheGet;ListCache.prototype.has=listCacheHas;ListCache.prototype.set=listCacheSet;/**
 * Creates a map cache object to store key-value pairs.
 *
 * @private
 * @constructor
 * @param {Array} [entries] The key-value pairs to cache.
 */function MapCache(entries){var index=-1,length=entries==null?0:entries.length;this.clear();while(++index<length){var entry=entries[index];this.set(entry[0],entry[1]);}}/**
 * Removes all key-value entries from the map.
 *
 * @private
 * @name clear
 * @memberOf MapCache
 */function mapCacheClear(){this.size=0;this.__data__={'hash':new Hash(),'map':new(Map||ListCache)(),'string':new Hash()};}/**
 * Removes `key` and its value from the map.
 *
 * @private
 * @name delete
 * @memberOf MapCache
 * @param {string} key The key of the value to remove.
 * @returns {boolean} Returns `true` if the entry was removed, else `false`.
 */function mapCacheDelete(key){var result=getMapData(this,key)['delete'](key);this.size-=result?1:0;return result;}/**
 * Gets the map value for `key`.
 *
 * @private
 * @name get
 * @memberOf MapCache
 * @param {string} key The key of the value to get.
 * @returns {*} Returns the entry value.
 */function mapCacheGet(key){return getMapData(this,key).get(key);}/**
 * Checks if a map value for `key` exists.
 *
 * @private
 * @name has
 * @memberOf MapCache
 * @param {string} key The key of the entry to check.
 * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
 */function mapCacheHas(key){return getMapData(this,key).has(key);}/**
 * Sets the map `key` to `value`.
 *
 * @private
 * @name set
 * @memberOf MapCache
 * @param {string} key The key of the value to set.
 * @param {*} value The value to set.
 * @returns {Object} Returns the map cache instance.
 */function mapCacheSet(key,value){var data=getMapData(this,key),size=data.size;data.set(key,value);this.size+=data.size==size?0:1;return this;}// Add methods to `MapCache`.
MapCache.prototype.clear=mapCacheClear;MapCache.prototype['delete']=mapCacheDelete;MapCache.prototype.get=mapCacheGet;MapCache.prototype.has=mapCacheHas;MapCache.prototype.set=mapCacheSet;/**
 * Creates a stack cache object to store key-value pairs.
 *
 * @private
 * @constructor
 * @param {Array} [entries] The key-value pairs to cache.
 */function Stack(entries){var data=this.__data__=new ListCache(entries);this.size=data.size;}/**
 * Removes all key-value entries from the stack.
 *
 * @private
 * @name clear
 * @memberOf Stack
 */function stackClear(){this.__data__=new ListCache();this.size=0;}/**
 * Removes `key` and its value from the stack.
 *
 * @private
 * @name delete
 * @memberOf Stack
 * @param {string} key The key of the value to remove.
 * @returns {boolean} Returns `true` if the entry was removed, else `false`.
 */function stackDelete(key){var data=this.__data__,result=data['delete'](key);this.size=data.size;return result;}/**
 * Gets the stack value for `key`.
 *
 * @private
 * @name get
 * @memberOf Stack
 * @param {string} key The key of the value to get.
 * @returns {*} Returns the entry value.
 */function stackGet(key){return this.__data__.get(key);}/**
 * Checks if a stack value for `key` exists.
 *
 * @private
 * @name has
 * @memberOf Stack
 * @param {string} key The key of the entry to check.
 * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
 */function stackHas(key){return this.__data__.has(key);}/**
 * Sets the stack `key` to `value`.
 *
 * @private
 * @name set
 * @memberOf Stack
 * @param {string} key The key of the value to set.
 * @param {*} value The value to set.
 * @returns {Object} Returns the stack cache instance.
 */function stackSet(key,value){var data=this.__data__;if(data instanceof ListCache){var pairs=data.__data__;if(!Map||pairs.length<LARGE_ARRAY_SIZE-1){pairs.push([key,value]);this.size=++data.size;return this;}data=this.__data__=new MapCache(pairs);}data.set(key,value);this.size=data.size;return this;}// Add methods to `Stack`.
Stack.prototype.clear=stackClear;Stack.prototype['delete']=stackDelete;Stack.prototype.get=stackGet;Stack.prototype.has=stackHas;Stack.prototype.set=stackSet;/**
 * Creates an array of the enumerable property names of the array-like `value`.
 *
 * @private
 * @param {*} value The value to query.
 * @param {boolean} inherited Specify returning inherited property names.
 * @returns {Array} Returns the array of property names.
 */function arrayLikeKeys(value,inherited){var isArr=isArray(value),isArg=!isArr&&isArguments(value),isBuff=!isArr&&!isArg&&isBuffer(value),isType=!isArr&&!isArg&&!isBuff&&isTypedArray(value),skipIndexes=isArr||isArg||isBuff||isType,result=skipIndexes?baseTimes(value.length,String):[],length=result.length;for(var key in value){if((inherited||hasOwnProperty.call(value,key))&&!(skipIndexes&&(// Safari 9 has enumerable `arguments.length` in strict mode.
key=='length'||// Node.js 0.10 has enumerable non-index properties on buffers.
isBuff&&(key=='offset'||key=='parent')||// PhantomJS 2 has enumerable non-index properties on typed arrays.
isType&&(key=='buffer'||key=='byteLength'||key=='byteOffset')||// Skip index properties.
isIndex(key,length)))){result.push(key);}}return result;}/**
 * This function is like `assignValue` except that it doesn't assign
 * `undefined` values.
 *
 * @private
 * @param {Object} object The object to modify.
 * @param {string} key The key of the property to assign.
 * @param {*} value The value to assign.
 */function assignMergeValue(object,key,value){if(value!==undefined&&!eq(object[key],value)||value===undefined&&!(key in object)){baseAssignValue(object,key,value);}}/**
 * Assigns `value` to `key` of `object` if the existing value is not equivalent
 * using [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
 * for equality comparisons.
 *
 * @private
 * @param {Object} object The object to modify.
 * @param {string} key The key of the property to assign.
 * @param {*} value The value to assign.
 */function assignValue(object,key,value){var objValue=object[key];if(!(hasOwnProperty.call(object,key)&&eq(objValue,value))||value===undefined&&!(key in object)){baseAssignValue(object,key,value);}}/**
 * Gets the index at which the `key` is found in `array` of key-value pairs.
 *
 * @private
 * @param {Array} array The array to inspect.
 * @param {*} key The key to search for.
 * @returns {number} Returns the index of the matched value, else `-1`.
 */function assocIndexOf(array,key){var length=array.length;while(length--){if(eq(array[length][0],key)){return length;}}return-1;}/**
 * The base implementation of `assignValue` and `assignMergeValue` without
 * value checks.
 *
 * @private
 * @param {Object} object The object to modify.
 * @param {string} key The key of the property to assign.
 * @param {*} value The value to assign.
 */function baseAssignValue(object,key,value){if(key=='__proto__'&&defineProperty){defineProperty(object,key,{'configurable':true,'enumerable':true,'value':value,'writable':true});}else{object[key]=value;}}/**
 * The base implementation of `baseForOwn` which iterates over `object`
 * properties returned by `keysFunc` and invokes `iteratee` for each property.
 * Iteratee functions may exit iteration early by explicitly returning `false`.
 *
 * @private
 * @param {Object} object The object to iterate over.
 * @param {Function} iteratee The function invoked per iteration.
 * @param {Function} keysFunc The function to get the keys of `object`.
 * @returns {Object} Returns `object`.
 */var baseFor=createBaseFor();/**
 * The base implementation of `getTag` without fallbacks for buggy environments.
 *
 * @private
 * @param {*} value The value to query.
 * @returns {string} Returns the `toStringTag`.
 */function baseGetTag(value){if(value==null){return value===undefined?undefinedTag:nullTag;}return symToStringTag&&symToStringTag in Object(value)?getRawTag(value):objectToString(value);}/**
 * The base implementation of `_.isArguments`.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an `arguments` object,
 */function baseIsArguments(value){return isObjectLike(value)&&baseGetTag(value)==argsTag;}/**
 * The base implementation of `_.isNative` without bad shim checks.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a native function,
 *  else `false`.
 */function baseIsNative(value){if(!isObject(value)||isMasked(value)){return false;}var pattern=isFunction(value)?reIsNative:reIsHostCtor;return pattern.test(toSource(value));}/**
 * The base implementation of `_.isTypedArray` without Node.js optimizations.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a typed array, else `false`.
 */function baseIsTypedArray(value){return isObjectLike(value)&&isLength(value.length)&&!!typedArrayTags[baseGetTag(value)];}/**
 * The base implementation of `_.keysIn` which doesn't treat sparse arrays as dense.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of property names.
 */function baseKeysIn(object){if(!isObject(object)){return nativeKeysIn(object);}var isProto=isPrototype(object),result=[];for(var key in object){if(!(key=='constructor'&&(isProto||!hasOwnProperty.call(object,key)))){result.push(key);}}return result;}/**
 * The base implementation of `_.merge` without support for multiple sources.
 *
 * @private
 * @param {Object} object The destination object.
 * @param {Object} source The source object.
 * @param {number} srcIndex The index of `source`.
 * @param {Function} [customizer] The function to customize merged values.
 * @param {Object} [stack] Tracks traversed source values and their merged
 *  counterparts.
 */function baseMerge(object,source,srcIndex,customizer,stack){if(object===source){return;}baseFor(source,function(srcValue,key){if(isObject(srcValue)){stack||(stack=new Stack());baseMergeDeep(object,source,key,srcIndex,baseMerge,customizer,stack);}else{var newValue=customizer?customizer(safeGet(object,key),srcValue,key+'',object,source,stack):undefined;if(newValue===undefined){newValue=srcValue;}assignMergeValue(object,key,newValue);}},keysIn);}/**
 * A specialized version of `baseMerge` for arrays and objects which performs
 * deep merges and tracks traversed objects enabling objects with circular
 * references to be merged.
 *
 * @private
 * @param {Object} object The destination object.
 * @param {Object} source The source object.
 * @param {string} key The key of the value to merge.
 * @param {number} srcIndex The index of `source`.
 * @param {Function} mergeFunc The function to merge values.
 * @param {Function} [customizer] The function to customize assigned values.
 * @param {Object} [stack] Tracks traversed source values and their merged
 *  counterparts.
 */function baseMergeDeep(object,source,key,srcIndex,mergeFunc,customizer,stack){var objValue=safeGet(object,key),srcValue=safeGet(source,key),stacked=stack.get(srcValue);if(stacked){assignMergeValue(object,key,stacked);return;}var newValue=customizer?customizer(objValue,srcValue,key+'',object,source,stack):undefined;var isCommon=newValue===undefined;if(isCommon){var isArr=isArray(srcValue),isBuff=!isArr&&isBuffer(srcValue),isTyped=!isArr&&!isBuff&&isTypedArray(srcValue);newValue=srcValue;if(isArr||isBuff||isTyped){if(isArray(objValue)){newValue=objValue;}else if(isArrayLikeObject(objValue)){newValue=copyArray(objValue);}else if(isBuff){isCommon=false;newValue=cloneBuffer(srcValue,true);}else if(isTyped){isCommon=false;newValue=cloneTypedArray(srcValue,true);}else{newValue=[];}}else if(isPlainObject(srcValue)||isArguments(srcValue)){newValue=objValue;if(isArguments(objValue)){newValue=toPlainObject(objValue);}else if(!isObject(objValue)||srcIndex&&isFunction(objValue)){newValue=initCloneObject(srcValue);}}else{isCommon=false;}}if(isCommon){// Recursively merge objects and arrays (susceptible to call stack limits).
stack.set(srcValue,newValue);mergeFunc(newValue,srcValue,srcIndex,customizer,stack);stack['delete'](srcValue);}assignMergeValue(object,key,newValue);}/**
 * The base implementation of `_.rest` which doesn't validate or coerce arguments.
 *
 * @private
 * @param {Function} func The function to apply a rest parameter to.
 * @param {number} [start=func.length-1] The start position of the rest parameter.
 * @returns {Function} Returns the new function.
 */function baseRest(func,start){return setToString(overRest(func,start,identity),func+'');}/**
 * The base implementation of `setToString` without support for hot loop shorting.
 *
 * @private
 * @param {Function} func The function to modify.
 * @param {Function} string The `toString` result.
 * @returns {Function} Returns `func`.
 */var baseSetToString=!defineProperty?identity:function(func,string){return defineProperty(func,'toString',{'configurable':true,'enumerable':false,'value':constant(string),'writable':true});};/**
 * Creates a clone of  `buffer`.
 *
 * @private
 * @param {Buffer} buffer The buffer to clone.
 * @param {boolean} [isDeep] Specify a deep clone.
 * @returns {Buffer} Returns the cloned buffer.
 */function cloneBuffer(buffer,isDeep){if(isDeep){return buffer.slice();}var length=buffer.length,result=allocUnsafe?allocUnsafe(length):new buffer.constructor(length);buffer.copy(result);return result;}/**
 * Creates a clone of `arrayBuffer`.
 *
 * @private
 * @param {ArrayBuffer} arrayBuffer The array buffer to clone.
 * @returns {ArrayBuffer} Returns the cloned array buffer.
 */function cloneArrayBuffer(arrayBuffer){var result=new arrayBuffer.constructor(arrayBuffer.byteLength);new Uint8Array(result).set(new Uint8Array(arrayBuffer));return result;}/**
 * Creates a clone of `typedArray`.
 *
 * @private
 * @param {Object} typedArray The typed array to clone.
 * @param {boolean} [isDeep] Specify a deep clone.
 * @returns {Object} Returns the cloned typed array.
 */function cloneTypedArray(typedArray,isDeep){var buffer=isDeep?cloneArrayBuffer(typedArray.buffer):typedArray.buffer;return new typedArray.constructor(buffer,typedArray.byteOffset,typedArray.length);}/**
 * Copies the values of `source` to `array`.
 *
 * @private
 * @param {Array} source The array to copy values from.
 * @param {Array} [array=[]] The array to copy values to.
 * @returns {Array} Returns `array`.
 */function copyArray(source,array){var index=-1,length=source.length;array||(array=Array(length));while(++index<length){array[index]=source[index];}return array;}/**
 * Copies properties of `source` to `object`.
 *
 * @private
 * @param {Object} source The object to copy properties from.
 * @param {Array} props The property identifiers to copy.
 * @param {Object} [object={}] The object to copy properties to.
 * @param {Function} [customizer] The function to customize copied values.
 * @returns {Object} Returns `object`.
 */function copyObject(source,props,object,customizer){var isNew=!object;object||(object={});var index=-1,length=props.length;while(++index<length){var key=props[index];var newValue=customizer?customizer(object[key],source[key],key,object,source):undefined;if(newValue===undefined){newValue=source[key];}if(isNew){baseAssignValue(object,key,newValue);}else{assignValue(object,key,newValue);}}return object;}/**
 * Creates a function like `_.assign`.
 *
 * @private
 * @param {Function} assigner The function to assign values.
 * @returns {Function} Returns the new assigner function.
 */function createAssigner(assigner){return baseRest(function(object,sources){var index=-1,length=sources.length,customizer=length>1?sources[length-1]:undefined,guard=length>2?sources[2]:undefined;customizer=assigner.length>3&&typeof customizer=='function'?(length--,customizer):undefined;if(guard&&isIterateeCall(sources[0],sources[1],guard)){customizer=length<3?undefined:customizer;length=1;}object=Object(object);while(++index<length){var source=sources[index];if(source){assigner(object,source,index,customizer);}}return object;});}/**
 * Creates a base function for methods like `_.forIn` and `_.forOwn`.
 *
 * @private
 * @param {boolean} [fromRight] Specify iterating from right to left.
 * @returns {Function} Returns the new base function.
 */function createBaseFor(fromRight){return function(object,iteratee,keysFunc){var index=-1,iterable=Object(object),props=keysFunc(object),length=props.length;while(length--){var key=props[fromRight?length:++index];if(iteratee(iterable[key],key,iterable)===false){break;}}return object;};}/**
 * Gets the data for `map`.
 *
 * @private
 * @param {Object} map The map to query.
 * @param {string} key The reference key.
 * @returns {*} Returns the map data.
 */function getMapData(map,key){var data=map.__data__;return isKeyable(key)?data[typeof key=='string'?'string':'hash']:data.map;}/**
 * Gets the native function at `key` of `object`.
 *
 * @private
 * @param {Object} object The object to query.
 * @param {string} key The key of the method to get.
 * @returns {*} Returns the function if it's native, else `undefined`.
 */function getNative(object,key){var value=getValue(object,key);return baseIsNative(value)?value:undefined;}/**
 * A specialized version of `baseGetTag` which ignores `Symbol.toStringTag` values.
 *
 * @private
 * @param {*} value The value to query.
 * @returns {string} Returns the raw `toStringTag`.
 */function getRawTag(value){var isOwn=hasOwnProperty.call(value,symToStringTag),tag=value[symToStringTag];try{value[symToStringTag]=undefined;var unmasked=true;}catch(e){}var result=nativeObjectToString.call(value);if(unmasked){if(isOwn){value[symToStringTag]=tag;}else{delete value[symToStringTag];}}return result;}/**
 * Initializes an object clone.
 *
 * @private
 * @param {Object} object The object to clone.
 * @returns {Object} Returns the initialized clone.
 */function initCloneObject(object){return typeof object.constructor=='function'&&!isPrototype(object)?baseCreate(getPrototype(object)):{};}/**
 * Checks if `value` is a valid array-like index.
 *
 * @private
 * @param {*} value The value to check.
 * @param {number} [length=MAX_SAFE_INTEGER] The upper bounds of a valid index.
 * @returns {boolean} Returns `true` if `value` is a valid index, else `false`.
 */function isIndex(value,length){var type=typeof value==='undefined'?'undefined':_typeof2(value);length=length==null?MAX_SAFE_INTEGER:length;return!!length&&(type=='number'||type!='symbol'&&reIsUint.test(value))&&value>-1&&value%1==0&&value<length;}/**
 * Checks if the given arguments are from an iteratee call.
 *
 * @private
 * @param {*} value The potential iteratee value argument.
 * @param {*} index The potential iteratee index or key argument.
 * @param {*} object The potential iteratee object argument.
 * @returns {boolean} Returns `true` if the arguments are from an iteratee call,
 *  else `false`.
 */function isIterateeCall(value,index,object){if(!isObject(object)){return false;}var type=typeof index==='undefined'?'undefined':_typeof2(index);if(type=='number'?isArrayLike(object)&&isIndex(index,object.length):type=='string'&&index in object){return eq(object[index],value);}return false;}/**
 * Checks if `value` is suitable for use as unique object key.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is suitable, else `false`.
 */function isKeyable(value){var type=typeof value==='undefined'?'undefined':_typeof2(value);return type=='string'||type=='number'||type=='symbol'||type=='boolean'?value!=='__proto__':value===null;}/**
 * Checks if `func` has its source masked.
 *
 * @private
 * @param {Function} func The function to check.
 * @returns {boolean} Returns `true` if `func` is masked, else `false`.
 */function isMasked(func){return!!maskSrcKey&&maskSrcKey in func;}/**
 * Checks if `value` is likely a prototype object.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a prototype, else `false`.
 */function isPrototype(value){var Ctor=value&&value.constructor,proto=typeof Ctor=='function'&&Ctor.prototype||objectProto;return value===proto;}/**
 * This function is like
 * [`Object.keys`](http://ecma-international.org/ecma-262/7.0/#sec-object.keys)
 * except that it includes inherited enumerable properties.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of property names.
 */function nativeKeysIn(object){var result=[];if(object!=null){for(var key in Object(object)){result.push(key);}}return result;}/**
 * Converts `value` to a string using `Object.prototype.toString`.
 *
 * @private
 * @param {*} value The value to convert.
 * @returns {string} Returns the converted string.
 */function objectToString(value){return nativeObjectToString.call(value);}/**
 * A specialized version of `baseRest` which transforms the rest array.
 *
 * @private
 * @param {Function} func The function to apply a rest parameter to.
 * @param {number} [start=func.length-1] The start position of the rest parameter.
 * @param {Function} transform The rest array transform.
 * @returns {Function} Returns the new function.
 */function overRest(func,start,transform){start=nativeMax(start===undefined?func.length-1:start,0);return function(){var args=arguments,index=-1,length=nativeMax(args.length-start,0),array=Array(length);while(++index<length){array[index]=args[start+index];}index=-1;var otherArgs=Array(start+1);while(++index<start){otherArgs[index]=args[index];}otherArgs[start]=transform(array);return apply(func,this,otherArgs);};}/**
 * Sets the `toString` method of `func` to return `string`.
 *
 * @private
 * @param {Function} func The function to modify.
 * @param {Function} string The `toString` result.
 * @returns {Function} Returns `func`.
 */var setToString=shortOut(baseSetToString);/**
 * Creates a function that'll short out and invoke `identity` instead
 * of `func` when it's called `HOT_COUNT` or more times in `HOT_SPAN`
 * milliseconds.
 *
 * @private
 * @param {Function} func The function to restrict.
 * @returns {Function} Returns the new shortable function.
 */function shortOut(func){var count=0,lastCalled=0;return function(){var stamp=nativeNow(),remaining=HOT_SPAN-(stamp-lastCalled);lastCalled=stamp;if(remaining>0){if(++count>=HOT_COUNT){return arguments[0];}}else{count=0;}return func.apply(undefined,arguments);};}/**
 * Converts `func` to its source code.
 *
 * @private
 * @param {Function} func The function to convert.
 * @returns {string} Returns the source code.
 */function toSource(func){if(func!=null){try{return funcToString.call(func);}catch(e){}try{return func+'';}catch(e){}}return'';}/**
 * Performs a
 * [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
 * comparison between two values to determine if they are equivalent.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to compare.
 * @param {*} other The other value to compare.
 * @returns {boolean} Returns `true` if the values are equivalent, else `false`.
 * @example
 *
 * var object = { 'a': 1 };
 * var other = { 'a': 1 };
 *
 * _.eq(object, object);
 * // => true
 *
 * _.eq(object, other);
 * // => false
 *
 * _.eq('a', 'a');
 * // => true
 *
 * _.eq('a', Object('a'));
 * // => false
 *
 * _.eq(NaN, NaN);
 * // => true
 */function eq(value,other){return value===other||value!==value&&other!==other;}/**
 * Checks if `value` is likely an `arguments` object.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an `arguments` object,
 *  else `false`.
 * @example
 *
 * _.isArguments(function() { return arguments; }());
 * // => true
 *
 * _.isArguments([1, 2, 3]);
 * // => false
 */var isArguments=baseIsArguments(function(){return arguments;}())?baseIsArguments:function(value){return isObjectLike(value)&&hasOwnProperty.call(value,'callee')&&!propertyIsEnumerable.call(value,'callee');};/**
 * Checks if `value` is classified as an `Array` object.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an array, else `false`.
 * @example
 *
 * _.isArray([1, 2, 3]);
 * // => true
 *
 * _.isArray(document.body.children);
 * // => false
 *
 * _.isArray('abc');
 * // => false
 *
 * _.isArray(_.noop);
 * // => false
 */var isArray=Array.isArray;/**
 * Checks if `value` is array-like. A value is considered array-like if it's
 * not a function and has a `value.length` that's an integer greater than or
 * equal to `0` and less than or equal to `Number.MAX_SAFE_INTEGER`.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is array-like, else `false`.
 * @example
 *
 * _.isArrayLike([1, 2, 3]);
 * // => true
 *
 * _.isArrayLike(document.body.children);
 * // => true
 *
 * _.isArrayLike('abc');
 * // => true
 *
 * _.isArrayLike(_.noop);
 * // => false
 */function isArrayLike(value){return value!=null&&isLength(value.length)&&!isFunction(value);}/**
 * This method is like `_.isArrayLike` except that it also checks if `value`
 * is an object.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an array-like object,
 *  else `false`.
 * @example
 *
 * _.isArrayLikeObject([1, 2, 3]);
 * // => true
 *
 * _.isArrayLikeObject(document.body.children);
 * // => true
 *
 * _.isArrayLikeObject('abc');
 * // => false
 *
 * _.isArrayLikeObject(_.noop);
 * // => false
 */function isArrayLikeObject(value){return isObjectLike(value)&&isArrayLike(value);}/**
 * Checks if `value` is a buffer.
 *
 * @static
 * @memberOf _
 * @since 4.3.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a buffer, else `false`.
 * @example
 *
 * _.isBuffer(new Buffer(2));
 * // => true
 *
 * _.isBuffer(new Uint8Array(2));
 * // => false
 */var isBuffer=nativeIsBuffer||stubFalse;/**
 * Checks if `value` is classified as a `Function` object.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a function, else `false`.
 * @example
 *
 * _.isFunction(_);
 * // => true
 *
 * _.isFunction(/abc/);
 * // => false
 */function isFunction(value){if(!isObject(value)){return false;}// The use of `Object#toString` avoids issues with the `typeof` operator
// in Safari 9 which returns 'object' for typed arrays and other constructors.
var tag=baseGetTag(value);return tag==funcTag||tag==genTag||tag==asyncTag||tag==proxyTag;}/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This method is loosely based on
 * [`ToLength`](http://ecma-international.org/ecma-262/7.0/#sec-tolength).
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 * @example
 *
 * _.isLength(3);
 * // => true
 *
 * _.isLength(Number.MIN_VALUE);
 * // => false
 *
 * _.isLength(Infinity);
 * // => false
 *
 * _.isLength('3');
 * // => false
 */function isLength(value){return typeof value=='number'&&value>-1&&value%1==0&&value<=MAX_SAFE_INTEGER;}/**
 * Checks if `value` is the
 * [language type](http://www.ecma-international.org/ecma-262/7.0/#sec-ecmascript-language-types)
 * of `Object`. (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(_.noop);
 * // => true
 *
 * _.isObject(null);
 * // => false
 */function isObject(value){var type=typeof value==='undefined'?'undefined':_typeof2(value);return value!=null&&(type=='object'||type=='function');}/**
 * Checks if `value` is object-like. A value is object-like if it's not `null`
 * and has a `typeof` result of "object".
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 * @example
 *
 * _.isObjectLike({});
 * // => true
 *
 * _.isObjectLike([1, 2, 3]);
 * // => true
 *
 * _.isObjectLike(_.noop);
 * // => false
 *
 * _.isObjectLike(null);
 * // => false
 */function isObjectLike(value){return value!=null&&(typeof value==='undefined'?'undefined':_typeof2(value))=='object';}/**
 * Checks if `value` is a plain object, that is, an object created by the
 * `Object` constructor or one with a `[[Prototype]]` of `null`.
 *
 * @static
 * @memberOf _
 * @since 0.8.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a plain object, else `false`.
 * @example
 *
 * function Foo() {
 *   this.a = 1;
 * }
 *
 * _.isPlainObject(new Foo);
 * // => false
 *
 * _.isPlainObject([1, 2, 3]);
 * // => false
 *
 * _.isPlainObject({ 'x': 0, 'y': 0 });
 * // => true
 *
 * _.isPlainObject(Object.create(null));
 * // => true
 */function isPlainObject(value){if(!isObjectLike(value)||baseGetTag(value)!=objectTag){return false;}var proto=getPrototype(value);if(proto===null){return true;}var Ctor=hasOwnProperty.call(proto,'constructor')&&proto.constructor;return typeof Ctor=='function'&&Ctor instanceof Ctor&&funcToString.call(Ctor)==objectCtorString;}/**
 * Checks if `value` is classified as a typed array.
 *
 * @static
 * @memberOf _
 * @since 3.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a typed array, else `false`.
 * @example
 *
 * _.isTypedArray(new Uint8Array);
 * // => true
 *
 * _.isTypedArray([]);
 * // => false
 */var isTypedArray=nodeIsTypedArray?baseUnary(nodeIsTypedArray):baseIsTypedArray;/**
 * Converts `value` to a plain object flattening inherited enumerable string
 * keyed properties of `value` to own properties of the plain object.
 *
 * @static
 * @memberOf _
 * @since 3.0.0
 * @category Lang
 * @param {*} value The value to convert.
 * @returns {Object} Returns the converted plain object.
 * @example
 *
 * function Foo() {
 *   this.b = 2;
 * }
 *
 * Foo.prototype.c = 3;
 *
 * _.assign({ 'a': 1 }, new Foo);
 * // => { 'a': 1, 'b': 2 }
 *
 * _.assign({ 'a': 1 }, _.toPlainObject(new Foo));
 * // => { 'a': 1, 'b': 2, 'c': 3 }
 */function toPlainObject(value){return copyObject(value,keysIn(value));}/**
 * Creates an array of the own and inherited enumerable property names of `object`.
 *
 * **Note:** Non-object values are coerced to objects.
 *
 * @static
 * @memberOf _
 * @since 3.0.0
 * @category Object
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of property names.
 * @example
 *
 * function Foo() {
 *   this.a = 1;
 *   this.b = 2;
 * }
 *
 * Foo.prototype.c = 3;
 *
 * _.keysIn(new Foo);
 * // => ['a', 'b', 'c'] (iteration order is not guaranteed)
 */function keysIn(object){return isArrayLike(object)?arrayLikeKeys(object,true):baseKeysIn(object);}/**
 * This method is like `_.assign` except that it recursively merges own and
 * inherited enumerable string keyed properties of source objects into the
 * destination object. Source properties that resolve to `undefined` are
 * skipped if a destination value exists. Array and plain object properties
 * are merged recursively. Other objects and value types are overridden by
 * assignment. Source objects are applied from left to right. Subsequent
 * sources overwrite property assignments of previous sources.
 *
 * **Note:** This method mutates `object`.
 *
 * @static
 * @memberOf _
 * @since 0.5.0
 * @category Object
 * @param {Object} object The destination object.
 * @param {...Object} [sources] The source objects.
 * @returns {Object} Returns `object`.
 * @example
 *
 * var object = {
 *   'a': [{ 'b': 2 }, { 'd': 4 }]
 * };
 *
 * var other = {
 *   'a': [{ 'c': 3 }, { 'e': 5 }]
 * };
 *
 * _.merge(object, other);
 * // => { 'a': [{ 'b': 2, 'c': 3 }, { 'd': 4, 'e': 5 }] }
 */var merge=createAssigner(function(object,source,srcIndex){baseMerge(object,source,srcIndex);});/**
 * Creates a function that returns `value`.
 *
 * @static
 * @memberOf _
 * @since 2.4.0
 * @category Util
 * @param {*} value The value to return from the new function.
 * @returns {Function} Returns the new constant function.
 * @example
 *
 * var objects = _.times(2, _.constant({ 'a': 1 }));
 *
 * console.log(objects);
 * // => [{ 'a': 1 }, { 'a': 1 }]
 *
 * console.log(objects[0] === objects[1]);
 * // => true
 */function constant(value){return function(){return value;};}/**
 * This method returns the first argument it receives.
 *
 * @static
 * @since 0.1.0
 * @memberOf _
 * @category Util
 * @param {*} value Any value.
 * @returns {*} Returns `value`.
 * @example
 *
 * var object = { 'a': 1 };
 *
 * console.log(_.identity(object) === object);
 * // => true
 */function identity(value){return value;}/**
 * This method returns `false`.
 *
 * @static
 * @memberOf _
 * @since 4.13.0
 * @category Util
 * @returns {boolean} Returns `false`.
 * @example
 *
 * _.times(2, _.stubFalse);
 * // => [false, false]
 */function stubFalse(){return false;}module.exports=merge;});function install(Vue){var options=arguments.length>1&&arguments[1]!==undefined?arguments[1]:{};if(install.installed)return;install.installed=true;var finalOptions={};lodash_merge(finalOptions,defaultOptions,options);plugin.options=finalOptions;directive.options=finalOptions;Vue.directive('tooltip',directive);Vue.directive('close-popover',vclosepopover);Vue.component('v-popover',Popover);}var VTooltip=directive;var VClosePopover=vclosepopover;var VPopover=Popover;var plugin={install:install,get enabled(){return state.enabled;},set enabled(value){state.enabled=value;}};// Auto-install
var GlobalVue=null;if(typeof window!=='undefined'){GlobalVue=window.Vue;}else if(typeof global!=='undefined'){GlobalVue=global.Vue;}if(GlobalVue){GlobalVue.use(plugin);}/* harmony default export */__webpack_exports__["a"]=plugin;/* WEBPACK VAR INJECTION */}).call(__webpack_exports__,__webpack_require__(5));/***/},/* 33 *//***/function(module,exports,__webpack_require__){!function(t,e){true?module.exports=e():"function"==typeof define&&define.amd?define([],e):"object"==(typeof exports==='undefined'?'undefined':_typeof2(exports))?exports.VueMultiselect=e():t.VueMultiselect=e();}(this,function(){return function(t){function e(i){if(n[i])return n[i].exports;var r=n[i]={i:i,l:!1,exports:{}};return t[i].call(r.exports,r,r.exports,e),r.l=!0,r.exports;}var n={};return e.m=t,e.c=n,e.i=function(t){return t;},e.d=function(t,n,i){e.o(t,n)||Object.defineProperty(t,n,{configurable:!1,enumerable:!0,get:i});},e.n=function(t){var n=t&&t.__esModule?function(){return t.default;}:function(){return t;};return e.d(n,"a",n),n;},e.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e);},e.p="/",e(e.s=66);}([function(t,e){var n=t.exports="undefined"!=typeof window&&window.Math==Math?window:"undefined"!=typeof self&&self.Math==Math?self:Function("return this")();"number"==typeof __g&&(__g=n);},function(t,e,n){t.exports=!n(12)(function(){return 7!=Object.defineProperty({},"a",{get:function get(){return 7;}}).a;});},function(t,e){var n={}.hasOwnProperty;t.exports=function(t,e){return n.call(t,e);};},function(t,e,n){var i=n(10),r=n(43),o=n(31),s=Object.defineProperty;e.f=n(1)?Object.defineProperty:function(t,e,n){if(i(t),e=o(e,!0),i(n),r)try{return s(t,e,n);}catch(t){}if("get"in n||"set"in n)throw TypeError("Accessors not supported!");return"value"in n&&(t[e]=n.value),t;};},function(t,e,n){var i=n(77),r=n(21);t.exports=function(t){return i(r(t));};},function(t,e,n){var i=n(9),r=n(52),o=n(18),s=n(55),u=n(53),a=function a(t,e,n){var l,c,f,p,h=t&a.F,d=t&a.G,v=t&a.S,y=t&a.P,g=t&a.B,b=d?i:v?i[e]||(i[e]={}):(i[e]||{}).prototype,m=d?r:r[e]||(r[e]={}),_=m.prototype||(m.prototype={});d&&(n=e);for(l in n){c=!h&&b&&void 0!==b[l],f=(c?b:n)[l],p=g&&c?u(f,i):y&&"function"==typeof f?u(Function.call,f):f,b&&s(b,l,f,t&a.U),m[l]!=f&&o(m,l,p),y&&_[l]!=f&&(_[l]=f);}};i.core=r,a.F=1,a.G=2,a.S=4,a.P=8,a.B=16,a.W=32,a.U=64,a.R=128,t.exports=a;},function(t,e,n){var i=n(3),r=n(15);t.exports=n(1)?function(t,e,n){return i.f(t,e,r(1,n));}:function(t,e,n){return t[e]=n,t;};},function(t,e,n){var i=n(29)("wks"),r=n(16),o=n(0).Symbol,s="function"==typeof o;(t.exports=function(t){return i[t]||(i[t]=s&&o[t]||(s?o:r)("Symbol."+t));}).store=i;},function(t,e){t.exports=function(t){try{return!!t();}catch(t){return!0;}};},function(t,e){var n=t.exports="undefined"!=typeof window&&window.Math==Math?window:"undefined"!=typeof self&&self.Math==Math?self:Function("return this")();"number"==typeof __g&&(__g=n);},function(t,e,n){var i=n(13);t.exports=function(t){if(!i(t))throw TypeError(t+" is not an object!");return t;};},function(t,e){var n=t.exports={version:"2.4.0"};"number"==typeof __e&&(__e=n);},function(t,e){t.exports=function(t){try{return!!t();}catch(t){return!0;}};},function(t,e){t.exports=function(t){return"object"==(typeof t==='undefined'?'undefined':_typeof2(t))?null!==t:"function"==typeof t;};},function(t,e,n){var i=n(48),r=n(22);t.exports=Object.keys||function(t){return i(t,r);};},function(t,e){t.exports=function(t,e){return{enumerable:!(1&t),configurable:!(2&t),writable:!(4&t),value:e};};},function(t,e){var n=0,i=Math.random();t.exports=function(t){return"Symbol(".concat(void 0===t?"":t,")_",(++n+i).toString(36));};},function(t,e){t.exports=function(t){if(void 0==t)throw TypeError("Can't call method on  "+t);return t;};},function(t,e,n){var i=n(109),r=n(110);t.exports=n(35)?function(t,e,n){return i.f(t,e,r(1,n));}:function(t,e,n){return t[e]=n,t;};},function(t,e){t.exports=function(t){return"object"==(typeof t==='undefined'?'undefined':_typeof2(t))?null!==t:"function"==typeof t;};},function(t,e,n){var i=n(8);t.exports=function(t,e){return!!t&&i(function(){e?t.call(null,function(){},1):t.call(null);});};},function(t,e){t.exports=function(t){if(void 0==t)throw TypeError("Can't call method on  "+t);return t;};},function(t,e){t.exports="constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf".split(",");},function(t,e,n){var i=n(0),r=n(11),o=n(74),s=n(6),u=function u(t,e,n){var a,l,c,f=t&u.F,p=t&u.G,h=t&u.S,d=t&u.P,v=t&u.B,y=t&u.W,g=p?r:r[e]||(r[e]={}),b=g.prototype,m=p?i:h?i[e]:(i[e]||{}).prototype;p&&(n=e);for(a in n){(l=!f&&m&&void 0!==m[a])&&a in g||(c=l?m[a]:n[a],g[a]=p&&"function"!=typeof m[a]?n[a]:v&&l?o(c,i):y&&m[a]==c?function(t){var e=function e(_e,n,i){if(this instanceof t){switch(arguments.length){case 0:return new t();case 1:return new t(_e);case 2:return new t(_e,n);}return new t(_e,n,i);}return t.apply(this,arguments);};return e.prototype=t.prototype,e;}(c):d&&"function"==typeof c?o(Function.call,c):c,d&&((g.virtual||(g.virtual={}))[a]=c,t&u.R&&b&&!b[a]&&s(b,a,c)));}};u.F=1,u.G=2,u.S=4,u.P=8,u.B=16,u.W=32,u.U=64,u.R=128,t.exports=u;},function(t,e){t.exports={};},function(t,e){t.exports=!0;},function(t,e){e.f={}.propertyIsEnumerable;},function(t,e,n){var i=n(3).f,r=n(2),o=n(7)("toStringTag");t.exports=function(t,e,n){t&&!r(t=n?t:t.prototype,o)&&i(t,o,{configurable:!0,value:e});};},function(t,e,n){var i=n(29)("keys"),r=n(16);t.exports=function(t){return i[t]||(i[t]=r(t));};},function(t,e,n){var i=n(0),r=i["__core-js_shared__"]||(i["__core-js_shared__"]={});t.exports=function(t){return r[t]||(r[t]={});};},function(t,e){var n=Math.ceil,i=Math.floor;t.exports=function(t){return isNaN(t=+t)?0:(t>0?i:n)(t);};},function(t,e,n){var i=n(13);t.exports=function(t,e){if(!i(t))return t;var n,r;if(e&&"function"==typeof(n=t.toString)&&!i(r=n.call(t)))return r;if("function"==typeof(n=t.valueOf)&&!i(r=n.call(t)))return r;if(!e&&"function"==typeof(n=t.toString)&&!i(r=n.call(t)))return r;throw TypeError("Can't convert object to primitive value");};},function(t,e,n){var i=n(0),r=n(11),o=n(25),s=n(33),u=n(3).f;t.exports=function(t){var e=r.Symbol||(r.Symbol=o?{}:i.Symbol||{});"_"==t.charAt(0)||t in e||u(e,t,{value:s.f(t)});};},function(t,e,n){e.f=n(7);},function(t,e,n){var i=n(53),r=n(36),o=n(57),s=n(37),u=n(104);t.exports=function(t,e){var n=1==t,a=2==t,l=3==t,c=4==t,f=6==t,p=5==t||f,h=e||u;return function(e,u,d){for(var v,y,g=o(e),b=r(g),m=i(u,d,3),_=s(b.length),x=0,w=n?h(e,_):a?h(e,0):void 0;_>x;x++){if((p||x in b)&&(v=b[x],y=m(v,x,g),t))if(n)w[x]=y;else if(y)switch(t){case 3:return!0;case 5:return v;case 6:return x;case 2:w.push(v);}else if(c)return!1;}return f?-1:l||c?c:w;};};},function(t,e,n){t.exports=!n(8)(function(){return 7!=Object.defineProperty({},"a",{get:function get(){return 7;}}).a;});},function(t,e,n){var i=n(51);t.exports=Object("z").propertyIsEnumerable(0)?Object:function(t){return"String"==i(t)?t.split(""):Object(t);};},function(t,e,n){var i=n(56),r=Math.min;t.exports=function(t){return t>0?r(i(t),9007199254740991):0;};},function(t,e,n){var i=n(111)("wks"),r=n(58),o=n(9).Symbol,s="function"==typeof o;(t.exports=function(t){return i[t]||(i[t]=s&&o[t]||(s?o:r)("Symbol."+t));}).store=i;},function(t,e,n){"use strict";function i(t){return 0!==t&&(!(!Array.isArray(t)||0!==t.length)||!t);}function r(t){return function(){return!t.apply(void 0,arguments);};}function o(t,e){return void 0===t&&(t="undefined"),null===t&&(t="null"),!1===t&&(t="false"),-1!==t.toString().toLowerCase().indexOf(e.trim());}function s(t,e,n,i){return t.filter(function(t){return o(i(t,n),e);});}function u(t){return t.filter(function(t){return!t.$isLabel;});}function a(t,e){return function(n){return n.reduce(function(n,i){return i[t]&&i[t].length?(n.push({$groupLabel:i[e],$isLabel:!0}),n.concat(i[t])):n;},[]);};}function l(t,e,n,i,r){return function(o){return o.map(function(o){var u;if(!o[n])return console.warn("Options passed to vue-multiselect do not contain groups, despite the config."),[];var a=s(o[n],t,e,r);return a.length?(u={},v()(u,i,o[i]),v()(u,n,a),u):[];});};}var c=n(65),f=n.n(c),p=n(59),h=(n.n(p),n(122)),d=(n.n(h),n(64)),v=n.n(d),y=n(120),g=(n.n(y),n(121)),b=(n.n(g),n(117)),m=(n.n(b),n(123)),_=(n.n(m),n(118)),x=(n.n(_),n(119)),w=(n.n(x),function(){for(var t=arguments.length,e=new Array(t),n=0;n<t;n++){e[n]=arguments[n];}return function(t){return e.reduce(function(t,e){return e(t);},t);};});e.a={data:function data(){return{search:"",isOpen:!1,prefferedOpenDirection:"below",optimizedHeight:this.maxHeight};},props:{internalSearch:{type:Boolean,default:!0},options:{type:Array,required:!0},multiple:{type:Boolean,default:!1},value:{type:null,default:function _default(){return[];}},trackBy:{type:String},label:{type:String},searchable:{type:Boolean,default:!0},clearOnSelect:{type:Boolean,default:!0},hideSelected:{type:Boolean,default:!1},placeholder:{type:String,default:"Select option"},allowEmpty:{type:Boolean,default:!0},resetAfter:{type:Boolean,default:!1},closeOnSelect:{type:Boolean,default:!0},customLabel:{type:Function,default:function _default(t,e){return i(t)?"":e?t[e]:t;}},taggable:{type:Boolean,default:!1},tagPlaceholder:{type:String,default:"Press enter to create a tag"},tagPosition:{type:String,default:"top"},max:{type:[Number,Boolean],default:!1},id:{default:null},optionsLimit:{type:Number,default:1e3},groupValues:{type:String},groupLabel:{type:String},groupSelect:{type:Boolean,default:!1},blockKeys:{type:Array,default:function _default(){return[];}},preserveSearch:{type:Boolean,default:!1},preselectFirst:{type:Boolean,default:!1}},mounted:function mounted(){this.multiple||this.clearOnSelect||console.warn("[Vue-Multiselect warn]: ClearOnSelect and Multiple props can’t be both set to false."),!this.multiple&&this.max&&console.warn("[Vue-Multiselect warn]: Max prop should not be used when prop Multiple equals false."),this.preselectFirst&&!this.internalValue.length&&this.options.length&&this.select(this.filteredOptions[0]);},computed:{internalValue:function internalValue(){return this.value||0===this.value?Array.isArray(this.value)?this.value:[this.value]:[];},filteredOptions:function filteredOptions(){var t=this.search||"",e=t.toLowerCase().trim(),n=this.options.concat();return n=this.internalSearch?this.groupValues?this.filterAndFlat(n,e,this.label):s(n,e,this.label,this.customLabel):this.groupValues?a(this.groupValues,this.groupLabel)(n):n,n=this.hideSelected?n.filter(r(this.isSelected)):n,this.taggable&&e.length&&!this.isExistingOption(e)&&("bottom"===this.tagPosition?n.push({isTag:!0,label:t}):n.unshift({isTag:!0,label:t})),n.slice(0,this.optionsLimit);},valueKeys:function valueKeys(){var t=this;return this.trackBy?this.internalValue.map(function(e){return e[t.trackBy];}):this.internalValue;},optionKeys:function optionKeys(){var t=this;return(this.groupValues?this.flatAndStrip(this.options):this.options).map(function(e){return t.customLabel(e,t.label).toString().toLowerCase();});},currentOptionLabel:function currentOptionLabel(){return this.multiple?this.searchable?"":this.placeholder:this.internalValue.length?this.getOptionLabel(this.internalValue[0]):this.searchable?"":this.placeholder;}},watch:{internalValue:function internalValue(){this.resetAfter&&this.internalValue.length&&(this.search="",this.$emit("input",this.multiple?[]:null));},search:function search(){this.$emit("search-change",this.search,this.id);}},methods:{getValue:function getValue(){return this.multiple?this.internalValue:0===this.internalValue.length?null:this.internalValue[0];},filterAndFlat:function filterAndFlat(t,e,n){return w(l(e,n,this.groupValues,this.groupLabel,this.customLabel),a(this.groupValues,this.groupLabel))(t);},flatAndStrip:function flatAndStrip(t){return w(a(this.groupValues,this.groupLabel),u)(t);},updateSearch:function updateSearch(t){this.search=t;},isExistingOption:function isExistingOption(t){return!!this.options&&this.optionKeys.indexOf(t)>-1;},isSelected:function isSelected(t){var e=this.trackBy?t[this.trackBy]:t;return this.valueKeys.indexOf(e)>-1;},getOptionLabel:function getOptionLabel(t){if(i(t))return"";if(t.isTag)return t.label;if(t.$isLabel)return t.$groupLabel;var e=this.customLabel(t,this.label);return i(e)?"":e;},select:function select(t,e){if(t.$isLabel&&this.groupSelect)return void this.selectGroup(t);if(!(-1!==this.blockKeys.indexOf(e)||this.disabled||t.$isDisabled||t.$isLabel)&&(!this.max||!this.multiple||this.internalValue.length!==this.max)&&("Tab"!==e||this.pointerDirty)){if(t.isTag)this.$emit("tag",t.label,this.id),this.search="",this.closeOnSelect&&!this.multiple&&this.deactivate();else{if(this.isSelected(t))return void("Tab"!==e&&this.removeElement(t));this.$emit("select",t,this.id),this.multiple?this.$emit("input",this.internalValue.concat([t]),this.id):this.$emit("input",t,this.id),this.clearOnSelect&&(this.search="");}this.closeOnSelect&&this.deactivate();}},selectGroup:function selectGroup(t){var e=this,n=this.options.find(function(n){return n[e.groupLabel]===t.$groupLabel;});if(n)if(this.wholeGroupSelected(n)){this.$emit("remove",n[this.groupValues],this.id);var i=this.internalValue.filter(function(t){return-1===n[e.groupValues].indexOf(t);});this.$emit("input",i,this.id);}else{var o=n[this.groupValues].filter(r(this.isSelected));this.$emit("select",o,this.id),this.$emit("input",this.internalValue.concat(o),this.id);}},wholeGroupSelected:function wholeGroupSelected(t){return t[this.groupValues].every(this.isSelected);},removeElement:function removeElement(t){var e=!(arguments.length>1&&void 0!==arguments[1])||arguments[1];if(!this.disabled){if(!this.allowEmpty&&this.internalValue.length<=1)return void this.deactivate();var n="object"===f()(t)?this.valueKeys.indexOf(t[this.trackBy]):this.valueKeys.indexOf(t);if(this.$emit("remove",t,this.id),this.multiple){var i=this.internalValue.slice(0,n).concat(this.internalValue.slice(n+1));this.$emit("input",i,this.id);}else this.$emit("input",null,this.id);this.closeOnSelect&&e&&this.deactivate();}},removeLastElement:function removeLastElement(){-1===this.blockKeys.indexOf("Delete")&&0===this.search.length&&Array.isArray(this.internalValue)&&this.removeElement(this.internalValue[this.internalValue.length-1],!1);},activate:function activate(){var t=this;this.isOpen||this.disabled||(this.adjustPosition(),this.groupValues&&0===this.pointer&&this.filteredOptions.length&&(this.pointer=1),this.isOpen=!0,this.searchable?(this.preserveSearch||(this.search=""),this.$nextTick(function(){return t.$refs.search.focus();})):this.$el.focus(),this.$emit("open",this.id));},deactivate:function deactivate(){this.isOpen&&(this.isOpen=!1,this.searchable?this.$refs.search.blur():this.$el.blur(),this.preserveSearch||(this.search=""),this.$emit("close",this.getValue(),this.id));},toggle:function toggle(){this.isOpen?this.deactivate():this.activate();},adjustPosition:function adjustPosition(){if("undefined"!=typeof window){var t=this.$el.getBoundingClientRect().top,e=window.innerHeight-this.$el.getBoundingClientRect().bottom;e>this.maxHeight||e>t||"below"===this.openDirection||"bottom"===this.openDirection?(this.prefferedOpenDirection="below",this.optimizedHeight=Math.min(e-40,this.maxHeight)):(this.prefferedOpenDirection="above",this.optimizedHeight=Math.min(t-40,this.maxHeight));}}}};},function(t,e,n){"use strict";var i=n(59);n.n(i);e.a={data:function data(){return{pointer:0,pointerDirty:!1};},props:{showPointer:{type:Boolean,default:!0},optionHeight:{type:Number,default:40}},computed:{pointerPosition:function pointerPosition(){return this.pointer*this.optionHeight;},visibleElements:function visibleElements(){return this.optimizedHeight/this.optionHeight;}},watch:{filteredOptions:function filteredOptions(){this.pointerAdjust();},isOpen:function isOpen(){this.pointerDirty=!1;}},methods:{optionHighlight:function optionHighlight(t,e){return{"multiselect__option--highlight":t===this.pointer&&this.showPointer,"multiselect__option--selected":this.isSelected(e)};},groupHighlight:function groupHighlight(t,e){var n=this;if(!this.groupSelect)return["multiselect__option--disabled"];var i=this.options.find(function(t){return t[n.groupLabel]===e.$groupLabel;});return[this.groupSelect?"multiselect__option--group":"multiselect__option--disabled",{"multiselect__option--highlight":t===this.pointer&&this.showPointer},{"multiselect__option--group-selected":this.wholeGroupSelected(i)}];},addPointerElement:function addPointerElement(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"Enter",e=t.key;this.filteredOptions.length>0&&this.select(this.filteredOptions[this.pointer],e),this.pointerReset();},pointerForward:function pointerForward(){this.pointer<this.filteredOptions.length-1&&(this.pointer++,this.$refs.list.scrollTop<=this.pointerPosition-(this.visibleElements-1)*this.optionHeight&&(this.$refs.list.scrollTop=this.pointerPosition-(this.visibleElements-1)*this.optionHeight),this.filteredOptions[this.pointer]&&this.filteredOptions[this.pointer].$isLabel&&!this.groupSelect&&this.pointerForward()),this.pointerDirty=!0;},pointerBackward:function pointerBackward(){this.pointer>0?(this.pointer--,this.$refs.list.scrollTop>=this.pointerPosition&&(this.$refs.list.scrollTop=this.pointerPosition),this.filteredOptions[this.pointer]&&this.filteredOptions[this.pointer].$isLabel&&!this.groupSelect&&this.pointerBackward()):this.filteredOptions[this.pointer]&&this.filteredOptions[0].$isLabel&&!this.groupSelect&&this.pointerForward(),this.pointerDirty=!0;},pointerReset:function pointerReset(){this.closeOnSelect&&(this.pointer=0,this.$refs.list&&(this.$refs.list.scrollTop=0));},pointerAdjust:function pointerAdjust(){this.pointer>=this.filteredOptions.length-1&&(this.pointer=this.filteredOptions.length?this.filteredOptions.length-1:0),this.filteredOptions.length>0&&this.filteredOptions[this.pointer].$isLabel&&!this.groupSelect&&this.pointerForward();},pointerSet:function pointerSet(t){this.pointer=t,this.pointerDirty=!0;}}};},function(t,e){var n={}.toString;t.exports=function(t){return n.call(t).slice(8,-1);};},function(t,e,n){var i=n(13),r=n(0).document,o=i(r)&&i(r.createElement);t.exports=function(t){return o?r.createElement(t):{};};},function(t,e,n){t.exports=!n(1)&&!n(12)(function(){return 7!=Object.defineProperty(n(42)("div"),"a",{get:function get(){return 7;}}).a;});},function(t,e,n){"use strict";var i=n(25),r=n(23),o=n(49),s=n(6),u=n(2),a=n(24),l=n(79),c=n(27),f=n(86),p=n(7)("iterator"),h=!([].keys&&"next"in[].keys()),d=function d(){return this;};t.exports=function(t,e,n,v,y,g,b){l(n,e,v);var m,_,x,w=function w(t){if(!h&&t in P)return P[t];switch(t){case"keys":case"values":return function(){return new n(this,t);};}return function(){return new n(this,t);};},S=e+" Iterator",O="values"==y,L=!1,P=t.prototype,k=P[p]||P["@@iterator"]||y&&P[y],E=k||w(y),j=y?O?w("entries"):E:void 0,V="Array"==e?P.entries||k:k;if(V&&(x=f(V.call(new t())))!==Object.prototype&&(c(x,S,!0),i||u(x,p)||s(x,p,d)),O&&k&&"values"!==k.name&&(L=!0,E=function E(){return k.call(this);}),i&&!b||!h&&!L&&P[p]||s(P,p,E),a[e]=E,a[S]=d,y)if(m={values:O?E:w("values"),keys:g?E:w("keys"),entries:j},b)for(_ in m){_ in P||o(P,_,m[_]);}else r(r.P+r.F*(h||L),e,m);return m;};},function(t,e,n){var i=n(10),r=n(83),o=n(22),s=n(28)("IE_PROTO"),u=function u(){},_a=function a(){var t,e=n(42)("iframe"),i=o.length;for(e.style.display="none",n(76).appendChild(e),e.src="javascript:",t=e.contentWindow.document,t.open(),t.write("<script>document.F=Object<\/script>"),t.close(),_a=t.F;i--;){delete _a.prototype[o[i]];}return _a();};t.exports=Object.create||function(t,e){var n;return null!==t?(u.prototype=i(t),n=new u(),u.prototype=null,n[s]=t):n=_a(),void 0===e?n:r(n,e);};},function(t,e,n){var i=n(48),r=n(22).concat("length","prototype");e.f=Object.getOwnPropertyNames||function(t){return i(t,r);};},function(t,e){e.f=Object.getOwnPropertySymbols;},function(t,e,n){var i=n(2),r=n(4),o=n(73)(!1),s=n(28)("IE_PROTO");t.exports=function(t,e){var n,u=r(t),a=0,l=[];for(n in u){n!=s&&i(u,n)&&l.push(n);}for(;e.length>a;){i(u,n=e[a++])&&(~o(l,n)||l.push(n));}return l;};},function(t,e,n){t.exports=n(6);},function(t,e){t.exports=function(t){if("function"!=typeof t)throw TypeError(t+" is not a function!");return t;};},function(t,e){var n={}.toString;t.exports=function(t){return n.call(t).slice(8,-1);};},function(t,e){var n=t.exports={version:"2.4.0"};"number"==typeof __e&&(__e=n);},function(t,e,n){var i=n(50);t.exports=function(t,e,n){if(i(t),void 0===e)return t;switch(n){case 1:return function(n){return t.call(e,n);};case 2:return function(n,i){return t.call(e,n,i);};case 3:return function(n,i,r){return t.call(e,n,i,r);};}return function(){return t.apply(e,arguments);};};},function(t,e,n){var i=n(51);t.exports=Array.isArray||function(t){return"Array"==i(t);};},function(t,e,n){var i=n(9),r=n(18),o=n(107),s=n(58)("src"),u=Function.toString,a=(""+u).split("toString");n(52).inspectSource=function(t){return u.call(t);},(t.exports=function(t,e,n,u){var l="function"==typeof n;l&&(o(n,"name")||r(n,"name",e)),t[e]!==n&&(l&&(o(n,s)||r(n,s,t[e]?""+t[e]:a.join(String(e)))),t===i?t[e]=n:u?t[e]?t[e]=n:r(t,e,n):(delete t[e],r(t,e,n)));})(Function.prototype,"toString",function(){return"function"==typeof this&&this[s]||u.call(this);});},function(t,e){var n=Math.ceil,i=Math.floor;t.exports=function(t){return isNaN(t=+t)?0:(t>0?i:n)(t);};},function(t,e,n){var i=n(17);t.exports=function(t){return Object(i(t));};},function(t,e){var n=0,i=Math.random();t.exports=function(t){return"Symbol(".concat(void 0===t?"":t,")_",(++n+i).toString(36));};},function(t,e,n){"use strict";var i=n(5),r=n(34)(5),o=!0;"find"in[]&&Array(1).find(function(){o=!1;}),i(i.P+i.F*o,"Array",{find:function find(t){return r(this,t,arguments.length>1?arguments[1]:void 0);}}),n(99)("find");},function(t,e,n){"use strict";function i(t){n(124);}var r=n(67),o=n(126),s=n(125),u=i,a=s(r.a,o.a,!1,u,null,null);e.a=a.exports;},function(t,e,n){t.exports=n(68);},function(t,e,n){t.exports=n(69);},function(t,e,n){t.exports=n(70);},function(t,e,n){function i(t,e,n){return e in t?r(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n,t;}var r=n(61);t.exports=i;},function(t,e,n){function i(t){return(i="function"==typeof s&&"symbol"==(typeof o==='undefined'?'undefined':_typeof2(o))?function(t){return typeof t==='undefined'?'undefined':_typeof2(t);}:function(t){return t&&"function"==typeof s&&t.constructor===s&&t!==s.prototype?"symbol":typeof t==='undefined'?'undefined':_typeof2(t);})(t);}function r(e){return"function"==typeof s&&"symbol"===i(o)?t.exports=r=function r(t){return i(t);}:t.exports=r=function r(t){return t&&"function"==typeof s&&t.constructor===s&&t!==s.prototype?"symbol":i(t);},r(e);}var o=n(63),s=n(62);t.exports=r;},function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var i=n(60),r=n(39),o=n(40);n.d(e,"Multiselect",function(){return i.a;}),n.d(e,"multiselectMixin",function(){return r.a;}),n.d(e,"pointerMixin",function(){return o.a;}),e.default=i.a;},function(t,e,n){"use strict";var i=n(39),r=n(40);e.a={name:"vue-multiselect",mixins:[i.a,r.a],props:{name:{type:String,default:""},selectLabel:{type:String,default:"Press enter to select"},selectGroupLabel:{type:String,default:"Press enter to select group"},selectedLabel:{type:String,default:"Selected"},deselectLabel:{type:String,default:"Press enter to remove"},deselectGroupLabel:{type:String,default:"Press enter to deselect group"},showLabels:{type:Boolean,default:!0},limit:{type:Number,default:99999},maxHeight:{type:Number,default:300},limitText:{type:Function,default:function _default(t){return"and ".concat(t," more");}},loading:{type:Boolean,default:!1},disabled:{type:Boolean,default:!1},openDirection:{type:String,default:""},showNoResults:{type:Boolean,default:!0},tabindex:{type:Number,default:0}},computed:{isSingleLabelVisible:function isSingleLabelVisible(){return this.singleValue&&(!this.isOpen||!this.searchable)&&!this.visibleValues.length;},isPlaceholderVisible:function isPlaceholderVisible(){return!(this.internalValue.length||this.searchable&&this.isOpen);},visibleValues:function visibleValues(){return this.multiple?this.internalValue.slice(0,this.limit):[];},singleValue:function singleValue(){return this.internalValue[0];},deselectLabelText:function deselectLabelText(){return this.showLabels?this.deselectLabel:"";},deselectGroupLabelText:function deselectGroupLabelText(){return this.showLabels?this.deselectGroupLabel:"";},selectLabelText:function selectLabelText(){return this.showLabels?this.selectLabel:"";},selectGroupLabelText:function selectGroupLabelText(){return this.showLabels?this.selectGroupLabel:"";},selectedLabelText:function selectedLabelText(){return this.showLabels?this.selectedLabel:"";},inputStyle:function inputStyle(){if(this.multiple&&this.value&&this.value.length)return this.isOpen?{width:"auto"}:{width:"0",position:"absolute",padding:"0"};},contentStyle:function contentStyle(){return this.options.length?{display:"inline-block"}:{display:"block"};},isAbove:function isAbove(){return"above"===this.openDirection||"top"===this.openDirection||"below"!==this.openDirection&&"bottom"!==this.openDirection&&"above"===this.prefferedOpenDirection;},showSearchInput:function showSearchInput(){return this.searchable&&(!this.hasSingleSelectedSlot||!this.visibleSingleValue&&0!==this.visibleSingleValue||this.isOpen);}}};},function(t,e,n){n(92);var i=n(11).Object;t.exports=function(t,e,n){return i.defineProperty(t,e,n);};},function(t,e,n){n(95),n(93),n(96),n(97),t.exports=n(11).Symbol;},function(t,e,n){n(94),n(98),t.exports=n(33).f("iterator");},function(t,e){t.exports=function(t){if("function"!=typeof t)throw TypeError(t+" is not a function!");return t;};},function(t,e){t.exports=function(){};},function(t,e,n){var i=n(4),r=n(89),o=n(88);t.exports=function(t){return function(e,n,s){var u,a=i(e),l=r(a.length),c=o(s,l);if(t&&n!=n){for(;l>c;){if((u=a[c++])!=u)return!0;}}else for(;l>c;c++){if((t||c in a)&&a[c]===n)return t||c||0;}return!t&&-1;};};},function(t,e,n){var i=n(71);t.exports=function(t,e,n){if(i(t),void 0===e)return t;switch(n){case 1:return function(n){return t.call(e,n);};case 2:return function(n,i){return t.call(e,n,i);};case 3:return function(n,i,r){return t.call(e,n,i,r);};}return function(){return t.apply(e,arguments);};};},function(t,e,n){var i=n(14),r=n(47),o=n(26);t.exports=function(t){var e=i(t),n=r.f;if(n)for(var s,u=n(t),a=o.f,l=0;u.length>l;){a.call(t,s=u[l++])&&e.push(s);}return e;};},function(t,e,n){t.exports=n(0).document&&document.documentElement;},function(t,e,n){var i=n(41);t.exports=Object("z").propertyIsEnumerable(0)?Object:function(t){return"String"==i(t)?t.split(""):Object(t);};},function(t,e,n){var i=n(41);t.exports=Array.isArray||function(t){return"Array"==i(t);};},function(t,e,n){"use strict";var i=n(45),r=n(15),o=n(27),s={};n(6)(s,n(7)("iterator"),function(){return this;}),t.exports=function(t,e,n){t.prototype=i(s,{next:r(1,n)}),o(t,e+" Iterator");};},function(t,e){t.exports=function(t,e){return{value:e,done:!!t};};},function(t,e,n){var i=n(14),r=n(4);t.exports=function(t,e){for(var n,o=r(t),s=i(o),u=s.length,a=0;u>a;){if(o[n=s[a++]]===e)return n;}};},function(t,e,n){var i=n(16)("meta"),r=n(13),o=n(2),s=n(3).f,u=0,a=Object.isExtensible||function(){return!0;},l=!n(12)(function(){return a(Object.preventExtensions({}));}),c=function c(t){s(t,i,{value:{i:"O"+ ++u,w:{}}});},f=function f(t,e){if(!r(t))return"symbol"==(typeof t==='undefined'?'undefined':_typeof2(t))?t:("string"==typeof t?"S":"P")+t;if(!o(t,i)){if(!a(t))return"F";if(!e)return"E";c(t);}return t[i].i;},p=function p(t,e){if(!o(t,i)){if(!a(t))return!0;if(!e)return!1;c(t);}return t[i].w;},h=function h(t){return l&&d.NEED&&a(t)&&!o(t,i)&&c(t),t;},d=t.exports={KEY:i,NEED:!1,fastKey:f,getWeak:p,onFreeze:h};},function(t,e,n){var i=n(3),r=n(10),o=n(14);t.exports=n(1)?Object.defineProperties:function(t,e){r(t);for(var n,s=o(e),u=s.length,a=0;u>a;){i.f(t,n=s[a++],e[n]);}return t;};},function(t,e,n){var i=n(26),r=n(15),o=n(4),s=n(31),u=n(2),a=n(43),l=Object.getOwnPropertyDescriptor;e.f=n(1)?l:function(t,e){if(t=o(t),e=s(e,!0),a)try{return l(t,e);}catch(t){}if(u(t,e))return r(!i.f.call(t,e),t[e]);};},function(t,e,n){var i=n(4),r=n(46).f,o={}.toString,s="object"==(typeof window==='undefined'?'undefined':_typeof2(window))&&window&&Object.getOwnPropertyNames?Object.getOwnPropertyNames(window):[],u=function u(t){try{return r(t);}catch(t){return s.slice();}};t.exports.f=function(t){return s&&"[object Window]"==o.call(t)?u(t):r(i(t));};},function(t,e,n){var i=n(2),r=n(90),o=n(28)("IE_PROTO"),s=Object.prototype;t.exports=Object.getPrototypeOf||function(t){return t=r(t),i(t,o)?t[o]:"function"==typeof t.constructor&&t instanceof t.constructor?t.constructor.prototype:t instanceof Object?s:null;};},function(t,e,n){var i=n(30),r=n(21);t.exports=function(t){return function(e,n){var o,s,u=String(r(e)),a=i(n),l=u.length;return a<0||a>=l?t?"":void 0:(o=u.charCodeAt(a),o<55296||o>56319||a+1===l||(s=u.charCodeAt(a+1))<56320||s>57343?t?u.charAt(a):o:t?u.slice(a,a+2):s-56320+(o-55296<<10)+65536);};};},function(t,e,n){var i=n(30),r=Math.max,o=Math.min;t.exports=function(t,e){return t=i(t),t<0?r(t+e,0):o(t,e);};},function(t,e,n){var i=n(30),r=Math.min;t.exports=function(t){return t>0?r(i(t),9007199254740991):0;};},function(t,e,n){var i=n(21);t.exports=function(t){return Object(i(t));};},function(t,e,n){"use strict";var i=n(72),r=n(80),o=n(24),s=n(4);t.exports=n(44)(Array,"Array",function(t,e){this._t=s(t),this._i=0,this._k=e;},function(){var t=this._t,e=this._k,n=this._i++;return!t||n>=t.length?(this._t=void 0,r(1)):"keys"==e?r(0,n):"values"==e?r(0,t[n]):r(0,[n,t[n]]);},"values"),o.Arguments=o.Array,i("keys"),i("values"),i("entries");},function(t,e,n){var i=n(23);i(i.S+i.F*!n(1),"Object",{defineProperty:n(3).f});},function(t,e){},function(t,e,n){"use strict";var i=n(87)(!0);n(44)(String,"String",function(t){this._t=String(t),this._i=0;},function(){var t,e=this._t,n=this._i;return n>=e.length?{value:void 0,done:!0}:(t=i(e,n),this._i+=t.length,{value:t,done:!1});});},function(t,e,n){"use strict";var i=n(0),r=n(2),o=n(1),s=n(23),u=n(49),a=n(82).KEY,l=n(12),c=n(29),f=n(27),p=n(16),h=n(7),d=n(33),v=n(32),y=n(81),g=n(75),b=n(78),m=n(10),_=n(4),x=n(31),w=n(15),S=n(45),O=n(85),L=n(84),P=n(3),k=n(14),E=L.f,j=P.f,V=O.f,_C=i.Symbol,T=i.JSON,A=T&&T.stringify,$=h("_hidden"),D=h("toPrimitive"),F={}.propertyIsEnumerable,M=c("symbol-registry"),B=c("symbols"),N=c("op-symbols"),R=Object.prototype,H="function"==typeof _C,G=i.QObject,I=!G||!G.prototype||!G.prototype.findChild,K=o&&l(function(){return 7!=S(j({},"a",{get:function get(){return j(this,"a",{value:7}).a;}})).a;})?function(t,e,n){var i=E(R,e);i&&delete R[e],j(t,e,n),i&&t!==R&&j(R,e,i);}:j,z=function z(t){var e=B[t]=S(_C.prototype);return e._k=t,e;},U=H&&"symbol"==_typeof2(_C.iterator)?function(t){return"symbol"==(typeof t==='undefined'?'undefined':_typeof2(t));}:function(t){return t instanceof _C;},W=function W(t,e,n){return t===R&&W(N,e,n),m(t),e=x(e,!0),m(n),r(B,e)?(n.enumerable?(r(t,$)&&t[$][e]&&(t[$][e]=!1),n=S(n,{enumerable:w(0,!1)})):(r(t,$)||j(t,$,w(1,{})),t[$][e]=!0),K(t,e,n)):j(t,e,n);},J=function J(t,e){m(t);for(var n,i=g(e=_(e)),r=0,o=i.length;o>r;){W(t,n=i[r++],e[n]);}return t;},q=function q(t,e){return void 0===e?S(t):J(S(t),e);},X=function X(t){var e=F.call(this,t=x(t,!0));return!(this===R&&r(B,t)&&!r(N,t))&&(!(e||!r(this,t)||!r(B,t)||r(this,$)&&this[$][t])||e);},Y=function Y(t,e){if(t=_(t),e=x(e,!0),t!==R||!r(B,e)||r(N,e)){var n=E(t,e);return!n||!r(B,e)||r(t,$)&&t[$][e]||(n.enumerable=!0),n;}},Q=function Q(t){for(var e,n=V(_(t)),i=[],o=0;n.length>o;){r(B,e=n[o++])||e==$||e==a||i.push(e);}return i;},Z=function Z(t){for(var e,n=t===R,i=V(n?N:_(t)),o=[],s=0;i.length>s;){!r(B,e=i[s++])||n&&!r(R,e)||o.push(B[e]);}return o;};H||(_C=function C(){if(this instanceof _C)throw TypeError("Symbol is not a constructor!");var t=p(arguments.length>0?arguments[0]:void 0),e=function e(n){this===R&&e.call(N,n),r(this,$)&&r(this[$],t)&&(this[$][t]=!1),K(this,t,w(1,n));};return o&&I&&K(R,t,{configurable:!0,set:e}),z(t);},u(_C.prototype,"toString",function(){return this._k;}),L.f=Y,P.f=W,n(46).f=O.f=Q,n(26).f=X,n(47).f=Z,o&&!n(25)&&u(R,"propertyIsEnumerable",X,!0),d.f=function(t){return z(h(t));}),s(s.G+s.W+s.F*!H,{Symbol:_C});for(var tt="hasInstance,isConcatSpreadable,iterator,match,replace,search,species,split,toPrimitive,toStringTag,unscopables".split(","),et=0;tt.length>et;){h(tt[et++]);}for(var tt=k(h.store),et=0;tt.length>et;){v(tt[et++]);}s(s.S+s.F*!H,"Symbol",{for:function _for(t){return r(M,t+="")?M[t]:M[t]=_C(t);},keyFor:function keyFor(t){if(U(t))return y(M,t);throw TypeError(t+" is not a symbol!");},useSetter:function useSetter(){I=!0;},useSimple:function useSimple(){I=!1;}}),s(s.S+s.F*!H,"Object",{create:q,defineProperty:W,defineProperties:J,getOwnPropertyDescriptor:Y,getOwnPropertyNames:Q,getOwnPropertySymbols:Z}),T&&s(s.S+s.F*(!H||l(function(){var t=_C();return"[null]"!=A([t])||"{}"!=A({a:t})||"{}"!=A(Object(t));})),"JSON",{stringify:function stringify(t){if(void 0!==t&&!U(t)){for(var e,n,i=[t],r=1;arguments.length>r;){i.push(arguments[r++]);}return e=i[1],"function"==typeof e&&(n=e),!n&&b(e)||(e=function e(t,_e2){if(n&&(_e2=n.call(this,t,_e2)),!U(_e2))return _e2;}),i[1]=e,A.apply(T,i);}}}),_C.prototype[D]||n(6)(_C.prototype,D,_C.prototype.valueOf),f(_C,"Symbol"),f(Math,"Math",!0),f(i.JSON,"JSON",!0);},function(t,e,n){n(32)("asyncIterator");},function(t,e,n){n(32)("observable");},function(t,e,n){n(91);for(var i=n(0),r=n(6),o=n(24),s=n(7)("toStringTag"),u=["NodeList","DOMTokenList","MediaList","StyleSheetList","CSSRuleList"],a=0;a<5;a++){var l=u[a],c=i[l],f=c&&c.prototype;f&&!f[s]&&r(f,s,l),o[l]=o.Array;}},function(t,e,n){var i=n(38)("unscopables"),r=Array.prototype;void 0==r[i]&&n(18)(r,i,{}),t.exports=function(t){r[i][t]=!0;};},function(t,e,n){var i=n(19);t.exports=function(t){if(!i(t))throw TypeError(t+" is not an object!");return t;};},function(t,e,n){var i=n(115),r=n(37),o=n(114);t.exports=function(t){return function(e,n,s){var u,a=i(e),l=r(a.length),c=o(s,l);if(t&&n!=n){for(;l>c;){if((u=a[c++])!=u)return!0;}}else for(;l>c;c++){if((t||c in a)&&a[c]===n)return t||c||0;}return!t&&-1;};};},function(t,e,n){var i=n(50),r=n(57),o=n(36),s=n(37);t.exports=function(t,e,n,u,a){i(e);var l=r(t),c=o(l),f=s(l.length),p=a?f-1:0,h=a?-1:1;if(n<2)for(;;){if(p in c){u=c[p],p+=h;break;}if(p+=h,a?p<0:f<=p)throw TypeError("Reduce of empty array with no initial value");}for(;a?p>=0:f>p;p+=h){p in c&&(u=e(u,c[p],p,l));}return u;};},function(t,e,n){var i=n(19),r=n(54),o=n(38)("species");t.exports=function(t){var e;return r(t)&&(e=t.constructor,"function"!=typeof e||e!==Array&&!r(e.prototype)||(e=void 0),i(e)&&null===(e=e[o])&&(e=void 0)),void 0===e?Array:e;};},function(t,e,n){var i=n(103);t.exports=function(t,e){return new(i(t))(e);};},function(t,e,n){var i=n(19),r=n(9).document,o=i(r)&&i(r.createElement);t.exports=function(t){return o?r.createElement(t):{};};},function(t,e,n){"use strict";var i=n(18),r=n(55),o=n(8),s=n(17),u=n(38);t.exports=function(t,e,n){var a=u(t),l=n(s,a,""[t]),c=l[0],f=l[1];o(function(){var e={};return e[a]=function(){return 7;},7!=""[t](e);})&&(r(String.prototype,t,c),i(RegExp.prototype,a,2==e?function(t,e){return f.call(t,this,e);}:function(t){return f.call(t,this);}));};},function(t,e){var n={}.hasOwnProperty;t.exports=function(t,e){return n.call(t,e);};},function(t,e,n){t.exports=!n(35)&&!n(8)(function(){return 7!=Object.defineProperty(n(105)("div"),"a",{get:function get(){return 7;}}).a;});},function(t,e,n){var i=n(100),r=n(108),o=n(116),s=Object.defineProperty;e.f=n(35)?Object.defineProperty:function(t,e,n){if(i(t),e=o(e,!0),i(n),r)try{return s(t,e,n);}catch(t){}if("get"in n||"set"in n)throw TypeError("Accessors not supported!");return"value"in n&&(t[e]=n.value),t;};},function(t,e){t.exports=function(t,e){return{enumerable:!(1&t),configurable:!(2&t),writable:!(4&t),value:e};};},function(t,e,n){var i=n(9),r=i["__core-js_shared__"]||(i["__core-js_shared__"]={});t.exports=function(t){return r[t]||(r[t]={});};},function(t,e,n){var i=n(5),r=n(17),o=n(8),s=n(113),u="["+s+"]",a="​",l=RegExp("^"+u+u+"*"),c=RegExp(u+u+"*$"),f=function f(t,e,n){var r={},u=o(function(){return!!s[t]()||a[t]()!=a;}),l=r[t]=u?e(p):s[t];n&&(r[n]=l),i(i.P+i.F*u,"String",r);},p=f.trim=function(t,e){return t=String(r(t)),1&e&&(t=t.replace(l,"")),2&e&&(t=t.replace(c,"")),t;};t.exports=f;},function(t,e){t.exports='\t\n\x0B\f\r \xA0\u1680\u180E\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';},function(t,e,n){var i=n(56),r=Math.max,o=Math.min;t.exports=function(t,e){return t=i(t),t<0?r(t+e,0):o(t,e);};},function(t,e,n){var i=n(36),r=n(17);t.exports=function(t){return i(r(t));};},function(t,e,n){var i=n(19);t.exports=function(t,e){if(!i(t))return t;var n,r;if(e&&"function"==typeof(n=t.toString)&&!i(r=n.call(t)))return r;if("function"==typeof(n=t.valueOf)&&!i(r=n.call(t)))return r;if(!e&&"function"==typeof(n=t.toString)&&!i(r=n.call(t)))return r;throw TypeError("Can't convert object to primitive value");};},function(t,e,n){"use strict";var i=n(5),r=n(34)(2);i(i.P+i.F*!n(20)([].filter,!0),"Array",{filter:function filter(t){return r(this,t,arguments[1]);}});},function(t,e,n){"use strict";var i=n(5),r=n(101)(!1),o=[].indexOf,s=!!o&&1/[1].indexOf(1,-0)<0;i(i.P+i.F*(s||!n(20)(o)),"Array",{indexOf:function indexOf(t){return s?o.apply(this,arguments)||0:r(this,t,arguments[1]);}});},function(t,e,n){var i=n(5);i(i.S,"Array",{isArray:n(54)});},function(t,e,n){"use strict";var i=n(5),r=n(34)(1);i(i.P+i.F*!n(20)([].map,!0),"Array",{map:function map(t){return r(this,t,arguments[1]);}});},function(t,e,n){"use strict";var i=n(5),r=n(102);i(i.P+i.F*!n(20)([].reduce,!0),"Array",{reduce:function reduce(t){return r(this,t,arguments.length,arguments[1],!1);}});},function(t,e,n){n(106)("search",1,function(t,e,n){return[function(n){"use strict";var i=t(this),r=void 0==n?void 0:n[e];return void 0!==r?r.call(n,i):new RegExp(n)[e](String(i));},n];});},function(t,e,n){"use strict";n(112)("trim",function(t){return function(){return t(this,3);};});},function(t,e){},function(t,e){t.exports=function(t,e,n,i,r,o){var s,u=t=t||{},a=_typeof2(t.default);"object"!==a&&"function"!==a||(s=t,u=t.default);var l="function"==typeof u?u.options:u;e&&(l.render=e.render,l.staticRenderFns=e.staticRenderFns,l._compiled=!0),n&&(l.functional=!0),r&&(l._scopeId=r);var c;if(o?(c=function c(t){t=t||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext,t||"undefined"==typeof __VUE_SSR_CONTEXT__||(t=__VUE_SSR_CONTEXT__),i&&i.call(this,t),t&&t._registeredComponents&&t._registeredComponents.add(o);},l._ssrRegister=c):i&&(c=i),c){var f=l.functional,p=f?l.render:l.beforeCreate;f?(l._injectStyles=c,l.render=function(t,e){return c.call(e),p(t,e);}):l.beforeCreate=p?[].concat(p,c):[c];}return{esModule:s,exports:u,options:l};};},function(t,e,n){"use strict";var i=function i(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"multiselect",class:{"multiselect--active":t.isOpen,"multiselect--disabled":t.disabled,"multiselect--above":t.isAbove},attrs:{tabindex:t.searchable?-1:t.tabindex},on:{focus:function focus(e){t.activate();},blur:function blur(e){!t.searchable&&t.deactivate();},keydown:[function(e){return"button"in e||!t._k(e.keyCode,"down",40,e.key,"ArrowDown")?e.target!==e.currentTarget?null:(e.preventDefault(),void t.pointerForward()):null;},function(e){return"button"in e||!t._k(e.keyCode,"up",38,e.key,"ArrowUp")?e.target!==e.currentTarget?null:(e.preventDefault(),void t.pointerBackward()):null;},function(e){return"button"in e||!t._k(e.keyCode,"enter",13,e.key,"Enter")||!t._k(e.keyCode,"tab",9,e.key,"Tab")?(e.stopPropagation(),e.target!==e.currentTarget?null:void t.addPointerElement(e)):null;}],keyup:function keyup(e){if(!("button"in e)&&t._k(e.keyCode,"esc",27,e.key,"Escape"))return null;t.deactivate();}}},[t._t("caret",[n("div",{staticClass:"multiselect__select",on:{mousedown:function mousedown(e){e.preventDefault(),e.stopPropagation(),t.toggle();}}})],{toggle:t.toggle}),t._v(" "),t._t("clear",null,{search:t.search}),t._v(" "),n("div",{ref:"tags",staticClass:"multiselect__tags"},[n("div",{directives:[{name:"show",rawName:"v-show",value:t.visibleValues.length>0,expression:"visibleValues.length > 0"}],staticClass:"multiselect__tags-wrap"},[t._l(t.visibleValues,function(e){return[t._t("tag",[n("span",{staticClass:"multiselect__tag"},[n("span",{domProps:{textContent:t._s(t.getOptionLabel(e))}}),t._v(" "),n("i",{staticClass:"multiselect__tag-icon",attrs:{"aria-hidden":"true",tabindex:"1"},on:{keydown:function keydown(n){if(!("button"in n)&&t._k(n.keyCode,"enter",13,n.key,"Enter"))return null;n.preventDefault(),t.removeElement(e);},mousedown:function mousedown(n){n.preventDefault(),t.removeElement(e);}}})])],{option:e,search:t.search,remove:t.removeElement})];})],2),t._v(" "),t.internalValue&&t.internalValue.length>t.limit?[t._t("limit",[n("strong",{staticClass:"multiselect__strong",domProps:{textContent:t._s(t.limitText(t.internalValue.length-t.limit))}})])]:t._e(),t._v(" "),n("transition",{attrs:{name:"multiselect__loading"}},[t._t("loading",[n("div",{directives:[{name:"show",rawName:"v-show",value:t.loading,expression:"loading"}],staticClass:"multiselect__spinner"})])],2),t._v(" "),n("input",{directives:[{name:"show",rawName:"v-show",value:t.isOpen&&t.searchable,expression:"isOpen && searchable"}],ref:"search",staticClass:"multiselect__input",style:t.inputStyle,attrs:{name:t.name,id:t.id,type:"text",autocomplete:"off",placeholder:t.placeholder,disabled:t.disabled,tabindex:t.tabindex},domProps:{value:t.search},on:{input:function input(e){t.updateSearch(e.target.value);},focus:function focus(e){e.preventDefault(),t.activate();},blur:function blur(e){e.preventDefault(),t.deactivate();},keyup:function keyup(e){if(!("button"in e)&&t._k(e.keyCode,"esc",27,e.key,"Escape"))return null;t.deactivate();},keydown:[function(e){if(!("button"in e)&&t._k(e.keyCode,"down",40,e.key,"ArrowDown"))return null;e.preventDefault(),t.pointerForward();},function(e){if(!("button"in e)&&t._k(e.keyCode,"up",38,e.key,"ArrowUp"))return null;e.preventDefault(),t.pointerBackward();},function(e){return"button"in e||!t._k(e.keyCode,"enter",13,e.key,"Enter")?(e.preventDefault(),e.stopPropagation(),e.target!==e.currentTarget?null:void t.addPointerElement(e)):null;},function(e){if(!("button"in e)&&t._k(e.keyCode,"delete",[8,46],e.key,["Backspace","Delete"]))return null;e.stopPropagation(),t.removeLastElement();}]}}),t._v(" "),t.isSingleLabelVisible?n("span",{staticClass:"multiselect__single",on:{mousedown:function mousedown(e){return e.preventDefault(),t.toggle(e);}}},[t._t("singleLabel",[[t._v(t._s(t.currentOptionLabel))]],{option:t.singleValue})],2):t._e(),t._v(" "),t.isPlaceholderVisible?n("span",{on:{mousedown:function mousedown(e){return e.preventDefault(),t.toggle(e);}}},[t._t("placeholder",[n("span",{staticClass:"multiselect__single"},[t._v("\n            "+t._s(t.placeholder)+"\n          ")])])],2):t._e()],2),t._v(" "),n("transition",{attrs:{name:"multiselect"}},[n("div",{directives:[{name:"show",rawName:"v-show",value:t.isOpen,expression:"isOpen"}],ref:"list",staticClass:"multiselect__content-wrapper",style:{maxHeight:t.optimizedHeight+"px"},on:{focus:t.activate,mousedown:function mousedown(t){t.preventDefault();}}},[n("ul",{staticClass:"multiselect__content",style:t.contentStyle},[t._t("beforeList"),t._v(" "),t.multiple&&t.max===t.internalValue.length?n("li",[n("span",{staticClass:"multiselect__option"},[t._t("maxElements",[t._v("Maximum of "+t._s(t.max)+" options selected. First remove a selected option to select another.")])],2)]):t._e(),t._v(" "),!t.max||t.internalValue.length<t.max?t._l(t.filteredOptions,function(e,i){return n("li",{key:i,staticClass:"multiselect__element"},[e&&(e.$isLabel||e.$isDisabled)?t._e():n("span",{staticClass:"multiselect__option",class:t.optionHighlight(i,e),attrs:{"data-select":e&&e.isTag?t.tagPlaceholder:t.selectLabelText,"data-selected":t.selectedLabelText,"data-deselect":t.deselectLabelText},on:{click:function click(n){n.stopPropagation(),t.select(e);},mouseenter:function mouseenter(e){if(e.target!==e.currentTarget)return null;t.pointerSet(i);}}},[t._t("option",[n("span",[t._v(t._s(t.getOptionLabel(e)))])],{option:e,search:t.search})],2),t._v(" "),e&&(e.$isLabel||e.$isDisabled)?n("span",{staticClass:"multiselect__option",class:t.groupHighlight(i,e),attrs:{"data-select":t.groupSelect&&t.selectGroupLabelText,"data-deselect":t.groupSelect&&t.deselectGroupLabelText},on:{mouseenter:function mouseenter(e){if(e.target!==e.currentTarget)return null;t.groupSelect&&t.pointerSet(i);},mousedown:function mousedown(n){n.preventDefault(),t.selectGroup(e);}}},[t._t("option",[n("span",[t._v(t._s(t.getOptionLabel(e)))])],{option:e,search:t.search})],2):t._e()]);}):t._e(),t._v(" "),n("li",{directives:[{name:"show",rawName:"v-show",value:t.showNoResults&&0===t.filteredOptions.length&&t.search&&!t.loading,expression:"showNoResults && (filteredOptions.length === 0 && search && !loading)"}]},[n("span",{staticClass:"multiselect__option"},[t._t("noResult",[t._v("No elements found. Consider changing the search query.")])],2)]),t._v(" "),t._t("afterList")],2)])])],2);},r=[],o={render:i,staticRenderFns:r};e.a=o;}]);});/***/},/* 34 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var SearchwpAjax={};SearchwpAjax.install=function(Vue,options){/**
   * Save a setting to the database
   */Vue.SearchwpSetSetting=function(setting,value,group){var data={action:"searchwp_set_setting",setting:setting,value:value};if(group){data.group=group;}// Everything is nonce'd
if(!_SEARCHWP_VARS.nonces[setting]){alert(_SEARCHWP_VARS.i18n.errors.missing_nonce);return;}data._ajax_nonce=_SEARCHWP_VARS.nonces[setting];return new Promise(function(resolve,reject){jQuery.post(ajaxurl,data,function(response){if(response.success){resolve(response);}else{reject(response);}});});};/**
   * Retrieve an existing SearchWP setting from the database
   */Vue.SearchwpGetSetting=function(setting,group){var data={action:"searchwp_get_setting",setting:setting};if(group){data.group=group;}// Everything is nonce'd
if(!_SEARCHWP_VARS.nonces[setting]){alert(_SEARCHWP_VARS.i18n.errors.missing_nonce);return;}data._ajax_nonce=_SEARCHWP_VARS.nonces[setting];return new Promise(function(resolve,reject){jQuery.post(ajaxurl,data,function(response){if(response.success){resolve(response.data);}else{reject(response);}});});};/**
   * Retrieve the index stats
   */Vue.SearchwpGetIndexStats=function(jumpstart){return new Promise(function(resolve,reject){var data={action:"searchwp_get_index_stats"};if(jumpstart){jQuery.get("options-general.php?page=searchwp&swpjumpstart&"+new Date().getTime(),function(data){});}var nonce="get_index_stats";// Everything is nonce'd
if(!_SEARCHWP_VARS.nonces[nonce]){reject(_SEARCHWP_VARS.i18n.errors.missing_nonce);}data._ajax_nonce=_SEARCHWP_VARS.nonces[nonce];jQuery.post(ajaxurl+"?"+new Date().getTime(),data,function(response){if(response.success){resolve(response.data);}else{reject(response);}});});};/**
   * Reset the index
   */Vue.SearchwpResetIndex=function(){// Proceed with the reset
return new Promise(function(resolve,reject){var data={action:"searchwp_reset_index"};var nonce="reset_index";// Everything is nonce'd
if(!_SEARCHWP_VARS.nonces[nonce]){reject(_SEARCHWP_VARS.i18n.errors.missing_nonce);}data._ajax_nonce=_SEARCHWP_VARS.nonces[nonce];jQuery.post(ajaxurl,data,function(response){if(response.success){resolve(response.data);}else{reject(response);}});});};/**
   * Recreate database tables
   */Vue.SearchwpRecreateDatabaseTables=function(){// Proceed with the reset
return new Promise(function(resolve,reject){var data={action:"searchwp_recreate_tables"};var nonce="recreate_tables";// Everything is nonce'd
if(!_SEARCHWP_VARS.nonces[nonce]){reject(_SEARCHWP_VARS.i18n.errors.missing_nonce);}data._ajax_nonce=_SEARCHWP_VARS.nonces[nonce];jQuery.post(ajaxurl,data,function(response){if(response.success){resolve(response.data);}else{reject(response);}});});};/**
   * Perform taxonomy term search
   */Vue.SearchwpSearchTaxonomyTerms=function(query,taxonomy,post_type){return new Promise(function(resolve,reject){var data={action:"searchwp_get_tax_terms",tax:taxonomy,q:query,post_type:post_type};var nonce="tax_"+taxonomy+"_"+post_type;// Everything is nonce'd
if(!_SEARCHWP_VARS.nonces[nonce]){reject(_SEARCHWP_VARS.i18n.errors.missing_nonce);}data._swpvtax_nonce=_SEARCHWP_VARS.nonces[nonce];jQuery.post(ajaxurl,data,function(response){if(response.success){resolve(response.data);}else{reject(response);}});});};/**
   * Save a setting to the database
   */Vue.SearchwpSetSetting=function(setting,value,group){var data={action:"searchwp_set_setting",setting:setting,value:value};if(group){data.group=group;}// Everything is nonce'd
if(!_SEARCHWP_VARS.nonces[setting]){alert(_SEARCHWP_VARS.i18n.errors.missing_nonce);return;}data._ajax_nonce=_SEARCHWP_VARS.nonces[setting];return new Promise(function(resolve,reject){jQuery.post(ajaxurl,data,function(response){if(response.success){resolve(response);}else{reject(response);}});});};/**
   * Check status of HTTP Basic Authentication
   */Vue.SearchwpCheckBasicAuth=function(){return new Promise(function(resolve,reject){var data={action:"searchwp_basic_auth"};var nonce="basic_auth";// Everything is nonce'd
if(!_SEARCHWP_VARS.nonces[nonce]){reject(_SEARCHWP_VARS.i18n.errors.missing_nonce);}data._ajax_nonce=_SEARCHWP_VARS.nonces[nonce];jQuery.post(ajaxurl,data,function(response){if(response.success){resolve(response.data);}else{reject(response);}});});};};/* harmony default export */__webpack_exports__["a"]=SearchwpAjax;/***/},/* 35 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_SearchwpSettings_vue__=__webpack_require__(10);/* unused harmony namespace reexport *//* harmony import */var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_54517db7_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_SearchwpSettings_vue__=__webpack_require__(109);var disposed=false;function injectStyle(ssrContext){if(disposed)return;__webpack_require__(36);__webpack_require__(39);}var normalizeComponent=__webpack_require__(2);/* script *//* template *//* template functional */var __vue_template_functional__=false;/* styles */var __vue_styles__=injectStyle;/* scopeId */var __vue_scopeId__=null;/* moduleIdentifier (server only) */var __vue_module_identifier__=null;var Component=normalizeComponent(__WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_SearchwpSettings_vue__["a"/* default */],__WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_54517db7_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_SearchwpSettings_vue__["a"/* default */],__vue_template_functional__,__vue_styles__,__vue_scopeId__,__vue_module_identifier__);Component.options.__file="assets/js/src/SearchwpSettings.vue";/* hot reload */if(false){(function(){var hotAPI=require("vue-hot-reload-api");hotAPI.install(require("vue"),false);if(!hotAPI.compatible)return;module.hot.accept();if(!module.hot.data){hotAPI.createRecord("data-v-54517db7",Component.options);}else{hotAPI.reload("data-v-54517db7",Component.options);}module.hot.dispose(function(data){disposed=true;});})();}/* harmony default export */__webpack_exports__["a"]=Component.exports;/***/},/* 36 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(37);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("7b4cea44",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../../node_modules/css-loader/index.js!../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-54517db7\",\"scoped\":false,\"hasInlineConfig\":false}!../../../node_modules/sass-loader/lib/loader.js!../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./SearchwpSettings.vue",function(){var newContent=require("!!../../../node_modules/css-loader/index.js!../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-54517db7\",\"scoped\":false,\"hasInlineConfig\":false}!../../../node_modules/sass-loader/lib/loader.js!../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./SearchwpSettings.vue");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 37 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\n.tooltip {\n  display: block !important;\n  z-index: 10000;\n}\n.tooltip .tooltip-inner {\n    background: rgba(0, 0, 0, 0.8);\n    color: #fff;\n    border-radius: 2px;\n    padding: 5px 10px;\n    text-align: center;\n}\n.tooltip .tooltip-arrow {\n    width: 0;\n    height: 0;\n    border-style: solid;\n    position: absolute;\n    margin: 5px;\n    border-color: rgba(0, 0, 0, 0.8);\n    z-index: 1;\n}\n.tooltip[x-placement^=\"top\"] {\n    margin-bottom: 5px;\n}\n.tooltip[x-placement^=\"top\"] .tooltip-arrow {\n      border-width: 5px 5px 0 5px;\n      border-left-color: transparent !important;\n      border-right-color: transparent !important;\n      border-bottom-color: transparent !important;\n      bottom: -5px;\n      left: calc(50% - 5px);\n      margin-top: 0;\n      margin-bottom: 0;\n}\n.tooltip[x-placement^=\"bottom\"] {\n    margin-top: 5px;\n}\n.tooltip[x-placement^=\"bottom\"] .tooltip-arrow {\n      border-width: 0 5px 5px 5px;\n      border-left-color: transparent !important;\n      border-right-color: transparent !important;\n      border-top-color: transparent !important;\n      top: -5px;\n      left: calc(50% - 5px);\n      margin-top: 0;\n      margin-bottom: 0;\n}\n.tooltip[x-placement^=\"right\"] {\n    margin-left: 5px;\n}\n.tooltip[x-placement^=\"right\"] .tooltip-arrow {\n      border-width: 5px 5px 5px 0;\n      border-left-color: transparent !important;\n      border-top-color: transparent !important;\n      border-bottom-color: transparent !important;\n      left: -5px;\n      top: calc(50% - 5px);\n      margin-left: 0;\n      margin-right: 0;\n}\n.tooltip[x-placement^=\"left\"] {\n    margin-right: 5px;\n}\n.tooltip[x-placement^=\"left\"] .tooltip-arrow {\n      border-width: 5px 0 5px 5px;\n      border-top-color: transparent !important;\n      border-right-color: transparent !important;\n      border-bottom-color: transparent !important;\n      right: -5px;\n      top: calc(50% - 5px);\n      margin-left: 0;\n      margin-right: 0;\n}\n.tooltip.popover .popover-inner {\n    background: #414141;\n    color: #fff;\n    padding: 0.4em 0 0.8em;\n    border-radius: 3px;\n    min-width: 150px;\n}\n.tooltip.popover .popover-inner > div > *:first-child {\n      margin-top: 0;\n      padding-top: 0;\n}\n.tooltip.popover .popover-inner > div > *:last-child {\n      margin-bottom: 0;\n      padding-bottom: 0;\n}\n.tooltip.popover .popover-inner a {\n      color: #fff;\n}\n.tooltip.popover .popover-inner ul {\n      margin: 0;\n      padding: 0.6em 0;\n      list-style: none;\n}\n.tooltip.popover .popover-inner li {\n      padding: 0.3em 0.7em;\n      line-height: 1.5;\n      margin: 0;\n      cursor: pointer;\n}\n.tooltip.popover .popover-inner li:hover {\n        background: #159FD2;\n}\n.tooltip.popover .popover-arrow {\n    border-color: #414141;\n}\n.tooltip[aria-hidden='true'] {\n    visibility: hidden;\n    opacity: 0;\n    transition: opacity .15s, visibility .15s;\n}\n.tooltip[aria-hidden='false'] {\n    visibility: visible;\n    opacity: 1;\n    transition: opacity .15s;\n}\n.tooltip .searchwp-popover-actions button {\n    cursor: pointer;\n    display: block;\n    width: 100%;\n    margin: 0;\n    padding: 0;\n    background: transparent;\n    text-align: center;\n    color: #fff;\n    border: 0;\n}\n.searchwp-spinner-message {\n  display: flex;\n  align-items: center;\n}\n.searchwp-spinner-message .vue-simple-spinner-text {\n    flex: 1;\n    padding-left: 0.75em;\n    margin-top: 0 !important;\n    text-align: left !important;\n}\n",""]);// exports
/***/},/* 38 *//***/function(module,exports){/**
 * Translates the list format produced by css-loader into something
 * easier to manipulate.
 */module.exports=function listToStyles(parentId,list){var styles=[];var newStyles={};for(var i=0;i<list.length;i++){var item=list[i];var id=item[0];var css=item[1];var media=item[2];var sourceMap=item[3];var part={id:parentId+':'+i,css:css,media:media,sourceMap:sourceMap};if(!newStyles[id]){styles.push(newStyles[id]={id:id,parts:[part]});}else{newStyles[id].parts.push(part);}}return styles;};/***/},/* 39 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(40);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("6de73b76",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../../node_modules/css-loader/index.js!../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-54517db7\",\"scoped\":false,\"hasInlineConfig\":false}!../../../node_modules/sass-loader/lib/loader.js!../../../node_modules/vue-loader/lib/selector.js?type=styles&index=1!./SearchwpSettings.vue",function(){var newContent=require("!!../../../node_modules/css-loader/index.js!../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-54517db7\",\"scoped\":false,\"hasInlineConfig\":false}!../../../node_modules/sass-loader/lib/loader.js!../../../node_modules/vue-loader/lib/selector.js?type=styles&index=1!./SearchwpSettings.vue");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 40 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\n.searchwp-loading {\n  background: transparent url(\"data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjgiIGhlaWdodD0iMjgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIiBjbGFzcz0ibGRzLWRpc2siIHN0eWxlPSJiYWNrZ3JvdW5kOm5vbmUiPjxnIHRyYW5zZm9ybT0ibWF0cml4KC42IDAgMCAuNiA1MCA1MCkiPjxjaXJjbGUgcj0iNTAiIGZpbGw9ImdyYXkiLz48Y2lyY2xlIGN5PSItMjgiIHI9IjE1IiBmaWxsPSIjZmZmIiB0cmFuc2Zvcm09InJvdGF0ZSgzNDIpIj48YW5pbWF0ZVRyYW5zZm9ybSBhdHRyaWJ1dGVOYW1lPSJ0cmFuc2Zvcm0iIHR5cGU9InJvdGF0ZSIgY2FsY01vZGU9ImxpbmVhciIgdmFsdWVzPSIwIDAgMDszNjAgMCAwIiBrZXlUaW1lcz0iMDsxIiBkdXI9IjFzIiBiZWdpbj0iMHMiIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIi8+PC9jaXJjbGU+PC9nPjwvc3ZnPg==\") 50% 50% no-repeat;\n  background-size: 28px 28px;\n}\n.searchwp-loading * {\n    visibility: hidden;\n}\n.searchwp-engines-index-stats {\n  display: flex;\n  justify-content: space-between;\n}\n.searchwp-engines-index-stats .searchwp-engines {\n    flex: 1;\n}\n.searchwp-engines-index-stats .searchwp-index-stats {\n    width: 350px;\n    margin-left: 2.5%;\n}\n",""]);// exports
/***/},/* 41 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Engines_vue__=__webpack_require__(11);/* unused harmony namespace reexport *//* harmony import */var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_58c1192a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Engines_vue__=__webpack_require__(91);var disposed=false;function injectStyle(ssrContext){if(disposed)return;__webpack_require__(42);}var normalizeComponent=__webpack_require__(2);/* script *//* template *//* template functional */var __vue_template_functional__=false;/* styles */var __vue_styles__=injectStyle;/* scopeId */var __vue_scopeId__=null;/* moduleIdentifier (server only) */var __vue_module_identifier__=null;var Component=normalizeComponent(__WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Engines_vue__["a"/* default */],__WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_58c1192a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Engines_vue__["a"/* default */],__vue_template_functional__,__vue_styles__,__vue_scopeId__,__vue_module_identifier__);Component.options.__file="assets/js/src/components/Engines.vue";/* hot reload */if(false){(function(){var hotAPI=require("vue-hot-reload-api");hotAPI.install(require("vue"),false);if(!hotAPI.compatible)return;module.hot.accept();if(!module.hot.data){hotAPI.createRecord("data-v-58c1192a",Component.options);}else{hotAPI.reload("data-v-58c1192a",Component.options);}module.hot.dispose(function(data){disposed=true;});})();}/* harmony default export */__webpack_exports__["a"]=Component.exports;/***/},/* 42 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(43);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("dec60660",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-58c1192a\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Engines.vue",function(){var newContent=require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-58c1192a\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Engines.vue");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 43 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\n.searchwp-is-waiting {\n  opacity: 0.5;\n  pointer-events: none;\n  cursor: wait;\n}\n.searchwp-engines * {\n  box-sizing: border-box;\n}\n.wp-core-ui .searchwp-button-message {\n  background: transparent !important;\n  border-color: transparent !important;\n  box-shadow: none !important;\n  display: flex;\n}\n.wp-core-ui .searchwp-button-message .dashicons {\n    width: 26px;\n    height: 28px;\n    font-size: 28px;\n}\n.searchwp-engines-actions {\n  margin: 0;\n  padding: 0.5em 0 0;\n  list-style: none;\n  display: flex;\n  align-items: center;\n}\n.searchwp-engines-actions > * {\n    margin: 0 1em 0 0;\n}\n.wp-core-ui .searchwp-engines-actions .button.searchwp-button {\n  margin-top: 0;\n}\n.wp-core-ui .searchwp-success .button {\n  color: #43ad4c;\n}\n@media screen and (max-width: 1023px) {\n.searchwp-engines-index-stats {\n    flex-direction: column-reverse;\n    width: 100%;\n}\n.searchwp-engines-index-stats .searchwp-engines,\n    .searchwp-engines-index-stats .searchwp-index-stats {\n      width: 100%;\n      margin: 0;\n}\n.searchwp-engines-index-stats .searchwp-index-stats {\n      margin-bottom: 1em;\n}\n}\n",""]);// exports
/***/},/* 44 *//***/function(module,exports){(function(){var base64map='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/',crypt={// Bit-wise rotation left
rotl:function rotl(n,b){return n<<b|n>>>32-b;},// Bit-wise rotation right
rotr:function rotr(n,b){return n<<32-b|n>>>b;},// Swap big-endian to little-endian and vice versa
endian:function endian(n){// If number given, swap endian
if(n.constructor==Number){return crypt.rotl(n,8)&0x00FF00FF|crypt.rotl(n,24)&0xFF00FF00;}// Else, assume array and swap all items
for(var i=0;i<n.length;i++){n[i]=crypt.endian(n[i]);}return n;},// Generate an array of any length of random bytes
randomBytes:function randomBytes(n){for(var bytes=[];n>0;n--){bytes.push(Math.floor(Math.random()*256));}return bytes;},// Convert a byte array to big-endian 32-bit words
bytesToWords:function bytesToWords(bytes){for(var words=[],i=0,b=0;i<bytes.length;i++,b+=8){words[b>>>5]|=bytes[i]<<24-b%32;}return words;},// Convert big-endian 32-bit words to a byte array
wordsToBytes:function wordsToBytes(words){for(var bytes=[],b=0;b<words.length*32;b+=8){bytes.push(words[b>>>5]>>>24-b%32&0xFF);}return bytes;},// Convert a byte array to a hex string
bytesToHex:function bytesToHex(bytes){for(var hex=[],i=0;i<bytes.length;i++){hex.push((bytes[i]>>>4).toString(16));hex.push((bytes[i]&0xF).toString(16));}return hex.join('');},// Convert a hex string to a byte array
hexToBytes:function hexToBytes(hex){for(var bytes=[],c=0;c<hex.length;c+=2){bytes.push(parseInt(hex.substr(c,2),16));}return bytes;},// Convert a byte array to a base-64 string
bytesToBase64:function bytesToBase64(bytes){for(var base64=[],i=0;i<bytes.length;i+=3){var triplet=bytes[i]<<16|bytes[i+1]<<8|bytes[i+2];for(var j=0;j<4;j++){if(i*8+j*6<=bytes.length*8)base64.push(base64map.charAt(triplet>>>6*(3-j)&0x3F));else base64.push('=');}}return base64.join('');},// Convert a base-64 string to a byte array
base64ToBytes:function base64ToBytes(base64){// Remove non-base-64 characters
base64=base64.replace(/[^A-Z0-9+\/]/ig,'');for(var bytes=[],i=0,imod4=0;i<base64.length;imod4=++i%4){if(imod4==0)continue;bytes.push((base64map.indexOf(base64.charAt(i-1))&Math.pow(2,-2*imod4+8)-1)<<imod4*2|base64map.indexOf(base64.charAt(i))>>>6-imod4*2);}return bytes;}};module.exports=crypt;})();/***/},/* 45 *//***/function(module,exports){/*!
 * Determine if an object is a Buffer
 *
 * @author   Feross Aboukhadijeh <https://feross.org>
 * @license  MIT
 */// The _isBuffer check is for Safari 5-7 support, because it's missing
// Object.prototype.constructor. Remove this eventually
module.exports=function(obj){return obj!=null&&(isBuffer(obj)||isSlowBuffer(obj)||!!obj._isBuffer);};function isBuffer(obj){return!!obj.constructor&&typeof obj.constructor.isBuffer==='function'&&obj.constructor.isBuffer(obj);}// For Node v0.10 support. Remove this eventually.
function isSlowBuffer(obj){return typeof obj.readFloatLE==='function'&&typeof obj.slice==='function'&&isBuffer(obj.slice(0,0));}/***/},/* 46 *//***/function(module,exports,__webpack_require__){"use strict";module.exports=function isObject(x){return(typeof x==='undefined'?'undefined':_typeof2(x))==="object"&&x!==null;};/***/},/* 47 *//***/function(module,exports){/**
 * isArray
 */var isArray=Array.isArray;/**
 * toString
 */var str=Object.prototype.toString;/**
 * Whether or not the given `val`
 * is an array.
 *
 * example:
 *
 *        isArray([]);
 *        // > true
 *        isArray(arguments);
 *        // > false
 *        isArray('');
 *        // > false
 *
 * @param {mixed} val
 * @return {bool}
 */module.exports=isArray||function(val){return!!val&&'[object Array]'==str.call(val);};/***/},/* 48 *//***/function(module,exports){module.exports=isFunction;var toString=Object.prototype.toString;function isFunction(fn){var string=toString.call(fn);return string==='[object Function]'||typeof fn==='function'&&string!=='[object RegExp]'||typeof window!=='undefined'&&(// IE8 and below
fn===window.setTimeout||fn===window.alert||fn===window.confirm||fn===window.prompt);};/***/},/* 49 *//***/function(module,exports,__webpack_require__){"use strict";/*!
 * array-unique <https://github.com/jonschlinkert/array-unique>
 *
 * Copyright (c) 2014-2015, Jon Schlinkert.
 * Licensed under the MIT License.
 */module.exports=function unique(arr){if(!Array.isArray(arr)){throw new TypeError('array-unique expects an array.');}var len=arr.length;var i=-1;while(i++<len){var j=i+1;for(;j<arr.length;++j){if(arr[i]===arr[j]){arr.splice(j--,1);}}}return arr;};module.exports.immutable=function uniqueImmutable(arr){if(!Array.isArray(arr)){throw new TypeError('array-unique expects an array.');}var arrLen=arr.length;var newArr=new Array(arrLen);for(var i=0;i<arrLen;i++){newArr[i]=arr[i];}return module.exports(newArr);};/***/},/* 50 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_EnvironmentCheck_vue__=__webpack_require__(14);/* unused harmony namespace reexport *//* harmony import */var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_0877722b_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_EnvironmentCheck_vue__=__webpack_require__(56);var disposed=false;function injectStyle(ssrContext){if(disposed)return;__webpack_require__(51);}var normalizeComponent=__webpack_require__(2);/* script *//* template *//* template functional */var __vue_template_functional__=false;/* styles */var __vue_styles__=injectStyle;/* scopeId */var __vue_scopeId__=null;/* moduleIdentifier (server only) */var __vue_module_identifier__=null;var Component=normalizeComponent(__WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_EnvironmentCheck_vue__["a"/* default */],__WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_0877722b_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_EnvironmentCheck_vue__["a"/* default */],__vue_template_functional__,__vue_styles__,__vue_scopeId__,__vue_module_identifier__);Component.options.__file="assets/js/src/components/EnvironmentCheck.vue";/* hot reload */if(false){(function(){var hotAPI=require("vue-hot-reload-api");hotAPI.install(require("vue"),false);if(!hotAPI.compatible)return;module.hot.accept();if(!module.hot.data){hotAPI.createRecord("data-v-0877722b",Component.options);}else{hotAPI.reload("data-v-0877722b",Component.options);}module.hot.dispose(function(data){disposed=true;});})();}/* harmony default export */__webpack_exports__["a"]=Component.exports;/***/},/* 51 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(52);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("13a79f8e",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-0877722b\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./EnvironmentCheck.vue",function(){var newContent=require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-0877722b\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./EnvironmentCheck.vue");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 52 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\n.swp-notices + #error {\n  display: none;\n}\n",""]);// exports
/***/},/* 53 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(54);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("177fc588",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-39246a01\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Message.vue",function(){var newContent=require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-39246a01\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Message.vue");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 54 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\n.searchwp-message {\n  display: flex;\n  align-items: center;\n  margin: 5px 0 15px;\n  border-left: 4px solid #00a0d2;\n  background: #fff;\n  box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.1);\n  padding: 1px 12px;\n}\n.searchwp-message p {\n    margin: 0.5em 0;\n    padding: 2px;\n}\n.searchwp-message .button {\n    display: inline-block;\n    margin: 0.5em 0 0.5em 2em;\n}\n.searchwp-message.searchwp-message__warning {\n    border-left-color: #ffb900;\n}\n.searchwp-message.searchwp-message__success {\n    border-left-color: #46b450;\n}\n.searchwp-message.searchwp-message__error {\n    border-left-color: #dc3232;\n}\n",""]);// exports
/***/},/* 55 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var render=function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("div",{class:["searchwp-message","searchwp-message__"+_vm.type]},[_vm._t("default"),_vm._v(" "),_vm.moreInfo?_c("div",[_c("a",{staticClass:"button",attrs:{href:_vm.moreInfo,target:"_blank"}},[_vm._v(_vm._s(_vm.i18n.moreInfo))])]):_vm._e(),_vm._v(" "),typeof _vm.action.target==="string"&&_vm.action.text.length?_c("div",[_c("a",{staticClass:"button",attrs:{href:_vm.action.target}},[_vm._v(_vm._s(_vm.action.text))])]):typeof _vm.action.target==="function"&&_vm.action.text.length?_c("div",[_c("button",{staticClass:"button",on:{click:function click($event){$event.preventDefault();return _vm.action.target($event);}}},[_vm._v(_vm._s(_vm.action.text))])]):_vm._e()],2);};var staticRenderFns=[];render._withStripped=true;var esExports={render:render,staticRenderFns:staticRenderFns/* harmony default export */};__webpack_exports__["a"]=esExports;if(false){module.hot.accept();if(module.hot.data){require("vue-hot-reload-api").rerender("data-v-39246a01",esExports);}}/***/},/* 56 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var render=function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("div",[_vm.basicAuth?_c("searchwp-message",{attrs:{type:"error",moreInfo:"https://searchwp.com/docs/hooks/searchwp_basic_auth_creds/"}},[_c("p",[_vm._v(_vm._s(_vm.i18n.basicAuth))])]):_vm._e(),_vm._v(" "),!_vm.validDatabase?_c("searchwp-message",{attrs:{type:"error",action:{target:_vm.recreateDatabaseTables,text:_vm.i18n.recreateTables}}},[_c("p",[_vm._v(_vm._s(_vm.i18n.databaseTablesMissing))])]):_vm._e()],1);};var staticRenderFns=[];render._withStripped=true;var esExports={render:render,staticRenderFns:staticRenderFns/* harmony default export */};__webpack_exports__["a"]=esExports;if(false){module.hot.accept();if(module.hot.data){require("vue-hot-reload-api").rerender("data-v-0877722b",esExports);}}/***/},/* 57 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Engine_vue__=__webpack_require__(16);/* unused harmony namespace reexport *//* harmony import */var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_76b67690_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Engine_vue__=__webpack_require__(90);var disposed=false;function injectStyle(ssrContext){if(disposed)return;__webpack_require__(58);}var normalizeComponent=__webpack_require__(2);/* script *//* template *//* template functional */var __vue_template_functional__=false;/* styles */var __vue_styles__=injectStyle;/* scopeId */var __vue_scopeId__=null;/* moduleIdentifier (server only) */var __vue_module_identifier__=null;var Component=normalizeComponent(__WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Engine_vue__["a"/* default */],__WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_76b67690_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Engine_vue__["a"/* default */],__vue_template_functional__,__vue_styles__,__vue_scopeId__,__vue_module_identifier__);Component.options.__file="assets/js/src/components/Engine.vue";/* hot reload */if(false){(function(){var hotAPI=require("vue-hot-reload-api");hotAPI.install(require("vue"),false);if(!hotAPI.compatible)return;module.hot.accept();if(!module.hot.data){hotAPI.createRecord("data-v-76b67690",Component.options);}else{hotAPI.reload("data-v-76b67690",Component.options);}module.hot.dispose(function(data){disposed=true;});})();}/* harmony default export */__webpack_exports__["a"]=Component.exports;/***/},/* 58 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(59);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("5a7eb3c9",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-76b67690\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Engine.vue",function(){var newContent=require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-76b67690\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Engine.vue");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 59 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\n.js .postbox.searchwp-engine .hndle {\n  cursor: default;\n}\n.searchwp-engine .hndle {\n  display: flex;\n  align-items: center;\n}\n.searchwp-engine .hndle a {\n    display: block;\n    margin-left: auto;\n    font-size: 12px;\n}\n.searchwp-engine .hndle span {\n    cursor: pointer;\n}\n.searchwp-engine .hndle span.searchwp-hndle-default {\n      cursor: default;\n}\n.searchwp-engine .hndle span:hover span {\n      opacity: 0.8;\n}\n.searchwp-engine .hndle span,\n  .searchwp-engine .hndle input,\n  .searchwp-engine .hndle code {\n    display: inline-block;\n    margin-right: 1em;\n}\n.searchwp-engine .hndle span span {\n    margin-right: 0;\n    opacity: 0.35;\n}\n.searchwp-engine .hndle input,\n  .searchwp-engine .hndle code {\n    font-weight: normal;\n}\n.searchwp-engine .hndle input {\n    border: 1px solid #e5e5e5;\n}\n.searchwp-engine p.description {\n  margin: 1.15em 0 0;\n}\n.searchwp-engine-post-type {\n  background-color: #fafafa;\n  border: 1px solid #ddd;\n  border-radius: 2px;\n  margin-top: 1em;\n}\n.searchwp-engine-post-type__heading {\n  margin: 0;\n  padding: 8px 1em 8px 4px;\n  display: flex;\n  justify-content: space-between;\n  align-items: center;\n  font-size: 14px;\n  line-height: 1.4;\n}\n.searchwp-engine-post-type__heading .searchwp-engine-post-type__label {\n    display: block;\n    flex: 1;\n    cursor: pointer;\n}\n.searchwp-engine-post-type__heading a {\n    display: block;\n    margin-left: 2em;\n}\n.searchwp-engine-post-type__heading .dashicons:before {\n    color: #BCBCBC;\n}\n.searchwp-engine-post-type__details {\n  padding: 12px 0.5em;\n  display: flex;\n  position: relative;\n  border-top: 1px solid #ddd;\n}\n.searchwp-engine-post-type__details > * {\n    width: 50%;\n}\n.searchwp-engine-post-type__details:before {\n    display: block;\n    position: absolute;\n    content: '';\n    width: 1px;\n    top: 1.5em;\n    bottom: 1.5em;\n    left: 50%;\n    background: #ddd;\n    z-index: 0;\n}\n.searchwp-engine-post-type__weights {\n  padding-right: 1em;\n  padding-bottom: 0;\n}\n.searchwp-engine-post-type__weights .searchwp-remove {\n    text-decoration: none;\n}\n.searchwp-engine-post-type__weights .searchwp-message {\n    margin-right: 0.5em;\n    margin-left: 0.5em;\n}\n.searchwp-engine-post-type__weights .dashicons {\n    margin-top: -2px;\n    transform: scale(0.8);\n    opacity: 0.5;\n}\n.searchwp-engine-post-type__weights table {\n    width: 100%;\n    border-collapse: collapse;\n    position: relative;\n    z-index: 0;\n}\n.searchwp-engine-post-type__weights .searchwp-engine-post-type-attribute {\n    width: 60%;\n}\n.searchwp-engine-post-type__weights .searchwp-engine-post-type-attribute-weight {\n    width: 40%;\n}\n.searchwp-engine-post-type__weights th {\n    text-align: left;\n    padding: 0.5em;\n}\n.searchwp-engine-post-type__weights td {\n    padding: 0.5em;\n}\n.searchwp-engine-post-type__weights td .searchwp-remove {\n      display: inline-block;\n      visibility: hidden;\n}\n.searchwp-engine-post-type__weights tr:hover td {\n    background-color: #f0f0f0;\n}\n.searchwp-engine-post-type__weights tr:hover td .searchwp-remove {\n      visibility: visible;\n}\n.searchwp-engine-post-type__weights .searchwp-dropdown {\n    margin-right: 0.5em;\n    margin-left: 0.5em;\n}\n.searchwp-engine-post-type__weights .vue-slider-component {\n    margin-top: 2px;\n}\n.searchwp-engine-post-type__options {\n  padding: 0.5em 0.5em 0 1.5em;\n}\n.searchwp-engine-post-type__options > div {\n    margin-bottom: 2em;\n}\n.searchwp-engine-post-type__options > div:last-of-type {\n      margin-bottom: 0.5em;\n}\n.searchwp-engine-post-type__options h5 {\n    font-size: 13px;\n    line-height: 1.4em;\n    margin: 0 0 0.25em;\n}\n.searchwp-engine-actions {\n  margin: 0;\n  padding: 1em 0 0;\n  list-style: none;\n  display: flex;\n  align-items: center;\n  justify-content: space-between;\n}\n.searchwp-engine-actions > * {\n    margin: 0 1em 0 0;\n}\n.searchwp-engine-actions > *:last-of-type {\n      margin-right: 0;\n}\n.searchwp-engine-actions .searchwp-remove-engine {\n    visibility: hidden;\n    margin: 0.9em 2px 0.1em auto;\n    line-height: 26px;\n    height: 28px;\n}\n.searchwp-engine-actions .searchwp-dropdown {\n    margin-top: 0;\n    border-top: 0;\n    padding-top: 0;\n}\n.searchwp-engine .inside {\n  padding-bottom: 2px;\n}\n.searchwp-engine .inside:hover .searchwp-remove-engine {\n    visibility: visible;\n}\n.searchwp-document-notes {\n  padding: 10px 0.5em 0;\n}\n.searchwp-engine-note span {\n  display: inline-block;\n  margin-right: 4px;\n  vertical-align: bottom;\n}\n.searchwp-input-taxonomy-terms {\n  display: flex;\n  justify-content: space-between;\n  margin-bottom: 1em;\n}\n.searchwp-input-taxonomy-terms label {\n    display: block;\n    width: 45%;\n    padding-top: 0.45em;\n}\n.searchwp-input-taxonomy-terms > div {\n    display: block;\n    width: 55%;\n}\n@media screen and (max-width: 1279px) {\n.searchwp-engine-post-type__details {\n    flex-direction: column;\n}\n.searchwp-engine-post-type__details > * {\n      width: 100%;\n}\n.searchwp-engine-post-type__details .searchwp-engine-post-type__weights {\n      padding-right: 0;\n      margin-bottom: 2.5em;\n}\n.searchwp-engine-post-type__details .searchwp-engine-post-type__options {\n      padding: 0.5em;\n}\n.searchwp-engine-post-type__details:before {\n      display: none;\n}\n}\n",""]);// exports
/***/},/* 60 *//***/function(module,exports,__webpack_require__){;(function(name,root,factory){if(true){module.exports=factory();module.exports['default']=factory();}/* istanbul ignore next */else if(typeof define==='function'&&define.amd){define(factory);}else{root[name]=factory();}})('slugify',this,function(){/* eslint-disable */var charMap=JSON.parse('{"$":"dollar","&":"and","<":"less",">":"greater","|":"or","¢":"cent","£":"pound","¤":"currency","¥":"yen","©":"(c)","ª":"a","®":"(r)","º":"o","À":"A","Á":"A","Â":"A","Ã":"A","Ä":"A","Å":"A","Æ":"AE","Ç":"C","È":"E","É":"E","Ê":"E","Ë":"E","Ì":"I","Í":"I","Î":"I","Ï":"I","Ð":"D","Ñ":"N","Ò":"O","Ó":"O","Ô":"O","Õ":"O","Ö":"O","Ø":"O","Ù":"U","Ú":"U","Û":"U","Ü":"U","Ý":"Y","Þ":"TH","ß":"ss","à":"a","á":"a","â":"a","ã":"a","ä":"a","å":"a","æ":"ae","ç":"c","è":"e","é":"e","ê":"e","ë":"e","ì":"i","í":"i","î":"i","ï":"i","ð":"d","ñ":"n","ò":"o","ó":"o","ô":"o","õ":"o","ö":"o","ø":"o","ù":"u","ú":"u","û":"u","ü":"u","ý":"y","þ":"th","ÿ":"y","Ā":"A","ā":"a","Ă":"A","ă":"a","Ą":"A","ą":"a","Ć":"C","ć":"c","Č":"C","č":"c","Ď":"D","ď":"d","Đ":"DJ","đ":"dj","Ē":"E","ē":"e","Ė":"E","ė":"e","Ę":"e","ę":"e","Ě":"E","ě":"e","Ğ":"G","ğ":"g","Ģ":"G","ģ":"g","Ĩ":"I","ĩ":"i","Ī":"i","ī":"i","Į":"I","į":"i","İ":"I","ı":"i","Ķ":"k","ķ":"k","Ļ":"L","ļ":"l","Ł":"L","ł":"l","Ń":"N","ń":"n","Ņ":"N","ņ":"n","Ň":"N","ň":"n","Ő":"O","ő":"o","Œ":"OE","œ":"oe","Ř":"R","ř":"r","Ś":"S","ś":"s","Ş":"S","ş":"s","Š":"S","š":"s","Ţ":"T","ţ":"t","Ť":"T","ť":"t","Ũ":"U","ũ":"u","Ū":"u","ū":"u","Ů":"U","ů":"u","Ű":"U","ű":"u","Ų":"U","ų":"u","Ź":"Z","ź":"z","Ż":"Z","ż":"z","Ž":"Z","ž":"z","ƒ":"f","Ơ":"O","ơ":"o","Ư":"U","ư":"u","ǈ":"LJ","ǉ":"lj","ǋ":"NJ","ǌ":"nj","Ș":"S","ș":"s","Ț":"T","ț":"t","˚":"o","Ά":"A","Έ":"E","Ή":"H","Ί":"I","Ό":"O","Ύ":"Y","Ώ":"W","ΐ":"i","Α":"A","Β":"B","Γ":"G","Δ":"D","Ε":"E","Ζ":"Z","Η":"H","Θ":"8","Ι":"I","Κ":"K","Λ":"L","Μ":"M","Ν":"N","Ξ":"3","Ο":"O","Π":"P","Ρ":"R","Σ":"S","Τ":"T","Υ":"Y","Φ":"F","Χ":"X","Ψ":"PS","Ω":"W","Ϊ":"I","Ϋ":"Y","ά":"a","έ":"e","ή":"h","ί":"i","ΰ":"y","α":"a","β":"b","γ":"g","δ":"d","ε":"e","ζ":"z","η":"h","θ":"8","ι":"i","κ":"k","λ":"l","μ":"m","ν":"n","ξ":"3","ο":"o","π":"p","ρ":"r","ς":"s","σ":"s","τ":"t","υ":"y","φ":"f","χ":"x","ψ":"ps","ω":"w","ϊ":"i","ϋ":"y","ό":"o","ύ":"y","ώ":"w","Ё":"Yo","Ђ":"DJ","Є":"Ye","І":"I","Ї":"Yi","Ј":"J","Љ":"LJ","Њ":"NJ","Ћ":"C","Џ":"DZ","А":"A","Б":"B","В":"V","Г":"G","Д":"D","Е":"E","Ж":"Zh","З":"Z","И":"I","Й":"J","К":"K","Л":"L","М":"M","Н":"N","О":"O","П":"P","Р":"R","С":"S","Т":"T","У":"U","Ф":"F","Х":"H","Ц":"C","Ч":"Ch","Ш":"Sh","Щ":"Sh","Ъ":"U","Ы":"Y","Ь":"","Э":"E","Ю":"Yu","Я":"Ya","а":"a","б":"b","в":"v","г":"g","д":"d","е":"e","ж":"zh","з":"z","и":"i","й":"j","к":"k","л":"l","м":"m","н":"n","о":"o","п":"p","р":"r","с":"s","т":"t","у":"u","ф":"f","х":"h","ц":"c","ч":"ch","ш":"sh","щ":"sh","ъ":"u","ы":"y","ь":"","э":"e","ю":"yu","я":"ya","ё":"yo","ђ":"dj","є":"ye","і":"i","ї":"yi","ј":"j","љ":"lj","њ":"nj","ћ":"c","џ":"dz","Ґ":"G","ґ":"g","฿":"baht","ა":"a","ბ":"b","გ":"g","დ":"d","ე":"e","ვ":"v","ზ":"z","თ":"t","ი":"i","კ":"k","ლ":"l","მ":"m","ნ":"n","ო":"o","პ":"p","ჟ":"zh","რ":"r","ს":"s","ტ":"t","უ":"u","ფ":"f","ქ":"k","ღ":"gh","ყ":"q","შ":"sh","ჩ":"ch","ც":"ts","ძ":"dz","წ":"ts","ჭ":"ch","ხ":"kh","ჯ":"j","ჰ":"h","ẞ":"SS","Ạ":"A","ạ":"a","Ả":"A","ả":"a","Ấ":"A","ấ":"a","Ầ":"A","ầ":"a","Ẩ":"A","ẩ":"a","Ẫ":"A","ẫ":"a","Ậ":"A","ậ":"a","Ắ":"A","ắ":"a","Ằ":"A","ằ":"a","Ẳ":"A","ẳ":"a","Ẵ":"A","ẵ":"a","Ặ":"A","ặ":"a","Ẹ":"E","ẹ":"e","Ẻ":"E","ẻ":"e","Ẽ":"E","ẽ":"e","Ế":"E","ế":"e","Ề":"E","ề":"e","Ể":"E","ể":"e","Ễ":"E","ễ":"e","Ệ":"E","ệ":"e","Ỉ":"I","ỉ":"i","Ị":"I","ị":"i","Ọ":"O","ọ":"o","Ỏ":"O","ỏ":"o","Ố":"O","ố":"o","Ồ":"O","ồ":"o","Ổ":"O","ổ":"o","Ỗ":"O","ỗ":"o","Ộ":"O","ộ":"o","Ớ":"O","ớ":"o","Ờ":"O","ờ":"o","Ở":"O","ở":"o","Ỡ":"O","ỡ":"o","Ợ":"O","ợ":"o","Ụ":"U","ụ":"u","Ủ":"U","ủ":"u","Ứ":"U","ứ":"u","Ừ":"U","ừ":"u","Ử":"U","ử":"u","Ữ":"U","ữ":"u","Ự":"U","ự":"u","Ỳ":"Y","ỳ":"y","Ỵ":"Y","ỵ":"y","Ỷ":"Y","ỷ":"y","Ỹ":"Y","ỹ":"y","‘":"\'","’":"\'","“":"\\\"","”":"\\\"","†":"+","•":"*","…":"...","₠":"ecu","₢":"cruzeiro","₣":"french franc","₤":"lira","₥":"mill","₦":"naira","₧":"peseta","₨":"rupee","₩":"won","₪":"new shequel","₫":"dong","€":"euro","₭":"kip","₮":"tugrik","₯":"drachma","₰":"penny","₱":"peso","₲":"guarani","₳":"austral","₴":"hryvnia","₵":"cedi","₹":"indian rupee","₽":"russian ruble","℠":"sm","™":"tm","∂":"d","∆":"delta","∑":"sum","∞":"infinity","♥":"love","元":"yuan","円":"yen","﷼":"rial"}');/* eslint-enable */function replace(string,options){if(typeof string!=='string'){throw new Error('slugify: string argument expected');}options=typeof options==='string'?{replacement:options}:options||{};var slug=string.split('').reduce(function(result,ch){return result+(charMap[ch]||ch).// allowed
replace(options.remove||/[^\w\s$*_+~.()'"!\-:@]/g,'');},'')// trim leading/trailing spaces
.trim()// convert spaces
.replace(/[-\s]+/g,options.replacement||'-')// remove trailing separator
.replace('#{replacement}$','');return options.lower?slug.toLowerCase():slug;}replace.extend=function(customMap){for(var key in customMap){charMap[key]=customMap[key];}};return replace;});/***/},/* 61 *//***/function(module,exports,__webpack_require__){!function(t,e){true?module.exports=e():"function"==typeof define&&define.amd?define("vue-slider-component",[],e):"object"==(typeof exports==='undefined'?'undefined':_typeof2(exports))?exports["vue-slider-component"]=e():t["vue-slider-component"]=e();}(this,function(){return function(t){function e(s){if(i[s])return i[s].exports;var r=i[s]={i:s,l:!1,exports:{}};return t[s].call(r.exports,r,r.exports,e),r.l=!0,r.exports;}var i={};return e.m=t,e.c=i,e.i=function(t){return t;},e.d=function(t,i,s){e.o(t,i)||Object.defineProperty(t,i,{configurable:!1,enumerable:!0,get:s});},e.n=function(t){var i=t&&t.__esModule?function(){return t.default;}:function(){return t;};return e.d(i,"a",i),i;},e.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e);},e.p="",e(e.s=2);}([function(t,e,i){i(7);var s=i(5)(i(1),i(6),null,null);t.exports=s.exports;},function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var s=function(){var t="undefined"!=typeof window?window.devicePixelRatio||1:1;return function(e){return Math.round(e*t)/t;};}();e.default={name:"VueSliderComponent",props:{width:{type:[Number,String],default:"auto"},height:{type:[Number,String],default:6},data:{type:Array,default:null},dotSize:{type:Number,default:16},dotWidth:{type:Number,required:!1},dotHeight:{type:Number,required:!1},min:{type:Number,default:0},max:{type:Number,default:100},interval:{type:Number,default:1},show:{type:Boolean,default:!0},disabled:{type:[Boolean,Array],default:!1},piecewise:{type:Boolean,default:!1},tooltip:{type:[String,Boolean],default:"always"},eventType:{type:String,default:"auto"},direction:{type:String,default:"horizontal"},reverse:{type:Boolean,default:!1},lazy:{type:Boolean,default:!1},clickable:{type:Boolean,default:!0},speed:{type:Number,default:.5},realTime:{type:Boolean,default:!1},stopPropagation:{type:Boolean,default:!1},value:{type:[String,Number,Array,Object],default:0},piecewiseLabel:{type:Boolean,default:!1},debug:{type:Boolean,default:!0},fixed:{type:Boolean,default:!1},processDragable:{type:Boolean,default:!1},useKeyboard:{type:Boolean,default:!1},actionsKeyboard:{type:Array,default:function _default(){return[function(t){return t-1;},function(t){return t+1;}];}},tooltipMerge:{type:Boolean,default:!0},startAnimation:{type:Boolean,default:!1},sliderStyle:[Array,Object,Function],focusStyle:[Array,Object,Function],tooltipDir:[Array,String],formatter:[String,Function],mergeFormatter:[String,Function],piecewiseStyle:Object,disabledStyle:Object,piecewiseActiveStyle:Object,processStyle:Object,bgStyle:Object,tooltipStyle:[Array,Object,Function],disabledDotStyle:[Array,Object,Function],labelStyle:Object,labelActiveStyle:Object},data:function data(){return{flag:!1,keydownFlag:null,focusFlag:!1,processFlag:!1,processSign:null,size:0,fixedValue:0,focusSlider:0,currentValue:0,currentSlider:0,isComponentExists:!0,isMounted:!1};},computed:{dotWidthVal:function dotWidthVal(){return"number"==typeof this.dotWidth?this.dotWidth:this.dotSize;},dotHeightVal:function dotHeightVal(){return"number"==typeof this.dotHeight?this.dotHeight:this.dotSize;},flowDirection:function flowDirection(){return"vue-slider-"+this.direction+(this.reverse?"-reverse":"");},tooltipMergedPosition:function tooltipMergedPosition(){if(!this.isMounted)return{};var t=this.tooltipDirection[0];if(this.$refs.dot0){if("vertical"===this.direction){var e={};return e[t]="-"+(this.dotHeightVal/2-this.width/2+9)+"px",e;}var i={};return i[t]="-"+(this.dotWidthVal/2-this.height/2+9)+"px",i.left="50%",i;}},tooltipDirection:function tooltipDirection(){var t=this.tooltipDir||("vertical"===this.direction?"left":"top");return Array.isArray(t)?this.isRange?t:t[1]:this.isRange?[t,t]:t;},tooltipStatus:function tooltipStatus(){return"hover"===this.tooltip&&this.flag?"vue-slider-always":this.tooltip?"vue-slider-"+this.tooltip:"";},tooltipClass:function tooltipClass(){return["vue-slider-tooltip-"+this.tooltipDirection,"vue-slider-tooltip"];},disabledArray:function disabledArray(){return Array.isArray(this.disabled)?this.disabled:[this.disabled,this.disabled];},boolDisabled:function boolDisabled(){return this.disabledArray.every(function(t){return!0===t;});},isDisabled:function isDisabled(){return"none"===this.eventType||this.boolDisabled;},disabledClass:function disabledClass(){return this.boolDisabled?"vue-slider-disabled":"";},stateClass:function stateClass(){return{"vue-slider-state-process-drag":this.processFlag,"vue-slider-state-drag":this.flag&&!this.processFlag&&!this.keydownFlag,"vue-slider-state-focus":this.focusFlag};},isRange:function isRange(){return Array.isArray(this.value);},slider:function slider(){return this.isRange?[this.$refs.dot0,this.$refs.dot1]:this.$refs.dot;},minimum:function minimum(){return this.data?0:this.min;},val:{get:function get(){return this.data?this.isRange?[this.data[this.currentValue[0]],this.data[this.currentValue[1]]]:this.data[this.currentValue]:this.currentValue;},set:function set(t){if(this.data){if(this.isRange){var e=this.data.indexOf(t[0]),i=this.data.indexOf(t[1]);e>-1&&i>-1&&(this.currentValue=[e,i]);}else{var s=this.data.indexOf(t);s>-1&&(this.currentValue=s);}}else this.currentValue=t;}},currentIndex:function currentIndex(){return this.isRange?this.data?this.currentValue:[this.getIndexByValue(this.currentValue[0]),this.getIndexByValue(this.currentValue[1])]:this.getIndexByValue(this.currentValue);},indexRange:function indexRange(){return this.isRange?this.currentIndex:[0,this.currentIndex];},maximum:function maximum(){return this.data?this.data.length-1:this.max;},multiple:function multiple(){var t=(""+this.interval).split(".")[1];return t?Math.pow(10,t.length):1;},spacing:function spacing(){return this.data?1:this.interval;},total:function total(){return this.data?this.data.length-1:(Math.floor((this.maximum-this.minimum)*this.multiple)%(this.interval*this.multiple)!=0&&this.printError("Prop[interval] is illegal, Please make sure that the interval can be divisible"),(this.maximum-this.minimum)/this.interval);},gap:function gap(){return this.size/this.total;},position:function position(){return this.isRange?[(this.currentValue[0]-this.minimum)/this.spacing*this.gap,(this.currentValue[1]-this.minimum)/this.spacing*this.gap]:(this.currentValue-this.minimum)/this.spacing*this.gap;},limit:function limit(){return this.isRange?this.fixed?[[0,(this.total-this.fixedValue)*this.gap],[this.fixedValue*this.gap,this.size]]:[[0,this.position[1]],[this.position[0],this.size]]:[0,this.size];},valueLimit:function valueLimit(){return this.isRange?this.fixed?[[this.minimum,this.maximum-this.fixedValue*(this.spacing*this.multiple)/this.multiple],[this.minimum+this.fixedValue*(this.spacing*this.multiple)/this.multiple,this.maximum]]:[[this.minimum,this.currentValue[1]],[this.currentValue[0],this.maximum]]:[this.minimum,this.maximum];},idleSlider:function idleSlider(){return 0===this.currentSlider?1:0;},wrapStyles:function wrapStyles(){return"vertical"===this.direction?{height:"number"==typeof this.height?this.height+"px":this.height,padding:this.dotHeightVal/2+"px "+this.dotWidthVal/2+"px"}:{width:"number"==typeof this.width?this.width+"px":this.width,padding:this.dotHeightVal/2+"px "+this.dotWidthVal/2+"px"};},sliderStyles:function sliderStyles(){return Array.isArray(this.sliderStyle)?this.isRange?this.sliderStyle:this.sliderStyle[1]:"function"==typeof this.sliderStyle?this.sliderStyle(this.val,this.currentIndex):this.isRange?[this.sliderStyle,this.sliderStyle]:this.sliderStyle;},focusStyles:function focusStyles(){return Array.isArray(this.focusStyle)?this.isRange?this.focusStyle:this.focusStyle[1]:"function"==typeof this.focusStyle?this.focusStyle(this.val,this.currentIndex):this.isRange?[this.focusStyle,this.focusStyle]:this.focusStyle;},disabledDotStyles:function disabledDotStyles(){var t=this.disabledDotStyle;if(Array.isArray(t))return t;if("function"==typeof t){var e=t(this.val,this.currentIndex);return Array.isArray(e)?e:[e,e];}return t?[t,t]:[{backgroundColor:"#ccc"},{backgroundColor:"#ccc"}];},tooltipStyles:function tooltipStyles(){return Array.isArray(this.tooltipStyle)?this.isRange?this.tooltipStyle:this.tooltipStyle[1]:"function"==typeof this.tooltipStyle?this.tooltipStyle(this.val,this.currentIndex):this.isRange?[this.tooltipStyle,this.tooltipStyle]:this.tooltipStyle;},elemStyles:function elemStyles(){return"vertical"===this.direction?{width:this.width+"px",height:"100%"}:{height:this.height+"px"};},dotStyles:function dotStyles(){return"vertical"===this.direction?{width:this.dotWidthVal+"px",height:this.dotHeightVal+"px",left:-(this.dotWidthVal-this.width)/2+"px"}:{width:this.dotWidthVal+"px",height:this.dotHeightVal+"px",top:-(this.dotHeightVal-this.height)/2+"px"};},piecewiseDotStyle:function piecewiseDotStyle(){return"vertical"===this.direction?{width:this.width+"px",height:this.width+"px"}:{width:this.height+"px",height:this.height+"px"};},piecewiseDotWrap:function piecewiseDotWrap(){if(!this.piecewise&&!this.piecewiseLabel)return!1;for(var t=[],e=0;e<=this.total;e++){var i="vertical"===this.direction?{bottom:this.gap*e-this.width/2+"px",left:0}:{left:this.gap*e-this.height/2+"px",top:0},s=this.reverse?this.total-e:e,r=this.data?this.data[s]:this.spacing*s+this.min;t.push({style:i,label:this.formatter?this.formatting(r):r,inRange:s>=this.indexRange[0]&&s<=this.indexRange[1]});}return t;}},watch:{value:function value(t){this.flag||this.setValue(t,!0);},max:function max(t){if(t<this.min)return this.printError("The maximum value can not be less than the minimum value.");var e=this.limitValue(this.val);this.setValue(e),this.refresh();},min:function min(t){if(t>this.max)return this.printError("The minimum value can not be greater than the maximum value.");var e=this.limitValue(this.val);this.setValue(e),this.refresh();},show:function show(t){var e=this;t&&!this.size&&this.$nextTick(function(){e.refresh();});},fixed:function fixed(){this.computedFixedValue();}},methods:{bindEvents:function bindEvents(){document.addEventListener("touchmove",this.moving,{passive:!1}),document.addEventListener("touchend",this.moveEnd,{passive:!1}),document.addEventListener("mousedown",this.blurSlider),document.addEventListener("mousemove",this.moving),document.addEventListener("mouseup",this.moveEnd),document.addEventListener("mouseleave",this.moveEnd),document.addEventListener("keydown",this.handleKeydown),document.addEventListener("keyup",this.handleKeyup),window.addEventListener("resize",this.refresh),this.isRange&&this.tooltipMerge&&(this.$refs.dot0.addEventListener("transitionend",this.handleOverlapTooltip),this.$refs.dot1.addEventListener("transitionend",this.handleOverlapTooltip));},unbindEvents:function unbindEvents(){document.removeEventListener("touchmove",this.moving),document.removeEventListener("touchend",this.moveEnd),document.removeEventListener("mousedown",this.blurSlider),document.removeEventListener("mousemove",this.moving),document.removeEventListener("mouseup",this.moveEnd),document.removeEventListener("mouseleave",this.moveEnd),document.removeEventListener("keydown",this.handleKeydown),document.removeEventListener("keyup",this.handleKeyup),window.removeEventListener("resize",this.refresh),this.isRange&&this.tooltipMerge&&(this.$refs.dot0.removeEventListener("transitionend",this.handleOverlapTooltip),this.$refs.dot1.removeEventListener("transitionend",this.handleOverlapTooltip));},handleKeydown:function handleKeydown(t){if(!this.useKeyboard||!this.focusFlag)return!1;switch(t.keyCode){case 37:case 40:t.preventDefault(),this.keydownFlag=!0,this.flag=!0,this.changeFocusSlider(this.actionsKeyboard[0]);break;case 38:case 39:t.preventDefault(),this.keydownFlag=!0,this.flag=!0,this.changeFocusSlider(this.actionsKeyboard[1]);}},handleKeyup:function handleKeyup(){this.keydownFlag&&(this.keydownFlag=!1,this.flag=!1);},changeFocusSlider:function changeFocusSlider(t){var e=this;if(this.isRange){var i=this.currentIndex.map(function(i,s){if(s===e.focusSlider||e.fixed){var r=t(i),o=e.fixed?e.valueLimit[s]:[0,e.total];if(r<=o[1]&&r>=o[0])return r;}return i;});i[0]>i[1]&&(this.focusSlider=0===this.focusSlider?1:0,i=i.reverse()),this.setIndex(i);}else this.setIndex(t(this.currentIndex));},blurSlider:function blurSlider(t){var e=this.isRange?this.$refs["dot"+this.focusSlider]:this.$refs.dot;if(!e||e===t.target)return!1;this.focusFlag=!1;},formatting:function formatting(t){return"string"==typeof this.formatter?this.formatter.replace(/\{value\}/,t):this.formatter(t);},mergeFormatting:function mergeFormatting(t,e){return"string"==typeof this.mergeFormatter?this.mergeFormatter.replace(/\{(value1|value2)\}/g,function(i,s){return"value1"===s?t:e;}):this.mergeFormatter(t,e);},getPos:function getPos(t){return this.realTime&&this.getStaticData(),"vertical"===this.direction?this.reverse?t.pageY-this.offset:this.size-(t.pageY-this.offset):this.reverse?this.size-(t.clientX-this.offset):t.clientX-this.offset;},processClick:function processClick(t){this.fixed&&t.stopPropagation();},wrapClick:function wrapClick(t){var e=this;if(this.isDisabled||!this.clickable||this.processFlag)return!1;var i=this.getPos(t);if(this.isRange)if(this.disabledArray.every(function(t){return!1===t;}))this.currentSlider=i>(this.position[1]-this.position[0])/2+this.position[0]?1:0;else if(this.disabledArray[0]){if(i<this.position[0])return!1;this.currentSlider=1;}else if(this.disabledArray[1]){if(i>this.position[1])return!1;this.currentSlider=0;}if(this.disabledArray[this.currentSlider])return!1;if(this.setValueOnPos(i),this.isRange&&this.tooltipMerge){var s=setInterval(function(){return e.handleOverlapTooltip();},16.7);setTimeout(function(){return window.clearInterval(s);},1e3*this.speed);}},moveStart:function moveStart(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0,i=arguments[2];if(this.disabledArray[e])return!1;if(this.stopPropagation&&t.stopPropagation(),this.isRange&&(this.currentSlider=e,i)){if(!this.processDragable)return!1;this.processFlag=!0,this.processSign={pos:this.position,start:this.getPos(t.targetTouches&&t.targetTouches[0]?t.targetTouches[0]:t)};}!i&&this.useKeyboard&&(this.focusFlag=!0,this.focusSlider=e),this.flag=!0,this.$emit("drag-start",this);},moving:function moving(t){if(this.stopPropagation&&t.stopPropagation(),!this.flag)return!1;t.preventDefault(),t.targetTouches&&t.targetTouches[0]&&(t=t.targetTouches[0]),this.processFlag?(this.currentSlider=0,this.setValueOnPos(this.processSign.pos[0]+this.getPos(t)-this.processSign.start,!0),this.currentSlider=1,this.setValueOnPos(this.processSign.pos[1]+this.getPos(t)-this.processSign.start,!0)):this.setValueOnPos(this.getPos(t),!0),this.isRange&&this.tooltipMerge&&this.handleOverlapTooltip();},moveEnd:function moveEnd(t){var e=this;if(this.stopPropagation&&t.stopPropagation(),!this.flag)return!1;this.$emit("drag-end",this),this.lazy&&this.isDiff(this.val,this.value)&&this.syncValue(),this.flag=!1,window.setTimeout(function(){e.processFlag=!1;},0),this.setPosition();},setValueOnPos:function setValueOnPos(t,e){var i=this.isRange?this.limit[this.currentSlider]:this.limit,s=this.isRange?this.valueLimit[this.currentSlider]:this.valueLimit;if(t>=i[0]&&t<=i[1]){this.setTransform(t);var r=this.getValueByIndex(Math.round(t/this.gap));this.setCurrentValue(r,e),this.isRange&&this.fixed&&(this.setTransform(t+this.fixedValue*this.gap*(0===this.currentSlider?1:-1),!0),this.setCurrentValue((r*this.multiple+this.fixedValue*this.spacing*this.multiple*(0===this.currentSlider?1:-1))/this.multiple,e,!0));}else t<i[0]?(this.setTransform(i[0]),this.setCurrentValue(s[0]),this.isRange&&this.fixed?(this.setTransform(this.limit[this.idleSlider][0],!0),this.setCurrentValue(this.valueLimit[this.idleSlider][0],e,!0)):this.fixed||this.disabledArray[0]||1!==this.currentSlider||(this.focusSlider=0,this.currentSlider=0)):(this.setTransform(i[1]),this.setCurrentValue(s[1]),this.isRange&&this.fixed?(this.setTransform(this.limit[this.idleSlider][1],!0),this.setCurrentValue(this.valueLimit[this.idleSlider][1],e,!0)):this.fixed||this.disabledArray[1]||0!==this.currentSlider||(this.focusSlider=1,this.currentSlider=1));},isDiff:function isDiff(t,e){return Object.prototype.toString.call(t)!==Object.prototype.toString.call(e)||(Array.isArray(t)&&t.length===e.length?t.some(function(t,i){return t!==e[i];}):t!==e);},setCurrentValue:function setCurrentValue(t,e,i){var s=i?this.idleSlider:this.currentSlider;if(t<this.minimum||t>this.maximum)return!1;this.isRange?this.isDiff(this.currentValue[s],t)&&(this.currentValue.splice(s,1,t),this.lazy&&this.flag||this.syncValue()):this.isDiff(this.currentValue,t)&&(this.currentValue=t,this.lazy&&this.flag||this.syncValue()),e||this.setPosition();},getValueByIndex:function getValueByIndex(t){return(this.spacing*this.multiple*t+this.minimum*this.multiple)/this.multiple;},getIndexByValue:function getIndexByValue(t){return Math.round((t-this.minimum)*this.multiple)/(this.spacing*this.multiple);},setIndex:function setIndex(t){if(Array.isArray(t)&&this.isRange){var e=void 0;e=this.data?[this.data[t[0]],this.data[t[1]]]:[this.getValueByIndex(t[0]),this.getValueByIndex(t[1])],this.setValue(e);}else t=this.getValueByIndex(t),this.isRange&&(this.currentSlider=t>(this.currentValue[1]-this.currentValue[0])/2+this.currentValue[0]?1:0),this.setCurrentValue(t);},setValue:function setValue(t,e,i){var s=this;if(this.isDiff(this.val,t)){var r=this.limitValue(t);this.val=this.isRange?r.concat():r,this.computedFixedValue(),this.syncValue(e);}this.$nextTick(function(){return s.setPosition(i);});},computedFixedValue:function computedFixedValue(){if(!this.fixed)return this.fixedValue=0,!1;this.fixedValue=this.currentIndex[1]-this.currentIndex[0];},setPosition:function setPosition(t){this.flag||this.setTransitionTime(void 0===t?this.speed:t),this.isRange?(this.setTransform(this.position[0],1===this.currentSlider),this.setTransform(this.position[1],0===this.currentSlider)):this.setTransform(this.position),this.flag||this.setTransitionTime(0);},setTransform:function setTransform(t,e){var i=e?this.idleSlider:this.currentSlider,r=s(("vertical"===this.direction?this.dotHeightVal/2-t:t-this.dotWidthVal/2)*(this.reverse?-1:1)),o="vertical"===this.direction?"translateY("+r+"px)":"translateX("+r+"px)",n=this.fixed?this.fixedValue*this.gap+"px":(0===i?this.position[1]-t:t-this.position[0])+"px",l=this.fixed?(0===i?t:t-this.fixedValue*this.gap)+"px":(0===i?t:this.position[0])+"px";this.isRange?(this.slider[i].style.transform=o,this.slider[i].style.WebkitTransform=o,this.slider[i].style.msTransform=o,"vertical"===this.direction?(this.$refs.process.style.height=n,this.$refs.process.style[this.reverse?"top":"bottom"]=l):(this.$refs.process.style.width=n,this.$refs.process.style[this.reverse?"right":"left"]=l)):(this.slider.style.transform=o,this.slider.style.WebkitTransform=o,this.slider.style.msTransform=o,"vertical"===this.direction?(this.$refs.process.style.height=t+"px",this.$refs.process.style[this.reverse?"top":"bottom"]=0):(this.$refs.process.style.width=t+"px",this.$refs.process.style[this.reverse?"right":"left"]=0));},setTransitionTime:function setTransitionTime(t){if(t||this.$refs.process.offsetWidth,this.isRange){for(var e=0;e<this.slider.length;e++){this.slider[e].style.transitionDuration=t+"s",this.slider[e].style.WebkitTransitionDuration=t+"s";}this.$refs.process.style.transitionDuration=t+"s",this.$refs.process.style.WebkitTransitionDuration=t+"s";}else this.slider.style.transitionDuration=t+"s",this.slider.style.WebkitTransitionDuration=t+"s",this.$refs.process.style.transitionDuration=t+"s",this.$refs.process.style.WebkitTransitionDuration=t+"s";},limitValue:function limitValue(t){var e=this;if(this.data)return t;var i=function i(_i2){return _i2<e.min?(e.printError("The value of the slider is "+t+", the minimum value is "+e.min+", the value of this slider can not be less than the minimum value"),e.min):_i2>e.max?(e.printError("The value of the slider is "+t+", the maximum value is "+e.max+", the value of this slider can not be greater than the maximum value"),e.max):_i2;};return this.isRange?t.map(function(t){return i(t);}):i(t);},syncValue:function syncValue(t){var e=this.isRange?this.val.concat():this.val;this.$emit("input",e),this.keydownFlag&&this.$emit("on-keypress",e),t||this.$emit("callback",e);},getValue:function getValue(){return this.val;},getIndex:function getIndex(){return this.currentIndex;},getStaticData:function getStaticData(){this.$refs.elem&&(this.size="vertical"===this.direction?this.$refs.elem.offsetHeight:this.$refs.elem.offsetWidth,this.offset="vertical"===this.direction?this.$refs.elem.getBoundingClientRect().top+window.pageYOffset||document.documentElement.scrollTop:this.$refs.elem.getBoundingClientRect().left);},refresh:function refresh(){this.$refs.elem&&(this.getStaticData(),this.computedFixedValue(),this.setPosition());},printError:function printError(t){this.debug&&console.error("[VueSlider error]: "+t);},handleOverlapTooltip:function handleOverlapTooltip(){var t=this.tooltipDirection[0]===this.tooltipDirection[1];if(this.isRange&&t){var e=this.reverse?this.$refs.tooltip1:this.$refs.tooltip0,i=this.reverse?this.$refs.tooltip0:this.$refs.tooltip1,s=e.getBoundingClientRect().right,r=i.getBoundingClientRect().left,o=e.getBoundingClientRect().y,n=i.getBoundingClientRect().y+i.getBoundingClientRect().height,l="horizontal"===this.direction&&s>r,a="vertical"===this.direction&&n>o;l||a?this.handleDisplayMergedTooltip(!0):this.handleDisplayMergedTooltip(!1);}},handleDisplayMergedTooltip:function handleDisplayMergedTooltip(t){var e=this.$refs.tooltip0,i=this.$refs.tooltip1,s=this.$refs.process.getElementsByClassName("vue-merged-tooltip")[0];t?(e.style.visibility="hidden",i.style.visibility="hidden",s.style.visibility="visible"):(e.style.visibility="visible",i.style.visibility="visible",s.style.visibility="hidden");}},mounted:function mounted(){var t=this;if(this.isComponentExists=!0,"undefined"==typeof window||"undefined"==typeof document)return this.printError("window or document is undefined, can not be initialization.");this.$nextTick(function(){t.isComponentExists&&(t.getStaticData(),t.setValue(t.limitValue(t.value),!0,t.startAnimation?t.speed:0),t.bindEvents());}),this.isMounted=!0;},beforeDestroy:function beforeDestroy(){this.isComponentExists=!1,this.unbindEvents();}};},function(t,e,i){"use strict";var s=i(0);t.exports=s;},function(t,e,i){e=t.exports=i(4)(),e.push([t.i,'.vue-slider-component{position:relative;box-sizing:border-box;-ms-user-select:none;user-select:none;-webkit-user-select:none;-moz-user-select:none;-o-user-select:none}.vue-slider-component.vue-slider-disabled{opacity:.5;cursor:not-allowed}.vue-slider-component.vue-slider-has-label{margin-bottom:15px}.vue-slider-component.vue-slider-disabled .vue-slider-dot{cursor:not-allowed}.vue-slider-component .vue-slider{position:relative;display:block;border-radius:15px;background-color:#ccc}.vue-slider-component .vue-slider:after{content:"";position:absolute;left:0;top:0;width:100%;height:100%;z-index:2}.vue-slider-component .vue-slider-process{position:absolute;border-radius:15px;background-color:#3498db;transition:all 0s;z-index:1}.vue-slider-component .vue-slider-process.vue-slider-process-dragable{cursor:pointer;z-index:3}.vue-slider-component.vue-slider-horizontal .vue-slider-process{width:0;height:100%;top:0;left:0;will-change:width}.vue-slider-component.vue-slider-vertical .vue-slider-process{width:100%;height:0;bottom:0;left:0;will-change:height}.vue-slider-component.vue-slider-horizontal-reverse .vue-slider-process{width:0;height:100%;top:0;right:0}.vue-slider-component.vue-slider-vertical-reverse .vue-slider-process{width:100%;height:0;top:0;left:0}.vue-slider-component .vue-slider-dot{position:absolute;border-radius:50%;background-color:#fff;box-shadow:.5px .5px 2px 1px rgba(0,0,0,.32);transition:all 0s;will-change:transform;cursor:pointer;z-index:5}.vue-slider-component .vue-slider-dot.vue-slider-dot-focus{box-shadow:0 0 2px 1px #3498db}.vue-slider-component .vue-slider-dot.vue-slider-dot-dragging{z-index:5}.vue-slider-component .vue-slider-dot.vue-slider-dot-disabled{z-index:4}.vue-slider-component.vue-slider-horizontal .vue-slider-dot{left:0}.vue-slider-component.vue-slider-vertical .vue-slider-dot{bottom:0}.vue-slider-component.vue-slider-horizontal-reverse .vue-slider-dot{right:0}.vue-slider-component.vue-slider-vertical-reverse .vue-slider-dot{top:0}.vue-slider-component .vue-slider-tooltip-wrap{display:none;position:absolute;z-index:9}.vue-slider-component .vue-slider-tooltip{display:block;font-size:14px;white-space:nowrap;padding:2px 5px;min-width:20px;text-align:center;color:#fff;border-radius:5px;border:1px solid #3498db;background-color:#3498db}.vue-slider-component .vue-slider-tooltip-wrap.vue-slider-tooltip-top{top:-9px;left:50%;transform:translate(-50%,-100%)}.vue-slider-component .vue-slider-tooltip-wrap.vue-slider-tooltip-bottom{bottom:-9px;left:50%;transform:translate(-50%,100%)}.vue-slider-component .vue-slider-tooltip-wrap.vue-slider-tooltip-left{top:50%;left:-9px;transform:translate(-100%,-50%)}.vue-slider-component .vue-slider-tooltip-wrap.vue-slider-tooltip-right{top:50%;right:-9px;transform:translate(100%,-50%)}.vue-slider-component .vue-slider-tooltip-top .vue-merged-tooltip .vue-slider-tooltip:before,.vue-slider-component .vue-slider-tooltip-wrap.vue-slider-tooltip-top .vue-slider-tooltip:before{content:"";position:absolute;bottom:-10px;left:50%;width:0;height:0;border:5px solid transparent;border:6px solid transparent\\0;border-top-color:inherit;transform:translate(-50%)}.vue-slider-component .vue-slider-tooltip-wrap.vue-merged-tooltip{display:block;visibility:hidden}.vue-slider-component .vue-slider-tooltip-bottom .vue-merged-tooltip .vue-slider-tooltip:before,.vue-slider-component .vue-slider-tooltip-wrap.vue-slider-tooltip-bottom .vue-slider-tooltip:before{content:"";position:absolute;top:-10px;left:50%;width:0;height:0;border:5px solid transparent;border:6px solid transparent\\0;border-bottom-color:inherit;transform:translate(-50%)}.vue-slider-component .vue-slider-tooltip-left .vue-merged-tooltip .vue-slider-tooltip:before,.vue-slider-component .vue-slider-tooltip-wrap.vue-slider-tooltip-left .vue-slider-tooltip:before{content:"";position:absolute;top:50%;right:-10px;width:0;height:0;border:5px solid transparent;border:6px solid transparent\\0;border-left-color:inherit;transform:translateY(-50%)}.vue-slider-component .vue-slider-tooltip-right .vue-merged-tooltip .vue-slider-tooltip:before,.vue-slider-component .vue-slider-tooltip-wrap.vue-slider-tooltip-right .vue-slider-tooltip:before{content:"";position:absolute;top:50%;left:-10px;width:0;height:0;border:5px solid transparent;border:6px solid transparent\\0;border-right-color:inherit;transform:translateY(-50%)}.vue-slider-component .vue-slider-dot.vue-slider-hover:hover .vue-slider-tooltip-wrap{display:block}.vue-slider-component .vue-slider-dot.vue-slider-always .vue-slider-tooltip-wrap{display:block!important}.vue-slider-component .vue-slider-piecewise{position:absolute;width:100%;padding:0;margin:0;left:0;top:0;height:100%;list-style:none}.vue-slider-component .vue-slider-piecewise-item{position:absolute;width:8px;height:8px}.vue-slider-component .vue-slider-piecewise-dot{position:absolute;left:50%;top:50%;width:100%;height:100%;display:inline-block;background-color:rgba(0,0,0,.16);border-radius:50%;transform:translate(-50%,-50%);z-index:2;transition:all .3s}.vue-slider-component .vue-slider-piecewise-item:first-child .vue-slider-piecewise-dot,.vue-slider-component .vue-slider-piecewise-item:last-child .vue-slider-piecewise-dot{visibility:hidden}.vue-slider-component.vue-slider-horizontal-reverse .vue-slider-piecewise-label,.vue-slider-component.vue-slider-horizontal .vue-slider-piecewise-label{position:absolute;display:inline-block;top:100%;left:50%;white-space:nowrap;font-size:12px;color:#333;transform:translate(-50%,8px);visibility:visible}.vue-slider-component.vue-slider-vertical-reverse .vue-slider-piecewise-label,.vue-slider-component.vue-slider-vertical .vue-slider-piecewise-label{position:absolute;display:inline-block;top:50%;left:100%;white-space:nowrap;font-size:12px;color:#333;transform:translate(8px,-50%);visibility:visible}.vue-slider-component .vue-slider-sr-only{clip:rect(1px,1px,1px,1px);height:1px;width:1px;overflow:hidden;position:absolute!important}',""]);},function(t,e){t.exports=function(){var t=[];return t.toString=function(){for(var t=[],e=0;e<this.length;e++){var i=this[e];i[2]?t.push("@media "+i[2]+"{"+i[1]+"}"):t.push(i[1]);}return t.join("");},t.i=function(e,i){"string"==typeof e&&(e=[[null,e,""]]);for(var s={},r=0;r<this.length;r++){var o=this[r][0];"number"==typeof o&&(s[o]=!0);}for(r=0;r<e.length;r++){var n=e[r];"number"==typeof n[0]&&s[n[0]]||(i&&!n[2]?n[2]=i:i&&(n[2]="("+n[2]+") and ("+i+")"),t.push(n));}},t;};},function(t,e){t.exports=function(t,e,i,s){var r,o=t=t||{},n=_typeof2(t.default);"object"!==n&&"function"!==n||(r=t,o=t.default);var l="function"==typeof o?o.options:o;if(e&&(l.render=e.render,l.staticRenderFns=e.staticRenderFns),i&&(l._scopeId=i),s){var a=Object.create(l.computed||null);Object.keys(s).forEach(function(t){var e=s[t];a[t]=function(){return e;};}),l.computed=a;}return{esModule:r,exports:o,options:l};};},function(t,e){t.exports={render:function render(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{directives:[{name:"show",rawName:"v-show",value:t.show,expression:"show"}],ref:"wrap",class:["vue-slider-component",t.flowDirection,t.disabledClass,t.stateClass,{"vue-slider-has-label":t.piecewiseLabel}],style:[t.wrapStyles,t.boolDisabled?t.disabledStyle:null],on:{click:t.wrapClick}},[i("div",{ref:"elem",staticClass:"vue-slider",style:[t.elemStyles,t.bgStyle],attrs:{"aria-hidden":"true"}},[t.isRange?[i("div",{key:"dot0",ref:"dot0",class:[t.tooltipStatus,"vue-slider-dot",{"vue-slider-dot-focus":t.focusFlag&&0===t.focusSlider,"vue-slider-dot-dragging":t.flag&&0===t.currentSlider,"vue-slider-dot-disabled":!t.boolDisabled&&t.disabledArray[0]}],style:[t.dotStyles,!t.boolDisabled&&t.disabledArray[0]?t.disabledDotStyles[0]:null,t.sliderStyles[0],t.focusFlag&&0===t.focusSlider?t.focusStyles[0]:null],on:{mousedown:function mousedown(e){t.moveStart(e,0);},touchstart:function touchstart(e){t.moveStart(e,0);}}},[i("div",{ref:"tooltip0",class:["vue-slider-tooltip-"+t.tooltipDirection[0],"vue-slider-tooltip-wrap"]},[t._t("tooltip",[i("span",{staticClass:"vue-slider-tooltip",style:t.tooltipStyles[0]},[t._v(t._s(t.formatter?t.formatting(t.val[0]):t.val[0]))])],{value:t.val[0],index:0,disabled:!t.boolDisabled&&t.disabledArray[0]})],2)]),t._v(" "),i("div",{key:"dot1",ref:"dot1",class:[t.tooltipStatus,"vue-slider-dot",{"vue-slider-dot-focus":t.focusFlag&&1===t.focusSlider,"vue-slider-dot-dragging":t.flag&&1===t.currentSlider,"vue-slider-dot-disabled":!t.boolDisabled&&t.disabledArray[1]}],style:[t.dotStyles,!t.boolDisabled&&t.disabledArray[1]?t.disabledDotStyles[1]:null,t.sliderStyles[1],t.focusFlag&&1===t.focusSlider?t.focusStyles[1]:null],on:{mousedown:function mousedown(e){t.moveStart(e,1);},touchstart:function touchstart(e){t.moveStart(e,1);}}},[i("div",{ref:"tooltip1",class:["vue-slider-tooltip-"+t.tooltipDirection[1],"vue-slider-tooltip-wrap"]},[t._t("tooltip",[i("span",{staticClass:"vue-slider-tooltip",style:t.tooltipStyles[1]},[t._v(t._s(t.formatter?t.formatting(t.val[1]):t.val[1]))])],{value:t.val[1],index:1,disabled:!t.boolDisabled&&t.disabledArray[1]})],2)])]:[i("div",{key:"dot",ref:"dot",class:[t.tooltipStatus,"vue-slider-dot",{"vue-slider-dot-focus":t.focusFlag&&0===t.focusSlider,"vue-slider-dot-dragging":t.flag&&0===t.currentSlider}],style:[t.dotStyles,t.sliderStyles,t.focusFlag&&0===t.focusSlider?t.focusStyles:null],on:{mousedown:t.moveStart,touchstart:t.moveStart}},[i("div",{class:["vue-slider-tooltip-"+t.tooltipDirection,"vue-slider-tooltip-wrap"]},[t._t("tooltip",[i("span",{staticClass:"vue-slider-tooltip",style:t.tooltipStyles},[t._v(t._s(t.formatter?t.formatting(t.val):t.val))])],{value:t.val})],2)])],t._v(" "),i("ul",{staticClass:"vue-slider-piecewise"},t._l(t.piecewiseDotWrap,function(e,s){return i("li",{key:s,staticClass:"vue-slider-piecewise-item",style:[t.piecewiseDotStyle,e.style]},[t._t("piecewise",[t.piecewise?i("span",{staticClass:"vue-slider-piecewise-dot",style:[t.piecewiseStyle,e.inRange?t.piecewiseActiveStyle:null]}):t._e()],{label:e.label,index:s,first:0===s,last:s===t.piecewiseDotWrap.length-1,active:e.inRange}),t._v(" "),t._t("label",[t.piecewiseLabel?i("span",{staticClass:"vue-slider-piecewise-label",style:[t.labelStyle,e.inRange?t.labelActiveStyle:null]},[t._v("\n            "+t._s(e.label)+"\n          ")]):t._e()],{label:e.label,index:s,first:0===s,last:s===t.piecewiseDotWrap.length-1,active:e.inRange})],2);})),t._v(" "),i("div",{ref:"process",class:["vue-slider-process",{"vue-slider-process-dragable":t.isRange&&t.processDragable}],style:t.processStyle,on:{click:t.processClick,mousedown:function mousedown(e){t.moveStart(e,0,!0);},touchstart:function touchstart(e){t.moveStart(e,0,!0);}}},[i("div",{ref:"mergedTooltip",staticClass:"vue-merged-tooltip",class:["vue-slider-tooltip-"+t.tooltipDirection[0],"vue-slider-tooltip-wrap"],style:t.tooltipMergedPosition},[t._t("tooltip",[i("span",{staticClass:"vue-slider-tooltip",style:t.tooltipStyles},[t._v("\n            "+t._s(t.mergeFormatter?t.mergeFormatting(t.val[0],t.val[1]):t.formatter?t.val[0]===t.val[1]?t.formatting(t.val[0]):t.formatting(t.val[0])+" - "+t.formatting(t.val[1]):t.val[0]===t.val[1]?t.val[0]:t.val[0]+" - "+t.val[1])+"\n          ")])])],2)]),t._v(" "),t.isRange||t.data?t._e():i("input",{directives:[{name:"model",rawName:"v-model",value:t.val,expression:"val"}],staticClass:"vue-slider-sr-only",attrs:{type:"range",min:t.min,max:t.max},domProps:{value:t.val},on:{__r:function __r(e){t.val=e.target.value;}}})],2)]);},staticRenderFns:[]};},function(t,e,i){var s=i(3);"string"==typeof s&&(s=[[t.i,s,""]]),s.locals&&(t.exports=s.locals);i(8)("743d98f5",s,!0);},function(t,e,i){function s(t){for(var e=0;e<t.length;e++){var i=t[e],s=d[i.id];if(s){s.refs++;for(var r=0;r<s.parts.length;r++){s.parts[r](i.parts[r]);}for(;r<i.parts.length;r++){s.parts.push(o(i.parts[r]));}s.parts.length>i.parts.length&&(s.parts.length=i.parts.length);}else{for(var n=[],r=0;r<i.parts.length;r++){n.push(o(i.parts[r]));}d[i.id]={id:i.id,refs:1,parts:n};}}}function r(){var t=document.createElement("style");return t.type="text/css",h.appendChild(t),t;}function o(t){var e,i,s=document.querySelector('style[data-vue-ssr-id~="'+t.id+'"]');if(s){if(f)return v;s.parentNode.removeChild(s);}if(m){var o=p++;s=c||(c=r()),e=n.bind(null,s,o,!1),i=n.bind(null,s,o,!0);}else s=r(),e=l.bind(null,s),i=function i(){s.parentNode.removeChild(s);};return e(t),function(s){if(s){if(s.css===t.css&&s.media===t.media&&s.sourceMap===t.sourceMap)return;e(t=s);}else i();};}function n(t,e,i,s){var r=i?"":s.css;if(t.styleSheet)t.styleSheet.cssText=g(e,r);else{var o=document.createTextNode(r),n=t.childNodes;n[e]&&t.removeChild(n[e]),n.length?t.insertBefore(o,n[e]):t.appendChild(o);}}function l(t,e){var i=e.css,s=e.media,r=e.sourceMap;if(s&&t.setAttribute("media",s),r&&(i+="\n/*# sourceURL="+r.sources[0]+" */",i+="\n/*# sourceMappingURL=data:application/json;base64,"+btoa(unescape(encodeURIComponent(JSON.stringify(r))))+" */"),t.styleSheet)t.styleSheet.cssText=i;else{for(;t.firstChild;){t.removeChild(t.firstChild);}t.appendChild(document.createTextNode(i));}}var a="undefined"!=typeof document;if("undefined"!=typeof DEBUG&&DEBUG&&!a)throw new Error("vue-style-loader cannot be used in a non-browser environment. Use { target: 'node' } in your Webpack config to indicate a server-rendering environment.");var u=i(9),d={},h=a&&(document.head||document.getElementsByTagName("head")[0]),c=null,p=0,f=!1,v=function v(){},m="undefined"!=typeof navigator&&/msie [6-9]\b/.test(navigator.userAgent.toLowerCase());t.exports=function(t,e,i){f=i;var r=u(t,e);return s(r),function(e){for(var i=[],o=0;o<r.length;o++){var n=r[o],l=d[n.id];l.refs--,i.push(l);}e?(r=u(t,e),s(r)):r=[];for(var o=0;o<i.length;o++){var l=i[o];if(0===l.refs){for(var a=0;a<l.parts.length;a++){l.parts[a]();}delete d[l.id];}}};};var g=function(){var t=[];return function(e,i){return t[e]=i,t.filter(Boolean).join("\n");};}();},function(t,e){t.exports=function(t,e){for(var i=[],s={},r=0;r<e.length;r++){var o=e[r],n=o[0],l=o[1],a=o[2],u=o[3],d={id:t+":"+r,css:l,media:a,sourceMap:u};s[n]?s[n].parts.push(d):i.push(s[n]={id:n,parts:[d]});}return i;};}]);});/***/},/* 62 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Remove_vue__=__webpack_require__(17);/* unused harmony namespace reexport *//* harmony import */var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_9f6a5c0c_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Remove_vue__=__webpack_require__(65);var disposed=false;function injectStyle(ssrContext){if(disposed)return;__webpack_require__(63);}var normalizeComponent=__webpack_require__(2);/* script *//* template *//* template functional */var __vue_template_functional__=false;/* styles */var __vue_styles__=injectStyle;/* scopeId */var __vue_scopeId__=null;/* moduleIdentifier (server only) */var __vue_module_identifier__=null;var Component=normalizeComponent(__WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Remove_vue__["a"/* default */],__WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_9f6a5c0c_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Remove_vue__["a"/* default */],__vue_template_functional__,__vue_styles__,__vue_scopeId__,__vue_module_identifier__);Component.options.__file="assets/js/src/components/Remove.vue";/* hot reload */if(false){(function(){var hotAPI=require("vue-hot-reload-api");hotAPI.install(require("vue"),false);if(!hotAPI.compatible)return;module.hot.accept();if(!module.hot.data){hotAPI.createRecord("data-v-9f6a5c0c",Component.options);}else{hotAPI.reload("data-v-9f6a5c0c",Component.options);}module.hot.dispose(function(data){disposed=true;});})();}/* harmony default export */__webpack_exports__["a"]=Component.exports;/***/},/* 63 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(64);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("5da3485d",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-9f6a5c0c\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Remove.vue",function(){var newContent=require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-9f6a5c0c\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Remove.vue");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 64 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\n.searchwp-remove {\n  color: #AA0000;\n  text-decoration: underline;\n  font-size: 12px;\n  font-weight: normal;\n}\n.searchwp-remove:hover {\n    color: #AA0000;\n    text-decoration: none;\n}\n.searchwp-remove:hover span {\n      opacity: 1;\n}\n",""]);// exports
/***/},/* 65 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var render=function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("a",{staticClass:"searchwp-remove",attrs:{href:"#"}},[!_vm.icon?_c("span",[_vm._v(_vm._s(_vm.text))]):_c("span",{staticClass:"dashicons dashicons-dismiss"})]);};var staticRenderFns=[];render._withStripped=true;var esExports={render:render,staticRenderFns:staticRenderFns/* harmony default export */};__webpack_exports__["a"]=esExports;if(false){module.hot.accept();if(module.hot.data){require("vue-hot-reload-api").rerender("data-v-9f6a5c0c",esExports);}}/***/},/* 66 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(67);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("415b4afa",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-f7a20cb0\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Button.vue",function(){var newContent=require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-f7a20cb0\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Button.vue");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 67 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\n.searchwp-button > span {\n  display: flex;\n  align-items: center;\n}\n.searchwp-button > span span {\n    display: block;\n}\n.searchwp-button .dashicons {\n  margin-right: 0.2em;\n}\n",""]);// exports
/***/},/* 68 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var render=function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("button",{staticClass:"searchwp-button button",on:{click:_vm.buttonClick}},[_c("span",[_vm.icon&&_vm.icon.length?_c("span",{class:["dashicons",_vm.icon]}):_vm._e(),_vm._v(" "),_c("span",[_vm._v(_vm._s(_vm.label))])])]);};var staticRenderFns=[];render._withStripped=true;var esExports={render:render,staticRenderFns:staticRenderFns/* harmony default export */};__webpack_exports__["a"]=esExports;if(false){module.hot.accept();if(module.hot.data){require("vue-hot-reload-api").rerender("data-v-f7a20cb0",esExports);}}/***/},/* 69 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_InputText_vue__=__webpack_require__(19);/* unused harmony namespace reexport *//* harmony import */var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_0031cd91_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_InputText_vue__=__webpack_require__(72);var disposed=false;function injectStyle(ssrContext){if(disposed)return;__webpack_require__(70);}var normalizeComponent=__webpack_require__(2);/* script *//* template *//* template functional */var __vue_template_functional__=false;/* styles */var __vue_styles__=injectStyle;/* scopeId */var __vue_scopeId__=null;/* moduleIdentifier (server only) */var __vue_module_identifier__=null;var Component=normalizeComponent(__WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_InputText_vue__["a"/* default */],__WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_0031cd91_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_InputText_vue__["a"/* default */],__vue_template_functional__,__vue_styles__,__vue_scopeId__,__vue_module_identifier__);Component.options.__file="assets/js/src/components/InputText.vue";/* hot reload */if(false){(function(){var hotAPI=require("vue-hot-reload-api");hotAPI.install(require("vue"),false);if(!hotAPI.compatible)return;module.hot.accept();if(!module.hot.data){hotAPI.createRecord("data-v-0031cd91",Component.options);}else{hotAPI.reload("data-v-0031cd91",Component.options);}module.hot.dispose(function(data){disposed=true;});})();}/* harmony default export */__webpack_exports__["a"]=Component.exports;/***/},/* 70 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(71);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("529c7502",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-0031cd91\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./InputText.vue",function(){var newContent=require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-0031cd91\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./InputText.vue");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 71 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\n.searchwp-input-text {\n  display: flex;\n  justify-content: space-between;\n  align-items: center;\n  margin-bottom: 1em;\n}\n.searchwp-input-text label {\n    display: block;\n    width: 40%;\n}\n.searchwp-input-text input {\n    display: block;\n    width: 55%;\n    border: 1px solid #e8e8e8;\n    background: #fff;\n    border-radius: 3px;\n    padding: 8px;\n    box-shadow: none;\n}\n",""]);// exports
/***/},/* 72 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var render=function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("div",{staticClass:"searchwp-input-text"},[_c("label",{attrs:{for:"searchwp_input_text_"+_vm._uid}},[_vm._v(_vm._s(_vm.label))]),_vm._v(" "),_c("input",{ref:"input",attrs:{type:"text",placeholder:_vm.placeholder,id:"searchwp_input_text_"+_vm._uid},domProps:{value:_vm.value},on:{input:function input($event){_vm.$emit("input",$event.target.value);}}})]);};var staticRenderFns=[];render._withStripped=true;var esExports={render:render,staticRenderFns:staticRenderFns/* harmony default export */};__webpack_exports__["a"]=esExports;if(false){module.hot.accept();if(module.hot.data){require("vue-hot-reload-api").rerender("data-v-0031cd91",esExports);}}/***/},/* 73 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_InputCheckbox_vue__=__webpack_require__(20);/* unused harmony namespace reexport *//* harmony import */var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_b01b39b2_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_InputCheckbox_vue__=__webpack_require__(80);var disposed=false;function injectStyle(ssrContext){if(disposed)return;__webpack_require__(74);}var normalizeComponent=__webpack_require__(2);/* script *//* template *//* template functional */var __vue_template_functional__=false;/* styles */var __vue_styles__=injectStyle;/* scopeId */var __vue_scopeId__=null;/* moduleIdentifier (server only) */var __vue_module_identifier__=null;var Component=normalizeComponent(__WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_InputCheckbox_vue__["a"/* default */],__WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_b01b39b2_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_InputCheckbox_vue__["a"/* default */],__vue_template_functional__,__vue_styles__,__vue_scopeId__,__vue_module_identifier__);Component.options.__file="assets/js/src/components/InputCheckbox.vue";/* hot reload */if(false){(function(){var hotAPI=require("vue-hot-reload-api");hotAPI.install(require("vue"),false);if(!hotAPI.compatible)return;module.hot.accept();if(!module.hot.data){hotAPI.createRecord("data-v-b01b39b2",Component.options);}else{hotAPI.reload("data-v-b01b39b2",Component.options);}module.hot.dispose(function(data){disposed=true;});})();}/* harmony default export */__webpack_exports__["a"]=Component.exports;/***/},/* 74 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(75);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("259acafd",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-b01b39b2\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./InputCheckbox.vue",function(){var newContent=require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-b01b39b2\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./InputCheckbox.vue");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 75 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\n.searchwp-input-checkbox {\n  display: flex;\n  align-items: center;\n  line-height: 1.4;\n  padding-top: 0.5em;\n}\n.searchwp-input-checkbox input {\n    display: inline-block;\n    margin-top: 1px;\n    margin-right: 0.6em;\n}\n",""]);// exports
/***/},/* 76 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Tooltip_vue__=__webpack_require__(21);/* unused harmony namespace reexport *//* harmony import */var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_c1b64806_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Tooltip_vue__=__webpack_require__(79);var disposed=false;function injectStyle(ssrContext){if(disposed)return;__webpack_require__(77);}var normalizeComponent=__webpack_require__(2);/* script *//* template *//* template functional */var __vue_template_functional__=false;/* styles */var __vue_styles__=injectStyle;/* scopeId */var __vue_scopeId__=null;/* moduleIdentifier (server only) */var __vue_module_identifier__=null;var Component=normalizeComponent(__WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Tooltip_vue__["a"/* default */],__WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_c1b64806_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Tooltip_vue__["a"/* default */],__vue_template_functional__,__vue_styles__,__vue_scopeId__,__vue_module_identifier__);Component.options.__file="assets/js/src/components/Tooltip.vue";/* hot reload */if(false){(function(){var hotAPI=require("vue-hot-reload-api");hotAPI.install(require("vue"),false);if(!hotAPI.compatible)return;module.hot.accept();if(!module.hot.data){hotAPI.createRecord("data-v-c1b64806",Component.options);}else{hotAPI.reload("data-v-c1b64806",Component.options);}module.hot.dispose(function(data){disposed=true;});})();}/* harmony default export */__webpack_exports__["a"]=Component.exports;/***/},/* 77 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(78);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("143553b4",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-c1b64806\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Tooltip.vue",function(){var newContent=require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-c1b64806\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Tooltip.vue");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 78 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\n.searchwp-tooltip {\n  display: flex;\n  align-items: center;\n}\n.searchwp-tooltip .dashicons {\n    margin-left: 0.2em;\n    width: 18px;\n    height: 18px;\n    font-size: 18px;\n}\n",""]);// exports
/***/},/* 79 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var render=function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("span",{staticClass:"searchwp-tooltip"},[_c("span",[_vm._t("default")],2),_vm._v(" "),_c("span",{directives:[{name:"tooltip",rawName:"v-tooltip",value:_vm.content,expression:"content"}],staticClass:"dashicons dashicons-editor-help"})]);};var staticRenderFns=[];render._withStripped=true;var esExports={render:render,staticRenderFns:staticRenderFns/* harmony default export */};__webpack_exports__["a"]=esExports;if(false){module.hot.accept();if(module.hot.data){require("vue-hot-reload-api").rerender("data-v-c1b64806",esExports);}}/***/},/* 80 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var render=function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("div",{staticClass:"searchwp-input-checkbox"},[_c("input",{directives:[{name:"model",rawName:"v-model",value:_vm.val,expression:"val"}],attrs:{type:"checkbox",disabled:_vm.disabled,id:"searchwp_input_checkbox_"+_vm._uid},domProps:{checked:Array.isArray(_vm.val)?_vm._i(_vm.val,null)>-1:_vm.val},on:{change:function change($event){var $$a=_vm.val,$$el=$event.target,$$c=$$el.checked?true:false;if(Array.isArray($$a)){var $$v=null,$$i=_vm._i($$a,$$v);if($$el.checked){$$i<0&&(_vm.val=$$a.concat([$$v]));}else{$$i>-1&&(_vm.val=$$a.slice(0,$$i).concat($$a.slice($$i+1)));}}else{_vm.val=$$c;}}}}),_vm._v(" "),_vm.tooltip.length?_c("label",{attrs:{for:"searchwp_input_checkbox_"+_vm._uid}},[_c("searchwp-tooltip",{attrs:{content:_vm.tooltip}},[_vm._v(_vm._s(_vm.label))])],1):_c("label",{attrs:{for:"searchwp_input_checkbox_"+_vm._uid}},[_vm._v(_vm._s(_vm.label))])]);};var staticRenderFns=[];render._withStripped=true;var esExports={render:render,staticRenderFns:staticRenderFns/* harmony default export */};__webpack_exports__["a"]=esExports;if(false){module.hot.accept();if(module.hot.data){require("vue-hot-reload-api").rerender("data-v-b01b39b2",esExports);}}/***/},/* 81 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Dropdown_vue__=__webpack_require__(22);/* unused harmony namespace reexport *//* harmony import */var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_ae7460f2_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Dropdown_vue__=__webpack_require__(86);var disposed=false;function injectStyle(ssrContext){if(disposed)return;__webpack_require__(82);__webpack_require__(84);}var normalizeComponent=__webpack_require__(2);/* script *//* template *//* template functional */var __vue_template_functional__=false;/* styles */var __vue_styles__=injectStyle;/* scopeId */var __vue_scopeId__=null;/* moduleIdentifier (server only) */var __vue_module_identifier__=null;var Component=normalizeComponent(__WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Dropdown_vue__["a"/* default */],__WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_ae7460f2_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Dropdown_vue__["a"/* default */],__vue_template_functional__,__vue_styles__,__vue_scopeId__,__vue_module_identifier__);Component.options.__file="assets/js/src/components/Dropdown.vue";/* hot reload */if(false){(function(){var hotAPI=require("vue-hot-reload-api");hotAPI.install(require("vue"),false);if(!hotAPI.compatible)return;module.hot.accept();if(!module.hot.data){hotAPI.createRecord("data-v-ae7460f2",Component.options);}else{hotAPI.reload("data-v-ae7460f2",Component.options);}module.hot.dispose(function(data){disposed=true;});})();}/* harmony default export */__webpack_exports__["a"]=Component.exports;/***/},/* 82 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(83);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("645c9ef7",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../css-loader/index.js!../../vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-ae7460f2\",\"scoped\":false,\"hasInlineConfig\":false}!./vue-multiselect.min.css",function(){var newContent=require("!!../../css-loader/index.js!../../vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-ae7460f2\",\"scoped\":false,\"hasInlineConfig\":false}!./vue-multiselect.min.css");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 83 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\nfieldset[disabled] .multiselect{pointer-events:none\n}\n.multiselect__spinner{position:absolute;right:1px;top:1px;width:48px;height:35px;background:#fff;display:block\n}\n.multiselect__spinner:after,.multiselect__spinner:before{position:absolute;content:\"\";top:50%;left:50%;margin:-8px 0 0 -8px;width:16px;height:16px;border-radius:100%;border-color:#41b883 transparent transparent;border-style:solid;border-width:2px;box-shadow:0 0 0 1px transparent\n}\n.multiselect__spinner:before{animation:a 2.4s cubic-bezier(.41,.26,.2,.62);animation-iteration-count:infinite\n}\n.multiselect__spinner:after{animation:a 2.4s cubic-bezier(.51,.09,.21,.8);animation-iteration-count:infinite\n}\n.multiselect__loading-enter-active,.multiselect__loading-leave-active{transition:opacity .4s ease-in-out;opacity:1\n}\n.multiselect__loading-enter,.multiselect__loading-leave-active{opacity:0\n}\n.multiselect,.multiselect__input,.multiselect__single{font-family:inherit;font-size:16px;-ms-touch-action:manipulation;touch-action:manipulation\n}\n.multiselect{box-sizing:content-box;display:block;position:relative;width:100%;min-height:40px;text-align:left;color:#35495e\n}\n.multiselect *{box-sizing:border-box\n}\n.multiselect:focus{outline:none\n}\n.multiselect--disabled{opacity:.6\n}\n.multiselect--active{z-index:1\n}\n.multiselect--active:not(.multiselect--above) .multiselect__current,.multiselect--active:not(.multiselect--above) .multiselect__input,.multiselect--active:not(.multiselect--above) .multiselect__tags{border-bottom-left-radius:0;border-bottom-right-radius:0\n}\n.multiselect--active .multiselect__select{transform:rotate(180deg)\n}\n.multiselect--above.multiselect--active .multiselect__current,.multiselect--above.multiselect--active .multiselect__input,.multiselect--above.multiselect--active .multiselect__tags{border-top-left-radius:0;border-top-right-radius:0\n}\n.multiselect__input,.multiselect__single{position:relative;display:inline-block;min-height:20px;line-height:20px;border:none;border-radius:5px;background:#fff;padding:0 0 0 5px;width:100%;transition:border .1s ease;box-sizing:border-box;margin-bottom:8px;vertical-align:top\n}\n.multiselect__input::-webkit-input-placeholder{color:#35495e\n}\n.multiselect__input:-ms-input-placeholder{color:#35495e\n}\n.multiselect__input::placeholder{color:#35495e\n}\n.multiselect__tag~.multiselect__input,.multiselect__tag~.multiselect__single{width:auto\n}\n.multiselect__input:hover,.multiselect__single:hover{border-color:#cfcfcf\n}\n.multiselect__input:focus,.multiselect__single:focus{border-color:#a8a8a8;outline:none\n}\n.multiselect__single{padding-left:5px;margin-bottom:8px\n}\n.multiselect__tags-wrap{display:inline\n}\n.multiselect__tags{min-height:40px;display:block;padding:8px 40px 0 8px;border-radius:5px;border:1px solid #e8e8e8;background:#fff;font-size:14px\n}\n.multiselect__tag{position:relative;display:inline-block;padding:4px 26px 4px 10px;border-radius:5px;margin-right:10px;color:#fff;line-height:1;background:#41b883;margin-bottom:5px;white-space:nowrap;overflow:hidden;max-width:100%;text-overflow:ellipsis\n}\n.multiselect__tag-icon{cursor:pointer;margin-left:7px;position:absolute;right:0;top:0;bottom:0;font-weight:700;font-style:normal;width:22px;text-align:center;line-height:22px;transition:all .2s ease;border-radius:5px\n}\n.multiselect__tag-icon:after{content:\"\\D7\";color:#266d4d;font-size:14px\n}\n.multiselect__tag-icon:focus,.multiselect__tag-icon:hover{background:#369a6e\n}\n.multiselect__tag-icon:focus:after,.multiselect__tag-icon:hover:after{color:#fff\n}\n.multiselect__current{min-height:40px;overflow:hidden;padding:8px 12px 0;padding-right:30px;white-space:nowrap;border-radius:5px;border:1px solid #e8e8e8\n}\n.multiselect__current,.multiselect__select{line-height:16px;box-sizing:border-box;display:block;margin:0;text-decoration:none;cursor:pointer\n}\n.multiselect__select{position:absolute;width:40px;height:38px;right:1px;top:1px;padding:4px 8px;text-align:center;transition:transform .2s ease\n}\n.multiselect__select:before{position:relative;right:0;top:65%;color:#999;margin-top:4px;border-style:solid;border-width:5px 5px 0;border-color:#999 transparent transparent;content:\"\"\n}\n.multiselect__placeholder{color:#adadad;display:inline-block;margin-bottom:10px;padding-top:2px\n}\n.multiselect--active .multiselect__placeholder{display:none\n}\n.multiselect__content-wrapper{position:absolute;display:block;background:#fff;width:100%;max-height:240px;overflow:auto;border:1px solid #e8e8e8;border-top:none;border-bottom-left-radius:5px;border-bottom-right-radius:5px;z-index:1;-webkit-overflow-scrolling:touch\n}\n.multiselect__content{list-style:none;display:inline-block;padding:0;margin:0;min-width:100%;vertical-align:top\n}\n.multiselect--above .multiselect__content-wrapper{bottom:100%;border-bottom-left-radius:0;border-bottom-right-radius:0;border-top-left-radius:5px;border-top-right-radius:5px;border-bottom:none;border-top:1px solid #e8e8e8\n}\n.multiselect__content::webkit-scrollbar{display:none\n}\n.multiselect__element{display:block\n}\n.multiselect__option{display:block;padding:12px;min-height:40px;line-height:16px;text-decoration:none;text-transform:none;vertical-align:middle;position:relative;cursor:pointer;white-space:nowrap\n}\n.multiselect__option:after{top:0;right:0;position:absolute;line-height:40px;padding-right:12px;padding-left:20px;font-size:13px\n}\n.multiselect__option--highlight{background:#41b883;outline:none;color:#fff\n}\n.multiselect__option--highlight:after{content:attr(data-select);background:#41b883;color:#fff\n}\n.multiselect__option--selected{background:#f3f3f3;color:#35495e;font-weight:700\n}\n.multiselect__option--selected:after{content:attr(data-selected);color:silver\n}\n.multiselect__option--selected.multiselect__option--highlight{background:#ff6a6a;color:#fff\n}\n.multiselect__option--selected.multiselect__option--highlight:after{background:#ff6a6a;content:attr(data-deselect);color:#fff\n}\n.multiselect--disabled{background:#ededed;pointer-events:none\n}\n.multiselect--disabled .multiselect__current,.multiselect--disabled .multiselect__select,.multiselect__option--disabled{background:#ededed;color:#a6a6a6\n}\n.multiselect__option--disabled{cursor:text;pointer-events:none\n}\n.multiselect__option--group{background:#ededed;color:#35495e\n}\n.multiselect__option--group.multiselect__option--highlight{background:#35495e;color:#fff\n}\n.multiselect__option--group.multiselect__option--highlight:after{background:#35495e\n}\n.multiselect__option--disabled.multiselect__option--highlight{background:#dedede\n}\n.multiselect__option--group-selected.multiselect__option--highlight{background:#ff6a6a;color:#fff\n}\n.multiselect__option--group-selected.multiselect__option--highlight:after{background:#ff6a6a;content:attr(data-deselect);color:#fff\n}\n.multiselect-enter-active,.multiselect-leave-active{transition:all .15s ease\n}\n.multiselect-enter,.multiselect-leave-active{opacity:0\n}\n.multiselect__strong{margin-bottom:8px;line-height:20px;display:inline-block;vertical-align:top\n}\n[dir=rtl] .multiselect{text-align:right\n}\n[dir=rtl] .multiselect__select{right:auto;left:1px\n}\n[dir=rtl] .multiselect__tags{padding:8px 8px 0 40px\n}\n[dir=rtl] .multiselect__content{text-align:right\n}\n[dir=rtl] .multiselect__option:after{right:auto;left:0\n}\n[dir=rtl] .multiselect__clear{right:auto;left:12px\n}\n[dir=rtl] .multiselect__spinner{right:auto;left:1px\n}\n@keyframes a{\n0%{transform:rotate(0)\n}\nto{transform:rotate(2turn)\n}\n}",""]);// exports
/***/},/* 84 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(85);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("448597b8",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-ae7460f2\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=1!./Dropdown.vue",function(){var newContent=require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-ae7460f2\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=1!./Dropdown.vue");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 85 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\n.searchwp-dropdown {\n  border-top: 1px solid #ddd;\n  margin-top: 1em;\n  padding-top: 0.5em;\n}\n.searchwp-dropdown-choices {\n  display: flex;\n  align-items: center;\n}\n.searchwp-excluded-types-note {\n  cursor: default !important;\n  color: rgba(255, 255, 255, 0.55);\n  margin-top: 0.5em;\n  padding-top: 0.6em;\n}\n.searchwp-excluded-types-note:hover {\n    background: transparent !important;\n}\n.searchwp-excluded-types-note:first-of-type {\n    border-top: 1px solid rgba(255, 255, 255, 0.25);\n}\n.vue-popover {\n  background: #414141;\n  color: #fff;\n  border-radius: 3px;\n  margin-top: -6px;\n  padding: 0;\n  transform: translateX(-182px) translateY(-150px);\n}\n.vue-popover.dropdown-position-top {\n    transform: translateX(-197px) translateY(-225px);\n}\n.vue-popover a {\n    color: #fff;\n}\n.vue-popover ul {\n    margin: 0;\n    padding: 0.6em 0;\n    list-style: none;\n}\n.vue-popover li {\n    padding: 0.3em 0.7em;\n    line-height: 1.5;\n    margin: 0;\n    cursor: pointer;\n}\n.vue-popover li:hover {\n      background: #159FD2;\n}\n.vue-popover li.searchwp-excluded-types-note {\n      border-top: 1px solid rgba(255, 255, 255, 0.25);\n      cursor: default;\n      color: rgba(255, 255, 255, 0.55);\n      margin-top: 0.5em;\n      padding-top: 0.6em;\n}\n.vue-popover li.searchwp-excluded-types-note:hover {\n        background: transparent;\n}\n.vue-popover.dropdown-position-top:before {\n    border-top-color: #414141;\n}\n.vue-popover.dropdown-position-bottom {\n    margin-top: 6px;\n}\n.vue-popover.dropdown-position-bottom:before {\n      border-bottom-color: #414141;\n}\n.wp-core-ui .searchwp-dropdown-choices .searchwp-button {\n  margin: 0 0 0 1em;\n}\n.searchwp-engine-post-type__details .searchwp-dropdown {\n  padding-top: 1.25em;\n}\n.multiselect {\n  color: #444;\n}\n.multiselect .multiselect__single {\n    margin-bottom: 5px;\n}\n.multiselect .multiselect__input {\n    margin-bottom: 4px;\n    border: 0;\n    box-shadow: none;\n}\n.multiselect,\n.multiselect__tags {\n  min-height: 30px;\n}\n.multiselect__tags {\n  padding: 4px 40px 0 2px;\n  border-radius: 3px;\n}\n.multiselect__select {\n  height: 32px;\n  width: 30px;\n  padding: 4px;\n}\n.searchwp-engine .multiselect__content {\n  z-index: 9999 !important;\n}\n.multiselect__input,\n.multiselect__single {\n  line-height: 22px;\n  width: auto;\n  background-color: transparent;\n  font-size: 1em;\n}\n.multiselect__element {\n  margin: 0;\n}\n.multiselect__option {\n  padding: 8px;\n  line-height: 1.4;\n  min-height: 30px;\n  font-size: 13px;\n}\n.multiselect__option--selected:after,\n.multiselect__option--highlight:after {\n  display: none;\n}\n.multiselect__option--selected {\n  background: #fff;\n  font-weight: normal;\n  color: #444;\n}\n.multiselect__option--highlight,\n.multiselect__option--highlight.multiselect__option--selected {\n  background: #159FD2;\n}\n.multiselect__spinner {\n  height: 28px;\n}\n.multiselect__spinner:before, .multiselect__spinner:after {\n    border-color: #159FD2 transparent transparent;\n}\n.multiselect__tag {\n  background: #159FD2;\n  font-size: 13px;\n  padding: 4px 26px 4px 7px;\n  border-radius: 3px;\n  margin-top: 2px;\n  margin-right: 5px;\n  margin-bottom: 2px;\n}\n.multiselect__tag-icon {\n  line-height: 19px;\n  border-radius: 3px;\n}\n.multiselect__tag-icon:after {\n    color: #0c5975;\n    font-size: 15px;\n}\n.multiselect__tag-icon:focus,\n.multiselect__tag-icon:hover {\n  background: #159FD2;\n}\n.multiselect__content-wrapper {\n  box-shadow: 0 2px 3px 0 rgba(44, 44, 44, 0.2);\n}\n",""]);// exports
/***/},/* 86 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var render=function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("div",{staticClass:"searchwp-dropdown"},[!_vm.showing?_c("v-popover",{attrs:{offset:"6"}},[_c("searchwp-button",{attrs:{label:_vm.buttonText}}),_vm._v(" "),_c("template",{slot:"popover"},[_vm.type=="postTypes"?_c("ul",[_vm._l(_vm.getUnusedPostTypes(),function(unusedPostType){return _c("li",{directives:[{name:"close-popover",rawName:"v-close-popover"}],key:unusedPostType.name,on:{click:function click($event){_vm.addPostType(unusedPostType.name);}}},[_vm._v(_vm._s(unusedPostType.label))]);}),_vm._v(" "),_vm._l(_vm.excludedPostTypes,function(excludedPostType){return _c("li",{staticClass:"searchwp-excluded-types-note"},[_vm._v("\n                    "+_vm._s(_vm.i18n.excluded)+": "+_vm._s(excludedPostType.label)+"\n                ")]);})],2):_vm.type=="contentTypes"?_c("ul",[_vm.getUnusedNativeAttributes().length?_c("li",{directives:[{name:"close-popover",rawName:"v-close-popover"}],on:{click:function click($event){_vm.showDetails("native");}}},[_vm._v(_vm._s(_vm.i18n.nativeAttribute))]):_vm._e(),_vm._v(" "),_vm.getUnusedTaxonomies().length?_c("li",{directives:[{name:"close-popover",rawName:"v-close-popover"}],on:{click:function click($event){_vm.showDetails("taxonomy");}}},[_vm._v(_vm._s(_vm.i18n.taxonomy))]):_vm._e(),_vm._v(" "),"attachment"==_vm.postType&&_vm.isWithoutCustomField("searchwp_content")?_c("li",{directives:[{name:"close-popover",rawName:"v-close-popover"}],on:{click:function click($event){_vm.addMetakeyRecord("searchwp_content",true);}}},[_vm._v(_vm._s(_vm.i18n.documentContent))]):_vm._e(),_vm._v(" "),"attachment"==_vm.postType&&_vm.isWithoutCustomField("searchwp_pdf_metadata")?_c("li",{directives:[{name:"close-popover",rawName:"v-close-popover"}],on:{click:function click($event){_vm.addMetakeyRecord("searchwp_pdf_metadata",true);}}},[_vm._v(_vm._s(_vm.i18n.pdfMetadata))]):_vm._e(),_vm._v(" "),_vm.getUnusedMetakeys().length?_c("li",{directives:[{name:"close-popover",rawName:"v-close-popover"}],on:{click:function click($event){_vm.showDetails("meta");}}},[_vm._v(_vm._s(_vm.i18n.customField))]):_vm._e()]):_vm.type=="rules"?_c("ul",[_vm.getUnusedRuleTaxonomies("exclude").length?_c("li",{directives:[{name:"close-popover",rawName:"v-close-popover"}],on:{click:function click($event){_vm.showDetails("excludeTaxonomy");}}},[_vm._v(_vm._s(_vm.i18n.excludeByTaxonomy))]):_vm._e(),_vm._v(" "),_vm.getUnusedRuleTaxonomies("limit_to").length?_c("li",{directives:[{name:"close-popover",rawName:"v-close-popover"}],on:{click:function click($event){_vm.showDetails("limitToTaxonomy");}}},[_vm._v(_vm._s(_vm.i18n.limitByTaxonomy))]):_vm._e()]):_vm._e()])],2):_vm._e(),_vm._v(" "),_vm.showing?_c("div",{staticClass:"searchwp-dropdown-choices"},[_vm.showing=="native"?_c("multiselect",{attrs:{label:"label",placeholder:_vm.i18n.chooseNativeAttribute,options:_vm.getUnusedNativeAttributes(),searchable:false,"allow-empty":false,"reset-after":true},on:{select:_vm.addNativeContentType}}):_vm._e(),_vm._v(" "),_vm.showing=="taxonomy"?_c("multiselect",{attrs:{label:"label",placeholder:_vm.i18n.chooseTaxonomy,options:_vm.getUnusedTaxonomies(),searchable:false,"allow-empty":false,"reset-after":true},on:{select:_vm.addTaxonomy}}):_vm._e(),_vm._v(" "),_vm.showing=="meta"?_c("multiselect",{attrs:{label:"label",placeholder:_vm.i18n.chooseCustomField,options:_vm.getUnusedMetakeys(false,true),"group-values":"metakeys","group-label":"label","group-select":false,searchable:true,"allow-empty":true,"reset-after":true},on:{select:_vm.addMetakey}}):_vm._e(),_vm._v(" "),_vm.showing=="excludeTaxonomy"?_c("multiselect",{attrs:{label:"label",placeholder:_vm.i18n.chooseTaxonomy,options:_vm.getUnusedRuleTaxonomies("exclude"),searchable:false,"allow-empty":false,"reset-after":true},on:{select:_vm.addExcludedTaxonomy}}):_vm._e(),_vm._v(" "),_vm.showing=="limitToTaxonomy"?_c("multiselect",{attrs:{label:"label",placeholder:_vm.i18n.chooseTaxonomy,options:_vm.getUnusedRuleTaxonomies("limit_to"),searchable:false,"allow-empty":false,"reset-after":true},on:{select:_vm.addLimitedToTaxonomy}}):_vm._e(),_vm._v(" "),_c("searchwp-button",{attrs:{label:_vm.i18n.done},nativeOn:{click:function click($event){$event.preventDefault();_vm.showing="";}}})],1):_vm._e()],1);};var staticRenderFns=[];render._withStripped=true;var esExports={render:render,staticRenderFns:staticRenderFns/* harmony default export */};__webpack_exports__["a"]=esExports;if(false){module.hot.accept();if(module.hot.data){require("vue-hot-reload-api").rerender("data-v-ae7460f2",esExports);}}/***/},/* 87 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_InputTaxonomyTerms_vue__=__webpack_require__(23);/* unused harmony namespace reexport *//* harmony import */var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_a1884cfc_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_InputTaxonomyTerms_vue__=__webpack_require__(89);var disposed=false;var normalizeComponent=__webpack_require__(2);/* script *//* template *//* template functional */var __vue_template_functional__=false;/* styles */var __vue_styles__=null;/* scopeId */var __vue_scopeId__=null;/* moduleIdentifier (server only) */var __vue_module_identifier__=null;var Component=normalizeComponent(__WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_InputTaxonomyTerms_vue__["a"/* default */],__WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_a1884cfc_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_InputTaxonomyTerms_vue__["a"/* default */],__vue_template_functional__,__vue_styles__,__vue_scopeId__,__vue_module_identifier__);Component.options.__file="assets/js/src/components/InputTaxonomyTerms.vue";/* hot reload */if(false){(function(){var hotAPI=require("vue-hot-reload-api");hotAPI.install(require("vue"),false);if(!hotAPI.compatible)return;module.hot.accept();if(!module.hot.data){hotAPI.createRecord("data-v-a1884cfc",Component.options);}else{hotAPI.reload("data-v-a1884cfc",Component.options);}module.hot.dispose(function(data){disposed=true;});})();}/* harmony default export */__webpack_exports__["a"]=Component.exports;/***/},/* 88 *//***/function(module,exports){/**
 * Returns a function, that, as long as it continues to be invoked, will not
 * be triggered. The function will be called after it stops being called for
 * N milliseconds. If `immediate` is passed, trigger the function on the
 * leading edge, instead of the trailing. The function also has a property 'clear' 
 * that is a function which will clear the timer to prevent previously scheduled executions. 
 *
 * @source underscore.js
 * @see http://unscriptable.com/2009/03/20/debouncing-javascript-methods/
 * @param {Function} function to wrap
 * @param {Number} timeout in ms (`100`)
 * @param {Boolean} whether to execute at the beginning (`false`)
 * @api public
 */function debounce(func,wait,immediate){var timeout,args,context,timestamp,result;if(null==wait)wait=100;function later(){var last=Date.now()-timestamp;if(last<wait&&last>=0){timeout=setTimeout(later,wait-last);}else{timeout=null;if(!immediate){result=func.apply(context,args);context=args=null;}}};var debounced=function debounced(){context=this;args=arguments;timestamp=Date.now();var callNow=immediate&&!timeout;if(!timeout)timeout=setTimeout(later,wait);if(callNow){result=func.apply(context,args);context=args=null;}return result;};debounced.clear=function(){if(timeout){clearTimeout(timeout);timeout=null;}};debounced.flush=function(){if(timeout){result=func.apply(context,args);context=args=null;clearTimeout(timeout);timeout=null;}};return debounced;};// Adds compatibility for ES modules
debounce.debounce=debounce;module.exports=debounce;/***/},/* 89 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var render=function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("div",{staticClass:"searchwp-input-taxonomy-terms"},[_c("label",[_vm._v(_vm._s(_vm.label))]),_vm._v(" "),_c("div",[_c("multiselect",{attrs:{label:"label","track-by":"value",placeholder:_vm.i18n.findTerms,options:_vm.terms,multiple:true,searchable:true,loading:_vm.isLoading,"internal-search":false,"clear-on-select":true,"close-on-select":true,"max-height":300,"show-no-results":true,"hide-selected":true},on:{input:function input($event){_vm.updateTerms();},closed:function closed($event){_vm.closed=true;},"search-change":_vm.asyncFind},model:{value:_vm.selectedTerms,callback:function callback($$v){_vm.selectedTerms=$$v;},expression:"selectedTerms"}},[_c("span",{attrs:{slot:"noResult"},slot:"noResult"},[_vm._v(_vm._s(_vm.i18n.noTermsFound))])])],1)]);};var staticRenderFns=[];render._withStripped=true;var esExports={render:render,staticRenderFns:staticRenderFns/* harmony default export */};__webpack_exports__["a"]=esExports;if(false){module.hot.accept();if(module.hot.data){require("vue-hot-reload-api").rerender("data-v-a1884cfc",esExports);}}/***/},/* 90 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var render=function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("div",{staticClass:"searchwp-engine postbox"},[_c("h3",{staticClass:"hndle"},[!_vm.editingLabel?_c("span",{class:{"searchwp-hndle-default":!_vm.settings.hasOwnProperty("searchwp_engine_label")||_vm.isAdminEngine},on:{click:function click($event){_vm.settings.hasOwnProperty("searchwp_engine_label")&&!_vm.isAdminEngine?_vm.editingLabel=true:_vm.editingLabel=false;}}},[_vm._v(_vm._s(_vm.label)+"\n            "),_vm.settings.hasOwnProperty("searchwp_engine_label")&&!_vm.isAdminEngine?_c("span",{staticClass:"dashicons dashicons-edit"}):_vm._e()]):_vm._e(),_vm._v(" "),_vm.settings.hasOwnProperty("searchwp_engine_label")&&(_vm.editingLabel||!_vm.settings.searchwp_engine_label)?_c("input",{directives:[{name:"model",rawName:"v-model",value:_vm.label,expression:"label"}],domProps:{value:_vm.label},on:{input:[function($event){if($event.target.composing){return;}_vm.label=$event.target.value;},function($event){_vm.updateName();}],keyup:[function($event){if(!("button"in $event)&&_vm._k($event.keyCode,"enter",13,$event.key,"Enter")){return null;}_vm.editingLabel=false;},function($event){if(!("button"in $event)&&_vm._k($event.keyCode,"esc",27,$event.key,["Esc","Escape"])){return null;}_vm.editingLabel=false;}]}}):_vm._e(),_vm._v(" "),_vm.settings.hasOwnProperty("searchwp_engine_label")&&(_vm.editingLabel||!_vm.settings.searchwp_engine_label)?_c("span",{staticClass:"button",on:{click:function click($event){$event.preventDefault();_vm.editingLabel=!(_vm.editingLabel&&_vm.settings.searchwp_engine_label);}}},[_vm._v(_vm._s(_vm.i18n.done))]):_vm._e(),_vm._v(" "),_vm.settings.hasOwnProperty("searchwp_engine_label")?_c("code",[_vm._v(_vm._s(_vm.getName))]):_vm._e(),_vm._v(" "),_c("a",{attrs:{href:_vm.$root.misc.stats_url+"&tab="+_vm.getName}},[_vm._v(_vm._s(_vm.i18n.statistics))])]),_vm._v(" "),_c("div",{staticClass:"inside"},[!_vm.settings.hasOwnProperty("searchwp_engine_label")?_c("p",{staticClass:"description searchwp-engine-note"},[_c("span",{staticClass:"dashicons dashicons-info"}),_vm._v(_vm._s(_vm.i18n.defaultEngineNote))]):_vm.isAdminEngine?_c("p",{staticClass:"description searchwp-engine-note"},[_c("span",{staticClass:"dashicons dashicons-lightbulb"}),_vm._v(_vm._s(_vm.i18n.adminEngineNote))]):_c("p",{staticClass:"description searchwp-engine-note"},[_vm._v(_vm._s(_vm.i18n.engineNote))]),_vm._v(" "),!_vm.hasPostTypes?_c("p",{staticClass:"description searchwp-engine-note"},[_c("strong",[_vm._v(_vm._s(_vm.i18n.note))]),_vm._v(" "+_vm._s(_vm.i18n.engineNoteNone))]):_vm._e(),_vm._v(" "),_vm._l(_vm.settings,function(postType,postTypeName){return postTypeName!=="searchwp_engine_label"&&_vm.model.objects[postTypeName].enabled?_c("div",{key:postTypeName,staticClass:"searchwp-engine-post-type"},[_vm.attributes&&_vm.attributes[postTypeName]?_c("div",[_c("h4",{staticClass:"searchwp-engine-post-type__heading"},[_c("span",{staticClass:"searchwp-engine-post-type__label",on:{click:function click($event){_vm.toggleDetails(postTypeName);}}},[!_vm.details.includes(postTypeName)?_c("span",{staticClass:"dashicons dashicons-arrow-right"}):_vm._e(),_vm._v(" "),_vm.details.includes(postTypeName)?_c("span",{staticClass:"dashicons dashicons-arrow-down"}):_vm._e(),_vm._v("\n                        "+_vm._s(_vm.attributes[postTypeName].label)+"\n                    ")]),_vm._v(" "),_vm.details.includes(postTypeName)?_c("searchwp-remove",{attrs:{text:_vm.i18n.exclude},nativeOn:{click:function click($event){$event.preventDefault();_vm.removePostType(postTypeName);}}}):_vm._e()],1),_vm._v(" "),_vm.details.includes(postTypeName)?_c("div",{staticClass:"searchwp-engine-post-type__details"},[_c("div",{staticClass:"searchwp-engine-post-type__weights"},[_vm.hasAttributes(postTypeName)?_c("table",[_vm._m(0,true),_vm._v(" "),_c("thead",[_c("tr",[_c("th",[_vm._v(_vm._s(_vm.i18n.attribute))]),_vm._v(" "),_c("th",[_vm._v(_vm._s(_vm.i18n.weightMultiplier))])])]),_vm._v(" "),_vm.attributes[postTypeName].supports&&_vm.model.objects[postTypeName]?_c("tbody",[_vm._l(_vm.attributes[postTypeName].supports,function(supports,supportsKey){return _vm.model.objects[postTypeName].weights[supportsKey]?_c("tr",{key:_vm.attributes[postTypeName].name+supportsKey},[_c("td",[_vm._v("\n                                        "+_vm._s(supports)+"\n                                        "),_c("searchwp-remove",{attrs:{icon:true},nativeOn:{click:function click($event){$event.preventDefault();_vm.model.objects[postTypeName].weights[supportsKey]=0;}}})],1),_vm._v(" "),_c("td",[_c("searchwp-slider",{attrs:{height:3,"dot-size":11,min:1,max:_vm.weightMax,interval:1,formatter:_vm.getTooltipText,tooltip:"hover","bg-style":{backgroundColor:"#dddddd"},"process-style":{backgroundColor:"#159FD2"},"tooltip-style":{backgroundColor:"#159FD2",color:"#ffffff",fontSize:"13px"}},model:{value:_vm.model.objects[postTypeName].weights[supportsKey],callback:function callback($$v){_vm.$set(_vm.model.objects[postTypeName].weights,supportsKey,$$v);},expression:"model.objects[ postTypeName ].weights[ supportsKey ]"}})],1)]):_vm._e();}),_vm._v(" "),_vm.model.objects[postTypeName].weights.tax&&_vm.unusedTaxonomies(postTypeName).length!==Object.keys(_vm.model.objects[postTypeName].weights.tax).length?_c("tr",{staticClass:"searchwp-engine-post-type__attribute-category"},[_c("th",[_vm._v(_vm._s(_vm.i18n.taxonomies))])]):_vm._e(),_vm._v(" "),_vm._l(_vm.attributes[postTypeName].taxonomies,function(taxonomy,taxonomyKey){return _vm.model.objects[postTypeName].weights.tax[_vm.attributes[postTypeName].taxonomies[taxonomyKey].name]?_c("tr",{key:_vm.attributes[postTypeName].name+"tax"+taxonomyKey},[_c("td",[_vm._v("\n                                        "+_vm._s(_vm.attributes[postTypeName].taxonomies[taxonomyKey].label)+"\n                                        "),_c("searchwp-remove",{attrs:{icon:true},nativeOn:{click:function click($event){$event.preventDefault();_vm.removeTaxonomy(postTypeName,taxonomyKey);}}})],1),_vm._v(" "),_c("td",[_c("searchwp-slider",{attrs:{height:3,"dot-size":11,min:1,max:_vm.weightMax,interval:1,formatter:_vm.getTooltipText,tooltip:"hover","bg-style":{backgroundColor:"#dddddd"},"process-style":{backgroundColor:"#159FD2"},"tooltip-style":{backgroundColor:"#159FD2",color:"#ffffff",fontSize:"13px"}},model:{value:_vm.model.objects[postTypeName].weights.tax[_vm.attributes[postTypeName].taxonomies[taxonomyKey].name],callback:function callback($$v){_vm.$set(_vm.model.objects[postTypeName].weights.tax,_vm.attributes[postTypeName].taxonomies[taxonomyKey].name,$$v);},expression:"model.objects[ postTypeName ].weights.tax[ attributes[ postTypeName ].taxonomies[ taxonomyKey ].name ]"}})],1)]):_vm._e();}),_vm._v(" "),"attachment"==postTypeName&&_vm.hasDocumentProperties?_c("tr",{staticClass:"searchwp-engine-post-type__attribute-category"},[_c("th",[_vm._v(_vm._s(_vm.i18n.documentProperties))])]):_vm._e(),_vm._v(" "),_vm._l(_vm.model.objects[postTypeName].weights.cf,function(metakey,metakeyKey){return"searchwp_content"==_vm.model.objects[postTypeName].weights.cf[metakeyKey].metakey||"searchwp_pdf_metadata"==_vm.model.objects[postTypeName].weights.cf[metakeyKey].metakey?_c("tr",{key:_vm.attributes[postTypeName].name+"meta"+metakeyKey},[_c("td",["searchwp_content"==_vm.model.objects[postTypeName].weights.cf[metakeyKey].metakey?_c("span",[_vm._v(_vm._s(_vm.i18n.documentContent))]):"searchwp_pdf_metadata"==_vm.model.objects[postTypeName].weights.cf[metakeyKey].metakey?_c("span",[_vm._v(_vm._s(_vm.i18n.pdfMetadata))]):_vm._e(),_vm._v(" "),_c("searchwp-remove",{attrs:{icon:true},nativeOn:{click:function click($event){$event.preventDefault();_vm.removeMetakey(postTypeName,metakeyKey);}}})],1),_vm._v(" "),_c("td",[_c("searchwp-slider",{attrs:{height:3,"dot-size":11,min:1,max:_vm.weightMax,interval:1,formatter:_vm.getTooltipText,tooltip:"hover","bg-style":{backgroundColor:"#dddddd"},"process-style":{backgroundColor:"#159FD2"},"tooltip-style":{backgroundColor:"#159FD2",color:"#ffffff",fontSize:"13px"}},model:{value:_vm.model.objects[postTypeName].weights.cf[metakeyKey].weight,callback:function callback($$v){_vm.$set(_vm.model.objects[postTypeName].weights.cf[metakeyKey],"weight",$$v);},expression:"model.objects[ postTypeName ].weights.cf[ metakeyKey ].weight"}})],1)]):_vm._e();}),_vm._v(" "),_vm.hasCustomFields(postTypeName)?_c("tr",{staticClass:"searchwp-engine-post-type__attribute-category"},[_c("th",[_vm._v(_vm._s(_vm.i18n.customFields))])]):_vm._e(),_vm._v(" "),_vm._l(_vm.model.objects[postTypeName].weights.cf,function(metakey,metakeyKey){return!(postTypeName=="attachment"&&("searchwp_content"==_vm.model.objects[postTypeName].weights.cf[metakeyKey].metakey||"searchwp_pdf_metadata"==_vm.model.objects[postTypeName].weights.cf[metakeyKey].metakey))?_c("tr",{key:_vm.attributes[postTypeName].name+"meta"+metakeyKey},[_c("td",["searchwpcfdefault"===_vm.model.objects[postTypeName].weights.cf[metakeyKey].metakey||"searchwp cf default"===_vm.model.objects[postTypeName].weights.cf[metakeyKey].metakey?_c("span",[_c("em",[_vm._v(_vm._s(_vm.i18n.anyCustomField))]),_vm._v(" "),_c("span",{staticClass:"dashicons dashicons-star-filled"})]):_c("span",[_vm._v("\n                                            "+_vm._s(_vm.model.objects[postTypeName].weights.cf[metakeyKey].metakey)+"\n                                        ")]),_vm._v(" "),_c("searchwp-remove",{attrs:{icon:true},nativeOn:{click:function click($event){$event.preventDefault();_vm.removeMetakey(postTypeName,metakeyKey);}}})],1),_vm._v(" "),_c("td",[_c("searchwp-slider",{attrs:{height:3,"dot-size":11,min:1,max:_vm.weightMax,interval:1,formatter:_vm.getTooltipText,tooltip:"hover","bg-style":{backgroundColor:"#dddddd"},"process-style":{backgroundColor:"#159FD2"},"tooltip-style":{backgroundColor:"#159FD2",color:"#ffffff",fontSize:"13px"}},model:{value:_vm.model.objects[postTypeName].weights.cf[metakeyKey].weight,callback:function callback($$v){_vm.$set(_vm.model.objects[postTypeName].weights.cf[metakeyKey],"weight",$$v);},expression:"model.objects[ postTypeName ].weights.cf[ metakeyKey ].weight"}})],1)]):_vm._e();})],2):_vm._e()]):_c("searchwp-message",{attrs:{type:"warning"}},[_c("p",[_vm._v(_vm._s(_vm.i18n.withoutAttributesNoResults))])]),_vm._v(" "),_vm.unusedAttributes(postTypeName)?_c("searchwp-dropdown",{attrs:{type:"contentTypes",postType:postTypeName,buttonText:_vm.i18n.addAttribute}}):_vm._e(),_vm._v(" "),"attachment"==postTypeName&&(!_vm.$root.misc.ziparchive||!_vm.$root.misc.domdocument)?_c("div",{staticClass:"searchwp-document-notes"},[!_vm.$root.misc.ziparchive?_c("p",{staticClass:"description"},[_c("strong",[_vm._v(_vm._s(_vm.i18n.note))]),_vm._v(" "),_c("code",[_vm._v("ZipArchive")]),_vm._v(" "+_vm._s(_vm.i18n.notAvailableNoIndex))]):_vm._e(),_vm._v(" "),!_vm.$root.misc.domdocument?_c("p",{staticClass:"description"},[_c("strong",[_vm._v(_vm._s(_vm.i18n.note))]),_vm._v(" "),_c("code",[_vm._v("DOMDocument")]),_vm._v(" "+_vm._s(_vm.i18n.notAvailableNoIndex))]):_vm._e()]):_vm._e()],1),_vm._v(" "),_c("div",{staticClass:"searchwp-engine-post-type__options"},[_c("div",[_c("h5",[_vm._v(_vm._s(_vm.i18n.weightTransfer))]),_vm._v(" "),_vm.$root.objects[postTypeName].attribution=="id"?_c("searchwp-input-text",{attrs:{label:_vm.i18n.transferWeightTo,placeholder:_vm.i18n.singlePostId},model:{value:_vm.model.objects[postTypeName].options.attribute_to,callback:function callback($$v){_vm.$set(_vm.model.objects[postTypeName].options,"attribute_to",$$v);},expression:"model.objects[ postTypeName ].options.attribute_to"}}):_vm._e(),_vm._v(" "),_vm.$root.objects[postTypeName].attribution=="parent"?_c("searchwp-input-checkbox",{attrs:{label:_vm.i18n.transferWeightToParent,checked:_vm.model.objects[postTypeName].options.parent},model:{value:_vm.model.objects[postTypeName].options.parent,callback:function callback($$v){_vm.$set(_vm.model.objects[postTypeName].options,"parent",$$v);},expression:"model.objects[ postTypeName ].options.parent"}}):_vm._e()],1),_vm._v(" "),_vm.stemming_supported?_c("div",[_c("h5",[_vm._v(_vm._s(_vm.i18n.options))]),_vm._v(" "),_c("searchwp-input-checkbox",{attrs:{label:_vm.i18n.useKeywordStem,checked:_vm.model.objects[postTypeName].options.stem},model:{value:_vm.model.objects[postTypeName].options.stem,callback:function callback($$v){_vm.$set(_vm.model.objects[postTypeName].options,"stem",$$v);},expression:"model.objects[ postTypeName ].options.stem"}})],1):_vm._e(),_vm._v(" "),_c("div",[_c("h5",[_vm._v(_vm._s(_vm.i18n.rules))]),_vm._v(" "),_c("searchwp-input-text",{attrs:{label:_vm.i18n.excludedIds,placeholder:_vm.i18n.commaSeparatedIds},model:{value:_vm.model.objects[postTypeName].options.exclude,callback:function callback($$v){_vm.$set(_vm.model.objects[postTypeName].options,"exclude",$$v);},expression:"model.objects[ postTypeName ].options.exclude"}}),_vm._v(" "),"attachment"==postTypeName?_c("div",{staticClass:"searchwp-input-taxonomy-terms"},[_c("label",[_vm._v("Limit to file type")]),_vm._v(" "),_c("div",[_c("multiselect",{attrs:{label:"label","track-by":"value",placeholder:_vm.i18n.choose,options:_vm.$root.misc.mimes,multiple:true,searchable:false,"internal-search":false,"clear-on-select":true,"close-on-select":true,"max-height":300,"show-no-results":false,"hide-selected":true},model:{value:_vm.model.objects[postTypeName].options.mimes,callback:function callback($$v){_vm.$set(_vm.model.objects[postTypeName].options,"mimes",$$v);},expression:"model.objects[ postTypeName ].options.mimes"}})],1)]):_vm._e(),_vm._v(" "),_vm._l(_vm.attributes[postTypeName].taxonomies,function(taxonomy,taxonomyKey){return _vm.model.objects[postTypeName].options.hasOwnProperty("exclude_"+_vm.attributes[postTypeName].taxonomies[taxonomyKey].name)?_c("searchwp-input-taxonomy-terms",{key:_vm.attributes[postTypeName].name+"tax"+taxonomyKey,attrs:{label:_vm.i18n.excluded+" "+_vm.attributes[postTypeName].taxonomies[taxonomyKey].label,mode:"exclude",postType:postTypeName,taxonomy:_vm.attributes[postTypeName].taxonomies[taxonomyKey].name},on:{termsUpdated:_vm.excludedTermsUpdated}}):_vm._e();}),_vm._v(" "),_vm._l(_vm.attributes[postTypeName].taxonomies,function(taxonomy,taxonomyKey){return _vm.model.objects[postTypeName].options.hasOwnProperty("limit_to_"+_vm.attributes[postTypeName].taxonomies[taxonomyKey].name)?_c("searchwp-input-taxonomy-terms",{key:_vm.attributes[postTypeName].name+"tax"+taxonomyKey,attrs:{label:_vm.i18n.limitTo+" "+_vm.attributes[postTypeName].taxonomies[taxonomyKey].label,mode:"limit_to",postType:postTypeName,taxonomy:_vm.attributes[postTypeName].taxonomies[taxonomyKey].name},on:{termsUpdated:_vm.limitedToTermsUpdated}}):_vm._e();}),_vm._v(" "),_vm.attributes[postTypeName].taxonomies.length?_c("searchwp-dropdown",{attrs:{type:"rules",postType:postTypeName,buttonText:_vm.i18n.addLimitExcludeRule}}):_vm._e()],2)])]):_vm._e()]):_vm._e()]):_vm._e();}),_vm._v(" "),_c("ul",{staticClass:"searchwp-engine-actions"},[_c("li",[_vm.unusedPostTypes().length?_c("searchwp-dropdown",{attrs:{type:"postTypes",position:"below",buttonText:_vm.i18n.addPostType}}):_vm._e()],1),_vm._v(" "),_vm.model.name!=="default"&&!_vm.isAdminEngine?_c("li",{staticClass:"searchwp-remove-engine"},[_c("searchwp-remove",{attrs:{text:_vm.i18n.deleteEngine},nativeOn:{click:function click($event){$event.preventDefault();_vm.removeEngine();}}})],1):_vm._e()])],2)]);};var staticRenderFns=[function(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("colgroup",[_c("col",{staticClass:"searchwp-engine-post-type-attribute"}),_vm._v(" "),_c("col",{staticClass:"searchwp-engine-post-type-attribute-weight"})]);}];render._withStripped=true;var esExports={render:render,staticRenderFns:staticRenderFns/* harmony default export */};__webpack_exports__["a"]=esExports;if(false){module.hot.accept();if(module.hot.data){require("vue-hot-reload-api").rerender("data-v-76b67690",esExports);}}/***/},/* 91 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var render=function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("div",{class:[_vm.waiting?"searchwp-is-waiting":"","searchwp-engines"]},[_c("searchwp-environment-check"),_vm._v(" "),!_vm.initialSettingsSaved?_c("searchwp-message",{attrs:{type:"warning",moreInfo:"https://searchwp.com/?p=207"}},[_c("p",[_vm._v(_vm._s(_vm.i18n.initialSettingsNotice))])]):_vm.legacySettings?_c("searchwp-message",{attrs:{type:"warning",action:{target:_vm.dismissLegacySettings,text:_vm.i18n.dismiss}}},[_c("p",[_vm._v(_vm._s(_vm.i18n.legacySettingsNotice)+" "),_c("br"),_c("a",{attrs:{href:"https://searchwp.com/?p=104890",target:"_blank"}},[_vm._v(_vm._s(_vm.i18n.moreInfo)+" »")])])]):_vm._e(),_vm._v(" "),_vm._l(_vm.engines,function(engine,engineName){return _c("searchwp-engine",{key:engineName,attrs:{name:engineName,settings:engine,isAdminEngine:_vm.adminSearchEnabled&&_vm.adminSearchEngine==engineName}});}),_vm._v(" "),_vm.dirtyIndex?_c("searchwp-message",{attrs:{type:"warning",action:{target:_vm.resetIndex,text:_vm.i18n.rebuildIndex}}},[_c("p",[_vm._v(_vm._s(_vm.i18n.indexDirtyFromEnginesSave))])]):_vm._e(),_vm._v(" "),_c("ul",{staticClass:"searchwp-engines-actions"},[_c("li",{class:{"searchwp-loading":_vm.saving,"searchwp-saving":_vm.saving||_vm.doingSave}},[_c("a",{staticClass:"searchwp-button button button-primary",attrs:{href:"#"},on:{click:function click($event){$event.preventDefault();return _vm.saveEngines($event);}}},[_vm._v(_vm._s(_vm.i18n.saveEngines))])]),_vm._v(" "),!_vm.saving&&!_vm.doingSave&&!_vm.saved?_c("li",[_c("a",{staticClass:"searchwp-button button",attrs:{href:"#"},on:{click:function click($event){$event.preventDefault();return _vm.addEngine($event);}}},[_vm._v(_vm._s(_vm.i18n.addEngine))])]):_vm._e(),_vm._v(" "),_vm.saved?_c("li",{staticClass:"searchwp-success"},[_c("span",{staticClass:"searchwp-button button searchwp-button-message"},[_c("span",{staticClass:"dashicons dashicons-yes"}),_vm._v(" "),_c("span",[_vm._v(_vm._s(_vm.i18n.saved))])])]):_vm._e()])],2);};var staticRenderFns=[];render._withStripped=true;var esExports={render:render,staticRenderFns:staticRenderFns/* harmony default export */};__webpack_exports__["a"]=esExports;if(false){module.hot.accept();if(module.hot.data){require("vue-hot-reload-api").rerender("data-v-58c1192a",esExports);}}/***/},/* 92 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_IndexStats_vue__=__webpack_require__(24);/* unused harmony namespace reexport *//* harmony import */var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_1d0f863a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_IndexStats_vue__=__webpack_require__(108);var disposed=false;function injectStyle(ssrContext){if(disposed)return;__webpack_require__(93);}var normalizeComponent=__webpack_require__(2);/* script *//* template *//* template functional */var __vue_template_functional__=false;/* styles */var __vue_styles__=injectStyle;/* scopeId */var __vue_scopeId__=null;/* moduleIdentifier (server only) */var __vue_module_identifier__=null;var Component=normalizeComponent(__WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_IndexStats_vue__["a"/* default */],__WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_1d0f863a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_IndexStats_vue__["a"/* default */],__vue_template_functional__,__vue_styles__,__vue_scopeId__,__vue_module_identifier__);Component.options.__file="assets/js/src/components/IndexStats.vue";/* hot reload */if(false){(function(){var hotAPI=require("vue-hot-reload-api");hotAPI.install(require("vue"),false);if(!hotAPI.compatible)return;module.hot.accept();if(!module.hot.data){hotAPI.createRecord("data-v-1d0f863a",Component.options);}else{hotAPI.reload("data-v-1d0f863a",Component.options);}module.hot.dispose(function(data){disposed=true;});})();}/* harmony default export */__webpack_exports__["a"]=Component.exports;/***/},/* 93 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(94);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("1fb3ef4b",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-1d0f863a\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./IndexStats.vue",function(){var newContent=require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-1d0f863a\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./IndexStats.vue");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 94 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\n.searchwp-notice {\n  background: #fff;\n}\n.searchwp-index-progress dl {\n  margin: 0 0 0.2em;\n  padding: 0;\n  display: flex;\n  justify-content: space-between;\n  align-items: center;\n}\n.searchwp-index-progress dt {\n  margin: 0;\n  font-size: 14px;\n  padding: 8px 0;\n  color: #23282d;\n  font-weight: bold;\n  line-height: 1.4;\n}\n.searchwp-index-progress dd {\n  margin: 0;\n  padding: 0 0 0 1em;\n  line-height: 1;\n}\n.searchwp-index-progress + .searchwp-message {\n  margin-top: 1em;\n}\n.searchwp-index-stats table {\n  text-align: left;\n  width: 100%;\n  margin: 1em 0;\n}\n.searchwp-index-stats th {\n  text-align: left;\n  font-weight: bold;\n}\n.searchwp-index-stats th,\n.searchwp-index-stats td {\n  padding: 0.7em 0.4em 0.7em;\n  border-bottom: 1px solid #dfdfdf;\n  border-collapse: collapse;\n  line-height: 1;\n}\n.searchwp-index-stats th span,\n  .searchwp-index-stats td span {\n    padding: 3px 5px 2px;\n    margin: 0 1px;\n    background: rgba(0, 0, 0, 0.07);\n    font-size: 13px;\n    line-height: 1;\n    display: inline-block;\n}\n.searchwp-index-stats th {\n  padding-left: 0;\n}\n.searchwp-index-stats > p {\n  margin-bottom: 2em;\n}\n",""]);// exports
/***/},/* 95 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_ProgressBar_vue__=__webpack_require__(25);/* unused harmony namespace reexport *//* harmony import */var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_fb4f8fc0_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_ProgressBar_vue__=__webpack_require__(98);var disposed=false;function injectStyle(ssrContext){if(disposed)return;__webpack_require__(96);}var normalizeComponent=__webpack_require__(2);/* script *//* template *//* template functional */var __vue_template_functional__=false;/* styles */var __vue_styles__=injectStyle;/* scopeId */var __vue_scopeId__=null;/* moduleIdentifier (server only) */var __vue_module_identifier__=null;var Component=normalizeComponent(__WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_ProgressBar_vue__["a"/* default */],__WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_fb4f8fc0_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_ProgressBar_vue__["a"/* default */],__vue_template_functional__,__vue_styles__,__vue_scopeId__,__vue_module_identifier__);Component.options.__file="assets/js/src/components/ProgressBar.vue";/* hot reload */if(false){(function(){var hotAPI=require("vue-hot-reload-api");hotAPI.install(require("vue"),false);if(!hotAPI.compatible)return;module.hot.accept();if(!module.hot.data){hotAPI.createRecord("data-v-fb4f8fc0",Component.options);}else{hotAPI.reload("data-v-fb4f8fc0",Component.options);}module.hot.dispose(function(data){disposed=true;});})();}/* harmony default export */__webpack_exports__["a"]=Component.exports;/***/},/* 96 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(97);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("4b0b1791",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-fb4f8fc0\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ProgressBar.vue",function(){var newContent=require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-fb4f8fc0\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ProgressBar.vue");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 97 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\n.searchwp-progress-bar {\n  width: 100%;\n  position: relative;\n  height: 8px;\n  border-radius: 16px;\n  transition: width 1s ease-in-out;\n  background-color: #e4f7ff;\n  background-size: 30px 30px;\n  background-image: -webkit-gradient(linear, left top, right bottom, color-stop(0.25, rgba(21, 159, 210, 0.2)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(21, 159, 210, 0.2)), color-stop(0.75, rgba(21, 159, 210, 0.2)), color-stop(0.75, transparent), to(transparent));\n  background-image: -webkit-linear-gradient(135deg, rgba(21, 159, 210, 0.2) 25%, transparent 25%, transparent 50%, rgba(21, 159, 210, 0.2) 50%, rgba(21, 159, 210, 0.2) 75%, transparent 75%, transparent);\n  background-image: -moz-linear-gradient(135deg, rgba(21, 159, 210, 0.2) 25%, transparent 25%, transparent 50%, rgba(21, 159, 210, 0.2) 50%, rgba(21, 159, 210, 0.2) 75%, transparent 75%, transparent);\n  background-image: -ms-linear-gradient(135deg, rgba(21, 159, 210, 0.2) 25%, transparent 25%, transparent 50%, rgba(21, 159, 210, 0.2) 50%, rgba(21, 159, 210, 0.2) 75%, transparent 75%, transparent);\n  background-image: -o-linear-gradient(135deg, rgba(21, 159, 210, 0.2) 25%, transparent 25%, transparent 50%, rgba(21, 159, 210, 0.2) 50%, rgba(21, 159, 210, 0.2) 75%, transparent 75%, transparent);\n  background-image: linear-gradient(135deg, rgba(21, 159, 210, 0.2) 25%, transparent 25%, transparent 50%, rgba(21, 159, 210, 0.2) 50%, rgba(21, 159, 210, 0.2) 75%, transparent 75%, transparent);\n  -webkit-animation: animate-stripes 3s linear infinite;\n  -moz-animation: animate-stripes 3s linear infinite;\n}\n.searchwp-progress-bar.searchwp-progress-bar--is-disabled {\n    background: #fff;\n}\n.searchwp-progress-bar__progress {\n  height: 8px;\n  border-radius: 16px;\n  background: #159FD2;\n  transition: width 400ms linear;\n  position: absolute;\n  top: 0;\n  left: 0;\n}\n@-webkit-keyframes animate-stripes {\n0% {\n    background-position: 0 0;\n}\n100% {\n    background-position: 60px 0;\n}\n}\n@-moz-keyframes animate-stripes {\n0% {\n    background-position: 0 0;\n}\n100% {\n    background-position: 60px 0;\n}\n}\n",""]);// exports
/***/},/* 98 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var render=function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("div",{class:[_vm.disabled||_vm.alternateIndexer?"searchwp-progress-bar--is-disabled":"","searchwp-progress-bar"]},[_c("div",{class:[_vm.progress<100?"searchwp-progress-bar--is-active":"","searchwp-progress-bar__progress"],style:{width:_vm.progress+"%"}})]);};var staticRenderFns=[];render._withStripped=true;var esExports={render:render,staticRenderFns:staticRenderFns/* harmony default export */};__webpack_exports__["a"]=esExports;if(false){module.hot.accept();if(module.hot.data){require("vue-hot-reload-api").rerender("data-v-fb4f8fc0",esExports);}}/***/},/* 99 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Confirm_vue__=__webpack_require__(26);/* unused harmony namespace reexport *//* harmony import */var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_fc93678c_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Confirm_vue__=__webpack_require__(102);var disposed=false;function injectStyle(ssrContext){if(disposed)return;__webpack_require__(100);}var normalizeComponent=__webpack_require__(2);/* script *//* template *//* template functional */var __vue_template_functional__=false;/* styles */var __vue_styles__=injectStyle;/* scopeId */var __vue_scopeId__=null;/* moduleIdentifier (server only) */var __vue_module_identifier__=null;var Component=normalizeComponent(__WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Confirm_vue__["a"/* default */],__WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_fc93678c_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Confirm_vue__["a"/* default */],__vue_template_functional__,__vue_styles__,__vue_scopeId__,__vue_module_identifier__);Component.options.__file="assets/js/src/components/Confirm.vue";/* hot reload */if(false){(function(){var hotAPI=require("vue-hot-reload-api");hotAPI.install(require("vue"),false);if(!hotAPI.compatible)return;module.hot.accept();if(!module.hot.data){hotAPI.createRecord("data-v-fc93678c",Component.options);}else{hotAPI.reload("data-v-fc93678c",Component.options);}module.hot.dispose(function(data){disposed=true;});})();}/* harmony default export */__webpack_exports__["a"]=Component.exports;/***/},/* 100 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(101);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("c5799d6a",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-fc93678c\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Confirm.vue",function(){var newContent=require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-fc93678c\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Confirm.vue");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 101 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\n.searchwp-confirmation p {\n  text-align: center;\n  font-weight: bold;\n  padding: 0.5em 1.5em 0;\n  margin: 0 0 0.5em;\n}\n.searchwp-confirmation button {\n  display: block;\n  width: 100%;\n  margin: 0.5em 0 0;\n  padding: 0.3em 0.5em;\n  border: 0;\n  background: transparent;\n  color: #fff;\n  text-align: center;\n  cursor: pointer;\n}\n.searchwp-confirmation button:hover {\n    background: #159FD2;\n}\n",""]);// exports
/***/},/* 102 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var render=function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("v-popover",{attrs:{offset:"6",placement:_vm.placement}},[_c("searchwp-button",{attrs:{icon:_vm.icon,label:_vm.buttonLabel}}),_vm._v(" "),_c("template",{slot:"popover"},[_c("div",{staticClass:"searchwp-confirmation"},[_vm.question.length?_c("p",[_vm._v(_vm._s(_vm.question))]):_vm._e(),_vm._v(" "),_c("button",{directives:[{name:"close-popover",rawName:"v-close-popover"}],on:{click:_vm.confirmed}},[_vm._v(_vm._s(_vm.confirm))])])])],2);};var staticRenderFns=[];render._withStripped=true;var esExports={render:render,staticRenderFns:staticRenderFns/* harmony default export */};__webpack_exports__["a"]=esExports;if(false){module.hot.accept();if(module.hot.data){require("vue-hot-reload-api").rerender("data-v-fc93678c",esExports);}}/***/},/* 103 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";/* harmony import */var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Notice_vue__=__webpack_require__(27);/* unused harmony namespace reexport *//* harmony import */var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_2328264e_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Notice_vue__=__webpack_require__(106);var disposed=false;function injectStyle(ssrContext){if(disposed)return;__webpack_require__(104);}var normalizeComponent=__webpack_require__(2);/* script *//* template *//* template functional */var __vue_template_functional__=false;/* styles */var __vue_styles__=injectStyle;/* scopeId */var __vue_scopeId__=null;/* moduleIdentifier (server only) */var __vue_module_identifier__=null;var Component=normalizeComponent(__WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Notice_vue__["a"/* default */],__WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_2328264e_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Notice_vue__["a"/* default */],__vue_template_functional__,__vue_styles__,__vue_scopeId__,__vue_module_identifier__);Component.options.__file="assets/js/src/components/Notice.vue";/* hot reload */if(false){(function(){var hotAPI=require("vue-hot-reload-api");hotAPI.install(require("vue"),false);if(!hotAPI.compatible)return;module.hot.accept();if(!module.hot.data){hotAPI.createRecord("data-v-2328264e",Component.options);}else{hotAPI.reload("data-v-2328264e",Component.options);}module.hot.dispose(function(data){disposed=true;});})();}/* harmony default export */__webpack_exports__["a"]=Component.exports;/***/},/* 104 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(105);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(1)("34a8ff70",content,false,{});// Hot Module Replacement
if(false){// When the styles change, update the <style> tags
if(!content.locals){module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-2328264e\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Notice.vue",function(){var newContent=require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-2328264e\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Notice.vue");if(typeof newContent==='string')newContent=[[module.id,newContent,'']];update(newContent);});}// When the module is disposed, remove the <style> tags
module.hot.dispose(function(){update();});}/***/},/* 105 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(0)(false);// imports
// module
exports.push([module.i,"\n.searchwp-notice-persist {\n  margin: 10px 0 3px;\n}\n.searchwp-notice-persist.notice {\n    margin: 10px 0 3px;\n}\n.searchwp-notice-persist.is-dismissable {\n    position: relative;\n    padding-right: 38px;\n}\n.searchwp-notice-persist p {\n    display: flex;\n    align-items: center;\n    justify-content: space-between;\n}\n.searchwp-notice-persist span {\n    display: inline-block;\n    flex: 1;\n}\n.searchwp-notice-persist a, .searchwp-notice-persist button {\n    display: inline-block;\n    margin-left: 1em;\n}\n.searchwp-notice-persist .searchwp-notice-text a {\n    margin-left: 0;\n}\n.searchwp-notice-persist.notice-success {\n    background-color: #ebf8ec;\n}\n.searchwp-notice-persist.notice-error {\n    background-color: #ffefef;\n}\n.searchwp-notice-persist.notice-warning {\n    background-color: #fff8e7;\n}\n.searchwp-notice-persist .notice-dismiss:before {\n    line-height: 22px;\n}\n",""]);// exports
/***/},/* 106 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var render=function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("div",{class:["searchwp-notice-persist","notice","notice-"+_vm.type,_vm.dismissable?"is-dismissable":""]},[_c("p",[_c("span",{staticClass:"searchwp-notice-text"},[_vm._t("default")],2),_vm._v(" "),_vm.link&&_vm.link.length&&_vm.linkText&&_vm.linkText.length?_c("a",{attrs:{href:_vm.link}},[_vm._v(_vm._s(_vm.linkText))]):_vm._e(),_vm._v(" "),_vm.buttonText?_c("button",{staticClass:"button",on:{click:_vm.emitButtonClick}},[_vm._v(_vm._s(_vm.buttonText))]):_vm._e(),_vm._v(" "),_vm.dismissable?_c("button",{staticClass:"notice-dismiss",attrs:{type:"button"},on:{click:_vm.emitDismissed}},[_c("span",{staticClass:"screen-reader-text"},[_vm._v(_vm._s(_vm.i18n.dismiss))])]):_vm._e()])]);};var staticRenderFns=[];render._withStripped=true;var esExports={render:render,staticRenderFns:staticRenderFns/* harmony default export */};__webpack_exports__["a"]=esExports;if(false){module.hot.accept();if(module.hot.data){require("vue-hot-reload-api").rerender("data-v-2328264e",esExports);}}/***/},/* 107 *//***/function(module,exports,__webpack_require__){/*!
 * vue-simple-spinner v1.2.8 (https://github.com/dzwillia/vue-simple-spinner)
 * (c) 2017 David Z. Williams
 * Released under the MIT License.
 */(function webpackUniversalModuleDefinition(root,factory){if(true)module.exports=factory();else if(typeof define==='function'&&define.amd)define([],factory);else if((typeof exports==='undefined'?'undefined':_typeof2(exports))==='object')exports["VueSimpleSpinner"]=factory();else root["VueSimpleSpinner"]=factory();})(this,function(){return(/******/function(modules){// webpackBootstrap
/******/// The module cache
/******/var installedModules={};/******//******/// The require function
/******/function __webpack_require__(moduleId){/******//******/// Check if module is in cache
/******/if(installedModules[moduleId]){/******/return installedModules[moduleId].exports;/******/}/******/// Create a new module (and put it into the cache)
/******/var module=installedModules[moduleId]={/******/i:moduleId,/******/l:false,/******/exports:{}/******/};/******//******/// Execute the module function
/******/modules[moduleId].call(module.exports,module,module.exports,__webpack_require__);/******//******/// Flag the module as loaded
/******/module.l=true;/******//******/// Return the exports of the module
/******/return module.exports;/******/}/******//******//******/// expose the modules object (__webpack_modules__)
/******/__webpack_require__.m=modules;/******//******/// expose the module cache
/******/__webpack_require__.c=installedModules;/******//******/// define getter function for harmony exports
/******/__webpack_require__.d=function(exports,name,getter){/******/if(!__webpack_require__.o(exports,name)){/******/Object.defineProperty(exports,name,{/******/configurable:false,/******/enumerable:true,/******/get:getter/******/});/******/}/******/};/******//******/// getDefaultExport function for compatibility with non-harmony modules
/******/__webpack_require__.n=function(module){/******/var getter=module&&module.__esModule?/******/function getDefault(){return module['default'];}:/******/function getModuleExports(){return module;};/******/__webpack_require__.d(getter,'a',getter);/******/return getter;/******/};/******//******/// Object.prototype.hasOwnProperty.call
/******/__webpack_require__.o=function(object,property){return Object.prototype.hasOwnProperty.call(object,property);};/******//******/// __webpack_public_path__
/******/__webpack_require__.p="";/******//******/// Load entry module and return exports
/******/return __webpack_require__(__webpack_require__.s=0);/******/}(/************************************************************************//******/[/* 0 *//***/function(module,exports,__webpack_require__){"use strict";Object.defineProperty(exports,"__esModule",{value:true});exports.VueSimpleSpinner=undefined;var _Spinner=__webpack_require__(1);var _Spinner2=_interopRequireDefault(_Spinner);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}if(typeof window!=='undefined'&&window.Vue){Vue.component('vue-simple-spinner',_Spinner2.default);}exports.VueSimpleSpinner=_Spinner2.default;exports.default=_Spinner2.default;/***/},/* 1 *//***/function(module,exports,__webpack_require__){/* styles */__webpack_require__(2);var Component=__webpack_require__(7)(/* script */__webpack_require__(8),/* template */__webpack_require__(9),/* scopeId */null,/* cssModules */null);module.exports=Component.exports;/***/},/* 2 *//***/function(module,exports,__webpack_require__){// style-loader: Adds some css to the DOM by adding a <style> tag
// load the styles
var content=__webpack_require__(3);if(typeof content==='string')content=[[module.i,content,'']];if(content.locals)module.exports=content.locals;// add the styles to the DOM
var update=__webpack_require__(5)("d89557e4",content,true);/***/},/* 3 *//***/function(module,exports,__webpack_require__){exports=module.exports=__webpack_require__(4)();// imports
// module
exports.push([module.i,".vue-simple-spinner{transition:all .3s linear}@keyframes vue-simple-spinner-spin{0%{transform:rotate(0deg)}to{transform:rotate(1turn)}}",""]);// exports
/***/},/* 4 *//***/function(module,exports){/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/// css base code, injected by the css-loader
module.exports=function(){var list=[];// return the list of modules as css string
list.toString=function toString(){var result=[];for(var i=0;i<this.length;i++){var item=this[i];if(item[2]){result.push("@media "+item[2]+"{"+item[1]+"}");}else{result.push(item[1]);}}return result.join("");};// import a list of modules into the list
list.i=function(modules,mediaQuery){if(typeof modules==="string")modules=[[null,modules,""]];var alreadyImportedModules={};for(var i=0;i<this.length;i++){var id=this[i][0];if(typeof id==="number")alreadyImportedModules[id]=true;}for(i=0;i<modules.length;i++){var item=modules[i];// skip already imported module
// this implementation is not 100% perfect for weird media query combinations
//  when a module is imported multiple times with different media queries.
//  I hope this will never occur (Hey this way we have smaller bundles)
if(typeof item[0]!=="number"||!alreadyImportedModules[item[0]]){if(mediaQuery&&!item[2]){item[2]=mediaQuery;}else if(mediaQuery){item[2]="("+item[2]+") and ("+mediaQuery+")";}list.push(item);}}};return list;};/***/},/* 5 *//***/function(module,exports,__webpack_require__){/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
  Modified by Evan You @yyx990803
*/var hasDocument=typeof document!=='undefined';if(typeof DEBUG!=='undefined'&&DEBUG){if(!hasDocument){throw new Error('vue-style-loader cannot be used in a non-browser environment. '+"Use { target: 'node' } in your Webpack config to indicate a server-rendering environment.");}}var listToStyles=__webpack_require__(6);/*
type StyleObject = {
  id: number;
  parts: Array<StyleObjectPart>
}

type StyleObjectPart = {
  css: string;
  media: string;
  sourceMap: ?string
}
*/var stylesInDom={/*
  [id: number]: {
    id: number,
    refs: number,
    parts: Array<(obj?: StyleObjectPart) => void>
  }
*/};var head=hasDocument&&(document.head||document.getElementsByTagName('head')[0]);var singletonElement=null;var singletonCounter=0;var isProduction=false;var noop=function noop(){};// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
// tags it will allow on a page
var isOldIE=typeof navigator!=='undefined'&&/msie [6-9]\b/.test(navigator.userAgent.toLowerCase());module.exports=function(parentId,list,_isProduction){isProduction=_isProduction;var styles=listToStyles(parentId,list);addStylesToDom(styles);return function update(newList){var mayRemove=[];for(var i=0;i<styles.length;i++){var item=styles[i];var domStyle=stylesInDom[item.id];domStyle.refs--;mayRemove.push(domStyle);}if(newList){styles=listToStyles(parentId,newList);addStylesToDom(styles);}else{styles=[];}for(var i=0;i<mayRemove.length;i++){var domStyle=mayRemove[i];if(domStyle.refs===0){for(var j=0;j<domStyle.parts.length;j++){domStyle.parts[j]();}delete stylesInDom[domStyle.id];}}};};function addStylesToDom(styles/* Array<StyleObject> */){for(var i=0;i<styles.length;i++){var item=styles[i];var domStyle=stylesInDom[item.id];if(domStyle){domStyle.refs++;for(var j=0;j<domStyle.parts.length;j++){domStyle.parts[j](item.parts[j]);}for(;j<item.parts.length;j++){domStyle.parts.push(addStyle(item.parts[j]));}if(domStyle.parts.length>item.parts.length){domStyle.parts.length=item.parts.length;}}else{var parts=[];for(var j=0;j<item.parts.length;j++){parts.push(addStyle(item.parts[j]));}stylesInDom[item.id]={id:item.id,refs:1,parts:parts};}}}function createStyleElement(){var styleElement=document.createElement('style');styleElement.type='text/css';head.appendChild(styleElement);return styleElement;}function addStyle(obj/* StyleObjectPart */){var update,remove;var styleElement=document.querySelector('style[data-vue-ssr-id~="'+obj.id+'"]');if(styleElement){if(isProduction){// has SSR styles and in production mode.
// simply do nothing.
return noop;}else{// has SSR styles but in dev mode.
// for some reason Chrome can't handle source map in server-rendered
// style tags - source maps in <style> only works if the style tag is
// created and inserted dynamically. So we remove the server rendered
// styles and inject new ones.
styleElement.parentNode.removeChild(styleElement);}}if(isOldIE){// use singleton mode for IE9.
var styleIndex=singletonCounter++;styleElement=singletonElement||(singletonElement=createStyleElement());update=applyToSingletonTag.bind(null,styleElement,styleIndex,false);remove=applyToSingletonTag.bind(null,styleElement,styleIndex,true);}else{// use multi-style-tag mode in all other cases
styleElement=createStyleElement();update=applyToTag.bind(null,styleElement);remove=function remove(){styleElement.parentNode.removeChild(styleElement);};}update(obj);return function updateStyle(newObj/* StyleObjectPart */){if(newObj){if(newObj.css===obj.css&&newObj.media===obj.media&&newObj.sourceMap===obj.sourceMap){return;}update(obj=newObj);}else{remove();}};}var replaceText=function(){var textStore=[];return function(index,replacement){textStore[index]=replacement;return textStore.filter(Boolean).join('\n');};}();function applyToSingletonTag(styleElement,index,remove,obj){var css=remove?'':obj.css;if(styleElement.styleSheet){styleElement.styleSheet.cssText=replaceText(index,css);}else{var cssNode=document.createTextNode(css);var childNodes=styleElement.childNodes;if(childNodes[index])styleElement.removeChild(childNodes[index]);if(childNodes.length){styleElement.insertBefore(cssNode,childNodes[index]);}else{styleElement.appendChild(cssNode);}}}function applyToTag(styleElement,obj){var css=obj.css;var media=obj.media;var sourceMap=obj.sourceMap;if(media){styleElement.setAttribute('media',media);}if(sourceMap){// https://developer.chrome.com/devtools/docs/javascript-debugging
// this makes source maps inside style tags work properly in Chrome
css+='\n/*# sourceURL='+sourceMap.sources[0]+' */';// http://stackoverflow.com/a/26603875
css+='\n/*# sourceMappingURL=data:application/json;base64,'+btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap))))+' */';}if(styleElement.styleSheet){styleElement.styleSheet.cssText=css;}else{while(styleElement.firstChild){styleElement.removeChild(styleElement.firstChild);}styleElement.appendChild(document.createTextNode(css));}}/***/},/* 6 *//***/function(module,exports){/**
 * Translates the list format produced by css-loader into something
 * easier to manipulate.
 */module.exports=function listToStyles(parentId,list){var styles=[];var newStyles={};for(var i=0;i<list.length;i++){var item=list[i];var id=item[0];var css=item[1];var media=item[2];var sourceMap=item[3];var part={id:parentId+':'+i,css:css,media:media,sourceMap:sourceMap};if(!newStyles[id]){styles.push(newStyles[id]={id:id,parts:[part]});}else{newStyles[id].parts.push(part);}}return styles;};/***/},/* 7 *//***/function(module,exports){// this module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle
module.exports=function normalizeComponent(rawScriptExports,compiledTemplate,scopeId,cssModules){var esModule;var scriptExports=rawScriptExports=rawScriptExports||{};// ES6 modules interop
var type=_typeof2(rawScriptExports.default);if(type==='object'||type==='function'){esModule=rawScriptExports;scriptExports=rawScriptExports.default;}// Vue.extend constructor export interop
var options=typeof scriptExports==='function'?scriptExports.options:scriptExports;// render functions
if(compiledTemplate){options.render=compiledTemplate.render;options.staticRenderFns=compiledTemplate.staticRenderFns;}// scopedId
if(scopeId){options._scopeId=scopeId;}// inject cssModules
if(cssModules){var computed=Object.create(options.computed||null);Object.keys(cssModules).forEach(function(key){var module=cssModules[key];computed[key]=function(){return module;};});options.computed=computed;}return{esModule:esModule,exports:scriptExports,options:options};};/***/},/* 8 *//***/function(module,exports,__webpack_require__){"use strict";Object.defineProperty(exports,"__esModule",{value:true});var isNumber=function isNumber(n){return!isNaN(parseFloat(n))&&isFinite(n);};exports.default={name:'vue-simple-spinner',props:{'size':{default:32},'line-size':{type:Number,default:3},'line-bg-color':{type:String,default:'#eee'},'line-fg-color':{type:String,default:'#2196f3'},'speed':{type:Number,default:0.8},'spacing':{type:Number,default:4},'message':{type:String,default:''},'font-size':{type:Number,default:13},'text-fg-color':{type:String,default:'#555'}},computed:{size_px:function size_px(){switch(this.size){case'tiny':return 12;case'small':return 16;case'medium':return 32;case'large':return 48;case'big':return 64;case'huge':return 96;case'massive':return 128;}return isNumber(this.size)?this.size:32;},line_size_px:function line_size_px(){switch(this.size){case'tiny':return 1;case'small':return 2;case'medium':return 3;case'large':return 3;case'big':return 4;case'huge':return 4;case'massive':return 5;}return isNumber(this.lineSize)?this.lineSize:4;},text_margin_top:function text_margin_top(){switch(this.size){case'tiny':case'small':case'medium':case'large':case'big':case'huge':case'massive':return Math.min(Math.max(Math.ceil(this.size_px/8),3),12);}return isNumber(this.spacing)?this.spacing:4;},text_font_size:function text_font_size(){switch(this.size){case'tiny':case'small':case'medium':case'large':case'big':case'huge':case'massive':return Math.min(Math.max(Math.ceil(this.size_px*0.4),11),32);}return isNumber(this.fontSize)?this.fontSize:13;},spinner_style:function spinner_style(){return{'margin':'0 auto','border-radius':'100%','border':this.line_size_px+'px solid '+this.lineBgColor,'border-top':this.line_size_px+'px solid '+this.lineFgColor,'width':this.size_px+'px','height':this.size_px+'px','animation':'vue-simple-spinner-spin '+this.speed+'s linear infinite'};},text_style:function text_style(){return{'margin-top':this.text_margin_top+'px','color':this.textFgColor,'font-size':this.text_font_size+'px','text-align':'center'};}}};/***/},/* 9 *//***/function(module,exports){module.exports={render:function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',[_c('div',{staticClass:"vue-simple-spinner",style:_vm.spinner_style}),_vm._v(" "),_vm.message.length>0?_c('div',{staticClass:"vue-simple-spinner-text",style:_vm.text_style},[_vm._v(_vm._s(_vm.message))]):_vm._e()]);},staticRenderFns:[]/***/};}]/******/)["default"]);});/***/},/* 108 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var render=function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("div",{staticClass:"searchwp-index-stats"},[_c("div",{staticClass:"searchwp-index-progress"},[_c("dl",[_c("dt",[_vm._v(_vm._s(_vm.i18n.indexProgress))]),_vm._v(" "),_c("dd",[_vm._v(_vm._s(_vm.progress)+"%")])]),_vm._v(" "),_c("searchwp-progress-bar",{attrs:{progress:_vm.progress,disabled:!_vm.initialSettingsSaved||_vm.emptyEngines}})],1),_vm._v(" "),_vm.dirtyIndex||_vm.$root.misc.index_dirty?_c("searchwp-message",{attrs:{type:"warning",action:{target:_vm.resetIndex,text:_vm.i18n.rebuildIndex}}},[_c("p",[_vm._v(_vm._s(_vm.i18n.indexDirty))])]):_vm._e(),_vm._v(" "),_vm.waiting?_c("searchwp-message",[_c("p",[_vm._v(_vm._s(_vm.i18n.autoScale))])]):_vm._e(),_vm._v(" "),_c("table",[_c("tbody",[_c("tr",[_c("th",[_vm._v(_vm._s(_vm.i18n.lastActivity))]),_vm._v(" "),_c("td",[_vm._v(_vm._s(_vm.lastActivity))])]),_vm._v(" "),_c("tr",[_c("th",[_vm._v(_vm._s(_vm.i18n.indexed))]),_vm._v(" "),_c("td",[_c("span",{staticClass:"code"},[_vm._v(_vm._s(_vm.indexed))]),_vm._v(" "+_vm._s(_vm.i18n.entries))])]),_vm._v(" "),_c("tr",[_c("th",[_vm._v(_vm._s(_vm.i18n.unindexed))]),_vm._v(" "),_c("td",[_c("span",{staticClass:"code"},[_vm._v(_vm._s(_vm.unindexed))]),_vm._v(" "+_vm._s(_vm.i18n.entries))])]),_vm._v(" "),_c("tr",[_c("th",[_vm._v(_vm._s(_vm.i18n.mainRowCount))]),_vm._v(" "),_c("td",[_c("span",{staticClass:"code"},[_vm._v(_vm._s(_vm.mainRowCount))]),_vm._v(" "+_vm._s(_vm.i18n.rows))])])])]),_vm._v(" "),_c("p",{staticClass:"description"},[_vm._v(_vm._s(_vm.i18n.indexNote))]),_vm._v(" "),false?_c("ul",{staticClass:"searchwp-index-actions"},[_c("li",[_c("confirm",{attrs:{buttonLabel:"Reset Index",question:"Are you sure?",confirm:"Yes, reset index"},on:{confirmed:_vm.resetIndex}})],1)]):_vm._e()],1);};var staticRenderFns=[];render._withStripped=true;var esExports={render:render,staticRenderFns:staticRenderFns/* harmony default export */};__webpack_exports__["a"]=esExports;if(false){module.hot.accept();if(module.hot.data){require("vue-hot-reload-api").rerender("data-v-1d0f863a",esExports);}}/***/},/* 109 *//***/function(module,__webpack_exports__,__webpack_require__){"use strict";var render=function render(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("div",{staticClass:"searchwp-settings"},[_c("div",{staticClass:"searchwp-engines-index-stats metabox-holder"},[_c("searchwp-engines"),_vm._v(" "),_c("searchwp-index-stats")],1)]);};var staticRenderFns=[];render._withStripped=true;var esExports={render:render,staticRenderFns:staticRenderFns/* harmony default export */};__webpack_exports__["a"]=esExports;if(false){module.hot.accept();if(module.hot.data){require("vue-hot-reload-api").rerender("data-v-54517db7",esExports);}}/***/}]/******/);