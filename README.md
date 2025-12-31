# 🛰️ Mars Rover Mission

A full-stack simulation of a Mars rover receiving commands from Earth, built with **Laravel (PHP)** on the backend and **Vue 3 + TypeScript** on the frontend.

The application simulates a rover moving on a bounded 200×200 world, handling obstacles, world borders, and command execution with visual feedback.

---

## ✨ Features

- 🚀 Rover movement simulation with commands (`F`, `L`, `R`)
- 🧭 Direction handling (North, East, South, West)
- 🗺️ World size: **200 × 200**
- 👀 Responsive viewport
    - **Mobile:** 10×10 view
    - **Desktop:** larger viewport
- 📍 Clickable obstacles on the grid
- ⛔ World borders behave like obstacles
- 🧠 Command validation and execution on the backend
- 🧪 Domain logic fully unit tested
- 📡 API endpoint for command execution
- 💡 “How to operate” modal with instructions
- 📱 Mobile-friendly UI

---

## 🧱 Tech Stack

### Backend

- PHP 8+
- Laravel
- Pest (testing)
- REST API

### Frontend

- Vue 3
- TypeScript
- Inertia.js
- Vite
- Tailwind CSS

---

## 🗂️ Project Structure (simplified)

```text
app/
├── Domain/
│   └── Rover/
├── Http/
│   ├── Controllers/
│   └── Requests/
routes/
├── api.php
resources/
├── js/
│   ├── pages/
│   │   └── Rover/
│   ├── components/
│   │   └── Modal.vue
│   └── types/
│       └── Rover/
tests/
├── Unit/
├── Feature/

```

---

## 🧠 How the simulation works

### Commands

- **F** → Move forward one cell
- **L** → Move to the left one cell (90°)
- **R** → Move to the right one cell (90°)

Commands are sent as a sequence (e.g. `FFRFFL`).

---

### Obstacles

- Obstacles can be placed by clicking cells on the grid
- Obstacles cannot be placed on the rover’s current position
- The rover checks for obstacles **before every forward move**

---

### Borders

- The world is bounded (`0..199`)
- Borders behave like obstacles
- If the rover attempts to leave the world:
    - It stops at the last valid position
    - The remaining commands are aborted

---

## 📡 API Endpoint

### Execute rover commands

**POST** `/api/rover/execute`

#### Request body

```json
{
    "initial": {
        "x": 0,
        "y": 0,
        "direction": "N"
    },
    "commands": "FFRFF",
    "obstacles": [{ "x": 3, "y": 2 }]
}
```

#### Response

```json
{
    "position": { "x": 2, "y": 1 },
    "direction": "E",
    "aborted": false,
    "executedCommands": 5,
    "obstacle": null,
    "usedCommands": "FFRFF"
}
```

---

## 🧪 Testing

- Domain logic is fully unit tested
- API behavior is covered by feature tests
- Tests are written using Pest

Run backend tests:

```sh
./vendor/bin/pest
```

Run frontend tests:

```sh
npm run test:frontend
```

---

## ⚙️ Local setup

### Requirements

- PHP 8+
- Composer
- Node.js + npm
- Laravel Herd (optional)

### Installation

```sh
composer install
cp .env.example .env
php artisan key:generate
npm install
```

### Run the backend if you don't use Herd

```sh
php artisan serve
```

### Run the app

```sh
npm run dev
```

Visit:
If you use Herd:

```sh
http://mars-rover.test
```

If not:

```sh
http://127.0.0.1:8000
```

## 🧭 Design principles

- Domain-driven design: core rover logic is isolated and framework-agnostic
- Separation of concerns: UI state ≠ domain state
- Defensive programming: rover never enters invalid states
- Progressive UX: viewport adapts to screen size
