<?php

/**
 * @package Widgets
 *
 * @file Roster.php
 * This file is part of MOVIM.
 *
 * @brief The Roster widget
 *
 * @author Jaussoin Timothée <edhelas@gmail.com>
 *
 * @version 1.0
 * @date 30 August 2010
 *
 * Copyright (C)2010 MOVIM project
 *
 * See COPYING for licensing information.
 */

class ContactCard extends WidgetCommon
{

    function WidgetLoad()
    {
    	$this->addcss('contactcard.css');
		$this->registerEvent('vcard', 'onVcard');
    }

    function onVcard($contact)
    {
        $html = $this->prepareContactCard($contact);
        RPC::call('movim_fill', 'contactcard', $html);
    }

    function prepareContactCard($contact)
    {
        $gender = getGender();
        $marital = getMarital();

        $html .= '
            <form name="vcard" id="vcardform"><br />
            <h1>'.t('Profile').'</h1><br />
                <fieldset>
                    <legend>'.t('General Informations').'</legend>';
                    
            if($this->testIsSet($contact->fn))
            $html .= '<div class="element simple">
                        <label for="fn">'.t('Name').'</label>
                        <span>'.$contact->fn.'</span>
                      </div>';

            if($this->testIsSet($contact->name))                        
            $html .= '<div class="element simple">
                        <label for="name">'.t('Nickname').'</label>
                        <span>'.$contact->name.'</span>
                      </div>';

            if(
                $contact->date != '0000-00-00' 
                && $contact->date != '1970-01-01'
                && $this->testIsSet($contact->date))
            $html .= '<div class="element simple">
                        <label for="day">'.t('Date of Birth').'</label>
                        <span>'.date('j M Y',strtotime($contact->date)).'</span>
                      </div>';
            
            if($contact->gender != 'N' && $this->testIsSet($contact->gender))
            $html .= '<div class="element simple">
                        <label for="gender">'.t('Gender').'</label>
                        <span>'.$gender[$contact->gender].'</span>
                      </div>';
       
            if($contact->marital != 'none' && $this->testIsSet($contact->marital))               
            $html .= '<div class="element simple">
                        <label for="marital">'.t('Marital Status').'</label>
                        <span>'.$marital[$contact->marital].'</span>
                      </div>';
         
            if($this->testIsSet($contact->email))
            $html .= '<div class="element simple">
                        <label for="url">'.t('Email').'</label>
                        <a target="_blank" href="mailto:'.$contact->email.'">'.$contact->email.'</a>
                      </div>';
            if($this->testIsSet($contact->url))
            $html .= '<div class="element simple">
                        <label for="url">'.t('Website').'</label>
                        <a target="_blank" href="'.$contact->url.'">'.$contact->url.'</a>
                      </div>';
              
            if($this->testIsSet($contact->desc) && prepareString($contact->desc) != '')
            $html .= '<div class="element large simple">
                        <label for="desc">'.t('About Me').'</label>
                        <span>'.prepareString($contact->desc).'</span>
                      </div>';
                      
            if($this->testIsSet($contact->adrlocality) ||
               $this->testIsSet($contact->adrcountry)) {
                $html .= '</fieldset>
                            <br />
                          <fieldset>
                            <legend>'.t('Geographic Position').'</legend>';
                            
                if($this->testIsSet($contact->adrlocality)) {
                    $locality .= '<div class="element simple">
                                <label for="desc">'.t('Locality').'</label>
                                <span>'.$contact->adrlocality;
                    if($contact->adrpostalcode != 0)
                        $locality .= ' ('.$contact->adrpostalcode.')';
                    $locality .= '</span>
                              </div>';
                    
                    $html .= $locality;
                }
                            
                if($this->testIsSet($contact->adrcountry))
                $html .= '<div class="element simple">
                            <label for="desc">'.t('Country').'</label>
                            <span>'.$contact->adrcountry.'</span>
                          </div>';
            }
                      
            $html .= '</fieldset>
                      <div class="config_button" onclick="'.$this->genCallWidget("ContactSummary","ajaxRefreshVcard", "'".$contact->jid."'").'"></div>
                </form>';
        
        return $html;
    }

    function build()
    {
        $cd = new modl\ContactDAO();
        $contact = $cd->get($_GET['f']);
        ?>
        <div class="tabelem protect red" title="<?php echo t('Profile'); ?>" id="contactcard" >
            <?php
            if(isset($contact))
                echo $this->prepareContactCard($contact);
            ?>
        </div>
        <?php
    }
}