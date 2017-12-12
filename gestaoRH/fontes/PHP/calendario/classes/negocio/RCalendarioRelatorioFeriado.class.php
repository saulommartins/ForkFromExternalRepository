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
    * Classe de Regra de Negócio Relatorio de Feriados
    * Data de Criação   : 03/09/2004

    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso :uc-04.02.03

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
// include_once ( "../../../bibliotecas/mascaras.lib.php"            );
include_once ( CLA_PERSISTENTE_RELATORIO );
include_once ( CAM_GRH_CAL_NEGOCIO."RCalendarioFeriado.class.php" );

/**
    * Classe de Regra de Negócio Itens
    * @author Desenvolvedor: Eduardo Martins
*/
class RRelatorioFeriado extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRFeriado;

/**
     * @access Public
     * @param Object $valor
*/
function setRFeriado($valor) { $this->obRFeriado = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getRFeriado() { return $this->obRFeriado;           }

/**
    * Método Construtor
    * @access Private
*/
function RRelatorioFeriado()
{
    $this->setRFeriado( new RFeriado );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsFeriadoVariavel , &$rsFeriadoFixo ,&$rsPontoFacultativo,&$rsDiaCompensado, $stOrder)
{
     $stOrder = "dt_feriado ASC";

    $obErro = $this->obRFeriado->listar( $rsRecordSet, $stFiltro, $stOrder,$boTransacao );

    $arFeriadoFixo     = array();
    $arFeriadoVariavel = array();
    $inCountFixo     = 0;
    $inCountVariavel = 0;
    $inCountPonto    = 0;
    $inDia           = 0;

    while ( !$rsRecordSet->eof() ) {
        if ( $rsRecordSet->getCampo('tipoferiado') == 'Fixo' ) {
            $arFeriadoFixo[$inCountFixo]['dt_feriado']  = $rsRecordSet->getCampo('dt_feriado');
//            $arFeriadoFixo[$inCountFixo]['dt_feriado']  = substr($rsRecordSet->getCampo('dt_feriado'),0,5);
            $arFeriadoFixo[$inCountFixo]['tipo']        = $rsRecordSet->getCampo('tipoferiado');
            $arFeriadoFixo[$inCountFixo]['tipoferiado'] = $rsRecordSet->getCampo('abrangencia');
            $arFeriadoFixo[$inCountFixo]['descricao']   = $rsRecordSet->getCampo('descricao');
            $inCountFixo++;
        } elseif ( $rsRecordSet->getCampo('tipoferiado') == 'Variável' ) {
            $arFeriadoVariavel[$inCountVariavel]['dt_feriado']  = $rsRecordSet->getCampo('dt_feriado');
            $arFeriadoVariavel[$inCountVariavel]['tipo']        = $rsRecordSet->getCampo('tipoferiado');
            $arFeriadoVariavel[$inCountVariavel]['tipoferiado'] = $rsRecordSet->getCampo('abrangencia');
            $arFeriadoVariavel[$inCountVariavel]['descricao']   = $rsRecordSet->getCampo('descricao');
            $inCountVariavel++;
        } elseif ( $rsRecordSet->getCampo('tipoferiado') == 'Ponto facultativo' ) {
            $arPontoFacultativo[$inCountPonto]['dt_feriado']  = $rsRecordSet->getCampo('dt_feriado');
            $arPontoFacultativo[$inCountPonto]['tipo']        = $rsRecordSet->getCampo('tipoferiado');
            $arPontoFacultativo[$inCountPonto]['descricao']   = $rsRecordSet->getCampo('descricao');
            $inCountPonto++;
        } elseif ( $rsRecordSet->getCampo('tipoferiado') == 'Dia compensado' ) {
            $arDiaCompensado[$inDia]['dt_feriado']  = $rsRecordSet->getCampo('dt_feriado');
            $arDiaCompensado[$inDia]['tipo']        = $rsRecordSet->getCampo('tipoferiado');
            $arDiaCompensado[$inDia]['descricao']   = $rsRecordSet->getCampo('descricao');
            $inDia++;
        }
        $rsRecordSet->proximo();
    }

    $rsFeriadoVariavel  = new RecordSet;
    $rsFeriadoFixo      = new RecordSet;
    $rsPontoFacultativo = new RecordSet;
    $rsDiaCompensado    = new RecordSet;

    $rsFeriadoVariavel->preenche( $arFeriadoVariavel );
    $rsFeriadoFixo->preenche( $arFeriadoFixo );
    if ($arPontoFacultativo) {$rsPontoFacultativo->preenche( $arPontoFacultativo );};
    if ($arDiaCompensado) {$rsDiaCompensado->preenche( $arDiaCompensado );};

    return $obErro;
}

}
