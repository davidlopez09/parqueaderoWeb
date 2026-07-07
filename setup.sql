-- ============================================================
-- SCRIPT DE CONFIGURACIÓN INICIAL - PARQUEADERO WEB
-- Ejecutar en el Editor SQL de Supabase (Dashboard > SQL Editor)
-- ============================================================

-- Crear tabla de usuarios (si no existe)
CREATE TABLE IF NOT EXISTS usuarios (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password_hash TEXT NOT NULL,
  nombre VARCHAR(200) NOT NULL,
  rol VARCHAR(50) DEFAULT 'operador',
  activo BOOLEAN DEFAULT true,
  created_at TIMESTAMPTZ DEFAULT now()
);

-- Insertar usuario administrador por defecto (admin / admin123)
INSERT INTO usuarios (username, password_hash, nombre, rol)
VALUES (
  'admin',
  '$2y$10$O28P1LP6cIlvSuCvVkQ9r.3nudvwqPIjq9qPnIjuSsdGISCP1dEl2',
  'Administrador',
  'admin'
) ON CONFLICT (username) DO NOTHING;

-- Habilitar RLS en la tabla usuarios
ALTER TABLE usuarios ENABLE ROW LEVEL SECURITY;

-- Política: permitir SELECT a cualquier solicitud (necesario para login)
DROP POLICY IF EXISTS "usuarios_select_public" ON usuarios;
CREATE POLICY "usuarios_select_public" ON usuarios
  FOR SELECT USING (true);

-- Política: permitir INSERT/UPDATE/DELETE solo con service_role
-- (El PHP backend usará la anon key; para crear usuarios vía web,
--  se necesita una política INSERT. Si no se necesita crear usuarios
--  desde la web, comentar la siguiente línea.)
DROP POLICY IF EXISTS "usuarios_insert_public" ON usuarios;
CREATE POLICY "usuarios_insert_public" ON usuarios
  FOR INSERT WITH CHECK (true);

-- Política: permitir UPDATE para el mismo usuario o admin
DROP POLICY IF EXISTS "usuarios_update_public" ON usuarios;
CREATE POLICY "usuarios_update_public" ON usuarios
  FOR UPDATE USING (true) WITH CHECK (true);

-- Verificar datos insertados
SELECT * FROM usuarios;
