
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
- **Data Separation, Group-Aware Splitting & Leakage Prevention:**
  - **Child-Level Separation:** Because a child can have multiple therapy goals or longitudinal sessions, training, validation, and test splits must be partitioned at the `child_id` level (`GroupShuffleSplit` / `StratifiedGroupKFold`) so records belonging to the same child never appear across both training and test sets.
  - **Temporal Integrity:** Only baseline clinical assessments and early session performance (`avg_success_first_2`, representing the first two sessions) are used as input predictors; future observations are excluded.
  - **Pipeline-Encapsulated Preprocessing:** Encapsulating `SimpleImputer`, `StandardScaler`, and `OneHotEncoder` inside the scikit-learn `Pipeline` guarantees that imputation medians/modes and scaling parameters are fitted strictly on the training split (`n = 1,083`) without leaking validation or test set distributions.
  - **Class Imbalance Handling:** `class_weight='balanced'` adjusts class weights inversely proportional to class frequencies in the training split so the minority class (*At-Risk / Regression / Plateau*) is not ignored in favour of majority-class accuracy.

### 2. Baseline Algorithms Comparison & Classification Metrics

#### 2.1 Baseline Decision Tree & Naïve Bayes vs. Exported Pipeline

| Algorithm | Role in Project Scope | Strengths on This Dataset | Limitations & Comparability Note |
|---|---|---|---|
| **Decision Tree Classifier** | Documented Baseline | Captures non-linear threshold splits (e.g., `avg_success_first_2` cutoffs) and produces human-readable decision rules | Unconstrained trees overfit small clinical subgroups; requires depth pruning (`max_depth`, `min_samples_leaf`) and `class_weight='balanced'` |
| **Naïve Bayes (Gaussian / Multinomial)** | Documented Baseline | Fast probabilistic baseline with low variance on small samples | Violates conditional independence because cognitive percentiles (`verbal`, `nonverbal`, `gca`, `adaptive`) and ADOS-2 severity scores are strongly correlated, leading to overconfident probability extremes |
| **Logistic Regression (`C=0.3`, `class_weight='balanced'`)** | Exported Production Pipeline (`child_intervention_pipeline.joblib`) | $L_2$ regularisation (`C=0.3`) handles collinearity among cognitive/adaptive percentiles, `class_weight='balanced'` counteracts class imbalance, and linear log-odds coefficients provide transparent feature attribution | Assumes a linear boundary in the 32-feature encoded space; outputs remain uncalibrated decision-support scores |

- **Note on Comparability & Holdout Dataset:** The raw CSV dataset and exploratory training notebook (comparing Decision Tree, Gaussian/Multinomial Naïve Bayes, and the exported `LogisticRegression` pipeline) are maintained outside the Git repository. Consequently, holdout test confusion matrices and numeric test-split scores are not fabricated here; any baseline-versus-improved comparison must use the identical child-grouped holdout split and 32-column `ColumnTransformer` preprocessing pipeline.

#### 2.2 Classification Metrics & Plain-Language Meaning

Because the dataset is imbalanced (fewer children in the *At-Risk: Regression / Plateau* class than the *On-Track: Improving / Slow Progress* class), **Accuracy alone can hide poor minority-class detection**. The following metrics are used to evaluate model adequacy:

| Metric | Plain-Language Explanation | Relevance to Identifying *At-Risk* (`Class 1`) Children |
|---|---|---|
| **Accuracy** | Overall proportion of children classified into the correct progress category across both classes. | Can be misleading on imbalanced data if the model mostly predicts the majority *On-Track* class. |
| **Recall / Sensitivity (`Class 1`)** | Out of all children who genuinely need additional support (*Plateau / Regression*), the percentage the model successfully flags. | **Most important metric for early support screening**—ensures children who are struggling are not missed. |
| **Precision (`Class 1`)** | When the model flags a child as *May Need Additional Support*, how often that flag is genuinely warranted. | Keeps false alarms manageable so caregivers and clinicians are not overwhelmed. |
| **F1-Score (`Class 1`)** | Harmonic balance between Precision and Recall for the *May Need Additional Support* class. | Primary single-number indicator of how well the model detects *At-Risk* children while balancing false positives. |
| **Balanced Accuracy** | Average of `Class 0` Recall and `Class 1` Recall (giving equal 50% weight to each class). | Directly measures whether `class_weight='balanced'` protects minority-class detection. |
| **ROC-AUC** | How well the model's continuous score separates *On-Track* from *At-Risk* children across all cutoff thresholds. | Measures overall ranking separation between the two trajectory groups. |
| **PR-AUC** | Trade-off between Precision and Recall across thresholds specifically for the minority *At-Risk* class. | More informative than ROC-AUC when the *At-Risk* class is a smaller fraction of the cohort. |
| **Confusion Matrix** | $2 \times 2$ table of True Positives, False Negatives, True Negatives, and False Positives. | Shows exact error types (missed support needs vs. precautionary clinician reviews). |

### 3. Target Variable & Caregiver-Facing Class Mapping

| Technical Output | Dataset Trajectory Grouping | Caregiver-Facing Wording |
|---|---|---|
| `Class 0` | Combines *Improving* and *Slow Progress* | **On-Track** |
| `Class 1` | Combines *Regression* and *Plateau* (At-Risk) | **May Need Additional Support** |
| `Predicted Outcome` | Binary target classification | **Developmental Progress Indicator** |
| `Class Scores` | Uncalibrated `predict_proba` outputs | **Model Assessment Scores** |

- **Audit Finding on Coefficient Polarity:** In the serialized `LogisticRegression` artifact (`classes_ = [0, 1]`), `num__avg_success_first_2` has a positive coefficient (`+0.7495`) toward class `1`. Before clinical deployment, the offline training script's `LabelEncoder` / target encoding polarity (`0` vs `1`) should be cross-checked against the dataset labels to confirm that class `1` was encoded as *At-Risk (Regression/Plateau)* during model export.

### 4. End-to-End Verification & Automated Test Record

| Test Case | Expected Result | Actual Result | Status |
|---|---|---|---|
| FastAPI health check | Service responds successfully | `GET /health` returns `{"status": "healthy", "model_loaded": true}` (`test_api.py::test_health_endpoint`) | **Pass** |
| Saved pipeline & leakage-safe structure | Pipeline encapsulates `prep` (`n_samples_seen_ = 1083`, 14 inputs) and `model` (`classes_ = [0, 1]`, `class_weight = 'balanced'`) | Verified directly against `child_intervention_pipeline.joblib` (`test_api.py::test_saved_pipeline_structure_and_leakage_safe_preprocessing`) | **Pass** |
| Laravel-to-Python request | Laravel sends valid input and receives a response | `ChildPredictionServiceTest::test_it_sends_valid_inputs_to_the_prediction_service` sends 14 features to `/predict` and parses response | **Pass** |
| Invalid prediction input | Validation error is handled correctly | FastAPI returns HTTP `422` on missing/out-of-range/extra fields; `ChildPredictionService` throws `ValidationException` | **Pass** |
| Prediction generation | Correct prediction is returned | `POST /behavioural-records/{record}/predict` and automatic caregiver workflow return valid class (`0` or `1`) and scores summing to `1.0` | **Pass** |
| Prediction storage | Prediction is saved against the correct child and observation | Stored in `predictions` with `child_id`, `observation_id`, `model_used = child_intervention_pipeline`, `predicted_outcome`, and `confidence_score` | **Pass** |
| Prediction history | Previous predictions are displayed correctly | `Prediction History & Update Timeline` renders saved predictions with caregiver-friendly labels (`On-Track` / `May Need Additional Support`) | **Pass** |
| Recommendation generation | Appropriate recommendations are generated or retrieved | `Prediction::syncSupportRecommendations()` stores domain-matched support recommendations in `recommendations` and supports status toggling | **Pass** |
| Automatic prediction | A new prediction is generated when required without caregiver button interaction | Opening `GET /children/{child}/predictions-analytics` with complete 14-feature assessment data automatically generates and stores the prediction | **Pass** |
| Duplicate prevention | Refreshing the page does not unnecessarily generate duplicate predictions | Second `GET /children/{child}/predictions-analytics` request with unchanged assessment/observation data makes `0` additional HTTP calls and keeps `1` prediction row | **Pass** |
| Missing assessment data | A clear message is displayed and no misleading prediction is generated | Displays *"Developmental Progress Indicator Pending Clinician Assessment"* and creates `0` predictions | **Pass** |
| Unavailable ML service | A friendly error state is displayed | When `/predict` returns HTTP `503`, displays *"Developmental Progress Analysis Temporarily Unavailable"* and stores `0` fabricated predictions | **Pass** |
| Caregiver access | Caregiver can only access their own children's records | Accessing another caregiver's child returns HTTP `404` across `show`, `predictions-analytics`, and `recommendations` | **Pass** |
| Clinician access | Clinician can access permitted assessment, prediction, and validation functions | Assigned clinician accesses `GET/PUT /clinician/observations/{record}/assessment` (`200`/`302`), `POST /behavioural-records/{record}/predict` (`201`), and `PATCH /clinician/predictions/{prediction}/validate` (`302`) | **Pass** |
| Unauthorized access | Restricted records and routes cannot be accessed | Caregiver receives HTTP `403` on clinician/admin routes; unassigned clinician receives HTTP `403` on unassigned child records | **Pass** |

### 5. AI Assistance and Independent Verification Statement

- **Aspects Involving AI Assistance:** AI pair-programming assistance (Google Antigravity) was used to inspect the serialized `child_intervention_pipeline.joblib` pipeline parameters, trace the Laravel-to-FastAPI integration, refactor `resources/views/caregiver/children/predictions-analytics.blade.php` to replace technical ML terminology with caregiver-friendly language, implement automatic prediction generation with duplicate prevention in `ChildController@predictionsAnalytics`, fix a layout rendering bug in `resources/views/layouts/app.blade.php` (`$slot ?? ''` alongside `@yield('content')`), and author automated unit/feature tests.
- **Developer Review & Independent Verification:** All code modifications in `ChildController`, `Prediction`, `layouts/app.blade.php`, `predictions-analytics.blade.php`, and `routes/web.php` are reviewed by the developer prior to staging and committing. Automated tests in `tests/Unit/ChildPredictionServiceTest.php` (`3 passed`), `tests/Feature/BehaviouralAnalyticsDataTest.php` (`12 passed`), and `ml-service/tests/test_api.py` (`6 passed`) were executed against the actual application codebase and model artifact (`child_intervention_pipeline.joblib`) to verify end-to-end behaviour.
- **Remaining Limitations:** Model class scores remain uncalibrated decision-support scores rather than clinically validated probabilities; automatic prediction requires a clinician to first record the specialist assessment inputs (`ados2_social_affect`, `ados2_rrb`, cognitive/adaptive percentiles, support level, and domains); and the offline training notebook/dataset is maintained outside the Git repository.
