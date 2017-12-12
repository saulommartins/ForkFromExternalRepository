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
* Arquivo de instância para manutenção de atributos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 24714 $
$Name$
$Author: domluc $
$Date: 2007-08-13 17:38:25 -0300 (Seg, 13 Ago 2007) $

Casos de uso: uc-01.03.96
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RAtributoDinamico.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterAtributo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgProx = $pgList;

include_once($pgJS);

Sessao::write('link','');

$obRAtributoDinamico = new RAtributoDinamico;
$obRAdministracaoMenu = new RAdministracaoMenu;

//if ( Sessao::read('numCgm') == '0' ) {
//    $obRAtributoDinamico->obRModulo->listar($rsModulo);
//} else {
//    $obRAtributoDinamico->obRModulo->listarModulosPorResponsavel($rsModulo);
//}

if ( Sessao::read('numCgm') == '0' ) {
    $obErro = $obRAdministracaoMenu->listarGestoesPorOrdem();
} else {
    $obErro = $obRAdministracaoMenu->listarGestoes();
}
//Monta o recordset para o preenchimento do combo de gestões
$rsGestao = new RecordSet;

if ( !$obErro->ocorreu() ) {
    $arGestao = Array();
    while ( !$obRAdministracaoMenu->rsRAdministracaoGestao->eof() ) {
        $obRGestao = $obRAdministracaoMenu->rsRAdministracaoGestao->getObjeto();
        $arTmpGestao = array( 'cod_gestao' => $obRGestao->getCodigoGestao(),
                              'nom_gestao' => $obRGestao->getNomeGestao() );
        $arGestao[] = $arTmpGestao;
        $obRAdministracaoMenu->rsRAdministracaoGestao->proximo();
    }
    $rsGestao->preenche( $arGestao );
}

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}
if ($stAcao == "incluir") {    }

if ($stAcao == "alterar") {    }

//alteração para a geração das funções pl de acesso a atributos
if ($stAcao == "gerar") {
    $pgProx = "FMGerarFuncaoAtributo.php";
}

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProx );
$obForm->setTarget( "telaPrincipal" ); //oculto - telaPrincipal

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obCmbGestao = new Select;
$obCmbGestao->setRotulo        ( "Gestão" );
$obCmbGestao->setName          ( "inCodGestao" );
$obCmbGestao->setValue         ( $inCodGestao );
$obCmbGestao->setStyle         ( "width: 200px");
$obCmbGestao->setCampoID       ( "cod_gestao" );
$obCmbGestao->setCampoDesc     ( "nom_gestao" );
$obCmbGestao->addOption        ( "", "Selecione" );
$obCmbGestao->setNull          ( false );
$obCmbGestao->preencheCombo    ( $rsGestao );
$obCmbGestao->obEvento->SetOnChange("goOculto('MontaModulo');");

$obCmbModulo = new Select;
$obCmbModulo->setRotulo        ( "Módulo" );
$obCmbModulo->setName          ( "inCodModulo" );
$obCmbModulo->setStyle         ( "width: 200px");
$obCmbModulo->setCampoID       ( "cod_modulo" );
$obCmbModulo->setCampoDesc     ( "nom_modulo" );
$obCmbModulo->addOption        ( "", "Selecione" );
$obCmbModulo->setValue         ( $inCodModulo );
$obCmbModulo->setNull          ( false );
//$obCmbModulo->preencheCombo    ( $rsModulo );
$obCmbModulo->obEvento->SetOnChange("buscaCadastro();");

$obCmbCadastro = new Select;
$obCmbCadastro->setRotulo        ( "Cadastro" );
$obCmbCadastro->setName          ( "inCodCadastro" );
$obCmbCadastro->setValue         ( $inCodCadastro );
$obCmbCadastro->setStyle         ( "width: 200px");
$obCmbCadastro->addOption         ( "", "Selecione" );
$obCmbCadastro->setNull          ( false );

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda("uc-01.03.96");
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addComponente( $obCmbGestao );
$obFormulario->addComponente( $obCmbModulo );
$obFormulario->addComponente( $obCmbCadastro );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
