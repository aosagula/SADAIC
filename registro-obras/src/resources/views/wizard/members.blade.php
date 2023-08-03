<div class="container">
    <form id="memberSearchForm">
        <div class="input-group">
            <input type="text" class="form-control" id="memberSearchQuery" placeholder="DNI, CUIT o N° de Socio"
                aria-label="DNI, CUIT o N° de Socio" aria-describedby="button-addon2">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="memberSearchButton">Buscar</button>
            </div>
        </div>
        <small id="memberSearchQueryHelp" class="form-text text-muted">
            Para bucar un socio ingrese el número de documento, de CUIT o de socio sin puntos ni guiones y haga click en
            <strong>Buscar</strong>.
        </small>
    </form>
    <div id="memberSearchResults"><ul></ul></div>
</div>