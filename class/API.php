<?php

//require 'vendor/autoload.php'; // Nạp Composer Autoload

use MongoDB\Client as MongoClient;

// Thay thế bằng khóa API của bạn
$googleApiKey = 'AIzaSyCC8hpw58bdOZjyX9F_a0fw00e3dsIHMCw'; // Đặt khóa API của bạn tại đây
$mongoUri = 'mongodb://localhost:27017'; // Đặt URI kết nối MongoDB của bạn tại đây
$dbName = 'ViettelPost'; // Tên cơ sở dữ liệu của bạn
$collectionName = 'BuuCuc'; // Tên bộ sưu tập của bạn

// Geocode địa chỉ và trả về tọa độ
function geocodeAddress($diaChi, $apiKey)
{
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($diaChi) . '&key=' . $apiKey;
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data['status'] === 'OK') {
        $location = $data['results'][0]['geometry']['location'];
        return $location;
    } else {
        return false;
    }
}

// Tính khoảng cách dựa trên đoạn đường đi
function getDrivingDistance($origin, $destination, $apiKey)
{
    $url = 'https://maps.googleapis.com/maps/api/directions/json?origin=' . urlencode($origin) . '&destination=' . urlencode($destination) . '&key=' . $apiKey;
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data['status'] === 'OK') {
        $distance = $data['routes'][0]['legs'][0]['distance']['text'];
        return $distance;
    } else {
        throw new Exception('Directions request failed: ' . $data['status']);
    }
}

// Tìm khoảng cách ngắn nhất
function findNearestAddress($newAddress, $mongoUri, $dbName, $collectionName, $googleApiKey)
{
    $client = new MongoClient($mongoUri);
    $collection = $client->selectDatabase($dbName)->selectCollection($collectionName);

    $newLocation = geocodeAddress($newAddress, $googleApiKey);

    $addresses = $collection->find([]);

    $nearestAddress = null;
    $shortestDistance = PHP_INT_MAX;
    $shortestDistanceText = '';

    foreach ($addresses as $address) {
        try {
            $distanceText = getDrivingDistance($newAddress, $address['diaChi'], $googleApiKey);
            $distance = floatval(str_replace([' km', ','], '', $distanceText));

            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $shortestDistanceText = $distanceText;
                $nearestAddress = $address;
            }
        } catch (Exception $e) {
            echo 'Lỗi: ' . $e->getMessage() . PHP_EOL;
        }
    }

    // Không cần phải gọi $client->close()

    return ['nearestAddress' => $nearestAddress, 'shortestDistance' => $shortestDistanceText];
}

function findNearestAddressAndRemoveAddress($newAddress, $mongoUri, $dbName, $collectionName, $googleApiKey, $buucucDiachiArray = []) {
    // Kiểm tra biến $buucucDiachiArray
    if (!is_array($buucucDiachiArray)) {
        echo 'Biến $buucucDiachiArray không phải là mảng.';
        return;
    }

    // Tạo kết nối MongoDB
    $client = new MongoDB\Client($mongoUri); // Thay đổi ở đây nếu cần
    $collection = $client->selectDatabase($dbName)->selectCollection($collectionName);

    $newLocation = geocodeAddress($newAddress, $googleApiKey);

    $addresses = $collection->find([]);

    $nearestAddress = null;
    $shortestDistance = PHP_INT_MAX;
    $shortestDistanceText = '';

    foreach ($addresses as $address) {
        // Kiểm tra chỉ mục 'address'
        if (!isset($address['diaChi'])) {
            continue; // Bỏ qua nếu chỉ mục 'address' không tồn tại
        }

        // Kiểm tra địa chỉ có nằm trong danh sách loại trừ không
        if (in_array($address['diaChi'], $buucucDiachiArray, true)) {
            continue; // Bỏ qua địa chỉ nếu nó có trong danh sách loại trừ
        }

        try {
            $distanceText = getDrivingDistance($newAddress, $address['diaChi'], $googleApiKey);
            $distance = floatval(str_replace([' km', ','], '', $distanceText));

            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $shortestDistanceText = $distanceText;
                $nearestAddress = $address;
            }
        } catch (Exception $e) {
            echo 'Lỗi: ' . $e->getMessage() . PHP_EOL;
        }
    }

    return ['nearestAddress' => $nearestAddress, 'shortestDistance' => $shortestDistanceText];
}

// Xuất tất cả các địa chỉ từ MongoDB
// function listAddresses($mongoUri, $dbName, $collectionName) {
//     try {
//         $client = new MongoClient($mongoUri);
//         $collection = $client->selectDatabase($dbName)->selectCollection($collectionName);

//         $addresses = $collection->find([]);

//         echo "<br>Danh sách địa chỉ trong bộ sưu tập:";
//         foreach ($addresses as $address) {
//             echo '<br>Địa chỉ: ' . $address['diaChi'] . PHP_EOL;
//         }

//     } catch (Exception $e) {
//         echo 'Lỗi: ' . $e->getMessage() . PHP_EOL;
//     }
// }

// // Hàm kiểm tra kết nối MongoDB
// function checkMongoDBConnection($mongoUri, $dbName) {
//     try {
//         $client = new MongoClient($mongoUri);
//         $database = $client->selectDatabase($dbName);
//         $database->command(['ping' => 1]); // Gửi lệnh ping để kiểm tra kết nối
//         return true; // Kết nối thành công
//     } catch (Exception $e) {
//         echo 'Lỗi kết nối MongoDB: ' . $e->getMessage() . PHP_EOL;
//         return false; // Kết nối không thành công
//     }
// }

// // Ví dụ sử dụng kiểm tra kết nối MongoDB
// if (checkMongoDBConnection($mongoUri, $dbName)) {
//     echo 'Kết nối MongoDB thành công!<br>' . PHP_EOL;
// } else {
//     echo 'Kết nối MongoDB không thành công!<br>' . PHP_EOL;
// }

// // Ví dụ sử dụng tìm địa chỉ gần nhất
// $newAddress = '129 Lê Đình Dương, Phường Nam Dương, Đà Nẵng, Việt Nam';
// $result = findNearestAddress($newAddress, $mongoUri, $dbName, $collectionName, $googleApiKey);

// if ($result['nearestAddress']) {
//     echo 'Địa chỉ gần nhất: ' . $result['nearestAddress']['diaChi'] . PHP_EOL;
//     echo 'Khoảng cách: ' . $result['shortestDistance'] . PHP_EOL;
// } else {
//     echo 'Không tìm thấy địa chỉ gần nhất.' . PHP_EOL;
// }

// // Xuất tất cả các địa chỉ
// listAddresses($mongoUri, $dbName, $collectionName);
