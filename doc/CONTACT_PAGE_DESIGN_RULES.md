# Contact Us Page Design Rules

## 🎨 Design Guidelines

### **1. Layout Structure**
- **Container**: Use slight off-color background instead of dashed borders
- **Responsive**: Mobile-first approach with Tailwind CSS
- **Typography**: Follow existing font hierarchy (Unbounded, Roboto, Roboto Mono)
- **Spacing**: Consistent padding and margins using Tailwind spacing scale

### **2. Color Scheme**
- **Theme Responsive**: Colors switch based on theme selection
- **Dark Theme**:
  - Primary Background: `#1a1a1a`
  - Secondary Background: Slight off-color variations for sections
  - Text Colors: 
    - Primary: `#f9fafb` (white)
    - Secondary: `#676665` (gray)
    - Accent: `#d6cdc2` (light beige)
  - Borders: `#404040` (dark gray)
- **Light Theme**:
  - Primary Background: `#ffffff` (white)
  - Secondary Background: Light off-color variations for sections
  - Text Colors:
    - Primary: `#111827` (dark gray)
    - Secondary: `#6b7280` (medium gray)
    - Accent: `#d6cdc2` (light beige)
  - Borders: `#d1d5db` (light gray)

### **3. Typography**
- **Headings**: Unbounded font family
- **Body Text**: Urbanist font family
- **Font Weights**: 400 (normal), 500 (medium), 600 (semibold), 700 (bold)

### **4. Component Guidelines**

#### **Form Elements**
- **Input Fields**: Dark background with light text
- **Labels**: Clear, accessible labeling
- **Validation**: Proper error states and success feedback
- **Buttons**: Consistent with existing button styles

#### **Contact Information**
- **Icons**: Use SVG icons for consistency
- **Layout**: Grid or flex layout for contact details
- **Spacing**: Proper visual hierarchy

#### **Map Integration**
- **Container**: Responsive map container
- **Styling**: Dark theme compatible
- **Loading**: Proper loading states

### **5. Responsive Breakpoints**
- **Mobile**: `max-sm:` (up to 640px)
- **Tablet**: `max-md:` (up to 768px)
- **Desktop**: `max-lg:` (up to 1024px)
- **Large Desktop**: `max-xl:` (up to 1280px)

### **6. Accessibility**
- **Color Contrast**: Minimum 4.5:1 ratio
- **Focus States**: Visible focus indicators
- **Screen Readers**: Proper ARIA labels
- **Keyboard Navigation**: Full keyboard accessibility

### **7. Performance**
- **Images**: Optimized and lazy-loaded
- **CSS**: Tailwind classes for minimal bundle size
- **JavaScript**: Minimal, progressive enhancement

### **8. File Structure**
```
packages/Webkul/Shop/src/Resources/views/
├── contact/
│   └── index.blade.php (standalone contact page)

routes/
└── web.php (add contact route)

Footer Link:
- Add contact page link to footer navigation
```

### **9. Implementation Steps**
1. Create contact page route in web.php
2. Create standalone contact page view
3. Implement responsive layout with theme switching
4. Add form functionality with validation
5. Integrate contact information section
6. Add map component (if included in design)
7. Add footer link to contact page
8. Test theme responsiveness (dark/light)
9. Test responsiveness across devices
10. Optimize performance

### **10. Quality Assurance**
- **Cross-browser**: Test on Chrome, Firefox, Safari, Edge
- **Mobile Testing**: Test on various screen sizes
- **Performance**: Lighthouse score > 90
- **Accessibility**: WCAG 2.1 AA compliance 