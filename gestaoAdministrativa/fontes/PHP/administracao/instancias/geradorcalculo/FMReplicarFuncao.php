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
    * Página de Formulário de Replicar Função
    * Data de Criação: 19/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    * Casos de uso: uc-01.03.95

    $Id: FMReplicarFuncao.php 63893 2015-11-03 16:32:58Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModulo.class.php"                                   );

//Define o nome dos arquivos PHP
$stPrograma = "ReplicarFuncao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

Sessao::write('link','');
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

include_once($pgJS);

// Utilizado para validar o nome da nova função.
include_once 'JSManterFuncao.js';

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnEval =  new HiddenEval;
$obHdnEval->setName  ( "stEval" );
$obHdnEval->setId    ( "stEval" );
$obHdnEval->setValue ( $stEval  );

$obTAdministracaoModulo = new TModulo;
$obTAdministracaoModulo->recuperaTodos($rsModulos,"","nom_modulo");

$obCmbModulo = new Select();
$obCmbModulo->setRotulo             ( "Módulo Origem"     );
$obCmbModulo->setName               ( "inCodModulo"       );
$obCmbModulo->setId                 ( "inCodModulo"       );
$obCmbModulo->setTitle              ( "Informe o módulo." );
$obCmbModulo->setStyle              ( "width: 250px"      );
$obCmbModulo->setNull               ( false               );
$obCmbModulo->addOption             ( "","Selecione"      );
$obCmbModulo->setCampoId            ( "cod_modulo"        );
$obCmbModulo->setCampoDesc          ( "nom_modulo"        );
$obCmbModulo->preencheCombo         ( $rsModulos          );
$obCmbModulo->obEvento->setOnChange ( "montaParametrosGET('preencherBiblioteca','inCodModulo');limpaCampoFuncao();" );

$obCmbBiblioteca = new Select();
$obCmbBiblioteca->setRotulo             ( "Biblioteca Origem"     );
$obCmbBiblioteca->setName               ( "inCodBiblioteca"       );
$obCmbBiblioteca->setId                 ( "inCodBiblioteca"       );
$obCmbBiblioteca->setTitle              ( "Informe a bibliotéca." );
$obCmbBiblioteca->setStyle              ( "width: 250px"          );
$obCmbBiblioteca->setNull               ( false                   );
$obCmbBiblioteca->addOption             ( "","Selecione"          );
$obCmbBiblioteca->setCampoId            ( "cod_biblioteca"        );
$obCmbBiblioteca->setCampoDesc          ( "desc_biblioteca"       );
$obCmbBiblioteca->preencheCombo         ( new RecordSet()         );
$obCmbBiblioteca->obEvento->setOnchange ( "limpaCampoFuncao()"     );

$obBscFuncao = new BuscaInner;
$obBscFuncao->setRotulo                         ( "Função Origem"              );
$obBscFuncao->setTitle                          ( "Selecione a função."        );
$obBscFuncao->setId                             ( "stFuncao"                   );
$obBscFuncao->setNull                           ( false                        );
$obBscFuncao->obCampoCod->setName               ( "inCodFuncao"                );
$obBscFuncao->obCampoCod->setValue              ( $inCodFuncao                 );
$obBscFuncao->obCampoCod->obEvento->setOnChange ( "buscaValor('buscaFuncao');" );
$obBscFuncao->obCampoCod->obEvento->setOnBlur   ( "buscaValor('buscaFuncao');" );
$obBscFuncao->obCampoCod->setMascara            ( "99.99.999"                  );
#$obBscFuncao->setFuncaoBusca                    ( "JavaScript:abrePopUpFuncao()");
$obBscFuncao->setFuncaoBusca                    ( "abrePopUpFuncao()"          );

$rsModulos->setPrimeiroElemento(true);

$obCmbModuloC = new Select();
$obCmbModuloC->setRotulo             ( "Módulo Destino"                         );
$obCmbModuloC->setName               ( "inCodModuloC"                           );
$obCmbModuloC->setId                 ( "inCodModuloC"                           );
$obCmbModuloC->setTitle              ( "Informe o módulo em que será copiado."  );
$obCmbModuloC->setStyle              ( "width: 250px"                           );
$obCmbModuloC->setNull               ( false                                    );
$obCmbModuloC->addOption             ( "","Selecione"                           );
$obCmbModuloC->setCampoId            ( "cod_modulo"                             );
$obCmbModuloC->setCampoDesc          ( "nom_modulo"                             );
$obCmbModuloC->preencheCombo         ( $rsModulos                               );
$obCmbModuloC->obEvento->setOnChange ( "montaParametrosGET('preencherBibliotecaC','inCodModuloC');" );

$obCmbBibliotecaC = new Select();
$obCmbBibliotecaC->setRotulo     ( "Biblioteca Destino"                        );
$obCmbBibliotecaC->setName       ( "inCodBibliotecaC"                          );
$obCmbBibliotecaC->setId         ( "inCodBibliotecaC"                          );
$obCmbBibliotecaC->setTitle      ( "Informe a bibliotéca em que será copiado." );
$obCmbBibliotecaC->setStyle      ( "width: 250px"                              );
$obCmbBibliotecaC->setNull       ( false                                       );
$obCmbBibliotecaC->addOption     ( "","Selecione"                              );
$obCmbBibliotecaC->setCampoId    ( "cod_bibliotecac"                           );
$obCmbBibliotecaC->setCampoDesc  ( "desc_bibliotecac"                          );
$obCmbBibliotecaC->preencheCombo ( new RecordSet()                             );

$obTxtFuncao = new TextBox();
$obTxtFuncao->setRotulo       ( "Nome da Nova Função"            );
$obTxtFuncao->setName         ( "stFuncaoCriada"                 );
$obTxtFuncao->setNull         ( false                            );
$obTxtFuncao->setTitle        ( "Informe o nome da nova função." );
$obTxtFuncao->setSize         ( 50                               );
$obTxtFuncao->setMaxLength    ( 50                               );
$obTxtFuncao->obEvento->setOnKeyPress("return validaNome(this, event);");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->obJavaScript->setComplementoValida ( $stValida         );
$obFormulario->addForm                            ( $obForm           );
$obFormulario->addHidden                          ( $obHdnAcao        );
$obFormulario->addHidden                          ( $obHdnCtrl        );
$obFormulario->addTitulo                          ( "Copiar Função"   );
$obFormulario->addComponente                      ( $obCmbModulo      );
$obFormulario->addComponente                      ( $obCmbBiblioteca  );
$obFormulario->addComponente                      ( $obBscFuncao      );
$obFormulario->addComponente                      ( $obCmbModuloC     );
$obFormulario->addComponente                      ( $obCmbBibliotecaC );
$obFormulario->addComponente                      ( $obTxtFuncao      );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
