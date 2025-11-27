$student_id = $_SESSION["student_id"];
?>

<!-- Notes Details -->
<div class="row px-3">
    <div class="col-12">
        <div class="card stat-card shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-book-open me-2"></i>Détail des Notes</h4>
                    <span class="badge bg-light text-dark fs-6"><?= htmlspecialchars($_SESSION["matricule"]) ?></span>
                </div>
            </div>

            <div class="card-body">
                <ul class="nav nav-tabs" id="notesTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="semestre1-tab" data-bs-toggle="tab" data-bs-target="#semestre1" type="button" data-semestre="1">
                            Semestre 1
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="semestre2-tab" data-bs-toggle="tab" data-bs-target="#semestre2" type="button" data-semestre="2">
                            Semestre 2
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content p-3 border border-top-0 rounded-bottom" id="notesTabContent">
                    <div class="tab-pane fade show active" id="semestre1">
                        <div id="notesSemestre1" class="text-center py-5 text-muted">
                            <i class="fas fa-spinner fa-spin fa-2x mb-3"></i><br>
                            Chargement des notes...
                        </div>
                    </div>

                    <div class="tab-pane fade" id="semestre2">
                        <div id="notesSemestre2" class="text-center py-5 text-muted">
                            <i class="fas fa-clock fa-3x mb-3"></i><br>
                            En attente de chargement...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>