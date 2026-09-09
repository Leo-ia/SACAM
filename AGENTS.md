# AGENTS.md — Reglas de trabajo para el repositorio SACAM

Instrucciones obligatorias para cualquier sesion de trabajo sobre este
repositorio. Aplican a cada peticion nueva del alumno solicitante.

## 1. Revisar el repositorio antes de cada peticion

Antes de comenzar a trabajar en cualquier solicitud se debe:

1. Sincronizar con el remoto:
   ```bash
   git fetch origin main
   ```
2. Comparar el historial local contra el remoto:
   ```bash
   git log HEAD..origin/main --oneline
   ```
3. Si existen commits nuevos (de companeros de equipo o del responsable), se
   debe revisar el diff:
   ```bash
   git diff HEAD..origin/main
   ```
4. Informar al usuario qué cambio llego del exterior y ajustar el trabajo
   para que sea compatible con esos cambios. Solo proceder tras eso.

Nunca trabajarse sobre una copia desactualizada del repositorio.

## 2. Mensajes de commit claros y descriptivos

Cada commit debe indicar con precisión qué se agrego o cambio. Convención:

Formato base:
`<Sprint N>: <accion> <que>` — p. ej. `Sprint 1: estructura del proyecto y base de datos (tarea 1)`

Reglas:

- Empieza por el sprint al que pertenece el trabajo (o `docs:` si es solo
  documentacion del avance).
- Usa verbos en infinitivo que describan el cambio concreto: `agrega`,
  `corrige`, `actualiza`, `refactoriza`, `elimina`.
- Mensiona el archivo o modulo afectado y, si aplica, la tarea del backlog
  (p. ej. `(tarea 3)`).
- Si un commit corrige algo de un commit anterior, indicarlo, p. ej.
  `Sprint 1: corrige el CHECK de motocicletas para la regla de permiso provisional`.
- No mensajes genericos tipo `cambios` o `fix`. Deben responder: qué se
  toco y por que.

Ademas:

- Revisar `git status` y `git diff` antes de cada commit; solo incluir
  archivos intencionados.
- No versionar secretos ni archivos subidos por usuarios (`uploads/` esta
  en `.gitignore`).

## 3. Contexto del proyecto

- Proyecto: SACAM (registro y verificacion de motocicletas, ESCOM/IPN).
- Materia: Analisis y Diseno de Sistemas Digitales 2027-1, metodologia Scrum.
- Aviso: el alumno es estudiante; el lenguaje responde a ese nivel y
  mantiene tono profesional sin errores ortograficos.
- Sin emojis en codigo, commits ni documentacion del repositorio.