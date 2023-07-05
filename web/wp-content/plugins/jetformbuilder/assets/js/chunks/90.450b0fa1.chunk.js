"use strict";(self.webpackChunk=self.webpackChunk||[]).push([[90],{3090:(e,t,n)=>{n.r(t),n.d(t,{default:()=>Ae});var r=wp.compose.compose,a=wp.data,o=a.withSelect,i=a.withDispatch,l=wp.i18n.__,c=wp.components,u=c.TextControl,s=c.SelectControl,p=c.withNotices,y=wp.element.useEffect,f=JetFBActions.renderGateway,m=JetFBHooks,w=m.withSelectGateways,b=m.withDispatchGateways,d=JetFBComponents.ToggleControl;const g=r(o(w),i(b),p)((function(e){var t=e.setGatewayRequest,n=e.gatewaySpecific,r=e.setGatewaySpecific,a=e.gatewayScenario,o=e.setGatewayScenario,i=e.getSpecificOrGlobal,c=e.additionalSourceGateway,p=e.specificGatewayLabel,m=e.noticeOperations,w=e.noticeUI,b=a.id,g=void 0===b?"PAY_NOW":b;return y((function(){t({id:g})}),[g]),y((function(){t({id:g})}),[]),wp.element.createElement(React.Fragment,null,w,wp.element.createElement(d,{checked:n.use_global,onChange:function(e){return r({use_global:e})}},l("Use","jet-form-builder")+" ",wp.element.createElement("a",{href:JetFormEditorData.global_settings_url+"#payments-gateways__paypal"},l("Global Settings","jet-form-builder"))),wp.element.createElement(u,{label:p("client_id"),key:"paypal_client_id_setting",value:i("client_id"),onChange:function(e){return r({client_id:e})},disabled:n.use_global}),wp.element.createElement(u,{label:p("secret"),key:"paypal_secret_setting",value:i("secret"),onChange:function(e){return r({secret:e})},disabled:n.use_global}),wp.element.createElement(s,{labelPosition:"side",label:p("gateway_type"),value:g,onChange:function(e){o({id:e})},options:c.scenarios}),f("paypal",{noticeOperations:m},g))}));function v(e){return v="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},v(e)}function h(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function S(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?h(Object(n),!0).forEach((function(t){_(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):h(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function _(e,t,n){return(t=function(e){var t=function(e,t){if("object"!==v(e)||null===e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var r=n.call(e,"string");if("object"!==v(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(e)}(e);return"symbol"===v(t)?t:String(t)}(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}var O=wp.compose.compose,j=wp.data,E=j.withSelect,P=j.withDispatch,C=wp.components,G=C.TextControl,k=C.SelectControl,F=C.BaseControl,D=(C.RadioControl,JetFBHooks),A=D.withSelectFormFields,R=D.withSelectGateways,B=D.withDispatchGateways,N=(D.withSelectActionsByType,JetFBComponents.GatewayFetchButton);const J=O(E((function(){return S(S({},A([],"--").apply(void 0,arguments)),R.apply(void 0,arguments))})),P((function(){return S({},B.apply(void 0,arguments))})))((function(e){var t=e.gatewayGeneral,n=e.gatewaySpecific,r=e.setGateway,a=e.setGatewaySpecific,o=e.formFields,i=e.getSpecificOrGlobal,l=e.loadingGateway,c=e.scenarioSource,u=e.noticeOperations,s=e.scenarioLabel,p=e.globalGatewayLabel;return wp.element.createElement(React.Fragment,null,wp.element.createElement(F,{label:s("fetch_button_label")},wp.element.createElement("div",{className:"jet-user-fields-map__list"},!l.success&&!l.loading&&wp.element.createElement("span",{className:"description-controls"},s("fetch_button_help")),wp.element.createElement(N,{initialLabel:s("fetch_button"),label:s("fetch_button_retry"),apiArgs:S(S({},c.fetch),{},{data:{client_id:i("client_id"),secret:i("secret")}}),onFail:("error",function(e){u.removeNotice(t.gateway),u.createNotice({status:"error",content:e.message,id:t.gateway})})}))),l.success&&wp.element.createElement(React.Fragment,null,wp.element.createElement(G,{label:s("currency"),key:"paypal_currency_code_setting",value:n.currency,onChange:function(e){return a({currency:e})}}),wp.element.createElement(k,{label:p("price_field"),key:"form_fields_price_field",value:t.price_field,labelPosition:"side",onChange:function(e){r({price_field:e})},options:o})))}));var T=JetFBActions.registerGateway,I=wp.hooks.addFilter,U=wp.i18n.__,L="paypal";function x(e){return x="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},x(e)}function H(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function q(e,t,n){return(t=function(e){var t=function(e,t){if("object"!==x(e)||null===e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var r=n.call(e,"string");if("object"!==x(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(e)}(e);return"symbol"===x(t)?t:String(t)}(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}T(L,g),T(L,J,"PAY_NOW"),I("jet.fb.gateways.getDisabledStateButton","jet-form-builder",(function(e,t,n){var r;return L===(null==t||null===(r=t._jf_gateways)||void 0===r?void 0:r.gateway)?!n("save_record"):e})),I("jet.fb.gateways.getDisabledInfo","jet-form-builder",(function(e,t){var n;return L!==(null==t||null===(n=t._jf_gateways)||void 0===n?void 0:n.gateway)?e:wp.element.createElement("p",null,U("Please add `Save Form Record` action","jet-form-builder"))}));var M=JetFBActions,W=M.gatewayAttr,Y=M.renderGateway,V=M.renderGatewayWithPlaceholder,$=wp.i18n.__,z=wp.components,K=z.TextareaControl,Q=z.BaseControl,X=wp.data,Z=X.withSelect,ee=X.withDispatch,te=wp.compose.compose,ne=JetFBHooks,re=ne.withSelectGateways,ae=ne.withDispatchGateways,oe=W(),ie=W("labels");const le=te(Z((function(){return function(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?H(Object(n),!0).forEach((function(t){q(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):H(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}({},re.apply(void 0,arguments))})),ee(ae))((function(e){var t=e.gatewayGeneral,n=e.setGatewayInner,r=e.loadingGateway,a=e.gatewayRequest,o=e.CURRENT_SCENARIO,i=e.currentScenario,l=function(e,t,r){n({key:e,value:q({},t,r)})},c=function(e,n){var r=arguments.length>2&&void 0!==arguments[2]&&arguments[2];return t[e]&&t[e][n]?t[e][n]:r},u=function(e,t){l("messages",e,t)},s=function(e){return c("messages",e,oe.messages[e])},p=wp.element.createElement(React.Fragment,null,V(t.gateway,{gatewayGeneral:t,CURRENT_SCENARIO:o,currentScenario:i},"macrosList",wp.element.createElement(Q,{key:"payment_result_macros_base_control"},wp.element.createElement("h4",null,$("Available macros list: ","jet-form-builder"),wp.element.createElement("br",null),$("%gateway_amount% - payment amount returned from gateway template;","jet-form-builder"),wp.element.createElement("br",null),$("%gateway_status% - payment status returned from payment gateway;","jet-form-builder"),wp.element.createElement("br",null),$('%field_name% - replace "field_name" with any field name from the form;',"jet-form-builder"),wp.element.createElement("br",null)))),wp.element.createElement(K,{key:"payment_result_message_success",label:ie("message_success"),value:s("success"),onChange:function(e){return u("success",e)}}),wp.element.createElement(K,{key:"payment_result_message_failed",label:ie("message_failed"),value:s("failed"),onChange:function(e){return u("failed",e)}}));return wp.element.createElement(React.Fragment,null,Y(t.gateway,{setValueInObject:l,getNotifications:c}),(-1===a.id||r.success||!a.id.includes(t.gateway))&&p)}));function ce(e){return ce="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},ce(e)}function ue(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function se(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?ue(Object(n),!0).forEach((function(t){pe(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):ue(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function pe(e,t,n){return(t=function(e){var t=function(e,t){if("object"!==ce(e)||null===e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var r=n.call(e,"string");if("object"!==ce(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(e)}(e);return"symbol"===ce(t)?t:String(t)}(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function ye(e,t){return function(e){if(Array.isArray(e))return e}(e)||function(e,t){var n=null==e?null:"undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(null!=n){var r,a,o,i,l=[],c=!0,u=!1;try{if(o=(n=n.call(e)).next,0===t){if(Object(n)!==n)return;c=!1}else for(;!(c=(r=o.call(n)).done)&&(l.push(r.value),l.length!==t);c=!0);}catch(e){u=!0,a=e}finally{try{if(!c&&null!=n.return&&(i=n.return(),Object(i)!==i))return}finally{if(u)throw a}}return l}}(e,t)||fe(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function fe(e,t){if(e){if("string"==typeof e)return me(e,t);var n=Object.prototype.toString.call(e).slice(8,-1);return"Object"===n&&e.constructor&&(n=e.constructor.name),"Map"===n||"Set"===n?Array.from(e):"Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?me(e,t):void 0}}function me(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,r=new Array(t);n<t;n++)r[n]=e[n];return r}var we=wp.components,be=we.RadioControl,de=we.Button,ge=wp.data,ve=ge.withDispatch,he=ge.withSelect,Se=wp.element,_e=Se.useState,Oe=Se.useEffect,je=wp.i18n.__,Ee=wp.compose.compose,Pe=JetFBComponents.ActionModal,Ce=JetFBHooks,Ge=Ce.withDispatchGateways,ke=Ce.withSelectGateways,Fe=Ce.useMetaState,De=window.JetFormEditorData.gateways;const Ae=Ee(ve(Ge),he(ke))((function(e){var t,n=e.setGateway,r=e.setGatewayScenario,a=e.clearGateway,o=e.clearScenario,i=e.gatewayGeneral,l=e.gatewayScenario,c=ye(Fe("_jf_gateways"),2),u=c[0],s=c[1],p=ye(_e(!1),2),y=p[0],f=p[1];Oe((function(){var e;y?(n(u),r(null===(e=u[u.gateway])||void 0===e?void 0:e.scenario)):(a(),o())}),[y]);var m,w,b=function(){var e=arguments.length>0&&void 0!==arguments[0]&&arguments[0];!1!==e&&s(e),f(!1)};return wp.element.createElement(React.Fragment,null,wp.element.createElement(be,{key:"gateways_radio_control",selected:null!==(t=u.gateway)&&void 0!==t?t:"none",options:[{label:"None",value:"none"}].concat((w=De.list,function(e){if(Array.isArray(e))return me(e)}(w)||function(e){if("undefined"!=typeof Symbol&&null!=e[Symbol.iterator]||null!=e["@@iterator"])return Array.from(e)}(w)||fe(w)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}())),onChange:function(e){s(se(se({},u),{},{gateway:e}))}}),"none"!==u.gateway&&u.gateway&&wp.element.createElement(de,{onClick:function(){return f(!0)},icon:"admin-tools",style:{margin:"1em 0"},isSecondary:!0},je("Edit","jet-form-builder")),y&&wp.element.createElement(Pe,{classNames:["width-60"],onRequestClose:function(){return b()},onCancelClick:function(){return b()},onUpdateClick:function(){return b(se(se({},i),{},pe({},i.gateway,se(se({},i[i.gateway]||{}),{},{scenario:l}))))},title:"Edit ".concat((m=u.gateway,De.list.find((function(e){return e.value===m})).label)," Settings")},wp.element.createElement(le,null)))}))}}]);