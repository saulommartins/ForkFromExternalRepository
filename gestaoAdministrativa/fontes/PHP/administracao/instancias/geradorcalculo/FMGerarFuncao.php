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
* Arquivo de instância para manutenção de funções
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 29070 $
$Name$
$Author: rodrigosoares $
$Date: 2008-04-08 18:09:06 -0300 (Ter, 08 Abr 2008) $

Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RAtributoDinamico.class.php");
include_once(CAM_GA_ADM_NEGOCIO."RBiblioteca.class.php");
include_once(CAM_GA_ADM_NEGOCIO."RModulo.class.php");

$stPrograma = "GerarFuncao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
include_once( $pgJS );

$stAcao = $_REQUEST['stAcao'] ? $_REQUEST['stAcao']:"incluir";

$obRBiblioteca = new RBiblioteca( new RModulo );

if ($stAcao == "incluir") {
    $obRBiblioteca->roRModulo->listarModulosPorResponsavel( $rsModulo );
}

Sessao::write('obRBiblioteca', $obRBiblioteca);

$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obCmbModulo = new Select;
$obCmbModulo->setName      ( "inCodModulo"  );
$obCmbModulo->setRotulo    ( "Módulo"       );
$obCmbModulo->addOption    ( "","Selecione" );
$obCmbModulo->setCampoId   ( "cod_modulo"   );
$obCmbModulo->setCampoDesc ( "nom_modulo"   );
$obCmbModulo->preencheCombo( $rsModulo      );
$obCmbModulo->setNull      ( false          );
$obCmbModulo->setStyle     ( "width: 200px" );
$obCmbModulo->obEvento->setOnChange( "BuscaValores('buscaCadastro')" );

$obCmbCadastro = new Select;
$obCmbCadastro->setName      ( "inCodCadastro" );
$obCmbCadastro->setRotulo    ( "Cadastro"      );
$obCmbCadastro->setNull      ( false           );
$obCmbCadastro->addOption    ( "", "Selecione" );
$obCmbCadastro->setStyle     ( "width: 200px"  );
$obCmbCadastro->obEvento->setOnChange( "BuscaValores('buscaAtributos')" );

$obCmbBiblioteca = new Select;
$obCmbBiblioteca->setName      ( "inCodBiblioteca" );
$obCmbBiblioteca->setRotulo    ( "Biblioteca"      );
$obCmbBiblioteca->addOption    ( "","Selecione"    );
$obCmbBiblioteca->setCampoId   ( "cod_biblioteca"  );
$obCmbBiblioteca->setCampoDesc ( "nom_biblioteca"  );
$obCmbBiblioteca->setNull      ( false             );
$obCmbBiblioteca->setStyle     ( "width: 200px"    );

$obCmbCadastro = new Select;
$obCmbCadastro->setName      ( "inCodCadastro" );
$obCmbCadastro->setRotulo    ( "Cadastro"      );
$obCmbCadastro->setNull      ( false           );
$obCmbCadastro->addOption    ( "", "Selecione" );
$obCmbCadastro->setStyle     ( "width: 200px"  );
$obCmbCadastro->obEvento->setOnChange( "BuscaValores('buscaAtributos')" );

$obCmbAtributos = new SelectMultiplo;
$obCmbAtributos->SetNomeLista1( "arAtributoDisp"    );
$obCmbAtributos->setRotulo    ( "Atributo"          );
$obCmbAtributos->SetNomeLista1( "arAtributoSemFunc" );
$obCmbAtributos->setrecord1   ( new RecordSet       );
$obCmbAtributos->SetNomeLista2( "arAtributoComFunc" );
$obCmbAtributos->setrecord2   ( new RecordSet       );

$obOk  = new Ok;
$obOk->setId ("Ok");
$obOk->obEvento->setOnClick("Salvar();");

$obLimpar = new Button;
$obLimpar->setValue( "Limpar" );
$obLimpar->obEvento->setOnClick( "Cancelar('".$stLocation."','telaPrincipal');" );

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm          );
$obFormulario->addHidden     ( $obHdnCtrl       );
$obFormulario->addHidden     ( $obHdnAcao       );
$obFormulario->addTitulo     ( "Atributos Cadastrados"  );
$obFormulario->addForm       ( $obForm          );
$obFormulario->addComponente ( $obCmbModulo     );
$obFormulario->addComponente ( $obCmbCadastro   );
$obFormulario->addComponente ( $obCmbBiblioteca );
$obFormulario->addComponente ( $obCmbAtributos  );
$obFormulario->defineBarra( array( $obOk, $obLimpar ) );
$obFormulario->show();
?>
