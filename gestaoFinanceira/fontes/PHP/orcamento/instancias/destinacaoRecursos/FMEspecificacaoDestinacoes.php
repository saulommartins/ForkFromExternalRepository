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
    * Página de Formulario de Inclusao/Alteracao Especificação de Destinação de Recursos
    * Data de Criação   : 29/10/2007

    * @author Desenvolvedor: Anderson cAko Konze

    $Id: FMEspecificacaoDestinacoes.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stProjeto = "EspecificacaoDestinacoes";
$pgFilt = "FL".$stProjeto.".php";
$pgList = "LS".$stProjeto.".php";
$pgForm = "FM".$stProjeto.".php";
$pgProc = "PR".$stProjeto.".php";
$pgOcul = "OC".$stProjeto.".php";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
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

    $obTxtCodEspec = new TextBox;
    $obTxtCodEspec->setName     ( "inCodEspec"  );
    $obTxtCodEspec->setRotulo   ( "Código"           );
    $obTxtCodEspec->setValue    ( str_pad($_GET['inCodEspec'],0,2)  );
    $obTxtCodEspec->setSize     ( 2 );
    $obTxtCodEspec->setMaxLength( 2 );
    $obTxtCodEspec->setNull     ( false              );
    $obTxtCodEspec->setInteiro  ( true               );
    if ($stAcao == 'alterar') {
        $obTxtCodEspec->setLabel( true );
    } else {
        $obTxtCodEspec->setTitle    ( "Informe o Código da Especificação."    );
    }

    $obTxtDescEspec = new TextBox;
    $obTxtDescEspec->setName     ( "stDescricao"           );
    $obTxtDescEspec->setValue    ( $_GET['stDescricao']    );
    $obTxtDescEspec->setRotulo   ( "Descrição"        );
    $obTxtDescEspec->setTitle    ( "Informe a descrição da Especificação." );
    $obTxtDescEspec->setSize     ( 80                 );
    $obTxtDescEspec->setMaxLength( 200                );
    $obTxtDescEspec->setNull     ( false              );

    $obCmbFonte = new Select(); // orcamento.fonte_recurso
    $obCmbFonte->setName  ( "inCodFonte"                );
    $obCmbFonte->setRotulo( "Fonte de Destinação"       );
    $obCmbFonte->setTitle ( "Selecione o Tipo da Fonte.");
    $obCmbFonte->setNull  ( false                       );
    $obCmbFonte->setValue ( $_GET['inCodFonte']         );
    $obCmbFonte->addOption( "", "Selecione"             );
    $obCmbFonte->addOption( 1 , "Primárias"             );
    $obCmbFonte->addOption( 2 , "Não Primárias"         );

if ($stAcao == 'incluir') {
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEspecificacaoDestinacaoRecurso.class.php");
    $obTOrcamentoEspecificacao = new TOrcamentoEspecificacaoDestinacaoRecurso;
    $obTOrcamentoEspecificacao->recuperaTodos( $rsEspec, " WHERE fr.cod_fonte = 1 and exercicio = '".Sessao::getExercicio() ,"' ORDER BY edr.cod_especificacao " );

    $obListaPrimarias = new Lista;
    $obListaPrimarias->setRecordSet( $rsEspec);
    $obListaPrimarias->setTitulo( "Especificações Primárias já inclusas") ;
    $obListaPrimarias->setMostraPaginacao(false);
    $obListaPrimarias->addCabecalho();
    $obListaPrimarias->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaPrimarias->ultimoCabecalho->setWidth( 5 );
    $obListaPrimarias->commitCabecalho();
    $obListaPrimarias->addCabecalho();
    $obListaPrimarias->ultimoCabecalho->addConteudo("Código");
    $obListaPrimarias->ultimoCabecalho->setWidth( 10 );
    $obListaPrimarias->commitCabecalho();
    $obListaPrimarias->addCabecalho();
    $obListaPrimarias->ultimoCabecalho->addConteudo("Especificação");
    $obListaPrimarias->ultimoCabecalho->setWidth( 75 );
    $obListaPrimarias->commitCabecalho();

    $obListaPrimarias->addDado();
    $obListaPrimarias->ultimoDado->setCampo( "cod_especificacao" );
    $obListaPrimarias->ultimoDado->setAlinhamento( 'DIREITA' );
    $obListaPrimarias->commitDado();
    $obListaPrimarias->addDado();
    $obListaPrimarias->ultimoDado->setCampo( "descricao" );
    $obListaPrimarias->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaPrimarias->commitDado();

    $obTOrcamentoEspecificacao->recuperaTodos( $rsEspec, " WHERE fr.cod_fonte = 2 and exercicio = '".Sessao::getExercicio(),"' ORDER BY edr.cod_especificacao " );

    $obListaNPrimarias = new Lista;
    $obListaNPrimarias->setRecordSet( $rsEspec);
    $obListaNPrimarias->setTitulo( "Especificações Não Primárias já inclusas") ;
    $obListaNPrimarias->setMostraPaginacao(false);
    $obListaNPrimarias->addCabecalho();
    $obListaNPrimarias->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaNPrimarias->ultimoCabecalho->setWidth( 5 );
    $obListaNPrimarias->commitCabecalho();
    $obListaNPrimarias->addCabecalho();
    $obListaNPrimarias->ultimoCabecalho->addConteudo("Código");
    $obListaNPrimarias->ultimoCabecalho->setWidth( 10 );
    $obListaNPrimarias->commitCabecalho();
    $obListaNPrimarias->addCabecalho();
    $obListaNPrimarias->ultimoCabecalho->addConteudo("Especificação");
    $obListaNPrimarias->ultimoCabecalho->setWidth( 75 );
    $obListaNPrimarias->commitCabecalho();

    $obListaNPrimarias->addDado();
    $obListaNPrimarias->ultimoDado->setCampo( "cod_especificacao" );
    $obListaNPrimarias->ultimoDado->setAlinhamento( 'DIREITA' );
    $obListaNPrimarias->commitDado();
    $obListaNPrimarias->addDado();
    $obListaNPrimarias->ultimoDado->setCampo( "descricao" );
    $obListaNPrimarias->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaNPrimarias->commitDado();

}

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
//$obFormulario->setAjuda ( "UC-02.01.38"                );
$obFormulario->addHidden( $obHdnCtrl                   );
$obFormulario->addHidden( $obHdnAcao                   );

$obFormulario->addTitulo( "Dados para a Especificação da Destinação de Recurso" );
$obFormulario->addComponente( $obTxtCodEspec );
$obFormulario->addComponente( $obTxtDescEspec );
$obFormulario->addComponente( $obCmbFonte );

//Define os botões de ação do formulário
$obBtnOK = new OK;
$obBtnOK->setId( "ok");

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "btnLimpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->obEvento->setOnClick ( "document.frm.reset();" );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;

$arBtn = array();
$arBtn[] = $obBtnOK;
$arBtn[] = $obBtnLimpar;
if ($stAcao=='alterar') {
    $obFormulario->Cancelar($stLocation);
} else {
    $obFormulario->defineBarra( $arBtn );
    $obFormulario->addLista     ( $obListaPrimarias    );
    $obFormulario->addLista     ( $obListaNPrimarias   );
}

include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );
$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
$obRConfiguracaoOrcamento->consultarConfiguracao();
$boDestinacao = $obRConfiguracaoOrcamento->getDestinacaoRecurso();

if ($boDestinacao == 'false') {
    SistemaLegado::exibeAviso("Ação não permitida. O sistema não está configurado para utilizar a Destinação de Recursos.","","erro");
    $obFormulario = new Formulario;
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
