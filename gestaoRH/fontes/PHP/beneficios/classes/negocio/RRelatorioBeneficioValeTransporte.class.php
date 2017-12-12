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
* Classe de regra de relatório para Vale-Transporte
* Data de Criação: 14/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra de Relatório

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.06.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO      );
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioValeTransporte.class.php"      );

class RRelatorioBeneficioValeTransporte extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRBeneficioValeTransporte;

/**
     * @access Public
     * @param Object $valor
*/
function setRBeneficioValeTransporte($valor) { $this->obRBeneficioValeTransporte = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getRBeneficioValeTransporte() { return $this->obRBeneficioValeTransporte;           }

/**
    * Método Construtor
    * @access Private
*/
function RRelatorioBeneficioValeTransporte()
{
    $this->setRBeneficioValeTransporte( new RBeneficioValeTransporte );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsVT , $stFiltro , $stOrder = "nom_cgm")
{
    $this->obRBeneficioValeTransporte->listarValeTransporteRelatorio( $rsRecordSet, $stFiltro , $stOrder );

    $arVT    = array();
    $inCount = 0;
    $rsRecordSet->addFormatacao('custo','NUMERIC_BR');
    while ( !$rsRecordSet->eof() ) {
        $arVT[$inCount]['numcgm']        = $rsRecordSet->getCampo('numcgm');
        $arVT[$inCount]['nom_cgm']       = $rsRecordSet->getCampo('nom_cgm');
        $arVT[$inCount]['origem']        = $rsRecordSet->getCampo('nom_municipio_o')."/".$rsRecordSet->getCampo('origem');
        $arVT[$inCount]['destino']       = $rsRecordSet->getCampo('nom_municipio_d')."/".$rsRecordSet->getCampo('destino');
        $arVT[$inCount]['custo']         = $rsRecordSet->getCampo('custo');
        $arVT[$inCount]['vigencia']      = $rsRecordSet->getCampo('vigencia');
        $inCount++;
        $rsRecordSet->proximo();
    }

    $rsVT = new RecordSet;

    $rsVT->preenche( $arVT );

    return $obErro;
}

}
