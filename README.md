# Child Data Analytics Platform

ML-assisted early cognitive and developmental support platform for children with Autism Spectrum Disorder (ASD). Final-year Computer Science project.

## Structure

- `app/` — Laravel 12 backend (controllers, Eloquent models, role middleware, `ChildPredictionService`)
- `resources/views/` — Blade + Tailwind CSS UI (Caregiver, Clinician, and Administrator portals)
- `routes/` & `database/` — Web routes, PostgreSQL migrations, factories, and seeders
- `ml-service/` — FastAPI inference microservice (`GET /health`, `POST /predict`, Pydantic validation, `joblib` pipeline, pytest suite)
- `ASD-Data-Model-Analysis/` — Synthetic ASD dataset (9 CSVs), leak-free ML experiments (`MLmodel.ipynb`, `run_pipeline.py`), metrics, plots, and trained models
- `tests/` — PHPUnit unit and feature test suites

## Build order

1. Repository + Laravel setup + `.gitignore`
2. PostgreSQL schema + migrations
3. Authentication + Role-Based Access Control (`caregiver`, `clinician`, `administrator`)
4. Child profile management + clinician caseload links (`child_clinician`)
5. Behavioural observation collection (`1–5` domain ratings) + validation + access controls
6. Dataset inspection + leak-free group splitting (`GroupShuffleSplit` on `child_id`)
7. Preprocessing pipeline (`ColumnTransformer`) + baseline Decision Tree & Naïve Bayes models
8. Hyperparameter optimization + pruned Decision Tree (`max_depth=5`, `class_weight='balanced'`)
9. FastAPI prediction microservice (`GET /health`, `POST /predict`)
10. Laravel–FastAPI integration (`ChildPredictionService`) + duplicate prediction prevention
11. Caregiver recommendations + longitudinal analytics UI
12. Clinician child monitoring + 14-domain assessment + SMART therapy goals & milestones
13. Administrator user onboarding + role governance + live FastAPI service health
14. Privacy-safe caseload oversight + ML research integration + end-to-end tests

## Setup

### 1. Web Application (Laravel + PostgreSQL)

```bash
cp .env.example .env
# configure DB_CONNECTION=pgsql, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
# set CHILD_PREDICTION_SERVICE_URL=http://127.0.0.1:8001

composer install
php artisan key:generate
php artisan migrate

npm install
npm run build
```

### 2. ML Prediction Service (FastAPI)

```bash
cd ml-service
python -m venv .venv
# Windows PowerShell: .\.venv\Scripts\Activate.ps1
# macOS/Linux: source .venv/bin/activate
pip install -r requirements.txt
```

### 3. Run Locally & Test

```bash
# Terminal 1: Start FastAPI ML service (port 8001)
cd ml-service
python -m uvicorn app.main:app --port 8001 --reload

# Terminal 2: Start Laravel web server (port 8000)
php artisan serve
```

Run automated test suites:

```bash
php artisan test
ml-service/.venv/Scripts/python -m pytest ml-service/tests/test_api.py -v
```
