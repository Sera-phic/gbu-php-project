<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GBU College Portal</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            background: 
                linear-gradient(to right, rgba(0, 31, 63, 0), rgba(0, 51, 102, 0)),
                url("assets/image/bg-img.png") center/cover no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .container {
            background: rgba(0, 0, 0, 0.6);
            padding: 30px 25px;
            border-radius: 15px;
            width: 90%;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        h1 {
            font-size: 1.7rem;
            margin-bottom: 20px;
        }

        p {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .btn {
            text-decoration: none;
            background: linear-gradient(135deg, #007bff, #00c6ff);
            color: white;
            padding: 12px 15px;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            transition: 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 123, 255, 0.5);
            background: linear-gradient(135deg, #0056b3, #0096d6);
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.4rem;
            }

            .btn {
                font-size: 0.95rem;
                padding: 10px 12px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Welcome to GBU College Portal</h1>
        <p>Select a service:</p>

        <div class="buttons">
            <a href="event/" class="btn">🎉 Event Management Portal</a>
            <a href="placement/" class="btn">💼 Placement Cell Portal</a>
        </div>
    </div>

</body>
</html>
