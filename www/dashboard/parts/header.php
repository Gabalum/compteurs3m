<?php
    namespace App;
    $compteurs = (Compteurs::getInstance())->getCompteurs();
    $slug = (isset($_GET['cpt']) ? strip_tags($_GET['cpt']) : '');
?><!doctype html><html class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" media="all" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" media="all" href="https://unpkg.com/@tailwindcss/custom-forms@0.2.1/dist/custom-forms.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Karla:400,700&display=swap');
        .font-family-karla { font-family: karla; }
    </style>
    <link rel="apple-touch-icon" sizes="90x90" href="<?php echo _BASE_URL_ ?>dashboard/assets/favicons/favicon.png">
        <link rel="icon" type="image/x-icon" sizes="90x90" href="<?php echo _BASE_URL_ ?>dashboard/assets/favicons/favicon.png">
</head>
<body class="bg-gray-100 font-family-karla flex">
    <?php require_once(dirname(__FILE__).'/sidebar.php') ?>
    <div id="mainContent" class="w-full flex flex-col h-screen overflow-y-scroll scroll-smooth">
