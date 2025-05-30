<?php
function fetchProducts($conn, $search = '', $category = '', $sort = 'newest', $itemsPerPage = 12, $offset = 0) {
    // Build the query
    $query = "SELECT * FROM products WHERE 1=1";
    
    // Add search condition
    if (!empty($search)) {
        $searchTerm = "%{$search}%";
        $query .= " AND (product_name LIKE ? OR category LIKE ?)";
    }
    
    // Add category filter
    if (!empty($category)) {
        $query .= " AND category = ?";
    }
    
    // Add sorting
    switch ($sort) {
        case 'price_low':
            $query .= " ORDER BY price ASC";
            break;
        case 'price_high':
            $query .= " ORDER BY price DESC";
            break;
        case 'name_asc':
            $query .= " ORDER BY product_name ASC";
            break;
        case 'name_desc':
            $query .= " ORDER BY product_name DESC";
            break;
        case 'newest':
        default:
            $query .= " ORDER BY id DESC";
            break;
    }
    
    // Add pagination
    $query .= " LIMIT ? OFFSET ?";
    
    // Prepare and execute main query
    $stmt = $conn->prepare($query);
    
    if (!empty($search) && !empty($category)) {
        $stmt->bind_param("ssii", $searchTerm, $searchTerm, $category, $itemsPerPage, $offset);
    } elseif (!empty($search)) {
        $stmt->bind_param("ssii", $searchTerm, $searchTerm, $itemsPerPage, $offset);
    } elseif (!empty($category)) {
        $stmt->bind_param("sii", $category, $itemsPerPage, $offset);
    } else {
        $stmt->bind_param("ii", $itemsPerPage, $offset);
    }
    
    $stmt->execute();
    return $stmt->get_result();
}
?>