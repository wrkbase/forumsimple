## PieForumSimple

A Simple discussion forum with an easy to user interace with collapse and un-collapse posts feature.

This Simple Forum has unique Topics and Replies sorting feature to suit your needs.

Easilly add any number of Topic Groups easily.


# Table of Contents

* [Installing Piebill Simple Forum](#installing-piebill-simple-forum)
* [Support](#support)
* [Reporting Discrepancies and Code Improvements](#reporting-discrepancies-and-code-improvements)

  
## Installing Piebill Simple Forum

* Download the zip file : [Download](https://github.com/wrkbase/forumsimple/archive/refs/heads/main.zip)
* Unzip the main.zip into your Web Root directory
* Edit the config.php file in forumsimple directory
   * Change the $COMPANY value to your company name.
   * Change the $CDEBUG = 0 , after you have tested all your pages are working.
   * Delete all the $TMZONE  entries except the one matching your time zone.

* If you are using XAMPP installed to C: drive on Windows?
Then make sure the below variables are pointing to the proper htdocs location:

$DRV = 'C:';

$DRVPOST = '/xampp/htdocs/forumsimple/posts';

* If you are using PieForumSimple on your godaddy or similar web hosting provider?
Set SRVSIDE variable to 1:

$SRVSIDE = 1;

then make sure that $DRV is pointing to your home/root directory on the server,
and $DRVPOST is pointing to the proper public_html/forumsimple/posts directory as below:

$DRV = '/home/atkshopwwk1'
$DRVPOST = '/public_html/forumsimple/posts'

* After you have made sure your XAMMP or other Web Server is started then go to the location:

	http://localhost/forumsimple/  OR  http://127.0.0.1/forumsimple/   to view your forums.
   
  
## Piebill Simple Forum Topic View
  ![Topic View](http://piebill.com/imgs/pieformtp.png)
  
   
## Piebill Simple Forum Reply View
  ![Reply View](http://piebill.com/imgs/pieformrp.png)
  
## Support

Looking for help? : [Contact me here](https://github.com/wrkbase/forumsimple/issues/)

## Reporting Discrepancies and Code Improvements

You can report them here : [Contact me here](https://github.com/wrkbase/forumsimple/issues/)
