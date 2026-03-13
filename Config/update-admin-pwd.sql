-- ============================================================
-- Atualizar Senha do Admin - TCC ETEC
-- Execução: mysql -u root -p tcc_etec < update-admin-pwd.sql
-- ============================================================

UPDATE usuarios SET 
  senha_hash = '$2y$12$kLOitn/i8ZPqJKIfAqG2GeLi1OEE2PN2PEo.my6m5acfc4vCA4xd6',
  atualizado_em = NOW()
WHERE email = 'admin@fetel.edu.br';

-- Verificar se atualizou
SELECT id, email, nome_completo, papel, ativo FROM usuarios WHERE email = 'admin@fetel.edu.br';

-- ============================================================
-- RESULTADO ESPERADO:
-- | id | email | nome_completo | papel | ativo |
-- |----|-------|---------------|-------|-------|
-- | 1  | admin@fetel.edu.br | Administrador | admin | 1 |
-- ============================================================
-- 
-- Agora faça login com:
--   Email: admin@fetel.edu.br
--   Senha: admin
--
-- ============================================================
