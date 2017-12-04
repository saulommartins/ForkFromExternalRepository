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
    * @author Desenvolvedor: Lucas  Teixeira Stephanou

    * @ignore

    * $Id: FMManterManutencaoProposta.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso : uc-03.05.25
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TCOM."TComprasMapaCotacao.class.php";

require_once CAM_GP_COM_COMPONENTES . "IPopUpMapaCompras.class.php";

$stPrograma = "ManterManutencaoProposta";
$pgFilt = "FL" . $stPrograma . ".php";
$pgList = "LS" . $stPrograma . ".php";
$pgForm = "FM" . $stPrograma . ".php";
$pgProc = "PR" . $stPrograma . ".php";
$pgOcul = "OC" . $stPrograma . ".php";
$pgJs   = "JS" . $stPrograma . ".js";

include ( $pgJs );

//Importante para ler informações da compra direta após recusar salvar os dados como marcas novas
if (count(Sessao::read('REQUEST')) > 0 ) {
    $_REQUEST = Sessao::read('REQUEST');
    $arMapaExercicio = explode('/',$request->get('stMapaCompras'));
    $_REQUEST['inCodMapa'] = $arMapaExercicio[0];
    $_REQUEST['stExercicio'] = $arMapaExercicio[1];
}

$rsParticipantes = new Recordset();
$rsItens = new Recordset();

Sessao::remove('arManterPropostas');
Sessao::remove("codCotacao");
Sessao::remove("REQUEST");
$arManterPropostas = array();
$arManterPropostas['rsParticipantes'] = $rsParticipantes;
$arManterPropostas['rsItens'] = $rsItens;

Sessao::write('arManterPropostas', $arManterPropostas);
Sessao::write('boProximoItem', false);

//Definição dos componentes(objetos) que irão ser adicionados no formulário
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setEncType                 ( "multipart/form-data" );

$stAcao =  $request->get('stAcao')  ;

//Define o Hidden de ação (padrão no framework)
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $request->get('stAcao') );

//Define o Hidde de controle (padrão no framework)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName( "inCodEntidade" );
$obHdnCodEntidade->setValue( $request->get('inCodEntidade') );

$obHdnCodModalidade = new Hidden;
$obHdnCodModalidade->setName( "inCodModalidade" );
$obHdnCodModalidade->setValue( $request->get('inCodModalidade') );

$obHdnExercicioEntidade = new Hidden;
$obHdnExercicioEntidade->setName( "stExercicioEntidade" );
$obHdnExercicioEntidade->setValue( $request->get('stExercicioEntidade') );

$obHdnCodCompraDireta = new Hidden;
$obHdnCodCompraDireta->setName( "inCompraDireta" );
$obHdnCodCompraDireta->setValue( $request->get('inCompraDireta') );

$obHdnCodLicitacao = new Hidden;
$obHdnCodLicitacao->setName( "inCodLicitacao" );
$obHdnCodLicitacao->setValue( $request->get('inCodLicitacao') );

$obHdnMapaCompra = new Hidden();
$obHdnMapaCompra->setName( 'stMapaCompras' );
$obHdnMapaCompra->setValue( $request->get('inCodMapa')."/".$request->get('stExercicio') );

$obHdnIncluiMarca = new Hidden;
$obHdnIncluiMarca->setName( "boIncluiMarca" );
$obHdnIncluiMarca->setId( "boIncluiMarca" );
$obHdnIncluiMarca->setValue( 'false' );

$obTComprasMapaCotacao = new TComprasMapaCotacao;

$stFiltroMapaCotacao = " WHERE mapa_cotacao.cod_mapa = ".$request->get('inCodMapa')."
                           AND mapa_cotacao.exercicio_mapa = ".$request->get('stExercicio')."::VARCHAR
                           AND NOT EXISTS
                           (
                            SELECT  1
                              FROM  compras.cotacao_anulada
                             WHERE  mapa_cotacao.cod_cotacao       = cotacao_anulada.cod_cotacao
                               AND  mapa_cotacao.exercicio_cotacao = cotacao_anulada.exercicio
                           )";

$obTComprasMapaCotacao->recuperaTodos($rsMapaCotacao,$stFiltroMapaCotacao);

$nuCodCotacao = $rsMapaCotacao->getCampo ( 'cod_cotacao' ) ?  $rsMapaCotacao->getCampo ( 'cod_cotacao' ) : 0 ;

$obHdnCodCotacao = new Hidden();
$obHdnCodCotacao->setName( 'nuCodCotacao' );
$obHdnCodCotacao->setValue( $nuCodCotacao );

# Funcionalidade para imprimir um Mapa Comparativo das melhores propostas.
$boImprimirComparativo = new CheckBox;
$boImprimirComparativo->setName    ("boImprimirComparativo");
$boImprimirComparativo->setChecked (true);
$boImprimirComparativo->setRotulo  ("Imprimir Mapa Comparativo");
$boImprimirComparativo->setValue   ("sim");

$obSpnLabels = new Span;
$obSpnLabels->setId ( 'spnLabels' );

$obSpnParticipantes = new Span;
$obSpnParticipantes->setId ( 'spnParticipantes' );

$obSpnItens = new Span;
$obSpnItens->setId ( 'spnItens' );

$obSpnDados = new Span;
$obSpnDados->setId ( 'spnDadosItem' );

$obBtnOk = new Ok;
$obBtnOk->obEvento->setOnClick("Salvar();");

$obHdnUltCodCotacaoAnulada = new Hidden();
$obHdnUltCodCotacaoAnulada->setId( 'nuUltCodCotacaoAnulada' );
$obHdnUltCodCotacaoAnulada->setName( 'nuUltCodCotacaoAnulada' );
$obHdnUltCodCotacaoAnulada->setValue( '' );

$obHdnCodMapa = new Hidden();
$obHdnCodMapa->setId( 'nuCodMapa' );
$obHdnCodMapa->setName( 'nuCodMapa' );
$obHdnCodMapa->setValue( '' );

$obHdnExercicioMapa = new Hidden();
$obHdnExercicioMapa->setId( 'nuExercicioMapa' );
$obHdnExercicioMapa->setName( 'nuExercicioMapa' );
$obHdnExercicioMapa->setValue( '' );

$obHdnCgmParticipante = new Hidden();
$obHdnCgmParticipante->setId( 'nuCgmParticipante' );
$obHdnCgmParticipante->setName( 'nuCgmParticipante' );
$obHdnCgmParticipante->setValue( '' );

$obArquivoXmlCotacao = new FileBox;
$obArquivoXmlCotacao->setRotulo('Arquivo Cotação');
$obArquivoXmlCotacao->setName('arquivoCotacao');
$obArquivoXmlCotacao->setId('arquivoCotacao');
$obArquivoXmlCotacao->setSize(70);

$obBotaoImportar = new Button;
$obBotaoImportar->setRotulo('importar');
$obBotaoImportar->setValue('Importar Dados');
$obBotaoImportar->obEvento->setOnClick("montaParametrosGET('importarArquivoFornecedor','arquivoCotacao');");

$arComponentes[0] = $obArquivoXmlCotacao;
$arComponentes[1] = $obBotaoImportar;

$link = "";
$arLink = Sessao::read('link');
if (is_array($arLink)) {
    foreach ($arLink as $campo =>$valor) {
        $link.="&".$campo."=".$valor;
    }
}
$stCaminho = ( $stAcao == 'dispensaLicitacao' ) ? CAM_GP_COM_INSTANCIAS.'compraDireta/LSManterProposta.php' : $pgList ;

$obBtnCancelar = new Cancelar();
$obBtnCancelar->obEvento->setOnClick( "Cancelar('".$stCaminho."?".Sessao::getId()."&stAcao=".$stAcao.$link."');" );

$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );

$obFormulario->addHidden ( $obHdnCtrl       );
$obFormulario->addHidden ( $obHdnAcao       );
$obFormulario->addHidden ( $obHdnMapaCompra );
$obFormulario->addHidden ( $obHdnCodCotacao );
$obFormulario->addHidden ( $obHdnIncluiMarca );
$obFormulario->addHidden ( $obHdnCodEntidade );
$obFormulario->addHidden ( $obHdnCodModalidade );
$obFormulario->addHidden ( $obHdnExercicioEntidade );
$obFormulario->addHidden ( $obHdnCodCompraDireta );
$obFormulario->addHidden ( $obHdnCodLicitacao );
$obFormulario->addHidden ( $obHdnUltCodCotacaoAnulada );
$obFormulario->addHidden ( $obHdnCodMapa );
$obFormulario->addHidden ( $obHdnExercicioMapa );
$obFormulario->addHidden ( $obHdnCgmParticipante );
$obFormulario->addTitulo ( "Manutenção de Propostas" );
$obFormulario->addSpan ( $obSpnLabels );

$obFormulario->addTitulo ( "Impressão" );
$obFormulario->addComponente( $boImprimirComparativo );

// Ticket #10715
//if ($_REQUEST['stAcao'] == 'dispensaLicitacao') {
//    // Comentado porque ainda faltam alguns detalhes na criação e importação das propostas!! Será descomentado para proxima versão GP
//    $obFormulario->addTitulo ( "Importar arquivo de Cotação" );
//    $obFormulario->agrupaComponentes ($arComponentes);
//}

$obFormulario->addSpan ( $obSpnParticipantes );
$obFormulario->addSpan ( $obSpnItens );
$obFormulario->addSpan ( $obSpnDados );

$obFormulario->defineBarra ( array($obBtnOk,$obBtnCancelar) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

echo "<script type='text/javascript'>\n";

if ($stAcao == 'manter') {
    echo "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stMapaCompras=".$request->get('inCodMapa')."/".$request->get('stExercicio')."&inCodLicitacao=".$request->get('inCodLicitacao')."&stExercicio=".$request->get('stExercicio')."&inCodModalidade=".$request->get('inCodModalidade')."&stAcao=".$request->get('stAcao')."&boAlteraAnula=true','montaClusterLabels');";
} else {
    echo "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stMapaCompras=".$request->get('inCodMapa')."/".$request->get('stExercicio')."&numMapa=".$request->get('inCodMapa')."&inCodLicitacao=".$request->get('inCodLicitacao')."&stExercicio=".$request->get('stExercicio')."&inCodModalidade=".$request->get('inCodModalidade')."&stAcao=".$request->get('stAcao')."&boAlteraAnula=true','montaLabelsDispensaLicitacao');";
}
echo "</script>\n";
