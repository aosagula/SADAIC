<h3>Documentación requerida *</h3>
<div class="form-row">
    <div class="form-group col-md-6">
        <label for="lyric_file">Archivo Partitura (PDF)</label>
        <x-file-uploader name="lyric_file" required />
        <small id="lyric_file_help" class="form-text text-muted">
            La partitura debe estar en formato PDF y tiene que dejar un margen en la parte superior de 5cm para poder
            agregar el membrete de SADAIC. Tamaño máximo: {{ $max_size }}.
        </small>
    </div>

    <div class="form-group col-md-6">
        <label for="audio_file">Archivo de Audio (MP3)</label>
        <x-file-uploader name="audio_file" exts="audio/mpeg" required />
        <small id="audio_file_help" class="form-text text-muted">
            El archivo de audio debe estar en formato MP3 y se recomienda que la calidad sea 44.1khz a 128kbps en stereo.
            Tamaño máximo: {{ $max_size }}.
        </small>
    </div>
</div>

<div class="form-row" id="scriptAttachment" style="display: none">
    <div class="form-group col-md-12">
        <label for="script_file">Archivo Letra (PDF)</label>
        <x-file-uploader name="script_file" required />
        <small id="script_file_help" class="form-text text-muted">
            La letra debe estar en formato PDF. Tamaño máximo: {{ $max_size }}.
        </small>
    </div>
</div>

<table id="attachmentsTable" class="table table-striped nowrap overflow-hidden" width="100%">
    <thead>
        <tr>
            <th class="min-mobile">Descripción</th>
            <th class="min-desktop">Archivo</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
