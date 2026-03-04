import { Router } from 'express';
import pool from '../lib/db';

const router = Router();

router.get('/', async (req, res) => {
  const q = String(req.query.q || '').trim();
  try {
    const [rows] = await pool.query(`SELECT id, titulo, autor, ano, disponivel FROM livros LIMIT 500`);
    let results = rows as any[];
    if (q) {
      const ql = q.toLowerCase();
      results = results.filter(r => (r.titulo + ' ' + (r.autor || '') + ' ' + (r.ano || '')).toLowerCase().includes(ql));
    }
    res.json({ ok: true, count: results.length, data: results });
  } catch (err) {
    console.error(err);
    res.json({ ok: false, error: 'DB error - see server logs' });
  }
});

export default router;
