import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import catalogRouter from './routes/catalog';
import emprestimosRouter from './routes/emprestimos';

dotenv.config();

const app = express();
app.use(cors());
app.use(express.json());

// basic error handler
app.use((err: any, _req: any, res: any, _next: any) => {
  console.error('Unhandled error in request pipeline:', err);
  res.status(500).json({ ok: false, error: 'Internal Server Error' });
});

app.get('/', (_req, res) => res.json({ ok: true, service: 'TCC-etec node-api' }));

app.use('/api/catalogo', catalogRouter);
app.use('/api/emprestimos', emprestimosRouter);

// basic error handler (after routes)
app.use((err: any, _req: any, res: any, _next: any) => {
  console.error('Unhandled error in request pipeline:', err);
  res.status(500).json({ ok: false, error: 'Internal Server Error' });
});

const port = Number(process.env.PORT || 4000);
app.listen(port, () => {
  // eslint-disable-next-line no-console
  console.log(`node-api listening on port ${port}`);
});
