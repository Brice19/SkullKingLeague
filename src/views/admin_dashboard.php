<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2"></i> Tableau de bord administrateur</h2>
    <div>
        <span class="text-muted">Connecté en tant que : <strong><?php echo $_SESSION['admin_username']; ?></strong></span>
        <a href="index.php?page=admin&action=logout" class="btn btn-outline-danger btn-sm ms-2">
            <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
    </div>
</div>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                <h3 class="mt-2"><?php echo $stats['total_users']; ?></h3>
                <p class="text-muted">Utilisateurs</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-controller text-success" style="font-size: 3rem;"></i>
                <h3 class="mt-2"><?php echo $stats['total_games']; ?></h3>
                <p class="text-muted">Parties terminées</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-calendar-event text-info" style="font-size: 3rem;"></i>
                <h3 class="mt-2"><?php echo $stats['total_seasons']; ?></h3>
                <p class="text-muted">Saisons</p>
            </div>
        </div>
    </div>
</div>

<!-- Current Season Info -->
<?php if ($current_season): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info d-flex align-items-center">
            <i class="bi bi-info-circle-fill me-2"></i>
            <div>
                <strong>Saison actuelle :</strong> <?php echo htmlspecialchars($current_season['name']); ?>
                <span class="text-muted">
                    (depuis le <?php echo date('d/m/Y', strtotime($current_season['start_date'])); ?>)
                </span>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Actions rapides -->
<div class="row">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h5><i class="bi bi-calendar-event"></i> Gestion des Saisons</h5>
            </div>
            <div class="card-body">
                <p>Gérez les saisons, démarrez de nouvelles compétitions et consultez l'historique.</p>
                <div class="d-grid gap-2">
                    <a href="index.php?page=admin&action=seasons" class="btn btn-info">
                        <i class="bi bi-list"></i> Voir les saisons
                    </a>
                    <a href="index.php?page=admin&action=start_new_season" class="btn btn-warning">
                        <i class="bi bi-plus-circle"></i> Nouvelle saison
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h5><i class="bi bi-people-fill"></i> Gestion des utilisateurs</h5>
            </div>
            <div class="card-body">
                <p>Ajoutez, modifiez ou supprimez des utilisateurs de la ligue.</p>
                <div class="d-grid gap-2">
                    <a href="index.php?page=admin&action=users" class="btn btn-primary">
                        <i class="bi bi-list"></i> Voir les utilisateurs
                    </a>
                    <a href="index.php?page=admin&action=add_user" class="btn btn-success">
                        <i class="bi bi-person-plus"></i> Ajouter un utilisateur
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h5><i class="bi bi-controller"></i> Gestion des parties</h5>
            </div>
            <div class="card-body">
                <p>Consultez et gérez l'historique des parties jouées.</p>
                <div class="d-grid gap-2">
                    <a href="index.php?page=admin&action=games" class="btn btn-info">
                        <i class="bi bi-list"></i> Voir les parties
                    </a>
                    <a href="index.php?page=history" class="btn btn-outline-info">
                        <i class="bi bi-eye"></i> Vue utilisateur
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats Row -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-hourglass-split text-warning" style="font-size: 3rem;"></i>
                <h3 class="mt-2"><?php echo $stats['games_in_progress']; ?></h3>
                <p class="text-muted">Parties en cours</p>
            </div>
        </div>
    </div>
    <?php if ($current_season): ?>
    <div class="col-md-6">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-star-fill text-success" style="font-size: 3rem;"></i>
                <h3 class="mt-2"><?php echo htmlspecialchars($current_season['name']); ?></h3>
                <p class="text-muted">Saison actuelle</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Actions système -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-gear-fill"></i> Actions système</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Base de données</h6>
                        <p class="text-muted small">Assurez-vous que la base de données est correctement initialisée.</p>
                        <a href="../config/init_db.php" target="_blank" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-database"></i> Réinitialiser la DB
                        </a>
                    </div>
                    <div class="col-md-6">
                        <h6>Navigation</h6>
                        <p class="text-muted small">Retourner à l'interface utilisateur.</p>
                        <a href="index.php" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-house"></i> Accueil utilisateur
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
