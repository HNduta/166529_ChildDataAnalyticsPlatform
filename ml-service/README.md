
# Python ML Prediction Service

## Overview

This service exposes the saved scikit-learn model through a FastAPI
HTTP API. It is intended for exploratory decision support within the
Child Data Analytics Platform.

The service loads the saved model at application startup. It does not
retrain the model when a prediction request is received.

## Model

- Artifact: `models/child_intervention_pipeline.joblib`
- Model pipeline: preprocessing followed by logistic regression
- Required input features: 14
- Output: predicted class and uncalibrated scores for each class

The scores are not clinically validated probabilities. The service is
not a diagnostic tool and must not replace assessment or professional
judgment by a qualified practitioner.

## Requirements

Python and the packages listed in `requirements.txt` are required.
Use a virtual environment for local development.

## Start the service

From the Laravel repository root, activate the virtual environment:

```powershell
.\ml-service\.venv\Scripts\Activate.ps1
```

Start the API:

```powershell
python -m uvicorn app.main:app --app-dir .\ml-service --reload
```

Interactive API documentation:

`http://127.0.0.1:8000/docs`

## Endpoints

### GET /health

Reports service status and whether the model is loaded.

### POST /predict

Accepts the 14 model input features and returns the predicted class,
class scores, score interpretation, and disclaimer.

The request schema is available at `/docs`.

## Run automated tests

From the repository root, with the virtual environment activated:

```powershell
python -m pytest .\ml-service\tests\test_api.py -v
```

## Limitations

- Model scores are not calibrated clinical probabilities.
- Class labels must not be given a clinical interpretation until the
  training target is confirmed.
- Numeric input limits must be checked against the training data.
- The API input contract is not interchangeable with the Laravel
  behavioural-observation fields.
- This service is an exploratory software component, not a medical
  diagnosis system.

---

## Model Evaluation & End-to-End Verification (GitHub Issue #26)

### 1. Saved Model Artifact & Preprocessing Pipeline Inspection

Direct inspection of `models/child_intervention_pipeline.joblib` confirms:

- **Pipeline Structure:** `sklearn.pipeline.Pipeline` with two steps:
  1. `prep` (`ColumnTransformer`):
     - **Numeric transformer (`9` features):** `SimpleImputer(strategy='median')` followed by `StandardScaler()`, fitted on **`n_samples_seen_ = 1,083`** training records.
       - Features: `avg_success_first_2`, `age_years`, `ados2_social_affect`, `ados2_rrb`, `cognitive_verbal_percentile`, `cognitive_nonverbal_percentile`, `cognitive_gca_percentile`, `adaptive_composite_percentile`, `num_current_services`.
       - Learned training medians: `[32.0, 5.0, 8.0, 3.0, 13.0, 25.0, 13.0, 7.0, 2.0]`.
       - Learned training means: `[31.72, 4.61, 8.88, 3.37, 18.42, 29.03, 16.39, 9.94, 2.04]`.
     - **Categorical transformer (`5` features):** `SimpleImputer(strategy='most_frequent')` followed by `OneHotEncoder(handle_unknown='ignore', sparse_output=False)` (expanding to `23` one-hot columns; `32` total encoded features).
       - Features: `domain`, `gender`, `support_level`, `primary_domain`, `ados2_comparison_score`.
  2. `model` (`LogisticRegression`):
     - `LogisticRegression(C=0.3, class_weight='balanced', max_iter=1000, random_state=42)` with binary classes `[0, 1]` and intercept `-0.5827`.
- **Class Imbalance & Leakage Prevention:**
  - `class_weight='balanced'` adjusts class weights inversely proportional to class frequencies in the training split so the minority class is not ignored in favour of majority-class accuracy.
  - Encapsulating `SimpleImputer`, `StandardScaler`, and `OneHotEncoder` inside the scikit-learn `Pipeline` guarantees that imputation medians/modes and scaling parameters are fitted strictly on the training split (`n = 1,083`) without leaking validation or test set distributions.
- **Note on Baseline Decision Tree / Naïve Bayes Notebook & Holdout Dataset:**
  - The raw CSV dataset and exploratory training notebook (comparing Decision Tree, Gaussian/Multinomial Naïve Bayes, and the exported `LogisticRegression` pipeline) are not committed to the Git repository. Consequently, holdout test confusion matrices and numeric test-split metrics (`Accuracy`, `Precision`, `Recall`, `F1-score`, `Balanced Accuracy`, `ROC-AUC`, `PR-AUC`) from the external notebook are not fabricated here and must be referenced from the offline training notebook.

### 2. Target Variable & Caregiver-Facing Class Mapping

| Technical Output | Dataset Trajectory Grouping | Caregiver-Facing Wording |
|---|---|---|
| `Class 0` | Combines *Improving* and *Slow Progress* | **On-Track** |
| `Class 1` | Combines *Regression* and *Plateau* (At-Risk) | **May Need Additional Support** |
| `Predicted Outcome` | Binary target classification | **Developmental Progress Indicator** |
| `Class Scores` | Uncalibrated `predict_proba` outputs | **Model Assessment Scores** |

- **Audit Finding on Coefficient Polarity:** In the serialized `LogisticRegression` artifact (`classes_ = [0, 1]`), `num__avg_success_first_2` has a positive coefficient (`+0.7495`) toward class `1`. Before clinical deployment, the offline training script's `LabelEncoder` / target encoding polarity (`0` vs `1`) should be cross-checked against the dataset labels to confirm that class `1` was encoded as *At-Risk (Regression/Plateau)* during model export.

### 3. End-to-End Verification & Automated Test Record

| Test Case | Expected Result | Actual Result | Status |
|---|---|---|---|
| FastAPI health check | Service responds successfully | `GET /health` returns `{"status": "healthy", "model_loaded": true}` | **Pass** |
| Laravel-to-Python request | Laravel sends valid input and receives a response | `ChildPredictionServiceTest::test_it_sends_valid_inputs_to_the_prediction_service` sends 14 features to `/predict` and parses response | **Pass** |
| Invalid prediction input | Validation error is handled correctly | FastAPI returns HTTP `422` on missing/out-of-range/extra fields; `ChildPredictionService` throws `ValidationException` | **Pass** |
| Prediction generation | Correct prediction is returned | `POST /behavioural-records/{record}/predict` and automatic caregiver workflow return valid class (`0` or `1`) and scores summing to `1.0` | **Pass** |
| Prediction storage | Prediction is saved against the correct child and observation | Stored in `predictions` with `child_id`, `observation_id`, `model_used = child_intervention_pipeline`, `predicted_outcome`, and `confidence_score` | **Pass** |
| Prediction history | Previous predictions are displayed correctly | `Prediction History & Update Timeline` renders saved predictions with caregiver-friendly labels (`On-Track` / `May Need Additional Support`) | **Pass** |
| Recommendation generation | Appropriate recommendations are generated or retrieved | `Prediction::syncSupportRecommendations()` stores domain-matched support recommendations in `recommendations` | **Pass** |
| Automatic prediction | A new prediction is generated when required without caregiver button interaction | Opening `GET /children/{child}/predictions-analytics` with complete 14-feature assessment data automatically generates and stores the prediction | **Pass** |
| Duplicate prevention | Refreshing the page does not unnecessarily generate duplicate predictions | Second `GET /children/{child}/predictions-analytics` request with unchanged assessment/observation data makes `0` additional HTTP calls and keeps `1` prediction row | **Pass** |
| Missing assessment data | A clear message is displayed and no misleading prediction is generated | Displays *"Developmental Progress Indicator Pending Clinician Assessment"* and creates `0` predictions | **Pass** |
| Unavailable ML service | A friendly error state is displayed | When `/predict` returns HTTP `503`, displays *"Developmental Progress Analysis Temporarily Unavailable"* and stores `0` fabricated predictions | **Pass** |
| Caregiver access | Caregiver can only access their own children's records | Accessing another caregiver's child returns HTTP `404` across `show`, `predictions-analytics`, and `recommendations` | **Pass** |
| Clinician access | Clinician can access permitted assessment and prediction functions | Assigned clinician accesses `GET/PUT /clinician/observations/{record}/assessment` (`200`/`302`) and `POST /behavioural-records/{record}/predict` (`201`) | **Pass** |
| Unauthorized access | Restricted records and routes cannot be accessed | Caregiver receives HTTP `403` on clinician/admin routes; unassigned clinician receives HTTP `403` on unassigned child records | **Pass** |

### 4. AI Assistance and Independent Verification Statement

- **Aspects Involving AI Assistance:** AI pair-programming assistance (Google Antigravity) was used to inspect the serialized `child_intervention_pipeline.joblib` pipeline parameters, trace the Laravel-to-FastAPI integration, refactor `resources/views/caregiver/children/predictions-analytics.blade.php` to replace technical ML terminology with caregiver-friendly language, implement automatic prediction generation with duplicate prevention in `ChildController@predictionsAnalytics`, fix a layout rendering bug in `resources/views/layouts/app.blade.php` (`$slot ?? ''` alongside `@yield('content')`), and author automated unit/feature tests.
- **Developer Review & Independent Verification:** All code modifications in `ChildController`, `Prediction`, `layouts/app.blade.php`, `predictions-analytics.blade.php`, and `routes/web.php` are reviewed by the developer prior to staging and committing. Automated tests in `tests/Unit/ChildPredictionServiceTest.php`, `tests/Feature/BehaviouralAnalyticsDataTest.php`, and `ml-service/tests/test_api.py` were executed against the actual application codebase and model artifact (`child_intervention_pipeline.joblib`) to verify end-to-end behaviour.
- **Remaining Limitations:** Model class scores remain uncalibrated decision-support scores rather than clinically validated probabilities; automatic prediction requires a clinician to first record the specialist assessment inputs (`ados2_social_affect`, `ados2_rrb`, cognitive/adaptive percentiles, support level, and domains); and the offline training notebook/dataset is maintained outside the Git repository.
