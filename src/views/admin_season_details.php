<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="bi bi-calendar-event"></i> 
        <?php echo htmlspecialchars($season_info['name']); ?>
    </h2>
    <div>
        <?php if ($season_info['is_current']): ?>
            <span class="badge bg-success fs-6">
                <i class="bi bi-star-fill"></i> Saison Actuelle
            </span>
        <?php else: ?>
            <span class="badge bg-secondary fs-6">
                <i class="bi bi-archive"></i> Saison Terminée
            </span>
        <?php endif; ?>
        <a href="index.php?page=admin&action=seasons" class="btn btn-outline-secondary ms-2">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>
</div>

<!-- Season Info -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-info-circle"></i> Informations de la Saison</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Date de début :</strong><br>
                        <i class="bi bi-calendar3"></i> 
                        <?php echo date('d/m/Y à H:i', strtotime($season_info['start_date'])); ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Date de fin :</strong><br>
                        <?php if ($season_info['end_date']): ?>
                            <i class="bi bi-calendar-x"></i> 
                            <?php echo date('d/m/Y à H:i', strtotime($season_info['end_date'])); ?>
                        <?php else: ?>
                            <span class="text-muted">En cours...</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Durée :</strong><br>
                        <?php 
                        $start = new DateTime($season_info['start_date']);
                        $end = $season_info['end_date'] ? new DateTime($season_info['end_date']) : new DateTime();
                        $diff = $start->diff($end);
                        echo $diff->days . ' jours';
                        ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Statut :</strong><br>
                        <?php if ($season_info['is_current']): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Terminée</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Season Summary Stats -->
<?php if ($season_summary && $season_summary['total_games'] > 0): ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-graph-up"></i> Statistiques de la Saison</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-2">
                        <div class="border-end">
                            <h3 class="text-primary"><?php echo $season_summary['total_games']; ?></h3>
                            <p class="text-muted mb-0">Parties Jouées</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border-end">
                            <h3 class="text-success"><?php echo $season_summary['total_players']; ?></h3>
                            <p class="text-muted mb-0">Joueurs Actifs</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border-end">
                            <h3 class="text-warning"><?php echo round($season_summary['avg_final_elo']); ?></h3>
                            <p class="text-muted mb-0">ELO Moyen</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end">
                            <h3 class="text-danger"><?php echo $season_summary['max_elo']; ?></h3>
                            <p class="text-muted mb-0">ELO Maximum</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-info"><?php echo $season_summary['min_elo']; ?></h3>
                        <p class="text-muted mb-0">ELO Minimum</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Final Rankings -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>
                    <i class="bi bi-trophy"></i> 
                    <?php echo $season_info['is_current'] ? 'Classement Actuel' : 'Classement Final'; ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($season_stats && $season_stats->rowCount() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Rang</th>
                                <th>Joueur</th>
                                <th>ELO</th>
                                <th>Parties</th>
                                <th>Victoires</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($player = $season_stats->fetch(PDO::FETCH_ASSOC)): 
                                $win_rate = $player['parties_jouees'] > 0 ? round(($player['victoires'] / $player['parties_jouees']) * 100) : 0;
                            ?>
                            <tr>
                                <td>
                                    <?php if ($player['final_rank'] == 1): ?>
                                        <i class="bi bi-trophy-fill text-warning"></i>
                                    <?php elseif ($player['final_rank'] == 2): ?>
                                        <i class="bi bi-award-fill text-secondary"></i>
                                    <?php elseif ($player['final_rank'] == 3): ?>
                                        <i class="bi bi-award-fill text-warning"></i>
                                    <?php else: ?>
                                        <?php echo $player['final_rank']; ?>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($player['pseudo']); ?></strong></td>
                                <td><span class="badge bg-info"><?php echo $player['final_elo']; ?></span></td>
                                <td><?php echo $player['parties_jouees']; ?></td>
                                <td><?php echo $player['victoires']; ?></td>
                                <td><?php echo $win_rate; ?>%</td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <?php echo $season_info['is_current'] ? 'Aucune partie jouée dans cette saison.' : 'Aucune statistique disponible pour cette saison.'; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-clock-history"></i> Parties Récentes</h5>
            </div>
            <div class="card-body">
                <?php if ($season_games && $season_games->rowCount() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Gagnant</th>
                                <th>Joueurs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($game = $season_games->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td class="small">
                                    <?php echo date('d/m H:i', strtotime($game['date_partie'])); ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($game['gagnant_pseudo']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo $game['nombre_joueurs']; ?></span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Aucune partie jouée dans cette saison.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>