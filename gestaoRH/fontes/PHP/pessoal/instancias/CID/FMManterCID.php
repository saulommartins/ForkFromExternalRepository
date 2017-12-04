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
* Página de Formulario de Inclusao/Alteracao do CID
* Data de Criação: 04/01/2006

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30892 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

* Casos de uso: uc-04.04.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define a função do arquivo, ex: incluir ou alterar
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma = "ManterCID";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

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

//*****************************************
//* Define o filtro da lista
//*****************************************

//Define o filtro por sigla
$obHdnFiltroSigla = new Hidden;
$obHdnFiltroSigla->setName ( "stFiltroSigla" );
$obHdnFiltroSigla->setValue( $_GET['stFiltroSigla']  );

//Define o filtro por descrição
$obHdnFiltroDescricao = new Hidden;
$obHdnFiltroDescricao->setName ( "stFiltroDescricao" );
$obHdnFiltroDescricao->setValue( $_GET['stFiltroDescricao']  );

//****************************************//
// Define os campos da página
//****************************************//

//Define o codigo do conselho
$obHdnCodCID = new Hidden;
$obHdnCodCID->setName ( "inCodCID" );
$obHdnCodCID->setValue( $_GET['inCodCid'] );

//Define objeto TEXTBOX para armazenar a SIGLA do CID
$obTxtSigla = new TextBox;
$obTxtSigla->setRotulo        ( "Sigla"                      );
$obTxtSigla->setTitle         ( "Informe a sigla para o CID." );
$obTxtSigla->setName          ( "stSigla"                    );
$obTxtSigla->setId            ( "stSigla"                    );
$obTxtSigla->setValue         ( trim($_REQUEST["stSigla"])               );
$obTxtSigla->setSize          ( 5                            );
$obTxtSigla->setMaxLength     ( 5                            );
$obTxtSigla->setNull          ( false                        );
$obTxtSigla->setEspacosExtras ( false                        );

//Define objeto TEXTBOX para armazenar a DESCRICAO do CID
$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo        ( "Descrição"                      );
$obTxtDescricao->setTitle         ( "Informe a descrição para o CID." );
$obTxtDescricao->setName          ( "stDescricao"                    );
$obTxtDescricao->setId            ( "stDescricao"                    );
$obTxtDescricao->setValue         ( $_REQUEST["stDescricao"]                     );
$obTxtDescricao->setSize          ( 40                               );
$obTxtDescricao->setMaxLength     ( 80                               );
$obTxtDescricao->setNull          ( false                            );
$obTxtDescricao->setEspacosExtras ( false                            );

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalTipoDeficiencia.class.php");
$obTPessoalTipoDeficiencia = new TPessoalTipoDeficiencia();
$obTPessoalTipoDeficiencia->recuperaTodos($rsTipoDeficiencia);

$obTxtTipoDeficiencia= new TextBox;
$obTxtTipoDeficiencia->setRotulo        ( "Tipo Deficiência" );
$obTxtTipoDeficiencia->setTitle         ( "Selecione o código do tipo da deficiência." );
$obTxtTipoDeficiencia->setName          ( "inNumTipoDeficienciaTxt" );
$obTxtTipoDeficiencia->setId            ( "inNumTipoDeficienciaTxt" );
$obTxtTipoDeficiencia->setValue         ( trim($_REQUEST["inNumDeficiencia"])  );
$obTxtTipoDeficiencia->setInteiro(true);
$obTxtTipoDeficiencia->setSize          ( 10            );
$obTxtTipoDeficiencia->setNull(false);

$obCmbTipoDeficiencia = new Select;
$obCmbTipoDeficiencia->setRotulo           ( "Tipo Deficiência"  );
$obCmbTipoDeficiencia->setName             ( "inNumTipoDeficiencia"    );
$obCmbTipoDeficiencia->setValue            ( trim($_REQUEST["inNumDeficiencia"])     );
$obCmbTipoDeficiencia->setStyle            ( "width: 200px"   );
$obCmbTipoDeficiencia->setCampoID          ( "num_deficiencia" );
$obCmbTipoDeficiencia->setCampoDesc        ( "descricao"      );
$obCmbTipoDeficiencia->addOption           ( "", "Selecione"  );
$obCmbTipoDeficiencia->preencheCombo       ( $rsTipoDeficiencia );
$obCmbTipoDeficiencia->setNull(false);

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden     ( $obHdnCtrl            );
$obFormulario->addHidden     ( $obHdnAcao            );
$obFormulario->addHidden     ( $obHdnFiltroSigla     );
$obFormulario->addHidden     ( $obHdnFiltroDescricao );
$obFormulario->addHidden     ( $obHdnCodCID          );
$obFormulario->addTitulo     ( "Dados da Sequência"  );
$obFormulario->addComponente ( $obTxtSigla           );
$obFormulario->addComponente ( $obTxtDescricao       );
$obFormulario->addComponenteComposto($obTxtTipoDeficiencia,$obCmbTipoDeficiencia);

if ( $stAcao == "incluir" )
    $obFormulario->OK();
else
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro.'&stFiltroSigla='.$_GET['stFiltroSigla'].'&stFiltroDescricao='.$_GET['stFiltroDescricao'] );

$obFormulario->setFormFocus($obTxtSigla->getId() );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
