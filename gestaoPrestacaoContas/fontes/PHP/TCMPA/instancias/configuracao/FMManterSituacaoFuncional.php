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
    * Página Formulário - Parâmetros do Arquivo
    * Data de Criação   : 30/01/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @ignore

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
/*
require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPATipoUnidadeGestora.class.php";
*/
require_once CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php";
require_once CAM_GRH_PES_MAPEAMENTO."TPessoalRegime.class.php";
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

SistemaLegado::BloqueiaFrames();

//Define o nome dos arquivos PHP
$stPrograma = "ManterSituacaoFuncional";
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

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
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

// Monta o Array dos dados da combo de situações funcionais
$arSituacoesFuncionais = array (
      10 => 'Comissionado'
    , 20 => 'Efetivo'
    , 31 => 'Prefeito'
    , 32 => 'Vice-Prefeito'
    , 33 => 'Presidente da Câmara'
    , 34 => 'Vereador'
    , 40 => 'Inativo'
    , 51 => 'Pensionista de Maior Idade'
    , 52 => 'Pensionista de Menor Idade'
    , 61 => 'Temporário sem Vínculo no Plano de Cargos e Salários'
    , 62 => 'Temporário com Vínculo no Plano de Cargos e Salários'
    , 71 => 'Disposição com Ônus para Órgão de Origem'
    , 72 => 'Disposição sem Ônus para Órgão de Origem'
    , 73 => 'Disposição por Convênio de Municipalização da Saúde'
    , 74 => 'Disposição por Convênio de Municipalização da Educação'
    , 81 => 'Presidente/Diretor de Empresa Pública ou de Economia Mista'
    , 82 => 'Vice-Presidente/Vice-Diretor de Empresa Pública ou de Economia Mista'
    , 83 => 'Servidor de Empresa Pública'
    , 84 => 'Servidor de Empresa Economica Mista'
);

$count = 0;
foreach ($arSituacoesFuncionais as $inCodigo => $stDescricao) {
    $arDadosComboSituacoesFuncionais[$count]['codigo']    = $inCodigo;
    $arDadosComboSituacoesFuncionais[$count]['descricao'] = $stDescricao;
    $count++;
}

$rsSituacaoFuncional = new RecordSet();
$rsSituacaoFuncional->preenche($arDadosComboSituacoesFuncionais);

// Cria a combo de Situações Funcionais
$obSelCodSituacao = new  Select();
$obSelCodSituacao->setName      ( 'inCodSituacao_[cod_sub_divisao]' );
$obSelCodSituacao->setId        ( 'inCodSituacao_[cod_sub_divisao]' );
$obSelCodSituacao->setValue     ( '[cod_situacao]' );
$obSelCodSituacao->setCampoId   ( 'codigo'  );
$obSelCodSituacao->setCampoDesc ( 'descricao' );
$obSelCodSituacao->addOption    ( '', 'Selecione' );
$obSelCodSituacao->preencheCombo( $rsSituacaoFuncional );

/* Faz as busca de todos os regimes para poder gerar as listagens */
$obTPessoalRegime = new TPessoalRegime();
$obTPessoalRegime->recuperaTodos( $rsPessoalRegime );
$count = 0;
$arObSpnLista = array();
$arTitulo = array();
while (!$rsPessoalRegime->eof()) {
    $obTPessoalSubDivisao = new TPessoalSubDivisao();
    $obTPessoalSubDivisao->setDado( 'cod_regime', $rsPessoalRegime->getCampo('cod_regime') );
    $obTPessoalSubDivisao->recuperaListagemSubDivisao( $rsPessoalSubDivisao );

    $table = new Table   ();
    $table->setRecordset  ( $rsPessoalSubDivisao );
    $table->setSummary    ($rsPessoalRegime->getCampo('cod_regime')." - ".$rsPessoalRegime->getCampo('descricao'));
    //$table->setConditional( true , "#ddd" );

    $table->Head->addCabecalho( 'Sub-Divisão' , 80  );
    $table->Head->addCabecalho( 'Situação Funcional' , 20  );

    $table->Body->addCampo     ( '[cod_sub_divisao] - [descricao]' , 'E');
    $table->Body->addComponente( $obSelCodSituacao );

    $table->montaHTML();

    $arObSpnLista[$count] = new Span;
    $arObSpnLista[$count]->setId ( "spnLista_".$count );
    $arObSpnLista[$count]->setValue ( $table->getHTML() );

    $rsPessoalRegime->proximo();
    $count++;
}

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );

foreach ($arObSpnLista as $inChave => $obSpnLista) {
    $obFormulario->addSpan( $obSpnLista );
}

$obFormulario->defineBarra( array( new Ok(true) ) );
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
