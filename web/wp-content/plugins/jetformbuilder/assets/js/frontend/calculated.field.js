/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./frontend/calculated.field/functions.js":
/*!************************************************!*\
  !*** ./frontend/calculated.field/functions.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"isCalculated\": () => (/* binding */ isCalculated)\n/* harmony export */ });\n/**\r\n * @param node {HTMLElement}\r\n * @returns {boolean}\r\n */\nfunction isCalculated(node) {\n  var _node$parentElement$d, _node$parentElement, _node$parentElement$d2, _node$parentElement$d3;\n  return !!((_node$parentElement$d = node === null || node === void 0 ? void 0 : (_node$parentElement = node.parentElement) === null || _node$parentElement === void 0 ? void 0 : (_node$parentElement$d2 = _node$parentElement.dataset) === null || _node$parentElement$d2 === void 0 ? void 0 : (_node$parentElement$d3 = _node$parentElement$d2.formula) === null || _node$parentElement$d3 === void 0 ? void 0 : _node$parentElement$d3.length) !== null && _node$parentElement$d !== void 0 ? _node$parentElement$d : '');\n}\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9mcm9udGVuZC9jYWxjdWxhdGVkLmZpZWxkL2Z1bmN0aW9ucy5qcy5qcyIsIm1hcHBpbmdzIjoiOzs7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxTQUFTQSxZQUFZQSxDQUFFQyxJQUFJLEVBQUc7RUFBQSxJQUFBQyxxQkFBQSxFQUFBQyxtQkFBQSxFQUFBQyxzQkFBQSxFQUFBQyxzQkFBQTtFQUM3QixPQUFPLENBQUMsR0FBQUgscUJBQUEsR0FDUEQsSUFBSSxhQUFKQSxJQUFJLHdCQUFBRSxtQkFBQSxHQUFKRixJQUFJLENBQUVLLGFBQWEsY0FBQUgsbUJBQUEsd0JBQUFDLHNCQUFBLEdBQW5CRCxtQkFBQSxDQUFxQkksT0FBTyxjQUFBSCxzQkFBQSx3QkFBQUMsc0JBQUEsR0FBNUJELHNCQUFBLENBQThCSSxPQUFPLGNBQUFILHNCQUFBLHVCQUFyQ0Esc0JBQUEsQ0FBdUNJLE1BQU0sY0FBQVAscUJBQUEsY0FBQUEscUJBQUEsR0FBSSxFQUFFLENBQ25EO0FBQ0YiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9mcm9udGVuZC9jYWxjdWxhdGVkLmZpZWxkL2Z1bmN0aW9ucy5qcz80ZmE4Il0sInNvdXJjZXNDb250ZW50IjpbIi8qKlxyXG4gKiBAcGFyYW0gbm9kZSB7SFRNTEVsZW1lbnR9XHJcbiAqIEByZXR1cm5zIHtib29sZWFufVxyXG4gKi9cclxuZnVuY3Rpb24gaXNDYWxjdWxhdGVkKCBub2RlICkge1xyXG5cdHJldHVybiAhIShcclxuXHRcdG5vZGU/LnBhcmVudEVsZW1lbnQ/LmRhdGFzZXQ/LmZvcm11bGE/Lmxlbmd0aCA/PyAnJ1xyXG5cdCk7XHJcbn1cclxuXHJcbmV4cG9ydCB7IGlzQ2FsY3VsYXRlZCB9OyJdLCJuYW1lcyI6WyJpc0NhbGN1bGF0ZWQiLCJub2RlIiwiX25vZGUkcGFyZW50RWxlbWVudCRkIiwiX25vZGUkcGFyZW50RWxlbWVudCIsIl9ub2RlJHBhcmVudEVsZW1lbnQkZDIiLCJfbm9kZSRwYXJlbnRFbGVtZW50JGQzIiwicGFyZW50RWxlbWVudCIsImRhdGFzZXQiLCJmb3JtdWxhIiwibGVuZ3RoIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./frontend/calculated.field/functions.js\n");

/***/ }),

/***/ "./frontend/calculated.field/input.js":
/*!********************************************!*\
  !*** ./frontend/calculated.field/input.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n/* harmony import */ var _functions__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./functions */ \"./frontend/calculated.field/functions.js\");\nvar _window$JetFormBuilde2, _window, _window$JetFormBuilde3;\n\nvar _window$JetFormBuilde = window.JetFormBuilderAbstract,\n  InputData = _window$JetFormBuilde.InputData,\n  CalculatedFormula = _window$JetFormBuilde.CalculatedFormula;\nvar applyFilters = JetPlugins.hooks.applyFilters;\nvar _ref = (_window$JetFormBuilde2 = (_window = window) === null || _window === void 0 ? void 0 : (_window$JetFormBuilde3 = _window.JetFormBuilderMain) === null || _window$JetFormBuilde3 === void 0 ? void 0 : _window$JetFormBuilde3.filters) !== null && _window$JetFormBuilde2 !== void 0 ? _window$JetFormBuilde2 : {},\n  _ref$applyFilters = _ref.applyFilters,\n  deprecatedApplyFilters = _ref$applyFilters === void 0 ? false : _ref$applyFilters;\nfunction CalculatedData() {\n  InputData.call(this);\n  this.formula = '';\n  this.precision = 0;\n  this.sepDecimal = '';\n  this.sepThousands = '';\n  this.visibleValNode = null;\n  this.valueTypeProp = 'number';\n  this.isSupported = function (node) {\n    return (0,_functions__WEBPACK_IMPORTED_MODULE_0__.isCalculated)(node);\n  };\n  this.setValue = function () {\n    var _this = this;\n    var formula = new CalculatedFormula(this, {\n      forceFunction: true\n    });\n    formula.observe(this.formula);\n    formula.setResult = function () {\n      _this.value.current = formula.calculate();\n    };\n    formula.relatedCallback = function (input) {\n      var value = applyFilters('jet.fb.calculated.callback', false, input, _this);\n      if (false !== value) {\n        return value;\n      }\n      var response = 'number' === _this.valueTypeProp ? input.calcValue : input.value.current;\n      if (false === deprecatedApplyFilters) {\n        return response;\n      }\n      var filterResult = deprecatedApplyFilters('forms/calculated-field-value', input.value.current, jQuery(input.nodes[0]));\n      return filterResult === input.value.current ? response : filterResult;\n    };\n    formula.emptyValue = function () {\n      return 'number' === _this.valueTypeProp ? 0 : '';\n    };\n    formula.setResult();\n    this.value.current = this.value.applySanitizers(this.value.current);\n    this.beforeSubmit(function (resolve) {\n      _this.value.silence();\n      _this.value.current = null;\n      _this.value.silence();\n      formula.setResult();\n      resolve();\n    });\n  };\n  this.setNode = function (node) {\n    InputData.prototype.setNode.call(this, node);\n    var _node$parentElement$d = node.parentElement.dataset,\n      formula = _node$parentElement$d.formula,\n      precision = _node$parentElement$d.precision,\n      sepDecimal = _node$parentElement$d.sepDecimal,\n      valueType = _node$parentElement$d.valueType,\n      sepThousands = _node$parentElement$d.sepThousands;\n    this.formula = formula;\n    this.precision = +precision;\n    this.sepDecimal = sepDecimal !== null && sepDecimal !== void 0 ? sepDecimal : '';\n    this.sepThousands = sepThousands !== null && sepThousands !== void 0 ? sepThousands : '';\n    this.visibleValNode = node.nextElementSibling;\n    this.valueTypeProp = valueType;\n    this.inputType = 'calculated';\n  };\n  this.addListeners = function () {\n    // silence is golden\n  };\n}\nCalculatedData.prototype = Object.create(InputData.prototype);\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (CalculatedData);//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,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WkEsWUFBWSxHQUFJLEVBQUU7SUFDeEMsSUFBSSxDQUFDQyxjQUFjLEdBQUdHLElBQUksQ0FBQzJCLGtCQUFrQjtJQUM3QyxJQUFJLENBQUM3QixhQUFhLEdBQUk0QixTQUFTO0lBRS9CLElBQUksQ0FBQ0UsU0FBUyxHQUFHLFlBQVk7RUFDOUIsQ0FBQztFQUNELElBQUksQ0FBQ0MsWUFBWSxHQUFHLFlBQVk7SUFDL0I7RUFBQSxDQUNBO0FBQ0Y7QUFFQXRDLGNBQWMsQ0FBQytCLFNBQVMsR0FBR1EsTUFBTSxDQUFDQyxNQUFNLENBQUVyRCxTQUFTLENBQUM0QyxTQUFTLENBQUU7QUFFL0QsaUVBQWUvQixjQUFjIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vLy4vZnJvbnRlbmQvY2FsY3VsYXRlZC5maWVsZC9pbnB1dC5qcz83ZDIyIl0sInNvdXJjZXNDb250ZW50IjpbImltcG9ydCB7IGlzQ2FsY3VsYXRlZCB9IGZyb20gJy4vZnVuY3Rpb25zJztcclxuXHJcbmNvbnN0IHtcclxuXHQgICAgICBJbnB1dERhdGEsXHJcblx0ICAgICAgQ2FsY3VsYXRlZEZvcm11bGEsXHJcbiAgICAgIH0gPSB3aW5kb3cuSmV0Rm9ybUJ1aWxkZXJBYnN0cmFjdDtcclxuY29uc3Qge1xyXG5cdCAgICAgIGFwcGx5RmlsdGVycyxcclxuICAgICAgfSA9IEpldFBsdWdpbnMuaG9va3M7XHJcblxyXG5jb25zdCB7XHJcblx0ICAgICAgYXBwbHlGaWx0ZXJzOiBkZXByZWNhdGVkQXBwbHlGaWx0ZXJzID0gZmFsc2UsXHJcbiAgICAgIH0gPSB3aW5kb3c/LkpldEZvcm1CdWlsZGVyTWFpbj8uZmlsdGVycyA/PyB7fTtcclxuXHJcbmZ1bmN0aW9uIENhbGN1bGF0ZWREYXRhKCkge1xyXG5cdElucHV0RGF0YS5jYWxsKCB0aGlzICk7XHJcblxyXG5cdHRoaXMuZm9ybXVsYSAgICAgICAgPSAnJztcclxuXHR0aGlzLnByZWNpc2lvbiAgICAgID0gMDtcclxuXHR0aGlzLnNlcERlY2ltYWwgICAgID0gJyc7XHJcblx0dGhpcy5zZXBUaG91c2FuZHMgICA9ICcnO1xyXG5cdHRoaXMudmlzaWJsZVZhbE5vZGUgPSBudWxsO1xyXG5cdHRoaXMudmFsdWVUeXBlUHJvcCAgPSAnbnVtYmVyJztcclxuXHJcblx0dGhpcy5pc1N1cHBvcnRlZCA9IGZ1bmN0aW9uICggbm9kZSApIHtcclxuXHRcdHJldHVybiBpc0NhbGN1bGF0ZWQoIG5vZGUgKTtcclxuXHR9O1xyXG5cdHRoaXMuc2V0VmFsdWUgICAgPSBmdW5jdGlvbiAoKSB7XHJcblx0XHRjb25zdCBmb3JtdWxhID0gbmV3IENhbGN1bGF0ZWRGb3JtdWxhKCB0aGlzLCB7IGZvcmNlRnVuY3Rpb246IHRydWUgfSApO1xyXG5cclxuXHRcdGZvcm11bGEub2JzZXJ2ZSggdGhpcy5mb3JtdWxhICk7XHJcblx0XHRmb3JtdWxhLnNldFJlc3VsdCAgICAgICA9ICgpID0+IHtcclxuXHRcdFx0dGhpcy52YWx1ZS5jdXJyZW50ID0gZm9ybXVsYS5jYWxjdWxhdGUoKTtcclxuXHRcdH07XHJcblx0XHRmb3JtdWxhLnJlbGF0ZWRDYWxsYmFjayA9ICggaW5wdXQgKSA9PiB7XHJcblx0XHRcdGNvbnN0IHZhbHVlID0gYXBwbHlGaWx0ZXJzKFxyXG5cdFx0XHRcdCdqZXQuZmIuY2FsY3VsYXRlZC5jYWxsYmFjaycsXHJcblx0XHRcdFx0ZmFsc2UsXHJcblx0XHRcdFx0aW5wdXQsXHJcblx0XHRcdFx0dGhpcyxcclxuXHRcdFx0KTtcclxuXHJcblx0XHRcdGlmICggZmFsc2UgIT09IHZhbHVlICkge1xyXG5cdFx0XHRcdHJldHVybiB2YWx1ZTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Y29uc3QgcmVzcG9uc2UgPSAnbnVtYmVyJyA9PT0gdGhpcy52YWx1ZVR5cGVQcm9wXHJcblx0XHRcdCAgICAgICAgICAgICAgICAgPyBpbnB1dC5jYWxjVmFsdWVcclxuXHRcdFx0ICAgICAgICAgICAgICAgICA6IGlucHV0LnZhbHVlLmN1cnJlbnQ7XHJcblxyXG5cdFx0XHRpZiAoIGZhbHNlID09PSBkZXByZWNhdGVkQXBwbHlGaWx0ZXJzICkge1xyXG5cdFx0XHRcdHJldHVybiByZXNwb25zZTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0Y29uc3QgZmlsdGVyUmVzdWx0ID0gZGVwcmVjYXRlZEFwcGx5RmlsdGVycyhcclxuXHRcdFx0XHQnZm9ybXMvY2FsY3VsYXRlZC1maWVsZC12YWx1ZScsXHJcblx0XHRcdFx0aW5wdXQudmFsdWUuY3VycmVudCxcclxuXHRcdFx0XHRqUXVlcnkoIGlucHV0Lm5vZGVzWyAwIF0gKSxcclxuXHRcdFx0KTtcclxuXHJcblx0XHRcdHJldHVybiBmaWx0ZXJSZXN1bHQgPT09IGlucHV0LnZhbHVlLmN1cnJlbnRcclxuXHRcdFx0ICAgICAgID8gcmVzcG9uc2VcclxuXHRcdFx0ICAgICAgIDogZmlsdGVyUmVzdWx0O1xyXG5cdFx0fTtcclxuXHJcblx0XHRmb3JtdWxhLmVtcHR5VmFsdWUgPSAoKSA9PiAnbnVtYmVyJyA9PT0gdGhpcy52YWx1ZVR5cGVQcm9wXHJcblx0XHQgICAgICAgICAgICAgICAgICAgICAgICAgICA/IDBcclxuXHRcdCAgICAgICAgICAgICAgICAgICAgICAgICAgIDogJyc7XHJcblx0XHRmb3JtdWxhLnNldFJlc3VsdCgpO1xyXG5cdFx0dGhpcy52YWx1ZS5jdXJyZW50ID0gdGhpcy52YWx1ZS5hcHBseVNhbml0aXplcnMoIHRoaXMudmFsdWUuY3VycmVudCApO1xyXG5cclxuXHRcdHRoaXMuYmVmb3JlU3VibWl0KCAoIHJlc29sdmUgKSA9PiB7XHJcblx0XHRcdHRoaXMudmFsdWUuc2lsZW5jZSgpO1xyXG5cdFx0XHR0aGlzLnZhbHVlLmN1cnJlbnQgPSBudWxsO1xyXG5cdFx0XHR0aGlzLnZhbHVlLnNpbGVuY2UoKTtcclxuXHJcblx0XHRcdGZvcm11bGEuc2V0UmVzdWx0KCk7XHJcblx0XHRcdHJlc29sdmUoKTtcclxuXHRcdH0gKTtcclxuXHR9O1xyXG5cclxuXHR0aGlzLnNldE5vZGUgICAgICA9IGZ1bmN0aW9uICggbm9kZSApIHtcclxuXHRcdElucHV0RGF0YS5wcm90b3R5cGUuc2V0Tm9kZS5jYWxsKCB0aGlzLCBub2RlICk7XHJcblxyXG5cdFx0Y29uc3Qge1xyXG5cdFx0XHQgICAgICBmb3JtdWxhLFxyXG5cdFx0XHQgICAgICBwcmVjaXNpb24sXHJcblx0XHRcdCAgICAgIHNlcERlY2ltYWwsXHJcblx0XHRcdCAgICAgIHZhbHVlVHlwZSxcclxuXHRcdFx0ICAgICAgc2VwVGhvdXNhbmRzLFxyXG5cdFx0ICAgICAgfSA9IG5vZGUucGFyZW50RWxlbWVudC5kYXRhc2V0O1xyXG5cclxuXHRcdHRoaXMuZm9ybXVsYSAgICAgICAgPSBmb3JtdWxhO1xyXG5cdFx0dGhpcy5wcmVjaXNpb24gICAgICA9ICtwcmVjaXNpb247XHJcblx0XHR0aGlzLnNlcERlY2ltYWwgICAgID0gc2VwRGVjaW1hbCA/PyAnJztcclxuXHRcdHRoaXMuc2VwVGhvdXNhbmRzICAgPSBzZXBUaG91c2FuZHMgPz8gJyc7XHJcblx0XHR0aGlzLnZpc2libGVWYWxOb2RlID0gbm9kZS5uZXh0RWxlbWVudFNpYmxpbmc7XHJcblx0XHR0aGlzLnZhbHVlVHlwZVByb3AgID0gdmFsdWVUeXBlO1xyXG5cclxuXHRcdHRoaXMuaW5wdXRUeXBlID0gJ2NhbGN1bGF0ZWQnO1xyXG5cdH07XHJcblx0dGhpcy5hZGRMaXN0ZW5lcnMgPSBmdW5jdGlvbiAoKSB7XHJcblx0XHQvLyBzaWxlbmNlIGlzIGdvbGRlblxyXG5cdH07XHJcbn1cclxuXHJcbkNhbGN1bGF0ZWREYXRhLnByb3RvdHlwZSA9IE9iamVjdC5jcmVhdGUoIElucHV0RGF0YS5wcm90b3R5cGUgKTtcclxuXHJcbmV4cG9ydCBkZWZhdWx0IENhbGN1bGF0ZWREYXRhOyJdLCJuYW1lcyI6WyJpc0NhbGN1bGF0ZWQiLCJfd2luZG93JEpldEZvcm1CdWlsZGUiLCJ3aW5kb3ciLCJKZXRGb3JtQnVpbGRlckFic3RyYWN0IiwiSW5wdXREYXRhIiwiQ2FsY3VsYXRlZEZvcm11bGEiLCJhcHBseUZpbHRlcnMiLCJKZXRQbHVnaW5zIiwiaG9va3MiLCJfcmVmIiwiX3dpbmRvdyRKZXRGb3JtQnVpbGRlMiIsIl93aW5kb3ciLCJfd2luZG93JEpldEZvcm1CdWlsZGUzIiwiSmV0Rm9ybUJ1aWxkZXJNYWluIiwiZmlsdGVycyIsIl9yZWYkYXBwbHlGaWx0ZXJzIiwiZGVwcmVjYXRlZEFwcGx5RmlsdGVycyIsIkNhbGN1bGF0ZWREYXRhIiwiY2FsbCIsImZvcm11bGEiLCJwcmVjaXNpb24iLCJzZXBEZWNpbWFsIiwic2VwVGhvdXNhbmRzIiwidmlzaWJsZVZhbE5vZGUiLCJ2YWx1ZVR5cGVQcm9wIiwiaXNTdXBwb3J0ZWQiLCJub2RlIiwic2V0VmFsdWUiLCJfdGhpcyIsImZvcmNlRnVuY3Rpb24iLCJvYnNlcnZlIiwic2V0UmVzdWx0IiwidmFsdWUiLCJjdXJyZW50IiwiY2FsY3VsYXRlIiwicmVsYXRlZENhbGxiYWNrIiwiaW5wdXQiLCJyZXNwb25zZSIsImNhbGNWYWx1ZSIsImZpbHRlclJlc3VsdCIsImpRdWVyeSIsIm5vZGVzIiwiZW1wdHlWYWx1ZSIsImFwcGx5U2FuaXRpemVycyIsImJlZm9yZVN1Ym1pdCIsInJlc29sdmUiLCJzaWxlbmNlIiwic2V0Tm9kZSIsInByb3RvdHlwZSIsIl9ub2RlJHBhcmVudEVsZW1lbnQkZCIsInBhcmVudEVsZW1lbnQiLCJkYXRhc2V0IiwidmFsdWVUeXBlIiwibmV4dEVsZW1lbnRTaWJsaW5nIiwiaW5wdXRUeXBlIiwiYWRkTGlzdGVuZXJzIiwiT2JqZWN0IiwiY3JlYXRlIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./frontend/calculated.field/input.js\n");

/***/ }),

/***/ "./frontend/calculated.field/main.js":
/*!*******************************************!*\
  !*** ./frontend/calculated.field/main.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _input__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./input */ \"./frontend/calculated.field/input.js\");\n/* harmony import */ var _signal__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./signal */ \"./frontend/calculated.field/signal.js\");\nfunction _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }\nfunction _nonIterableSpread() { throw new TypeError(\"Invalid attempt to spread non-iterable instance.\\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.\"); }\nfunction _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === \"string\") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === \"Object\" && o.constructor) n = o.constructor.name; if (n === \"Map\" || n === \"Set\") return Array.from(o); if (n === \"Arguments\" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }\nfunction _iterableToArray(iter) { if (typeof Symbol !== \"undefined\" && iter[Symbol.iterator] != null || iter[\"@@iterator\"] != null) return Array.from(iter); }\nfunction _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }\nfunction _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }\n\n\nvar addFilter = JetPlugins.hooks.addFilter;\naddFilter('jet.fb.inputs', 'jet-form-builder/calculated-field', function (inputs) {\n  inputs = [_input__WEBPACK_IMPORTED_MODULE_0__[\"default\"]].concat(_toConsumableArray(inputs));\n  return inputs;\n});\naddFilter('jet.fb.signals', 'jet-form-builder/calculated-field', function (signals) {\n  signals = [_signal__WEBPACK_IMPORTED_MODULE_1__[\"default\"]].concat(_toConsumableArray(signals));\n  return signals;\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9mcm9udGVuZC9jYWxjdWxhdGVkLmZpZWxkL21haW4uanMuanMiLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7O0FBQXFDO0FBQ0c7QUFFeEMsSUFBUUUsU0FBUyxHQUFLQyxVQUFVLENBQUNDLEtBQUssQ0FBOUJGLFNBQVM7QUFFakJBLFNBQVMsQ0FDUixlQUFlLEVBQ2YsbUNBQW1DLEVBQ25DLFVBQVdHLE1BQU0sRUFBRztFQUNuQkEsTUFBTSxJQUFLTCw4Q0FBYyxFQUFBTSxNQUFBLENBQUFDLGtCQUFBLENBQUtGLE1BQU0sRUFBRTtFQUV0QyxPQUFPQSxNQUFNO0FBQ2QsQ0FBQyxDQUNEO0FBRURILFNBQVMsQ0FDUixnQkFBZ0IsRUFDaEIsbUNBQW1DLEVBQ25DLFVBQVdNLE9BQU8sRUFBRztFQUNwQkEsT0FBTyxJQUFLUCwrQ0FBZ0IsRUFBQUssTUFBQSxDQUFBQyxrQkFBQSxDQUFLQyxPQUFPLEVBQUU7RUFFMUMsT0FBT0EsT0FBTztBQUNmLENBQUMsQ0FDRCIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL2Zyb250ZW5kL2NhbGN1bGF0ZWQuZmllbGQvbWFpbi5qcz9iNzYyIl0sInNvdXJjZXNDb250ZW50IjpbImltcG9ydCBDYWxjdWxhdGVkRGF0YSBmcm9tICcuL2lucHV0JztcclxuaW1wb3J0IFNpZ25hbENhbGN1bGF0ZWQgZnJvbSAnLi9zaWduYWwnO1xyXG5cclxuY29uc3QgeyBhZGRGaWx0ZXIgfSA9IEpldFBsdWdpbnMuaG9va3M7XHJcblxyXG5hZGRGaWx0ZXIoXHJcblx0J2pldC5mYi5pbnB1dHMnLFxyXG5cdCdqZXQtZm9ybS1idWlsZGVyL2NhbGN1bGF0ZWQtZmllbGQnLFxyXG5cdGZ1bmN0aW9uICggaW5wdXRzICkge1xyXG5cdFx0aW5wdXRzID0gWyBDYWxjdWxhdGVkRGF0YSwgLi4uaW5wdXRzIF07XHJcblxyXG5cdFx0cmV0dXJuIGlucHV0cztcclxuXHR9LFxyXG4pO1xyXG5cclxuYWRkRmlsdGVyKFxyXG5cdCdqZXQuZmIuc2lnbmFscycsXHJcblx0J2pldC1mb3JtLWJ1aWxkZXIvY2FsY3VsYXRlZC1maWVsZCcsXHJcblx0ZnVuY3Rpb24gKCBzaWduYWxzICkge1xyXG5cdFx0c2lnbmFscyA9IFsgU2lnbmFsQ2FsY3VsYXRlZCwgLi4uc2lnbmFscyBdO1xyXG5cclxuXHRcdHJldHVybiBzaWduYWxzO1xyXG5cdH0sXHJcbik7Il0sIm5hbWVzIjpbIkNhbGN1bGF0ZWREYXRhIiwiU2lnbmFsQ2FsY3VsYXRlZCIsImFkZEZpbHRlciIsIkpldFBsdWdpbnMiLCJob29rcyIsImlucHV0cyIsImNvbmNhdCIsIl90b0NvbnN1bWFibGVBcnJheSIsInNpZ25hbHMiXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./frontend/calculated.field/main.js\n");

/***/ }),

/***/ "./frontend/calculated.field/signal.js":
/*!*********************************************!*\
  !*** ./frontend/calculated.field/signal.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n/* harmony import */ var _functions__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./functions */ \"./frontend/calculated.field/functions.js\");\nfunction _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }\nfunction _nonIterableRest() { throw new TypeError(\"Invalid attempt to destructure non-iterable instance.\\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.\"); }\nfunction _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === \"string\") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === \"Object\" && o.constructor) n = o.constructor.name; if (n === \"Map\" || n === \"Set\") return Array.from(o); if (n === \"Arguments\" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }\nfunction _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }\nfunction _iterableToArrayLimit(arr, i) { var _i = null == arr ? null : \"undefined\" != typeof Symbol && arr[Symbol.iterator] || arr[\"@@iterator\"]; if (null != _i) { var _s, _e, _x, _r, _arr = [], _n = !0, _d = !1; try { if (_x = (_i = _i.call(arr)).next, 0 === i) { if (Object(_i) !== _i) return; _n = !1; } else for (; !(_n = (_s = _x.call(_i)).done) && (_arr.push(_s.value), _arr.length !== i); _n = !0); } catch (err) { _d = !0, _e = err; } finally { try { if (!_n && null != _i.return && (_r = _i.return(), Object(_r) !== _r)) return; } finally { if (_d) throw _e; } } return _arr; } }\nfunction _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }\n\nvar BaseSignal = window.JetFormBuilderAbstract.BaseSignal;\n\n/**\r\n * @property {CalculatedData} input\r\n */\nfunction SignalCalculated() {\n  BaseSignal.call(this);\n  this.isSupported = function (node, inputData) {\n    return (0,_functions__WEBPACK_IMPORTED_MODULE_0__.isCalculated)(node);\n  };\n  this.baseSignal = function () {\n    var _this$input$nodes = _slicedToArray(this.input.nodes, 1),\n      node = _this$input$nodes[0];\n    var isNumber = 'number' === this.input.valueTypeProp;\n    this.input.calcValue = isNumber ? this.withPrecision() : this.input.value.current;\n    this.input.value.silence();\n    this.input.value.current = isNumber ? this.convertValue() : this.input.value.current;\n    this.input.value.silence();\n    this.input.visibleValNode.textContent = this.input.value.current;\n    node.value = this.input.calcValue;\n  };\n  this.runSignal = function () {\n    this.baseSignal();\n    var _this$input$nodes2 = _slicedToArray(this.input.nodes, 1),\n      node = _this$input$nodes2[0];\n    this.triggerJQuery(node);\n  };\n}\nSignalCalculated.prototype = Object.create(BaseSignal.prototype);\nSignalCalculated.prototype.convertValue = function () {\n  var value = this.input.value.current;\n  if (Number.isNaN(Number(value))) {\n    return 0;\n  }\n  var parts = this.withPrecision().toString().split('.');\n  if (this.input.sepThousands) {\n    parts[0] = parts[0].replace(/\\B(?=(\\d{3})+(?!\\d))/g, this.input.sepThousands);\n  }\n  return parts.join(this.input.sepDecimal);\n};\nSignalCalculated.prototype.withPrecision = function () {\n  return Number(this.input.value.current).toFixed(this.input.precision);\n};\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (SignalCalculated);//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,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Z0JBQWdCLENBQUN1QixTQUFTLENBQUNWLGFBQWEsR0FBRyxZQUFZO0VBQ3RELE9BQU9hLE1BQU0sQ0FBRSxJQUFJLENBQUNsQixLQUFLLENBQUNNLEtBQUssQ0FBQ0MsT0FBTyxDQUFFLENBQUNvQixPQUFPLENBQUUsSUFBSSxDQUFDM0IsS0FBSyxDQUFDNEIsU0FBUyxDQUFFO0FBQzFFLENBQUM7QUFFRCxpRUFBZXBDLGdCQUFnQiIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL2Zyb250ZW5kL2NhbGN1bGF0ZWQuZmllbGQvc2lnbmFsLmpzP2E1MTIiXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IHsgaXNDYWxjdWxhdGVkIH0gZnJvbSAnLi9mdW5jdGlvbnMnO1xyXG5cclxuY29uc3Qge1xyXG5cdCAgICAgIEJhc2VTaWduYWwsXHJcbiAgICAgIH0gPSB3aW5kb3cuSmV0Rm9ybUJ1aWxkZXJBYnN0cmFjdDtcclxuXHJcbi8qKlxyXG4gKiBAcHJvcGVydHkge0NhbGN1bGF0ZWREYXRhfSBpbnB1dFxyXG4gKi9cclxuZnVuY3Rpb24gU2lnbmFsQ2FsY3VsYXRlZCgpIHtcclxuXHRCYXNlU2lnbmFsLmNhbGwoIHRoaXMgKTtcclxuXHJcblx0dGhpcy5pc1N1cHBvcnRlZCA9IGZ1bmN0aW9uICggbm9kZSwgaW5wdXREYXRhICkge1xyXG5cdFx0cmV0dXJuIGlzQ2FsY3VsYXRlZCggbm9kZSApO1xyXG5cdH07XHJcblxyXG5cdHRoaXMuYmFzZVNpZ25hbCA9IGZ1bmN0aW9uICgpIHtcclxuXHRcdGNvbnN0IFsgbm9kZSBdID0gdGhpcy5pbnB1dC5ub2RlcztcclxuXHJcblx0XHRjb25zdCBpc051bWJlciA9ICdudW1iZXInID09PSB0aGlzLmlucHV0LnZhbHVlVHlwZVByb3A7XHJcblxyXG5cdFx0dGhpcy5pbnB1dC5jYWxjVmFsdWUgPSBpc051bWJlclxyXG5cdFx0ICAgICAgICAgICAgICAgICAgICAgICA/IHRoaXMud2l0aFByZWNpc2lvbigpXHJcblx0XHQgICAgICAgICAgICAgICAgICAgICAgIDogdGhpcy5pbnB1dC52YWx1ZS5jdXJyZW50O1xyXG5cclxuXHRcdHRoaXMuaW5wdXQudmFsdWUuc2lsZW5jZSgpO1xyXG5cdFx0dGhpcy5pbnB1dC52YWx1ZS5jdXJyZW50ID0gaXNOdW1iZXJcclxuXHRcdCAgICAgICAgICAgICAgICAgICAgICAgICAgID8gdGhpcy5jb252ZXJ0VmFsdWUoKVxyXG5cdFx0ICAgICAgICAgICAgICAgICAgICAgICAgICAgOiB0aGlzLmlucHV0LnZhbHVlLmN1cnJlbnQ7XHJcblx0XHR0aGlzLmlucHV0LnZhbHVlLnNpbGVuY2UoKTtcclxuXHJcblx0XHR0aGlzLmlucHV0LnZpc2libGVWYWxOb2RlLnRleHRDb250ZW50ID0gdGhpcy5pbnB1dC52YWx1ZS5jdXJyZW50O1xyXG5cclxuXHRcdG5vZGUudmFsdWUgPSB0aGlzLmlucHV0LmNhbGNWYWx1ZTtcclxuXHR9O1xyXG5cclxuXHR0aGlzLnJ1blNpZ25hbCA9IGZ1bmN0aW9uICgpIHtcclxuXHRcdHRoaXMuYmFzZVNpZ25hbCgpO1xyXG5cclxuXHRcdGNvbnN0IFsgbm9kZSBdID0gdGhpcy5pbnB1dC5ub2RlcztcclxuXHJcblx0XHR0aGlzLnRyaWdnZXJKUXVlcnkoIG5vZGUgKTtcclxuXHR9O1xyXG59XHJcblxyXG5TaWduYWxDYWxjdWxhdGVkLnByb3RvdHlwZSA9IE9iamVjdC5jcmVhdGUoIEJhc2VTaWduYWwucHJvdG90eXBlICk7XHJcblxyXG5TaWduYWxDYWxjdWxhdGVkLnByb3RvdHlwZS5jb252ZXJ0VmFsdWUgPSBmdW5jdGlvbiAoKSB7XHJcblx0Y29uc3QgdmFsdWUgPSB0aGlzLmlucHV0LnZhbHVlLmN1cnJlbnQ7XHJcblxyXG5cdGlmICggTnVtYmVyLmlzTmFOKCBOdW1iZXIoIHZhbHVlICkgKSApIHtcclxuXHRcdHJldHVybiAwO1xyXG5cdH1cclxuXHJcblx0Y29uc3QgcGFydHMgPSB0aGlzLndpdGhQcmVjaXNpb24oKS50b1N0cmluZygpLnNwbGl0KCAnLicgKTtcclxuXHJcblx0aWYgKCB0aGlzLmlucHV0LnNlcFRob3VzYW5kcyApIHtcclxuXHRcdHBhcnRzWyAwIF0gPSBwYXJ0c1sgMCBdLnJlcGxhY2UoXHJcblx0XHRcdC9cXEIoPz0oXFxkezN9KSsoPyFcXGQpKS9nLFxyXG5cdFx0XHR0aGlzLmlucHV0LnNlcFRob3VzYW5kcyxcclxuXHRcdCk7XHJcblx0fVxyXG5cclxuXHRyZXR1cm4gcGFydHMuam9pbiggdGhpcy5pbnB1dC5zZXBEZWNpbWFsICk7XHJcbn07XHJcblxyXG5TaWduYWxDYWxjdWxhdGVkLnByb3RvdHlwZS53aXRoUHJlY2lzaW9uID0gZnVuY3Rpb24gKCkge1xyXG5cdHJldHVybiBOdW1iZXIoIHRoaXMuaW5wdXQudmFsdWUuY3VycmVudCApLnRvRml4ZWQoIHRoaXMuaW5wdXQucHJlY2lzaW9uICk7XHJcbn07XHJcblxyXG5leHBvcnQgZGVmYXVsdCBTaWduYWxDYWxjdWxhdGVkOyJdLCJuYW1lcyI6WyJpc0NhbGN1bGF0ZWQiLCJCYXNlU2lnbmFsIiwid2luZG93IiwiSmV0Rm9ybUJ1aWxkZXJBYnN0cmFjdCIsIlNpZ25hbENhbGN1bGF0ZWQiLCJjYWxsIiwiaXNTdXBwb3J0ZWQiLCJub2RlIiwiaW5wdXREYXRhIiwiYmFzZVNpZ25hbCIsIl90aGlzJGlucHV0JG5vZGVzIiwiX3NsaWNlZFRvQXJyYXkiLCJpbnB1dCIsIm5vZGVzIiwiaXNOdW1iZXIiLCJ2YWx1ZVR5cGVQcm9wIiwiY2FsY1ZhbHVlIiwid2l0aFByZWNpc2lvbiIsInZhbHVlIiwiY3VycmVudCIsInNpbGVuY2UiLCJjb252ZXJ0VmFsdWUiLCJ2aXNpYmxlVmFsTm9kZSIsInRleHRDb250ZW50IiwicnVuU2lnbmFsIiwiX3RoaXMkaW5wdXQkbm9kZXMyIiwidHJpZ2dlckpRdWVyeSIsInByb3RvdHlwZSIsIk9iamVjdCIsImNyZWF0ZSIsIk51bWJlciIsImlzTmFOIiwicGFydHMiLCJ0b1N0cmluZyIsInNwbGl0Iiwic2VwVGhvdXNhbmRzIiwicmVwbGFjZSIsImpvaW4iLCJzZXBEZWNpbWFsIiwidG9GaXhlZCIsInByZWNpc2lvbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./frontend/calculated.field/signal.js\n");

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
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = __webpack_require__("./frontend/calculated.field/main.js");
/******/ 	
/******/ })()
;