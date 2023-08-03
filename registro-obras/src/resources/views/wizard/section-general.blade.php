<div class="form-group">
    <label for="title">Título *</label>
    <input type="text" class="form-control" name="title" id="title" required>
</div>

<div class="form-row">
    <div class="form-group col-md-9">
        <label for="genre_id">Género *</label>
        <select class="custom-select" id="genre_id" name="genre_id" required>
            @foreach($genres as $genre)
            <option value="{{ $genre->id }}">{{ $genre->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-3">
        <label for="duration">Duración Aproximada *</label>
        <input type="text" class="form-control" name="duration" id="duration" required>
    </div>
</div>

<div class="form-group">
    <label for="title">Títulos alternativos</label><br>
    @foreach(old('alternative_titles', []) as $title)
    <div class="input-group mb-3">
        <input type="text" class="form-control alternative_titles" placeholder="" name="alternative_titles[]" value="{{ $title }}">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary delete_alternative_title" type="button">Borrar</button>
        </div>
    </div>
    @endforeach
    <button type="button" class="btn btn-primary" id="addAltTitle">Agregar títtulo alternativo</button>
</div>

<div class="form-group">
    <label for="dnda_title">Título Álbum *</label>
    <input type="text" class="form-control" name="dnda_title" id="dnda_title" value="{{ old('dnda_title') }}">
</div>


<div class="form-group">
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="is_jingle" value="1" {{ old('is_jingle') ? 'checked' : '' }}>
        <label class="form-check-label" for="is_jingle">Música en Publicidad</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="is_jingle" value="2" {{ old('is_movie') ? 'checked' : '' }}>
        <label class="form-check-label" for="is_movie">Música en Producción Audiovisual</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="is_jingle" value="3" checked>
        <label class="form-check-label" for="is_regular">Música Regular</label>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label for="lyric_dnda_in_file">N° Expediente DNDA Inédita (Letra)</label>
        <input type="text" class="form-control" name="lyric_dnda_in_file" id="lyric_dnda_in_file" maxlength="64">
    </div>
    <div class="form-group col-md-4">
        <label for="audio_dnda_in_file">N° Expediente DNDA Inédita (Musica)</label>
        <input type="text" class="form-control" name="audio_dnda_in_file" id="audio_dnda_in_file" maxlength="64">
    </div>
    <div class="form-group col-md-4">
        <label for="dnda_in_date">Fecha Inédita</label>
        <input type="date" placeholder="__/__/____" class="form-control" name="dnda_in_date" id="dnda_in_date" max="9999-12-31">
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label for="lyric_dnda_ed_file">N° Expediente DNDA Editada (Letra)</label>
        <input type="text" class="form-control" name="lyric_dnda_ed_file" id="lyric_dnda_ed_file" maxlength="64">
    </div>
    <div class="form-group col-md-4">
        <label for="audio_dnda_ed_file">N° Expediente DNDA Editada (Musica)</label>
        <input type="text" class="form-control" name="audio_dnda_ed_file" id="audio_dnda_ed_file" maxlength="64">
    </div>
    <div class="form-group col-md-4">
        <label for="dnda_ed_date">Fecha Editada</label>
        <input type="date" placeholder="__/__/____" class="form-control" name="dnda_ed_date" id="dnda_ed_date" max="9999-12-31">
    </div>
</div>
