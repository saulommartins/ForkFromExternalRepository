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

    $Revision: 26727 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-11-12 16:31:31 -0200 (Seg, 12 Nov 2007) $

    * Casos de uso: uc-03.01.03
*/

/*
$Log$
Revision 1.3  2007/10/17 13:27:12  hboaventura
correção dos arquivos

Revision 1.2  2007/09/18 15:36:50  hboaventura
Adicionando ao repositório

Revision 1.1  2007/09/18 15:11:11  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_PAT_MAPEAMENTO."TPatrimonioNatureza.class.php");
include_once ( CAM_GP_PAT_MAPEAMENTO."TPatrimonioTipoNatureza.class.php");

$stPrograma = "ManterNatureza";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//se a acao for alterar, recupera os dados da base
if ($stAcao == 'alterar') {
    $obTPatrimonioNatureza = new TPatrimonioNatureza();

    $obTPatrimonioNatureza->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
    $obTPatrimonioNatureza->recuperaPorChave( $rsNatureza );
    
    $inTipoNatureza = $rsNatureza->getCampo( 'cod_tipo' );
    $stNomNatureza  = $rsNatureza->getCampo( 'nom_natureza' );
    
    //cria um objeto hidden para passar o valor do cod_natureza
    $obHdnCodNatureza = new Hidden();
    $obHdnCodNatureza->setName 	  ( 'inCodNatureza' );
    $obHdnCodNatureza->setValue    ( $rsNatureza->getCampo('cod_natureza') );

    $obLblCodNatureza = new Label();
    $obLblCodNatureza->setRotulo( 'Código da Natureza' );
    $obLblCodNatureza->setValue( $rsNatureza->getCampo('cod_natureza') );

}else{
  $inTipoNatureza = "";
  $stNomNatureza  = "";  
}

$obTPatrimonioTipoNatureza = new TPatrimonioTipoNatureza();
$obTPatrimonioTipoNatureza->recuperaTodos($rsTipoNatureza , " WHERE codigo > 0 " ," ORDER BY codigo " );

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

//cria o textbox da descrição da natureza
$obTxtDescricaoNatureza = new TextBox();
$obTxtDescricaoNatureza->setId    ( 'stDescricaoNatureza' );
$obTxtDescricaoNatureza->setName  ( 'stDescricaoNatureza' );
$obTxtDescricaoNatureza->setRotulo( 'Descrição da Natureza' );
$obTxtDescricaoNatureza->setTitle ( 'Informe a descrição da natureza do bem.' );
$obTxtDescricaoNatureza->setSize  ( 50 );
$obTxtDescricaoNatureza->setMaxLength( 60 );
$obTxtDescricaoNatureza->setNull  ( false );
$obTxtDescricaoNatureza->setValue ( $stNomNatureza );

//select com os tipos de natureza
$obCmbTipoNatureza = new Select();
$obCmbTipoNatureza->setRotulo       ( "Tipo da Natureza"            );
$obCmbTipoNatureza->setName         ( "inTipoNtureza"               );
$obCmbTipoNatureza->setId           ( "inTipoNtureza"               );
$obCmbTipoNatureza->setTitle        ( 'Informe o Tipo da Natureza.' );
$obCmbTipoNatureza->setNull         ( false                         );
$obCmbTipoNatureza->setValue        ( $inTipoNatureza               );
$obCmbTipoNatureza->addOption       ( "", "Selecione"               );
$obCmbTipoNatureza->setCampoID      ( "codigo"                      );
$obCmbTipoNatureza->setCampoDesc    ( "[codigo] - [descricao]"      );
$obCmbTipoNatureza->preencheCombo   ( $rsTipoNatureza               );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda     ('UC-03.01.03');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
//inclui no formulario o objeto hidden que foi criado previamente
$obFormulario->addTitulo    ( "Dados da Natureza" );
if ($stAcao == 'alterar') {
    $obFormulario->addHidden( $obHdnCodNatureza );
    $obFormulario->addComponente( $obLblCodNatureza );
}
$obFormulario->addComponente( $obTxtDescricaoNatureza );
$obFormulario->addComponente( $obCmbTipoNatureza );
if ($stAcao == 'alterar') {
    $obFormulario->Cancelar($pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
} else {
    $obFormulario->OK();
}
$obFormulario->show();
