<?php
/**
 * Hydrothermal Vent Database - Home Page
 * Displays a list of all hydrothermal vents
 *
 * SET08101 Web Technologies Coursework Starter Code
 */

require_once 'includes/db.php';

$pageTitle = 'All Vents';

// Fetch all vents from the database
$pdo = getDbConnection();
$stmt = $pdo->query('SELECT id, name, location, type, depth_metres, discovery_year FROM vents ORDER BY name');
$vents = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<h2>Hydrothermal Vents</h2>

<p>Explore our database of hydrothermal vents from the Western Pacific region.</p>

<?php if (empty($vents)): ?>
    <p>No vents found in the database.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Location</th>
                <th>Type</th>
                <th>Depth (m)</th>
                <th>Discovered</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vents as $vent): ?>
                <tr>
                    <td><?php echo e($vent['name']); ?></td>
                    <td><?php echo e($vent['location']); ?></td>
                    <td><?php echo e($vent['type']); ?></td>
                    <td><?php echo e($vent['depth_metres']); ?></td>
                    <td><?php echo e($vent['discovery_year']); ?></td>
                    <td><a href="vent.php?id=<?php echo e($vent['id']); ?>">View Details</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
