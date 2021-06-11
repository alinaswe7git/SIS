<?php
class acceptanceDocument {  

    public $core;
    public $view;
    public $item = NULL;

    public function configView() {
        $this->view->header = TRUE;
        $this->view->footer = TRUE;
        $this->view->menu = TRUE;
        $this->view->javascript = array();
        $this->view->css = array();

        return $this->view;
    }

    public function buildView($core) {
        $this->core = $core;
    }

  public function showAcceptanceDocument($item) {
        
        $uid = $this->core->userID;
        $this->core->audit(__CLASS__, $item, $uid, " $item ");

//


        $sql="SELECT a.ID, a.FirstName,a.MiddleName,a.Surname,a.GovernmentID,a.StudyType,(SELECT PeriodStartDate FROM `periods` WHERE id=23) as startDate,(SELECT Name FROM `periods` WHERE id=23) as 'Semester',  c.StudentID,c.StudyID,c.Status,d.ID, d.Name as 'CourseName',d.ShortName FROM `basic-information` a LEFT JOIN `student-study-link` c ON c.StudentID=a.ID 
            LEFT JOIN `study` d ON d.ID=c.StudyID
             LEFT JOIN `appl_status` e ON e.student_number = a.ID
             WHERE a.ID=".$item;

               // echo $sql;

        $run = $this->core->database->doSelectQuery($sql);

      //  $count = $this->offset+1;

        while ($row = $run->fetch_assoc()) {
            $results = TRUE;
           /* $firstname = $row['FirstName'];
            $middlename = $row['MiddleName'];
            $surname = $row['Surname']; */
            $uid = $row['ID'];
            $startDate=$row['startDate'];
            $tuitionFees =  $row[''];
            $client =  $row['FirstName'].' '.$row['MiddleName'].' '.$row['Surname'];
            $studyType =  $row['StudyType'];
            $semester = $row['Semester'];
            $studentNumber = $row['StudentID'];
            $yearEnrolled = $row['Status'];
            $courseName = $row['CourseName'];
            $courseCode =$row['ShortName'];
           // $processFee=(3/100*$amount);



            echo "
<!DOCTYPE html>
<html lang='en' >
<head>
  <meta charset='UTF-8'>
  <title>Nipa Acceptance Letter</title>
  <style src='https://printjs-4de6.kxcdn.com/print.min.css'></script>
 
  <style type='text/css'>
      
                            /* Slideshow container */

                    .slideshow-container {
                        max-width: 1000px;
                        height: auto;
                        position: relative;
                        margin: auto;
                    }

                   


              




                   

                    body {
                      background: rgb(204,204,204); 
                    }
                    page {
                      background: white;
                      display: block;
                      margin: 0 auto;
                      margin-bottom: 0.5cm;
                      box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
                    }

                    page[size='A4'] {  
                      width: 21cm;
                      height: 90cm; 
                      position: relative;
                    }

                    @media print {

                        @breadcrumb{
                           display:none; 
                        }
                        @button{
                            display:none;
                        }
                       @page {
                        margin: 0cm;
                      

                      }

                      @body {
                        width:100%;
                        margin:20px;
                      }
                      
                         
                    }

                    .nipatitle{
                        color: #076d3f;
                        font-family: BloggerSans;
                        font-size:20px;
                        text-align: right;
                        margin-top:17%;
                        border-right:  solid #076d3f; 
                        padding: 20px;
                    }

                    @font-face{

                       font-family: BloggerSans;
                        src: url('../../templates/loanpro/fonts/Blogger Sans/BloggerSans-Bold.woff') format('woff');

                    }

                     @font-face{

                        font-family: Helvetica;
                        src: url('../../templates/loanpro/fonts/Helvetica/Helvetica.ttf') format('ttf');

                    }

                     .smalltext{
                        color: #A6A6A6;
                        font-family: BloggerSans;
                        text-align: right;
                        margin-right: 30px;
                        font-size: 70%;


                        
                    }

                    .letterbody{
                        margin:20%;
                                
                        height: 100%;
                    }

                    .letterdate{
                        
                        font-family: Helvetica;
                        position: absolute;
                        margin: 10%;
                        margin-right:25%;
                        margin-top: 30%;
                        
                    }

                       .lettertext{
                        
                        font-family: Helvetica;
                        font-size: 15px;
                        position: absolute;
                        margin: 12%;
                        margin-right:25%;
                        margin-top: 35%;
                        
                    }

                    .watermark{
                        font-family: BloggerSans;
                        font-size: 300px;
                        color: #076d3f;
                        opacity: 0.1;
                        transform: rotate(90deg);
                        z-index: -1;

                    }

  </style>

</head>
<body>
<button style='background-color:green; color:white; 'primary onclick='window.print();'>Print</button>
<!-- partial:index.partial.html -->
<div class='slideshow-container'>
        
        <div class='mySlides'>
           


           <page size='A4' id='page1'>
                   <div style='position: absolute; right: 0px; margin-right: 2%; height: 100%;'> 
                  <div style='display: flex;'>
                    <h3 class='nipatitle'>National <br/> Institute Of Public <br/>Administration</h3>
                    <img src='https://svgshare.com/i/RX3.svg' alt='' border='0' style=' width:170px;height:170px; padding: 30px; margin-top: 10%;' />
                    
                    </div>
                     <p class='smalltext'>
                        All Official Communication should be<br>        
                        Addressed to the <strong>Executive Director</strong>   
                         <br>
                        And Not to Individual Officers
                        
                    </p>


                    <p class='smalltext' style=' margin-top: 90%;'>
                             P.O Box 31990 <br/>
                            4810 Dushambe Road<br/>
                            Lusaka, Zambia<br/>
                          <b style='color: black;'>  T </b> +260 211 228803/4<br/>
                            233643,222480<br/>
                           <b style='color: black;'> F </b> +260 211 227213<br/>
                          <b style='color: black;'>  E </b> executivedirector@nipa.ac.zm<br/>
                          <b style='color: black;'>  W </b> www.nipa.ac.zm<br/><br/><br/>


                         <b style='color: black;'><i>  Governing Council</i></b><br/>
                            Chairperson<br/>
                            Prof. Mary S. Ngoma<br/>
                           <b style='color: black;'><i> Vice Chairperson</i></b><br/>
                            Christabel. N. Reinke (Mrs.)<br/>
                          <b style='color: black;'><i>  Members </i></b><br/>
                            Seulu A. M (Mrs.)<br/>
                             B. Chimbwali (Mr.)<br/>
                             C. Kaziya (Mr.)<br/>
                             R. G. Zyambo (Mrs.)<br/>
                             M. Silumbu (Mr.) <br/>
 
                    </p>


                    </div>

                    <div style='position: absolute; margin-top: 40%; margin:none; width: 50%;'>
                        <h1 class='watermark'>NIPA</h1>
                    </div>

                    <div id='letterbody'>
                    <p class='letterdate' id='date'></p>
                    <p class='lettertext'>
                        
                        Dear ".$client.",<br/><br/><br/>

                        <b style='text-decoration: underline;'> RE: Admission To ".$courseName." (".$courseCode.") with Student Number ".$studentNumber." </b><br/><br/>

                        We are pleased to officially inform you that the Board of Studies (BOS) of the National Institute of Public Administration (NIPA) has approved your admission to pursue a ".$courseName." (".$courseCode.") on ".$studyType." learning for ".$semester." semester. The programme starts on ".$startDate.".<br/><br/>

                        Enclosed herewith are the following:<br/><br/>

                        1)Calendar for the programme,<br/>
                        2)Tuition and other fees<br/>
                        3)Confirmation slip which you must fill in to indicate that you have both accepted<br/><br/>
                         the studentship offer and to abide by the general rules and regulations of the institute.
                        The payment may be deposited in to ZANACO Bank Bill Muster at any of the branches countrywide.
                        Please take note that during registration you will be required to provide the following:<br/><br/>

                        -National Registration Card or Passport,<br/> 
                        -Original copies of your academic certificates<br/><br/>


                        You can visit our website www.nipa.ac.zm to familiarise yourself with what the Institute is all about.
                        For further details, please do not hesitate to contact the Academic Office.
                        We are looking forward to welcoming you on 16th January, 2021.<br/><br/><br/>

                        Yours sincerely,<br/><br/>
                         
                       <img src='https://svgshare.com/i/RXR.svg' alt='signature' border='0' style=' width:30%; '/><br/><br><br>
                        Nasilele B. Nasilele<br/>
                        DEPUTY REGISTRAR – ACADEMIC<br/>
                        For/EXECUTIVE DIRECTOR 


                        <br><br><br><br> <br><br><br><br>  <br><br><br><br>  <br><br><br><br>
                                            1.PAYMENTS <br><br>
                                            
                                                
                            Tuition:   K $ADDTUITION.<br><br>
                            Registration:               K165<br>
                            Identity card:              K55<br>
                            Medical:                    K55 <br>
                            Union:                  K55<br>
                            Maintenance:                K165<br>
                            Internet:                   K220 <br>   
                            Library:                    K220<br>
                            _______________________________________<br><br>
                            TOTAL :          $TOTAL<br><br>                  

                            OTHERS FEES<br>
                            Penalty for late Registration:      K110<br>
                            Research/Project Handling Fees  K1100<br>
                            (fourth year only)<br><br>
                            Research Report Binding (4 Copies)  K660<br>
                            (fourth year only)<br><br>

                            All payments must be made at any   ZANACO Bank Branch except at ZANACO Head Office in town and at ZANACO Acacia Branch at Arcades. The nearest ZANACO branches to NIPA Main Campus are at Civic Centre, Premium House, and Government Complex.<br><br>

                            Use the Student Bill Muster Deposit form, please ensure that you indicate NIPA under Institution and correct student computer number.

                            <br><br><br><br> <br><br><br><br>  <br><br><br><br>  <br><br><br><br>
                              2.REGISTRATION <br><br>
                        (a)FULL TIME/PART TIME /DISTANCE LERNING<br><br>
                         Registration will be conducted on 16th January, 2021. Classes for full-time and part-time will commence on 8th February, 2021.<br><br> 

                        (b)DISTANCE LEARNING RESIDENTIAL SCHOOL<br><br>


                         Students are required to report for Residential school from 25th January - 5th February, 2021. Classes start at 08:00hours and end at   17:00hours during week days and from 08:00hours to 15hours on Saturdays. It is mandatory for distance learning students to attend the entire Residential School.<br><br>

                        (C) REQUESTS FOR REFUND AND DEFERMENT<br><br>


                        (An extract from page 15 of the NIPA Academic Regulations) Requests for refunds of the fees shall attract a penalty of the total fees paid, as provided below:<br><br>

                        (i)30% of the total fees paid, if the claim is made between 0 day to 29 days following the commencement of classes;<br>

                        (ii) 50% of the total fees paid, if the claim is made 30 days of the commencement of classes;<br>

                        (iii)Beyond 30 days, No requests for refunds on deferment will be entertained.

                        <br><br><br><br> <br><br><br><br>  <br><br><br><br>  <br><br><br><br>

                           ACCOMODATION <br><br>
                        (a)FULL TIME<br><br>
                        We have very limited number of bed space. Accommodation is offered on first come first save basis. Apply separate to the Accommodation Officer if you wish to be considered to Accommodation.<br>
                        The following are the fees for accommodation.<br>
                        Shared rooms    K1, 898.000 <br>
                        Single rooms    K3, 163.000<br>
                        Self-Contained  K6, 600.00<br><br>
                            
                        (a)PART TIME<br><br>
                        Accommodation is not offered to part time students.<br><br>

                        (b)DISTANCE LEARNING <br><br>
                        The following are the fees for accommodation<br>
                        Shared rooms K55.00<br>
                        Single rooms   K44.00<br>
                        Self-Contained K77.00<br><br>

                        3.EXAMINATION FEES FOR ALL PROGRAMMES<br><br>
                        Examination fees are K121 per subject and are not included to the above fees<br><br>

                        4.CATERING SERVICES<br><br>
                        The institute has Catering service providers where you can buy your meals from both main and Burma Road Campuses. 
                        NOTE: Further note that students granted advanced standing (Year Two) may be required to pay an additional amount of money as Exemption Fee.

                        <br><br><br><br> <br><br><br><br>  <br><br><br><br>  <br><br><br><br>

                          CONFIRMATION SLIP  <br><br>
                         
                        Detach and return the slip to the ACADEMIC OFFICE during registration (Attach evidence of payment Semester fees). You can also scan and email the slip to executivedirector@nipa.ac.zm <br><br> 
                         
                        I……………………………………………………………………………………<br>
                                               (Student)<br><br>
                                                 
                        Of…………………………………………………………………………………<br>
                                                (Full Address)<br><br>
                                                       
                        Accept to pay the fees <br><br><br>

                        For……………………………………………………………………………and <br> 
                                                    (Course/Programme)<br><br> 

                        To abide by Rules and Regulations of the Institute, <br><br>
                         
                        STUDENT’S SIGNATURE………………………………………<br><br>DATE………………………………..... <br><br>
                         
                         
                        SPONSOR’S DETAILS  <br><br>
                        (a)NAMES………………………………………… <br><br>(b) SIGNATURE…………………….. <br><br>

                        (a)Email Address………………………………… <br><br>(b) Mobile Number………………. <br><br>

                        Date………………………………………………...  

                    </p>
                    </div>


           </page>
        </div>

        

    

         

        

        

      
    </div>
    <br>
   
<!-- partial -->
 <script src='https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.2.61/jspdf.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js'></script>
<script src='https://printjs-4de6.kxcdn.com/print.min.js'></script>
  <script >
                    //slideshow
                      var slideIndex = 1;
                showSlides(slideIndex);

                function plusSlides(n) {
                    showSlides(slideIndex += n);
                }

                function currentSlide(n) {
                    showSlides(slideIndex = n);
                }

              

                        var d = new Date();
                        document.getElementById('date').innerHTML = d.toDateString();


                      

                        

  </script>
</body>
</html>

";
}
}
}
?>