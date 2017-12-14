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
 * Página de Formulario de Manter Autorização
 * Data de Criação: 23/10/2006

 * @author Analista: Gelson
 * @author Desenvolvedor: Bruce Cruz de Sena

 * @ignore

 * Casos de uso: uc-03.05.21

 $Id: FMManterAutorizacao.php 65614 2016-06-02 13:10:59Z franver $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_COMPONENTES.'IMontaAssinaturas.class.php';
include_once CAM_GA_ADM_COMPONENTES.'ITextBoxSelectDocumento.class.php';
include_once CAM_GP_LIC_COMPONENTES.'IMontaNumeroLicitacao.class.php';
include_once CAM_GP_LIC_MAPEAMENTO.'TLicitacaoLicitacao.class.php';
include_once CAM_GP_LIC_MAPEAMENTO.'TLicitacaoHomologacao.class.php';
include_once CAM_GF_EMP_NEGOCIO.'REmpenhoAutorizacaoEmpenho.class.php';
include_once CAM_GP_COM_COMPONENTES.'ILabelEditObjeto.class.php';
require_once TCOM.'TComprasMapaItem.class.php';
require_once TCOM.'TComprasMapa.class.php';

function recuperaUltimaDataContabil()
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
    $obTEmpenhoEmpenho = new TEmpenhoEmpenho;

    $stFiltro = "      AND  empenho.cod_entidade = ".$_REQUEST['inCodEntidade']." \n";
    $stFiltro.= "      AND  empenho.exercicio = '".Sessao::getExercicio()."'      \n";
    $stOrdem  = " ORDER BY  empenho.dt_empenho DESC LIMIT 1                       \n";

    $obTEmpenhoEmpenho->recuperaUltimaDataEmpenho( $rsRecordSet,$stFiltro,$stOrdem );

    if ($dataUltimoEmpenho != "") {
        $dataUltimoEmpenho = SistemaLegado::dataToBr($rsRecordSet->getCampo('dt_empenho'));
    }

    /*
        Rotina que serve para preencher a data da compra direta com
        a última data do lançamento contábil.
    */
    $obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;

    $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
    $obErro = $obREmpenhoAutorizacaoEmpenho->listarMaiorData( $rsMaiorData );

    if (($rsMaiorData->getCampo( "data_autorizacao" ) !="") ) {
        $stDtAutorizacao = $rsMaiorData->getCampo( "data_autorizacao" );
    } elseif ( ( $dataUltimoEmpenho !="") ) {
        $stDtAutorizacao = $dataUltimoEmpenho;
    } else {
        $stDtAutorizacao = "01/01/".Sessao::getExercicio();
    }

    return $stDtAutorizacao;
}

Sessao::write('arAutorizacao', array());
Sessao::write('assinaturas', array());

$stPrograma = "ManterAutorizacao";
$pgFilt		= "FL".$stPrograma.".php";
$pgList		= "LS".$stPrograma.".php";
$pgForm		= "FM".$stPrograma.".php";
$pgProc		= "PR".$stPrograma.".php";
$pgOcul		= "OC".$stPrograma.".php";
$pgJs		= "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$stCtrl = $request->get('stCtrl');

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obTLicitacaoHomolgacao = new TLicitacaoHomologacao;

$stFiltro = "where homologacao.homologado                                           \n".
            " and homologacao.cod_cotacao       = ".$request->get('inCodCotacao')." \n".
            " and homologacao.exercicio_cotacao = '".Sessao::getExercicio()."'      \n";
$obTLicitacaoHomolgacao->recuperaItensAutorizacaoParcial ( $rsItens, $stFiltro );

$boParcial = false;
while ( !$rsItens->eof() ) {
    if($rsItens->getCampo('quantidade_empenho') > 0)
        $boParcial = true;

    $rsItens->proximo();
}

if($boParcial){
    $stMsg  = "Licitação ".$request->get('inCodLicitacao')."/".Sessao::getExercicio()." possui autorização de empenho parcial.";
    $stMsg .= " Para autorizar está licitação, utilize a ação: Gestão Patrimonial :: Licitação :: Autorização de Empenho :: Emitir Autorização de Empenho Parcial";
    SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId(), $stMsg , "unica", "erro", Sessao::getId(), "../");
}

$stUltimaDataContabil = recuperaUltimaDataContabil();

$obTLicitacaoLicitacao = new TLicitacaoLicitacao;
$obTLicitacaoLicitacao->setDado( 'cod_licitacao' , $request->get('inCodLicitacao')  );
$obTLicitacaoLicitacao->setDado( 'exercicio'     , Sessao::getExercicio()           );
$obTLicitacaoLicitacao->setDado( 'cod_entidade'  , $request->get('inCodEntidade')   );
$obTLicitacaoLicitacao->setDado( 'cod_modalidade', $request->get('inCodModalidade') );
$obTLicitacaoLicitacao->recuperaLicitacaoCompleta( $rsLicitacao );

list($inCodProcesso, $stExercicioProcesso) = explode('/',$rsLicitacao->getCampo('processo'));
list($inCodMapa, $stExercicioMapa) = explode('/', $rsLicitacao->getCampo('mapa_compra'));

$obTComprasMapa = new TComprasMapa;
$obTComprasMapa->setDado( 'cod_mapa'     , $inCodMapa        );
$obTComprasMapa->setDado( 'exercicio'    , $stExercicioMapa  );
$obTComprasMapa->recuperaTipoMapa($rsMapa);

$boRegistroPreco = ($rsMapa->getCampo('registro_precos') == 't') ? TRUE : FALSE;

$obHdnNumeroProcesso = new Hidden;
$obHdnNumeroProcesso->setName  ( "stNumeroProcesso" );
$obHdnNumeroProcesso->setValue ( (int) $inCodProcesso  );

$obHdnExercicioProcesso = new Hidden;
$obHdnExercicioProcesso->setName  ( "stExercicioProcesso" );
$obHdnExercicioProcesso->setValue ( $stExercicioProcesso  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnLicitacao = new Hidden;
$obHdnLicitacao->setName  ( 'inCodLicitacao'                );
$obHdnLicitacao->setValue ( $request->get('inCodLicitacao') );

$obHdnEntidade = new Hidden;
$obHdnEntidade->setId    ( 'inCodEntidade' );
$obHdnEntidade->setName  ( 'inCodEntidade' );
$obHdnEntidade->setValue ( $request->get('inCodEntidade') );

$obHdnStUltimaDataContabil = new Hidden;
$obHdnStUltimaDataContabil->setName ( 'stUltimaDataContabil'    );
$obHdnStUltimaDataContabil->setValue( $stUltimaDataContabil     );

$obHdnModalidade = new Hidden;
$obHdnModalidade->setName   ( 'inCodModalidade' );
$obHdnModalidade->setValue  ( $request->get('inCodModalidade') );

$obHdnDtLicitacao = new Hidden;
$obHdnDtLicitacao->setName  ( 'inDataLicitacao' );
$obHdnDtLicitacao->setValue ( $rsLicitacao->getCampo('dt_licitacao') );

$obLblExercicio = new Label;
$obLblExercicio->setValue  ( Sessao::getExercicio() );
$obLblExercicio->setRotulo ( 'Exercício'            );

$obLblEntidade = new Label;
$obLblEntidade->setValue  ( $rsLicitacao->getCampo('entidade') );
$obLblEntidade->setRotulo ( 'Entidade' );

$obLblLicitacao = new Label;
$obLblLicitacao->setRotulo  ( 'Código Licitação' );
$obLblLicitacao->setValue   ( $rsLicitacao->getCampo('cod_licitacao')."/".$rsLicitacao->getCampo('exercicio') );

$obLblDtLicitacao = new Label;
$obLblDtLicitacao->setRotulo( 'Data Licitação' );
$obLblDtLicitacao->setValue ( $rsLicitacao->getCampo('dt_licitacao') );

$obLblTipoObjetoLicitacao = new Label;
$obLblTipoObjetoLicitacao->setRotulo( 'Tipo Objeto' );
$obLblTipoObjetoLicitacao->setValue ( $rsLicitacao->getCampo('cod_tipo_objeto').' - '.SistemaLegado::pegaDado('descricao','compras.tipo_objeto','where cod_tipo_objeto ='.$rsLicitacao->getCampo('cod_tipo_objeto')) );

$obLblObjetoLicitacao = new Label;
$obLblObjetoLicitacao->setRotulo( 'Objeto' );
$obLblObjetoLicitacao->setValue ( $rsLicitacao->getCampo('cod_objeto').' - '.SistemaLegado::pegaDado('descricao','compras.objeto','where cod_objeto ='.$rsLicitacao->getCampo('cod_objeto')) );

$obLblDtEntregaLicitacao = new Label;
$obLblDtEntregaLicitacao->setRotulo ( 'Data Entrega Proposta' );
$obLblDtEntregaLicitacao->setValue  ( $rsLicitacao->getCampo('dt_entrega_proposta') );

$obLblValidadeLicitacao = new Label;
$obLblValidadeLicitacao->setRotulo  ( 'Data Validade Proposta' );
$obLblValidadeLicitacao->setValue   ( $rsLicitacao->getCampo('dt_validade_proposta') );

$obLblCondicoesPagamentoLicitacao = new Label;
$obLblCondicoesPagamentoLicitacao->setRotulo( 'Condições de Pagamento' );
$obLblCondicoesPagamentoLicitacao->setValue ( $rsLicitacao->getCampo('condicoes_pagamento') );

$obDtAprovacaoJuridicoLicitacao = new Data;
$obDtAprovacaoJuridicoLicitacao->setName   ( "stDtAutorizacao" );
$obDtAprovacaoJuridicoLicitacao->setValue  ( $stUltimaDataContabil );
$obDtAprovacaoJuridicoLicitacao->setRotulo ( "Data Autorização");
$obDtAprovacaoJuridicoLicitacao->setTitle  ( 'Informe a data da autorização.' );
$obDtAprovacaoJuridicoLicitacao->setNull   ( false );

$oblblModalidade = new Label;
$oblblModalidade->setValue  ( $rsLicitacao->getCampo( 'modalidade' ) );
$oblblModalidade->setRotulo ( 'Modalidade' );

$obLblCotacao = new Label;
$obLblCotacao->setValue  ( $request->get('inCodCotacao')."/".Sessao::getExercicio() );
$obLblCotacao->setRotulo ( 'Cotação' );

$obLblMapaLicitacao = new Label;
$obLblMapaLicitacao->setValue  ( $rsLicitacao->getCampo('mapa_compra'));
$obLblMapaLicitacao->setRotulo ( 'Mapa' );

if($boRegistroPreco){
    $obLblRegistroPreco = new Label;
    $obLblRegistroPreco->setValue   ( 'Sim' );
    $obLblRegistroPreco->setRotulo  ( 'Registro de Preço' );
}

$obSpnAutorizacoes = new Span;
$obSpnAutorizacoes->setId   ( 'spnAutorizacoes' );

$obSpnSpace = new Span;
$obSpnSpace->setId          ( 'spnLabels'       );

$obSpnItens = new Span;
$obSpnItens->setId          ( 'spnItens'        );

$obLblTotalMapa = new Label();
$obLblTotalMapa->setRotulo  ( 'Total do Mapa'   );
$obLblTotalMapa->setId      ( 'stTotalMapa'     );
$obLblTotalMapa->setName    ( 'stTotalMapa'     );

# Componente que monta as Assinaturas.
$obMontaAssinaturas = new IMontaAssinaturas(null, 'autorizacao_empenho');
$obMontaAssinaturas->definePapeisDisponiveis('autorizacao_empenho');
$obMontaAssinaturas->setOpcaoAssinaturas( false );

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden ( $obHdnLicitacao );
$obFormulario->addHidden ( $obHdnEntidade );
$obFormulario->addHidden ( $obHdnModalidade );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnDtLicitacao );
$obFormulario->addHidden ( $obHdnStUltimaDataContabil );
$obFormulario->addHidden ( $obHdnNumeroProcesso );
$obFormulario->addHidden ( $obHdnExercicioProcesso );

if($boRegistroPreco)
    $obFormulario->addComponente( $obLblRegistroPreco );
    
$obFormulario->addComponente    ( $obLblExercicio );
$obFormulario->addComponente    ( $obLblEntidade );
$obFormulario->addComponente    ( $obLblLicitacao );

$obFormulario->addComponente    ( $obLblDtLicitacao );
$obFormulario->addComponente    ( $obLblTipoObjetoLicitacao );
$obFormulario->addComponente    ( $obLblObjetoLicitacao );
$obFormulario->addComponente    ( $obLblDtEntregaLicitacao );
$obFormulario->addComponente    ( $obLblValidadeLicitacao );
$obFormulario->addComponente    ( $obLblCondicoesPagamentoLicitacao );

$obFormulario->addComponente    ( $oblblModalidade );
$obFormulario->addComponente    ( $obLblCotacao );

$obFormulario->addComponente    ( $obLblMapaLicitacao );
$obFormulario->addComponente    ( $obLblTotalMapa );

# Monta o componente de Assinaturas.
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->addSpan          ( $obSpnSpace );
$obFormulario->addTitulo        ( $rsLicitacao->getCampo('entidade') );
$obFormulario->addComponente    ( $obDtAprovacaoJuridicoLicitacao  );
$obFormulario->addSpan          ( $obSpnItens );

$obOk  = new Ok(true);
if($boRegistroPreco)
    $obOk->setDisabled(true);

$obCancelar  = new Cancelar();
$obCancelar->obEvento->setOnClick("Cancelar('".$pgList."','telaPrincipal');");

$obFormulario->defineBarra( array( $obOk, $obCancelar ) );

$obFormulario->Show();

if ($obMontaAssinaturas->getOpcaoAssinaturas()) {
    echo $obMontaAssinaturas->disparaLista();
}

# Parâmetros necessários para requisitar as informações da Licitação.
$stParams .= "&inCodCotacao=".$request->get('inCodCotacao');
$stParams .= "&inCodLicitacao=".$rsLicitacao->getCampo('cod_licitacao');
$stParams .= "&inCodEntidade=".$rsLicitacao->getCampo('cod_entidade');
$stParams .= "&inCodModalidade=".$rsLicitacao->getCampo('cod_modalidade');
$stParams .= "&inCodMapa=".$rsLicitacao->getCampo('mapa_compra');
$stParams .= "&stExercicioMapa=".$rsLicitacao->getCampo('exercicio');

# Carrega as informações básicas da Licitação.
$stJs .= "<script type='text/javascript'> \n";
$stJs .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId().$stParams."','buscaInfoLicitacao'); \n";
$stJs .= "</script>	\n";

echo $stJs;

if($boRegistroPreco)
    SistemaLegado::exibeAviso("Autorizações de Empenho de Registros de Preços devem ser feitas na ação: Gestão Patrimonial :: Licitação :: Autorização de Empenho :: Emitir Autorização de Empenho Parcial.","aviso","aviso");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
