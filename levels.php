<?php
require_once 'config/database.php';

// Récupérer tous les niveaux
$query = "SELECT * FROM level ORDER BY level_id";
$stmt = $db->prepare($query);
$stmt->execute();
$levels = $stmt->fetchAll();

// AJAX pour récupérer les secteurs
if (isset($_GET['ajax'], $_GET['level_id']) && $_GET['ajax'] === 'sectors') {
  header('Content-Type: application/json');
  $level_id = (int)$_GET['level_id'];

  $query = "SELECT s.Sector_id, s.sector_name 
              FROM sector s 
              INNER JOIN level_sector ls ON s.Sector_id = ls.Sector_id 
              WHERE ls.level_id = :level_id 
              ORDER BY s.sector_name";
  $stmt = $db->prepare($query);
  $stmt->bindParam(':level_id', $level_id);
  $stmt->execute();
  echo json_encode($stmt->fetchAll());
  exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>StudySwap - Niveaux</title>
  <link rel="stylesheet" href="CSS/levels.css" />
  <link rel="stylesheet" href="CSS/style.css" />
</head>

<body>
  <?php include "header.php" ?>

  <section class="hero">
    <div class="container">
      <h1>NIVEAUX SCOLAIRES</h1>
      <p>Choisissez votre niveau pour explorer les secteurs disponibles</p>
    </div>
  </section>

  <section class="courses">
    <div class="container">
      <div class="course-grid">
        <?php
        $icons = ['computer.png', 'school.png', 'community.png'];
        foreach ($levels as $index => $level):
          $icon = $icons[$index] ?? 'computer.png';
        ?>
          <div class="course-card <?php echo $index === 1 ? 'card-unique' : ''; ?>"
            onclick="showSectors(<?= $level['level_id'] ?>, '<?= htmlspecialchars($level['level_name']) ?>')">
            <div class="icon-container">
              <img src="IMG/<?= $icon ?>" alt="<?= htmlspecialchars($level['level_name']) ?>" />
            </div>
            <h3 class="course-title"><?= htmlspecialchars($level['level_name']) ?></h3>
            <button class="arrow-button" aria-label="Voir les secteurs">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white" viewBox="0 0 24 24">
                <path d="M5 12h14M12 5l7 7-7 7" />
              </svg>
            </button>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <div id="sectorsModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle" tabindex="-1">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="modalTitle">Secteurs disponibles</h2>
        <button class="close-btn" onclick="closeModal()" aria-label="Fermer la fenêtre">&times;</button>
      </div>
      <div id="sectorsContainer">
        <div class="loading">
          <div class="spinner"></div>
          <p>Chargement des secteurs...</p>
        </div>
      </div>
    </div>
  </div>

  <?php include "footer.php" ?>

  <script>
    let currentModal = null;

    function showSectors(levelId, levelName) {
      const modal = document.getElementById('sectorsModal');
      const modalTitle = document.getElementById('modalTitle');
      const sectorsContainer = document.getElementById('sectorsContainer');

      modalTitle.textContent = `Secteurs - ${levelName}`;
      sectorsContainer.innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Chargement des secteurs...</p>
                </div>
            `;
      modal.classList.add('show');
      currentModal = modal;

      fetch(`levels.php?ajax=sectors&level_id=${levelId}`)
        .then(response => {
          if (!response.ok) throw new Error('Erreur réseau');
          return response.json();
        })
        .then(sectors => {
          if (sectors.length === 0) {
            sectorsContainer.innerHTML = `
                            <div class="no-sectors">
                                <h3>😔 Aucun secteur disponible</h3>
                                <p>Il n'y a pas de secteurs configurés pour ce niveau.</p>
                            </div>
                        `;
          } else {
            sectorsContainer.innerHTML = `
                            <div class="sectors-grid">
                                ${sectors.map(s => `
                                    <div class="sector-card" onclick="goToCourses(${levelId}, ${s.Sector_id})">
                                        <div class="sector-name">${s.sector_name}</div>
                                        <div class="sector-arrow">→</div>
                                    </div>
                                `).join('')}
                            </div>
                        `;
          }
        })
        .catch(() => {
          sectorsContainer.innerHTML = `
                        <div class="no-sectors">
                            <h3>❌ Erreur de chargement</h3>
                            <p>Impossible de charger les secteurs. Veuillez réessayer.</p>
                            <button onclick="showSectors(${levelId}, '${levelName}')" 
                                style="background:#2f69b1;color:#fff;border:none;padding:10px 20px;border-radius:5px;cursor:pointer;margin-top:10px;">
                                Réessayer
                            </button>
                        </div>
                    `;
        });
    }

    function closeModal() {
      if (currentModal) {
        currentModal.classList.remove('show');
        currentModal = null;
      }
    }

    function goToCourses(levelId, sectorId) {
      window.location.href = `list.php?level_id=${levelId}&sector_id=${sectorId}`;
    }

    window.addEventListener('click', e => {
      if (currentModal && e.target === currentModal) closeModal();
    });

    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && currentModal) closeModal();
    });
  </script>
</body>

</html>