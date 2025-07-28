<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-calendar-event"></i> Gestion des Saisons</h2>
    <a href="index.php?page=admin&action=start_new_season" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Nouvelle Saison
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php if ($_GET['success'] == 'season_started'): ?>
            <i class="bi bi-check-circle"></i> Nouvelle saison créée avec succès !
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Current Season Card -->
<?php if ($current_season): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-star-fill"></i> Saison Actuelle
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4><?php echo htmlspecialchars($current_season['name']); ?></h4>
                        <p class="text-muted">
                            <i class="bi bi-calendar3"></i> 
                            Commencée le <?php echo date('d/m/Y à H:i', strtotime($current_season['start_date'])); ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="index.php?page=admin&action=season_details&id=<?php echo $current_season['id']; ?>" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-eye"></i> Voir détails
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- All Seasons -->
<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-list"></i> Historique des Saisons</h5>
    </div>
    <div class="card-body">
        <?php if ($all_seasons && $all_seasons->rowCount() > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nom</th>
                        <th>Statut</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <th>Durée</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($season_row = $all_seasons->fetch(PDO::FETCH_ASSOC)): 
                        $duration = '';
                        if ($season_row['end_date']) {
                            $start = new DateTime($season_row['start_date']);
                            $end = new DateTime($season_row['end_date']);
                            $diff = $start->diff($end);
                            $duration = $diff->days . ' jours';
                        } else {
                            $start = new DateTime($season_row['start_date']);
                            $now = new DateTime();
                            $diff = $start->diff($now);
                            $duration = $diff->days . ' jours (en cours)';
                        }
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($season_row['name']); ?></strong>
                            <?php if ($season_row['is_current']): ?>
                                <span class="badge bg-success ms-2">Actuelle</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($season_row['is_current']): ?>
                                <span class="badge bg-success">
                                    <i class="bi bi-play-fill"></i> En cours
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">
                                    <i class="bi bi-stop-fill"></i> Terminée
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <i class="bi bi-calendar3"></i>
                            <?php echo date('d/m/Y H:i', strtotime($season_row['start_date'])); ?>
                        </td>
                        <td>
                            <?php if ($season_row['end_date']): ?>
                                <i class="bi bi-calendar-x"></i>
                                <?php echo date('d/m/Y H:i', strtotime($season_row['end_date'])); ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $duration; ?></td>
                        <td>
                            <a href="index.php?page=admin&action=season_details&id=<?php echo $season_row['id']; ?>" 
                               class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i> Détails
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle"></i>
            Aucune saison trouvée. Créez votre première saison !
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Help Card -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-question-circle"></i> À propos des Saisons</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Qu'est-ce qu'une saison ?</h6>
                        <p class="text-muted">Une saison est une période de compétition avec un classement distinct. Chaque saison a ses propres statistiques et classements.</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Commencer une nouvelle saison</h6>
                        <p class="text-muted">Démarrer une nouvelle saison termine la saison actuelle, sauvegarde les résultats finaux et remet tous les ELO à 1000.</p>
                    </div>
                </div>
                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Attention :</strong> Démarrer une nouvelle saison est irréversible et remet à zéro tous les ELO des joueurs.
                </div>
            </div>
        </div>
    </div>
</div>