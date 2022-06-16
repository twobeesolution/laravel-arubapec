<?php

namespace TwoBeeSolution\ArubaPec;

use Illuminate\Support\Facades\Config;
use SoapClient;
use SoapFault;
use SoapHeader;

class ArubaPecWsClient
{

    protected $client = null;
    protected $lastError = '';


    /**
     * Gets the last error
     * @return string Last error
     */
    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * Initializes a session on the Aruba PEC WS
     * @throws SoapFault
     */
    public function __construct()
    {
        $this->client = new SoapClient("https://areaclienti.arubapec.it/arubapec/rpc/EmailManager?wsdl", ['trace' => 1,]);
        $auth = [
            'username' => Config::get('aruba-pec.user'),
            'password' => Config::get('aruba-pec.pass'),
        ];
        $header = new SoapHeader('http://email.service.rpc.arubapec.it', 'AuthHeader', $auth);
        $this->client->__setSoapHeaders($header);
    }

    /**
     * Verifies if the username is already registered
     * @param string $name Username
     * @return bool Is the username registered?
     */
    public function verificaEsistenzaEmail(string $name): ?bool
    {
        if (Config::get('aruba-pec.dry_run', false)) {
            return true;
        }

        $client = $this->client;

        $params = [
            'nomeCasella' => $name,
            'nomeDominio' => Config::get('aruba-pec.domain')
        ];

        try {
            $response = $client->VerificaEsistenzaEmail($params);
        } catch (SoapFault $e) {
            $this->lastError = $e->getMessage();
            return null;
        }

        if ($response->out->errorNum > 0) {
            $this->lastError = $response->out->errorDesc;
            return null;
        } else {
            return ($response->out->returnValue == 1);
        }
    }

    /**
     * Verifies is a person already has a user information record
     *
     * @param string $codice_fiscale Person's codice fiscale
     * @return bool Is the person registered?
     */
    public function verificaEsistenzaAnagrafica(string $codice_fiscale): ?bool
    {
        if (Config::get('aruba-pec.dry_run', false)) {
            return true;
        }

        $params = [
            'codiceFiscale' => $codice_fiscale
        ];

        $client = $this->client;

        try {
            $response = $client->SelezionaTitolare($params);
        } catch (SoapFault $e) {
            $this->lastError = $e->getMessage();
            return null;
        }

        return ($response->out->errorNum == 0);
    }

    /**
     * Returns a person ID
     *
     * @param string $codice_fiscale Person's codice fiscale
     * @return string Person ID
     */
    public function getIdTitolare(string $codice_fiscale): ?string
    {
        if (Config::get('aruba-pec.dry_run', false)) {
            return '1';
        }

        $params = array(
            'codiceFiscale' => $codice_fiscale
        );

        $client = $this->client;

        try {
            $response = $client->SelezionaTitolare($params);
        } catch (SoapFault $e) {
            $this->lastError = $e->getMessage();
            return null;
        }

        if ($response->out->errorNum == 0) {
            return $response->out->IDTitolare;
        } else {
            $this->lastError = $response->out->errorDesc;
            return null;
        }
    }

    /**
     * Adds user information record
     *
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
    ): ?string
    {
        if (Config::get('aruba-pec.dry_run', false)) {
            return '1';
        }

        $anag = new ArubaPecAnagrafica($nome, $cognome, $email, $codiceFiscale, $indirizzo, $cap, $loc, $provincia, $nazione, $telefono, $fax, $cellulare);

        $client = $this->client;

        try {
            $response = $client->InserisciTitolare_v2($anag->toArray());
        } catch (SoapFault $e) {
            $this->lastError = $e->getMessage();
            return null;
        }

        if ($response->out->errorNum == 0) {
            //Ritorno l'id dell'utente
            return $response->out->returnValue;
        } else {
            $this->lastError = $response->out->errorDesc;
            return null;
        }
    }

    /**
     * Creates a PEC Box
     *
     * @param $id_titolare string Person id
     * @param $nome_casella string Mailbox name
     * @param $email_recupero string Mailbox password recovery email
     * @return bool Stato di creazione della casella di posta
     */
    public function creaCasella($id_titolare, $nome_casella, $email_recupero)
    {
        if (Config::get('aruba-pec.dry_run', false)) {
            return true;
        }

        $client = $this->client;

        $params = [
            'idTitolare'    => $id_titolare,
            'nomeCasella'   => mb_strtolower($nome_casella),
            'emailRecupero' => $email_recupero,
            'nomeDominio'   => Config::get('aruba-pec.domain'),
            'classe'        => Config::get('aruba-pec.class'),
            'tipoRinnovo'   => Config::get('aruba-pec.renewal_type', 'T'),
            'durata'        => Config::get('aruba-pec.expires_after', '1'),
            'cigOda'        => Config::get('aruba-pec.cig'),
            'codicePa'      => Config::get('aruba-pec.codice_pa'),
        ];

        try {
            $response = $client->Certifica($params);
        } catch (SoapFault $e) {
            $this->lastError = $e->getMessage();
            return null;
        }

        if ($response->out->errorNum == 0) {
            return true;
        } else {
            $this->lastError = $response->out->errorDesc;
            return null;
        }
    }

}
