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
    * Filtro para funcionalidade Manter Pagamentos
    * Data de Criação   : 24/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: FLManterPagamento.php 64697 2016-03-22 19:12:28Z carlos.silva $

    $Revision: 32797 $
    $Name$
    $Autor:$
    $Date: 2006-11-11 12:12:11 -0200 (Sáb, 11 Nov 2006) $

    * Casos de uso: uc-02.04.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

if($_REQUEST['stAcao'] == 'incluir' || $_REQUEST['stAcao'] == 'alterar')
include_once( CAM_GF_INCLUDE."validaGF.inc.php");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_IAPPLETTERMINAL );
include_once ( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"          );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterPagamento";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

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
    $obRTesourariaBoletim = new RTesourariaBoletim();
    $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
    $obRTesourariaBoletim->setDataBoletim( date( 'd/m/'.Sessao::getExercicio() ) );
    $obRTesourariaBoletim->addPagamento();
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->listarEntidadeRestos( $rsEntidades );

    // DEFINICAO DOS COMPONENTES
    $obForm = new Form;
    $obForm->setAction( $pgList );
    $obForm->setTarget ( "oculto" );
    $obForm->setTarget( "telaPrincipal");

    // OBJETOS HIDDEN
    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName  ( "stCtrl"            );
    $obHdnCtrl->setValue ( $request->get('stCtrl') );

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName  ( "stAcao"            );
    $obHdnAcao->setValue ( $stAcao );

    $obApplet = new IAppletTerminal( $obForm );

    // DEFINE OBJETOS DO FORMULARIO
    // Define SELECT multiplo para codigo da entidade
    $obCmbEntidades = new SelectMultiplo();
    $obCmbEntidades->setName   ('inCodEntidade');
    $obCmbEntidades->setRotulo ( "Entidades" );
    $obCmbEntidades->setTitle  ( "Selecione a Entidade." );
    $obCmbEntidades->setNull   ( false );

    // Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
    $rsRecordSet = new RecordSet;
    if ($rsEntidades->getNumLinhas()==1) {
           $rsRecordSet = $rsEntidades;
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
    $obCmbEntidades->SetRecord2    ( $rsRecordSet );

    //Define Objeto Exercicio para exercicio
    $obTxtExercicio = new Exercicio();
    $obTxtExercicio->setRotulo  ( 'Exercício do Empenho' );
    $obTxtExercicio->setName    ( 'stExercicioEmpenho'   );
    $obTxtExercicio->setNull    ( false                  );
    $obTxtExercicio->setTitle   ( 'Informe Exercício.'   );
    $obTxtExercicio->setValue   ( Sessao::getExercicio()     );

    //Define Objeto Label
    $obLabel = new Label();
    $obLabel->setValue   ( ' até ' );

    //Define Objeto TextBox para número do empenho inicial
    $obTxtCodEmpenhoInicial = new TextBox();
    $obTxtCodEmpenhoInicial->setTitle ( 'Informe o Número do Empenho.' );
    $obTxtCodEmpenhoInicial->setRotulo ( 'Nr. Empenho'                                  );
    $obTxtCodEmpenhoInicial->setName   ( 'inCodEmpenhoInicial'                          );
    $obTxtCodEmpenhoInicial->setId     ( 'inCodEmpenhoInicial'                          );
    $obTxtCodEmpenhoInicial->setInteiro( true                                           );
    $obTxtCodEmpenhoInicial->setNull   ( true                                           );

    //Define Objeto TextBox para número do empenho final
    $obTxtCodEmpenhoFinal = new TextBox();
    $obTxtCodEmpenhoFinal->setTitle ( 'Informe o Número do Empenho.' );
    $obTxtCodEmpenhoFinal->setRotulo ( 'Nr. Empenho'                                  );
    $obTxtCodEmpenhoFinal->setName   ( 'inCodEmpenhoFinal'                            );
    $obTxtCodEmpenhoFinal->setId     ( 'inCodEmpenhoFinal'                            );
    $obTxtCodEmpenhoFinal->setInteiro( true                                           );
    $obTxtCodEmpenhoFinal->setNull   ( true                                           );

    //Define Objeto TextBox para número da liquidacao inicial
    $obTxtCodNotaInicial = new TextBox();
    $obTxtCodNotaInicial->setTitle  ( 'Informe o Número da Liquidação.' );
    $obTxtCodNotaInicial->setRotulo ( 'Nr. Liquidação'                                  );
    $obTxtCodNotaInicial->setName   ( 'inCodNotaInicial'                                );
    $obTxtCodNotaInicial->setId     ( 'inCodNotaInicial'                                );
    $obTxtCodNotaInicial->setInteiro( true                                              );
    $obTxtCodNotaInicial->setNull   ( true                                              );

    //Define Objeto TextBox para número da liquidacao final
    $obTxtCodNotaFinal = new TextBox();
    $obTxtCodNotaFinal->setTitle ( 'Informe o Número da Liquidação.' );
    $obTxtCodNotaFinal->setRotulo ( 'Nr. Liquidação'                                  );
    $obTxtCodNotaFinal->setName   ( 'inCodNotaFinal'                                  );
    $obTxtCodNotaFinal->setId     ( 'inCodNotaFinal'                                  );
    $obTxtCodNotaFinal->setInteiro( true                                              );
    $obTxtCodNotaFinal->setNull   ( true                                              );

    //Define Objeto TextBox para número da ordem de pagamento inicial
    $obTxtCodOrdemInicial = new TextBox();
    $obTxtCodOrdemInicial->setTitle ( 'Informe o Número da Ordem de Pagamento.' );
    $obTxtCodOrdemInicial->setRotulo ( 'Nr. da OP'                                               );
    $obTxtCodOrdemInicial->setName   ( 'inCodOrdemInicial'                                       );
    $obTxtCodOrdemInicial->setId     ( 'inCodOrdemInicial'                                       );
    $obTxtCodOrdemInicial->setInteiro( true                                                      );
    $obTxtCodOrdemInicial->setNull   ( true                                                      );

    //Define Objeto TextBox para número da ordem de pagamento final
    $obTxtCodOrdemFinal = new TextBox();
    $obTxtCodOrdemFinal->setTitle ( 'Informe o Número da Ordem de Pagamento.' );
    $obTxtCodOrdemFinal->setRotulo ( 'Nr. da OP'                                               );
    $obTxtCodOrdemFinal->setName   ( 'inCodOrdemFinal'                                         );
    $obTxtCodOrdemFinal->setId     ( 'inCodOrdemFinal'                                         );
    $obTxtCodOrdemFinal->setInteiro( true                                                      );
    $obTxtCodOrdemFinal->setNull   ( true                                                      );

    //Define Objeto TextBox para Código de Barras da OP
    $obTxtCodBarrasOP = new TextBox();
    $obTxtCodBarrasOP->setTitle     ( 'Informe o Código de Barras da OP.'                    );
    $obTxtCodBarrasOP->setRotulo    ( 'Cód. Barras da OP'                                   );
    $obTxtCodBarrasOP->setName      ( 'inCodBarrasOP'                                       );
    $obTxtCodBarrasOP->setId        ( 'inCodBarrasOP'                                       );
    $obTxtCodBarrasOP->setInteiro   ( true                                                  );
    $obTxtCodBarrasOP->setNull      ( true                                                  );
    $obTxtCodBarrasOP->setSize      ( 25                                                    );
    $obTxtCodBarrasOP->setMaxLength ( 20                                                    );
    $obTxtCodBarrasOP->obEvento->setOnChange("buscaDado( 'preencheCampos' );");

    // Define objeto BuscaInner para cgm
    $obBscCGM = new BuscaInner();
    $obBscCGM->setRotulo               ( "Credor"                                               );
    $obBscCGM->setTitle                ( "Informe o CGM do Credor do Empenho."  );
    $obBscCGM->setId                   ( "stNomCgm"                                             );
    $obBscCGM->setNull                 ( true                                                   );
    $obBscCGM->obCampoCod->setName     ( "inNumCgm"                                             );
    $obBscCGM->obCampoCod->setSize     ( 10                                                     );
    $obBscCGM->obCampoCod->setMaxLength( 8                                                      );
    $obBscCGM->obCampoCod->setAlign    ( "left"                                                 );
    $obBscCGM->setFuncaoBusca          ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCgm','stNomCgm','geral','".Sessao::getId()."','800','550');");
    $obBscCGM->setValoresBusca           ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName() );

    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->addForm      ( $obForm                                   );
    $obFormulario->addHidden    ( $obHdnCtrl                                );
    $obFormulario->addHidden    ( $obHdnAcao                                );
    $obFormulario->addHidden    ( $obApplet                                 );
    $obFormulario->addTitulo    ( "Dados para Filtro"                       );
    $obFormulario->addComponente( $obCmbEntidades                           );
    $obFormulario->addComponente( $obTxtExercicio                           );
    $obFormulario->agrupaComponentes( array( $obTxtCodEmpenhoInicial, $obLabel, $obTxtCodEmpenhoFinal ) );
    $obFormulario->agrupaComponentes( array( $obTxtCodNotaInicial   , $obLabel, $obTxtCodNotaFinal    ) );
    $obFormulario->agrupaComponentes( array( $obTxtCodOrdemInicial  , $obLabel, $obTxtCodOrdemFinal   ) );
    $obFormulario->addComponente( $obTxtCodBarrasOP                         );
    $obFormulario->addComponente( $obBscCGM                                 );

    $obFormulario->Ok();

    $obFormulario->show();
    include_once( $pgJs );
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
