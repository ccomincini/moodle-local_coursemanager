# Course Manager Plugin for Moodle

**Version:** 1.1.0  
**Moodle Compatibility:** 3.9+  
**Type:** Local Plugin  
**License:** GPL v3  

## 📋 Description

Course Manager is a Moodle local plugin that provides REST API services for programmatically managing course sections and URL resources. It enables external applications to create, update, and manage course content using external IDs for reliable referencing.

### Key Features

- ✅ **External ID Mapping**: Reference sections and resources with your own IDs
- ✅ **RESTful API**: Standard REST endpoints for integration
- ✅ **Multilingual Support**: Messages in Italian and English
- ✅ **Error Handling**: Comprehensive validation and clear error messages
- ✅ **UTF-8 Encoding**: Proper character encoding for international content
- ✅ **Automatic Rollback**: Database consistency in case of failures

---

## 🚀 Installation

### Method 1: ZIP Upload

1. Download the plugin as `local_coursemanager.zip`
2. Go to **Site Administration > Plugins > Install plugins**
3. Upload the ZIP file
4. Follow the installation prompts

### Method 2: Manual Installation

1. Extract files to `moodle/local/coursemanager/`
2. Visit **Site Administration > Notifications**
3. Complete the installation process

### Post-Installation Setup

1. **Enable Web Services**: Site Administration > Server > Web services > Overview
2. **Create Service**: Go to External services, enable "Course Manager Service"  
3. **Generate Token**: Create a token for authorized users
4. **Set Capabilities**: Ensure users have required permissions

---

## 📡 API Functions

### Available REST Endpoints

| Function | Description | Type |
|----------|-------------|------|
| `create_section` | Create a new course section with external ID | Write |
| `update_section` | Update an existing section title | Write |
| `get_section_info` | Retrieve section information | Read |
| `add_url_resource` | Add URL resource to a section | Write |

### Required Capabilities

- `moodle/course:update` - For section management
- `moodle/course:manageactivities` - For resource creation
- `moodle/course:view` - For reading section information

---

## 🛠️ Usage Examples

### Base URL Format
```
https://your-moodle-site.com/webservice/rest/server.php
```

### Authentication
All requests require:
- `wstoken`: Your web service token
- `moodlewsrestformat`: `json`

---

### 1. Create Section

Creates a new section in a course with an external reference ID.

**Parameters:**
- `wsfunction`: `create_section`
- `courseidnumber`: Course ID number
- `sectiontitle`: Section title
- `external_id`: Your external reference ID

**Example:**
```bash
curl -X POST "https://your-moodle-site.com/webservice/rest/server.php" \
  -d "wstoken=YOUR_TOKEN" \
  -d "wsfunction=create_section" \
  -d "moodlewsrestformat=json" \
  -d "courseidnumber=COURSE123" \
  -d "sectiontitle=Webinar Cardiology 2024" \
  -d "external_id=CARD_2024_001"
```

**Response:**
```json
{
  "success": true,
  "sectionid": 156,
  "sectionnumber": 3,
  "external_id": "CARD_2024_001",
  "mapping_id": 45,
  "message": "Section created successfully"
}
```

---

### 2. Add URL Resource

Adds a URL resource to an existing section using external ID reference.

**Parameters:**
- `wsfunction`: `add_url_resource`
- `courseidnumber`: Course ID number
- `section_external_id`: External ID of target section
- `resourcetitle`: Resource title
- `resourceurl`: URL (optional, defaults to placeholder)
- `description`: Resource description (optional)
- `external_id`: External ID for the resource (optional)

**Example:**
```bash
curl -X POST "https://your-moodle-site.com/webservice/rest/server.php" \
  -d "wstoken=YOUR_TOKEN" \
  -d "wsfunction=add_url_resource" \
  -d "moodlewsrestformat=json" \
  -d "courseidnumber=COURSE123" \
  -d "section_external_id=CARD_2024_001" \
  -d "resourcetitle=Cardiology Webinar Link" \
  -d "resourceurl=https://INSERISCI.QUI.IL/LINK_CORRETTO/AL_WEBINAR" \
  -d "description=Advanced cardiology training session" \
  -d "external_id=URL_CARD_001"
```

**Response:**
```json
{
  "success": true,
  "cmid": 789,
  "instanceid": 234,
  "section_external_id": "CARD_2024_001",
  "resource_external_id": "URL_CARD_001",
  "message": "URL resource created successfully"
}
```

**Note:** Resource titles automatically get "- LINK DA MODIFICARE!!!" suffix to indicate placeholder URLs.

---

### 3. Update Section

Updates the title of an existing section using external ID.

**Parameters:**
- `wsfunction`: `update_section`
- `courseidnumber`: Course ID number
- `external_id`: External ID of section to update
- `sectiontitle`: New section title

**Example:**
```bash
curl -X POST "https://your-moodle-site.com/webservice/rest/server.php" \
  -d "wstoken=YOUR_TOKEN" \
  -d "wsfunction=update_section" \
  -d "moodlewsrestformat=json" \
  -d "courseidnumber=COURSE123" \
  -d "external_id=CARD_2024_001" \
  -d "sectiontitle=Webinar Cardiology 2024 - ADVANCED COURSE"
```

**Response:**
```json
{
  "success": true,
  "external_id": "CARD_2024_001",
  "message": "Section updated successfully"
}
```

---

### 4. Get Section Information

Retrieves information about a section using external ID.

**Parameters:**
- `wsfunction`: `get_section_info`
- `courseidnumber`: Course ID number
- `external_id`: External ID of section

**Example:**
```bash
curl -X POST "https://your-moodle-site.com/webservice/rest/server.php" \
  -d "wstoken=YOUR_TOKEN" \
  -d "wsfunction=get_section_info" \
  -d "moodlewsrestformat=json" \
  -d "courseidnumber=COURSE123" \
  -d "external_id=CARD_2024_001"
```

**Response:**
```json
{
  "sectionid": 156,
  "sectionnumber": 3,
  "sectionname": "Webinar Cardiology 2024 - ADVANCED COURSE",
  "external_id": "CARD_2024_001",
  "visible": 1,
  "timecreated": 1704067200,
  "timemodified": 1704153600
}
```

---

## 🔧 JavaScript Integration Example

```javascript
class MoodleCourseManager {
  constructor(baseUrl, token) {
    this.baseUrl = baseUrl;
    this.token = token;
  }

  async createSection(courseIdNumber, title, externalId) {
    const formData = new FormData();
    formData.append('wstoken', this.token);
    formData.append('wsfunction', 'create_section');
    formData.append('moodlewsrestformat', 'json');
    formData.append('courseidnumber', courseIdNumber);
    formData.append('sectiontitle', title);
    formData.append('external_id', externalId);

    const response = await fetch(this.baseUrl, {
      method: 'POST',
      body: formData
    });
    
    return await response.json();
  }

  async addUrlResource(courseIdNumber, sectionExternalId, title, url, description, externalId) {
    const formData = new FormData();
    formData.append('wstoken', this.token);
    formData.append('wsfunction', 'add_url_resource');
    formData.append('moodlewsrestformat', 'json');
    formData.append('courseidnumber', courseIdNumber);
    formData.append('section_external_id', sectionExternalId);
    formData.append('resourcetitle', title);
    formData.append('resourceurl', url);
    formData.append('description', description);
    formData.append('external_id', externalId);

    const response = await fetch(this.baseUrl, {
      method: 'POST',
      body: formData
    });
    
    return await response.json();
  }
}

// Usage
const manager = new MoodleCourseManager(
  'https://your-moodle-site.com/webservice/rest/server.php',
  'YOUR_TOKEN'
);

// Create section and add resource
const section = await manager.createSection(
  'COURSE123', 
  'Webinar Cardiology 2024', 
  'CARD_2024_001'
);

if (section.success) {
  const resource = await manager.addUrlResource(
    'COURSE123',
    'CARD_2024_001',
    'Cardiology Training Link',
    'https://your-webinar-platform.com/link',
    'Advanced cardiology training session',
    'URL_CARD_001'
  );
}
```

---

## ⚠️ Error Handling

The plugin provides detailed error messages for common issues:

### Common Errors

| Error | Cause | Solution |
|-------|-------|----------|
| `Course not found with idnumber: X` | Invalid course ID | Verify course exists and idnumber is correct |
| `External ID already exists for this course: X` | Duplicate external ID | Use unique external IDs within each course |
| `Section not found with external_id: X` | Invalid section external ID | Verify section was created successfully |
| `Invalid URL: "X". Must be a valid HTTPS URL.` | Malformed URL | Provide valid HTTPS URL |

### Error Response Format

```json
{
  "exception": "invalid_parameter_exception",
  "errorcode": "invalidparameter",
  "message": "External ID already exists for this course: CARD_2024_001"
}
```

---

## 🎯 Best Practices

### External ID Strategy

Use consistent naming patterns for external IDs:

**Sections:** `[SPECIALTY]_[YEAR]_[NUMBER]`
```
CARD_2024_001, PEDI_2024_002, SURG_2024_003
```

**Resources:** `URL_[SPECIALTY]_[TYPE]`
```
URL_CARD_MAIN, URL_CARD_MATERIALS, URL_CARD_QUIZ
```

### URL Management

- Use the default placeholder URL for initial creation
- Update URLs later through Moodle interface or external system
- The "- LINK DA MODIFICARE!!!" suffix helps identify placeholder links

### Error Prevention

- Always check if external IDs exist before creating
- Validate URLs before sending requests
- Handle API responses properly in your application
- Use unique external IDs across your system

---

## 🗄️ Database Schema

The plugin creates a mapping table for external ID management:

```sql
CREATE TABLE mdl_local_coursemanager_sections (
  id bigint(10) NOT NULL AUTO_INCREMENT,
  courseid bigint(10) NOT NULL,
  sectionid bigint(10) NOT NULL,
  external_id varchar(255) NOT NULL,
  timecreated bigint(10) NOT NULL,
  timemodified bigint(10) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY external_id_unique (courseid, external_id),
  UNIQUE KEY sectionid_unique (sectionid)
);
```

---

## 🐛 Troubleshooting

### Service Not Available
- Verify web services are enabled
- Check that "Course Manager Service" is active
- Ensure user has valid token for the service

### Permission Denied
- Verify user has required capabilities
- Check course enrollment and role assignments
- Confirm token is not expired

### Function Not Found
- Ensure plugin is properly installed
- Check plugin version compatibility
- Verify service functions are enabled

### Debug Mode
Enable Moodle debugging for detailed error information:
```
Site Administration > Development > Debugging
Set to "DEVELOPER: extra Moodle debug messages"
```

---

## 📞 Support

For issues, feature requests, or contributions:

1. Check the troubleshooting section above
2. Enable debug mode for detailed error messages
3. Verify all installation and configuration steps
4. Review the examples and ensure correct API usage

---

## 🔄 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.1.0 | 2024-01 | UTF-8 encoding fix, comprehensive error handling |
| 1.0.9 | 2024-01 | Manual course module creation, improved reliability |
| 1.0.4 | 2024-01 | Database table creation fixes |
| 1.0.2 | 2024-01 | Complete localization support |
| 1.0.1 | 2024-01 | Initial release |

---

## 📜 License

This plugin is licensed under the GNU General Public License v3.0. See the LICENSE file for details.

---

## 🏥 Use Case: Medical Training Platform

This plugin was specifically designed for medical training platforms managing webinar courses. The external ID system allows training management systems to maintain their own references while seamlessly integrating with Moodle's course structure.

**Perfect for:**
- Medical education platforms
- Corporate training systems  
- Webinar management integration
- Course content automation
- External LMS integration

---

*Course Manager Plugin v1.1.0 - Reliable course content management via REST API*