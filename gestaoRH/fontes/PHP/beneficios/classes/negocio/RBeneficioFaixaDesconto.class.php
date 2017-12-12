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
  * Página de
  * Data de criação : 07/07/2005

    * @author Analista: Vandre Ramos
    * @author Programador: Rafael Almeida

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.06.06
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_BEN_MAPEAMENTO."TBeneficioFaixaDesconto.class.php"    );
//include_once    ( CAM_GRH_BEN_NEGOCIO."RBeneficioVigencia.class.php"              );

class RBeneficioFaixaDesconto
{
/**
    * @access Private
    * @var Array
*/
var $arFaixa;
/**
    * @access Private
    * @var Integer
*/
var $inCodFaixa;
/**
    * @access Private
    * @var Float
*/
var $nuSalarioInicial;
/**
    * @access Private
    * @var Float
*/
var $nuSalarioFinal;
/**
    * @access Private
    * @var flPercentualDesc
*/
var $nuPercentualDesc;
/**
    * @access Private
    * @var Reference Object
*/
var $roBeneficioVigencia;
/**
    * @access Private
    * @var Object
*/
var $obTBeneficioFaixaDesconto;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Public
    * @param Object $Valor
*/
function setFaixa($valor) { $this->arFaixa             = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodFaixa($valor) { $this->inCodFaixa          = $valor; }
/**
    * @access Public
    * @param Float $Valor
*/
function setSalarioInicial($valor) { $this->nuSalarioInicial   = $valor; }
/**
    * @access Public
    * @param Float $Valor
*/
function setSalarioFinal($valor) { $this->nuSalarioFinal      = $valor; }
/**
    * @access Public
    * @param Float $Valor
*/
function setPercentualDesc($valor) { $this->nuPercentualDesc    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTBeneficioFaixaDesconto($valor) { $this->obTBeneficioFaixaDesconto = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                      = $valor; }

/**
    * @access Public
    * @return Array
*/
function getFaixa() { return $this->arFaixa            ; }
/**
    * @access Public
    * @return Integer
*/
function getCodFaixa() { return $this->inCodFaixa         ; }
/**
    * @access Public
    * @return Float
*/
function getSalarioInicial() { return $this->nuSalarioInicial   ; }
/**
    * @access Public
    * @return Float
*/
function getSalarioFinal() { return $this->nuSalarioFinal     ; }

/**
    * @access Public
    * @return Float
*/
function getPercentualDesc() { return $this->nuPercentualDesc   ; }
/**
    * @access Public
    * @return Object
*/
function getTBeneficioFaixaDesconto() { return $this->obTBeneficioFaixaDesconto; }
/**
     * @access Public
     * @param Object $valor
*/
function getTransacao() { return $this->obTransacao;                         }

/**
     * Método construtor
     * @access Private
*/
function RBeneficioFaixaDesconto(&$roBeneficioVigencia)
{
    $this->setTBeneficioFaixaDesconto ( new TBeneficioFaixaDesconto       );
    $this->obTransacao               = new Transacao                     ;
    $this->roBeneficioVigencia        = &$roBeneficioVigencia;
    $this->setFaixa                   = array();
}

/**
    * Salva dados de Faixas de Desconto  no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirFaixaDesconto($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $rsFaixa = new RecordSet;
    if ( !$obErro->ocorreu() ) {
        if ( $this->getFaixa() ) {
            $obErro = $this->excluirFaixaDesconto( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arFaixa = $this->getFaixa();
                $rsFaixa->preenche($arFaixa);
                while ( !$rsFaixa->eof() ) {
                    $obErro = $this->obTBeneficioFaixaDesconto->proximoCod( $inCodFaixa , $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $this->obTBeneficioFaixaDesconto->setDado("cod_faixa", $inCodFaixa );
                        $this->obTBeneficioFaixaDesconto->setDado("cod_vigencia" , $this->roBeneficioVigencia->getCodVigencia() );
                        $this->obTBeneficioFaixaDesconto->setDado("vl_inicial", $rsFaixa->getCampo("flSalarioInicial")          );
                        $this->obTBeneficioFaixaDesconto->setDado("vl_final"  , $rsFaixa->getCampo("flSalarioFinal")            );
                        $this->obTBeneficioFaixaDesconto->setDado("percentual_desconto", $rsFaixa->getCampo("flPercentualDesc") );
                        $obErro = $this->obTBeneficioFaixaDesconto->inclusao( $boTransacao );
                    }
                    $rsFaixa->proximo();
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/** Exclui registro do banco
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirFaixaDesconto($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTBeneficioFaixaDesconto->setCampoCod("cod_vigencia" );
        $this->obTBeneficioFaixaDesconto->setDado("cod_vigencia", $this->roBeneficioVigencia->getCodVigencia() );
        $obErro = $this->obTBeneficioFaixaDesconto->exclusao( $boTransacao );
        $this->obTBeneficioFaixaDesconto->setCampoCod('cod_faixa');
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarFaixaDesconto(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if( $this->roBeneficioVigencia->getCodVigencia() )
        $stFiltro .= " WHERE cod_vigencia = " . $this->roBeneficioVigencia->getCodVigencia();
    $stOrder = ($stOrder)?$stOrder:" ORDER BY percentual_desconto ASC ";
    $obErro = $this->obTBeneficioFaixaDesconto->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recupera na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarUltimaVigencia(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if ( $this->roBeneficioVigencia->getCodVigencia() ) {
        $stFiltro .= " AND cod_vigencia = " . $this->roBeneficioVigencia->getCodVigencia();
    }
//     $stOrder = ($stOrder)?$stOrder:" ORDER BY percentual_desconto ASC ";
    $obErro = $this->obTBeneficioFaixaDesconto->recuperaUltimaVigencia( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
