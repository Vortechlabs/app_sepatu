<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Register</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #eef2f3;
            font-family: Arial, sans-serif;
        }
        .register-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 320px;
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .register-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .register-container button {
            width: 100%;
            padding: 10px;
            background: #261d6c;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .register-container button:hover {
            background: #261d6c;
        }
    </style>
</head>
<body>


<div class="register-container">
    <h2>Register</h2>
    <form action="proses_register.php" method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="konfirmasi" placeholder="Konfirmasi Password" required>
        <input type="text" name="no_telepon" placeholder="No Telepon" required>
        <button type="submit">Daftar</button>
    </form>
</div>

</body>
</html>