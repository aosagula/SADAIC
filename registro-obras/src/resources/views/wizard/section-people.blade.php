<h3>Derechohabientes *</h3>
<table id="distributionTable" class="table table-striped nowrap" width="100%">
    <thead>
        <tr>
            <th class="min-desktop" style="min-width: 200px">Rol</th>
            <th class="min-desktop">Socio</th>
            <th class="all">Apellido y Nombre</th>
            <th class="min-desktop">DNI/CUIT</th>
            <th class="min-tablet-p">% Ejecución Pública</th>
            <th class="min-tablet-p">% Reprod. Mecánica</th>
            <th class="min-tablet-p">% Sinc.</th>
            <th class="min-mobile"></th>
            <th class="min-mobile"></th>
        </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
        <tr>
            <td><em>Autor = Letra</em><br><em>Compositor = Música</em></td>
            <th></th>
            <th></th>
            <th>Total</th>
            <th id="publicTotal"></th>
            <th id="mechanicTotal"></th>
            <th id="syncTotal"></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>

<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPersonModal">Agregar Derechohabiente</button>

<div class="modal fade" id="addPersonModal" tabindex="-1" aria-labelledby="addPersonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPersonModalLabel">Agregar Persona al Registro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <ul class="nav nav-tabs nav-fill" id="addPersonTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="member-tab" data-toggle="tab" href="#member" role="tab"
                        aria-controls="member" aria-selected="true">Socio</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="no-member-tab" data-toggle="tab" href="#no-member" role="tab"
                        aria-controls="no-member" aria-selected="false">No Socio</a>
                </li>
            </ul>
            <div class="tab-content" id="addPersonTabsContent">
                <div class="tab-pane fade show active" id="member" role="tabpanel" aria-labelledby="member-tab">
                    @include('wizard.members')
                </div>
                <div class="tab-pane fade" id="no-member" role="tabpanel" aria-labelledby="no-member-tab">
                    @include('wizard.no-members')
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editPersonModal" tabindex="-1" aria-labelledby="editPersonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPersonModalLabel">Editar Persona</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('wizard.no-members', ['edit' => true])
            </div>
        </div>
    </div>
</div>
