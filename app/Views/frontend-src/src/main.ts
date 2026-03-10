import './styles.css';

const el = document.getElementById('app');
if (el) {
  el.innerHTML = `
    <main class="p-6">
      <h1 class="text-2xl font-bold">FETEL — Portal</h1>
      <p class="mt-2 text-gray-700">Front-end em TypeScript + Tailwind (build para app/public/frontend)</p>
    </main>
  `;
  // Entrypoint: pequenas interações e inicialização
  document.addEventListener('DOMContentLoaded', () => {
    // atualizar ano no rodapé
    const yearEl = document.getElementById('year');
    if (yearEl) yearEl.textContent = String(new Date().getFullYear());

    // aumentar suavidade do CTA: pulso sutil
    const cta = document.querySelector('.btn-primary');
    if (cta) {
      setInterval(() => {
        cta.animate([{ transform: 'translateY(0)' }, { transform: 'translateY(-4px)' }, { transform: 'translateY(0)' }], { duration: 4200, easing: 'ease-in-out' });
      }, 4200);
    }
  
    // Reveal-on-scroll for cards (uses IntersectionObserver)
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        const el = e.target as HTMLElement;
        if (e.isIntersecting) {
          el.classList.add('in-view');
          observer.unobserve(el);
        }
      });
    }, { threshold: 0.18 });

    document.querySelectorAll('.card-reveal').forEach((n) => observer.observe(n));

    // small parallax for hero blob based on scroll
    const blob = document.querySelector('.float-blob') as HTMLElement | null;
    if (blob) {
      window.addEventListener('scroll', () => {
        const y = Math.min(window.scrollY, 400);
        blob.style.transform = `translateY(${y * 0.06}px) rotate(${y * 0.01}deg)`;
      }, { passive: true });
    }

    // create micro-orbs inside hero for extra motion
    const hero = document.querySelector('.hero');
    if (hero) {
      const orbA = document.createElement('div'); orbA.className = 'orb orb--a';
      const orbB = document.createElement('div'); orbB.className = 'orb orb--b';
      hero.appendChild(orbA); hero.appendChild(orbB);
    }

    // GSAP timeline for subtle choreography (if GSAP available)
    try {
      // dynamic import so SSR/build doesn't fail if missing
      (async () => {
        const mod = await import('gsap');
        const g = (mod && (mod as any).default) ? (mod as any).default : mod;
        if (!g) return;
        const tl = g.timeline ? g.timeline({ defaults: { ease: 'power2.out' } }) : null;
        const title = document.querySelector('.gradient-text');
        const ribbon = document.querySelector('svg path');
        if (tl && title) tl.from(title, { y: 18, opacity: 0, duration: .9 });
        if (tl && ribbon) tl.from(ribbon, { y: 24, opacity: 0, duration: 1 }, '-=.6');
        // slight bobbing for orbs
        if (g.to) {
          g.to('.orb--a', { y: -18, repeat: -1, yoyo: true, duration: 8, ease: 'sine.inOut' });
          g.to('.orb--b', { y: 14, repeat: -1, yoyo: true, duration: 10, ease: 'sine.inOut', delay: .8 });
        }
      })();
    } catch (e) {
      // GSAP not available — graceful fallback
    }
  });
}
