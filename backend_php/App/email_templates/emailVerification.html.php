<!DOCTYPE html>
<html lang="<?= htmlspecialchars($userLocale) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $translations['email_verification_title'] ?></title>
</head>
<body>
    <div>
        <h2><?= $translations['email_verification_prompt'] ?></h2>
        <p><?= $translations['email_verification_instruction'] ?></p>
        <div style="font-size: 24px; font-weight: bold;"><?= htmlspecialchars($verification_code) ?></div>
        <p><?= $translations['email_verification_ignore'] ?></p>
    </div>
</body>
</html>