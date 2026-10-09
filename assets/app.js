function validatePasswordConfirmation(form) {
  if (form.elements.password.value !== form.elements.confirm_password.value) {
    alert('Passwords do not match.');
    return false;
  }
  return true;
}

// Add ingredient rows and handle removal for both initial and new rows.
const list = document.getElementById('ingredient-list');
if (list) {
  const template = document.getElementById('ingredient-template');
  document.getElementById('add-ingredient').addEventListener('click', () => {
    list.appendChild(document.importNode(template.content, true));
  });
  list.addEventListener('click', event => {
    if (event.target.closest('.remove-btn')) event.target.closest('.ingredient-row').remove();
  });
  list.addEventListener('input', e => {
    if (e.target.type === 'number' && parseFloat(e.target.value) < 0) e.target.value = '';
  });
  list.addEventListener('keydown', e => { if (e.target.type === 'number' && e.key === '-') e.preventDefault(); });
}
// Favorites without reload
document.querySelectorAll('.fav-btn').forEach(btn => btn.addEventListener('click', async () => {
  const res = await fetch('favorite.php', { method: 'POST', body: new URLSearchParams({ id: btn.dataset.id }) });
  const data = await res.json();
  btn.textContent = data.favorited ? 'Remove from favorites' : 'Save to favorites';
}));
// Yield multiplier
document.querySelectorAll('.mult').forEach(btn => btn.addEventListener('click', () => {
  const m = Number(btn.dataset.m);
  document.querySelectorAll('.amt').forEach(s => {
    s.textContent = parseFloat((Number(s.dataset.base) * m).toFixed(2));
  });
  document.querySelectorAll('.mult').forEach(b => b.classList.toggle('active', b === btn));
}));
