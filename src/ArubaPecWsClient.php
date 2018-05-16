<?php

namespace TwoBeeSolution\ArubaPec;

class ArubaPecWsClient {

    protected $client = null;
    protected $lastError = '';


    /**
     * Gets the last error
     * @return string Last error
     */
    public function getLastError() : string
    {
        return $this->lastError;
    }

    /**
     * Initializes a session on the Aruba PEC WS
     */
    public function __construct()
    {
        $this->client = new SoapClient("https://areaclienti.arubapec.it/arubapec/rpc/EmailManager?wsdl", ['trace' => 1,]);
        $auth = [
            'username' => config('aruba-pec.user'),
            'password' => config('aruba-pec.pass'),
        ];
        $header = new SoapHeader('http://email.service.rpc.arubapec.it','AuthHeader',$auth);
        $this->client->__setSoapHeaders($header);
    }

    /**
     * Verifies if the username is already registered
     * @param string $name Username
     * @return bool Is the username registered?
     */
    public function verificaEsistenzaEmail(string $name) : ?bool
    {
        if(config('aruba-pec.dry_run', false)){
            return true;
        }

        $client = $this->client;

        $params = [
            'nomeCasella' => $name,
            'nomeDominio' => config('aruba-pec.domain')
        ];

        try {
            $response = $client->VerificaEsistenzaEmail($params);
        }
        catch(SoapFault $e) {
            $this->lastError = $e->getMessage();
            return null;
        }

        if($response->out->errorNum >= 0) {
            $this->lastError = $response->out->errorDesc;
            return null;
        }
        else {
            return ($response->out->returnValue == 1);
        }
    }

    /**
     * Verifies is a person is already registered
     * @param string $codice_fiscale Person's codice fiscale
     * @return bool Is the person registered?
     */
    public function verificaEsistenzaAnagrafica(string $codice_fiscale) : ?bool
    {
        if(config('aruba-pec.dry_run', false)){
            return true;
        }

        $params = [
            'codiceFiscale' => $codice_fiscale
        ];

        $client = $this->client;

        try {
            $response = $client->SelezionaTitolare($params);
        }
        catch(SoapFault $e) {
            $this->lastError = $e->getMessage();
            return null;
        }

        return ($response->out->errorNum == 0);
    }

    /**
     * Returns a person ID
     * @param string $codice_fiscale Person's codice fiscale
     * @return string Person ID
     */
    public function getIdTitolare(string $codice_fiscale) : ?string
    {
        if(config('aruba-pec.dry_run', false)){
            return '1';
        }

        $params = array(
            'codiceFiscale'=>$codice_fiscale
        );

        $client = $this->client;

        try {
            $response = $client->SelezionaTitolare($params);
        }
        catch(SoapFault $e) {
            $this->lastError = $e->getMessage();
            return null;
        }

        if($response->out->errorNum == 0){
            return $response->out->IDTitolare;
        }
        else{
            $this->lastError = $response->out->errorDesc;
            return null;
        }
    }

    /**
     * Aggiunge un'anagrafica
     * @param string $nome
     * @param string $cognome
     * @param string $email
     * @param string $codiceFiscale
     * @param string $indirizzo
     * @param string $cap
     * @param string $loc
     * @param string $provincia
     * @param string $nazione
     * @param string $telefono
     * @param string $fax
     * @param string $cellulare
     * @return string User id
     */
    public function aggiungiAnagrafica(
        string $nome,
        string $cognome,
        string $email,
        string $codiceFiscale,
        string $indirizzo = '',
        string $cap = '',
        string $loc = '',
        string $provincia = '',
        string $nazione = '',
        string $telefono = '',
        string $fax = '',
        string $cellulare = ''
    ) : ?string
    {
        if(config('aruba-pec.dry_run', false)){
            return '1';
        }

        $anag = new ArubaPecAnagrafica($nome, $cognome, $email, $codiceFiscale, $indirizzo, $cap, $loc, $provincia, $nazione, $telefono, $fax, $cellulare);

        $client = $this->client;

        try {
            $response = $client->InserisciTitolare($anag->toArray());
        }
        catch(SoapFault $e) {
            $this->lastError = $e->getMessage();
            return null;
        }

        if($response->out->errorNum == 0){
            //Ritorno l'id dell'utente
            return $response->out->returnValue;
        }
        else{
            $this->lastError = $response->out->errorDesc;
            return null;
        }
    }

    /**
     * @param $id_titolare string ID del titolare
     * @param $nome_casella string Nome della casella di posta
     * @param $password string Password della casella di posta
     * @return bool Stato di creazione della casella di posta
     */
    public function creaCasella($id_titolare, $nome_casella, $password){
        if(config('aruba-pec.dry_run', false)){
            return true;
        }

        $client = $this->client;

        $params = [
            'idTitolare'  => $id_titolare,
            'nomeCasella' => mb_strtolower($nome_casella),
            'password'    => $password,
            'nomeDominio' => config('aruba-pec.domain'),
            'classe'      => config('aruba-pec.class'),
            'tipoRinnovo' => config('aruba-pec.renewal_type', 'T'),
            'durata'      => config('aruba-pec.expires_after', '1'),
            'cig'         => config('aruba-pec.cig'),
        ];

        try {
            $response = $client->Certifica($params);
        }
        catch(SoapFault $e) {
            $this->lastError = $e->getMessage();
            return null;
        }

        if($response->out->errorNum == 0){
            return true;
        }
        else{
            $this->lastError = $response->out->errorDesc;
            return null;
        }
    }

}