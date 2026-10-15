/**
 * Hülya Tuncer – hulyatuncer.com.tr
 * Ana Etkileşim ve Form Yönetim Betiği
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobil Menü Yönetimi
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  const mobileLinks = document.querySelectorAll('.mobile-nav-link');

  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });

    mobileLinks.forEach(link => {
      link.addEventListener('click', () => {
        mobileMenu.classList.add('hidden');
      });
    });
  }

  // 2. Navbar Scroll Efekti
  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 40) {
      navbar.classList.add('shadow-md');
    } else {
      navbar.classList.remove('shadow-md');
    }
  });

  // 3. SSS (Accordion) Etkileşimi
  const faqItems = document.querySelectorAll('.faq-item');
  faqItems.forEach(item => {
    const questionBtn = item.querySelector('.faq-question');
    if (questionBtn) {
      questionBtn.addEventListener('click', () => {
        const isActive = item.classList.contains('active');
        // Kardeş elemanları kapat
        faqItems.forEach(otherItem => otherItem.classList.remove('active'));
        if (!isActive) {
          item.classList.add('active');
        }
      });
    }
  });

  // 4. Terapötik Araçlar & Metodoloji Sekme Değiştirici
  const methodTabs = document.querySelectorAll('.method-tab-btn');
  const methodPanels = document.querySelectorAll('.method-panel');

  methodTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const target = tab.getAttribute('data-target');

      methodTabs.forEach(t => {
        t.classList.remove('bg-emerald-800', 'text-white', 'shadow-md');
        t.classList.add('bg-white', 'text-gray-700', 'border');
      });

      tab.classList.add('bg-emerald-800', 'text-white', 'shadow-md');
      tab.classList.remove('bg-white', 'text-gray-700', 'border');

      methodPanels.forEach(panel => {
        if (panel.id === target) {
          panel.classList.remove('hidden');
          panel.classList.add('block');
        } else {
          panel.classList.add('hidden');
          panel.classList.remove('block');
        }
      });
    });
  });

  // 5. WhatsApp Ön Kayıt & Randevu Formu Entegrasyonu
  const bookingForm = document.getElementById('bookingForm');
  const formSuccess = document.getElementById('formSuccess');

  if (bookingForm) {
    bookingForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const fullName = document.getElementById('fullName').value.trim();
      const phone = document.getElementById('phone').value.trim();
      const serviceType = document.getElementById('serviceType').value;
      const sessionFormat = document.getElementById('sessionFormat').value;
      const note = document.getElementById('note').value.trim();

      if (!fullName || !phone) {
        alert('Lütfen adınızı ve telefon numaranızı giriniz.');
        return;
      }

      // WhatsApp Mesaj Metni Hazırlama
      const phoneTarget = '905055783259';
      let message = `🌸 *hulyatuncer.com.tr Randevu & Danışmanlık Talebi*\n\n`;
      message += `👤 *Ad Soyad:* ${fullName}\n`;
      message += `📞 *Telefon:* ${phone}\n`;
      message += `🌿 *Hizmet:* ${serviceType}\n`;
      message += `💻 *Seans Tercihi:* ${sessionFormat}\n`;
      if (note) {
        message += `📝 *Danışan Notu:* ${note}\n`;
      }
      message += `\n_Hülya Tuncer Danışmanlık İletişim Hattı üzerinden iletilmiştir._`;

      const encodedMessage = encodeURIComponent(message);
      const whatsappUrl = `https://wa.me/${phoneTarget}?text=${encodedMessage}`;

      // Görsel Başarı Bildirimi
      if (formSuccess) {
        formSuccess.classList.remove('hidden');
      }

      // WhatsApp'a yönlendir (Yeni sekmede aç)
      setTimeout(() => {
        window.open(whatsappUrl, '_blank');
      }, 700);
    });
  }
});
