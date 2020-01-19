<?php

class FtpClass {
    private $ftp_serve;
    private $ftp_port;
    private $ftp_user;
    private $ftp_password;
    private $ftp_pasv;
    private $conn_id;

    public function __construct($ftp_serve, $ftp_port, $ftp_user, $ftp_password, $ftp_pasv = true)
    {
        $this->ftp_serve = $ftp_serve;
        $this->ftp_port = $ftp_port;
        $this->ftp_password = $ftp_password;
        $this->ftp_user = $ftp_user;
        $this->ftp_pasv = $ftp_pasv;
    }

    public function ConnectFTP()
    {
        $this->conn_id = ftp_connect($this->ftp_serve); 
        
        $login_ftp = ftp_login($this->conn_id, $this->ftp_user, $this->ftp_password);
        
        if($this->ftp_pasv){
            ftp_pasv($this->conn_id, true);
        }

        if(!$this->conn_id || !$login_ftp){
            return false; 
            ftp_close($this->conn_id); 
        } else {
            return true; 
        }
    }
    
    public function sendFile($archivo_local, $archivo_remoto)
    {
        if(!ftp_put($this->conn_id, $archivo_remoto, $archivo_local, FTP_BINARY)) {
            ftp_close($this->conn_id); 
            return false;
        }  
        ftp_close($this->conn_id); 
        return true;
    }
    
    public function ObtenerRuta()
    {
        $Directorio = ftp_pwd($this->conn_id);
        
        ftp_close($this->conn_id); 
        
        return $Directorio; 
    }

}

