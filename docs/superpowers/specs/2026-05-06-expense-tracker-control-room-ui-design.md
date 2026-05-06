# Expense Tracker Control Room UI Design

Date: 2026-05-06
Status: Approved for implementation planning

## Goal

Redesign the authenticated Expense Tracker interface to closely match the warm editorial control-room UI already used in `/Users/wmafendi/Herd/notion-blog-automation`.

The result should feel like a calm personal finance operations room: warm, readable, compact, and useful for repeated expense logging and review.

## Reference Direction

Use the faithful control-room direction selected by the user:

- Paper workspace background.
- Espresso navigation surface.
- Terracotta primary accent.
- Cream cards and soft panels.
- Compact page hero blocks.
- Uppercase system labels.
- 8px rounded corners.
- Subtle borders and shadows.
- Operational tables with tinted headers, readable row spacing, and hover states.

This is intentionally closer to the reference app than a new finance-specific visual language.

## Scope

Apply the system across the authenticated app:

- App shell and navigation.
- Dashboard.
- Expenses index, filters, table, empty state, pagination area, and modal.
- Categories index, list items, empty state, and modal.
- Insights page and embedded chart/stat components where styling is visible.
- Profile page surfaces if they are using the same authenticated layout.

The public welcome and auth pages can remain lower priority unless they visibly clash after the authenticated app is complete.

## App Shell

Replace the current plain Breeze-style top navigation treatment with a stronger app shell inspired by the reference project.

The shell should include:

- Warm paper page background.
- Espresso navigation with high-contrast cream text.
- Clear active navigation state.
- Links for Dashboard, Expenses, Categories, Insights, and Profile.
- User menu or logout access that remains easy to find.
- Responsive behavior that works on mobile without overlapping text or controls.

Because the app has a small navigation set, the shell should stay efficient. A full sidebar is acceptable if it remains responsive and readable; a warm top rail is acceptable only if it still clearly carries the reference project's visual DNA.

## Shared UI System

Create reusable utility classes in `resources/css/app.css` mirroring the reference style:

- `app-workspace`
- `app-main`
- `page-stack`
- `page-hero`
- `page-hero-kicker`
- `page-hero-title`
- `page-hero-copy`
- `metric-card`
- `metric-label`
- `metric-value`
- `app-card`
- `app-card-padded`
- `app-card-header`
- `app-soft-panel`
- `app-table`
- `app-table-head`
- `app-table-th`
- `app-table-body`
- `app-table-row`
- `empty-state`
- `empty-state-icon`

The exact class list can be adjusted during implementation if the existing Tailwind version requires different syntax, but the visual system should remain consistent.

## Dashboard

Dashboard should be the closest visual match to the selected mockup.

It should include:

- A compact page hero with a finance overview kicker, strong title, and short supporting copy.
- Soft summary panels in the hero when useful.
- Three metric cards for today total, current month total, and total entries.
- Chart cards styled as app cards, not generic starter-kit panels.
- Chart colors adjusted away from default purple/blue toward terracotta and restrained supporting tones.

## Expenses

The expenses page is the primary work surface.

It should include:

- A compact hero that shows the page title, current filtered month total, and the add action.
- Filters grouped in a soft panel or compact control row.
- A styled operational table using the shared app table classes.
- Category swatches that remain visible and readable.
- Amounts aligned and formatted for scanning.
- Icon buttons for edit and delete.
- An empty state with one useful next-step sentence.
- A modal form that uses the same card rhythm and spacing.

## Categories

The categories page should feel like a tidy management list.

It should include:

- A compact hero with the add category action.
- Category rows as standalone list items, not nested cards.
- Color swatches, category names, expense counts, and edit/delete actions.
- A polished empty state.
- A modal form matching the expenses modal treatment.

## Insights

The insights page should reuse the same dashboard language.

It should include:

- A compact hero explaining the analysis view.
- Metric cards for average monthly spend, highest month, lowest month, and top category.
- A restrained month-over-month banner with status color that still fits the warm palette.
- Chart/stat component wrappers updated to app-card styling where those components expose markup.

## Data Flow

This redesign should not change data models, routes, validation rules, or Livewire behavior.

Existing Livewire computed data should continue to power:

- Dashboard totals and charts.
- Expense filters and pagination.
- Expense and category create/edit/delete modals.
- Insights metrics and chart components.

JavaScript chart initialization may be adjusted only for visual colors and layout resilience.

## Error Handling And Empty States

Existing validation and toast behavior should remain.

Empty states should be upgraded visually but remain concise:

- Expenses: explain that logged expenses will appear here and point users to the add action.
- Categories: explain that categories help organize spending.
- Dashboard and insights charts: handle empty data without broken layout.

## Testing And Verification

Implementation should be verified with:

- A frontend build command such as `npm run build`.
- A Laravel test command if the app test suite is available and reasonably fast.
- Browser inspection on desktop and mobile widths.
- A quick check that navigation, filters, modals, and chart rendering still work.

## Out Of Scope

- New budget logic or spending limits.
- New database fields.
- New chart types beyond visual styling of current charts.
- Rewriting Livewire behavior unrelated to presentation.
- Major public landing page redesign unless needed to avoid severe visual inconsistency.
