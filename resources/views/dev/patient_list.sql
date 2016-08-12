-- Patient List
SET NOCOUNT ON

DECLARE @Zip varchar(40)
SELECT @Zip = LTRIM(RTRIM('?ZIPCODE.TEXT.U?')) + '%';
WITH cteMedlitsPatientStatus AS
(
SELECT * FROM Medlists WHERE TableName = 'PatientProfileStatus'
)

SELECT
	PatientID,
  RespSameAsPatient=ISNULL(PatientSameAsGuarantor,0),
	ISNULL(RTRIM(LTRIM(pp.First)), '')+';'+ISNULL(RTRIM(LTRIM(pp.Middle)), '')+';'+ISNULL(RTRIM(LTRIM(pp.Last)), '')+';'+ISNULL(RTRIM(LTRIM(pp.Suffix)), '') AS PatientName,
	PatientAddr1=ISNULL(pp.Address1,NULL),
	PatientAddr2=ISNULL(pp.phone1, '')+';'+ISNULL(pp.EmailAddress, ''),
	ISNULL(RTRIM(LTRIM(pp.City)), '')+';'+ISNULL(RTRIM(LTRIM(pp.State)), '') AS PatientCityState,
	PatientCity=pp.City,
	PatientState=pp.State,
	PatientZip=ISNULL(pp.Zip,''),

	ISNULL(RTRIM(LTRIM(pr.First)), '')+';'+ISNULL(RTRIM(LTRIM(pr.Middle)), '')+';'+ISNULL(RTRIM(LTRIM(pr.Last)), '')+';'+ISNULL(RTRIM(LTRIM(pr.Suffix)), '') AS PatientRespName,

	PatientRespAddr1=ISNULL(pr.Address1,NULL),
	PatientRespAddr2=pr.Address2,

  ISNULL(RTRIM(LTRIM(pr.City)), '')+';'+ISNULL(RTRIM(LTRIM(pr.State)), '') AS PatientRespCityState,

	PatientRespCity=pr.City,
	PatientRespState=pr.State,
	PatientRespZip= ISNULL(pr.Zip,NULL),
	FinancialClass=isnull(ml.Description,''),
	Doctor=ISNULL(df.ListName,NULL),
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