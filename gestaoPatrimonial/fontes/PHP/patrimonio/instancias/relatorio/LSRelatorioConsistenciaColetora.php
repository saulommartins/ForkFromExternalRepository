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
    * Data de Criação   : 13/08/2010

    * @description Tela filtro para escolher arquivo para relatório

    * @author Desenvolvedor: Tonismar R. Bernardo

    * $Id: LSRelatorioConsistenciaColetora.php 66466 2016-08-31 14:34:38Z michel $

    * @ignore
*/
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetora.class.php';

$caminho = CAM_GP_PAT_INSTANCIAS.'relatorio/';
$stAcao = $_POST['stAcao'] ? $_POST['stAcao'] : $_GET['stAcao'];
$link = '&stAcao='.$stAcao;

$stDataInicial         = $_REQUEST['stDataInicial'];
$stDataFinal           = $_REQUEST['stDataFinal'];
$tipoRelatorio         = $_REQUEST['tipoRelatorio'];
$arrLocaisSelecionados = $_REQUEST['locaisSelecionados'];
$locaisSelecionados    = implode(',' , $arrLocaisSelecionados );
$inCodEntidade         = $_REQUEST['inCodEntidade'];

$arFiltro['stDataInicial']      = $stDataInicial;
$arFiltro['stDataFinal']        = $stDataFinal;
$arFiltro['locaisSelecionados'] = $locaisSelecionados;
$arFiltro['tipoRelatorio']      = $tipoRelatorio;
$arFiltro['inCodEntidade']      = $inCodEntidade;

Sessao::write('arFiltro' , $arFiltro );

$model = new TPatrimonioArquivoColetora;

switch ($tipoRelatorio) {
    case "divergencia":
        $stTipoRelatorio = "AND arquivo_coletora_consistencia.status ='Divergente'";
    break;

    case "naoLidos":
        $stTipoRelatorio = "AND arquivo_coletora_consistencia.status <>'Não cadastrado'";
    break;

    case "semDivergencia":
        $stTipoRelatorio = "AND arquivo_coletora_consistencia.status ='Sem divergência'";
    break;
}

$filtro = "
           WHERE arquivo_coletora.timestamp Between to_date('".$stDataInicial."', 'dd/mm/yyyy') and to_date('".$stDataFinal."', 'dd/mm/yyyy')
             ".$stTipoRelatorio."
             AND bem_comprado.cod_entidade  = ".$inCodEntidade."
        GROUP BY arquivo_coletora.codigo
               , arquivo_coletora.nome
        ";

$model->recuperaRelatorioConsistencia( $dados, $filtro );

$lista = new Lista;
$lista->setRecordSet( $dados );

$lista->addCabecalho();
$lista->ultimoCabecalho->addConteudo( '&nbsp;' );
$lista->ultimoCabecalho->setWidth( 5 );
$lista->commitCabecalho();

$lista->addCabecalho();
$lista->ultimoCabecalho->addConteudo( 'Código' );
$lista->ultimoCabecalho->setWidth( 3 );
$lista->commitCabecalho();

$lista->addCabecalho();
$lista->ultimoCabecalho->addConteudo( 'Nome' );
$lista->ultimoCabecalho->setWidth( 20 );
$lista->commitCabecalho();

$lista->addCabecalho();
$lista->ultimoCabecalho->addConteudo( '&nbsp;' );
$lista->ultimoCabecalho->setWidth( 3 );
$lista->commitCabecalho();

$lista->addDado();
$lista->ultimoDado->setAlinhamento( 'CENTRO' );
$lista->ultimoDado->setCampo( 'codigo' );
$lista->commitDado();

$lista->addDado();
$lista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$lista->ultimoDado->setCampo( 'nome' );
$lista->commitDado();

$lista->addAcao();
$lista->ultimaAcao->setAcao( $stAcao );
$lista->ultimaAcao->addCampo( '&codigo', 'codigo' );
$lista->ultimaAcao->addCampo( '&stDescQuestao', 'codigo' );
$lista->ultimaAcao->setLink( $caminho.'OCRelatorioConsistenciaColetora.php?'.Sessao::getId().$link );
$lista->commitAcao();

$lista->show();
