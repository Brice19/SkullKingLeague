<?php
require_once '../config/database.php';
require_once '../src/models/User.php';
require_once '../src/models/Season.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$season = new Season($db);

// Get current season
$current_season = $season->getCurrentSeason();
$season_id = isset($_GET['season']) ? $_GET['season'] : ($current_season ? $current_season['id'] : null);

// Get users for the specified season (or all-time if no season)
$users = $season_id ? $season->getCurrentSeasonRankings() : $user->getAll();
$all_seasons = $season->getAllSeasons();
?>

<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-trophy-fill text-warning"></i> Classement ELO</h2>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-calendar-event"></i> 
                    <?php echo $season_id && $current_season ? htmlspecialchars($current_season['name']) : 'Tout temps'; ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="index.php?page=ranking">Tout temps</a></li>
                    <?php if ($all_seasons): ?>
                        <?php while ($season_row = $all_seasons->fetch(PDO::FETCH_ASSOC)): ?>
                        <li>
                            <a class="dropdown-item <?php echo $season_id == $season_row['id'] ? 'active' : ''; ?>" 
                               href="index.php?page=ranking&season=<?php echo $season_row['id']; ?>">
                                <?php echo htmlspecialchars($season_row['name']); ?>
                                <?php if ($season_row['is_current']): ?>
                                    <span class="badge bg-success ms-1">Actuelle</span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <?php if ($season_id && $current_season): ?>
        <p class="lead">
            Classement de la saison actuelle : <strong><?php echo htmlspecialchars($current_season['name']); ?></strong>
            <small class="text-muted">(parties classées uniquement)</small>
        </p>
        <?php else: ?>
        <p class="lead">Classement général des joueurs par ordre de rating ELO</p>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Rang</th>
                                <th>Joueur</th>
                                <th>ELO</th>
                                <th>Parties</th>
                                <th>Victoires</th>
                                <th>% Victoires</th>
                                <th>Badge</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rank = 1;
                            if ($users && (is_object($users) ? $users->rowCount() > 0 : count($users) > 0)): 
                                $users_array = is_object($users) ? $users->fetchAll(PDO::FETCH_ASSOC) : $users;
                                foreach ($users_array as $row): 
                                    $win_rate = $row['parties_jouees'] > 0 ? round(($row['victoires'] / $row['parties_jouees']) * 100) : 0;
                                    
                                    // Déterminer le badge selon l'ELO
                                    $badge = '';
                                    $badge_class = '';
                                    if ($row['elo'] >= 1400) {
                                        $badge = 'Maître';
                                        $badge_class = 'bg-danger';
                                    } elseif ($row['elo'] >= 1200) {
                                        $badge = 'Expert';
                                        $badge_class = 'bg-warning';
                                    } elseif ($row['elo'] >= 1000) {
                                        $badge = 'Intermédiaire';
                                        $badge_class = 'bg-primary';
                                    } else {
                                        $badge = 'Débutant';
                                        $badge_class = 'bg-secondary';
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <?php if ($rank == 1): ?>
                                            <i class="bi bi-trophy-fill text-warning"></i>
                                        <?php elseif ($rank == 2): ?>
                                            <i class="bi bi-award-fill text-secondary"></i>
                                        <?php elseif ($rank == 3): ?>
                                            <i class="bi bi-award-fill text-warning"></i>
                                        <?php else: ?>
                                            <span class="fw-bold"><?php echo $rank; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['pseudo']); ?></strong>
                                        <?php if ($rank <= 3): ?>
                                            <i class="bi bi-star-fill text-warning"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info fs-6"><?php echo $row['elo']; ?></span>
                                    </td>
                                    <td><?php echo $row['parties_jouees']; ?></td>
                                    <td><?php echo $row['victoires']; ?></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $win_rate; ?>%">
                                                <?php echo $win_rate; ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $badge_class; ?>"><?php echo $badge; ?></span>
                                    </td>
                                </tr>
                                <?php 
                                $rank++;
                                endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="bi bi-info-circle"></i>
                                        <?php echo $season_id ? 'Aucune partie jouée dans cette saison.' : 'Aucun joueur trouvé.'; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-info-circle-fill"></i> Système ELO</h5>
                    </div>
                    <div class="card-body">
                        <p>Le système ELO calcule votre rating en fonction de vos victoires et défaites :</p>
                        <ul class="list-unstyled">
                            <li><span class="badge bg-danger">Maître</span> 1400+ ELO</li>
                            <li><span class="badge bg-warning">Expert</span> 1200-1399 ELO</li>
                            <li><span class="badge bg-primary">Intermédiaire</span> 1000-1199 ELO</li>
                            <li><span class="badge bg-secondary">Débutant</span> < 1000 ELO</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-lightbulb-fill"></i> Comment ça marche ?</h5>
                    </div>
                    <div class="card-body">
                        <p>Votre ELO évolue selon :</p>
                        <ul>
                            <li>Votre résultat (victoire/défaite)</li>
                            <li>L'ELO de vos adversaires</li>
                            <li>La probabilité de victoire calculée</li>
                        </ul>
                        <p class="text-muted small">Plus vos adversaires sont forts, plus vous gagnez de points en cas de victoire !</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
