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
    * Arquivo de Oculto
    * Data de Criação: 25/10/2007

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: OCManterConfiguracaoRais.php 46943 2012-06-29 12:10:50Z tonismar $

    * Casos de uso: uc-04.08.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"            );

//Define o nome dos arquivos PHP
$stPrograma = "ConfiguracaoTransparencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function preencherDados()
{
    $stJs  = montaJavaScriptParametrosExportacao();
    $stJs .= montaJavaScriptComboEventosRemuneracao();
    $stJs .= montaJavaScriptComboEventosRedutorTeto();
    $stJs .= montaJavaScriptComboEventosVerba();
    $stJs .= montaJavaScriptComboEventosDeducoes();
    $stJs .= montaJavaScriptComboEventosJetons();
    $stJs .= montaJavascriptOrgaoUnidade();

    return $stJs;
}

function montaJavaScriptComboEventosRemuneracao()
{
    include_once( CAM_GPC_TRANSPARENCIA_MAPEAMENTO."TConfiguracaoTransparencia.class.php");
    $obTConfiguracaoTransparencia = new TConfiguracaoTransparencia();
    $eventos= SistemaLegado::pegaConfiguracao('remuneracao_eventual', 8, Sessao::getExercicio() );
    if ($eventos == 'remuneracao_eventual não encontrado para o módulo 8' || $eventos == '') {
        $eventos='null';
        $stFiltro .= "WHERE cod_evento is ".$eventos."  ";
    } else {
         $stFiltro .= "WHERE cod_evento in (".$eventos.")  ";
    }
    $obTConfiguracaoTransparencia->recuperaValorConfiguracao($rsEventosGravados, $stFiltro);

    $stJs .= "limpaSelect(f.inCodEventoSelecionadosRemuneracao,0);\n";
    $stJs .= "limpaSelect(f.inCodEventoDisponiveisRemuneracao,0);\n";

    $inIndex = 0;
    $stCodEventos = "";
    while (!$rsEventosGravados->eof()) {
        $stJs .= "f.inCodEventoSelecionadosRemuneracao[".$inIndex."] = new Option('".$rsEventosGravados->getCampo("codigo")."-".trim($rsEventosGravados->getCampo("descricao"))."','".$rsEventosGravados->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventosGravados->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventosGravados->proximo();
    }
    $stCodEventos = substr($stCodEventos,0,strlen($stCodEventos)-1);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltro  = " WHERE (natureza = 'P')";
    if ($stCodEventos!="") {
        $stFiltro .= "   AND cod_evento NOT IN (".$stCodEventos.")";
    }
    $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro," descricao");
    $inIndex = 0;
    while (!$rsEventos->eof()) {
        $stJs .= "f.inCodEventoDisponiveisRemuneracao[".$inIndex."] = new Option('".$rsEventos->getCampo("codigo")."-".trim($rsEventos->getCampo("descricao"))."','".$rsEventos->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventos->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventos->proximo();
    }

    return $stJs;
}

function montaJavaScriptComboEventosRedutorTeto()
{
    include_once( CAM_GPC_TRANSPARENCIA_MAPEAMENTO."TConfiguracaoTransparencia.class.php");
    $obTConfiguracaoTransparencia = new TConfiguracaoTransparencia();
    $eventos= SistemaLegado::pegaConfiguracao('redutor_teto', 8, Sessao::getExercicio() );

    if ($eventos == 'redutor_teto não encontrado para o módulo 8' || $eventos == '' || $eventos == "''") {
        $eventos='null';
        $stFiltro .= "WHERE cod_evento is ".$eventos."  ";
    } else {
         $stFiltro .= "WHERE cod_evento in (".$eventos.")  ";
    }

    $obTConfiguracaoTransparencia->recuperaValorConfiguracao($rsEventosGravados, $stFiltro);
    
    $stJs .= "limpaSelect(f.inCodEventoSelecionadosRedutorTeto,0);\n";
    $stJs .= "limpaSelect(f.inCodEventoDisponiveisRedutorTeto,0);\n";

    $inIndex = 0;
    $stCodEventos = "";
    while (!$rsEventosGravados->eof()) {
        $stJs .= "f.inCodEventoSelecionadosRedutorTeto[".$inIndex."] = new Option('".$rsEventosGravados->getCampo("codigo")."-".trim($rsEventosGravados->getCampo("descricao"))."','".$rsEventosGravados->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventosGravados->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventosGravados->proximo();
    }
    $stCodEventos = substr($stCodEventos,0,strlen($stCodEventos)-1);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltro  = " WHERE (natureza = 'D')";
    if ($stCodEventos!="") {
        $stFiltro .= "   AND cod_evento NOT IN (".$stCodEventos.")";
    }
    $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro," descricao");
    $inIndex = 0;
    while (!$rsEventos->eof()) {
        $stJs .= "f.inCodEventoDisponiveisRedutorTeto[".$inIndex."] = new Option('".$rsEventos->getCampo("codigo")."-".trim($rsEventos->getCampo("descricao"))."','".$rsEventos->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventos->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventos->proximo();
    }

    return $stJs;
}

function montaJavaScriptComboEventosVerba()
{
    include_once( CAM_GPC_TRANSPARENCIA_MAPEAMENTO."TConfiguracaoTransparencia.class.php");
    $obTConfiguracaoTransparencia = new TConfiguracaoTransparencia();
    $eventos= SistemaLegado::pegaConfiguracao('verbas_indenizatorias', 8, Sessao::getExercicio() );
    if ($eventos == 'verbas_indenizatorias não encontrado para o módulo 8' || $eventos == '' || $eventos = "''") {
        $eventos='null';
        $stFiltro .= "WHERE cod_evento is ".$eventos."  ";
    } else {
         $stFiltro .= "WHERE cod_evento in (".$eventos.")  ";
    }

    $obTConfiguracaoTransparencia->recuperaValorConfiguracao($rsEventosGravados, $stFiltro);

    $stJs .= "limpaSelect(f.inCodEventoSelecionadosVerba,0);\n";
    $stJs .= "limpaSelect(f.inCodEventoDisponiveisVerba,0);\n";

    $inIndex = 0;
    $stCodEventos = "";
    while (!$rsEventosGravados->eof()) {
        $stJs .= "f.inCodEventoSelecionadosVerba[".$inIndex."] = new Option('".$rsEventosGravados->getCampo("codigo")."-".trim($rsEventosGravados->getCampo("descricao"))."','".$rsEventosGravados->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventosGravados->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventosGravados->proximo();
    }
    $stCodEventos = substr($stCodEventos,0,strlen($stCodEventos)-1);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltro  = " WHERE (natureza = 'P')";
    if ($stCodEventos!="") {
        $stFiltro .= "   AND cod_evento NOT IN (".$stCodEventos.")";
    }
    $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro," descricao");
    $inIndex = 0;
    while (!$rsEventos->eof()) {
        $stJs .= "f.inCodEventoDisponiveisVerba[".$inIndex."] = new Option('".$rsEventos->getCampo("codigo")."-".trim($rsEventos->getCampo("descricao"))."','".$rsEventos->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventos->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventos->proximo();
    }

    return $stJs;
}

function montaJavaScriptComboEventosDeducoes()
{
    include_once( CAM_GPC_TRANSPARENCIA_MAPEAMENTO."TConfiguracaoTransparencia.class.php");
    $obTConfiguracaoTransparencia = new TConfiguracaoTransparencia();
    $eventos= SistemaLegado::pegaConfiguracao('demais_deducoes', 8, Sessao::getExercicio() );

    if ($eventos == 'demais_deducoes não encontrado para o módulo 8' || $eventos == '' || $eventos = "''") {
        $eventos='null';
        $stFiltro .= "WHERE cod_evento is  ".$eventos."  ";
    } else {
         $stFiltro .= "WHERE cod_evento in (".$eventos.")  ";
    }

    $obTConfiguracaoTransparencia->recuperaValorConfiguracao($rsEventosGravados, $stFiltro);

    $stJs .= "limpaSelect(f.inCodEventoSelecionadosDeducoes,0);\n";
    $stJs .= "limpaSelect(f.inCodEventoDisponiveisDeducoes,0);\n";

    $inIndex = 0;
    $stCodEventos = "";
    while (!$rsEventosGravados->eof()) {
        $stJs .= "f.inCodEventoSelecionadosDeducoes[".$inIndex."] = new Option('".$rsEventosGravados->getCampo("codigo")."-".trim($rsEventosGravados->getCampo("descricao"))."','".$rsEventosGravados->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventosGravados->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventosGravados->proximo();
    }
    $stCodEventos = substr($stCodEventos,0,strlen($stCodEventos)-1);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltro  = " WHERE (natureza = 'D')";
    if ($stCodEventos!="") {
        $stFiltro .= "   AND cod_evento NOT IN (".$stCodEventos.")";
    }
    $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro," descricao");
    $inIndex = 0;
    while (!$rsEventos->eof()) {
        $stJs .= "f.inCodEventoDisponiveisDeducoes[".$inIndex."] = new Option('".$rsEventos->getCampo("codigo")."-".trim($rsEventos->getCampo("descricao"))."','".$rsEventos->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventos->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventos->proximo();
    }

    return $stJs;
}

function montaJavaScriptComboEventosJetons()
{
    include_once( CAM_GPC_TRANSPARENCIA_MAPEAMENTO."TConfiguracaoTransparencia.class.php");
    $obTConfiguracaoTransparencia = new TConfiguracaoTransparencia();
    $eventos= SistemaLegado::pegaConfiguracao('pagamento_jetons', 8, Sessao::getExercicio() );

    if ($eventos == 'pagamento_jetons não encontrado para o módulo 8' || $eventos == '' || $eventos = "''") {
        $eventos='null';
        $stFiltro .= "WHERE cod_evento is ".$eventos."  ";
    } else {
         $stFiltro .= "WHERE cod_evento in (".$eventos.")  ";
    }

    $obTConfiguracaoTransparencia->recuperaValorConfiguracao($rsEventosGravados, $stFiltro);

    $stJs .= "limpaSelect(f.inCodEventoSelecionadosJetons,0);\n";
    $stJs .= "limpaSelect(f.inCodEventoDisponiveisJetons,0);\n";

    $inIndex = 0;
    $stCodEventos = "";
    while (!$rsEventosGravados->eof()) {
        $stJs .= "f.inCodEventoSelecionadosJetons[".$inIndex."] = new Option('".$rsEventosGravados->getCampo("codigo")."-".trim($rsEventosGravados->getCampo("descricao"))."','".$rsEventosGravados->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventosGravados->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventosGravados->proximo();
    }
    $stCodEventos = substr($stCodEventos,0,strlen($stCodEventos)-1);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltro  = " WHERE (natureza = 'P')";
    if ($stCodEventos!="") {
        $stFiltro .= "   AND cod_evento NOT IN (".$stCodEventos.")";
    }
    $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro," descricao");
    $inIndex = 0;
    while (!$rsEventos->eof()) {
        $stJs .= "f.inCodEventoDisponiveisJetons[".$inIndex."] = new Option('".$rsEventos->getCampo("codigo")."-".trim($rsEventos->getCampo("descricao"))."','".$rsEventos->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventos->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventos->proximo();
    }

    return $stJs;
}

function montaJavascriptOrgaoUnidade()
{
    include_once( CAM_GPC_TRANSPARENCIA_MAPEAMENTO."TConfiguracaoTransparencia.class.php");
    $obTConfiguracaoTransparencia = new TConfiguracaoTransparencia();
    $eventoOrgaoExecutivo = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 58 AND parametro = 'orgao_prefeitura'");
    $eventoUnidadeExecutivo = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 58 AND parametro = 'unidade_prefeitura'");
    $eventoOrgaoLegislativo = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 58 AND parametro = 'orgao_camara'");
    $eventoUnidadeLegislativo = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 58 AND parametro = 'unidade_camara'");
    $eventoOrgaoRRPS = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 58 AND parametro = 'orgao_rpps'");
    $eventoUnidadeRRPS = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 58 AND parametro = 'unidade_rpps'");
    $eventoOrgaoOutros = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 58 AND parametro = 'orgao_outros'");
    $eventoUnidadeOutros = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 58 AND parametro = 'unidade_outros'");

    $stJs .= "d.getElementById('inCodOrgaoExecutivo').value = '".$eventoOrgaoExecutivo."';\n";
    $stJs .= "d.getElementById('inCodUnidadeExecutivo').value = '".$eventoUnidadeExecutivo."';\n";
    $stJs .= "d.getElementById('inCodOrgaoLegislativo').value = '".$eventoOrgaoLegislativo."';\n";
    $stJs .= "d.getElementById('inCodUnidadeLegislativo').value = '".$eventoUnidadeLegislativo."';\n";
    $stJs .= "d.getElementById('inCodOrgaoRPPS').value = '".$eventoOrgaoRRPS."';\n";
    $stJs .= "d.getElementById('inCodUnidadeRPPS').value = '".$eventoUnidadeRRPS."';\n";
    $stJs .= "d.getElementById('inCodOrgaoOutros').value = '".$eventoOrgaoOutros."';\n";
    $stJs .= "d.getElementById('inCodUnidadeOutros').value = '".$eventoUnidadeOutros."';\n";

    return $stJs;

}

function montaFiltroEntidades()
{
    include_once( CAM_GPC_TRANSPARENCIA_MAPEAMENTO."TConfiguracaoTransparencia.class.php");
    $obTConfiguracaoTransparencia = new TConfiguracaoTransparencia();

    $stEntidadeExportacao = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 58 AND parametro = 'entidade_exportacao'");

    $obRegra = new ROrcamentoDespesa;
    $obRegra->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obRegra->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obRegra->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );
    $rsRecordset = new RecordSet;

    // Define SELECT multiplo para codigo da entidade
    $obCmbEntidades = new SelectMultiplo();
    $obCmbEntidades->setName   ('inCodEntidade');
    $obCmbEntidades->setRotulo ( "Entidades" );
    $obCmbEntidades->setTitle  ( "Selecione as entidades." );
    $obCmbEntidades->setNull   ( false );

    // Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
    if ($rsEntidades->getNumLinhas()==1) {
        $rsRecordset = $rsEntidades;
        $rsEntidades = new RecordSet;
    }

    $rsEntidadesSelecionadas = new Recordset;

    if (!empty($stEntidadeExportacao)) {

        $arEntidadeExportacao  = explode(',', $stEntidadeExportacao);
        $arEntidadeSelecionada = $arTmp = array();

        foreach ($arEntidadeExportacao as $entidadeExportacao) {
            foreach ($rsEntidades->getElementos() as $row) {
                if ($entidadeExportacao == $row['cod_entidade']) {
                    $arEntidadeSelecionada['cod_entidade'] = $row['cod_entidade'];
                    $arEntidadeSelecionada['nom_cgm']      = $row['nom_cgm'];
                    $arTmp[] = $arEntidadeSelecionada;
                }
            }
        }

        $rsEntidadesSelecionadas->preenche($arTmp);

        $arTmp = array();

        while (!$rsEntidades->eof()) {
            $boExiste = false;
            $rsEntidadesSelecionadas->setPrimeiroElemento();

            while (!$rsEntidadesSelecionadas->eof()) {
                if ($rsEntidades->getCampo('cod_entidade') == $rsEntidadesSelecionadas->getCampo('cod_entidade')) {
                    $boExiste = true;
                    break;
                }
                $rsEntidadesSelecionadas->proximo();
            }

            if ($boExiste == false) {
                $arEntidadeDisponivel['cod_entidade'] =  $rsEntidades->getCampo('cod_entidade');
                $arEntidadeDisponivel['nom_cgm']      =  $rsEntidades->getCampo('nom_cgm');
                $arTmp[] = $arEntidadeDisponivel;
            }

            $rsEntidades->proximo();
        }

        $rsEntidades = new RecordSet;
        $rsEntidades->preenche($arTmp);
    }

    $rsEntidades->setPrimeiroElemento();
    $rsEntidadesSelecionadas->setPrimeiroElemento();

    // lista de atributos disponiveis
    $obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
    $obCmbEntidades->setCampoId1   ( 'cod_entidade' );
    $obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
    $obCmbEntidades->SetRecord1    ( $rsEntidades );
    // lista de atributos selecionados
    $obCmbEntidades->SetNomeLista2 ('inCodEntidade');
    $obCmbEntidades->setCampoId2   ('cod_entidade');
    $obCmbEntidades->setCampoDesc2 ('nom_cgm');
    $obCmbEntidades->SetRecord2    ( $rsEntidadesSelecionadas );

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obCmbEntidades );
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs  = "jQuery('#spnEntidade').html('".$stHtml."');\n";
    $stJs .= "if (document.frm.inCodEntidade) { jQuery('#stEval').val('selecionaTodosSelect(document.frm.inCodEntidade)'); }\n";

    return $stJs;
}

function montaJavaScriptParametrosExportacao()
{
    include_once CAM_GPC_TRANSPARENCIA_MAPEAMENTO."TConfiguracaoTransparencia.class.php";

    $obTConfiguracaoTransparencia = new TConfiguracaoTransparencia();
    $hashIdentificador = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 58 AND parametro = 'hash_identificador'");
    $stJs = "jQuery('#stHashIdentificador').val('".$hashIdentificador."');\n";

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "preencherDados":
        $stJs = preencherDados();
    break;
}

if ($stJs) {
    echo $stJs;
}

?>
