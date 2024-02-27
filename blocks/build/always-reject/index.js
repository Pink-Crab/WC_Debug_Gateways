(()=>{"use strict";const e=window.React,t=window.wc.wcBlocksRegistry,n=window.wc.wcSettings,s=(window.wp.i18n,window.wp.element),l=window.wp.components,a=({eventRegistration:t,emitResponse:n})=>{const[a,c]=(0,s.useState)(!1),{onPaymentSetup:o}=t;return(0,s.useEffect)((()=>{const e=o((async()=>(console.log(a),a?{type:n.responseTypes.ERROR,error:{message:"Client side rejection"}}:{type:n.responseTypes.SUCCESS})));return()=>{e()}}),[n.responseTypes.ERROR,n.responseTypes.SUCCESS,o,a]),(0,e.createElement)(e.Fragment,null,(0,e.createElement)("style",null,"\n    .pc-gateway-reject label {\n        position: inherit;\n    }\n    "),(0,e.createElement)("div",{className:"wc-block-gateway-container wc-inline-card-element pc-gateway-reject"},(0,e.createElement)(l.RadioControl,{label:"Rejection Type",help:"Should the form reject the user on the client side or server side?",selected:a?"c":"s",options:[{label:"Client Side",value:"c"},{label:"Server Side",value:"s"}],onChange:e=>c("c"===e)})))},c=t=>(0,e.createElement)(a,{...t}),o=()=>(0,n.getSetting)("pc_always_reject_data",{});console.log(o());const r={name:"pc_always_reject",label:o().title,content:(0,e.createElement)(c,null),edit:(0,e.createElement)(c,null),canMakePayment:()=>!0,ariaLabel:o().title,supports:{features:o().supports}};(0,t.registerPaymentMethod)(r)})();