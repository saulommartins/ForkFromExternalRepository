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
    * Página de Filtro de Anulacao de Empenho
    * Data de Criação   : 06/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 31087 $
    $Name$
    $Autor:$
    $Date: 2007-07-23 11:56:48 -0300 (Seg, 23 Jul 2007) $

    * Casos de uso: uc-02.03.03,uc-02.03.17,uc-02.03.18
*/

include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php" );
include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php" );
include_once ( CAM_GF_EMP_COMPONENTES."IMontaCompraDiretaLicitacaoEmpenho.class.php" );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php" );

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

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
    if($stAcao<>"imprimir" and $stAcao<>"imprimirAN")
        include_once( CAM_GF_INCLUDE."validaGF.inc.php");

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );

    //Define o nome dos arquivos PHP
    $stPrograma = "AnularEmpenho";
    $pgFilt = "FL".$stPrograma.".php";
    $pgList = "LS".$stPrograma.".php";
    $pgForm = "FM".$stPrograma.".php";
    $pgProc = "PR".$stPrograma.".php";
    $pgOcul = "OC".$stPrograma.".php";
    $pgJS   = "JS".$stPrograma.".js";

    include_once( $pgJS );

    //Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
    $stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
    if ( empty( $stAcao ) ) {
        $stAcao = "excluir";
    }

    Sessao::remove('filtro');
    Sessao::remove('pg');
    Sessao::remove('pos');
    Sessao::remove('paginando');
    Sessao::remove('link');

    $rsRecordset = new RecordSet;
    $obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
    $stMascaraRubrica = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    
    if($stAcao<>"imprimir" and $stAcao<>"imprimirAN")
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->listarEntidadeRestos( $rsEntidades, $stOrdem );
    else
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );

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

    // Define objeto TextBox para Armazenar Exercicio
    $obTxtAno = new TextBox;
    $obTxtAno->setName      ( "stExercicio"       );
    $obTxtAno->setId        ( "stExercicio"       );
    $obTxtAno->setValue     ( Sessao::getExercicio()  );
    $obTxtAno->setRotulo    ( "Exercício do Empenho" );
    $obTxtAno->setTitle     ( "Informe o exercício do empenho." );
    $obTxtAno->setNull      ( false               );
    $obTxtAno->setMaxLength ( 4                   );
    $obTxtAno->setSize      ( 4                   );
    $obTxtAno->setInteiro   ( true                );

    // Define Objeto BuscaInner para Despesa
    $obBscDespesa = new BuscaInner;
    $obBscDespesa->setRotulo ( "Dotação Orçamentária"   );
    $obBscDespesa->setTitle  ( "Informe a dotação orçamentária." );
    $obBscDespesa->setNulL   ( true                     );
    $obBscDespesa->setId     ( "stNomDespesa"           );
    $obBscDespesa->setValue  ( $stNomDespesa            );
    $obBscDespesa->obCampoCod->setName ( "inCodDespesa" );
    $obBscDespesa->obCampoCod->setSize ( 10 );
    $obBscDespesa->obCampoCod->setMaxLength( 5 );
    $obBscDespesa->obCampoCod->setValue ( $inCodDespesa );
    $obBscDespesa->obCampoCod->setAlign ("left");
    $obBscDespesa->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDespesa','stNomDespesa','','".Sessao::getId()."','800','550');");
    $obBscDespesa->setValoresBusca ( CAM_GF_ORC_POPUPS."despesa/OCDespesa.php?".Sessao::getId(), $obForm->getName(), '');

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

    //Define o objeto TEXT para Código da Autorização Inicial
    $obTxtCodAutorizacaoInicial = new TextBox;
    $obTxtCodAutorizacaoInicial->setName     ( "inCodAutorizacaoInicial" );
    $obTxtCodAutorizacaoInicial->setValue    ( $inCodAutorizacaoInicial  );
    $obTxtCodAutorizacaoInicial->setRotulo   ( "Número da Autorização"   );
    $obTxtCodAutorizacaoInicial->setTitle    ( "Informe o número da autorização."   );
    $obTxtCodAutorizacaoInicial->setInteiro  ( true                      );
    $obTxtCodAutorizacaoInicial->setNull     ( true                      );

    //Define objeto Label
    $obLblAutorizacao = new Label;
    $obLblAutorizacao->setValue( "a" );

    //Define o objeto TEXT para Codigo da Autorização Final
    $obTxtCodAutorizacaoFinal = new TextBox;
    $obTxtCodAutorizacaoFinal->setName     ( "inCodAutorizacaoFinal" );
    $obTxtCodAutorizacaoFinal->setValue    ( $inCodAutorizacaoFinal  );
    $obTxtCodAutorizacaoFinal->setRotulo   ( "Número da Autorização" );
    $obTxtCodAutorizacaoFinal->setInteiro  ( true                    );
    $obTxtCodAutorizacaoFinal->setNull     ( true                    );

    // Define Objeto BuscaInner para Fornecedor
    $obBscFornecedor = new BuscaInner;
    if ($stAcao == "imprimirAN") {
        $obBscFornecedor->setRotulo ( "Credor" );
    } else {
        $obBscFornecedor->setRotulo ( "Fornecedor" );
    }
    $obBscFornecedor->setTitle  ( "Informe o ".strtolower($obBscFornecedor->getRotulo()) );
    $obBscFornecedor->setId     ( "stNomFornecedor" );
    $obBscFornecedor->setValue  ( $stNomFornecedor  );
    $obBscFornecedor->obCampoCod->setName ( "inCodFornecedor" );
    $obBscFornecedor->obCampoCod->setSize ( 10 );
    $obBscFornecedor->obCampoCod->setMaxLength( 8 );
    $obBscFornecedor->obCampoCod->setValue ( $inCodFornecedor );
    $obBscFornecedor->obCampoCod->setAlign ("left");
    $obBscFornecedor->obCampoCod->obEvento->setOnBlur("buscaDado('buscaFornecedorDiverso');");
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

    $obBtnClean = new Button;
    $obBtnClean->setName                    ( "btnClean"       );
    $obBtnClean->setValue                   ( "Limpar"         );
    $obBtnClean->setTipo                    ( "button"         );
    $obBtnClean->obEvento->setOnClick       ( "limparFiltro();" );
    $obBtnClean->setDisabled                ( false            );
    
    $obIMontaCompraDiretaLicitacaoEmpenho = new IMontaCompraDiretaLicitacaoEmpenho($obForm);

    $obBtnOK = new Ok;

    $botoesForm     = array ( $obBtnOK , $obBtnClean );

    //****************************************//
    //Monta FORMULARIO
    //****************************************//
    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );

    $obFormulario->addHidden( $obHdnAcao              );
    $obFormulario->addHidden( $obHdnCtrl              );

    $obFormulario->addTitulo         ( "Dados para Filtro"  );
    $obFormulario->addComponente     ( $obCmbEntidades  );
    $obFormulario->addComponente     ( $obTxtAno    );
    $obFormulario->addComponente     ( $obBscDespesa );
    $obFormulario->agrupaComponentes ( array( $obTxtCodEmpenhoInicial, $obLblEmpenho, $obTxtCodEmpenhoFinal ) );
    $obFormulario->agrupaComponentes ( array( $obTxtCodAutorizacaoInicial, $obLblAutorizacao, $obTxtCodAutorizacaoFinal ) );
    $obFormulario->addComponente     ( $obBscFornecedor );
    $obFormulario->agrupaComponentes ( array( $obDtInicial,$obLabel, $obDtFinal ) );
    
    if($stAcao == "anular" || $stAcao == "imprimir" || $stAcao == "imprimirAN" )
        $obIMontaCompraDiretaLicitacaoEmpenho->geraFormulario( $obFormulario );        
    
    $obFormulario->defineBarra( $botoesForm );

    $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
