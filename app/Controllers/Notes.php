<?php

namespace App\Controllers;

use App\Models\MatiereModel;
use App\Models\EtudiantModel;
use App\Models\NoteModel;
use App\Models\OptionModel;

class Notes extends BaseController
{
    public function ajout(): string
    {
        $matiereModel = new MatiereModel();
        $matieres = $matiereModel->orderBy('nom', 'ASC')->findAll();

        $etudiantModel = new EtudiantModel();
        $etudiants = $etudiantModel->orderBy('etudiant_id', 'ASC')->findAll();

        $optionModel = new OptionModel();
        $options = $optionModel->orderBy('nom', 'ASC')->findAll();
        $defaultOptionId = null;
        if (!empty($options)) {
            $globalOption = $optionModel->where('nom', 'Global')->first();
            if (!empty($globalOption['id'])) {
                $defaultOptionId = (int) $globalOption['id'];
            } else {
                $defaultOptionId = (int) $options[0]['id'];
            }
        }

        $errors = [];
        if ($this->request->getMethod() === 'post') {
            $etudiantId = $this->request->getPost('etudiant_id');
            $matieresPost = $this->request->getPost('matieres');

            if (empty($etudiantId)) {
                $errors[] = 'Veuillez selectionner un etudiant.';
            }

            if (!is_array($matieresPost) || empty($matieresPost)) {
                $errors[] = 'Veuillez ajouter au moins une matiere.';
            }

            if (empty($errors)) {
                $noteModel = new NoteModel();
                $rowIndex = 0;

                foreach ($matieresPost as $row) {
                    $rowIndex++;
                    $matiereId = $row['matiere_id'] ?? null;
                    $noteValue = $row['note'] ?? null;
                    $optionId = $row['option_id'] ?? null;

                    if (empty($matiereId) || $noteValue === null || $noteValue === '') {
                        $errors[] = 'Ligne ' . $rowIndex . ' : matiere et note obligatoires.';
                        continue;
                    }

                    if (!is_numeric($noteValue) || $noteValue < 0 || $noteValue > 20) {
                        $errors[] = 'Ligne ' . $rowIndex . ' : note invalide.';
                        continue;
                    }

                    if ($optionId !== null && $optionId !== '' && !is_numeric($optionId)) {
                        $errors[] = 'Ligne ' . $rowIndex . ' : option invalide.';
                        continue;
                    }

                    if (empty($optionId)) {
                        $optionId = $defaultOptionId;
                    }

                    if (empty($optionId)) {
                        $errors[] = 'Ligne ' . $rowIndex . ' : option requise.';
                        continue;
                    }

                    $insertResult = $noteModel->insert([
                        'etudiant_id' => (int) $etudiantId,
                        'matiere_id' => (int) $matiereId,
                        'option_id' => (int) $optionId,
                        'note' => $noteValue,
                    ]);

                    if ($insertResult === false) {
                        $modelErrors = $noteModel->errors();
                        if (!empty($modelErrors)) {
                            $errors[] = 'Ligne ' . $rowIndex . ' : ' . implode(' ', $modelErrors);
                        }

                        $dbError = $noteModel->db->error();
                        if (!empty($dbError['message'])) {
                            $errors[] = 'Ligne ' . $rowIndex . ' : ' . $dbError['message'];
                        }

                        if (empty($modelErrors) && empty($dbError['message'])) {
                            $errors[] = 'Ligne ' . $rowIndex . ' : insertion echouee.';
                        }
                    }
                }
            }

            if (empty($errors)) {
                return redirect()->to('/notes/ajout')->with('success', 'Notes enregistrees avec succes.');
            }
        }

        return view('pages/ajout_note', [
            'title' => 'EtuNote - Ajout note',
            'pageTitle' => 'Ajout note',
            'activeMenu' => 'ajout_note',
            'matieres' => $matieres,
            'etudiants' => $etudiants,
            'options' => $options,
            'errors' => $errors,
        ]);
    }

    public function semestres($id): string
    {
        return view('pages/liste_notes_semestre', [
            'title' => 'EtuNote - Notes par semestre',
            'pageTitle' => 'Notes par semestre',
            'activeMenu' => 'notes_semestres',
            'id_etudiant' => $id
        ]);
    }

    public function releve(): string
    {
        // $semestre = $this->request->getGet('semestre');
        // $idSemestre = $semestre->getIdByName($name);

        return view('pages/detail_note', [
            'title' => 'EtuNote - Releve de notes',
            'pageTitle' => 'Releve de notes',
            'activeMenu' => 'notes_semestres',
        ]);
    }
}
