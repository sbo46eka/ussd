<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #e0f7fa;
            margin: 0;
        }
        .container {
            background: #05ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 500px;
            position: relative;
        }
        .task-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        .task-row input, button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .completed {
            text-decoration: line-through;
            color: gray;
        }
        button {
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
        }
        .edit-btn {
            background-color: green;
        }
        .search-container {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        @media (max-width: 500px) {
            .task-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search tasks..." onkeyup="searchTasks()">
        </div>
        <h2>Todo List</h2>
        <div class="task-row">
            <input type="text" id="taskInput" placeholder="Add a new task">
            <input type="date" id="taskDate">
            <button onclick="addTask()" style="background-color: green;">Add</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Checkbox</th>
                    <th>Task</th>
                    <th>Date Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="taskList"></tbody>
        </table>
        <button onclick="confirmDeleteAll()">Delete All</button>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", loadTasks);

        function addTask() {
            let taskText = document.getElementById("taskInput").value.trim();
            let taskDate = document.getElementById("taskDate").value;
            if (taskText === "" || taskDate === "") return;
            
            let taskList = document.getElementById("taskList");
            let row = createTaskRow(taskText, taskDate, false);
            taskList.appendChild(row);
            
            saveTasks();
            document.getElementById("taskInput").value = "";
            document.getElementById("taskDate").value = "";
        }

        function createTaskRow(text, date, completed) {
            let row = document.createElement("tr");
            
            let checkboxCell = document.createElement("td");
            let checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.checked = completed;
            checkbox.addEventListener("change", saveTasks);
            checkboxCell.appendChild(checkbox);
            
            let taskCell = document.createElement("td");
            taskCell.textContent = text;
            if (completed) taskCell.classList.add("completed");
            
            let dateCell = document.createElement("td");
            dateCell.textContent = date;
            
            let actionsCell = document.createElement("td");
            
            let editButton = document.createElement("button");
            editButton.textContent = "Edit";
            editButton.classList.add("edit-btn");
            editButton.addEventListener("click", () => editTask(row, text, date));
            
            let delButton = document.createElement("button");
            delButton.textContent = "Delete";
            delButton.addEventListener("click", () => {
                row.remove();
                saveTasks();
            });
            
            actionsCell.appendChild(editButton);
            actionsCell.appendChild(delButton);
            
            row.appendChild(checkboxCell);
            row.appendChild(taskCell);
            row.appendChild(dateCell);
            row.appendChild(actionsCell);
            
            return row;
        }

        function editTask(row, oldText, oldDate) {
            let newText = prompt("Edit task:", oldText);
            let newDate = prompt("Edit due date (YYYY-MM-DD):", oldDate);
            if (newText && newDate) {
                row.children[1].textContent = newText;
                row.children[2].textContent = newDate;
                saveTasks();
            }
        }

        function saveTasks() {
            let tasks = [];
            document.querySelectorAll("#taskList tr").forEach(row => {
                tasks.push({
                    text: row.children[1].textContent,
                    date: row.children[2].textContent,
                    completed: row.children[0].firstChild.checked
                });
            });
            localStorage.setItem("tasks", JSON.stringify(tasks));
            loadTasks();
        }

        function loadTasks() {
            let taskList = document.getElementById("taskList");
            taskList.innerHTML = "";
            let tasks = JSON.parse(localStorage.getItem("tasks")) || [];
            tasks.forEach(task => taskList.appendChild(createTaskRow(task.text, task.date, task.completed)));
        }

        function confirmDeleteAll() {
            if (confirm("Are you sure you want to delete all tasks?")) {
                document.getElementById("taskList").innerHTML = "";
                localStorage.removeItem("tasks");
            }
        }

        function searchTasks() {
            let searchText = document.getElementById("searchInput").value.toLowerCase();
            document.querySelectorAll("#taskList tr").forEach(row => {
                let taskText = row.children[1].textContent.toLowerCase();
                row.style.display = taskText.includes(searchText) ? "" : "none";
            });
        }
    </script>
</body>
</html>