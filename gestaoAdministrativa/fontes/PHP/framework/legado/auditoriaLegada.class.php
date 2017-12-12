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

class auditoriaLegada
{
/**************************************************************************/
/**** Declaração da  variáveis                                          ***/
/**************************************************************************/
    public $numcgm;
    public $acao;
    public $comboUsuario;
    public $comboModulo;
    public $codModulo;
    public $codUsuario;

/**************************************************************************/
/**** Método  Construtor                                                ***/
/**************************************************************************/

    public function auditoriaLegada()
    {
        $this->numcgm = "";
        $this->acao = "";
        $this->comboUsuario = "";
        $this->comboModulo = "";
        $this->codModulo = "";
        $this->codUsuario = "";
        }

/**************************************************************************/
/**** Pega as variáveis de  ação do usuário                             ***/
/**************************************************************************/
    public function setaAuditoria($numcgm, $acao, $objeto)
    {
        $this->numcgm = $numcgm;
        $this->acao = $acao;
        $this->objeto = $objeto;
        }
/**************************************************************************/
/**** Insere na tabela Auditoria  a ação praticada pelo usuário         ***/
/**************************************************************************/
    public function insereAuditoria()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "";
        if (is_array($this->objeto)) {
            foreach ($this->objeto as $chave=>$valor) {
                $insert = "INSERT INTO administracao.auditoria (numcgm, cod_acao, objeto) VALUES ('".$this->numcgm."', '".$this->acao."', '".$valor."'); "; //insere a Auditoria
                if ($dbConfig->executaSql($insert))
                    $ok = true;
                else
                    $ok = false;
            }
        } else {
            $insert = "INSERT INTO administracao.auditoria (numcgm, cod_acao, objeto) VALUES ('".$this->numcgm."', '".$this->acao."', '".$this->objeto."'); "; //insere a Auditoria
            if ($dbConfig->executaSql($insert))
                $ok = true;
            else
                $ok = false;
        }

        return $ok;
        }
/**************************************************************************/
/**** Gera o Combo com os Módulos para seleção                          ***/
/**************************************************************************/
    public function listaComboModulos()
    {
        $sSQL = "SELECT * FROM administracao.modulo ORDER by nom_modulo";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->comboModulo = "";
        $this->comboModulo .= "<select name=moduloCod>\n<option value=xxx SELECTED>Todos</option>\n";
        while (!$dbEmp->eof()) {
            $cod_modulo  = trim($dbEmp->pegaCampo("cod_modulo"));
            $nom_modulo  = trim($dbEmp->pegaCampo("nom_modulo"));
            $dbEmp->vaiProximo();
            $this->comboModulo .= "<option value=".$cod_modulo.">".$nom_modulo."</option>\n";
    }
        $this->comboModulo .= "</select>";
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    }
/**************************************************************************/
/**** Mostra o na tela o Combo por módulos gerado                       ***/
/**************************************************************************/
    public function mostraComboModulos()
    {
        echo $this->comboModulo;
    }
/**************************************************************************/
/**** Gera oo combo de usuários para seleção                            ***/
/**************************************************************************/
    public function listaComboUsuarios()
    {
        $sSQL = "SELECT numcgm, username FROM administracao.usuario ORDER by username";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->comboUsuario = "";
        $this->comboUsuario .= "<select name=usuarioCod>\n<option value=xxx SELECTED>Todos</option>\n";
        while (!$dbEmp->eof()) {
            $numcgm  = trim($dbEmp->pegaCampo("numcgm"));
            $username  = trim($dbEmp->pegaCampo("username"));
            $dbEmp->vaiProximo();
            $this->comboUsuario .= "<option value=".$numcgm.">".$username."</option>\n";
    }
        $this->comboUsuario .= "</select>";
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    }
/**************************************************************************/
/**** Mostra o na tela o Combo por usuários gerado                      ***/
/**************************************************************************/
    public function mostraComboUsuarios()
    {
        echo $this->comboUsuario;
    }
/**************************************************************************/
/**** Captura a variável gerada pelo combo de Módulos                   ***/
/**************************************************************************/
    public function pegaVarModulos($moduloCod)
    {
        $this->codModulo = $moduloCod;

    }
/**************************************************************************/
/**** Captura a variável gerada pelo combo de usuários                  ***/
/**************************************************************************/
    public function pegaVarUsuarios($usuarioCod)
    {
        $this->codUsuario = $usuarioCod;

    }

}
?>
