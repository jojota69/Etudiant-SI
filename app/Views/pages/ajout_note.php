<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="page-header">
  <div>
    <h2>Ajout note</h2>
    <div class="breadcrumb">Accueil / Notes / <span>Ajout</span></div>
  </div>
  <a href="/notes/semestres" class="btn btn-secondary btn-sm">
    <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    Retour
  </a>
</div>

<form action="/notes/ajout" method="post">
  <?= csrf_field() ?>

  <?php if (!empty(session()->getFlashdata('success'))) : ?>
    <div class="alert">
      <?= esc(session()->getFlashdata('success')) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($errors)) : ?>
    <div class="alert">
      <ul>
        <?php foreach ($errors as $error) : ?>
          <li><?= esc($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="form-card section-gap">
    <div class="form-section-title">1. Etudiant</div>
    <div class="form-grid">
      <div>
        <label class="field-label">Etudiant <span class="required">*</span></label>
        <select name="etudiant_id">
          <option value="">-- Selectionner --</option>
          <?php if (!empty($etudiants)) : ?>
            <?php foreach ($etudiants as $etudiant) : ?>
              <option value="<?= esc($etudiant['id']) ?>">
                <?= esc(trim(($etudiant['prenom'] ?? '') . ' ' . ($etudiant['nom'] ?? ''))) ?>
                <?= !empty($etudiant['etudiant_id']) ? ' (' . esc($etudiant['etudiant_id']) . ')' : '' ?>
              </option>
            <?php endforeach; ?>
          <?php else : ?>
            <option value="" disabled>Aucun etudiant disponible</option>
          <?php endif; ?>
        </select>
      </div>
    </div>
  </div>

  <div class="form-card section-gap">
    <div class="form-section-title">2. Matiere et evaluation</div>
    <div id="matiere-rows">
      <div class="matiere-row" data-row-index="0">
        <div class="form-grid cols-3">
          <div>
            <label class="field-label">Semestre <span class="required">*</span></label>
            <select name="matieres[0][semestre]" class="matiere-semestre">
              <option value="">-- Selectionner --</option>
              <option value="S3">S3</option>
              <option value="S4">S4</option>
            </select>
          </div>
          <div>
            <label class="field-label">Matiere <span class="required">*</span></label>
            <select name="matieres[0][matiere_id]">
              <option value="">-- Selectionner --</option>
              <?php if (!empty($matieres)) : ?>
                <?php foreach ($matieres as $matiere) : ?>
                  <option value="<?= esc($matiere['id']) ?>"><?= esc($matiere['nom']) ?></option>
                <?php endforeach; ?>
              <?php else : ?>
                <option value="" disabled>Aucune matiere disponible</option>
              <?php endif; ?>
            </select>
          </div>
          <div>
            <label class="field-label">Note /20 <span class="required">*</span></label>
            <input name="matieres[0][note]" type="number" min="0" max="20" step="0.25" placeholder="15.5" />
          </div>
          <div class="option-field" style="display:none">
            <label class="field-label">Option (S4)</label>
            <select name="matieres[0][option_id]" disabled>
              <option value="">-- Selectionner --</option>
              <?php if (!empty($options)) : ?>
                <?php foreach ($options as $option) : ?>
                  <option value="<?= esc($option['id']) ?>"><?= esc($option['nom']) ?></option>
                <?php endforeach; ?>
              <?php else : ?>
                <option value="" disabled>Aucune option disponible</option>
              <?php endif; ?>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="form-footer">
    <button type="submit" class="btn btn-primary">
      <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
      Enregistrer la note
    </button>
  </div>

</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  (function () {
    const updateOptionField = (row) => {
      const semesterSelect = row.querySelector('.matiere-semestre');
      const optionField = row.querySelector('.option-field');
      const optionSelect = optionField ? optionField.querySelector('select') : null;

      if (!semesterSelect || !optionField || !optionSelect) {
        return;
      }

      const isS4 = semesterSelect.value === 'S4';
      optionField.style.display = isS4 ? '' : 'none';
      optionSelect.disabled = !isS4;
      if (!isS4) {
        optionSelect.selectedIndex = 0;
      }
    };
    const row = document.querySelector('.matiere-row');
    if (!row) {
      return;
    }

    const semesterSelect = row.querySelector('.matiere-semestre');
    if (!semesterSelect) {
      return;
    }

    semesterSelect.addEventListener('change', () => updateOptionField(row));
    updateOptionField(row);
  })();
</script>
<?= $this->endSection() ?>
