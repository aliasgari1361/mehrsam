<?php
/**
 * SEO management for posts
 */

function update_seo($id, $data) {
    if (empty($id)) return false;
    
    require_once MASIR_DADE . 'bank.php';
    $bank = new Bank();
    $conn = $bank->getConnection();
    
    $stmt = $conn->prepare("UPDATE posts SET meta_title=?, meta_description=?, meta_keywords=? WHERE id=?");
    $stmt->bind_param("sssi", $data['meta_title'], $data['meta_description'], $data['meta_keywords'], $id);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    
    return $result;
}

function get_seo($id) {
    require_once MASIR_DADE . 'bank.php';
    $bank = new Bank();
    $conn = $bank->getConnection();
    
    $stmt = $conn->prepare("SELECT meta_title, meta_description, meta_keywords FROM posts WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $seo = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    
    return $seo ?: ['meta_title' => '', 'meta_description' => '', 'meta_keywords' => ''];
}