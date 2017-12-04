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
    $Author: eduardoschitz $
    $Date: 2008-02-27 07:17:08 -0300 (Qua, 27 Fev 2008) $

    * Casos de uso: uc-02.03.17
                    uc-02.03.18
                    uc-02.03.19
                    uc-02.03.15
                    uc-02.03.21
                    uc-02.03.24
                    uc-02.03.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

if($_REQUEST['stAcao'] == 'liquidar' || $_REQUEST['stAcao'] == 'anular')
include_once( CAM_GF_INCLUDE."validaGF.inc.php");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );


//Define o nome dos arquivos PHP
$stPrograma = "ManterLiquidacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::remove('link');

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
} elseif ($stAcao == "anular" or $stAcao == "imprimir"  or $stAcao == "consultar") {
    $pgList = "LSAnularLiquidacao.php";
}

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($rsUltimoMesEncerrado->getCampo('mes') >= $mesAtual AND $boUtilizarEncerramentoMes == 'true' AND $stAcao != "consultar") {
    $obSpan = new Span;
    $obSpan->setValue('<b>Não é possível utilizar esta rotina pois o mês atual está encerrado!</b>');
    $obSpan->setStyle('align: center;');
    $obFormulario = new Formulario;
    $obFormulario->addSpan($obSpan);
    $obFormulario->show();
} else {
    Sessao::remove('filtro');
    Sessao::remove('pg');
    Sessao::remove('pos');
    Sessao::remove('paginando');
    $rsRecordset = new RecordSet;
    $obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
    $stMascaraRubrica = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    
    if($stAcao == 'liquidar' || $stAcao == 'anular')
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->listarEntidadeRestos( $rsEntidades);
    else
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades);

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
    $obHdnCtrl->setValue( '' );

    // Define SELECT multiplo para codigo da entidade
    $obCmbEntidades = new SelectMultiplo();
    $obCmbEntidades->setName   ('inCodEntidade');
    $obCmbEntidades->setRotulo ( "Entidades" );
    $obCmbEntidades->setTitle  ( "Selecione as entidades para o filtro." );
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

    //Define o objeto DATA para armazenar o Exercicio do empenho
    $obDtExercicioEmpenho = new TextBox;
    $obDtExercicioEmpenho->setSize      ( 4 );
    $obDtExercicioEmpenho->setMaxLength ( 4 );
    $obDtExercicioEmpenho->setInteiro  ( true );
    $obDtExercicioEmpenho->setName   ( "dtExercicioEmpenho" );
    $obDtExercicioEmpenho->setRotulo ( "Exercício Empenho" );
    $obDtExercicioEmpenho->setTitle  ( "Informe o exercício do empenho para o filtro." );
    $obDtExercicioEmpenho->setNull   ( false );
    $obDtExercicioEmpenho->setValue  ( Sessao::getExercicio() );

    // Define Objeto BuscaInner para Despesa
    $obBscDespesa = new BuscaInner;
    $obBscDespesa->setRotulo ( "Dotação Orçamentária"   );
    $obBscDespesa->setTitle  ( "Informe a dotação orçamentária para o filtro." );
    $obBscDespesa->setNulL   ( true                     );
    $obBscDespesa->setId     ( "stNomDespesa"           );
    $obBscDespesa->setValue  ( ''            );
    $obBscDespesa->obCampoCod->setName ( "inCodDespesa" );
    $obBscDespesa->obCampoCod->setSize ( 10 );
    $obBscDespesa->obCampoCod->setMaxLength( 5 );
    $obBscDespesa->obCampoCod->setAlign ("left");
    $obBscDespesa->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDespesa','stNomDespesa','','".Sessao::getId()."','800','550');");
    $obBscDespesa->setValoresBusca ( CAM_GF_ORC_POPUPS."despesa/OCDespesa.php?".Sessao::getId(), $obForm->getName(), '');

    //Define o objeto TEXT para Codigo do Empenho Inicial
    $obTxtCodEmpenhoInicial = new TextBox;
    $obTxtCodEmpenhoInicial->setName     ( "inCodEmpenhoInicial" );
    $obTxtCodEmpenhoInicial->setRotulo   ( "Número do Empenho"   );
    $obTxtCodEmpenhoInicial->setTitle    ( "Informe a faixa de números de empenho para o filtro." );
    $obTxtCodEmpenhoInicial->setInteiro  ( true                  );
    $obTxtCodEmpenhoInicial->setNull     ( true                  );

    //Define objeto Label
    $obLblEmpenho = new Label;
    $obLblEmpenho->setValue( "a" );

    //Define o objeto TEXT para Codigo do Empenho Final
    $obTxtCodEmpenhoFinal = new TextBox;
    $obTxtCodEmpenhoFinal->setName     ( "inCodEmpenhoFinal" );
    $obTxtCodEmpenhoFinal->setRotulo   ( "Número do Empenho" );
    $obTxtCodEmpenhoFinal->setInteiro  ( true                );
    $obTxtCodEmpenhoFinal->setNull     ( true                );

    // Define objeto Data para Vencimento
    $obDtVencimento = new Data;
    $obDtVencimento->setName     ( "stDtVencimento" );
    $obDtVencimento->setRotulo   ( "Vencimento" );
    $obDtVencimento->setTitle    ( 'Informe o vencimento para o filtro.' );
    $obDtVencimento->setNull     ( true );

    // Define objeto Data para Periodo
    $obDtInicial = new Data;
    $obDtInicial->setName     ( "stDtInicial" );
    $obDtInicial->setRotulo   ( "Período" );
    $obDtInicial->setTitle    ( 'Informe o período para o filtro.' );
    $obDtInicial->setNull     ( true );

    // Define Objeto Label
    $obLabel = new Label;
    $obLabel->setValue( " até " );

    // Define objeto Data para validade final
    $obDtFinal = new Data;
    $obDtFinal->setName     ( "stDtFinal" );
    $obDtFinal->setRotulo   ( "Período" );
    $obDtFinal->setNull     ( true );

    // Define Objeto BuscaInner para Fornecedor
    $obBscFornecedor = new BuscaInner;
    $obBscFornecedor->setRotulo ( "Credor" );
    $obBscFornecedor->setTitle  ( "Informe o credor para o filtro." );
    $obBscFornecedor->setId     ( "stNomFornecedor" );
    $obBscFornecedor->obCampoCod->setName ( "inCodFornecedor" );
    $obBscFornecedor->obCampoCod->setSize ( 10 );
    $obBscFornecedor->obCampoCod->setMaxLength( 8 );
    $obBscFornecedor->obCampoCod->setAlign ("left");
    $obBscFornecedor->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodFornecedor','stNomFornecedor','','".Sessao::getId()."','800','550');");
    $obBscFornecedor->setValoresBusca( CAM_GA_CGM_POPUPS."cgm/OCProcurarCgm.php?".Sessao::getId(), $obForm->getName() );

    //EXERCICIO
    $obCmbSituacao= new Select;
    $obCmbSituacao->setRotulo              ( "Situação"                     );
    $obCmbSituacao->setName                ( "inSituacao"                   );
    $obCmbSituacao->setTitle               ( "Selecione a situação"         );
    $obCmbSituacao->setStyle               ( "width: 200px"                 );
    $obCmbSituacao->addOption              ( "0", "Todas"                   );
    $obCmbSituacao->addOption              ( "6", "Anuladas"                );
    $obCmbSituacao->addOption              ( "7", "Pagas"                   );
    $obCmbSituacao->addOption              ( "8", "Válidas"                 );

    //Define o objeto TEXT para armazenar Codigo da Autorizacao
    $obTxtCodLiquidacaoInicial = new TextBox;
    $obTxtCodLiquidacaoInicial->setName     ( "inCodLiquidacaoInicial" );
    $obTxtCodLiquidacaoInicial->setRotulo   ( "Número da Liquidação"    );
    $obTxtCodLiquidacaoInicial->setTitle    ( "Informe a faixa de números da liquidação para o filtro." );
    $obTxtCodLiquidacaoInicial->setNull     ( true );
    $obTxtCodLiquidacaoInicial->setInteiro  ( true );

    //Define o objeto TEXT para armazenar Codigo da Autorizacao
    $obTxtCodLiquidacaoFinal = new TextBox;
    $obTxtCodLiquidacaoFinal->setName     ( "inCodLiquidacaoFinal" );
    $obTxtCodLiquidacaoFinal->setRotulo   ( "Número da Liquidação"  );
    $obTxtCodLiquidacaoFinal->setNull     ( true );
    $obTxtCodLiquidacaoFinal->setInteiro  ( true );

    if ((strtolower(SistemaLegado::pegaConfiguracao( 'seta_tipo_documento_liq_tceam',30, Sessao::getExercicio()))=='true') && $stAcao <> "liquidar") {
        $boMostrarCombo = 'true';

        include_once CAM_GPC_TCEAM_NEGOCIO.'RTCEAMTipoDocumento.class.php';
        $obRTCEAMTipoDocumento = new RTCEAMTipoDocumento;
        $obRTCEAMTipoDocumento->recuperaTipoDocumento($rsTipoDocumento);

        $obTxtTipoDocumento = new TextBox;
        $obTxtTipoDocumento->setRotulo   ('Tipo de Documento');
        $obTxtTipoDocumento->setTitle    ('Informe o Tipo do Documento');
        $obTxtTipoDocumento->setName     ('inCodTipoDocumentoTxt');
        $obTxtTipoDocumento->setValue    ('');
        $obTxtTipoDocumento->setSize     (4);
        $obTxtTipoDocumento->setMaxLength(3);
        $obTxtTipoDocumento->setInteiro  (true);

        $obCboTipoDocumento = new Select;
        $obCboTipoDocumento->setName      ('inCodTipoDocumento');
        $obCboTipoDocumento->setId        ('inCodTipoDocumento');
        $obCboTipoDocumento->setTitle     ('Informe o Tipo do Documento.');
        $obCboTipoDocumento->setRotulo    ('Tipo de Documento');
        $obCboTipoDocumento->setCampoDesc ('descricao');
        $obCboTipoDocumento->setCampoId   ('cod_tipo');
        $obCboTipoDocumento->addOption    ('', 'Selecione');
        $obCboTipoDocumento->preencheCombo($rsTipoDocumento);

    } else {
        $boMostrarCombo = 'false';
    }

    //****************************************//
    //Monta FORMULARIO
    //****************************************//
    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );

    $obFormulario->addHidden( $obHdnAcao              );
    $obFormulario->addHidden( $obHdnCtrl              );

    $obFormulario->addTitulo( "Dados para filtro"  );
    $obFormulario->addComponente( $obCmbEntidades );
    $obFormulario->addComponente( $obDtExercicioEmpenho );
    $obFormulario->addComponente( $obBscDespesa );
    $obFormulario->agrupaComponentes( array( $obTxtCodEmpenhoInicial, $obLblEmpenho, $obTxtCodEmpenhoFinal ) );
    $obFormulario->agrupaComponentes( array( $obDtInicial,$obLabel, $obDtFinal ) );
    if($stAcao<>"liquidar")
        $obFormulario->agrupaComponentes( array( $obTxtCodLiquidacaoInicial, $obLabel, $obTxtCodLiquidacaoFinal ) );
    $obFormulario->addComponente( $obDtVencimento );
    $obFormulario->addComponente( $obBscFornecedor );
    if ($_REQUEST['stAcao'] == 'liquidar') {
        $obFormulario->addComponente( $obCmbSituacao );
    }

    if ($boMostrarCombo=='true') {
        $obFormulario->addComponenteComposto($obTxtTipoDocumento, $obCboTipoDocumento);
    }

    $obFormulario->OK('BloqueiaFrames(true,false);');
    $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>