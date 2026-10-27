<?php
/**
 * Hülya Tuncer – Yönetici ve Uzman Danışman Portalı
 * Mahkeme & Vaka Arşivi ve Ön Görüşme Yönetimi
 */

require_once __DIR__ . '/auth.php';

// Giriş işlemi kontrolü
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $result = attemptAdminLogin($username, $password);
    if ($result['success']) {
        header('Location: /admin/');
        exit;
    } else {
        $loginError = $result['error'] ?? 'Giriş başarısız.';
    }
}

// Çıkış işlemi kontrolü
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    adminLogout();
    header('Location: /admin/');
    exit;
}

$isLoggedIn = isAdminLoggedIn();
?>
<!DOCTYPE html>
<html lang="tr" class="h-full bg-stone-100">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Yönetici Portalı | Hülya Tuncer</title>
  <meta name="robots" content="noindex, nofollow, noarchive">
  <link rel="icon" type="image/svg+xml" href="/assets/img/logo_emblem.svg?v=4">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            serif: ['"Cormorant Garamond"', 'Georgia', 'serif'],
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
          },
          colors: {
            brand: {
              50: '#F4F7F5',
              100: '#E6ECE8',
              500: '#2E6B56',
              600: '#245645',
              700: '#1C4235',
              800: '#153127',
              900: '#0E211A',
            }
          }
        }
      }
    }
  </script>
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    .font-serif { font-family: 'Cormorant Garamond', Georgia, serif; }
  </style>
</head>
<body class="h-full text-stone-800 antialiased selection:bg-brand-500 selection:text-white">

<?php if (!$isLoggedIn): ?>
  <!-- ========================================== -->
  <!-- 1. GİRİŞ EKRANI (LOGIN SCREEN)             -->
  <!-- ========================================== -->
  <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white shadow-sm border border-stone-200/80 mb-4 p-2">
        <img src="/assets/img/master_symbol_square.png?v=4" alt="Hülya Tuncer" class="w-full h-full object-cover rounded-xl">
      </div>
      <h2 class="text-3xl font-serif font-bold text-stone-900 tracking-tight">Hülya Tuncer</h2>
      <p class="text-xs font-semibold uppercase tracking-wider text-brand-700 mt-1">Uzman Danışman &amp; Yönetici Portalı</p>
      <div class="inline-block mt-3 px-3 py-1 bg-stone-200/70 border border-stone-300/80 rounded-full text-[11px] text-stone-600 font-medium">
        Mahkeme Hikayeleri &amp; Gizli Vaka Arşivi
      </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
      <div class="bg-white py-8 px-6 sm:px-10 shadow-xl border border-stone-200/80 rounded-3xl space-y-6">
        
        <?php if (!empty($loginError)): ?>
          <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs flex items-center gap-2">
            <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span><?= htmlspecialchars($loginError) ?></span>
          </div>
        <?php endif; ?>

        <form action="/admin/" method="POST" class="space-y-4">
          <input type="hidden" name="action" value="login">
          
          <div>
            <label for="username" class="block text-xs font-semibold text-stone-700 mb-1.5">Kullanıcı Adı</label>
            <input type="text" id="username" name="username" required autocomplete="username" value="admin"
                   class="w-full px-4 py-3 rounded-xl border border-stone-300 text-stone-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label for="password" class="block text-xs font-semibold text-stone-700">Yönetici Şifresi</label>
            </div>
            <input type="password" id="password" name="password" required autocomplete="current-password"
                   placeholder="••••••••••••"
                   class="w-full px-4 py-3 rounded-xl border border-stone-300 text-stone-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
          </div>

          <div class="pt-2">
            <button type="submit" 
                    class="w-full py-3.5 px-4 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-semibold text-sm shadow-md hover:shadow-lg transition cursor-pointer flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              <span>Güvenli Giriş Yap</span>
            </button>
          </div>
        </form>

        <div class="pt-4 border-t border-stone-200/80 text-center">
          <p class="text-[11px] text-stone-500 leading-relaxed">
            Bu portal yalnızca Aile Danışmanı Hülya Tuncer'in yetkili erişimine açıktır. Mahkeme vaka notları ve danışan bilgileri mesleki etik ve gizlilik ilkeleri kapsamında korunur.
          </p>
        </div>

      </div>

      <div class="text-center mt-6">
        <a href="/demo/" class="text-xs text-stone-500 hover:text-brand-700 underline underline-offset-4 transition">
          &larr; Web Sitesi Demosuna Dön
        </a>
      </div>
    </div>

  </div>

<?php else: ?>
  <!-- ========================================== -->
  <!-- 2. YÖNETİCİ KONTROL PANELİ (DASHBOARD)     -->
  <!-- ========================================== -->
  <div class="min-h-full flex flex-col">
    
    <!-- Üst Bar -->
    <header class="bg-white border-b border-stone-200/90 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
          
          <div class="flex items-center gap-3">
            <img src="/assets/img/master_symbol_square.png?v=4" alt="Hülya Tuncer" class="w-10 h-10 rounded-xl object-cover ring-1 ring-emerald-500/20 shadow-xs">
            <div>
              <div class="flex items-center gap-2">
                <span class="text-lg font-serif font-bold text-stone-900 leading-none">Hülya Tuncer</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-200">Admin Portalı</span>
              </div>
              <span class="text-[11px] text-stone-500 tracking-wide block mt-0.5">Mahkeme &amp; Vaka Arşivi • Gizli Dosyalar</span>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <a href="/demo/" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-stone-200 text-xs font-medium text-stone-600 hover:bg-stone-50 transition">
              <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
              <span>Site Demosunu Gör</span>
            </a>
            <a href="/admin/?action=logout" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-xs font-semibold text-stone-700 border border-stone-200 transition">
              <svg class="w-3.5 h-3.5 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
              <span>Güvenli Çıkış</span>
            </a>
          </div>

        </div>
      </div>

      <!-- Sekme Menüsü -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-stone-100">
        <nav class="flex gap-2 -mb-px">
          <button id="tabBtnVakalar" onclick="switchTab('vakalar')" 
                  class="tab-btn px-4 py-3 text-xs sm:text-sm font-semibold border-b-2 border-brand-500 text-brand-700 flex items-center gap-2 transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span>Mahkeme &amp; Vaka Dosyaları</span>
            <span id="badgeVakalarCount" class="px-2 py-0.5 rounded-full text-[10px] bg-brand-100 text-brand-800 font-bold">0</span>
          </button>

          <button id="tabBtnTalepler" onclick="switchTab('talepler')" 
                  class="tab-btn px-4 py-3 text-xs sm:text-sm font-semibold border-b-2 border-transparent text-stone-500 hover:text-stone-800 flex items-center gap-2 transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Ön Görüşme Talepleri</span>
            <span id="badgeTaleplerCount" class="px-2 py-0.5 rounded-full text-[10px] bg-stone-200 text-stone-700 font-bold">0</span>
          </button>

          <button id="tabBtnAyarlar" onclick="switchTab('ayarlar')" 
                  class="tab-btn px-4 py-3 text-xs sm:text-sm font-semibold border-b-2 border-transparent text-stone-500 hover:text-stone-800 flex items-center gap-2 transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Güvenlik &amp; Şifre</span>
          </button>
        </nav>
      </div>
    </header>

    <!-- Ana İçerik -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full">
      
      <!-- ============================================== -->
      <!-- SEKME 1: MAHKEME & VAKA DOSYALARI (PRIVATE)    -->
      <!-- ============================================== -->
      <section id="tabVakalar" class="space-y-6">
        
        <!-- Üst Başlık & Aksiyon Çubuğu -->
        <div class="bg-white p-6 rounded-3xl border border-stone-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div class="space-y-1">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold">
              <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
              <span>Gizli Adli &amp; Klinik Vaka Arşivi</span>
            </div>
            <h1 class="text-2xl font-serif font-bold text-stone-900">Mahkeme Hikayeleri &amp; Vaka Değerlendirmeleri</h1>
            <p class="text-xs text-stone-500 max-w-2xl">
              Aile mahkemesi süreçleri, velayet, çekişmeli boşanma, ebeveyn yabancılaştırma ve bilirkişilik gözlemlerine dair danışmanlık notlarınız. Bu veriler yalnızca sizin oturumunuza açıktır.
            </p>
          </div>

          <div class="flex items-center gap-3">
            <button onclick="openVakaModal()" 
                    class="px-5 py-3 rounded-2xl bg-brand-500 hover:bg-brand-600 text-white font-semibold text-xs sm:text-sm shadow-md hover:shadow-lg transition flex items-center gap-2 cursor-pointer shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
              <span>Yeni Vaka / Not Ekle</span>
            </button>
          </div>
        </div>

        <!-- Filtreleme ve Arama -->
        <div class="flex flex-col sm:flex-row gap-3">
          <div class="relative flex-1">
            <input type="text" id="vakaSearchInput" oninput="renderVakalar()" placeholder="Başlık, mahkeme türü veya vaka içeriğinde ara..." 
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white border border-stone-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            <svg class="w-4 h-4 text-stone-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </div>
          <select id="vakaCategoryFilter" onchange="renderVakalar()" class="px-3.5 py-2.5 rounded-xl bg-white border border-stone-300 text-xs sm:text-sm text-stone-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
            <option value="ALL">Tüm Kategoriler</option>
            <option value="Mahkeme & Velayet">Mahkeme &amp; Velayet</option>
            <option value="Boşanma & İletişim">Boşanma &amp; İletişim</option>
            <option value="Bilirkişilik & Gözlem">Bilirkişilik &amp; Gözlem</option>
            <option value="Sistemik Aile">Sistemik Aile</option>
          </select>
        </div>

        <!-- Vaka Kartları Listesi -->
        <div id="vakalarContainer" class="space-y-4">
          <!-- Dinamik JavaScript ile doldurulur -->
          <div class="p-8 text-center bg-white rounded-3xl border border-stone-200 text-stone-400 text-xs">
            Vaka kayıtları yükleniyor...
          </div>
        </div>

      </section>

      <!-- ============================================== -->
      <!-- SEKME 2: ÖN GÖRÜŞME TALEPLERİ (LEADS)          -->
      <!-- ============================================== -->
      <section id="tabTalepler" class="hidden space-y-6">
        
        <div class="bg-white p-6 rounded-3xl border border-stone-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div class="space-y-1">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-800 border border-blue-200 text-xs font-semibold">
              <span class="w-2 h-2 rounded-full bg-blue-600"></span>
              <span>Web Sitesi Ön Görüşme Başvuruları</span>
            </div>
            <h2 class="text-2xl font-serif font-bold text-stone-900">Gelen Danışan Talepleri</h2>
            <p class="text-xs text-stone-500">
              Web sitesindeki 15 dakikalık ücretsiz online ön görüşme formu üzerinden iletilen başvurular.
            </p>
          </div>

          <button onclick="loadTalepler()" class="px-4 py-2 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-semibold border border-stone-200 transition flex items-center gap-1.5 cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span>Listeyi Yenile</span>
          </button>
        </div>

        <div id="taleplerContainer" class="space-y-4">
          <div class="p-8 text-center bg-white rounded-3xl border border-stone-200 text-stone-400 text-xs">
            Talepler yükleniyor...
          </div>
        </div>

      </section>

      <!-- ============================================== -->
      <!-- SEKME 3: HESAP VE GÜVENLİK AYARLARI            -->
      <!-- ============================================== -->
      <section id="tabAyarlar" class="hidden max-w-2xl mx-auto space-y-6">
        
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200/80 shadow-xs space-y-6">
          <div>
            <h2 class="text-2xl font-serif font-bold text-stone-900">Yönetici Şifresini Değiştir</h2>
            <p class="text-xs text-stone-500 mt-1">
              Admin portalına ve vaka arşivine erişim sağlayan ana şifrenizi güncelleyebilirsiniz.
            </p>
          </div>

          <form id="changePasswordForm" onsubmit="handlePasswordChange(event)" class="space-y-4">
            <div>
              <label for="currentPassword" class="block text-xs font-semibold text-stone-700 mb-1">Mevcut Şifre</label>
              <input type="password" id="currentPassword" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
              <label for="newPassword" class="block text-xs font-semibold text-stone-700 mb-1">Yeni Şifre (En az 8 karakter)</label>
              <input type="password" id="newPassword" minlength="8" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
              <label for="newPasswordConfirm" class="block text-xs font-semibold text-stone-700 mb-1">Yeni Şifre Tekrar</label>
              <input type="password" id="newPasswordConfirm" minlength="8" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div id="passwordAlert" class="hidden p-3 rounded-xl text-xs"></div>

            <div class="pt-2">
              <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-semibold text-xs sm:text-sm shadow transition cursor-pointer">
                Şifreyi Güncelle
              </button>
            </div>
          </form>
        </div>

        <div class="bg-stone-50 p-6 rounded-3xl border border-stone-200/80 text-xs text-stone-600 space-y-2">
          <div class="font-bold text-stone-800">Veri Güvenliği Notu:</div>
          <p>
            Vaka notları ve ön görüşme kayıtları sunucuda <code>data/</code> dizininde saklanmakta olup, hem Nginx hem de Apache seviyesinde dış dünyaya tamamen kapatılmıştır. Verilere yalnızca bu oturum üzerinden erişilebilir.
          </p>
        </div>

      </section>

    </main>

  </div>

  <!-- ============================================== -->
  <!-- MODAL: YENİ / DÜZENLE VAKA & MAHKEME DOSYASI   -->
  <!-- ============================================== -->
  <div id="vakaModal" class="fixed inset-0 z-50 bg-stone-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 opacity-0 pointer-events-none transition-opacity duration-300 overflow-y-auto">
    <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-stone-200 p-6 sm:p-8 my-auto transform scale-95 transition-transform duration-300" id="vakaModalCard">
      
      <div class="flex items-center justify-between pb-4 border-b border-stone-200">
        <div>
          <span class="text-xs uppercase tracking-wider text-brand-600 font-semibold block">Adli &amp; Sistemik Dosya</span>
          <h3 id="vakaModalTitle" class="text-xl font-serif font-bold text-stone-900">Yeni Vaka / Mahkeme Notu Ekle</h3>
        </div>
        <button onclick="closeVakaModal()" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 flex items-center justify-center text-stone-500 cursor-pointer">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <form id="vakaForm" onsubmit="handleVakaSubmit(event)" class="space-y-4 pt-4">
        <input type="hidden" id="vakaId" name="id" value="">

        <div>
          <label for="vTitle" class="block text-xs font-semibold text-stone-700 mb-1">Vaka / Mahkeme Konusu Başlığı *</label>
          <input type="text" id="vTitle" required placeholder="Örn: Aile Mahkemesi Velayet Sürecinde PAS Dinamikleri"
                 class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label for="vCategory" class="block text-xs font-semibold text-stone-700 mb-1">Kategori</label>
            <select id="vCategory" class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
              <option value="Mahkeme & Velayet">Mahkeme &amp; Velayet</option>
              <option value="Boşanma & İletişim">Boşanma &amp; İletişim</option>
              <option value="Bilirkişilik & Gözlem">Bilirkişilik &amp; Gözlem</option>
              <option value="Sistemik Aile">Sistemik Aile</option>
            </select>
          </div>

          <div>
            <label for="vCourtType" class="block text-xs font-semibold text-stone-700 mb-1">Adli Boyut / Mahkeme Türü</label>
            <input type="text" id="vCourtType" placeholder="Örn: 2. Aile Mahkemesi / Çekişmeli Boşanma"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
          </div>
        </div>

        <div>
          <label for="vSummary" class="block text-xs font-semibold text-stone-700 mb-1">Anonim Vaka Hikayesi &amp; Çatışma Özeti</label>
          <textarea id="vSummary" rows="3" placeholder="Aile yapısı, çocukların yaşları, ebeveynler arasındaki temel çatışma noktası..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
        </div>

        <div>
          <label for="vClinical" class="block text-xs font-semibold text-stone-700 mb-1">Klinik Değerlendirme &amp; Sistemik Gözlem</label>
          <textarea id="vClinical" rows="3" placeholder="Görüşmelerde öne çıkan ebeveyn tutumları, çocuğun psikolojik direnci, sınır problemleri..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
        </div>

        <div>
          <label for="vRecommendations" class="block text-xs font-semibold text-stone-700 mb-1">Tavsiye Edilen Ebeveynlik Protokolü &amp; Yol Haritası</label>
          <textarea id="vRecommendations" rows="2" placeholder="Görüş günleri, iletişim kuralları, ebeveynler arası anlaşma adımları..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label for="vConfidentiality" class="block text-xs font-semibold text-stone-700 mb-1">Gizlilik Derecesi</label>
            <select id="vConfidentiality" class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
              <option value="Yüksek Gizlilik / Adli Vaka Notu">Yüksek Gizlilik / Adli Vaka Notu</option>
              <option value="Danışmanlık İçi Dosya">Danışmanlık İçi Dosya</option>
              <option value="Süpervizyon & Araştırma">Süpervizyon &amp; Araştırma</option>
            </select>
          </div>

          <div>
            <label for="vStatus" class="block text-xs font-semibold text-stone-700 mb-1">Durum</label>
            <select id="vStatus" class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
              <option value="Devam Ediyor">Devam Ediyor</option>
              <option value="Tamamlandı">Tamamlandı</option>
              <option value="Arşiv">Arşiv</option>
            </select>
          </div>
        </div>

        <div class="pt-3 border-t border-stone-200 flex justify-end gap-2.5">
          <button type="button" onclick="closeVakaModal()" class="px-5 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 font-semibold text-xs sm:text-sm transition cursor-pointer">
            İptal
          </button>
          <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-semibold text-xs sm:text-sm shadow transition cursor-pointer">
            Kaydet
          </button>
        </div>

      </form>
    </div>
  </div>

  <!-- JAVASCRIPT PANEL MANTIĞI -->
  <script>
    let globalVakalar = [];
    let globalTalepler = [];

    // Sekme Değiştirici
    function switchTab(tabId) {
      const tabs = ['vakalar', 'talepler', 'ayarlar'];
      tabs.forEach(t => {
        const el = document.getElementById('tab' + t.charAt(0).toUpperCase() + t.slice(1));
        const btn = document.getElementById('tabBtn' + t.charAt(0).toUpperCase() + t.slice(1));
        if (t === tabId) {
          el.classList.remove('hidden');
          btn.classList.add('border-brand-500', 'text-brand-700');
          btn.classList.remove('border-transparent', 'text-stone-500');
        } else {
          el.classList.add('hidden');
          btn.classList.remove('border-brand-500', 'text-brand-700');
          btn.classList.add('border-transparent', 'text-stone-500');
        }
      });

      if (tabId === 'talepler' && globalTalepler.length === 0) {
        loadTalepler();
      }
    }

    // 1. VAKALARI YÜKLE
    async function loadVakalar() {
      try {
        const res = await fetch('/admin/api.php?action=get_vakalar');
        const data = await res.json();
        if (data.success) {
          globalVakalar = data.vakalar || [];
          document.getElementById('badgeVakalarCount').textContent = globalVakalar.length;
          renderVakalar();
        }
      } catch (err) {
        console.error('Vakalar yüklenemedi:', err);
      }
    }

    function renderVakalar() {
      const container = document.getElementById('vakalarContainer');
      const search = (document.getElementById('vakaSearchInput').value || '').toLowerCase();
      const cat = document.getElementById('vakaCategoryFilter').value;

      const filtered = globalVakalar.filter(v => {
        const matchSearch = (v.title + ' ' + (v.courtType || '') + ' ' + (v.summary || '') + ' ' + (v.clinicalObservation || '')).toLowerCase().includes(search);
        const matchCat = cat === 'ALL' || v.category === cat;
        return matchSearch && matchCat;
      });

      if (filtered.length === 0) {
        container.innerHTML = `
          <div class="p-12 text-center bg-white rounded-3xl border border-stone-200">
            <div class="w-12 h-12 rounded-full bg-stone-100 mx-auto flex items-center justify-center text-stone-400 mb-3">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-sm font-semibold text-stone-700">Kayıt Bulunamadı</p>
            <p class="text-xs text-stone-400 mt-1">Arama kriterlerine uygun vaka notu yok veya henüz eklenmedi.</p>
          </div>
        `;
        return;
      }

      container.innerHTML = filtered.map(v => `
        <div class="bg-white rounded-3xl border border-stone-200/90 p-6 shadow-xs hover:shadow-md transition space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-stone-100">
            <div class="flex items-center gap-2">
              <span class="text-xs font-mono font-bold text-stone-400 bg-stone-100 px-2 py-0.5 rounded-md">${v.id}</span>
              <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">${v.category || 'Mahkeme & Velayet'}</span>
              ${v.courtType ? `<span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-900 border border-amber-200">${v.courtType}</span>` : ''}
              <span class="text-[11px] font-semibold text-stone-500">${v.status || 'Devam Ediyor'}</span>
            </div>
            <div class="flex items-center gap-2">
              <button onclick="editVaka('${v.id}')" class="px-3 py-1 rounded-lg text-xs font-semibold bg-stone-100 hover:bg-stone-200 text-stone-700 transition cursor-pointer">Düzenle</button>
              <button onclick="deleteVaka('${v.id}')" class="px-3 py-1 rounded-lg text-xs font-semibold bg-red-50 hover:bg-red-100 text-red-700 transition cursor-pointer">Sil</button>
            </div>
          </div>

          <div>
            <h4 class="text-lg font-serif font-bold text-stone-900 leading-snug">${v.title}</h4>
            <p class="text-xs text-stone-400 mt-0.5">Kayıt Tarihi: ${v.createdAt ? new Date(v.createdAt).toLocaleDateString('tr-TR') : '-'}</p>
          </div>

          ${v.summary ? `
            <div class="bg-stone-50 p-4 rounded-2xl border border-stone-200/70 text-xs text-stone-700 space-y-1">
              <div class="font-bold text-stone-900">Anonim Vaka &amp; Çatışma Özeti:</div>
              <p class="leading-relaxed whitespace-pre-line">${v.summary}</p>
            </div>
          ` : ''}

          ${v.clinicalObservation ? `
            <div class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-200/60 text-xs text-stone-700 space-y-1">
              <div class="font-bold text-emerald-900">Klinik Değerlendirme &amp; Sistemik Gözlem:</div>
              <p class="leading-relaxed whitespace-pre-line">${v.clinicalObservation}</p>
            </div>
          ` : ''}

          ${v.recommendations ? `
            <div class="bg-amber-50/40 p-4 rounded-2xl border border-amber-200/50 text-xs text-stone-700 space-y-1">
              <div class="font-bold text-amber-950">Önerilen Ebeveynlik Protokolü &amp; Yol Haritası:</div>
              <p class="leading-relaxed whitespace-pre-line">${v.recommendations}</p>
            </div>
          ` : ''}

          <div class="pt-2 flex items-center justify-between text-[11px] text-stone-400">
            <span>Gizlilik: <strong class="text-stone-600">${v.confidentiality || 'Yüksek Gizlilik'}</strong></span>
            <span>Son Güncelleme: ${v.updatedAt ? new Date(v.updatedAt).toLocaleDateString('tr-TR') : '-'}</span>
          </div>
        </div>
      `).join('');
    }

    // Modal Yönetimi
    function openVakaModal(id = null) {
      const modal = document.getElementById('vakaModal');
      const card = document.getElementById('vakaModalCard');
      const form = document.getElementById('vakaForm');
      form.reset();

      if (id) {
        const v = globalVakalar.find(item => item.id === id);
        if (v) {
          document.getElementById('vakaModalTitle').textContent = 'Vaka Dosyasını Düzenle';
          document.getElementById('vakaId').value = v.id;
          document.getElementById('vTitle').value = v.title || '';
          document.getElementById('vCategory').value = v.category || 'Mahkeme & Velayet';
          document.getElementById('vCourtType').value = v.courtType || '';
          document.getElementById('vSummary').value = v.summary || '';
          document.getElementById('vClinical').value = v.clinicalObservation || '';
          document.getElementById('vRecommendations').value = v.recommendations || '';
          document.getElementById('vConfidentiality').value = v.confidentiality || 'Yüksek Gizlilik / Adli Vaka Notu';
          document.getElementById('vStatus').value = v.status || 'Devam Ediyor';
        }
      } else {
        document.getElementById('vakaModalTitle').textContent = 'Yeni Vaka / Mahkeme Notu Ekle';
        document.getElementById('vakaId').value = '';
      }

      modal.classList.remove('opacity-0', 'pointer-events-none');
      card.classList.remove('scale-95');
      card.classList.add('scale-100');
    }

    function closeVakaModal() {
      const modal = document.getElementById('vakaModal');
      const card = document.getElementById('vakaModalCard');
      modal.classList.add('opacity-0', 'pointer-events-none');
      card.classList.remove('scale-100');
      card.classList.add('scale-95');
    }

    function editVaka(id) {
      openVakaModal(id);
    }

    async function handleVakaSubmit(e) {
      e.preventDefault();
      const payload = {
        id: document.getElementById('vakaId').value,
        title: document.getElementById('vTitle').value,
        category: document.getElementById('vCategory').value,
        courtType: document.getElementById('vCourtType').value,
        summary: document.getElementById('vSummary').value,
        clinicalObservation: document.getElementById('vClinical').value,
        recommendations: document.getElementById('vRecommendations').value,
        confidentiality: document.getElementById('vConfidentiality').value,
        status: document.getElementById('vStatus').value
      };

      try {
        const res = await fetch('/admin/api.php?action=save_vaka', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
          closeVakaModal();
          loadVakalar();
        } else {
          alert(data.error || 'Kaydedilemedi');
        }
      } catch (err) {
        alert('Sunucu bağlantı hatası');
      }
    }

    async function deleteVaka(id) {
      if (!confirm('Bu vaka dosyasını silmek istediğinize emin misiniz?')) return;
      try {
        const res = await fetch('/admin/api.php?action=delete_vaka', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        });
        const data = await res.json();
        if (data.success) {
          loadVakalar();
        }
      } catch (err) {
        alert('Silme işlemi başarısız');
      }
    }

    // 2. TALEPLERİ YÜKLE
    async function loadTalepler() {
      try {
        const res = await fetch('/admin/api.php?action=get_talepler');
        const data = await res.json();
        if (data.success) {
          globalTalepler = data.talepler || [];
          document.getElementById('badgeTaleplerCount').textContent = globalTalepler.length;
          renderTalepler();
        }
      } catch (err) {
        console.error('Talepler yüklenemedi:', err);
      }
    }

    function renderTalepler() {
      const container = document.getElementById('taleplerContainer');
      if (globalTalepler.length === 0) {
        container.innerHTML = `
          <div class="p-12 text-center bg-white rounded-3xl border border-stone-200">
            <p class="text-sm font-semibold text-stone-700">Henüz Ön Görüşme Talebi Yok</p>
            <p class="text-xs text-stone-400 mt-1">Web sitesinden form doldurulduğunda talepler burada listelenecektir.</p>
          </div>
        `;
        return;
      }

      container.innerHTML = globalTalepler.map(t => `
        <div class="bg-white rounded-3xl border border-stone-200/90 p-6 shadow-xs space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-stone-100">
            <div class="flex items-center gap-2">
              <span class="text-xs font-mono font-bold text-stone-500 bg-stone-100 px-2 py-0.5 rounded-md">${t.refId || '-'}</span>
              <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full ${getStatusBadgeClass(t.status)}">${t.status || 'Yeni Talep'}</span>
              <span class="text-[11px] text-stone-400">${t.createdAt ? new Date(t.createdAt).toLocaleString('tr-TR') : '-'}</span>
            </div>
            <div class="flex items-center gap-2">
              <a href="https://wa.me/90${(t.phone || '').replace(/\D/g, '').replace(/^90/, '')}?text=Merhaba%20Sayın%20${encodeURIComponent(t.fullName || '')}%2C%20hulyatuncer.com.tr%20üzerinden%20oluşturduğunuz%20ön%20görüşme%20talebiniz%20için%20yazıyorum." 
                 target="_blank" 
                 class="px-3 py-1 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition flex items-center gap-1">
                <span>WhatsApp İletişim</span>
              </a>
              <button onclick="deleteTalep('${t.refId}')" class="px-3 py-1 rounded-lg text-xs font-semibold bg-stone-100 hover:bg-red-50 hover:text-red-700 text-stone-600 transition cursor-pointer">Sil</button>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="space-y-1">
              <span class="text-[11px] text-stone-400 uppercase tracking-wider block font-semibold">Danışan Bilgisi</span>
              <p class="text-sm font-bold text-stone-900">${t.fullName || '-'}</p>
              <p class="text-xs text-stone-600">${t.phone || '-'}</p>
              ${t.email ? `<p class="text-xs text-stone-500">${t.email}</p>` : ''}
            </div>

            <div class="space-y-1">
              <span class="text-[11px] text-stone-400 uppercase tracking-wider block font-semibold">Odak &amp; Katılım</span>
              <p class="text-xs font-semibold text-brand-700">${t.topic || 'Genel Görüşme'}</p>
              <p class="text-xs text-stone-600">${t.participants || 'Bireysel'}</p>
              <p class="text-xs text-stone-500">Zaman: ${t.preferredTime || 'En Erken'}</p>
            </div>

            <div class="space-y-2">
              <span class="text-[11px] text-stone-400 uppercase tracking-wider block font-semibold">Talep Durumu</span>
              <select onchange="updateTalepStatus('${t.refId}', this.value)" class="w-full px-3 py-1.5 rounded-xl border border-stone-300 text-xs text-stone-800 bg-white">
                <option value="Yeni Talep" ${(t.status === 'Yeni Talep' || !t.status) ? 'selected' : ''}>Yeni Talep</option>
                <option value="İletişime Geçildi" ${t.status === 'İletişime Geçildi' ? 'selected' : ''}>İletişime Geçildi</option>
                <option value="Seans Planlandı" ${t.status === 'Seans Planlandı' ? 'selected' : ''}>Seans Planlandı</option>
                <option value="Tamamlandı" ${t.status === 'Tamamlandı' ? 'selected' : ''}>Tamamlandı</option>
                <option value="Arşivlendi" ${t.status === 'Arşivlendi' ? 'selected' : ''}>Arşivlendi</option>
              </select>
            </div>
          </div>

          ${t.note ? `
            <div class="bg-stone-50 p-3 rounded-xl border border-stone-200/70 text-xs text-stone-700">
              <strong class="text-stone-900">Danışan Notu:</strong> ${t.note}
            </div>
          ` : ''}

          <div class="pt-2 border-t border-stone-100 flex items-center gap-2">
            <input type="text" placeholder="Özel danışman notu ekleyin..." value="${t.adminNote || ''}" 
                   onblur="updateTalepNote('${t.refId}', this.value)"
                   class="flex-1 px-3 py-1.5 text-xs rounded-xl bg-stone-50 border border-stone-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500">
          </div>
        </div>
      `).join('');
    }

    function getStatusBadgeClass(status) {
      switch (status) {
        case 'İletişime Geçildi': return 'bg-amber-100 text-amber-900 border border-amber-300';
        case 'Seans Planlandı': return 'bg-emerald-100 text-emerald-900 border border-emerald-300';
        case 'Tamamlandı': return 'bg-blue-100 text-blue-900 border border-blue-300';
        case 'Arşivlendi': return 'bg-stone-200 text-stone-700 border border-stone-300';
        default: return 'bg-rose-100 text-rose-900 border border-rose-300';
      }
    }

    async function updateTalepStatus(refId, newStatus) {
      try {
        await fetch('/admin/api.php?action=update_talep', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ refId, status: newStatus })
        });
        loadTalepler();
      } catch (err) {
        alert('Durum güncellenemedi');
      }
    }

    async function updateTalepNote(refId, note) {
      try {
        await fetch('/admin/api.php?action=update_talep', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ refId, adminNote: note })
        });
      } catch (err) {}
    }

    async function deleteTalep(refId) {
      if (!confirm('Bu talebi silmek istediğinize emin misiniz?')) return;
      try {
        await fetch('/admin/api.php?action=delete_talep', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ refId })
        });
        loadTalepler();
      } catch (err) {
        alert('Talep silinemedi');
      }
    }

    // 3. ŞİFRE DEĞİŞTİRME
    async function handlePasswordChange(e) {
      e.preventDefault();
      const current = document.getElementById('currentPassword').value;
      const newPass = document.getElementById('newPassword').value;
      const confirmPass = document.getElementById('newPasswordConfirm').value;
      const alertBox = document.getElementById('passwordAlert');

      if (newPass !== confirmPass) {
        alertBox.className = 'p-3 rounded-xl text-xs bg-red-100 text-red-800 border border-red-200';
        alertBox.textContent = 'Yeni şifreler birbiriyle uyuşmuyor.';
        alertBox.classList.remove('hidden');
        return;
      }

      try {
        const res = await fetch('/admin/api.php?action=change_password', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ currentPassword: current, newPassword: newPass })
        });
        const data = await res.json();
        if (data.success) {
          alertBox.className = 'p-3 rounded-xl text-xs bg-emerald-100 text-emerald-800 border border-emerald-200';
          alertBox.textContent = data.message || 'Şifre güncellendi.';
          alertBox.classList.remove('hidden');
          document.getElementById('changePasswordForm').reset();
        } else {
          alertBox.className = 'p-3 rounded-xl text-xs bg-red-100 text-red-800 border border-red-200';
          alertBox.textContent = data.error || 'Şifre güncellenemedi.';
          alertBox.classList.remove('hidden');
        }
      } catch (err) {
        alertBox.className = 'p-3 rounded-xl text-xs bg-red-100 text-red-800 border border-red-200';
        alertBox.textContent = 'Bağlantı hatası.';
        alertBox.classList.remove('hidden');
      }
    }

    // Başlangıç Yüklemesi
    loadVakalar();
  </script>

<?php endif; ?>

</body>
</html>
