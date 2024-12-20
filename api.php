<?php

header("Content-Type: application/json");

$serverName = "127.0.0.1";
$userName = "root";
$password = "";
$dbName = "";

$connection = new mysqli($serverName, $userName, $password, $dbName);

if ($connection->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $connection->connect_error]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = parseInput($method);

function parseInput($method)
{
    if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
        return json_decode(file_get_contents('php://input'), true);
    } elseif (str_contains($_SERVER['CONTENT_TYPE'], 'multipart/form-data')) {
        return parseMultipartFormData();
    } elseif ($method === 'POST') {
        return $_POST;
    }
    return [];
}

function parseMultipartFormData(): array
{
    $data = [];
    $rawData = file_get_contents('php://input');
    $boundary = substr($rawData, 0, strpos($rawData, "\r\n"));
    $parts = array_slice(explode($boundary, $rawData), 1);

    foreach ($parts as $part) {
        if ($part == "--\r\n") {
            break;
        }

        preg_match('/name="([^"]*)"/', $part, $matches);
        $name = $matches[1] ?? null;
        $value = trim(substr($part, strpos($part, "\r\n\r\n") + 4));
        if ($name) {
            $data[$name] = $value;
        }
    }

    return $data;
}

function respond($status, $data): void
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// API Logic
switch ($method) {
    case 'POST':
        handleCreate($connection, $input);
        break;

    case 'GET':
        handleRead($connection);
        break;

    case 'PUT':
        handleUpdate($connection, $input);
        break;

    case 'DELETE':
        handleDelete($connection);
        break;

    default:
        respond(405, ["error" => "Method not allowed"]);
}

$connection->close();

// Handlers
function handleCreate($connection, $input): void
{
    $name = $input['name'] ?? null;
    $email = $input['email'] ?? null;

    if (!$name || !$email) {
        respond(400, ["error" => "Missing required fields: name or email"]);
    }

    $stmt = $connection->prepare("INSERT INTO customers (name, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $email);

    if ($stmt->execute()) {
        respond(201, ["message" => "Customer created", "id" => $stmt->insert_id]);
    } else {
        respond(500, ["error" => "Failed to create customer"]);
    }
    $stmt->close();

}

function handleRead($connection): void
{
    $id = $_GET['id'] ?? null;

    if ($id) {
        $stmt = $connection->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $customer = $result->fetch_assoc();

        if ($customer) {
            respond(200, $customer);
        } else {
            respond(404, ["error" => "Customer not found"]);
        }

        $stmt->close();
    } else {
        $result = $connection->query("SELECT * FROM customers");
        $customers = $result->fetch_all(MYSQLI_ASSOC);
        respond(200, $customers);
    }
}

function handleUpdate($connection, $input): void
{
    $id = $_GET['id'] ?? null;
    $name = $input['name'] ?? null;
    $email = $input['email'] ?? null;

    if (!$id || !$name || !$email) {
        respond(400, ["error" => "Missing required fields: id, name, or email"]);
    }

    $stmt = $connection->prepare("UPDATE customers SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        respond(200, ["message" => "Customer updated"]);
    } else {
        respond(404, ["error" => "Customer not found"]);
    }

    $stmt->close();
}

function handleDelete($connection): void
{
    $id = $_GET['id'] ?? null;

    if (!$id) {
        respond(400, ["error" => "Missing customer ID"]);
    }

    $stmt = $connection->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        respond(200, ["message" => "Customer deleted"]);
    } else {
        respond(404, ["error" => "Customer not found"]);
    }

    $stmt->close();
}
