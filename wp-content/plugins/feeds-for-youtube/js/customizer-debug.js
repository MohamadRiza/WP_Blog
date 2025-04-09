/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/@wordpress/hooks/build-module/createAddHook.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@wordpress/hooks/build-module/createAddHook.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _validateNamespace_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./validateNamespace.js */ "./node_modules/@wordpress/hooks/build-module/validateNamespace.js");
/* harmony import */ var _validateHookName_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./validateHookName.js */ "./node_modules/@wordpress/hooks/build-module/validateHookName.js");
/**
 * Internal dependencies
 */



/**
 * @callback AddHook
 *
 * Adds the hook to the appropriate hooks container.
 *
 * @param {string}               hookName      Name of hook to add
 * @param {string}               namespace     The unique namespace identifying the callback in the form `vendor/plugin/function`.
 * @param {import('.').Callback} callback      Function to call when the hook is run
 * @param {number}               [priority=10] Priority of this hook
 */

/**
 * Returns a function which, when invoked, will add a hook.
 *
 * @param {import('.').Hooks}    hooks    Hooks instance.
 * @param {import('.').StoreKey} storeKey
 *
 * @return {AddHook} Function that adds a new hook.
 */
function createAddHook(hooks, storeKey) {
  return function addHook(hookName, namespace, callback, priority = 10) {
    const hooksStore = hooks[storeKey];
    if (!(0,_validateHookName_js__WEBPACK_IMPORTED_MODULE_1__["default"])(hookName)) {
      return;
    }
    if (!(0,_validateNamespace_js__WEBPACK_IMPORTED_MODULE_0__["default"])(namespace)) {
      return;
    }
    if ('function' !== typeof callback) {
      // eslint-disable-next-line no-console
      console.error('The hook callback must be a function.');
      return;
    }

    // Validate numeric priority
    if ('number' !== typeof priority) {
      // eslint-disable-next-line no-console
      console.error('If specified, the hook priority must be a number.');
      return;
    }
    const handler = {
      callback,
      priority,
      namespace
    };
    if (hooksStore[hookName]) {
      // Find the correct insert index of the new hook.
      const handlers = hooksStore[hookName].handlers;

      /** @type {number} */
      let i;
      for (i = handlers.length; i > 0; i--) {
        if (priority >= handlers[i - 1].priority) {
          break;
        }
      }
      if (i === handlers.length) {
        // If append, operate via direct assignment.
        handlers[i] = handler;
      } else {
        // Otherwise, insert before index via splice.
        handlers.splice(i, 0, handler);
      }

      // We may also be currently executing this hook.  If the callback
      // we're adding would come after the current callback, there's no
      // problem; otherwise we need to increase the execution index of
      // any other runs by 1 to account for the added element.
      hooksStore.__current.forEach(hookInfo => {
        if (hookInfo.name === hookName && hookInfo.currentIndex >= i) {
          hookInfo.currentIndex++;
        }
      });
    } else {
      // This is the first hook of its type.
      hooksStore[hookName] = {
        handlers: [handler],
        runs: 0
      };
    }
    if (hookName !== 'hookAdded') {
      hooks.doAction('hookAdded', hookName, namespace, callback, priority);
    }
  };
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (createAddHook);
//# sourceMappingURL=createAddHook.js.map

/***/ }),

/***/ "./node_modules/@wordpress/hooks/build-module/createCurrentHook.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/hooks/build-module/createCurrentHook.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/**
 * Returns a function which, when invoked, will return the name of the
 * currently running hook, or `null` if no hook of the given type is currently
 * running.
 *
 * @param {import('.').Hooks}    hooks    Hooks instance.
 * @param {import('.').StoreKey} storeKey
 *
 * @return {() => string | null} Function that returns the current hook name or null.
 */
function createCurrentHook(hooks, storeKey) {
  return function currentHook() {
    var _hooksStore$__current;
    const hooksStore = hooks[storeKey];
    return (_hooksStore$__current = hooksStore.__current[hooksStore.__current.length - 1]?.name) !== null && _hooksStore$__current !== void 0 ? _hooksStore$__current : null;
  };
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (createCurrentHook);
//# sourceMappingURL=createCurrentHook.js.map

/***/ }),

/***/ "./node_modules/@wordpress/hooks/build-module/createDidHook.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@wordpress/hooks/build-module/createDidHook.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _validateHookName_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./validateHookName.js */ "./node_modules/@wordpress/hooks/build-module/validateHookName.js");
/**
 * Internal dependencies
 */


/**
 * @callback DidHook
 *
 * Returns the number of times an action has been fired.
 *
 * @param {string} hookName The hook name to check.
 *
 * @return {number | undefined} The number of times the hook has run.
 */

/**
 * Returns a function which, when invoked, will return the number of times a
 * hook has been called.
 *
 * @param {import('.').Hooks}    hooks    Hooks instance.
 * @param {import('.').StoreKey} storeKey
 *
 * @return {DidHook} Function that returns a hook's call count.
 */
function createDidHook(hooks, storeKey) {
  return function didHook(hookName) {
    const hooksStore = hooks[storeKey];
    if (!(0,_validateHookName_js__WEBPACK_IMPORTED_MODULE_0__["default"])(hookName)) {
      return;
    }
    return hooksStore[hookName] && hooksStore[hookName].runs ? hooksStore[hookName].runs : 0;
  };
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (createDidHook);
//# sourceMappingURL=createDidHook.js.map

/***/ }),

/***/ "./node_modules/@wordpress/hooks/build-module/createDoingHook.js":
/*!***********************************************************************!*\
  !*** ./node_modules/@wordpress/hooks/build-module/createDoingHook.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/**
 * @callback DoingHook
 * Returns whether a hook is currently being executed.
 *
 * @param {string} [hookName] The name of the hook to check for.  If
 *                            omitted, will check for any hook being executed.
 *
 * @return {boolean} Whether the hook is being executed.
 */

/**
 * Returns a function which, when invoked, will return whether a hook is
 * currently being executed.
 *
 * @param {import('.').Hooks}    hooks    Hooks instance.
 * @param {import('.').StoreKey} storeKey
 *
 * @return {DoingHook} Function that returns whether a hook is currently
 *                     being executed.
 */
function createDoingHook(hooks, storeKey) {
  return function doingHook(hookName) {
    const hooksStore = hooks[storeKey];

    // If the hookName was not passed, check for any current hook.
    if ('undefined' === typeof hookName) {
      return 'undefined' !== typeof hooksStore.__current[0];
    }

    // Return the __current hook.
    return hooksStore.__current[0] ? hookName === hooksStore.__current[0].name : false;
  };
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (createDoingHook);
//# sourceMappingURL=createDoingHook.js.map

/***/ }),

/***/ "./node_modules/@wordpress/hooks/build-module/createHasHook.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@wordpress/hooks/build-module/createHasHook.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/**
 * @callback HasHook
 *
 * Returns whether any handlers are attached for the given hookName and optional namespace.
 *
 * @param {string} hookName    The name of the hook to check for.
 * @param {string} [namespace] Optional. The unique namespace identifying the callback
 *                             in the form `vendor/plugin/function`.
 *
 * @return {boolean} Whether there are handlers that are attached to the given hook.
 */
/**
 * Returns a function which, when invoked, will return whether any handlers are
 * attached to a particular hook.
 *
 * @param {import('.').Hooks}    hooks    Hooks instance.
 * @param {import('.').StoreKey} storeKey
 *
 * @return {HasHook} Function that returns whether any handlers are
 *                   attached to a particular hook and optional namespace.
 */
function createHasHook(hooks, storeKey) {
  return function hasHook(hookName, namespace) {
    const hooksStore = hooks[storeKey];

    // Use the namespace if provided.
    if ('undefined' !== typeof namespace) {
      return hookName in hooksStore && hooksStore[hookName].handlers.some(hook => hook.namespace === namespace);
    }
    return hookName in hooksStore;
  };
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (createHasHook);
//# sourceMappingURL=createHasHook.js.map

/***/ }),

/***/ "./node_modules/@wordpress/hooks/build-module/createHooks.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@wordpress/hooks/build-module/createHooks.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   _Hooks: () => (/* binding */ _Hooks),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _createAddHook__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./createAddHook */ "./node_modules/@wordpress/hooks/build-module/createAddHook.js");
/* harmony import */ var _createRemoveHook__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./createRemoveHook */ "./node_modules/@wordpress/hooks/build-module/createRemoveHook.js");
/* harmony import */ var _createHasHook__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./createHasHook */ "./node_modules/@wordpress/hooks/build-module/createHasHook.js");
/* harmony import */ var _createRunHook__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./createRunHook */ "./node_modules/@wordpress/hooks/build-module/createRunHook.js");
/* harmony import */ var _createCurrentHook__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./createCurrentHook */ "./node_modules/@wordpress/hooks/build-module/createCurrentHook.js");
/* harmony import */ var _createDoingHook__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./createDoingHook */ "./node_modules/@wordpress/hooks/build-module/createDoingHook.js");
/* harmony import */ var _createDidHook__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./createDidHook */ "./node_modules/@wordpress/hooks/build-module/createDidHook.js");
/**
 * Internal dependencies
 */








/**
 * Internal class for constructing hooks. Use `createHooks()` function
 *
 * Note, it is necessary to expose this class to make its type public.
 *
 * @private
 */
class _Hooks {
  constructor() {
    /** @type {import('.').Store} actions */
    this.actions = Object.create(null);
    this.actions.__current = [];

    /** @type {import('.').Store} filters */
    this.filters = Object.create(null);
    this.filters.__current = [];
    this.addAction = (0,_createAddHook__WEBPACK_IMPORTED_MODULE_0__["default"])(this, 'actions');
    this.addFilter = (0,_createAddHook__WEBPACK_IMPORTED_MODULE_0__["default"])(this, 'filters');
    this.removeAction = (0,_createRemoveHook__WEBPACK_IMPORTED_MODULE_1__["default"])(this, 'actions');
    this.removeFilter = (0,_createRemoveHook__WEBPACK_IMPORTED_MODULE_1__["default"])(this, 'filters');
    this.hasAction = (0,_createHasHook__WEBPACK_IMPORTED_MODULE_2__["default"])(this, 'actions');
    this.hasFilter = (0,_createHasHook__WEBPACK_IMPORTED_MODULE_2__["default"])(this, 'filters');
    this.removeAllActions = (0,_createRemoveHook__WEBPACK_IMPORTED_MODULE_1__["default"])(this, 'actions', true);
    this.removeAllFilters = (0,_createRemoveHook__WEBPACK_IMPORTED_MODULE_1__["default"])(this, 'filters', true);
    this.doAction = (0,_createRunHook__WEBPACK_IMPORTED_MODULE_3__["default"])(this, 'actions');
    this.applyFilters = (0,_createRunHook__WEBPACK_IMPORTED_MODULE_3__["default"])(this, 'filters', true);
    this.currentAction = (0,_createCurrentHook__WEBPACK_IMPORTED_MODULE_4__["default"])(this, 'actions');
    this.currentFilter = (0,_createCurrentHook__WEBPACK_IMPORTED_MODULE_4__["default"])(this, 'filters');
    this.doingAction = (0,_createDoingHook__WEBPACK_IMPORTED_MODULE_5__["default"])(this, 'actions');
    this.doingFilter = (0,_createDoingHook__WEBPACK_IMPORTED_MODULE_5__["default"])(this, 'filters');
    this.didAction = (0,_createDidHook__WEBPACK_IMPORTED_MODULE_6__["default"])(this, 'actions');
    this.didFilter = (0,_createDidHook__WEBPACK_IMPORTED_MODULE_6__["default"])(this, 'filters');
  }
}

/** @typedef {_Hooks} Hooks */

/**
 * Returns an instance of the hooks object.
 *
 * @return {Hooks} A Hooks instance.
 */
function createHooks() {
  return new _Hooks();
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (createHooks);
//# sourceMappingURL=createHooks.js.map

/***/ }),

/***/ "./node_modules/@wordpress/hooks/build-module/createRemoveHook.js":
/*!************************************************************************!*\
  !*** ./node_modules/@wordpress/hooks/build-module/createRemoveHook.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _validateNamespace_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./validateNamespace.js */ "./node_modules/@wordpress/hooks/build-module/validateNamespace.js");
/* harmony import */ var _validateHookName_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./validateHookName.js */ "./node_modules/@wordpress/hooks/build-module/validateHookName.js");
/**
 * Internal dependencies
 */



/**
 * @callback RemoveHook
 * Removes the specified callback (or all callbacks) from the hook with a given hookName
 * and namespace.
 *
 * @param {string} hookName  The name of the hook to modify.
 * @param {string} namespace The unique namespace identifying the callback in the
 *                           form `vendor/plugin/function`.
 *
 * @return {number | undefined} The number of callbacks removed.
 */

/**
 * Returns a function which, when invoked, will remove a specified hook or all
 * hooks by the given name.
 *
 * @param {import('.').Hooks}    hooks             Hooks instance.
 * @param {import('.').StoreKey} storeKey
 * @param {boolean}              [removeAll=false] Whether to remove all callbacks for a hookName,
 *                                                 without regard to namespace. Used to create
 *                                                 `removeAll*` functions.
 *
 * @return {RemoveHook} Function that removes hooks.
 */
function createRemoveHook(hooks, storeKey, removeAll = false) {
  return function removeHook(hookName, namespace) {
    const hooksStore = hooks[storeKey];
    if (!(0,_validateHookName_js__WEBPACK_IMPORTED_MODULE_1__["default"])(hookName)) {
      return;
    }
    if (!removeAll && !(0,_validateNamespace_js__WEBPACK_IMPORTED_MODULE_0__["default"])(namespace)) {
      return;
    }

    // Bail if no hooks exist by this name.
    if (!hooksStore[hookName]) {
      return 0;
    }
    let handlersRemoved = 0;
    if (removeAll) {
      handlersRemoved = hooksStore[hookName].handlers.length;
      hooksStore[hookName] = {
        runs: hooksStore[hookName].runs,
        handlers: []
      };
    } else {
      // Try to find the specified callback to remove.
      const handlers = hooksStore[hookName].handlers;
      for (let i = handlers.length - 1; i >= 0; i--) {
        if (handlers[i].namespace === namespace) {
          handlers.splice(i, 1);
          handlersRemoved++;
          // This callback may also be part of a hook that is
          // currently executing.  If the callback we're removing
          // comes after the current callback, there's no problem;
          // otherwise we need to decrease the execution index of any
          // other runs by 1 to account for the removed element.
          hooksStore.__current.forEach(hookInfo => {
            if (hookInfo.name === hookName && hookInfo.currentIndex >= i) {
              hookInfo.currentIndex--;
            }
          });
        }
      }
    }
    if (hookName !== 'hookRemoved') {
      hooks.doAction('hookRemoved', hookName, namespace);
    }
    return handlersRemoved;
  };
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (createRemoveHook);
//# sourceMappingURL=createRemoveHook.js.map

/***/ }),

/***/ "./node_modules/@wordpress/hooks/build-module/createRunHook.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@wordpress/hooks/build-module/createRunHook.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/**
 * Returns a function which, when invoked, will execute all callbacks
 * registered to a hook of the specified type, optionally returning the final
 * value of the call chain.
 *
 * @param {import('.').Hooks}    hooks                  Hooks instance.
 * @param {import('.').StoreKey} storeKey
 * @param {boolean}              [returnFirstArg=false] Whether each hook callback is expected to
 *                                                      return its first argument.
 *
 * @return {(hookName:string, ...args: unknown[]) => undefined|unknown} Function that runs hook callbacks.
 */
function createRunHook(hooks, storeKey, returnFirstArg = false) {
  return function runHooks(hookName, ...args) {
    const hooksStore = hooks[storeKey];
    if (!hooksStore[hookName]) {
      hooksStore[hookName] = {
        handlers: [],
        runs: 0
      };
    }
    hooksStore[hookName].runs++;
    const handlers = hooksStore[hookName].handlers;

    // The following code is stripped from production builds.
    if (true) {
      // Handle any 'all' hooks registered.
      if ('hookAdded' !== hookName && hooksStore.all) {
        handlers.push(...hooksStore.all.handlers);
      }
    }
    if (!handlers || !handlers.length) {
      return returnFirstArg ? args[0] : undefined;
    }
    const hookInfo = {
      name: hookName,
      currentIndex: 0
    };
    hooksStore.__current.push(hookInfo);
    while (hookInfo.currentIndex < handlers.length) {
      const handler = handlers[hookInfo.currentIndex];
      const result = handler.callback.apply(null, args);
      if (returnFirstArg) {
        args[0] = result;
      }
      hookInfo.currentIndex++;
    }
    hooksStore.__current.pop();
    if (returnFirstArg) {
      return args[0];
    }
    return undefined;
  };
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (createRunHook);
//# sourceMappingURL=createRunHook.js.map

/***/ }),

/***/ "./node_modules/@wordpress/hooks/build-module/index.js":
/*!*************************************************************!*\
  !*** ./node_modules/@wordpress/hooks/build-module/index.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   actions: () => (/* binding */ actions),
/* harmony export */   addAction: () => (/* binding */ addAction),
/* harmony export */   addFilter: () => (/* binding */ addFilter),
/* harmony export */   applyFilters: () => (/* binding */ applyFilters),
/* harmony export */   createHooks: () => (/* reexport safe */ _createHooks__WEBPACK_IMPORTED_MODULE_0__["default"]),
/* harmony export */   currentAction: () => (/* binding */ currentAction),
/* harmony export */   currentFilter: () => (/* binding */ currentFilter),
/* harmony export */   defaultHooks: () => (/* binding */ defaultHooks),
/* harmony export */   didAction: () => (/* binding */ didAction),
/* harmony export */   didFilter: () => (/* binding */ didFilter),
/* harmony export */   doAction: () => (/* binding */ doAction),
/* harmony export */   doingAction: () => (/* binding */ doingAction),
/* harmony export */   doingFilter: () => (/* binding */ doingFilter),
/* harmony export */   filters: () => (/* binding */ filters),
/* harmony export */   hasAction: () => (/* binding */ hasAction),
/* harmony export */   hasFilter: () => (/* binding */ hasFilter),
/* harmony export */   removeAction: () => (/* binding */ removeAction),
/* harmony export */   removeAllActions: () => (/* binding */ removeAllActions),
/* harmony export */   removeAllFilters: () => (/* binding */ removeAllFilters),
/* harmony export */   removeFilter: () => (/* binding */ removeFilter)
/* harmony export */ });
/* harmony import */ var _createHooks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./createHooks */ "./node_modules/@wordpress/hooks/build-module/createHooks.js");
/**
 * Internal dependencies
 */


/** @typedef {(...args: any[])=>any} Callback */

/**
 * @typedef Handler
 * @property {Callback} callback  The callback
 * @property {string}   namespace The namespace
 * @property {number}   priority  The namespace
 */

/**
 * @typedef Hook
 * @property {Handler[]} handlers Array of handlers
 * @property {number}    runs     Run counter
 */

/**
 * @typedef Current
 * @property {string} name         Hook name
 * @property {number} currentIndex The index
 */

/**
 * @typedef {Record<string, Hook> & {__current: Current[]}} Store
 */

/**
 * @typedef {'actions' | 'filters'} StoreKey
 */

/**
 * @typedef {import('./createHooks').Hooks} Hooks
 */

const defaultHooks = (0,_createHooks__WEBPACK_IMPORTED_MODULE_0__["default"])();
const {
  addAction,
  addFilter,
  removeAction,
  removeFilter,
  hasAction,
  hasFilter,
  removeAllActions,
  removeAllFilters,
  doAction,
  applyFilters,
  currentAction,
  currentFilter,
  doingAction,
  doingFilter,
  didAction,
  didFilter,
  actions,
  filters
} = defaultHooks;

//# sourceMappingURL=index.js.map

/***/ }),

/***/ "./node_modules/@wordpress/hooks/build-module/validateHookName.js":
/*!************************************************************************!*\
  !*** ./node_modules/@wordpress/hooks/build-module/validateHookName.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/**
 * Validate a hookName string.
 *
 * @param {string} hookName The hook name to validate. Should be a non empty string containing
 *                          only numbers, letters, dashes, periods and underscores. Also,
 *                          the hook name cannot begin with `__`.
 *
 * @return {boolean} Whether the hook name is valid.
 */
function validateHookName(hookName) {
  if ('string' !== typeof hookName || '' === hookName) {
    // eslint-disable-next-line no-console
    console.error('The hook name must be a non-empty string.');
    return false;
  }
  if (/^__/.test(hookName)) {
    // eslint-disable-next-line no-console
    console.error('The hook name cannot begin with `__`.');
    return false;
  }
  if (!/^[a-zA-Z][a-zA-Z0-9_.-]*$/.test(hookName)) {
    // eslint-disable-next-line no-console
    console.error('The hook name can only contain numbers, letters, dashes, periods and underscores.');
    return false;
  }
  return true;
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (validateHookName);
//# sourceMappingURL=validateHookName.js.map

/***/ }),

/***/ "./node_modules/@wordpress/hooks/build-module/validateNamespace.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/hooks/build-module/validateNamespace.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/**
 * Validate a namespace string.
 *
 * @param {string} namespace The namespace to validate - should take the form
 *                           `vendor/plugin/function`.
 *
 * @return {boolean} Whether the namespace is valid.
 */
function validateNamespace(namespace) {
  if ('string' !== typeof namespace || '' === namespace) {
    // eslint-disable-next-line no-console
    console.error('The namespace must be a non-empty string.');
    return false;
  }
  if (!/^[a-zA-Z][a-zA-Z0-9_.\-\/]*$/.test(namespace)) {
    // eslint-disable-next-line no-console
    console.error('The namespace can only contain numbers, letters, dashes, periods, underscores and slashes.');
    return false;
  }
  return true;
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (validateNamespace);
//# sourceMappingURL=validateNamespace.js.map

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
/*!**************************!*\
  !*** ./js/customizer.js ***!
  \**************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/hooks */ "./node_modules/@wordpress/hooks/build-module/index.js");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }

SB_Customizer.initPromise = new Promise(function (resolve) {
  SB_Customizer.extraData = _objectSpread(_objectSpread({}, SB_Customizer.extraData), {}, {
    allFeedsScreen: sbc_builder.allFeedsScreen,
    feedsList: sbc_builder.feeds,
    legacyFeedsList: sbc_builder.legacyFeeds,
    tooltipContent: sbc_builder.feedtypesTooltipContent,
    feedSettingsDomOptions: null,
    selectedFeedModel: {
      channel: sbc_builder.prefilledChannelId,
      playlist: '',
      favorites: sbc_builder.prefilledChannelId,
      search: '',
      live: sbc_builder.prefilledChannelId,
      single: '',
      apiKey: '',
      accessToken: ''
    },
    youtubeAccountConnectURL: sbc_builder.youtubeAccountConnectURL,
    connectSiteParameters: sbc_builder.youtubeAccountConnectParameters,
    prefilledChannelId: sbc_builder.prefilledChannelId,
    dismissLite: sbc_builder.youtube_feed_dismiss_lite,
    shouldShowFeedAPIForm: false,
    shouldShowManualConnect: false,
    showShowYTAccountWarning: false,
    sw_feed: false,
    sw_feed_id: false
  });
  SB_Customizer.extraMethods = _objectSpread(_objectSpread({}, SB_Customizer.extraMethods), {}, {
    /**
     * Change Settings Value
     *
     * @since 2.0
     */
    changeSettingValue: function changeSettingValue(settingID, value) {
      var doProcess = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
      var ajaxAction = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
      var self = this;
      if (doProcess) {
        self.customizerFeedData.settings[settingID] = value;
      }
      if (ajaxAction !== false) {
        self.customizerControlAjaxAction(ajaxAction, settingID);
      }
      self.regenerateLayout(settingID);
    },
    checkExtensionActive: function checkExtensionActive(extension) {
      var self = this;
      return self.activeExtensions[extension];
    },
    /**
     * Should show overlay for the sidebar elements on top
     * 
     * @since 2.0
     */
    shouldShowOverlay: function shouldShowOverlay(control) {
      var self = this;
      if (!self.sbyIsPro || self.sbyLicenseNoticeActive || (control.checkExtensionPopup == 'call_to_action' || control.checkExtensionPopup == 'advancedFilters') && (!self.hasFeature('call_to_actions') || !self.hasFeature('advancedFilters'))) {
        return control.checkExtensionPopup != undefined || (control.condition != undefined || control.checkExtension != undefined || control.checkExtensionDimmed != undefined ? !self.checkControlCondition(control.condition, control.checkExtension, control.checkExtensionDimmed) : false);
      } else {
        return control.condition != undefined || control.checkExtension != undefined || control.checkExtensionDimmed != undefined ? !self.checkControlCondition(control.condition, control.checkExtension, control.checkExtensionDimmed) : false;
      }
    },
    /**
     * Should show toggleset type cover
     * 
     * @since 2.0
     */
    shouldShowTogglesetCover: function shouldShowTogglesetCover(toggle) {
      var self = this;
      if (!self.sbyIsPro || self.sbyLicenseNoticeActive) {
        return toggle.checkExtension != undefined && !self.checkExtensionActive(toggle.checkExtension);
      } else {
        return false;
      }
    },
    /**
     * Open extension popup from toggleset cover
     * 
     * @since 2.0
     */
    togglesetExtPopup: function togglesetExtPopup(toggle) {
      var self = this;
      self.viewsActive.extensionsPopupElement = toggle.checkExtension;
    },
    /**
     * Shortcode Global Layout Settings
     *
     * @since 2.0
     */
    regenerateLayout: function regenerateLayout(settingID) {
      var self = this,
        regenerateFeedHTML = ['layout'],
        relayoutFeed = ['layout', 'carouselarrows', 'carouselpag', 'carouselautoplay', 'carouseltime', 'carouselloop', 'carouselrows', 'cols', 'colstablet', 'colsmobile', 'imagepadding'];
      if (relayoutFeed.includes(settingID)) {
        setTimeout(function () {
          self.setShortcodeGlobalSettings(true);
        }, 200);
      }
    },
    /**
     * Back to all feeds
     *
     * @since 2.0
     */
    backToAllFeeds: function backToAllFeeds() {
      var self = this;
      if (JSON.stringify(self.customizerFeedDataInitial) === JSON.stringify(self.customizerFeedData)) {
        window.location = self.builderUrl;
      } else {
        self.openDialogBox('backAllToFeed');
      }
    },
    /**
     * Open Dialog Box
     *
     * @since 2.0
     */
    openDialogBox: function openDialogBox(type) {
      var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
      var self = this,
        heading = self.dialogBoxPopupScreen[type].heading,
        description = self.dialogBoxPopupScreen[type].description,
        customButtons = self.dialogBoxPopupScreen[type].customButtons;
      switch (type) {
        case "deleteSingleFeed":
          self.feedToDelete = args;
          heading = heading.replace("#", self.feedToDelete.feed_name);
          break;
      }
      self.dialogBox = {
        active: true,
        type: type,
        heading: heading,
        description: description,
        customButtons: customButtons
      };
      window.event.stopPropagation();
    },
    /**
     * Confirm Dialog Box Actions
     *
     * @since 2.0
     */
    confirmDialogAction: function confirmDialogAction() {
      var self = this;
      switch (self.dialogBox.type) {
        case 'deleteSingleFeed':
          self.feedActionDelete([self.feedToDelete.id]);
          break;
        case 'deleteMultipleFeeds':
          self.feedActionDelete(self.feedsSelected);
          break;
        case 'backAllToFeed':
          window.location = self.builderUrl;
          break;
      }
    },
    /**
     * Delete Feed
     *
     * @since 2.0
     */
    feedActionDelete: function feedActionDelete(feeds_ids) {
      var self = this,
        feedsDeleteData = {
          action: 'sby_feed_saver_manager_delete_feeds',
          feeds_ids: feeds_ids
        };
      self.ajaxPost(feedsDeleteData, function (_ref) {
        var data = _ref.data;
        self.feedsList = Object.values(Object.assign({}, data));
        self.feedsSelected = [];
      });
    },
    /**
     * Enable & Show Color Picker
     *
     * @since 2.0
     */
    showColorPickerPospup: function showColorPickerPospup(controlId) {
      this.customizerScreens.activeColorPicker = controlId;
    },
    /**
     * Hide Color Picker
     *
     * @since 2.0
     */
    hideColorPickerPopup: function hideColorPickerPopup() {
      this.customizerScreens.activeColorPicker = null;
    },
    /**
     * Get Feed Preview Global CSS Class
     *
     * @since 2.0
     * @return String
     */
    getPaletteClass: function getPaletteClass() {
      var context = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
      var self = this,
        colorPalette = self.customizerFeedData.settings.colorpalette;
      if (self.checkNotEmpty(colorPalette)) {
        var feedID = colorPalette === 'custom' ? '_' + self.customizerFeedData.feed_info.id : '';
        console.log(colorPalette !== 'inherit' ? ' sby' + context + '_palette_' + colorPalette + feedID : '');
        return colorPalette !== 'inherit' ? ' sby' + context + '_palette_' + colorPalette + feedID : '';
      }
      return '';
    },
    /**
     * Check if Value is Empty
     *
     * @since 2.0
     *
     * @return boolean
     */
    checkNotEmpty: function checkNotEmpty(value) {
      return value != null && value.replace(/ /gi, '') != '';
    },
    /**
     * Get feed container class
     *
     * @since 2.0
     *
     * @returns string
     */
    getFeedContainerClasses: function getFeedContainerClasses() {
      var self = this;
      var classes = ['sb_youtube', 'sby_layout_' + self.customizerFeedData.settings.layout, 'sby_col_' + self.getColSettings(), 'sby_mob_col_' + self.getMobColSettings(), 'sby_palette_' + self.getColorPaletteClass()];
      return classes.join(' ');
    },
    getColorPaletteClass: function getColorPaletteClass() {
      var self = this;
      if (self.customizerFeedData.settings.colorpalette == 'custom') {
        return self.customizerFeedData.settings.colorpalette + '_' + self.customizerFeedData.feed_info.id;
      } else {
        return self.customizerFeedData.settings.colorpalette;
      }
    },
    /**
     * Get Col Settings
     *
     * @since 2.0
     */
    getColSettings: function getColSettings() {
      var self = this;
      if (self.customizerFeedData.settings['layout'] == 'list' || self.customizerScreens.previewScreen === 'mobile') {
        return 0;
      }
      if (self.customizerFeedData.settings['cols']) {
        return self.customizerFeedData.settings['cols'];
      }
      return 0;
    },
    /**
     * Get Mob Col Settings
     *
     * @since 2.0
     */
    getMobColSettings: function getMobColSettings() {
      var self = this;
      if (self.customizerFeedData.settings['layout'] == 'list') {
        return 0;
      }
      if (self.customizerFeedData.settings['colsmobile']) {
        return self.customizerFeedData.settings['colsmobile'];
      }
      return 0;
    },
    /**
     * Check if header subscribers needs to show
     *
     * @since 2.0
     */
    checkShouldShowSubscribers: function checkShouldShowSubscribers() {
      return this.customizerFeedData.settings.showsubscribe == true ? "shown" : '';
    },
    /**
     * Check if Data Setting is Enabled
     *
     * @since 2.0
     *
     * @return boolean
     */
    valueIsEnabled: function valueIsEnabled(value) {
      return value == 1 || value == true || value == 'true' || value == 'on';
    },
    //Change Switcher Settings
    changeSwitcherSettingValue: function changeSwitcherSettingValue(settingID, onValue, offValue) {
      var ajaxAction = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
      var extension = arguments.length > 4 ? arguments[4] : undefined;
      var self = this;
      console.log(extension);
      if (Object.keys(self.inActiveExtensions).includes(settingID)) {
        self.viewsActive.extensionsPopupElement = self.inActiveExtensions[settingID];
      }
      self.customizerFeedData.settings[settingID] = self.customizerFeedData.settings[settingID] == onValue ? offValue : onValue;
      if (ajaxAction !== false) {
        self.customizerControlAjaxAction(ajaxAction);
      }
      self.regenerateLayout(settingID);
    },
    /**
     * Parse JSON
     *
     * @since 2.0
     *
     * @return jsonObject / Boolean
     */
    jsonParse: function jsonParse(jsonString) {
      try {
        return JSON.parse(jsonString);
      } catch (e) {
        return false;
      }
    },
    /**
     * Get custom header text
     *
     * @since 2.0
     */
    getCustomHeaderText: function getCustomHeaderText() {
      return this.customizerFeedData.settings.customheadertext;
    },
    /**
     * Should show the standard header
     *
     * @since 2.0
     */
    shouldShowStandardHeader: function shouldShowStandardHeader() {
      var self = this;
      return self.customizerFeedData.settings.showheader && self.customizerFeedData.settings.headerstyle === 'standard';
    },
    /**
     * Should show the text style header
     *
     * @since 2.0
     */
    shouldShowTextHeader: function shouldShowTextHeader() {
      var self = this;
      return self.customizerFeedData.settings.showheader && self.customizerFeedData.settings.headerstyle === 'text';
    },
    /**
     * Get flags attributes
     *
     * @since 2.0
     */
    getFlagsAttr: function getFlagsAttr() {
      var self = this,
        flags = [];
      if (self.customizerFeedData.settings['disable_resize']) {
        flags.push('resizeDisable');
      }
      if (self.customizerFeedData.settings['favor_local']) {
        flags.push('favorLocal');
      }
      if (self.customizerFeedData.settings['disable_js_image_loading']) {
        flags.push('imageLoadDisable');
      }
      if (self.customizerFeedData.settings['ajax_post_load']) {
        flags.push('ajaxPostLoad');
      }
      if (self.customizerFeedData.settings['playerratio'] === '3:4') {
        flags.push('narrowPlayer');
      }
      if (self.customizerFeedData.settings['disablecdn']) {
        flags.push('disablecdn');
      }
      return flags.toString();
    },
    /**
     * Should show gallery layout player
     *
     * @since 2.0
     */
    shouldShowPlayer: function shouldShowPlayer() {
      var self = this;
      if (self.customizerFeedData.settings.layout != 'gallery') {
        return;
      }
      return true;
    },
    /**
     * Switch to Videos sections
     * From Feed Layout section bottom link
     *
     * @since 2.0
     */
    switchToVideosSection: function switchToVideosSection() {
      var self = this;
      self.customizerScreens.parentActiveSection = null;
      self.customizerScreens.parentActiveSectionData = null;
      self.customizerScreens.activeSection = 'customize_videos';
      self.customizerScreens.activeSectionData = self.customizerSidebarBuilder.customize.sections.customize_videos;
    },
    /**
     * Shortcode Global Layout Settings
     *
     * @since 2.0
     */
    setShortcodeGlobalSettings: function setShortcodeGlobalSettings() {
      var flyPreview = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      var self = this,
        youtubeFeed = jQuery("html").find(".sb_youtube"),
        feedSettings = self.jsonParse(youtubeFeed.attr('data-options')),
        customizerSettings = self.customizerFeedData.settings;
      if (!youtubeFeed.length) {
        return;
      }
      if (customizerSettings.layout === 'carousel') {
        var arrows = self.valueIsEnabled(customizerSettings['carouselarrows']),
          pag = self.valueIsEnabled(customizerSettings['carouselpag']),
          autoplay = self.valueIsEnabled(customizerSettings['carouselautoplay']),
          time = autoplay ? parseInt(customizerSettings['carouseltime']) : false,
          loop = self.checkNotEmpty(customizerSettings['carouselloop']) && customizerSettings['carouselloop'] !== 'rewind' ? false : true,
          rows = customizerSettings['carouselrows'] ? Math.min(parseInt(customizerSettings['carouselrows']), 2) : 1;
        delete feedSettings['gallery'];
        delete feedSettings['masonry'];
        delete feedSettings['grid'];
        feedSettings['carousel'] = [arrows, pag, autoplay, time, loop, rows];
      } else if (customizerSettings.layout == 'grid') {
        delete feedSettings['gallery'];
        delete feedSettings['masonry'];
      } else if (customizerSettings.layout == 'masonry') {
        delete feedSettings['gallery'];
        delete feedSettings['grid'];
      } else if (customizerSettings.layout == 'gallery') {
        delete feedSettings['masonry'];
        delete feedSettings['grid'];
      }
      if (customizerSettings.layout !== 'carousel') {
        delete feedSettings['carousel'];
      }
      youtubeFeed.attr("data-options", JSON.stringify(feedSettings));
      if (typeof window.sby_init !== 'undefined' && flyPreview) {
        //setTimeout(function(){
        window.sby_init();
        //}, 2000)
      }
    },
    /**
     * Should Show Manual Connect
     * 
     * @since 2.0
     */
    showManualConnect: function showManualConnect() {
      var self = this;
      self.shouldShowManualConnect = true;
      self.shouldShowFeedAPIBackBtn = true;
    },
    /**
     * Should Show Manual Connect
     * 
     * @since 2.0
     */
    showFeedSourceManualConnect: function showFeedSourceManualConnect() {
      var self = this;
      self.viewsActive.accountAPIPopup = true;
      self.shouldShowManualConnect = true;
    },
    /**
     * Show API connect form in feed creation flow
     */
    showAPIConnectForm: function showAPIConnectForm() {
      var self = this;
      self.shouldShowFeedAPIForm = true;
      self.shouldShowFeedAPIBackBtn = true;
    },
    /**
     * Show the limitations of connecting with YouTube Account
     * @since 2.3
     */
    showYTAccountLimitations: function showYTAccountLimitations() {
      var self = this;
      self.showShowYTAccountWarning = true;
    },
    backToApiPopup: function backToApiPopup() {
      var self = this;
      self.showShowYTAccountWarning = false;
      self.shouldShowManualConnect = false;
      self.shouldShowFeedAPIForm = false;
      self.shouldShowFeedAPIBackBtn = false;
    },
    /**
     * Show API connect form in feed creation flow
     */
    hideAPIConnectForm: function hideAPIConnectForm() {
      var self = this;
      self.shouldShowManualConnect = false;
      self.shouldShowFeedAPIForm = false;
      self.shouldShowFeedAPIBackBtn = false;
    },
    /**
     * Add API Key from the select feed flow
     * 
     * @since 2.0
     */
    addAPIKey: function addAPIKey() {
      var self = this;
      if (!self.selectedFeedModel.apiKey) {
        self.apiKeyError = true;
        return;
      }
      var self = this,
        addAPIKeyData = {
          action: 'sby_add_api_key',
          api: self.selectedFeedModel.apiKey
        };
      self.apiKeyBtnLoader = true;
      self.ajaxPost(addAPIKeyData, function (_ref) {
        var data = _ref.data;
        self.apiKeyBtnLoader = false;
        self.apiKeyError = false;
        self.apiKeyStatus = true;
        self.activateView('accountAPIPopup');
      });
    },
    /**
     * Add Access Tokoen from the select feed flow
     * 
     * @since 2.0
     */
    addAccessToken: function addAccessToken() {
      var self = this;
      if (!self.selectedFeedModel.accessToken) {
        self.accessTokenError = true;
        return;
      }
      var self = this,
        addAPIKeyData = {
          action: 'sby_manual_access_token',
          sby_access_token: self.selectedFeedModel.accessToken
        };
      self.apiKeyBtnLoader = true;
      self.ajaxPost(addAPIKeyData, function (_ref) {
        var data = _ref.data;
        self.apiKeyBtnLoader = false;
        self.accessTokenError = false;
        self.apiKeyStatus = true;
        self.activateView('accountAPIPopup');
      });
    },
    /**
     * Create & Submit New Feed
     *
     * @since 2.0
     */
    submitNewFeed: function submitNewFeed() {
      var self = this,
        newFeedData = {
          action: 'sby_feed_saver_manager_builder_update',
          feedtype: self.selectedFeed,
          feedtemplate: self.selectedFeedTemplate,
          selectedFeedModel: self.selectedFeedModel,
          new_insert: 'true'
        };
      self.fullScreenLoader = true;
      self.ajaxPost(newFeedData, function (_ref) {
        var data = _ref.data;
        if (data.feed_id && data.success) {
          window.location = self.builderUrl + '&feed_id=' + data.feed_id + self.sw_feed_params();
        }
      });
    },
    /**
     * Custom field click action
     * Action
     * @since 2.3.3
     */
    fieldCustomClickAction: function fieldCustomClickAction(clickAction) {
      var self = this;
      switch (clickAction) {
        case 'clearCommentCache':
          self.clearCommentCache();
          break;
      }
    },
    /**
     * Clear comment cache action
     *
     * @since 2.3.3
     */
    clearCommentCache: function clearCommentCache() {
      var self = this;
      self.loadingBar = true;
      var clearCommentCacheData = {
        action: 'sby_feed_saver_clear_comments_cache'
      };
      self.ajaxPost(clearCommentCacheData, function (_ref) {
        var data = _ref.data;
        if (data === 'success') {
          self.processNotification("commentCacheCleared");
        } else {
          self.processNotification("unkownError");
        }
      });
    }
  });
  resolve(SB_Customizer);
});
/******/ })()
;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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L0MsSUFBSW5LLElBQUksR0FBRyxJQUFJO01BQ2YsUUFBUW1LLFdBQVc7UUFDbEIsS0FBSyxtQkFBbUI7VUFDdkJuSyxJQUFJLENBQUNvSyxpQkFBaUIsQ0FBQyxDQUFDO1VBQ3pCO01BQ0Q7SUFDRCxDQUFDO0lBRUQ7QUFDRjtBQUNBO0FBQ0E7QUFDQTtJQUNFQSxpQkFBaUIsRUFBRyxTQUFBQSxrQkFBQSxFQUFVO01BQzdCLElBQUlwSyxJQUFJLEdBQUcsSUFBSTtNQUNmQSxJQUFJLENBQUNxSyxVQUFVLEdBQUcsSUFBSTtNQUN0QixJQUFJQyxxQkFBcUIsR0FBRztRQUMzQi9HLE1BQU0sRUFBRztNQUNWLENBQUM7TUFDRHZELElBQUksQ0FBQ3dELFFBQVEsQ0FBQzhHLHFCQUFxQixFQUFFLFVBQVM3RyxJQUFJLEVBQUM7UUFDbEQsSUFBSUMsSUFBSSxHQUFHRCxJQUFJLENBQUNDLElBQUk7UUFDcEIsSUFBSUEsSUFBSSxLQUFLLFNBQVMsRUFBRTtVQUN2QjFELElBQUksQ0FBQ3VLLG1CQUFtQixDQUFDLHFCQUFxQixDQUFDO1FBQ2hELENBQUMsTUFBSTtVQUNKdkssSUFBSSxDQUFDdUssbUJBQW1CLENBQUMsYUFBYSxDQUFDO1FBQ3hDO01BQ0QsQ0FBQyxDQUFDO0lBQ0g7RUFBQyxFQUNEO0VBQ0RoTixPQUFPLENBQUNILGFBQWEsQ0FBQztBQUN2QixDQUFDLENBQUMsQyIsInNvdXJjZXMiOlsid2VicGFjazovL3lvdXR1YmUtZmVlZC1wcm8vLi9ub2RlX21vZHVsZXMvQHdvcmRwcmVzcy9ob29rcy9idWlsZC1tb2R1bGUvY3JlYXRlQWRkSG9vay5qcyIsIndlYnBhY2s6Ly95b3V0dWJlLWZlZWQtcHJvLy4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvaG9va3MvYnVpbGQtbW9kdWxlL2NyZWF0ZUN1cnJlbnRIb29rLmpzIiwid2VicGFjazovL3lvdXR1YmUtZmVlZC1wcm8vLi9ub2RlX21vZHVsZXMvQHdvcmRwcmVzcy9ob29rcy9idWlsZC1tb2R1bGUvY3JlYXRlRGlkSG9vay5qcyIsIndlYnBhY2s6Ly95b3V0dWJlLWZlZWQtcHJvLy4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvaG9va3MvYnVpbGQtbW9kdWxlL2NyZWF0ZURvaW5nSG9vay5qcyIsIndlYnBhY2s6Ly95b3V0dWJlLWZlZWQtcHJvLy4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvaG9va3MvYnVpbGQtbW9kdWxlL2NyZWF0ZUhhc0hvb2suanMiLCJ3ZWJwYWNrOi8veW91dHViZS1mZWVkLXByby8uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2hvb2tzL2J1aWxkLW1vZHVsZS9jcmVhdGVIb29rcy5qcyIsIndlYnBhY2s6Ly95b3V0dWJlLWZlZWQtcHJvLy4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvaG9va3MvYnVpbGQtbW9kdWxlL2NyZWF0ZVJlbW92ZUhvb2suanMiLCJ3ZWJwYWNrOi8veW91dHViZS1mZWVkLXByby8uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2hvb2tzL2J1aWxkLW1vZHVsZS9jcmVhdGVSdW5Ib29rLmpzIiwid2VicGFjazovL3lvdXR1YmUtZmVlZC1wcm8vLi9ub2RlX21vZHVsZXMvQHdvcmRwcmVzcy9ob29rcy9idWlsZC1tb2R1bGUvaW5kZXguanMiLCJ3ZWJwYWNrOi8veW91dHViZS1mZWVkLXByby8uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2hvb2tzL2J1aWxkLW1vZHVsZS92YWxpZGF0ZUhvb2tOYW1lLmpzIiwid2VicGFjazovL3lvdXR1YmUtZmVlZC1wcm8vLi9ub2RlX21vZHVsZXMvQHdvcmRwcmVzcy9ob29rcy9idWlsZC1tb2R1bGUvdmFsaWRhdGVOYW1lc3BhY2UuanMiLCJ3ZWJwYWNrOi8veW91dHViZS1mZWVkLXByby93ZWJwYWNrL2Jvb3RzdHJhcCIsIndlYnBhY2s6Ly95b3V0dWJlLWZlZWQtcHJvL3dlYnBhY2svcnVudGltZS9kZWZpbmUgcHJvcGVydHkgZ2V0dGVycyIsIndlYnBhY2s6Ly95b3V0dWJlLWZlZWQtcHJvL3dlYnBhY2svcnVudGltZS9oYXNPd25Qcm9wZXJ0eSBzaG9ydGhhbmQiLCJ3ZWJwYWNrOi8veW91dHViZS1mZWVkLXByby93ZWJwYWNrL3J1bnRpbWUvbWFrZSBuYW1lc3BhY2Ugb2JqZWN0Iiwid2VicGFjazovL3lvdXR1YmUtZmVlZC1wcm8vLi9qcy9jdXN0b21pemVyLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogSW50ZXJuYWwgZGVwZW5kZW5jaWVzXG4gKi9cbmltcG9ydCB2YWxpZGF0ZU5hbWVzcGFjZSBmcm9tICcuL3ZhbGlkYXRlTmFtZXNwYWNlLmpzJztcbmltcG9ydCB2YWxpZGF0ZUhvb2tOYW1lIGZyb20gJy4vdmFsaWRhdGVIb29rTmFtZS5qcyc7XG5cbi8qKlxuICogQGNhbGxiYWNrIEFkZEhvb2tcbiAqXG4gKiBBZGRzIHRoZSBob29rIHRvIHRoZSBhcHByb3ByaWF0ZSBob29rcyBjb250YWluZXIuXG4gKlxuICogQHBhcmFtIHtzdHJpbmd9ICAgICAgICAgICAgICAgaG9va05hbWUgICAgICBOYW1lIG9mIGhvb2sgdG8gYWRkXG4gKiBAcGFyYW0ge3N0cmluZ30gICAgICAgICAgICAgICBuYW1lc3BhY2UgICAgIFRoZSB1bmlxdWUgbmFtZXNwYWNlIGlkZW50aWZ5aW5nIHRoZSBjYWxsYmFjayBpbiB0aGUgZm9ybSBgdmVuZG9yL3BsdWdpbi9mdW5jdGlvbmAuXG4gKiBAcGFyYW0ge2ltcG9ydCgnLicpLkNhbGxiYWNrfSBjYWxsYmFjayAgICAgIEZ1bmN0aW9uIHRvIGNhbGwgd2hlbiB0aGUgaG9vayBpcyBydW5cbiAqIEBwYXJhbSB7bnVtYmVyfSAgICAgICAgICAgICAgIFtwcmlvcml0eT0xMF0gUHJpb3JpdHkgb2YgdGhpcyBob29rXG4gKi9cblxuLyoqXG4gKiBSZXR1cm5zIGEgZnVuY3Rpb24gd2hpY2gsIHdoZW4gaW52b2tlZCwgd2lsbCBhZGQgYSBob29rLlxuICpcbiAqIEBwYXJhbSB7aW1wb3J0KCcuJykuSG9va3N9ICAgIGhvb2tzICAgIEhvb2tzIGluc3RhbmNlLlxuICogQHBhcmFtIHtpbXBvcnQoJy4nKS5TdG9yZUtleX0gc3RvcmVLZXlcbiAqXG4gKiBAcmV0dXJuIHtBZGRIb29rfSBGdW5jdGlvbiB0aGF0IGFkZHMgYSBuZXcgaG9vay5cbiAqL1xuZnVuY3Rpb24gY3JlYXRlQWRkSG9vayhob29rcywgc3RvcmVLZXkpIHtcbiAgcmV0dXJuIGZ1bmN0aW9uIGFkZEhvb2soaG9va05hbWUsIG5hbWVzcGFjZSwgY2FsbGJhY2ssIHByaW9yaXR5ID0gMTApIHtcbiAgICBjb25zdCBob29rc1N0b3JlID0gaG9va3Nbc3RvcmVLZXldO1xuICAgIGlmICghdmFsaWRhdGVIb29rTmFtZShob29rTmFtZSkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKCF2YWxpZGF0ZU5hbWVzcGFjZShuYW1lc3BhY2UpKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIGlmICgnZnVuY3Rpb24nICE9PSB0eXBlb2YgY2FsbGJhY2spIHtcbiAgICAgIC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSBuby1jb25zb2xlXG4gICAgICBjb25zb2xlLmVycm9yKCdUaGUgaG9vayBjYWxsYmFjayBtdXN0IGJlIGEgZnVuY3Rpb24uJyk7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgLy8gVmFsaWRhdGUgbnVtZXJpYyBwcmlvcml0eVxuICAgIGlmICgnbnVtYmVyJyAhPT0gdHlwZW9mIHByaW9yaXR5KSB7XG4gICAgICAvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgbm8tY29uc29sZVxuICAgICAgY29uc29sZS5lcnJvcignSWYgc3BlY2lmaWVkLCB0aGUgaG9vayBwcmlvcml0eSBtdXN0IGJlIGEgbnVtYmVyLicpO1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICBjb25zdCBoYW5kbGVyID0ge1xuICAgICAgY2FsbGJhY2ssXG4gICAgICBwcmlvcml0eSxcbiAgICAgIG5hbWVzcGFjZVxuICAgIH07XG4gICAgaWYgKGhvb2tzU3RvcmVbaG9va05hbWVdKSB7XG4gICAgICAvLyBGaW5kIHRoZSBjb3JyZWN0IGluc2VydCBpbmRleCBvZiB0aGUgbmV3IGhvb2suXG4gICAgICBjb25zdCBoYW5kbGVycyA9IGhvb2tzU3RvcmVbaG9va05hbWVdLmhhbmRsZXJzO1xuXG4gICAgICAvKiogQHR5cGUge251bWJlcn0gKi9cbiAgICAgIGxldCBpO1xuICAgICAgZm9yIChpID0gaGFuZGxlcnMubGVuZ3RoOyBpID4gMDsgaS0tKSB7XG4gICAgICAgIGlmIChwcmlvcml0eSA+PSBoYW5kbGVyc1tpIC0gMV0ucHJpb3JpdHkpIHtcbiAgICAgICAgICBicmVhaztcbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgaWYgKGkgPT09IGhhbmRsZXJzLmxlbmd0aCkge1xuICAgICAgICAvLyBJZiBhcHBlbmQsIG9wZXJhdGUgdmlhIGRpcmVjdCBhc3NpZ25tZW50LlxuICAgICAgICBoYW5kbGVyc1tpXSA9IGhhbmRsZXI7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAvLyBPdGhlcndpc2UsIGluc2VydCBiZWZvcmUgaW5kZXggdmlhIHNwbGljZS5cbiAgICAgICAgaGFuZGxlcnMuc3BsaWNlKGksIDAsIGhhbmRsZXIpO1xuICAgICAgfVxuXG4gICAgICAvLyBXZSBtYXkgYWxzbyBiZSBjdXJyZW50bHkgZXhlY3V0aW5nIHRoaXMgaG9vay4gIElmIHRoZSBjYWxsYmFja1xuICAgICAgLy8gd2UncmUgYWRkaW5nIHdvdWxkIGNvbWUgYWZ0ZXIgdGhlIGN1cnJlbnQgY2FsbGJhY2ssIHRoZXJlJ3Mgbm9cbiAgICAgIC8vIHByb2JsZW07IG90aGVyd2lzZSB3ZSBuZWVkIHRvIGluY3JlYXNlIHRoZSBleGVjdXRpb24gaW5kZXggb2ZcbiAgICAgIC8vIGFueSBvdGhlciBydW5zIGJ5IDEgdG8gYWNjb3VudCBmb3IgdGhlIGFkZGVkIGVsZW1lbnQuXG4gICAgICBob29rc1N0b3JlLl9fY3VycmVudC5mb3JFYWNoKGhvb2tJbmZvID0+IHtcbiAgICAgICAgaWYgKGhvb2tJbmZvLm5hbWUgPT09IGhvb2tOYW1lICYmIGhvb2tJbmZvLmN1cnJlbnRJbmRleCA+PSBpKSB7XG4gICAgICAgICAgaG9va0luZm8uY3VycmVudEluZGV4Kys7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgIH0gZWxzZSB7XG4gICAgICAvLyBUaGlzIGlzIHRoZSBmaXJzdCBob29rIG9mIGl0cyB0eXBlLlxuICAgICAgaG9va3NTdG9yZVtob29rTmFtZV0gPSB7XG4gICAgICAgIGhhbmRsZXJzOiBbaGFuZGxlcl0sXG4gICAgICAgIHJ1bnM6IDBcbiAgICAgIH07XG4gICAgfVxuICAgIGlmIChob29rTmFtZSAhPT0gJ2hvb2tBZGRlZCcpIHtcbiAgICAgIGhvb2tzLmRvQWN0aW9uKCdob29rQWRkZWQnLCBob29rTmFtZSwgbmFtZXNwYWNlLCBjYWxsYmFjaywgcHJpb3JpdHkpO1xuICAgIH1cbiAgfTtcbn1cbmV4cG9ydCBkZWZhdWx0IGNyZWF0ZUFkZEhvb2s7XG4vLyMgc291cmNlTWFwcGluZ1VSTD1jcmVhdGVBZGRIb29rLmpzLm1hcCIsIi8qKlxuICogUmV0dXJucyBhIGZ1bmN0aW9uIHdoaWNoLCB3aGVuIGludm9rZWQsIHdpbGwgcmV0dXJuIHRoZSBuYW1lIG9mIHRoZVxuICogY3VycmVudGx5IHJ1bm5pbmcgaG9vaywgb3IgYG51bGxgIGlmIG5vIGhvb2sgb2YgdGhlIGdpdmVuIHR5cGUgaXMgY3VycmVudGx5XG4gKiBydW5uaW5nLlxuICpcbiAqIEBwYXJhbSB7aW1wb3J0KCcuJykuSG9va3N9ICAgIGhvb2tzICAgIEhvb2tzIGluc3RhbmNlLlxuICogQHBhcmFtIHtpbXBvcnQoJy4nKS5TdG9yZUtleX0gc3RvcmVLZXlcbiAqXG4gKiBAcmV0dXJuIHsoKSA9PiBzdHJpbmcgfCBudWxsfSBGdW5jdGlvbiB0aGF0IHJldHVybnMgdGhlIGN1cnJlbnQgaG9vayBuYW1lIG9yIG51bGwuXG4gKi9cbmZ1bmN0aW9uIGNyZWF0ZUN1cnJlbnRIb29rKGhvb2tzLCBzdG9yZUtleSkge1xuICByZXR1cm4gZnVuY3Rpb24gY3VycmVudEhvb2soKSB7XG4gICAgdmFyIF9ob29rc1N0b3JlJF9fY3VycmVudDtcbiAgICBjb25zdCBob29rc1N0b3JlID0gaG9va3Nbc3RvcmVLZXldO1xuICAgIHJldHVybiAoX2hvb2tzU3RvcmUkX19jdXJyZW50ID0gaG9va3NTdG9yZS5fX2N1cnJlbnRbaG9va3NTdG9yZS5fX2N1cnJlbnQubGVuZ3RoIC0gMV0/Lm5hbWUpICE9PSBudWxsICYmIF9ob29rc1N0b3JlJF9fY3VycmVudCAhPT0gdm9pZCAwID8gX2hvb2tzU3RvcmUkX19jdXJyZW50IDogbnVsbDtcbiAgfTtcbn1cbmV4cG9ydCBkZWZhdWx0IGNyZWF0ZUN1cnJlbnRIb29rO1xuLy8jIHNvdXJjZU1hcHBpbmdVUkw9Y3JlYXRlQ3VycmVudEhvb2suanMubWFwIiwiLyoqXG4gKiBJbnRlcm5hbCBkZXBlbmRlbmNpZXNcbiAqL1xuaW1wb3J0IHZhbGlkYXRlSG9va05hbWUgZnJvbSAnLi92YWxpZGF0ZUhvb2tOYW1lLmpzJztcblxuLyoqXG4gKiBAY2FsbGJhY2sgRGlkSG9va1xuICpcbiAqIFJldHVybnMgdGhlIG51bWJlciBvZiB0aW1lcyBhbiBhY3Rpb24gaGFzIGJlZW4gZmlyZWQuXG4gKlxuICogQHBhcmFtIHtzdHJpbmd9IGhvb2tOYW1lIFRoZSBob29rIG5hbWUgdG8gY2hlY2suXG4gKlxuICogQHJldHVybiB7bnVtYmVyIHwgdW5kZWZpbmVkfSBUaGUgbnVtYmVyIG9mIHRpbWVzIHRoZSBob29rIGhhcyBydW4uXG4gKi9cblxuLyoqXG4gKiBSZXR1cm5zIGEgZnVuY3Rpb24gd2hpY2gsIHdoZW4gaW52b2tlZCwgd2lsbCByZXR1cm4gdGhlIG51bWJlciBvZiB0aW1lcyBhXG4gKiBob29rIGhhcyBiZWVuIGNhbGxlZC5cbiAqXG4gKiBAcGFyYW0ge2ltcG9ydCgnLicpLkhvb2tzfSAgICBob29rcyAgICBIb29rcyBpbnN0YW5jZS5cbiAqIEBwYXJhbSB7aW1wb3J0KCcuJykuU3RvcmVLZXl9IHN0b3JlS2V5XG4gKlxuICogQHJldHVybiB7RGlkSG9va30gRnVuY3Rpb24gdGhhdCByZXR1cm5zIGEgaG9vaydzIGNhbGwgY291bnQuXG4gKi9cbmZ1bmN0aW9uIGNyZWF0ZURpZEhvb2soaG9va3MsIHN0b3JlS2V5KSB7XG4gIHJldHVybiBmdW5jdGlvbiBkaWRIb29rKGhvb2tOYW1lKSB7XG4gICAgY29uc3QgaG9va3NTdG9yZSA9IGhvb2tzW3N0b3JlS2V5XTtcbiAgICBpZiAoIXZhbGlkYXRlSG9va05hbWUoaG9va05hbWUpKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIHJldHVybiBob29rc1N0b3JlW2hvb2tOYW1lXSAmJiBob29rc1N0b3JlW2hvb2tOYW1lXS5ydW5zID8gaG9va3NTdG9yZVtob29rTmFtZV0ucnVucyA6IDA7XG4gIH07XG59XG5leHBvcnQgZGVmYXVsdCBjcmVhdGVEaWRIb29rO1xuLy8jIHNvdXJjZU1hcHBpbmdVUkw9Y3JlYXRlRGlkSG9vay5qcy5tYXAiLCIvKipcbiAqIEBjYWxsYmFjayBEb2luZ0hvb2tcbiAqIFJldHVybnMgd2hldGhlciBhIGhvb2sgaXMgY3VycmVudGx5IGJlaW5nIGV4ZWN1dGVkLlxuICpcbiAqIEBwYXJhbSB7c3RyaW5nfSBbaG9va05hbWVdIFRoZSBuYW1lIG9mIHRoZSBob29rIHRvIGNoZWNrIGZvci4gIElmXG4gKiAgICAgICAgICAgICAgICAgICAgICAgICAgICBvbWl0dGVkLCB3aWxsIGNoZWNrIGZvciBhbnkgaG9vayBiZWluZyBleGVjdXRlZC5cbiAqXG4gKiBAcmV0dXJuIHtib29sZWFufSBXaGV0aGVyIHRoZSBob29rIGlzIGJlaW5nIGV4ZWN1dGVkLlxuICovXG5cbi8qKlxuICogUmV0dXJucyBhIGZ1bmN0aW9uIHdoaWNoLCB3aGVuIGludm9rZWQsIHdpbGwgcmV0dXJuIHdoZXRoZXIgYSBob29rIGlzXG4gKiBjdXJyZW50bHkgYmVpbmcgZXhlY3V0ZWQuXG4gKlxuICogQHBhcmFtIHtpbXBvcnQoJy4nKS5Ib29rc30gICAgaG9va3MgICAgSG9va3MgaW5zdGFuY2UuXG4gKiBAcGFyYW0ge2ltcG9ydCgnLicpLlN0b3JlS2V5fSBzdG9yZUtleVxuICpcbiAqIEByZXR1cm4ge0RvaW5nSG9va30gRnVuY3Rpb24gdGhhdCByZXR1cm5zIHdoZXRoZXIgYSBob29rIGlzIGN1cnJlbnRseVxuICogICAgICAgICAgICAgICAgICAgICBiZWluZyBleGVjdXRlZC5cbiAqL1xuZnVuY3Rpb24gY3JlYXRlRG9pbmdIb29rKGhvb2tzLCBzdG9yZUtleSkge1xuICByZXR1cm4gZnVuY3Rpb24gZG9pbmdIb29rKGhvb2tOYW1lKSB7XG4gICAgY29uc3QgaG9va3NTdG9yZSA9IGhvb2tzW3N0b3JlS2V5XTtcblxuICAgIC8vIElmIHRoZSBob29rTmFtZSB3YXMgbm90IHBhc3NlZCwgY2hlY2sgZm9yIGFueSBjdXJyZW50IGhvb2suXG4gICAgaWYgKCd1bmRlZmluZWQnID09PSB0eXBlb2YgaG9va05hbWUpIHtcbiAgICAgIHJldHVybiAndW5kZWZpbmVkJyAhPT0gdHlwZW9mIGhvb2tzU3RvcmUuX19jdXJyZW50WzBdO1xuICAgIH1cblxuICAgIC8vIFJldHVybiB0aGUgX19jdXJyZW50IGhvb2suXG4gICAgcmV0dXJuIGhvb2tzU3RvcmUuX19jdXJyZW50WzBdID8gaG9va05hbWUgPT09IGhvb2tzU3RvcmUuX19jdXJyZW50WzBdLm5hbWUgOiBmYWxzZTtcbiAgfTtcbn1cbmV4cG9ydCBkZWZhdWx0IGNyZWF0ZURvaW5nSG9vaztcbi8vIyBzb3VyY2VNYXBwaW5nVVJMPWNyZWF0ZURvaW5nSG9vay5qcy5tYXAiLCIvKipcbiAqIEBjYWxsYmFjayBIYXNIb29rXG4gKlxuICogUmV0dXJucyB3aGV0aGVyIGFueSBoYW5kbGVycyBhcmUgYXR0YWNoZWQgZm9yIHRoZSBnaXZlbiBob29rTmFtZSBhbmQgb3B0aW9uYWwgbmFtZXNwYWNlLlxuICpcbiAqIEBwYXJhbSB7c3RyaW5nfSBob29rTmFtZSAgICBUaGUgbmFtZSBvZiB0aGUgaG9vayB0byBjaGVjayBmb3IuXG4gKiBAcGFyYW0ge3N0cmluZ30gW25hbWVzcGFjZV0gT3B0aW9uYWwuIFRoZSB1bmlxdWUgbmFtZXNwYWNlIGlkZW50aWZ5aW5nIHRoZSBjYWxsYmFja1xuICogICAgICAgICAgICAgICAgICAgICAgICAgICAgIGluIHRoZSBmb3JtIGB2ZW5kb3IvcGx1Z2luL2Z1bmN0aW9uYC5cbiAqXG4gKiBAcmV0dXJuIHtib29sZWFufSBXaGV0aGVyIHRoZXJlIGFyZSBoYW5kbGVycyB0aGF0IGFyZSBhdHRhY2hlZCB0byB0aGUgZ2l2ZW4gaG9vay5cbiAqL1xuLyoqXG4gKiBSZXR1cm5zIGEgZnVuY3Rpb24gd2hpY2gsIHdoZW4gaW52b2tlZCwgd2lsbCByZXR1cm4gd2hldGhlciBhbnkgaGFuZGxlcnMgYXJlXG4gKiBhdHRhY2hlZCB0byBhIHBhcnRpY3VsYXIgaG9vay5cbiAqXG4gKiBAcGFyYW0ge2ltcG9ydCgnLicpLkhvb2tzfSAgICBob29rcyAgICBIb29rcyBpbnN0YW5jZS5cbiAqIEBwYXJhbSB7aW1wb3J0KCcuJykuU3RvcmVLZXl9IHN0b3JlS2V5XG4gKlxuICogQHJldHVybiB7SGFzSG9va30gRnVuY3Rpb24gdGhhdCByZXR1cm5zIHdoZXRoZXIgYW55IGhhbmRsZXJzIGFyZVxuICogICAgICAgICAgICAgICAgICAgYXR0YWNoZWQgdG8gYSBwYXJ0aWN1bGFyIGhvb2sgYW5kIG9wdGlvbmFsIG5hbWVzcGFjZS5cbiAqL1xuZnVuY3Rpb24gY3JlYXRlSGFzSG9vayhob29rcywgc3RvcmVLZXkpIHtcbiAgcmV0dXJuIGZ1bmN0aW9uIGhhc0hvb2soaG9va05hbWUsIG5hbWVzcGFjZSkge1xuICAgIGNvbnN0IGhvb2tzU3RvcmUgPSBob29rc1tzdG9yZUtleV07XG5cbiAgICAvLyBVc2UgdGhlIG5hbWVzcGFjZSBpZiBwcm92aWRlZC5cbiAgICBpZiAoJ3VuZGVmaW5lZCcgIT09IHR5cGVvZiBuYW1lc3BhY2UpIHtcbiAgICAgIHJldHVybiBob29rTmFtZSBpbiBob29rc1N0b3JlICYmIGhvb2tzU3RvcmVbaG9va05hbWVdLmhhbmRsZXJzLnNvbWUoaG9vayA9PiBob29rLm5hbWVzcGFjZSA9PT0gbmFtZXNwYWNlKTtcbiAgICB9XG4gICAgcmV0dXJuIGhvb2tOYW1lIGluIGhvb2tzU3RvcmU7XG4gIH07XG59XG5leHBvcnQgZGVmYXVsdCBjcmVhdGVIYXNIb29rO1xuLy8jIHNvdXJjZU1hcHBpbmdVUkw9Y3JlYXRlSGFzSG9vay5qcy5tYXAiLCIvKipcbiAqIEludGVybmFsIGRlcGVuZGVuY2llc1xuICovXG5pbXBvcnQgY3JlYXRlQWRkSG9vayBmcm9tICcuL2NyZWF0ZUFkZEhvb2snO1xuaW1wb3J0IGNyZWF0ZVJlbW92ZUhvb2sgZnJvbSAnLi9jcmVhdGVSZW1vdmVIb29rJztcbmltcG9ydCBjcmVhdGVIYXNIb29rIGZyb20gJy4vY3JlYXRlSGFzSG9vayc7XG5pbXBvcnQgY3JlYXRlUnVuSG9vayBmcm9tICcuL2NyZWF0ZVJ1bkhvb2snO1xuaW1wb3J0IGNyZWF0ZUN1cnJlbnRIb29rIGZyb20gJy4vY3JlYXRlQ3VycmVudEhvb2snO1xuaW1wb3J0IGNyZWF0ZURvaW5nSG9vayBmcm9tICcuL2NyZWF0ZURvaW5nSG9vayc7XG5pbXBvcnQgY3JlYXRlRGlkSG9vayBmcm9tICcuL2NyZWF0ZURpZEhvb2snO1xuXG4vKipcbiAqIEludGVybmFsIGNsYXNzIGZvciBjb25zdHJ1Y3RpbmcgaG9va3MuIFVzZSBgY3JlYXRlSG9va3MoKWAgZnVuY3Rpb25cbiAqXG4gKiBOb3RlLCBpdCBpcyBuZWNlc3NhcnkgdG8gZXhwb3NlIHRoaXMgY2xhc3MgdG8gbWFrZSBpdHMgdHlwZSBwdWJsaWMuXG4gKlxuICogQHByaXZhdGVcbiAqL1xuZXhwb3J0IGNsYXNzIF9Ib29rcyB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIC8qKiBAdHlwZSB7aW1wb3J0KCcuJykuU3RvcmV9IGFjdGlvbnMgKi9cbiAgICB0aGlzLmFjdGlvbnMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgIHRoaXMuYWN0aW9ucy5fX2N1cnJlbnQgPSBbXTtcblxuICAgIC8qKiBAdHlwZSB7aW1wb3J0KCcuJykuU3RvcmV9IGZpbHRlcnMgKi9cbiAgICB0aGlzLmZpbHRlcnMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgIHRoaXMuZmlsdGVycy5fX2N1cnJlbnQgPSBbXTtcbiAgICB0aGlzLmFkZEFjdGlvbiA9IGNyZWF0ZUFkZEhvb2sodGhpcywgJ2FjdGlvbnMnKTtcbiAgICB0aGlzLmFkZEZpbHRlciA9IGNyZWF0ZUFkZEhvb2sodGhpcywgJ2ZpbHRlcnMnKTtcbiAgICB0aGlzLnJlbW92ZUFjdGlvbiA9IGNyZWF0ZVJlbW92ZUhvb2sodGhpcywgJ2FjdGlvbnMnKTtcbiAgICB0aGlzLnJlbW92ZUZpbHRlciA9IGNyZWF0ZVJlbW92ZUhvb2sodGhpcywgJ2ZpbHRlcnMnKTtcbiAgICB0aGlzLmhhc0FjdGlvbiA9IGNyZWF0ZUhhc0hvb2sodGhpcywgJ2FjdGlvbnMnKTtcbiAgICB0aGlzLmhhc0ZpbHRlciA9IGNyZWF0ZUhhc0hvb2sodGhpcywgJ2ZpbHRlcnMnKTtcbiAgICB0aGlzLnJlbW92ZUFsbEFjdGlvbnMgPSBjcmVhdGVSZW1vdmVIb29rKHRoaXMsICdhY3Rpb25zJywgdHJ1ZSk7XG4gICAgdGhpcy5yZW1vdmVBbGxGaWx0ZXJzID0gY3JlYXRlUmVtb3ZlSG9vayh0aGlzLCAnZmlsdGVycycsIHRydWUpO1xuICAgIHRoaXMuZG9BY3Rpb24gPSBjcmVhdGVSdW5Ib29rKHRoaXMsICdhY3Rpb25zJyk7XG4gICAgdGhpcy5hcHBseUZpbHRlcnMgPSBjcmVhdGVSdW5Ib29rKHRoaXMsICdmaWx0ZXJzJywgdHJ1ZSk7XG4gICAgdGhpcy5jdXJyZW50QWN0aW9uID0gY3JlYXRlQ3VycmVudEhvb2sodGhpcywgJ2FjdGlvbnMnKTtcbiAgICB0aGlzLmN1cnJlbnRGaWx0ZXIgPSBjcmVhdGVDdXJyZW50SG9vayh0aGlzLCAnZmlsdGVycycpO1xuICAgIHRoaXMuZG9pbmdBY3Rpb24gPSBjcmVhdGVEb2luZ0hvb2sodGhpcywgJ2FjdGlvbnMnKTtcbiAgICB0aGlzLmRvaW5nRmlsdGVyID0gY3JlYXRlRG9pbmdIb29rKHRoaXMsICdmaWx0ZXJzJyk7XG4gICAgdGhpcy5kaWRBY3Rpb24gPSBjcmVhdGVEaWRIb29rKHRoaXMsICdhY3Rpb25zJyk7XG4gICAgdGhpcy5kaWRGaWx0ZXIgPSBjcmVhdGVEaWRIb29rKHRoaXMsICdmaWx0ZXJzJyk7XG4gIH1cbn1cblxuLyoqIEB0eXBlZGVmIHtfSG9va3N9IEhvb2tzICovXG5cbi8qKlxuICogUmV0dXJucyBhbiBpbnN0YW5jZSBvZiB0aGUgaG9va3Mgb2JqZWN0LlxuICpcbiAqIEByZXR1cm4ge0hvb2tzfSBBIEhvb2tzIGluc3RhbmNlLlxuICovXG5mdW5jdGlvbiBjcmVhdGVIb29rcygpIHtcbiAgcmV0dXJuIG5ldyBfSG9va3MoKTtcbn1cbmV4cG9ydCBkZWZhdWx0IGNyZWF0ZUhvb2tzO1xuLy8jIHNvdXJjZU1hcHBpbmdVUkw9Y3JlYXRlSG9va3MuanMubWFwIiwiLyoqXG4gKiBJbnRlcm5hbCBkZXBlbmRlbmNpZXNcbiAqL1xuaW1wb3J0IHZhbGlkYXRlTmFtZXNwYWNlIGZyb20gJy4vdmFsaWRhdGVOYW1lc3BhY2UuanMnO1xuaW1wb3J0IHZhbGlkYXRlSG9va05hbWUgZnJvbSAnLi92YWxpZGF0ZUhvb2tOYW1lLmpzJztcblxuLyoqXG4gKiBAY2FsbGJhY2sgUmVtb3ZlSG9va1xuICogUmVtb3ZlcyB0aGUgc3BlY2lmaWVkIGNhbGxiYWNrIChvciBhbGwgY2FsbGJhY2tzKSBmcm9tIHRoZSBob29rIHdpdGggYSBnaXZlbiBob29rTmFtZVxuICogYW5kIG5hbWVzcGFjZS5cbiAqXG4gKiBAcGFyYW0ge3N0cmluZ30gaG9va05hbWUgIFRoZSBuYW1lIG9mIHRoZSBob29rIHRvIG1vZGlmeS5cbiAqIEBwYXJhbSB7c3RyaW5nfSBuYW1lc3BhY2UgVGhlIHVuaXF1ZSBuYW1lc3BhY2UgaWRlbnRpZnlpbmcgdGhlIGNhbGxiYWNrIGluIHRoZVxuICogICAgICAgICAgICAgICAgICAgICAgICAgICBmb3JtIGB2ZW5kb3IvcGx1Z2luL2Z1bmN0aW9uYC5cbiAqXG4gKiBAcmV0dXJuIHtudW1iZXIgfCB1bmRlZmluZWR9IFRoZSBudW1iZXIgb2YgY2FsbGJhY2tzIHJlbW92ZWQuXG4gKi9cblxuLyoqXG4gKiBSZXR1cm5zIGEgZnVuY3Rpb24gd2hpY2gsIHdoZW4gaW52b2tlZCwgd2lsbCByZW1vdmUgYSBzcGVjaWZpZWQgaG9vayBvciBhbGxcbiAqIGhvb2tzIGJ5IHRoZSBnaXZlbiBuYW1lLlxuICpcbiAqIEBwYXJhbSB7aW1wb3J0KCcuJykuSG9va3N9ICAgIGhvb2tzICAgICAgICAgICAgIEhvb2tzIGluc3RhbmNlLlxuICogQHBhcmFtIHtpbXBvcnQoJy4nKS5TdG9yZUtleX0gc3RvcmVLZXlcbiAqIEBwYXJhbSB7Ym9vbGVhbn0gICAgICAgICAgICAgIFtyZW1vdmVBbGw9ZmFsc2VdIFdoZXRoZXIgdG8gcmVtb3ZlIGFsbCBjYWxsYmFja3MgZm9yIGEgaG9va05hbWUsXG4gKiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB3aXRob3V0IHJlZ2FyZCB0byBuYW1lc3BhY2UuIFVzZWQgdG8gY3JlYXRlXG4gKiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBgcmVtb3ZlQWxsKmAgZnVuY3Rpb25zLlxuICpcbiAqIEByZXR1cm4ge1JlbW92ZUhvb2t9IEZ1bmN0aW9uIHRoYXQgcmVtb3ZlcyBob29rcy5cbiAqL1xuZnVuY3Rpb24gY3JlYXRlUmVtb3ZlSG9vayhob29rcywgc3RvcmVLZXksIHJlbW92ZUFsbCA9IGZhbHNlKSB7XG4gIHJldHVybiBmdW5jdGlvbiByZW1vdmVIb29rKGhvb2tOYW1lLCBuYW1lc3BhY2UpIHtcbiAgICBjb25zdCBob29rc1N0b3JlID0gaG9va3Nbc3RvcmVLZXldO1xuICAgIGlmICghdmFsaWRhdGVIb29rTmFtZShob29rTmFtZSkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKCFyZW1vdmVBbGwgJiYgIXZhbGlkYXRlTmFtZXNwYWNlKG5hbWVzcGFjZSkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICAvLyBCYWlsIGlmIG5vIGhvb2tzIGV4aXN0IGJ5IHRoaXMgbmFtZS5cbiAgICBpZiAoIWhvb2tzU3RvcmVbaG9va05hbWVdKSB7XG4gICAgICByZXR1cm4gMDtcbiAgICB9XG4gICAgbGV0IGhhbmRsZXJzUmVtb3ZlZCA9IDA7XG4gICAgaWYgKHJlbW92ZUFsbCkge1xuICAgICAgaGFuZGxlcnNSZW1vdmVkID0gaG9va3NTdG9yZVtob29rTmFtZV0uaGFuZGxlcnMubGVuZ3RoO1xuICAgICAgaG9va3NTdG9yZVtob29rTmFtZV0gPSB7XG4gICAgICAgIHJ1bnM6IGhvb2tzU3RvcmVbaG9va05hbWVdLnJ1bnMsXG4gICAgICAgIGhhbmRsZXJzOiBbXVxuICAgICAgfTtcbiAgICB9IGVsc2Uge1xuICAgICAgLy8gVHJ5IHRvIGZpbmQgdGhlIHNwZWNpZmllZCBjYWxsYmFjayB0byByZW1vdmUuXG4gICAgICBjb25zdCBoYW5kbGVycyA9IGhvb2tzU3RvcmVbaG9va05hbWVdLmhhbmRsZXJzO1xuICAgICAgZm9yIChsZXQgaSA9IGhhbmRsZXJzLmxlbmd0aCAtIDE7IGkgPj0gMDsgaS0tKSB7XG4gICAgICAgIGlmIChoYW5kbGVyc1tpXS5uYW1lc3BhY2UgPT09IG5hbWVzcGFjZSkge1xuICAgICAgICAgIGhhbmRsZXJzLnNwbGljZShpLCAxKTtcbiAgICAgICAgICBoYW5kbGVyc1JlbW92ZWQrKztcbiAgICAgICAgICAvLyBUaGlzIGNhbGxiYWNrIG1heSBhbHNvIGJlIHBhcnQgb2YgYSBob29rIHRoYXQgaXNcbiAgICAgICAgICAvLyBjdXJyZW50bHkgZXhlY3V0aW5nLiAgSWYgdGhlIGNhbGxiYWNrIHdlJ3JlIHJlbW92aW5nXG4gICAgICAgICAgLy8gY29tZXMgYWZ0ZXIgdGhlIGN1cnJlbnQgY2FsbGJhY2ssIHRoZXJlJ3Mgbm8gcHJvYmxlbTtcbiAgICAgICAgICAvLyBvdGhlcndpc2Ugd2UgbmVlZCB0byBkZWNyZWFzZSB0aGUgZXhlY3V0aW9uIGluZGV4IG9mIGFueVxuICAgICAgICAgIC8vIG90aGVyIHJ1bnMgYnkgMSB0byBhY2NvdW50IGZvciB0aGUgcmVtb3ZlZCBlbGVtZW50LlxuICAgICAgICAgIGhvb2tzU3RvcmUuX19jdXJyZW50LmZvckVhY2goaG9va0luZm8gPT4ge1xuICAgICAgICAgICAgaWYgKGhvb2tJbmZvLm5hbWUgPT09IGhvb2tOYW1lICYmIGhvb2tJbmZvLmN1cnJlbnRJbmRleCA+PSBpKSB7XG4gICAgICAgICAgICAgIGhvb2tJbmZvLmN1cnJlbnRJbmRleC0tO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfVxuICAgIGlmIChob29rTmFtZSAhPT0gJ2hvb2tSZW1vdmVkJykge1xuICAgICAgaG9va3MuZG9BY3Rpb24oJ2hvb2tSZW1vdmVkJywgaG9va05hbWUsIG5hbWVzcGFjZSk7XG4gICAgfVxuICAgIHJldHVybiBoYW5kbGVyc1JlbW92ZWQ7XG4gIH07XG59XG5leHBvcnQgZGVmYXVsdCBjcmVhdGVSZW1vdmVIb29rO1xuLy8jIHNvdXJjZU1hcHBpbmdVUkw9Y3JlYXRlUmVtb3ZlSG9vay5qcy5tYXAiLCIvKipcbiAqIFJldHVybnMgYSBmdW5jdGlvbiB3aGljaCwgd2hlbiBpbnZva2VkLCB3aWxsIGV4ZWN1dGUgYWxsIGNhbGxiYWNrc1xuICogcmVnaXN0ZXJlZCB0byBhIGhvb2sgb2YgdGhlIHNwZWNpZmllZCB0eXBlLCBvcHRpb25hbGx5IHJldHVybmluZyB0aGUgZmluYWxcbiAqIHZhbHVlIG9mIHRoZSBjYWxsIGNoYWluLlxuICpcbiAqIEBwYXJhbSB7aW1wb3J0KCcuJykuSG9va3N9ICAgIGhvb2tzICAgICAgICAgICAgICAgICAgSG9va3MgaW5zdGFuY2UuXG4gKiBAcGFyYW0ge2ltcG9ydCgnLicpLlN0b3JlS2V5fSBzdG9yZUtleVxuICogQHBhcmFtIHtib29sZWFufSAgICAgICAgICAgICAgW3JldHVybkZpcnN0QXJnPWZhbHNlXSBXaGV0aGVyIGVhY2ggaG9vayBjYWxsYmFjayBpcyBleHBlY3RlZCB0b1xuICogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gaXRzIGZpcnN0IGFyZ3VtZW50LlxuICpcbiAqIEByZXR1cm4geyhob29rTmFtZTpzdHJpbmcsIC4uLmFyZ3M6IHVua25vd25bXSkgPT4gdW5kZWZpbmVkfHVua25vd259IEZ1bmN0aW9uIHRoYXQgcnVucyBob29rIGNhbGxiYWNrcy5cbiAqL1xuZnVuY3Rpb24gY3JlYXRlUnVuSG9vayhob29rcywgc3RvcmVLZXksIHJldHVybkZpcnN0QXJnID0gZmFsc2UpIHtcbiAgcmV0dXJuIGZ1bmN0aW9uIHJ1bkhvb2tzKGhvb2tOYW1lLCAuLi5hcmdzKSB7XG4gICAgY29uc3QgaG9va3NTdG9yZSA9IGhvb2tzW3N0b3JlS2V5XTtcbiAgICBpZiAoIWhvb2tzU3RvcmVbaG9va05hbWVdKSB7XG4gICAgICBob29rc1N0b3JlW2hvb2tOYW1lXSA9IHtcbiAgICAgICAgaGFuZGxlcnM6IFtdLFxuICAgICAgICBydW5zOiAwXG4gICAgICB9O1xuICAgIH1cbiAgICBob29rc1N0b3JlW2hvb2tOYW1lXS5ydW5zKys7XG4gICAgY29uc3QgaGFuZGxlcnMgPSBob29rc1N0b3JlW2hvb2tOYW1lXS5oYW5kbGVycztcblxuICAgIC8vIFRoZSBmb2xsb3dpbmcgY29kZSBpcyBzdHJpcHBlZCBmcm9tIHByb2R1Y3Rpb24gYnVpbGRzLlxuICAgIGlmICgncHJvZHVjdGlvbicgIT09IHByb2Nlc3MuZW52Lk5PREVfRU5WKSB7XG4gICAgICAvLyBIYW5kbGUgYW55ICdhbGwnIGhvb2tzIHJlZ2lzdGVyZWQuXG4gICAgICBpZiAoJ2hvb2tBZGRlZCcgIT09IGhvb2tOYW1lICYmIGhvb2tzU3RvcmUuYWxsKSB7XG4gICAgICAgIGhhbmRsZXJzLnB1c2goLi4uaG9va3NTdG9yZS5hbGwuaGFuZGxlcnMpO1xuICAgICAgfVxuICAgIH1cbiAgICBpZiAoIWhhbmRsZXJzIHx8ICFoYW5kbGVycy5sZW5ndGgpIHtcbiAgICAgIHJldHVybiByZXR1cm5GaXJzdEFyZyA/IGFyZ3NbMF0gOiB1bmRlZmluZWQ7XG4gICAgfVxuICAgIGNvbnN0IGhvb2tJbmZvID0ge1xuICAgICAgbmFtZTogaG9va05hbWUsXG4gICAgICBjdXJyZW50SW5kZXg6IDBcbiAgICB9O1xuICAgIGhvb2tzU3RvcmUuX19jdXJyZW50LnB1c2goaG9va0luZm8pO1xuICAgIHdoaWxlIChob29rSW5mby5jdXJyZW50SW5kZXggPCBoYW5kbGVycy5sZW5ndGgpIHtcbiAgICAgIGNvbnN0IGhhbmRsZXIgPSBoYW5kbGVyc1tob29rSW5mby5jdXJyZW50SW5kZXhdO1xuICAgICAgY29uc3QgcmVzdWx0ID0gaGFuZGxlci5jYWxsYmFjay5hcHBseShudWxsLCBhcmdzKTtcbiAgICAgIGlmIChyZXR1cm5GaXJzdEFyZykge1xuICAgICAgICBhcmdzWzBdID0gcmVzdWx0O1xuICAgICAgfVxuICAgICAgaG9va0luZm8uY3VycmVudEluZGV4Kys7XG4gICAgfVxuICAgIGhvb2tzU3RvcmUuX19jdXJyZW50LnBvcCgpO1xuICAgIGlmIChyZXR1cm5GaXJzdEFyZykge1xuICAgICAgcmV0dXJuIGFyZ3NbMF07XG4gICAgfVxuICAgIHJldHVybiB1bmRlZmluZWQ7XG4gIH07XG59XG5leHBvcnQgZGVmYXVsdCBjcmVhdGVSdW5Ib29rO1xuLy8jIHNvdXJjZU1hcHBpbmdVUkw9Y3JlYXRlUnVuSG9vay5qcy5tYXAiLCIvKipcbiAqIEludGVybmFsIGRlcGVuZGVuY2llc1xuICovXG5pbXBvcnQgY3JlYXRlSG9va3MgZnJvbSAnLi9jcmVhdGVIb29rcyc7XG5cbi8qKiBAdHlwZWRlZiB7KC4uLmFyZ3M6IGFueVtdKT0+YW55fSBDYWxsYmFjayAqL1xuXG4vKipcbiAqIEB0eXBlZGVmIEhhbmRsZXJcbiAqIEBwcm9wZXJ0eSB7Q2FsbGJhY2t9IGNhbGxiYWNrICBUaGUgY2FsbGJhY2tcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgIG5hbWVzcGFjZSBUaGUgbmFtZXNwYWNlXG4gKiBAcHJvcGVydHkge251bWJlcn0gICBwcmlvcml0eSAgVGhlIG5hbWVzcGFjZVxuICovXG5cbi8qKlxuICogQHR5cGVkZWYgSG9va1xuICogQHByb3BlcnR5IHtIYW5kbGVyW119IGhhbmRsZXJzIEFycmF5IG9mIGhhbmRsZXJzXG4gKiBAcHJvcGVydHkge251bWJlcn0gICAgcnVucyAgICAgUnVuIGNvdW50ZXJcbiAqL1xuXG4vKipcbiAqIEB0eXBlZGVmIEN1cnJlbnRcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSBuYW1lICAgICAgICAgSG9vayBuYW1lXG4gKiBAcHJvcGVydHkge251bWJlcn0gY3VycmVudEluZGV4IFRoZSBpbmRleFxuICovXG5cbi8qKlxuICogQHR5cGVkZWYge1JlY29yZDxzdHJpbmcsIEhvb2s+ICYge19fY3VycmVudDogQ3VycmVudFtdfX0gU3RvcmVcbiAqL1xuXG4vKipcbiAqIEB0eXBlZGVmIHsnYWN0aW9ucycgfCAnZmlsdGVycyd9IFN0b3JlS2V5XG4gKi9cblxuLyoqXG4gKiBAdHlwZWRlZiB7aW1wb3J0KCcuL2NyZWF0ZUhvb2tzJykuSG9va3N9IEhvb2tzXG4gKi9cblxuZXhwb3J0IGNvbnN0IGRlZmF1bHRIb29rcyA9IGNyZWF0ZUhvb2tzKCk7XG5jb25zdCB7XG4gIGFkZEFjdGlvbixcbiAgYWRkRmlsdGVyLFxuICByZW1vdmVBY3Rpb24sXG4gIHJlbW92ZUZpbHRlcixcbiAgaGFzQWN0aW9uLFxuICBoYXNGaWx0ZXIsXG4gIHJlbW92ZUFsbEFjdGlvbnMsXG4gIHJlbW92ZUFsbEZpbHRlcnMsXG4gIGRvQWN0aW9uLFxuICBhcHBseUZpbHRlcnMsXG4gIGN1cnJlbnRBY3Rpb24sXG4gIGN1cnJlbnRGaWx0ZXIsXG4gIGRvaW5nQWN0aW9uLFxuICBkb2luZ0ZpbHRlcixcbiAgZGlkQWN0aW9uLFxuICBkaWRGaWx0ZXIsXG4gIGFjdGlvbnMsXG4gIGZpbHRlcnNcbn0gPSBkZWZhdWx0SG9va3M7XG5leHBvcnQgeyBjcmVhdGVIb29rcywgYWRkQWN0aW9uLCBhZGRGaWx0ZXIsIHJlbW92ZUFjdGlvbiwgcmVtb3ZlRmlsdGVyLCBoYXNBY3Rpb24sIGhhc0ZpbHRlciwgcmVtb3ZlQWxsQWN0aW9ucywgcmVtb3ZlQWxsRmlsdGVycywgZG9BY3Rpb24sIGFwcGx5RmlsdGVycywgY3VycmVudEFjdGlvbiwgY3VycmVudEZpbHRlciwgZG9pbmdBY3Rpb24sIGRvaW5nRmlsdGVyLCBkaWRBY3Rpb24sIGRpZEZpbHRlciwgYWN0aW9ucywgZmlsdGVycyB9O1xuLy8jIHNvdXJjZU1hcHBpbmdVUkw9aW5kZXguanMubWFwIiwiLyoqXG4gKiBWYWxpZGF0ZSBhIGhvb2tOYW1lIHN0cmluZy5cbiAqXG4gKiBAcGFyYW0ge3N0cmluZ30gaG9va05hbWUgVGhlIGhvb2sgbmFtZSB0byB2YWxpZGF0ZS4gU2hvdWxkIGJlIGEgbm9uIGVtcHR5IHN0cmluZyBjb250YWluaW5nXG4gKiAgICAgICAgICAgICAgICAgICAgICAgICAgb25seSBudW1iZXJzLCBsZXR0ZXJzLCBkYXNoZXMsIHBlcmlvZHMgYW5kIHVuZGVyc2NvcmVzLiBBbHNvLFxuICogICAgICAgICAgICAgICAgICAgICAgICAgIHRoZSBob29rIG5hbWUgY2Fubm90IGJlZ2luIHdpdGggYF9fYC5cbiAqXG4gKiBAcmV0dXJuIHtib29sZWFufSBXaGV0aGVyIHRoZSBob29rIG5hbWUgaXMgdmFsaWQuXG4gKi9cbmZ1bmN0aW9uIHZhbGlkYXRlSG9va05hbWUoaG9va05hbWUpIHtcbiAgaWYgKCdzdHJpbmcnICE9PSB0eXBlb2YgaG9va05hbWUgfHwgJycgPT09IGhvb2tOYW1lKSB7XG4gICAgLy8gZXNsaW50LWRpc2FibGUtbmV4dC1saW5lIG5vLWNvbnNvbGVcbiAgICBjb25zb2xlLmVycm9yKCdUaGUgaG9vayBuYW1lIG11c3QgYmUgYSBub24tZW1wdHkgc3RyaW5nLicpO1xuICAgIHJldHVybiBmYWxzZTtcbiAgfVxuICBpZiAoL15fXy8udGVzdChob29rTmFtZSkpIHtcbiAgICAvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgbm8tY29uc29sZVxuICAgIGNvbnNvbGUuZXJyb3IoJ1RoZSBob29rIG5hbWUgY2Fubm90IGJlZ2luIHdpdGggYF9fYC4nKTtcbiAgICByZXR1cm4gZmFsc2U7XG4gIH1cbiAgaWYgKCEvXlthLXpBLVpdW2EtekEtWjAtOV8uLV0qJC8udGVzdChob29rTmFtZSkpIHtcbiAgICAvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgbm8tY29uc29sZVxuICAgIGNvbnNvbGUuZXJyb3IoJ1RoZSBob29rIG5hbWUgY2FuIG9ubHkgY29udGFpbiBudW1iZXJzLCBsZXR0ZXJzLCBkYXNoZXMsIHBlcmlvZHMgYW5kIHVuZGVyc2NvcmVzLicpO1xuICAgIHJldHVybiBmYWxzZTtcbiAgfVxuICByZXR1cm4gdHJ1ZTtcbn1cbmV4cG9ydCBkZWZhdWx0IHZhbGlkYXRlSG9va05hbWU7XG4vLyMgc291cmNlTWFwcGluZ1VSTD12YWxpZGF0ZUhvb2tOYW1lLmpzLm1hcCIsIi8qKlxuICogVmFsaWRhdGUgYSBuYW1lc3BhY2Ugc3RyaW5nLlxuICpcbiAqIEBwYXJhbSB7c3RyaW5nfSBuYW1lc3BhY2UgVGhlIG5hbWVzcGFjZSB0byB2YWxpZGF0ZSAtIHNob3VsZCB0YWtlIHRoZSBmb3JtXG4gKiAgICAgICAgICAgICAgICAgICAgICAgICAgIGB2ZW5kb3IvcGx1Z2luL2Z1bmN0aW9uYC5cbiAqXG4gKiBAcmV0dXJuIHtib29sZWFufSBXaGV0aGVyIHRoZSBuYW1lc3BhY2UgaXMgdmFsaWQuXG4gKi9cbmZ1bmN0aW9uIHZhbGlkYXRlTmFtZXNwYWNlKG5hbWVzcGFjZSkge1xuICBpZiAoJ3N0cmluZycgIT09IHR5cGVvZiBuYW1lc3BhY2UgfHwgJycgPT09IG5hbWVzcGFjZSkge1xuICAgIC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSBuby1jb25zb2xlXG4gICAgY29uc29sZS5lcnJvcignVGhlIG5hbWVzcGFjZSBtdXN0IGJlIGEgbm9uLWVtcHR5IHN0cmluZy4nKTtcbiAgICByZXR1cm4gZmFsc2U7XG4gIH1cbiAgaWYgKCEvXlthLXpBLVpdW2EtekEtWjAtOV8uXFwtXFwvXSokLy50ZXN0KG5hbWVzcGFjZSkpIHtcbiAgICAvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgbm8tY29uc29sZVxuICAgIGNvbnNvbGUuZXJyb3IoJ1RoZSBuYW1lc3BhY2UgY2FuIG9ubHkgY29udGFpbiBudW1iZXJzLCBsZXR0ZXJzLCBkYXNoZXMsIHBlcmlvZHMsIHVuZGVyc2NvcmVzIGFuZCBzbGFzaGVzLicpO1xuICAgIHJldHVybiBmYWxzZTtcbiAgfVxuICByZXR1cm4gdHJ1ZTtcbn1cbmV4cG9ydCBkZWZhdWx0IHZhbGlkYXRlTmFtZXNwYWNlO1xuLy8jIHNvdXJjZU1hcHBpbmdVUkw9dmFsaWRhdGVOYW1lc3BhY2UuanMubWFwIiwiLy8gVGhlIG1vZHVsZSBjYWNoZVxudmFyIF9fd2VicGFja19tb2R1bGVfY2FjaGVfXyA9IHt9O1xuXG4vLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcblx0dmFyIGNhY2hlZE1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF07XG5cdGlmIChjYWNoZWRNb2R1bGUgIT09IHVuZGVmaW5lZCkge1xuXHRcdHJldHVybiBjYWNoZWRNb2R1bGUuZXhwb3J0cztcblx0fVxuXHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuXHR2YXIgbW9kdWxlID0gX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fW21vZHVsZUlkXSA9IHtcblx0XHQvLyBubyBtb2R1bGUuaWQgbmVlZGVkXG5cdFx0Ly8gbm8gbW9kdWxlLmxvYWRlZCBuZWVkZWRcblx0XHRleHBvcnRzOiB7fVxuXHR9O1xuXG5cdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuXHRfX3dlYnBhY2tfbW9kdWxlc19fW21vZHVsZUlkXShtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuXHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuXHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG59XG5cbiIsIi8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb25zIGZvciBoYXJtb255IGV4cG9ydHNcbl9fd2VicGFja19yZXF1aXJlX18uZCA9IChleHBvcnRzLCBkZWZpbml0aW9uKSA9PiB7XG5cdGZvcih2YXIga2V5IGluIGRlZmluaXRpb24pIHtcblx0XHRpZihfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZGVmaW5pdGlvbiwga2V5KSAmJiAhX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIGtleSkpIHtcblx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBrZXksIHsgZW51bWVyYWJsZTogdHJ1ZSwgZ2V0OiBkZWZpbml0aW9uW2tleV0gfSk7XG5cdFx0fVxuXHR9XG59OyIsIl9fd2VicGFja19yZXF1aXJlX18ubyA9IChvYmosIHByb3ApID0+IChPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqLCBwcm9wKSkiLCIvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG5fX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSAoZXhwb3J0cykgPT4ge1xuXHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcblx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcblx0fVxuXHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xufTsiLCJpbXBvcnQge2FkZEZpbHRlciwgYWRkQWN0aW9ufSBmcm9tIFwiQHdvcmRwcmVzcy9ob29rc1wiO1xyXG5cclxuXHJcblNCX0N1c3RvbWl6ZXIuaW5pdFByb21pc2UgPSBuZXcgUHJvbWlzZSgocmVzb2x2ZSkgPT4ge1xyXG5cdFNCX0N1c3RvbWl6ZXIuZXh0cmFEYXRhID0ge1xyXG5cdFx0Li4uU0JfQ3VzdG9taXplci5leHRyYURhdGEsXHJcblx0XHRhbGxGZWVkc1NjcmVlbiA6IHNiY19idWlsZGVyLmFsbEZlZWRzU2NyZWVuLFxyXG5cdFx0ZmVlZHNMaXN0IDogc2JjX2J1aWxkZXIuZmVlZHMsXHJcblx0XHRsZWdhY3lGZWVkc0xpc3Q6IHNiY19idWlsZGVyLmxlZ2FjeUZlZWRzLFxyXG5cdFx0dG9vbHRpcENvbnRlbnQgOiBzYmNfYnVpbGRlci5mZWVkdHlwZXNUb29sdGlwQ29udGVudCxcclxuXHRcdGZlZWRTZXR0aW5nc0RvbU9wdGlvbnMgOiBudWxsLFxyXG5cdFx0c2VsZWN0ZWRGZWVkTW9kZWwgOiB7XHJcblx0XHRcdGNoYW5uZWw6IHNiY19idWlsZGVyLnByZWZpbGxlZENoYW5uZWxJZCxcclxuXHRcdFx0cGxheWxpc3Q6ICcnLFxyXG5cdFx0XHRmYXZvcml0ZXM6IHNiY19idWlsZGVyLnByZWZpbGxlZENoYW5uZWxJZCxcclxuXHRcdFx0c2VhcmNoOiAnJyxcclxuXHRcdFx0bGl2ZTogc2JjX2J1aWxkZXIucHJlZmlsbGVkQ2hhbm5lbElkLFxyXG5cdFx0XHRzaW5nbGU6ICcnLFxyXG5cdFx0XHRhcGlLZXk6ICcnLFxyXG5cdFx0XHRhY2Nlc3NUb2tlbjogJydcclxuXHRcdH0sXHJcblx0XHR5b3V0dWJlQWNjb3VudENvbm5lY3RVUkwgOiBzYmNfYnVpbGRlci55b3V0dWJlQWNjb3VudENvbm5lY3RVUkwsXHJcblx0XHRjb25uZWN0U2l0ZVBhcmFtZXRlcnM6IHNiY19idWlsZGVyLnlvdXR1YmVBY2NvdW50Q29ubmVjdFBhcmFtZXRlcnMsXHJcblx0XHRwcmVmaWxsZWRDaGFubmVsSWQ6IHNiY19idWlsZGVyLnByZWZpbGxlZENoYW5uZWxJZCxcclxuXHRcdGRpc21pc3NMaXRlOiBzYmNfYnVpbGRlci55b3V0dWJlX2ZlZWRfZGlzbWlzc19saXRlLFxyXG5cdFx0c2hvdWxkU2hvd0ZlZWRBUElGb3JtIDogZmFsc2UsXHJcblx0XHRzaG91bGRTaG93TWFudWFsQ29ubmVjdCA6IGZhbHNlLFxyXG5cdFx0c2hvd1Nob3dZVEFjY291bnRXYXJuaW5nIDogZmFsc2UsXHJcblxyXG5cdFx0c3dfZmVlZDogZmFsc2UsXHJcblx0XHRzd19mZWVkX2lkOiBmYWxzZVxyXG5cdH1cclxuXHJcblx0U0JfQ3VzdG9taXplci5leHRyYU1ldGhvZHMgPSB7XHJcblx0XHQuLi5TQl9DdXN0b21pemVyLmV4dHJhTWV0aG9kcyxcclxuXHRcdC8qKlxyXG5cdFx0ICogQ2hhbmdlIFNldHRpbmdzIFZhbHVlXHJcblx0XHQgKlxyXG5cdFx0ICogQHNpbmNlIDIuMFxyXG5cdFx0ICovXHJcblx0XHRjaGFuZ2VTZXR0aW5nVmFsdWUgOiBmdW5jdGlvbihzZXR0aW5nSUQsIHZhbHVlLCBkb1Byb2Nlc3MgPSB0cnVlLCBhamF4QWN0aW9uID0gZmFsc2UpIHtcclxuXHRcdFx0dmFyIHNlbGYgPSB0aGlzO1xyXG5cdFx0XHRpZihkb1Byb2Nlc3Mpe1xyXG5cdFx0XHRcdHNlbGYuY3VzdG9taXplckZlZWREYXRhLnNldHRpbmdzW3NldHRpbmdJRF0gPSB2YWx1ZTtcclxuXHRcdFx0fVxyXG5cdFx0XHRpZihhamF4QWN0aW9uICE9PSBmYWxzZSl7XHJcblx0XHRcdFx0c2VsZi5jdXN0b21pemVyQ29udHJvbEFqYXhBY3Rpb24oYWpheEFjdGlvbiwgc2V0dGluZ0lEKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRzZWxmLnJlZ2VuZXJhdGVMYXlvdXQoc2V0dGluZ0lEKTtcclxuXHRcdH0sXHJcblxyXG5cdFx0Y2hlY2tFeHRlbnNpb25BY3RpdmUgOiBmdW5jdGlvbihleHRlbnNpb24pe1xyXG5cdFx0XHR2YXIgc2VsZiA9IHRoaXM7XHJcblx0XHRcdHJldHVybiBzZWxmLmFjdGl2ZUV4dGVuc2lvbnNbZXh0ZW5zaW9uXTtcclxuXHRcdH0sXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBTaG91bGQgc2hvdyBvdmVybGF5IGZvciB0aGUgc2lkZWJhciBlbGVtZW50cyBvbiB0b3BcclxuXHRcdCAqIFxyXG5cdFx0ICogQHNpbmNlIDIuMFxyXG5cdFx0ICovXHJcblx0XHRzaG91bGRTaG93T3ZlcmxheShjb250cm9sKSB7XHJcblx0XHRcdHZhciBzZWxmID0gdGhpcztcclxuXHRcdFx0aWYgKCAhc2VsZi5zYnlJc1BybyB8fCBcclxuXHRcdFx0XHRcdHNlbGYuc2J5TGljZW5zZU5vdGljZUFjdGl2ZSB8fCBcclxuXHRcdFx0XHRcdCggKCBjb250cm9sLmNoZWNrRXh0ZW5zaW9uUG9wdXAgPT0gJ2NhbGxfdG9fYWN0aW9uJyB8fCBjb250cm9sLmNoZWNrRXh0ZW5zaW9uUG9wdXAgPT0gJ2FkdmFuY2VkRmlsdGVycycgKSAmJiBcclxuXHRcdFx0XHRcdCggIXNlbGYuaGFzRmVhdHVyZSgnY2FsbF90b19hY3Rpb25zJykgfHwgIXNlbGYuaGFzRmVhdHVyZSgnYWR2YW5jZWRGaWx0ZXJzJykgKVxyXG5cdFx0XHRcdFx0KSBcclxuXHRcdFx0XHQpIHtcclxuXHRcdFx0XHRyZXR1cm4gY29udHJvbC5jaGVja0V4dGVuc2lvblBvcHVwICE9IHVuZGVmaW5lZCB8fCAoXHJcblx0XHRcdFx0XHRjb250cm9sLmNvbmRpdGlvbiAhPSB1bmRlZmluZWQgfHwgXHJcblx0XHRcdFx0XHRjb250cm9sLmNoZWNrRXh0ZW5zaW9uICE9IHVuZGVmaW5lZCB8fCBcclxuXHRcdFx0XHRcdGNvbnRyb2wuY2hlY2tFeHRlbnNpb25EaW1tZWQgIT0gdW5kZWZpbmVkICA/IFxyXG5cdFx0XHRcdFx0IXNlbGYuY2hlY2tDb250cm9sQ29uZGl0aW9uKGNvbnRyb2wuY29uZGl0aW9uLCBjb250cm9sLmNoZWNrRXh0ZW5zaW9uLCBjb250cm9sLmNoZWNrRXh0ZW5zaW9uRGltbWVkKSA6IFxyXG5cdFx0XHRcdFx0ZmFsc2VcclxuXHRcdFx0XHRcdCk7XHJcblx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0cmV0dXJuIGNvbnRyb2wuY29uZGl0aW9uICE9IHVuZGVmaW5lZCB8fCBcclxuXHRcdFx0XHRcdGNvbnRyb2wuY2hlY2tFeHRlbnNpb24gIT0gdW5kZWZpbmVkIHx8IFxyXG5cdFx0XHRcdFx0Y29udHJvbC5jaGVja0V4dGVuc2lvbkRpbW1lZCAhPSB1bmRlZmluZWQgID8gXHJcblx0XHRcdFx0XHQhc2VsZi5jaGVja0NvbnRyb2xDb25kaXRpb24oY29udHJvbC5jb25kaXRpb24sIGNvbnRyb2wuY2hlY2tFeHRlbnNpb24sIGNvbnRyb2wuY2hlY2tFeHRlbnNpb25EaW1tZWQpIDogXHJcblx0XHRcdFx0XHRmYWxzZTtcclxuXHRcdFx0fVxyXG5cdFx0fSxcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFNob3VsZCBzaG93IHRvZ2dsZXNldCB0eXBlIGNvdmVyXHJcblx0XHQgKiBcclxuXHRcdCAqIEBzaW5jZSAyLjBcclxuXHRcdCAqL1xyXG5cdFx0c2hvdWxkU2hvd1RvZ2dsZXNldENvdmVyIDogZnVuY3Rpb24odG9nZ2xlKSB7XHJcblx0XHRcdHZhciBzZWxmID0gdGhpcztcclxuXHRcdFx0aWYgKCAhc2VsZi5zYnlJc1BybyB8fCBzZWxmLnNieUxpY2Vuc2VOb3RpY2VBY3RpdmUgKSB7XHJcblx0XHRcdFx0cmV0dXJuIHRvZ2dsZS5jaGVja0V4dGVuc2lvbiAhPSB1bmRlZmluZWQgJiYgIXNlbGYuY2hlY2tFeHRlbnNpb25BY3RpdmUodG9nZ2xlLmNoZWNrRXh0ZW5zaW9uKVxyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdHJldHVybiBmYWxzZVxyXG5cdFx0XHR9XHJcblx0XHR9LFxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogT3BlbiBleHRlbnNpb24gcG9wdXAgZnJvbSB0b2dnbGVzZXQgY292ZXJcclxuXHRcdCAqIFxyXG5cdFx0ICogQHNpbmNlIDIuMFxyXG5cdFx0ICovXHJcblx0XHR0b2dnbGVzZXRFeHRQb3B1cCA6IGZ1bmN0aW9uKHRvZ2dsZSkge1xyXG5cdFx0XHR2YXIgc2VsZiA9IHRoaXM7XHJcblx0XHRcdHNlbGYudmlld3NBY3RpdmUuZXh0ZW5zaW9uc1BvcHVwRWxlbWVudCA9IHRvZ2dsZS5jaGVja0V4dGVuc2lvbjtcclxuXHRcdH0sXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBTaG9ydGNvZGUgR2xvYmFsIExheW91dCBTZXR0aW5nc1xyXG5cdFx0ICpcclxuXHRcdCAqIEBzaW5jZSAyLjBcclxuXHRcdCAqL1xyXG5cdFx0cmVnZW5lcmF0ZUxheW91dCA6IGZ1bmN0aW9uKHNldHRpbmdJRCkge1xyXG5cdFx0XHR2YXIgc2VsZiA9IHRoaXMsXHJcblx0XHRcdFx0cmVnZW5lcmF0ZUZlZWRIVE1MID0gXHRbXHJcblx0XHRcdFx0XHQnbGF5b3V0J1xyXG5cdFx0XHRcdF0sXHJcblx0XHRcdFx0cmVsYXlvdXRGZWVkID0gW1xyXG5cdFx0XHRcdFx0J2xheW91dCcsXHJcblx0XHRcdFx0XHQnY2Fyb3VzZWxhcnJvd3MnLFxyXG5cdFx0XHRcdFx0J2Nhcm91c2VscGFnJyxcclxuXHRcdFx0XHRcdCdjYXJvdXNlbGF1dG9wbGF5JyxcclxuXHRcdFx0XHRcdCdjYXJvdXNlbHRpbWUnLFxyXG5cdFx0XHRcdFx0J2Nhcm91c2VsbG9vcCcsXHJcblx0XHRcdFx0XHQnY2Fyb3VzZWxyb3dzJyxcclxuXHRcdFx0XHRcdCdjb2xzJyxcclxuXHRcdFx0XHRcdCdjb2xzdGFibGV0JyxcclxuXHRcdFx0XHRcdCdjb2xzbW9iaWxlJyxcclxuXHRcdFx0XHRcdCdpbWFnZXBhZGRpbmcnXHJcblx0XHRcdFx0XTtcclxuXHRcdFx0aWYoIHJlbGF5b3V0RmVlZC5pbmNsdWRlcyggc2V0dGluZ0lEICkgKXtcclxuXHRcdFx0XHRzZXRUaW1lb3V0KGZ1bmN0aW9uKCl7XHJcblx0XHRcdFx0XHRzZWxmLnNldFNob3J0Y29kZUdsb2JhbFNldHRpbmdzKHRydWUpO1xyXG5cdFx0XHRcdH0sIDIwMClcclxuXHRcdFx0fVxyXG5cdFx0fSxcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEJhY2sgdG8gYWxsIGZlZWRzXHJcblx0XHQgKlxyXG5cdFx0ICogQHNpbmNlIDIuMFxyXG5cdFx0ICovXHJcblx0XHRiYWNrVG9BbGxGZWVkcyA6IGZ1bmN0aW9uKCkge1xyXG5cdFx0XHR2YXIgc2VsZiA9IHRoaXM7XHJcblx0XHRcdGlmICggSlNPTi5zdHJpbmdpZnkoc2VsZi5jdXN0b21pemVyRmVlZERhdGFJbml0aWFsKSA9PT0gSlNPTi5zdHJpbmdpZnkoc2VsZi5jdXN0b21pemVyRmVlZERhdGEpICkge1xyXG5cdFx0XHRcdHdpbmRvdy5sb2NhdGlvbiA9IHNlbGYuYnVpbGRlclVybDtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRzZWxmLm9wZW5EaWFsb2dCb3goJ2JhY2tBbGxUb0ZlZWQnKTtcclxuXHRcdFx0fVxyXG5cdFx0fSxcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIE9wZW4gRGlhbG9nIEJveFxyXG5cdFx0ICpcclxuXHRcdCAqIEBzaW5jZSAyLjBcclxuXHRcdCAqL1xyXG5cdFx0b3BlbkRpYWxvZ0JveCA6IGZ1bmN0aW9uKHR5cGUsIGFyZ3MgPSBbXSl7XHJcblx0XHRcdHZhciBzZWxmID0gdGhpcyxcclxuXHRcdFx0XHRoZWFkaW5nID0gc2VsZi5kaWFsb2dCb3hQb3B1cFNjcmVlblt0eXBlXS5oZWFkaW5nLFxyXG5cdFx0XHRcdGRlc2NyaXB0aW9uID0gc2VsZi5kaWFsb2dCb3hQb3B1cFNjcmVlblt0eXBlXS5kZXNjcmlwdGlvbixcclxuXHRcdFx0XHRjdXN0b21CdXR0b25zID0gc2VsZi5kaWFsb2dCb3hQb3B1cFNjcmVlblt0eXBlXS5jdXN0b21CdXR0b25zO1xyXG5cdFx0XHRzd2l0Y2ggKHR5cGUpIHtcclxuXHRcdFx0XHRjYXNlIFwiZGVsZXRlU2luZ2xlRmVlZFwiOlxyXG5cdFx0XHRcdFx0c2VsZi5mZWVkVG9EZWxldGUgPSBhcmdzO1xyXG5cdFx0XHRcdFx0aGVhZGluZyA9IGhlYWRpbmcucmVwbGFjZShcIiNcIiwgc2VsZi5mZWVkVG9EZWxldGUuZmVlZF9uYW1lKTtcclxuXHRcdFx0XHRcdGJyZWFrO1xyXG5cdFx0XHR9XHJcblx0XHRcdHNlbGYuZGlhbG9nQm94ID0ge1xyXG5cdFx0XHRcdGFjdGl2ZSA6IHRydWUsXHJcblx0XHRcdFx0dHlwZSA6IHR5cGUsXHJcblx0XHRcdFx0aGVhZGluZyA6IGhlYWRpbmcsXHJcblx0XHRcdFx0ZGVzY3JpcHRpb24gOiBkZXNjcmlwdGlvbixcclxuXHRcdFx0XHRjdXN0b21CdXR0b25zIDogY3VzdG9tQnV0dG9uc1xyXG5cdFx0XHR9O1xyXG5cdFx0XHR3aW5kb3cuZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XHJcblx0XHR9LFxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQ29uZmlybSBEaWFsb2cgQm94IEFjdGlvbnNcclxuXHRcdCAqXHJcblx0XHQgKiBAc2luY2UgMi4wXHJcblx0XHQgKi9cclxuXHRcdGNvbmZpcm1EaWFsb2dBY3Rpb24gOiBmdW5jdGlvbigpe1xyXG5cdFx0XHR2YXIgc2VsZiA9IHRoaXM7XHJcblx0XHRcdHN3aXRjaCAoc2VsZi5kaWFsb2dCb3gudHlwZSkge1xyXG5cdFx0XHRcdGNhc2UgJ2RlbGV0ZVNpbmdsZUZlZWQnOlxyXG5cdFx0XHRcdFx0c2VsZi5mZWVkQWN0aW9uRGVsZXRlKFtzZWxmLmZlZWRUb0RlbGV0ZS5pZF0pO1xyXG5cdFx0XHRcdFx0YnJlYWs7XHJcblx0XHRcdFx0Y2FzZSAnZGVsZXRlTXVsdGlwbGVGZWVkcyc6XHJcblx0XHRcdFx0XHRzZWxmLmZlZWRBY3Rpb25EZWxldGUoc2VsZi5mZWVkc1NlbGVjdGVkKTtcclxuXHRcdFx0XHRcdGJyZWFrO1xyXG5cdFx0XHRcdGNhc2UgJ2JhY2tBbGxUb0ZlZWQnOlxyXG5cdFx0XHRcdFx0d2luZG93LmxvY2F0aW9uID0gc2VsZi5idWlsZGVyVXJsO1xyXG5cdFx0XHRcdFx0YnJlYWs7XHJcblx0XHRcdH1cclxuXHRcdH0sXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBEZWxldGUgRmVlZFxyXG5cdFx0ICpcclxuXHRcdCAqIEBzaW5jZSAyLjBcclxuXHRcdCAqL1xyXG5cdFx0ZmVlZEFjdGlvbkRlbGV0ZSA6IGZ1bmN0aW9uKGZlZWRzX2lkcyl7XHJcblx0XHRcdHZhciBzZWxmID0gdGhpcyxcclxuXHRcdFx0XHRmZWVkc0RlbGV0ZURhdGEgPSB7XHJcblx0XHRcdFx0XHRhY3Rpb24gOiAnc2J5X2ZlZWRfc2F2ZXJfbWFuYWdlcl9kZWxldGVfZmVlZHMnLFxyXG5cdFx0XHRcdFx0ZmVlZHNfaWRzIDogZmVlZHNfaWRzXHJcblx0XHRcdFx0fTtcclxuXHRcdFx0c2VsZi5hamF4UG9zdChmZWVkc0RlbGV0ZURhdGEsIGZ1bmN0aW9uKF9yZWYpe1xyXG5cdFx0XHRcdHZhciBkYXRhID0gX3JlZi5kYXRhO1xyXG5cdFx0XHRcdHNlbGYuZmVlZHNMaXN0ID0gT2JqZWN0LnZhbHVlcyhPYmplY3QuYXNzaWduKHt9LCBkYXRhKSk7XHJcblx0XHRcdFx0c2VsZi5mZWVkc1NlbGVjdGVkID0gW107XHJcblx0XHRcdH0pO1xyXG5cdFx0fSxcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEVuYWJsZSAmIFNob3cgQ29sb3IgUGlja2VyXHJcblx0XHQgKlxyXG5cdFx0ICogQHNpbmNlIDIuMFxyXG5cdFx0ICovXHJcblx0XHRzaG93Q29sb3JQaWNrZXJQb3NwdXAgOiBmdW5jdGlvbihjb250cm9sSWQpe1xyXG5cdFx0XHR0aGlzLmN1c3RvbWl6ZXJTY3JlZW5zLmFjdGl2ZUNvbG9yUGlja2VyID0gY29udHJvbElkO1xyXG5cdFx0fSxcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEhpZGUgQ29sb3IgUGlja2VyXHJcblx0XHQgKlxyXG5cdFx0ICogQHNpbmNlIDIuMFxyXG5cdFx0ICovXHJcblx0XHRoaWRlQ29sb3JQaWNrZXJQb3B1cCA6IGZ1bmN0aW9uKCl7XHJcblx0XHRcdHRoaXMuY3VzdG9taXplclNjcmVlbnMuYWN0aXZlQ29sb3JQaWNrZXIgPSBudWxsO1xyXG5cdFx0fSxcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEdldCBGZWVkIFByZXZpZXcgR2xvYmFsIENTUyBDbGFzc1xyXG5cdFx0ICpcclxuXHRcdCAqIEBzaW5jZSAyLjBcclxuXHRcdCAqIEByZXR1cm4gU3RyaW5nXHJcblx0XHQgKi9cclxuXHRcdGdldFBhbGV0dGVDbGFzcyA6IGZ1bmN0aW9uKGNvbnRleHQgPSAnJyl7XHJcblx0XHRcdHZhciBzZWxmID0gdGhpcyxcclxuXHRcdFx0XHRjb2xvclBhbGV0dGUgPSBzZWxmLmN1c3RvbWl6ZXJGZWVkRGF0YS5zZXR0aW5ncy5jb2xvcnBhbGV0dGU7XHJcblxyXG5cdFx0XHRpZihzZWxmLmNoZWNrTm90RW1wdHkoIGNvbG9yUGFsZXR0ZSApKXtcclxuXHRcdFx0XHR2YXIgZmVlZElEID0gY29sb3JQYWxldHRlID09PSAnY3VzdG9tJyAgPyAoJ18nICsgc2VsZi5jdXN0b21pemVyRmVlZERhdGEuZmVlZF9pbmZvLmlkKSAgOiAnJztcclxuXHRcdFx0XHRjb25zb2xlLmxvZyhjb2xvclBhbGV0dGUgIT09ICdpbmhlcml0JyA/ICcgc2J5JyArIGNvbnRleHQgKyAnX3BhbGV0dGVfJyArIGNvbG9yUGFsZXR0ZSArIGZlZWRJRDogJycpO1xyXG5cdFx0XHRcdHJldHVybiBjb2xvclBhbGV0dGUgIT09ICdpbmhlcml0JyA/ICcgc2J5JyArIGNvbnRleHQgKyAnX3BhbGV0dGVfJyArIGNvbG9yUGFsZXR0ZSArIGZlZWRJRDogJyc7XHJcblx0XHRcdH1cclxuXHRcdFx0cmV0dXJuICcnO1xyXG5cdFx0fSxcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIENoZWNrIGlmIFZhbHVlIGlzIEVtcHR5XHJcblx0XHQgKlxyXG5cdFx0ICogQHNpbmNlIDIuMFxyXG5cdFx0ICpcclxuXHRcdCAqIEByZXR1cm4gYm9vbGVhblxyXG5cdFx0ICovXHJcblx0XHRjaGVja05vdEVtcHR5IDogZnVuY3Rpb24odmFsdWUpe1xyXG5cdFx0XHRyZXR1cm4gdmFsdWUgIT0gbnVsbCAmJiB2YWx1ZS5yZXBsYWNlKC8gL2dpLCcnKSAhPSAnJztcclxuXHRcdH0sXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBHZXQgZmVlZCBjb250YWluZXIgY2xhc3NcclxuXHRcdCAqXHJcblx0XHQgKiBAc2luY2UgMi4wXHJcblx0XHQgKlxyXG5cdFx0ICogQHJldHVybnMgc3RyaW5nXHJcblx0XHQgKi9cclxuXHRcdGdldEZlZWRDb250YWluZXJDbGFzc2VzOiBmdW5jdGlvbigpIHtcclxuXHRcdFx0bGV0IHNlbGYgPSB0aGlzO1xyXG5cdFx0XHRsZXQgY2xhc3NlcyA9IFtcclxuXHRcdFx0XHQnc2JfeW91dHViZScsXHJcblx0XHRcdFx0J3NieV9sYXlvdXRfJyArIHNlbGYuY3VzdG9taXplckZlZWREYXRhLnNldHRpbmdzLmxheW91dCxcclxuXHRcdFx0XHQnc2J5X2NvbF8nICsgc2VsZi5nZXRDb2xTZXR0aW5ncygpLFxyXG5cdFx0XHRcdCdzYnlfbW9iX2NvbF8nICsgc2VsZi5nZXRNb2JDb2xTZXR0aW5ncygpLFxyXG5cdFx0XHRcdCdzYnlfcGFsZXR0ZV8nICsgc2VsZi5nZXRDb2xvclBhbGV0dGVDbGFzcygpLFxyXG5cdFx0XHRdO1xyXG5cdFx0XHRyZXR1cm4gY2xhc3Nlcy5qb2luKCcgJyk7XHJcblx0XHR9LFxyXG5cclxuXHRcdGdldENvbG9yUGFsZXR0ZUNsYXNzIDogZnVuY3Rpb24oKSB7XHJcblx0XHRcdGxldCBzZWxmID0gdGhpcztcclxuXHRcdFx0aWYgKCBzZWxmLmN1c3RvbWl6ZXJGZWVkRGF0YS5zZXR0aW5ncy5jb2xvcnBhbGV0dGUgPT0gJ2N1c3RvbScgKSB7XHJcblx0XHRcdFx0cmV0dXJuIHNlbGYuY3VzdG9taXplckZlZWREYXRhLnNldHRpbmdzLmNvbG9ycGFsZXR0ZSArICdfJyArIHNlbGYuY3VzdG9taXplckZlZWREYXRhLmZlZWRfaW5mby5pZDtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRyZXR1cm4gc2VsZi5jdXN0b21pemVyRmVlZERhdGEuc2V0dGluZ3MuY29sb3JwYWxldHRlO1xyXG5cdFx0XHR9XHJcblx0XHR9LFxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogR2V0IENvbCBTZXR0aW5nc1xyXG5cdFx0ICpcclxuXHRcdCAqIEBzaW5jZSAyLjBcclxuXHRcdCAqL1xyXG5cdFx0Z2V0Q29sU2V0dGluZ3M6IGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRsZXQgc2VsZiA9IHRoaXM7XHJcblxyXG5cdFx0XHRpZiAoIHNlbGYuY3VzdG9taXplckZlZWREYXRhLnNldHRpbmdzWydsYXlvdXQnXSA9PSAnbGlzdCcgfHwgc2VsZi5jdXN0b21pemVyU2NyZWVucy5wcmV2aWV3U2NyZWVuID09PSAnbW9iaWxlJyApIHtcclxuXHRcdFx0XHRyZXR1cm4gMDtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0aWYgKCBzZWxmLmN1c3RvbWl6ZXJGZWVkRGF0YS5zZXR0aW5nc1snY29scyddICkge1xyXG5cdFx0XHRcdHJldHVybiBzZWxmLmN1c3RvbWl6ZXJGZWVkRGF0YS5zZXR0aW5nc1snY29scyddO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRyZXR1cm4gMDtcclxuXHRcdH0sXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBHZXQgTW9iIENvbCBTZXR0aW5nc1xyXG5cdFx0ICpcclxuXHRcdCAqIEBzaW5jZSAyLjBcclxuXHRcdCAqL1xyXG5cdFx0Z2V0TW9iQ29sU2V0dGluZ3M6IGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRsZXQgc2VsZiA9IHRoaXM7XHJcblxyXG5cdFx0XHRpZiAoIHNlbGYuY3VzdG9taXplckZlZWREYXRhLnNldHRpbmdzWydsYXlvdXQnXSA9PSAnbGlzdCcgKSB7XHJcblx0XHRcdFx0cmV0dXJuIDA7XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCBzZWxmLmN1c3RvbWl6ZXJGZWVkRGF0YS5zZXR0aW5nc1snY29sc21vYmlsZSddICkge1xyXG5cdFx0XHRcdHJldHVybiBzZWxmLmN1c3RvbWl6ZXJGZWVkRGF0YS5zZXR0aW5nc1snY29sc21vYmlsZSddO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRyZXR1cm4gMDtcclxuXHRcdH0sXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBDaGVjayBpZiBoZWFkZXIgc3Vic2NyaWJlcnMgbmVlZHMgdG8gc2hvd1xyXG5cdFx0ICpcclxuXHRcdCAqIEBzaW5jZSAyLjBcclxuXHRcdCAqL1xyXG5cdFx0Y2hlY2tTaG91bGRTaG93U3Vic2NyaWJlcnM6IGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRyZXR1cm4gdGhpcy5jdXN0b21pemVyRmVlZERhdGEuc2V0dGluZ3Muc2hvd3N1YnNjcmliZSA9PSB0cnVlID8gXCJzaG93blwiIDogJyc7XHJcblx0XHR9LFxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQ2hlY2sgaWYgRGF0YSBTZXR0aW5nIGlzIEVuYWJsZWRcclxuXHRcdCAqXHJcblx0XHQgKiBAc2luY2UgMi4wXHJcblx0XHQgKlxyXG5cdFx0ICogQHJldHVybiBib29sZWFuXHJcblx0XHQgKi9cclxuXHRcdHZhbHVlSXNFbmFibGVkIDogZnVuY3Rpb24odmFsdWUpe1xyXG5cdFx0XHRyZXR1cm4gdmFsdWUgPT0gMSB8fCB2YWx1ZSA9PSB0cnVlIHx8IHZhbHVlID09ICd0cnVlJyB8fCB2YWx1ZSA9PSAnb24nO1xyXG5cdFx0fSxcclxuXHJcblx0XHQvL0NoYW5nZSBTd2l0Y2hlciBTZXR0aW5nc1xyXG5cdFx0Y2hhbmdlU3dpdGNoZXJTZXR0aW5nVmFsdWUgOiBmdW5jdGlvbihzZXR0aW5nSUQsIG9uVmFsdWUsIG9mZlZhbHVlLCBhamF4QWN0aW9uID0gZmFsc2UsIGV4dGVuc2lvbikge1xyXG5cdFx0XHR2YXIgc2VsZiA9IHRoaXM7XHJcblx0XHRcdGNvbnNvbGUubG9nKGV4dGVuc2lvbik7XHJcblx0XHRcdGlmIChPYmplY3Qua2V5cyhzZWxmLmluQWN0aXZlRXh0ZW5zaW9ucykuaW5jbHVkZXMoc2V0dGluZ0lEKSkge1xyXG5cdFx0XHRcdHNlbGYudmlld3NBY3RpdmUuZXh0ZW5zaW9uc1BvcHVwRWxlbWVudCA9IHNlbGYuaW5BY3RpdmVFeHRlbnNpb25zW3NldHRpbmdJRF07XHJcblx0XHRcdH1cclxuXHRcdFx0c2VsZi5jdXN0b21pemVyRmVlZERhdGEuc2V0dGluZ3Nbc2V0dGluZ0lEXSA9IHNlbGYuY3VzdG9taXplckZlZWREYXRhLnNldHRpbmdzW3NldHRpbmdJRF0gPT0gb25WYWx1ZSA/IG9mZlZhbHVlIDogb25WYWx1ZTtcclxuXHRcdFx0aWYoYWpheEFjdGlvbiAhPT0gZmFsc2Upe1xyXG5cdFx0XHRcdHNlbGYuY3VzdG9taXplckNvbnRyb2xBamF4QWN0aW9uKGFqYXhBY3Rpb24pO1xyXG5cdFx0XHR9XHJcblx0XHRcdHNlbGYucmVnZW5lcmF0ZUxheW91dChzZXR0aW5nSUQpO1xyXG5cdFx0fSxcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFBhcnNlIEpTT05cclxuXHRcdCAqXHJcblx0XHQgKiBAc2luY2UgMi4wXHJcblx0XHQgKlxyXG5cdFx0ICogQHJldHVybiBqc29uT2JqZWN0IC8gQm9vbGVhblxyXG5cdFx0ICovXHJcblx0XHRqc29uUGFyc2UgOiBmdW5jdGlvbihqc29uU3RyaW5nKXtcclxuXHRcdFx0dHJ5IHtcclxuXHRcdFx0XHRyZXR1cm4gSlNPTi5wYXJzZShqc29uU3RyaW5nKTtcclxuXHRcdFx0fSBjYXRjaChlKSB7XHJcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0XHR9XHJcblx0XHR9LFxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogR2V0IGN1c3RvbSBoZWFkZXIgdGV4dFxyXG5cdFx0ICpcclxuXHRcdCAqIEBzaW5jZSAyLjBcclxuXHRcdCAqL1xyXG5cdFx0Z2V0Q3VzdG9tSGVhZGVyVGV4dCA6IGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRyZXR1cm4gdGhpcy5jdXN0b21pemVyRmVlZERhdGEuc2V0dGluZ3MuY3VzdG9taGVhZGVydGV4dDtcclxuXHRcdH0sXHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIFNob3VsZCBzaG93IHRoZSBzdGFuZGFyZCBoZWFkZXJcclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEBzaW5jZSAyLjBcclxuICAgICAgICAgKi9cclxuICAgICAgICBzaG91bGRTaG93U3RhbmRhcmRIZWFkZXI6IGZ1bmN0aW9uKCkge1xyXG4gICAgICAgICAgICBsZXQgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgICAgIHJldHVybiBzZWxmLmN1c3RvbWl6ZXJGZWVkRGF0YS5zZXR0aW5ncy5zaG93aGVhZGVyICYmIHNlbGYuY3VzdG9taXplckZlZWREYXRhLnNldHRpbmdzLmhlYWRlcnN0eWxlID09PSAnc3RhbmRhcmQnO1xyXG4gICAgICAgIH0sXHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIFNob3VsZCBzaG93IHRoZSB0ZXh0IHN0eWxlIGhlYWRlclxyXG4gICAgICAgICAqXHJcbiAgICAgICAgICogQHNpbmNlIDIuMFxyXG4gICAgICAgICAqL1xyXG4gICAgICAgIHNob3VsZFNob3dUZXh0SGVhZGVyOiBmdW5jdGlvbigpIHtcclxuICAgICAgICAgICAgbGV0IHNlbGYgPSB0aGlzO1xyXG4gICAgICAgICAgICByZXR1cm4gc2VsZi5jdXN0b21pemVyRmVlZERhdGEuc2V0dGluZ3Muc2hvd2hlYWRlciAmJiBzZWxmLmN1c3RvbWl6ZXJGZWVkRGF0YS5zZXR0aW5ncy5oZWFkZXJzdHlsZSA9PT0gJ3RleHQnO1xyXG4gICAgICAgIH0sXHJcblxyXG5cclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEdldCBmbGFncyBhdHRyaWJ1dGVzXHJcblx0XHQgKlxyXG5cdFx0ICogQHNpbmNlIDIuMFxyXG5cdFx0ICovXHJcblx0XHRnZXRGbGFnc0F0dHIgOiBmdW5jdGlvbiggKSB7XHJcblx0XHRcdGxldCBzZWxmID0gdGhpcyxcclxuXHRcdFx0XHRmbGFncyA9IFtdO1xyXG5cclxuXHRcdFx0aWYgKCBzZWxmLmN1c3RvbWl6ZXJGZWVkRGF0YS5zZXR0aW5nc1snZGlzYWJsZV9yZXNpemUnXSApIHtcclxuXHRcdFx0XHRmbGFncy5wdXNoKCdyZXNpemVEaXNhYmxlJyk7XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCBzZWxmLmN1c3RvbWl6ZXJGZWVkRGF0YS5zZXR0aW5nc1snZmF2b3JfbG9jYWwnXSApIHtcclxuXHRcdFx0XHRmbGFncy5wdXNoKCdmYXZvckxvY2FsJyk7XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCBzZWxmLmN1c3RvbWl6ZXJGZWVkRGF0YS5zZXR0aW5nc1snZGlzYWJsZV9qc19pbWFnZV9sb2FkaW5nJ10gKSB7XHJcblx0XHRcdFx0ZmxhZ3MucHVzaCgnaW1hZ2VMb2FkRGlzYWJsZScpO1xyXG5cdFx0XHR9XHJcblx0XHRcdGlmICggc2VsZi5jdXN0b21pemVyRmVlZERhdGEuc2V0dGluZ3NbJ2FqYXhfcG9zdF9sb2FkJ10gKSB7XHJcblx0XHRcdFx0ZmxhZ3MucHVzaCgnYWpheFBvc3RMb2FkJyk7XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCBzZWxmLmN1c3RvbWl6ZXJGZWVkRGF0YS5zZXR0aW5nc1sncGxheWVycmF0aW8nXSA9PT0gJzM6NCcgKSB7XHJcblx0XHRcdFx0ZmxhZ3MucHVzaCgnbmFycm93UGxheWVyJyk7XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCBzZWxmLmN1c3RvbWl6ZXJGZWVkRGF0YS5zZXR0aW5nc1snZGlzYWJsZWNkbiddICkge1xyXG5cdFx0XHRcdGZsYWdzLnB1c2goJ2Rpc2FibGVjZG4nKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0cmV0dXJuIGZsYWdzLnRvU3RyaW5nKCk7XHJcblx0XHR9LFxyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBTaG91bGQgc2hvdyBnYWxsZXJ5IGxheW91dCBwbGF5ZXJcclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEBzaW5jZSAyLjBcclxuICAgICAgICAgKi9cclxuICAgICAgICBzaG91bGRTaG93UGxheWVyIDogZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICAgICAgaWYgKCBzZWxmLmN1c3RvbWl6ZXJGZWVkRGF0YS5zZXR0aW5ncy5sYXlvdXQgIT0gJ2dhbGxlcnknICkge1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIHJldHVybiB0cnVlO1xyXG4gICAgICAgIH0sXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBTd2l0Y2ggdG8gVmlkZW9zIHNlY3Rpb25zXHJcblx0XHQgKiBGcm9tIEZlZWQgTGF5b3V0IHNlY3Rpb24gYm90dG9tIGxpbmtcclxuXHRcdCAqXHJcblx0XHQgKiBAc2luY2UgMi4wXHJcblx0XHQgKi9cclxuXHRcdHN3aXRjaFRvVmlkZW9zU2VjdGlvbjogZnVuY3Rpb24oKSB7XHJcblx0XHRcdHZhciBzZWxmID0gdGhpcztcclxuXHRcdFx0c2VsZi5jdXN0b21pemVyU2NyZWVucy5wYXJlbnRBY3RpdmVTZWN0aW9uID0gbnVsbDtcclxuXHRcdFx0c2VsZi5jdXN0b21pemVyU2NyZWVucy5wYXJlbnRBY3RpdmVTZWN0aW9uRGF0YSA9IG51bGw7XHJcblx0XHRcdHNlbGYuY3VzdG9taXplclNjcmVlbnMuYWN0aXZlU2VjdGlvbiA9ICdjdXN0b21pemVfdmlkZW9zJztcclxuXHRcdFx0c2VsZi5jdXN0b21pemVyU2NyZWVucy5hY3RpdmVTZWN0aW9uRGF0YSA9IHNlbGYuY3VzdG9taXplclNpZGViYXJCdWlsZGVyLmN1c3RvbWl6ZS5zZWN0aW9ucy5jdXN0b21pemVfdmlkZW9zO1xyXG5cdFx0fSxcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIFNob3J0Y29kZSBHbG9iYWwgTGF5b3V0IFNldHRpbmdzXHJcblx0XHQgKlxyXG5cdFx0ICogQHNpbmNlIDIuMFxyXG5cdFx0ICovXHJcblx0XHRzZXRTaG9ydGNvZGVHbG9iYWxTZXR0aW5ncyA6IGZ1bmN0aW9uKGZseVByZXZpZXcgPSBmYWxzZSl7XHJcblx0XHRcdGxldCBzZWxmID0gdGhpcyxcclxuXHRcdFx0XHR5b3V0dWJlRmVlZCA9IGpRdWVyeShcImh0bWxcIikuZmluZChcIi5zYl95b3V0dWJlXCIpLFxyXG5cdFx0XHRcdGZlZWRTZXR0aW5ncyA9IHNlbGYuanNvblBhcnNlKHlvdXR1YmVGZWVkLmF0dHIoJ2RhdGEtb3B0aW9ucycpKSxcclxuXHRcdFx0XHRjdXN0b21pemVyU2V0dGluZ3MgPSBzZWxmLmN1c3RvbWl6ZXJGZWVkRGF0YS5zZXR0aW5ncztcclxuXHJcblx0XHRcdFx0aWYgKCAheW91dHViZUZlZWQubGVuZ3RoICkge1xyXG5cdFx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0aWYoIGN1c3RvbWl6ZXJTZXR0aW5ncy5sYXlvdXQgPT09ICdjYXJvdXNlbCcgKXtcclxuXHRcdFx0XHRsZXQgYXJyb3dzIFx0XHQ9IHNlbGYudmFsdWVJc0VuYWJsZWQoIGN1c3RvbWl6ZXJTZXR0aW5nc1snY2Fyb3VzZWxhcnJvd3MnXSApLFxyXG5cdFx0XHRcdFx0cGFnIFx0XHQ9IHNlbGYudmFsdWVJc0VuYWJsZWQoIGN1c3RvbWl6ZXJTZXR0aW5nc1snY2Fyb3VzZWxwYWcnXSApLFxyXG5cdFx0XHRcdFx0YXV0b3BsYXkgXHQ9IHNlbGYudmFsdWVJc0VuYWJsZWQoIGN1c3RvbWl6ZXJTZXR0aW5nc1snY2Fyb3VzZWxhdXRvcGxheSddICksXHJcblx0XHRcdFx0XHR0aW1lIFx0XHQ9IGF1dG9wbGF5ID8gcGFyc2VJbnQoY3VzdG9taXplclNldHRpbmdzWydjYXJvdXNlbHRpbWUnXSkgOiBmYWxzZSxcclxuXHRcdFx0XHRcdGxvb3AgXHRcdD0gc2VsZi5jaGVja05vdEVtcHR5KGN1c3RvbWl6ZXJTZXR0aW5nc1snY2Fyb3VzZWxsb29wJ10pICYmIGN1c3RvbWl6ZXJTZXR0aW5nc1snY2Fyb3VzZWxsb29wJ10gIT09ICdyZXdpbmQnID8gZmFsc2UgOiB0cnVlLFxyXG5cdFx0XHRcdFx0cm93cyBcdFx0PSBjdXN0b21pemVyU2V0dGluZ3NbJ2Nhcm91c2Vscm93cyddICA/IE1hdGgubWluKCBwYXJzZUludChjdXN0b21pemVyU2V0dGluZ3NbJ2Nhcm91c2Vscm93cyddKSwgMikgOiAxO1xyXG5cdFx0XHRcdGRlbGV0ZSBmZWVkU2V0dGluZ3NbJ2dhbGxlcnknXTtcclxuXHRcdFx0XHRkZWxldGUgZmVlZFNldHRpbmdzWydtYXNvbnJ5J107XHJcblx0XHRcdFx0ZGVsZXRlIGZlZWRTZXR0aW5nc1snZ3JpZCddO1xyXG5cdFx0XHRcdGZlZWRTZXR0aW5nc1snY2Fyb3VzZWwnXSA9IFthcnJvd3MsIHBhZywgYXV0b3BsYXksIHRpbWUsIGxvb3AsIHJvd3NdO1xyXG5cdFx0XHR9XHJcblx0XHRcdGVsc2UgaWYoY3VzdG9taXplclNldHRpbmdzLmxheW91dCA9PSAnZ3JpZCcpe1xyXG5cdFx0XHRcdGRlbGV0ZSBmZWVkU2V0dGluZ3NbJ2dhbGxlcnknXTtcclxuXHRcdFx0XHRkZWxldGUgZmVlZFNldHRpbmdzWydtYXNvbnJ5J107XHJcblx0XHRcdH1cclxuXHRcdFx0ZWxzZSBpZihjdXN0b21pemVyU2V0dGluZ3MubGF5b3V0ID09ICdtYXNvbnJ5Jyl7XHJcblx0XHRcdFx0ZGVsZXRlIGZlZWRTZXR0aW5nc1snZ2FsbGVyeSddO1xyXG5cdFx0XHRcdGRlbGV0ZSBmZWVkU2V0dGluZ3NbJ2dyaWQnXTtcclxuXHRcdFx0fVxyXG5cdFx0XHRlbHNlIGlmKGN1c3RvbWl6ZXJTZXR0aW5ncy5sYXlvdXQgPT0gJ2dhbGxlcnknKXtcclxuXHRcdFx0XHRkZWxldGUgZmVlZFNldHRpbmdzWydtYXNvbnJ5J107XHJcblx0XHRcdFx0ZGVsZXRlIGZlZWRTZXR0aW5nc1snZ3JpZCddO1xyXG5cdFx0XHR9XHJcblxyXG5cdFx0XHRpZihjdXN0b21pemVyU2V0dGluZ3MubGF5b3V0ICE9PSAnY2Fyb3VzZWwnKXtcclxuXHRcdFx0XHRkZWxldGUgZmVlZFNldHRpbmdzWydjYXJvdXNlbCddO1xyXG5cdFx0XHR9XHJcblx0XHRcdHlvdXR1YmVGZWVkLmF0dHIoXCJkYXRhLW9wdGlvbnNcIiwgSlNPTi5zdHJpbmdpZnkoZmVlZFNldHRpbmdzKSk7XHJcblxyXG5cdFx0XHRpZiAoIHR5cGVvZiB3aW5kb3cuc2J5X2luaXQgIT09ICd1bmRlZmluZWQnICYmIGZseVByZXZpZXcgKSB7XHJcblx0XHRcdFx0Ly9zZXRUaW1lb3V0KGZ1bmN0aW9uKCl7XHJcblx0XHRcdFx0XHR3aW5kb3cuc2J5X2luaXQoKTtcclxuXHRcdFx0XHQvL30sIDIwMDApXHJcblx0XHRcdH1cclxuXHRcdH0sXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBTaG91bGQgU2hvdyBNYW51YWwgQ29ubmVjdFxyXG5cdFx0ICogXHJcblx0XHQgKiBAc2luY2UgMi4wXHJcblx0XHQgKi9cclxuXHRcdHNob3dNYW51YWxDb25uZWN0IDogZnVuY3Rpb24oKSB7XHJcblx0XHRcdHZhciBzZWxmID0gdGhpcztcclxuXHRcdFx0c2VsZi5zaG91bGRTaG93TWFudWFsQ29ubmVjdCA9IHRydWU7XHJcblx0XHRcdHNlbGYuc2hvdWxkU2hvd0ZlZWRBUElCYWNrQnRuID0gdHJ1ZTtcclxuXHRcdH0sXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBTaG91bGQgU2hvdyBNYW51YWwgQ29ubmVjdFxyXG5cdFx0ICogXHJcblx0XHQgKiBAc2luY2UgMi4wXHJcblx0XHQgKi9cclxuXHRcdHNob3dGZWVkU291cmNlTWFudWFsQ29ubmVjdCA6IGZ1bmN0aW9uKCkge1xyXG5cdFx0XHR2YXIgc2VsZiA9IHRoaXM7XHJcblx0XHRcdHNlbGYudmlld3NBY3RpdmUuYWNjb3VudEFQSVBvcHVwID0gdHJ1ZTtcclxuXHRcdFx0c2VsZi5zaG91bGRTaG93TWFudWFsQ29ubmVjdCA9IHRydWU7XHJcblx0XHR9LFxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogU2hvdyBBUEkgY29ubmVjdCBmb3JtIGluIGZlZWQgY3JlYXRpb24gZmxvd1xyXG5cdFx0ICovXHJcblx0XHRzaG93QVBJQ29ubmVjdEZvcm0gOiBmdW5jdGlvbigpIHtcclxuXHRcdFx0dmFyIHNlbGYgPSB0aGlzO1xyXG5cdFx0XHRzZWxmLnNob3VsZFNob3dGZWVkQVBJRm9ybSA9IHRydWU7XHJcblx0XHRcdHNlbGYuc2hvdWxkU2hvd0ZlZWRBUElCYWNrQnRuID0gdHJ1ZTtcclxuXHRcdH0sXHJcblx0XHJcblx0XHQvKipcclxuXHRcdCAqIFNob3cgdGhlIGxpbWl0YXRpb25zIG9mIGNvbm5lY3Rpbmcgd2l0aCBZb3VUdWJlIEFjY291bnRcclxuXHRcdCAqIEBzaW5jZSAyLjNcclxuXHRcdCAqL1xyXG5cdFx0c2hvd1lUQWNjb3VudExpbWl0YXRpb25zIDogZnVuY3Rpb24oKSB7XHJcblx0XHRcdHZhciBzZWxmID0gdGhpcztcclxuXHRcdFx0c2VsZi5zaG93U2hvd1lUQWNjb3VudFdhcm5pbmcgPSB0cnVlO1xyXG5cdFx0fSxcclxuXHJcblx0XHRiYWNrVG9BcGlQb3B1cCA6IGZ1bmN0aW9uKCkge1xyXG5cdFx0XHR2YXIgc2VsZiA9IHRoaXM7XHJcblx0XHRcdHNlbGYuc2hvd1Nob3dZVEFjY291bnRXYXJuaW5nID0gZmFsc2U7XHJcblx0XHRcdHNlbGYuc2hvdWxkU2hvd01hbnVhbENvbm5lY3QgPSBmYWxzZTtcclxuXHRcdFx0c2VsZi5zaG91bGRTaG93RmVlZEFQSUZvcm0gPSBmYWxzZTtcclxuXHRcdFx0c2VsZi5zaG91bGRTaG93RmVlZEFQSUJhY2tCdG4gPSBmYWxzZTtcclxuXHRcdH0sXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBTaG93IEFQSSBjb25uZWN0IGZvcm0gaW4gZmVlZCBjcmVhdGlvbiBmbG93XHJcblx0XHQgKi9cclxuXHRcdGhpZGVBUElDb25uZWN0Rm9ybSA6IGZ1bmN0aW9uKCkge1xyXG5cdFx0XHR2YXIgc2VsZiA9IHRoaXM7XHJcblx0XHRcdHNlbGYuc2hvdWxkU2hvd01hbnVhbENvbm5lY3QgPSBmYWxzZTtcclxuXHRcdFx0c2VsZi5zaG91bGRTaG93RmVlZEFQSUZvcm0gPSBmYWxzZTtcclxuXHRcdFx0c2VsZi5zaG91bGRTaG93RmVlZEFQSUJhY2tCdG4gPSBmYWxzZTtcclxuXHRcdH0sXHJcblxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQWRkIEFQSSBLZXkgZnJvbSB0aGUgc2VsZWN0IGZlZWQgZmxvd1xyXG5cdFx0ICogXHJcblx0XHQgKiBAc2luY2UgMi4wXHJcblx0XHQgKi9cclxuXHRcdGFkZEFQSUtleSA6IGZ1bmN0aW9uKCkge1xyXG5cdFx0XHR2YXIgc2VsZiA9IHRoaXM7XHJcblxyXG5cdFx0XHRpZiAoICFzZWxmLnNlbGVjdGVkRmVlZE1vZGVsLmFwaUtleSApIHtcclxuXHRcdFx0XHRzZWxmLmFwaUtleUVycm9yID0gdHJ1ZTtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHJcblx0XHRcdHZhciBzZWxmID0gdGhpcyxcclxuXHRcdFx0XHRhZGRBUElLZXlEYXRhID0ge1xyXG5cdFx0XHRcdFx0YWN0aW9uIDogJ3NieV9hZGRfYXBpX2tleScsXHJcblx0XHRcdFx0XHRhcGkgOiBzZWxmLnNlbGVjdGVkRmVlZE1vZGVsLmFwaUtleVxyXG5cdFx0XHRcdH07XHJcblx0XHRcdHNlbGYuYXBpS2V5QnRuTG9hZGVyID0gdHJ1ZTtcclxuXHRcdFx0c2VsZi5hamF4UG9zdChhZGRBUElLZXlEYXRhLCBmdW5jdGlvbihfcmVmKXtcclxuXHRcdFx0XHR2YXIgZGF0YSA9IF9yZWYuZGF0YTtcclxuXHRcdFx0XHRzZWxmLmFwaUtleUJ0bkxvYWRlciA9IGZhbHNlO1xyXG5cdFx0XHRcdHNlbGYuYXBpS2V5RXJyb3IgPSBmYWxzZTtcclxuXHRcdFx0XHRzZWxmLmFwaUtleVN0YXR1cyA9IHRydWU7XHJcblx0XHRcdFx0c2VsZi5hY3RpdmF0ZVZpZXcoJ2FjY291bnRBUElQb3B1cCcpO1xyXG5cdFx0XHR9KTtcclxuXHRcdH0sXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBBZGQgQWNjZXNzIFRva29lbiBmcm9tIHRoZSBzZWxlY3QgZmVlZCBmbG93XHJcblx0XHQgKiBcclxuXHRcdCAqIEBzaW5jZSAyLjBcclxuXHRcdCAqL1xyXG5cdFx0YWRkQWNjZXNzVG9rZW4gOiBmdW5jdGlvbigpIHtcclxuXHRcdFx0dmFyIHNlbGYgPSB0aGlzO1xyXG5cclxuXHRcdFx0aWYgKCAhc2VsZi5zZWxlY3RlZEZlZWRNb2RlbC5hY2Nlc3NUb2tlbiApIHtcclxuXHRcdFx0XHRzZWxmLmFjY2Vzc1Rva2VuRXJyb3IgPSB0cnVlO1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0dmFyIHNlbGYgPSB0aGlzLFxyXG5cdFx0XHRcdGFkZEFQSUtleURhdGEgPSB7XHJcblx0XHRcdFx0XHRhY3Rpb24gOiAnc2J5X21hbnVhbF9hY2Nlc3NfdG9rZW4nLFxyXG5cdFx0XHRcdFx0c2J5X2FjY2Vzc190b2tlbiA6IHNlbGYuc2VsZWN0ZWRGZWVkTW9kZWwuYWNjZXNzVG9rZW5cclxuXHRcdFx0XHR9O1xyXG5cdFx0XHRzZWxmLmFwaUtleUJ0bkxvYWRlciA9IHRydWU7XHJcblx0XHRcdHNlbGYuYWpheFBvc3QoYWRkQVBJS2V5RGF0YSwgZnVuY3Rpb24oX3JlZil7XHJcblx0XHRcdFx0dmFyIGRhdGEgPSBfcmVmLmRhdGE7XHJcblx0XHRcdFx0c2VsZi5hcGlLZXlCdG5Mb2FkZXIgPSBmYWxzZTtcclxuXHRcdFx0XHRzZWxmLmFjY2Vzc1Rva2VuRXJyb3IgPSBmYWxzZTtcclxuXHRcdFx0XHRzZWxmLmFwaUtleVN0YXR1cyA9IHRydWU7XHJcblx0XHRcdFx0c2VsZi5hY3RpdmF0ZVZpZXcoJ2FjY291bnRBUElQb3B1cCcpO1xyXG5cdFx0XHR9KTtcclxuXHRcdH0sXHJcblxyXG5cdFx0LyoqXHJcblx0XHQgKiBDcmVhdGUgJiBTdWJtaXQgTmV3IEZlZWRcclxuXHRcdCAqXHJcblx0XHQgKiBAc2luY2UgMi4wXHJcblx0XHQgKi9cclxuXHRcdHN1Ym1pdE5ld0ZlZWQgOiBmdW5jdGlvbigpe1xyXG5cdFx0XHR2YXIgc2VsZiA9IHRoaXMsXHJcblx0XHRcdFx0bmV3RmVlZERhdGEgPSB7XHJcblx0XHRcdFx0XHRhY3Rpb24gOiAnc2J5X2ZlZWRfc2F2ZXJfbWFuYWdlcl9idWlsZGVyX3VwZGF0ZScsXHJcblx0XHRcdFx0XHRmZWVkdHlwZSA6IHNlbGYuc2VsZWN0ZWRGZWVkLFxyXG5cdFx0XHRcdFx0ZmVlZHRlbXBsYXRlIDogc2VsZi5zZWxlY3RlZEZlZWRUZW1wbGF0ZSxcclxuXHRcdFx0XHRcdHNlbGVjdGVkRmVlZE1vZGVsIDogc2VsZi5zZWxlY3RlZEZlZWRNb2RlbCxcclxuXHRcdFx0XHRcdG5ld19pbnNlcnQgOiAndHJ1ZScsXHJcblx0XHRcdFx0fTtcclxuXHRcdFx0c2VsZi5mdWxsU2NyZWVuTG9hZGVyID0gdHJ1ZTtcclxuXHRcdFx0c2VsZi5hamF4UG9zdChuZXdGZWVkRGF0YSwgZnVuY3Rpb24oX3JlZil7XHJcblx0XHRcdFx0dmFyIGRhdGEgPSBfcmVmLmRhdGE7XHJcblx0XHRcdFx0aWYoZGF0YS5mZWVkX2lkICYmIGRhdGEuc3VjY2Vzcyl7XHJcblx0XHRcdFx0XHR3aW5kb3cubG9jYXRpb24gPSBzZWxmLmJ1aWxkZXJVcmwgKyAnJmZlZWRfaWQ9JyArIGRhdGEuZmVlZF9pZCArIHNlbGYuc3dfZmVlZF9wYXJhbXMoKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH0pO1xyXG5cdFx0fSxcclxuXHJcblx0XHQvKipcclxuXHRcdCAqIEN1c3RvbSBmaWVsZCBjbGljayBhY3Rpb25cclxuXHRcdCAqIEFjdGlvblxyXG5cdFx0ICogQHNpbmNlIDIuMy4zXHJcblx0XHQgKi9cclxuXHRcdGZpZWxkQ3VzdG9tQ2xpY2tBY3Rpb24gOiBmdW5jdGlvbiggY2xpY2tBY3Rpb24gKXtcclxuXHRcdFx0dmFyIHNlbGYgPSB0aGlzO1xyXG5cdFx0XHRzd2l0Y2ggKGNsaWNrQWN0aW9uKSB7XHJcblx0XHRcdFx0Y2FzZSAnY2xlYXJDb21tZW50Q2FjaGUnOlxyXG5cdFx0XHRcdFx0c2VsZi5jbGVhckNvbW1lbnRDYWNoZSgpO1xyXG5cdFx0XHRcdGJyZWFrO1xyXG5cdFx0XHR9XHJcblx0XHR9LFxyXG5cclxuXHRcdC8qKlxyXG5cdFx0ICogQ2xlYXIgY29tbWVudCBjYWNoZSBhY3Rpb25cclxuXHRcdCAqXHJcblx0XHQgKiBAc2luY2UgMi4zLjNcclxuXHRcdCAqL1xyXG5cdFx0Y2xlYXJDb21tZW50Q2FjaGUgOiBmdW5jdGlvbigpe1xyXG5cdFx0XHR2YXIgc2VsZiA9IHRoaXM7XHJcblx0XHRcdHNlbGYubG9hZGluZ0JhciA9IHRydWU7XHJcblx0XHRcdHZhciBjbGVhckNvbW1lbnRDYWNoZURhdGEgPSB7XHJcblx0XHRcdFx0YWN0aW9uIDogJ3NieV9mZWVkX3NhdmVyX2NsZWFyX2NvbW1lbnRzX2NhY2hlJyxcclxuXHRcdFx0fTtcclxuXHRcdFx0c2VsZi5hamF4UG9zdChjbGVhckNvbW1lbnRDYWNoZURhdGEsIGZ1bmN0aW9uKF9yZWYpe1xyXG5cdFx0XHRcdHZhciBkYXRhID0gX3JlZi5kYXRhO1xyXG5cdFx0XHRcdGlmKCBkYXRhID09PSAnc3VjY2VzcycgKXtcclxuXHRcdFx0XHRcdHNlbGYucHJvY2Vzc05vdGlmaWNhdGlvbihcImNvbW1lbnRDYWNoZUNsZWFyZWRcIik7XHJcblx0XHRcdFx0fWVsc2V7XHJcblx0XHRcdFx0XHRzZWxmLnByb2Nlc3NOb3RpZmljYXRpb24oXCJ1bmtvd25FcnJvclwiKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH0pO1xyXG5cdFx0fSxcclxuXHR9XHJcblx0cmVzb2x2ZShTQl9DdXN0b21pemVyKTtcclxufSk7Il0sIm5hbWVzIjpbImFkZEZpbHRlciIsImFkZEFjdGlvbiIsIlNCX0N1c3RvbWl6ZXIiLCJpbml0UHJvbWlzZSIsIlByb21pc2UiLCJyZXNvbHZlIiwiZXh0cmFEYXRhIiwiX29iamVjdFNwcmVhZCIsImFsbEZlZWRzU2NyZWVuIiwic2JjX2J1aWxkZXIiLCJmZWVkc0xpc3QiLCJmZWVkcyIsImxlZ2FjeUZlZWRzTGlzdCIsImxlZ2FjeUZlZWRzIiwidG9vbHRpcENvbnRlbnQiLCJmZWVkdHlwZXNUb29sdGlwQ29udGVudCIsImZlZWRTZXR0aW5nc0RvbU9wdGlvbnMiLCJzZWxlY3RlZEZlZWRNb2RlbCIsImNoYW5uZWwiLCJwcmVmaWxsZWRDaGFubmVsSWQiLCJwbGF5bGlzdCIsImZhdm9yaXRlcyIsInNlYXJjaCIsImxpdmUiLCJzaW5nbGUiLCJhcGlLZXkiLCJhY2Nlc3NUb2tlbiIsInlvdXR1YmVBY2NvdW50Q29ubmVjdFVSTCIsImNvbm5lY3RTaXRlUGFyYW1ldGVycyIsInlvdXR1YmVBY2NvdW50Q29ubmVjdFBhcmFtZXRlcnMiLCJkaXNtaXNzTGl0ZSIsInlvdXR1YmVfZmVlZF9kaXNtaXNzX2xpdGUiLCJzaG91bGRTaG93RmVlZEFQSUZvcm0iLCJzaG91bGRTaG93TWFudWFsQ29ubmVjdCIsInNob3dTaG93WVRBY2NvdW50V2FybmluZyIsInN3X2ZlZWQiLCJzd19mZWVkX2lkIiwiZXh0cmFNZXRob2RzIiwiY2hhbmdlU2V0dGluZ1ZhbHVlIiwic2V0dGluZ0lEIiwidmFsdWUiLCJkb1Byb2Nlc3MiLCJhcmd1bWVudHMiLCJsZW5ndGgiLCJ1bmRlZmluZWQiLCJhamF4QWN0aW9uIiwic2VsZiIsImN1c3RvbWl6ZXJGZWVkRGF0YSIsInNldHRpbmdzIiwiY3VzdG9taXplckNvbnRyb2xBamF4QWN0aW9uIiwicmVnZW5lcmF0ZUxheW91dCIsImNoZWNrRXh0ZW5zaW9uQWN0aXZlIiwiZXh0ZW5zaW9uIiwiYWN0aXZlRXh0ZW5zaW9ucyIsInNob3VsZFNob3dPdmVybGF5IiwiY29udHJvbCIsInNieUlzUHJvIiwic2J5TGljZW5zZU5vdGljZUFjdGl2ZSIsImNoZWNrRXh0ZW5zaW9uUG9wdXAiLCJoYXNGZWF0dXJlIiwiY29uZGl0aW9uIiwiY2hlY2tFeHRlbnNpb24iLCJjaGVja0V4dGVuc2lvbkRpbW1lZCIsImNoZWNrQ29udHJvbENvbmRpdGlvbiIsInNob3VsZFNob3dUb2dnbGVzZXRDb3ZlciIsInRvZ2dsZSIsInRvZ2dsZXNldEV4dFBvcHVwIiwidmlld3NBY3RpdmUiLCJleHRlbnNpb25zUG9wdXBFbGVtZW50IiwicmVnZW5lcmF0ZUZlZWRIVE1MIiwicmVsYXlvdXRGZWVkIiwiaW5jbHVkZXMiLCJzZXRUaW1lb3V0Iiwic2V0U2hvcnRjb2RlR2xvYmFsU2V0dGluZ3MiLCJiYWNrVG9BbGxGZWVkcyIsIkpTT04iLCJzdHJpbmdpZnkiLCJjdXN0b21pemVyRmVlZERhdGFJbml0aWFsIiwid2luZG93IiwibG9jYXRpb24iLCJidWlsZGVyVXJsIiwib3BlbkRpYWxvZ0JveCIsInR5cGUiLCJhcmdzIiwiaGVhZGluZyIsImRpYWxvZ0JveFBvcHVwU2NyZWVuIiwiZGVzY3JpcHRpb24iLCJjdXN0b21CdXR0b25zIiwiZmVlZFRvRGVsZXRlIiwicmVwbGFjZSIsImZlZWRfbmFtZSIsImRpYWxvZ0JveCIsImFjdGl2ZSIsImV2ZW50Iiwic3RvcFByb3BhZ2F0aW9uIiwiY29uZmlybURpYWxvZ0FjdGlvbiIsImZlZWRBY3Rpb25EZWxldGUiLCJpZCIsImZlZWRzU2VsZWN0ZWQiLCJmZWVkc19pZHMiLCJmZWVkc0RlbGV0ZURhdGEiLCJhY3Rpb24iLCJhamF4UG9zdCIsIl9yZWYiLCJkYXRhIiwiT2JqZWN0IiwidmFsdWVzIiwiYXNzaWduIiwic2hvd0NvbG9yUGlja2VyUG9zcHVwIiwiY29udHJvbElkIiwiY3VzdG9taXplclNjcmVlbnMiLCJhY3RpdmVDb2xvclBpY2tlciIsImhpZGVDb2xvclBpY2tlclBvcHVwIiwiZ2V0UGFsZXR0ZUNsYXNzIiwiY29udGV4dCIsImNvbG9yUGFsZXR0ZSIsImNvbG9ycGFsZXR0ZSIsImNoZWNrTm90RW1wdHkiLCJmZWVkSUQiLCJmZWVkX2luZm8iLCJjb25zb2xlIiwibG9nIiwiZ2V0RmVlZENvbnRhaW5lckNsYXNzZXMiLCJjbGFzc2VzIiwibGF5b3V0IiwiZ2V0Q29sU2V0dGluZ3MiLCJnZXRNb2JDb2xTZXR0aW5ncyIsImdldENvbG9yUGFsZXR0ZUNsYXNzIiwiam9pbiIsInByZXZpZXdTY3JlZW4iLCJjaGVja1Nob3VsZFNob3dTdWJzY3JpYmVycyIsInNob3dzdWJzY3JpYmUiLCJ2YWx1ZUlzRW5hYmxlZCIsImNoYW5nZVN3aXRjaGVyU2V0dGluZ1ZhbHVlIiwib25WYWx1ZSIsIm9mZlZhbHVlIiwia2V5cyIsImluQWN0aXZlRXh0ZW5zaW9ucyIsImpzb25QYXJzZSIsImpzb25TdHJpbmciLCJwYXJzZSIsImUiLCJnZXRDdXN0b21IZWFkZXJUZXh0IiwiY3VzdG9taGVhZGVydGV4dCIsInNob3VsZFNob3dTdGFuZGFyZEhlYWRlciIsInNob3doZWFkZXIiLCJoZWFkZXJzdHlsZSIsInNob3VsZFNob3dUZXh0SGVhZGVyIiwiZ2V0RmxhZ3NBdHRyIiwiZmxhZ3MiLCJwdXNoIiwidG9TdHJpbmciLCJzaG91bGRTaG93UGxheWVyIiwic3dpdGNoVG9WaWRlb3NTZWN0aW9uIiwicGFyZW50QWN0aXZlU2VjdGlvbiIsInBhcmVudEFjdGl2ZVNlY3Rpb25EYXRhIiwiYWN0aXZlU2VjdGlvbiIsImFjdGl2ZVNlY3Rpb25EYXRhIiwiY3VzdG9taXplclNpZGViYXJCdWlsZGVyIiwiY3VzdG9taXplIiwic2VjdGlvbnMiLCJjdXN0b21pemVfdmlkZW9zIiwiZmx5UHJldmlldyIsInlvdXR1YmVGZWVkIiwialF1ZXJ5IiwiZmluZCIsImZlZWRTZXR0aW5ncyIsImF0dHIiLCJjdXN0b21pemVyU2V0dGluZ3MiLCJhcnJvd3MiLCJwYWciLCJhdXRvcGxheSIsInRpbWUiLCJwYXJzZUludCIsImxvb3AiLCJyb3dzIiwiTWF0aCIsIm1pbiIsInNieV9pbml0Iiwic2hvd01hbnVhbENvbm5lY3QiLCJzaG91bGRTaG93RmVlZEFQSUJhY2tCdG4iLCJzaG93RmVlZFNvdXJjZU1hbnVhbENvbm5lY3QiLCJhY2NvdW50QVBJUG9wdXAiLCJzaG93QVBJQ29ubmVjdEZvcm0iLCJzaG93WVRBY2NvdW50TGltaXRhdGlvbnMiLCJiYWNrVG9BcGlQb3B1cCIsImhpZGVBUElDb25uZWN0Rm9ybSIsImFkZEFQSUtleSIsImFwaUtleUVycm9yIiwiYWRkQVBJS2V5RGF0YSIsImFwaSIsImFwaUtleUJ0bkxvYWRlciIsImFwaUtleVN0YXR1cyIsImFjdGl2YXRlVmlldyIsImFkZEFjY2Vzc1Rva2VuIiwiYWNjZXNzVG9rZW5FcnJvciIsInNieV9hY2Nlc3NfdG9rZW4iLCJzdWJtaXROZXdGZWVkIiwibmV3RmVlZERhdGEiLCJmZWVkdHlwZSIsInNlbGVjdGVkRmVlZCIsImZlZWR0ZW1wbGF0ZSIsInNlbGVjdGVkRmVlZFRlbXBsYXRlIiwibmV3X2luc2VydCIsImZ1bGxTY3JlZW5Mb2FkZXIiLCJmZWVkX2lkIiwic3VjY2VzcyIsInN3X2ZlZWRfcGFyYW1zIiwiZmllbGRDdXN0b21DbGlja0FjdGlvbiIsImNsaWNrQWN0aW9uIiwiY2xlYXJDb21tZW50Q2FjaGUiLCJsb2FkaW5nQmFyIiwiY2xlYXJDb21tZW50Q2FjaGVEYXRhIiwicHJvY2Vzc05vdGlmaWNhdGlvbiJdLCJzb3VyY2VSb290IjoiIn0=