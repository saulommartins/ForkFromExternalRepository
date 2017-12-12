<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

class dataBaseLegado
{
    public $registro,
        $numeroDeLinhas,
        $registroCorrente,
        $linhaCorrente,
        $campo,
        $conexao,
        $sql,
        $comBegin;

    public function dataBaseLegado()
    {
        $registro         = 0;
        $numeroDeLinhas   = 0;
        $registroCorrente = 0;
        $linhaCorrente    = 0;
        $campo            = 0;
        $conexao          = 0;
        $sql              = "";
        $this->version    = (float) phpversion();
        $this->comBegin   = true;
    }

    // Função private de mensagem de erro
    public function deuErro($mensagem)
    {
        exit('<font face="Arial,Helvetica,sans-serif" color="#FF0000">'.$mensagem.'</font>');
    }

    public function abreBD($bancoDeDados=BD_NAME, $servidor=BD_HOST, $porta=BD_PORT,
                    $usuario="", $senha="") {

        $usuario = $usuario ? $usuario : BD_USER;
        $senha = $senha ? $senha : BD_PASS;
        $textoConexao = "host=$servidor port=$porta dbname=$bancoDeDados ".
                        "user='$usuario' password=$senha";
        $this->conexao = pg_connect($textoConexao);
        if (!$this->conexao) {
            $this->deuErro("Falha ao conectar com o banco de dados!");

        } else {
           return $this->conexao;
        }
    }

    public function fechaBD()
    {
        pg_close($this->conexao);
    }

    public function executaSql($comandoSql)
    {
        if ($this->comBegin) {
            $comandoSql = "BEGIN;\n ".$comandoSql.";\n COMMIT;";
        }
        //print $comandoSql;
        $this->sql = $comandoSql;
        if ($this->version<4.3) {
            $ok = pg_exec($this->conexao,$comandoSql);
        } else {
            $ok = pg_query($this->conexao,$comandoSql);
        }

        //if (!$ok) {
            //print $this->sql;
            //$this->deuErro("Falha ao executar comandos no banco de dados!");
        //}
        return $ok;

    }

    public function abreSelecao($comandoSql)
    {
        $this->sql = $comandoSql;
        //print "<br> $comandoSql <br>";
        if ($this->version<4.3) {
            $this->registro = pg_exec($this->conexao,$comandoSql);
        } else {
            $this->registro = pg_query($this->conexao,$comandoSql);
        }
        if ($this->registro < 1) {
            $this->deuErro("Falha ao consultar o banco de dados!");
        }
        if ($this->version<4.3) {
            $this->numeroDeLinhas = pg_numrows($this->registro);
        } else {
            $this->numeroDeLinhas = pg_num_rows($this->registro);
        }
        if ($this->registro < 1) {
            $this->deuErro("Falha ao consultar o banco de dados!");
        }
        $this->linhaCorrente  = 0;
        if ($this->numeroDeLinhas>0) {
            if ($this->version<4.3) {
                $aAux = pg_fetch_array($this->registro,$this->linhaCorrente);
            } else {
                pg_result_seek($this->registro,$this->linhaCorrente);
                $aAux = pg_fetch_array($this->registro);
            }
            if (is_array($aAux)) {
                while (list($key, $val) = each($aAux)) {
                    $key = strtolower($key);
                    $this->registroCorrente[$key] = $val;
                }
            }
            reset($this->registroCorrente);
        }

        return $this->registro;
    }

    public function limpaSelecao()
    {
        if ($this->version<4.3) {
            pg_freeresult($this->registro);
        } else {
            pg_free_result($this->registro);
        }
    }

    public function pegaCampo($nomeCampo)
    {
        $nomeCampo = strtolower($nomeCampo);
        $aAux  = $this->registroCorrente;
        $this->campo = "";
        if (is_array($aAux)) {
            if (array_key_exists($nomeCampo, $aAux)) {
                $this->campo = stripslashes($this->registroCorrente[$nomeCampo]);
            }//else{
                //echo $this->sql."<br>";
                //echo "A coluna <b>$nomeCampo</b> não existe na seleção!";
            //}
        }

        return $this->campo;
    }

    public function vaiPrimeiro()
    {
        $this->linhaCorrente=0;
        if ($this->numeroDeLinhas>0) {
            if ($this->version<4.3) {
                $aAux = pg_fetch_array($this->registro,$this->linhaCorrente);
            } else {
                pg_result_seek($this->registro,$this->linhaCorrente);
                $aAux = pg_fetch_array($this->registro);
            }
            if (is_array($aAux)) {
                while (list($key, $val) = each($aAux)) {
                    $key = strtolower($key);
                    $this->registroCorrente[$key] = $val;
                }
            }
            reset($this->registroCorrente);
        }
    }

    public function vaiProximo()
    {
       $this->linhaCorrente+=1;
       if ($this->linhaCorrente <= $this->numeroDeLinhas-1) {
            if ($this->version<4.3) {
                $aAux = pg_fetch_array($this->registro,$this->linhaCorrente);
            } else {
                pg_result_seek($this->registro,$this->linhaCorrente);
                $aAux = pg_fetch_array($this->registro);
            }
            if (is_array($aAux)) {
                while (list($key, $val) = each($aAux)) {
                    $key = strtolower($key);
                    $this->registroCorrente[$key] = $val;
                }
            }
            reset($this->registroCorrente);
        }
    }

    public function vaiAnterior()
    {
        $this->linhaCorrente-=1;
        if ($this->linhaCorrente >= 0) {
            if ($this->version<4.3) {
                $aAux = pg_fetch_array($this->registro,$this->linhaCorrente);
            } else {
                pg_result_seek($this->registro,$this->linhaCorrente);
                $aAux = pg_fetch_array($this->registro);
            }
            if (is_array($aAux)) {
                while ( list( $key, $val ) = each( $aAux ) ) {
                    $key = strtolower($key);
                    $this->registroCorrente[$key] = $val;
                }
            }
            reset($this->registroCorrente);
        }
    }

    public function vaiUltimo()
    {
        if ($this->numeroDeLinhas>0) {
            $this->linhaCorrente = $this->numeroDeLinhas-1;
                if ($this->version<4.3) {
                    $aAux = pg_fetch_array($this->registro,$this->linhaCorrente);
                } else {
                    pg_result_seek($this->registro,$this->linhaCorrente);
                    $aAux = pg_fetch_array($this->registro);
                }
            if (is_array($aAux)) {
                while (list($key, $val) = each($aAux)) {
                    $key = strtolower($key);
                    $this->registroCorrente[$key] = $val;
                }
            }
            reset($this->registroCorrente);
        }
    }

    public function eof()
    {
        if ($this->linhaCorrente >= $this->numeroDeLinhas) {
            $this->linhaCorrente = $this->numeroDeLinhas;

            return true;
        } else {
            return false;
        }
    }

    public function bof()
    {
        if ($this->linhaCorrente < 0) {
            $this->linhaCorrente = 0;

            return true;
        } else {
            return false;
        }
   }

   public function pegaUltimoErro()
   {
        return urlencode(pg_last_error( $this->conexao ));
   }
}

?>
