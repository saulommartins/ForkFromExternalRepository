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
    * Filtro para Empenho - Ordem de Pagamento
    * Data de Criação   : 17/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    $Id: FLManterOrdemPagamento.php 64697 2016-03-22 19:12:28Z carlos.silva $

    * Casos de uso: uc-02.03.19
                    uc-02.03.20
                    uc-02.03.25
                    uc-02.03.22
                    uc-02.03.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

if($_REQUEST['stAcao'] == 'liquidar' || $_REQUEST['stAcao'] == 'anular')
include_once( CAM_GF_INCLUDE."validaGF.inc.php");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
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
    include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php" );

    //Define o nome dos arquivos PHP
    $stPrograma      = "ManterOrdemPagamento";
    $pgFilt          = "FL".$stPrograma.".php";
    $pgList          = "LS".$stPrograma.".php";
    $pgForm          = "FM".$stPrograma.".php";
    $pgProc          = "PR".$stPrograma.".php";
    $pgOcul          = "OC".$stPrograma.".php";
    $pgJs            = "JS".$stPrograma.".js";
    include_once( $pgJs );

    Sessao::remove('link');

    // OBJETOS HIDDEN
    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName  ( "stCtrl" );
    $obHdnCtrl->setValue ( $stCtrl  );

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName  ( "stAcao" );
    $obHdnAcao->setValue ( $stAcao  );

    // DEFINE OBJETOS DAS CLASSES
    $obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
    $rsRecordset = new RecordSet;
    $obREmpenhoOrdemPagamento->obROrcamentoEntidade->setExercicio( Sessao::getExercicio()     );
    $obREmpenhoOrdemPagamento->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm')   );
    
    if($stAcao == 'incluir' || $stAcao == 'anular')
    $obREmpenhoOrdemPagamento->obROrcamentoEntidade->listarEntidadeRestos( $rsEntidades );
    else
    $obREmpenhoOrdemPagamento->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades );
    
    // DEFINE OBJETOS DO FORMULARIO

    // Define SELECT multiplo para codigo da entidade
    $obCmbEntidades = new SelectMultiplo();
    $obCmbEntidades->setName                       ( 'inCodEntidade'                 );
    $obCmbEntidades->setRotulo                     ( "Entidades"                     );
    $obCmbEntidades->setTitle                      ( "Selecione as entidades."       );
    $obCmbEntidades->setNull                       ( false                           );

    // Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
    if ($rsEntidades->getNumLinhas()==1) {
           $rsRecordset = $rsEntidades;
           $rsEntidades = new RecordSet;
    }

    // lista de atributos disponiveis
    $obCmbEntidades->SetNomeLista1                 ( 'inCodigoEntidadeDisponivel'    );
    $obCmbEntidades->setCampoId1                   ( 'cod_entidade'                  );
    $obCmbEntidades->setCampoDesc1                 ( 'nom_cgm'                       );
    $obCmbEntidades->SetRecord1                    ( $rsEntidades                    );
    // lista de atributos selecionados
    $obCmbEntidades->SetNomeLista2                 ( 'inCodEntidade'                 );
    $obCmbEntidades->setCampoId2                   ( 'cod_entidade'                  );
    $obCmbEntidades->setCampoDesc2                 ( 'nom_cgm'                       );
    $obCmbEntidades->SetRecord2                    ( $rsRecordset                    );

    //Define o objeto TEXT para Exercicio
    $obTxtExercicioEmpenho = new TextBox;
    $obTxtExercicioEmpenho->setName     ( "stExercicioEmpenho"   );
    $obTxtExercicioEmpenho->setValue    ( Sessao::getExercicio()     );
    $obTxtExercicioEmpenho->setRotulo   ( "Exercício do Empenho" );
    $obTxtExercicioEmpenho->setTitle    ( "Informe o exercício do empenho." );
    $obTxtExercicioEmpenho->setMaxLength( 4                      );
    $obTxtExercicioEmpenho->setSize     ( 4                      );
    $obTxtExercicioEmpenho->setInteiro  ( true                   );
    $obTxtExercicioEmpenho->setNull     ( false                  );

    //Define o objeto TEXT para Codigo da Ordem de Pagamento inicial
    $obTxtCodigoOrdemPagamentoInicial = new TextBox;
    $obTxtCodigoOrdemPagamentoInicial->setName     ( "inCodigoOrdemPagamentoInicial" );
    $obTxtCodigoOrdemPagamentoInicial->setValue    ( $inCodigoOrdemPagamentoInicial  );
    $obTxtCodigoOrdemPagamentoInicial->setRotulo   ( "Número da Ordem"               );
    $obTxtCodigoOrdemPagamentoInicial->setTitle    ( "Informe o número da ordem."    );
    $obTxtCodigoOrdemPagamentoInicial->setInteiro  ( true                            );
    $obTxtCodigoOrdemPagamentoInicial->setNull     ( true                            );

    //Define objeto Label
    $obLblOrdemPagamento = new Label;
    $obLblOrdemPagamento->setValue( "&nbsp;a&nbsp;" );

    //Define o objeto TEXT para Codigo da Ordem de Pagamento final
    $obTxtCodigoOrdemPagamentoFinal = new TextBox;
    $obTxtCodigoOrdemPagamentoFinal->setName       ( "inCodigoOrdemPagamentoFinal"   );
    $obTxtCodigoOrdemPagamentoFinal->setValue      ( $inCodigoOrdemPagamentoFinal    );
    $obTxtCodigoOrdemPagamentoFinal->setRotulo     ( "Número da Ordem"               );
    $obTxtCodigoOrdemPagamentoFinal->setInteiro    ( true                            );
    $obTxtCodigoOrdemPagamentoFinal->setNull       ( true                            );

    //Define o objeto TEXT para Código do empenho
    $obTxtCodEmpenhoInicial = new TextBox;
    $obTxtCodEmpenhoInicial->setName     ( "inCodEmpenhoInicial" );
    $obTxtCodEmpenhoInicial->setValue    ( $inCodEmpenhoInicial  );
    $obTxtCodEmpenhoInicial->setRotulo   ( "Número do Empenho"   );
    $obTxtCodEmpenhoInicial->setTitle    ( "Informe o número do empenho."  );
    $obTxtCodEmpenhoInicial->setInteiro  ( true                            );
    $obTxtCodEmpenhoInicial->setNull     ( true                            );
    //Define objeto Label
    $obLblEmpenho = new Label;
    $obLblEmpenho->setValue( "&nbsp;a&nbsp;" );
    //Define o objeto TEXT para Codigo do Empenho final
    $obTxtCodEmpenhoFinal = new TextBox;
    $obTxtCodEmpenhoFinal->setName       ( "inCodEmpenhoFinal"   );
    $obTxtCodEmpenhoFinal->setValue      ( $inCodEmpenhoFinal    );
    $obTxtCodEmpenhoFinal->setRotulo     ( "Número do Empenho"   );
    $obTxtCodEmpenhoFinal->setInteiro    ( true                            );
    $obTxtCodEmpenhoFinal->setNull       ( true                            );

    // Define objeto Data para Vencimento
    $obDataVencimento = new Data;
    $obDataVencimento->setName     ( "dtDataVencimento" );
    $obDataVencimento->setRotulo   ( "Vencimento" );
    $obDataVencimento->setTitle    ( 'Informe o vencimento.' );
    $obDataVencimento->setNull     ( true );

    // Define objeto Data inicial para Periodo
    $obDataInicial = new Data;
    $obDataInicial->setName                        ( "dtDataInicial"                 );
    $obDataInicial->setRotulo                      ( "Período"                       );
    $obDataInicial->setTitle                       ( 'Informe o período.'            );
    $obDataInicial->setNull                        ( true                            );
    // Define Objeto Label
    $obLabel = new Label;
    $obLabel->setValue( " até " );
    // Define objeto Data final para Periodo
    $obDataFinal = new Data;
    $obDataFinal->setName                          ( "dtDataFinal"                   );
    $obDataFinal->setRotulo                        ( "Período"                       );
    $obDataFinal->setTitle                         ( ''                              );
    $obDataFinal->setNull                          ( true                            );

    // Define Objeto BuscaInner para Fornecedor
    $obBscFornecedor = new BuscaInner;
    $obBscFornecedor->setRotulo ( "Credor" );
    $obBscFornecedor->setTitle  ( "Informe o credor."             );
    $obBscFornecedor->setId     ( "stNomFornecedor" );
    $obBscFornecedor->setValue  ( $stNomFornecedor  );
    $obBscFornecedor->obCampoCod->setName ( "inCodFornecedor" );
    $obBscFornecedor->obCampoCod->setSize ( 10 );
    $obBscFornecedor->obCampoCod->setMaxLength( 8 );
    $obBscFornecedor->obCampoCod->setValue ( $inCodFornecedor );
    $obBscFornecedor->obCampoCod->setAlign ("left");
    $obBscFornecedor->obCampoCod->obEvento->setOnBlur("buscaFornecedorDiverso();");
    $obBscFornecedor->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodFornecedor','stNomFornecedor','','".Sessao::getId()."','800','550');");

    $obRadEstornados = new SimNao();
    $obRadEstornados->setRotulo( "Listar Anuladas"     );
    $obRadEstornados->setTitle ( "Selecione listar anuladas."     );
    $obRadEstornados->setName  ( "stMostrarEstornados" );

    //DEFINICAO DOS COMPONENTES
    $obForm = new Form;
    $obForm->setAction           ( $pgList                  );
    $obForm->setTarget           ( "telaPrincipal"          );

    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->addForm       ( $obForm                  );
    $obFormulario->addHidden     ( $obHdnCtrl               );
    $obFormulario->addHidden     ( $obHdnAcao               );

    $obFormulario->addTitulo     ( "Dados para Filtro"      );

    $obFormulario->addComponente( $obCmbEntidades );
    $obFormulario->addComponente( $obTxtExercicioEmpenho );
    $obFormulario->agrupaComponentes( array( $obTxtCodEmpenhoInicial, $obLblEmpenho, $obTxtCodEmpenhoFinal ) );
    $obFormulario->agrupaComponentes( array( $obTxtCodigoOrdemPagamentoInicial, $obLblOrdemPagamento, $obTxtCodigoOrdemPagamentoFinal ) );
    $obFormulario->agrupaComponentes( array( $obDataInicial,$obLabel, $obDataFinal ) );
    $obFormulario->addComponente( $obDataVencimento );
    $obFormulario->addComponente( $obBscFornecedor );
    if($stAcao != "anular" and $stAcao != "imprimirAN")
        $obFormulario->addComponente( $obRadEstornados );
    $obFormulario->Ok();
    $obFormulario->show();

    $js .= "document.frm.inCodigoEntidadeDisponivel.focus();";
    SistemaLegado::executaFramePrincipal($js);
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
