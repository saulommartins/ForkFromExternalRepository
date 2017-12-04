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
    * Página de Formulario de Inclusao/Alteracao de Fornecedores
    * Data de Criação   : 13/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-05-21 16:04:19 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-02.01.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"  );
include_once CAM_GA_CGM_COMPONENTES.'IPopUpCGM.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "UnidadeOrcamentaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRUnidadeOrcamentaria  = new ROrcamentoUnidadeOrcamentaria;
$rsOrgao = $rsUnidade = new RecordSet;

$obRUnidadeOrcamentaria->obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
$obRUnidadeOrcamentaria->buscarMascara();
$stMascara = $obRUnidadeOrcamentaria->getMascara();

$rsUnidadeOrcamento = new RecordSet();

if ($stAcao == 'alterar') {
    $obRUnidadeOrcamentaria->setNumeroUnidade( $_GET['inNumeroUnidade']                   );
    $obRUnidadeOrcamentaria->setExercicio    ( $_GET['stExercicio']                       );
    $obRUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $_GET['inNumeroOrgao'] );
    $obRUnidadeOrcamentaria->consultar( $rsUnidadeOrcamento );

    $stOrgaoOrcamento = $rsUnidadeOrcamento->getCampo('num_orgao')." - ".$rsUnidadeOrcamento->getCampo('nom_orgao');
    $inNumeroUnidade  = $rsUnidadeOrcamento->getCampo('num_unidade');
    $inCodUnidade     = $rsUnidadeOrcamento->getCampo('cod_unidade')."-".$rsUnidadeOrcamento->getCampo('exercicio_unidade');
    $inCodOrgao       = $rsUnidadeOrcamento->getCampo('num_orgao')."-".$rsUnidadeOrcamento->getCampo('exercicio');
    $inCodResponsavel = $rsUnidadeOrcamento->getCampo('usuario_responsavel');
    $stNomeResponsavel = $rsUnidadeOrcamento->getCampo('nome_usuario');
    $stNomeUnidade = $rsUnidadeOrcamento->getCampo('nom_unidade');
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnUnidade = new Hidden;
$obHdnUnidade->setName ("inNumeroUnidade");
$obHdnUnidade->setValue($inNumeroUnidade );

$obHdnOrgao = new Hidden;
$obHdnOrgao->setName ( "inCodOrgao" );
$obHdnOrgao->setValue( $inCodOrgao  );

//Define o objeto TEXT para armazenar o NUMERO DO ORGAO NO ORCAMENTO
$obTxtNomUnidade = new TextBox;
$obTxtNomUnidade->setName     ( "stNomeUnidade"   );
$obTxtNomUnidade->setValue    ( $stNomeUnidade    );
$obTxtNomUnidade->setRotulo   ( "Nome da Unidade" );
$obTxtNomUnidade->setNull     ( false );
$obTxtNomUnidade->setTitle    ( "Informe o nome da unidade." );
$obTxtNomUnidade->setSize     ( 60 );
$obTxtNomUnidade->setMaxLength( 60 );

//Define o objeto TEXT para armazenar o NUMERO DO ORGAO NO ORCAMENTO
$obTxtNumUnidade = new TextBox;
$obTxtNumUnidade->setName     ( "inNumeroUnidade"   );
$obTxtNumUnidade->setValue    ( $inNumeroUnidade    );
$obTxtNumUnidade->setRotulo   ( "Número da Unidade" );
$obTxtNumUnidade->setNull     ( false );
$obTxtNumUnidade->setTitle    ( "Informe o número da unidade." );
$obTxtNumUnidade->setInteiro  ( true );
$obTxtNumUnidade->setSize     ( strlen($stMascara) );
$obTxtNumUnidade->setMaxLength( strlen($stMascara) );
$obTxtNumUnidade->obEvento->setOnKeyUp("mascaraDinamico('".$stMascara."', this, event);");

//Define o objeto COMBO para armazenar o COD ORGAO
$stOrder = " ORDER BY OO.num_orgao, OO.nom_orgao";
$obRUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao, $stOrder );

$obCmbOrgao = new Select;
$obCmbOrgao->setName      ( "inCodOrgao" );
$obCmbOrgao->setRotulo    ( "Órgão no Orçamento" );
$obCmbOrgao->setTitle     ( "Selecione o órgão no orçamento." );
$obCmbOrgao->addOption    ( "", "Selecione" );
$obCmbOrgao->setCampoId   ( "[num_orgao]-[exercicio]" );
$obCmbOrgao->setCampoDesc ( "[num_orgao] - [nom_orgao]" );
$obCmbOrgao->preencheCombo( $rsOrgao );
$obCmbOrgao->setNull      ( false );
$obCmbOrgao->setStyle     ( "width: 400px" );

if ($stAcao == 'alterar') {
    $obLblNumUnidade = new Label;
    $obLblNumUnidade->setRotulo( "Número da Unidade" );
    $obLblNumUnidade->setName  ( "inNumeroUnidade"   );
    $obLblNumUnidade->setValue ( $inNumeroUnidade    );

    $obLblOrgao = new Label;
    $obLblOrgao->setRotulo( "Órgão no Orçamento" );
    $obLblOrgao->setName  ( "stOrgaoOrcamento"   );
    $obLblOrgao->setValue ( $stOrgaoOrcamento    );

}

//Campo Responsável
$obPopUpResponsavel = new IPopUpCGM( $obForm );
$obPopUpResponsavel->setNull     ( false              );
$obPopUpResponsavel->setRotulo   ( "Responsável"      );
$obPopUpResponsavel->setTitle    ( "Informe o responsável" );
$obPopUpResponsavel->setTipo     ( "usuario"           );
$obPopUpResponsavel->setValue    ( $stNomeResponsavel );
$obPopUpResponsavel->obCampoCod->setValue    ( $inCodResponsavel );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->setAjuda ( "UC-02.01.02" );
$obFormulario->addForm  ( $obForm       );
$obFormulario->addHidden( $obHdnCtrl    );
$obFormulario->addHidden( $obHdnAcao    );

$obFormulario->addTitulo( "Dados para Unidade Orçamentária" );
if ($stAcao == 'alterar') {
    $obFormulario->addHidden    ( $obHdnUnidade             );
} else {
    $obFormulario->addComponente( $obTxtNumUnidade          );
    $obFormulario->addComponente( $obTxtNomUnidade          );
    $obFormulario->addComponente( $obPopUpResponsavel       );
}
if ($stAcao == 'alterar') {
    $obFormulario->addComponente( $obLblNumUnidade          );
    $obFormulario->addComponente( $obLblOrgao               );
    $obFormulario->addHidden    ( $obHdnOrgao               );
    $obFormulario->addComponente( $obTxtNomUnidade          );
    $obFormulario->addComponente( $obPopUpResponsavel       );

} else {
    $obFormulario->addComponente( $obCmbOrgao               );
}
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
