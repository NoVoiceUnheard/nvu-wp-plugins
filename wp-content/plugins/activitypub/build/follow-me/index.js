(()=>{"use strict";var e,t={919:(e,t,r)=>{const o=window.wp.blocks,n=window.wp.primitives;var a=r(848);const i=(0,a.jsx)(n.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",children:(0,a.jsx)(n.Path,{d:"M15.5 9.5a1 1 0 100-2 1 1 0 000 2zm0 1.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5zm-2.25 6v-2a2.75 2.75 0 00-2.75-2.75h-4A2.75 2.75 0 003.75 15v2h1.5v-2c0-.69.56-1.25 1.25-1.25h4c.69 0 1.25.56 1.25 1.25v2h1.5zm7-2v2h-1.5v-2c0-.69-.56-1.25-1.25-1.25H15v-1.5h2.5A2.75 2.75 0 0120.25 15zM9.5 8.5a1 1 0 11-2 0 1 1 0 012 0zm1.5 0a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z",fillRule:"evenodd"})});var l=r(609);const c=window.wp.blockEditor,s=window.wp.i18n,u=window.wp.data,p=window.wp.coreData,d=window.wp.components,m=window.wp.element;function v(){return window._activityPubOptions||{}}const f=window.wp.apiFetch;var b=r.n(f);function y(e){return`var(--wp--preset--color--${e})`}function _(e){if("string"!=typeof e)return null;if(e.match(/^#/))return e.substring(0,7);const[,,t]=e.split("|");return y(t)}function h(e,t,r=null,o=""){return r?`${e}${o} { ${t}: ${r}; }\n`:""}function w(e,t,r,o){return h(e,"background-color",t)+h(e,"color",r)+h(e,"background-color",o,":hover")+h(e,"background-color",o,":focus")}function g({selector:e,style:t,backgroundColor:r}){const o=function(e,t,r){const o=`${e} .components-button`,n=("string"==typeof(a=r)?y(a):a?.color?.background||null)||t?.color?.background;var a;return w(o,_(t?.elements?.link?.color?.text),n,_(t?.elements?.link?.[":hover"]?.color?.text))}(e,t,r);return(0,l.createElement)("style",null,o)}const E=(0,a.jsx)(n.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",children:(0,a.jsx)(n.Path,{fillRule:"evenodd",clipRule:"evenodd",d:"M5 4.5h11a.5.5 0 0 1 .5.5v11a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V5a.5.5 0 0 1 .5-.5ZM3 5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5Zm17 3v10.75c0 .69-.56 1.25-1.25 1.25H6v1.5h12.75a2.75 2.75 0 0 0 2.75-2.75V8H20Z"})}),k=(0,a.jsx)(n.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",children:(0,a.jsx)(n.Path,{d:"M16.7 7.1l-6.3 8.5-3.3-2.5-.9 1.2 4.5 3.4L17.9 8z"})}),x=(0,m.forwardRef)((function({icon:e,size:t=24,...r},o){return(0,m.cloneElement)(e,{width:t,height:t,...r,ref:o})})),S=window.wp.compose,C="fediverse-remote-user";function O(e){try{return new URL(e),!0}catch(e){return!1}}function N({actionText:e,copyDescription:t,handle:r,resourceUrl:o,myProfile:n=!1,rememberProfile:a=!1}){const c=(0,s.__)("Loading...","activitypub"),u=(0,s.__)("Opening...","activitypub"),p=(0,s.__)("Error","activitypub"),v=(0,s.__)("Invalid","activitypub"),f=n||(0,s.__)("My Profile","activitypub"),[y,_]=(0,m.useState)(e),[h,w]=(0,m.useState)(E),g=(0,S.useCopyToClipboard)(r,(()=>{w(k),setTimeout((()=>w(E)),1e3)})),[N,I]=(0,m.useState)(""),[R,U]=(0,m.useState)(!0),{setRemoteUser:$}=function(){const[e,t]=(0,m.useState)(function(){const e=localStorage.getItem(C);return e?JSON.parse(e):{}}()),r=(0,m.useCallback)((e=>{!function(e){localStorage.setItem(C,JSON.stringify(e))}(e),t(e)}),[]),o=(0,m.useCallback)((()=>{localStorage.removeItem(C),t({})}),[]);return{template:e?.template||!1,profileURL:e?.profileURL||!1,setRemoteUser:r,deleteRemoteUser:o}}(),P=(0,m.useCallback)((()=>{let t;if(!O(N)&&!function(e){const t=e.replace(/^@/,"").split("@");return 2===t.length&&O(`https://${t[1]}`)}(N))return _(v),t=setTimeout((()=>_(e)),2e3),()=>clearTimeout(t);const r=o+N;_(c),b()({path:r}).then((({url:t,template:r})=>{R&&$({profileURL:N,template:r}),_(u),setTimeout((()=>{window.open(t,"_blank"),_(e)}),200)})).catch((()=>{_(p),setTimeout((()=>_(e)),2e3)}))}),[N]);return(0,l.createElement)("div",{className:"activitypub__dialog",role:"dialog","aria-labelledby":"dialog-title"},(0,l.createElement)("div",{className:"activitypub-dialog__section"},(0,l.createElement)("h4",{id:"dialog-title"},f),(0,l.createElement)("div",{className:"activitypub-dialog__description",id:"copy-description"},t),(0,l.createElement)("div",{className:"activitypub-dialog__button-group"},(0,l.createElement)("label",{htmlFor:"profile-handle",className:"screen-reader-text"},t),(0,l.createElement)("input",{type:"text",id:"profile-handle",value:r,readOnly:!0}),(0,l.createElement)(d.Button,{ref:g,"aria-label":(0,s.__)("Copy handle to clipboard","activitypub")},(0,l.createElement)(x,{icon:h}),(0,s.__)("Copy","activitypub")))),(0,l.createElement)("div",{className:"activitypub-dialog__section"},(0,l.createElement)("h4",{id:"remote-profile-title"},(0,s.__)("Your Profile","activitypub")),(0,l.createElement)("div",{className:"activitypub-dialog__description",id:"remote-profile-description"},(0,m.createInterpolateElement)((0,s.__)("Or, if you know your own profile, we can start things that way! (eg <code>@yourusername@example.com</code>)","activitypub"),{code:(0,l.createElement)("code",null)})),(0,l.createElement)("div",{className:"activitypub-dialog__button-group"},(0,l.createElement)("label",{htmlFor:"remote-profile",className:"screen-reader-text"},(0,s.__)("Enter your ActivityPub profile","activitypub")),(0,l.createElement)("input",{type:"text",id:"remote-profile",value:N,onKeyDown:e=>{"Enter"===e?.code&&P()},onChange:e=>I(e.target.value),"aria-invalid":y===v}),(0,l.createElement)(d.Button,{onClick:P,"aria-label":(0,s.__)("Submit profile","activitypub")},(0,l.createElement)(x,{icon:i}),y)),a&&(0,l.createElement)("div",{className:"activitypub-dialog__remember"},(0,l.createElement)(d.CheckboxControl,{checked:R,label:(0,s.__)("Remember me for easier comments","activitypub"),onChange:()=>{U(!R)}}))))}const I={avatar:"",webfinger:"@well@hello.dolly",name:(0,s.__)("Hello Dolly Fan Account","activitypub"),url:"#"};function R(e){if(!e)return I;const t={...I,...e};return t.avatar=t?.icon?.url,t}function U({profile:e,popupStyles:t,userId:r}){const{webfinger:o,avatar:n,name:a}=e,i=o.startsWith("@")?o:`@${o}`;return(0,l.createElement)("div",{className:"activitypub-profile"},(0,l.createElement)("img",{className:"activitypub-profile__avatar",src:n,alt:a}),(0,l.createElement)("div",{className:"activitypub-profile__content"},(0,l.createElement)("div",{className:"activitypub-profile__name"},a),(0,l.createElement)("div",{className:"activitypub-profile__handle",title:i},i)),(0,l.createElement)($,{profile:e,popupStyles:t,userId:r}))}function $({profile:e,popupStyles:t,userId:r}){const[o,n]=(0,m.useState)(!1),a=(0,s.sprintf)((0,s.__)("Follow %s","activitypub"),e?.name);return(0,l.createElement)(l.Fragment,null,(0,l.createElement)(d.Button,{className:"activitypub-profile__follow",onClick:()=>n(!0),"aria-haspopup":"dialog","aria-expanded":o,"aria-label":(0,s.__)("Follow me on the Fediverse","activitypub")},(0,s.__)("Follow","activitypub")),o&&(0,l.createElement)(d.Modal,{className:"activitypub-profile__confirm activitypub__modal",onRequestClose:()=>n(!1),title:a,"aria-label":a,role:"dialog"},(0,l.createElement)(P,{profile:e,userId:r}),(0,l.createElement)("style",null,t)))}function P({profile:e,userId:t}){const{namespace:r}=v(),{webfinger:o}=e,n=(0,s.__)("Follow","activitypub"),a=`/${r}/actors/${t}/remote-follow?resource=`,i=(0,s.__)("Copy and paste my profile into the search field of your favorite fediverse app or server.","activitypub"),c=o.startsWith("@")?o:`@${o}`;return(0,l.createElement)(N,{actionText:n,copyDescription:i,handle:c,resourceUrl:a})}function T({selectedUser:e,style:t,backgroundColor:r,id:o,useId:n=!1,profileData:a=!1}){const[i,c]=(0,m.useState)(R()),s="site"===e?0:e,u=function(e){return w(".apfmd__button-group .components-button",_(e?.elements?.link?.color?.text)||"#111","#fff",_(e?.elements?.link?.[":hover"]?.color?.text)||"#333")}(t),p=n?{id:o}:{};function d(e){c(R(e))}return(0,m.useEffect)((()=>{if(a)return d(a);(function(e){const{namespace:t}=v(),r={headers:{Accept:"application/activity+json"},path:`/${t}/actors/${e}`};return b()(r)})(s).then(d)}),[s,a]),(0,l.createElement)("div",{...p},(0,l.createElement)(g,{selector:`#${o}`,style:t,backgroundColor:r}),(0,l.createElement)(U,{profile:i,userId:s,popupStyles:u}))}function j({name:e}){const{enabled:t}=v(),r=t?.site?"":(0,s.__)("It will be empty in other non-author contexts.","activitypub"),o=(0,s.sprintf)(/* translators: %1$s: block name, %2$s: extra information for non-author context */ /* translators: %1$s: block name, %2$s: extra information for non-author context */
(0,s.__)("This <strong>%1$s</strong> block will adapt to the page it is on, displaying the user profile associated with a post author (in a loop) or a user archive. %2$s","activitypub"),e,r).trim();return(0,l.createElement)(d.Card,null,(0,l.createElement)(d.CardBody,null,(0,m.createInterpolateElement)(o,{strong:(0,l.createElement)("strong",null)})))}(0,o.registerBlockType)("activitypub/follow-me",{edit:function({attributes:e,setAttributes:t,context:{postType:r,postId:o}}){const n=(0,c.useBlockProps)({className:"activitypub-follow-me-block-wrapper"}),a=function({withInherit:e=!1}){const{enabled:t}=v(),r=t?.users?(0,u.useSelect)((e=>e("core").getUsers({who:"authors"}))):[];return(0,m.useMemo)((()=>{if(!r)return[];const o=[];return t?.site&&o.push({label:(0,s.__)("Site","activitypub"),value:"site"}),e&&t?.users&&o.push({label:(0,s.__)("Dynamic User","activitypub"),value:"inherit"}),r.reduce(((e,t)=>(e.push({label:t.name,value:`${t.id}`}),e)),o)}),[r])}({withInherit:!0}),{selectedUser:i}=e,f="inherit"===i,b=(0,u.useSelect)((e=>{const{getEditedEntityRecord:t}=e(p.store),n=t("postType",r,o)?.author;return null!=n?n:null}),[r,o]);return(0,m.useEffect)((()=>{a.length&&(a.find((({value:e})=>e===i))||t({selectedUser:a[0].value}))}),[i,a]),(0,l.createElement)("div",{...n},a.length>1&&(0,l.createElement)(c.InspectorControls,{key:"setting"},(0,l.createElement)(d.PanelBody,{title:(0,s.__)("Followers Options","activitypub")},(0,l.createElement)(d.SelectControl,{label:(0,s.__)("Select User","activitypub"),value:e.selectedUser,options:a,onChange:e=>t({selectedUser:e})}))),f?b?(0,l.createElement)(T,{...e,id:n.id,selectedUser:b}):(0,l.createElement)(j,{name:(0,s.__)("Follow Me","activitypub")}):(0,l.createElement)(T,{...e,id:n.id}))},save:()=>null,icon:i})},20:(e,t,r)=>{var o=r(609),n=Symbol.for("react.element"),a=(Symbol.for("react.fragment"),Object.prototype.hasOwnProperty),i=o.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,l={key:!0,ref:!0,__self:!0,__source:!0};t.jsx=function(e,t,r){var o,c={},s=null,u=null;for(o in void 0!==r&&(s=""+r),void 0!==t.key&&(s=""+t.key),void 0!==t.ref&&(u=t.ref),t)a.call(t,o)&&!l.hasOwnProperty(o)&&(c[o]=t[o]);if(e&&e.defaultProps)for(o in t=e.defaultProps)void 0===c[o]&&(c[o]=t[o]);return{$$typeof:n,type:e,key:s,ref:u,props:c,_owner:i.current}}},848:(e,t,r)=>{e.exports=r(20)},609:e=>{e.exports=window.React}},r={};function o(e){var n=r[e];if(void 0!==n)return n.exports;var a=r[e]={exports:{}};return t[e](a,a.exports,o),a.exports}o.m=t,e=[],o.O=(t,r,n,a)=>{if(!r){var i=1/0;for(u=0;u<e.length;u++){for(var[r,n,a]=e[u],l=!0,c=0;c<r.length;c++)(!1&a||i>=a)&&Object.keys(o.O).every((e=>o.O[e](r[c])))?r.splice(c--,1):(l=!1,a<i&&(i=a));if(l){e.splice(u--,1);var s=n();void 0!==s&&(t=s)}}return t}a=a||0;for(var u=e.length;u>0&&e[u-1][2]>a;u--)e[u]=e[u-1];e[u]=[r,n,a]},o.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return o.d(t,{a:t}),t},o.d=(e,t)=>{for(var r in t)o.o(t,r)&&!o.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},o.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e={338:0,301:0};o.O.j=t=>0===e[t];var t=(t,r)=>{var n,a,[i,l,c]=r,s=0;if(i.some((t=>0!==e[t]))){for(n in l)o.o(l,n)&&(o.m[n]=l[n]);if(c)var u=c(o)}for(t&&t(r);s<i.length;s++)a=i[s],o.o(e,a)&&e[a]&&e[a][0](),e[a]=0;return o.O(u)},r=globalThis.webpackChunkwordpress_activitypub=globalThis.webpackChunkwordpress_activitypub||[];r.forEach(t.bind(null,0)),r.push=t.bind(null,r.push.bind(r))})();var n=o.O(void 0,[301],(()=>o(919)));n=o.O(n)})();