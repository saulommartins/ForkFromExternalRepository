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
    * Página de Oculto do Calculo de Férias
    * Data de Criação: 07/07/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.19

    $Id: PRRelatorioCalculoFerias.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"                                                        );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoFerias.class.php"                   );

$obTFolhaPagamentoLogErroCalculoFerias = new TFolhaPagamentoLogErroCalculoFerias;
$stCodContratos = "";
$rsContratos = Sessao::read('arContratos');
$arContratos = $rsContratos->getElementos();
foreach ($arContratos as $arContrato) {
    $stCodContratos .= $arContrato['cod_contrato'].",";
}
$stCodContratos = substr($stCodContratos,0,strlen($stCodContratos)-1);
$stFiltro .= " AND registro_evento_ferias.cod_contrato IN (".$stCodContratos.")";
$obTFolhaPagamentoLogErroCalculoFerias->recuperaErrosDoContrato($rsErros,$stFiltro);
$arErro = array();
$inContador = 0;
while (!$rsErros->eof()) {
    $inContador++;
    $arEventos = array();
    $arEventos['evento']        = $rsErros->getCampo("codigo");
    $arEventos['descricao']     = $rsErros->getCampo("descricao");
    $arEventos['erro']          = $rsErros->getCampo("erro");
    $arEventos['cod_contrato']  = $rsErros->getCampo("cod_contrato");
    $arErro[]               = $arEventos;
    if ( ($rsErros->getCampo("cod_contrato") != $arEventos['cod_contrato']) or $rsErros->getNumLinhas() == $inContador ) {
        $arContrato['contrato'][] = array("campo1"=>$rsErros->getCampo("registro"),"campo2"=>$rsErros->getCampo("numcgm")."-".$rsErros->getCampo("nom_cgm"));
        $arContrato['erros']      = $arErro;
        $arErros[] = $arContrato;
    }
    $rsErros->proximo();
}

$obRRelatorio   = new RRelatorio;
$rsErros        = new RecordSet;
$rsErros->preenche($arErros);
Sessao::write("rsErros",$rsErros);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioCalculoFolhaFerias.php" );

?>
