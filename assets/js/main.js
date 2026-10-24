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

  // 5. Online Ön Görüşme Formu Entegrasyonu
  function setupConsultationForm(formId, successCardId) {
    const form = document.getElementById(formId);
    const successCard = document.getElementById(successCardId);
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      const submitBtn = form.querySelector('button[type="submit"]');
      const submitBtnText = form.querySelector('#submitBtnText') || (submitBtn ? submitBtn.querySelector('span') : null);
      const originalText = submitBtnText ? submitBtnText.textContent : 'Ön Görüşme Talebini Gönder';

      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());

      if (!data.fullName || !data.phone) {
        alert('Lütfen adınızı ve telefon numaranızı eksiksiz giriniz.');
        return;
      }

      if (submitBtn) {
        submitBtn.disabled = true;
        if (submitBtnText) submitBtnText.textContent = 'Talebiniz Kaydediliyor...';
      }

      // Varsayılan Referans No
      let refId = 'HT-' + new Date().toISOString().slice(0, 10).replace(/-/g, '') + '-' + Math.floor(1000 + Math.random() * 9000);

      try {
        const response = await fetch('api/on-gorusme.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        });
        if (response.ok) {
          const resData = await response.json();
          if (resData && resData.success && resData.refId) {
            refId = resData.refId;
          }
        }
      } catch (err) {
        console.warn('API bağlantısı doğrudan kurulamadı, yerel yedek devreye alındı:', err);
      }

      // LocalStorage Yedekleme
      try {
        const existing = JSON.parse(localStorage.getItem('ht_on_gorusmeler') || '[]');
        existing.unshift({ refId, ...data, submittedAt: new Date().toISOString() });
        localStorage.setItem('ht_on_gorusmeler', JSON.stringify(existing));
      } catch (err) {
        // LocalStorage hata yutucu
      }

      // Formu gizle, başarı onay kartını göster
      form.classList.add('hidden');
      if (successCard) {
        successCard.classList.remove('hidden');

        const refEl = successCard.querySelector('#successRefId') || successCard.querySelector('.ref-id-display');
        if (refEl) refEl.textContent = refId;

        const msgClient = successCard.querySelector('#successClientMessage') || successCard.querySelector('.client-message-display');
        if (msgClient) {
          msgClient.textContent = `Sayın ${data.fullName}, talebiniz tarafımıza ulaştı. Hülya Tuncer en kısa sürede belirttiğiniz telefon (${data.phone}) üzerinden sizinle iletişime geçerek 15 dakikalık online görüşme saatinizi netleştirecektir.`;
        }

        // WhatsApp İsteğe Bağlı Hızlı İletişim Butonu (Sıfır emoji, sade ve saygılı dil)
        const waBtn = successCard.querySelector('#successWhatsAppBtn') || successCard.querySelector('.whatsapp-send-btn');
        if (waBtn) {
          let msg = `*Online Ön Görüşme Talebi (Ref: ${refId})*\n\n`;
          msg += `• Ad Soyad: ${data.fullName}\n`;
          msg += `• Telefon: ${data.phone}\n`;
          if (data.email) msg += `• E-Posta: ${data.email}\n`;
          if (data.topic) msg += `• Konu: ${data.topic}\n`;
          if (data.participants) msg += `• Katılım: ${data.participants}\n`;
          if (data.preferredTime) msg += `• Tercih Edilen Zaman: ${data.preferredTime}\n`;
          if (data.note) msg += `• Danışan Notu: ${data.note}\n`;
          msg += `\nhulyatuncer.com.tr web sitesi ön görüşme formu üzerinden iletilmiştir.`;

          waBtn.href = `https://wa.me/905055783259?text=${encodeURIComponent(msg)}`;
        }

        // Formu Yeniden Aç Butonu
        const resetBtn = successCard.querySelector('#resetFormBtn') || successCard.querySelector('.reset-form-btn');
        if (resetBtn) {
          resetBtn.onclick = () => {
            form.reset();
            successCard.classList.add('hidden');
            form.classList.remove('hidden');
            if (submitBtn) {
              submitBtn.disabled = false;
              if (submitBtnText) submitBtnText.textContent = originalText;
            }
          };
        }
      }
    });
  }

  // Demo sayfasındaki ana form
  setupConsultationForm('bookingForm', 'formSuccessCard');

  // Modal içerisindeki form (index.html ve genel modal)
  setupConsultationForm('modalBookingForm', 'modalSuccessCard');
});
