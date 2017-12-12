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
* Classe de regra de relatório para Fornecedor Vale-Transporte
* Data de Criação: 13/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra de Relatório

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.06.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO      );
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioFornecedorValeTransporte.class.php" );

class RRelatorioFornecedorValeTransporte extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRBeneficioFornecedorValeTransporte;

/**
     * @access Public
     * @param Object $valor
*/
function setRBeneficioFornecedorValeTransporte($valor) { $this->obRBeneficioFornecedorValeTransporte = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getRBeneficioFornecedorValeTransporte() { return $this->obRBeneficioFornecedorValeTransporte;           }

/**
    * Método Construtor
    * @access Private
*/
function RRelatorioFornecedorValeTransporte()
{
    $this->setRBeneficioFornecedorValeTransporte( new RBeneficioFornecedorValeTransporte );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsFornecedor, $stFiltro="", $stOrder = " ORDER BY sw_cgm.nom_cgm")
{
    $this->obRBeneficioFornecedorValeTransporte->listarFornecedorValeTransporteRelatorio( $rsRecordSet, $stFiltro, $stOrder );
    $arFornecedor = array();
    $inCount       = 0;

    while ( !$rsRecordSet->eof() ) {
        $inSequencia = ($inCount + 1);
        $arFornecedor[$inCount]['sequencia']  = $inSequencia;
        $arFornecedor[$inCount]['numcgm']     = $rsRecordSet->getCampo('numcgm');
        $arFornecedor[$inCount]['nom_cgm']    = $rsRecordSet->getCampo('nom_cgm');
        $inCount++;
        $rsRecordSet->proximo();
    }

    $rsFornecedor = new RecordSet;

    $rsFornecedor->preenche( $arFornecedor );

    return $obErro;
}

}
