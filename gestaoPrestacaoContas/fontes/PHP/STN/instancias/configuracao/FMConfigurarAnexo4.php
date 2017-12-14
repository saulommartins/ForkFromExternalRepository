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
    * Formulário de Configuração do Anexo 4
    * Data de Criação   : 05/04/2013

    * @author Desenvolvedor: Davi Ritter Aroldi

    * @package URBEM
    * @subpackage Configuração

    * Casos de uso: uc-02.08.07
*/

/* Includes */
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php";
require_once CAM_GPC_STN_MAPEAMENTO."TSTNAporteRecursoRPPS.class.php";

$stPrograma = "ConfigurarAnexo4";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

$obTSTNAporteRecursoRPPS = new TSTNAporteRecursoRPPS();
//busca os grupos de aportes para difinir um grupo por aba
$obTSTNAporteRecursoRPPS->listarAporteRecursoRPPSGrupo($rsGrupos, " WHERE exercicio = '".Sessao::getExercicio()."' ", " ORDER BY cod_grupo ");

######## COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Declara o objeto FormularioAbas
$obFormulario = new FormularioAbas();
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);

//form é montado dinâmicamente conforme os grupos e aportes existentes na base
foreach ($rsGrupos->arElementos as $grupo) {
    $obFormulario->addAba($grupo['descricao']);
    $stAportes = "rsAportes_".$grupo['cod_grupo'];
    //busca os aportes para montar o form de configuração de cada um
    $obTSTNAporteRecursoRPPS->listarAporteRecursoRPPS($$stAportes, " WHERE exercicio = '".Sessao::getExercicio()."' and cod_grupo = ".$grupo['cod_grupo']." ", " ORDER BY cod_aporte ");

    foreach ($$stAportes->arElementos as $aportes) {
        //monta os nomes de objetos dinâmicamente para não haver problemas ao montar o form
        $stPopUpReceita = "obPopUpReceita_".$aportes['cod_aporte'];
        $stSpanLista = "obSpnLista_".$aportes['cod_aporte'];
        $stBtnIncluir = "obBtnIncluir_".$aportes['cod_aporte'];
        $stBtnLimpar = "obBtnLimpar_".$aportes['cod_aporte'];

        $$stPopUpReceita = new BuscaInner;
        $$stPopUpReceita->setRotulo ( "Receita" );
        $$stPopUpReceita->setTitle  ( "Digite o Reduzido da Receita");
        // $$stPopUpReceita->setNull ( false );
        $$stPopUpReceita->setId ( "stNomReceita_".$aportes['cod_aporte'] );
        $$stPopUpReceita->setValue ( $stNomReceita );
        $$stPopUpReceita->obCampoCod->setName ( "inCodReceita_".$aportes['cod_aporte'] );
        $$stPopUpReceita->obCampoCod->setSize ( 10 );
        $$stPopUpReceita->obCampoCod->setMaxLength( 5 );
        $$stPopUpReceita->obCampoCod->setValue ( $inCodReceita );
        $$stPopUpReceita->obCampoCod->setAlign ("left");
        $$stPopUpReceita->obCampoCod->obEvento->setOnBlur("montaParametrosGET('buscaReceita', 'inCodReceita_".$aportes['cod_aporte']."');");
        // $$stPopUpReceita->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."receita/LSReceita.php','frm','inCodReceita','stNomReceita','','".Sessao::getId()."','800','550');");
        $$stPopUpReceita->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."receita/FLReceita.php','frm','inCodReceita_".$aportes['cod_aporte']."','stNomReceita_".$aportes['cod_aporte']."','','".Sessao::getId()."','800','550');");

        $$stSpanLista = new Span;
        $$stSpanLista->setId("spnLista_".$aportes['cod_aporte']);

        //botão de incluir conta na lista
        $$stBtnIncluir = new Button();
        $$stBtnIncluir->setId('btnIncluir_'.$aportes['cod_aporte']);
        $$stBtnIncluir->setName('btnIncluir_'.$aportes['cod_aporte']);
        $$stBtnIncluir->setValue('Incluir');
        $$stBtnIncluir->obEvento->setOnClick("montaParametrosGET('incluiReceita', 'inCodReceita_".$aportes['cod_aporte']."')");

        //botão de limpar lista
        $$stBtnLimpar = new Button();
        $$stBtnLimpar->setId('btnLimpar_'.$aportes['cod_aporte']);
        $$stBtnLimpar->setName('btnLimpar_'.$aportes['cod_aporte']);
        $$stBtnLimpar->setValue('Limpar');
        $$stBtnLimpar->obEvento->setOnClick("montaParametrosGET('limparCampos', 'btnLimpar_".$aportes['cod_aporte']."')");
        #########################################################################

        //cria o array na sessão para a lista
        Sessao::write('arReceitaAporte_'.$aportes['cod_aporte'], array());

        ###########Adiciona no form os objetos###################################
        $obFormulario->addTitulo($aportes['descricao']);
        $obFormulario->addComponente($$stPopUpReceita);
        $obFormulario->agrupaComponentes(array($$stBtnIncluir, $$stBtnLimpar));
        $obFormulario->addSpan($$stSpanLista);
        #########################################################################
    }
}

$obFormulario->OK();
$obFormulario->show();
####################

SistemaLegado::executaFrameOculto('montaListas();');

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
