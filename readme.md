

Module Generator for CodeIgniter 3.1.13
=======================================

This CodeIgniter 3.1.13 web application provides robust functionality for managing modules, user permissions, and seamless integration with mobile applications through API endpoints.

Features
--------

1.  **Automatic CRUD Generation System**
    *   Automatically generates Create, Read, Update, and Delete operations for modules.
    *   Simplifies data management within the application.
2.  **Module Permissions**
    *   Assign specific permissions to users for accessing modules.
    *   Ensures security and control over module functionalities based on user roles.
3.  **API Integration for Mobile Apps**
    *   Provides API endpoints to retrieve module data.
    *   Facilitates seamless data access for mobile applications, enhancing user experience and accessibility.

Getting Started
---------------

### Clone the Repository

    git clone https://github.com/ramp00786/module-generator-ci3.git
    cd module-generator-ci3

### Installation

1.  **Configure Database**: Update the database settings in `application/config/database.php`.
2.  **Server Requirements**: Ensure PHP, MySQL, and Apache/Nginx are installed and configured.

### Configuration

Set up your CodeIgniter configuration in `application/config/config.php`.

How to Use
----------

### Use in Front-End Controller

    
                /*Load model file*/
                $this->load->model("tfn");
                /*Get all data of the module*/
                $data = $this->tfn->getData('*', 'slider_3', "status = 1 ");
                /*Pass data to the view file*/
                $this->load->view('slider_view', $data );
            

### Use in Front-End View

    
            /*Create CI instance*/
            $CI =& get_instance();
            /*Load model*/
            $CI->load->model('tfn');
            /*Get Data*/
            $data = $this->tfn->getData('*', 'slider_3', "status = 1 ");
        

### Get Data Using API

    http://localhost/tulsiram_work/MG-CI-3.1.13/API/get?key=8ad677-0516f1-3c9709-18d6a6-64323c&module_slug=slider

User Guide and Demo
-------------------

*   **User Guide**: [How to Use](https://pro.intactautomation.com/module-generator/How-to-use.pdf)
*   **Demo**: [Module Generator Demo](https://pro.intactautomation.com/module-generator/login)
*   **Dashboard Screenshot**: [Dashboard](https://pro.intactautomation.com/module-generator/Module-Generator.png)

Technologies Used
-----------------

*   **CodeIgniter 3.1.13**: PHP framework for building robust web applications.
*   **Bootstrap 4**: Front-end framework for responsive design and UI components.
*   **MySQL**: Database management system for storing application data.
*   **API Integration**: Utilizes RESTful API principles for seamless mobile app integration.

Contributing
------------

Contributions are welcome! Fork the repository, make your changes, and submit a pull request.

License
-------

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

Author
------

Tulsiram Kushwah - [Website](https://codecartbazaar.intactautomation.com/tulsiram-kushwah/)
