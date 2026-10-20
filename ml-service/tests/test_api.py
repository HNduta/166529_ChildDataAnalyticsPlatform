import sys
from pathlib import Path

import pytest
from fastapi.testclient import TestClient

# Make the ml-service directory available for imports.
ML_SERVICE_DIR = Path(__file__).resolve().parents[1]
sys.path.insert(0, str(ML_SERVICE_DIR))

from app.main import app


@pytest.fixture()
def client():
    # Using TestClient as a context manager runs FastAPI's lifespan,
    # which loads the saved model before each test.
    with TestClient(app) as test_client:
        yield test_client


def valid_payload():
    return {
        "avg_success_first_2": 50,
        "age_years": 5,
        "ados2_social_affect": 8,
        "ados2_rrb": 4,
        "cognitive_verbal_percentile": 40,
        "cognitive_nonverbal_percentile": 55,
        "cognitive_gca_percentile": 45,
        "adaptive_composite_percentile": 50,
        "num_current_services": 2,
        "domain": "communication",
        "gender": "female",
        "support_level": "moderate",
        "primary_domain": "social",
        "ados2_comparison_score": "moderate",
    }


def test_health_endpoint(client):
    response = client.get("/health")

    assert response.status_code == 200
    assert response.json()["status"] == "healthy"
    assert response.json()["model_loaded"] is True


def test_valid_prediction(client):
    response = client.post("/predict", json=valid_payload())

    assert response.status_code == 200

    result = response.json()

    assert result["predicted_class"] in [0, 1]
    assert set(result["class_scores"].keys()) == {"0", "1"}
    assert all(
        0 <= score <= 1
        for score in result["class_scores"].values()
    )
    assert abs(sum(result["class_scores"].values()) - 1) < 1e-6
    assert "not validated clinical probabilities" in (
        result["score_interpretation"].lower()
    )


def test_missing_required_field(client):
    payload = valid_payload()
    payload.pop("age_years")

    response = client.post("/predict", json=payload)

    assert response.status_code == 422


def test_percentile_out_of_range(client):
    payload = valid_payload()
    payload["cognitive_gca_percentile"] = 150

    response = client.post("/predict", json=payload)

    assert response.status_code == 422


def test_unexpected_field_is_rejected(client):
    payload = valid_payload()
    payload["unexpected_field"] = "not allowed"

    response = client.post("/predict", json=payload)

    assert response.status_code == 422
