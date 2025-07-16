<?php
class EloCalculator {
    const K_FACTOR = 32;
    
    public static function calculateNewElo($player_elo, $opponent_elos, $result) {
        // result: 1 pour victoire, 0 pour défaite
        // opponent_elos: array des ELO des adversaires
        
        $average_opponent_elo = array_sum($opponent_elos) / count($opponent_elos);
        
        // Calcul de l'espérance
        $expected = 1 / (1 + pow(10, ($average_opponent_elo - $player_elo) / 400));
        
        // Nouveau ELO
        $new_elo = $player_elo + self::K_FACTOR * ($result - $expected);
        
        return round($new_elo);
    }
    
    /**
     * Calcule les nouveaux ELOs selon un système où :
     * - La moitié supérieure gagne des points
     * - La moitié inférieure perd des points
     * - Le joueur du milieu (en cas d'effectif impair) ne change pas de points
     * - L'impact est plus fort aux extrémités du classement
     */
    public static function calculateNewEloByRanking($player_rank, $total_players, $player_elo) {
        // Déterminer si le joueur est dans la moitié supérieure, inférieure ou au milieu
        $middle_position = ceil($total_players / 2);
        
        // Si nombre de joueurs impair et joueur au milieu, pas de changement
        if ($total_players % 2 != 0 && $player_rank == $middle_position) {
            return $player_elo;
        }
        
        // Calcul du coefficient basé sur la position par rapport aux extrêmes
        // Plus on est proche des extrêmes, plus l'impact est fort
        $max_impact = self::K_FACTOR; // Impact maximal
        
        if ($player_rank <= $middle_position) {
            // Moitié supérieure : gain d'ELO
            // Position 1 a l'impact maximum, position proche du milieu a un impact minimum
            
            // Eviter division par zéro pour les petits nombres de joueurs (cas à 2 joueurs)
            if ($middle_position <= 1) {
                $normalized_position = 1; // Max impact pour le premier joueur si 2 joueurs
            } else {
                // Correction pour le cas à 4 joueurs où middle_position = 2
                $normalized_position = ($middle_position - $player_rank + 1) / $middle_position;
            }
            
            // S'assurer que la valeur est entre 0 et 1
            $normalized_position = max(0, min(1, $normalized_position));
            $elo_change = $max_impact * $normalized_position;
            return round($player_elo + $elo_change);
        } else {
            // Moitié inférieure : perte d'ELO
            // Dernière position a l'impact maximum, position proche du milieu a un impact minimum
            
            // Eviter division par zéro
            if ($total_players - $middle_position <= 0) {
                $normalized_position = 1;
            } else {
                $normalized_position = ($player_rank - $middle_position) / ($total_players - $middle_position);
            }
            
            // S'assurer que la valeur est entre 0 et 1
            $normalized_position = max(0, min(1, $normalized_position));
            $elo_change = $max_impact * $normalized_position;
            return round($player_elo - $elo_change);
        }
    }
    
    public static function updateElosAfterGame($db, $game_id, $winner_id) {
        // Récupérer tous les joueurs et leurs ELO actuels, triés par score décroissant
        $query = "SELECT gp.user_id, u.elo, gp.score_total
                  FROM game_players gp
                  JOIN users u ON gp.user_id = u.id
                  WHERE gp.game_id = ?
                  ORDER BY gp.score_total DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $game_id);
        $stmt->execute();
        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $total_players = count($players);
        $elo_changes = [];
        
        // Gestion des égalités : créer un tableau de rangs réels
        $real_ranks = [];
        $last_score = null;
        $last_rank = 0;
        $tied_players = 0;
        
        // Premier passage : identifier les égalités et attribuer les rangs
        foreach ($players as $index => $player) {
            $score = $player['score_total'];
            
            if ($last_score !== null && $score == $last_score) {
                // Égalité avec le joueur précédent
                $tied_players++;
                $real_ranks[$index] = $last_rank;
            } else {
                // Nouveau score
                $last_rank = $index + 1 - $tied_players;
                $real_ranks[$index] = $last_rank;
                $tied_players = 0;
            }
            
            $last_score = $score;
        }
        
        // Calculer les nouveaux ELO basés sur le classement final
        foreach($players as $index => $player) {
            $player_id = $player['user_id'];
            $player_elo = $player['elo'];
            
            // Utiliser le rang réel qui tient compte des égalités
            $player_rank = $real_ranks[$index];
            
            $new_elo = self::calculateNewEloByRanking($player_rank, $total_players, $player_elo);
            
            // Stocker l'ancien et le nouveau ELO pour affichage ultérieur
            $elo_changes[$player_id] = [
                'old_elo' => $player_elo,
                'new_elo' => $new_elo,
                'change' => $new_elo - $player_elo,
                'rank' => $player_rank // Stocker le rang pour le débogage
            ];
            
            // Mettre à jour l'ELO en base
            $update_query = "UPDATE users SET elo = ? WHERE id = ?";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bindParam(1, $new_elo);
            $update_stmt->bindParam(2, $player_id);
            $update_stmt->execute();
        }
        
        // Vérifier si la table elo_history existe, sinon la créer
        try {
            $check_table = $db->query("SELECT 1 FROM elo_history LIMIT 1");
        } catch (PDOException $e) {
            // Table n'existe pas, on la crée
            $sql_file = file_get_contents(__DIR__ . '/../../config/create_elo_history_table.sql');
            $db->exec($sql_file);
        }
        
        // Stocker les changements d'ELO dans la base de données pour référence ultérieure
        $query_elo_history = "INSERT INTO elo_history (game_id, user_id, old_elo, new_elo, elo_change, rank) 
                              VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_elo_history = $db->prepare($query_elo_history);
        
        foreach($elo_changes as $player_id => $change) {
            $stmt_elo_history->bindParam(1, $game_id);
            $stmt_elo_history->bindParam(2, $player_id);
            $stmt_elo_history->bindParam(3, $change['old_elo']);
            $stmt_elo_history->bindParam(4, $change['new_elo']);
            $stmt_elo_history->bindParam(5, $change['change']);
            $stmt_elo_history->bindParam(6, $change['rank']);
            $stmt_elo_history->execute();
        }
    }
}
?>
