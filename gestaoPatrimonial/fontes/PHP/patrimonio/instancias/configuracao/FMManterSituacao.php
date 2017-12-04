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

    * Casos de uso: uc-03.01.10
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
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioSituacaoBem.class.php");

$stPrograma = "ManterSituacao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//se a acao for alterar, recupera os dados da base
if ($stAcao == 'alterar') {
    $obTPatrimonioSituacaoBem = new TPatrimonioSituacaoBem();
    $obTPatrimonioSituacaoBem->setDado( 'cod_situacao', $_REQUEST['inCodSituacao'] );
    $obTPatrimonioSituacaoBem->recuperaSituacaoBem( $rsSituacaoBem );

    $stNomSituacaoBem = $rsSituacaoBem->getCampo( 'nom_situacao' );

    //cria um objeto hidden para passar o valor do cod_natureza
    $obHdnCodSituacaoBem = new Hidden();
    $obHdnCodSituacaoBem->setName 	  ( 'inCodSituacao' );
    $obHdnCodSituacaoBem->setValue    ( $rsSituacaoBem->getCampo('cod_situacao') );

    //cria um label para demonstra o codigo da situação
    $obLblCodSituacao = new Label();
    $obLblCodSituacao->setRotulo( 'Código da Situação' );
    $obLblCodSituacao->setValue( $rsSituacaoBem->getCampo('cod_situacao') );

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

//cria o textbox da descrição da situacao do bom
$obTxtDescricaoSituacaoBem = new TextBox();
$obTxtDescricaoSituacaoBem->setId    ( 'stDescricaoSituacaoBem' );
$obTxtDescricaoSituacaoBem->setName  ( 'stDescricaoSituacaoBem' );
$obTxtDescricaoSituacaoBem->setRotulo( 'Descrição da Situação' );
$obTxtDescricaoSituacaoBem->setTitle ( 'Informe a descrição da situação do bem.' );
$obTxtDescricaoSituacaoBem->setSize  ( 50 );
$obTxtDescricaoSituacaoBem->setMaxLength( 60 );
$obTxtDescricaoSituacaoBem->setNull  ( false );
$obTxtDescricaoSituacaoBem->setValue ( $stNomSituacaoBem );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda     ('UC-03.01.10');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addTitulo    ( "Dados da Situação do Bem" );
//inclui no formulario o objeto hidden que foi criado previamente
if ($stAcao == 'alterar') {
    $obFormulario->addHidden( $obHdnCodSituacaoBem );
    $obFormulario->addComponente( $obLblCodSituacao );
}
$obFormulario->addComponente( $obTxtDescricaoSituacaoBem );
if ($stAcao == 'alterar') {
    $obFormulario->Cancelar($pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
} else {
    $obFormulario->OK();
}
$obFormulario->show();
