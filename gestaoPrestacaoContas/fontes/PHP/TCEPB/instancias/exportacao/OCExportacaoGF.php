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
    * Página Oculta - Exportação Arquivos GF

    * Data de Criação   : 18/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    * $Id: OCExportacaoGF.php 59751 2014-09-09 18:16:38Z michel $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TPB_MAPEAMENTO.'TTPBConfiguracaoEntidade.class.php';
include_once CLA_EXPORTADOR;

//Define o nome dos arquivos PHP
$stPrograma = 'ExportacaoGF';
$pgFilt 	= 'FL'.$stPrograma.'.php';
$pgList 	= 'LS'.$stPrograma.'.php';
$pgForm 	= 'FM'.$stPrograma.'.php';
$pgProc 	= 'PR'.$stPrograma.'.php';
$pgOcul 	= 'OC'.$stPrograma.'.php';
$pgJS   	= 'JS'.$stPrograma.'.js';

SistemaLegado::BloqueiaFrames();

$stAcao             = $_REQUEST['stAcao'];
$arFiltro           = Sessao::read('filtroRelatorio');
$inMes              = $arFiltro['inMes'];
$arUnidadesGestoras = array();
$inCodEntidade      = array($arFiltro['inCodEntidade']);
$arFiltro['inCodEntidade']=$inCodEntidade;

$inTmsInicial  = mktime(0,0,0,$inMes,01,Sessao::getExercicio());
$stDataInicial = date  ('d/m/Y',$inTmsInicial);
$inTmsFinal    = mktime(0,0,0,$inMes+1,01,Sessao::getExercicio()) - 1;
$stDataFinal   = date  ('d/m/Y',$inTmsFinal);

$stEntidades = implode(',', $arFiltro['inCodEntidade']);
if ($stAcao == 'pessoal') {
    // Busca a entidade definida como prefeitura na configuração do orçamento
    $stCampo   = "valor";
    $stTabela  = "administracao.configuracao";
    $stFiltro  = " WHERE exercicio = '".Sessao::getExercicio()."'";
    $stFiltro .= "   AND parametro = 'cod_entidade_prefeitura' ";

    $inCodEntidadePrefeitura = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);

    $inCount = 0;
    foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
        $obTConfiguracao = new TTPBConfiguracaoEntidade();
        $obTConfiguracao->setDado('parametro','tcepb_codigo_unidade_gestora');
        $obTConfiguracao->setDado('cod_entidade', $inCodEntidade);
        $obTConfiguracao->consultar();

        $stCampo   = "cod_entidade";
        $stTabela  = "administracao.entidade_rh";
        $stFiltro  = " WHERE exercicio = '".Sessao::getExercicio()."'";
        $stFiltro .= "   AND cod_entidade = ".$inCodEntidade;
        
        $inCodEntidadeSchema = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);
        
        // Verifica se a entidade selecionada é a entidade prefeitura
        if ($inCodEntidadePrefeitura == $inCodEntidade) {
            $inCodEntidadeSchema = $inCodEntidade;
        }
        
        $arUnidadesGestoras[$inCount]['inCodUnidadeGestora'] = $obTConfiguracao->getDado('valor');
        $arUnidadesGestoras[$inCount]['inCodEntidade']       = $inCodEntidade;
        
        // Verifica se existe o schema para cada entidade selecionada
        if ($inCodEntidadeSchema) {
            $arUnidadesGestoras[$inCount]['boExecutaConsulta'] = true;
        } else {
            $arUnidadesGestoras[$inCount]['boExecutaConsulta'] = false;
        }
        $inCount++;
    }

    // Se o arquivo HistoricoFuncional tiver sido selecionado, fará as verficações necessárias do arquivo
    if (array_search('HistoricoFuncional.txt', $arFiltro['arArquivosSelecionados']) !== false) {

        // Verifica se todos foi feita todas as ligações do organograma.orgao com o orcamentario.unidade
        // onde a pesquisa traz todos os orgao do organograma e o valor correspondente ao orcamentario, se o orcamentario vier vazio
        // quer dizer que nao houve uma ligacao ainda, entao deve parar aqui o processamento
        require_once CAM_GA_ORGAN_MAPEAMENTO.'TOrganogramaOrgao.class.php';
        $obTOrganogramaOrgao = new TOrganogramaOrgao;
        $obTOrganogramaOrgao->recuperaOrgaosServidores($rsOrgao, ' WHERE de_para_orgao_unidade.num_orgao IS NULL');

        if ($rsOrgao->getNumLinhas() > 0) {
            SistemaLegado::alertaAviso($pgFilt."?stAcao=".$stAcao, 'Para gerar o arquivo HistoricoFuncional.txt é necessário relacionar todas as unidades orçamentárias.');
            SistemaLegado::LiberaFrames();
            exit;
        } else {
            require_once CAM_GRH_PES_MAPEAMENTO.'TPessoalSubDivisao.class.php';
            $obTPessoalSubDivisao = new TPessoalSubDivisao;
            $obTPessoalSubDivisao->recuperaDeParaTipoRegimeTrabalho($rsSubDivisao, ' WHERE cod_tipo_regime_trabalho_tce IS NULL', ' ORDER BY cod_regime, cod_sub_divisao ');

            if ($rsSubDivisao->getNumLinhas() > 0) {
                SistemaLegado::alertaAviso($pgFilt."?stAcao=".$stAcao, 'Para gerar o arquivo HistoricoFuncional.txt é necessário relacionar todas os tipos de regimes de trabalho.');
                SistemaLegado::LiberaFrames();
                exit;
            }

        }
    }
} else {
    $obTConfiguracao = new TTPBConfiguracaoEntidade();
    $obTConfiguracao->setDado('parametro','tcepb_codigo_unidade_gestora');

    foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
        $obTConfiguracao->setDado('cod_entidade', $inCodEntidade);
        $obTConfiguracao->consultar();

        if (trim($arUnidadesGestoras[$obTConfiguracao->getDado('valor')])) {
            $arUnidadesGestoras[$obTConfiguracao->getDado('valor')] .= ',';
        }
        $arUnidadesGestoras[$obTConfiguracao->getDado('valor')] .= $inCodEntidade;
    }
}

$stTipoDocumento = 'TCE_PB';
$obExportador    = new Exportador();

if ($stAcao == 'pessoal') {
    include_once CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php";
    $obTEntidade = new TEntidade();
    
    foreach ($arFiltro['arArquivosSelecionados'] as $stArquivo) {
        foreach ($arUnidadesGestoras as $arUnidadesGestorasTMP) {
            $stEntidades       = $arUnidadesGestorasTMP['inCodEntidade'];
            $inUnidadeGestora  = ($arUnidadesGestorasTMP['inCodUnidadeGestora']!='') ? $arUnidadesGestorasTMP['inCodUnidadeGestora'] : '000000';
            $boExecutaConsulta = $arUnidadesGestorasTMP['boExecutaConsulta'];

            // Verifica se a entidade selecionada é a entidade principal, pois no RH cada entidade possui um schema diferente.
            // Nas consultas dos Arquivos de Pessoal, deve-se concatenar na consulta a variável stSchemaEntidade
            if ($inCodEntidadePrefeitura == $stEntidades) {
                // Se for a entidade principal, não precisa utilizar '_' no schema do RH
                $stSchemaEntidade = '';
            } else {
                // Se não for a entidade principal, é necessário concatenar '_' + código da entidade selecionada
                $stSchemaEntidade = '_'.$stEntidades;
            }

            $inMes = $arFiltro['inMes'];
            
            //Verificar se existe Schema para a entidade, se Não existir, gera o arquivo vazio.
            $stFiltro = " WHERE nspname = 'folhapagamento".$stSchemaEntidade."'";
            $obTEntidade->recuperaEsquemasCriados($rsEsquema,$stFiltro);
            
            if($rsEsquema->getNumLinhas()==1)
                $boExecutaConsulta = true;
            else
                $boExecutaConsulta = false;
            
            $obExportador->addArquivo($inUnidadeGestora.$inMes.Sessao::getExercicio().$stArquivo);
            $obExportador->roUltimoArquivo->setTipoDocumento($stTipoDocumento);

            include substr($stArquivo,0,strpos($stArquivo,'.txt')).'.inc.php';
            unset($obTMapeamento,$rsRecordSet,$stEntidades);

            $arRecordSet = null;
        }
    }
} else {

    foreach ($arFiltro['arArquivosSelecionados'] as $stArquivo) {
        foreach ($arUnidadesGestoras as $inUnidadeGestora => $stEntidades) {
            $inUnidadeGestora  = ($inUnidadeGestora!='') ? $inUnidadeGestora : '000000';
            $inMes = $arFiltro['inMes'];
            
            $obExportador->addArquivo($inUnidadeGestora.$inMes.Sessao::getExercicio().$stArquivo);
            $obExportador->roUltimoArquivo->setTipoDocumento($stTipoDocumento);

            # Necessário modificar o nome do arquivo conforme ticket #21844
            if ($stArquivo == "CadastroContaBancaria.txt") {
                $stArquivo = "CadastroContas.txt"; 
            }

            include substr($stArquivo,0,strpos($stArquivo,'.txt')).'.inc.php';
            unset($obTMapeamento,$rsRecordSet,$stEntidades);

            $arRecordSet = null;
        }
    }
}

if ($arFiltro['stTipoExport'] == 'compactados') {
    if ($arFiltro['stAcao'] == 'principais') {
        $obExportador->setNomeArquivoZip('ExportacaoArquivosPrincipais.zip');
    } elseif ($arFiltro['stAcao'] == 'auxiliares') {
        $obExportador->setNomeArquivoZip('ExportacaoArquivosAuxiliares.zip');
    } elseif ($arFiltro['stAcao'] == 'pessoal') {
        $obExportador->setNomeArquivoZip('ExportacaoArquivosPessoal.zip');
    } else {
        $obExportador->setNomeArquivoZip('ExportacaoArquivosPrincipais.zip');
    }
}

$obExportador->show();
SistemaLegado::LiberaFrames();

?>
