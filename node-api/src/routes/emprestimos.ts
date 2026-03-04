import { Router } from 'express';
import pool from '../lib/db';

const router = Router();

// GET /api/emprestimos?usuario_id=...
router.get('/', async (req, res) => {
  const usuario_id = Number(req.query.usuario_id || 0);
  if (!usuario_id) return res.json({ ok: false, error: 'usuario_id required' });
  try {
    const [rows] = await pool.query(
      `SELECT e.id, e.livro_id, l.titulo, e.data_solicitacao, e.status FROM emprestimos e LEFT JOIN livros l ON l.id = e.livro_id WHERE e.usuario_id = ? ORDER BY e.data_solicitacao DESC`,
      [usuario_id]
    );
    res.json({ ok: true, data: rows });
  } catch (err) {
    console.error(err);
    res.json({ ok: false, error: 'DB error' });
  }
});

// POST /api/emprestimos  { usuario_id, livro_id }
router.post('/', async (req, res) => {
  const usuario_id = Number(req.body.usuario_id || 0);
  const livro_id = Number(req.body.livro_id || 0);
  if (!usuario_id || !livro_id) return res.status(400).json({ ok: false, error: 'usuario_id and livro_id required' });
  try {
    const [result] = await pool.query(`INSERT INTO emprestimos (usuario_id, livro_id, data_solicitacao, status) VALUES (?, ?, NOW(), ?)`, [usuario_id, livro_id, 'solicitado']);
    res.json({ ok: true, insertId: (result as any).insertId });
  } catch (err) {
    console.error(err);
    res.json({ ok: false, error: 'DB error' });
  }
});

export default router;
