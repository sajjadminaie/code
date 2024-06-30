<?php
// include 'data.php';
// print_r($imageDimensions[1][1]);    echo "</br>";  
// print_r($second_sub_mainFolder[1][1]);
?>

<?php
// $mainFolder = "../Sort/Library/Majles";
// $sub_mainFolder = scandir($mainFolder);

// $mainFolderlength = count($sub_mainFolder);
// for ($i = 0; $i < $mainFolderlength; $i++) {
//     if ($sub_mainFolder[$i] == ".." || $sub_mainFolder[$i] == ".") {
//         array_splice($sub_mainFolder, $i, 1);
//         $mainFolderlength--;
//         $i--;
//     }
// }

// $mainFolderlength = count($sub_mainFolder);

// $mysqli = new mysqli("192.168.1.4", "Administrator", "Sajjad5858308", "book_database");
// if ($mysqli->connect_error) {
//     die("Connection failed: " . $mysqli->connect_error);
// }

// for ($i = 0; $i < $mainFolderlength; $i++) {
//     if (!is_file($mainFolder . '/' . $sub_mainFolder[$i])) {
//         $second_sub_mainFolder = scandir("$mainFolder/$sub_mainFolder[$i]");
//         array_splice($second_sub_mainFolder, 0, 2);
//         sort($second_sub_mainFolder, SORT_NATURAL);

//         foreach ($second_sub_mainFolder as $file) {
//             $filePath = $mainFolder . '/' . $sub_mainFolder[$i] . '/' . $file;
//             list($width, $height) = getimagesize($filePath);

//             $stmt = $mysqli->prepare("INSERT INTO images (book_name, file_name, width, height) VALUES (?, ?, ?, ?)");
//             $stmt->bind_param("ssii", $sub_mainFolder[$i], $file, $width, $height);
//             $stmt->execute();
//             $stmt->close();
//         }
//     }
// }

// $mysqli->close();
?>
<?php
// $mainFolder = "../Sort/Library/Majles";
// $sub_mainFolder = scandir($mainFolder);

// $mainFolderlength = count($sub_mainFolder);
// for ($i = 0; $i < $mainFolderlength; $i++) {
//     if ($sub_mainFolder[$i] == ".." || $sub_mainFolder[$i] == ".") {
//         array_splice($sub_mainFolder, $i, 1);
//         $mainFolderlength--;
//         $i--;
//     }
// }

// $mainFolderlength = count($sub_mainFolder);

// $mysqli = new mysqli("192.168.1.4", "Administrator", "Sajjad5858308", "image_gallery");
// if ($mysqli->connect_error) {
//     die("Connection failed: " . $mysqli->connect_error);
// }

// for ($i = 0; $i < $mainFolderlength; $i++) {
//     if (!is_file($mainFolder . '/' . $sub_mainFolder[$i])) {
//         // درج نام پوشه در جدول اصلی
//         $stmt = $mysqli->prepare("INSERT INTO folders (folder_name) VALUES (?)");
//         $stmt->bind_param("s", $sub_mainFolder[$i]);
//         $stmt->execute();
//         $folder_id = $stmt->insert_id; // شناسه پوشه درج شده را دریافت کنید
//         $stmt->close();

//         // ایجاد جدول جداگانه برای هر پوشه
//         $table_name = "folder_" . $folder_id;
//         $createTableSql = "CREATE TABLE $table_name (
//             id INT AUTO_INCREMENT PRIMARY KEY,
//             file_name VARCHAR(255) NOT NULL,
//             width INT NOT NULL,
//             height INT NOT NULL
//         )";
//         $mysqli->query($createTableSql);

//         // درج اطلاعات تصاویر در جدول مربوطه
//         $second_sub_mainFolder = scandir("$mainFolder/$sub_mainFolder[$i]");
//         array_splice($second_sub_mainFolder, 0, 2);
//         sort($second_sub_mainFolder, SORT_NATURAL);

//         foreach ($second_sub_mainFolder as $file) {
//             $filePath = $mainFolder . '/' . $sub_mainFolder[$i] . '/' . $file;
//             list($width, $height) = getimagesize($filePath);

//             $stmt = $mysqli->prepare("INSERT INTO $table_name (file_name, width, height) VALUES (?, ?, ?)");
//             $stmt->bind_param("sii", $file, $width, $height);
//             $stmt->execute();
//             $stmt->close();
//         }
//     }
// }

// $mysqli->close();
?>

<?php
$mainFolder = "../Sort/Library/Majles";
$sub_mainFolder = scandir($mainFolder);

$mainFolderlength = count($sub_mainFolder);
for ($i = 0; $i < $mainFolderlength; $i++) {
    if ($sub_mainFolder[$i] == ".." || $sub_mainFolder[$i] == ".") {
        array_splice($sub_mainFolder, $i, 1);
        $mainFolderlength--;
        $i--;
    }
}

$mainFolderlength = count($sub_mainFolder);

// اتصال به دیتابیس
$mysqli = new mysqli("192.168.1.4", "Administrator", "Sajjad5858308", "book_database");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// شروع تراکنش
$mysqli->begin_transaction();

try {
    foreach ($sub_mainFolder as $folder) {
        if (!is_dir($mainFolder . '/' . $folder)) {
            continue;
        }
        $library = "Majles";

        // درج کتاب در جدول books
        $stmt = $mysqli->prepare("INSERT INTO books (bookname, library) VALUES ( ?, ?)");
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement for inserting book: " . $mysqli->error);
        }
        $stmt->bind_param("ss", $folder,$library);
        $stmt->execute();
        $book_id = $stmt->insert_id;

        $stmt->close();

        $second_sub_mainFolder = array_diff(scandir("$mainFolder/$folder"), array('..', '.'));

        // با استفاده از تابع exif_imagetype برای بررسی نوع فایل و فقط پردازش تصاویر
        foreach ($second_sub_mainFolder as $page_number => $file) {
            $filePath = $mainFolder . '/' . $folder . '/' . $file;

            if (!is_file($filePath)) {
                continue;
            }

            // بررسی نوع فایل با exif_imagetype
            $image_type = exif_imagetype($filePath);
            if ($image_type === false) {
                continue; // اگر فایل تصویری نبود، ادامه نده
            }

            // حساب طول و عرض عکس با استفاده از getimagesize
            list($width, $height) = getimagesize($filePath);

            // بررسی اینکه آیا عرض و ارتفاع به‌درستی خوانده شده‌اند
            if ($width === false || $height === false) {
                throw new Exception("Failed to get image size for file: $filePath");
            }
            // درج صفحه کتاب در جدول book_pages
            $stmt = $mysqli->prepare("INSERT INTO bookpages (bookid, bookname, pageid, pagenumber, file_name, width, height) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                throw new Exception("Failed to prepare statement for inserting page: " . $mysqli->error);
            }
            $page_id = $stmt->insert_id;

            $stmt->bind_param("isiisii",$book_id, $folder, $page_id, $page_number, $file, $width, $height);
            $stmt->execute();
            $stmt->close();
        }
    }

    // پایان تراکنش با موفقیت
    $mysqli->commit();
} catch (Exception $e) {
    // بازگردانی در صورت بروز خطا
    $mysqli->rollback();
    echo "Transaction failed: " . $e->getMessage();
}

$mysqli->close();
?>

<!-- TRUNCATE TABLE bookpages;
DELETE FROM books;
ALTER TABLE books AUTO_INCREMENT = 1; -->
