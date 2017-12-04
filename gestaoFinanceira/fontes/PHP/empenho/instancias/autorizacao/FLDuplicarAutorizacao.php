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
    * Página de Processamento de Autorização
    * Data de Criação   : 01/12/2004

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor:$
    $Date: 2007-02-23 13:15:05 -0200 (Sex, 23 Fev 2007) $

    * Casos de uso: uc-02.03.02
                    uc-02.01.08
*/

/*
$Log$
Revision 1.11  2007/02/23 15:15:05  gelson
Sempre que for autorização tem que ir a reserva. Adicionado em todos arquivos o caso de uso da reserva.

Revision 1.10  2006/07/17 19:26:36  leandro.zis
Bug #6184#

Revision 1.9  2006/07/17 13:27:57  leandro.zis
Bug #6183#

Revision 1.8  2006/07/05 20:47:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "DuplicarAutorizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$stCaminho = CAM_GF_EMP_INSTANCIAS."autorizacao/OCGeraRelatorioAutorizacao.php";

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

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
    if ($stAcao == "imprimir") {
        $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho;
        $arDuplicarAutorizacao['inCodAutorizacao']        = $_REQUEST['inCodAutorizacao'];
        $arDuplicarAutorizacao['inCodAutorizacaoAnulada'] = $_REQUEST['inCodAutorizacaoAnulada'];
        $arDuplicarAutorizacao['inCodPreEmpenho']         = $_REQUEST['inCodPreEmpenho'];
        $arDuplicarAutorizacao['inCodPreEmpenhoAnulada']  = $_REQUEST['inCodPreEmpenhoAnulada'];
        $arDuplicarAutorizacao['inCodEntidade']           = $_REQUEST['inCodEntidade'];
        $arDuplicarAutorizacao['inCodDespesa']            = $_REQUEST['inCodDespesa'];
        $arDuplicarAutorizacao['stExercicio']             = $_REQUEST['stExercicio'];
        $arDuplicarAutorizacao['stAcao']                  = 'autorizacao';
        Sessao::write('arDuplicarAutorizacao', $arDuplicarAutorizacao);

        SistemaLegado::executaFramePrincipal( "window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );
        $stAcao = "duplicar";
    }

    //Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
    if ( empty( $stAcao ) ) {
        $stAcao = "consultar";
    }

    Sessao::write('filtro', array());
    Sessao::write('pg', '');
    Sessao::write('pos', '');
    Sessao::write('paginando', false);
    Sessao::remove('link');

    $rsRecordset    = new RecordSet;

    $obRegra = new REmpenhoEmpenhoAutorizacao;
    $obRegra->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obRegra->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obRegra->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );

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

    //Define o objeto TEXT para Exercicio
    $obTxtExercicio = new TextBox;
    $obTxtExercicio->setName     ( "stExercicio"   );
    $obTxtExercicio->setValue    ( Sessao::getExercicio()     );
    $obTxtExercicio->setRotulo   ( "Exercício" );
    $obTxtExercicio->setTitle    ( "Informe o exercício." );
    $obTxtExercicio->setMaxLength( 4                      );
    $obTxtExercicio->setSize     ( 4                      );
    $obTxtExercicio->setInteiro  ( true                   );
    $obTxtExercicio->setNull     ( false                  );

    // define objeto Data
    $obTxtDtInicio = new Data;
    $obTxtDtInicio->setName     ( "stDtInicio" );
    $obTxtDtInicio->setValue    ( $stDtInicio  );
    $obTxtDtInicio->setRotulo   ( "Período" );
    $obTxtDtInicio->setTitle    ( "Informe o período." );
    $obTxtDtInicio->setNull     ( true );

    // define objeto Label
    $obLblPeriodo = new Label;
    $obLblPeriodo->setValue( " até " );

    // define objeto Data
    $obTxtDtTermino = new Data;
    $obTxtDtTermino->setName     ( "stDtTermino" );
    $obTxtDtTermino->setValue    ( $stDtTermino  );
    $obTxtDtTermino->setRotulo   ( "Período" );
    $obTxtDtTermino->setNull     ( true );

    //Define o objeto TEXT para Codigo do Empenho Inicial
    $obTxtCodEmpenhoInicial = new TextBox;
    $obTxtCodEmpenhoInicial->setName     ( "inCodEmpenhoInicial" );
    $obTxtCodEmpenhoInicial->setValue    ( $inCodEmpenhoInicial  );
    $obTxtCodEmpenhoInicial->setRotulo   ( "Número do Empenho"   );
    $obTxtCodEmpenhoInicial->setTitle    ( "Informe o número do empenho." );
    $obTxtCodEmpenhoInicial->setInteiro  ( true                  );
    $obTxtCodEmpenhoInicial->setNull     ( true                  );

    //Define objeto Label
    $obLblEmpenho = new Label;
    $obLblEmpenho->setValue( "a" );

    //Define o objeto TEXT para Codigo do Empenho Final
    $obTxtCodEmpenhoFinal = new TextBox;
    $obTxtCodEmpenhoFinal->setName     ( "inCodEmpenhoFinal" );
    $obTxtCodEmpenhoFinal->setValue    ( $inCodEmpenhoFinal  );
    $obTxtCodEmpenhoFinal->setRotulo   ( "Número do Empenho" );
    $obTxtCodEmpenhoFinal->setInteiro  ( true                );
    $obTxtCodEmpenhoFinal->setNull     ( true                );

    //Define o objeto TEXT para Codigo da Autorização Inicial
    $obTxtCodAutorizacaoInicial = new TextBox;
    $obTxtCodAutorizacaoInicial->setName     ( "inCodAutorizacaoInicial" );
    $obTxtCodAutorizacaoInicial->setValue    ( $inCodAutorizacaoInicial  );
    $obTxtCodAutorizacaoInicial->setRotulo   ( "Número da Autorização"   );
    $obTxtCodAutorizacaoInicial->setTitle    ( "Informe o número da autorização."   );
    $obTxtCodAutorizacaoInicial->setInteiro  ( true                  );
    $obTxtCodAutorizacaoInicial->setNull     ( true                  );

    //Define objeto Label
    $obLblAutorizacao = new Label;
    $obLblAutorizacao->setValue( "a" );

    //Define o objeto TEXT para Codigo da Autorização Final
    $obTxtCodAutorizacaoFinal = new TextBox;
    $obTxtCodAutorizacaoFinal->setName     ( "inCodAutorizacaoFinal" );
    $obTxtCodAutorizacaoFinal->setValue    ( $inCodAutorizacaoFinal  );
    $obTxtCodAutorizacaoFinal->setRotulo   ( "Número da Autorização" );
    $obTxtCodAutorizacaoFinal->setInteiro  ( true                );
    $obTxtCodAutorizacaoFinal->setNull     ( true                );

    // Define Objeto BuscaInner para Fornecedor
    $obBscFornecedor = new BuscaInner;
    $obBscFornecedor->setRotulo                 ( "Credor"      );
    $obBscFornecedor->setTitle                  ( "Selecione o fornecedor.");
    $obBscFornecedor->setId                     ( "stNomFornecedor" );
    $obBscFornecedor->setValue                  ( $stNomFornecedor  );
    $obBscFornecedor->obCampoCod->setName       ( "inCodFornecedor" );
    $obBscFornecedor->obCampoCod->setSize       ( 10                );
    $obBscFornecedor->obCampoCod->setMaxLength  ( 8                 );
    $obBscFornecedor->obCampoCod->setValue      ( $inCodFornecedor  );
    $obBscFornecedor->obCampoCod->setAlign      ("left"             );
    $obBscFornecedor->obCampoCod->obEvento->setOnBlur("buscaDado('buscaFornecedorDiverso');");
    $obBscFornecedor->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodFornecedor','stNomFornecedor','','".Sessao::getId()."','800','550');");

    $obBtnClean = new Button;
    $obBtnClean->setName                    ( "btnClean"       );
    $obBtnClean->setValue                   ( "Limpar"         );
    $obBtnClean->setTipo                    ( "button"         );
    $obBtnClean->obEvento->setOnClick       ( "limparFiltro();");
    $obBtnClean->setDisabled                ( false            );

    $obBtnOK = new Ok;

    $botoesForm     = array ( $obBtnOK , $obBtnClean );

    //****************************************//
    //Monta FORMULARIO
    //****************************************//
    $obFormulario = new Formulario;
    $obFormulario->addForm              ( $obForm                                   );

    $obFormulario->addHidden            ( $obHdnAcao                                );
    $obFormulario->addHidden            ( $obHdnCtrl                                );

    $obFormulario->addTitulo            ( "Dados para Filtro"                       );
    $obFormulario->addComponente        ( $obCmbEntidades                           );
    $obFormulario->addComponente        ( $obTxtExercicio                           );
    $obFormulario->agrupaComponentes    ( array($obTxtDtInicio, $obLblPeriodo, $obTxtDtTermino) );
    $obFormulario->agrupaComponentes    ( array( $obTxtCodEmpenhoInicial, $obLblEmpenho, $obTxtCodEmpenhoFinal ) );
    $obFormulario->agrupaComponentes    ( array( $obTxtCodAutorizacaoInicial, $obLblAutorizacao, $obTxtCodAutorizacaoFinal ) );
    $obFormulario->addComponente        ( $obBscFornecedor                          );
    $obFormulario->defineBarra          ( $botoesForm                               );

    //$obFormulario->OK();
    $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
