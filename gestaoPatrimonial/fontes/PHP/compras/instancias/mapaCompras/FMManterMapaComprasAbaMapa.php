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
 * Aba dos dados do Mapa de Compras
 * Data de Criação   : 19/09/2006

 * @author Analista: Cleisson Barbosa
 * @author Desenvolvedor: Anderson C. Konze

 * @ignore

 * Casos de uso: uc-03.04.05

 $Id: FMManterMapaComprasAbaMapa.php 63445 2015-08-28 13:44:54Z michel $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapa.class.php';

Sessao::write('itens_excluidos' , array());

$inCodMapa   = $request->get('cod_mapa');
$stExercicio = $request->get('exercicio');

if ($inCodMapa) {
    //está variavél só tera valor se a ação for alteração anulação etc. menos inclusão
    $obTComprasMapa = new TComprasMapa;
    $stFiltro = " where mapa.cod_mapa = ".$inCodMapa." and mapa.exercicio = '".$stExercicio."'";
    $obTComprasMapa->recuperaRelacionamento ( $rsMapa, $stFiltro );

    $obTComprasMapa->setDado( 'cod_mapa', $inCodMapa );
    $obTComprasMapa->consultar();

    $stExercicio        = $rsMapa->getCampo('exercicio'          );
    $inCodObjeto        = $rsMapa->getCampo('cod_objeto'         );
    $inCodTipoLicitacao = $rsMapa->getCampo('cod_tipo_licitacao' );

} else {
    // setando dados para inclusão
    $stExercicio = Sessao::getExercicio();
}

$obLblExercicio = new Label;
$obLblExercicio->setRotulo ('Exercício'   );
$obLblExercicio->setName   ('stExercicio' );
$obLblExercicio->setValue  ( $stExercicio );

$obTxtCodMapa = new Label;
$obTxtCodMapa->setRotulo ( 'Número do Mapa' );
$obTxtCodMapa->setValue  ( $inCodMapa       );

$obHdnCodMapa = new Hidden;
$obHdnCodMapa->setName  ( 'inCodMapa' );
$obHdnCodMapa->setValue ( $inCodMapa  );

$obHdnExercicioMapa = new Hidden;
$obHdnExercicioMapa->setName  ( 'stExercicioMapa' );
$obHdnExercicioMapa->setValue ( $stExercicio  );

if ($stAcao != 'anular') {
    $obObjeto = new IPopUpObjeto($obForm);
    $obObjeto->setNull ( false );
    $obObjeto->setRotulo ("Objeto");
    $obObjeto->obCampoCod->setValue ( $inCodObjeto );

    $obISelectTipoLicitacao = new ISelectTipoLicitacao();
    $obISelectTipoLicitacao->setNull( false );
    $obISelectTipoLicitacao->setRotulo ( 'Tipo Cotação' );
    $obISelectTipoLicitacao->setValue ( $inCodTipoLicitacao );
    $obISelectTipoLicitacao->obEvento->setOnChange ("montaParametrosGET('tipoLicitacao','inCodTipoLicitacao');");
    Sessao::write('inTipoLicitacao' , $inCodTipoLicitacao);
} else {

    $obObjeto = new Label;
    $obObjeto->setRotulo( 'Objeto' );
    $obObjeto->setValue ( "$inCodObjeto - ". $rsMapa->getCampo( 'descricao_objeto' ) );

    // Define Objeto TextArea para observações/justificativas
    $obTxtObs = new TextArea;
    $obTxtObs->setName   ( "stMotivo" );
    $obTxtObs->setId     ( "stMotivo" );
    $obTxtObs->setValue  ( $stObservacao );
    $obTxtObs->setRotulo ( "Motivo da Anulação" );
    $obTxtObs->setTitle  ( "Informe o motivo para a anulação." );
    $obTxtObs->setNull   ( false );
    $obTxtObs->setRows   ( 2 );
    $obTxtObs->setCols   ( 100 );
    $obTxtObs->setMaxCaracteres ( 200 );

    /// buscando o tipo de licitação
    $obISelectTipoLicitacao = new Label;
    $obISelectTipoLicitacao->setRotulo ( 'Tipo Cotação' );
    $obISelectTipoLicitacao->setvalue  ( $rsMapa->getCampo( 'cod_tipo_licitacao') . ' - ' . $rsMapa->getCampo( 'descricao_tipo_licitacao' ));

}

$obSolicitacao = new IMontaSolicitacao( $obForm );
$obSolicitacao->obExercicio->setRotulo                          ( "Exercício da Solicitação");
$obSolicitacao->obExercicio->setReadOnly                        ( true );
$obSolicitacao->obExercicio->setObrigatorioBarra                ( true );
$obSolicitacao->obExercicio->setNull                            ( true );
$obSolicitacao->setTipoBusca                                    ( 'mapa_compras'            );
$obSolicitacao->obITextBoxSelectEntidade->setRotulo             ( "Entidade da Solicitação" );
$obSolicitacao->obITextBoxSelectEntidade->setObrigatorioBarra   ( true );
$obSolicitacao->obITextBoxSelectEntidade->setNull               ( true );
$obSolicitacao->obPopUpSolicitacao->obCampoCod->setRotulo       ( "Número da Solicitacao"   );
$obSolicitacao->obPopUpSolicitacao->setObrigatorioBarra         ( true );
$obSolicitacao->obPopUpSolicitacao->setNull                     ( true );
$obSolicitacao->obRdRegistroPrecoSim->setNull                   ( true );
$obSolicitacao->obRdRegistroPrecoSim->setObrigatorioBarra       ( true );
$obSolicitacao->obRdRegistroPrecoNao->setNull                   ( true );
$obSolicitacao->obRdRegistroPrecoNao->setObrigatorioBarra       ( true );
$obSolicitacao->setObrigatorioBarra                             ( true );
$obSolicitacao->setNull                                         ( true );
$obSolicitacao->setRegistroPreco                                ( true );

$obHdnBoRegistroPreco = new Hidden;
$obHdnBoRegistroPreco->setName  ( 'boRegistroPreco' );
$obHdnBoRegistroPreco->setId    ( 'boRegistroPreco' );
$obHdnBoRegistroPreco->setValue ( 'false'  );

$arIncluirSolicitacao =  array( &$obSolicitacao->obPopUpSolicitacao, &$obSolicitacao->obITextBoxSelectEntidade );

$obFormularioAbaMapa = new Formulario;
$obFormularioAbaMapa->addForm( $obForm );

$obBtnIncluirForm = new Button;
$obBtnIncluirForm->setName               ( "btnIncluirSolicitacao"   );
$obBtnIncluirForm->setValue              ( "Incluir"                 );
$obBtnIncluirForm->setTipo               ( "button"                  );
$obBtnIncluirForm->obEvento->setOnClick  ( " montaParametrosGET('incluirSolicitacao','stExercicioSolicitacao, inCodEntidadeSolicitacao, inCodSolicitacao, inCodTipoLicitacao, boRegistroPreco, stCtrl, stAcao'); limparDadosSolicitacao();" );

$obBtnLimparSolicitacaoForm = new Button;
$obBtnLimparSolicitacaoForm->setName               ( "btnLimparSolicitacao"       );
$obBtnLimparSolicitacaoForm->setValue              ( "Limpar"                     );
$obBtnLimparSolicitacaoForm->setTipo               ( "button"                     );
$obBtnLimparSolicitacaoForm->obEvento->setOnClick  ( " limparDadosSolicitacao();" );

$obFormularioAbaMapa->defineBarra( array( $obBtnIncluirForm, $obBtnLimparSolicitacaoForm ), '', '');

$obSpnSolicitacoes = new Span;
$obSpnSolicitacoes->setId ('spnSolicitacoes');

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
