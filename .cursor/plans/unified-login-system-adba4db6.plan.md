<!-- adba4db6-1d9e-44e2-8da2-b78f39e02762 2e9a8653-18c3-495a-959a-6579e0afb518 -->
# HOD Portal Complete Rebuild

## Design Specifications

**Color Scheme**: Green as primary (matching system color)

- Primary: #10b981 (green-500), #059669 (green-600)
- Secondary: #047857 (green-700), #065f46 (green-800)
- Accent: #34d399 (green-400), #6ee7b7 (green-300)
- Background: #f0fdf4 (green-50), #dcfce7 (green-100)

**Typography**: Montserrat font family

**Design Style**: Clean professional dashboard with solid backgrounds

**Data Visualization**: Complete redesign with new chart types and modern styling

## Files to Update

### 1. Layout File: `resources/views/hod/layouts/app.blade.php`

**Changes**:

- Replace blue-purple gradient with green color scheme throughout
- Update sidebar background from `bg-gradient-to-b from-blue-600 to-purple-600` to `bg-gradient-to-b from-green-600 to-green-700`
- Change navbar from `bg-gradient-to-r from-blue-600 to-purple-600` to `bg-white shadow-md` with green accents
- Update all hover states from blue/purple to green
- Change font from Figtree to Montserrat
- Add proper footer section with university branding
- Update mobile sidebar colors to match new green theme
- Improve navbar with better spacing and modern design
- Add logout button styling with green theme

**Key Updates**:

```html
<!-- Font -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Sidebar -->
<div class="bg-gradient-to-b from-green-600 to-green-700">
  <!-- Logo section with green accents -->
  <!-- Navigation items with green hover states -->
</div>

<!-- Navbar -->
<div class="bg-white shadow-md border-b border-green-200">
  <!-- Clean white navbar with green accents -->
</div>

<!-- Footer -->
<footer class="bg-white border-t border-green-200 py-4">
  <!-- University branding and copyright -->
</footer>
```

### 2. Dashboard: `resources/views/hod/dashboard.blade.php`

**Changes**:

- Update page header from blue-purple gradient to clean white card with green accent border
- Redesign all 4 KPI cards with new green color scheme:
  - Department card: green-500 accent
  - Staff ID card: green-600 accent
  - Status card: green-700 accent
  - Additional metric card: green-400 accent
- Update card shadows and hover effects
- Redesign chart sections with modern card layouts
- Update all chart colors to green palette
- Add new data visualization components:
  - Attendance trend line chart (green gradients)
  - Course performance bar chart (green shades)
  - Student distribution pie chart (green palette)
  - Weekly activity heatmap (green color scale)
- Improve spacing and typography with Montserrat
- Add loading states with green spinners
- Update refresh button styling to green theme

**KPI Card Structure**:

```html
<div class="bg-white shadow-lg rounded-xl border-l-4 border-green-500 hover:shadow-xl transition-all">
  <div class="p-6">
    <div class="flex items-center">
      <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
        <!-- Icon -->
      </div>
      <div class="ml-4">
        <dt class="text-sm font-semibold text-gray-600">Label</dt>
        <dd class="text-2xl font-bold text-gray-900">Value</dd>
      </div>
    </div>
  </div>
</div>
```

### 3. Course Monitoring: `resources/views/hod/monitoring/courses.blade.php`

**Changes**:

- Update page header to white card with green accent
- Redesign filter section with green buttons and inputs
- Update data table styling with green accents
- Change chart colors to green palette:
  - Course attendance chart: green gradient bars
  - Performance trends: green line chart
  - Lecturer metrics: green-themed cards
- Update action buttons to green theme
- Improve mobile responsiveness
- Add export button with green styling

### 4. Student Monitoring: `resources/views/hod/monitoring/students.blade.php`

**Changes**:

- Update page header styling to match new theme
- Redesign student statistics cards with green colors
- Update attendance charts with green palette
- Change at-risk student indicators to use green/yellow/red appropriately
- Update filter dropdowns with green focus states
- Redesign student list table with green accents
- Update pagination controls to green theme
- Add student performance visualizations with green colors

### 5. Exam Eligibility: `resources/views/hod/exam/eligibility.blade.php`

**Changes**:

- Update page header to green theme
- Redesign eligibility status cards with green indicators
- Update progress bars to green gradients
- Change action buttons to green theme
- Update eligibility chart colors to green palette
- Redesign override controls with green styling
- Update bulk action buttons to green theme

### 6. Audit Logs: `resources/views/hod/audit/index.blade.php`

**Changes**:

- Update page header to green theme
- Redesign log entry cards with green accents
- Update filter controls with green styling
- Change security alert indicators appropriately
- Update export button to green theme
- Redesign log timeline with green markers
- Update search functionality styling

## Chart Color Palette

All charts will use this green-based palette:

- Primary: #10b981, #059669, #047857
- Secondary: #34d399, #6ee7b7, #a7f3d0
- Gradients: from-green-400 to-green-600
- Background: rgba(16, 185, 129, 0.1)
- Border: #10b981

## Common Components

### Button Styles

```css
Primary: bg-green-600 hover:bg-green-700 text-white
Secondary: bg-green-100 hover:bg-green-200 text-green-800
Outline: border-green-600 text-green-600 hover:bg-green-50
```

### Input Styles

```css
focus:ring-green-500 focus:border-green-500 border-gray-300
```

### Badge Styles

```css
Success: bg-green-100 text-green-800
Warning: bg-yellow-100 text-yellow-800
Danger: bg-red-100 text-red-800
```

## Implementation Notes

1. All color changes from blue/purple to green throughout
2. Font family changed to Montserrat with appropriate weights
3. Maintain existing functionality while updating UI
4. Ensure mobile responsiveness is preserved
5. Update Chart.js configurations with new green color palette
6. Add smooth transitions and hover effects
7. Improve accessibility with proper contrast ratios
8. Add loading states and skeleton screens where appropriate

### To-dos

- [ ] Update HOD layout file with green theme, Montserrat font, new sidebar, navbar, and footer
- [ ] Redesign dashboard with green KPI cards and new chart visualizations
- [ ] Update course monitoring page with green theme and redesigned charts
- [ ] Update student monitoring page with green theme and new visualizations
- [ ] Update exam eligibility page with green theme and redesigned components
- [ ] Update audit logs page with green theme and improved layout
- [ ] Test all pages on mobile, tablet, and desktop devices
- [ ] Verify all charts display correctly with new green color palette