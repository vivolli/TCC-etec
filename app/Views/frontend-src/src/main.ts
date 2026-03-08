import './styles.css';

const el = document.getElementById('app');
if (el) {
  el.innerHTML = `
    <main class="p-6">
      <h1 class="text-2xl font-bold">FETEL — Portal</h1>
      <p class="mt-2 text-gray-700">Front-end em TypeScript + Tailwind (build para app/public/frontend)</p>
    </main>
  `;
}
