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
