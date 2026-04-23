<?php
/**
 * Hydrothermal Vent Database - Home Page
 * Displays a list of all hydrothermal vents with search/filter functionality
 *
 * SET08101 Web Technologies Coursework
 */

require_once "includes/db.php";

$pageTitle = "All Vents";

$pdo = getDbConnection();

// ── Grab filter inputs from GET (all optional) ──────────────────────────────
$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$filterType = isset($_GET["type"]) ? trim($_GET["type"]) : "";
$filterDepth = isset($_GET["depth"]) ? trim($_GET["depth"]) : "";

// ── Build query dynamically based on active filters ─────────────────────────
$conditions = [];
$params = [];

if ($search !== "") {
    $conditions[] = "name LIKE ?";
    $params[] = "%" . $search . "%";
}

if ($filterType !== "") {
    $conditions[] = "type = ?";
    $params[] = $filterType;
}

if ($filterDepth !== "") {
    switch ($filterDepth) {
        case "shallow": // < 1500 m
            $conditions[] = "depth_metres < 1500";
            break;
        case "mid": // 1500–2500 m
            $conditions[] = "depth_metres BETWEEN 1500 AND 2500";
            break;
        case "deep": // > 2500 m
            $conditions[] = "depth_metres > 2500";
            break;
    }
}

$sql =
    "SELECT id, name, location, type, depth_metres, discovery_year FROM vents";
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$vents = $stmt->fetchAll();

// ── Fetch distinct vent types for the dropdown ───────────────────────────────
$typeStmt = $pdo->query("SELECT DISTINCT type FROM vents ORDER BY type");
$ventTypes = $typeStmt->fetchAll(PDO::FETCH_COLUMN);

// ── Is any filter currently active? (used to show "Clear" button) ────────────
$isFiltered = $search !== "" || $filterType !== "" || $filterDepth !== "";

require_once "includes/header.php";
?>

<h2>Hydrothermal Vents</h2>
<p>Explore our database of hydrothermal vents from the Western Pacific region.</p>

<!-- ── Search & Filter Form ─────────────────────────────────────────────── -->
<section class="filter-bar" aria-label="Search and filter vents">
    <form method="get" action="index.php" class="filter-form">

        <!-- Text search -->
        <div class="filter-group">
            <label for="search">Search by name</label>
            <input
                type="text"
                id="search"
                name="search"
                placeholder="e.g. Mariana…"
                value="<?php echo e($search); ?>"
            >
        </div>

        <!-- Type dropdown -->
        <div class="filter-group">
            <label for="type">Vent type</label>
            <select id="type" name="type">
                <option value="">All types</option>
                <?php foreach ($ventTypes as $type): ?>
                    <option value="<?php echo e($type); ?>"
                        <?php echo $filterType === $type ? "selected" : ""; ?>>
                        <?php echo e($type); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Depth range dropdown -->
        <div class="filter-group">
            <label for="depth">Depth range</label>
            <select id="depth" name="depth">
                <option value="">Any depth</option>
                <option value="shallow" <?php echo $filterDepth === "shallow"
                    ? "selected"
                    : ""; ?>>
                    Shallow (&lt; 1,500 m)
                </option>
                <option value="mid" <?php echo $filterDepth === "mid"
                    ? "selected"
                    : ""; ?>>
                    Mid (1,500 – 2,500 m)
                </option>
                <option value="deep" <?php echo $filterDepth === "deep"
                    ? "selected"
                    : ""; ?>>
                    Deep (&gt; 2,500 m)
                </option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">Filter</button>
            <?php if ($isFiltered): ?>
                <a href="index.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </div>

    </form>
</section>

<!-- ── Results ───────────────────────────────────────────────────────────── -->
<?php if ($isFiltered): ?>
    <p class="results-count">
        <?php echo count($vents); ?> result<?php echo count($vents) !== 1
     ? "s"
     : ""; ?> found
        <?php if ($search !== ""): ?>
            for &ldquo;<?php echo e($search); ?>&rdquo;
        <?php endif; ?>
    </p>
<?php endif; ?>

<?php if (empty($vents)): ?>
    <p class="no-results">No vents match your search. <a href="index.php">Clear filters</a></p>
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
                    <td><?php echo e($vent["name"]); ?></td>
                    <td><?php echo e($vent["location"]); ?></td>
                    <td><?php echo e($vent["type"]); ?></td>
                    <td><?php echo e($vent["depth_metres"]); ?></td>
                    <td><?php echo e($vent["discovery_year"]); ?></td>
                    <td><a href="vent.php?id=<?php echo e(
                        $vent["id"],
                    ); ?>">View Details</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once "includes/footer.php"; ?>
