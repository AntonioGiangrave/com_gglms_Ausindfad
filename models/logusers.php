<?php

error_reporting(E_ALL & ~E_NOTICE);

jimport('joomla.application.component.model');
jimport('joomla.user.helper');

class gglmsModellogusers extends JModel {

    private $_japp;
    protected $_db;
    private $_id_azienda;

    public function __construct($config = array()) {
        parent::__construct($config);
        $this->_japp = & JFactory::getApplication();
        $this->_db = & JFactory::getDbo();
    }

    public function __destruct() {

    }

    public function check_user($chiamata){
        $username = $chiamata['username'];
        $query = 'SELECT u.password FROM #__users AS u WHERE u.username = '.$username;
        $this->_db->setQuery($query);
        if (false === ($results = $this->_db->loadResult()))
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
        $arraypass=explode(":", $results);
        $salt=$arraypass[1];
        $password = md5(trim($chiamata['password'].$salt)).":".$salt;
        $query = 'SELECT u.id FROM #__users AS u WHERE u.username = '.$username.' AND u.password = "'.$password.'"';
        $this->_db->setQuery($query);
        if (false === ($results = $this->_db->loadResult()))
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
        $this->_id_azienda= $results;
    }
    
    public function decodifica_chiamata_azienda(){
        $data_base64 = $_GET['data'];
        $data_base64 = str_replace(' ','+',$data_base64);
        
        if (!isset($data_base64))
            throw new DomainException('No data sent', E_USER_ERROR);
        if (false === ($data = base64_decode($data_base64)))
            throw new DomainException('Invalid encoded string sent: ' . $data_base64, E_USER_ERROR);

        $xml = new DOMDocument();
        $xml->loadXML($data);
        $xml_data = array();

        $xml_data['username'] = filter_var($xml->getElementsByTagName('username')->item(0)->nodeValue, FILTER_SANITIZE_STRING);
        if (empty($xml_data['username']))
            throw new DomainException('username not set or is not valid: '.$xml_data['username'], E_USER_ERROR);

        $xml_data['password'] = filter_var($xml->getElementsByTagName('password')->item(0)->nodeValue, FILTER_SANITIZE_STRING);
        if (empty($xml_data['password']))
            throw new DomainException('password not set or is not valid: '.$xml_data['password'], E_USER_ERROR);

        $xml_data['usati'] = filter_var($xml->getElementsByTagName('usati')->item(0)->nodeValue, FILTER_SANITIZE_STRING);
        if (empty($xml_data['usati']))
            throw new DomainException('usati not set or is not valid: '.$xml_data['usati'], E_USER_ERROR);

        $xml_data['data_inizio'] = filter_var($xml->getElementsByTagName('data_inizio')->item(0)->nodeValue, FILTER_SANITIZE_STRING);
        if (empty($xml_data['data_inizio']))
            throw new DomainException('data_inizio not set or is not valid: '.$xml_data['data_inizio'], E_USER_ERROR);

        $xml_data['data_fine'] = filter_var($xml->getElementsByTagName('data_fine')->item(0)->nodeValue, FILTER_SANITIZE_STRING);
        if (empty($xml_data['data_fine']))
            throw new DomainException('data_fine not set or is not valid: '.$xml_data['data_fine'], E_USER_ERROR);

        return $xml_data;
    } 
    
    public function get_coupon_list($richiesta){
        $id_azienda = $this->_id_azienda;
        $xml_result = '<result>';

        if(!$id_azienda){
            $xml_result .= '<response>false</response>';
            $xml_result .= '</result>';
            echo $xml_result;
        } else {
            $this->_db = & JFactory::getDbo();
            $query = 'SELECT * FROM `#__gg_coupon` AS c WHERE c.id_societa ='.$id_azienda;

            if($richiesta['usati']=='true')
                $query.= ' AND c.id_utente IS NOT NULL';

            if($richiesta['data_inizio'])
                $query.= ' AND c.data_utilizzo >="'.$richiesta['data_inizio'].'"';

            if($richiesta['data_fine'])
                $query.= ' AND c.data_utilizzo <="'.$richiesta['data_fine'].'"';

                $this->_db->setQuery($query);
            if (false === ($results = $this->_db->loadAssocList()))
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);

            $xml_result .= '<response>true</response>';

            foreach ($results AS $item){
                $xml_result .= '<item>';
                    $xml_result .= '<coupon>'.$item['coupon'].'</coupon>';

                    $xml_result .= '<id_utente>'.$item['id_utente'].'</id_utente>';

                    $xml_result .= '<abilitato>'.$item['abilitato'].'</abilitato>';

                    $xml_result .= '<data_utilizzo>'.$item['data_utilizzo'].'</data_utilizzo>';

                $xml_result .= '</item>';
            }
            $xml_result .= '</result>';

            echo $xml_result;
        }
    }

    public function decodifica_chiamata_coupon(){
        $data_base64 = $_GET['data'];
        $data_base64 = str_replace(' ','+',$data_base64);

        if (!isset($data_base64))
            throw new DomainException('No data sent', E_USER_ERROR);
        if (false === ($data = base64_decode($data_base64)))
            throw new DomainException('Invalid encoded string sent: ' . $data_base64, E_USER_ERROR);

        $xml = new DOMDocument();
        $xml->loadXML($data);
        $xml_data = array();

        $xml_data['username'] = filter_var($xml->getElementsByTagName('username')->item(0)->nodeValue, FILTER_SANITIZE_STRING);
        if (empty($xml_data['username']))
            throw new DomainException('username not set or is not valid: '.$xml_data['username'], E_USER_ERROR);

        $xml_data['password'] = filter_var($xml->getElementsByTagName('password')->item(0)->nodeValue, FILTER_SANITIZE_STRING);
        if (empty($xml_data['password']))
            throw new DomainException('password not set or is not valid: '.$xml_data['password'], E_USER_ERROR);

        $xml_data['coupon'] = filter_var($xml->getElementsByTagName('coupon')->item(0)->nodeValue, FILTER_SANITIZE_STRING);
        if (empty($xml_data['coupon']))
            throw new DomainException('coupon not set or is not valid: '.$xml_data['coupon'], E_USER_ERROR);

        return $xml_data;
    }

    public function get_user_detail($richiesta){
        $id_azienda = $this->_id_azienda;
        $coupon = $richiesta['coupon'];

            $this->_db = & JFactory::getDbo();
            $query = 'SELECT * FROM #__comprofiler AS u WHERE u.user_id =
                      (SELECT c.id_utente FROM `#__gg_coupon` AS c WHERE c.id_societa ='.$id_azienda.' AND c.coupon = "'.$coupon.'")';

            $this->_db->setQuery($query);
            if (false === ($anagrafica = $this->_db->loadAssoc()))
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);

            return $anagrafica;

    }

    public function get_course_detail($richiesta){
        $id_azienda = $this->_id_azienda;
        $coupon = $richiesta['coupon'];

        $this->_db = & JFactory::getDbo();
        $query = 'SELECT * FROM #__gg_corsi AS r WHERE r.id =
                      (SELECT c.corsi_abilitati FROM `#__gg_coupon` AS c WHERE c.id_societa ='.$id_azienda.' AND c.coupon = "'.$coupon.'")';
        $this->_db->setQuery($query);
        if (false === ($dati_corso = $this->_db->loadAssoc()))
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);

        return $dati_corso;

    }

    public function get_user_track($richiesta){
        $id_azienda = $this->_id_azienda;
        $coupon = $richiesta['coupon'];

            $this->_db = & JFactory::getDbo();

            $query =	'SELECT
							e.elemento,
							DATE_FORMAT(t.data,"%d/%m/%Y - %H:%i") as data,
							SEC_TO_TIME(tview) as tview
						FROM
							#__gg_corsi AS c
						INNER JOIN #__gg_corsi_versione AS v ON c.id = v.id_corso
						INNER JOIN #__gg_moduli AS m ON m.id_corso = v.id
						INNER JOIN #__gg_elementi AS e ON e.id_modulo = m.id
						INNER JOIN #__gg_track AS t ON t.id_elemento = e.id
						WHERE t.id_utente = (SELECT p.id_utente FROM `#__gg_coupon` AS p 
						                      WHERE p.id_societa ='.$id_azienda.' 
						                      AND p.coupon = "'.$coupon.'")
						                      ORDER BY e.ordinamento ASC';

            $this->_db->setQuery($query);
            if (false === ($track = $this->_db->loadAssocList()))
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);

            return $track;
    }

    public function get_final_test($richiesta){
        $id_azienda = $this->_id_azienda;
        $coupon = $richiesta['coupon'];

        $this->_db = & JFactory::getDbo();
        $query = 'SELECT p.id_utente, p.corsi_abilitati 
                  FROM `#__gg_coupon` AS p
				  WHERE p.id_societa ='.$id_azienda.'
				  AND p.coupon = "'.$coupon.'"';
        $this->_db->setQuery($query);
        if (false === ($corso_utente = $this->_db->loadAssoc()))
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
//        FB::log($corso_utente, "corso utente");

        $query = 'SELECT q.c_date_time
                  FROM #__quiz_r_student_quiz AS q
                  WHERE q.c_quiz_id IN (
                    SELECT c.id_quiz_finale
                    FROM ihyb8_gg_corsi AS c
                    WHERE c.id = '.$corso_utente['corsi_abilitati'].')
                  AND q.c_student_id = '.$corso_utente['id_utente'].'
                  ORDER BY c_date_time ASC LIMIT 1
                  ';

        $this->_db->setQuery($query);
        if (false === ($test = $this->_db->loadAssoc()))
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
//        FB::log($query, "query");
//        FB::log($test, "risultato query");

        return $test;
    }

    public function generate_xml_log($richiesta)
    {
        $id_azienda = $this->_id_azienda;
        $xml_result = '<result>';

        if(!$id_azienda){
            $xml_result .= '<response>false</response>';
            $xml_result .= '</result>';
            echo $xml_result;
        } else {
            $xml_result .= '<response>true</response>';

            $anagrafica= $this->get_user_detail($richiesta);

                $xml_result .= '<anagrafica>';

                $xml_result .= '<nome>'. $anagrafica['cb_nome']. '</nome>';

                $xml_result .= '<cognome>'.$anagrafica['cb_cognome'].'</cognome>';

                $xml_result .= '<datadinascita>'.$anagrafica['cb_datadinascita'].'</datadinascita>';

                $xml_result .= '<luogodinascita>'.$anagrafica['cb_luogodinascita'].'</luogodinascita>';

                $xml_result .= '<provinciadinascita>'.$anagrafica['cb_provinciadinascita'].'</provinciadinascita>';

                $xml_result .= '<terminiecondizioni>'.$anagrafica['cb_terminiecondizioni'].'</terminiecondizioni>';

                $xml_result .= '<societa>'.$anagrafica['cb_societa'].'</societa>';

                $xml_result .= '<telefono>'.$anagrafica['cb_telefono'].'</telefono>';

                $xml_result .= '<residenza>'.$anagrafica['cb_residenza'].'</residenza>';

                $xml_result .= '</anagrafica>';

            $dati_corso = $this->get_course_detail($richiesta);

                $xml_result .= '<corso>';

                $xml_result .= '<id>'.$dati_corso['id'].'</id>';

                $xml_result .= '<nome>'.$dati_corso['corso'].'</nome>';

                $xml_result .= '<titoloattestato>'.$dati_corso['titoloattestato'].'</titoloattestato>';

                $xml_result .= '<descrizione>'.strip_tags($dati_corso['abstract']).'</descrizione>';

                $xml_result .= '<url>http://www.ausindfad.it/home/index.php?option=com_gglms&view=corso&id='.$dati_corso['id'].'</url>';

            $xml_result .= '</corso>';

            $track= $this->get_user_track($richiesta);
            $xml_result .= '<tracklog>';
            foreach ($track AS $item){
                $xml_result .= '<item>';
                $xml_result .= '<elemento>'.$item['elemento'].'</elemento>';

                $xml_result .= '<data>'.$item['data'].'</data>';

                $xml_result .= '<tview>'.$item['tview'].'</tview>';

                $xml_result .= '</item>';
            }
            $xml_result .= '</tracklog>';

            $test= $this->get_final_test($richiesta);

                $xml_result .= '<finaltest>';
                    $xml_result .= '<data>'.$test['c_date_time'].'</data>';
                $xml_result .= '</finaltest>';

        }
        $xml_result .= '</result>';

        echo $xml_result;
    }

}
