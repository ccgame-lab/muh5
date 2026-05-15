<?php

declare(strict_types=1);

require_login();

$user = current_user();

// Placeholders/Config for Launcher
$uid = $user['username'];
$sid = 1;

$spverify = 'error';
try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $config['db_s1']['host'], $config['db_s1']['port'], $config['db_s1']['database']),
        $config['db_s1']['username'],
        $config['db_s1']['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $stmt = $pdo->prepare('SELECT passwd FROM globaluser WHERE account = ? LIMIT 1');
    $stmt->execute([$uid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $spverify = $row['passwd'];
    }
} catch (PDOException $e) {
    error_log("DB Fetch Error: " . $e->getMessage());
}

$srvaddr = ($config['server']['ws_host'] ?? 'muh5-ws.ccgame.org') . ($config['server']['ws_path'] ?? '/s1/');
$srvport = (string) ($config['server']['ws_port'] ?? 443);
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$loginre = $protocol . $_SERVER['HTTP_HOST'] . '/?p=login_bt';

$cdnUrl = rtrim($config['assets']['base_url'] ?? '', '/');
$cdnResBase = empty($cdnUrl) ? '/resource/' : $cdnUrl . '/resource/';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>MUH5 - Play</title>
    <meta name="viewport" content="width=device-width,initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="full-screen" content="true" />
    <meta name="screen-orientation" content="portrait" />
    <meta name="x5-fullscreen" content="true" />
    <meta name="360-fullscreen" content="true" />
    <style>
        html, body {
            -ms-touch-action: none;
            background: #000000;
            padding: 0;
            border: 0;
            margin: 0;
            height: 100%;
        }
    </style>
</head>
<body>
    <div style="margin: auto;width: 100%;height: 100%;" class="egret-player" 
        data-entry-class="Main"
        data-orientation="portrait" 
        data-scale-mode="fixedNarrow" 
        data-frame-rate="30" 
        data-content-width="720"
        data-content-height="1280" 
        data-show-paint-rect="false" 
        data-multi-fingered="2" 
        data-show-fps="false"
        data-show-log="false" 
        data-show-fps-style="x:0,y:0,size:12,textColor:0xffffff,bgAlpha:0.9">
    </div>

    <script>
        // Global Game Vars
        window["loginre"] = <?php echo json_encode($loginre); ?>;
        window["uid"] = <?php echo json_encode($uid); ?>;
        window["sid"] = <?php echo json_encode($sid); ?>;
        window["spverify"] = <?php echo json_encode($spverify); ?>;
        window["svrip"] = <?php echo json_encode($srvaddr); ?>;
        window["port"] = <?php echo json_encode($srvport); ?>;
        window["showurl"] = true;
        window["hosts"] = <?php echo json_encode($cdnResBase); ?>;

        // Legacy Fallback
        window.openPayModal = function(url) {
            console.log("Premium Store requested (Fallback): " + url);
        };

        var loadScript = function (list, callback) {
            var loaded = 0;
            var loadNext = function () {
                loadSingleScript(list[loaded], function () {
                    loaded++;
                    if (loaded >= list.length) {
                        callback();
                    }
                    else {
                        loadNext();
                    }
                })
            };
            loadNext();
        };

        var loadSingleScript = function (src, callback) {
            var s = document.createElement('script');
            s.async = false;
            
            // Path mapping logic
            var finalSrc = src;
            if (src.indexOf("../resource/") === 0) {
                finalSrc = src.replace("../resource/", window["hosts"]);
            } else if (src.indexOf("http") !== 0 && src.indexOf("/") !== 0) {
                finalSrc = "/" + src;
            }
            
            s.src = finalSrc;
            s.addEventListener('load', function () {
                s.parentNode.removeChild(s);
                s.removeEventListener('load', arguments.callee, false);
                callback();
            }, false);
            document.body.appendChild(s);
        };

        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/manifest.json?v=' + Math.random(), true);
        xhr.addEventListener("load", function () {
            var manifest = JSON.parse(xhr.response);
            var list = manifest.initial.concat(manifest.game);
            loadScript(list, function () {
                /**
                 * {
                 * "renderMode":, //Engine rendering mode, "canvas" or "webgl"
                 * "audioType": 0 //Use the audio type, 0: default, 2: web audio, 3: audio
                 * "calculateCanvasScaleFactor":function //Build-in method of collecting screen resolution
                 * }
                 **/
                egret.runEgret({ renderMode: "webgl", audioType: 0, calculateCanvasScaleFactor:function(context) {
                    var backingStore = context.backingStorePixelRatio ||
                        context.webkitBackingStorePixelRatio ||
                        context.mozBackingStorePixelRatio ||
                        context.msBackingStorePixelRatio ||
                        context.oBackingStorePixelRatio ||
                        context.backingStorePixelRatio || 1;
                    return (window.devicePixelRatio || 1) / backingStore;
                }});
            });
        });
        xhr.send(null);
    </script>

    <!-- SDK Floating Icon -->
    <style>
        #muh5-sdk-icon {
            position: fixed;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg,#206bc4,#0ca678);
            color: white;
            text-align: center;
            line-height: 48px;
            font-weight: bold;
            font-family: sans-serif;
            cursor: move;
            z-index: 9999;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            user-select: none;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            touch-action: none;
        }
        #muh5-sdk-panel {
            position: fixed;
            width: 150px;
            background: rgba(15, 17, 23, 0.95);
            border: 1px solid #333;
            border-radius: 8px;
            z-index: 9998;
            display: none;
            flex-direction: column;
            overflow: hidden;
            font-family: sans-serif;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
        }
        #muh5-sdk-panel a, #muh5-sdk-panel span {
            display: block;
            padding: 10px 15px;
            color: #ddd;
            text-decoration: none;
            font-size: 14px;
            border-bottom: 1px solid #222;
        }
        #muh5-sdk-panel a:last-child, #muh5-sdk-panel span:last-child {
            border-bottom: none;
        }
        #muh5-sdk-panel a:hover {
            background: #206bc4;
            color: white;
        }
        #muh5-sdk-panel .disabled {
            color: #666;
            cursor: not-allowed;
        }
    </style>

    <div id="muh5-sdk-icon">SDK</div>
    <div id="muh5-sdk-panel">
        <a href="/">Tài khoản</a>
        <a href="/?p=servers">Máy chủ</a>
        <a href="#" onclick="showPayModal(); return false;">Nạp</a>
        <span class="disabled" title="Chưa mở">Hỗ trợ</span>
        <span class="disabled" title="Chưa mở">GM</span>
    </div>

    <!-- Payment Modal Stub -->
    <div id="pay-modal-overlay" onclick="if(event.target === this) hidePayModal()" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: #1a1c23; border: 1px solid #333; border-radius: 8px; width: 300px; padding: 20px; font-family: sans-serif; color: #ddd; box-shadow: 0 4px 12px rgba(0,0,0,0.5); text-align: center;">
            <h3 style="margin-top: 0; color: #fff;">Nạp</h3>
            <p style="margin-bottom: 20px; font-size: 14px; color: #bbb;">GreenJade ID payment provider - coming soon</p>
            <button onclick="hidePayModal()" style="background: #206bc4; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: bold;">Đóng</button>
        </div>
    </div>

    <script>
        function showPayModal() {
            document.getElementById('pay-modal-overlay').style.display = 'flex';
            document.getElementById('muh5-sdk-panel').style.display = 'none';
        }
        function hidePayModal() {
            document.getElementById('pay-modal-overlay').style.display = 'none';
        }

        (function() {
            var icon = document.getElementById('muh5-sdk-icon');
            var panel = document.getElementById('muh5-sdk-panel');
            
            var pos = localStorage.getItem('muh5_sdk_position');
            if (pos) {
                try {
                    pos = JSON.parse(pos);
                    icon.style.left = pos.left + 'px';
                    icon.style.top = pos.top + 'px';
                    icon.style.right = 'auto';
                    icon.style.transform = 'none';
                } catch(e) {}
            }

            var isDragging = false;
            var isClick = true;
            var startX, startY, startLeft, startTop;

            function onDown(e) {
                var clientX = e.type.indexOf('touch') === 0 ? e.touches[0].clientX : e.clientX;
                var clientY = e.type.indexOf('touch') === 0 ? e.touches[0].clientY : e.clientY;
                startX = clientX;
                startY = clientY;
                var rect = icon.getBoundingClientRect();
                startLeft = rect.left;
                startTop = rect.top;
                
                icon.style.right = 'auto';
                icon.style.transform = 'none';
                icon.style.left = startLeft + 'px';
                icon.style.top = startTop + 'px';

                isDragging = true;
                isClick = true;
                
                if (e.type === 'mousedown') {
                    e.preventDefault();
                }
            }

            function onMove(e) {
                if (!isDragging) return;
                var clientX = e.type.indexOf('touch') === 0 ? e.touches[0].clientX : e.clientX;
                var clientY = e.type.indexOf('touch') === 0 ? e.touches[0].clientY : e.clientY;
                
                var dx = clientX - startX;
                var dy = clientY - startY;

                if (Math.abs(dx) > 5 || Math.abs(dy) > 5) {
                    isClick = false;
                }

                var newLeft = startLeft + dx;
                var newTop = startTop + dy;

                var maxLeft = window.innerWidth - icon.offsetWidth;
                var maxTop = window.innerHeight - icon.offsetHeight;

                newLeft = Math.max(0, Math.min(newLeft, maxLeft));
                newTop = Math.max(0, Math.min(newTop, maxTop));

                icon.style.left = newLeft + 'px';
                icon.style.top = newTop + 'px';
                
                updatePanelPos();
                
                if (e.type === 'touchmove') {
                    e.preventDefault();
                }
            }

            function onUp(e) {
                if (!isDragging) return;
                isDragging = false;
                
                if (isClick) {
                    togglePanel();
                } else {
                    localStorage.setItem('muh5_sdk_position', JSON.stringify({
                        left: parseInt(icon.style.left),
                        top: parseInt(icon.style.top)
                    }));
                }
            }

            function togglePanel() {
                if (panel.style.display === 'flex') {
                    panel.style.display = 'none';
                } else {
                    panel.style.display = 'flex';
                    updatePanelPos();
                }
            }

            function updatePanelPos() {
                if (panel.style.display !== 'flex') return;
                
                var rect = icon.getBoundingClientRect();
                var pTop = rect.top;
                var pLeft = rect.left - 160;
                if (pLeft < 0) pLeft = rect.right + 10;
                
                var pHeight = panel.offsetHeight || 160;
                var adjustedTop = pTop;
                if (adjustedTop + pHeight > window.innerHeight) {
                    adjustedTop = window.innerHeight - pHeight - 10;
                }
                if (adjustedTop < 0) adjustedTop = 10;
                
                panel.style.top = adjustedTop + 'px';
                panel.style.left = pLeft + 'px';
            }

            icon.addEventListener('mousedown', onDown, {passive: false});
            window.addEventListener('mousemove', onMove, {passive: false});
            window.addEventListener('mouseup', onUp);

            icon.addEventListener('touchstart', onDown, {passive: false});
            window.addEventListener('touchmove', onMove, {passive: false});
            window.addEventListener('touchend', onUp);
        })();
    </script>
</body>
</html>