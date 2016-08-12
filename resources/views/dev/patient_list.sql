* Patient List*/
SET NOCOUNT ON

DECLARE @Zip varchar(40)
SELECT @Zip = LTRIM(RTRIM('?ZIPCODE.TEXT.U?')) + '%';
WITH cteMedlitsPatientStatus AS
(
SELECT * FROM Medlists WHERE TableName = 'PatientProfileStatus'
)

SELECT
	PatientID, RespSameAsPatient=isnull(PatientSameAsGuarantor,0),
	TRIM(pp.Last) + ' ' + ISNULL(pp.Suffix,'') + ', ' + ISNULL(pp.First,'') + ' ' + ISNULL(pp.Middle,'')) AS PatientName,
	PatientAddr1=ISNULL(pp.Address1,NULL),
	PatientAddr2=pp.Address2,
	PatientCityState = CASE WHEN IsNULL(pp.Zip, '0')= '0' THEN '---'ELSE pp.City + ',' + pp.State END ,
	PatientCity=pp.City,
	PatientState=pp.State,
	PatientZip=ISNULL(pp.Zip,NULL),
	RTRIM(RTRIM(pr.Last + ' ' + ISNULL(pr.Suffix,'')) + ', ' + ISNULL(pr.First,'') + ' ' + ISNULL(pr.Middle,'')) AS PatientRespName,
	PatientRespAddr1=ISNULL(pr.Address1,'---'),
	PatientRespAddr2=pr.Address2,
	PatientRespCityState = CASE WHEN IsNULL(pr.Zip, '0')= '0'  THEN '---' ELSE pr.City + ',' + pr.State END ,
	PatientRespCity=pr.City,
	PatientRespState=pr.State,
	PatientRespZip= ISNULL(pr.Zip,'---'),
	FinancialClass=isnull(ml.Description,'---'),
	Doctor=ISNULL(df.ListName,'---'),
	Facility=ISNULL(df1.OrgName,'Unknown'),
	Balance=isnull(ppa.PatBalance,0)+isnull(ppa.InsBalance,0),
	pp.DeathDate,
	Status = ml1.Description,
	pp.BirthDate,
	Groupby=convert(varchar(50),?GROUPBY.TEXT?)
FROM 	PatientProfile pp
LEFT JOIN	PatientProfileAgg ppa ON pp.PatientProfileID = ppa.PatientProfileID
LEFT JOIN Guarantor pr ON pp.GuarantorID = pr.GuarantorID
LEFT JOIN MedLists ml ON pp.FinancialClassMID = ml.MedListsID
LEFT JOIN DoctorFacility df ON pp.DoctorID = df.DoctorFacilityID
LEFT JOIN DoctorFacility df1 ON pp.FacilityId = df1.DoctorFacilityID
LEFT JOIN cteMedlitsPatientStatus ml1 ON pp.PatientStatusMId = ml1.MedlistsId
WHERE  --Filter on patient
	(
	(?PATIENT.ITEMDATA? IS NOT NULL AND pp.PatientProfileID IN (?PATIENT.ITEMDATA.U?)) OR
	(?PATIENT.ITEMDATA? IS NULL)
	)
	AND  --Patient Status
	(
	(?ALLSTATUS.VALUE.U? = 2 AND ml1.MedlistsId IN (?STATUS.ITEMDATA.U?)) OR
	(?ALLSTATUS.VALUE.U? = 1)
	)
	AND  --Filter on doctor
	(
	(?DOCTOR.ITEMDATA? IS NOT NULL AND pp.DoctorID IN (?DOCTOR.ITEMDATA.U?)) OR
	(?DOCTOR.ITEMDATA? IS NULL)
	)
	AND	--Filter on financial class
	(
	(?FINANCIALCLASS.ITEMDATA.U? IS NOT NULL AND pp.FinancialClassMId = ?FINANCIALCLASS.ITEMDATA.U?) OR
	(?FINANCIALCLASS.ITEMDATA.U? IS NULL)
	)
	AND  --Filter on guarantor
	(
	(?GUARANTOR.ITEMDATA? IS NOT NULL AND pp.GuarantorID IN (?GUARANTOR.ITEMDATA.U?)) OR
	(?GUARANTOR.ITEMDATA? IS NULL)
	)
	AND  --Filter on facility
	(
	(?FACILITY.ITEMDATA? IS NOT NULL AND pp.FacilityID IN (?FACILITY.ITEMDATA.U?)) OR
	(?FACILITY.ITEMDATA? IS NULL)
	)
	AND
	(
	(?ZIPCODE.TEXT? IS NOT NULL AND ?ADDRESS.VALUE.U? = 1 AND pp.zip LIKE @Zip) OR
	(?ZIPCODE.TEXT? IS NOT NULL AND ?ADDRESS.VALUE.U? = 2 AND pr.zip LIKE @Zip) OR
	(?ZIPCODE.TEXT? IS NULL)
	)