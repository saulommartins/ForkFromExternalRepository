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
 * Página de Formulario para Requisição
 * Data de criação : 26/01/2006

 * @author Analista: Diego Victoria
 * @author Programador: tonismar R. Bernardo

 * @ignore

 Caso de uso: uc-03.03.10

 $Id: FMManterRequisicao.php 59612 2014-09-02 12:00:51Z gelson $

 **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarifado.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoPermissaoCentroDeCustos.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoRequisicao.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoRequisicaoItemValor.class.php";
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoAtributoEstoqueMaterialValor.class.php";
include_once CAM_GP_ALM_COMPONENTES."ISelectAlmoxarifado.class.php";
include_once CAM_GP_ALM_COMPONENTES."IMontaItemQuantidade.class.php";

Sessao::remove('transf3');
Sessao::write('arItens',array());

$stAcao              = $_REQUEST['stAcao'];
$stExercicio         = $_REQUEST['stExercicio'];
$inCodRequisicao     = $_REQUEST['inCodRequisicao'];
$inCodAlmoxarifado   = $_REQUEST['inCodAlmoxarifado'];
if (isset ($_REQUEST['inCodAlmoxarifado']) &&
    isset ($_REQUEST['stExercicio']) &&
    isset ($_REQUEST['inCodRequisicao'])) {

    $obRAlmoxarifadoRequisicao = new RAlmoxarifadoRequisicao;
    $obRAlmoxarifadoRequisicao->addRequisicaoItem();
    $obRAlmoxarifadoRequisicao->setExercicio( $stExercicio );
    $obRAlmoxarifadoRequisicao->obRAlmoxarifadoAlmoxarifado->setCodigo($inCodAlmoxarifado);
    $obRAlmoxarifadoRequisicao->setCodigo( $inCodRequisicao );
    $obRAlmoxarifadoRequisicao->setAcao($stAcao);
    if (($stAcao == "consultar") || ($stAcao == "homologar") || ($stAcao == "anular_homolog")) {
        $obRAlmoxarifadoRequisicao->listarRequisicaoItemConsultar($rsLista, "", $stOrder);
        $inCGMRequisitante    = $rsLista->getCampo('cgm_requisitante');
        $stNomCGMRequisitante = $rsLista->getCampo('nom_requisitante');
    } elseif ($stAcao == "anular") {
        $obRAlmoxarifadoRequisicao->listarRequisicaoAlteracaoAnulacao($rsLista, "", $stOrder);
    } else {
        $obRAlmoxarifadoRequisicao->listarRequisicaoAlteracao($rsLista, "", $stOrder);
    }

    $dtRequisicao        = $rsLista->getCampo('dt_requisicao');
    $stObservacao        = $rsLista->getCampo('observacao');
    $inCGMSolicitante    = $rsLista->getCampo('cgm_solicitante');
    $stNomCGMSolicitante = $rsLista->getCampo('nom_solicitante');

} else {
    $dtRequisicao        = $_REQUEST['dtRequisicao'];
    $stObservacao        = $_REQUEST['stObservacao'];
    $inCGMSolicitante    = $_REQUEST['inCGMSolicitante'];
    $stNomCGMSolicitante = $_REQUEST['stNomCGMSolicitante'];
}

$obRAlmoxarifado = new RAlmoxarifadoAlmoxarifado;
$obRAlmoxarifado->setCodigo( $inCodAlmoxarifado );
$obRAlmoxarifado->listar( $rsAlmoxarifados );

//Define o nome dos arquivos PHP
$stPrograma = "ManterRequisicao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once $pgJS;

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obLblExercicio = new Label;
$obLblExercicio->setRotulo( 'Exercício' );
$obLblExercicio->setId    ( 'stExercicio' );
if (( $stAcao == 'alterar' ) || ( $stAcao == 'anular' ) || ( $stAcao == 'consultar' ) || ( $stAcao == 'homologar' ) || ($stAcao == "anular_homolog") ) {
    $obLblExercicio->setValue ( $stExercicio );
} else {
    $obLblExercicio->setValue ( Sessao::getExercicio() );
}

if (( $stAcao == 'alterar' ) || ( $stAcao == 'anular' ) || ( $stAcao == 'consultar' ) || ( $stAcao == 'homologar' ) || ($stAcao == "anular_homolog") ) {

    $obHdnExercicio = new Hidden;
    $obHdnExercicio->setName ( 'stExercicio' );
    $obHdnExercicio->setValue( $stExercicio  );

    $obLblCodigoRequisicao = new Label();
    $obLblCodigoRequisicao->setId   ( "inCodRequisicao" );
    $obLblCodigoRequisicao->setValue( $inCodRequisicao  );
    $obLblCodigoRequisicao->setRotulo( "Código" );

    $obHdnCodigoRequisicao = new Hidden();
    $obHdnCodigoRequisicao->setName( 'inCodRequisicao' );
    $obHdnCodigoRequisicao->setValue( $inCodRequisicao );

    $obLblDataRequisicao = new Label();
    $obLblDataRequisicao->setId   ( "dtRequisicao" );
    $obLblDataRequisicao->setValue( $dtRequisicao  );
    $obLblDataRequisicao->setRotulo( "Data da Requisição" );

    $obLblAlmoxarifado = new Label();
    $obLblAlmoxarifado->setId    ( "stAlmoxarifado"                                             );
    $obLblAlmoxarifado->setValue ( $inCodAlmoxarifado."-".$rsAlmoxarifados->getCampo( 'nom_a' ) );
    $obLblAlmoxarifado->setRotulo( "Almoxarifado"                                               );
    $obLblAlmoxarifado->setNull  ( false                                                        );

    $obHdnAlmoxarifado = new Hidden();
    $obHdnAlmoxarifado->setName  ( "inCodAlmoxarifado" );
    $obHdnAlmoxarifado->setValue ( $inCodAlmoxarifado  );

    $obLblRequisicao = new Label();
    $obLblRequisicao->setRotulo( "Data da Requisição" );
    $obLblRequisicao->setName  ( "dtRequisicao"       );
    $obLblRequisicao->setValue ( $dtRequisicao        );

    $obLblSolicitante = new Label();
    $obLblSolicitante->setRotulo( "Solicitante" );
    $obLblSolicitante->setId ( "CgmSolicitante" );
    $obLblSolicitante->setValue( $inCGMSolicitante.' - '.$stNomCGMSolicitante );

    if ($stAcao == "anular") {
        $obTxtMotivo = new TextArea;
        $obTxtMotivo->setName  ( "stMotivo" );
        $obTxtMotivo->setRotulo( "Motivo" );
        $obTxtMotivo->setTitle ( "Informe o motivo para anulação." );
        $obTxtMotivo->setCols  ( 30 );
        $obTxtMotivo->setRows  ( 5 );
        $obTxtMotivo->setValue ( $stMotivo );
        $obTxtMotivo->setNull  ( false );
    }

    if ($stAcao == "homologar") {
        $obHomologarSim = new Radio;
        $obHomologarSim->setName('boHomologar');
        $obHomologarSim->setRotulo('Homologar Requisição');
        $obHomologarSim->setTitle('Homologar a Requisição para retirada dos itens.');
        $obHomologarSim->setLabel('Sim');
        $obHomologarSim->setValue('true');
        $obHomologarSim->setChecked(true);

        $obHomologarNao = new Radio;
        $obHomologarNao->setName('boHomologar');
        $obHomologarNao->setLabel('Não');
        $obHomologarNao->setValue('false');
    }

    if ($stAcao == "anular_homolog") {
        $obAnularHomologacaoSim = new Radio;
        $obAnularHomologacaoSim->setName('boAnularHomologacao');
        $obAnularHomologacaoSim->setRotulo('Anular Homologação de Requisição');
        $obAnularHomologacaoSim->setTitle('Homologar a Homologação de Requisição impedindo a retirada dos itens.');
        $obAnularHomologacaoSim->setLabel('Sim');
        $obAnularHomologacaoSim->setValue('true');
        $obAnularHomologacaoSim->setChecked(true);

        $obAnularHomologacaoNao = new Radio;
        $obAnularHomologacaoNao->setName('boAnularHomologacao');
        $obAnularHomologacaoNao->setLabel('Não');
        $obAnularHomologacaoNao->setValue('false');
    }

    $obLblObservacao = new Label();
    $obLblObservacao->setId ( "stObservacao" );
    if (!$stObservacao) { $stObservacao = '&nbsp;'; }
    $obLblObservacao->setValue( $stObservacao );
    $obLblObservacao->setRotulo( "Observação" );

    include_once( TALM . "TAlmoxarifadoRequisicaoItens.class.php" );
    $obTAlmoxarifadoRequisicaoItens = new TAlmoxarifadoRequisicaoItens;
    $obTAlmoxarifadoRequisicaoItens->setDado( 'cod_requisicao', $_REQUEST['inCodRequisicao'] );
    $obTAlmoxarifadoRequisicaoItens->setDado( 'cod_almoxarifado', $_REQUEST['inCodAlmoxarifado'] );

    $arTmp = array();
    $inContador = 0;

    $obTAlmoxarifadoRequisicaoItens->setDado( 'exercicio', $_REQUEST['stExercicio'] );

    if ($stAcao == "alterar") {
        $obTAlmoxarifadoRequisicaoItens->recuperaItens( $rsRequisicaoItens );

        include_once( CAM_GP_ALM_NEGOCIO ."RAlmoxarifadoPermissaoCentroDeCustos.class.php" );
        $obRAlmoxarifadoPermissaoCentroCustos = new RAlmoxarifadoPermissaoCentroDeCustos;
        $obRAlmoxarifadoPermissaoCentroCustos->addCentroDeCustos();
        $obRAlmoxarifadoPermissaoCentroCustos->obRCGMPessoaFisica->setNumCGM( Sessao::read('numCgm') );

        while (!$rsRequisicaoItens->eof()) {

            $indice = 0;

            $obRAlmoxarifadoPermissaoCentroCustos->roUltimoCentro->setCodigo( $rsRequisicaoItens->getCampo('cod_centro') );
            $obRAlmoxarifadoPermissaoCentroCustos->listar( $obRPermissaoCentroCustos );

            // recupera os atributos da requisão
            $obTAlmoxarifadoAtributoRequisicaoItemValor = new TAlmoxarifadoAtributoRequisicaoItemValor;
            $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado('cod_item',$rsRequisicaoItens->getCampo('cod_item'));
            $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado('cod_centro',$rsRequisicaoItens->getCampo('cod_centro'));
            $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado('cod_marca',$rsRequisicaoItens->getCampo('cod_marca'));
            $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado('cod_almoxarifado',$_REQUEST['inCodAlmoxarifado']);
            $obTAlmoxarifadoAtributoRequisicaoItemValor->setDado('cod_requisicao',$_REQUEST['inCodRequisicao']);
            $obTAlmoxarifadoAtributoRequisicaoItemValor->recuperaAtributosItemRequisicao($rsAtributos);

            ///recupera os lancamentos para os atributos do item
            $obTAlmoxarifadoAtributoEstoqueMaterialValor = new TAlmoxarifadoAtributoEstoqueMaterialValor;
            $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_item',$rsRequisicaoItens->getCampo('cod_item'));
            $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_centro',$rsRequisicaoItens->getCampo('cod_centro'));
            $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_marca',$rsRequisicaoItens->getCampo('cod_marca'));
            $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_almoxarifado',$_REQUEST['inCodAlmoxarifado']);
            $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_atributo',$rsAtributos->getCampo('cod_atributo'));
            $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_modulo',$rsAtributos->getCampo('cod_modulo'));
            $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_cadastro',$rsAtributos->getCampo('cod_cadastro'));
            $obTAlmoxarifadoAtributoEstoqueMaterialValor->recuperaLancamentoValoresAtributo($rsLancamentos);

            $indiceAtributos = 0;
            $possuiAtributos = false;
            while (!$rsAtributos->eof()) {

                // recupera o saldo dos atributos conforme seu lancamento.
                $obTAlmoxarifadoAtributoEstoqueMaterialValor->setDado('cod_lancamento',$rsLancamentos->getCampo('cod_lancamento'));
                $obTAlmoxarifadoAtributoEstoqueMaterialValor->recuperaSaldoDinamico($rsSaldoAtributo);

                $atributosItemRequisicao[$indice]['stValoresAtributosLista'] = "<b>".$rsAtributos->getCampo('nom_atributo').": ".$rsAtributos->getCampo('valor')."</b>";
                $atributosItemRequisicao[$indice]['quantidade'] = $rsAtributos->getCampo('quantidade');
                $atributosItemRequisicao[$indice]['saldo_atributo'] = $rsSaldoAtributo->getCampo('qtd');
                $atributosItemRequisicao[$indice]['stValoresAtributos'] = $rsAtributos->getCampo('valor');
                $atributosItemRequisicao[$indice]['atributo'][$indiceAtributos]['cod_atributo'] = $rsAtributos->getCampo('cod_atributo');
                $atributosItemRequisicao[$indice]['atributo'][$indiceAtributos]['nom_atributo'] = $rsAtributos->getCampo('nom_atributo');
                $atributosItemRequisicao[$indice]['atributo'][$indiceAtributos]['valor'] = $rsAtributos->getCampo('valor');

                $possuiAtributos = "true";
                $indice++;
                $rsLancamentos->proximo();
                $rsAtributos->proximo();
            }

            if ( $obRPermissaoCentroCustos->getNumLinhas() <= 0 ) {
                $arTmp[$inContador]['disabled'] = array( 'nuQuantidade' );
            }
            $arTmp[$inContador]['cadastrado'] = 'true';
            $arTmp[$inContador]['id'] = $inContador+1;
            $arTmp[$inContador]['cod_item'] = $rsRequisicaoItens->getCampo('cod_item');
            $arTmp[$inContador]['cod_centro'] = $rsRequisicaoItens->getCampo('cod_centro');
            $arTmp[$inContador]['cod_marca' ] = $rsRequisicaoItens->getCampo('cod_marca');
            $arTmp[$inContador]['descricao_item'] = str_replace("'","&#39;",$rsRequisicaoItens->getCampo('nom_item'));
            $arTmp[$inContador]['descricao_marca'] = str_replace("'","&#39;",$rsRequisicaoItens->getCampo('nom_marca'));
            $arTmp[$inContador]['descricao_centro'] = str_replace("'","&#39;",$rsRequisicaoItens->getCampo('nom_centro'));
            $arTmp[$inContador]['saldo_formatado'] = number_format(str_replace('.',',',$rsRequisicaoItens->getCampo('saldo_estoque')),4,',','.');
            $arTmp[$inContador]['quantidade'] = str_replace('.',',',$rsRequisicaoItens->getCampo('quantidade'));
            $arTmp[$inContador]['cod_almoxarifado'] = $inCodAlmoxarifado;
            $arTmp[$inContador]['valores_atributos'] = $atributosItemRequisicao;
            $arTmp[$inContador]['possui_atributos'] = $possuiAtributos;

            $rsRequisicaoItens->proximo();
            $inContador++;
        }
    } else {
        if ($stAcao == 'anular') {
           $obTAlmoxarifadoRequisicaoItens->recuperaRequisicaoItensAnulacao( $rsRequisicaoItensAnulacao );
        } else {
           $obTAlmoxarifadoRequisicaoItens->recuperaRequisicaoItensConsultar( $rsRequisicaoItensAnulacao );
        }
        $boStatus = false;

        while ( !$rsRequisicaoItensAnulacao->eof() ) {

            if (( $rsRequisicaoItensAnulacao->getCampo('qtd_requisitada') > $rsRequisicaoItensAnulacao->getCampo('qtd_atendida') ) || ( $stAcao != "anular" )) {
                $arTmp[$inContador]['id'] = $inContador+1;
                $arTmp[$inContador]['cod_item'] = $rsRequisicaoItensAnulacao->getCampo('cod_item');
                $arTmp[$inContador]['cod_centro'] = $rsRequisicaoItensAnulacao->getCampo('cod_centro');
                $arTmp[$inContador]['cod_marca'] = $rsRequisicaoItensAnulacao->getCampo('cod_marca');
                $arTmp[$inContador]['descricao_item'] = str_replace("'","&#39;",$rsRequisicaoItensAnulacao->getCampo('nom_item'));
                $arTmp[$inContador]['descricao_marca'] = str_replace("'","&#39;",$rsRequisicaoItensAnulacao->getCampo('nom_marca'));
                $arTmp[$inContador]['descricao_centro'] = str_replace("'","&#39;",$rsRequisicaoItensAnulacao->getCampo('nom_centro'));
                $arTmp[$inContador]['requisitada'] = number_format($rsRequisicaoItensAnulacao->getCampo('qtd_requisitada'), 4, ',', '');
                $arTmp[$inContador]['atendida'] = number_format($rsRequisicaoItensAnulacao->getCampo('qtd_atendida'), 4, ',', '');
                $arTmp[$inContador]['anulada'] = number_format($rsRequisicaoItensAnulacao->getCampo('qtd_anulada'), 4, ',', '');
                if ($stAcao == "anular") {
                    $arTmp[$inContador]['anular'] = number_format(($rsRequisicaoItensAnulacao->getCampo('qtd_requisitada')-
                                                                   $rsRequisicaoItensAnulacao->getCampo('qtd_atendida')-
                                                                   $rsRequisicaoItensAnulacao->getCampo('qtd_anulada')), 4, ',', '');
                } else {
                    $arTmp[$inContador]['devolvida'] = number_format($rsRequisicaoItensAnulacao->getCampo('qtd_devolvida'), 4, ',', '');
                    $arTmp[$inContador]['pendente'] = number_format((((
                                                                    $rsRequisicaoItensAnulacao->getCampo('qtd_requisitada')-
                                                                    $rsRequisicaoItensAnulacao->getCampo('qtd_atendida'))-
                                                                    $rsRequisicaoItensAnulacao->getCampo('qtd_anulada'))), 4, ',', '');
                }
                if ( ($rsRequisicaoItensAnulacao->getCampo('qtd_requisitada') - $rsRequisicaoItensAnulacao->getCampo('qtd_anulada')) >  $arTmp[$inContador]['pendente'] && $arTmp[$inContador]['pendente'] > 0 ) {
                    $boStatus = "1";
                } elseif ( ($rsRequisicaoItensAnulacao->getCampo('qtd_requisitada') - $rsRequisicaoItensAnulacao->getCampo('qtd_anulada')) == $arTmp[$inContador]['pendente'] ) {
                    $boStatus = "2";
                } else {
                    $boStatus = "3";
                }
                $inContador++;
            }
            $rsRequisicaoItensAnulacao->proximo();
        }

        if ($boStatus == "1") {
            $stStatus = "Parcialmente Atendida";
        } elseif ($boStatus == "2") {
            $stStatus = "Não Atendida";
        } else {
            $stStatus = "Atendida";
        }
    }

    Sessao::write('arItens', $arTmp);
    if ($stAcao == 'alterar') {
        $jsOnload = "executaFuncaoAjax('alteraItem');";
    } elseif ($stAcao == 'anular') {
        $jsOnload = "executaFuncaoAjax('anularRequisicao');";
    } elseif ( ($stAcao == 'consultar') || ($stAcao == 'homologar') || ($stAcao == "anular_homolog") ) {
        $jsOnload = "executaFuncaoAjax('consultarRequisicao');";
    }
}

$obSelectAlmoxarifado = new ISelectAlmoxarifado;
$obSelectAlmoxarifado->setId('inCodAlmoxarifado');
$obSelectAlmoxarifado->setNull( false );

$obBscItem = new IMontaItemQuantidade($obForm, $obSelectAlmoxarifado);
$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->setObrigatorioBarra(true);
$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->setComSaldo(true);
$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->setAlmoxarifadoOrigem($obSelectAlmoxarifado);

$obBscItem->obTxtQuantidade->setObrigatorioBarra(true);
$obBscItem->obCmbCentroCusto->setObrigatorioBarra(true);
$obBscItem->obCmbMarca->setObrigatorioBarra(true);
$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->setId('inCodItem');
$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->obEvento->setOnBlur('if (validaAlmoxarifado()) {'.$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->obEvento->getOnBlur().'}');
$obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem->setTipoBusca('buscaPopupItemRequisicao');

$obTxtObservacao = new TextArea;
$obTxtObservacao->setName  ( "stObservacao" );
$obTxtObservacao->setRotulo( "Observação" );
$obTxtObservacao->setTitle ( "Informe a observação para requisição." );
$obTxtObservacao->setCols  ( 30 );
$obTxtObservacao->setRows  ( 3 );
$obTxtObservacao->setValue ( $stObservacao );

$obLblRequisitante = new Label;
$obLblRequisitante->setRotulo ( 'Requisitante' );
$obLblRequisitante->setId     ( 'stRequisitante' );
if (( $stAcao == "consultar" ) || ( $stAcao == "homologar" ) || ($stAcao == "anular_homolog") ) {
    $obLblRequisitante->setValue  ( $inCGMRequisitante.' - '.$stNomCGMRequisitante);
} else {
    $obLblRequisitante->setValue  ( Sessao::read('numCgm').' - '.Sessao::read('nomCgm') );
}

$obBscCGMSolicitante = new IPopUpCGM($obForm);
$obBscCGMSolicitante->setId                    ( 'stNomCGMSolicitante' );
$obBscCGMSolicitante->setRotulo                ( 'Solicitante'  );
$obBscCGMSolicitante->setTitle                 ( 'Informe o CGM do solicitante.' );
$obBscCGMSolicitante->setTipo                  ( 'geral' );

if ($stAcao == "alterar") {
    $obBscCGMSolicitante->setValue             ( $stNomCGMSolicitante );
} else {
    $obBscCGMSolicitante->setValue             ( Sessao::read('nomCgm')  );
}

$obBscCGMSolicitante->setNull                  ( false );
$obBscCGMSolicitante->obCampoCod->setSize      (10);
$obBscCGMSolicitante->obCampoCod->setName      ( 'inCGMSolicitante' );

if ($stAcao == "alterar") {
    $obBscCGMSolicitante->obCampoCod->setValue ( $inCGMSolicitante );
} else {
    $obBscCGMSolicitante->obCampoCod->setValue     ( Sessao::read('numCgm') );
}
$obBscCGMSolicitante->obCampoCod->obEvento->setOnChange( "executaFuncaoAjax('buscaSolicitante');" );
if ( ($stAcao == "consultar") ) {
    $obLblStatus = new Label;
    $obLblStatus->setRotulo ( 'Status'  );
    $obLblStatus->setId     ( 'stStatus' );
    $obLblStatus->setValue  ( $stStatus );
}

$obBtnOk = new Ok(true);

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

$obBtnVoltar = new Button;
$obBtnVoltar->setName ( "btnVoltar" );
$obBtnVoltar->setValue(  $stAcao == "consultar" ? "Voltar" : "Cancelar" );
$obBtnVoltar->obEvento->setOnClick( "Cancelar('".$stLocation."','telaPrincipal');" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName ( "btnOk" );
$obBtnLimpar->setValue(  "Limpar" );
$obBtnLimpar->obEvento->setOnClick( "Limpar();" );

$obSpnItens = new Span;
$obSpnItens->setId( "spnItens" );

$obSpnAtributos = new Span;
$obSpnAtributos->setId( "spnAtributos" );

$obFormulario = new Formulario;
$obFormulario->addTitulo     ( 'Dados da Requisição' );
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ("UC-03.03.10");
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addComponente ( $obLblExercicio );
if (( $stAcao == "alterar" ) || ( $stAcao == "anular" ) || ( $stAcao == "consultar" ) || ( $stAcao == "homologar" ) || ($stAcao == "anular_homolog") ) {
    $obFormulario->addHidden    ( $obHdnExercicio        );
    $obFormulario->addHidden    ( $obHdnCodigoRequisicao );
    $obFormulario->addHidden    ( $obHdnAlmoxarifado     );
    $obFormulario->addComponente( $obLblCodigoRequisicao );
    $obFormulario->addComponente( $obLblAlmoxarifado     );
    $obFormulario->addComponente( $obLblRequisicao        );
} else {
    $obFormulario->addComponente ( $obSelectAlmoxarifado );
}
if (( $stAcao == "anular" ) || ( $stAcao == "consultar" ) || ( $stAcao == "homologar" ) || ($stAcao == "anular_homolog") ) {
    $obFormulario->addComponente( $obLblObservacao );
} else {
    $obFormulario->addComponente ( $obTxtObservacao );
}
$obFormulario->addComponente ( $obLblRequisitante );

if (( $stAcao == "incluir" ) || ( $stAcao == "alterar" )) {
    $obFormulario->addComponente ( $obBscCGMSolicitante );

} else {
    $obFormulario->addComponente ( $obLblSolicitante );
}
if (( $stAcao == "consultar" )) {
    $obFormulario->addComponente ( $obLblStatus );
}
if (( $stAcao == "incluir" ) || ( $stAcao == "alterar" )) {
    $obFormulario->addTitulo     ( 'Dados do Item' );
    $obBscItem->geraFormulario($obFormulario);
} elseif ($stAcao == "anular") {
    $obFormulario->addComponente( $obTxtMotivo );
}
if (( $stAcao == "incluir" ) || ( $stAcao == "alterar" )) {
    $obFormulario->Incluir('Item', array ( $obBscItem->obIMontaItemUnidade->obIPopUpCatalogoItem, $obBscItem->obCmbMarca, $obBscItem->obCmbCentroCusto, $obBscItem->obTxtQuantidade ),false,false,'', false );
}
$obFormulario->addSpan       ( $obSpnItens );

if ($stAcao == "homologar") {
    $obFormulario->agrupaComponentes(array($obHomologarSim, $obHomologarNao));
}

if ($stAcao == "anular_homolog") {
    $obFormulario->agrupaComponentes(array($obAnularHomologacaoSim, $obAnularHomologacaoNao));
}

if ($stAcao == "consultar") {
    $obFormulario->defineBarra( array( $obBtnVoltar ), "left", "");
} elseif (( $stAcao == "anular") || ( $stAcao == "alterar" ) || ( $stAcao == "homologar" ) || ($stAcao == "anular_homolog") ) {
    $obFormulario->defineBarra( array( $obBtnOk, $obBtnVoltar), "left", "" );
} else {
    $obFormulario->defineBarra( array( $obBtnOk, $obBtnLimpar), "left", "" );
}

$obFormulario->Show();

//$jsOnLoad ="document.getElementById('imgBuscar').style.display = 'none';";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
