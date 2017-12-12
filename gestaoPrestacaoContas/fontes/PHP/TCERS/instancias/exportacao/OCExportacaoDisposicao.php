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

 * Data de Criação   : 12/02/2009

 * @author Analista: Tonismar Bernardo
 * @author Desenvolvedor: André Machado

 * @ignore

 * Casos de uso: uc-06.03.00

 $Id: OCExportacaoDisposicao.php 66249 2016-08-01 12:55:19Z michel $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";
include_once CLA_EXPORTADOR;

SistemaLegado::BloqueiaFrames();

$stAcao = $request->get('stAcao', '');
$arFiltro  = Sessao::read('filtroRelatorio');
$stEntidades = implode(",",$arFiltro['inCodEntidade']);

$stFiltro = " WHERE parametro = 'cod_entidade_prefeitura' AND exercicio = '".Sessao::getExercicio()."' AND valor IN ('".$stEntidades."')";

$inCodEntidade = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stFiltro);

if ($inCodEntidade == '') {
    $stFiltro = " WHERE parametro = 'cod_entidade_camara' AND exercicio = '".Sessao::getExercicio()."' AND valor IN ('".$stEntidades."')";

    $inCodEntidade = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stFiltro);

    if ($inCodEntidade == '') {
        $stFiltro = " WHERE parametro = 'cod_entidade_rpps' AND exercicio = '".Sessao::getExercicio()."' AND valor IN ('".$stEntidades."')";

        $inCodEntidade = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stFiltro);
    }
}

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obREntidade->setNumCGM        ( $inCodEntidade);
$obREntidade->listarUsuariosEntidadeCnpj($rsEntidadesDisponiveisCnpj , " ORDER BY cod_entidade" );

while (!$rsEntidadesDisponiveisCnpj->EOF()) {
    $stCNPJ = $inCodEntidade."|".$rsEntidadesDisponiveisCnpj->getCampo('cnpj')."|".$rsEntidadesDisponiveisCnpj->getCampo('nom_cgm');

    $rsEntidadesDisponiveisCnpj->proximo();
}

$arFiltro['stCnpjSetor'] = $stCNPJ;

$arFiltro = Sessao::write('exp_arFiltro',$arFiltro);
$inMes    = isset($arFiltro['inMes']) ? $arFiltro['inMes'] : '';

$obExportador    = new Exportador();

if (is_int(array_search('TCE_4010.txt', $arFiltro['arArquivosSelecionados']))) {
    $obExportador->addArquivo("TCE_4010.txt");
    $obExportador->roUltimoArquivo->setTipoDocumento("TCE_RS");
    $obExportador->roUltimoArquivo->addBloco($arRecordSet["TCE_4010.txt"]);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
}

if (is_int(array_search('TCE_4011.txt', $arFiltro['arArquivosSelecionados']))) {
    $obExportador->addArquivo("TCE_4011.txt");
    $obExportador->roUltimoArquivo->setTipoDocumento("TCE_RS");
    $obExportador->roUltimoArquivo->addBloco($arRecordSet["TCE_4011.txt"]);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
}

if (is_int(array_search('TCE_4111.txt', $arFiltro['arArquivosSelecionados']))) {
    $obExportador->addArquivo("TCE_4111.txt");
    $obExportador->roUltimoArquivo->setTipoDocumento("TCE_RS");

    include_once(CAM_GPC_TCERS_MAPEAMENTO.'TTCERSLivroDiario.class.php');
    $obTTCERSLivroDiario = new TTCERSLivroDiario();
    $obTTCERSLivroDiario->setDado('exercicio', Sessao::read('exercicio'));
    $obTTCERSLivroDiario->setDado('dtInicial', $arFiltro['stDataInicio'] );
    $obTTCERSLivroDiario->setDado('dtFinal',   $arFiltro['stDataFinal']);
    $obTTCERSLivroDiario->recuperaTodos($rsTCE_4111);

    $obExportador->roUltimoArquivo->addBloco($rsTCE_4111);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_conta");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencia");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_lote");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_nota");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_lote");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_lancamento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta1");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_historico");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_historico");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(150);
}

if (is_int(array_search('TCE_4810.txt', $arFiltro['arArquivosSelecionados']))) {
    include_once(CAM_GPC_TCERS_MAPEAMENTO."FExportacaoFolhaPagamento.class.php");
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");

    //Salva filtro na sessão para acessar dentro do método que gera o cabeçalho
    Sessao::write('exp_arFiltro', $arFiltro);

    $obFExportacaoFolhaPagamento = new FExportacaoFolhaPagamento();
    $arEntidades = explode(',',$stEntidades);
    foreach ($arEntidades as $chave=>$valor) {
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
        $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
        $obTAdministracaoConfiguracao->setDado( 'parametro', '%prefeitura%' );
        $obTAdministracaoConfiguracao->pegaConfiguracao( $codEntidadePrefeitura, 'cod_entidade_prefeitura' );

        $rsEntidadesDisponiveisCnpj = new RecordSet;
        $obREntidade = new ROrcamentoEntidade;
        $obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
        $obREntidade->setNumCGM        ( $valor);
        $obREntidade->listarUsuariosEntidadeCnpj($rsEntidadesDisponiveisCnpj , " ORDER BY cod_entidade" );

        $stCNPJ = '';
        while (!$rsEntidadesDisponiveisCnpj->EOF()) {
            $stCNPJ = $valor."|".$rsEntidadesDisponiveisCnpj->getCampo('cnpj')."|".$rsEntidadesDisponiveisCnpj->getCampo('nom_cgm');

            $rsEntidadesDisponiveisCnpj->proximo();
        }

        if ($codEntidadePrefeitura == $valor) {
            $stEntidade = '';
            $stArquivo = "TCE_4810.txt";
        } else {
            $stEntidade = "_".$valor;
            $stArquivo = "TCE_4810".$stEntidade.".txt";
        }

        $obFExportacaoFolhaPagamento->setDado('stEntidade', $stEntidade);
        $arDtInicio = explode('/', $arFiltro['stDataInicio']);
        $arDtFinal = explode('/', $arFiltro['stDataFinal']);

        $obFExportacaoFolhaPagamento->setDado('dt_inicial', $arDtInicio[2].'-'.$arDtInicio[1].'-'.$arDtInicio[0] );
        $obFExportacaoFolhaPagamento->setDado('dt_final'  , $arDtFinal[2].'-'.$arDtFinal[1].'-'.$arDtFinal[0] );

        $obFExportacaoFolhaPagamento->recuperaDadosExportacao($rsTemp);

        $arTemp = $rsTemp->getElementos();
        foreach ($arTemp as $key=>$local) {
            $arBanco = explode('#', $local['array_banco']);
            if (is_array($arBanco)) {
               $arTemp[$key]['bancoDep']    = $arBanco[0];
               $arTemp[$key]['agenciaDep']  = $arBanco[1];
               $arTemp[$key]['correnteDep'] = $arBanco[2];
            }
        }

        $arRecord[$chave] = new RecordSet();
        $arRecord[$chave]->preenche($arTemp);

        $arCNPJSetor = explode('|', $stCNPJ);
        $stCnpj = $arCNPJSetor[1];
        $stNomPrefeitura = $arCNPJSetor[2];

        $arCabecalho = array();
        $arCabecalho[0]['cnpj']         = $stCnpj;
        $arCabecalho[0]['dt_inicial']   = $arFiltro['stDataInicio'];
        $arCabecalho[0]['dt_final']     = $arFiltro['stDataFinal'];
        $arCabecalho[0]['dt_geracao']   = date('d/m/Y',time());
        $arCabecalho[0]['nom_setor']    = $stNomPrefeitura;
        $rsCabecalho = new RecordSet;
        $rsCabecalho->preenche($arCabecalho);

        $obExportador->addArquivo($stArquivo);
        $obExportador->roUltimoArquivo->setTipoDocumento("TCE_RS_DISPOSICAO");
        $obExportador->roUltimoArquivo->addBloco($rsCabecalho);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inicial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_final");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_geracao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_setor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(80);

        $obExportador->roUltimoArquivo->addBloco($arRecord[$chave]);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_folha");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("matricula");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_competencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_pagamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(17);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("irrf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("bancoDep");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agenciaDep");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("correnteDep");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("observacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        //Conta quantidade de registros para inserir no rodapé
        $arRodape = array();
        $arRodape[0]["quantidade_registros"] = $arRecord[$chave]->getNumLinhas();
        $rsRodape = new RecordSet;
        $rsRodape->preenche($arRodape);

        //Rodapé
        $obExportador->roUltimoArquivo->addBloco($rsRodape);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("FINALIZADOR[]");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade_registros");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        unset($rsCabecalho);
    }
}

if (is_int(array_search('TCE_4820.txt', $arFiltro['arArquivosSelecionados']))) {
    include_once(CAM_GPC_TCERS_MAPEAMENTO."FExportacaoFuncionarios.class.php");

    //Salva filtro na sessão para acessar dentro do método que gera o cabeçalho
    Sessao::write('exp_arFiltro', $arFiltro);
    $obFExportacaoFuncionarios = new FExportacaoFuncionarios();
    $arEntidades = explode(',',$stEntidades);

    foreach ($arEntidades as $chave=>$valor) {
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
        $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
        $obTAdministracaoConfiguracao->setDado( 'parametro', '%prefeitura%' );
        $obTAdministracaoConfiguracao->pegaConfiguracao( $codEntidadePrefeitura, 'cod_entidade_prefeitura' );

        $obREntidade = new ROrcamentoEntidade;
        $obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
        $obREntidade->setNumCGM        ( $valor);
        $obREntidade->listarUsuariosEntidadeCnpj($rsEntidadesDisponiveisCnpj , " ORDER BY cod_entidade" );

        while (!$rsEntidadesDisponiveisCnpj->EOF()) {
            $stCNPJ = $valor."|".$rsEntidadesDisponiveisCnpj->getCampo('cnpj')."|".$rsEntidadesDisponiveisCnpj->getCampo('nom_cgm');

            $rsEntidadesDisponiveisCnpj->proximo();
        }

        if ($codEntidadePrefeitura == $valor) {
            $stEntidade = '';
            $stArquivo = "TCE_4820.txt";
        } else {
            $stEntidade = "_".$valor;
            $stArquivo = "TCE_4820".$stEntidade.".txt";
        }

        $stDataFinal = explode('/', $arFiltro['stDataFinal']);
        $inMesFinal = (int) $stDataFinal[1];
        $stDataFinal = $stDataFinal[2].'-'.$stDataFinal[1].'-'.$stDataFinal[0];

        $stDataInicial = explode('/', $arFiltro['stDataInicio']);
        $inMesInicial = (int) $stDataInicial[1];
        $stDataInicial = $stDataInicial[2].'-'.$stDataInicial[1].'-'.$stDataInicial[0];

        if($inMesInicial != $inMesFinal){
            $arRecordFinal = array();

            for($inMes = $inMesInicial; $inMes <= $inMesFinal; $inMes++) {
                //Data Inicial
                if($inMes == $inMesInicial)
                    $stDataInicialTemp = $stDataInicial;
                else
                    $stDataInicialTemp =  Sessao::getExercicio().'-'.str_pad($inMes,2,"0",STR_PAD_LEFT).'-01';

                //Data Final
                if($inMes == $inMesFinal){
                    $stDataFinalTemp = $stDataFinal;
                } else {
                    $stDataFinalTemp = sistemalegado::retornaUltimoDiaMes($inMes, Sessao::getExercicio());
                    $stDataFinalTemp = explode('/', $stDataFinalTemp);
                    $stDataFinalTemp = $stDataFinalTemp[2].'-'.$stDataFinalTemp[1].'-'.$stDataFinalTemp[0];
                }

                $obFExportacaoFuncionarios->setDado('cod_entidade', $stEntidade);
                $obFExportacaoFuncionarios->setDado('dt_inicial'  , $stDataInicialTemp);
                $obFExportacaoFuncionarios->setDado('dt_final'    , $stDataFinalTemp);
                $obFExportacaoFuncionarios->recuperaDadosExportacao($rsTemp);

                foreach ($rsTemp->getElementos() as $key => $campo) {
                    $arRecordFinal[] = $campo;
                }
            }

            $arRecord[$chave] = new RecordSet();
            $arRecord[$chave]->preenche($arRecordFinal);
        }else{
            $obFExportacaoFuncionarios->setDado('cod_entidade', $stEntidade);
            $obFExportacaoFuncionarios->setDado('dt_inicial', $stDataInicial);
            $obFExportacaoFuncionarios->setDado('dt_final', $stDataFinal);
            $obFExportacaoFuncionarios->recuperaDadosExportacao($arRecord[$chave]);
        }

        $arCNPJSetor = explode('|', $stCNPJ);
        $stCnpj = $arCNPJSetor[1];
        $stNomPrefeitura = $arCNPJSetor[2];

        $arCabecalho[0]['cnpj']         = $stCnpj;
        $arCabecalho[0]['dt_inicial']   = $arFiltro['stDataInicio'];
        $arCabecalho[0]['dt_final']     = $arFiltro['stDataFinal'];
        $arCabecalho[0]['dt_geracao']   = date('d/m/Y',time());
        $arCabecalho[0]['nom_setor']    = $stNomPrefeitura;
        $rsCabecalho = new RecordSet;
        $rsCabecalho->preenche($arCabecalho);

        $obExportador->addArquivo($stArquivo);
        $obExportador->roUltimoArquivo->setTipoDocumento("TCE_RS_DISPOSICAO");
        $obExportador->roUltimoArquivo->addBloco($rsCabecalho);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inicial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_final");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_geracao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_setor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(80);

        $obExportador->roUltimoArquivo->addBloco($arRecord[$chave]);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inicial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_registro_funcionario");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(70);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_nascimento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_admissao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_rescisao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cargo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cargo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_setor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("setor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sexo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("qtd_dependentes_irrf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("situacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_regime");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_sub_divisao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_regime_previdencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("rg");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cbo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("servidor_pis_pasep");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_categoria");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("endereco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("observacoes_modificado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("carga_horaria");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_carga_horaria");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cedido_adido");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("onus_origem");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ressarcimento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_movimentacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_orgao_origem_destino");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        //Conta quantidade de registros para inserir no rodapé
        $arRodape = array();
        $arRodape[0]["quantidade_registros"] = $arRecord[$chave]->getNumLinhas();
        $rsRodape = new RecordSet;
        $rsRodape->preenche($arRodape);

        //Rodapé
        $obExportador->roUltimoArquivo->addBloco($rsRodape);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("FINALIZADOR[]");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade_registros");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        unset($rsCabecalho);
    }
}

if (is_int(array_search('TCE_4860.txt', $arFiltro['arArquivosSelecionados']))) {
    $obExportador->addArquivo("TCE_4860.txt");
    $obExportador->roUltimoArquivo->setTipoDocumento("TCE_RS");
    $obExportador->roUltimoArquivo->addBloco($arRecordSet["TCE_4860.txt"]);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

}

if (is_int(array_search('TCE_4960.txt', $arFiltro['arArquivosSelecionados']))) {
    include_once(CAM_GPC_TCERS_MAPEAMENTO."FExportacaoTabelaEventos.class.php");

    //Salva filtro na sessão para acessar dentro do método que gera o cabeçalho
    Sessao::write('exp_arFiltro', $arFiltro);
    $obFExportacaoTabelaEventos = new FExportacaoTabelaEventos();
    $arEntidades = explode(',',$stEntidades);
    foreach ($arEntidades as $chave=>$valor) {
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
        $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
        $obTAdministracaoConfiguracao->setDado( 'parametro', '%prefeitura%' );
        $obTAdministracaoConfiguracao->pegaConfiguracao( $codEntidadePrefeitura, 'cod_entidade_prefeitura' );

        $obREntidade = new ROrcamentoEntidade;
        $obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
        $obREntidade->setNumCGM        ( $valor);
        $obREntidade->listarUsuariosEntidadeCnpj($rsEntidadesDisponiveisCnpj , " ORDER BY cod_entidade" );

        while (!$rsEntidadesDisponiveisCnpj->EOF()) {
            $stCNPJ = $valor."|".$rsEntidadesDisponiveisCnpj->getCampo('cnpj')."|".$rsEntidadesDisponiveisCnpj->getCampo('nom_cgm');

            $rsEntidadesDisponiveisCnpj->proximo();
        }

        if ($codEntidadePrefeitura == $valor) {
            $stEntidade = '';
            $stArquivo = "TCE_4960.txt";
        } else {
            $stEntidade = "_".$valor;
            $stArquivo = "TCE_4960".$stEntidade.".txt";
        }

        $stCondicao = "GROUP BY                                        \n";
        $stCondicao.= "folhapagamento".$stEntidade.".evento.codigo,    \n";
        $stCondicao.= "folhapagamento".$stEntidade.".evento.descricao, \n";
        $stCondicao.= "folhapagamento".$stEntidade.".evento.cod_evento \n";

        $obFExportacaoTabelaEventos->setDado('cod_entidade', $stEntidade);
        $obFExportacaoTabelaEventos->setDado('dt_inicial',   $arFiltro['stDataInicio']);
        $obFExportacaoTabelaEventos->recuperaDadosExportacao($arRecord[$chave], $stCondicao, $stOrdem='folhapagamento'.$stEntidade.'.evento.codigo');

        $arCNPJSetor = explode('|', $stCNPJ);
        $stCnpj = $arCNPJSetor[1];
        $stNomPrefeitura = $arCNPJSetor[2];

        $arCabecalho[0]['cnpj']         = $stCnpj;
        $arCabecalho[0]['dt_inicial']   = $arFiltro['stDataInicio'];
        $arCabecalho[0]['dt_final']     = $arFiltro['stDataFinal'];
        $arCabecalho[0]['dt_geracao']   = date('d/m/Y',time());
        $arCabecalho[0]['nom_setor']    = $stNomPrefeitura;
        $rsCabecalho = new RecordSet;
        $rsCabecalho->preenche($arCabecalho);

        $obExportador->addArquivo($stArquivo);
        $obExportador->roUltimoArquivo->setTipoDocumento("TCE_RS_DISPOSICAO");
        $obExportador->roUltimoArquivo->addBloco($rsCabecalho);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inicial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_final");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_geracao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_setor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(80);

        $obExportador->roUltimoArquivo->addBloco($arRecord[$chave]);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('dt_inicial');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('branco');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('descricao');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('base');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(150);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('codigo');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

        //Conta quantidade de registros para inserir no rodapé
        $arRodape = array();
        $arRodape[0]["quantidade_registros"] = $arRecord[$chave]->getNumLinhas();
        $rsRodape = new RecordSet;
        $rsRodape->preenche($arRodape);

        //Rodapé
        $obExportador->roUltimoArquivo->addBloco($rsRodape);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("FINALIZADOR[]");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade_registros");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        unset($rsCabecalho);
    }
}

if (is_int(array_search('LEIAME.txt', $arFiltro['arArquivosSelecionados']))) {
    $obExportador->addArquivo("LEIAME.txt");
    $obExportador->roUltimoArquivo->setTipoDocumento("TCE_RS");
    $obExportador->roUltimoArquivo->addBloco($arRecordSet["LEIAME.txt"]);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

}

if (is_int(array_search('LEIAUTE.txt', $arFiltro['arArquivosSelecionados']))) {
    $obExportador->addArquivo("LEIAUTE.txt");
    $obExportador->roUltimoArquivo->setTipoDocumento("TCE_RS");
    $obExportador->roUltimoArquivo->addBloco($arRecordSet["LEIAUTE.txt"]);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

}

if (is_int(array_search('CADASTRO.txt', $arFiltro['arArquivosSelecionados']))) {
    $obExportador->addArquivo("CADASTRO.txt");
    $obExportador->roUltimoArquivo->setTipoDocumento("TCE_RS");
    $obExportador->roUltimoArquivo->addBloco($arRecordSet["CADASTRO.txt"]);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

}

if ($arFiltro['stTipoExport'] == 'compactados') {
    $obExportador->setNomeArquivoZip('ExportacaoArquivosDisponiveis.zip');
}

$obExportador->show();

SistemaLegado::LiberaFrames();

?>
