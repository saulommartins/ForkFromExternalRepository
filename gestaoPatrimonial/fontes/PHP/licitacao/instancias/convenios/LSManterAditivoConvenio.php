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
    * Data de Criação: 15/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    $Revision: 26126 $
    $Name$
    $Author: girardi $
    $Date: 2007-10-16 17:23:35 -0200 (Ter, 16 Out 2007) $

    * Casos de uso : uc-03.05.29
*/

/*
$Log:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TLIC."TLicitacaoConvenio.class.php");
include_once(TLIC."TLicitacaoConvenioAditivos.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterAditivoConvenio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProx = $pgForm;
$stCaminho = CAM_GP_LIC_INSTANCIAS."convenios/";

$stFiltroConvenioAditivo = Sessao::read('requestConvenioAditivo');

if (count($stFiltroConvenioAditivo) < 1) {
    $stFiltroConvenioAditivo = $_REQUEST;
    Sessao::write('requestConvenioAditivo',$_REQUEST);
} else {
    //Sessao::remove('requestConvenioAditivo');
}

//Define a função do arquivo, ex: incluir, alterar, anular, etc
$stAcao = $stFiltroConvenioAditivo['stAcao'];
$stLink = "&stAcao=".$stAcao;

$stFiltro = ($stFiltro)?" \nAND ".substr($stFiltro,0,strlen($stFiltro)-4):'';

$rsLista = new RecordSet;
$obLista = new Lista;
$obTLicitacaoConvenio = ($stAcao == "incluir" ? new TLicitacaoConvenio : new TLicitacaoConvenioAditivos);

$obTLicitacaoConvenio->setDado('exercicio'       , $stFiltroConvenioAditivo['stExercicio']      );
$obTLicitacaoConvenio->setDado('dt_assinatura'   , $stFiltroConvenioAditivo['dtConvenio']       );
$obTLicitacaoConvenio->setDado('num_convenio'    , $stFiltroConvenioAditivo['inNumConvenio']    );
$obTLicitacaoConvenio->setDado('num_participante', $stFiltroConvenioAditivo['inCodParticipante']);

if ($stAcao != "incluir") {
    if ($stFiltroConvenioAditivo['inCodParticipante']!='') {
        $stFiltro .=  " AND participante_convenio.cgm_fornecedor = ".$stFiltroConvenioAditivo['inCodParticipante']." \n";
    }
    $stFiltro .=  " AND NOT EXISTS (SELECT 1                                                             \n";
    $stFiltro .=  "               FROM licitacao.convenio_aditivos_anulacao                          \n";
    $stFiltro .=  "              WHERE convenio_aditivos.exercicio_convenio = convenio_aditivos_anulacao.exercicio_convenio \n ";
    $stFiltro .=  "                AND convenio_aditivos.num_convenio = convenio_aditivos_anulacao.num_convenio \n";
    $stFiltro .=  "                AND convenio_aditivos.exercicio = convenio_aditivos_anulacao.exercicio \n";
    $stFiltro .=  "                AND convenio_aditivos.num_aditivo = convenio_aditivos_anulacao.num_aditivo) \n";

    $obTLicitacaoConvenio->setDado('num_aditivo', $stFiltroConvenioAditivo['inNumAditivo']      );
    $obTLicitacaoConvenio->setDado('', $stFiltroConvenioAditivo['stExercicioAditivo']);
}

    $stFiltro .=  " AND NOT EXISTS( SELECT 1                                                             \n";
    $stFiltro .=  "                   FROM licitacao.rescisao_convenio                                   \n";
    $stFiltro .=  "                  WHERE convenio.num_convenio = rescisao_convenio.num_convenio        \n";
    $stFiltro .=  "                    AND convenio.exercicio    = rescisao_convenio.exercicio_convenio) \n";

$obTLicitacaoConvenio->recuperaConvenioListagem( $rsLista, $stFiltro );

$obLista->obPaginacao->setFiltro("&stLink=".$stLink);
$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Convênios cadastrados");
$obLista->addCabecalho();

$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Convênio" );
$obLista->ultimoCabecalho->setWidth( 4 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data Convênio" );
$obLista->ultimoCabecalho->setWidth( 4 );
$obLista->commitCabecalho();

if ($stAcao != "incluir") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Aditivo" );
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Aditivo" );
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Objeto" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

//ADICIONAR DADOS

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[num_convenio]/[exercicio]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_assinatura" );
$obLista->commitDado();

if ($stAcao != "incluir") {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "[num_aditivo]/[exercicio_aditivo]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "dt_assinatura_aditivo" );
    $obLista->commitDado();
}

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "objeto_descricao" );
$obLista->commitDado();

$obLista->addAcao();

if ($stAcao == 'incluir') {
    $obLista->ultimaAcao->setAcao ('Selecionar');
} else {
    $obLista->ultimaAcao->setAcao ( $stAcao );
}

$obLista->ultimaAcao->addCampo( "&inNumConvenio", "num_convenio" );
$obLista->ultimaAcao->addCampo( "&stExercicio", "exercicio" );
if ($stAcao != 'incluir') {
    $obLista->ultimaAcao->addCampo( "&inNumeroAditivo", "num_aditivo" );
    $obLista->ultimaAcao->addCampo( "&stExercicioAditivo", "exercicio_aditivo" );
    $obLista->ultimaAcao->addCampo( "&inCodRespJuridico", "responsavel_juridico" );
}
$obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();
?>
