/*Appointments*/
SET NOCOUNT ON
--Patient Appointments

SELECT 	a.AppointmentsId,
	aset.ApptSetId,
	dbo.asGetApptSetName(ISNULL(aset.ApptSetId,0),a.AppointmentsId) ApptSetName,
	ac.ApptChainId,
	ac.Name ApptChainName,
	Date=convert(datetime,convert(char(12),a.ApptStart,1)),
	StrtHour=convert(varchar(2),datepart(hour,a.ApptStart)),
	StrtMin=convert(varchar(2),datepart(minute,a.ApptStart)),
	StpHour=convert(varchar(2),datepart(hour,a.ApptStop)),
	StpMin=convert(varchar(2),datepart(minute,a.ApptStop)),
	Facility= dff.Listname,
	a.ApptStart,
	a.ApptStop,
	dfd.Listname AS DoctorName,
	a.DoctorId,
	mlas.Description AS Status,
	at.Name AS Type,
	Case WHEN ( exists (Select PatientVisitID from PatientVisit where PatientProfileID = pp.PatientProfileId and BillStatus = 13 ) )
		THEN	'**' + RTRIM(RTRIM(pp.Last + ' ' + ISNULL(pp.Suffix,'')) + ', '+ ISNULL(pp.First,'') + ' ' + ISNULL(pp.Middle,''))
		ELSE	RTRIM(RTRIM(pp.Last + ' ' + ISNULL(pp.Suffix,'')) + ', '+ ISNULL(pp.First,'') + ' ' + ISNULL(pp.Middle,''))
	END   AS PatientName,
	pp.PatientId,
	case  WHEN ISNULL(pp.Phone1,'') = '' THEN '' ELSE dbo.FormatPhone(pp.Phone1,1) END AS Phone1,
	pp.Birthdate,
	case  WHEN ISNULL(pp.Phone2,'') = '' THEN '' ELSE dbo.FormatPhone(pp.Phone2,1) END AS Phone2,
	ISNULL(pp.Phone1Type,'') AS Phone1Type,
	ISNULL(pp.Phone2Type,'') AS Phone2Type,
	dfr.Listname AS Resource,
	convert(varchar(255), a.Notes) AS Notes,
	CASE WHEN ISNULL(a.CasesId,0) = 0 THEN mlfc.Description ELSE mlfcc.Description END AS Financial,
                CASE WHEN ISNULL(a.CasesId,0) = 0 THEN mlrs.Description ELSE ''  END AS RefSource,
	CASE WHEN ISNULL(a.CasesId,0) = 0 THEN dfr1.doctorfacilityid ELSE dfcr.DoctorFacilityId END as Refdoctorid,
	CASE WHEN ISNULL(a.CasesId,0) = 0 THEN dfr1.ListName ELSE dfcr.ListName END as RefPhysician,
                CASE WHEN ISNULL(a.CasesId,0) = 0 THEN
			case  WHEN ISNULL(dfr1.Phone1,'') = '' THEN '' ELSE dbo.FormatPhone(dfr1.Phone1,1) END
			ELSE
			CASE WHEN ISNULL(dfcr.Phone1,'') = '' THEN '' ELSE dbo.FormatPhone(dfcr.Phone1,1) END END as RefPhyPhone,
	Flag = convert(varchar(50), ?SORTBY.TEXT?),
	a.PatientVisitId  AS PatientVisitId,
             	pv.BillStatus AS VisitStatus

FROM 	Appointments a
LEFT JOIN ApptChain ac ON a.ApptChainId = ac.ApptChainId
LEFT JOIN ApptSet aset ON a.ApptSetId = aset.ApptSetId
JOIN 	DoctorFacility dff ON a.FacilityId = dff.DoctorFacilityId
JOIN 	PatientProfile pp ON a.OwnerId = pp.PatientProfileId
JOIN 	DoctorFacility dfr ON a.ResourceId = dfr.DoctorFacilityId
LEFT JOIN DoctorFacility dfr1 ON pp.RefDoctorId = dfr1.DoctorFacilityId
LEFT JOIN DoctorFacility dfd ON a.DoctorId = dfd.DoctorFacilityId
LEFT JOIN ApptType at ON a.ApptTypeId = at.ApptTypeId
LEFT JOIN Medlists mlas ON a.ApptStatusMId = mlas.MedlistsId
LEFT JOIN Medlists mlfc ON pp.FinancialClassMId = mlfc.MedlistsId
LEFT JOIN Cases c ON a.CasesId = c.CasesId
LEFT JOIN DoctorFacility dfcr ON c.ReferringDoctorId = dfcr.DoctorFacilityId
LEFT JOIN Medlists mlfcc ON c.FinancialClassMId = mlfcc.MedlistsId
LEFT JOIN Medlists mlrs ON pp.ReferenceSourceMID = mlrs.MedlistsId
LEFT JOIN patientvisit pv ON a.PatientVisitId = pv.PatientVisitId

WHERE 	ApptKind = 1 AND ISNULL(Canceled,0)  = 0 AND
	a.ApptStart >= ISNULL(?DATERANGE.DATE1?,'1/1/1900') AND a.ApptStart < dateadd(d, 1, ISNULL(?DATERANGE.DATE2?,'1/1/3000'))
	AND  --Filter on doctor
	(
	(?RESOURCE.ITEMDATA? IS NOT NULL AND a.ResourceID IN (?RESOURCE.ITEMDATA.U?)) OR
	(?RESOURCE.ITEMDATA? IS NULL)
	)
	AND  --Filter on facility
	(
	(?FACILITY.ITEMDATA? IS NOT NULL AND a.FacilityID IN (?FACILITY.ITEMDATA.U?)) OR
	(?FACILITY.ITEMDATA? IS NULL)
	)
	AND (?APPTTYPE.ITEMDATA.U? = 1 OR ?APPTTYPE.ITEMDATA.U? = 2)
--               AND dfr1.Type=1

UNION ALL

--Resource Appointments
SELECT
	a.AppointmentsId,
	NULL as ApptSetId,
	NULL as ApptSetName,
	NULL as ApptChainId,
	NULL as ApptChainName,
	Date=convert(datetime,convert(char(12),a.ApptStart,1)),
	StrtHour=convert(varchar(2),datepart(hour,a.ApptStart)),
	StrtMin=convert(varchar(2),datepart(minute,a.ApptStart)),
	StpHour=convert(varchar(2),datepart(hour,a.ApptStop)),
	StpMin=convert(varchar(2),datepart(minute,a.ApptStop)),
	Facility= dff.Listname,
	a.ApptStart,
	a.ApptStop,
	NULL AS DoctorName,
	NULL,
	NULL Status,
	at.Name AS Type,
	CASE WHEN ApptKind = 3 THEN '<Doctor/Resource>'
                          WHEN ApptKind = 5 THEN '<Block Out>'
                          ELSE '<Other>' END AS PatientName,
	NULL AS PatientId,
	'' AS Phone1,
	NULL AS Birthdate,
	'' AS Phone2,
	'' AS Phone1Type,
	'' AS Phone2Type,
	dfr.Listname AS Resource,
	convert(varchar(255), a.Notes) AS Notes,
	NULL AS Financial,
	NULL AS RefSource,
	NULL as Refdoctorid,
	NULL As RefPhysician,
	'' As RefPhyPhone,
	Flag = convert(varchar(50), ?SORTBY.TEXT?),
 	'' AS PatientVisitId,
	'' AS  VisitStatus

FROM 	Appointments a
JOIN 	DoctorFacility dff ON a.FacilityId = dff.DoctorFacilityId
JOIN 	DoctorFacility dfr ON a.ResourceId = dfr.DoctorFacilityId
LEFT JOIN ApptType at ON a.ApptTypeId = at.ApptTypeId
WHERE 	ApptKind <> 1  AND
	a.ApptStart >= ISNULL(?DATERANGE.DATE1?,'1/1/1900') AND a.ApptStart < dateadd(d, 1, ISNULL(?DATERANGE.DATE2?,'1/1/3000'))
	AND  --Filter on doctor
	(
	(?RESOURCE.ITEMDATA? IS NOT NULL AND a.ResourceID IN (?RESOURCE.ITEMDATA.U?)) OR
	(?RESOURCE.ITEMDATA? IS NULL)
	)
	AND  --Filter on facility
	(
	(?FACILITY.ITEMDATA? IS NOT NULL AND a.FacilityID IN (?FACILITY.ITEMDATA.U?)) OR
	(?FACILITY.ITEMDATA? IS NULL)
	)
	AND (?APPTTYPE.ITEMDATA.U? = 1 OR ?APPTTYPE.ITEMDATA.U? = 3)