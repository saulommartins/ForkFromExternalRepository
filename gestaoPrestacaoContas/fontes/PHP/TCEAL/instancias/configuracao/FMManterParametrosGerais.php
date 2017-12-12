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

    * Página de Formulario de Ajustes Gerais Exportacao - TCE-RS
    * Data de Criação   : 11/07/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    * $Revision: 57368 $
    * $Name$
    * $Author: diogo.zarpelon $
    * $Date: 2014-02-28 14:23:28 -0300 (Fri, 28 Feb 2014) $
    *
    * $id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCEAL_MAPEAMENTO."TTCEALExportacaoConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterParametrosGerais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

//Define Span para DataGrid
$obSpnOrgaoUnidade = new Span;
$obSpnOrgaoUnidade->setId ( "spnOrgaoUnidade" );

$obTExportacaoConfiguracao = new TTCEALExportacaoConfiguracao;
$obTExportacaoConfiguracao->recuperaOrgaoUnidade($rsRecordSet,'',' ORDER BY cod_entidade',$boTransacao);

if ($rsRecordSet->getNumLinhas() < 1) {
    $obTExportacaoConfiguracao->recuperaEntidades($rsRecordSet,'',' ORDER BY cod_entidade',$boTransacao);
}

$arOrgaoUnidade = array();

foreach ($rsRecordSet->getElementos() as $chave => $valor) {
    $arOrgaoUnidade[$chave]['descricao_entidade'] = $valor["nom_cgm"];
    $arOrgaoUnidade[$chave]['descricao_orgao'] = "tceal_orgao";
    $arOrgaoUnidade[$chave]['descricao_unidade'] = "tceal_unidade";
    $arOrgaoUnidade[$chave]['cod_orgao'] = $valor["valor"];
    $stFiltro = " WHERE exercicio = '".Sessao::getExercicio()."'
                    AND cod_modulo = 62
                    AND parametro ilike 'tceal_unidade%'
                    AND cod_entidade = ".$valor["cod_entidade"];
    $inCodUnidade = SistemaLegado::pegaDado('valor', 'administracao.configuracao_entidade', $stFiltro); 
    $arOrgaoUnidade[$chave]['cod_unidade'] = $inCodUnidade;
}

$rsTemp = new RecordSet;
$rsTemp->preenche( $arOrgaoUnidade );

$obLista = new Lista;
$obLista->setMostraPaginacao( false );
$obLista->setRecordSet( $rsTemp );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();

//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[descricao_entidade]');
$obLista->commitDadoComponente();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Orgão" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obTxtOrgao = new TextBox;
$obTxtOrgao->setName      ( '[descricao_orgao]_[cod_entidade]' );
$obTxtOrgao->setid        ( '[descricao_orgao]_[cod_entidade]' );
$obTxtOrgao->setValue     ( '[cod_orgao]'                      );
$obTxtOrgao->setRotulo    ( '[descricao_entidade]'             );
$obTxtOrgao->setTitle     ( "Informe o código do orgão "       );
$obTxtOrgao->setInteiro   ( true                               );
$obTxtOrgao->setSize      ( 20                                 );
$obTxtOrgao->setMaxLength ( "10"                               );
$obTxtOrgao->setNull      ( false                              );

$obLista->addDadoComponente( $obTxtOrgao );
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDadoComponente();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Unidade" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obTxtUnidade = new TextBox;
$obTxtUnidade->setName      ( '[descricao_unidade]_[cod_entidade]' );
$obTxtUnidade->setid        ( '[descricao_unidade]_[cod_entidade]' );
$obTxtUnidade->setValue     ( '[cod_unidade]'                      );
$obTxtUnidade->setRotulo    ( '[descricao_entidade]'               );
$obTxtUnidade->setTitle     ( "Informe o código da unidade "       );
$obTxtUnidade->setInteiro   ( true                                 );
$obTxtUnidade->setSize      ( 20                                   );
$obTxtUnidade->setMaxLength ( "10"                                 );
$obTxtUnidade->setNull      ( false                                );

$obLista->addDadoComponente( $obTxtUnidade );
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDadoComponente();
$obLista->montaHTML();
$obSpnOrgaoUnidade->setValue($obLista->getHTML());

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addTitulo    ( "Configuração de Órgão/Unidade" );
$obFormulario->addHidden    ( $obHdnAcao                      );
$obFormulario->addHidden    ( $obHdnCtrl                      );
$obFormulario->addSpan      ( $obSpnOrgaoUnidade              );

$obOk  = new Ok;
$obLimpar = new Button;
$obLimpar->setValue( "Limpar" );
$obLimpar->obEvento->setOnClick( "limpaFormulario();" );

$obFormulario->defineBarra( array( $obOk, $obLimpar ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
