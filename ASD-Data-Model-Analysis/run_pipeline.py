import os
os.environ['LOKY_MAX_CPU_COUNT'] = '4'
import json
import pandas as pd
import numpy as np
import joblib
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import seaborn as sns

from sklearn.model_selection import GroupShuffleSplit, StratifiedGroupKFold, train_test_split
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import OneHotEncoder, StandardScaler
from sklearn.impute import SimpleImputer
from sklearn.tree import DecisionTreeClassifier
from sklearn.naive_bayes import GaussianNB
from sklearn.linear_model import LogisticRegression
from sklearn.ensemble import RandomForestClassifier, HistGradientBoostingClassifier
from sklearn.metrics import (
    accuracy_score, balanced_accuracy_score, precision_score, recall_score,
    f1_score, roc_auc_score, average_precision_score, confusion_matrix,
    classification_report, roc_curve, precision_recall_curve
)

# 1. SETUP DIRECTORIES & REPRODUCIBILITY
RANDOM_STATE = 42
for d in ['artifacts/models', 'artifacts/metrics', 'artifacts/plots', 'artifacts/predictions', 'artifacts/tests']:
    os.makedirs(d, exist_ok=True)

print("1. Loading datasets...")
profiles = pd.read_csv("child_profiles.csv")
progress = pd.read_csv("progress_summaries.csv")
merged_df = pd.merge(profiles, progress, on="child_id")
print(f"   Loaded: {len(profiles)} children, {len(progress)} progress records -> {len(merged_df)} merged records.")

# 2. AUDIT TARGET DERIVATION & GROUP DATA LEAKAGE (Issue A)
print("\n2. Auditing Mathematical Target Derivation & Group Leakage...")
print("   [TARGET AUDIT] change_points = avg_success_last_2 - avg_success_first_2")
naive_target = (merged_df["trend_status"] == "Regression").astype(int)
tr_df, te_df = train_test_split(merged_df, test_size=0.20, random_state=RANDOM_STATE, stratify=naive_target)
naive_overlap = len(set(tr_df["child_id"]).intersection(set(te_df["child_id"])))
pct_overlap = (naive_overlap / te_df["child_id"].nunique()) * 100
print(f"   [AUDIT FINDING] Naive split child overlap: {naive_overlap} of {te_df['child_id'].nunique()} test children ({pct_overlap:.1f}%) were in train set!")

# 3. LEAK-FREE GROUP SPLIT & PURE EARLY ASSESSMENT FEATURES (Issue A)
print("\n3. Setting up Leak-Free Group Split & Pure Early Assessment Features...")
merged_df["target_at_risk"] = merged_df["trend_status"].isin(["Regression", "Plateau"]).astype(int)

NUMERIC_FEATURES = [
    "avg_success_first_2",
    "age_years",
    "ados2_social_affect",
    "ados2_rrb",
    "cognitive_verbal_percentile",
    "cognitive_nonverbal_percentile",
    "cognitive_gca_percentile",
    "adaptive_composite_percentile",
    "num_current_services"
]
CATEGORICAL_FEATURES = ["domain", "gender", "support_level", "primary_domain", "ados2_comparison_score"]
ALL_FEATURES = NUMERIC_FEATURES + CATEGORICAL_FEATURES

X = merged_df[ALL_FEATURES]
y = merged_df["target_at_risk"]
groups = merged_df["child_id"]

gss = GroupShuffleSplit(n_splits=1, test_size=0.20, random_state=RANDOM_STATE)
train_idx, test_idx = next(gss.split(X, y, groups=groups))

X_train, X_test = X.iloc[train_idx].copy(), X.iloc[test_idx].copy()
y_train, y_test = y.iloc[train_idx].copy(), y.iloc[test_idx].copy()
train_groups, test_groups = groups.iloc[train_idx], groups.iloc[test_idx]

clean_overlap = len(set(train_groups).intersection(set(test_groups)))
assert clean_overlap == 0, "Data leakage detected: train and test share children!"
print(f"   Leak-free split verified: Train={len(X_train)} samples ({y_train.sum()} pos), Test={len(X_test)} samples ({y_test.sum()} pos). Overlap = {clean_overlap}.")

# 4. PREPROCESSOR FACTORY FUNCTION (Issue B)
def create_preprocessor():
    num_transformer = Pipeline([
        ("imputer", SimpleImputer(strategy="median")),
        ("scaler", StandardScaler())
    ])
    cat_transformer = Pipeline([
        ("imputer", SimpleImputer(strategy="most_frequent")),
        ("ohe", OneHotEncoder(handle_unknown="ignore", sparse_output=False))
    ])
    return ColumnTransformer([
        ("num", num_transformer, NUMERIC_FEATURES),
        ("cat", cat_transformer, CATEGORICAL_FEATURES)
    ])

# 5. MODEL FAMILIES
models = {
    "Decision Tree (Baseline)": DecisionTreeClassifier(max_depth=6, min_samples_leaf=10, class_weight="balanced", random_state=RANDOM_STATE),
    "Gaussian NB (Baseline)": GaussianNB(),
    "Logistic Regression (Balanced)": LogisticRegression(class_weight="balanced", max_iter=1000, C=0.3, random_state=RANDOM_STATE),
    "Random Forest (Balanced)": RandomForestClassifier(n_estimators=150, max_depth=6, min_samples_leaf=4, class_weight="balanced", random_state=RANDOM_STATE),
    "HistGradientBoosting": HistGradientBoostingClassifier(max_iter=100, max_depth=4, class_weight="balanced", random_state=RANDOM_STATE)
}

print("\n4. Fitting models using create_preprocessor() and computing Test Metrics...")
metrics_records = []
fitted_pipelines = {}
test_predictions_dict = {"y_actual": y_test.values}

for name, clf in models.items():
    pipe = Pipeline([("prep", create_preprocessor()), ("model", clf)])
    pipe.fit(X_train, y_train)
    fitted_pipelines[name] = pipe
    
    preds = pipe.predict(X_test)
    probs = pipe.predict_proba(X_test)[:, 1]
    
    test_predictions_dict[f"{name}_pred"] = preds
    test_predictions_dict[f"{name}_prob"] = probs
    
    acc = accuracy_score(y_test, preds)
    bacc = balanced_accuracy_score(y_test, preds)
    prec = precision_score(y_test, preds, zero_division=0)
    rec = recall_score(y_test, preds, zero_division=0)
    f1 = f1_score(y_test, preds, zero_division=0)
    roc = roc_auc_score(y_test, probs)
    pr_auc = average_precision_score(y_test, probs)
    
    metrics_records.append({
        "Model": name,
        "Accuracy": round(acc, 4),
        "Balanced_Accuracy": round(bacc, 4),
        "Precision": round(prec, 4),
        "Recall": round(rec, 4),
        "F1_Score": round(f1, 4),
        "ROC_AUC": round(roc, 4),
        "PR_AUC": round(pr_auc, 4)
    })

metrics_df = pd.DataFrame(metrics_records)
print(metrics_df.to_string(index=False))

# 6. 5-FOLD STRATIFIED GROUP CROSS-VALIDATION (Issue B Fixed)
print("\n5. Running 5-Fold Stratified Group Cross-Validation with fresh preprocessors...")
sgkf = StratifiedGroupKFold(n_splits=5)
cv_records = []
for name, clf in models.items():
    cv_rocs, cv_praucs, cv_f1s = [], [], []
    for f_tr, f_val in sgkf.split(X_train, y_train, groups=train_groups):
        pipe = Pipeline([("prep", create_preprocessor()), ("model", clf)])
        pipe.fit(X_train.iloc[f_tr], y_train.iloc[f_tr])
        val_probs = pipe.predict_proba(X_train.iloc[f_val])[:, 1]
        val_preds = pipe.predict(X_train.iloc[f_val])
        cv_rocs.append(roc_auc_score(y_train.iloc[f_val], val_probs))
        cv_praucs.append(average_precision_score(y_train.iloc[f_val], val_probs))
        cv_f1s.append(f1_score(y_train.iloc[f_val], val_preds, zero_division=0))
    cv_records.append({
        "Model": name,
        "CV_ROC_AUC_Mean": round(np.mean(cv_rocs), 4),
        "CV_ROC_AUC_Std": round(np.std(cv_rocs), 4),
        "CV_PR_AUC_Mean": round(np.mean(cv_praucs), 4),
        "CV_PR_AUC_Std": round(np.std(cv_praucs), 4),
        "CV_F1_Mean": round(np.mean(cv_f1s), 4)
    })

cv_df = pd.DataFrame(cv_records)
full_metrics_df = pd.merge(metrics_df, cv_df, on="Model")
full_metrics_df.to_csv("artifacts/metrics/model_comparison_metrics.csv", index=False)
print("   Metrics saved to artifacts/metrics/model_comparison_metrics.csv")

# 7. EXPERIMENTAL THRESHOLD ANALYSIS (Issue C)
print("\n6. Experimental Decision Threshold Analysis (Supporting Caregiver-Clinician Discussion)...")
lr_probs = fitted_pipelines["Logistic Regression (Balanced)"].predict_proba(X_test)[:, 1]
threshold_records = []
for th in np.arange(0.25, 0.80, 0.05):
    th_preds = (lr_probs >= th).astype(int)
    threshold_records.append({
        "Experimental_Threshold": round(th, 2),
        "Precision": round(precision_score(y_test, th_preds, zero_division=0), 4),
        "Recall": round(recall_score(y_test, th_preds, zero_division=0), 4),
        "F1": round(f1_score(y_test, th_preds, zero_division=0), 4),
        "Alerts_Triggered": int(th_preds.sum())
    })
th_df = pd.DataFrame(threshold_records)
print(th_df.to_string(index=False))

# 8. TESTING SUITE WITH ISSUES E ENHANCEMENTS
print("\n7. Executing Sprint II Testing Suite (with specific exception & reload checks)...")
class ClinicalInputValidationError(ValueError):
    """Dedicated validation exception (Issue E)."""
    pass

def validate_child_input(data_dict):
    errors = []
    age = data_dict.get("age_years", 0)
    if not (1 <= age <= 18):
        errors.append(f"Invalid age_years: {age}. Must be 1-18.")
    for pct_col in ["cognitive_verbal_percentile", "cognitive_nonverbal_percentile", "cognitive_gca_percentile", "adaptive_composite_percentile"]:
        val = data_dict.get(pct_col, 50)
        if val is not None and not np.isnan(val):
            if not (0 <= val <= 100):
                errors.append(f"Invalid {pct_col}: {val}. Must be 0-100.")
    if not (0 <= data_dict.get("avg_success_first_2", 0) <= 100):
        errors.append(f"Invalid avg_success_first_2: {data_dict.get('avg_success_first_2')}. Must be 0-100.")
    if errors:
        raise ClinicalInputValidationError(f"Clinical input validation error: {'; '.join(errors)}")
    return True

test_log = []
best_model = fitted_pipelines["Logistic Regression (Balanced)"]
sample_valid = X_test.iloc[0].to_dict()

# TC-01: Expected Inference
try:
    validate_child_input(sample_valid)
    prob_val = float(best_model.predict_proba(pd.DataFrame([sample_valid]))[0, 1])
    test_log.append({"Test_ID": "TC-01", "Test_Name": "Expected Inference Workflow", "Condition": "Valid input", "Expected": "Probability in [0, 1]", "Actual": f"Probability = {prob_val:.3f}", "Status": "PASS", "Corrective_Action": "None required"})
except Exception as e:
    test_log.append({"Test_ID": "TC-01", "Test_Name": "Expected Inference Workflow", "Condition": "Valid input", "Expected": "p in [0, 1]", "Actual": str(e), "Status": "FAIL", "Corrective_Action": "Fix inference"})

# TC-02: Boundary Extremes
sample_extreme = sample_valid.copy()
sample_extreme["age_years"] = 2
sample_extreme["avg_success_first_2"] = 100.0
sample_extreme["cognitive_verbal_percentile"] = 0
try:
    validate_child_input(sample_extreme)
    prob_ext = float(best_model.predict_proba(pd.DataFrame([sample_extreme]))[0, 1])
    test_log.append({"Test_ID": "TC-02", "Test_Name": "Boundary Extremes", "Condition": "Age=2, Percentile=0, Success=100%", "Expected": "Safe execution", "Actual": f"Handled safely: p={prob_ext:.3f}", "Status": "PASS", "Corrective_Action": "None required"})
except Exception as e:
    test_log.append({"Test_ID": "TC-02", "Test_Name": "Boundary Extremes", "Condition": "Boundary extremes", "Expected": "Safe execution", "Actual": str(e), "Status": "FAIL", "Corrective_Action": "Handle bounds"})

# TC-03: Invalid Input with Specific Exception (Issue E)
sample_inv = sample_valid.copy()
sample_inv["cognitive_gca_percentile"] = 150
try:
    validate_child_input(sample_inv)
    test_log.append({"Test_ID": "TC-03", "Test_Name": "Invalid Input Interception", "Condition": "Percentile=150", "Expected": "ClinicalInputValidationError raised", "Actual": "Not caught", "Status": "FAIL", "Corrective_Action": "Enforce validator"})
except ClinicalInputValidationError as cve:
    assert "clinical input validation error" in str(cve).lower()
    test_log.append({"Test_ID": "TC-03", "Test_Name": "Invalid Input Interception", "Condition": "Percentile=150", "Expected": "ClinicalInputValidationError raised", "Actual": f"Caught ClinicalInputValidationError ('{str(cve)[:45]}...')", "Status": "PASS", "Corrective_Action": "Verified error originates directly from input validator (Issue E)"})
except Exception as unexpected:
    test_log.append({"Test_ID": "TC-03", "Test_Name": "Invalid Input Interception", "Condition": "Percentile=150", "Expected": "ClinicalInputValidationError", "Actual": f"Wrong error: {type(unexpected)}", "Status": "FAIL", "Corrective_Action": "Raise ClinicalInputValidationError"})

# TC-04: Missing & Novel Categories
sample_unseen = sample_valid.copy()
sample_unseen["gender"] = "Novel_Category"
sample_unseen["cognitive_verbal_percentile"] = np.nan
try:
    validate_child_input(sample_unseen)
    prob_uns = float(best_model.predict_proba(pd.DataFrame([sample_unseen]))[0, 1])
    test_log.append({"Test_ID": "TC-04", "Test_Name": "Missing & Novel Categories", "Condition": "NaN percentile + novel category", "Expected": "Imputed & predicted safely", "Actual": f"Handled safely: p={prob_uns:.3f}", "Status": "PASS", "Corrective_Action": "Configured imputer and handle_unknown='ignore'"})
except Exception as e:
    test_log.append({"Test_ID": "TC-04", "Test_Name": "Missing & Novel Categories", "Condition": "Missing/novel", "Expected": "Impute & predict", "Actual": str(e), "Status": "FAIL", "Corrective_Action": "Fix pipeline imputation"})

# TC-05: Zero Data Leakage
leak_count = len(set(train_groups).intersection(set(test_groups)))
if leak_count == 0:
    test_log.append({"Test_ID": "TC-05", "Test_Name": "Data Leakage Independence Assert", "Condition": "GroupShuffleSplit on child_id", "Expected": "0 overlapping children", "Actual": "Strictly 0 overlapping children (0.0% overlap)", "Status": "PASS", "Corrective_Action": "Enforced GroupShuffleSplit"})
else:
    test_log.append({"Test_ID": "TC-05", "Test_Name": "Data Leakage Independence Assert", "Condition": "Group split", "Expected": "0 overlap", "Actual": f"{leak_count} overlap", "Status": "FAIL", "Corrective_Action": "Fix partition"})

# TC-06: Model Serialization & Reload Verification (Issue E)
PRIMARY_PIPELINE_PATH = "artifacts/models/child_intervention_pipeline.joblib"
joblib.dump(best_model, PRIMARY_PIPELINE_PATH)
reloaded_pipeline = joblib.load(PRIMARY_PIPELINE_PATH)
p_orig = float(best_model.predict_proba(pd.DataFrame([sample_valid]))[0, 1])
p_reloaded = float(reloaded_pipeline.predict_proba(pd.DataFrame([sample_valid]))[0, 1])

if np.isclose(p_orig, p_reloaded, atol=1e-7):
    test_log.append({"Test_ID": "TC-06", "Test_Name": "Model Serialization & Reload Consistency", "Condition": "Save to .joblib and reload via joblib.load()", "Expected": "p_original == p_reloaded", "Actual": f"p_original={p_orig:.4f}, p_reloaded={p_reloaded:.4f} (delta = 0.0)", "Status": "PASS", "Corrective_Action": "Verified single-file pipeline serialization for Laravel integration (Issue E)"})
else:
    test_log.append({"Test_ID": "TC-06", "Test_Name": "Model Serialization & Reload Consistency", "Condition": "Save and reload", "Expected": "Equal probabilities", "Actual": "Mismatch", "Status": "FAIL", "Corrective_Action": "Fix serialization"})

test_df = pd.DataFrame(test_log)
test_df.to_csv("artifacts/tests/initial_testing_results.csv", index=False)
print(test_df.to_string(index=False))

# 9. PRIMARY DEPLOYMENT ARTIFACT EXPORT (Issue D)
print("\n8. Primary Deployment Artifact Export (Issue D)...")
print(f"   -> PRIMARY DEPLOYMENT MODEL: {PRIMARY_PIPELINE_PATH} ({os.path.getsize(PRIMARY_PIPELINE_PATH) / 1024:.1f} KB)")
joblib.dump(fitted_pipelines["Decision Tree (Baseline)"], "artifacts/models/baseline_dt.joblib")
joblib.dump(fitted_pipelines["Gaussian NB (Baseline)"], "artifacts/models/baseline_nb.joblib")
joblib.dump(fitted_pipelines["Random Forest (Balanced)"], "artifacts/models/improved_random_forest.joblib")
pd.DataFrame(test_predictions_dict).to_csv("artifacts/predictions/test_set_predictions.csv", index=False)
print("Execution Complete!")
