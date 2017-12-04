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
    * Página de Filtro para Conciliação Bancária CC
    * Data de Criação   : 22/08/2014

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    * $Id: FLManterConciliacaoCC.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_TES_NEGOCIO."RTesourariaConciliacao.class.php";
include_once CAM_GT_MON_COMPONENTES."IPopUpContaCorrente.class.php";
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConciliacaoCC";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

/* Limpa os dados da sessao */
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
Sessao::remove('filtro');
Sessao::remove('filtroAux');

$rsEntidadesDisponiveis  = new recordSet;
$rsEntidadesSelecionadas = new recordSet;

$obRTesourariaConciliacao  = new RTesourariaConciliacao;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

$rsEntidadesDisponiveis = $rsEntidadesSelecionadas = new recordSet;
$stOrdem = " ORDER BY C.nom_cgm";

if ($stAcao == "") {
    $stAcao = "incluir";
}

$obRTesourariaConciliacao->obRContabilidadePlanoBanco->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRTesourariaConciliacao->obRContabilidadePlanoBanco->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRTesourariaConciliacao->obRContabilidadePlanoBanco->obROrcamentoEntidade->listarUsuariosEntidade($rsEntidadesDisponiveis, " ORDER BY cod_entidade");

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
$obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->listarBanco( $rsBanco );

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

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ("inCodigoEntidadesSelecionadas");
$obCmbEntidades->setRotulo ( "Entidade" );
$obCmbEntidades->setTitle  ( "Selecione a(s) Entidade(s)." );
$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidadesDisponiveis->getNumLinhas()==1) {
       $rsEntidadesSelecionadas = $rsEntidadesDisponiveis;
       $rsEntidadesDisponiveis = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodigoEntidadesDisponiveis');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidadesDisponiveis );

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodigoEntidadesSelecionadas');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsEntidadesSelecionadas );

$obMes = new Mes;
$obMes->setNull            ( false );
$obMes->setTitle           ( "Informe o mês de movimentação." );
$obMes->setPeriodo         ( true );
$obMes->setExercicio       ( Sessao::getExercicio() );

$stMsg = "A data do extrato deve ser no Mês informado!";
// Define objeto Data
$obTxtDtExtrato = new Data;
$obTxtDtExtrato->setName   ( "stDtExtrato"               );
$obTxtDtExtrato->setId     ( "stDtExtrato"               );
$obTxtDtExtrato->setRotulo ( "Data do Extrato"           );
$obTxtDtExtrato->setTitle  ( "Informe a Data do Extrato." );
$obTxtDtExtrato->setNull   ( false                       );
$obTxtDtExtrato->obEvento->setOnChange("if (document.frm.inMes.value!=this.value.substr(3,2)) {this.value='';alertaAviso('".$stMsg."','aviso','','".Sessao::getId()."');}");

// Define objeto Conta Corrente
$obContaCorrente = new IPopUpContaCorrente($obForm);
$obContaCorrente->setTitle  ( "Informe o Número da Conta Corrente." );
$obContaCorrente->setName   ( 'stNumeroConta'                       );
$obContaCorrente->setId     ( 'stNumeroConta'                       );
$obContaCorrente->obCampoCod->setName       ( 'inNumeroConta'       );
$obContaCorrente->obCampoCod->setId         ( 'inNumeroConta'       );
$obContaCorrente->obCampoCod->setInteiro    ( false                 );
$obContaCorrente->obCampoCod->setMaxLength  ( 20                    );
$obContaCorrente->obCampoCod->setValue      ( ""                    );
$obContaCorrente->setNull   ( true );

$arBancos = $rsBanco->getElementos();
foreach ($arBancos as $arBanco) {
    if ($arBanco['cod_banco'] != 0) {
        $arNewBancos[] = $arBanco;
    }
}
$rsBanco->setElementos( $arNewBancos );
$rsBanco->setNumLinhas( count( $arNewBancos ) );

$obTxtBanco = new TextBox;
$obTxtBanco->setName     ( "inNumBanco"        );
$obTxtBanco->setId       ( "inNumBanco"        );
$obTxtBanco->setValue    ( $_REQUEST['inNumBanco'] );
$obTxtBanco->setRotulo   ( "Banco"             );
$obTxtBanco->setMaxlength( 5                   );
$obTxtBanco->setTitle    ( "Selecione o Banco" );
$obTxtBanco->setDisabled ( $boDisabled         );
$obTxtBanco->setInteiro  ( true                );
$obTxtBanco->obEvento->setOnChange  ( " if(this.value != '') montaParametrosGET('MontaAgencia');
                                        else {
                                            document.getElementById('inCodBanco').value = '';
                                            document.getElementById('inCodAgencia').value = '';
                                            document.getElementById('stContaCorrente').value = '';
                                        }
                                    ");

$obHdnBanco = new Hidden;
$obHdnBanco->setName('inCodBanco');
$obHdnBanco->setId  ('inCodBanco');
$obHdnBanco->setValue ( $_REQUEST['inCodBanco'] );

$obCmbBanco = new Select;
$obCmbBanco->setName      ( "stNomeBanco"   );
$obCmbBanco->setId        ( "stNomeBanco"   );
$obCmbBanco->setValue     ( $_REQUEST['inNumBanco'] );
$obCmbBanco->setDisabled  ( $boDisabled     );
$obCmbBanco->addOption    ( "", "Selecione" );
$obCmbBanco->setCampoId   ( "num_banco"     );
$obCmbBanco->setCampoDesc ( "nom_banco"     );
$obCmbBanco->preencheCombo( $rsBanco        );
$obCmbBanco->setNull      (true);
$obCmbBanco->obEvento->setOnChange ( " montaParametrosGET('MontaAgencia'); " );

$obTxtAgencia = new TextBox;
$obTxtAgencia->setName     ( "inNumAgencia"        );
$obTxtAgencia->setId       ( "inNumAgencia"        );
$obTxtAgencia->setValue    ( $_REQUEST['inNumAgencia'] );
$obTxtAgencia->setRotulo   ( "Agência"            );
$obTxtAgencia->setMaxLength( 10                    );
$obTxtAgencia->setTitle    ( "Selecione a Agência" );
$obTxtAgencia->setDisabled ( $boDisabled           );
$obTxtAgencia->setNull(true);
$obTxtAgencia->obEvento->setOnChange ( " montaParametrosGET('MontaContaCorrente'); " );

$obHdnAgencia = new Hidden;
$obHdnAgencia->setName  ( 'inCodAgencia' );
$obHdnAgencia->setId    ( 'inCodAgencia' );
$obHdnAgencia->setValue ( $_REQUEST['inCodAgencia'] );

$obCmbAgencia = new Select;
$obCmbAgencia->setName      ( "stNomeAgencia"  );
$obCmbAgencia->setId        ( "stNomeAgencia"  );
$obCmbAgencia->setValue     ( $_REQUEST['inNumAgencia'] );
$obCmbAgencia->addOption    ( "", "Selecione"  );
$obCmbAgencia->setDisabled  ( $boDisabled      );
$obCmbAgencia->setNull      (true);
$obCmbAgencia->obEvento->setOnChange( " montaParametrosGET('MontaContaCorrente'); " );

$obHdnContaCorrente = new Hidden();
$obHdnContaCorrente->setName( 'inContaCorrente');
$obHdnContaCorrente->setId  ( 'inContaCorrente');
$obHdnContaCorrente->setValue( $_REQUEST['inContaCorrente'] );

$obCmbContaCorrente = new Select();
$obCmbContaCorrente->setRotulo      ( "Conta Corrente"  );
$obCmbContaCorrente->setTitle       ( "Selecione a Conta Corrente." );
$obCmbContaCorrente->setName        ( "inNumeroConta"   );
$obCmbContaCorrente->setId          ( "inNumeroConta"   );
$obCmbContaCorrente->setValue       ( $_REQUEST['stContaCorrente']  );
$obCmbContaCorrente->addOption      ( "", "Selecione"   );
$obCmbContaCorrente->setCampoId     ( "num_conta_corrente"     );
$obCmbContaCorrente->setCampoDesc   ( "num_conta_corrente"     );
$obCmbContaCorrente->setDisabled    ( $boDisabled       );
$obCmbContaCorrente->setNull        (true);
$obCmbContaCorrente->obEvento->setOnChange( " montaParametrosGET('BuscaContaCorrente'); " );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden        ( $obHdnCtrl            );
$obFormulario->addHidden        ( $obHdnAcao            );
$obFormulario->addHidden        ( $obHdnBanco           );
$obFormulario->addHidden        ( $obHdnAgencia         );
$obFormulario->addHidden        ( $obHdnContaCorrente   );

$obFormulario->addTitulo        ( "Dados para Conciliação Bancária" );
$obFormulario->addComponente    ( $obCmbEntidades       );
$obFormulario->addComponente    ( $obMes                );
//$obFormulario->addComponente    ( $obContaCorrente  );
$obFormulario->addComponenteComposto( $obTxtBanco  , $obCmbBanco    );
$obFormulario->addComponenteComposto( $obTxtAgencia, $obCmbAgencia  );
$obFormulario->addComponente    ( $obCmbContaCorrente   );
$obFormulario->addComponente    ( $obTxtDtExtrato       );

$obFormulario->Ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
