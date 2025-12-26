# Changelog
Tutti i cambiamenti rilevanti di questo progetto verranno documentati in questo file.

Il formato segue le linee guida di **Keep a Changelog**  
e questo progetto aderisce a **Semantic Versioning**.

---

## [Unreleased]
### Added
- Namespaces e struttura package `fratac/laravel-distributed-kv`
- Sincronizzazione distribuita key/value tra applicazioni Laravel
- API endpoints:
    - `GET /api/kv/pull`
    - `POST /api/kv/push`
    - `POST /api/kv/register-client`
- Storage Eloquent con:
    - `key`
    - `value`
    - `version`
    - `updated_at`
    - `deleted_at`
- Versioning delle chiavi con risoluzione conflitti:
    - priorità alla versione
    - fallback su timestamp
- Soft delete distribuito (`deleted_at`)
- Sistema di propagazione automatica dei client nel cluster
- Comando Artisan `kv:sync` per sincronizzazione pull/push
- Facade `DistributedKv` con metodi:
    - `set()`
    - `get()`
    - `delete()`
    - `registerNewClient()`
    - `sync()`
- Eventi Laravel:
    - `KeyCreated`
    - `KeyUpdated`
    - `KeyDeleted`
    - `KeySynced`
    - `ClusterClientAdded`
- Admin panel `/kv-admin` con:
    - lista chiavi
    - versioni
    - stato (active/deleted)
    - timestamp
    - client conosciuti
    - ultimo sync
- Middleware `VerifyKvToken` per autenticazione API
- Configurazione pubblicabile `distributed-kv.php`
- Migration `create_distributed_kv_entries_table`
- Documentazione iniziale (README)

### Changed
- Nessuna modifica al momento.

### Fixed
- Nessuna correzione al momento.

---

## [1.0.0] – TBD
Versione stabile iniziale.

### Added
- Primo rilascio pubblico del package.

---

