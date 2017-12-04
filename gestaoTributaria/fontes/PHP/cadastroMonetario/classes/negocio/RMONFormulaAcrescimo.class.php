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
    * Classe de regra de negocio para MONETARIO.Formula_Acrescimo
    * Data de Criacao: 20/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Regra

    * $Id: RMONFormulaAcrescimo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.11
*/

/*
$Log$
Revision 1.5  2006/09/15 14:46:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

/*include_once ( "../../../includes/Constante.inc.php"        );*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_TRANSACAO      );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONFormulaAcrescimo.class.php" );

class RMONFormulaAcrescimo
{
/**
    * @access Private
    * @var Integer
*/
var $inCodAcrescimo;
/**
    * @access Private
    * @var Integer
*/
var $inCodModulo;
/**
    * @access Private
    * @var Integer
*/
var $inCodTipo;
/**
    * @access Private
    * @var Integer
*/
var $inCodBiblioteca;
/**
    * @access Private
    * @var Integer
*/
var $inCodFuncao;
/**
    * @access Private
    * @var Date
*/
var $dtVigencia;
var $dtVigenciaAntes;
/**
    * @access Private
    * @var Object
*/
var $obTMONFormulaAcrescimo;

//SETTERS
function setCodAcrescimo($valor) { $this->inCodAcrescimo = $valor; }
function setCodFuncao($valor) { $this->inCodFuncao = $valor; }
function setCodModulo($valor) { $this->inCodModulo = $valor; }
function setCodBiblioteca($valor) { $this->inCodBiblioteca = $valor; }
function setCodTipo($valor) { $this->inCodTipo = $valor; }
function setDtVigencia($valor) { $this->dtVigencia = $valor; }
function setDtVigenciaAntes($valor) { $this->dtVigenciaAntes = $valor; }

//GETTERS
function getCodAcrescimo() { return $this->inCodAcrescimo; }
function getCodFuncao() { return $this->inCodFuncao; }
function getCodModulo() { return $this->inCodModulo; }
function getCodBiblioteca() { return $this->inCodBiblioteca; }
function getCodTipo() { return $this->inCodTipo; }
function getDtVigencia() { return $this->dtVigencia; }
function getDtVigenciaAntes() { return $this->dtVigenciaAntes; }

/**
* Metodo construtor
* @access Private
*/
function RMONFormulaAcrescimo()
{
    $this->obTransacao = new Transacao;
    // instancia mapeamentos
    $this->obTMONFormulaAcrescimo = new TMONFormulaAcrescimo;
    // instancia regras
}

/**
    * Altera os dados referentes a FORMULA de Acrescimo
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function AlterarFormulaAcrescimo($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->verificaFormulaAcrescimo();
    if ( !$obErro->ocorreu() ) {

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTMONFormulaAcrescimo->setDado("cod_acrescimo", $this->getCodAcrescimo());
            $this->obTMONFormulaAcrescimo->setDado("cod_modulo", $this->getCodModulo());
            $this->obTMONFormulaAcrescimo->setDado("cod_tipo", $this->getCodTipo());
            $this->obTMONFormulaAcrescimo->setDado("cod_biblioteca",$this->getCodBiblioteca());
            $this->obTMONFormulaAcrescimo->setDado("cod_funcao", $this->getCodFuncao());
            $this->obTMONFormulaAcrescimo->setDado("inicio_vigencia",$this->getDtVigencia());

            $obErro = $this->obTMONFormulaAcrescimo->inclusao( $boTransacao );
            //$this->obTMONFormulaAcrescimo->debug(); die();
        }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONFormulaAcrescimo );
    }

 return $obErro;
}

/**
    * Verificacao da data de vigencia se ja tem uma existente ou é anterior à mais recente
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function VerificaFormulaAcrescimo($boTransacao = "")
{
    $obErro = $this->obTMONFormulaAcrescimo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    $cont =0;
    $achou = false;
    $valores = Array ();
    $codigos = Array ();

    //pega a data da vigencia mais recente no banco, para a fórmula
    $this->PegaDataUltimaVigencia( $rsRecordSet );

    if ( $this->ValidaNovaVigencia() ) {

        while ($cont < $rsRecordSet->getNumLinhas()) {

            $codigos[$cont] = strtoupper ($rsRecordSet->getCampo("cod_acrescimo"));
            $valores[$cont] = strtoupper ($rsRecordSet->getCampo("inicio_vigencia"));

            if ( $valores[$cont] == strtoupper ($this->getDtVigencia ()) ) {
                if ( $rsRecordSet->getCampo("cod_acrescimo")==($this->getCodAcrescimo ()) ) {
                    $achou = true; break;
                }
            }
            $cont++;
            $rsRecordSet->proximo();
        }

        if ( $rsRecordSet->getNumLinhas() > 0 && $achou ) {
            $obErro->setDescricao("Data de Vigência para este acréscimo já cadastrada no Sistema! [". $this->getCodAcrescimo().'-'.$this->getDtVigenciaAntes().']');
        }
    } else {
        $obErro->setDescricao("Data de Vigência para este acréscimo igual ou anterior à mais recente no Sistema! ". $this->getDtVigencia      ());
    }
    //fim - se a data é maior
    return $obErro;

}

/**
    * Retorna a data da vigência mais recente para a fórmula
    * @access Public
    * @param  Object $obTransacao Parametro Transacao, Recordset
    * @return Object Objeto Erro
*/
function PegaDataUltimaVigencia(&$rsRecordSet , $boTransacao='')
{
    $stFiltro = "";

    if ( $this->getCodAcrescimo()) {
        $stFiltro .= " cod_acrescimo = '".$this->getCodAcrescimo()."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrder = "";
    $obErro = $this->obTMONFormulaAcrescimo->recuperaRelacionamentoUltimaVigenciaDoAcrescimo( $rsRecordSet, $stFiltro, $stOrder, $boTransacao, $this->getCodAcrescimo() );
    if ( !$obErro->ocorreu () ) {
        $this->setDtVigenciaAntes ($rsRecordSet->getCampo("inicio_vigencia"));
    }

   return $obErro;

}

/**
    * Verificacao da data de vigencia se ja tem uma existente ou é anterior à mais recente
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function ValidaNovaVigencia()
{
    $antes = $this->getDtVigenciaAntes();
    $agora = $this->getDtVigencia();

    $x = explode ('-', $antes);
    $dtAntes = $x[0].$x[1].$x[2];
    $x = explode ('/', $agora);
    $dtAgora = $x[2].$x[1].$x[0];

    if ($dtAgora > $dtAntes) {
        return true;
    }

    return false;

}

}//fim da classe
