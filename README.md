# Laravel Distributed KV
**Distributed key–value registry for Laravel applications — no Redis required.**  
Sync configuration, feature flags, and shared state across multiple Laravel apps using only PHP, HTTP APIs, and scheduled commands.

---

## 🚀 Features

- 🔄 **Distributed key/value store** shared across multiple Laravel applications
- 🌐 **Sync via HTTP API** (pull + push)
- 🕒 **Cron‑based synchronization** (`php artisan kv:sync`)
- 🧠 **Versioning + timestamp conflict resolution**
- 🗑️ **Soft delete with propagation**
- 📡 **Automatic client discovery** (new clients propagate to all others)
- 🔔 **Laravel events** for every change:
    - `KeyCreated`
    - `KeyUpdated`
    - `KeyDeleted`
    - `KeySynced`
    - `ClusterClientAdded`
- 🛠️ **Simple API** (`DistributedKv::set()`, `get()`, `delete()`)
- 🧩 **Admin panel** (`/kv-admin`) to inspect keys, versions, and cluster state
- 🧱 **Zero external dependencies** — no Redis, no queues, no message brokers

---

## 📦 Installation

### 1. Require the package

```bash
composer require fratac/laravel-distributed-kv
