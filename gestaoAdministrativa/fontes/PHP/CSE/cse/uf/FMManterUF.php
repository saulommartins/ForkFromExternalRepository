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
/*
* Arquivo de instância para manutenção de estados
* Data de Criação: 22/06/2007

* @author Analista     : Fabio Bertoldi
* @author Desenvolvedor: Rodrigo

Casos de uso: uc-01.07.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../classes/mapeamento/TPais.class.php';

$stPrograma = "ManterUF";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stLocation = $pgList."?".$sessao->id."&stAcao=".$_REQUEST['stAcao'].$stFiltro;

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( ""       );

$obPais = new TPais();
$rsPais = new RecordSet();
$stFiltro = "";
$obPais->recuperaTodos($rsPais,$stFiltro);

if ($stAcao=="incluir") {
    $obTxtPais = new TextBox;
    $obTxtPais->setName     ( "inCodPais"         );
    $obTxtPais->setInteiro  ( true                );
    $obTxtPais->setRotulo   ( "País"              );
    $obTxtPais->setTitle    ( "Selecione o País." );
    $obTxtPais->setSize     ( "4"                 );
    $obTxtPais->setMaxLength( "2"                 );
    $obTxtPais->setNull     ( false               );

    $obCmbPais = new Select;
    $obCmbPais->setName      ( 'stNomePais'    );
    $obCmbPais->setNull      ( false           );
    $obCmbPais->addOption    ( "", "Selecione" );
    $obCmbPais->setCampoId   ( 'cod_pais'      );
    $obCmbPais->setCampoDesc ( 'nom_pais'      );
    $obCmbPais->preencheCombo( $rsPais         );
} elseif ($stAcao=="alterar") {
    $obHdnCodUf = new Hidden;
    $obHdnCodUf->setName ( "inCodUf" );
    $obHdnCodUf->setValue( $_REQUEST['inCodUf'] );

    $obHdnCodPais = new Hidden;
    $obHdnCodPais->setName ( "inCodPais" );
    $obHdnCodPais->setValue( $_REQUEST['inCodPais'] );

    $obLblPais = new Label;
    $obLblPais->setRotulo( 'País:'                                              );
    $obLblPais->setId    ( 'lblNomePais'                                        );
    $obLblPais->setValue ( $_REQUEST['inCodPais']." - ".$_REQUEST['stNomePais'] );

    $obLblCodUf = new Label;
    $obLblCodUf->setRotulo( 'Código:'            );
    $obLblCodUf->setId    ( 'lblCodUf'           );
    $obLblCodUf->setValue ( $_REQUEST['inCodUf'] );
}

$obTxtNome = new TextBox;
$obTxtNome->setRotulo   ( "Nome"                  );
$obTxtNome->setTitle    ( "Informe o nome da UF." );
$obTxtNome->setName     ( "stNomeUf"              );
$obTxtNome->setNull     ( false                   );
$obTxtNome->setValue    ( $_REQUEST['stNome']     );
$obTxtNome->setMaxLength( 50                      );
$obTxtNome->setSize     ( 50                      );

$obTxtSigla = new TextBox;
$obTxtSigla->setRotulo   ( "Sigla"                          );
$obTxtSigla->setTitle    ( "Informe o nome da sigla da UF." );
$obTxtSigla->setName     ( "stSiglaUf"                      );
$obTxtSigla->setNull     ( false                            );
$obTxtSigla->setValue    ( $_REQUEST['stNomeSigla']         );
$obTxtSigla->setSize     ( 2                                );
$obTxtSigla->setMaxLength( 2                                );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm     );
$obFormulario->addHidden            ( $obHdnAcao  );
$obFormulario->addHidden            ( $obHdnCtrl  );
if ($stAcao=="incluir") {
    $obFormulario->addComponenteComposto($obTxtPais,$obCmbPais);
} else {
    $obFormulario->addHidden    ( $obHdnCodUf   );
    $obFormulario->addHidden    ( $obHdnCodPais );
    $obFormulario->addComponente( $obLblPais    );
    $obFormulario->addComponente( $obLblCodUf   );
}
$obFormulario->addComponente        ( $obTxtNome  );
$obFormulario->addComponente        ( $obTxtSigla );

if ($stAcao=="incluir") {
    $obFormulario->OK();
} else {
    $stLocation = $pgList.'?'.$sessao->id.'&stAcao='.$stAcao.'&pg='.$_REQUEST['pg'].'&pos='.$_REQUEST['pos'];
    $obFormulario->Cancelar( $stLocation );
}
$obFormulario->show();
?>
