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
  * Data de Criação: 04/09/2007

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Henrique Boaventura

  * @package URBEM
  * @subpackage

  $Id: FMManterGrupo.php 65901 2016-06-28 14:22:28Z michel $

  * Casos de uso: uc-03.01.04
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupo.class.php";
include_once CAM_GP_PAT_COMPONENTES."ISelectNatureza.class.php";

$stPrograma = "ManterGrupo";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//se a acao for alterar, recupera os dados da base
if ($stAcao == 'alterar') {
    $obTPatrimonioGrupo = new TPatrimonioGrupo();

    $stFiltro = "
          WHERE grupo.cod_natureza = ".$request->get('inCodNatureza')."
            AND grupo.cod_grupo    = ".$request->get('inCodGrupo');

    $obTPatrimonioGrupo->recuperaGrupo( $rsGrupo, $stFiltro );

    $inCodNatureza    = $rsGrupo->getCampo( 'cod_natureza' );
    $inCodPlano       = $rsGrupo->getCampo( 'cod_plano' );
    $stNomConta       = $rsGrupo->getCampo( 'nom_conta' );
    $stNomGrupo       = $rsGrupo->getCampo( 'nom_grupo' );
    $inCodPlanoDoacao = $rsGrupo->getCampo( 'cod_plano_doacao' );
    $stNomContaDoacao = $rsGrupo->getCampo( 'nom_conta_doacao' );
    $inCodPlanoPerdaInvoluntaria = $rsGrupo->getCampo( 'cod_plano_perda_involuntaria' );
    $stNomContaPerdaInvoluntaria = $rsGrupo->getCampo( 'nom_conta_perda' );
    $inCodPlanoTransferencia     = $rsGrupo->getCampo( 'cod_plano_transferencia' );
    $stNomContaTransferencia     = $rsGrupo->getCampo( 'nom_conta_transferencia' );
    $inCodPlanoDepreciacao  = $rsGrupo->getCampo( 'cod_plano_depreciacao' );
    $stNomContaDepreciacao  = $rsGrupo->getCampo( 'nom_conta_depreciacao' );
    $inCodPlanoVPAAlienacao = $rsGrupo->getCampo( 'cod_plano_alienacao_ganho' );
    $stNomContaVPAAlienacao = $rsGrupo->getCampo( 'nom_conta_alienacao_ganho' );
    $inCodPlanoVPDAlienacao = $rsGrupo->getCampo( 'cod_plano_alienacao_perda' );
    $stNomContaVPDAlienacao = $rsGrupo->getCampo( 'nom_conta_alienacao_perda' );
    $inCodPlanoReavaliacao  = $rsGrupo->getCampo( 'cod_plano_reavaliacao' );
    $stNomContaReavaliacao  = $rsGrupo->getCampo( 'nom_conta_reavaliacao' );

    (float) $inDepreciacao = $rsGrupo->getCampo( 'depreciacao' );

    $inDepreciacao = number_format($inDepreciacao,2,',','');

    //cria um objeto hidden para passar o valor do cod_grupo
    $obHdnCodGrupo = new Hidden();
    $obHdnCodGrupo->setName     ( 'inCodGrupo' );
    $obHdnCodGrupo->setValue    ( $rsGrupo->getCampo('cod_grupo') );

    //cria um objeto hidden para passar o valor da natureza
    $obHdnCodNatureza = new Hidden();
    $obHdnCodNatureza->setName    ( 'inCodNatureza' );
    $obHdnCodNatureza->setId      ( 'inCodNatureza' );
    $obHdnCodNatureza->setValue   ( $rsGrupo->getCampo('cod_natureza') );

    //cria um objeto hidden para passar o valor do codigo do plano de depreciação acumulada
    $obHdnCodPlanoDeprecicao = new Hidden();
    $obHdnCodPlanoDeprecicao->setName  ( 'inCodPlanoDepreciacao' );
    $obHdnCodPlanoDeprecicao->setValue ( $rsGrupo->getCampo( 'cod_plano_depreciacao' ) );

    //cria um label para demonstrar o nome da natureza
    $obLblNatureza = new Label();
    $obLblNatureza->setRotulo( 'Natureza' );
    $obLblNatureza->setValue( $rsGrupo->getCampo('cod_natureza').' - '.$rsGrupo->getCampo('nom_natureza') );

    //cria um label para demonstrar o código do grupo
    $obLblCodGrupo = new Label();
    $obLblCodGrupo->setRotulo( 'Código do Grupo' );
    $obLblCodGrupo->setValue( $rsGrupo->getCampo('cod_grupo') );
} else {
    $inCodNatureza         = "";
    $inCodPlano            = "";
    $stNomConta            = "";
    $stNomGrupo            = "";
    $inCodPlanoDepreciacao = "";
    $stNomContaDepreciacao = "";
    $stNomContaDoacao      = "";
    $inCodPlanoDoacao      = "";
    $stNomContaPerdaInvoluntaria = "";
    $inCodPlanoPerdaInvoluntaria = "";
    $stNomContaTransferencia = "";
    $inCodPlanoTransferencia = "";
    $stNomContaVPAAlienacao = "";
    $inCodPlanoVPAAlienacao = "";
    $stNomContaVPDAlienacao = "";
    $inCodPlanoVPDAlienacao = "";
    $inCodPlanoReavaliacao  = "";
    $stNomContaReavaliacao  = "";
}

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("oculto");

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

if ($stAcao != 'alterar') {
    $stOnChange  = "javascript: var stDisplay = 'none'; ";
    $stOnChange .= "if(jQuery('#inCodNatureza').val() == 1 || jQuery('#inCodNatureza').val() == 2) { stDisplay = ''; }";
    $stOnChange .= "jQuery('#spnReavaliacao').parent().parent('tr').css('display', stDisplay); ";
    $stOnChange .= "jQuery('#inCodContaReavaliacao').val(''); ";
    $stOnChange .= "jQuery('#stDescricaoReavaliacao').html('&nbsp;'); ";

    $jsOnLoad = $stOnChange;

    //instancia o componente ISelectNatureza
    $obISelectNatureza = new ISelectNatureza( $obForm );
    $obISelectNatureza->setValue( $inCodNatureza );
    $obISelectNatureza->obEvento->setOnChange($stOnChange);
}

//cria o textbox da descrição do grupo
$obTxtDescricaoGrupo = new TextBox();
$obTxtDescricaoGrupo->setId    ( 'stDescricaoGrupo' );
$obTxtDescricaoGrupo->setName  ( 'stDescricaoGrupo' );
$obTxtDescricaoGrupo->setRotulo( 'Descrição do Grupo' );
$obTxtDescricaoGrupo->setTitle ( 'Informe a descrição do grupo do bem.' );
$obTxtDescricaoGrupo->setSize  ( 50 );
$obTxtDescricaoGrupo->setMaxLength( 60 );
$obTxtDescricaoGrupo->setNull  ( false );
$obTxtDescricaoGrupo->setValue ( $stNomGrupo );

//cria um busca inner para retornar uma conta contábil
$obBscContaContabil = new BuscaInner;
$obBscContaContabil->setRotulo               ( "Conta Contábil do Bem"                       );
$obBscContaContabil->setTitle                ( "Informe a conta do plano de contas."     );
$obBscContaContabil->setId                   ( "stDescricaoConta"                        );
$obBscContaContabil->obCampoCod->setName     ( "inCodConta"                              );
$obBscContaContabil->obCampoCod->setSize     ( 10    );
$obBscContaContabil->obCampoCod->setAlign    ("left" );
$obBscContaContabil->setValoresBusca	     ( CAM_GF_CONT_POPUPS."planoConta/OCPlanoConta.php?".Sessao::getId(),$obForm->getName(),"contaSinteticaAtivoPermanente");
$obBscContaContabil->setFuncaoBusca 	     ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodConta','stDescricaoConta','contaSinteticaAtivoPermanente','".Sessao::getId()."','800','550');" );
$obBscContaContabil->setValue                ( $stNomConta );
$obBscContaContabil->obCampoCod->setValue    ( $inCodPlano );

$obBscContaContabilDoacao = new BuscaInner;
$obBscContaContabilDoacao->setRotulo               ( "Conta de VPD para Baixa por Doação"  );
$obBscContaContabilDoacao->setTitle                ( "Informe o Código do Plano de Contas para efetuar lançamento contábil de Baixa por Doação." );
$obBscContaContabilDoacao->setId                   ( "stDescricaoContaDoacao"              );
$obBscContaContabilDoacao->obCampoCod->setName     ( "inCodContaDoacao"                    );
$obBscContaContabilDoacao->obCampoCod->setSize     ( 10    );
$obBscContaContabilDoacao->obCampoCod->setAlign    ("left" );
$obBscContaContabilDoacao->setValoresBusca	       ( CAM_GF_CONT_POPUPS."planoConta/OCPlanoConta.php?".Sessao::getId(),$obForm->getName(),"contaContabilBaixaBem");
$obBscContaContabilDoacao->setFuncaoBusca 	       ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaDoacao','stDescricaoContaDoacao','contaContabilBaixaBem','".Sessao::getId()."','800','550');" );
$obBscContaContabilDoacao->setValue                ( $stNomContaDoacao );
$obBscContaContabilDoacao->obCampoCod->setValue    ( $inCodPlanoDoacao );

$obBscContaContabilPerdaInvoluntaria = new BuscaInner;
$obBscContaContabilPerdaInvoluntaria->setRotulo               ( " Conta de VPD para Baixa por Perda Involuntária"  );
$obBscContaContabilPerdaInvoluntaria->setTitle                ( "Informe o Código do Plano de Contas para efetuar lançamento contábil de Baixa por Perda Involuntária." );
$obBscContaContabilPerdaInvoluntaria->setId                   ( "stDescricaoPerdaInvoluntaria"                    );
$obBscContaContabilPerdaInvoluntaria->obCampoCod->setName     ( "inCodContaPerdaInvoluntaria"                     );
$obBscContaContabilPerdaInvoluntaria->obCampoCod->setSize     ( 10    );
$obBscContaContabilPerdaInvoluntaria->obCampoCod->setAlign    ("left" );
$obBscContaContabilPerdaInvoluntaria->setValoresBusca	      ( CAM_GF_CONT_POPUPS."planoConta/OCPlanoConta.php?".Sessao::getId(),$obForm->getName(),"contaContabilBaixaBem");
$obBscContaContabilPerdaInvoluntaria->setFuncaoBusca 	      ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaPerdaInvoluntaria','stDescricaoPerdaInvoluntaria','contaContabilBaixaBem','".Sessao::getId()."','800','550');" );
$obBscContaContabilPerdaInvoluntaria->setValue                ( $stNomContaPerdaInvoluntaria );
$obBscContaContabilPerdaInvoluntaria->obCampoCod->setValue    ( $inCodPlanoPerdaInvoluntaria );

$obBscContaContabilTransferencia = new BuscaInner;
$obBscContaContabilTransferencia->setRotulo               ( "Conta de VPD para Baixa por Transferência" );
$obBscContaContabilTransferencia->setTitle                ( "Informe o Código do Plano de Contas para efetuar lançamento contábil de Baixa por Transferência." );
$obBscContaContabilTransferencia->setId                   ( "stDescricaoTransferencia"                  );
$obBscContaContabilTransferencia->obCampoCod->setName     ( "inCodContaTransferencia"                   );
$obBscContaContabilTransferencia->obCampoCod->setSize     ( 10    );
$obBscContaContabilTransferencia->obCampoCod->setAlign    ("left" );
$obBscContaContabilTransferencia->setValoresBusca	  ( CAM_GF_CONT_POPUPS."planoConta/OCPlanoConta.php?".Sessao::getId(),$obForm->getName(),"contaContabilBaixaBem");
$obBscContaContabilTransferencia->setFuncaoBusca 	  ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaTransferencia','stDescricaoTransferencia','contaContabilBaixaBem','".Sessao::getId()."','800','550');" );
$obBscContaContabilTransferencia->setValue                ( $stNomContaTransferencia );
$obBscContaContabilTransferencia->obCampoCod->setValue    ( $inCodPlanoTransferencia ); 

//cria um busca inner para retornar uma Conta Contábil de Depreciação Acumulada
$obBscContaContabilDepreciacao = new BuscaInner;
$obBscContaContabilDepreciacao->setRotulo               ( "Conta Contábil de Depreciação Acumulada"     );
$obBscContaContabilDepreciacao->setTitle                ( "Informe a conta do plano de contas."         );
$obBscContaContabilDepreciacao->setId                   ( "stDescricaoContaDepreciacao"                 );
$obBscContaContabilDepreciacao->obCampoCod->setName     ( "inCodContaDepreciacao"                       );
$obBscContaContabilDepreciacao->obCampoCod->setSize     ( 10    );
$obBscContaContabilDepreciacao->obCampoCod->setAlign    ("left" );
$obBscContaContabilDepreciacao->setValoresBusca	        ( CAM_GF_CONT_POPUPS."planoConta/OCPlanoConta.php?".Sessao::getId(),$obForm->getName(),"contaContabilDepreciacaoAcumulada");
$obBscContaContabilDepreciacao->setFuncaoBusca 		( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaDepreciacao','stDescricaoContaDepreciacao','contaContabilDepreciacaoAcumulada','".Sessao::getId()."','800','550');" );
$obBscContaContabilDepreciacao->setNull			( true );
$obBscContaContabilDepreciacao->setValue                ( $stNomContaDepreciacao );
$obBscContaContabilDepreciacao->obCampoCod->setValue    ( $inCodPlanoDepreciacao );

//cria um busca inner para retornar uma Conta Contábil de Variação Patrimonial Aumentativa de Alienação
$obBscContaContabilVPAAlienacao = new BuscaInner;
$obBscContaContabilVPAAlienacao->setRotulo               ( "Conta de VPA para Alienação"           );
$obBscContaContabilVPAAlienacao->setTitle                ( "Informe a conta do plano de contas." );
$obBscContaContabilVPAAlienacao->setId                   ( "stDescricaoVPAAlienacao"             );
$obBscContaContabilVPAAlienacao->obCampoCod->setName     ( "inCodContaVPAAlienacao"              );
$obBscContaContabilVPAAlienacao->obCampoCod->setSize     ( 10     );
$obBscContaContabilVPAAlienacao->obCampoCod->setAlign    ( "left" );
$obBscContaContabilVPAAlienacao->setValoresBusca	     ( CAM_GF_CONT_POPUPS."planoConta/OCPlanoConta.php?".Sessao::getId()."&tipoConta=VPAAlienacao",$obForm->getName(),"contaContabilAlienacao");
$obBscContaContabilVPAAlienacao->setFuncaoBusca 		 ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaVPAAlienacao','stDescricaoVPAAlienacao','contaContabilAlienacao','".Sessao::getId()."','800','550');" );
$obBscContaContabilVPAAlienacao->setNull			     ( true );
$obBscContaContabilVPAAlienacao->setValue                ( $stNomContaVPAAlienacao );
$obBscContaContabilVPAAlienacao->obCampoCod->setValue    ( $inCodPlanoVPAAlienacao );

//cria um busca inner para retornar uma Conta Contábil de Variação Patrimonial Diminutiva de Alienação
$obBscContaContabilVPDAlienacao = new BuscaInner;
$obBscContaContabilVPDAlienacao->setRotulo               ( "Conta de VPD para Alienação"           );
$obBscContaContabilVPDAlienacao->setTitle                ( "Informe a conta do plano de contas." );
$obBscContaContabilVPDAlienacao->setId                   ( "stDescricaoVPDAlienacao"             );
$obBscContaContabilVPDAlienacao->obCampoCod->setName     ( "inCodContaVPDAlienacao"              );
$obBscContaContabilVPDAlienacao->obCampoCod->setSize     ( 10     );
$obBscContaContabilVPDAlienacao->obCampoCod->setAlign    ( "left" );
$obBscContaContabilVPDAlienacao->setValoresBusca	     ( CAM_GF_CONT_POPUPS."planoConta/OCPlanoConta.php?".Sessao::getId()."&tipoConta=VPDAlienacao",$obForm->getName(),"contaContabilAlienacao");
$obBscContaContabilVPDAlienacao->setFuncaoBusca 		 ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaVPDAlienacao','stDescricaoVPDAlienacao','contaContabilAlienacao','".Sessao::getId()."','800','550');" );
$obBscContaContabilVPDAlienacao->setNull			     ( true );
$obBscContaContabilVPDAlienacao->setValue                ( $stNomContaVPDAlienacao );
$obBscContaContabilVPDAlienacao->obCampoCod->setValue    ( $inCodPlanoVPDAlienacao );

//cria um busca inner para retornar uma Conta Contábil de Reavaliação
$obBscContaContabilReavaliacao = new BuscaInner;
$obBscContaContabilReavaliacao->setRotulo               ( "Conta Contábil de Reavaliação"         );
$obBscContaContabilReavaliacao->setTitle                ( "Informe a conta do plano de contas."   );
$obBscContaContabilReavaliacao->setId                   ( "stDescricaoReavaliacao"                );
$obBscContaContabilReavaliacao->obCampoCod->setName     ( "inCodContaReavaliacao"                 );
$obBscContaContabilReavaliacao->obCampoCod->setId       ( "inCodContaReavaliacao"                 );
$obBscContaContabilReavaliacao->obCampoCod->setSize     ( 10     );
$obBscContaContabilReavaliacao->obCampoCod->setAlign    ( "left" );
$obBscContaContabilReavaliacao->setValoresBusca         ( CAM_GF_CONT_POPUPS."planoConta/OCPlanoConta.php?".Sessao::getId(),$obForm->getName(),"contaContabilReavaliacao" );
$obBscContaContabilReavaliacao->setFuncaoBusca          ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaReavaliacao','stDescricaoReavaliacao','contaContabilReavaliacao','".Sessao::getId()."&tipoBusca2='+jQuery('#inCodNatureza').val(),'800','550');" );
$obBscContaContabilReavaliacao->setNull                 ( true );
$obBscContaContabilReavaliacao->setValue                ( $stNomContaReavaliacao );
$obBscContaContabilReavaliacao->obCampoCod->setValue    ( $inCodPlanoReavaliacao );

$obFormularioReavaliacao = new Formulario();
$obFormularioReavaliacao->addComponente( $obBscContaContabilReavaliacao );

$obFormularioReavaliacao->montaHTML();
$stHtmlReavaliacao = $obFormularioReavaliacao->getHTML();

$obSpnReavaliacao = new Span;
$obSpnReavaliacao->setId('spnReavaliacao');
$obSpnReavaliacao->setValue( $stHtmlReavaliacao );

//cria um numerico para o valor da depreciacao
$obIntDepreciacao = new Porcentagem();
$obIntDepreciacao->setId( 'inDepreciacao' );
$obIntDepreciacao->setName( 'inDepreciacao' );
$obIntDepreciacao->setRotulo( 'Quota Depreciação Anual' );
$obIntDepreciacao->setTitle( 'Selecione o percentual de depreciação.' );
$obIntDepreciacao->setValue( $inDepreciacao );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda     ('UC-03.01.04');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addTitulo    ( "Dados do Grupo" );

//inclui no formulario o objeto hidden que foi criado previamente
if ($stAcao == 'alterar') {
    $obFormulario->addHidden( $obHdnCodNatureza );
    $obFormulario->addHidden( $obHdnCodGrupo );
    $obFormulario->addHidden( $obHdnCodPlanoDeprecicao );
    $obFormulario->addComponente( $obLblNatureza );
    $obFormulario->addComponente( $obLblCodGrupo );
} else
    $obFormulario->addComponente( $obISelectNatureza );

$obFormulario->addComponente( $obTxtDescricaoGrupo );
$obFormulario->addComponente( $obBscContaContabil );
$obFormulario->addComponente( $obBscContaContabilDoacao );
$obFormulario->addComponente( $obBscContaContabilPerdaInvoluntaria );
$obFormulario->addComponente( $obBscContaContabilTransferencia );
$obFormulario->addComponente( $obBscContaContabilVPAAlienacao );
$obFormulario->addComponente( $obBscContaContabilVPDAlienacao );
if ( ($stAcao == 'alterar' && ($inCodNatureza == 1 || $inCodNatureza == 2)) || $stAcao == 'incluir' )
    $obFormulario->addSpan( $obSpnReavaliacao );
$obFormulario->addComponente( $obBscContaContabilDepreciacao);
$obFormulario->addComponente( $obIntDepreciacao );

if ($stAcao == 'alterar')
    $obFormulario->Cancelar($pgList.'?'.Sessao::getId().'&stAcao='.$stAcao."&pos=".Sessao::read('pos')."&pg=".Sessao::read('pg') );
else
    $obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>