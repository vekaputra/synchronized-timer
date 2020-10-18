<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $timeLeft = $_POST['hour'] * 3600 + $_POST['minute'] * 60 + $_POST['second'];
        $description = $_POST['description'];
        $now = time();

        $endTime = $now + $timeLeft;
        $content = base64_encode($description . "\n" . $endTime);
        $filename = substr($content, 5, 6);

        file_put_contents('./storage/' . $filename . '.time', $content);

        header('Location: http://' . $_SERVER['HTTP_HOST'] . '?f=' . $filename);
    }

    $endTime = 0;
    $description = '';
    if (!empty($_GET['f'])) {
        $filename = $_GET['f'];
        $encodedContent = file_get_contents('./storage/' . $filename . '.time');
        $data = explode("\n", base64_decode($encodedContent));
        
        $endTime = intval($data[1]);
        $description = $data[0];

        echo $endTime . ' ' . $description;
    }
?>
<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Skills Competition Timer</title>
    <style>
        html {
            font-family: 'Montserrat', sans-serif;
            background: #000000;
            color: #ffffff;
        }
        .center {
            text-align: center;
        }
        .s144 {
            margin: 20px 0 40px;
            font-size: 144px;
            font-weight: 700;
        }
        .timer span {
            margin: 0 40px;
        }
        h1 {
            margin: 150px 0 50px;
            font-size: 72px;
        }
        h3 {
            font-size: 24px;
        }
        form {
            background: #4f4f4f;
            width: 600px;
            padding: 10px 20px 30px;
            border-radius: 6px;
            margin: auto;
        }
        input[type="text"], input[type="number"] {
            font-size: 2em;
            padding: 15px;
            width: 540px;
        }
        span {
            display: inline-block;
            width: 174px;
            padding: 10px;
        }
        span input[type="number"] {
            width: 140px;
            text-align: center;
        }
        input[type="submit"] {
            width: 200px;
            border-radius: 6px;
            background: #27ae60;
            border: none;
            font-size: 18px;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            margin: auto;
            font-weight: 700;
            display: block;
        }
        input[type="submit"]:hover {
            cursor: pointer;
            background: #179e50;
        }
        a, a:active, a:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>
<?php
    if ($endTime == 0) {
?>
    <h1 class="center">SET WAKTU</h1>
    <form method="POST">
        <span>
            <h3>DESKRIPSI</h3>
            <p>
                <input type="text" name="description"/>
            </p>
        </span>
        <div>
            <span>
                <p><input type="number" name="hour" value="0" min="0"/></p>
                <h3>HOUR</h3>
            </span>
            <span>
                <p><input type="number" name="minute" value="0" min="0"/></p>
                <h3>MINUTE</h3>
            </span>
            <span>
                <p><input type="number" name="second" value="0" min="0"/></p>
                <h3>SECOND</h3>
            </span>
        </div>
        <p>
            <input type="submit" value="START TIMER"/>
        </p>
    </form>
<?php
    } else {
        $diff = $endTime - time();
        $hour = floor($diff / 3600);
        $minute = floor(($diff % 3600) / 60);
        $second = $diff % 60;
        $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
        $shareUrl = $baseUrl . '?f=' . $filename;
        $timeUrl = $baseUrl . '/time.php';
?>
    <h1 class="center">WAKTU PENGERJAAN</h1>
    <h3 class="center">
        <?php echo $description; ?>
    </h3>
    <div class="center timer">
        <span>
            <p class="s144" id="hour"><?php echo sprintf("%02d", $hour); ?></p>
            <h3>HOUR</h3>
        </span>
        <span>
            <p class="s144" id="minute"><?php echo sprintf("%02d", $minute); ?></p>
            <h3>MINUTE</h3>
        </span>
        <span>
            <p class="s144" id="second"><?php echo sprintf("%02d", $second); ?></p>
            <h3>SECOND</h3>
        </span>
    </div>
    <div class="center share">
        <h3>Share: <a href="<?php echo $shareUrl; ?>"><?php echo $shareUrl; ?></a></h3>
    </div>
    <script>
        const endTime = <?php echo $endTime; ?>;

        async function update() {
            let response = await fetch('<?php echo $timeUrl; ?>');
            let now = parseInt(await response.text());

            let diff = Math.floor(endTime - now);
            let hour = Math.max(0, Math.floor(diff / 3600));
            let minute = Math.max(0, Math.floor((diff % 3600) / 60));
            let second = Math.max(0, diff % 60);

            document.getElementById('hour').innerHTML = leadZero(hour);
            document.getElementById('minute').innerHTML = leadZero(minute);
            document.getElementById('second').innerHTML = leadZero(second);
        }

        function leadZero(num) {
            return ("0" + num.toString()).slice(-2);
        }

        setInterval(update, 1000);
    </script>
<?php
    }
?>
</body>
</html>
