Front-end scaffold (Vite + TypeScript + Tailwind)

How to build locally:

1. Open a terminal in `app/Views/frontend-src`
2. Run `npm install`
3. Run `npm run build` — output will be generated into `app/public/frontend` (files: main.js, style.css)
4. Start your local server (WAMP) and open the site; `app/public/index.php` and views include the built assets.

Notes:
- I created a minimal entry in `src/main.ts` and `src/styles.css`. You can replace with React/Vue if preferred.
- Tailwind requires `npm install` to produce the final CSS.
