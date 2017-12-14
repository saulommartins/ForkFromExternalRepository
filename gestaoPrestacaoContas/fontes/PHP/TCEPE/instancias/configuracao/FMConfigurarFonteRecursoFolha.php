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
    * Pacote de configuração do TCEPE
    * Data de Criação   : 30/09/2014

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos
    *
    $Id: FMConfigurarFonteRecursoFolha.php 60373 2014-10-16 14:35:21Z diogo.zarpelon $
    *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEFonteRecurso.class.php";

$stPrograma = "ConfigurarFonteRecursoFolha";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";



// Busca a entidade definida como prefeitura na configuração do orçamento
$stCampo   = "valor";
$stTabela  = "administracao.configuracao";
$stFiltro  = " WHERE exercicio = '".Sessao::getExercicio()."'";
$stFiltro .= "   AND parametro = 'cod_entidade_prefeitura' ";

$inCodEntidadePrefeitura = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);
    
$inCodEntidade = $_REQUEST['inCodEntidade'];

// Se foi selecionada a entidade definida como prefeitura, não vai "_" no schema
if ($inCodEntidade == $inCodEntidadePrefeitura) {
    $stFiltro = " WHERE nspname = 'pessoal'";
} else {
    $stFiltro = " WHERE nspname = 'pessoal_".$inCodEntidade."'";
}

$obTEntidade = new TEntidade();
$obTEntidade->recuperaEsquemasCriados($rsEsquemas, $stFiltro);

// Verifica se existe o schema para a entidade selecionada
if ($rsEsquemas->getNumLinhas() < 1) {
    SistemaLegado::alertaAviso($pgFilt.'?stAcao='.$_REQUEST['stAcao'], 'Não existe entidade criada no RH para a entidade selecionada!' , '', 'aviso', Sessao::getId(), '../');
}

Sessao::write('cod_entidade', $_REQUEST['inCodEntidade']);

$boTransacao = new Transacao();
$obTTCEPEFonteRecurso = new TTCEPEFonteRecurso();

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( 'configurar' );

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName( "inCodEntidade" );
$obHdnEntidade->setValue( $_REQUEST["inCodEntidade"] );

$obHdnJs = new Hidden;
$obHdnJs->setId( "hdnJs" );
$obHdnJs->setValue( "" );

$obLblEntidade = new Label;
$obLblEntidade->setRotulo('Entidade');
$obLblEntidade->setId('stEntidade');
$obLblEntidade->setValue($_REQUEST['stNomEntidade']);

$obSpnLotacaoLocal = new Span;
$obSpnLotacaoLocal->setId('spnLotacaoLocal');
$obSpnLotacaoLocal->setValue('');

$obTTCEPEFonteRecurso->recuperaTodos( $rsFonteRecursos, "", "", $boTransacao );

$obCmbFonteRecurso = new Select();
$obCmbFonteRecurso->setRotulo    ( 'Fonte de Recurso'          );
$obCmbFonteRecurso->setId        ( 'inCodFonte'                );
$obCmbFonteRecurso->setName      ( 'inCodFonte'                );
$obCmbFonteRecurso->setCampoId   ( 'cod_fonte'                 );
$obCmbFonteRecurso->setCampoDesc ( '[cod_fonte] - [descricao]' );
$obCmbFonteRecurso->addOption    ( '', 'Selecione'             );
$obCmbFonteRecurso->setNull      ( false                       );
$obCmbFonteRecurso->preencheCombo( $rsFonteRecursos            );
$obCmbFonteRecurso->obEvento->setOnChange( "montaParametrosGET('carregaForm');" );

$obFormulario = new Formulario();
$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnEntidade );
$obFormulario->addHidden     ( $obHdnJs );
$obFormulario->addComponente ( $obLblEntidade );
$obFormulario->addComponente ( $obCmbFonteRecurso );
$obFormulario->addSpan       ( $obSpnLotacaoLocal );

# Executa os JS que estão guardados no Hidden para forçar a seleção de todos os registros dos campos Multiplos Selects.
$obOk = new Ok;
$obOk->obEvento->setOnClick ( "eval(jQuery('#hdnJs').val()); Salvar();" );

$stLocation = $pgFilt.'?'.Sessao::getId().'&stAcao='.$stAcao;

$obCancelar = new Cancelar;
$obCancelar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");

$obFormulario->defineBarra (array($obOk, $obCancelar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>