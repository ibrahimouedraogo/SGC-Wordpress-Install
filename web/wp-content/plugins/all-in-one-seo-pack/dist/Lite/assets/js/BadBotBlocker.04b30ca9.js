import{a as l}from"./vuex.esm.19624049.js";import{B as n}from"./Textarea.e385048b.js";import{C as c}from"./index.81e35b24.js";import{C as a}from"./Card.efd2e18e.js";import{C as i}from"./SettingsRow.d7400549.js";import{n as p}from"./vueComponentNormalizer.67c9b86e.js";import"./client.90beecd8.js";import"./_commonjsHelpers.10c44588.js";import"./translations.3bc9d58c.js";import"./default-i18n.0e73c33c.js";import"./Caret.eeb84d06.js";import"./index.7b63dad7.js";import"./isArrayLikeObject.5268a676.js";import"./helpers.a2b0759e.js";import"./constants.8bff9f56.js";import"./portal-vue.esm.18ed59c3.js";import"./Tooltip.d723c3c3.js";import"./Slide.9538a421.js";import"./Row.2d17f2cd.js";var d=function(){var t=this,s=t.$createElement,o=t._self._c||s;return o("div",{staticClass:"aioseo-tools-bad-bot-blocker"},[o("core-card",{attrs:{slug:"badBotBlocker","header-text":t.strings.badBotBlocker}},[o("core-settings-row",{attrs:{name:t.strings.blockBadBotsHttp},scopedSlots:t._u([{key:"content",fn:function(){return[o("base-toggle",{model:{value:t.options.deprecated.tools.blocker.blockBots,callback:function(e){t.$set(t.options.deprecated.tools.blocker,"blockBots",e)},expression:"options.deprecated.tools.blocker.blockBots"}})]},proxy:!0}])}),o("core-settings-row",{attrs:{name:t.strings.blockReferralSpamHttp},scopedSlots:t._u([{key:"content",fn:function(){return[o("base-toggle",{model:{value:t.options.deprecated.tools.blocker.blockReferer,callback:function(e){t.$set(t.options.deprecated.tools.blocker,"blockReferer",e)},expression:"options.deprecated.tools.blocker.blockReferer"}})]},proxy:!0}])}),t.options.deprecated.tools.blocker.blockBots||t.options.deprecated.tools.blocker.blockReferer?o("core-settings-row",{attrs:{name:t.strings.useCustomBlocklists},scopedSlots:t._u([{key:"content",fn:function(){return[o("base-toggle",{model:{value:t.options.deprecated.tools.blocker.custom.enable,callback:function(e){t.$set(t.options.deprecated.tools.blocker.custom,"enable",e)},expression:"options.deprecated.tools.blocker.custom.enable"}})]},proxy:!0}],null,!1,2813344989)}):t._e(),t.options.deprecated.tools.blocker.blockBots&&t.options.deprecated.tools.blocker.custom.enable?o("core-settings-row",{attrs:{name:t.strings.userAgentBlocklist},scopedSlots:t._u([{key:"content",fn:function(){return[o("base-textarea",{attrs:{minHeight:200,maxHeight:200},model:{value:t.options.deprecated.tools.blocker.custom.bots,callback:function(e){t.$set(t.options.deprecated.tools.blocker.custom,"bots",e)},expression:"options.deprecated.tools.blocker.custom.bots"}})]},proxy:!0}],null,!1,2333962956)}):t._e(),t.options.deprecated.tools.blocker.blockReferer&&t.options.deprecated.tools.blocker.custom.enable?o("core-settings-row",{attrs:{name:t.strings.refererBlockList},scopedSlots:t._u([{key:"content",fn:function(){return[o("base-textarea",{attrs:{minHeight:200,maxHeight:200},model:{value:t.options.deprecated.tools.blocker.custom.referer,callback:function(e){t.$set(t.options.deprecated.tools.blocker.custom,"referer",e)},expression:"options.deprecated.tools.blocker.custom.referer"}})]},proxy:!0}],null,!1,3362070519)}):t._e(),t.options.deprecated.tools.blocker.blockBots||t.options.deprecated.tools.blocker.blockReferer?o("core-settings-row",{attrs:{name:t.strings.trackBlockedBots},scopedSlots:t._u([{key:"content",fn:function(){return[o("base-toggle",{model:{value:t.options.deprecated.tools.blocker.track,callback:function(e){t.$set(t.options.deprecated.tools.blocker,"track",e)},expression:"options.deprecated.tools.blocker.track"}}),o("core-alert",{attrs:{type:"blue"},domProps:{innerHTML:t._s(t.strings.logLocation)}})]},proxy:!0}],null,!1,3972286096)}):t._e()],1)],1)},u=[];const k={components:{BaseTextarea:n,CoreAlert:c,CoreCard:a,CoreSettingsRow:i},data(){return{strings:{badBotBlocker:this.$t.__("Bad Bot Blocker",this.$td),blockBadBotsHttp:this.$t.__("Block Bad Bots using HTTP",this.$td),blockReferralSpamHttp:this.$t.__("Block Referral Spam using HTTP",this.$td),trackBlockedBots:this.$t.__("Track Blocked Bots",this.$td),useCustomBlocklists:this.$t.__("Use Custom Blocklists",this.$td),userAgentBlocklist:this.$t.__("User Agent Blocklist",this.$td),refererBlockList:this.$t.__("Referer Blocklist",this.$td),blockedBotsLog:this.$t.__("Blocked Bots Log",this.$td),logLocation:this.$t.sprintf(this.$t.__("The log for the blocked bots is located here: %1$s",this.$td),'<br><a href="'+this.$aioseo.urls.blockedBotsLogUrl+'" target="_blank">'+this.$aioseo.urls.blockedBotsLogUrl+"</a>")}}},computed:{...l(["options"])}},r={};var b=p(k,d,u,!1,m,null,null,null);function m(t){for(let s in r)this[s]=r[s]}const M=function(){return b.exports}();export{M as default};
