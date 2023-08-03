<header>
  <div class="container-fluid">
    <div class="row flex-column flex-sm-row align-items-md-center align-items-around justify-content-around pt-3 pb-0 pb-sm-3">
      <div class="header-left mr-md-auto ml-3 d-flex flex-md-row align-items-center">
        <i id="menu_hamb" class="fas fa-bars fa-2x d-inline-block d-lg-none ml-3 mr-5"></i>
        <img class="cafar-logo" src="{{ asset('images/logo.png') }}" alt="SADAIC">
        <span class="ofic-virtual d-none d-lg-block ml-3 pl-2"> Servicios en Línea <i class="fas fa-desktop"></i></span>
      </div>
      <div class="d-flex mt-3 mt-sm-0">
        <div class="user-out d-flex w-100 flex-row justify-content-center align-items-center py-2 py-sm-0 px-2 px-sm-0">
          <div class="header-nombre d-flex  flex-column justify-content-md-end align-items-md-end mr-3 ">
            <span class=" font-weight-bold d-block"> {{ Auth::user()->name }} </span>
            <span class=" d-block"> USUARIO</span>
          </div>
          <div class="avatar mr-3 d-flex align-items-center">
            <span class="circle d-flex align-items-center justify-content-center"> {{ Auth::user()->name[0] }} </span>
          </div>
          <div class="salir-out align-items-right">
            <a class="a-salir d-flex justify-content-start align-items-center py-2" href="/logout">
              <div class=" d-flex flex-column justify-content-start ">
                <span class="d-block"> Salir </span>
              </div>
              <div class="avatar ml-1 mr-3 d-flex align-items-center">
                <span style="width:30px; height:25px;">
                  <svg class="nav-icon mr-3" viewBox="0 0 20 20"><use class="icon-svg" xlink:href="#icon-salir"></use></svg>
                </span>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>