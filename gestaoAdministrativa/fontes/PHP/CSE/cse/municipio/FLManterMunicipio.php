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
Arquivo de instância para manutenção de municipios
* Data de Criação: 25/06/2007

* @author Analista     : Fabio Bertoldi
* @author Desenvolvedor: Rodrigo

Casos de uso: uc-01.07.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../classes/mapeamento/TPais.class.php';
include_once '../../classes/mapeamento/TUf.class.php';

$stPrograma = "ManterMunicipio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stLocation = $pgList."?".$sessao->id."&stAcao=".$_REQUEST['stAcao'].$stFiltro;

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction( $pgList  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( ""       );

$obPais = new TPais();
$obUf   = new TUf();
$rsPais = new RecordSet();
$rsUf   = new RecordSet();
$stFiltro = "";
$obPais->recuperaTodos($rsPais,$stFiltro);

$stFiltro = "";
$obUf->recuperaTodos($rsUf,$stFiltro);

//Combo de Países
$obTxtPais = new TextBox;
$obTxtPais->setName     ( "inCodPais"         );
$obTxtPais->setInteiro  ( true                );
$obTxtPais->setRotulo   ( "País"              );
$obTxtPais->setTitle    ( "Selecione o País." );
$obTxtPais->setSize     ( "4"                 );
$obTxtPais->setMaxLength( "2"                 );
$obTxtPais->setNull     ( true                );

$obCmbPais = new Select;
$obCmbPais->setName      ( 'stNomePais'    );
$obCmbPais->setNull      ( true            );
$obCmbPais->addOption    ( "", "Selecione" );
$obCmbPais->setCampoId   ( 'cod_pais'      );
$obCmbPais->setCampoDesc ( 'nom_pais'      );
$obCmbPais->preencheCombo( $rsPais         );

//Combo de Estados
$obTxtUf = new TextBox;
$obTxtUf->setName     ( "inCodUf"         );
$obTxtUf->setInteiro  ( true              );
$obTxtUf->setRotulo   ( "UF"              );
$obTxtUf->setTitle    ( "Selecione a UF." );
$obTxtUf->setSize     ( "4"               );
$obTxtUf->setMaxLength( "2"               );
$obTxtUf->setNull     ( true              );

$obCmbUf = new Select;
$obCmbUf->setName      ( 'stNomeUf'      );
$obCmbUf->setNull      ( true            );
$obCmbUf->addOption    ( "", "Selecione" );
$obCmbUf->setCampoId   ( 'cod_uf'        );
$obCmbUf->setCampoDesc ( 'nom_uf'        );
$obCmbUf->preencheCombo( $rsUf           );

$obCodMunicipio = new TextBox;
$obCodMunicipio->setRotulo( "Código"                         );
$obCodMunicipio->setTitle ( "Informe o código do município." );
$obCodMunicipio->setName  ( "inCodMunicipio"                 );
$obCodMunicipio->setSize  ( 10                               );

$obTxtNome = new TextBox;
$obTxtNome->setRotulo( "Nome"                         );
$obTxtNome->setTitle ( "Informe o nome da município." );
$obTxtNome->setName  ( "stNomeMunicipio"              );
$obTxtNome->setSize  ( 20                             );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm               );
$obFormulario->addTitulo            ( "Dados para o Filtro" );
$obFormulario->addHidden            ( $obHdnAcao            );
$obFormulario->addHidden            ( $obHdnCtrl            );
$obFormulario->addComponenteComposto( $obTxtPais,$obCmbPais );
$obFormulario->addComponenteComposto( $obTxtUf,$obCmbUf     );
$obFormulario->addComponente        ( $obCodMunicipio       );
$obFormulario->addComponente        ( $obTxtNome            );

$obFormulario->OK();
$obFormulario->show();
?>
