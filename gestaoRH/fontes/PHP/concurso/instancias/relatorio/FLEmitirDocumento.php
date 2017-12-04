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
* Página de Filtro Emissão de Relatório
* Data de Criação   : 11/04/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Vandré Miguel Ramos

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-13 09:55:05 -0300 (Qua, 13 Jun 2007) $

* Casos de uso: uc-04.01.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/administracao/classes/negocio/RDocumentoDinamicoDocumento.class.php';
include_once ( CAM_GRH_CON_NEGOCIO."RConcursoConcurso.class.php"                                      );

//Define o nome dos arquivos PHP
$stPrograma = "EmitirDocumento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$pgProx = $pgList;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "";
}

$obRConcursoNorma = $obRConcursoConcurso = new RConcursoConcurso;
$rsEdital  = new RecordSet;

$obRConcursoConcurso->recuperaConfiguracao( $arConfiguracao );

foreach ($arConfiguracao as $key => $valor) {
    if ( $key == 'mascara_concurso'.Sessao::getEntidade() ) {
        $stMascaraConcurso = $valor;
    }
    if ( $key == 'tipo_portaria_edital'.Sessao::getEntidade() ) {
        $inTipoNormaEdital = $valor;
    }
}

$obForm = new Form;
$obForm->setAction                  ( $pgProx );
$obForm->setTarget                  ( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( ""       );

//Define o objeto do modulo a ser utilizado nas consultas
$obHdnModulo = new Hidden;
$obHdnModulo->setName ( "inModulo" );
$obHdnModulo->setValue( 17 );

$obRConcursoConcurso->listarEditais( $inTipoNormaEdital,$rsEdital );

$obCmbEdital = new Select;
$obCmbEdital->setName       ( "stEdital"              );
$obCmbEdital->setRotulo     ( "Edital"                );
$obCmbEdital->setStyle      ( "width: 250px"          );
$obCmbEdital->setCampoId    ( "cod_norma"             );
$obCmbEdital->setCampoDesc  ( "nom_norma"             );
$obCmbEdital->addOption     ( "","Selecione o Edital" );
$obCmbEdital->preencheCombo ( $rsEdital               );
$obCmbEdital->setValue      ( $stEdital               );
$obCmbEdital->setNull       ( false                   );
$obCmbEdital->setTitle      ( "Edital"                );

$obTxtEdital= new TextBox;
$obTxtEdital->setName     ( "nuEdital"          );
$obTxtEdital->setValue    ( $nuEdital           );
$obTxtEdital->setRotulo   ( "Edital"            );
$obTxtEdital->setSize     ( 5                   );
$obTxtEdital->setMaxLength( 5                   );
$obTxtEdital->setNull     ( false               );
$obTxtEdital->setInteiro  ( true                );
$obTxtEdital->setTitle    ( 'Número do edital'  );

$obRDocumentoDinamico = new RDocumentoDinamicoDocumento;
$obRDocumentoDinamico->obRModulo->setCodModulo(17);
$obRDocumentoDinamico->listarDocumento($rsDocumento,$stOrder,$boTransacao);

$obCmbDocumento = new Select;
$obCmbDocumento->setName       ( "stDocumento"              );
$obCmbDocumento->setRotulo     ( "Documento"                );
$obCmbDocumento->setStyle      ( "width: 250px"             );
$obCmbDocumento->setCampoId    ( "cod_documento"            );
$obCmbDocumento->setCampoDesc  ( "nom_documento"            );
$obCmbDocumento->addOption     ( "","Selecione o Documento" );
$obCmbDocumento->preencheCombo ( $rsDocumento               );
$obCmbDocumento->setValue      ( $stDocumento               );
$obCmbDocumento->setNull       ( false                      );
$obCmbDocumento->setTitle      ( "Documento"                );

$obTxtDocumento= new TextBox;
$obTxtDocumento->setName     ( "nuDocumento"        );
$obTxtDocumento->setValue    ( $nuDocumento         );
$obTxtDocumento->setRotulo   ( "Documento"          );
$obTxtDocumento->setSize     ( 5                    );
$obTxtDocumento->setMaxLength( 5                    );
$obTxtDocumento->setNull     ( false                );
$obTxtDocumento->setInteiro  ( true                 );
$obTxtDocumento->setTitle    ( 'Número do documento');

$obBscCgm = new BuscaInner;
$obBscCgm->setRotulo			( "Candidato" 				);
$obBscCgm->setTitle				( "Informe o CGM do Candidato" );
$obBscCgm->setId				( "nom_cgm" 				);
$obBscCgm->obCampoCod->setName	( "inNumCGM" 				);
$obBscCgm->obCampoCod->setValue	( $inNumCGM 				);
$obBscCgm->obCampoCod->setAlign	("right");
$obBscCgm->obCampoCod->obEvento->setOnBlur("buscaValor( 'buscaCGM','".$pgOcul."','".$pgProx."','telaPrincipal','".Sessao::getId()."' );");
$obBscCgm->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','nom_cgm','fisica','".Sessao::getId()."','800','550')" );

$obTxtClaInicial = new TextBox;
$obTxtClaInicial->setName     ( "inClaInicial"                   );
$obTxtClaInicial->setValue    ( $inClaInicial                    );
$obTxtClaInicial->setRotulo   ( "Classificação Inicial"          );
$obTxtClaInicial->setSize     ( 5                                );
$obTxtClaInicial->setMaxLength( 5                                );
$obTxtClaInicial->setInteiro  ( true                             );
$obTxtClaInicial->setTitle    ( 'Informe a classificação inicial');

$obTxtClaFinal = new TextBox;
$obTxtClaFinal->setName     ( "inClaFinal"            );
$obTxtClaFinal->setValue    ( $inClaFinal             );
$obTxtClaFinal->setRotulo   ( "Classificação Final"   );
$obTxtClaFinal->setSize     ( 5                       );
$obTxtClaFinal->setMaxLength( 5                       );
$obTxtClaFinal->setInteiro  ( true                    );
$obTxtClaFinal->setTitle    ( 'Informe a classificação Final' );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                          );
$obFormulario->addHidden            ( $obHdnCtrl                       );
$obFormulario->addHidden            ( $obHdnModulo                     );
$obFormulario->addHidden            ( $obHdnAcao                       );
$obFormulario->addTitulo            ( "Dados para o filtro"            );
$obFormulario->addComponenteComposto( $obTxtEdital ,$obCmbEdital       );
$obFormulario->addComponente        ( $obBscCgm                        );
$obFormulario->addComponente        ( $obTxtClaInicial                 );
$obFormulario->addComponente        ( $obTxtClaFinal                   );
$obFormulario->addComponenteComposto( $obTxtDocumento ,$obCmbDocumento );
$obFormulario->OK                   ();
$obFormulario->show                 ();

//include_once ($pgJS);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
