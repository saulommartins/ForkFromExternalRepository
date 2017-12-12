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
* Classe de negócio RelatórioOrganogramaLocal
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.05.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."PersistenteRelatorio.class.php"      );
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaLocal.class.php" );

/**
    * Classe de Regra de Negócio Itens
    * @author Desenvolvedor: João Rafael Tissot
*/
class RRelatorioOrganogramaLocal extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obROrganogramaLocal;

/**
     * @access Public
     * @param Object $valor
*/
function setROrganogramaLocal($valor) { $this->obROrganogramaLocal = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getROrganogramaLocal() { return $this->obROrganogramaLocal;           }

/**
    * Método Construtor
    * @access Private
*/
function RRelatorioOrganogramaLocal()
{
    $this->setROrganogramaLocal( new ROrganogramaLocal() );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsVT , $stFiltro = "" , $stOrder = "")
{
    $this->obROrganogramaLocal->obTOrganogramaLocal->recuperaRelacionamentoRelatorio( $rsRecordSet, $stFiltro , $stOrder );

    $arVT    = array();
    $inCount = 0;

    while ( !$rsRecordSet->eof() ) {
        $inSequencia = ($inCount + 1);

        $arVT[$inCount]['sequencia']     	= $inSequencia;
        $arVT[$inCount]['descricao']        = $rsRecordSet->getCampo('descricao');
        $arVT[$inCount]['endereco']        	= $rsRecordSet->getCampo('endereco');
        $arVT[$inCount]['numero']    		= $rsRecordSet->getCampo('numero');
        $arVT[$inCount]['fone'] 			= $rsRecordSet->getCampo('fone');
        $arVT[$inCount]['dificil_acesso']   = $rsRecordSet->getCampo('dificil_acesso');
        $arVT[$inCount]['insalubre']   		= $rsRecordSet->getCampo('insalubre');

        $inCount++;
        $rsRecordSet->proximo();
    }

    $rsVT = new RecordSet;

    $rsVT->preenche( $arVT );

    return $obErro;
}

}
