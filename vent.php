<?php
/**
 * Hydrothermal Vent Database - Single Vent Page
 * Displays details of a single vent and its associated fauna
 *
 * SET08101 Web Technologies Coursework
 */

require_once "includes/db.php";

// Validate the vent ID parameter
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$ventId = (int) $_GET["id"];
$pdo = getDbConnection();

// Fetch the vent details
$stmt = $pdo->prepare(
    "SELECT id, name, location, type, depth_metres, discovery_year FROM vents WHERE id = ?",
);
$stmt->execute([$ventId]);
$vent = $stmt->fetch();

// If vent not found, redirect to home
if (!$vent) {
    header("Location: index.php");
    exit();
}

// Fetch all fauna associated with this vent
$faunaStmt = $pdo->prepare(
    "SELECT name, scientific_name, description, image_url FROM fauna WHERE vent_id = ? ORDER BY name",
);
$faunaStmt->execute([$ventId]);
$fauna = $faunaStmt->fetchAll();

$pageTitle = $vent["name"];

require_once "includes/header.php";
?>

<p><a href="index.php">&larr; Back to all vents</a></p>

<h2><?php echo e($vent["name"]); ?></h2>

<!-- ── Vent Details ───────────────────────────────────────────────────────── -->
<dl>
    <dt>Location</dt>
    <dd><?php echo e($vent["location"]); ?></dd>

    <dt>Type</dt>
    <dd><?php echo e($vent["type"]); ?></dd>

    <dt>Depth</dt>
    <dd><?php echo e($vent["depth_metres"]); ?> metres</dd>

    <dt>Discovery Year</dt>
    <dd><?php echo e($vent["discovery_year"]); ?></dd>
</dl>

<!-- ── Associated Fauna ──────────────────────────────────────────────────── -->
<section class="fauna-section">
    <h3>Associated Fauna</h3>

    <?php if (empty($fauna)): ?>
        <p class="no-fauna">No fauna recorded for this vent.</p>
    <?php else: ?>
        <p class="fauna-count">
            <?php echo count($fauna); ?> species recorded at this vent.
        </p>

        <div class="fauna-grid">
            <?php foreach ($fauna as $animal): ?>
                <article class="fauna-card">
                    <img
                        src="<?php echo e($animal["image_url"]); ?>"
                        alt="<?php echo e($animal["name"]); ?>"
                        class="fauna-img"
                    >
                    <div class="fauna-info">
                        <h4 class="fauna-name"><?php echo e(
                            $animal["name"],
                        ); ?></h4>
                        <p class="fauna-scientific"><em><?php echo e(
                            $animal["scientific_name"],
                        ); ?></em></p>
                        <?php if (!empty($animal["description"])): ?>
                            <p class="fauna-description"><?php echo e(
                                $animal["description"],
                            ); ?></p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once "includes/footer.php"; ?>
