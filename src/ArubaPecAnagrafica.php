<?php

namespace TwoBeeSolution\ArubaPec;


class ArubaPecAnagrafica {
    protected $nome = '';
    protected $cognome = '';
    protected $email = '';
    protected $codiceFiscale = '';
    protected $indirizzo = '';
    protected $cap = '';
    protected $comune = '';
    protected $provincia = '';
    protected $nazione = '';
    protected $telefono = '';
    protected $fax = '';
    protected $cellulare = '';

    public function setNome (string $nome)
    {
        $this->nome = $nome;
    }
    public function setCognome (string $cognome)
    {
        $this->cognome = $cognome;
    }
    public function setEmail (string $email)
    {
        $this->email = $email;
    }
    public function setCodiceFiscale (string $codiceFiscale)
    {
        $this->codiceFiscale = $codiceFiscale;
    }
    public function setIndirizzo (string $indirizzo)
    {
        $this->indirizzo = $indirizzo;
    }
    public function setCap (string $cap)
    {
        $this->cap = $cap;
    }
    public function setComune (string $comune)
    {
        $this->comune = $comune;
    }
    public function setProvincia (string $provincia)
    {
        $this->provincia = $provincia;
    }
    public function setNazione (string $nazione)
    {
        $this->nazione = $nazione;
    }
    public function setTelefono (string $telefono)
    {
        $this->telefono = $telefono ? '+39.' . preg_replace('/^\+39|\D/', '', $telefono) : '';
    }
    public function setFax (string $fax)
    {
        $this->fax = preg_replace('/^\+39|\D/', '', $fax);
    }
    public function setCellulare (string $cellulare)
    {
        $this->cellulare = preg_replace('/^\+39|\D/', '', $cellulare);;
    }

    public function getNome () : string
    {
        return $this->nome;
    }
    public function getCognome () : string
    {
        return $this->cognome;
    }
    public function getEmail () : string
    {
        return $this->email;
    }
    public function getCodiceFiscale () : string
    {
        return $this->codiceFiscale;
    }
    public function getIndirizzo () : string
    {
        return $this->indirizzo;
    }
    public function getCap () : string
    {
        return $this->cap;
    }
    public function getComune () : string
    {
        return $this->comune;
    }
    public function getProvincia () : string
    {
        return $this->provincia;
    }
    public function getNazione () : string
    {
        return $this->nazione;
    }
    public function getTelefono () : string
    {
        return $this->telefono;
    }
    public function getFax () : string
    {
        return $this->fax;
    }
    public function getCellulare () : string
    {
        return $this->cellulare;
    }

    /**
     * @param string $nome
     * @param string $cognome
     * @param string $email
     * @param string $codiceFiscale
     * @param string $indirizzo
     * @param string $cap
     * @param string $comune
     * @param string $provincia
     * @param string $nazione
     * @param string $telefono
     * @param string $fax
     * @param string $cellulare
     */
    public function __construct (
        string $nome,
        string $cognome,
        string $email,
        string $codiceFiscale,
        string $indirizzo = '',
        string $cap = '',
        string $comune = '',
        string $provincia = '',
        string $nazione = '',
        string $telefono = '',
        string $fax = '',
        string $cellulare = ''
    )
    {
        $this->nome = $nome;
        $this->cognome = $cognome;
        $this->email = $email;
        $this->codiceFiscale = $codiceFiscale;
        $this->indirizzo = $indirizzo;
        $this->cap = $cap;
        $this->comune = $comune;
        $this->provincia = $provincia;
        $this->nazione = $nazione;
        $this->telefono = $telefono;
        $this->fax = $fax;
        $this->cellulare = $cellulare;

        $this->telefono = $this->telefono ? '+39.' . preg_replace('/^\+39|\D/', '', $this->telefono) : '';
        $this->fax = preg_replace('/^\+39|\D/', '', $this->fax);
        $this->cellulare = preg_replace('/^\+39|\D/', '', $this->cellulare);
    }

    /**
     * Validates the object, checking for required data
     * @return bool
     */
    public function validate() : bool
    {
        return ($this->nome && $this->cognome && $this->email && $this->codiceFiscale);
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function toArray() : array
    {
        $reflector = new \ReflectionClass(__CLASS__);
        $out = [];
        foreach($reflector->getProperties() as $p){
            $p->setAccessible(true);
            if($p->getValue($this)){ 
                $out[$p->getName()] = $p->getValue($this);
            }
            $p->setAccessible(false);
        }
        return $out;
    }
}