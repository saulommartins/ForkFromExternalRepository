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
* Página de Listagem dos Documentos
* Data de Criação   : 06/10/2008

* @author Analista: Gelson Gonsalves
* @author Desenvolvedor: Luiz Felipe Prestes Teixeira

* @ignore

* $Id: $

* Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TCOM."TComprasContratoCompraDireta.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgAnular = "FMAnularContrato.php";
$pgFormResc = "FMManterRescindirContrato.php";

$stCaminho = CAM_GP_COM_INSTANCIAS."contrato/";

$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "excluirCD";
}

switch ($stAcao) {
    case 'alterarCD': $pgProx = $pgForm; break;
    case 'anularCD':  $pgProx = $pgAnular; break;
    case 'rescindir': $pgProx = $pgFormResc; break;
}

if ($_REQUEST['inNumContrato'] == "") {
    $_REQUEST['inNumContrato'] = $_REQUEST['inNumContratoBusca'];
}

$stLink = "&stAcao=".$stAcao."&inNumContratoBusca=".$_REQUEST['inNumContratoBusca'];

foreach ($_REQUEST as $key => $value) {
    $param[$key]= $value;
}

Sessao::write('dadosFiltro',$param);

$filtro = Sessao::read('filtro');

if ($_REQUEST['inCodCompraDireta'] || $_REQUEST['stDataInicial'] || $_REQUEST['inCodMapa'] || $_REQUEST['inNumContrato']) {
    foreach ($_REQUEST as $key => $value) {
        $filtro[$key] = $value;
    }
} else {
    if ($filtro) {
        foreach ($filtro as $key => $value) {
            $_REQUEST[$key] = $value;
        }
    }
    Sessao::write('paginando', true);
}
Sessao::write('filtro', $filtro);

$stFiltro = montaListaFiltros();

$stFiltro = ($stFiltro)?' and '.substr($stFiltro,0,strlen($stFiltro)-4):'';

$rsLista = new RecordSet;
$obTCompraDiretaContrato = new TComprasContratoCompraDireta;
$obTCompraDiretaContrato->recuperaContratosCompraDireta($rsLista, $stFiltro );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$rsLista->addFormatacao('valor_contratado', 'NUMERIC_BR');
$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Contrato cadastrados");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entidade" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Contrato" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data do Contrato" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Valor Total" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_entidade] - [entidade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[numero_contrato]/[exercicio_contrato]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_assinatura" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "valor_contratado" );
$obLista->commitDado();

$obLista->addAcao();

if ($stAcao == 'alterarCD') {
    $obLista->ultimaAcao->setAcao ( 'ALTERAR' );
}

if ($stAcao == 'excluirCD') {
    $obLista->ultimaAcao->setAcao ( 'EXCLUIR' );
}

if ($stAcao == 'anularCD') {
    $obLista->ultimaAcao->setAcao ( 'ANULAR' );
}

if ($stAcao == 'rescindir') {
    $obLista->ultimaAcao->setAcao ( 'RESCINDIR' );
}

$obLista->ultimaAcao->addCampo( "&inNumContrato", "num_contrato" );
$obLista->ultimaAcao->addCampo( "&inCodEntidade", "cod_entidade" );
$obLista->ultimaAcao->addCampo( "stExercicio", "exercicio_contrato" );
$obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();

/**
* Monta os filtros que serão testados no sql
* @return string
*/
function montaListaFiltros()
{
    if ($_REQUEST['inCodCompraDireta']) {
        $stFiltro .= " contrato_compra_direta.cod_compra_direta = ". $_REQUEST['inCodCompraDireta']." \nand ";
    }

    if ($_REQUEST['inCodModalidade']) {
        $stFiltro .= " contrato_compra_direta.cod_modalidade = ". $_REQUEST['inCodModalidade']." \nand ";
    }

    if ($_REQUEST['stMapaCompras']) {
        $arMapaCompras = explode('/', $_REQUEST['stMapaCompras']);

        $exercicio = $arMapaCompras[1] != "" ? $arMapaCompras[1] : Sessao::getExercicio();
        $stFiltro .= " compra_direta.exercicio_mapa = ".$exercicio." \nand ";
        $stFiltro .= " compra_direta.cod_mapa = ".$arMapaCompras[0]." \nand ";
    }

    if ($_REQUEST['inNumContrato']) {
       $stFiltro .= " contrato_compra_direta.num_contrato = ". $_REQUEST['inNumContrato']." \nand ";
    }

    if ($_REQUEST['stDataInicial']) {
        $stFiltro .= " contrato.dt_assinatura between to_date('". $_REQUEST['stDataInicial']."','dd/mm/yyyy') and to_date('". $_REQUEST['stDataFinal']."', 'dd/mm/yyyy') \nand ";
    }

    if ($_REQUEST['stMes']) {
        $ano = Sessao::getExercicio();
        $inNumDiasDoMes = cal_days_in_month(CAL_GREGORIAN, $_REQUEST['stMes'], $ano);
        $inMes = $_REQUEST['stMes'];
        $stFiltro .= " contrato.dt_assinatura between to_date('01/".$inMes."/".$ano."','dd/mm/yyyy') and to_date('".$inNumDiasDoMes."/".$inMes."/".$ano."', 'dd/mm/yyyy') \nand ";
    }

    if ( is_array($_REQUEST["inNumCGM"]) ) {
       $stFiltro .= " entidade.numcgm in (".implode(",", $_REQUEST["inNumCGM"]).")  AND ";
    }elseif(isset($_REQUEST["inNumCGM"])){
        $stFiltro .= " entidade.numcgm in (".$_REQUEST["inNumCGM"].")  AND ";
    }

    $stFiltro .=  " NOT EXISTS (SELECT 1 "
                   ."\n           FROM licitacao.contrato_anulado "
                   ."\n          WHERE contrato_anulado.exercicio    = contrato.exercicio"
                   ."\n            AND contrato_anulado.cod_entidade = contrato.cod_entidade"
                   ."\n            AND contrato_anulado.num_contrato = contrato.num_contrato"
                   ."\n         )  and ";

    return $stFiltro;
}

?>
