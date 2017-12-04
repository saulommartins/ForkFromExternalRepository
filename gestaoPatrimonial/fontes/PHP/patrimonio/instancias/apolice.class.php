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
    * classe que inclui as apólices , altera e exclui, sempre retornando mensagem de sucesso ou de erro
    * Data de Criação   : 24/03/2003

    * @author Analista Jorge B. Ribarr

    * @ignore

    $Revision: 12234 $
    $Name$
    $Autor: $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.01.08
*/

/*
$Log$
Revision 1.10  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.9  2006/07/06 12:11:27  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>
<?php

class apolice
{
    /*******************************/
    /* Propriedades públicas       */
    /*******************************/
    public $codigo,
        $numero,
        $codSeguradora,
        $nomSeguradora,
        $dtVencimento,
        $contato,
        $comboSeguradoras,
        $codBem;

    /*******************************/
    /* Método Construtor           */
    /*******************************/
    public function apolice()
    {
        $this->codigo        = 0;
        $this->numero        = "";
        $this->codSeguradora = "";
        $this->nomSeguradora = "";
        $this->dtVencimento  = "";
        $this->contato       = "";
        $this->comboSeguradoras = "";
        $this->codBem = "";
    }

    /*******************************/
    /* Métodos Públicos            */
    /*******************************/
    public function incluiApolice()
    {
        $bOK = false;
        $dbApolice = new dataBaseLegado;
        $dbApolice->abreBd();
        $this->geraCodigo();
        $insert = " insert into patrimonio.apolice
                    (cod_apolice, numcgm, num_apolice, dt_vencimento, contato)  values
                    ($this->codigo, $this->codSeguradora, '$this->numero',
                    '".datatosql($this->dtVencimento)."', '$this->contato')";
        $bOK = $dbApolice->executaSql($insert);
        $dbApolice->fechaBd();

        return $bOK;
    }

    public function listaApolice()
    {
        $aLista = array();
        $dbApolice = new dataBaseLegado;
        $dbApolice->abreBd();
        $select =   "select a.cod_apolice, a.numcgm, c.nom_cgm, a.num_apolice,
                            a.dt_vencimento, a.contato
                     from patrimonio.apolice a, sw_cgm c
                     where c.numcgm = a.numcgm
                     order by c.nom_cgm, a.num_apolice";
        $dbApolice->abreSelecao($select);
        while (!$dbApolice->eof()) {
            $cod = $dbApolice->pegaCampo("cod_apolice");
            $lista[$cod]["segu"] = $dbApolice->pegaCampo("nom_cgm");
            $lista[$cod]["nume"] = $dbApolice->pegaCampo("num_apolice");
            $lista[$cod]["vcto"] = datatobr($dbApolice->pegaCampo("dt_vencimento"));
            $lista[$cod]["cont"] = $dbApolice->pegaCampo("contato");
            $dbApolice->vaiProximo();
        }
        $dbApolice->limpaSelecao();
        $dbApolice->fechaBd();

        return $lista;
    }

    public function mostraApolice($codigo)
    {
        $dbApolice = new dataBaseLegado;
        $dbApolice->abreBd();
        $select =   "select a.cod_apolice, a.numcgm, c.nom_cgm, a.num_apolice,
                            a.dt_vencimento, a.contato
                     from patrimonio.apolice a, sw_cgm c
                     where c.numcgm = a.numcgm and
                     a.cod_apolice = $codigo";
        $dbApolice->abreSelecao($select);
        if (!$dbApolice->eof()) {
            $this->numero        = $dbApolice->pegaCampo("num_apolice");
            $this->codSeguradora = $dbApolice->pegaCampo("numcgm");
            $this->nomSeguradora = $dbApolice->pegaCampo("nom_cgm");
            $this->dtVencimento  = datatobr($dbApolice->pegaCampo("dt_vencimento"));
            $this->contato       = $dbApolice->pegaCampo("contato");
            $dbApolice->limpaSelecao();
        }
        $dbApolice->fechaBd();
    }

    public function alteraApolice($codigo)
    {
        $dbApolice = new dataBaseLegado;
        $dbApolice->abreBd();
        $update =   "update patrimonio.apolice set
                     num_apolice   = '$this->numero',
                     numcgm        = $this->codSeguradora,
                     dt_vencimento = '".datatoSQL($this->dtVencimento)."',
                     contato       = '$this->contato'
                     where cod_apolice = $codigo";
        $bOK = $dbApolice->executaSql($update);
        $dbApolice->fechaBd();

        return $bOK;
    }

    public function excluiApolice($codigo)
    {
        $dbApolice = new dataBaseLegado;
        $dbApolice->abreBd();
        $delete =   "delete from patrimonio.apolice
                        where cod_apolice = $codigo";
        $result = $dbApolice->executaSql($delete);
        if ($result) {
            $dbApolice->fechaBd();

            return true;
        } else {
            $dbApolice->fechaBd();

            return false;
        }
    }

    public function listaComboSeguradoras()
    {
        $sSQL = "SELECT DISTINCT a.numcgm, c.nom_cgm FROM patrimonio.apolice as a, sw_cgm as c
                WHERE a.numcgm = c.numcgm
                ORDER by nom_cgm";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->comboSeguradoras = "";
        $this->comboSeguradoras .= "<select name=numCgm style='width:200px'>\n<option value=xxx SELECTED>Selecione</option>\n";
        while (!$dbEmp->eof()) {
            $numcgm  = trim($dbEmp->pegaCampo("numcgm"));
            $nomCgm  = trim($dbEmp->pegaCampo("nom_cgm"));
            $dbEmp->vaiProximo();
            $this->comboSeguradoras .= "<option value=".$numcgm.">".$nomCgm."</option>\n";
        }
        $this->comboSeguradoras .= "</select>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
    }

    public function mostraComboSeguradoras()
    {
        echo $this->comboSeguradoras;
    }

    public function setaVariaveisBemApolice($codigo, $codBem)
    {
        $this->codigo = $codigo;
        $this->codBem = $codBem;
    }

    public function insereBemApolice()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "INSERT INTO patrimonio.apolice_bem (cod_apolice, cod_bem) VALUES ('".$this->codigo."', '".$this->codBem."')";
        if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
    }

    public function insereArrayBens($arBens)
    {
        $insert = '';
        $delete = 'delete from patrimonio.apolice_bem;';
        for ($i=0;$i<count($arBens);$i++) {
           $insert.= "INSERT INTO patrimonio.apolice_bem (cod_apolice, cod_bem) VALUES (".$this->codigo.",".$arBens[$i]['codBem'].");";
        }
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($dbConfig->executaSql($delete.$insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();

    }

    public function deleteBemApolice()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "DELETE FROM patrimonio.apolice_bem WHERE cod_bem = ".$this->codBem." AND cod_apolice = ".$this->codigo;
        if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
    }

    /*******************************/
    /* Métodos Privados            */
    /*******************************/
    public function geraCodigo()
    {
        $this->codigo = pegaID('cod_apolice', 'patrimonio.apolice' );
    }

    /***********************************************************************************/
    /* Verifica se o bem e se a apolice informados, jah nao estao inseridos            */
    /***********************************************************************************/
    public function verificaBemApolice()
    {
        $dbApolice = new dataBaseLegado;
        $dbApolice->abreBd();
        $select = " SELECT
                        a.cod_bem
                    FROM
                        patrimonio.apolice_bem AS a
                    WHERE
                        a.cod_bem     = ".$this->codBem."
                    AND a.cod_apolice = ".$this->codigo.";";
        $dbApolice->abreSelecao( $select );
        if ( !$dbApolice->eof() ) {
            $this->codBem = $dbApolice->pegaCampo("cod_bem");
            $dbApolice->limpaSelecao();

            return true;
        } else {
            return false;
        }
        $dbApolice->fechaBd();
    }

    /***********************************************************************************/
    /* Verifica se o bem existe no banco de dados                                      */
    /***********************************************************************************/
    public function verificaBem()
    {
        $dbApolice = new dataBaseLegado;
        $dbApolice->abreBd();
        $select = "SELECT
                         b.cod_bem
                   FROM
                      patrimonio.bem as b
                   WHERE
                         b.cod_bem = ".$this->codBem.";";
        $dbApolice->abreSelecao( $select );
        if ( $dbApolice->eof() ) {
            $this->codBem = $dbApolice->pegaCampo("cod_bem");
            $dbApolice->limpaSelecao();

            return true;
        } else {
            return false;
        }
        $dbApolice->fechaBd();
    }
}
