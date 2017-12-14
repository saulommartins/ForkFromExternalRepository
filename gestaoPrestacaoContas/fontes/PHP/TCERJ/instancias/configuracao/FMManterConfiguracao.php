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
    * Página de Formulário para configuração de Relatório de MODELOS
    * Data de Criação   : 22/05/2006

    * @author Fernando Zank Correa Evangelista

    * @ignore

    * Casos de uso : uc-06.02.01
*/

/*
$Log$
Revision 1.7  2006/07/06 13:52:24  diego
Retirada tag de log com erro.

Revision 1.6  2006/07/06 12:42:06  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TCRJ."TCRJConfiguracao.class.php");

$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

if ($inCodigo) {
    $stLocation .= "&inCodigo=$inCodigo";
}

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTxtUnidadeControle = new TextBox;
$obTxtUnidadeControle->setRotulo   ("Unidade de Controle");
$obTxtUnidadeControle->setName     ("stUnidadeControle"  );
$obTxtUnidadeControle->setSize     (80                   );
$obTxtUnidadeControle->setMaxLength(80                   );
$obTxtUnidadeControle->setInteiro  (false                );
$obTxtUnidadeControle->setTitle    ("Informe a unidade de medida.");
$obTxtUnidadeControle->setNull     (false                );

$obTxtSiglaUnidadeControle = new TextBox;
$obTxtSiglaUnidadeControle->setRotulo   ("Sigla da Unidade de Controle");
$obTxtSiglaUnidadeControle->setName     ("stSiglaUnidadeControle"  );
$obTxtSiglaUnidadeControle->setSize     (20                   );
$obTxtSiglaUnidadeControle->setMaxLength(20                   );
$obTxtSiglaUnidadeControle->setTitle    ("Informe a sigla da Unidade de medida.");
$obTxtSiglaUnidadeControle->setInteiro  (false                );
$obTxtSiglaUnidadeControle->setNull     (false                );

//bens patrimoniais
$obBscCGMResponsavelBens = new IPopUpCGM($obForm);
$obBscCGMResponsavelBens->setId                    ('stNomeCGMResponsavelBens');
$obBscCGMResponsavelBens->setRotulo                ( 'CGM do Responsável'       );
$obBscCGMResponsavelBens->setTitle                 ( 'Informe o CGM relacionado ao responsável pelos bens patrimoniais.');
$obBscCGMResponsavelBens->setValue                 ( $stNomeCGMResponsavelBens);
$obBscCGMResponsavelBens->obCampoCod->setName      ( 'inCGMResponsavelBens' );
$obBscCGMResponsavelBens->obCampoCod->setSize      (8);
$obBscCGMResponsavelBens->obCampoCod->setValue     ( $inCGMResponsavelBens   );
$obBscCGMResponsavelBens->setNull                  ( false               );

$obTxtMatriculaBens = new TextBox;
$obTxtMatriculaBens->setRotulo   ("Matrícula");
$obTxtMatriculaBens->setName     ("stMatriculaBens"  );
$obTxtMatriculaBens->setSize     (20                   );
$obTxtMatriculaBens->setMaxLength(20                   );
$obTxtMatriculaBens->setTitle    ("Informe a matricula do responsável pelos bens patrimoniais.");
$obTxtMatriculaBens->setInteiro  (false                );
$obTxtMatriculaBens->setNull     (false                );

$obTxtCargoBens = new TextBox;
$obTxtCargoBens->setRotulo   ("Cargo");
$obTxtCargoBens->setName     ("stCargoBens"  );
$obTxtCargoBens->setSize     (80                   );
$obTxtCargoBens->setMaxLength(80                   );
$obTxtCargoBens->setInteiro  (false                );
$obTxtCargoBens->setTitle    ("Informe o Cargo do responsável pelos bens patrimoniais.");
$obTxtCargoBens->setNull     (false                );

//conferencia
$obBscCGMResponsavelConferencia = new IPopUpCGM($obForm);
$obBscCGMResponsavelConferencia->setId                    ('stNomeCGMResponsavelConferencia');
$obBscCGMResponsavelConferencia->setRotulo                ( 'CGM do Responsável'       );
$obBscCGMResponsavelConferencia->setTitle                 ( 'Informe o CGM relacionado ao responsável pela conferência.');
$obBscCGMResponsavelConferencia->setValue                 ( $stNomeCGMResponsavelConferencia);
$obBscCGMResponsavelConferencia->obCampoCod->setName      ( 'inCGMResponsavelConferencia' );
$obBscCGMResponsavelConferencia->obCampoCod->setSize      (8);
$obBscCGMResponsavelConferencia->obCampoCod->setValue     ( $inCGMResponsavelConferencia   );
$obBscCGMResponsavelConferencia->setNull                  ( false               );

$obTxtMatriculaConferencia = new TextBox;
$obTxtMatriculaConferencia->setRotulo   ("Matrícula");
$obTxtMatriculaConferencia->setName     ("stMatriculaConferencia"  );
$obTxtMatriculaConferencia->setSize     (20                   );
$obTxtMatriculaConferencia->setMaxLength(20                   );
$obTxtMatriculaConferencia->setTitle    ("Informe a matricula do responsável pela conferência.");
$obTxtMatriculaConferencia->setInteiro  (false                );
$obTxtMatriculaConferencia->setNull     (false                );

$obTxtCargoConferencia = new TextBox;
$obTxtCargoConferencia->setRotulo   ("Cargo");
$obTxtCargoConferencia->setName     ("stCargoConferencia"  );
$obTxtCargoConferencia->setSize     (80                   );
$obTxtCargoConferencia->setMaxLength(80                   );
$obTxtCargoConferencia->setInteiro  (false                );
$obTxtCargoConferencia->setTitle    ("Informe o Cargo do responsável pela conferência.");
$obTxtCargoConferencia->setNull     (false                );

//visto

$obBscCGMResponsavelVisto = new IPopUpCGM($obForm);
$obBscCGMResponsavelVisto->setId                    ('stNomeCGMResponsavelVisto');
$obBscCGMResponsavelVisto->setRotulo                ( 'CGM do Responsável'       );
$obBscCGMResponsavelVisto->setTitle                 ( 'Informe o CGM relacionado ao responsável pelo visto.');
$obBscCGMResponsavelVisto->setValue                 ( $stNomeCGMResponsavelVisto);
$obBscCGMResponsavelVisto->obCampoCod->setName      ( 'inCGMResponsavelVisto' );
$obBscCGMResponsavelVisto->obCampoCod->setSize      (8);
$obBscCGMResponsavelVisto->obCampoCod->setValue     ( $inCGMResponsavelVisto   );
$obBscCGMResponsavelVisto->setNull                  ( false               );

$obTxtMatriculaVisto = new TextBox;
$obTxtMatriculaVisto->setRotulo   ("Matrícula");
$obTxtMatriculaVisto->setName     ("stMatriculaVisto"  );
$obTxtMatriculaVisto->setSize     (20                   );
$obTxtMatriculaVisto->setMaxLength(20                   );
$obTxtMatriculaVisto->setTitle    ("Informe a matricula do responsável pelo visto.");
$obTxtMatriculaVisto->setInteiro  (false                );
$obTxtMatriculaVisto->setNull     (false                );

$obTxtCargoVisto = new TextBox;
$obTxtCargoVisto->setRotulo   ("Cargo");
$obTxtCargoVisto->setName     ("stCargoVisto"  );
$obTxtCargoVisto->setSize     (80                   );
$obTxtCargoVisto->setMaxLength(80                   );
$obTxtCargoVisto->setInteiro  (false                );
$obTxtCargoVisto->setTitle    ("Informe o Cargo do responsável pelo visto.");
$obTxtCargoVisto->setNull     (false                );

//Contabilidade
$obBscCGMResponsavelContabilidade = new IPopUpCGM($obForm);
$obBscCGMResponsavelContabilidade->setId                    ('stNomeCGMResponsavelContabilidade');
$obBscCGMResponsavelContabilidade->setRotulo                ( 'CGM do Responsável'       );
$obBscCGMResponsavelContabilidade->setTitle                 ( 'Informe o CGM relacionado ao responsável pela contabilidade.');
$obBscCGMResponsavelContabilidade->setValue                 ( $stNomeCGMResponsavelContabilidade);
$obBscCGMResponsavelContabilidade->obCampoCod->setName      ( 'inCGMResponsavelContabilidade' );
$obBscCGMResponsavelContabilidade->obCampoCod->setSize      (8);
$obBscCGMResponsavelContabilidade->obCampoCod->setValue     ( $inCGMResponsavelContabilidade   );
$obBscCGMResponsavelContabilidade->setNull                  ( false               );

$obTxtMatriculaContabilidade = new TextBox;
$obTxtMatriculaContabilidade->setRotulo   ("Matrícula");
$obTxtMatriculaContabilidade->setName     ("stMatriculaContabilidade"  );
$obTxtMatriculaContabilidade->setSize     (20                   );
$obTxtMatriculaContabilidade->setMaxLength(20                   );
$obTxtMatriculaContabilidade->setTitle    ("Informe a matricula do responsável pela contabilidade.");
$obTxtMatriculaContabilidade->setInteiro  (false                );
$obTxtMatriculaContabilidade->setNull     (false                );

$obTxtCargoContabilidade = new TextBox;
$obTxtCargoContabilidade->setRotulo   ("Cargo");
$obTxtCargoContabilidade->setName     ("stCargoContabilidade"  );
$obTxtCargoContabilidade->setSize     (80                   );
$obTxtCargoContabilidade->setMaxLength(80                   );
$obTxtCargoContabilidade->setInteiro  (false                );
$obTxtCargoContabilidade->setTitle    ("Informe o Cargo do responsável pela contabilidade.");
$obTxtCargoContabilidade->setNull     (false                );

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);

$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addHidden            ($obHdnCtrl);
$obFormulario->addTitulo            ( "Parâmetros do Módulo TCE-RJ" );
$obFormulario->addComponente        ( $obTxtUnidadeControle );
$obFormulario->addComponente        ( $obTxtSiglaUnidadeControle );
$obFormulario->addTitulo            ( "Responsável pelos Bens Patrimoniais" );
$obFormulario->addComponente        ( $obBscCGMResponsavelBens );
$obFormulario->addComponente        ( $obTxtMatriculaBens );
$obFormulario->addComponente        ( $obTxtCargoBens );
$obFormulario->addTitulo            ( "Responsável pela Conferência" );
$obFormulario->addComponente        ( $obBscCGMResponsavelConferencia );
$obFormulario->addComponente        ( $obTxtMatriculaConferencia );
$obFormulario->addComponente        ( $obTxtCargoConferencia );
$obFormulario->addTitulo            ( "Responsável pelo Visto" );
$obFormulario->addComponente        ( $obBscCGMResponsavelVisto );
$obFormulario->addComponente        ( $obTxtMatriculaVisto );
$obFormulario->addComponente        ( $obTxtCargoVisto );
$obFormulario->addTitulo            ( "Responsável pelo Setor de Contabilidade" );
$obFormulario->addComponente        ( $obBscCGMResponsavelContabilidade );
$obFormulario->addComponente        ( $obTxtMatriculaContabilidade );
$obFormulario->addComponente        ( $obTxtCargoContabilidade );
$obFormulario->OK      ();
$obFormulario->show();

SistemaLegado::executaFrameOculto( "buscaValor('recuperaFormularioAlteracao','$pgOcul','$pgProc','','Sessao::getId()');" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
