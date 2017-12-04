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
    * Formulário para funcionalidade Definir contas para Inscrição de RP
    * Data de Criação   : 21/02/2006

    * @author Analista:
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    * $Id: FMDefinirContasRP.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php";
include_once CAM_GF_CONT_COMPONENTES.'IPopUpContaAnalitica.class.php';
include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeTipoContaLancamentoRp.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "DefinirContasRP";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgOcul );

Sessao::write('arContasCredito', '');
Sessao::write('arContasDebito', '');

Sessao::write('codigoCredito', 0);
Sessao::write('codigoDebito' , 0);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

/////****************************  Contas a Crédito              ***************/////////////////

//Define objeto de select multiplo de entidade por usuários
$obISelectEntidadeUsuarioCredito = new ITextBoxSelectEntidadeGeral ();
$obISelectEntidadeUsuarioCredito->obTextBox->setId   ( "inCodEntidadeCredito" );
$obISelectEntidadeUsuarioCredito->obTextBox->setName ( "inCodEntidadeCredito" );
$obISelectEntidadeUsuarioCredito->obSelect->setName  ( "stNomEntidadeCredito" );
$obISelectEntidadeUsuarioCredito->obSelect->setId    ( "stNomEntidadeCredito" );
$obISelectEntidadeUsuarioCredito->obTextBox->setNull ( true                   );
$obISelectEntidadeUsuarioCredito->obSelect->setNull  ( true                   );
$obISelectEntidadeUsuarioCredito->setNull ( true );
$obISelectEntidadeUsuarioCredito->setObrigatorioBarra ( true );

// busca de conta para não processados
$obPopUpContaPagarNaoProcessados = new IPopUpContaAnalitica ( $obISelectEntidadeUsuarioCredito->obSelect  );
$obPopUpContaPagarNaoProcessados->setID              ( 'innerContaPagarNaoProcessados'       );
$obPopUpContaPagarNaoProcessados->setName            ( 'innerContaPagarNaoProcessados'       );
$obPopUpContaPagarNaoProcessados->obCampoCod->setName( "inCodContaCredito_tipo_6"            );
$obPopUpContaPagarNaoProcessados->obCampoCod->setId  ( "inCodContaCredito_tipo_6"            );
$obPopUpContaPagarNaoProcessados->setRotulo          ( 'Restos a Pagar Não Processados '.Sessao::getExercicio() );
$obPopUpContaPagarNaoProcessados->setTipoBusca       ( 'con_conta_lancamento_rp_credito'     );
$obPopUpContaPagarNaoProcessados->setObrigatorioBarra ( true );

/// span para listagem de contas 5 e 6 das entidades

$obSpanContasCredito = new Span ;
$obSpanContasCredito->setId ( 'spnContasCredito' );

////////////////////////////////////////////

////////////************* Contas a Débito ************************////////////////////////

//Define objeto de select multiplo de entidade por usuários
$obISelectEntidadeUsuarioDebito = new ITextBoxSelectEntidadeGeral();
$obISelectEntidadeUsuarioDebito->obTextBox->setNull ( true                   );
$obISelectEntidadeUsuarioDebito->obSelect->setNull  ( true                   );
$obISelectEntidadeUsuarioDebito->setNull ( true );

$rsTipoConta = new RecordSet;
$obTContabilidadeTipoContaLancamentoRp   = new TContabilidadeTipoContaLancamentoRp;
$obTContabilidadeTipoContaLancamentoRp->recuperaTodos ( $rsTipoConta  ," WHERE cod_tipo_conta >= 6" );

$obSelTipoCredorProcessado = new Select;
$obSelTipoCredorProcessado->setRotulo     ( 'Tipo Credor Para Restos a Pagar Processados '.Sessao::getExercicio() );
$obSelTipoCredorProcessado->setName       ( 'inCodTipoProcessado'        );
$obSelTipoCredorProcessado->setStyle      ( "width: 200px"     );
$obSelTipoCredorProcessado->setCampoID    ( 'cod_tipo_conta'   );
$obSelTipoCredorProcessado->setCampoDesc  ( 'descricao'        );
$obSelTipoCredorProcessado->addOption     ( '', 'Selecione'    );
//$obSelTipoCredorProcessado->setNullBarra  ( false              );
$obSelTipoCredorProcessado->preencheCombo ( $rsTipoConta       );

// busca de conta para processados
$obPopUpContaPagarProcessados = new IPopUpContaAnalitica ($obISelectEntidadeUsuarioDebito->obSelect   );
$obPopUpContaPagarProcessados->setID              ( 'innerContaPagarProcessados'           );
$obPopUpContaPagarProcessados->setName            ( 'innerContaPagarProcessados'           );
$obPopUpContaPagarProcessados->obCampoCod->setName( "inCodContaCredito_tipo_7"             );
$obPopUpContaPagarProcessados->obCampoCod->setID  ( "inCodContaCredito_tipo_7"             );
$obPopUpContaPagarProcessados->setRotulo          ( 'Restos a Pagar Processados '.Sessao::getExercicio() );
$obPopUpContaPagarProcessados->setTipoBusca       ( 'con_conta_lancamento_rp_credito'      );

/// tipo do credor
$obTContabilidadeTipoContaLancamentoRp->recuperaTodos ( $rsTipoConta ," WHERE cod_tipo_conta < 6 and cod_tipo_conta > 0 " );

$obSelTipoCredor = new Select;
$obSelTipoCredor->setRotulo              ( 'Tipo Credor'      );
$obSelTipoCredor->setName                ( 'inCodTipo'  );
$obSelTipoCredor->setStyle               ( "width: 200px"     );
$obSelTipoCredor->setCampoID             ( 'cod_tipo_conta'   );
$obSelTipoCredor->setCampoDesc           ( 'descricao'        );
$obSelTipoCredor->addOption              ( '', 'Selecione'    );
//$obSelTipoCredor->setNullBarra           ( false              );
$obSelTipoCredor->preencheCombo          ( $rsTipoConta     );

// busca de conta para processados
$obPopUpConta = new IPopUpContaAnalitica (  );
$obPopUpConta->setID              ( 'innerConta'                     );
$obPopUpConta->setName            ( 'innerConta'                     );
$obPopUpConta->obCampoCod->setName( "inCodContas"                    );
$obPopUpConta->setRotulo          ( 'Selecione a conta'              );
$obPopUpConta->setTipoBusca       ( 'con_conta_lancamento_rp_debito' );
$obPopUpConta->setNull            ( true                           );

$obSpanContasDebito = new Span;
$obSpanContasDebito->setId ( 'spnContasDebito' );

///////////////**************************//////////////////////////////

////// montagem do formulário

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

$obFormulario = new Formulario();
$obFormulario->addForm       ( $obForm                                                );
$obFormulario->addHidden     ( $obHdnAcao                                             );
$obFormulario->addHidden     ( $obHdnCtrl                                             );
$obFormulario->addTitulo     ( 'Definição de Contas para Incrição de Restos a Pagar.' );
$obFormulario->addComponente ( $obISelectEntidadeUsuarioCredito                       );

$obFormulario->addComponente ( $obPopUpContaPagarNaoProcessados   );
$obFormulario->Incluir        ('ContaCredito', array( $obISelectEntidadeUsuarioCredito->obTextBox,
                                                      $obISelectEntidadeUsuarioCredito->obSelect,
                                                      $obPopUpContaPagarNaoProcessados) );

$obFormulario->addSpan       ( $obSpanContasCredito               );

$obFormulario->addTitulo     ( 'Contas a Débito'               );
$obFormulario->addComponente ( $obISelectEntidadeUsuarioDebito );
$obFormulario->addComponente ( $obSelTipoCredorProcessado     );
$obFormulario->addComponente ( $obPopUpContaPagarProcessados   );
$obFormulario->addComponente ( $obSelTipoCredor                );
$obFormulario->addComponente ( $obPopUpConta                   );
$obFormulario->Incluir       ('ContaDebito',array( $obISelectEntidadeUsuarioDebito->obTextBox,
                                                   $obISelectEntidadeUsuarioDebito->obSelect,
                                                   $obSelTipoCredor,
                                                   $obPopUpConta,
                                                   $obSelTipoCredorProcessado,
                                                   $obPopUpContaPagarProcessados ) );
$obFormulario->addSpan ( $obSpanContasDebito );

$obOk = new Ok();
$obLimpar = new Limpar();
$obLimpar->obEvento->setOnClick("executaFuncaoAjax('limpar');");
$obFormulario->defineBarra(array($obOk, $obLimpar));
$obFormulario->show();

//// mostrando contas já cadastradas
buscaContas();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
