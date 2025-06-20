<?php
require_once 'config/database.php';

if (!isset($_GET['level_id']) || !isset($_GET['sector_id'])) {
    header('Location: levels.php');
    exit;
}

$level_id = (int)$_GET['level_id'];
$sector_id = (int)$_GET['sector_id'];
$subject_filter = isset($_GET['subject']) ? (int)$_GET['subject'] : null;

// Récup infos niveau + secteur
$query = "SELECT l.level_name, s.sector_name 
          FROM level l, sector s 
          WHERE l.level_id = :level_id AND s.Sector_id = :sector_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':level_id', $level_id);
$stmt->bindParam(':sector_id', $sector_id);
$stmt->execute();
$info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$info) {
    header('Location: levels.php');
    exit;
}

// Matières
$query = "SELECT DISTINCT sub.subject_id, sub.subject_name 
          FROM subject sub
          INNER JOIN program p ON sub.subject_id = p.subject_id
          WHERE p.level_id = :level_id AND p.Sector_id = :sector_id
          ORDER BY sub.subject_name";
$stmt = $db->prepare($query);
$stmt->bindParam(':level_id', $level_id);
$stmt->bindParam(':sector_id', $sector_id);
$stmt->execute();
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupère les cours (sans documents)
$query = "
SELECT c.course_id, c.tittle, sub.subject_name
FROM course c
INNER JOIN program p ON c.program_id = p.program_id
INNER JOIN subject sub ON p.subject_id = sub.subject_id
WHERE p.level_id = :level_id AND p.Sector_id = :sector_id
";

$params = [
    ':level_id' => $level_id,
    ':sector_id' => $sector_id,
];

if ($subject_filter) {
    $query .= " AND p.subject_id = :subject_id";
    $params[':subject_id'] = $subject_filter;
}

$query .= " ORDER BY sub.subject_name, c.tittle";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getSubjectIcon($subject_name)
{
    $icons = [
        'mathematics' => ['icon' => '📊', 'color' => 'bg-blue'],
        'physics and chemistry' => ['icon' => '🔬', 'color' => 'bg-green'],
        'life and earth science' => ['icon' => '🧬', 'color' => 'bg-purple'],
        'islamic education' => ['icon' => '🕌', 'color' => 'bg-yellow'],
        'philosophy' => ['icon' => '🤔', 'color' => 'bg-purple'],
        'frensh' => ['icon' => '🇫🇷', 'color' => 'bg-blue'],
        'english' => ['icon' => '📖', 'color' => 'bg-red'],
        'arab' => ['icon' => '📚', 'color' => 'bg-yellow'],
        'history and geography' => ['icon' => '🌍', 'color' => 'bg-green']
    ];

    $subject_lower = strtolower($subject_name);
    return $icons[$subject_lower] ?? ['icon' => '📚', 'color' => 'bg-gray'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>StudySwap - <?php echo htmlspecialchars($info['sector_name']); ?></title>
    <link rel="stylesheet" href="CSS/list.css" />
    <link rel="stylesheet" href="CSS/style.css" />
    <style>
        .course-card { cursor:pointer; }
    </style>
</head>
<body>

<?php include "header.php"; ?>

<section class="breadcrumb">
    <div class="container">
        <nav class="breadcrumb-nav">
            <a href="index.php">Accueil</a> &gt;
            <a href="levels.php">Niveaux</a> &gt;
            <a href="levels.php?level_id=<?php echo $level_id; ?>"><?php echo htmlspecialchars($info['level_name']); ?></a> &gt;
            <span><?php echo htmlspecialchars($info['sector_name']); ?></span>
        </nav>
    </div>
</section>

<section class="hero">
    <div class="container">
        <h1><?php echo htmlspecialchars($info['sector_name']); ?></h1>
        <p><?php echo htmlspecialchars($info['level_name']); ?></p>
    </div>
</section>

<main class="main">
    <div class="container">
        <form method="GET" class="search-container">
            <input type="hidden" name="level_id" value="<?php echo $level_id; ?>">
            <input type="hidden" name="sector_id" value="<?php echo $sector_id; ?>">

            <select name="subject" onchange="this.form.submit()" class="search-input">
                <option value="0">📚 Toutes les matières</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?php echo $subject['subject_id']; ?>" <?php if ($subject_filter == $subject['subject_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($subject['subject_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if (count($courses) > 0): ?>
            <div class="course-grid">
                <?php foreach ($courses as $course):
                    $subject_info = getSubjectIcon($course['subject_name']);
                ?>
                    <div class="course-card" onclick="location.href='view.php?course_id=<?php echo $course['course_id']; ?>'">
                        <div class="course-subject"><?php echo htmlspecialchars($course['subject_name']); ?></div>
                        <div class="course-icon <?php echo $subject_info['color']; ?>">
                            <?php echo $subject_info['icon']; ?>
                        </div>
                        <div class="course-title"><?php echo htmlspecialchars($course['tittle']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <h3>🔍 Aucun cours trouvé</h3>
                <p>Aucun cours n'est disponible pour ce filtre.</p>
                <?php if ($subject_filter): ?>
                    <button class="clear-filters" onclick="window.location.href='list.php?level_id=<?php echo $level_id; ?>&sector_id=<?php echo $sector_id; ?>'">
                        Voir tous les cours
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include "footer.php"; ?>

</body>
</html>
