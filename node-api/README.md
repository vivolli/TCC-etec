Node API scaffold for TCC-etec

Overview
--------
This small Node + TypeScript API is a scaffold to modernize parts of the FETEL/TCC-etec site.
It exposes two routes used by the frontend: `/api/catalogo` and `/api/emprestimos`.

Quick start (development)
-------------------------
Requirements: Node.js (16+), npm

1. Open a terminal and go to the `node-api` folder:

```powershell
cd D:\wamp64\www\TCC-etec\node-api
```

2. Install dependencies:

```powershell
npm install
```

3. Copy `.env.example` to `.env` and set DB credentials (optional):

```powershell
copy .env.example .env
# then edit .env
```

4. Run in dev mode (ts-node-dev):

```powershell
npm run dev
```

What it provides
----------------
- `/api/catalogo?q=...` — returns JSON list of books (reads `livros` table).
- `/api/emprestimos?usuario_id=...` — returns loans for `usuario_id`.
- `POST /api/emprestimos` — create a loan (body: usuario_id, livro_id).

Notes about integration
-----------------------
- This scaffold expects a MySQL database reachable with the provided credentials. If you do not have a DB yet, the API will error; in that case keep using existing PHP fallbacks or populate the DB.
- Authentication: the current scaffold assumes the frontend will send `usuario_id` for emprestimos. For production you must integrate proper auth (JWT or session validation).

Next steps I can do for you
-------------------------
- Add JWT generation in PHP and validation in Node (so Node can trust requests).
- Create SQL migrations for `livros` and `emprestimos` tables and seeds.
- Add automated tests and a small frontend module that consumes the API.
