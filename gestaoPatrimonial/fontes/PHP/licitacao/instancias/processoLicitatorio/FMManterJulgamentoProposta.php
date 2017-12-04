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
    * Página de Formulário para incluir processo licitatório
    * Data de Criação   : 04/10/2006

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    * $Id: FMManterJulgamentoProposta.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-03.05.26
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_COM_COMPONENTES. "IPopUpMapaCompras.class.php"     );
include_once ( CAM_GP_LIC_COMPONENTES.'IClusterLabelsMapa.class.php');

//Definições padrões do framework
$stPrograma = "ManterJulgamentoProposta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::write('arItens', array());

include_once ( $pgJs   );

$stAcao = $request->get('stAcao');
$stCtrl = $request->get('stCtrl');

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden();
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnMapa = new Hidden();
$obHdnMapa->setName( "stMapaCompras" );
$obHdnMapa->setId( "stMapaCompras" );
$obHdnMapa->setValue( $request->get('inCodMapa').'/'.$request->get('stExercicio') );

$obHdnInCodEntidade = new Hidden;
$obHdnInCodEntidade->setName    ( "inCodEntidade" );
$obHdnInCodEntidade->setId      ( "inCodEntidade" );
$obHdnInCodEntidade->setValue   ( $request->get('inCodEntidade') );

$obHdnInCodModalidade = new Hidden;
$obHdnInCodModalidade->setName    ( "inCodModalidade"              );
$obHdnInCodModalidade->setId      ( "inCodModalidade"              );
$obHdnInCodModalidade->setValue   ( $request->get('inCodModalidade')   );

$obHdnInCodTipoModalidade = new Hidden;
$obHdnInCodTipoModalidade->setName    ( "inCodTipoObjeto"              );
$obHdnInCodTipoModalidade->setId      ( "inCodTipoObjeto"              );
$obHdnInCodTipoModalidade->setValue   ( $request->get('inCodTipoObjeto')   );

$obHdnInCodCompraDireta = new Hidden;
$obHdnInCodCompraDireta->setName  ( "inCodCompraDireta"            );
$obHdnInCodCompraDireta->setId    ( "inCodCompraDireta" );
$obHdnInCodCompraDireta->setValue ( $request->get('inCodCompraDireta') );

# Guarda o cod_licitacao / exercicio_licitacao
$obHdnInCodLicitacao = new Hidden;
$obHdnInCodLicitacao->setName  ('inCodLicitacao');
$obHdnInCodLicitacao->setId    ('inCodLicitacao');
$obHdnInCodLicitacao->setValue ($request->get('inCodLicitacao'));

$obHdnStEntidade = new Hidden;
$obHdnStEntidade->setName      ( "stEntidade"                   );
$obHdnStEntidade->setId        ( "stEntidade"                   );
$obHdnStEntidade->setValue     ( $request->get('inCodEntidade')     );

$obHdnCodCotacao = new Hidden;
$obHdnCodCotacao->setName  ( "inCodCotacao" );
$obHdnCodCotacao->setValue ( $request->get('inCodCotacao')  );

$obHdnExercicioCotacao = new Hidden;
$obHdnExercicioCotacao->setName("inExercicioCotacao");
$obHdnExercicioCotacao->setValue($request->get('inExercicioCotacao'));

include_once ( CAM_GP_COM_MAPEAMENTO. 'TComprasJulgamento.class.php' );
$obTComprasJulgamento = new TComprasJulgamento();
$obTComprasJulgamento->setDado('cod_cotacao', $request->get('inCodCotacao'));
$obTComprasJulgamento->setDado('exercicio', $request->get('inExercicioCotacao'));
$obTComprasJulgamento->recuperaPorCotacao( $rsComprasJulgamento );

$stDtEmissao = SistemaLegado::dataToBr(substr($rsComprasJulgamento->getCampo('timestamp'), 0, 10));
$stHrEmissao = substr($rsComprasJulgamento->getCampo('timestamp'), 11, 5);

if ($rsComprasJulgamento->getNumLinhas() < 0) {
    // Quando for compra direta. monta um textbox sugerindo a data da Compra Direta, podendo ser
    // alterada entre a data da compra direta e a data atual.
    if ($stAcao == 'dispensaLicitacao') {
        $stTimestampManutencao = '';
        $stTimestampManutencao = SistemaLegado::pegaDado("timestamp", "compras.cotacao", "WHERE cod_cotacao = ".$request->get('inCodCotacao')." AND exercicio = ".$request->get('inExercicioCotacao')."::VARCHAR");
        $stDataManutencao      = SistemaLegado::dataToBr($stTimestampManutencao);
        $stHoraManutencao      = substr($stTimestampManutencao, 11, 5);

    } else {
        $stTimestampManutencao = '';
        $stTimestampManutencao = SistemaLegado::pegaDado("timestamp", "compras.cotacao", "WHERE cod_cotacao = ".$request->get('inCodCotacao')." AND exercicio = ".$request->get('inExercicioCotacao')."::VARCHAR");
        $stDataManutencao      = SistemaLegado::dataToBr($stTimestampManutencao);
        $stHoraManutencao      = substr($stTimestampManutencao, 11, 5);
    }
}

$obTxtDataEmissao = new Data();
$obTxtDataEmissao->setRotulo( "Data do Julgamento" );
$obTxtDataEmissao->setTitle( "Informe a Data de Emissão do Julgamento da Propostas." );
$obTxtDataEmissao->setId  ( "stDataEmissao" );
$obTxtDataEmissao->setName( "stDataEmissao" );
$obTxtDataEmissao->setNull( false         );
$obTxtDataEmissao->setValue( $stDataManutencao );
$obTxtDataEmissao->obEvento->setOnChange( "montaParametrosGET( 'validaDataJulgamento', 'stDataEmissao' ); " );

$obTxtHoraEmissao = new Hora();
$obTxtHoraEmissao->setRotulo( "Hora do Julgamento" );
$obTxtHoraEmissao->setTitle( "Informe a Hora da Emissão do Julgamento da Propostas." );
$obTxtHoraEmissao->setId  ( "stHoraEmissao" );
$obTxtHoraEmissao->setName( "stHoraEmissao" );
$obTxtHoraEmissao->setNull ( false                        );
$obTxtHoraEmissao->setValue( $stHoraManutencao );

Sessao::write('stDtEmissao', $stDataManutencao);
Sessao::write('stHrEmissao', $stHoraManutencao);

$obSpanLabels = new Span;
$obSpanLabels->setId( 'spnLabels' );

$obSpanLotes = new Span;
$obSpanLotes->setId ( 'spnLotes' );

$obSpanItens = New Span;
$obSpanItens->setId ( 'spnItens' );

$obSpanFornecedores = New Span;
$obSpanFornecedores->setId ( 'spnFornecedores' );

$obFormulario = new Formulario();
$obFormulario->addForm  ( $obForm    );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addTitulo( 'Julgamento das Propostas dos Participantes' );
$obFormulario->addHidden( $obHdnMapa                );
$obFormulario->addHidden( $obHdnCodCotacao          );
$obFormulario->addHidden( $obHdnExercicioCotacao    );
$obFormulario->addHidden( $obHdnInCodModalidade     );
$obFormulario->addHidden( $obHdnInCodTipoModalidade );
$obFormulario->addHidden( $obHdnInCodCompraDireta   );
$obFormulario->addHidden( $obHdnInCodEntidade       );
$obFormulario->addHidden( $obHdnInCodLicitacao      );
$obFormulario->addHidden( $obHdnStEntidade          );
$obFormulario->addSpan       ( $obSpanLabels        );
//if ($stAcao == 'dispensaLicitacao') {
    $obFormulario->addComponente       ( $obTxtDataEmissao      );
    $obFormulario->addComponente       ( $obTxtHoraEmissao      );
//}
$obFormulario->addSpan       ( $obSpanItens        );
$obFormulario->addSpan       ( $obSpanFornecedores );

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obBtnOk = new Ok;

$stCaminho = ( strstr($stAcao, 'dispensaLicitacao') ) ? CAM_GP_COM_INSTANCIAS.'compraDireta/LSManterJulgamento.php' : $pgList ;

$obBtnCancelar = new Cancelar();
$obBtnCancelar->obEvento->setOnClick( "Cancelar('".$stCaminho."?".Sessao::getId()."&stAcao=".$stAcao."');" );
$obFormulario->defineBarra ( array($obBtnOk,$obBtnCancelar) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
echo "<script type='text/javascript'>\n";

$stUrl = $pgOcul."?".Sessao::getId()."&stMapaCompras=".$request->get('inCodMapa')."/".$request->get('stExercicio')."&boAlteraAnula=true&inCodLicitacao=".$request->get('inCodLicitacao')."&inCodModalidade=".$request->get('inCodModalidade');

if ($stAcao == 'manter' || $stAcao == 'excluir') {
    $stUrl .= "&inExercicioCotacao=".$request->get('inExercicioCotacao');
    $stUrl .= "&inCodCotacao=".$request->get('inCodCotacao');
    $stUrl .= "&inCodTipoObjeto=".$request->get('inCodTipoObjeto');
    echo "ajaxJavaScript('$stUrl','montaClusterLabels');";
} else {
    echo "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stMapaCompras=".$request->get('inCodMapa')."/".$request->get('stExercicio')."&boAlteraAnula=true','montaClusterLabelsDispensa');";
}

// Desabilita o botão OK até que o usuário clique em algum objeto radio do julgamento.
echo "jQuery('#Ok').attr('disabled', true);";
echo "</script>\n";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
