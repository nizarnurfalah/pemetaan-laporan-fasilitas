# Class Diagram - Sistem Pemetaan Laporan Fasilitas

## Diagram dalam Format PlantUML

```plantuml
@startuml Class Diagram - Pemetaan Laporan Fasilitas

!define ABSTRACT abstract

skinparam classAttributeIconSize 0
skinparam classFontStyle bold
skinparam packageStyle rectangle

' ========================================
' FRAMEWORK CLASSES (CodeIgniter 4)
' ========================================
package "CodeIgniter Framework" {
    abstract class Controller {
        # request
        # response
        # logger
        + initController()
    }
    
    abstract class Model {
        # table : string
        # primaryKey : string
        # useAutoIncrement : bool
        # returnType : string
        # useSoftDeletes : bool
        # allowedFields : array
        # validationRules : array
        # validationMessages : array
        + find(id) : array
        + findAll() : array
        + save(data) : bool
        + delete(id) : bool
        + where(field, value) : Model
        + countAll() : int
        + countAllResults() : int
        + orderBy(field, direction) : Model
        + getInsertID() : int
    }
}

' ========================================
' BASE CONTROLLER
' ========================================
package "App\Controllers" {
    abstract class BaseController extends Controller {
        # request : CLIRequest|IncomingRequest
        # helpers : array = ['form']
        + initController(request, response, logger) : void
    }
}

' ========================================
' MODEL CLASSES
' ========================================
package "App\Models" {
    class AdminModel extends Model {
        # table : string = 'admin'
        # primaryKey : string = 'id'
        # allowedFields : array = ['username', 'password']
        # validationRules : array
        # validationMessages : array
        # beforeInsert : array = ['hashPassword']
        # beforeUpdate : array = ['hashPassword']
        --
        # hashPassword(data) : array
    }
    
    class LaporanModel extends Model {
        # table : string = 'laporan'
        # primaryKey : string = 'id'
        # allowedFields : array
        # validationRules : array
        # validationMessages : array
        --
        .. Allowed Fields ..
        - email_pelapor : string
        - jenis_kerusakan : string
        - deskripsi : string
        - foto_lokasi : string
        - latitude : decimal
        - longitude : decimal
        - status : string
        - prioritas : string
        - tgl_lapor : datetime
        - tgl_perbaikan_dijadwalkan : date
        - catatan_admin : string
        - is_verified : tinyint
        - verified_at : datetime
        - verified_by : int
        - rejection_reason : string
    }
    
    class UserModel extends Model {
        # table : string = 'users'
        # primaryKey : string = 'id'
        # allowedFields : array = ['username', 'password', 'role']
    }
}

' ========================================
' CONTROLLER CLASSES
' ========================================
package "App\Controllers" {
    class Auth extends BaseController {
        # adminModel : AdminModel
        --
        + __construct()
        + login() : view
        + prosesLogin() : redirect
        + logout() : redirect
        + register() : view
        + prosesRegister() : redirect
    }
    
    class Laporan extends BaseController {
        # laporanModel : LaporanModel
        # email : EmailService
        --
        + __construct()
        + index() : view
        + tambah() : view
        + simpan() : redirect|json
        + detail(id) : view
        + edit(id) : view
        + update(id) : redirect
        - sendEmailNotification(to, subject, body) : bool
    }
}

package "App\Controllers\Admin" {
    class Dashboard extends BaseController {
        # laporanModel : LaporanModel
        # adminModel : AdminModel
        --
        + __construct()
        + index() : view
        + daftarLaporan() : view
        + laporanBaru() : view
        + detailLaporan(id) : view
        + updateStatus(id) : redirect
        + hapusLaporan(id) : redirect
        + verifyLaporan(id, status) : redirect
        - sendEmailNotifikasi(laporan) : bool
    }
}

' ========================================
' RELATIONSHIPS
' ========================================

' Inheritance
BaseController --|> Controller
Auth --|> BaseController
Laporan --|> BaseController
Dashboard --|> BaseController

AdminModel --|> Model
LaporanModel --|> Model
UserModel --|> Model

' Composition / Association
Auth "1" *-- "1" AdminModel : uses >
Laporan "1" *-- "1" LaporanModel : uses >
Dashboard "1" *-- "1" LaporanModel : uses >
Dashboard "1" *-- "1" AdminModel : uses >

' Entity Relationships
note right of LaporanModel
  <b>Status Values:</b>
  - Baru
  - Diproses
  - Dijadwalkan
  - Selesai
  
  <b>Prioritas Values:</b>
  - tinggi
  - sedang
  - rendah
  
  <b>Jenis Kerusakan:</b>
  - Jalan Berlubang
  - Fasilitas Publik Rusak
end note

note right of AdminModel
  Admin dapat memverifikasi
  dan mengelola laporan
end note

@enduml
```

---

## Diagram Versi Text (ASCII)

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                        SISTEM PEMETAAN LAPORAN FASILITAS                            │
│                              CLASS DIAGRAM                                          │
└─────────────────────────────────────────────────────────────────────────────────────┘

                          ┌───────────────────────┐
                          │   <<abstract>>        │
                          │   CI4 Controller      │
                          ├───────────────────────┤
                          │ # request             │
                          │ # response            │
                          │ # logger              │
                          ├───────────────────────┤
                          │ + initController()    │
                          └───────────┬───────────┘
                                      │
                                      ▼
                          ┌───────────────────────┐
                          │   <<abstract>>        │
                          │   BaseController      │
                          ├───────────────────────┤
                          │ # request             │
                          │ # helpers = ['form']  │
                          ├───────────────────────┤
                          │ + initController()    │
                          └───────────┬───────────┘
                                      │
            ┌─────────────────────────┼─────────────────────────┐
            │                         │                         │
            ▼                         ▼                         ▼
┌───────────────────────┐ ┌───────────────────────┐ ┌───────────────────────────┐
│       Auth            │ │      Laporan          │ │   Admin\Dashboard         │
├───────────────────────┤ ├───────────────────────┤ ├───────────────────────────┤
│ # adminModel          │ │ # laporanModel        │ │ # laporanModel            │
│                       │ │ # email               │ │ # adminModel              │
├───────────────────────┤ ├───────────────────────┤ ├───────────────────────────┤
│ + login()             │ │ + index()             │ │ + index()                 │
│ + prosesLogin()       │ │ + tambah()            │ │ + daftarLaporan()         │
│ + logout()            │ │ + simpan()            │ │ + laporanBaru()           │
│ + register()          │ │ + detail()            │ │ + detailLaporan()         │
│ + prosesRegister()    │ │ + edit()              │ │ + updateStatus()          │
│                       │ │ + update()            │ │ + hapusLaporan()          │
│                       │ │ - sendEmailNotif()    │ │ + verifyLaporan()         │
│                       │ │                       │ │ - sendEmailNotifikasi()   │
└───────────┬───────────┘ └───────────┬───────────┘ └─────────────┬─────────────┘
            │                         │                           │
            │ uses                    │ uses                      │ uses
            ▼                         ▼                           ▼
┌───────────────────────┐ ┌───────────────────────┐ ┌───────────────────────────┐
│     AdminModel        │ │    LaporanModel       │ │      (Both Models)        │
└───────────────────────┘ └───────────────────────┘ └───────────────────────────┘


                          ┌───────────────────────┐
                          │   <<abstract>>        │
                          │     CI4 Model         │
                          ├───────────────────────┤
                          │ # table               │
                          │ # primaryKey          │
                          │ # allowedFields       │
                          │ # validationRules     │
                          ├───────────────────────┤
                          │ + find()              │
                          │ + findAll()           │
                          │ + save()              │
                          │ + delete()            │
                          │ + where()             │
                          │ + countAll()          │
                          └───────────┬───────────┘
                                      │
            ┌─────────────────────────┼─────────────────────────┐
            │                         │                         │
            ▼                         ▼                         ▼
┌───────────────────────┐ ┌───────────────────────┐ ┌───────────────────────┐
│     AdminModel        │ │    LaporanModel       │ │      UserModel        │
├───────────────────────┤ ├───────────────────────┤ ├───────────────────────┤
│ - table = 'admin'     │ │ - table = 'laporan'   │ │ - table = 'users'     │
│ - primaryKey = 'id'   │ │ - primaryKey = 'id'   │ │ - primaryKey = 'id'   │
├───────────────────────┤ ├───────────────────────┤ ├───────────────────────┤
│ Fields:               │ │ Fields:               │ │ Fields:               │
│ - username            │ │ - email_pelapor       │ │ - username            │
│ - password            │ │ - jenis_kerusakan     │ │ - password            │
├───────────────────────┤ │ - deskripsi           │ │ - role                │
│ Methods:              │ │ - foto_lokasi         │ └───────────────────────┘
│ # hashPassword()      │ │ - latitude            │
└───────────────────────┘ │ - longitude           │
                          │ - status              │
                          │ - prioritas           │
                          │ - tgl_lapor           │
                          │ - tgl_perbaikan_      │
                          │   dijadwalkan         │
                          │ - catatan_admin       │
                          │ - is_verified         │
                          │ - verified_at         │
                          │ - verified_by         │
                          │ - rejection_reason    │
                          └───────────────────────┘
```

---

## Deskripsi Class

### 1. Models (Layer Data)

| Class | Deskripsi | Table |
|-------|-----------|-------|
| **AdminModel** | Model untuk mengelola data admin/pengelola sistem | `admin` |
| **LaporanModel** | Model untuk mengelola data laporan fasilitas rusak | `laporan` |
| **UserModel** | Model untuk mengelola data pengguna umum | `users` |

### 2. Controllers (Layer Business Logic)

| Class | Deskripsi |
|-------|-----------|
| **BaseController** | Abstract controller dasar yang menyediakan fungsionalitas umum |
| **Auth** | Menangani autentikasi admin (login, logout, register) |
| **Laporan** | Menangani CRUD laporan dari sisi publik/guest |
| **Admin\Dashboard** | Menangani dashboard dan pengelolaan laporan oleh admin |

---

## Relasi Antar Class

### Inheritance (Pewarisan)
- `Auth` → `BaseController` → `Controller`
- `Laporan` → `BaseController` → `Controller`
- `Admin\Dashboard` → `BaseController` → `Controller`
- `AdminModel` → `Model`
- `LaporanModel` → `Model`
- `UserModel` → `Model`

### Association (Asosiasi)
- `Auth` menggunakan `AdminModel`
- `Laporan` menggunakan `LaporanModel`
- `Admin\Dashboard` menggunakan `LaporanModel` dan `AdminModel`

---

## Entity Relationship

```
┌─────────────┐         verifies          ┌─────────────┐
│    Admin    │ ────────────────────────► │   Laporan   │
│             │         manages           │             │
└─────────────┘                           └─────────────┘
      │                                         │
      │                                         │
      └───────── creates report ◄───────────────┘
                    (Guest/Public)
```

### Status Flow Laporan:
```
[Baru] ──► [Diproses] ──► [Dijadwalkan] ──► [Selesai]
   │            │                │
   └── [Ditolak/Rejected] ◄──────┴─── (jika tidak valid)
```

### Prioritas:
- **Tinggi** - Kerusakan urgent/berbahaya
- **Sedang** - Kerusakan perlu diperbaiki segera
- **Rendah** - Kerusakan dapat ditunda

---

## Cara Generate Diagram Visual

1. **Menggunakan PlantUML Online:**
   - Kunjungi https://www.plantuml.com/plantuml/uml
   - Copy-paste kode PlantUML di atas
   - Generate diagram

2. **VS Code Extension:**
   - Install extension "PlantUML"
   - Buka file ini dan preview diagram

3. **Command Line:**
   ```bash
   java -jar plantuml.jar CLASS_DIAGRAM.md
   ```
