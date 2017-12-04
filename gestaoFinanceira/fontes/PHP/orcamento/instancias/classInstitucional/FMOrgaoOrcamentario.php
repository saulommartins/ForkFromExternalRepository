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
include_once CAM_GF_INCLUDE . 'validaGF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO . 'ROrcamentoOrgaoOrcamentario.class.php';
include_once CAM_GF_ORC_NEGOCIO . 'ROrcamentoConfiguracao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "OrgaoOrcamentario";
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

$obROrgaoOrcamentario = new ROrcamentoOrgaoOrcamentario;
$rsOrgao              = new RecordSet;

$obROrgaoOrcamentario->obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
$obROrgaoOrcamentario->buscarMascara();
$stMascara = $obROrgaoOrcamentario->getMascara();

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

//Define o objeto TEXT para armazenar o NUMERO DO ORGAO NO ORCAMENTO
$obTxtNumOrgao = new TextBox;
$obTxtNumOrgao->setName     ( "inNumeroOrgao" );
$obTxtNumOrgao->setValue    ( $inNumeroOrgao );
$obTxtNumOrgao->setRotulo   ( "Número do Órgão no Orçamento" );
$obTxtNumOrgao->setNull     ( false );
$obTxtNumOrgao->setTitle    ( 'Informe o número do órgão no orçamento.' );
$obTxtNumOrgao->setInteiro  ( true );
$obTxtNumOrgao->setSize     ( strlen($stMascara) );
$obTxtNumOrgao->setMaxLength( strlen($stMascara) );
$obTxtNumOrgao->obEvento->setOnKeyUp("mascaraDinamico('".$stMascara."', this, event);");

//Define o objeto TEXT para armazenar o NUMERO DO ORGAO NO ORCAMENTO
$obTxtNomOrgao = new TextBox;
$obTxtNomOrgao->setName     ( "stNomeOrgao"   );
$obTxtNomOrgao->setValue    ( $stNomeOrgao    );
$obTxtNomOrgao->setRotulo   ( "Nome do Órgão" );
$obTxtNomOrgao->setNull     ( false );
$obTxtNomOrgao->setTitle    ( "Informe o nome do órgão." );
$obTxtNomOrgao->setSize     ( 60 );
$obTxtNomOrgao->setMaxLength( 60 );

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
$obFormulario->addForm      ( $obForm                         );
$obFormulario->setAjuda     ( "UC-02.01.02"                   );
$obFormulario->addHidden    ( $obHdnCtrl                      );
$obFormulario->addHidden    ( $obHdnAcao                      );
$obFormulario->addTitulo    ( "Dados para Órgão Orçamentário" );
$obFormulario->addComponente( $obTxtNumOrgao                  );
$obFormulario->addComponente( $obTxtNomOrgao                  );
$obFormulario->addComponente( $obPopUpResponsavel             );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
