(()=>{"use strict";const e=window.React,t=window.wc.wcBlocksRegistry,n=window.wc.wcSettings,l=window.wp.i18n,a=window.wp.element,c=window.wp.components,r=({eventRegistration:t,emitResponse:n})=>{const[r,s]=(0,a.useState)({}),[o,m]=(0,a.useState)(""),[i,u]=(0,a.useState)(""),{onPaymentSetup:w}=t;return(0,a.useEffect)((()=>{const e=w((async()=>({type:n.responseTypes.SUCCESS,meta:{paymentMethodData:{pc_always_confirm_meta:JSON.stringify(r)}}})));return()=>{e()}}),[n.responseTypes.ERROR,n.responseTypes.SUCCESS,w,r]),(0,e.createElement)(e.Fragment,null,(0,e.createElement)("div",{className:"wc-block-gateway-container wc-inline-card-element"},(0,e.createElement)("div",null,(0,e.createElement)("ul",null,Object.keys(r).map((t=>(0,e.createElement)("li",{key:t,id:t,className:"payment-meta-row"},(0,e.createElement)(c.Flex,null,(0,e.createElement)(c.FlexItem,null,(0,e.createElement)("strong",null,t,":")),(0,e.createElement)(c.FlexBlock,null,r[t]),(0,e.createElement)(c.FlexItem,null,(0,e.createElement)(c.Button,{onClick:()=>{const e={...r};delete e[t],s(e)}},(0,l.__)("Remove","pinkcrab-debug-gateways")))))))),(0,e.createElement)("p",null,(0,l.__)("Add Meta Key/Value","pinkcrab-debug-gateways")),(0,e.createElement)(c.Flex,null,(0,e.createElement)(c.FlexItem,null,(0,e.createElement)(c.TextControl,{placeholder:(0,l.__)("Meta Key","pinkcrab-debug-gateways"),value:o,onChange:e=>m(e)})),(0,e.createElement)(c.FlexBlock,null,(0,e.createElement)(c.TextControl,{placeholder:(0,l.__)("Meta Value","pinkcrab-debug-gateways"),value:i,onChange:e=>u(e),style:{width:"100%"}})),(0,e.createElement)(c.FlexItem,null,(0,e.createElement)(c.Button,{onClick:()=>{var e,t;""!==o&&""!==i&&(e=o,t=i,s({...r,[e]:t}),m(""),u(""))},variant:"secondary",sizw:"small"},(0,l.__)("Add","pinkcrab-debug-gateways")))))))},s=t=>(0,e.createElement)(r,{...t}),o=()=>(0,n.getSetting)("pc_always_confirm_data",{});console.log(o());const m={name:"pc_always_confirm",label:o().title,content:(0,e.createElement)(s,null),edit:(0,e.createElement)(s,null),canMakePayment:()=>!0,ariaLabel:o().title,supports:{features:o().supports}};(0,t.registerPaymentMethod)(m)})();