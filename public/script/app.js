// =========================
// 1) Confirmation suppression
// =========================
document.addEventListener("submit", (e) => {
  const form = e.target.closest(".js-delete-book-form");
  if (!form) return;

  const msg = form.getAttribute("data-confirm") || "Supprimer ?";
  if (!window.confirm(msg)) {
    e.preventDefault();
  }
});

// =========================
// 2) Recherche par titre
// =========================
document.addEventListener("input", (e) => {
  const input = e.target.closest(".js-book-search");
  if (!input) return;

  const q = input.value.trim().toLowerCase();
  const rows = document.querySelectorAll(".js-book-row");

  rows.forEach((row) => {
    const titleEl = row.querySelector(".js-book-title");
    const title = (titleEl?.textContent || "").toLowerCase();
    row.style.display = title.includes(q) ? "" : "none";
  });
});

// =========================
// 3) Preview image upload (form add)
// =========================
document.addEventListener("change", (e) => {
  const fileInput = e.target.closest('input[type="file"][name="image"]');
  if (!fileInput) return;

  const file = fileInput.files && fileInput.files[0];
  if (!file) return;

  // Vérif basique côté client
  if (!file.type.startsWith("image/")) {
    alert("Veuillez choisir une image.");
    fileInput.value = "";
    return;
  }

  // Cherche/Crée un élément preview juste après l'input
  let preview = fileInput.parentElement.querySelector(".js-image-preview");
  if (!preview) {
    preview = document.createElement("img");
    preview.className = "js-image-preview mt-2";
    preview.style.width = "70px";
    preview.style.height = "70px";
    preview.style.objectFit = "cover";
    preview.style.borderRadius = "10px";
    preview.style.border = "1px solid rgba(0,0,0,0.1)";
    fileInput.parentElement.appendChild(preview);
  }

  // Affiche l'image
  preview.src = URL.createObjectURL(file);
});