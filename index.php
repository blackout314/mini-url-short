<?php
// config
$jsonFile = 'urls.json';
$title = 'Short URL Manager';
$magickey = 'KEY';
$baseUrl = "http://DOMAIN/index.php?s=";

function readUrls() {
    global $jsonFile;
    if (file_exists($jsonFile)) {
        return json_decode(file_get_contents($jsonFile), true);
    }
    return [];
}

function saveUrls($urls) {
    global $jsonFile;
    file_put_contents($jsonFile, json_encode($urls, JSON_PRETTY_PRINT));
}

if (isset($_GET['s'])) {
    $shortUrl = $_GET['s'];
    $urls = readUrls();
    if (isset($urls[$shortUrl])) {
        header("Location: " . $urls[$shortUrl]);
        exit;
    } else {
        echo "URL non trovata.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['real_url'], $_POST['short_url'])) {
    $realUrl = $_POST['real_url'];
    $shortUrl = $_POST['short_url'];
    $urls = readUrls();
    $urls[$shortUrl] = $realUrl;
    saveUrls($urls);
    header("Location: index.php");
    exit;
}

if (isset($_GET['delete'])) {
    $shortUrlToDelete = $_GET['delete'];
    $urls = readUrls();
    if (isset($urls[$shortUrlToDelete])) {
        unset($urls[$shortUrlToDelete]);
        saveUrls($urls);
    }
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 10px;
            color: #555;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            background-color: #fff;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
        }
        .url-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .url-container strong {
            color: #333;
        }
        a {
            color: #d9534f;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .short-url {
            color: #5bc0de;
        }
    </style>
</head>
<body>
  <h1>Gestione Short URL</h1>
  <form action="index.php" method="POST">
      <label for="real_url">URL Reale:</label>
      <input type="text" id="real_url" name="real_url" required>
      <label for="short_url">URL Short:</label>
      <input type="text" id="short_url" name="short_url" required>
      <button type="submit">Salva</button>
  </form>

  <?php
  if(isset($_GET['k']) && $_GET['k'] == $magickey) {
  ?>
      <h2>Lista di URL</h2>
      <ul>
          <?php
          $urls = readUrls();
          foreach ($urls as $shortUrl => $realUrl) {
              $shortUrlFull = $baseUrl . $shortUrl;
              echo "<li>
                      <div class='url-container'>
                          <div>
                              <strong>Short URL:</strong> <a href=\"$shortUrlFull\" class=\"short-url\">$shortUrlFull</a><br>
                              <strong>URL Reale:</strong> $realUrl
                          </div>
                          <a href=\"?delete=$shortUrl\" onclick=\"return confirm('Sei sicuro di voler eliminare questa URL?')\">Elimina</a>
                      </div>
                    </li>";
          }
          ?>
      </ul>
  <?php
  }
  ?>
</body>
</html>
