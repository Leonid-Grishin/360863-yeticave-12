<?php
require_once('helpers.php');
require_once('data.php');
require_once('functions.php');
require_once('validate-form.php');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);



$categories = getCategories ($CONNECTION);
foreach ($categories as $key => $category){
    $categories[$key]['isSelected'] = '';
}
foreach ($categories as $key => $category) {
    $categories[$key]['title'] = htmlspecialchars($category['title']);
    $categories[$key]['symbolic_code'] = htmlspecialchars($category['symbolic_code']);
    if (!empty($_POST['category'])){
        $categories[$key]['isSelected'] = ($_POST['category'] === $category['id']) ? 'selected' : '';
    }

};

$requiredFields = [
    'lot-name',
    'category',
    'message',
    'lot-rate',
    'lot-step',
    'lot-date',
    ];
$errors = [];
$safeData = [];
$nameValue = '';
$categoryValue = '';
$messageValue = '';
$imgValue = '';
$rateValue = '';
$stepValue = '';
$dateValue = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    var_dump($_POST);
    $safeData = getSafeData($_POST);

    if ($_FILES['lot-img']['size'] > 0) {
        $fileName = $_FILES['lot-img']['name'];
        $filePath = __DIR__ . '/uploads/';
        $imgUrlPost = $filePath . $fileName;
        move_uploaded_file($_FILES['lot-img']['tmp_name'], $filePath . $fileName);
        $imgValue = $filePath . $fileName;
    }

    $errors = validateForm($requiredFields);

if (count($errors) === 0){
    insertLot($CONNECTION);
    $nameValue = '';
    $categoryValue = '';
    $messageValue = '';
    $imgValue = '';
    $rateValue = '';
    $stepValue = '';
    $dateValue = '';
    foreach ($categories as $key => $category) {
        $categories[$key]['isSelected'] = '';
    };
        /*header("Location: /index.php");*/
}
}

$content = include_template('add-lot.php', [
    'categories' => $categories,
    'errors' => $errors,
    'safeData' => $safeData,
    'imgValue' => $imgValue,
]);

print include_template('layout.php', [
    'title' => $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'content' => $content,
    'categories' => $categories,
]);