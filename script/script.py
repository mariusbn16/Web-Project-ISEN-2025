from joblib import load
import sys
import pandas as pd
from sklearn.preprocessing import StandardScaler, LabelEncoder

model_rf = load("../script/model_rf.pkl") 
model_svm = load("../script/model_svm.pkl") 
model_gb = load("../script/model_gb.pkl") 
model_knn = load("../script/model_knn.pkl") 

scaler = load("../script/scaler.pkl") 
encoder = load("../script/encoder.pkl") 

numeric_cols = ["Length", "Width", "Draft","SOG", "COG"]

types = [float, float, float, float, float, float, int]  
caracteristiques = [type(arg) for type, arg in zip(types, sys.argv[1:7])]

X = pd.DataFrame([caracteristiques], columns=["Length", "Width", "Draft", "SOG", "COG", "Status"]) 
X[numeric_cols] = scaler.transform(X[numeric_cols])
X['Status'] = encoder.transform(X["Status"])  

prediction_rf = model_rf.predict(X) 
prediction_svm = model_svm.predict(X)
prediction_gb = model_gb.predict(X)
prediction_knn = model_knn.predict(X)

print(f"Type de bateau prédit : {prediction_rf[0]}")
print(f"Type de bateau prédit : {prediction_svm[0]}")
print(f"Type de bateau prédit : {prediction_gb[0]}")
print(f"Type de bateau prédit : {prediction_knn[0]}")
