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
    * Página de Formulario de Inclusao/Alteracao de Fornecedores
    * Data de Criação   : 14/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-05-21 16:04:19 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-02.01.03
*/

/*
$Log$
Revision 1.10  2007/05/21 18:56:14  melo
Bug #9229#

Revision 1.9  2006/07/19 18:14:49  leandro.zis
Bug #6376#

Revision 1.8  2006/07/14 18:11:19  leandro.zis
Bug #6376#

Revision 1.7  2006/07/05 20:42:33  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrograma.class.php"              );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Programa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;

// Consulta a configuração para selecionar o GRUPO X
$obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
$obRConfiguracaoOrcamento->consultarConfiguracao();
$stMascara = $obRConfiguracaoOrcamento->getMascDespesa();
$arMarcara = preg_split( "/[^a-zA-Z0-9]/", $stMascara);
// Grupo X;
$stMascara = $arMarcara[4];

if ($stAcao == 'alterar') {
    $obROrcamentoPrograma = new ROrcamentoPrograma;
    $obROrcamentoPrograma->setCodPrograma( $_GET['inCodPrograma'] );
    $obROrcamentoPrograma->setExercicio  ( $_GET['stExercicio']   );
    $inCodPrograma = $_GET['inCodPrograma']."/".$_GET['stExercicio'];
    $obROrcamentoPrograma->consultar( $rsPrograma );
    $stDescricao = $rsPrograma->getCampo( 'descricao' );
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodPrograma = new Hidden;
$obHdnCodPrograma->setName ( "inCodPrograma" );
$obHdnCodPrograma->setValue( $inCodPrograma  );

$obLblCodPrograma = new Label;
$obLblCodPrograma->setRotulo( "Código" );
$obLblCodPrograma->setName  ( "lblCodPrograma"   );
$obLblCodPrograma->setValue ( $inCodPrograma    );

if ($stAcao =="incluir") {
    $obTxtCodPrograma = new TextBox;
    $obTxtCodPrograma->setName     ( "inNumeroPrograma"   );
    $obTxtCodPrograma->setValue    ( $inNumeroPrograma    );
    $obTxtCodPrograma->setRotulo   ( "Código" );
    $obTxtCodPrograma->setSize     ( strlen($stMascara) );
    $obTxtCodPrograma->setMaxLength( strlen($stMascara) );
    $obTxtCodPrograma->setNull     ( false );
    $obTxtCodPrograma->setInteiro  ( true );
    $obTxtCodPrograma->setTitle    ( "Informe o código do programa." );
    $obTxtCodPrograma->obEvento->setOnKeyUp("mascaraDinamico('".$stMascara."', this, event);");
}

//Define o objeto TEXT para armazenar o NUMERO DO ORGAO NO ORCAMENTO
$obTxtDescPrograma = new TextBox;
$obTxtDescPrograma->setName     ( "stDescricao"   );
$obTxtDescPrograma->setValue    ( $stDescricao    );
$obTxtDescPrograma->setRotulo   ( "Descrição" );
$obTxtDescPrograma->setSize     ( 80 );
$obTxtDescPrograma->setMaxLength( 80 );
$obTxtDescPrograma->setNull     ( false );
$obTxtDescPrograma->setTitle    ( "Informe a descrição do programa." );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-02.01.03"                     );
$obFormulario->addHidden( $obHdnCtrl                        );
$obFormulario->addHidden( $obHdnAcao                        );

$obFormulario->addTitulo( "Dados para o Programa" );
if ($stAcao == 'alterar') {
    $obFormulario->addComponente( $obLblCodPrograma         );
    $obFormulario->addHidden    ( $obHdnCodPrograma         );
} else {
    $obFormulario->addComponente( $obTxtCodPrograma         );
}
$obFormulario->addComponente( $obTxtDescPrograma        );

$obFormulario->OK();
$obFormulario->show();

// inicio teste

$obROrcamentoPrograma = new ROrcamentoPrograma;
$stOrder = " ORDER BY exercicio, cod_programa DESC";
$obROrcamentoPrograma->obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
$obROrcamentoPrograma->listar( $rsLista, $stOrder );
$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->setTitulo( "Programas já Inclusos" );
$obLista->setMostraPaginacao(false);
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Número ");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição ");
$obLista->ultimoCabecalho->setWidth( 75 );
$obLista->commitCabecalho();
/*
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho(); */

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_programa" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

/*
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodPrograma", "cod_programa");
$obLista->ultimaAcao->addCampo("&stExercicio"  , "exercicio");
$obLista->ultimaAcao->addCampo("stDescQuestao" , "cod_programa");
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
    } else {
        $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
        }
$obLista->commitAcao(); */

$obLista->show();
// Fim teste

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
