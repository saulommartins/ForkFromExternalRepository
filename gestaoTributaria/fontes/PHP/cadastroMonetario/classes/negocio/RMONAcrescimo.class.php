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
    * Classe de regra de negocio para MONETARIO.Acrescimo
    * Data de Criacao: 20/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Regra

    * $Id: RMONAcrescimo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.11
                uc-02.04.03
                uc-02.04.33
*/

/*
$Log$
Revision 1.20  2007/04/16 15:17:04  cassiano
Bug #8424#

Revision 1.19  2007/03/15 18:58:15  domluc
Caso de Uso 02.04.33

Revision 1.18  2006/11/22 17:55:36  cercato
bug #7576#

Revision 1.17  2006/09/15 14:46:22  fabio
correÃ§Ã£o do cabeÃ§alho,
adicionado trecho de log do CVS

*/

/*include_once ( "../../../includes/Constante.inc.php"        );*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_TRANSACAO );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONAcrescimo.class.php" );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONAcrescimoNorma.class.php" );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONValorAcrescimo.class.php" );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONFormulaAcrescimo.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONFormulaAcrescimo.class.php" );

class RMONAcrescimo
{
/**
    * @access Private
    * @var Integer
*/
var $inCodTipo;
var $inCodNorma;

/**
    * @access Private
    * @var String
*/
var $stNomTipo;
/**
    * @access Private
    * @var Integer
*/
var $inCodAcrescimo;
/**
    * @access Private
    * @var Array
*/
var $arDadosValorAcrescimo;

/**
    * @access Private
    * @var String
*/
var $stDescricao;
/**
    * @access Private
    * @var String
*/
var $strFormula;
/**
    * @access Private
    * @var Object
*/
var $obTMONValorAcrescimo;
/**
    * @access Private
    * @var Object
*/
var $obTMONAcrescimo;
/**
    * @access Private
    * @var Object
*/
var $obTMONTipoAcrescimo;
/**
    * @access Private
    * @var Object
*/
var $obTMONFormulaAcrescimo;
/**
    * @access Private
    * @var Object
*/
var $obRMONFormulaAcrescimo;

//SETTERS
function setCodNorma($valor) { $this->inCodNorma = $valor; }
function setDadosValorAcrescimo($valor) { $this->arDadosValorAcrescimo = $valor; }
function setCodAcrescimo($valor) { $this->inCodAcrescimo = $valor; }
function setDescricao($valor) { $this->stDescricao = $valor; }
function setCodTipo($valor) { $this->inCodTipo = $valor; }
function setNomTipo($valor) { $this->stNomTipo = $valor; }
function setStrFormula($valor) { $this->strFormula = $valor; }

//GETTERS
function getCodNorma() { return $this->inCodNorma; }
function getDadosValorAcrescimo() { return $this->arDadosValorAcrescimo; }
function getCodAcrescimo() { return $this->inCodAcrescimo; }
function getDescricao() { return $this->stDescricao; }
function getCodTipo() { return $this->inCodTipo; }
function getNomTipo() { return $this->stNomTipo; }
function getStrFormula() { return $this->StrFormula; }

/**
* Metodo construtor
* @access Private
*/
function RMONAcrescimo()
{
    $this->obTransacao            = new Transacao;
    // instancia mapeamentos
    $this->obTMONAcrescimo        = new TMONAcrescimo;
    $this->obTMONValorAcrescimo   = new TMONValorAcrescimo;
    $this->obTMONFormulaAcrescimo = new TMONFormulaAcrescimo;
    // instancia regras
    $this->obRMONFormulaAcrescimo = new RMONFormulaAcrescimo;
}

function ListarValorAcrescimo(&$rsValores, $boTransacao = "")
{
    if ( $this->getCodAcrescimo() ) {
        $stFiltro .= " cod_acrescimo = '".$this->getCodAcrescimo()."' AND ";
    }
    if ( $this->getCodTipo () ) {
        $stFiltro .= " cod_tipo = ". $this->getCodTipo()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $obErro = $this->obTMONValorAcrescimo->recuperaTodos( $rsValores, $stFiltro, "", $boTransacao );
    //$this->obTMONValorAcrescimo->debug();
    return $obErro;
}

function IncluirValorAcrescimo($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONValorAcrescimo );

        return $obErro;
    }

    $this->obTMONValorAcrescimo->setDado("cod_acrescimo", $this->getCodAcrescimo() );
    $this->obTMONValorAcrescimo->setDado("cod_tipo" , $this->getCodTipo() );
    $inTotalValores = count( $this->arDadosValorAcrescimo );
    $obErro = $this->obTMONValorAcrescimo->exclusao( $boTransacao );
    $this->obTMONValorAcrescimo->debug();
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONValorAcrescimo );

        return $obErro;
    }

    for ($inX=0; $inX<$inTotalValores; $inX++) {
        $this->obTMONValorAcrescimo->setDado("inicio_vigencia", $this->arDadosValorAcrescimo[$inX]["dtVigencia"] );
        $this->obTMONValorAcrescimo->setDado("valor" , $this->arDadosValorAcrescimo[$inX]["flValorAcrescimo"] );
        $obErro = $this->obTMONValorAcrescimo->inclusao( $boTransacao );
        if ( $obErro->ocorreu() ) {
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONValorAcrescimo );

            return $obErro;
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONValorAcrescimo );

    return $obErro;
}

/**
    * Inclui os dados referentes a Acrescimo
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function IncluirAcrescimo($boTransacao = "")
{
    $obErro = new Erro;
    //INCLUSAO NA TABELA ACRESCIMO
    $boFlagTransacao = false;
    $obErro = $this->verificaAcrescimo();
    if ( !$obErro->ocorreu() ) {

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {

            $obErro = $this->obTMONAcrescimo->proximoCod( $this->inCodAcrescimo, $boTransacao );
            if ( !$obErro->ocorreu() ) {

                $this->obTMONAcrescimo->setDado("cod_acrescimo", $this->getCodAcrescimo());
                $this->obTMONAcrescimo->setDado("descricao_acrescimo",$this->getDescricao() );
                $this->obTMONAcrescimo->setDado("cod_tipo" , $this->getCodTipo () );

                $obErro = $this->obTMONAcrescimo->inclusao( $boTransacao );
                //$this->obTMONAcrescimo->debug();

                if ( !$obErro->ocorreu() ) {

                    //tabela formula_acrescimo
                    $this->obTMONFormulaAcrescimo->setDado("cod_acrescimo", $this->getCodAcrescimo() );

                    $this->obTMONFormulaAcrescimo->setDado("cod_tipo", $this->getCodTipo());

                    $this->obTMONFormulaAcrescimo->setDado("cod_funcao", $this->obRMONFormulaAcrescimo->getCodFuncao ());
                    $this->obTMONFormulaAcrescimo->setDado("cod_modulo", $this->obRMONFormulaAcrescimo->getCodModulo ());
                    $this->obTMONFormulaAcrescimo->setDado("cod_biblioteca", $this->obRMONFormulaAcrescimo->getCodBiblioteca ());

                    $obErro = $this->obTMONFormulaAcrescimo->inclusao( $boTransacao);
                    // $this->obTMONFormulaAcrescimo->debug();

                    if ( !$obErro->ocorreu() ) {
                        $obTMONAcrescimoNorma = new TMONAcrescimoNorma;
                        $obTMONAcrescimoNorma->setDado( "cod_acrescimo", $this->getCodAcrescimo() );
                        $obTMONAcrescimoNorma->setDado( "cod_tipo", $this->getCodTipo() );
                        $obTMONAcrescimoNorma->setDado( "cod_norma", $this->getCodNorma() );
                        $obErro = $obTMONAcrescimoNorma->inclusao( $boTransacao );
                    }
                }//fim segunda insercao
            }//fim primeira inseraco
        }//fim erro transacao
    }//fim erro verifica acrescimo

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONAcrescimo );

    return $obErro;
}

/**
    * Altera os dados referentes a Acrescimo
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function AlterarAcrescimo($boTransacao = "")
{
    $obErro = new Erro;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {

        $this->obTMONAcrescimo->setDado("descricao_acrescimo", $this->getDescricao());
        $this->obTMONAcrescimo->setDado("cod_acrescimo", $this->getCodAcrescimo());
        $this->obTMONAcrescimo->setDado("cod_tipo", $this->getCodTipo());

        $obErro = $this->obTMONAcrescimo->alteracao( $boTransacao );
        //$this->obTMONAcrescimo->debug();
        if ( !$obErro->ocorreu() ) {
            $obTMONAcrescimoNorma = new TMONAcrescimoNorma;
            $obTMONAcrescimoNorma->setDado( "cod_acrescimo", $this->getCodAcrescimo() );
            $obTMONAcrescimoNorma->setDado( "cod_tipo", $this->getCodTipo() );
            $obTMONAcrescimoNorma->setDado( "cod_norma", $this->getCodNorma() );
            $obErro = $obTMONAcrescimoNorma->inclusao( $boTransacao );

            if ( !$obErro->ocorreu() ) {
                $this->obTMONFormulaAcrescimo->setDado("cod_acrescimo", $this->getCodAcrescimo());
                $this->obTMONFormulaAcrescimo->setDado("cod_tipo", $this->getCodTipo());
                $this->obTMONFormulaAcrescimo->setDado("cod_funcao", $this->obRMONFormulaAcrescimo->getCodFuncao());
                $this->obTMONFormulaAcrescimo->setDado("cod_modulo", $this->obRMONFormulaAcrescimo->getCodModulo());
                $this->obTMONFormulaAcrescimo->setDado("cod_biblioteca",$this->obRMONFormulaAcrescimo->getCodBiblioteca());

                $obErro = $this->obTMONFormulaAcrescimo->inclusao( $boTransacao );

    //            $this->obTMONFormulaAcrescimo->debug(); die();
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONAcrescimo );

    return $obErro;
}

/**
    * Exclui os dados referentes a Acrescimo
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function ExcluirAcrescimo($boTransacao = "")
{
    $obErro = new Erro;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {

        $obTMONAcrescimoNorma = new TMONAcrescimoNorma;
        $obTMONAcrescimoNorma->setDado( "cod_acrescimo", $this->getCodAcrescimo() );
        $obTMONAcrescimoNorma->setDado( "cod_tipo", $this->getCodTipo() );
        $obErro = $obTMONAcrescimoNorma->exclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTMONFormulaAcrescimo->setDado( "cod_acrescimo" , $this->getCodAcrescimo() );
            $this->obTMONFormulaAcrescimo->setDado("cod_tipo", $this->getCodTipo());

            $obErro = $this->obTMONFormulaAcrescimo->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTMONAcrescimo->setDado( "cod_acrescimo" , $this->getCodAcrescimo() );
                $this->obTMONAcrescimo->setDado( "descricao_acrescimo",$this->getDescricao());
                $obErro = $this->obTMONAcrescimo->exclusao( $boTransacao );
            }

            if ( $obErro->ocorreu() and strpos($obErro->getDescricao(), "fk_") ) {
                $obErro->setDescricao("O acréscimo ".$this->getDescricao()." está sendo utilizado pelo sistema!");
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONAcrescimo );

    return $obErro;

}

/**
* Lista os Acrescimos
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function listarAcrescimos(&$rsRecordSet, $boTransacao = "")
{
    $obErro = new Erro;
    $stFiltro = "";

    if ( $this->getCodAcrescimo() ) {
        $stFiltro .= " ma.cod_acrescimo = '".$this->getCodAcrescimo()."' AND ";
    }
    if ( $this->getCodTipo () ) {
        $stFiltro .= " ma.cod_tipo = ". $this->getCodTipo()." AND ";
    }
    if ( $this->getDescricao() ) {
        $stFiltro .= " upper (ma.descricao_acrescimo) like upper ('%".$this->getDescricao()."%') AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = '';
    $stOrder.= " ORDER BY ma.cod_acrescimo";
    $obErro = $this->obTMONAcrescimo->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //$this->obTMONAcrescimo->debug();
    return $obErro;

}

/**
    * Recupera do banco de dados os dados do Acrescimo
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function ConsultarAcrescimo(&$rsRecordSet, $boTransacao = "")
{
    $obErro = new Erro;

    $stFiltro = "";
    if ( $this->getCodAcrescimo() ) {
        $stFiltro = " ma.cod_acrescimo = ".$this->getCodAcrescimo(). " AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrder = '';
    $obErro = $this->obTMONAcrescimo->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //$this->obTMONAcrescimo->debug();
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->SetCodAcrescimo   ( $rsRecordSet->getCampo("cod_acrescimo"      ));
        $this->SetDescricao      ( $rsRecordSet->getCampo("descricao_acrescimo") );
        $this->SetCodTipo        ( $rsRecordSet->getCampo("cod_tipo") );
        $this->SetNomTipo        ( $rsRecordSet->getCampo("nom_tipo") );
    }

    return $obErro;
}

/**
* Lista os registros de Tipos de Acrescimos
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ListarTipo(&$rsRecordSet, $boTransacao = '')
{
    include_once ( CAM_GT_MON_MAPEAMENTO."TMONTipoAcrescimo.class.php");
    $this->obTMONTipoAcrescimo = new TMONTipoAcrescimo;

    $obErro = new Erro;
    $stFiltro = "";

    if ( $this->getCodTipo() ) {

        $stFiltro .= " cod_tipo = '".$this->getCodTipo()."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrder = "";
    $obErro = $this->obTMONTipoAcrescimo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;

}//fim lista tipos de acrescimos

/**
    * Verifica se a Especie a ser incluida jÃ¡ existe
    * @access Public
    * @param  Object $rsBanco Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function VerificaAcrescimo($boTransacao = "")
{
    $obErro = new Erro;
    $obErro = $this->obTMONAcrescimo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    $cont =0;
    $achou = false;
    $valores = Array ();
    while ($cont < $rsRecordSet->getNumLinhas()) {

        $valores[$cont] = strtoupper ($rsRecordSet->getCampo("descricao_acrescimo"));

        if ( $valores[$cont] == strtoupper ($this->getDescricao()) ) {
            $achou = true; break;
        }
        $cont++;
        $rsRecordSet->proximo();

    }

    if ( $rsRecordSet->getNumLinhas() > 0 && $achou ) {
        $obErro->setDescricao("AcrÃ©scimo jÃ¡ cadastrado no Sistema! ". $this->getDescricao());
    }

    return $obErro;
}

/**
    * Busca os valores dos cÃ³digos da formula
    * @access Public
    * @param  Object $obTransacao Parametro Transacao, codigo do acrescimo
    * @return Object Objeto Erro
*/
function DevolveFormula(&$rsRecordSet , $inCodAcrescimo, $boTransacao='')
{
    $obErro = new Erro;
    $obErro = $this->obTMONFormulaAcrescimo->RecuperaRelacionamentoDadosDaFormula( $rsRecordSet, $stFiltro, $stOrder, $boTransacao, $inCodAcrescimo );
    //$this->obTMONFormulaAcrescimo->debug();

    if ( !$obErro->ocorreu () ) {

        $this->setCodAcrescimo    ($rsRecordSet->getCampo("cod_acrescimo"));
        $this->obRMONFormulaAcrescimo->setCodModulo       ($rsRecordSet->getCampo("cod_modulo"));
        $this->obRMONFormulaAcrescimo->setCodBiblioteca   ($rsRecordSet->getCampo("cod_biblioteca"));
        $this->obRMONFormulaAcrescimo->setCodFuncao       ($rsRecordSet->getCampo("cod_funcao"));
        $this->obRMONFormulaAcrescimo->setDtVigencia      ($rsRecordSet->getCampo("inicio_vigencia"));

        $this->DevolveDescFormula ( $rsRecordSet , $boTransacao='', $rsRecordSet->getCampo("cod_modulo"), $rsRecordSet->getCampo("cod_biblioteca"), $rsRecordSet->getCampo("cod_funcao") );

    }

   return $obErro;
}

}
