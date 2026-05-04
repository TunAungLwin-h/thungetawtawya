<?php
session_start();
$pageTitle = "Dhamma Videos | Sutta & Talks";
$defaultVideoId = 'kXYF2xVs_B0';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Crimson+Pro:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../layouts/assets/css/dhama_content.css">
    <style>
    /* Upgraded video info block */
    .video-info {
        padding: 20px 20px 24px;
        background: linear-gradient(180deg, #fffefb 0%, #faf6ef 100%);
        border: 1px solid var(--monastery-border, #d4c4a8);
        border-top: none;
        border-radius: 0 0 12px 12px;
        box-shadow: 0 4px 16px rgba(74, 51, 32, 0.06);
    }

    .video-info .video-title {
        font-family: 'Crimson Pro', serif;
        font-size: 1.35rem;
        font-weight: 700;
        line-height: 1.35;
        color: var(--monastery-deep, #4a3320);
        margin: 0 0 12px;
        letter-spacing: 0.01em;
    }

    .video-info .video-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 16px 20px;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 14px;
        border-bottom: 1px solid rgba(212, 196, 168, 0.6);
    }

    .video-info .video-meta span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.9rem;
        color: var(--monastery-brown, #6b4e3d);
    }

    .video-info .video-meta i {
        color: var(--monastery-gold, #c9a227);
        font-size: 0.85rem;
    }

    .video-info .captions-wrap {
        margin: 0;
        padding: 14px 16px;
        background: rgba(201, 162, 39, 0.07);
        border-radius: 10px;
        border-left: 4px solid var(--monastery-gold, #c9a227);
    }

    .video-info .captions-wrap .captions-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--monastery-brown, #6b4e3d);
        margin-bottom: 6px;
    }

    .video-info .captions-wrap .captions-text {
        font-size: 0.95rem;
        line-height: 1.65;
        color: var(--monastery-deep, #4a3320);
    }

    .video-info .captions-wrap.empty {
        display: none;
    }

    body.focus-mode .video-info {
        background: var(--focus-panel, #252019);
        border-color: rgba(255, 255, 255, 0.06);
    }

    body.focus-mode .video-info .video-title {
        color: #e8e0d4;
    }

    body.focus-mode .video-info .video-meta span {
        color: #a89888;
    }

    body.focus-mode .video-info .captions-wrap {
        background: rgba(201, 162, 39, 0.1);
    }

    body.focus-mode .video-info .captions-wrap .captions-text {
        color: #e0d8cc;
    }
    </style>
</head>

<body>
    <header class="stream-header">
        <?php include '../layouts/navbar.php'; ?>
        <div class="header-actions">
            <button type="button" id="focusModeBtn" aria-label="Focus mode">
                <i class="fa-solid fa-expand"></i> Focus mode
            </button>
        </div>
    </header>

    <main class="stream-main">
        <section class="player-section">
            <div class="filters" id="filters">
                <button type="button" class="active" data-category="all">All</button>
                <button type="button" data-category="sutta">Sutta</button>
                <button type="button" data-category="talk">Dhamma Talk</button>
                <button type="button" data-category="meditation">Meditation</button>
                <button type="button" data-category="chanting">Chanting</button>
            </div>
            <div class="player-wrap">
                <button type="button" class="focus-toggle" id="focusToggle" aria-label="Toggle focus mode">
                    <i class="fa-solid fa-expand"></i>
                </button>
                <div id="embedContainer" class="embed-container">
                    <!-- Paste your first video embed code below; or leave this iframe and set the first item in DHAMMA_VIDEOS to match -->
                    <iframe id="playerFrame" width="560" height="315"
                        src="https://www.youtube.com/embed/_7Rh5VdDcrI?si=YL0zXjjQ3tvdfuaE" title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
            </div>

            <div class="video-info">
                <h2 class="video-title" id="videoTitle">Dhamma Talk — Select a video from the list</h2>
                <div class="video-meta">
                    <span><i class="fa-solid fa-clock"></i> <span id="videoDuration">—</span></span>
                    <span><i class="fa-solid fa-tag"></i> <span id="videoCategory">—</span></span>
                </div>
                <div class="captions-wrap empty" id="captionsWrap">
                    <div class="captions-label">Description</div>
                    <p class="captions-text" id="captionsText"></p>
                </div>
            </div>


        </section>

        <aside class="playlist-panel">
            <h3><i class="fa-solid fa-list"></i> Video list</h3>
            <div class="playlist-list" id="playlistList"></div>
        </aside>
    </main>

    <!-- Static video list: add entries manually. For each video use embedSrc (URL from YouTube embed code) or embed (full iframe HTML). -->
    <script>
    window.DHAMMA_VIDEOS = [{
            embedSrc: 'https://www.youtube.com/embed/V3AQ2of3_0M?si=t9FojQoNNyr0FRnj',
            title: 'Your first Dhamma talk',
            duration: '45:00',
            category: 'talk',
            thumb: 'https://img.youtube.com/vi/V3AQ2of3_0M/mqdefault.jpg',
            captions: 'Brief description of this video. You can add a short summary or key points here.'
        },
        {
            embedSrc: 'https://www.youtube.com/embed/ZlmUXb8oQnM?si=CPLF7kYmVb5PjRaM',
            title: 'Dhamma Talk — The Four Noble Truths',
            duration: '45:00',
            category: 'sutta',
            thumb: 'https://img.youtube.com/vi/ZlmUXb8oQnM/mqdefault.jpg',
            captions: 'Exploring the Four Noble Truths as the foundation of the Buddha\'s teaching.'
        },
        {
            embedSrc: 'https://youtu.be/RP5Vi8o-wLk?si=Z6Q3YauHfnt--yYZ',
            title: 'Metta Bhavana — Loving Kindness',
            duration: '28:15',
            category: 'meditation',
            thumb: 'https://img.youtube.com/vi/RP5Vi8o-wLk/mqdefault.jpg',
            captions: 'Guided loving-kindness meditation practice.'
        },
        {
            embedSrc: 'https://youtu.be/aPPM0wv-nAs?si=YbMygFe4Z5zo9MAZ',
            title: 'Pali Chanting — Paritta',
            duration: '18:00',
            category: 'chanting',
            thumb: 'https://img.youtube.com/vi/aPPM0wv-nAs/mqdefault.jpg',
            captions: 'Traditional Paritta chanting for peace and protection.'
        },
        {
            embedSrc: 'https://youtu.be/d5Pc6SC7wYw?si=nUqw-MwPyWEuSPCE',
            title: 'Dhamma Discussion — Kamma',
            duration: '52:00',
            category: 'talk',
            thumb: 'https://img.youtube.com/vi/d5Pc6SC7wYw/mqdefault.jpg',
            captions: 'Understanding kamma (action) and its results in Buddhist teaching.'
        }
    ];
    </script>
    <script>
    (function() {
        const embedContainer = document.getElementById('embedContainer');
        const videoTitle = document.getElementById('videoTitle');
        const videoDuration = document.getElementById('videoDuration');
        const videoCategory = document.getElementById('videoCategory');
        const captionsWrap = document.getElementById('captionsWrap');
        const captionsText = document.getElementById('captionsText');
        const playlistList = document.getElementById('playlistList');
        const filters = document.getElementById('filters');
        const focusModeBtn = document.getElementById('focusModeBtn');
        const focusToggle = document.getElementById('focusToggle');

        const categoryLabels = {
            sutta: 'Sutta',
            talk: 'Dhamma Talk',
            meditation: 'Meditation',
            chanting: 'Chanting'
        };
        let currentFilter = 'all';
        let currentIndex = 0;
        const list = window.DHAMMA_VIDEOS || [];

        function playVideo(item, index) {
            currentIndex = index;
            var frame = embedContainer ? embedContainer.querySelector('#playerFrame') || document.getElementById(
                'playerFrame') : document.getElementById('playerFrame');
            if (item.embed) {
                embedContainer.innerHTML = item.embed;
            } else if (item.embedSrc) {
                if (!frame) {
                    var iframe = document.createElement('iframe');
                    iframe.id = 'playerFrame';
                    iframe.setAttribute('width', '560');
                    iframe.setAttribute('height', '315');
                    iframe.setAttribute('frameborder', '0');
                    iframe.setAttribute('allow',
                        'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share'
                    );
                    iframe.setAttribute('allowfullscreen', '');
                    iframe.setAttribute('title', 'YouTube video player');
                    embedContainer.innerHTML = '';
                    embedContainer.appendChild(iframe);
                    frame = iframe;
                }
                frame.src = item.embedSrc;
            }
            videoTitle.textContent = item.title;
            videoDuration.textContent = item.duration || '—';
            videoCategory.textContent = categoryLabels[item.category] || item.category || '—';
            if (item.captions) {
                captionsText.textContent = item.captions;
                captionsWrap.classList.remove('empty');
            } else {
                captionsText.textContent = '';
                captionsWrap.classList.add('empty');
            }
            var idx = list.indexOf(item);
            document.querySelectorAll('.playlist-item').forEach(function(el) {
                el.classList.toggle('active', parseInt(el.dataset.index, 10) === idx);
            });
        }

        function thumbUrl(item) {
            if (item.thumb) return item.thumb;
            if (item.embedSrc) {
                var m = item.embedSrc.match(/\/embed\/([^/?]+)/);
                if (m) return 'https://img.youtube.com/vi/' + m[1] + '/mqdefault.jpg';
            }
            return '';
        }

        function renderPlaylist() {
            var filtered = currentFilter === 'all' ? list : list.filter(function(v) {
                return v.category === currentFilter;
            });
            playlistList.innerHTML = filtered.map(function(item, i) {
                var globalIndex = list.indexOf(item);
                var thumb = thumbUrl(item);
                return '<div class="playlist-item' + (globalIndex === currentIndex ? ' active' : '') +
                    '" data-index="' + globalIndex + '">' +
                    '<div class="thumb">' + (thumb ? '<img src="' + thumb + '" alt="" loading="lazy">' :
                        '') + '</div>' +
                    '<div class="text"><div class="title">' + item.title + '</div>' +
                    '<div class="meta">' + (item.duration || '—') + ' · ' + (categoryLabels[item
                        .category] || item.category || '—') + '</div></div></div>';
            }).join('');
            playlistList.querySelectorAll('.playlist-item').forEach(function(el) {
                el.addEventListener('click', function() {
                    playVideo(list[parseInt(this.dataset.index, 10)], parseInt(this.dataset.index,
                        10));
                });
            });
        }

        filters.addEventListener('click', function(e) {
            var btn = e.target.closest('button');
            if (!btn) return;
            filters.querySelectorAll('button').forEach(function(b) {
                b.classList.remove('active');
            });
            btn.classList.add('active');
            currentFilter = btn.dataset.category || 'all';
            renderPlaylist();
        });

        function toggleFocus() {
            document.body.classList.toggle('focus-mode');
            var isFocus = document.body.classList.contains('focus-mode');
            focusModeBtn.innerHTML = isFocus ? '<i class="fa-solid fa-compress"></i> Exit focus' :
                '<i class="fa-solid fa-expand"></i> Focus mode';
            if (focusToggle && focusToggle.querySelector('i')) focusToggle.querySelector('i').className = isFocus ?
                'fa-solid fa-compress' : 'fa-solid fa-expand';
        }
        if (focusModeBtn) focusModeBtn.addEventListener('click', toggleFocus);
        if (focusToggle) focusToggle.addEventListener('click', toggleFocus);

        if (list.length) {
            playVideo(list[0], 0);
            renderPlaylist();
        } else {
            videoTitle.textContent = 'No videos yet';
            playlistList.innerHTML =
                '<p style="padding:16px;color:var(--monastery-brown);">Add videos to DHAMMA_VIDEOS in this page.</p>';
        }
    })();
    </script>
</body>

</html>