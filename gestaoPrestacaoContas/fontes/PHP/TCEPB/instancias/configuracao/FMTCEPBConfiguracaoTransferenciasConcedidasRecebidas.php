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
    * Página de Formulário para configuração
    * Data de Criação   : 23/09/2014

    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM

    $Id: FMTCEPBConfiguracaoTransferenciasConcedidasRecebidas.php 59957 2014-09-23 19:17:01Z michel $

    * Casos de uso :
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TTPB.'TTPBPlanoAnaliticaTipoTransferencia.class.php';

$stPrograma = "TCEPBConfiguracaoTransferenciasConcedidasRecebidas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

if ($inCodigo) {
    $stLocation .= "&inCodigo=$inCodigo";
}

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obLabel = new Label();
$obLabel->setValue("Os arquivos serão agrupados pelo código informado para as entidades");

$obTTPBPlanoAnaliticaTipoTransferencia = new TTPBPlanoAnaliticaTipoTransferencia();
$stOrder = "    ORDER BY    plano_conta.cod_estrutural   ";
$stFiltro = "";

//Lista de Contas Recebidas
$obTTPBPlanoAnaliticaTipoTransferencia->recuperaContaTransferenciaRecebida( $rsContasRecebidas, $stFiltro, $stOrder );

//Lista de Contas Concedidas
$obTTPBPlanoAnaliticaTipoTransferencia->recuperaContaTransferenciaConcedidas( $rsContasConcedidas, $stFiltro, $stOrder );

//Lista Tipo de Transferencia
$obTTPBPlanoAnaliticaTipoTransferencia->recuperaTipoTransferencia( $rsTipoTransferencia );
$arTipoTransferencia = $rsTipoTransferencia->arElementos;

$obCmbTipo = new Select();
$obCmbTipo->setName( 'inTransferencia_[cod_conta]' );
$obCmbTipo->setId( 'inTransferencia' );
$obCmbTipo->addOption( '','Selecione' );

foreach($arTipoTransferencia AS $key=>$value){
    $obCmbTipo->addOption( $value['cod_tipo'], $value['descricao']);
}
$obCmbTipo->setValue( 'cod_tipo_transferencia' );

//RECEBIDAS
$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Recebidas');
$obLista->setRecordSet($rsContasRecebidas);

//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Reduzido',5 );
$obLista->addCabecalho('Estrutural', 25);
$obLista->addCabecalho('Descrição da Conta', 35);
$obLista->addCabecalho('Tipo de Transferência', 15);

//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('cod_plano');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('cod_estrutural');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('nom_conta');
$obLista->commitDado();

$obLista->addDadoComponente( $obCmbTipo , false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('cod_tipo_transferencia');
$obLista->commitDadoComponente();

$obSpnRecebidas = new Span();
$obSpnRecebidas->setId('spnRecebidas');
$obLista->montaHTML();
$obSpnRecebidas->setValue($obLista->getHTML());

//CONCEDIDAS
$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Concedidas');
$obLista->setRecordSet($rsContasConcedidas);

//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Reduzido',5 );
$obLista->addCabecalho('Estrutural', 25);
$obLista->addCabecalho('Descrição da Conta', 35);
$obLista->addCabecalho('Tipo de Transferência', 15);

//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('cod_plano');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('cod_estrutural');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('nom_conta');
$obLista->commitDado();

$obLista->addDadoComponente( $obCmbTipo , false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('cod_tipo_transferencia');
$obLista->commitDadoComponente();

$obSpnConcedidas = new Span();
$obSpnConcedidas->setId('spnConcedidas');
$obLista->montaHTML();
$obSpnConcedidas->setValue($obLista->getHTML());

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);

$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addHidden            ($obHdnCtrl);
$obFormulario->addTitulo            ( "Grupos de Contas de Transferências Concedidas/Recebidas" );
$obFormulario->addSpan              ($obSpnRecebidas);
$obFormulario->addSpan              ($obSpnConcedidas);

$obFormulario->OK(true);
$obFormulario->show();

SistemaLegado::LiberaFrames(true, false);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
