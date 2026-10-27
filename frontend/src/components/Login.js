import { api, BASE_URL } from "../utils/api.js";

const form = document.getElementById("loginForm");
const usernameInput = document.getElementById("username");
const passwordInput = document.getElementById("password");
const rememberInput = document.getElementById("remember");
const message = document.getElementById("formMessage");
const submitButton = form.querySelector('button[type="submit"]');
const togglePassword = document.getElementById("togglePassword");

const showMessage = (text, isError = true) => {
  message.textContent = text;
  message.classList.remove("hidden", "bg-amber-50", "text-amber-800", "bg-rose-50", "text-rose-700", "bg-emerald-50", "text-emerald-700");
  message.classList.add(
    ...(isError ? ["bg-rose-50", "text-rose-700"] : ["bg-emerald-50", "text-emerald-700"]),
  );
};

const rememberedUsername = localStorage.getItem("sexto_login_username");
if (rememberedUsername) {
  usernameInput.value = rememberedUsername;
  rememberInput.checked = true;
  passwordInput.focus();
}

togglePassword.addEventListener("click", () => {
  const showing = passwordInput.type === "text";
  passwordInput.type = showing ? "password" : "text";
  togglePassword.setAttribute("aria-pressed", String(!showing));
  togglePassword.setAttribute("aria-label", showing ? "Mostrar contraseña" : "Ocultar contraseña");
});

form.addEventListener("submit", async (event) => {
  event.preventDefault();
  message.classList.add("hidden");

  const username = usernameInput.value.trim();
  const password = passwordInput.value;
  if (!username || !password) {
    showMessage("Ingresa tu usuario y contraseña.");
    return;
  }

  submitButton.disabled = true;
  submitButton.dataset.originalText = submitButton.textContent.trim();
  submitButton.textContent = "Verificando...";

  try {
    const result = await api.post("login", { username, password });
    if (!result.estado) {
      throw new Error(result.message || "No se pudo iniciar sesión.");
    }
    const role = String(result.usuario?.rol || "").trim().toLowerCase();
    const destination =
      role === "administrador"
        ? `${BASE_URL}/index.html`
        : role === "vendedor"
          ? `${BASE_URL}/indexVendedor.html`
          : null;
    if (!destination) {
      throw new Error("El usuario no tiene un rol válido asignado.");
    }

    if (rememberInput.checked) {
      localStorage.setItem("sexto_login_username", username);
    } else {
      localStorage.removeItem("sexto_login_username");
    }
    localStorage.setItem("sexto_usuario", JSON.stringify(result.usuario));

    showMessage("Inicio de sesión correcto. Ingresando al sistema…", false);
    window.setTimeout(() => window.location.assign(destination), 500);
  } catch (error) {
    showMessage(error.message || "No se pudo conectar con el servicio de inicio de sesión.");
  } finally {
    submitButton.disabled = false;
    submitButton.textContent = "Ingresar al sistema";
  }
});
