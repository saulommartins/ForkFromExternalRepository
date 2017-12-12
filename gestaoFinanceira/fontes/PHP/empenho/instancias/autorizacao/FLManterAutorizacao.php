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
    * Página de Filtro de Autorizacao
    * Data de Criação   : 04/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 32090 $
    $Name$
    $Autor:$
    $Date: 2007-05-11 11:02:06 -0300 (Sex, 11 Mai 2007) $

    * Casos de uso: uc-02.03.02
                    uc-02.03.19
                    uc-02.03.20
                    uc-02.01.08
*/

include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php" );
include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php" );
include_once ( CAM_GF_EMP_COMPONENTES."IMontaCompraDiretaLicitacaoEmpenho.class.php" );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($rsUltimoMesEncerrado->getCampo('mes') >= $mesAtual AND $boUtilizarEncerramentoMes == 'true') {
    $obSpan = new Span;
    $obSpan->setValue('<b>Não é possível utilizar esta rotina pois o mês atual está encerrado!</b>');
    $obSpan->setStyle('align: center;');
    $obFormulario = new Formulario;
    $obFormulario->addSpan($obSpan);
    $obFormulario->show();
} else {
    if($stAcao<>"imprimir" and $stAcao<>"imprimirAN")
        include_once( CAM_GF_INCLUDE."validaGF.inc.php");

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );

    //Define o nome dos arquivos PHP
    $stPrograma = "ManterAutorizacao";
    $pgFilt = "FL".$stPrograma.".php";
    $pgList = "LS".$stPrograma.".php";
    $pgForm = "FM".$stPrograma.".php";
    $pgProc = "PR".$stPrograma.".php";
    $pgOcul = "OC".$stPrograma.".php";
    $pgJS   = "JS".$stPrograma.".js";

    include_once($pgJS);

    Sessao::write('filtro', array());
    Sessao::write('pg', '');
    Sessao::write('pos', '');
    Sessao::write('paginando', '');

    $rsRecordset = new RecordSet;
    $obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
    $stMascaraRubrica = $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );

    //****************************************//
    //Define COMPONENTES DO FORMULARIO
    //****************************************//
    //Instancia o formulário
    $obForm = new Form;
    $obForm->setAction( $pgList );
    $obForm->setTarget( "telaPrincipal" );

    //Define o objeto da ação stAcao
    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ( "stAcao" );
    $obHdnAcao->setValue( $stAcao );

    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName ( "stCtrl" );
    $obHdnCtrl->setValue( $stCtrl );

    $obHdnExercicio = new Hidden;
    $obHdnExercicio->setName ( "stExercicio" );
    $obHdnExercicio->setValue( Sessao::getExercicio() );

    include_once( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php" );
    $obCmbEntidades = new ISelectMultiploEntidadeUsuario();

    // Define objeto TextBox para Armazenar Exercicio
    $obLblExercicio = new Label;
    $obLblExercicio->setRotulo( "*Exercício" );
    $obLblExercicio->setValue ( Sessao::getExercicio() );

    // Define Objeto BuscaInner para Despesa
    include_once( CAM_GF_ORC_COMPONENTES."IPopUpDotacaoFiltro.class.php" );
    $obPopUpDotacao = new IPopUpDotacaoFiltro(serialize($obCmbEntidades));
    $obPopUpDotacao->obCampoCod->setName ( 'inCodDespesa' );
    $obPopUpDotacao->obCampoCod->setId   ( 'inCodDespesa' );

    //Define o objeto TEXT para armazenar Codigo da Autorizacao
    $obTxtCodAutorizacaoInicial = new TextBox;
    $obTxtCodAutorizacaoInicial->setName     ( "inCodAutorizacaoInicial" );
    $obTxtCodAutorizacaoInicial->setValue    ( $inCodAutorizacaoInicial  );
    $obTxtCodAutorizacaoInicial->setRotulo   ( "Número da Autorização" );
    $obTxtCodAutorizacaoInicial->setTitle    ( "Informe o número da autorização." );
    $obTxtCodAutorizacaoInicial->setNull     ( true );
    $obTxtCodAutorizacaoInicial->setInteiro  ( true );

    //Define o objeto TEXT para armazenar Codigo da Autorizacao
    $obTxtCodAutorizacaoFinal = new TextBox;
    $obTxtCodAutorizacaoFinal->setName     ( "inCodAutorizacaoFinal" );
    $obTxtCodAutorizacaoFinal->setValue    ( $inCodAutorizacaoFinal  );
    $obTxtCodAutorizacaoFinal->setRotulo   ( "Número da Autorização" );
    $obTxtCodAutorizacaoFinal->setTitle    ( "Informe o número da autorização." );
    $obTxtCodAutorizacaoFinal->setNull     ( true );
    $obTxtCodAutorizacaoFinal->setInteiro  ( true );

    // Define objeto Data para Periodo
    $obDtInicial = new Data;
    $obDtInicial->setName     ( "stDtInicial" );
    $obDtInicial->setRotulo   ( "Período" );
    $obDtInicial->setTitle    ( 'Informe o período.' );
    $obDtInicial->setNull     ( true );

    // Define Objeto Label
    $obLabel = new Label;
    $obLabel->setValue( " até " );

    // Define objeto Data para validade final
    $obDtFinal = new Data;
    $obDtFinal->setName     ( "stDtFinal" );
    $obDtFinal->setRotulo   ( "Período" );
    $obDtFinal->setTitle    ( '' );
    $obDtFinal->setNull     ( true );

    // Define Objeto BuscaInner para Fornecedor
    $obBscFornecedor = new BuscaInner;
    $obBscFornecedor->setRotulo ( "Fornecedor" );
    $obBscFornecedor->setTitle  ( "Informe o fornecedor." );
    $obBscFornecedor->setId     ( "stNomFornecedor" );
    $obBscFornecedor->setValue  ( $stNomFornecedor  );
    $obBscFornecedor->obCampoCod->setName ( "inCodFornecedor" );
    $obBscFornecedor->obCampoCod->setSize ( 10 );
    $obBscFornecedor->obCampoCod->setMaxLength( 8 );
    $obBscFornecedor->obCampoCod->setValue ( $inCodFornecedor );
    $obBscFornecedor->obCampoCod->setAlign ("left");
    $obBscFornecedor->obCampoCod->obEvento->setOnBlur("buscaFornecedor();");
    $obBscFornecedor->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodFornecedor','stNomFornecedor','','".Sessao::getId()."','800','550');");
    
    $obIMontaCompraDiretaLicitacaoEmpenho = new IMontaCompraDiretaLicitacaoEmpenho($obForm);

    if (Sessao::getExercicio() > '2015') {
        $obCentroCusto = new TextBox;
        $obCentroCusto->setRotulo ("Centro de Custo");
        $obCentroCusto->setTitle ("Informe o centro de custo");
        $obCentroCusto->setName ('inCentroCusto');
        $obCentroCusto->setId ('inCentroCusto');
        $obCentroCusto->setInteiro (true);
    }

    //****************************************//
    //Monta FORMULARIO
    //****************************************//
    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );

    $obFormulario->addHidden( $obHdnAcao              );
    $obFormulario->addHidden( $obHdnCtrl              );
    $obFormulario->addHidden( $obHdnExercicio         );

    $obFormulario->addTitulo( "Dados para Filtro"  );
    $obFormulario->addComponente( $obCmbEntidades );
    $obFormulario->addComponente( $obLblExercicio );
    if (Sessao::getExercicio() > '2015') {
        $obFormulario->addComponente( $obCentroCusto  );
    }
    $obFormulario->addComponente( $obPopUpDotacao );

    $obFormulario->agrupaComponentes( array( $obTxtCodAutorizacaoInicial, $obLabel, $obTxtCodAutorizacaoFinal ) );
    $obFormulario->agrupaComponentes( array( $obDtInicial,$obLabel, $obDtFinal ) );
    $obFormulario->addComponente( $obBscFornecedor );
    
    if($stAcao == "alterar" || $stAcao == "anular" || $stAcao == "imprimir")
        $obIMontaCompraDiretaLicitacaoEmpenho->geraFormulario( $obFormulario );
    
    $obFormulario->OK();
    $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
