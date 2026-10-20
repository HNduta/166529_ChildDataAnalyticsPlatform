
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
