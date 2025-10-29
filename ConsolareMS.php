<?php

class ConsolareMS
{
    private ?PDO $conn;
    private string $dsn = "sqlsrv:server=consolaredb.grupozelo.com;Database=Consolare;TrustServerCertificate=yes;";
    private string $username;
    private string $password;

    public function __construct(){
        $this->username = 'Integracao_Rastreamento';
        $this->password = 'a0}};r9%IQ:nAPnE';
    }

    function Connection()
    {
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        );
        $this->conn = new PDO($this->dsn, $this->username, $this->password, $options);
    }
    function Close()
    {
        $this->conn = null;
    }

    function GetNew()
    {
        $stmt = $this->conn->prepare("SELECT * FROM VW_OBITOS_OS WHERE _DADOSOS_DataUpdate >= DATEADD(MINUTE, -400, GETDATE()) AND OSFechada='S'");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}