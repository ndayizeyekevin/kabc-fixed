<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Upload</title>
</head>
<body>

    <h2>Upload CSV File</h2>

    <!-- Form to upload CSV file -->
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="csvFile">Choose CSV File:</label>
        <input type="file" name="csvFile" id="csvFile" accept=".csv" required>
        <button type="submit" name="submit">Upload</button>
    </form>

</body>
</html>
