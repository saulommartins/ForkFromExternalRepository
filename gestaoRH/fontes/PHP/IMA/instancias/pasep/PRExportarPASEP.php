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
    * Página de Processamento do Exportação PASEP
    * Data de Criação: 29/05/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.08.23

    $Id: PRExportarPASEP.php 66258 2016-08-03 14:25:21Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_EXPORTADOR  );
include_once ( CLA_ARQUIVO_CSV );

Sessao::write("filtroRelatorio", $_POST);
$stAcao = $request->get('stAcao');
$arSessaoLink = Sessao::read('link');
$stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"];

foreach ($_POST as $key=>$value) {
    $stLink .= $key."=".$value."&";
}

//Define o nome dos arquivos PHP
$stPrograma = "ExportarPASEP";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");
$obTEntidade = new TEntidade();
$stFiltroEntidade  = " AND entidade.cod_entidade = ".Sessao::getCodEntidade($boTransacao);
$stFiltroEntidade .= " AND entidade.exercicio = '".Sessao::getExercicio()."'";
$obTEntidade->recuperaInformacoesCGMEntidade($rsCGMEntidade,$stFiltroEntidade);

include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoPasep.class.php");
$obTIMAConfiguracaoPasep = new TIMAConfiguracaoPasep;
$obTIMAConfiguracaoPasep->recuperaRelacionamento($rsPasep);
$arAgencia = explode("-",$rsPasep->getCampo("num_agencia"));
$arConta   = explode("-",$rsPasep->getCampo("num_conta_corrente"));

function montaFiltro()
{
    switch ($_POST["stTipoFiltro"]) {
        case "contrato_todos":
        case "cgm_contrato_todos":
            $stCodContratos = "";
            foreach (Sessao::read("arContratos") as $arContrato) {
                $stCodContratos .= $arContrato["cod_contrato"].",";
            }
            $stCodContratos = substr($stCodContratos,0,strlen($stCodContratos)-1);
            $stFiltro = " AND contrato.cod_contrato IN (".$stCodContratos.")";
            break;
        case "lotacao":
            $stCodOrgao = implode(",",$_POST["inCodLotacaoSelecionados"]);
            $stFiltro = " AND contrato_servidor_orgao.cod_orgao IN (".$stCodOrgao.")";
            break;
        case "local":
            $stCodLocal = implode(",",$_POST["inCodLocalSelecionados"]);
            $stFiltro = " AND contrato_servidor_local.cod_local IN (".$stCodLocal.")";
            break;
        case "atributo_servidor":
            $inCodAtributo  = $_POST["inCodAtributo"];
            $inCodCadastro  = $_POST["inCodCadastro"];
            $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;

            $stFiltro = " AND atributo_contrato_servidor_valor.cod_atributo = ".$inCodAtributo;

            if (is_array($_POST[$stNomeAtributo."_Selecionados"])) {
                $arAtributos = $_POST["Atributo_".$inCodAtributo."_".$inCodCadastro."_Selecionados"];
                $atributo = "";
                foreach ($arAtributos as $inCodValorAtributo) {
                    $atributo .= $inCodValorAtributo.",";
                }
                $atributo = substr($atributo,0,strlen($atributo)-1);
                $stFiltro .= " AND atributo_contrato_servidor_valor.valor IN (".$atributo.")";
            } else {
                $atributo = $_POST[$stNomeAtributo];
                $stFiltro .= " AND atributo_contrato_servidor_valor.valor = '".$atributo."'";
            }
            break;
    }

    return $stFiltro;
}

Sessao::setTrataExcecao(true);
$arSessaoFiltroRelatorio = Sessao::read('filtroRelatorio');
$obErro = new Erro();

switch ($_POST["inEtapaProcessamento"]) {
    // Inicio: Lista de Participantes a Pagar (Exportar FPS900)
    case 1:
        $obExportador = new Exportador();
        $obExportador->setRetorno($pgForm);
        $obExportador->addArquivo("FPS900.TXT");
        $obExportador->roUltimoArquivo->setTipoDocumento("PASEP");

        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");
        $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade();

        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
        $obTAdministracaoConfiguracao->pegaConfiguracao($inExercicioRemessa,"exercicio_remessa_pasep".Sessao::getEntidade());
        if ($inExercicioRemessa < Sessao::getExercicio()) {
            $obTAdministracaoConfiguracao->setDado("exercicio",Sessao::getExercicio());
            $obTAdministracaoConfiguracao->setDado("cod_modulo",40);
            $obTAdministracaoConfiguracao->setDado("parametro","exercicio_remessa_pasep".Sessao::getEntidade());
            $obTAdministracaoConfiguracao->setDado("valor",Sessao::getExercicio());
            $obTAdministracaoConfiguracao->alteracao();

            $obTAdministracaoConfiguracaoEntidade->setDado("exercicio",Sessao::getExercicio());
            $obTAdministracaoConfiguracaoEntidade->setDado("cod_modulo",40);
            $obTAdministracaoConfiguracaoEntidade->setDado("cod_entidade",Sessao::getCodEntidade($boTransacao));
            $obTAdministracaoConfiguracaoEntidade->setDado("parametro","exercicio_remessa_pasep".Sessao::getEntidade());
            $obTAdministracaoConfiguracaoEntidade->setDado("valor",Sessao::getExercicio());
            $obTAdministracaoConfiguracaoEntidade->alteracao();

            $obTAdministracaoConfiguracao->setDado("exercicio",Sessao::getExercicio());
            $obTAdministracaoConfiguracao->setDado("cod_modulo",40);
            $obTAdministracaoConfiguracao->setDado("parametro","num_remessa_pasep".Sessao::getEntidade());
            $obTAdministracaoConfiguracao->setDado("valor",1);
            $obTAdministracaoConfiguracao->alteracao();

            $obTAdministracaoConfiguracaoEntidade->setDado("exercicio",Sessao::getExercicio());
            $obTAdministracaoConfiguracaoEntidade->setDado("cod_modulo",40);
            $obTAdministracaoConfiguracaoEntidade->setDado("cod_entidade",Sessao::getCodEntidade($boTransacao));
            $obTAdministracaoConfiguracaoEntidade->setDado("parametro","num_remessa_pasep".Sessao::getEntidade());
            $obTAdministracaoConfiguracaoEntidade->setDado("valor",1);
            $obTAdministracaoConfiguracaoEntidade->alteracao();
        }
        $obTAdministracaoConfiguracao->pegaConfiguracao($inNumeroRemessa,"num_remessa_pasep".Sessao::getEntidade());

        $obTAdministracaoConfiguracao->setDado("exercicio",Sessao::getExercicio());
        $obTAdministracaoConfiguracao->setDado("cod_modulo",40);
        $obTAdministracaoConfiguracao->setDado("parametro","num_remessa_pasep".Sessao::getEntidade());
        $obTAdministracaoConfiguracao->setDado("valor",$inNumeroRemessa+1);
        $obTAdministracaoConfiguracao->alteracao();

        $obTAdministracaoConfiguracaoEntidade->setDado("exercicio",Sessao::getExercicio());
        $obTAdministracaoConfiguracaoEntidade->setDado("cod_modulo",40);
        $obTAdministracaoConfiguracaoEntidade->setDado("cod_entidade",Sessao::getCodEntidade($boTransacao));
        $obTAdministracaoConfiguracaoEntidade->setDado("parametro","num_remessa_pasep".Sessao::getEntidade());
        $obTAdministracaoConfiguracaoEntidade->setDado("valor",$inNumeroRemessa+1);
        $obTAdministracaoConfiguracaoEntidade->alteracao();

        $arHeaderArquivo[0]["tipo_registro"]        = 1;
        $arHeaderArquivo[0]["nome_arquivo"]         = "FPSF900";
        $arHeaderArquivo[0]["data_geracao"]         = str_replace("/","",$_POST["dtGeracaoArquivo"]);
        $arHeaderArquivo[0]["cnpj_entidade"]        = $rsCGMEntidade->getCampo("cnpj");
        $arHeaderArquivo[0]["numero_remessa"]       = $inNumeroRemessa;
        $arHeaderArquivo[0]["agencia_controle"]     = $arAgencia[0];
        $arHeaderArquivo[0]["digito_agencia"]       = $arAgencia[1];
        $arHeaderArquivo[0]["data_pagamento"]       = str_replace("/","",$_POST["dtCredito"]);
        $arHeaderArquivo[0]["numero_convenio"]      = $rsPasep->getCampo("num_convenio");
        $arHeaderArquivo[0]["codigo_repasse"]       = 1;
        $arHeaderArquivo[0]["agencia_lancamento"]   = $arAgencia[0];
        $arHeaderArquivo[0]["digito_agencia"]       = $arAgencia[1];
        $arHeaderArquivo[0]["conta_corrente"]       = $arConta[0];
        $arHeaderArquivo[0]["digito_conta"]         = $arConta[1];
        $arHeaderArquivo[0]["codigo_lancamento"]    = 0;
        $arHeaderArquivo[0]["codigo_banco"]         = 0;
        $arHeaderArquivo[0]["digito_banco"]         = 0;
        $arHeaderArquivo[0]["brancos"]              = "";
        $arHeaderArquivo[0]["email"]                = strtolower($rsPasep->getCampo("email"));

        $rsHeaderArquivo = new RecordSet;
        $rsHeaderArquivo->preenche($arHeaderArquivo);

        $obExportador->roUltimoArquivo->addBloco($rsHeaderArquivo);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_arquivo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_geracao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_entidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_remessa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia_controle");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_agencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_pagamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_convenio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_repasse");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia_lancamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_agencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_conta");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_lancamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_banco");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("email");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(80);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(43);

        if ($_POST["stTipoFiltro"] == "atributo_servidor") {
            $obTIMAConfiguracaoPasep->setDado("boAtributo","true");
        }
        $obTIMAConfiguracaoPasep->recuperaExportarArquivoFPS900($rsDetalhesArquivo,montaFiltro()," ORDER BY nom_cgm");
        $rsDetalhesArquivo->setCampo("tipo_registro","2",true);

        $obExportador->roUltimoArquivo->addBloco($rsDetalhesArquivo);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("servidor_pis_pasep_formatado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_cgm");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("logradouro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("complemento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("bairro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_municipio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sigla_uf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        $arTrailerArquivo[0]["tipo_registro"]       = 9;
        $arTrailerArquivo[0]["brancos"]             = "";
        $arTrailerArquivo[0]["quantidade"]          = ($rsDetalhesArquivo->getNumLinhas() > 0) ? $rsDetalhesArquivo->getNumLinhas() : 0;

        $rsTrailerArquivo = new RecordSet;
        $rsTrailerArquivo->preenche($arTrailerArquivo);

        $obExportador->roUltimoArquivo->addBloco($rsTrailerArquivo);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(221);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        $obExportador->Show();
        break;
        // Fim: Lista de Participantes a Pagar (Exportar FPS900)

    // Inicio: Rejeitados do FPS900 (Importar FPS909)
    case 2:
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAErrosPasep.class.php");
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAOcorrenciaDetalhe909910.class.php");
        $obTIMAErrosPasep = new TIMAErrosPasep();
        $obTIMAOcorrenciaDetalhe909910 = new TIMAOcorrenciaDetalhe909910();
        $obTIMAErrosPasep->excluirTodos();
        $stCaminho = $_FILES["stCaminho"]["tmp_name"];
        $arquivoImportacao = new ArquivoCSV( $stCaminho );
        $obErro = $arquivoImportacao->Abrir('r');
        $inContador = 1;
        while ( !feof( $arquivoImportacao->reArquivo ) ) {
            $arLinhas = $arquivoImportacao->LerLinha();
            //Processa o cabeçalho do arquivo para pegar a informações necessário para serem
            //incluídas no relatórios de erros a ser gerado
            if ($inContador == 1) {
                //Verifica se o arquivo a ser lido e o arquivo 909
                $stNomeInternoArquivo = trim(substr($arLinhas[0],1,7));

                if (trim($stNomeInternoArquivo) === "FPS909" || trim($stNomeInternoArquivo) === "FPSF909") {
                    $stVetorOcorrencia = substr($arLinhas[0],39,40);
                    $arErrosRegistroHeader = array();
                    for ($inIndex=0;$inIndex<=strlen($stVetorOcorrencia);$inIndex++) {
                        if ($stVetorOcorrencia[$inIndex] == 1) {
                            $inPosicao = $inIndex+40;
                            $arErrosRegistroHeader[] = $inPosicao;
                        }
                    }
                    $arSessaoFiltroRelatorio["arErrosRegistroHeader"] = $arErrosRegistroHeader;
                } else {
            $obErro->setDescricao("Arquivo a ser importado não corresponde a um arquivo FPSF909 válido. Verifique o arquivo importado.");
                    break;
                }
            } else {
                $inRegistro = trim(substr($arLinhas[0],183,15));
                $stNome     = trim(substr($arLinhas[0],133,50));
                $inPisPasep = trim(substr($arLinhas[0],122,11));

                $stVetorOcorrencia = substr($arLinhas[0],80,40);
                for ($inIndex=0;$inIndex<=strlen($stVetorOcorrencia);$inIndex++) {
                    if ($stVetorOcorrencia[$inIndex] == 1) {
                        $inPosicao = $inIndex+81;
                        $stFiltro = " WHERE posicao = ".$inPosicao;
                        $obTIMAOcorrenciaDetalhe909910->recuperaTodos($rsOcorrencia,$stFiltro);
                        if ($rsOcorrencia->getNumLinhas() == 1) {
                            $obTIMAErrosPasep->setDado("num_ocorrencia",$rsOcorrencia->getCampo("num_ocorrencia"));
                            $obTIMAErrosPasep->setDado("registro",$inRegistro);
                            $obTIMAErrosPasep->setDado("nome",$stNome);
                            $obTIMAErrosPasep->setDado("pis_pasep",$inPisPasep);
                            $obTIMAErrosPasep->inclusao();
                            $obTIMAErrosPasep->setDado("cod_erro","");
                        }
                    }
                }
            }
            $inContador++;
        }
        $arSessaoFiltroRelatorio["inRegistrosComErro"] = $inContador--;
        $arquivoImportacao->Fechar();

        if (!$obErro->ocorreu()) {
            sistemaLegado::alertaAviso($pgForm,"Importação do arquivo FPS909 concluído com sucesso!","importação","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgFilt,$obErro->getDescricao(),"importação","erro", Sessao::getId(), "../");
        }
        break;
        // Fim: Rejeitados do FPS900 (Importar FPS909)

    // Inicio: Retorno do FPS900 - Valores a Lançar na Folha (Importar FPS910)
    case 3:
        Sessao::remove("stFolhaPagamentoPasepAtual");
        Sessao::remove("stFolhaPagamentoPasepAnterior");

        // PASEP - Tabelas do arquivo 910
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAErrosPasep910.class.php");
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAErrosCadastraisPasep910.class.php");
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAOcorrenciaDetalhe910.class.php");
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConferencia910.class.php");
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAOcorrenciaCadastral910.class.php");
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAPagamento910.class.php");

        $obTIMAErrosCadastraisPasep910      = new TIMAErrosCadastraisPasep910();
        $obTIMAErrosPasep910 				= new TIMAErrosPasep910();
        $obTIMAOcorrenciaDetalhe910 		= new TIMAOcorrenciaDetalhe910();
        $obTIMAOcorrenciaCadastral910       = new TIMAOcorrenciaCadastral910();
        $obTIMAConferencia910 				= new TIMAConferencia910();
        $obTIMAPagamento910                 = new TIMAPagamento910();

        // Limpando tabelas de LOG de erros
        $obTIMAErrosCadastraisPasep910->excluirTodos();
        $obTIMAErrosPasep910->excluirTodos();
        $obTIMAConferencia910->excluirTodos();

        // Recupera a situacao da folha salario
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php" );
        $obTFolhaSituacao = new TFolhaPagamentoFolhaSituacao;
        $obTFolhaSituacao->recuperaUltimaFolhaSituacao($rsPeriodoMovimentacao);

        // Recupera a situacao da folha complementar
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementarSituacao.class.php");
        $obTFolhaPagamentoComplementarSituacao = new TFolhaPagamentoComplementarSituacao;
        $obTFolhaPagamentoComplementarSituacao->recuperaUltimaFolhaComplementarSituacao($rsFolhaComplementar);

        // Tabelas da Folha Salário
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorPeriodo.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoPeriodo.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEvento.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php");

        $obTFolhaPagamentoEventoCalculado         = new TFolhaPagamentoEventoCalculado;
        $obTFolhaPagamentoContratoServidorPeriodo = new TFolhaPagamentoContratoServidorPeriodo();
        $obTFolhaPagamentoRegistroEventoPeriodo   = new TFolhaPagamentoRegistroEventoPeriodo();
        $obTFolhaPagamentoRegistroEvento          = new TFolhaPagamentoRegistroEvento();
        $obTFolhaPagamentoUltimoRegistroEvento    = new TFolhaPagamentoUltimoRegistroEvento();
        $obTFolhaPagamentoLogErroCalculo          = new TFolhaPagamentoLogErroCalculo;

        $obTFolhaPagamentoRegistroEventoPeriodo->obTFolhaPagamentoContratoServidorPeriodo = &$obTFolhaPagamentoContratoServidorPeriodo;
        $obTFolhaPagamentoRegistroEvento->obTFolhaPagamentoRegistroEventoPeriodo 	      = &$obTFolhaPagamentoRegistroEventoPeriodo;
        $obTFolhaPagamentoUltimoRegistroEvento->obTFolhaPagamentoRegistroEvento 		  = &$obTFolhaPagamentoRegistroEvento;
        $obTFolhaPagamentoEventoCalculado->obTFolhaPagamentoUltimoRegistroEvento          = &$obTFolhaPagamentoUltimoRegistroEvento;
        $obTFolhaPagamentoLogErroCalculo->obTFolhaPagamentoUltimoRegistroEvento           = &$obTFolhaPagamentoUltimoRegistroEvento;

        // Tabelas da Folha Complementar
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorComplementar.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementar.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoComplementar.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoComplementar.class.php");

        $obTFolhaPagamentoEventoComplementarCalculado       = new TFolhaPagamentoEventoComplementarCalculado;
        $obTFolhaPagamentoContratoServidorComplementar      = new TFolhaPagamentoContratoServidorComplementar;
        $obTFolhaPagamentoRegistroEventoComplementar        = new TFolhaPagamentoRegistroEventoComplementar();
        $obTFolhaPagamentoUltimoRegistroEventoComplementar  = new TFolhaPagamentoUltimoRegistroEventoComplementar();
        $obTFolhaPagamentoLogErroCalculoComplementar        = new TFolhaPagamentoLogErroCalculoComplementar;

        $obTFolhaPagamentoRegistroEventoComplementar->obTFolhaPagamentoContratoServidorComplementar      = &$obTFolhaPagamentoContratoServidorComplementar;
        $obTFolhaPagamentoUltimoRegistroEventoComplementar->obTFolhaPagamentoRegistroEventoComplementar  = &$obTFolhaPagamentoRegistroEventoComplementar;
        $obTFolhaPagamentoLogErroCalculoComplementar->obTFolhaPagamentoUltimoRegistroEventoComplementar  = &$obTFolhaPagamentoUltimoRegistroEventoComplementar;
        $obTFolhaPagamentoEventoComplementarCalculado->obTFolhaPagamentoUltimoRegistroEventoComplementar = &$obTFolhaPagamentoUltimoRegistroEventoComplementar;

        /*******************
        * Inicío da exclusão
        * *****************/
        // Recupera em qual folha foi pago o PASEP para poder excluir
        $stFiltro = " WHERE cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
        $obTIMAPagamento910->recuperaTodos($rsPagamento910,$stFiltro);
        Sessao::write("stFolhaPagamentoPasepAnterior", $rsPagamento910->getCampo("cod_tipo"));
        Sessao::write("stFolhaPagamentoPasepAtual"   , $_REQUEST["inCodTipoFolha"]);

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
        $stFiltro = " WHERE evento_sistema = true";
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $obTFolhaPagamentoEvento->recuperaTodos($rsEvento, $stFiltro);

        $arEventosAutomaticos = array();
        while (!$rsEvento->eof()) {
            $arEventosAutomaticos[] = $rsEvento->getCampo("cod_evento");

            $rsEvento->proximo();
        }

        if (count($arEventosAutomaticos) > 0) {
            $stEventos = implode(",",$arEventosAutomaticos);
            $stEventos .= ",".$rsPasep->getCampo("cod_evento");
        } else {
            $stEventos = $rsPasep->getCampo("cod_evento");
        }

        switch ($rsPagamento910->getCampo("cod_tipo")) {
            case 1: // Folha Salario
                if (trim($rsPeriodoMovimentacao->getCampo("situacao")) == "a") {
                $stFiltro  = " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltro .= " AND ultimo_registro_evento.cod_evento in (".$stEventos.")";
                $obTFolhaPagamentoUltimoRegistroEvento->recuperaRelacionamento($rsUltimoRegistro,$stFiltro);

                while (!$rsUltimoRegistro->eof()) {
                    $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_registro"    , $rsUltimoRegistro->getCampo("cod_registro"));
                    $obTFolhaPagamentoUltimoRegistroEvento->setDado("timestamp"       , $rsUltimoRegistro->getCampo("timestamp"));
                    $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_evento"      , $rsUltimoRegistro->getCampo("cod_evento"));
                    $obTFolhaPagamentoUltimoRegistroEvento->deletarUltimoRegistroEvento();

                    $rsUltimoRegistro->proximo();
                }
                } else {
                SistemaLegado::LiberaFrames();
                Sessao::getExcecao()->setDescricao("Folha Salário está fechada, é necessário reabrí-la para que os lançamentos sejam transferidos da folha salário para complementar.");
                }
                break;
            case 3: // Folha Complementar
                if (trim($rsFolhaComplementar->getCampo("situacao")) == "a") {
                $stFiltro  = " AND registro_evento_complementar.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltro .= " AND registro_evento_complementar.cod_evento IN (".$stEventos.")";
                $stFiltro .= " AND registro_evento_complementar.cod_complementar = ".$rsFolhaComplementar->getCampo("cod_complementar");
                $obTFolhaPagamentoRegistroEventoComplementar->recuperaRelacionamento($rsRegistroEventoComplementar,$stFiltro);

                while (!$rsRegistroEventoComplementar->eof()) {
                    $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_registro"     , $rsRegistroEventoComplementar->getCampo("cod_registro"));
                    $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_evento"       , $rsRegistroEventoComplementar->getCampo("cod_evento"));
                    $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_configuracao" , $rsRegistroEventoComplementar->getCampo("cod_configuracao"));
                    $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("timestamp"        , $rsRegistroEventoComplementar->getCampo("timestamp"));
                    $obTFolhaPagamentoUltimoRegistroEventoComplementar->deletarUltimoRegistroEvento();

                    $rsRegistroEventoComplementar->proximo();
                }
                } else {
                SistemaLegado::LiberaFrames();
                Sessao::getExcecao()->setDescricao("Folha Complementar está fechada, é necessário reabrí-la para que os lançamentos sejam transferidos da folha complementar para salário.");
                }

                break;
        }
        // Atualizando nova forma de pagamento( Salario ou Complementar)
        $obTIMAPagamento910->setDado("cod_periodo_movimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
        $obTIMAPagamento910->setDado("cod_tipo"				   , $_REQUEST["inCodTipoFolha"]);
        if ($rsPagamento910->getNumLinhas() != -1) {
            $obTIMAPagamento910->alteracao();
        } else {
            $obTIMAPagamento910->inclusao();
        }

        /*******************
        * Fim da exclusão
        * *****************/
        $stCaminho = $_FILES["stCaminho"]["tmp_name"];
        $arquivoImportacao = new ArquivoCSV( $stCaminho );
        $obErro = $arquivoImportacao->Abrir('r');
        $inLinha = 0;
        while (!feof( $arquivoImportacao->reArquivo)) {
            $arLinhas = $arquivoImportacao->LerLinha();
            $inLinha++;
            switch ($arLinhas[0][0]) {

            case 1:
                //Verifica se o arquivo a ser lido e o arquivo 910
                $stNomeInternoArquivo = trim(substr($arLinhas[0],7,7));
                if (trim($stNomeInternoArquivo) === "FPS910" || trim($stNomeInternoArquivo) === "FPSF910") {
                $stVetorOcorrencia = substr($arLinhas[0],123,20);
                $arErrosRegistroHeader = array();
                for ($inIndex=0;$inIndex<=strlen($stVetorOcorrencia);$inIndex++) {
                    if ($stVetorOcorrencia[$inIndex] == 1) {
                    $inPosicao = $inIndex+124;
                    $arErrosRegistroHeader[] = $inPosicao;
                    }
                }
                $arSessaoFiltroRelatorio["arErrosRegistroHeader"] = $arErrosRegistroHeader;
                } else {
                SistemaLegado::LiberaFrames();
                Sessao::getExcecao()->setDescricao("Arquivo a ser importado não corresponde a um arquivo FPSF910 válido. Verifique o arquivo importado.");
                break 2;
                }
                break;
            case 2:
                //inProcessamentoDefinitivo igual a 1 registro sem ocorrências, pode ser incluído no banco
                //inProcessamentoDefinitivo igual a 0 registro possui alguma ocorrência

                $inRegistro = trim(substr($arLinhas[0],7,15));
                $inPisPasep = trim(substr($arLinhas[0],22,11));
                $nuValorPasep = trim(substr($arLinhas[0],106,11));
                $nuValorPasep = substr($nuValorPasep, 0, strlen($nuValorPasep)-2).".".substr($nuValorPasep, strlen($nuValorPasep)-2);

                // Verifica se o arquivo possue mais de um registro de PASEP, se existir ele vai inserir somente o último registro.
                $arquivoImportacaoAux = new ArquivoCSV( $stCaminho );
                $obErroAux = $arquivoImportacaoAux->Abrir('r');
                $inLinhaAux = 0;
                $inContaRepeticaoes=0;
                while (!feof( $arquivoImportacaoAux->reArquivo)) {
                $inLinhaAux++;
                $arLinhasAux = $arquivoImportacaoAux->LerLinha();
                $inPisPasepAux = trim(substr($arLinhasAux[0],22,11));
                $inProcessamentoDefinitivoAux = trim(substr($arLinhasAux[0],85,1));
                if ($inProcessamentoDefinitivoAux != '') {
                    if ($inPisPasepAux == $inPisPasep && $inLinhaAux > $inLinha) {
                    $inContaRepeticaoes++;
                    }
                }
                }
                $arquivoImportacaoAux->Fechar();
                if ($inContaRepeticaoes < 1) {

                $stFiltro = " AND trim(translate(sw_cgm_pessoa_fisica.servidor_pis_pasep,'.,-','')) = trim('".$inPisPasep."')";
                $obTIMAOcorrenciaDetalhe910->recuperaDadosServidor($rsServidor,$stFiltro);

                $inProcessamentoDefinitivo = trim(substr($arLinhas[0],85,1));

                if ($inProcessamentoDefinitivo && $rsServidor->getNumLinhas() != -1) {
                    //incluir na conferência e incluir e evento de sistema de pasep com o valor indicado
                    $obTIMAConferencia910->setDado("cod_contrato",$rsServidor->getCampo("cod_contrato"));
                    $obTIMAConferencia910->setDado("valor_pasep",$nuValorPasep);
                    $obTIMAConferencia910->inclusao();
                    $obTIMAConferencia910->setDado("cod_conferencia","");

                    switch ($_REQUEST["inCodTipoFolha"]) {
                    case 1: // Folha Salario
                        $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro","");
                        $obTFolhaPagamentoContratoServidorPeriodo->setDado("cod_contrato",$rsServidor->getCampo("cod_contrato"));
                        $obTFolhaPagamentoContratoServidorPeriodo->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                        $obTFolhaPagamentoContratoServidorPeriodo->recuperaPorChave($rsContratoServidorPeriodo);
                        if ($rsContratoServidorPeriodo->getNumLinhas() < 0) {
                        $obTFolhaPagamentoContratoServidorPeriodo->inclusao();
                        }
                        $obTFolhaPagamentoRegistroEventoPeriodo->inclusao();

                        $obTFolhaPagamentoRegistroEvento->setDado("cod_evento",$rsPasep->getCampo("cod_evento"));
                        $obTFolhaPagamentoRegistroEvento->setDado("valor",$nuValorPasep);
                        $obTFolhaPagamentoRegistroEvento->setDado("quantidade",0);
                        $obTFolhaPagamentoRegistroEvento->inclusao();

                        $obTFolhaPagamentoUltimoRegistroEvento->inclusao();
                        break;
                    case 3: // Folha Complementar
                        $obTFolhaPagamentoContratoServidorComplementar->setDado("cod_contrato"            , $rsServidor->getCampo("cod_contrato"));
                        $obTFolhaPagamentoContratoServidorComplementar->setDado("cod_periodo_movimentacao", $rsFolhaComplementar->getCampo("cod_periodo_movimentacao"));
                        $obTFolhaPagamentoContratoServidorComplementar->setDado("cod_complementar"		  , $rsFolhaComplementar->getCampo("cod_complementar"));
                        $obTFolhaPagamentoContratoServidorComplementar->recuperaPorChave($rsContratoComplementar);

                        if ($rsContratoComplementar->getNumLinhas() < 0) {
                        $obTFolhaPagamentoContratoServidorComplementar->inclusao();
                        }

                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_registro"            , "");
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_contrato"            , $rsServidor->getCampo("cod_contrato"));
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_complementar"        , $rsFolhaComplementar->getCampo("cod_complementar"));
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_periodo_movimentacao", $rsFolhaComplementar->getCampo("cod_periodo_movimentacao"));
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_evento"              , $rsPasep->getCampo("cod_evento"));
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("valor"                   , $nuValorPasep);
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("quantidade"              , 0);
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_configuracao"        , 1);
                        $obTFolhaPagamentoRegistroEventoComplementar->inclusao();

                        $obTFolhaPagamentoUltimoRegistroEventoComplementar->inclusao();
                        break;
                    }
                } else {
                    $boEncontrouOcorrencia = false;
                    $stVetorOcorrencia = substr($arLinhas[0],86,20);
                    for ($inIndex=0;$inIndex<=strlen($stVetorOcorrencia);$inIndex++) {
                        if ($stVetorOcorrencia[$inIndex] == 1) {
                            $boEncontrouOcorrencia = true;
                            $inPosicao = $inIndex+87;
                            $stFiltro = " WHERE posicao = ".$inPosicao;
                            $obTIMAOcorrenciaDetalhe910->recuperaTodos($rsOcorrencia,$stFiltro);
                            if ($rsOcorrencia->getNumLinhas() == 1) {
                                $obTIMAErrosPasep910->setDado("num_ocorrencia"	, $rsOcorrencia->getCampo("num_ocorrencia"));
                                $obTIMAErrosPasep910->setDado("registro"		, $rsServidor->getCampo("matricula"));
                                $obTIMAErrosPasep910->setDado("nome"			, $rsServidor->getCampo("nom_cgm"));
                                $obTIMAErrosPasep910->setDado("pis_pasep"		, $inPisPasep);
                                $obTIMAErrosPasep910->inclusao();
                                $obTIMAErrosPasep910->setDado("cod_erro","");
                            }
                        }
                    }

                    if ($boEncontrouOcorrencia === false and $nuValorPasep > 0) {
                        $obTIMAErrosCadastraisPasep910->setDado("num_ocorrencia" , 1); // COM valor e SEM pispasep
                        $obTIMAErrosCadastraisPasep910->setDado("pis_pasep"		 , $inPisPasep);
                        $obTIMAErrosCadastraisPasep910->setDado("valor"			 , $nuValorPasep);
                        $obTIMAErrosCadastraisPasep910->inclusao();
                        $obTIMAErrosCadastraisPasep910->setDado("cod_erro","");
                    }
                }
                }
                break;
                //default:
                //SistemaLegado::LiberaFrames();
                //Sessao::getExcecao()->setDescricao("Arquivo a ser importado não corresponde a um arquivo FPSF910 válido. Verifique o arquivo importado.");
                //break 3;

                //break;
            }
        }
        $arquivoImportacao->Fechar();

        if (!$obErro->ocorreu()) {
        sistemaLegado::alertaAviso($pgForm,"Importação do arquivo FPS910 concluído com sucesso!","importação","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgFilt,$obErro->getDescricao(),"importação","erro", Sessao::getId(), "../");
        }
        break;
        // Fim: Retorno do FPS900 - Valores a Lançar na Folha (Importar FPS910)

    // Inicio: Lista de Participantes não Pagos na Folha (Exportar FPS950)
    case 4:
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
        $obTAdministracaoConfiguracao->pegaConfiguracao($inNumeroRemessa,"num_remessa_pasep".Sessao::getEntidade());

        $obExportador = new Exportador();
        $obExportador->setRetorno($pgForm);
        $obExportador->addArquivo("FPS950.TXT");
        $obExportador->roUltimoArquivo->setTipoDocumento("PASEP");

        $arHeaderArquivo[0]["tipo_registro"]        = 1;
        $arHeaderArquivo[0]["nome_arquivo"]         = "FPSF950";
        $arHeaderArquivo[0]["data_geracao"]         = str_replace("/","",$_POST["dtGeracaoArquivo"]);
        $arHeaderArquivo[0]["cnpj_entidade"]        = $rsCGMEntidade->getCampo("cnpj");
        $arHeaderArquivo[0]["numero_convenio"]      = $rsPasep->getCampo("num_convenio");
        $arHeaderArquivo[0]["numero_remessa"]       = $inNumeroRemessa;

        $rsHeaderArquivo = new recordset;
        $rsHeaderArquivo->preenche($arHeaderArquivo);

        $obExportador->roUltimoArquivo->addBloco($rsHeaderArquivo);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_arquivo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_geracao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_entidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_convenio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_remessa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        // Recupera ultimo periodo de movimentacao
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php" );
        $obTFolhaSituacao = new TFolhaPagamentoFolhaSituacao;
        $obTFolhaSituacao->recuperaUltimaFolhaSituacao($rsPeriodoMovimentacao);

        // Recupera em qual folha foi pago o PASEP
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAPagamento910.class.php");
        $obTIMAPagamento910 = new TIMAPagamento910();
        $stFiltro = " WHERE cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
        $obTIMAPagamento910->recuperaTodos($rsPagamento910, $stFiltro);

        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConferencia910.class.php");
        $obTIMAConferencia910 = new TIMAConferencia910();
        $obTIMAConferencia910->setDado("inFolhaPagamento", $rsPagamento910->getCampo("cod_tipo"));
        $obTIMAConferencia910->recuperaRelacionamento($rsDetalhesArquivo);
        $rsDetalhesArquivo->setCampo("tipo_registro",2,true);

        $obExportador->roUltimoArquivo->addBloco($rsDetalhesArquivo);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("pis_pasep");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

        $arTrailerArquivo[0]["tipo_registro"]       = 9;
        $arTrailerArquivo[0]["brancos"]             = "";
        $arTrailerArquivo[0]["quantidade"]          = ($rsDetalhesArquivo->getNumLinhas() > 0) ? $rsDetalhesArquivo->getNumLinhas() : 0;

        $rsTrailerArquivo = new recordset;
        $rsTrailerArquivo->preenche($arTrailerArquivo);

        $obExportador->roUltimoArquivo->addBloco($rsTrailerArquivo);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(35);

        $obExportador->Show();
        break;
        // Fim: Lista de Participantes não Pagos na Folha (Exportar FPS950)

    // Inicio: Rejeitados do FPS900 (Importar FPS959)
    case 5:
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAErrosPasep959.class.php");
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAOcorrenciaDetalhe959.class.php");
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAOcorrenciaDetalhe910.class.php");
        $obTIMAErrosPasep959 = new TIMAErrosPasep959();
        $obTIMAOcorrenciaDetalhe959 = new TIMAOcorrenciaDetalhe959();
        $obTIMAOcorrenciaDetalhe910 = new TIMAOcorrenciaDetalhe910();
        $obTIMAErrosPasep959->excluirTodos();

        $stCaminho = $_FILES["stCaminho"]["tmp_name"];
        $arquivoImportacao = new ArquivoCSV( $stCaminho );
        $obErro = $arquivoImportacao->Abrir('r');
        while ( !feof( $arquivoImportacao->reArquivo ) ) {
            $arLinhas = $arquivoImportacao->LerLinha();
            switch ($arLinhas[0][0]) {
                case 1:
                    //Verifica se o arquivo a ser lido e o arquivo 959
                    $stNomeInternoArquivo = trim(substr($arLinhas[0],1,7));

                    if (trim($stNomeInternoArquivo) === "FPS959" || trim($stNomeInternoArquivo) === "FPSF959") {
                        $stVetorOcorrencia = substr($arLinhas[0],39,40);
                        $arErrosRegistroHeader = array();
                        for ($inIndex=0;$inIndex<=strlen($stVetorOcorrencia);$inIndex++) {
                            if ($stVetorOcorrencia[$inIndex] == 1) {
                                $inPosicao = $inIndex+40;
                                $arErrosRegistroHeader[] = $inPosicao;

                }
                        }
                        $arSessaoFiltroRelatorio["arErrosRegistroHeader"] = $arErrosRegistroHeader;
                    } else {
            $obErro->setDescricao("Arquivo a ser importado não corresponde a um arquivo FPSF959 válido. Verifique o arquivo importado.");
                        break 2;
                    }
                    break;
                case 2:
            $inPisPasep = trim(substr($arLinhas[0],7,11));

            $stFiltro = " AND trim(translate(sw_cgm_pessoa_fisica.servidor_pis_pasep,'.,-','')) = trim('".$inPisPasep."')";
                    $obTIMAOcorrenciaDetalhe910->recuperaDadosServidor($rsServidor,$stFiltro);

                    $stVetorOcorrencia = substr($arLinhas[0],30,40);
                    for ($inIndex=0;$inIndex<=strlen($stVetorOcorrencia);$inIndex++) {
                        if ($stVetorOcorrencia[$inIndex] == 1) {
                            $inPosicao = $inIndex+31;
                            $stFiltro = " WHERE posicao = ".$inPosicao;
                            $obTIMAOcorrenciaDetalhe959->recuperaTodos($rsOcorrencia,$stFiltro);
                            if ($rsOcorrencia->getNumLinhas() == 1) {
                                $obTIMAErrosPasep959->setDado("num_ocorrencia"	, $rsOcorrencia->getCampo("num_ocorrencia"));
                                $obTIMAErrosPasep959->setDado("registro"		, $rsServidor->getCampo("matricula"));
                                $obTIMAErrosPasep959->setDado("nome"	        , $rsServidor->getCampo("nom_cgm"));
                                $obTIMAErrosPasep959->setDado("pis_pasep"		, $rsServidor->getCampo("pis_pasep"));
                                $obTIMAErrosPasep959->inclusao();
                                $obTIMAErrosPasep959->setDado("cod_erro","");
                            }
                        }
                    }
                    break;
            }
        }
        $arquivoImportacao->Fechar();

        if (!$obErro->ocorreu()) {
            sistemaLegado::alertaAviso($pgForm,"Importação do arquivo FPS959 concluído com sucesso!","importação","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgFilt,$obErro->getDescricao(),"importação","erro", Sessao::getId(), "../");
        }
        break;
        // Fim: Rejeitados do FPS900 (Importar FPS959)

    // Inicio: Retorno Definitivo do FPS950 (Importar FPS952)
    case 6:
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAErrosPasep952.class.php");
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAOcorrenciaDetalhe952.class.php");
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAOcorrenciaDetalhe910.class.php");
        $obTIMAErrosPasep952 = new TIMAErrosPasep952();
        $obTIMAOcorrenciaDetalhe952 = new TIMAOcorrenciaDetalhe952();
        $obTIMAOcorrenciaDetalhe910 = new TIMAOcorrenciaDetalhe910();
        $obTIMAErrosPasep952->excluirTodos();

        $stCaminho = $_FILES["stCaminho"]["tmp_name"];
        $arquivoImportacao = new ArquivoCSV( $stCaminho );
        $obErro = $arquivoImportacao->Abrir('r');
        while ( !feof( $arquivoImportacao->reArquivo ) ) {
            $arLinhas = $arquivoImportacao->LerLinha();
            switch ($arLinhas[0][0]) {//NÃO FUNCIONA COMO NO FPS910
                case 1:
                    //Verifica se o arquivo a ser lido e o arquivo 952
                    $stNomeInternoArquivo = trim(substr($arLinhas[0],1,7));

                    if (trim($stNomeInternoArquivo) === "FPS952" || trim($stNomeInternoArquivo) === "FPSF952") {
                        $stVetorOcorrencia = substr($arLinhas[0],39,20);
                        $arErrosRegistroHeader = array();
                        for ($inIndex=0;$inIndex<=strlen($stVetorOcorrencia);$inIndex++) {
                            if ($stVetorOcorrencia[$inIndex] == 1) {
                                $inPosicao = $inIndex+40;
                                $arErrosRegistroHeader[] = $inPosicao;
                            }
                        }
                        $arSessaoFiltroRelatorio["arErrosRegistroHeader"] = $arErrosRegistroHeader;
                    } else {
            $obErro->setDescricao("Arquivo a ser importado não corresponde a um arquivo FPSF952 válido. Verifique o arquivo importado.");
                        break 2;
                    }
                    break;
                case 2:
            $inPisPasep = trim(substr($arLinhas[0],7,11));
            $inRegistro = trim(substr($arLinhas[0],29,15));

            $stFiltro = " AND trim(translate(sw_cgm_pessoa_fisica.servidor_pis_pasep,'.,-','')) = trim('".$inPisPasep."')";
                    $obTIMAOcorrenciaDetalhe910->recuperaDadosServidor($rsServidor,$stFiltro);

                    $stVetorOcorrencia = substr($arLinhas[0],86,20);
                    for ($inIndex=0;$inIndex<=strlen($stVetorOcorrencia);$inIndex++) {
                        if ($stVetorOcorrencia[$inIndex] == 1) {
                            $inPosicao = $inIndex+87;
                            $stFiltro = " WHERE posicao = ".$inPosicao;
                            $obTIMAOcorrenciaDetalhe952->recuperaTodos($rsOcorrencia,$stFiltro);
                            if ($rsOcorrencia->getNumLinhas() == 1) {
                                $obTIMAErrosPasep952->setDado("num_ocorrencia", $rsOcorrencia->getCampo("num_ocorrencia"));
                                $obTIMAErrosPasep952->setDado("registro"	  , $rsServidor->getCampo("matricula"));
                                $obTIMAErrosPasep952->setDado("nome"		  , $rsServidor->getCampo("nom_cgm"));
                                $obTIMAErrosPasep952->setDado("pis_pasep"	  , $rsServidor->getCampo("pis_pasep"));
                                $obTIMAErrosPasep952->inclusao();
                                $obTIMAErrosPasep952->setDado("cod_erro","");
                            }
                        }
                    }
                    break;
            }
        }
        $arquivoImportacao->Fechar();

        if (!$obErro->ocorreu()) {
            sistemaLegado::alertaAviso($pgForm,"Importação do arquivo FPS952 concluído com sucesso!","importação","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgFilt,$obErro->getDescricao(),"importação","erro", Sessao::getId(), "../");
        }
        break;
        // Fim: Retorno Definitivo do FPS950 (Importar FPS952)
}
Sessao::write('filtroRelatorio', $arSessaoFiltroRelatorio);
Sessao::encerraExcecao();
SistemaLegado::LiberaFrames();

?>
