# Esempi chiamate REST API - Course Manager Plugin

**Token:** `701c68857ed322829532f359387e4fad`  
**Corso:** `123coursemanager`  
**Base URL:** `https://test2.formazionesanitaria.it/webservice/rest/server.php`

---

## 1. 📁 Creare una sezione

### cURL:
```bash
curl -X POST "https://test2.formazionesanitaria.it/webservice/rest/server.php" \
  -d "wstoken=701c68857ed322829532f359387e4fad" \
  -d "wsfunction=create_section" \
  -d "moodlewsrestformat=json" \
  -d "courseidnumber=123coursemanager" \
  -d "sectiontitle=Webinar Gennaio 2024" \
  -d "external_id=WEB_JAN_2024_001"
```

### JavaScript:
```javascript
const createSection = async () => {
  const formData = new FormData();
  formData.append('wstoken', '701c68857ed322829532f359387e4fad');
  formData.append('wsfunction', 'create_section');
  formData.append('moodlewsrestformat', 'json');
  formData.append('courseidnumber', '123coursemanager');
  formData.append('sectiontitle', 'Webinar Gennaio 2024');
  formData.append('external_id', 'WEB_JAN_2024_001');

  const response = await fetch('https://test2.formazionesanitaria.it/webservice/rest/server.php', {
    method: 'POST',
    body: formData
  });
  
  return await response.json();
};
```

**Risposta attesa:**
```json
{
  "success": true,
  "sectionid": 156,
  "sectionnumber": 3,
  "external_id": "WEB_JAN_2024_001",
  "mapping_id": 45,
  "message": "Sezione creata con successo"
}
```

---

## 2. 🔗 Aggiungere risorsa URL alla sezione

### cURL:
```bash
curl -X POST "https://test2.formazionesanitaria.it/webservice/rest/server.php" \
  -d "wstoken=701c68857ed322829532f359387e4fad" \
  -d "wsfunction=add_url_resource" \
  -d "moodlewsrestformat=json" \
  -d "courseidnumber=123coursemanager" \
  -d "section_external_id=WEB_JAN_2024_001" \
  -d "resourcetitle=Link Webinar Formazione Sanitaria" \
  -d "resourceurl=#inserisci qui il link al webinar" \
  -d "description=Partecipa al webinar mensile di formazione sanitaria - Gennaio 2024" \
  -d "external_id=3847291"
```

### JavaScript:
```javascript
const addUrlResource = async () => {
  const formData = new FormData();
  formData.append('wstoken', 'acc6d7247c5ab04148a32eb9e60998e9');
  formData.append('wsfunction', 'add_url_resource');
  formData.append('moodlewsrestformat', 'json');
  formData.append('courseidnumber', '123coursemanager');
  formData.append('section_external_id', 'WEB_JAN_2024_001');
  formData.append('resourcetitle', 'Link Webinar Formazione Sanitaria');
  formData.append('resourceurl', '#inserisci qui il link al webinar');
  formData.append('description', 'Partecipa al webinar mensile di formazione sanitaria - Gennaio 2024');
  formData.append('external_id', '3847291');

  const response = await fetch('https://test2.formazionesanitaria.it/webservice/rest/server.php', {
    method: 'POST',
    body: formData
  });
  
  return await response.json();
};
```

**Risposta attesa:**
```json
{
  "success": true,
  "cmid": 789,
  "instanceid": 234,
  "section_external_id": "WEB_JAN_2024_001",
  "resource_external_id": "3847291",
  "message": "Risorsa URL creata con successo"
}
```

---

## 3. 📝 Aggiornare una sezione esistente

### cURL:
```bash
curl -X POST "https://test2.formazionesanitaria.it/webservice/rest/server.php" \
  -d "wstoken=acc6d7247c5ab04148a32eb9e60998e9" \
  -d "wsfunction=update_section" \
  -d "moodlewsrestformat=json" \
  -d "courseidnumber=123coursemanager" \
  -d "external_id=WEB_JAN_2024_001" \
  -d "sectiontitle=Webinar Gennaio 2024 - AGGIORNATO"
```

### JavaScript:
```javascript
const updateSection = async () => {
  const formData = new FormData();
  formData.append('wstoken', 'acc6d7247c5ab04148a32eb9e60998e9');
  formData.append('wsfunction', 'update_section');
  formData.append('moodlewsrestformat', 'json');
  formData.append('courseidnumber', '123coursemanager');
  formData.append('external_id', 'WEB_JAN_2024_001');
  formData.append('sectiontitle', 'Webinar Gennaio 2024 - AGGIORNATO');

  const response = await fetch('https://test2.formazionesanitaria.it/webservice/rest/server.php', {
    method: 'POST',
    body: formData
  });
  
  return await response.json();
};
```

**Risposta attesa:**
```json
{
  "success": true,
  "external_id": "WEB_JAN_2024_001",
  "message": "Sezione aggiornata con successo"
}
```

---

## 4. 📊 Ottenere informazioni su una sezione

### cURL:
```bash
curl -X POST "https://test2.formazionesanitaria.it/webservice/rest/server.php" \
  -d "wstoken=acc6d7247c5ab04148a32eb9e60998e9" \
  -d "wsfunction=get_section_info" \
  -d "moodlewsrestformat=json" \
  -d "courseidnumber=123coursemanager" \
  -d "external_id=WEB_JAN_2024_001"
```

### JavaScript:
```javascript
const getSectionInfo = async () => {
  const formData = new FormData();
  formData.append('wstoken', 'acc6d7247c5ab04148a32eb9e60998e9');
  formData.append('wsfunction', 'get_section_info');
  formData.append('moodlewsrestformat', 'json');
  formData.append('courseidnumber', '123coursemanager');
  formData.append('external_id', 'WEB_JAN_2024_001');

  const response = await fetch('https://test2.formazionesanitaria.it/webservice/rest/server.php', {
    method: 'POST',
    body: formData
  });
  
  return await response.json();
};
```

**Risposta attesa:**
```json
{
  "sectionid": 156,
  "sectionnumber": 3,
  "sectionname": "Webinar Gennaio 2024 - AGGIORNATO",
  "external_id": "WEB_JAN_2024_001",
  "visible": 1,
  "timecreated": 1704067200,
  "timemodified": 1704153600
}
```

---

## 5. 🔄 Workflow completo di esempio

### Scenario: Creare una seconda sezione con più risorse

```bash
# 1. Crea seconda sezione
curl -X POST "https://test2.formazionesanitaria.it/webservice/rest/server.php" \
  -d "wstoken=acc6d7247c5ab04148a32eb9e60998e9" \
  -d "wsfunction=create_section" \
  -d "moodlewsrestformat=json" \
  -d "courseidnumber=123coursemanager" \
  -d "sectiontitle=Webinar Febbraio 2024" \
  -d "external_id=WEB_FEB_2024_002"

# 2. Aggiungi prima risorsa URL
curl -X POST "https://test2.formazionesanitaria.it/webservice/rest/server.php" \
  -d "wstoken=acc6d7247c5ab04148a32eb9e60998e9" \
  -d "wsfunction=add_url_resource" \
  -d "moodlewsrestformat=json" \
  -d "courseidnumber=123coursemanager" \
  -d "section_external_id=WEB_FEB_2024_002" \
  -d "resourcetitle=Materiale Preparatorio" \
  -d "resourceurl=#inserisci qui il link al webinar" \
  -d "description=Scarica il materiale preparatorio per il webinar" \
  -d "external_id=9182736"

# 3. Aggiungi seconda risorsa URL
curl -X POST "https://test2.formazionesanitaria.it/webservice/rest/server.php" \
  -d "wstoken=acc6d7247c5ab04148a32eb9e60998e9" \
  -d "wsfunction=add_url_resource" \
  -d "moodlewsrestformat=json" \
  -d "courseidnumber=123coursemanager" \
  -d "section_external_id=WEB_FEB_2024_002" \
  -d "resourcetitle=Registrazione Webinar" \
  -d "resourceurl=#inserisci qui il link al webinar" \
  -d "description=Accedi alla registrazione del webinar" \
  -d "external_id=5047392"

# 4. Verifica info sezione
curl -X POST "https://test2.formazionesanitaria.it/webservice/rest/server.php" \
  -d "wstoken=acc6d7247c5ab04148a32eb9e60998e9" \
  -d "wsfunction=get_section_info" \
  -d "moodlewsrestformat=json" \
  -d "courseidnumber=123coursemanager" \
  -d "external_id=WEB_FEB_2024_002"
```

---

## 6. 🧪 Test di gestione errori

### Test corso non esistente:
```bash
curl -X POST "https://test2.formazionesanitaria.it/webservice/rest/server.php" \
  -d "wstoken=acc6d7247c5ab04148a32eb9e60998e9" \
  -d "wsfunction=create_section" \
  -d "moodlewsrestformat=json" \
  -d "courseidnumber=CORSO_INESISTENTE" \
  -d "sectiontitle=Test Errore" \
  -d "external_id=TEST_ERROR_001"
```

**Risposta errore attesa:**
```json
{
  "exception": "invalid_parameter_exception",
  "errorcode": "invalidparameter", 
  "message": "Corso non trovato con idnumber: CORSO_INESISTENTE"
}
```

### Test external_id duplicato:
```bash
# Prova a creare una sezione con external_id già esistente
curl -X POST "https://test2.formazionesanitaria.it/webservice/rest/server.php" \
  -d "wstoken=acc6d7247c5ab04148a32eb9e60998e9" \
  -d "wsfunction=create_section" \
  -d "moodlewsrestformat=json" \
  -d "courseidnumber=123coursemanager" \
  -d "sectiontitle=Sezione Duplicata" \
  -d "external_id=WEB_JAN_2024_001"
```

**Risposta errore attesa:**
```json
{
  "exception": "invalid_parameter_exception",
  "errorcode": "invalidparameter",
  "message": "External ID già esistente per questo corso: WEB_JAN_2024_001"
}
```

---

## 📝 Note per i test:

- **Esegui in ordine**: Prima crea le sezioni, poi aggiungi le risorse
- **External ID univoci**: Ogni sezione deve avere un external_id unico per corso
- **Token valido**: Assicurati che il token non sia scaduto
- **Permessi utente**: L'utente associato al token deve avere i permessi necessari
- **Corso esistente**: Il corso con idnumber '123coursemanager' deve esistere nel sistema