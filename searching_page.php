<?php
/**
 * 1. Database Connection (DB 연결)
 * 팀원들과 공유한 MySQL 접속 정보로 수정해주세요.
 */
$host = 'localhost';
$db   = 'lost_found_db'; // 사용하시는 DB 이름
$user = 'root';          // DB 아이디
$pass = '';              // DB 비밀번호
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

/**
 * 2. GET 요청 파라미터 받기 (검색 필터)
 * PDF Page 6 의 UI 요소와 매핑됩니다.
 */
$keyword = $_GET['keyword'] ?? '';
$location = $_GET['location'] ?? ''; // Value: Tokyo, Kyoto
$time = $_GET['time'] ?? '';         // Value: today, yesterday
$categories = $_GET['category'] ?? []; // Array: Phone, Keys...

/**
 * 3. SQL 쿼리 작성 (Core Logic)
 * PDF Page 9 의 ER Diagram을 기반으로 ITEM과 REPORT 테이블을 JOIN합니다.
 * - ITEM 테이블: 물건의 이름(name), 카테고리(category)
 * - REPORT 테이블: 습득 장소(location), 날짜(reportDate), 사진(picture)
 * - 관계: ITEM (1) --- involves --- (N) REPORT (PDF source: 143)
 */
$sql = "SELECT i.itemID, i.name, i.category, r.location, r.reportDate, r.picture 
        FROM ITEM i
        JOIN REPORT r ON i.itemID = r.itemID
        WHERE 1=1"; // 유동적인 WHERE 절 추가를 위한 기본 조건

$params = [];

// [필터 1] Keyword Search: 아이템 이름 부분 검색 (LIKE) 
if (!empty($keyword)) {
    $sql .= " AND i.name LIKE ?";
    $params[] = "%$keyword%";
}

// [필터 2] Location Filter: 지역 일치 여부 
if (!empty($location)) {
    $sql .= " AND r.location = ?";
    $params[] = $location;
}

// [필터 3] Time Filter: 날짜 계산 
if ($time === 'today') {
    $sql .= " AND r.reportDate = CURDATE()"; 
} elseif ($time === 'yesterday') {
    $sql .= " AND r.reportDate = CURDATE() - INTERVAL 1 DAY";
}

// [필터 4] Category Filter: 다중 선택 (IN 절 사용) 
if (!empty($categories)) {
    // 체크된 개수만큼 물음표(?) 생성 (예: IN (?, ?))
    $placeholders = implode(',', array_fill(0, count($categories), '?'));
    $sql .= " AND i.category IN ($placeholders)";
    foreach ($categories as $cat) {
        $params[] = $cat;
    }
}

// 정렬: 최신순 (내림차순)
$sql .= " ORDER BY r.reportDate DESC";

// 4. 쿼리 실행
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found - Search</title>
    <style>
        /* PDF Page 1의 "Blue, White, Minimal" 테마 및 Page 6 레이아웃 적용 */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f8f9fa; color: #333; }
        
        /* Header */
        .header { background-color: #fff; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e1e4e8; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; color: #000; }
        .login-link { text-decoration: none; color: #666; font-size: 14px; }

        /* Layout: Sidebar + Content */
        .container { display: flex; max-width: 1200px; margin: 40px auto; padding: 0 20px; gap: 40px; }
        
        /* Left Sidebar (Filters) */
        .sidebar { width: 260px; flex-shrink: 0; }
        .sidebar-title { font-size: 18px; font-weight: 600; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; display: inline-block; }
        
        .filter-section { margin-bottom: 30px; }
        .filter-label { font-weight: 600; margin-bottom: 10px; display: block; font-size: 14px; color: #444; }
        
        .search-box { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
        
        .radio-group, .check-group { display: flex; flex-direction: column; gap: 8px; }
        .radio-item, .check-item { display: flex; align-items: center; font-size: 14px; color: #555; cursor: pointer; }
        .radio-item input, .check-item input { margin-right: 10px; accent-color: #007bff; }

        .search-btn { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .search-btn:hover { background-color: #0056b3; }

        /* Right Content (Results) */
        .results-area { flex-grow: 1; }
        .item-card { background: #fff; border-radius: 10px; padding: 20px; margin-bottom: 20px; display: flex; align-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: transform 0.2s; }
        .item-card:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.1); }
        
        .item-img { width: 100px; height: 100px; border-radius: 8px; object-fit: cover; background-color: #eee; margin-right: 25px; }
        
        .item-info { flex-grow: 1; }
        .item-name { font-size: 18px; font-weight: 700; margin: 0 0 8px 0; color: #000; }
        .item-detail { margin: 0 0 5px 0; font-size: 14px; color: #777; display: flex; align-items: center; }
        .item-detail span { margin-left: 5px; }
        
        .detail-btn { background-color: #2c7be5; color: white; text-decoration: none; padding: 8px 20px; border-radius: 5px; font-size: 13px; font-weight: 600; }
        .detail-btn:hover { background-color: #1a68d1; }

        .no-result { text-align: center; color: #888; margin-top: 50px; }
    </style>
</head>
<body>

    <header class="header">
        <h1>Lost & Found</h1>
        <a href="#" class="login-link">Login</a>
    </header>

    <div class="container">
        <aside class="sidebar">
            <h2 class="sidebar-title">Searching Page</h2>
            
            <form action="" method="GET">
                <input type="text" name="keyword" class="search-box" placeholder="Keyword (e.g., Bag)" value="<?= htmlspecialchars($keyword) ?>">

                <div class="filter-section">
                    <label class="filter-label">Location</label>
                    <div class="radio-group">
                        <label class="radio-item"><input type="radio" name="location" value="" <?= $location==''?'checked':'' ?>> All</label>
                        <label class="radio-item"><input type="radio" name="location" value="Tokyo" <?= $location=='Tokyo'?'checked':'' ?>> Tokyo</label>
                        <label class="radio-item"><input type="radio" name="location" value="Kyoto" <?= $location=='Kyoto'?'checked':'' ?>> Kyoto</label>
                    </div>
                </div>

                <div class="filter-section">
                    <label class="filter-label">Time</label>
                    <div class="radio-group">
                        <label class="radio-item"><input type="radio" name="time" value="" <?= $time==''?'checked':'' ?>> All Time</label>
                        <label class="radio-item"><input type="radio" name="time" value="today" <?= $time=='today'?'checked':'' ?>> Today</label>
                        <label class="radio-item"><input type="radio" name="time" value="yesterday" <?= $time=='yesterday'?'checked':'' ?>> Yesterday</label>
                    </div>
                </div>

                <div class="filter-section">
                    <label class="filter-label">Category</label>
                    <div class="check-group">
                        <label class="check-item"><input type="checkbox" name="category[]" value="Phone" <?= in_array('Phone', $categories)?'checked':'' ?>> Phone</label>
                        <label class="check-item"><input type="checkbox" name="category[]" value="Keys" <?= in_array('Keys', $categories)?'checked':'' ?>> Keys</label>
                        <label class="check-item"><input type="checkbox" name="category[]" value="Bag" <?= in_array('Bag', $categories)?'checked':'' ?>> Bag</label>
                    </div>
                </div>

                <button type="submit" class="search-btn">SEARCH</button>
            </form>
        </aside>

        <main class="results-area">
            <?php if (count($items) > 0): ?>
                <?php foreach ($items as $item): ?>
                    <div class="item-card">
                        <?php 
                            $imgSrc = !empty($item['picture']) ? htmlspecialchars($item['picture']) : 'https://via.placeholder.com/150/F0F0F0/888888?text=No+Image'; 
                        ?>
                        <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="item-img">
                        
                        <div class="item-info">
                            <h3 class="item-name"><?= htmlspecialchars($item['name']) ?></h3>
                            
                            <p class="item-detail">
                                📍 <?= htmlspecialchars($item['location']) ?>
                            </p>
                            
                            <p class="item-detail">
                                🕒 <?= htmlspecialchars($item['reportDate']) ?>
                            </p>
                        </div>

                        <a href="detail.php?id=<?= $item['itemID'] ?>" class="detail-btn">detail</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-result">
                    <p>No items found matching your criteria.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

</body>
</html>