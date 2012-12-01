<?php

class Notification
{

    static function valid($msg)
    {
	return '
                        	           <div class="notification_globe_valid">
                                            <div class="notification_conteneur">
                                                <div class="notification_close_valid"></div>
                                                <div class="notification_content_valid">
                                                    <b>SUCCESS!</b> ' . $msg . '
                                                </div>
                                            </div>
                                        </div>
                                    ';
    }

    static function warning($msg)
    {
	return '                    
                                        <div class="notification_globe_warning">
                                            <div class="notification_conteneur">
                                                <div class="notification_close_warning"></div>
                                                <div class="notification_content_warning">
                                                    <b>WARNING:</b> ' . $msg . '
                                                </div>
                                            </div>
                                        </div>   
                                    ';
    }

    static function error($msg)
    {
	return '                    
                                        <div class="notification_globe_error">
                                            <div class="notification_conteneur">
                                                <div class="notification_close_error"></div>
                                                <div class="notification_content_error">
                                                    <b>ERROR:</b> ' . $msg . '
                                                </div>
                                            </div>
                                        </div>
                                    ';
    }

    static function information($msg)
    {
	return '                    
                                            <div class="notification_globe_info">
                                                <div class="notification_conteneur">
                                                    <div class="notification_close_info"></div>
                                                    <div class="notification_content_info">
                                                        <b>INFORMATION: </b>' . $msg . '
                                                    </div>
                                                </div>
                                            </div>
                                        ';
    }

    static function tip($msg)
    {
	return '                    
                                            <div class="notification_globe_idea">
                                                <div class="notification_conteneur">
                                                    <div class="notification_close_idea"></div>
                                                    <div class="notification_content_idea">
                                                        <b>TIP:</b> ' . $msg . '
                                                    </div>
                                                </div>
                                            </div>
                                        ';
    }

}

?>