!function(){"use strict";var e={n:function(t){var n=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(n,{a:n}),n},d:function(t,n){for(var a in n)e.o(n,a)&&!e.o(t,a)&&Object.defineProperty(t,a,{enumerable:!0,get:n[a]})},o:function(e,t){return Object.prototype.hasOwnProperty.call(e,t)}},t=window.wp.element,n=window.wp.blocks,a=JSON.parse('{"u2":"am/faux-feeds-for-tiktok"}'),l=window.wp.i18n,i=window.React,o=window.wp.components,r=window.wp.data,c=window.wp.apiFetch,s=e.n(c);const m=()=>(0,t.createElement)(o.Icon,{icon:(0,t.createElement)("svg",{width:"14",height:"14",viewBox:"0 0 14 14",fill:"none",xmlns:"http://www.w3.org/2000/svg"},(0,t.createElement)("path",{d:"M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z",fill:"#434960"})),size:14}),d=()=>(0,t.createElement)(o.Icon,{icon:"admin-plugins",size:50,style:{height:66,width:66,display:"flex",justifyContent:"center",alignItems:"center"}}),u=()=>(0,t.createElement)(o.Icon,{icon:(0,t.createElement)("svg",{width:"26",height:"29",viewBox:"0 0 26 29",fill:"none",xmlns:"http://www.w3.org/2000/svg"},(0,t.createElement)("path",{d:"M10.6667 1.03341C11.0889 0.78897 11.5333 0.666748 12 0.666748C12.4667 0.666748 12.9111 0.78897 13.3333 1.03341L22.6667 6.40008C23.0889 6.64453 23.4167 6.96675 23.65 7.36675C23.8833 7.76675 24 8.21119 24 8.70008V14.3334C23.2444 13.8001 22.4167 13.389 21.5167 13.1001C20.6167 12.8112 19.6667 12.6667 18.6667 12.6667C18.2889 12.6667 17.9278 12.6834 17.5833 12.7167C17.2389 12.7501 16.8889 12.8112 16.5333 12.9001L21.3333 10.1334V8.73342L19.9 7.90008L12 12.4667L4.1 7.90008L2.66667 8.73342V10.1334L10.6667 14.7667V17.2001C10.2444 17.9112 9.91667 18.6667 9.68333 19.4667C9.45 20.2667 9.33333 21.1112 9.33333 22.0001C9.33333 22.9334 9.46111 23.8223 9.71667 24.6667C9.97222 25.5112 10.3333 26.3001 10.8 27.0334C10.7778 27.0112 10.7556 27.0001 10.7333 27.0001C10.7111 27.0001 10.6889 26.989 10.6667 26.9667L1.33333 21.6001C0.911111 21.3556 0.583333 21.0334 0.35 20.6334C0.116667 20.2334 0 19.789 0 19.3001V8.70008C0 8.21119 0.116667 7.76675 0.35 7.36675C0.583333 6.96675 0.911111 6.64453 1.33333 6.40008L10.6667 1.03341ZM18.6667 26.0001L22.6667 22.0001L21.7333 21.0667L19.3333 23.4667V18.0001H18V23.4667L15.6 21.0667L14.6667 22.0001L18.6667 26.0001ZM18.6667 28.6667C16.8222 28.6667 15.25 28.0168 13.95 26.7167C12.65 25.4167 12 23.8445 12 22.0001C12 20.1556 12.65 18.5834 13.95 17.2834C15.25 15.9834 16.8222 15.3334 18.6667 15.3334C20.5111 15.3334 22.0833 15.9834 23.3833 17.2834C24.6833 18.5834 25.3333 20.1556 25.3333 22.0001C25.3333 23.8445 24.6833 25.4167 23.3833 26.7167C22.0833 28.0168 20.5111 28.6667 18.6667 28.6667Z",fill:"#0068A0"})),size:26});var p=e=>{const{pluginName:n}=e;return(0,t.createElement)("div",{className:"am-faux-block-preview"},(0,t.createElement)(u,null),(0,t.createElement)("div",{className:"am-faux-block-preview-text"},(0,t.createElement)("p",null,(0,t.createElement)("strong",null,n+" required")),(0,t.createElement)("p",null,"Add this block to install it.")))};(0,n.registerBlockType)(a.u2,{attributes:{text:{type:"string",default:"TikTok Feeds"},pluginName:{type:"string",default:"Feeds for TikTok"},pluginPath:{type:"string",default:"feeds-for-tiktok/feeds-for-tiktok.php"},logo:{type:"string",default:(0,t.createElement)((()=>(0,t.createElement)(o.Icon,{icon:(0,t.createElement)("svg",{width:"86",height:"83",viewBox:"0 0 86 83",fill:"none",xmlns:"http://www.w3.org/2000/svg"},(0,t.createElement)("rect",{x:"1",y:"4.43494",width:"65.6329",height:"65.6329",rx:"12",transform:"rotate(-3 1 4.43494)",fill:"white"}),(0,t.createElement)("rect",{x:"1",y:"4.43494",width:"65.6329",height:"65.6329",rx:"12",transform:"rotate(-3 1 4.43494)",stroke:"#CED0D9",strokeWidth:"2.5003"}),(0,t.createElement)("path",{d:"M42.4718 30.2399C44.6196 31.6106 47.2015 32.348 49.9408 32.2044L49.6647 26.9359C49.1463 26.9632 48.6263 26.9363 48.1135 26.8555L48.3309 31.0025C45.5918 31.1461 43.0103 30.4086 40.8619 29.0381L41.4253 39.7895C41.7072 45.1679 37.5734 49.7563 32.1926 50.0383C30.1849 50.1435 28.287 49.6347 26.6823 48.6754C28.5463 50.3908 31.0725 51.3832 33.8022 51.2401C39.1833 50.9581 43.3173 46.3697 43.0355 40.9911L42.472 30.2399L42.4718 30.2399ZM44.0963 24.8249C42.9777 23.725 42.2047 22.2684 41.9679 20.6256L41.9324 19.948L40.4705 20.0246C40.9484 22.1033 42.2975 23.8299 44.0963 24.8249ZM29.8694 44.3699C29.2376 43.6261 28.8685 42.6952 28.8189 41.7207C28.69 39.2608 30.5807 37.1617 33.0424 37.0327C33.5011 37.0086 33.9608 37.0549 34.4053 37.1706L34.123 31.7844C33.6084 31.7412 33.0912 31.7385 32.5763 31.7764L32.796 35.9688C32.3512 35.8531 31.8913 35.8066 31.4325 35.831C28.9708 35.9601 27.0802 38.0589 27.2091 40.5191C27.3003 42.2587 28.3765 43.7124 29.8694 44.3699Z",fill:"#FF004F"}),(0,t.createElement)("path",{d:"M40.8619 29.038C43.0103 30.4085 45.5919 31.1459 48.3309 31.0024L48.1136 26.8553C46.5676 26.61 45.1722 25.8823 44.0963 24.8249C42.2974 23.8297 40.9485 22.1032 40.4706 20.0246L36.6305 20.2258L37.7333 41.2687C37.8532 43.7223 35.9654 45.8131 33.5091 45.9418C32.0616 46.0177 30.7395 45.3955 29.8691 44.3698C28.3763 43.7124 27.3001 42.2585 27.2089 40.5192C27.08 38.0592 28.9707 35.9602 31.4323 35.8311C31.9039 35.8064 32.3624 35.856 32.7959 35.9689L32.5762 31.7765C27.2956 32.1627 23.2704 36.7027 23.5486 42.0122C23.6875 44.6627 24.8722 47.01 26.6825 48.6755C28.2872 49.6346 30.185 50.1436 32.1928 50.0384C37.5737 49.7564 41.7074 45.1678 41.4255 39.7895L40.862 29.038L40.8619 29.038Z",fill:"black"}),(0,t.createElement)("path",{d:"M48.1135 26.8553L48.0547 25.7339C46.6761 25.8083 45.3042 25.4932 44.0963 24.8247C45.1911 25.9035 46.5956 26.6134 48.1135 26.8553ZM40.4705 20.0245C40.4249 19.8259 40.3873 19.6255 40.358 19.4238L40.3225 18.7462L35.0205 19.0241L36.1233 40.0672C36.2433 42.5205 34.3557 44.6112 31.8991 44.7399C31.1779 44.7777 30.488 44.6423 29.869 44.3699C30.7394 45.3954 32.0615 46.0175 33.509 45.9416C35.9651 45.8129 37.8533 43.7224 37.7333 41.2687L36.6305 20.2258L40.4705 20.0245ZM32.5763 31.7764L32.5137 30.5827C32.0675 30.5454 31.6193 30.5384 31.1721 30.5621C25.7907 30.844 21.6569 35.4326 21.9388 40.8106C22.1155 44.1822 23.9856 47.0638 26.6825 48.6752C24.8722 47.0098 23.6875 44.6624 23.5486 42.012C23.2704 36.7026 27.2955 32.1626 32.5763 31.7764Z",fill:"#00F2EA"}),(0,t.createElement)("path",{d:"M54.2493 60.4452C54.2493 68.7888 59.2323 75.931 66.0155 77.1715L65.6194 78.4257L65.2177 79.6978L66.547 79.5848L71.0345 79.2031L72.7237 79.0594L71.6502 77.7473L71.0255 76.9839C77.3765 75.3291 81.9509 68.4326 81.9509 60.4452C81.9509 51.2906 75.9193 43.5316 68.1014 43.5316C60.2838 43.5316 54.2493 51.2904 54.2493 60.4452Z",fill:"#FE544F",stroke:"white",strokeWidth:"1.78661"}),(0,t.createElement)("path",{fillRule:"evenodd",clipRule:"evenodd",d:"M71.1909 50.0573L71.8144 56.491L78.2752 56.6766L73.6016 61.0221L77.2929 66.3611L71.0743 65.1921L69.189 71.4108L66.3266 65.842L60.5468 68.4904L62.7702 62.5204L57.1333 59.6772L63.1675 57.7366L61.5024 51.7922L67.2111 55.0468L71.1909 50.0573Z",fill:"white"})),size:66})),null)},description:{type:"string",default:"Feeds for TikTok is a highly customizable way to display feeds from your TikTok account. Promote your latest content and update your site content automatically."},preview:{type:"boolean",default:!1}},edit:function(e){const{attributes:n}=e,{pluginPath:a,pluginName:c,logo:u,description:C,preview:L}=n,w="am-faux-blocks",{removeBlock:h}=(0,r.useDispatch)("core/block-editor"),{autosave:f}=(0,r.useDispatch)("core/editor"),{getEditedPostContent:E}=(0,r.useSelect)("core/editor"),[g,v]=(0,i.useState)(!1),k=()=>{h(e.clientId),E()&&f(),v(!1),function(e){s()({path:"/wp/v2/plugins"}).then((t=>{t.forEach((t=>{const n=t.plugin,a=t.status;n.slice(0,n.indexOf("/"))===n.slice(0,e.indexOf("/"))&&"active"===a&&setTimeout((()=>{window.location=window.location.href.split("#")[0]}),"1000")}))}))}(a)};return(0,i.useEffect)((()=>{v(!0)}),[]),L?(0,t.createElement)(p,{pluginName:c}):(0,t.createElement)(t.Fragment,null,(0,t.createElement)(o.Modal,{__experimentalHideHeader:!0,show:g,onRequestClose:k,className:"am-faux-block-modal"},(0,t.createElement)("div",{className:"am-modal-header"},(0,t.createElement)("div",{className:"am-modal-title-wrap"},(0,t.createElement)("span",{className:"am-modal-icon"},u||(0,t.createElement)(d,null)),(0,t.createElement)("div",{className:"am-modal-title"},(0,t.createElement)("div",{className:"am-requires"},(0,l.__)("REQUIRES",w)),(0,t.createElement)("div",{className:"am-title"},(0,t.createElement)("h2",null,c),(0,t.createElement)("span",{className:"am-plugin-tag"},(0,l.__)("FREE",w)))))),(0,t.createElement)("div",{className:"am-modal-content-wrap"},(0,t.createElement)("div",{className:"am-modal-content"},(0,t.createElement)("p",null,C),(0,t.createElement)(o.Button,{className:"am-modal-cta-btn",onClick:()=>{const e=fauxData.nonce,t=fauxData.siteUrl,n=document.querySelector(".am-modal-cta-btn"),l=document.querySelector(".components-spinner"),i={action:"am_faux_install",nonce:e,plugin:a},o=new URL(t);o.search=new URLSearchParams(i).toString(),l.style="display: inline-block",fetch(o,{cache:"no-cache",headers:{"user-agent":"Faux Block","content-type":"application/json"},method:"POST"}).then((e=>{e.ok?(n.innerHTML="Installed and activated!",l.style="display: none",n.addEventListener("click",(function(e){k()}),!1)):n.innerHTML="There was a problem with the plugin install"}))},variant:"primary"},(0,t.createElement)(t.Fragment,null,(0,l.__)("Install",w),(0,t.createElement)(o.Spinner,null))))),(0,t.createElement)(o.Button,{icon:m,onClick:k,className:"am-modal-close"})))},save:function(){return(0,t.createElement)(t.Fragment,null)}})}();