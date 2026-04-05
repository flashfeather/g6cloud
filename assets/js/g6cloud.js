/* ══════════════════════════════════════════════════════
   G6 CLOUD — JS COMPARTILHADO
   Funções: tema, hamburger, modal, formulário, calc, faq
══════════════════════════════════════════════════════ */

/* ── TEMA ── */
const themeBtn = document.getElementById('theme-toggle');
if (themeBtn) {
  themeBtn.addEventListener('click', () => {
    document.documentElement.classList.toggle('dark');
    localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
  });
}

/* ── HAMBURGER ── */
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobile-menu');
if (hamburger && mobileMenu) {
  hamburger.addEventListener('click', () => mobileMenu.classList.toggle('open'));
}

/* ── MODAL ── */
function openModal() {
  const modal = document.getElementById('contact-modal');
  if (modal) { modal.classList.add('open'); document.body.style.overflow = 'hidden'; }
}
function closeModal() {
  const modal = document.getElementById('contact-modal');
  if (modal) { modal.classList.remove('open'); document.body.style.overflow = ''; }
}
function handleModalClick(e) {
  if (e.target === document.getElementById('contact-modal')) closeModal();
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

/* ── FORMULÁRIO ── */
function showStatus(msg, type) {
  const el = document.getElementById('modal-status');
  if (!el) return;
  el.textContent = msg;
  el.className = 'status-msg show ' + type;
}

const modalForm = document.getElementById('contact-modal-form');
const submitBtn = document.getElementById('modal-submit');

if (modalForm) {
  modalForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(modalForm);
    const name    = (fd.get('name') || '').trim();
    const email   = (fd.get('email') || '').trim();
    const phone   = (fd.get('phone') || '').trim();
    const company = (fd.get('company') || '').trim();
    const message = (fd.get('message') || '').trim();

    if (!name || !email || !phone || !company || !message) {
      showStatus('❌ Por favor, preencha todos os campos obrigatórios.', 'error');
      return;
    }

    fd.append('subject', `Lead-${name.replace(/\s+/g, '')}`);
    if (submitBtn) submitBtn.disabled = true;
    showStatus('📤 Enviando mensagem...', 'loading');

    try {
      const res = await fetch(modalForm.action, { method: 'POST', body: fd });
      const data = await res.json();
      if (data.success) {
        showStatus('✅ Mensagem enviada! Em breve entraremos em contato.', 'success');
        modalForm.reset();
        setTimeout(closeModal, 3000);
      } else {
        showStatus('❌ Erro ao enviar. Tente novamente.', 'error');
      }
    } catch (err) {
      showStatus('❌ Erro de conexão. Verifique sua internet.', 'error');
    } finally {
      if (submitBtn) submitBtn.disabled = false;
    }
  });
}

/* ── CALCULADORA (só existe no index) ── */
function fmt(v) { return 'R$ ' + Math.round(v).toLocaleString('pt-BR'); }

function calcular() {
  const gastoEl = document.getElementById('gasto');
  const matEl   = document.getElementById('maturidade');
  const resEl   = document.getElementById('reservas');
  const instEl  = document.getElementById('instancias');
  if (!gastoEl) return;

  const gasto     = parseInt(gastoEl.value);
  const maturidade = parseFloat(matEl.value);
  const reservas  = parseFloat(resEl.value);

  document.getElementById('gastoVal').textContent = fmt(gasto);
  document.getElementById('instVal').textContent  = instEl.value;

  const taxa       = maturidade * reservas;
  const desperdicio = gasto * taxa;
  const anual      = desperdicio * 12;
  const payback    = Math.max(1, Math.round(gasto * 0.8 / desperdicio));

  document.getElementById('r-atual').textContent     = fmt(gasto);
  document.getElementById('r-desperdicio').textContent = fmt(desperdicio);
  document.getElementById('r-note').textContent      = `≈ ${Math.round(taxa * 100)}% do gasto atual`;
  document.getElementById('r-anual').textContent     = fmt(anual);
  document.getElementById('r-payback').textContent   = payback <= 1 ? '< 1 mês' : payback + ' meses';
}

if (document.getElementById('gasto')) calcular();

/* ── FAQ (FinOps) ── */
function toggleFaq(btn) {
  const answer = btn.nextElementSibling;
  const isOpen = btn.classList.contains('open');
  // Fecha todos
  document.querySelectorAll('.faq-q.open').forEach(b => {
    b.classList.remove('open');
    b.nextElementSibling.classList.remove('open');
  });
  // Abre o clicado (se estava fechado)
  if (!isOpen) {
    btn.classList.add('open');
    answer.classList.add('open');
  }
}
