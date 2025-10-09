# 💰 Casher

<center><svg width="250" height="250" viewBox="0 0 293 312" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect x="22" y="42.678" width="248" height="250" rx="6" fill="#BA30FF" stroke="#1E1E1E" stroke-width="4" stroke-linecap="round"/>
    <path d="M7.72737 104.697L83.8707 27.4655C84.9982 26.3219 86.5372 25.678 88.1433 25.678H203.857C205.463 25.678 207.002 26.3219 208.129 27.4655L284.273 104.697C285.379 105.819 286 107.332 286 108.909V226.447C286 228.023 285.379 229.537 284.273 230.659L208.129 307.89C207.002 309.034 205.463 309.678 203.857 309.678H88.1433C86.5372 309.678 84.9982 309.034 83.8706 307.89L7.72738 230.659C6.62052 229.537 6 228.023 6 226.447V108.909C6 107.332 6.62051 105.819 7.72737 104.697Z" fill="#FFD427" stroke="#1E1E1E" stroke-width="4" stroke-linecap="round"/>
    <path d="M135.3 209.5L147.62 221.6C141.313 230.84 132.88 238.173 122.32 243.6C111.76 248.88 100.76 251.52 89.32 251.52C78.6133 251.52 68.7133 249.467 59.62 245.36C50.5267 241.253 42.5333 235.46 35.64 227.98C28.8933 220.5 23.6133 211.773 19.8 201.8C16.1333 191.827 14.3 181.047 14.3 169.46C14.3 157.727 16.2067 146.873 20.02 136.9C23.8333 126.927 29.1133 118.2 35.86 110.72C42.6067 103.24 50.6 97.4467 59.84 93.34C69.08 89.2333 79.0533 87.18 89.76 87.18C101.787 87.18 112.713 89.7467 122.54 94.88C132.513 100.013 140.873 107.42 147.62 117.1L135.3 129.2C129.873 120.547 123.273 114.02 115.5 109.62C107.873 105.22 99.1467 103.02 89.32 103.02C78.4667 103.02 68.7133 105.88 60.06 111.6C51.5533 117.32 44.8067 125.167 39.82 135.14C34.98 145.113 32.56 156.553 32.56 169.46C32.56 182.073 34.98 193.44 39.82 203.56C44.8067 213.533 51.5533 221.38 60.06 227.1C68.7133 232.82 78.4667 235.68 89.32 235.68C99 235.68 107.727 233.48 115.5 229.08C123.42 224.533 130.02 218.007 135.3 209.5ZM166.705 220.72L178.585 209.72C190.172 226.293 205.278 234.58 223.905 234.58C235.638 234.58 245.025 231.647 252.065 225.78C259.105 219.913 262.625 212.507 262.625 203.56C262.625 195.347 259.545 189.04 253.385 184.64C247.225 180.093 237.178 176.647 223.245 174.3C204.912 171.073 191.785 166.307 183.865 160C175.945 153.547 171.985 144.307 171.985 132.28C171.985 124.067 174.185 116.733 178.585 110.28C183.132 103.827 189.218 98.7667 196.845 95.1C204.618 91.2867 213.345 89.38 223.025 89.38C233.732 89.38 243.485 91.8733 252.285 96.86C261.085 101.7 268.785 108.74 275.385 117.98L263.505 128.98C258.518 121.207 252.505 115.193 245.465 110.94C238.425 106.54 231.018 104.34 223.245 104.34C212.832 104.34 204.325 106.907 197.725 112.04C191.272 117.173 188.045 123.847 188.045 132.06C188.045 139.98 190.758 145.92 196.185 149.88C201.758 153.693 210.778 156.627 223.245 158.68C243.045 161.907 257.198 166.967 265.705 173.86C274.358 180.607 278.685 190.507 278.685 203.56C278.685 212.213 276.412 219.987 271.865 226.88C267.318 233.773 260.938 239.273 252.725 243.38C244.512 247.34 234.905 249.32 223.905 249.32C212.318 249.32 201.612 246.9 191.785 242.06C182.105 237.22 173.745 230.107 166.705 220.72ZM230.945 165.28H217.525V65.18H230.945V165.28ZM230.725 273.52H217.305V167.48H230.725V273.52Z" fill="#1E1E1E"/>
</svg></center>

**Casher** is a modern web application for managing and tracking personal finances.  
It allows users to record their income and expenses, categorize them, and analyze their financial habits through tables and visual charts.

This project was developed as a school assignment using the **Laravel framework**.

---

## Features

- User registration & authentication (Laravel Breeze)
- Add, edit, and delete transactions
- Overview of income, expenses, and total balance
- Categorization (food, rent, entertainment, transport, etc.)
- Filtering by date, category, and transaction type
- Data storage in a **MySQL** database
- Responsive UI with **Tailwind CSS**
- Secure authentication, CSRF protection, input validation

---

## Tech Stack

| Layer | Technology |
|--------|-------------|
| Backend | **Laravel 11 (PHP 8.3)** |
| Frontend | **Blade + Alpine.js + Tailwind CSS** |
| Authentication | **Laravel Breeze (Blade stack)** |
| Database | **MySQL 8.0** |
| Testing | **Pest / PHPUnit** |
| Local Dev | **Laravel Herd** |
| Version Control | **Git + GitHub** |

---

## Application Structure

| Module | Description |
|---------|--------------|
| **Dashboard** | Overview of total income, expenses, and balance |
| **Transactions** | Table of all transactions |
| **Add Transaction** | Form for creating new transactions |
| **Categories** | Category management |
| **Profile** | User profile & settings |
| **Auth** | Login, Register, Logout |





<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>