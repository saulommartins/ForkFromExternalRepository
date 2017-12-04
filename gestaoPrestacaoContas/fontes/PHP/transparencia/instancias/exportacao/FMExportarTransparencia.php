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
  $Id: FMExportarTransparencia.php 60442 2014-10-21 19:11:39Z evandro $
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php";
include_once CAM_GPC_TRANSPARENCIA_MAPEAMENTO."TConfiguracaoTransparencia.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ExportarTransparencia";
$pgFilt 	= "FL".$stPrograma.".php";
$pgList 	= "LS".$stPrograma.".php";
$pgForm 	= "FM".$stPrograma.".php";
$pgProc 	= "PR".$stPrograma.".php";
$pgOcul 	= "OC".$stPrograma.".php";
$pgJS   	= "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
//destroi arrays de sessão que armazenam os dados do FILTRO
Sessao::remove('link');

$rsArqExport = $rsAtributos = new RecordSet;

$stAcao = $request->get('stAcao');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto que ira armazenar o nome da pagina oculta
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "hdnPaginaExportacao" );
$obHdnAcao->setValue( "../../../transparencia/instancias/exportacao/".$pgOcul );

$boExportaAutomatico = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 58 AND parametro = 'exporta_automatico'");

if ($boExportaAutomatico == "true") {
    $obAvisoExportacaoAutomatica = new Label;
    $obAvisoExportacaoAutomatica->setName  ( 'lbAviso' );
    $obAvisoExportacaoAutomatica->setRotulo( 'Exportação Automática' );
    $obAvisoExportacaoAutomatica->setValue ( 'A exportação automática está ativada.' );
}

$rsArqSelecionados = $rsArqDisponiveis = new RecordSet;

# Seta data limite da exportação. Dia atual -1.
$data = mktime (0, 0, 0, date("m"), date("d")-1, date("Y"));
$dtFinalEmissao = date('d/m/Y', $data);

$obDtInicialEmissao = new Hidden;
$obDtInicialEmissao->setName  ( 'stDataInicial' );
$obDtInicialEmissao->setValue ( '01/01/'.Sessao::getExercicio() );

$obDtFinalEmissao = new Data;
$obDtFinalEmissao->setName   ( 'stDataFinal' );
$obDtFinalEmissao->setTitle  ( 'Informe a data final para emissão dos arquivos para a exportação. A data inicial é o primeiro dia do ano.' );
$obDtFinalEmissao->setRotulo ( 'Data Final de emissão' );
$obDtFinalEmissao->setValue  ( $dtFinalEmissao );
$obDtFinalEmissao->setObrigatorio(true);

$obLabelArquivos = new Label;
$obLabelArquivos->setName  ( 'arArquivosExportacao' );
$obLabelArquivos->setRotulo( "Arquivos a serem enviados" );
$obLabelArquivos->setTitle ( 'Arquivos que serão enviados ao Portal da Transparência' );
$obLabelArquivos->setValue ( '
          <strong>Ações</strong>
    <br /><strong>Balancete de Despesa</strong>
    <br /><strong>Balancete de Receita</strong>
    <br /><strong>Cargos</strong>
    <br /><strong>Cedidos Adidos</strong>
    <br /><strong>Compras</strong>
    <br /><strong>Credor</strong>
    <br /><strong>Empenho</strong>
    <br /><strong>Entidades</strong>
    <br /><strong>Estagiários</strong>
    <br /><strong>Funções</strong>
    <br /><strong>Item</strong>
    <br /><strong>Licitação</strong>
    <br /><strong>Liquidação</strong>
    <br /><strong>Órgão</strong>
    <br /><strong>Pagamento</strong>
    <br /><strong>Programa</strong>
    <br /><strong>Publicações de Edital</strong>
    <br /><strong>Recurso</strong>
    <br /><strong>Remuneração</strong>
    <br /><strong>Rubrica</strong>
    <br /><strong>Servidores</strong>
    <br /><strong>Sub-funções</strong>
    <br /><strong>Unidades</strong>
');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction ( "../../../exportacao/instancias/processamento/PRExportador.php"   );
$obForm->setTarget ( "telaPrincipal" ); //oculto - telaPrincipal

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->addTitulo ( "Dados para geração de arquivos" );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obDtInicialEmissao );

if ($boExportaAutomatico == "true") {
    $obFormulario->addComponente  ( $obAvisoExportacaoAutomatica );
}

$obFormulario->addComponente  ( $obDtFinalEmissao );
$obFormulario->addComponente  ( $obLabelArquivos);
$obFormulario->OK             ();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
