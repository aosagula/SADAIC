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
/******/return __webpack_require__(__webpack_require__.s=6);
/******/})(
/************************************************************************/
/******/{

/***/"./resources/js/works.view.js":
/*!************************************!*\
  !*** ./resources/js/works.view.js ***!
  \************************************/
/*! no static exports found */
/***/function resourcesJsWorksViewJs(module,exports){

$('#beginAction').on('click',function(){
axios.post("/works/".concat(workId,"/status"),{
status:'beginAction'}).
then(function(_ref){
var data=_ref.data;

if(data.errors){
data.errors.forEach(function(e){
toastr.warning(e);
});
}

if(data.status=='success'){
toastr.success('Proceso iniciado');
setTimeout(function(){
location.reload();
},1000);
}
})["catch"](function(err){
toastr.error('Se encontró un problema mientras se realizaba la solicitud');
});
});
$('#modalRejection').on('show.bs.modal',function(){
$('textarea','#modalRejection').val('');
});
$('#modalRejection').on('shown.bs.modal',function(){
$('textarea','#modalRejection').focus();
});
$('#rejectAction').on('click',function(event){
axios.post("/works/".concat(workId,"/status"),{
status:'rejectAction',
reason:$('textarea','#modalRejection').val()}).
then(function(_ref2){
var data=_ref2.data;

if(data.status=='failed'){
toastr.error('No se pudo realizar el rechazo de la solicitud.');
data.errors.forEach(function(e){
toastr.warning(e);
});
}else if(data.status=='success'){
toastr.success('Rechazo guardado correctamente');
setTimeout(function(){
location.reload();
},1000);
}
});
});
$('#sendToInternal').on('click',function(){
axios.post("/works/".concat(workId,"/status"),{
status:'sendToInternal'}).
then(function(_ref3){
var data=_ref3.data;

if(data.status=='failed'){
toastr.error('No se pudo registrar el pase a sistema interno de la solicitud.');
data.errors.forEach(function(e){
toastr.warning(e);
});
}else if(data.status=='success'){
toastr.success('Pase registrado correctamente');
setTimeout(function(){
location.reload();
},1000);
}
});
});
$('#finishRequest').on('click',function(){
axios.post("/works/".concat(workId,"/status"),{
status:'finishRequest'}).
then(function(_ref4){
var data=_ref4.data;

if(data.status=='failed'){
toastr.error('No se pudo registrar la finalización del trámite.');
data.errors.forEach(function(e){
toastr.warning(e);
});
}else if(data.status=='success'){
toastr.success('Finalización registrada correctamente');
setTimeout(function(){
location.reload();
},1000);
}
});
});
$('.acceptDistribution').on('click',function(event){
axios.post("/works/".concat(workId,"/response"),{
response:'accept',
distribution_id:$(event.target).data('did')}).
then(function(_ref5){
var data=_ref5.data;

if(data.status=='failed'){
toastr.error('No se puedo cambiar la respuesta a la solicitud.');
data.errors.forEach(function(e){
toastr.warning(e);
});
}else if(data.status=='success'){
toastr.success('Respuesta cambiada correctamente');
setTimeout(function(){
location.reload();
},1000);
}
})["catch"](function(err){
toastr.error('Se encontró un problema mientras se realizaba la solicitud');
});
});
$('.rejectDistribution').on('click',function(event){
axios.post("/works/".concat(workId,"/response"),{
response:'reject',
distribution_id:$(event.target).data('did')}).
then(function(_ref6){
var data=_ref6.data;

if(data.status=='failed'){
toastr.error('No se puedo cambiar la respuesta a la solicitud.');
data.errors.forEach(function(e){
toastr.warning(e);
});
}else if(data.status=='success'){
toastr.success('Respuesta cambiada correctamente');
setTimeout(function(){
location.reload();
},1000);
}
})["catch"](function(err){
toastr.error('Se encontró un problema mientras se realizaba la solicitud');
});
});
$('#saveObservations').on('click',function(event){
axios.post("/works/".concat(workId,"/observations"),{
content:$('#observations').val()}).
then(function(_ref7){
var data=_ref7.data;

if(data.status=='failed'){
toastr.error('No se puedo guardar las observaciones');
}else if(data.status=='success'){
toastr.success('Se guardaron las observaciones');
}
})["catch"](function(err){
toastr.error('Se encontró un problema mientras se guardaban las observaciones');
});
});

/***/},

/***/6:
/*!******************************************!*\
  !*** multi ./resources/js/works.view.js ***!
  \******************************************/
/*! no static exports found */
/***/function _(module,exports,__webpack_require__){

module.exports=__webpack_require__(/*! /app/resources/js/works.view.js */"./resources/js/works.view.js");


/***/}

/******/});

}());
