<?php
/**
 * Hydrothermal Vent Database - Single Vent Page
 * Displays details of a single vent
 *
 * SET08101 Web Technologies Coursework Starter Code
 */

require_once 'includes/db.php';

// Validate the vent ID parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$ventId = (int)$_GET['id'];
$pdo = getDbConnection();

// Fetch the vent details
$stmt = $pdo->prepare('SELECT id, name, location, type, depth_metres, discovery_year FROM vents WHERE id = ?');
$stmt->execute([$ventId]);
$vent = $stmt->fetch();

// If vent not found, redirect to home
if (!$vent) {
    header('Location: index.php');
    exit;
}

$pageTitle = $vent['name'];

require_once 'includes/header.php';
?>

<p><a href="index.php">&larr; Back to all vents</a></p>

<h2><?php echo e($vent['name']); ?></h2>

<dl>
    <dt>Location</dt>
    <dd><?php echo e($vent['location']); ?></dd>

    <dt>Type</dt>
    <dd><?php echo e($vent['type']); ?></dd>

    <dt>Depth</dt>
    <dd><?php echo e($vent['depth_metres']); ?> metres</dd>

    <dt>Discovery Year</dt>
    <dd><?php echo e($vent['discovery_year']); ?></dd>
</dl>

<?php require_once 'includes/footer.php'; ?>
