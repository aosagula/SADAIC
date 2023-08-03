(function () {
'use strict';

/******/(function(modules){// webpackBootstrap
/******/ // The module cache
/******/var installedModules={};
/******/
/******/ // The require function
/******/function __webpack_require__(moduleId){
/******/
/******/ // Check if module is in cache
/******/if(installedModules[moduleId]){
/******/return installedModules[moduleId].exports;
/******/}
/******/ // Create a new module (and put it into the cache)
/******/var module=installedModules[moduleId]={
/******/i:moduleId,
/******/l:false,
/******/exports:{}
/******/};
/******/
/******/ // Execute the module function
/******/modules[moduleId].call(module.exports,module,module.exports,__webpack_require__);
/******/
/******/ // Flag the module as loaded
/******/module.l=true;
/******/
/******/ // Return the exports of the module
/******/return module.exports;
/******/}
/******/
/******/
/******/ // expose the modules object (__webpack_modules__)
/******/__webpack_require__.m=modules;
/******/
/******/ // expose the module cache
/******/__webpack_require__.c=installedModules;
/******/
/******/ // define getter function for harmony exports
/******/__webpack_require__.d=function(exports,name,getter){
/******/if(!__webpack_require__.o(exports,name)){
/******/Object.defineProperty(exports,name,{enumerable:true,get:getter});
/******/}
/******/};
/******/
/******/ // define __esModule on exports
/******/__webpack_require__.r=function(exports){
/******/if(typeof Symbol!=='undefined'&&Symbol.toStringTag){
/******/Object.defineProperty(exports,Symbol.toStringTag,{value:'Module'});
/******/}
/******/Object.defineProperty(exports,'__esModule',{value:true});
/******/};
/******/
/******/ // create a fake namespace object
/******/ // mode & 1: value is a module id, require it
/******/ // mode & 2: merge all properties of value into the ns
/******/ // mode & 4: return value when already ns object
/******/ // mode & 8|1: behave like require
/******/__webpack_require__.t=function(value,mode){
/******/if(mode&1)value=__webpack_require__(value);
/******/if(mode&8)return value;
/******/if(mode&4&&typeof value==='object'&&value&&value.__esModule)return value;
/******/var ns=Object.create(null);
/******/__webpack_require__.r(ns);
/******/Object.defineProperty(ns,'default',{enumerable:true,value:value});
/******/if(mode&2&&typeof value!='string')for(var key in value){__webpack_require__.d(ns,key,function(key){return value[key];}.bind(null,key));}
/******/return ns;
/******/};
/******/
/******/ // getDefaultExport function for compatibility with non-harmony modules
/******/__webpack_require__.n=function(module){
/******/var getter=module&&module.__esModule?
/******/function getDefault(){return module['default'];}:
/******/function getModuleExports(){return module;};
/******/__webpack_require__.d(getter,'a',getter);
/******/return getter;
/******/};
/******/
/******/ // Object.prototype.hasOwnProperty.call
/******/__webpack_require__.o=function(object,property){return Object.prototype.hasOwnProperty.call(object,property);};
/******/
/******/ // __webpack_public_path__
/******/__webpack_require__.p="/";
/******/
/******/
/******/ // Load entry module and return exports
/******/return __webpack_require__(__webpack_require__.s=7);
/******/})(
/************************************************************************/
/******/{

/***/"./resources/js/jingles.index.js":
/*!***************************************!*\
  !*** ./resources/js/jingles.index.js ***!
  \***************************************/
/*! no static exports found */
/***/function resourcesJsJinglesIndexJs(module,exports){

// Inicialización dataTables
var $dt=$('.table').DataTable({
ajax:'/jingles/datatables',
language:{
url:'/localisation/datatables.json'},

serverSide:true,
columns:[{
name:'id',
data:'id'},
{
name:'product_name',
data:'product_name'},
{
name:'work_title',
data:'work_title'},
{
name:'status_id',
data:'status.name'},
{
orderable:false,
data:null,
"class":'text-center',
render:function render(data,type){
if(type=='display'){
return "<a href=\"/jingles/".concat(data.id,"\">Ver</a>");
}

return null;
}}],

searchCols:[null,null,null,{
search:-1},
// Status "Todos"
null],
order:[[1,'asc']],
// Orden por defecto: id ascendente
initComplete:function initComplete(){
// Reemplazamos la búsqueda por defecto por un select con los estados de los trámites
var statusSelect="<select id=\"statusFilter\">\n            <option value=\"-1\">Todos</option>\n            ".concat(statusOptions.map(function(opt){
return "<option value=\"".concat(opt.id,"\">").concat(opt.name,"</option>");
}).join(),"\n        </select>");
$('.dataTables_filter').html(statusSelect);
$('#statusFilter').on('change',function(event){
$dt.column('status_id:name').search(event.target.value).draw();
});
}});


/***/},

/***/7:
/*!*********************************************!*\
  !*** multi ./resources/js/jingles.index.js ***!
  \*********************************************/
/*! no static exports found */
/***/function _(module,exports,__webpack_require__){

module.exports=__webpack_require__(/*! /app/resources/js/jingles.index.js */"./resources/js/jingles.index.js");


/***/}

/******/});

}());
