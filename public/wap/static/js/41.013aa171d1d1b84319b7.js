webpackJsonp([41],{xJOu:function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var s=i("Gu7T"),o=i.n(s),a={data:function(){return{page:1,pageSize:10,list:[]}},created:function(){this.goodsCollectionList()},methods:{showDetail:function(t){this.$router.push({path:"/goodsdetail",query:{goods_id:t}})},goodsCollectionList:function(){var t=this;this.$api.goodsCollectionList({page:this.page,limit:this.pageSize,token:this.GLOBAL.getStorage("user_token")},function(e){var i=t.dateFormat(e.data.list);t.list=[].concat(o()(i)),i.length<t.pageSize&&t.$refs.infinitescrollDemo.$emit("ydui.infinitescroll.loadedDone")})},loadMore:function(){var t=this;this.$api.goodsCollectionList({page:++this.page,limit:this.pageSize,token:this.GLOBAL.getStorage("user_token")},function(e){var i=t.dateFormat(e.data.list);t.list=[].concat(o()(t.list),o()(i)),i.length<t.pageSize?t.$refs.infinitescrollDemo.$emit("ydui.infinitescroll.loadedDone"):t.$refs.infinitescrollDemo.$emit("ydui.infinitescroll.finishLoad")})},dateFormat:function(t){for(var e in t)t[e].ctime=this.GLOBAL.timeToDate(t[e].ctime);return t},skip:function(){},touchStart:function(t){this.startX=t.touches[0].clientX},touchEnd:function(t){var e=t.currentTarget.parentElement;this.endX=t.changedTouches[0].clientX,"0"===e.dataset.type&&this.startX-this.endX>30&&(this.restSlide(),e.dataset.type=1),"1"===e.dataset.type&&this.startX-this.endX<-30&&(this.restSlide(),e.dataset.type=0),this.startX=0,this.endX=0},checkSlide:function(){for(var t=document.querySelectorAll(".list-item"),e=0;e<t.length;e++)if("1"===t[e].dataset.type)return!0;return!1},restSlide:function(){for(var t=document.querySelectorAll(".list-item"),e=0;e<t.length;e++)t[e].dataset.type=0},deleteItem:function(t){var e=this,i=t.currentTarget.dataset.index;this.$api.goodsCollection({goods_id:this.list[i].goods_id},function(t){t.status&&(e.$dialog.toast({mes:t.msg,timeout:1e3,icon:"success"}),e.restSlide(),e.list.splice(i,1))})}}},n={render:function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{staticClass:"collect"},[s("yd-infinitescroll",{ref:"infinitescrollDemo",attrs:{callback:t.loadMore,distance:2}},[t.list.length?s("ul",{attrs:{slot:"list"},slot:"list"},t._l(t.list,function(e,o){return s("li",{key:o,staticClass:"list-item",attrs:{"data-type":"0"}},[s("div",{staticClass:"list-box",staticStyle:{padding:".15rem"},on:{"!touchstart":function(e){return t.touchStart(e)},"!touchend":function(e){return t.touchEnd(e)},click:function(i){t.showDetail(e.goods_id)}}},[s("img",{directives:[{name:"lazy",rawName:"v-lazy",value:e.goods.image_url,expression:"item.goods.image_url"}],staticClass:"goodsimg",attrs:{slot:"img"},slot:"img"}),t._v(" "),s("div",{staticClass:"list-body"},[s("h3",{staticClass:"goodsname",attrs:{slot:"title"},slot:"title"},[t._v(t._s(e.goods_name))]),t._v(" "),s("div",{staticClass:"btn-numbox"},[s("div",[s("span",{staticClass:"demo-list-price"},[s("em",[t._v("¥")]),t._v(t._s(e.goods.price))])])]),t._v(" "),s("div",{attrs:{slot:"other"},slot:"other"},[s("div",[s("span",{staticClass:"time"},[t._v(t._s(e.ctime))])])]),t._v(" "),s("img",{staticClass:"right-img",attrs:{slot:"other",src:i("oenc")},slot:"other"})])]),t._v(" "),s("div",{staticClass:"delete",attrs:{"data-index":o},on:{click:t.deleteItem}},[t._v("取消收藏")])])})):t._e()])],1)},staticRenderFns:[]},l=i("VU/8")(a,n,!1,null,null,null);e.default=l.exports}});
//# sourceMappingURL=41.013aa171d1d1b84319b7.js.map