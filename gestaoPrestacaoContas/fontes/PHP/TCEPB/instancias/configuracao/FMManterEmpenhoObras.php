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
    * Página de Formulário para configuração
    * Data de Criação   : 26/04/2008

    * @author Diego Barbosa Victoria

    * @ignore

    * Casos de uso : uc-06.03.00
*/

/*
$Log$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");
include_once(CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php" );
include_once(TTPB."TTPBConfiguracaoEntidade.class.php");
include_once(TTPB."TTPBObras.class.php");
include_once ( CAM_GF_EMP_COMPONENTES."IPopUpEmpenho.class.php" );

$stPrograma = "ManterEmpenhoObras";
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

$obEmpenho = new IPopUpEmpenho( $obForm );
$obEmpenho->obCampoCod->setDisabled ( false );
$stTmp = $obEmpenho->obCampoCod->obEvento->getOnChange();
$obEmpenho->obCampoCod->obEvento->onChange = "";
//$obEmpenho->obCampoCod->obEvento->setOnChange( "executaFuncaoAjax('BuscaPreEmpenho');");
$obEmpenho->obITextBoxSelectEntidadeUsuario->setNull(false);
$obEmpenho->setNull (false);

$obExercicioObra = new Exercicio;
$obExercicioObra->setRotulo ("Exercício da Obra");
$obExercicioObra->setName ( "inExercicioObra"  );
$obExercicioObra->setId   ( "inExercicioObra"  );
$obExercicioObra->obEvento->setOnChange("executaFuncaoAjax('BuscaObras&'+this.name+'='+this.value);");

$obTObras = new TTPBObras();
$obTObras->recuperaTodos($rsObras, " WHERE exercicio= '".Sessao::getExercicio()."' ORDER BY num_obra");

$obObras = new Select;
$obObras->setRotulo     ( "Obras"                 );
$obObras->setTitle      ( "Selecione a obra."    );
$obObras->setName       ( "inCodObra"                    );
$obObras->setId         ( "inCodObra"                    );
$obObras->setCampoID    ( "num_obra"                     );
$obObras->setCampoDesc  ( "[num_obra] - [descricao]"     );
$obObras->addOption     ( "", "Selecione"                );
$obObras->preencheCombo ( $rsObras                       );
$obObras->setStyle      ( "width: 200px"                 );
$obObras->setNull       ( false );

$obSpnLista = new Span();
$obSpnLista->setId('spnLista');

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);

$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addHidden            ($obHdnCtrl);
$obFormulario->addTitulo            ( "Relacionamento de Empenho a Obras" );

$obEmpenho->geraFormulario($obFormulario);

$obFormulario->addComponente        ( $obExercicioObra  );
$obFormulario->addComponente        ( $obObras    );

$obFormulario->OK      ();

$obFormulario->addSpan              ($obSpnLista);

$obFormulario->show();

SistemaLegado::executaFrameOculto( "buscaValor('Lista','$pgOcul','$pgProc','','Sessao::getId()');" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
