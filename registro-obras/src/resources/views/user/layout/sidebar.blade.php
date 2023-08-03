<div id="asideWrap" class=" px-sm-0 trans-05">
    <aside id="side_bar" class=" d-md-block">
        <nav id="navmain" data-simplebar>
            <ul class="menu">
                <li class="user sub-menu pl-4 pt-2">
                    <div
                        class="d-flex align-items-center justify-content-center justify-content-center justify-content-md-start ">

                        <div class="avatar mr-3 d-flex align-items-center">
                            <span class="circle d-flex align-items-center justify-content-center"> {{ Auth::user()->name[0] }} </span>
                        </div>

                        <div id="userWrap" class="d-flex flex-column"
                            style="width: 80%; white-space: normal; word-break: break-word;">
                            <div class="d-flex flex-column">
                                <span class=" d-lg-inline-block text-nav title-nav"> ¡Hola </span>
                                <span class="name-user d-lg-inline-block text-nav title-nav"> {{ Auth::user()->name }}! </span>
                            </div>
                        </div>
                    </div>
                    <ul class="sub-items">
                        <li><a href="#">Editar Perfil</a></li>
                    </ul>
                </li>

                <li @if (Request::is('user')) class="active" @endif><a href="/user/"> <svg class="nav-icon mr-3" viewBox="0 0 90 90">
                            <use class="icon-svg" xlink:href="#inicio"></use>
                        </svg> <span class=" text-nav d-md-inline-block"> Inicio </span></a> </li>

                <li @if (Request::is('user/member/password')) class="active" @endif><a href="/user/member/password"> <svg class="nav-icon mr-3" viewBox="0 0 90 90">
                    <use class="icon-svg" xlink:href="#cuenta"></use>
                </svg> <span class=" text-nav d-md-inline-block"> Cambiar clave </span></a> </li>

                <li @if (Request::is('user/member/profile')) class="active" @endif><a href="/user/member/profile"> <svg class="nav-icon mr-3" viewBox="0 0 90 90">
                    <use class="icon-svg" xlink:href="#cuenta"></use>
                </svg> <span class=" text-nav d-md-inline-block"> Actualizar datos </span></a> </li>

                <li @if (Request::is('user/member/list')) class="active" @endif><a href="/user/member/list"> <svg class="nav-icon mr-3" viewBox="0 0 90 90">
                    <use class="icon-svg" xlink:href="#registro"></use>
                        </svg> <span class=" text-nav d-md-inline-block"> Solicitudes de Ingreso </span></a>
                </li>

                <li @if (Request::is('user/work/list')) class="active" @endif><a href="/user/work/list"> <svg class="nav-icon mr-3" viewBox="0 0 512 640">
                            <use class="icon-svg" xlink:href="#registro"></use>
                        </svg> <span class=" text-nav d-md-inline-block"> Registro de Obras </span></a> </li>

                        <li @if (Request::is('user/jingles')) class="active" @endif><a href="/user/jingles"> <svg class="nav-icon mr-3" viewBox="0 0 90 90">
                            <use class="icon-svg" xlink:href="#registro"></use>
                        </svg> <span class=" text-nav d-md-inline-block"> Registro de Solicitud de Inclusión </span></a>
                </li>
            </ul>
            <div id="version">v{{ config('app.version') }}</div>
        </nav>
    </aside>
</div>

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 317.84 252.05" width="100%" style="display:none;" >

<!--  Inicio -->

 <svg id="inicio" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
viewBox="0 0 496 354.3" style="enable-background:new 0 0 496 354.3;" xml:space="preserve">
<path d="M481.1,147.2L253,9.7c-0.1-0.1-0.3-0.1-0.4-0.2c-0.3-0.2-0.6-0.3-0.9-0.4c-0.3-0.1-0.6-0.2-0.9-0.3
c-0.3-0.1-0.6-0.2-0.9-0.2c-0.3-0.1-0.6-0.1-0.9-0.1c-0.3,0-0.6,0-1,0c-0.3,0-0.6,0-0.9,0c-0.3,0-0.7,0.1-1,0.1
c-0.3,0.1-0.6,0.1-0.9,0.2c-0.3,0.1-0.6,0.2-0.9,0.3c-0.3,0.1-0.6,0.3-0.9,0.4c-0.1,0.1-0.3,0.1-0.4,0.2L14.9,147.2
c-4.6,2.8-6.1,8.8-3.3,13.4c1.8,3,5,4.7,8.3,4.7c1.7,0,3.4-0.5,5-1.4l63.8-38.4v199.9c0,11.4,9.3,20.7,20.7,20.7h277.2
c11.4,0,20.7-9.3,20.7-20.7V125.4l63.8,38.4c1.6,0.9,3.3,1.4,5,1.4c3.3,0,6.5-1.7,8.3-4.7C487.2,155.9,485.7,149.9,481.1,147.2z
M289.4,326.5h-82.7V186.8h82.7V326.5z M387.8,325.3c0,0.7-0.5,1.2-1.2,1.2h-77.8V186.8c0-10.7-8.7-19.5-19.5-19.5h-82.7
c-10.7,0-19.5,8.7-19.5,19.5v139.6h-77.8c-0.7,0-1.2-0.5-1.2-1.2V113.7L248,29.4l139.8,84.3V325.3z"/>
</svg>

<!-- Salir -->
<svg id="icon-salir" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
    y="0px" viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
    <path class="" d="M17.8,20H2.2C1,20,0,19,0,17.8v-4.4h2.2v4.4h15.6V2.2H2.2v4.4H0V2.2C0,1,1,0,2.2,0h15.6C19,0,20,1,20,2.2v15.6
C20,19,19,20,17.8,20z M7.9,6l1.6-1.6L15,10l-5.6,5.6L7.9,14l2.9-2.9H0V8.9h10.8L7.9,6z" />
</svg>


<!-- Obras -->
<svg id="obras" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px"
    y="0px" viewBox="0 0 50 62.5" enable-background="new 0 0 50 50" xml:space="preserve">
    <path
        d="M41.976,30.261c0,0.553-0.447,1-1,1l0,0c-0.553,0-1-0.447-1-1V5.595c0-0.552,0.447-1,1-1l0,0c0.553,0,1,0.448,1,1V30.261z" />
    <path
        d="M41.901,5.22c0.208,0.512-0.039,1.095-0.551,1.303l-22.703,9.203c-0.512,0.207-1.095-0.039-1.303-0.552l0,0  c-0.207-0.512,0.04-1.095,0.551-1.302L40.6,4.669C41.111,4.461,41.694,4.708,41.901,5.22L41.901,5.22z" />
    <path
        d="M41.901,9.877c0.208,0.512-0.039,1.095-0.551,1.303l-22.703,9.202c-0.512,0.208-1.095-0.039-1.303-0.551l0,0  c-0.207-0.512,0.04-1.095,0.551-1.303L40.6,9.326C41.111,9.118,41.694,9.365,41.901,9.877L41.901,9.877z" />
    <path
        d="M38.146,35.235c-2.981,1.558-6.105,1.271-7.112-0.655s0.541-4.655,3.522-6.214c2.981-1.558,6.106-1.27,7.112,0.655  C42.676,30.947,41.128,33.677,38.146,35.235z M35.483,30.139c-2.029,1.061-3.087,2.73-2.677,3.515s2.385,0.869,4.413-0.19  c2.029-1.061,3.087-2.73,2.677-3.515C39.487,29.164,37.512,29.079,35.483,30.139z" />
    <path
        d="M15.443,44.402c-2.981,1.558-6.105,1.271-7.112-0.655s0.541-4.655,3.522-6.214c2.981-1.558,6.106-1.271,7.112,0.655  C19.972,40.114,18.424,42.844,15.443,44.402z M12.78,39.306c-2.029,1.061-3.087,2.73-2.677,3.515s2.385,0.869,4.413-0.19  c2.029-1.061,3.087-2.73,2.677-3.515C16.784,38.331,14.808,38.246,12.78,39.306z" />
    <path
        d="M19.271,39.511c0,0.553-0.447,1-1,1l0,0c-0.553,0-1-0.447-1-1V14.845c0-0.552,0.447-1,1-1l0,0c0.553,0,1,0.448,1,1V39.511z" />
    </svg>

<!-- Registro -->
<svg id="registro" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 640" x="0px" y="0px">
    <path
        d="M32,96H96v16a16.02085,16.02085,0,0,0,16,16H272a16.02085,16.02085,0,0,0,16-16V96h64V200h16V96a16.02085,16.02085,0,0,0-16-16H282.07129a15.77035,15.77035,0,0,0-1.19629-1.03125l-20.4375-13.625A8.00771,8.00771,0,0,0,256,64H240V52a16.04729,16.04729,0,0,0-6.40625-12.79688l-26.65625-20A16.11552,16.11552,0,0,0,197.33594,16H186.66406a16.07518,16.07518,0,0,0-9.59375,3.20312L150.41406,39.19531A16.05841,16.05841,0,0,0,144,52V64H128a8.00771,8.00771,0,0,0-4.4375,1.34375l-20.4375,13.625A15.77035,15.77035,0,0,0,101.92871,80H32A16.02085,16.02085,0,0,0,16,96V480a16.02085,16.02085,0,0,0,16,16H312V480H32Zm80-3.71875L130.42188,80H152a7.99539,7.99539,0,0,0,8-8V52l26.66406-20h10.67188L224,52V72a7.99539,7.99539,0,0,0,8,8h21.57812L272,92.28125V112l-160,.01562Z" />
    <circle cx="192" cy="56" r="8" />
    <path
        d="M130.875,297.85156l-19.34424,16.12012A15.79119,15.79119,0,0,0,104,312H72a16.02085,16.02085,0,0,0-16,16v32a16.02085,16.02085,0,0,0,16,16h32a16.02085,16.02085,0,0,0,16-16V328c0-.07812-.022-.15039-.02295-.228L141.125,310.14844ZM104,360H72V328H94.69678L82.875,337.85156l10.25,12.29688L104,341.08594Z" />
    <path
        d="M130.875,385.85156l-19.34424,16.12012A15.79119,15.79119,0,0,0,104,400H72a16.02085,16.02085,0,0,0-16,16v32a16.02085,16.02085,0,0,0,16,16h32a16.02085,16.02085,0,0,0,16-16V416c0-.07812-.022-.15039-.02295-.228L141.125,398.14844ZM104,448H72V416H94.69678L82.875,425.85156l10.25,12.29688L104,429.08594Z" />
    <rect x="168" y="320" width="16" height="16" />
    <rect x="200" y="320" width="104" height="16" />
    <rect x="152" y="352" width="56" height="16" />
    <rect x="224" y="352" width="80" height="16" />
    <rect x="168" y="408" width="16" height="16" />
    <rect x="200" y="408" width="112" height="16" />
    <rect x="152" y="440" width="56" height="16" />
    <rect x="224" y="440" width="88" height="16" />
    <path
        d="M328,200V168a16.02085,16.02085,0,0,0-16-16H72a16.02085,16.02085,0,0,0-16,16v96a16.02085,16.02085,0,0,0,16,16H264V264H152V216H272V200H152V168H312v32ZM136,264H72V216h64ZM72,200V168h64v32Z" />
    <path
        d="M495.5625,309.39844a7.99629,7.99629,0,0,0-4.125-4.625L456,287.89844V273.28906a24.0689,24.0689,0,0,0-12.09375-20.84375l-41.22656-23.55469a23.8759,23.8759,0,0,0-20.82031-1.45312l-28.709,11.48486L323.4375,224.77344a7.90536,7.90536,0,0,0-5.96875-.35938l-24,8a7.99315,7.99315,0,0,0-5.05469,10.11719l8,24a7.994,7.994,0,0,0,4.14844,4.69531l29.96338,14.26758A23.71272,23.71272,0,0,0,328,296v54.10938a16.14018,16.14018,0,0,0,1.6875,7.15624L344,385.89062V496h16V385.89062a16.14018,16.14018,0,0,0-1.6875-7.15624L344,350.10938V296a8,8,0,0,1,16,0v27.3125l26.34375,26.34375,11.3125-11.3125L376,316.6875v-9.53809l64,30.47608v7.95263a39.52769,39.52769,0,0,1-6.65625,21.98438A55.49954,55.49954,0,0,0,424,398.42188V496h16V398.42188a39.52769,39.52769,0,0,1,6.65625-21.98438A55.49954,55.49954,0,0,0,456,345.57812v-.33349l12.5625,5.98193a8.00806,8.00806,0,0,0,10.59375-3.64844l16-32A7.97885,7.97885,0,0,0,495.5625,309.39844ZM387.79688,242.29688a8.01072,8.01072,0,0,1,6.94531.48437l41.22656,23.55469A8.0305,8.0305,0,0,1,440,273.28906v6.99024l-67.18066-31.99073Zm80.55468,91.10937-93.562-44.56006a23.75878,23.75878,0,0,0-31.69141-15.0918l-32.58252-15.5122-4.39843-13.17969,13.35156-4.45312,157.72656,75.10937Z" />
    </svg>

<!-- Estado cuenta -->
<svg id="cuenta" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve"
    version="1.1"
    style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;"
    viewBox="0 0 172 215" x="0px" y="0px" fill-rule="evenodd" clip-rule="evenodd">
    <defs>
        <style type="text/css">

        </style>
    </defs>
    <g>
        <path class="fil0"
            d="M86 32c14,0 25,11 25,25 0,14 -11,25 -25,25 -14,0 -25,-11 -25,-25 0,-14 11,-25 25,-25zm0 10c-8,0 -15,7 -15,15 0,8 7,15 15,15 8,0 15,-7 15,-15 0,-8 -7,-15 -15,-15z" />
        <path class="fil0"
            d="M136 133c0,9 -10,9 -10,1 -1,-22 -19,-38 -40,-38 -22,0 -39,16 -40,38 0,8 -10,7 -10,-1 2,-27 24,-47 50,-47 27,0 49,20 50,47z" />
        <path class="fil0"
            d="M86 0c47,0 86,38 86,86 0,47 -38,86 -86,86 -47,0 -86,-38 -86,-86 0,-47 38,-86 86,-86zm0 10c-42,0 -76,34 -76,75 0,42 34,76 76,76 42,0 75,-34 75,-76 0,-42 -34,-75 -75,-75z" />
    </g>
</svg>

</svg>
