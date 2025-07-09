<!DOCTYPE html>
<html lang="<?= htmlspecialchars($sellerLang ?? 'id') ?>">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0"/>
        <title><?= htmlspecialchars($browserTitle ?? '') ?></title>
        <meta property="fb:app_id" content="256135015242904" />
        <meta property="og:title" content="InviezID Admin Panel"/>
        <meta property="og:description" content="Website theme, Digital Invitation, Mockup Design, and more"/>
        <meta property="og:image" content="https://inviez.my-board.org/assets/images/og-image.png"/>
        <meta property="og:image:alt" content="https://inviez.my-board.org/assets/images/og-image.png"/>
        <meta property="og:url" content="https://inviez.my-board.org"/>
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="INVIEZ.ID" />
        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="InviezID Admin Panel" />
        <meta name="twitter:description" content="Website theme, Digital Invitation, Mockup Design, and more" />
        <meta name="twitter:image" content="https://inviez.my-board.org/assets/images/og-image.png" />
        <meta name="twitter:site" content="@inviezid" />

        <link rel="preconnect" href="https://fonts.googleapis.com"/>
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=MuseoModerno:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet"/>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
        <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico"/>
        <link rel="stylesheet" href="/assets/css/style.css"/>
        <link rel="stylesheet" href="/assets/css/media.css"/>
    </head>
    <body class="<?= htmlspecialchars($sellerTheme ?? 'light') ?>">