import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import react from '@vitejs/plugin-react'
import alias from '@rollup/plugin-alias'
import eslint from 'vite-plugin-eslint'

export default defineConfig({
  plugins: [
    react(),
    alias({
      entries: [{ find: '@', replacement: '/resources/js' }],
    }),
    eslint(),
    laravel({
      input: ['resources/js/app.tsx'],
      refresh: true,
    }),
  ],
})
