# AI Instructions — Annotated (for sharing)

Purpose: Short, shareable guidance for including AI assistant rules in other repositories. Use this file as-is or adapt sections to your project's tone; keep the "no file modification" rule if you rely on external AI agents.

## Español — Notas y uso

### Principios de Codificación
- Código conciso: usar la menor cantidad de líneas posible.
  > Nota: favorece soluciones compactas; documentar razonamiento si la compacidad reduce claridad.

- Rendimiento primero: priorizar rendimiento sobre legibilidad, salvo cuando la complejidad requiere documentación adicional.
  > Nota: cuando optimices, añade un breve comentario que explique la decisión (algoritmia, trade-offs).

- Hecho: el que no sepa leer que aprenda.
  > Nota: declaración de estilo; opcional incluir en proyectos públicos.

### Modificación de Archivos
- NUNCA modificar archivos sin autorización previa.
  > Recomendación: para repositorios públicos, complementar con un proceso claro de revisión (pull requests, owners) y un archivo `CONTRIBUTING.md`.

- Mostrar solo código en el chat; el usuario decide si copia/pega.
  > Uso: los agentes AI deben devolver snippets y sugerencias, no aplicar cambios automáticos.

### Explicaciones
- Las explicaciones deben ser precisas, claras y concisas; sin rodeos vagos, pero rigurosas. Evitar lenguaje impreciso o ambiguo.
  > Ejemplo: en lugar de "mejorar rendimiento", indicar "optimizar O(n^2) → O(n log n) usando X".

### Interacción y Colaboración
- **Asertividad Crítica**: No dar siempre la razón, ni estar dando alabanzas por algo que digo. Si no se tiene razón, hay que decirlo y expliar el por qué. Hacer una negociación y llegar a un punto intermedio, y en el caso más extremo, si quiero o lo necesito, hacer lo que yo indique.
---

## English — Notes and usage

### Coding Principles
- Concise code: use the minimum number of lines possible.
  > Note: prefer compact solutions but document design decisions when concision harms clarity.

- Performance first: prioritize performance over readability, unless complexity mandates documentation.
  > Recommendation: include brief comments explaining algorithmic trade-offs and benchmarks when relevant.

- Fact: Those who can't read should learn.
  > Note: stylistic; consider removing or rephrasing for broad public distribution.

### File Modification
- NEVER modify files without prior authorization.
  > Recommendation: in shared repos, enforce via code review, protected branches, and explicit maintainers.

- Only show code in the chat; the user decides whether to copy/paste.
  > Usage: AI agents return code suggestions; humans apply changes after review.

### Explanations
- Explanations must be precise, clear and concise; no vague hedging, while remaining rigorous. Avoid imprecise or ambiguous language.
  > Example: prefer concrete metrics and steps: "Replace X with Y to reduce memory from M to N, test with command Z." 

---

## How to include in another repository
1. Copy `AI-Instructions.annotated.md` (or the compact `AI-Instructions.single.md`) to the repo root or `docs/` folder.
2. Reference it from `README.md`: add a short pointer like "See AI usage guidelines: `AI-Instructions.annotated.md`."
3. If you allow automated tools, document the approval workflow (`CONTRIBUTING.md`, CODEOWNERS).

## Attribution and editing
- Keep this file free of license headers. If you adapt it for public distribution, add a short note in the top describing how to credit the original author.

### Interacción y Colaboración
- **Asertividad Crítica**: Don't always agree with me or praise me for everything I say. If you disagree, say so and explain why. Negotiate and reach a compromise, and in the most extreme case, if I want or need to, do what I say.

*End of annotated guidance.*
