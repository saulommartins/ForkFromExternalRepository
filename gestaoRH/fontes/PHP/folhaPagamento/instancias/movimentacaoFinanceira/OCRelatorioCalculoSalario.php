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
    * culto para geração do recordset do relatório
    * Data de Criação: 08/12/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30924 $
    $Name$
    $Author: souzadl $
    $Date: 2007-05-11 10:07:40 -0300 (Sex, 11 Mai 2007) $

    * Casos de uso: uc-04.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"                                                        );
include_once ( CAM_GRH_FOL_NEGOCIO."RRelatorioCalculoFolhaPagamento.class.php"                          );

$obRRelatorio                      = new RRelatorio;
$obRRelatorioCalculoFolhaPagamento = new RRelatorioCalculoFolhaPagamento;
$rsRecordset                       = new Recordset;

$rsRecordset = Sessao::read('rsListaErro');

$arRecordSet = array();
while (!$rsRecordset->eof()) {
    $stErro = wordwrap($rsRecordset->getCampo("erro"),75,"#");
    $arErro = explode("#",$stErro);
    foreach ($arErro as $inIndex=>$stErro) {
        if ($inIndex === 0) {
            $arTemp["registro"] = $rsRecordset->getCampo("registro");
            $arTemp["cgm"]      = $rsRecordset->getCampo("numcgm")."-".$rsRecordset->getCampo("nom_cgm");
            $arTemp["codigo"]   = $rsRecordset->getCampo("codigo");
        } else {
            $arTemp["registro"] = "";
            $arTemp["cgm"]      = "";
            $arTemp["codigo"]   = "";
        }
        $arTemp["erro"]     = $stErro;
        $arRecordSet[] = $arTemp;
    }
    $rsRecordset->proximo();
}
$rsRecordset = new RecordSet();
$rsRecordset->preenche($arRecordSet);

Sessao::write("rsErros",$rsRecordset);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioCalculoSalario.php" );
?>
