import os
from contextlib import asynccontextmanager
from pathlib import Path
from typing import Any

import joblib
import pandas as pd
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel, ConfigDict, Field


BASE_DIR = Path(__file__).resolve().parent.parent
DEFAULT_MODEL_PATH = (
    BASE_DIR / "models" / "child_intervention_pipeline.joblib"
)
MODEL_PATH = Path(os.getenv("MODEL_PATH", str(DEFAULT_MODEL_PATH)))

EXPECTED_FEATURES = [
    "avg_success_first_2",
    "age_years",
    "ados2_social_affect",
    "ados2_rrb",
    "cognitive_verbal_percentile",
    "cognitive_nonverbal_percentile",
    "cognitive_gca_percentile",
    "adaptive_composite_percentile",
    "num_current_services",
    "domain",
    "gender",
    "support_level",
    "primary_domain",
    "ados2_comparison_score",
]


class PredictionRequest(BaseModel):
    model_config = ConfigDict(extra="forbid")

    avg_success_first_2: float = Field(ge=0, le=100)
    age_years: float = Field(ge=0, le=120)

    ados2_social_affect: float
    ados2_rrb: float

    cognitive_verbal_percentile: float = Field(ge=0, le=100)
    cognitive_nonverbal_percentile: float = Field(ge=0, le=100)
    cognitive_gca_percentile: float = Field(ge=0, le=100)
    adaptive_composite_percentile: float = Field(ge=0, le=100)

    num_current_services: int = Field(ge=0)

    domain: str = Field(min_length=1)
    gender: str = Field(min_length=1)
    support_level: str = Field(min_length=1)
    primary_domain: str = Field(min_length=1)
    ados2_comparison_score: str = Field(min_length=1)


class PredictionResponse(BaseModel):
    predicted_class: int
    class_scores: dict[str, float]
    score_interpretation: str
    disclaimer: str


model: Any = None


@asynccontextmanager
async def lifespan(app: FastAPI):
    global model

    if not MODEL_PATH.is_file():
        raise RuntimeError(f"Model file not found: {MODEL_PATH}")

    model = joblib.load(MODEL_PATH)

    if list(model.feature_names_in_) != EXPECTED_FEATURES:
        raise RuntimeError(
            "The loaded model features do not match the API input contract."
        )

    yield

    model = None


app = FastAPI(
    title="Child Data Analytics ML Prediction Service",
    description=(
        "An exploratory machine-learning service for developmental "
        "support decision-making. It is not a diagnostic tool."
    ),
    version="1.0.0",
    lifespan=lifespan,
)


@app.get("/health")
def health_check():
    return {
        "status": "healthy",
        "model_loaded": model is not None,
    }


@app.post("/predict", response_model=PredictionResponse)
def predict(request: PredictionRequest):
    if model is None:
        raise HTTPException(
            status_code=503,
            detail="The prediction model is not available.",
        )

    input_data = pd.DataFrame(
        [request.model_dump()],
        columns=EXPECTED_FEATURES,
    )

    try:
        predicted_class = int(model.predict(input_data)[0])

        if not hasattr(model, "predict_proba"):
            raise HTTPException(
                status_code=500,
                detail="The model does not support class scores.",
            )

        probabilities = model.predict_proba(input_data)[0]
        classes = model.named_steps["model"].classes_

        class_scores = {
            str(int(label)): float(score)
            for label, score in zip(classes, probabilities)
        }

        return PredictionResponse(
            predicted_class=predicted_class,
            class_scores=class_scores,
            score_interpretation=(
                "Uncalibrated model scores; they are not validated "
                "clinical probabilities."
            ),
            disclaimer=(
                "For exploratory decision support only. This result is "
                "not a diagnosis and must not replace assessment or "
                "professional judgement by a qualified practitioner."
            ),
        )

    except HTTPException:
        raise
    except Exception as exc:
        raise HTTPException(
            status_code=500,
            detail="Prediction failed. Check the input values and model.",
        ) from exc