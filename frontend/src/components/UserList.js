import { api } from "../utils/api.js";

const renderEstado = (estado) => {
  const badge = {
    1: "bg-emerald-100 text-emerald-700",
    0: "bg-gray-100 text-gray-500",
    2: "bg-rose-100 text-rose-700",
  };
  const key = String(estado ?? "").toLowerCase();
  const cls = badge[key] || "bg-gray-100 text-gray-500";
  const label =
    estado === 1
      ? "Activo"
      : estado === 0
        ? "Inactivo"
        : estado === 2
          ? "Suspendido"
          : "Desconocido";
  return `<span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${cls}">${label}</span>`;
};

const handleView = (user) => {
  alert(`Ver usuario: ${user.username}`);
};

const handleDelete = async (id, row) => {
  if (!confirm("¿Está seguro de eliminar este usuario?")) return;
  try {
    await api.del(`users/${id}`);
    row.remove();
  } catch (error) {
    alert("Error al eliminar el usuario");
  }
};

const onClick = (e) => {
  const btn = e.target.closest("button[data-action]");
  if (!btn) return;
  const { action, id, username } = btn.dataset;
  if (action === "view") {
    handleView({ id, username });
  } else if (action === "delete") {
    handleDelete(id, btn.closest("tr"));
  }
};

const refreshUserList = async () => {
  const container = document.getElementById("userTableList");
  container.innerHTML =
    '<tr><td class="px-6 py-8 text-center text-gray-400" colspan="6">Cargando...</td></tr>';
  try {
    const users = await api.get("users");
    console.log("Usuarios obtenidos:", users);
    container.innerHTML = users.length
      ? users
          .map(
            (user) => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 text-sm text-gray-900">${user.id}</td>
                <td class="px-6 py-4 text-sm text-gray-900 font-medium">${user.username}</td>
                <td class="px-6 py-4 text-sm text-gray-600 capitalize">${user.rol || "vendedor"}</td>
                <td class="px-6 py-4">${renderEstado(user.estado)}</td>
                <td class="px-6 py-4">${user.email}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        <button data-action="view" data-id="${user.id}" data-username="${user.username}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors">Ver</button>
                        <button data-action="delete" data-id="${user.id}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-rose-600 bg-rose-50 hover:bg-rose-100 transition-colors">Eliminar</button>
                    </div>
                </td>
            </tr>
        `,
          )
          .join("")
      : '<tr><td class="px-6 py-8 text-center text-gray-400" colspan="6">No hay usuarios registrados</td></tr>';
  } catch (error) {
    console.error("Error al obtener la lista de usuarios:", error);
    container.innerHTML =
      '<tr><td class="px-6 py-8 text-center text-rose-500" colspan="6">Error al cargar los usuarios</td></tr>';
  }
};

export const getUserList = async () => {
  const container = document.getElementById("userTableList");
  if (!container) {
    console.error("No se encontró #userTableList en la vista de usuarios.");
    return;
  }
  container.addEventListener("click", onClick);
  const modal = document.getElementById("userModal");
  const form = document.getElementById("userForm");
  const message = document.getElementById("userFormMessage");
  const employeeSelect = document.getElementById("newEmployee");
  const openButton = document.getElementById("openUserModal");
  const closeButton = document.getElementById("closeUserModal");
  const cancelButton = document.getElementById("cancelUserModal");

  if (
    modal &&
    form &&
    message &&
    employeeSelect &&
    openButton &&
    closeButton &&
    cancelButton
  ) {
    const openModal = () => {
      message.textContent = "";
      message.classList.add("hidden");
      modal.classList.remove("hidden");
      modal.classList.add("flex");
      document.getElementById("newUsername").focus();
    };
    const closeModal = () => {
      modal.classList.add("hidden");
      modal.classList.remove("flex");
    };
    openButton.addEventListener("click", openModal);
    closeButton.addEventListener("click", closeModal);
    cancelButton.addEventListener("click", closeModal);
    modal.addEventListener("click", (event) => {
      if (event.target === modal) closeModal();
    });

    employeeSelect.disabled = true;
    api
      .get("empleados")
      .then((employees) => {
        employeeSelect.innerHTML =
          '<option value="">Seleccione un empleado</option>' +
          employees
            .map(
              (employee) =>
                `<option value="${employee.id}">${employee.nombre} ${employee.apellidos} (CI: ${employee.ci})</option>`,
            )
            .join("");
        employeeSelect.disabled = employees.length === 0;
        if (employees.length === 0) {
          employeeSelect.innerHTML =
            '<option value="">No hay empleados registrados</option>';
        }
      })
      .catch(() => {
        employeeSelect.innerHTML =
          '<option value="">No se pudieron cargar los empleados</option>';
      });

    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      const saveButton = document.getElementById("saveUser");
      const formData = new FormData(form);
      const data = {
        username: formData.get("username").trim(),
        email: formData.get("email").trim(),
        password_hash: formData.get("password"),
        rol: formData.get("rol"),
        cod_empleado: formData.get("cod_empleado"),
      };
      saveButton.disabled = true;
      saveButton.textContent = "Guardando...";
      message.classList.add("hidden");
      try {
        await api.post("users", data);
        form.reset();
        closeModal();
        await refreshUserList();
      } catch (error) {
        message.textContent = error.message || "No se pudo guardar el usuario.";
        message.classList.remove("hidden");
        message.classList.add("text-rose-600");
      } finally {
        saveButton.disabled = false;
        saveButton.textContent = "Guardar usuario";
      }
    });
  } else {
    const requiredModalIds = [
      "userModal",
      "userForm",
      "userFormMessage",
      "newEmployee",
      "openUserModal",
      "closeUserModal",
      "cancelUserModal",
    ];
    const missingIds = requiredModalIds.filter(
      (id) => !document.getElementById(id),
    );
    console.warn(
      `No se inicializó el modal. Faltan estos elementos en la vista: ${missingIds.join(", ")}`,
    );
  }
  await refreshUserList();
};
