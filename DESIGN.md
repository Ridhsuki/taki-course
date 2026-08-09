# Taki Course Design Direction

This document establishes project-specific UI/UX direction and principles for `taki-course`.

---

## 1. Upstream Provenance & Anti-Slop Integration

This repository adopts upstream Anti AI Slop rules for UI quality filtering:

* **Source Repository**: [`miqdadbadjuber/anti-slop`](https://github.com/miqdadbadjuber/anti-slop)
* **Upstream Commit SHA**: `6f0e364de45883a93c64c78ceab6987365171860`
* **Upstream License**: MIT License (preserved in [`docs/third-party/anti-slop-LICENSE.txt`](docs/third-party/anti-slop-LICENSE.txt))

### Role Distinction
* **`DESIGN.md`** (this file) supplies project-specific visual direction and intent.
* **`ANTISLOP.md`** serves as the specialist UI quality filter and craftsmanship gate.

### Scope & Precedence Rule
`ANTISLOP.md` is a UI quality filter applied strictly within the explicitly requested task scope. It must not independently expand a task into unrelated redesigns, features, themes, states, components, or infrastructure. Explicit user requirements, project business rules, security/accessibility requirements, `AGENTS.md`, and the approved task scope take precedence.

> [!NOTE]
> `ANTISLOP.md` is an unedited upstream snapshot. Do NOT manually modify `ANTISLOP.md` for project-specific rules. Future updates should intentionally re-sync from upstream and review the diff.

---

## 2. Core Project Principles

1. **Source of Truth Baseline**: Existing Blade views (`resources/views/`) and assets (`public/`, `resources/css/`) serve as the visual baseline and source of truth until an explicit redesign task is approved.
2. **Flexible Restyling**: Preserving the current baseline does not freeze existing layouts forever. Future frontend tasks may intentionally restyle, extract, or modernize UI layouts when explicitly requested.
3. **Craftsmanship & Accessibility**: Every UI change must prioritize mobile responsiveness, WCAG AA color contrast, and functional completeness (interactive elements must have real behaviors).
4. **State Handling Where Applicable**: Empty, loading, and error states must be implemented where applicable (e.g., interactive forms, video streaming, data tables), but should not be invented for screens that do not have such states.
5. **Pragmatic Component Extraction**: Reusable Blade components or UI abstractions should only be introduced when elements are genuinely shared across multiple views. Avoid premature abstraction.
6. **No Fabricated Content**: Never invent fake testimonials, fabricated statistics, unsupported security claims, non-existent navigation targets, or placeholder product claims.
