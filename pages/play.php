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
$loginre = '/?p=login_bt';

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
                finalSrc = src.replace("../resource/", "/resource/");
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
</body>
</html>