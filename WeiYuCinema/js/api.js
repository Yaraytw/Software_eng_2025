// js/api.js - 前端與後端溝通的橋樑

// 設定後端 API 入口 (假設 index.php 在專案根目錄)
const API_URL = 'index.php';

/**
 * 通用的 API 呼叫函式
 * @param {string} action - 要執行的動作 (例如 'login', 'register', 'get_movies')
 * @param {object} data - 要傳送的資料 (例如 { email: '...', pwd: '...' })
 * @param {string} method - HTTP 方法 (預設 POST)
 */
async function callApi(action, data = {}, method = 'POST') {
    try {
        // 1. 準備請求選項
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json' // 告訴後端我們傳的是 JSON
            }
        };

        // 2. 如果是 POST，將資料放入 body；如果是 GET，將資料轉為 URL 參數
        if (method.toUpperCase() === 'POST') {
            // 所有的 action 都包在資料裡傳給 index.php
            data.action = action;
            options.body = JSON.stringify(data);
        } else {
            // GET 請求將參數串在網址後
            const params = new URLSearchParams({ action: action, ...data });
            return await fetch(`${API_URL}?${params.toString()}`).then(res => res.json());
        }

        // 3. 發送請求並等待回應
        const response = await fetch(API_URL, options);
        
        // 4. 解析 JSON 回應
        return await response.json();

    } catch (error) {
        console.error("API Error:", error);
        return { status: false, message: "系統連線錯誤，請稍後再試。" };
    }
}