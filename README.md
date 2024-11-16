# Assessment

**Assessment** is a web application built with **Laravel 11**, **Inertia.js**, and **Vue.js**, designed to manage medical appointments between patients and doctors. The system implements authentication and authorization with **Jetstream** and **Spatie Permissions**.

## Features

- **Authentication and authorization system**: Based on **Jetstream** and **Spatie Permissions**. Three available roles: **admin**, **patient**, and **doctor**.
- **Views**:
  - **Patient Dashboard**: Patients can view their scheduled appointments and filter them by status (pending, confirmed, or cancelled). They can also schedule new appointments with doctors.
  - **Doctor Dashboard**: Doctors can view the appointments scheduled by patients, with options to confirm or cancel them. They can also manage their schedule by creating **schedules** and **blocks** of available time.
- **Reactive Interface**: Both views are built with **Vue.js** and managed with **Inertia.js**, providing a smooth and reactive experience.
- **API with Axios**: Appointment and schedule requests are handled via **Axios**, communicating with the Laravel API routes.
- **Roles and Permissions**: Access control implementation using **Spatie Permissions**, ensuring that each user can only access the views and actions permitted for their role.

## Installation

1. **Clone the repository**:

    ```bash
    git clone <repository-url>
    cd assessment
    ```

2. **Install Node.js dependencies**:

    ```bash
    npm install
    ```

3. **Install Composer dependencies**:

    ```bash
    composer install
    ```

4. **Configure the `.env` file**:

    Run the following command to generate the `.env` file:

    ```bash
    cp .env.example .env
    ```

5. **Configure the database credentials**:

    Open the `.env` file and set your database credentials:

    ```ini
    DB_CONNECTION=mysql
    DB_HOST=your_hostname
    DB_PORT=your_port
    DB_DATABASE=your_database_name
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

    **Note**: Make sure to use the correct credentials for your own database if setting up a local environment.

6. **Generate the application key**:

    Run the following command to generate the application's private key:

    ```bash
    php artisan key:generate
    ```

7. **Run the application**:

    Run the following commands to start the server:

    ```bash
    npm run dev
    php artisan serve
    ```

8. **Access the application**:

    Open your browser and go to the URL:

    ```
    http://127.0.0.1:8000
    ```

## Roles

- **admin**: Manages the application, has access to all resources.
- **patient**: Can schedule appointments with doctors, view their scheduled appointments, and filter them.
- **doctor**: Can manage their schedule, view appointments scheduled by patients, and confirm or cancel appointments.

## Entities

### 1. **User**
- Represents the users of the system (patients and doctors).
- Fields: `name`, `email`, `password`, `role`, among others.

### 2. **Appointments**
- Represents the appointments scheduled by patients.
- Fields: `reason`, `status`, `speciality`, `patient_id`, `doctor_id`, `block_id`.

### 3. **Schedules**
- Represents the doctor's schedule, where they can define the seasons.
- Fields: `doctor_id`, `season`.

### 4. **Blocks**
- Represents the available time blocks of doctors.
- Fields: `schedule_id`, `hour_start`, `hour_end`, `disponibility`, `status`, `day`.

### 5. **Comments**
- Related to appointments, it can be used for patients or doctors to add comments to scheduled appointments.
