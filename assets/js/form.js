// Script para envio do formulário
const form = document.getElementById("contact-form");
const statusEl = document.getElementById("contact-status");
const submitBtn = document.getElementById("contact-submit");

form?.addEventListener("submit", async (e) => {
  e.preventDefault();

  const formData = new FormData(form);
  const name = (formData.get("name") || "").toString().trim();
  const email = (formData.get("email") || "").toString().trim();
  const message = (formData.get("message") || "").toString().trim();

  if (!name || !email || !message) {
    statusEl.textContent = "Por favor, preencha todos os campos.";
    return;
  }

  const subject = `Lead-${name.replace(/\s+/g, "")}`;
  formData.append("subject", subject);

  submitBtn.disabled = true;
  submitBtn.classList.add("opacity-60");
  statusEl.textContent = "Enviando...";

  try {
    const res = await fetch(form.action, {
      method: "POST",
      body: formData
    });
    const data = await res.json();
    statusEl.textContent =
      data.message || (data.success ? "Mensagem enviada." : "Erro ao enviar.");
    if (data.success) form.reset();
  } catch (err) {
    console.error(err);
    statusEl.textContent = "Erro de conexão. Tente novamente mais tarde.";
  } finally {
    submitBtn.disabled = false;
    submitBtn.classList.remove("opacity-60");
  }
});