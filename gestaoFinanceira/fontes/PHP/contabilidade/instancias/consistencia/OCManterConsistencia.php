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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 24/05/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    * $Id: OCManterConsistencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.32
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_CONT_MAPEAMENTO."FContabilidadeConsistencia.class.php";
include_once CAM_FW_PDF."RRelatorio.class.php";

$obRRelatorio = new RRelatorio;

//seta elementos do filtro
$stFiltro = "";

//seta elementos do filtro para ENTIDADE
$arFiltro = Sessao::read('filtroRelatorio');

if ($arFiltro['inCodEntidade'] != "") {
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stFiltro .= $valor." , ";
    }
    $stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 2 );
} else {
    $stFiltro .= $arFiltro['stTodasEntidades'];
}

function geraRecordSet()
{
    $arRecordSet = Sessao::read('arRecordSet');
    $obFContabilidadeConsistencia = new FContabilidadeConsistencia;
    $rsRecordSet6 = new RecordSet;

    $obErro = $obFContabilidadeConsistencia->recuperaConsistencia2( $rsRecordSet2);
    if(!$obErro->ocorreu() && !$rsRecordSet2->eof()) $arRecordSet[2] = $rsRecordSet2;

    $obErro = $obFContabilidadeConsistencia->recuperaConsistencia3( $rsRecordSet3);
    if(!$obErro->ocorreu() && !$rsRecordSet3->eof()) $arRecordSet[3] = $rsRecordSet3;

    $obErro = $obFContabilidadeConsistencia->recuperaConsistencia4( $rsRecordSet4);
    if(!$obErro->ocorreu()  && !$rsRecordSet4->eof())  $arRecordSet[4] = $rsRecordSet4;

    $obErro = $obFContabilidadeConsistencia->recuperaConsistencia5( $rsRecordSet5);
    if(!$obErro->ocorreu() && !$rsRecordSet5->eof()) $arRecordSet[5] = $rsRecordSet5;

    $obErro = $obFContabilidadeConsistencia->recuperaConsistencia6( $rsRecordSet6);
    if(!$obErro->ocorreu() && !$rsRecordSet6->eof()) $arRecordSet[6] = $rsRecordSet6;

    $obErro =$obFContabilidadeConsistencia->recuperaConsistencia7($rsRecordSet7,"where tipo <> 'M' and tipo <> 'I' ");
    if(!$obErro->ocorreu() && !$rsRecordSet7->eof()) $arRecordSet[7] = $rsRecordSet7;

    $obErro = $obFContabilidadeConsistencia->recuperaConsistencia8($rsRecordSet8,"where lancamentos < 6 ");
    if(!$obErro->ocorreu() && !$rsRecordSet8->eof()) $arRecordSet[8] = $rsRecordSet8;

    $obErro = $obFContabilidadeConsistencia->recuperaConsistencia9($rsRecordSet9);
    if(!$obErro->ocorreu() && !$rsRecordSet9->eof()) $arRecordSet[9] = $rsRecordSet9;

    $obErro = $obFContabilidadeConsistencia->recuperaConsistencia10( $rsRecordSet10);
    if(!$obErro->ocorreu() && !$rsRecordSet10->eof()) $arRecordSet[10] = $rsRecordSet10;

    $obErro = $obFContabilidadeConsistencia->recuperaConsistencia11($rsRecordSet11,"where natureza_saldo <> '' ");
    if(!$obErro->ocorreu() && !$rsRecordSet11->eof()) $arRecordSet[11]=$rsRecordSet11;

    sessao::write('arRecordSet',$arRecordSet );

}

$obFContabilidadeConsistencia        = new FContabilidadeConsistencia;
$obFContabilidadeConsistencia->setDado("stExercicio", Sessao::getExercicio());
$obFContabilidadeConsistencia->setDado("stEntidades", $stFiltro);
$obFContabilidadeConsistencia->setDado("stDtInicial", $arFiltro['stDataInicial']);
$obFContabilidadeConsistencia->setDado("stDtFinal",   $arFiltro['stDataFinal']);
$obErro = $obFContabilidadeConsistencia->recuperaTodos( $rsRecordSet, "" , "" );

if (!$obErro->ocorreu()) {
     geraRecordSet();
     $obRRelatorio->executaFrameOculto( "OCGeraRelatorioConsistencia.php" );
}
?>
