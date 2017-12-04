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
    * Página de Filtro de Empenho
    * Data de Criação   : 05/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: $

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2007-09-12 12:43:41 -0300 (Qua, 12 Set 2007) $

    * Casos de uso: uc-02.03.03
                    uc-02.01.08

*/
include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php" );
include_once ( CAM_GF_INCLUDE."validaGF.inc.php" );
include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php" );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php"             );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php"      );
include_once ( CAM_GF_EMP_COMPONENTES."IMontaCompraDiretaLicitacaoEmpenho.class.php" );
include_once CAM_GP_ALM_COMPONENTES.'IPopUpCentroCustoUsuario.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterEmpenho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');

include_once ($pgJS);

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);

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
    $stOrdem = isset($stOrdem) ? $stOrdem : "";
    //Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
    $stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
    if ( empty( $stAcao ) ) {
        $stAcao = "excluir";
    }

    Sessao::write('filtro', array());
    Sessao::write('pg', '');
    Sessao::write('pos', '');
    Sessao::write('paginando', false);

    $rsRecordset = new RecordSet;
    $obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
    $stMascaraRubrica = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->listarEntidadeRestos( $rsEntidades, $stOrdem );

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

    // Define SELECT multiplo para codigo da entidade
    $obCmbEntidades = new SelectMultiplo();
    $obCmbEntidades->setName   ('inCodEntidade');
    $obCmbEntidades->setRotulo ( "Entidades" );
    $obCmbEntidades->setTitle  ( "Selecione as entidades." );
    $obCmbEntidades->setNull   ( false );

    // Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
    if ($rsEntidades->getNumLinhas()==1) {
           $rsRecordset = $rsEntidades;
           $rsEntidades = new RecordSet;
    }

    // lista de atributos disponiveis
    $obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
    $obCmbEntidades->setCampoId1   ( 'cod_entidade' );
    $obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
    $obCmbEntidades->SetRecord1    ( $rsEntidades );
    // lista de atributos selecionados
    $obCmbEntidades->SetNomeLista2 ('inCodEntidade');
    $obCmbEntidades->setCampoId2   ('cod_entidade');
    $obCmbEntidades->setCampoDesc2 ('nom_cgm');
    $obCmbEntidades->SetRecord2    ( $rsRecordset );

    // Define Objeto BuscaInner para Despesa
    include_once( CAM_GF_ORC_COMPONENTES."IPopUpDotacaoFiltroClassificacao.class.php" );
    $obPopUpDotacao = new IPopUpDotacaoFiltroClassificacao($obCmbEntidades);
    $obPopUpDotacao->obCampoCod->setName ( 'inCodDespesa' );
    $obPopUpDotacao->obCampoCod->setId   ( 'inCodDespesa' );

    //Define o objeto TEXT para armazenar Codigo da Autorizacao
    $obTxtCodAutorizacaoInicial = new TextBox;
    $obTxtCodAutorizacaoInicial->setName     ( "inCodAutorizacaoInicial" );
    $obTxtCodAutorizacaoInicial->setRotulo   ( "Número da Autorização" );
    $obTxtCodAutorizacaoInicial->setTitle    ( "Informe o número da autorização." );
    $obTxtCodAutorizacaoInicial->setNull     ( true );
    $obTxtCodAutorizacaoInicial->setInteiro  ( true );

    //Define o objeto TEXT para armazenar Codigo da Autorizacao
    $obTxtCodAutorizacaoFinal = new TextBox;
    $obTxtCodAutorizacaoFinal->setName     ( "inCodAutorizacaoFinal" );
    $obTxtCodAutorizacaoFinal->setRotulo   ( "Número da Autorização" );
    $obTxtCodAutorizacaoFinal->setTitle    ( "Informe o número da autorização." );
    $obTxtCodAutorizacaoFinal->setNull     ( true );
    $obTxtCodAutorizacaoFinal->setInteiro  ( true );

    // Define Objeto BuscaInner para Fornecedor
    $obBscFornecedor = new BuscaInner;
    $obBscFornecedor->setRotulo ( "Fornecedor" );
    $obBscFornecedor->setTitle  ( "Informe o fornecedor." );
    $obBscFornecedor->setId     ( "stNomFornecedor" );
    $obBscFornecedor->obCampoCod->setName ( "inCodFornecedor" );
    $obBscFornecedor->obCampoCod->setSize ( 10 );
    $obBscFornecedor->obCampoCod->setMaxLength( 8 );
    $obBscFornecedor->obCampoCod->setAlign ("left");
    $obBscFornecedor->obCampoCod->obEvento->setOnBlur("montaParametrosGET('buscaFornecedorDiverso','');");
    $obBscFornecedor->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodFornecedor','stNomFornecedor','','".Sessao::getId()."','800','550');");

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

    if (Sessao::getExercicio() > '2015') {
        $obCentroCusto = new IPopUpCentroCustoUsuario($obForm);
        $obCentroCusto->setNull             ( true );
        $obCentroCusto->setRotulo           (' Centro de Custo' );
        $obCentroCusto->obCampoCod->setName ( 'inCentroCusto' );
        $obCentroCusto->obCampoCod->setId   ( 'inCentroCusto' );
    }
        
    $obIMontaCompraDiretaLicitacaoEmpenho = new IMontaCompraDiretaLicitacaoEmpenho($obForm);
    
    //****************************************//
    //Monta FORMULARIO
    //****************************************//
    $obFormulario = new Formulario;
    $obFormulario->addForm           ( $obForm );
    $obFormulario->addHidden         ( $obHdnAcao );
    $obFormulario->addHidden         ( $obHdnCtrl );
    $obFormulario->addTitulo         ( "Dados para Filtro"  );
    $obFormulario->addComponente     ( $obCmbEntidades );
    if (Sessao::getExercicio() > '2015') {
        $obFormulario->addComponente     ( $obCentroCusto  );
    }
    $obFormulario->addComponente     ( $obPopUpDotacao );
    $obFormulario->agrupaComponentes ( array( $obTxtCodAutorizacaoInicial,$obLabel, $obTxtCodAutorizacaoFinal ) );
    $obFormulario->addComponente     ( $obBscFornecedor );
    $obFormulario->agrupaComponentes ( array( $obDtInicial,$obLabel, $obDtFinal ) );
    $obIMontaCompraDiretaLicitacaoEmpenho->geraFormulario( $obFormulario );
    
    $obFormulario->OK();
    $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
