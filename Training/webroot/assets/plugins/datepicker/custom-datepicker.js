class CustomDatePicker {
  constructor(inputId, options = {}) {
    if (typeof window !== 'undefined') {
      window.customDatePickers = window.customDatePickers || {};
      if (window.customDatePickers[inputId]) {
        return window.customDatePickers[inputId];
      }
    }

    this.inputId = inputId;
    this.input = document.getElementById(inputId);
    this.options = {
      format: 'MM/DD/YYYY',
      minDate: null,
      maxDate: null,
      startDate: null,
      onChange: null,
      showFooter: true,
      ...options
    };

    this.currentDate = new Date();
    this.selectedDate = null;
    this.viewMode = 'days'; // 'days', 'months', 'years'
    this.decadeStart = null;

    if (this.input) {
      this.init();
    }
  }

  init() {
    // Create picker container if not exists
    let container = document.getElementById(this.inputId + '_picker');
    if (!container) {
      container = document.createElement('div');
      container.id = this.inputId + '_picker';
      container.className = 'date-picker-container';
    }
    // Always append container to document.body to ensure absolute positioning is relative to document body
    document.body.appendChild(container);
    this.container = container;
    this.toggle = this.input.parentElement.querySelector('.date-picker-toggle');

    // Build picker HTML
    this.buildPicker();

    // Attach events
    this.attachEvents();

    if (typeof window !== 'undefined') {
      window.customDatePickers[this.inputId] = this;
    }

    // Parse initial value if exists
    if (this.input.value) {
      this.selectedDate = this.parseDate(this.input.value);
      if (this.selectedDate) {
        this.currentDate = new Date(this.selectedDate);
      }
    }

    this.render();
  }

  buildPicker() {
    this.container.innerHTML = `
      <div class="date-picker-header">
        <button type="button" class="prev-btn">❮</button>
        <div class="month-year"></div>
        <button type="button" class="next-btn">❯</button>
      </div>
      <div class="date-picker-weekdays">
        <div>Sun</div>
        <div>Mon</div>
        <div>Tue</div>
        <div>Wed</div>
        <div>Thu</div>
        <div>Fri</div>
        <div>Sat</div>
      </div>
      <div class="date-picker-days"></div>
      <div class="date-picker-months"></div>
      <div class="date-picker-years"></div>
      ${this.options.showFooter ? `
      <div class="date-picker-footer">
        <button type="button" class="today-btn">Today</button>
        <button type="button" class="clear-btn">Clear</button>
      </div>` : ''}
    `;
  }

  attachEvents() {
    // Toggle button
    if (this.toggle) {
      this.toggle.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        this.togglePicker();
      });
    }

    // Input click
    this.input.addEventListener('click', (e) => {
      e.stopPropagation();
      this.show();
    });

    // Navigation buttons
    this.container.querySelector('.prev-btn').addEventListener('click', (e) => {
      e.stopPropagation();
      this.navigate('prev');
    });
    this.container.querySelector('.next-btn').addEventListener('click', (e) => {
      e.stopPropagation();
      this.navigate('next');
    });

    // Month/Year header click
    this.container.querySelector('.month-year').addEventListener('click', (e) => {
      e.stopPropagation();
      this.switchViewMode();
    });

    // Today and Clear buttons
    if (this.options.showFooter) {
      this.container.querySelector('.today-btn').addEventListener('click', (e) => {
        e.stopPropagation();
        this.selectToday();
      });
      this.container.querySelector('.clear-btn').addEventListener('click', (e) => {
        e.stopPropagation();
        this.clearDate();
      });
    }

    // Close on outside click
    document.addEventListener('click', (e) => {
      if (!this.input.contains(e.target) &&
        !(this.toggle && this.toggle.contains(e.target)) &&
        !this.container.contains(e.target)) {
        this.hide();
      }
    });
  }

  render() {
    const year = this.currentDate.getFullYear();
    const month = this.currentDate.getMonth();

    // Update header
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'];
    if (this.viewMode === 'years') {
      const yearStart = this.decadeStart || Math.floor(year / 10) * 10;
      this.container.querySelector('.month-year').textContent = `${yearStart} - ${yearStart + 19}`;
    } else if (this.viewMode === 'months') {
      this.container.querySelector('.month-year').textContent = `${year}`;
    } else {
      this.container.querySelector('.month-year').textContent = `${monthNames[month]} ${year}`;
    }

    if (this.viewMode === 'days') {
      this.renderDays();
    } else if (this.viewMode === 'months') {
      this.renderMonths();
    } else if (this.viewMode === 'years') {
      this.renderYears();
    }
  }

  renderDays() {
    const year = this.currentDate.getFullYear();
    const month = this.currentDate.getMonth();

    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const prevLastDay = new Date(year, month, 0);

    const firstDayOfWeek = firstDay.getDay();
    const lastDateOfMonth = lastDay.getDate();
    const prevLastDate = prevLastDay.getDate();

    const daysContainer = this.container.querySelector('.date-picker-days');
    daysContainer.innerHTML = '';

    // Previous month days
    for (let i = firstDayOfWeek - 1; i >= 0; i--) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = prevLastDate - i;
      btn.className = 'other-month';
      btn.disabled = true;
      daysContainer.appendChild(btn);
    }

    // Current month days
    for (let day = 1; day <= lastDateOfMonth; day++) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = day;

      const currentDateObj = new Date(year, month, day);
      const isToday = this.isToday(currentDateObj);
      const isSelected = this.selectedDate &&
        this.isSameDay(currentDateObj, this.selectedDate);

      if (isToday) btn.classList.add('today');
      if (isSelected) btn.classList.add('selected');

      btn.addEventListener('click', () => this.selectDate(currentDateObj));
      daysContainer.appendChild(btn);
    }

    // Next month days
    const totalCells = daysContainer.children.length;
    const remainingCells = 42 - totalCells;
    for (let day = 1; day <= remainingCells; day++) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = day;
      btn.className = 'other-month';
      btn.disabled = true;
      daysContainer.appendChild(btn);
    }

    // Hide months and years
    this.container.querySelector('.date-picker-months').classList.remove('show');
    this.container.querySelector('.date-picker-years').classList.remove('show');
    this.container.querySelector('.date-picker-weekdays').style.display = 'grid';
    this.container.querySelector('.date-picker-days').style.display = 'grid';
  }

  renderMonths() {
    const year = this.currentDate.getFullYear();
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
      'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const monthsContainer = this.container.querySelector('.date-picker-months');
    monthsContainer.innerHTML = '';

    monthNames.forEach((monthName, index) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = monthName;

      if (this.selectedDate &&
        this.selectedDate.getFullYear() === year &&
        this.selectedDate.getMonth() === index) {
        btn.classList.add('selected');
      }

      btn.addEventListener('click', () => {
        this.currentDate.setMonth(index);
        this.viewMode = 'days';
        this.render();
      });

      monthsContainer.appendChild(btn);
    });

    monthsContainer.classList.add('show');
    this.container.querySelector('.date-picker-years').classList.remove('show');
    this.container.querySelector('.date-picker-days').style.display = 'none';
    this.container.querySelector('.date-picker-weekdays').style.display = 'none';
  }

  renderYears() {
    const currentYear = this.currentDate.getFullYear();
    const startYear = this.decadeStart || Math.floor(currentYear / 10) * 10;
    this.decadeStart = startYear;
    const yearsContainer = this.container.querySelector('.date-picker-years');
    yearsContainer.innerHTML = '';

    // Show 20 years (2 decades) for dynamic navigation
    for (let i = 0; i < 20; i++) {
      const year = startYear + i;
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = year;

      if (this.selectedDate && this.selectedDate.getFullYear() === year) {
        btn.classList.add('selected');
      }

      btn.addEventListener('click', () => {
        this.currentDate.setFullYear(year);
        this.viewMode = 'months';
        this.render();
      });

      yearsContainer.appendChild(btn);
    }

    yearsContainer.classList.add('show');
    this.container.querySelector('.date-picker-months').classList.remove('show');
    this.container.querySelector('.date-picker-days').style.display = 'none';
    this.container.querySelector('.date-picker-weekdays').style.display = 'none';
  }

  selectDate(dateObj) {
    this.selectedDate = dateObj;
    this.input.value = this.formatDate(dateObj);
    this.render();
    this.hide();

    // Trigger change event for Angular
    this.input.dispatchEvent(new Event('change', { bubbles: true }));

    if (this.options.onChange) {
      this.options.onChange(this.selectedDate);
    }
  }

  selectToday() {
    const today = new Date();
    this.currentDate = new Date(today);
    this.selectDate(today);
  }

  clearDate() {
    this.selectedDate = null;
    this.input.value = '';
    this.currentDate = new Date();
    this.render();

    // Trigger change event for Angular
    this.input.dispatchEvent(new Event('change', { bubbles: true }));

    if (this.options.onChange) {
      this.options.onChange(null);
    }
  }

  navigate(direction) {
    if (this.viewMode === 'days') {
      if (direction === 'prev') {
        this.currentDate.setMonth(this.currentDate.getMonth() - 1);
      } else {
        this.currentDate.setMonth(this.currentDate.getMonth() + 1);
      }
    } else if (this.viewMode === 'months') {
      if (direction === 'prev') {
        this.currentDate.setFullYear(this.currentDate.getFullYear() - 1);
      } else {
        this.currentDate.setFullYear(this.currentDate.getFullYear() + 1);
      }
    } else if (this.viewMode === 'years') {
      if (direction === 'prev') {
        this.decadeStart = (this.decadeStart || Math.floor(this.currentDate.getFullYear() / 10) * 10) - 10;
      } else {
        this.decadeStart = (this.decadeStart || Math.floor(this.currentDate.getFullYear() / 10) * 10) + 10;
      }
    }
    this.render();
  }

  prevMonth() {
    this.currentDate.setMonth(this.currentDate.getMonth() - 1);
    this.render();
  }

  nextMonth() {
    this.currentDate.setMonth(this.currentDate.getMonth() + 1);
    this.render();
  }

  switchViewMode() {
    if (this.viewMode === 'days') {
      this.viewMode = 'years';
      this.decadeStart = Math.floor(this.currentDate.getFullYear() / 10) * 10;
    } else if (this.viewMode === 'months') {
      this.viewMode = 'years';
      this.decadeStart = Math.floor(this.currentDate.getFullYear() / 10) * 10;
    } else {
      this.viewMode = 'days';
    }
    this.render();
  }

  show() {
    this.container.classList.add('show');
    this.container.style.display = 'block';
    this.positionPicker();
  }

  hide() {
    this.container.classList.remove('show');
    this.container.style.display = 'none';
  }

  togglePicker() {
    if (this.container.classList.contains('show')) {
      this.hide();
    } else {
      this.show();
    }
  }

  positionPicker() {
    const rect = this.input.getBoundingClientRect();
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
    const viewportHeight = window.innerHeight;
    
    const pickerWidth = this.container.offsetWidth || 210;
    const pickerHeight = this.container.offsetHeight || 190;
    
    // Check if there is enough space below the input to show the picker
    const spaceBelow = viewportHeight - rect.bottom;
    let top;
    
    if (spaceBelow >= pickerHeight + 10) {
      top = rect.bottom + scrollTop + 6;
    } else {
      top = rect.top + scrollTop - pickerHeight - 6;
    }
    
    const left = rect.left + scrollLeft + (rect.width / 2) - (pickerWidth / 2);
    
    this.container.style.position = 'absolute';
    this.container.style.top = top + 'px';
    this.container.style.left = left + 'px';
  }

  formatDate(date) {
    if (!date) return '';

    const pad = (n) => String(n).padStart(2, '0');
    const month = pad(date.getMonth() + 1);
    const day = pad(date.getDate());
    const year = date.getFullYear();

    switch (this.options.format.toUpperCase()) {
      case 'MM/DD/YYYY':
        return `${month}/${day}/${year}`;
      case 'DD/MM/YYYY':
        return `${day}/${month}/${year}`;
      case 'YYYY-MM-DD':
        return `${year}-${month}-${day}`;
      default:
        return `${month}/${day}/${year}`;
    }
  }

  parseDate(dateStr) {
    const formats = [
      { regex: /(\d{2})\/(\d{2})\/(\d{4})/, groups: [2, 1, 3] }, // MM/DD/YYYY
      { regex: /(\d{4})-(\d{2})-(\d{2})/, groups: [1, 3, 2] }    // YYYY-MM-DD
    ];

    for (let format of formats) {
      const match = dateStr.match(format.regex);
      if (match) {
        const year = parseInt(match[format.groups[0]]);
        const month = parseInt(match[format.groups[1]]) - 1;
        const day = parseInt(match[format.groups[2]]);
        const parsed = new Date(year, month, day);
        return this.isValidDate(parsed) ? parsed : null;
      }
    }

    const fallback = new Date(dateStr);
    return this.isValidDate(fallback) ? fallback : null;
  }

  isValidDate(date) {
    return date instanceof Date && !isNaN(date.getTime());
  }

  isToday(date) {
    const today = new Date();
    return this.isSameDay(date, today);
  }

  isSameDay(date1, date2) {
    return date1.getFullYear() === date2.getFullYear() &&
      date1.getMonth() === date2.getMonth() &&
      date1.getDate() === date2.getDate();
  }

  setDate(dateObj) {
    this.selectedDate = dateObj;
    this.currentDate = new Date(dateObj);
    this.input.value = this.formatDate(dateObj);
    this.render();
  }

  getDate() {
    return this.selectedDate;
  }
}

// Auto-initialize all date inputs with id attribute
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.date-input[id]').forEach(input => {
    new CustomDatePicker(input.id);
  });
});