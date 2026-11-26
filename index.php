
<?php

define('DB_USER','root');
define('DB_PASS', 'youssef2003@');
define('DB_NAME', 'todolist');
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');

$conn = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME,
    DB_PORT
);

if ($conn->connect_error) {
     echo ("Connection failed: " . $conn->connect_error);
}
/* add task */

if (isset($_POST['action']) && $_POST['action'] == 'new') {

    $title = trim($_POST['title']);

    if ($title != '') {

        $add = $conn->prepare(
            "INSERT INTO todo (title) VALUES (?)"
        );

        $add->bind_param("s", $title);
        $add->execute();
        $add->close();
    }
}




if (isset($_POST['action']) && $_POST['action'] == 'delete') {

    $id = $_POST['id'];

    $delete = $conn->prepare(
        "DELETE FROM todo WHERE id = ?"
    );

    $delete->bind_param("i", $id);
    $delete->execute();
    $delete->close();
}


// ======================================
// 4. TOGGLE - CHANGE STATUS
// ======================================

if (isset($_POST['action']) && $_POST['action'] == 'toggle') {

    $id = $_POST['id'];

    $toggle = $conn->prepare(
        "UPDATE todo SET done = 1 - done WHERE id = ?"
    );

    $toggle->bind_param("i", $id);
    $toggle->execute();
    $toggle->close();
}


// ======================================
// 5. REDIRECT AFTER POST
// ======================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    header("Location: index.php");
    exit;
}


// ======================================
// 6. READ TASKS
// ======================================

$result = $conn->query(
    "SELECT * FROM todo"
);

$taches = [];

while ($row = $result->fetch_assoc()) {
    $taches[] = $row;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Todo List</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <!-- NAVBAR -->

    <nav class="navbar">
        <div class="container">
            <h2>TodoList</h2>
        </div>
    </nav>


    <!-- MAIN CONTENT -->

    <main class="container">

        <section class="todo-container">

            <h1>My Todo List</h1>

            <p class="subtitle">
                Organize your day, one task at a time.
            </p>


           

            <form method="POST" class="add-form">

                <input
                    type="text"
                    name="title"
                    placeholder="Enter a new task..."
                    required
                >

                <button
                    type="submit"
                    name="action"
                    value="new"
                    class="add-btn"
                >
                    Add Task
                </button>

            </form>


           

            <h3>My Tasks (<?php echo count($taches); ?>)</h3>

            <ul class="task-list">

                <?php foreach ($taches as $tache): ?>

                    <li class="task-item
                        <?php echo $tache['done']
                            ? 'completed'
                            : 'pending'; ?>">

                        <span class="task-title">
                            <?php echo htmlspecialchars($tache['title']); ?>
                        </span>


                        <!-- TASK ACTIONS -->

                        <form method="POST" class="task-actions">

                            <input
                                type="hidden"
                                name="id"
                                value="<?php echo $tache['id']; ?>"
                            >

                            <button
                                type="submit"
                                name="action"
                                value="toggle"
                                class="toggle-btn"
                            >
                                <?php echo $tache['done']
                                    ? 'Undo'
                                    : 'Done'; ?>
                            </button>

                            <button
                                type="submit"
                                name="action"
                                value="delete"
                                class="delete-btn"
                                onclick="return confirm('Delete this task?')"
                            >
                                Delete
                            </button>

                        </form>

                    </li>

                <?php endforeach; ?>

            </ul>

        </section>

    </main>

</body>

</html>

<?php
$conn->close();
?>
