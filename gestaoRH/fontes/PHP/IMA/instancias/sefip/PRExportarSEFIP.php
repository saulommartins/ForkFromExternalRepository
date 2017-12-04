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
    * Página de Processamento do Exportação SEFIP
    * Data de Criação: 15/01/2007

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.08.03

    $Id: PRExportarSEFIP.php 66258 2016-08-03 14:25:21Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CLA_EXPORTADOR;
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFgtsEvento.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaEvento.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculado.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculado.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculado.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorPrevidencia.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoMovSefipSaida.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAdidoCedido.class.php";
include_once CAM_GA_CGM_MAPEAMENTO."TCGMPessoaJuridica.class.php";
include_once CAM_GA_ORGAN_MAPEAMENTO.'VOrganogramaOrgaoNivel.class.php';

$obTFolhaPagamentoFgtsEvento                    = new TFolhaPagamentoFgtsEvento();
$obTFolhaPagamentoEventoCalculado               = new TFolhaPagamentoEventoCalculado();
$obTFolhaPagamentoEventoComplementarCalculado   = new TFolhaPagamentoEventoComplementarCalculado();
$obTFolhaPagamentoEventoRescisaoCalculado       = new TFolhaPagamentoEventoRescisaoCalculado();
$obTFolhaPagamentoEventoFeriasCalculado         = new TFolhaPagamentoEventoFeriasCalculado();
$obTFolhaPagamentoEventoDecimoCalculado         = new TFolhaPagamentoEventoDecimoCalculado();
$obTFolhaPagamentoPrevidenciaEvento             = new TFolhaPagamentoPrevidenciaEvento();
$obTFolhaPagamentoPeriodoMovimentacao           = new TFolhaPagamentoPeriodoMovimentacao();
$obTPessoalContratoServidorPrevidencia          = new TPessoalContratoServidorPrevidencia();
$obTPessoalContratoServidor                     = new TPessoalContratoServidor();
$obTPessoalAssentamentoGerado                   = new TPessoalAssentamentoGerado();
$obTPessoalAssentamentoMovSefipSaida            = new TPessoalAssentamentoMovSefipSaida();
$obTPessoalContratoServidorCasoCausa            = new TPessoalContratoServidorCasoCausa();
$obTPessoalAdidoCedido                          = new TPessoalAdidoCedido();
$obTCGMPessoaJuridica                           = new TCGMPessoaJuridica();

$stAcao = $request->get('stAcao');

$arSessaoLink = Sessao::read('link');
if ( !empty($arSessaoLink) )
    $stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"];

foreach ($request->getAll() as $key=>$value) {
    $stLink .= $key."=".$value."&";
}

Sessao::write('filtroRelatorio', array());

//Define o nome dos arquivos PHP
$stPrograma = "ExportarSEFIP";
$pgForm = "FM".$stPrograma.".php";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

switch ($stAcao) {
    case "exportar":
        Sessao::setTrataExcecao(true);
        Sessao::write("arContratos2", Sessao::read('arContratos'));

        $inMes = $request->get('inCodMes');
        $inMes = str_pad($inMes, 2, "0", STR_PAD_LEFT);
        $dtCompetencia = $inMes."-".$request->get("inAno");
        $stFiltroPeriodo = " AND to_char(dt_final,'mm-yyyy') = '".$dtCompetencia."'";
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltroPeriodo);
        $arCompetencia = explode("-",$rsPeriodoMovimentacao->getCampo("dt_final"));
        $boDezembro = ( $arCompetencia[1] == 12 ) ? true : false;

        $obExportador = new Exportador();
        $obExportador->setRetorno($pgForm."?inAno=".$request->get('inAno')."&inCodRecolhimentoTxt=".$request->get('inCodRecolhimentoTxt')."&inCodMes=".$request->get('inCodMes'));
        $obExportador->setNomeArquivoZip("SEFIP.zip");

        include_once CAM_GRH_IMA_MAPEAMENTO."TIMACategoriaSefip.class.php";
        $obTIMACategoriaSefip = new TIMACategoriaSefip();
        $obTIMACategoriaSefip->recuperaModalidades($rsModalidades);

        $inIndexArquivo = 1;

        Sessao::write("inTotalServidoresArquivo", 0);
        Sessao::write("inDoenca15Dias", 0);
        Sessao::write("inAcidenteTrabalho", 0);
        Sessao::write("inLicencaMaternidade", 0);
        Sessao::write("inRescisoes", 0);
        Sessao::write("nuSalarioFamilia",0);
        Sessao::write("nuTotalSalarioMaternidade",0);
        Sessao::write("nuBasePrevidenciaS13",0);
        Sessao::write("boCompetencia13",$request->get("boCompetencia13"));

        $boCompetencia13 = $request->get('boCompetencia13');

        while (!$rsModalidades->eof()) {
            $stModalidade = ($rsModalidades->getCampo("sefip") === "0") ? "" : $rsModalidades->getCampo("sefip");
            Sessao::write("stFiltroRegistroTrabalhadoresExtra", "");
            $boAdicionarFiltroExtra = false;
            $stFiltro = " AND modalidade_recolhimento.cod_modalidade = ".$rsModalidades->getCampo("cod_modalidade");
            $obTIMACategoriaSefip->recuperaRelacionamento($rsCategoriasModalidade,$stFiltro);
            $stCodCategorias = "";

            while (!$rsCategoriasModalidade->eof()) {
                $stCodCategorias .= $rsCategoriasModalidade->getCampo("cod_categoria").",";
                $rsCategoriasModalidade->proximo();
            }
            $stCodCategorias = substr($stCodCategorias,0,strlen($stCodCategorias)-1);
            if (trim($stCodCategorias)!="") {
               Sessao::write("stFiltroRegistroTrabalhadoresExtra", Sessao::read("stFiltroRegistroTrabalhadoresExtra") . " AND contrato_servidor.cod_categoria IN (".$stCodCategorias.")");
            }

            if ( $request->get('boSefipRetificadora') ) {
                $inIndexArquivo = 9;
            }

            $obExportador->addArquivo("SEFIP".$inIndexArquivo.".re");
            $obExportador->roUltimoArquivo->setTipoDocumento("SEFIP");

            include_once CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php";
            $obTEntidade = new TEntidade();
            $stFiltroEntidade  = " AND entidade.cod_entidade = ".Sessao::getCodEntidade($boTransacao);
            $stFiltroEntidade .= " AND entidade.exercicio = '".Sessao::getExercicio()."'";
            $obTEntidade->recuperaInformacoesCGMEntidade($rsCGMEntidade,$stFiltroEntidade);

            $stNomePrefeitura = removeAcentos($rsCGMEntidade->getCampo("nom_cgm"));
            $stLogradouro     = removeAcentos($rsCGMEntidade->getCampo("logradouro")." ".$rsCGMEntidade->getCampo("numero"));
            $stBairro         = removeAcentos($rsCGMEntidade->getCampo("bairro"));
            $inCep            = $rsCGMEntidade->getCampo("cep");
            $inCNPJ           = $rsCGMEntidade->getCampo("cnpj");

            include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
            $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
            $obTAdministracaoConfiguracao->setDado('exercicio', Sessao::getExercicio());
            $obTAdministracaoConfiguracao->pegaConfiguracao($stPessoaContato,"nome_pessoa_contato_sefip".Sessao::getEntidade());
            $obTAdministracaoConfiguracao->pegaConfiguracao($stDDDContato,"DDD_pessoa_contato_sefip".Sessao::getEntidade());
            $obTAdministracaoConfiguracao->pegaConfiguracao($stTelefoneContato,"telefone_pessoa_contato_sefip".Sessao::getEntidade());
            $obTAdministracaoConfiguracao->pegaConfiguracao($stEmailContato,"mail_pessoa_contato_sefip".Sessao::getEntidade());
            $obTAdministracaoConfiguracao->pegaConfiguracao($stCodigoOutrasEntidades,"codigo_outras_entidades_sefip".Sessao::getEntidade());

            ##########HEADER ARQUIVO
            $arHeaderArquivo = array();
            $arHeaderArquivo[0]['tipo_registro']                                 = "00";
            $arHeaderArquivo[0]['brancos']                                       = "";
            $arHeaderArquivo[0]['tipo_remessa']                                  = $request->get("inTipoRemessa");
            $arHeaderArquivo[0]['tipo_inscricao']                                = 1;
            $arHeaderArquivo[0]['inscricao_resp']                                = $inCNPJ;
            $arHeaderArquivo[0]['nome_resp']                                     = $stNomePrefeitura;
            $arHeaderArquivo[0]['nome_pessoa_contato']                           = removeAcentos($stPessoaContato);
            $arHeaderArquivo[0]['logradouro']                                    = str_replace(".","",$stLogradouro);
            $arHeaderArquivo[0]['bairro']                                        = $stBairro;
            $arHeaderArquivo[0]['cep']                                           = $inCep;
            include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php";
            $obTMunicipio = new TMunicipio();
            $obTMunicipio->setDado("cod_municipio",$rsCGMEntidade->getCampo("cod_municipio"));
            $obTMunicipio->setDado("cod_uf",$rsCGMEntidade->getCampo("cod_uf"));
            $obTMunicipio->recuperaPorChave($rsMunicipio);
            $arHeaderArquivo[0]['cidade']                                        = removeAcentos($rsMunicipio->getCampo("nom_municipio"));
            include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php";
            $obTUF = new TUF();
            $obTUF->setDado("cod_uf",$rsCGMEntidade->getCampo("cod_uf"));
            $obTUF->recuperaPorChave($rsUf);
            $arHeaderArquivo[0]['unid_federal']                                  = $rsUf->getCampo("sigla_uf");
            $arHeaderArquivo[0]['fone_contato']                                  = $stDDDContato.$stTelefoneContato;
            $arHeaderArquivo[0]['email']                                         = $stEmailContato;
            if ($boCompetencia13) {
                $dtCompetencia = $request->get("inAno")."13";
            } else {
                $inMes = $request->get('inCodMes');
                $inMes = str_pad($inMes, 2, "0", STR_PAD_LEFT);

                $dtCompetencia = $request->get("inAno").$inMes;
            }

            if ( $request->get('boSefipRetificadora') ) {
                $stModalidade = 9;
            }

            $arHeaderArquivo[0]['competencia']                                   = $dtCompetencia;
            $arHeaderArquivo[0]['recolhimento']                                  = $request->get("inCodRecolhimento");
            $arHeaderArquivo[0]['ind_recolhimento']                              = $request->get("inCodIndicadorRecolhimento");
            $arHeaderArquivo[0]['modalidade']                                    = $stModalidade;
            $arHeaderArquivo[0]['data_recolhimento_fgts']                        = str_replace("/","",$request->get("dtRecolhimentoFGTS"));
            $arHeaderArquivo[0]['ind_recolhimento_previdencia']                  = $request->get("inCodIndicadorRecolhimentoPrevidencia");
            $arHeaderArquivo[0]['data_recolhimento_previdencia']                 = str_replace("/","",$request->get("dtRecolhimentoPrevidencia"));
            $arHeaderArquivo[0]['indice_recolhimento']                           = "";
            include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
            $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
            $obTAdministracaoConfiguracao->setDado( "cod_modulo", 40 );
            $obTAdministracaoConfiguracao->setDado( "exercicio" , Sessao::getExercicio() );
            $obTAdministracaoConfiguracao->setDado( "parametro" , "tipo_inscricao"        );
            $obTAdministracaoConfiguracao->recuperaPorChave($rsTipoInscricao);
            $arHeaderArquivo[0]['tipo_inscricao_fornecedor']                     = $rsTipoInscricao->getCampo("valor");
            $obTAdministracaoConfiguracao->setDado( "parametro" , "inscricao_fornecedor".Sessao::getEntidade() );
            $obTAdministracaoConfiguracao->recuperaPorChave($rsInscricao);
            switch ($rsTipoInscricao->getCampo("valor")) {
                case 1:
                    include_once CAM_GA_CGM_MAPEAMENTO."TCGMPessoaJuridica.class.php";
                    $TCGMPessoaJuridica =  new TCGMPessoaJuridica();
                    $TCGMPessoaJuridica->setDado("numcgm",$rsInscricao->getCampo("valor"));
                    $TCGMPessoaJuridica->recuperaPorChave($rsCgm);
                    $stInscricao = $rsCgm->getCampo("cnpj");
                    break;
                case 2:
                    $stInscricao = $rsInscricao->getCampo("valor");
                    break;
                case 3:
                    include_once CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php";
                    $obTCGMPessoaFisica =  new TCGMPessoaFisica();

                    $obTCGMPessoaFisica->setDado("numcgm",$rsInscricao->getCampo("valor"));
                    $obTCGMPessoaFisica->recuperaPorChave($rsCgm);
                    $stInscricao = $rsCgm->getCampo("cpf");
                    break;
            }
            $arHeaderArquivo[0]['inscricao_fornecedor']                          = $stInscricao;
            $arHeaderArquivo[0]['brancos']                                       = "";
            $arHeaderArquivo[0]['final']                                         = "*";
            $rsHeaderArquivo = new RecordSet();
            $rsHeaderArquivo->preenche($arHeaderArquivo);

            $obExportador->roUltimoArquivo->addBloco($rsHeaderArquivo);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(51);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_remessa");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_inscricao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inscricao_resp");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_resp");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_pessoa_contato");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("logradouro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("bairro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unid_federal");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fone_contato");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("email");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("competencia");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("recolhimento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_recolhimento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("modalidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_recolhimento_fgts");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ind_recolhimento_previdencia");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_recolhimento_previdencia");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indice_recolhimento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_inscricao_fornecedor");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inscricao_fornecedor");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(18);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("final");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            ##########HEADER ARQUIVO

            include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php";
            include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php";
            include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculado.class.php";
            include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculado.class.php";
            include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculado.class.php";
            include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSalarioFamiliaEvento.class.php";

            $obTFolhaPagamentoSalarioFamiliaEvento = new TFolhaPagamentoSalarioFamiliaEvento();
            $stFiltroSalEvento = "   AND fsfe.cod_regime_previdencia = 1 \n";
            $stFiltroSalEvento .= "  AND fsfe.cod_tipo = 1               \n";
            $obTFolhaPagamentoSalarioFamiliaEvento->recuperaRelacionamento($rsSalarioEvento,$stFiltroSalEvento);

            ##########TOMADOR DE SERVIÇO
            $inCodRecolhimento = $request->get('inCodRecolhimento');
            if ( in_array($inCodRecolhimento,array(130,135,150,155,211,317,337,608)) ) {
                include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAdidoCedido.class.php";
                $obTPessoalAdidoCedido = new TPessoalAdidoCedido();
                $obTPessoalAdidoCedido->recuperaAdidosCedidosSEFIP($rsAdidoCedido);
                $arTomadorServico = array();
                $inIndex = 0;
                $obTFolhaPagamentoEventoCalculado               = new TFolhaPagamentoEventoCalculado();
                $obTFolhaPagamentoEventoComplementarCalculado   = new TFolhaPagamentoEventoComplementarCalculado();
                $obTFolhaPagamentoEventoDecimoCalculado         = new TFolhaPagamentoEventoDecimoCalculado();
                $obTFolhaPagamentoEventoFeriasCalculado         = new TFolhaPagamentoEventoFeriasCalculado();
                $obTFolhaPagamentoEventoRescisaoCalculado       = new TFolhaPagamentoEventoRescisaoCalculado();
                $stCodContratosAdidosCedidos = "";
                while (!$rsAdidoCedido->eof()) {
                    $stFiltroAdidoCedido = " AND adido_cedido.cgm_cedente_cessionario = ".$rsAdidoCedido->getCampo("cgm_cedente_cessionario");
                    $obTPessoalAdidoCedido->recuperaAdidosCedidosSEFIPContratos($rsAdidoCedidoContratos,$stFiltroAdidoCedido);
                    if ($boCompetencia13) {
                        $nuTotalSalarioFamilia = 0;
                    } else {
                        while (!$rsAdidoCedidoContratos->eof()) {
                            $arContratosAdidosCedidosTomador[$rsAdidoCedidoContratos->getCampo("cod_contrato")] = $rsAdidoCedido->getCampo("cnpj");
                            $stCodContratosAdidosCedidos .= $rsAdidoCedidoContratos->getCampo("cod_contrato").",";
                            $stFiltroEvento  = " AND evento.cod_evento = ".$rsSalarioEvento->getCampo("cod_evento");
                            $stFiltroEvento .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                            $stFiltroEvento .= " AND cod_contrato = ".$rsAdidoCedidoContratos->getCampo("cod_contrato");
                            $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventoCalculados,$stFiltroEvento);
                            $arTotalSalarioFamilia = $rsEventoCalculados->getSomaCampo("valor");
                            $nuTotalSalarioFamilia += $arTotalSalarioFamilia["valor"];
                            $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventosCalculados($rsEventoCalculados,$stFiltroEvento);
                            $arTotalSalarioFamilia = $rsEventoCalculados->getSomaCampo("valor");
                            $nuTotalSalarioFamilia += $arTotalSalarioFamilia["valor"];
                            $obTFolhaPagamentoEventoDecimoCalculado->recuperaEventosDecimoCalculado($rsEventoCalculados,$stFiltroEvento);
                            $arTotalSalarioFamilia = $rsEventoCalculados->getSomaCampo("valor");
                            $nuTotalSalarioFamilia += $arTotalSalarioFamilia["valor"];
                            $obTFolhaPagamentoEventoFeriasCalculado->recuperaEventosCalculados($rsEventoCalculados,$stFiltroEvento);
                            $arTotalSalarioFamilia = $rsEventoCalculados->getSomaCampo("valor");
                            $nuTotalSalarioFamilia += $arTotalSalarioFamilia["valor"];
                            $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventoRescisaoCalculado($rsEventoCalculados,$stFiltroEvento);
                            $arTotalSalarioFamilia = $rsEventoCalculados->getSomaCampo("valor");
                            $nuTotalSalarioFamilia += $arTotalSalarioFamilia["valor"];
                            $rsAdidoCedidoContratos->proximo();
                        }
                        Sessao::write("nuSalarioFamilia", Sessao::read("nuSalarioFamilia") + $nuTotalSalarioFamilia);
                    }
                    $arTomadorServico[$inIndex]['tipo_registro']                                 = 20;
                    $arTomadorServico[$inIndex]['tipo_inscricao']                                = 1;
                    $arTomadorServico[$inIndex]['inscricao_empresa']                             = $inCNPJ;
                    $arTomadorServico[$inIndex]['tipo_inscricao_tomador']                        = 1;
                    $arTomadorServico[$inIndex]['inscricao_tomador']                             = $rsAdidoCedido->getCampo("cnpj");
                    $arTomadorServico[$inIndex]['zeros']                                         = 0;
                    $arTomadorServico[$inIndex]['nome_tomador']                                  = removeAcentos($rsAdidoCedido->getCampo("nom_cgm"));
                    $arTomadorServico[$inIndex]['logradouro']                                    = removeAcentos($rsAdidoCedido->getCampo("logradouro")." ".$rsAdidoCedido->getCampo("numero")." ".$rsAdidoCedido->getCampo("complemento"));
                    $arTomadorServico[$inIndex]['bairro']                                        = removeAcentos($rsAdidoCedido->getCampo("bairro"));
                    $arTomadorServico[$inIndex]['cep']                                           = removeAcentos($rsAdidoCedido->getCampo("cep"));
                    $arTomadorServico[$inIndex]['cidade']                                        = removeAcentos($rsAdidoCedido->getCampo("nom_municipio"));
                    $arTomadorServico[$inIndex]['unid_federal']                                  = $rsAdidoCedido->getCampo("sigla");
                    $arTomadorServico[$inIndex]['gps']                                           = $request->get("gps");
                    $arTomadorServico[$inIndex]['salario_familia']                               = str_replace(".","",number_format($nuTotalSalarioFamilia,2,".",""));
                    $arTomadorServico[$inIndex]['contribuicao']                                  = 0;
                    $arTomadorServico[$inIndex]['indicador']                                     = 0;
                    $arTomadorServico[$inIndex]['valor_devido']                                  = 0;
                    $arTomadorServico[$inIndex]['valor_retencao']                                = 0;
                    $arTomadorServico[$inIndex]['valor_fatura']                                  = 0;
                    $arTomadorServico[$inIndex]['zeros']                                         = 0;
                    $arTomadorServico[$inIndex]['brancos']                                       = "";
                    $arTomadorServico[$inIndex]['final']                                         = "*";
                    $inIndex++;
                    $rsAdidoCedido->proximo();
                }
                $stCodContratosAdidosCedidos = substr($stCodContratosAdidosCedidos,0,strlen($stCodContratosAdidosCedidos)-1);
            }
            ##########TOMADOR DE SERVIÇO

            ##########HEADER EMPRESA
            $arHeaderEmpresa = array();
            $arHeaderEmpresa[0]['tipo_registro']                                 = 10;
            $arHeaderEmpresa[0]['tipo_inscricao']                                = 1;
            $arHeaderEmpresa[0]['inscricao_empresa']                             = $inCNPJ;
            $arHeaderEmpresa[0]['zeros']                                         = 0;
            $arHeaderEmpresa[0]['nome_empresa']                                  = $stNomePrefeitura;
            $arHeaderEmpresa[0]['logradouro']                                    = str_replace(".","",$stLogradouro);
            $arHeaderEmpresa[0]['bairro']                                        = $stBairro;
            $arHeaderEmpresa[0]['cep']                                           = $inCep;
            include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php";
            $obTMunicipio = new TMunicipio();
            $obTMunicipio->setDado("cod_municipio",$rsCGMEntidade->getCampo("cod_municipio"));
            $obTMunicipio->setDado("cod_uf",$rsCGMEntidade->getCampo("cod_uf"));
            $obTMunicipio->recuperaPorChave($rsMunicipio);
            $arHeaderEmpresa[0]['cidade']                                        = removeAcentos($rsMunicipio->getCampo("nom_municipio"));
            include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php";
            $obTUF = new TUF();
            $obTUF->setDado("cod_uf",$rsCGMEntidade->getCampo("cod_uf"));
            $obTUF->recuperaPorChave($rsUf);
            $arHeaderEmpresa[0]['unid_federal']                                  = $rsUf->getCampo("sigla_uf");
            $arHeaderEmpresa[0]['fone']                                          = (trim($rsCGMEntidade->getCampo("fone_residencial")) != "") ? trim($rsCGMEntidade->getCampo("fone_residencial")) : trim($rsCGMEntidade->getCampo("fone_comercial"));
            $arHeaderEmpresa[0]['indicador_alteracao']                           = "n";
            $arHeaderEmpresa[0]['cnae_fiscal']                                   = preg_replace("/[A-Za-z.\/-]/","",$request->get("cnae_fiscal"));
            $arHeaderEmpresa[0]['indicador_alteracao_cnae']                      = "n";
            include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaRegimeRat.class.php";
            $obTFolhaPagamentoPrevidenciaRegimeRat = new TFolhaPagamentoPrevidenciaRegimeRat();
            $obTFolhaPagamentoPrevidenciaRegimeRat->recuperaAliquotaSefip($rsRat);
            $arHeaderEmpresa[0]['aliquota_rat']                                  = str_replace('.','',$rsRat->getCampo("aliquota_rat"));
            Sessao::write("aliquota_rat", $rsRat->getCampo("aliquota_rat"));
            $arHeaderEmpresa[0]['centralizacao']                                 = $request->get("centralizacao");
            $arHeaderEmpresa[0]['simples']                                       = 1;
            $arHeaderEmpresa[0]['fpas']                                          = $request->get("fpas");
            $dtCompetenciaFPas = $request->get("inAno")."-".$inMes;
            $arHeaderEmpresa[0]['outras_entidades']                              = ($request->get("fpas") == 582 and $dtCompetenciaFPas >= "1998-10" ) ? $stCodigoOutrasEntidades : "";
            $arHeaderEmpresa[0]['gps']                                           = $request->get("gps");
            $arHeaderEmpresa[0]['filantropia']                                   = "";

            $obTFolhaPagamentoEventoCalculado               = new TFolhaPagamentoEventoCalculado();
            $obTFolhaPagamentoEventoComplementarCalculado   = new TFolhaPagamentoEventoComplementarCalculado();
            $obTFolhaPagamentoEventoDecimoCalculado         = new TFolhaPagamentoEventoDecimoCalculado();
            $obTFolhaPagamentoEventoFeriasCalculado         = new TFolhaPagamentoEventoFeriasCalculado();
            $obTFolhaPagamentoEventoRescisaoCalculado       = new TFolhaPagamentoEventoRescisaoCalculado();
            if ($boCompetencia13) {
                $nuTotalSalarioFamilia = 0;
            } else {
                $stFiltroEvento  = " AND evento.cod_evento = ".$rsSalarioEvento->getCampo("cod_evento");
                $stFiltroEvento .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltroEvento .= " AND cod_contrato IN (".retornaContratosDoFiltro().")";
                if ($stCodContratosAdidosCedidos != "") {
                    $stFiltroEvento .= " AND cod_contrato NOT IN (".$stCodContratosAdidosCedidos.")";
                }
                $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventoCalculados,$stFiltroEvento);
                $arTotalSalarioFamilia = $rsEventoCalculados->getSomaCampo("valor");
                $nuTotalSalarioFamilia += $arTotalSalarioFamilia["valor"];
                $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventosCalculados($rsEventoCalculados,$stFiltroEvento);
                $arTotalSalarioFamilia = $rsEventoCalculados->getSomaCampo("valor");
                $nuTotalSalarioFamilia += $arTotalSalarioFamilia["valor"];
                $obTFolhaPagamentoEventoDecimoCalculado->recuperaEventosDecimoCalculado($rsEventoCalculados,$stFiltroEvento);
                $arTotalSalarioFamilia = $rsEventoCalculados->getSomaCampo("valor");
                $nuTotalSalarioFamilia += $arTotalSalarioFamilia["valor"];
                $obTFolhaPagamentoEventoFeriasCalculado->recuperaEventosCalculados($rsEventoCalculados,$stFiltroEvento);
                $arTotalSalarioFamilia = $rsEventoCalculados->getSomaCampo("valor");
                $nuTotalSalarioFamilia += $arTotalSalarioFamilia["valor"];
                $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventoRescisaoCalculado($rsEventoCalculados,$stFiltroEvento);
                $arTotalSalarioFamilia = $rsEventoCalculados->getSomaCampo("valor");
                $nuTotalSalarioFamilia += $arTotalSalarioFamilia["valor"];
                Sessao::write("nuSalarioFamilia", Sessao::read("nuSalarioFamilia") + $nuTotalSalarioFamilia);
            }

            $arHeaderEmpresa[0]['salario_familia']                               = str_replace('.','',number_format($nuTotalSalarioFamilia,2,'.',''));
            $nuTotalSalarioFamilia = 0;

            $nuTotalSalarioMaternidade = 0;
            $stFiltroEventoMaternidade  = " AND assentamento_assentamento.cod_motivo = 7";
            $stFiltroEventoMaternidade .= " AND assentamento_gerado_contrato_servidor.cod_contrato IN (".retornaContratosDoFiltro().")";
            $obTPessoalAssentamentoGerado->recuperaEventosAssentamento($rsEventoMaternidade,$stFiltroEventoMaternidade);
            if ($rsEventoMaternidade->getNumLinhas() > 0) {
                $arEventoMaternidade = array();
                while (!$rsEventoMaternidade->eof()) {
                    $arEventoMaternidade[] = $rsEventoMaternidade->getCampo("cod_evento");
                    $rsEventoMaternidade->proximo();
                }
                $arEventoMaternidade = array_unique($arEventoMaternidade);
                $stEventoMaternidade = implode(",",$arEventoMaternidade);
                $stFiltroEvento  = " AND evento.cod_evento IN (".$stEventoMaternidade.")";
                $stFiltroEvento .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltroEvento .= " AND cod_contrato IN (".retornaContratosDoFiltro().")";

                $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventoCalculados,$stFiltroEvento);
                $arTotalSalarioMaternidade = $rsEventoCalculados->getSomaCampo("valor");
                $nuTotalSalarioMaternidade += $arTotalSalarioMaternidade["valor"];
                $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventosCalculados($rsEventoCalculados,$stFiltroEvento);
                $arTotalSalarioMaternidade = $rsEventoCalculados->getSomaCampo("valor");
                $nuTotalSalarioMaternidade += $arTotalSalarioMaternidade["valor"];
                $obTFolhaPagamentoEventoDecimoCalculado->recuperaEventosDecimoCalculado($rsEventoDecimoCalculados,$stFiltroEvento);
                $arTotalSalarioMaternidade = $rsEventoDecimoCalculados->getSomaCampo("valor");
                $nuTotalSalarioMaternidade += $arTotalSalarioMaternidade["valor"];
                $obTFolhaPagamentoEventoFeriasCalculado->recuperaEventosCalculados($rsEventoCalculados,$stFiltroEvento);
                $arTotalSalarioMaternidade = $rsEventoCalculados->getSomaCampo("valor");
                $nuTotalSalarioMaternidade += $arTotalSalarioMaternidade["valor"];
                $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventoRescisaoCalculado($rsEventoCalculados,$stFiltroEvento);
                $arTotalSalarioMaternidade = $rsEventoCalculados->getSomaCampo("valor");
                $nuTotalSalarioMaternidade += $arTotalSalarioMaternidade["valor"];
            }

            if ($boCompetencia13) {
                $arHeaderEmpresa[0]['salario_maternidade']                           = 0;
            } else {
                $arHeaderEmpresa[0]['salario_maternidade']                           = str_replace('.','',number_format($nuTotalSalarioMaternidade,2,'.',''));
                Sessao::write("nuTotalSalarioMaternidade", Sessao::read("nuTotalSalarioMaternidade") + $nuTotalSalarioMaternidade);
            }
            $arHeaderEmpresa[0]['contribuicao']                                  = 0;
            $arHeaderEmpresa[0]['indicador']                                     = 0;
            $arHeaderEmpresa[0]['valor_devido']                                  = 0;
            $arHeaderEmpresa[0]['banco']                                         = "";
            $arHeaderEmpresa[0]['agencia']                                       = "";
            $arHeaderEmpresa[0]['conta_corrente']                                = "";
            $arHeaderEmpresa[0]['zeros']                                         = 0;
            $arHeaderEmpresa[0]['brancos']                                       = "";
            $arHeaderEmpresa[0]['final']                                         = "*";

            ##########REGISTRO INFORMAÇÕES ADICIONAIS DO RECOLHIMENTO DA EMPRESA
            if ($boCompetencia13) {
                $nuTotalSalarioMaternidade13 = 0;
                if (is_object($rsEventoDecimoCalculados)) {
                    while (!$rsEventoDecimoCalculados->eof()) {
                        $nuTotalSalarioMaternidade13 += $rsEventoDecimoCalculados->getCampo("valor");
                        $rsEventoDecimoCalculados->proximo();
                    }
                }

                $arInformacoesAdicionais = array();
                $arInformacoesAdicionais[0]['tipo_registro']                         = 12;
                $arInformacoesAdicionais[0]['tipo_inscricao']                        = 1;
                $arInformacoesAdicionais[0]['inscricao_empresa']                     = $inCNPJ;
                $arInformacoesAdicionais[0]['zeros']                                 = 0;
                $arInformacoesAdicionais[0]['deducao_13']                            = str_replace('.','',number_format($nuTotalSalarioMaternidade13,2,'.',''));
                $arInformacoesAdicionais[0]['brancos']                               = "";
                $arInformacoesAdicionais[0]['final']                                 = "*";
            }

            ##########REGISTRO DO TRABALHADOR
            $stFiltroEventoFGTS = " AND cod_tipo = 3";
            $obTFolhaPagamentoFgtsEvento->recuperaRelacionamento($rsEventoFgts,$stFiltroEventoFGTS);

            if ($boAdicionarFiltroExtra === false) {
                $stFiltroRegistroTrabalhadores = processarFiltro($obTPessoalContratoServidor);
            } else {
                $stFiltroRegistroTrabalhadores = Sessao::read("stFiltroRegistroTrabalhadoresExtra");
            }

            if (Sessao::getEntidade() == '') {
                $obTPessoalContratoServidor->setDado("entidade","" );
            } else {
                $obTPessoalContratoServidor->setDado("entidade",Sessao::getEntidade() );
            }

            $obTPessoalContratoServidor->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao') );
            $obTPessoalContratoServidor->recuperaRegistroTrabalhadoresSEFIP($rsContratos,$stFiltroRegistroTrabalhadores,"servidor_pis_pasep,dt_admissao_n_formatado,cod_categoria");

            ##########movimentação DO TRABALHADOR
            $inCodRecolhimento = $request->get('inCodRecolhimento');
            if( !$boCompetencia13 AND !(in_array($inCodRecolhimento,array(145,307,317,327,337,345)))){
                include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php";
                include_once CAM_GRH_PES_MAPEAMENTO."TPessoalCategoriaMovimento.class.php";
                include_once CAM_GRH_PES_MAPEAMENTO."TPessoalMovSefipSaidaMovSefipRetorno.class.php";
                $obTPessoalAssentamentoGerado = new TPessoalAssentamentoGerado();
                $obTPessoalCategoriaMovimentacao = new TPessoalCategoriaMovimento();
                $obTPessoalMovSefipSaidaMovSefipRetorno = new TPessoalMovSefipSaidaMovSefipRetorno();
                $inIndex = 0;
                $arMovimentacaoTrabalhador = array();
                $arCompetencia = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));
                $stCompetencia1 = $arCompetencia[2]."-".$arCompetencia[1];
                $stCompetencia2 = date("Y-m",mktime(0,0,0,$arCompetencia[1]-1,$arCompetencia[0],$arCompetencia[2]));

                include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoMovSefipSaida.class.php";
                $obTPessoalAssentamentoMovSefipSaida = new TPessoalAssentamentoMovSefipSaida();

                include_once CAM_GRH_PES_MAPEAMENTO."TPessoalCausaRescisao.class.php";
                $obTPessoalCausaRescisao = new TPessoalCausaRescisao();

                while (!$rsContratos->eof()) {
                    $stFiltroAssentamento  = " AND assentamento_gerado_contrato_servidor.cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltroAssentamento .= " AND (cod_tipo = 2 OR cod_tipo = 3)\n";
                    $obTPessoalAssentamentoGerado->setDado("competencia1",$stCompetencia1);
                    $obTPessoalAssentamentoGerado->setDado("competencia2",$stCompetencia2);
                    $obTPessoalAssentamentoGerado->recuperaAssentamentoSEFIP($rsAssentamentoSEFIP,$stFiltroAssentamento);

                    $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventoRescisaoCalculado($rsEventoRescisaoCalculado,$stFiltro);

                    if( $rsAssentamentoSEFIP->getNumLinhas() != -1 and
                        in_array($rsContratos->getCampo("cod_categoria"),array("01","02","03","04","05","06","07","11","12","19","20","21","26"))){

                        $arPeriodoMoviInicial = explode("/",$rsPeriodoMovimentacao->getCampo("dt_inicial"));
                        $arPeriodoMoviFinal   = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));
                        $dtPeriodoMoviInicial = $arPeriodoMoviInicial[2]."-".$arPeriodoMoviInicial[1]."-".$arPeriodoMoviInicial[0];
                        $dtPeriodoMoviFinal   = $arPeriodoMoviFinal[2]."-".$arPeriodoMoviFinal[1]."-".$arPeriodoMoviFinal[0];

                        while (!$rsAssentamentoSEFIP->eof()) {

                            if (!($rsAssentamentoSEFIP->getCampo("cod_tipo") == 3 and $rsEventoRescisaoCalculado->getNumLinhas() == -1)) {

                                $inCodSefipSaida = "";
                                if ($rsAssentamentoSEFIP->getCampo("cod_tipo") == 3) {
                                    //Busca o cod_sefip_saida para assentamentos do tipo afastamento permanente
                                    $stFiltroSefipSaida = " AND contrato_servidor_caso_causa.cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                                    $obTPessoalCausaRescisao->recuperaSefipRescisao($rsSefipSaida,$stFiltroSefipSaida);
                                    $inCodSefipSaida = $rsSefipSaida->getCampo("cod_sefip_saida");
                                }
                                if ($rsAssentamentoSEFIP->getCampo("cod_tipo") == 2) {
                                    //Busca o cod_sefip_saida para assentamentos do tipo afastamento temporário
                                    $stFiltroSefipSaida = " AND PAS.cod_assentamento = ".$rsAssentamentoSEFIP->getCampo("cod_assentamento");
                                    $obTPessoalAssentamentoMovSefipSaida->recuperaRelacionamento($rsSefipSaida,$stFiltroSefipSaida);
                                    $inCodSefipSaida = $rsSefipSaida->getCampo("cod_sefip_saida");
                                }

                                if ($inCodSefipSaida != "") {
                                    $stFiltroSefipRetorno = " WHERE cod_sefip = ".$inCodSefipSaida;
                                    include_once CAM_GRH_PES_MAPEAMENTO."TPessoalSefip.class.php";
                                    $obTPessoalSefip = new TPessoalSefip;
                                    $obTPessoalSefip->recuperaTodos($rsSefip,$stFiltroSefipRetorno);
                                    $inNumSefip = trim($rsSefip->getCampo("num_sefip"));

                                    #indicativo_recolhimento_fgts
                                    $stFiltroCategoria  = " WHERE cod_sefip_saida = ".$inCodSefipSaida;
                                    $stFiltroCategoria .= "   AND cod_categoria = ".$rsContratos->getCampo("cod_categoria");
                                    $obTPessoalCategoriaMovimentacao->recuperaTodos($rsCategoriaMovimentacao,$stFiltroCategoria);

                                    $boPeriodoInicial = false;
                                    $boPeriodoFinal   = false;
                                    if ($rsAssentamentoSEFIP->getCampo("cod_tipo") == 3) {
                                        $boPeriodoInicial = true;
                                    } else {
                                        #Periodo inicial compreendido dentro do periodo de movimentação deverá entrar na sefip
                                        if( $rsAssentamentoSEFIP->getCampo("periodo_inicial") >= $dtPeriodoMoviInicial and
                                            $rsAssentamentoSEFIP->getCampo("periodo_inicial") <= $dtPeriodoMoviFinal ){
                                            $boPeriodoInicial = true;
                                        }
                                        #Periodo final compreendido dentro do periodo de movimentação deverá entrar na sefip
                                        #também deverá entrar o periodo inicial na sefip
                                        if( $rsAssentamentoSEFIP->getCampo("periodo_final") >= $dtPeriodoMoviInicial and
                                            $rsAssentamentoSEFIP->getCampo("periodo_final") <= $dtPeriodoMoviFinal ){
                                            $boPeriodoInicial = true;
                                            $boPeriodoFinal   = true;
                                        }
                                        #Periodo inicial não compeendido dentro do periodo de movimentação e menor que a data inicial do periodo de movimentação
                                        #Periodo final não compeendido dentro do periodo de movimentação e maior que a data final do periodo de movimentação
                                        #Timestamp do assentamento gerado compeendido dentro do periodo de movimentação
                                        $arTimestamp = explode(" ",$rsAssentamentoSEFIP->getCampo("timestamp"));
                                        if( $rsAssentamentoSEFIP->getCampo("periodo_inicial") < $dtPeriodoMoviInicial and
                                            $rsAssentamentoSEFIP->getCampo("periodo_inicial") < $dtPeriodoMoviFinal and
                                            $rsAssentamentoSEFIP->getCampo("periodo_final") > $dtPeriodoMoviInicial and
                                            $rsAssentamentoSEFIP->getCampo("periodo_final") > $dtPeriodoMoviFinal and
                                            $arTimestamp[0] >= $dtPeriodoMoviInicial and
                                            $arTimestamp[0] <= $dtPeriodoMoviFinal
                                            ){
                                            $boPeriodoInicial = true;
                                        }
                                        if( $rsSefip->getCampo("repetir_mensal") == "t" and
                                            $dtPeriodoMoviFinal >= $rsAssentamentoSEFIP->getCampo("periodo_inicial") and
                                            $dtPeriodoMoviFinal <= $rsAssentamentoSEFIP->getCampo("periodo_final")
                                            ){
                                            $boPeriodoInicial = true;
                                        }
                                    }
                                    if ($boPeriodoInicial or $boPeriodoFinal) {
                                        $arIncluidoMovimentacao[$rsContratos->getCampo("cod_contrato")] = true;

                                        #Contador para Doença +15 dias
                                        if ($inNumSefip == "P1") {
                                            Sessao::write("inDoenca15Dias", Sessao::read("inDoenca15Dias")+1);
                                        }

                                        #Contador para Acidente trabalho
                                        if ( in_array($inNumSefip,array('O2','O3','U2','Z2','Z3','O1')) ) {
                                            Sessao::write("inAcidenteTrabalho", Sessao::read("inAcidenteTrabalho")+1);
                                        }

                                        #Contador para Licença Maternidade
                                        if ( in_array($inNumSefip,array("Q2","Q4","Q5","Q6","Z1","Q1")) ) {
                                            Sessao::write("inLicencaMaternidade", Sessao::read("inLicencaMaternidade")+1);
                                        }

                                        #Contador para movimentação Definitiva
                                        if ($rsAssentamentoSEFIP->getCampo("cod_tipo") == 3) {
                                            Sessao::write("inRescisoes", Sessao::read("inRescisoes")+1);
                                        }
                                    }

                                    #Campo 12 - Indicativo de recolhimento do FGTS
                                    $arTemp = explode("-",$dtPeriodoMoviFinal);
                                    $stIndicativoRecolhimentoFgts = "";
                                    if (($arTemp[0]."-".$arTemp[1]) > "1998-01") {
                                        if (($rsCategoriaMovimentacao->getCampo("indicativo") == "S" OR $rsCategoriaMovimentacao->getCampo("indicativo") == "N") AND in_array($inNumSefip,array("I1","I2","I3","I4","L"))) {
                                            $stIndicativoRecolhimentoFgts = $rsCategoriaMovimentacao->getCampo("indicativo");
                                        }
                                    } else {
                                        if ($rsCategoriaMovimentacao->getCampo("indicativo") != "S" AND $rsCategoriaMovimentacao->getCampo("indicativo") != "N") {
                                            $stIndicativoRecolhimentoFgts = $rsCategoriaMovimentacao->getCampo("indicativo");
                                        }
                                    }
                                    if ( ($rsCategoriaMovimentacao->getCampo("indicativo") == "S" OR $rsCategoriaMovimentacao->getCampo("indicativo") == "N" OR $rsCategoriaMovimentacao->getCampo("indicativo") == "C") AND $boCompetencia13) {
                                        $stIndicativoRecolhimentoFgts = "";
                                    }

                                    //Verifica se deve ser informado a data de admissão - Só deverá ser informada a data de admissão
                                    //para contratos que possuirem uma das seguinte categorias (01,03,04,05,06,07,11,12,19,20,21,26)
                                    if (in_array($rsContratos->getCampo("cod_categoria"),array(01,03,04,05,06,07,11,12,19,20,21,26))) {
                                        $dtAdmissao = $rsContratos->getCampo("dt_admissao");
                                        $dtAdmissaoFormatada = str_replace("-","",$rsContratos->getCampo("dt_admissao_n_formatado"));
                                    } else {
                                        $dtAdmissao = "";
                                        $dtAdmissaoFormatada = "";
                                    }
                                    if ($boPeriodoInicial) {
                                        $arPeriodoInicial = explode("-",$rsAssentamentoSEFIP->getCampo("periodo_inicial"));
                                        $dtPeriodoInicial = $arPeriodoInicial[2].$arPeriodoInicial[1].$arPeriodoInicial[0];
                                        $arMovimentacaoTrabalhador[$inIndex]['tipo_registro']                                 = 32;
                                        $arMovimentacaoTrabalhador[$inIndex]['tipo_inscricao']                                = 1;
                                        $arMovimentacaoTrabalhador[$inIndex]['inscricao_empresa']                             = $inCNPJ;
                                        $arMovimentacaoTrabalhador[$inIndex]['tipo_inscricao_tomador']                        = ( in_array($request->get("inCodRecolhimento"),array(130,135,150,155,608)) ) ? 1 : "";
                                        $arMovimentacaoTrabalhador[$inIndex]['inscricao_tomador']                             = ( in_array($request->get("inCodRecolhimento"),array(130,135,150,155,608)) ) ? $arContratosAdidosCedidosTomador[$rsContratos->getCampo("cod_contrato")] : "";
                                        $arMovimentacaoTrabalhador[$inIndex]['pis_pasep']                                     = preg_replace( "/[A-Za-z.\-]/","",$rsContratos->getCampo("servidor_pis_pasep"));
                                        $arMovimentacaoTrabalhador[$inIndex]['data_admissao']                                 = $dtAdmissao;
                                        $arMovimentacaoTrabalhador[$inIndex]['data_admissao_n_formatado']                     = $dtAdmissaoFormatada;
                                        $arMovimentacaoTrabalhador[$inIndex]['categoria_trabalhador']                         = $rsContratos->getCampo("cod_categoria");
                                        $arMovimentacaoTrabalhador[$inIndex]['nome_trabalhador']                              = removeAcentos($rsContratos->getCampo("nom_cgm"));
                                        //Esse campo não é utilizado no arquivo da sefip
                                        //serve apenas para a procura da informação mais abaixo no programa
                                        $arMovimentacaoTrabalhador[$inIndex]['registro']                                      = $rsContratos->getCampo("registro");
                                        $arMovimentacaoTrabalhador[$inIndex]['cod_movimentacao']                              = $inNumSefip;
                                        //No arquivo da sefip, quando informar registros do tipo 32 - movimentacao do trabalhador,
                                        //e nesse registro tratar-se de assentamento de afastamento temporário, o sistema deve
                                        //informar na data de início do afastamento o dia imediatamente inferior ao dia registrado no
                                        //assentamento. Esta regra (consta no layout) serve para todos os assentamentos de afastamento
                                        //temporário e somente na data de início.
                                        if ($rsAssentamentoSEFIP->getCampo("cod_tipo") == 2) {
                                            $arMovimentacaoTrabalhador[$inIndex]['data_movimentacao']                             = date("dmY",mktime(0,0,0,$arPeriodoInicial[1],$arPeriodoInicial[2]-1,$arPeriodoInicial[0]));
                                        } else {
                                            $arMovimentacaoTrabalhador[$inIndex]['data_movimentacao']                             = $dtPeriodoInicial;
                                        }
                                        $arMovimentacaoTrabalhador[$inIndex]['indicativo_recolhimento_fgts']                  = $stIndicativoRecolhimentoFgts;
                                        $arMovimentacaoTrabalhador[$inIndex]['brancos']                                       = "";
                                        $arMovimentacaoTrabalhador[$inIndex]['final']                                         = "*";

                                        $inIndex++;
                                    }

                                    if ($boPeriodoFinal) {
                                        $stFiltroSefipRetorno = " AND cod_sefip_saida = ".$inCodSefipSaida;
                                        $obTPessoalMovSefipSaidaMovSefipRetorno->recuperaMovSefipRetorno($rsMovSefipRetorno,$stFiltroSefipRetorno);

                                        $arPeriodoFinal = explode("-",$rsAssentamentoSEFIP->getCampo("periodo_final"));
                                        $dtPeriodoFinal = $arPeriodoFinal[2].$arPeriodoFinal[1].$arPeriodoFinal[0];

                                        $arMovimentacaoTrabalhador[$inIndex]['tipo_registro']                                 = 32;
                                        $arMovimentacaoTrabalhador[$inIndex]['tipo_inscricao']                                = 1;
                                        $arMovimentacaoTrabalhador[$inIndex]['inscricao_empresa']                             = $inCNPJ;
                                        $arMovimentacaoTrabalhador[$inIndex]['tipo_inscricao_tomador']                        = ( in_array($request->get("inCodRecolhimento"),array(130,135,150,155,608)) ) ? 1 : "";
                                        $arMovimentacaoTrabalhador[$inIndex]['inscricao_tomador']                             = ( in_array($request->get("inCodRecolhimento"),array(130,135,150,155,608)) ) ? $arContratosAdidosCedidosTomador[$rsContratos->getCampo("cod_contrato")] : "";
                                        $arMovimentacaoTrabalhador[$inIndex]['pis_pasep']                                     = preg_replace( "/[A-Za-z.\-]/","",$rsContratos->getCampo("servidor_pis_pasep"));
                                        $arMovimentacaoTrabalhador[$inIndex]['data_admissao']                                 = $dtAdmissao;
                                        $arMovimentacaoTrabalhador[$inIndex]['data_admissao_n_formatado']                     = $dtAdmissaoFormatada;
                                        $arMovimentacaoTrabalhador[$inIndex]['categoria_trabalhador']                         = $rsContratos->getCampo("cod_categoria");
                                        $arMovimentacaoTrabalhador[$inIndex]['nome_trabalhador']                              = removeAcentos($rsContratos->getCampo("nom_cgm"));
                                        //Esse campo não é utilizado no arquivo da sefip
                                        //serve apenas para a procura da informação mais abaixo no programa
                                        $arMovimentacaoTrabalhador[$inIndex]['registro']                                      = $rsContratos->getCampo("registro");
                                        $arMovimentacaoTrabalhador[$inIndex]['cod_movimentacao']                              = $rsMovSefipRetorno->getCampo("num_sefip");
                                        $arMovimentacaoTrabalhador[$inIndex]['data_movimentacao']                             = $dtPeriodoFinal;
                                        $arMovimentacaoTrabalhador[$inIndex]['indicativo_recolhimento_fgts']                  = $stIndicativoRecolhimentoFgts;
                                        $arMovimentacaoTrabalhador[$inIndex]['brancos']                                       = "";
                                        $arMovimentacaoTrabalhador[$inIndex]['final']                                         = "*";
                                        $inIndex++;
                                    }
                                }
                            }
                            $rsAssentamentoSEFIP->proximo();
                        }
                    }
                    $rsContratos->proximo();
                }
            }

            //Limpa Licença maternidade no caso de inLicencaMaternidade ser igual a 0
            if (Sessao::read("inLicencaMaternidade") == 0) {
                Sessao::write("nuTotalSalarioMaternidade",0);
                $arHeaderEmpresa[0]['salario_maternidade'] = 0;
            }

            $rsHeaderEmpresa = new RecordSet();
            $rsHeaderEmpresa->preenche($arHeaderEmpresa);
            $obExportador->roUltimoArquivo->addBloco($rsHeaderEmpresa);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_inscricao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inscricao_empresa");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("zeros");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(36);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_empresa");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("logradouro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("bairro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unid_federal");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fone");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador_alteracao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnae_fiscal");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador_alteracao_cnae");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("aliquota_rat");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("centralizacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("simples");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fpas");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("outras_entidades");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("gps");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("filantropia");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario_familia");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario_maternidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("contribuicao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_devido");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("zeros");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("final");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            ##########HEADER EMPRESA

            ##########REGISTRO INFORMAÇÕES ADICIONAIS DO RECOLHIMENTO DA EMPRESA
            if ($boCompetencia13) {
                $rsInformacoesAdicionais = new RecordSet();
                $rsInformacoesAdicionais->preenche($arInformacoesAdicionais);
                $obExportador->roUltimoArquivo->addBloco($rsInformacoesAdicionais);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_inscricao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inscricao_empresa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("zeros");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(36);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("deducao_13");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo6");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo7");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo8");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo9");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo10");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo11");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo12");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo13");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo14");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo15");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo16");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo17");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo18");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo19");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo20");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo21");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo22");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo23");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo24");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo25");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo26");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("campo27");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("final");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            }
            ##########REGISTRO INFORMAÇÕES ADICIONAIS DO RECOLHIMENTO DA EMPRESA

            ##########TOMADOR DE SERVIÇO
            if ( in_array($request->get('inCodRecolhimento'),array(130,135,150,155,211,317,337,608)) ) {
                $rsTomadorServico = new RecordSet();
                $rsTomadorServico->preenche($arTomadorServico);
                $obExportador->roUltimoArquivo->addBloco($rsTomadorServico);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_inscricao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inscricao_empresa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_inscricao_tomador");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inscricao_tomador");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("zeros");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(21);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_tomador");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("logradouro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("bairro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unid_federal");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("gps");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario_familia");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("contribuicao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_devido");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_retencao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_faturas");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("zeros");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(42);
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("final");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            }
            ##########TOMADOR DE SERVIÇO

            ##########movimentação DO TRABALHADOR
            $inIndex = 0;
            $arRegistroTrabalhador = array();
            $rsContratos->setPrimeiroElemento();

            $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa();

            while (!$rsContratos->eof()) {
                $nuEventoBaseCalculadoRescisaoDesD          = 0;
                $nuBaseCalculo1323                          = 0;
                $nuValorDescontado                          = 0;
                $nuRemuneracaoBase                          = 0;
                $nuRemuneracao13                            = 0;
                $nuRemuneracaoSem13                         = 0;
                $nuEventoCalculadoPrevidencia               = 0;
                $nuEventoBaseCalculadoPrevidencia           = 0;
                $nuEventoDescontoCalculadoPrevidencia       = 0;
                $nuEventoBaseCalculadoFGTS                  = 0;
                $nuEventoBaseCalculadoFGTSDecimo            = 0;
                $nuEventoRescisaoCalculadoDesDecimo         = 0;
                $nuEventoBaseCalculadoPrevidencia           = 0;
                $nuEventoBaseCalculadoPrevidenciaDecimoDesD = 0;
                $nuEventoBaseCalculadoRescisaoDesD          = 0;
                $nuEventoDescontoCalculadoPrevidencia       = 0;
                $nuEventoDescontoCalculadoPrevidenciaDecimo = 0;

                ####EVENTO DE BASE DE FGTS
                ####EVENTO CALCULADOS (SALÁRIO/COMPLEMENTAR/RESCISÃO/FÉRIAS) DE FGTS
                $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                $stFiltro .= " AND evento_calculado.cod_evento = ".$rsEventoFgts->getCampo("cod_evento");
                $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventosCalculados,$stFiltro);

                $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                $stFiltro .= " AND evento_complementar_calculado.cod_evento = ".$rsEventoFgts->getCampo("cod_evento");
                $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltro .= " AND evento_complementar_calculado.cod_configuracao != 3";
                $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventoComplementarCalculadoParaRelatorio($rsEventoComplementarCalculado,$stFiltro);
                $arEventoComplementarCalculado = $rsEventoComplementarCalculado->getSomaCampo("valor");

                $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                $stFiltro .= " AND evento_rescisao_calculado.cod_evento = ".$rsEventoFgts->getCampo("cod_evento");
                $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltro .= " AND evento_rescisao_calculado.desdobramento != 'D'";
                $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventoRescisaoCalculado($rsEventoRescisaoCalculado,$stFiltro);
                $arEventoRescisaoCalculado = $rsEventoRescisaoCalculado->getSomaCampo("valor");

                $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                $stFiltro .= " AND evento_ferias_calculado.cod_evento = ".$rsEventoFgts->getCampo("cod_evento");
                $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltro .= " AND (evento_ferias_calculado.desdobramento = 'F' OR evento_ferias_calculado.desdobramento = 'A')";
                $obTFolhaPagamentoEventoFeriasCalculado->recuperaEventosCalculados($rsEventoFeriasCalculado,$stFiltro);
                $arEventoFeriasCalculado = $rsEventoFeriasCalculado->getSomaCampo("valor");

                $nuEventoBaseCalculadoFGTS = $rsEventosCalculados->getCampo("valor")+$arEventoComplementarCalculado["valor"]+$arEventoRescisaoCalculado["valor"]+$arEventoFeriasCalculado["valor"];
                ####EVENTO CALCULADOS (SALÁRIO/COMPLEMENTAR/RESCISÃO/FÉRIAS) DE FGTS

                ####EVENTO CALCULADOS (DÉCIMO) DE FGTS
                $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                $stFiltro .= " AND evento_decimo_calculado.cod_evento = ".$rsEventoFgts->getCampo("cod_evento");
                $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltro .= " AND evento_decimo_calculado.desdobramento = 'A'";
                $obTFolhaPagamentoEventoDecimoCalculado->recuperaEventosDecimoCalculado($rsEventosDecimoCalculados,$stFiltro);

                $nuEventoBaseCalculadoFGTSDecimo = ($rsEventosDecimoCalculados->getCampo("valor") != "") ? $rsEventosDecimoCalculados->getCampo("valor") : 0;
                ####EVENTO CALCULADOS (DÉCIMO) DE FGTS
                ####EVENTO DE BASE DE FGTS

                $arPeriodoMoviInicial = explode("/",$rsPeriodoMovimentacao->getCampo("dt_inicial"));
                $arPeriodoMoviFinal   = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));
                $dtPeriodoMoviInicial = $arPeriodoMoviInicial[2]."-".$arPeriodoMoviInicial[1]."-".$arPeriodoMoviInicial[0];
                $dtPeriodoMoviFinal   = $arPeriodoMoviFinal[2]."-".$arPeriodoMoviFinal[1]."-".$arPeriodoMoviFinal[0];

                ####PREVIDÊNCIA
                $stFiltroPrevidencia  = " AND contrato_servidor_previdencia.cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                $stFiltroPrevidencia .= " AND contrato_servidor_previdencia.bo_excluido  = 'f'";
                $stFiltroPrevidencia .= " AND (SELECT true
                                                 FROM folhapagamento.previdencia_previdencia
                                                    , (  SELECT cod_previdencia
                                                              , max(timestamp) as timestamp
                                                           FROM folhapagamento.previdencia_previdencia
                                                          WHERE previdencia_previdencia.vigencia <= '".$dtPeriodoMoviFinal."'
                                                       GROUP BY cod_previdencia) as max_previdencia_previdencia
                                                WHERE previdencia_previdencia.cod_previdencia  = max_previdencia_previdencia.cod_previdencia
                                                  AND previdencia_previdencia.timestamp        = max_previdencia_previdencia.timestamp
                                                  AND previdencia_previdencia.tipo_previdencia = 'o'
                                                  AND previdencia_previdencia.cod_previdencia  = contrato_servidor_previdencia.cod_previdencia)";
                $obTPessoalContratoServidorPrevidencia->recuperaRelacionamento($rsPrevidencia,$stFiltroPrevidencia);

                ####EVENTO DE BASE DE PREVIDÊNCIA
                if ( $rsPrevidencia->getNumLinhas() == 1 ) {
                    $stFiltroEventoPrevidencia  = " AND contrato_servidor_previdencia.cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltroEventoPrevidencia .= " AND contrato_servidor_previdencia.cod_previdencia = ".$rsPrevidencia->getCampo("cod_previdencia");
                    $stFiltroEventoPrevidencia .= " AND cod_tipo = 2";
                    $obTFolhaPagamentoPrevidenciaEvento->recuperaEventosDePrevidenciaPorContrato($rsEventoPrevidencia,$stFiltroEventoPrevidencia);

                    ####EVENTO CALCULADOS (SALÁRIO/COMPLEMENTAR/RESCISÃO/FÉRIAS) DE PREVIDÊNCIA
                    $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltro .= " AND evento_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND (desdobramento IS NULL OR desdobramento = 'F')";
                    $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventosCalculados,$stFiltro);
                    $arEventoSalarioCalculado = $rsEventosCalculados->getSomaCampo("valor");

                    $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltro .= " AND evento_complementar_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND evento_complementar_calculado.cod_configuracao != 3";
                    $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventoComplementarCalculadoParaRelatorio($rsEventoComplementarCalculado,$stFiltro);
                    $arEventoComplementarCalculado = $rsEventoComplementarCalculado->getSomaCampo("valor");

                    $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltro .= " AND evento_rescisao_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND evento_rescisao_calculado.desdobramento != 'D'";
                    $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventoRescisaoCalculado($rsEventoRescisaoCalculado,$stFiltro);
                    $arEventoRescisaoCalculado = $rsEventoRescisaoCalculado->getSomaCampo("valor");

                    $stFiltro  = " WHERE cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltro .= " AND dt_rescisao BETWEEN to_date('".$rsPeriodoMovimentacao->getCampo("dt_inicial")."','dd-mm-yyyy') AND to_date('".$rsPeriodoMovimentacao->getCampo("dt_final")."','dd-mm-yyyy')";
                    $obTPessoalContratoServidorCasoCausa->recuperaTodos($rsContratoRescisao,$stFiltro);

                    while (!$rsContratoRescisao->eof()) {
                        $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                        $stFiltro .= " AND evento_rescisao_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                        $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                        $stFiltro .= " AND evento_rescisao_calculado.desdobramento = 'D'";
                        $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventoRescisaoCalculado($rsEventoRescisaoCalculado,$stFiltro);
                        $arEventoRescisaoCalculadoDesDecimo = $rsEventoRescisaoCalculado->getSomaCampo("valor");
                        $nuEventoRescisaoCalculadoDesDecimo = $arEventoRescisaoCalculadoDesDecimo["valor"];
                        if ($nuEventoRescisaoCalculadoDesDecimo == '0' or $nuEventoRescisaoCalculadoDesDecimo == '0.00' or $nuEventoRescisaoCalculadoDesDecimo == "") {
                            $nuEventoRescisaoCalculadoDesDecimo = "0.01";
                        }
                        $rsContratoRescisao->proximo();
                    }

                    $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltro .= " AND evento_ferias_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND (evento_ferias_calculado.desdobramento = 'F' OR evento_ferias_calculado.desdobramento = 'A')";
                    $obTFolhaPagamentoEventoFeriasCalculado->recuperaEventosCalculados($rsEventoFeriasCalculado,$stFiltro);
                    $arEventoFeriasCalculado = $rsEventoFeriasCalculado->getSomaCampo("valor");

                    $nuEventoBaseCalculadoPrevidencia = $arEventoSalarioCalculado["valor"]+$arEventoComplementarCalculado["valor"]+$arEventoRescisaoCalculado["valor"]+$arEventoFeriasCalculado["valor"];
                    ####EVENTO CALCULADOS (SALÁRIO/COMPLEMENTAR/RESCISÃO/FÉRIAS) DE PREVIDÊNCIA

                    ####EVENTO CALCULADOS (DECIMO) DE PREVIDÊNCIA
                    $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltro .= " AND evento_decimo_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND evento_decimo_calculado.desdobramento = 'D'";
                    $obTFolhaPagamentoEventoDecimoCalculado->recuperaEventosDecimoCalculado($rsEventosDecimoCalculadosDesD,$stFiltro);
                    $arEventosDecimoCalculadosDesD = $rsEventosDecimoCalculadosDesD->getSomaCampo("valor");

                    $nuEventoBaseCalculadoPrevidenciaDecimoDesD = $arEventosDecimoCalculadosDesD["valor"];
                    if ($boCompetencia13) {
                        Sessao::write("nuBasePrevidencia13",Sessao::read("nuBasePrevidencia13")+$nuEventoBaseCalculadoPrevidenciaDecimoDesD);
                    }
                    ####EVENTO CALCULADOS (DECIMO) DE PREVIDÊNCIA

                    ####EVENTO CALCULADOS (RESCISÃO) DE PREVIDÊNCIA
                    $stFiltro = " AND contrato_servidor_caso_causa.cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $obTPessoalContratoServidorCasoCausa->recuperaSefipContrato($rsSefipContratoRescisao,$stFiltro);
                    if (trim($rsSefipContratoRescisao->getCampo("num_sefip")) != 'H') {
                        $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                        $stFiltro .= " AND evento_rescisao_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                        $stFiltro .= " AND evento_rescisao_calculado.desdobramento = 'D'";
                        $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                        $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventoRescisaoCalculado($rsEventoRescisaoCalculado,$stFiltro);
                        $arEventoRescisaoCalculado = $rsEventoRescisaoCalculado->getSomaCampo("valor");

                        $nuEventoBaseCalculadoRescisaoDesD = $arEventoRescisaoCalculado["valor"];
                    }
                    ####EVENTO CALCULADOS (RESCISÃO) DE PREVIDÊNCIA
                }
                ####EVENTO DE BASE DE PREVIDÊNCIA

                ####EVENTO DE DESCONTO DE PREVIDÊNCIA
                if ( $rsPrevidencia->getNumLinhas() == 1 ) {
                    $stFiltroEventoPrevidencia  = " AND contrato_servidor_previdencia.cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltroEventoPrevidencia .= " AND contrato_servidor_previdencia.cod_previdencia = ".$rsPrevidencia->getCampo("cod_previdencia");
                    $stFiltroEventoPrevidencia .= " AND cod_tipo = 1";
                    $obTFolhaPagamentoPrevidenciaEvento->recuperaEventosDePrevidenciaPorContrato($rsEventoPrevidencia,$stFiltroEventoPrevidencia);

                    ####EVENTO CALCULADOS (SALÁRIO/COMPLEMENTAR/RESCISÃO/FÉRIAS) DE PREVIDÊNCIA
                    $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltro .= " AND evento_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventosCalculados,$stFiltro);
                    $arEventosCalculados = $rsEventosCalculados->getSomaCampo("valor");

                    $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltro .= " AND evento_complementar_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND evento_complementar_calculado.cod_configuracao != 3";
                    $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventoComplementarCalculadoParaRelatorio($rsEventoComplementarCalculado,$stFiltro);
                    $arEventoComplementarCalculado = $rsEventoComplementarCalculado->getSomaCampo("valor");

                    $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltro .= " AND evento_rescisao_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND evento_rescisao_calculado.desdobramento != 'D'";
                    $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventoRescisaoCalculado($rsEventoRescisaoCalculado,$stFiltro);
                    $arEventoRescisaoCalculado = $rsEventoRescisaoCalculado->getSomaCampo("valor");

                    $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltro .= " AND evento_ferias_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND (evento_ferias_calculado.desdobramento = 'F' OR evento_ferias_calculado.desdobramento = 'A')";
                    $obTFolhaPagamentoEventoFeriasCalculado->recuperaEventosCalculados($rsEventoFeriasCalculado,$stFiltro);
                    $arEventoFeriasCalculado = $rsEventoFeriasCalculado->getSomaCampo("valor");

                    $nuEventoDescontoCalculadoPrevidencia = $arEventosCalculados['valor']+$arEventoComplementarCalculado["valor"]+$arEventoRescisaoCalculado["valor"]+$arEventoFeriasCalculado["valor"];
                    ####EVENTO CALCULADOS (SALÁRIO/COMPLEMENTAR/RESCISÃO/FÉRIAS) DE PREVIDÊNCIA

                    ####EVENTO CALCULADOS (DECIMO) DE PREVIDÊNCIA
                    $stFiltro  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltro .= " AND evento_decimo_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND evento_decimo_calculado.desdobramento = 'D'";
                    $obTFolhaPagamentoEventoDecimoCalculado->recuperaEventosDecimoCalculado($rsEventosDecimoCalculados,$stFiltro);
                    $arEventosDecimoCalculados = $rsEventosDecimoCalculados->getSomaCampo("valor");

                    $nuEventoDescontoCalculadoPrevidenciaDecimo = $arEventosDecimoCalculados["valor"];
                    ####EVENTO CALCULADOS (DECIMO) DE PREVIDÊNCIA
                }
                ####EVENTO DE DESCONTO DE PREVIDÊNCIA
                ####PREVIDÊNCIA

                if ($boCompetencia13) {
                    $nuTotalLinha = $nuEventoBaseCalculadoFGTSDecimo + $nuEventoBaseCalculadoPrevidenciaDecimoDesD + $nuEventoDescontoCalculadoPrevidenciaDecimo;
                } else {
                    $nuTotalLinha = $nuEventoBaseCalculadoFGTS + $nuEventoBaseCalculadoFGTSDecimo + $nuEventoBaseCalculadoPrevidencia + $nuEventoBaseCalculadoPrevidenciaDecimoDesD + $nuEventoBaseCalculadoRescisaoDesD + $nuEventoDescontoCalculadoPrevidencia + $nuEventoDescontoCalculadoPrevidenciaDecimo;
                }

                if ($nuTotalLinha > 0) {
                    $stFiltroRescisao = " WHERE cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $obTPessoalContratoServidorCasoCausa->recuperaTodos($rsRescisao,$stFiltroRescisao);

                    ######remuneracao_sem_13
                    $nuRemuneracaoSem13 = $nuEventoBaseCalculadoFGTS;
                    Sessao::write("nuBaseFGTS", Sessao::read("nuBaseFGTS")+$nuEventoBaseCalculadoFGTS);

                    $nuRemuneracaoSem13 = $nuEventoBaseCalculadoPrevidencia;
                    Sessao::write("nuBasePrevidenciaS13", Sessao::read("nuBasePrevidenciaS13")+$nuEventoBaseCalculadoPrevidencia);

                    if (!strpos($nuRemuneracaoSem13,".")) {
                        $nuRemuneracaoSem13 .= ".00";
                    }
                    ######remuneracao_sem_13

                    ######remuneracao_13
                    $nuRemuneracao13 = $nuEventoBaseCalculadoFGTSDecimo;
                    Sessao::write("nuBaseFGTS13", Sessao::read("nuBaseFGTS13")+$nuEventoBaseCalculadoFGTSDecimo);
                    if ($nuRemuneracao13 == 0) {
                        $nuRemuneracao13 = $nuEventoBaseCalculadoPrevidenciaDecimoDesD;
                        Sessao::write("nuBasePrevidencia13", Sessao::read("nuBasePrevidencia13")+$nuEventoBaseCalculadoPrevidenciaDecimoDesD);
                    }
                    ######remuneracao_13

                    ######valor_descontado
                    ##Verificação de assentamento de afastamento temporário para maternidade (Q1,Q2,Q3,Q4,Q5,Q6) para o contrato
                    include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php";
                    $obTPessoalAssentamentoGerado = new TPessoalAssentamentoGerado();
                    $stFiltroAssentamento  = " AND assentamento_mov_sefip_saida.cod_sefip_saida IN (18,19,20,21,22,23)\n";
                    $stFiltroAssentamento .= " AND assentamento_gerado_contrato_servidor.cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltroAssentamento .= " AND cod_tipo = 2 \n";
                    $stFiltroAssentamento .= " AND (to_char(periodo_inicial,'yyyy-mm-dd')::date BETWEEN to_date('".$rsPeriodoMovimentacao->getCampo("dt_inicial")."','dd-mm-yyyy')
                                                                                            AND to_date('".$rsPeriodoMovimentacao->getCampo("dt_final")."','dd-mm-yyyy')\n";
                    $stFiltroAssentamento .= "   OR (to_char(periodo_final,'yyyy-mm-dd'))::date  BETWEEN to_date('".$rsPeriodoMovimentacao->getCampo("dt_inicial")."','dd-mm-yyyy')
                                                                                            AND to_date('".$rsPeriodoMovimentacao->getCampo("dt_final")."','dd-mm-yyyy'))\n";
                    $obTPessoalAssentamentoGerado->setDado("competencia1",$stCompetencia1);
                    $obTPessoalAssentamentoGerado->setDado("competencia2",$stCompetencia2);
                    $obTPessoalAssentamentoGerado->recuperaAssentamentoSEFIPTemporario($rsAssentamentoSEFIP,$stFiltroAssentamento);

                    ##Verificação de mais de um vínculo empregatício do trabalhador
                    $boMultiploVinculo = false;
                    if ( $rsAssentamentoSEFIP->getNumLinhas() == -1 ) {
                        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php";
                        $obTPessoalContrato = new TPessoalContrato();
                        $stFiltroContratosCGM = " AND numcgm = ".$rsContratos->getCampo("numcgm");
                        $obTPessoalContrato->recuperaCgmDoRegistro($rsContratosCGM,$stFiltroContratosCGM);
                        if ( $rsContratosCGM->getNumLinhas() >= 2 ) {
                            while (!$rsContratosCGM->eof()) {
                                if ($rsContratosCGM->getCampo("cod_contrato") != $rsContratos->getCampo("cod_contrato")) {
                                    ####EVENTO CALCULADOS (DÉCIMO) DE PREVIDÊNCIA
                                    $stFiltro  = " AND cod_contrato = ".$rsContratosCGM->getCampo("cod_contrato");
                                    //$stFiltro .= " AND evento_decimo_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                                    $obTFolhaPagamentoEventoDecimoCalculado->recuperaEventosDecimoCalculado($rsEventoDecimoCalculado,$stFiltro);
                                    if ($rsEventoDecimoCalculado->getNumLinhas() > 0) {
                                        $boMultiploVinculo = true;
                                        break;
                                    }
                                    ####EVENTO CALCULADOS (DÉCIMO) DE PREVIDÊNCIA

                                    ####EVENTO CALCULADOS (SALÁRIO/COMPLEMENTAR/RESCISÃO/FÉRIAS) DE PREVIDÊNCIA
                                    $stFiltro  = " AND cod_contrato = ".$rsContratosCGM->getCampo("cod_contrato");
                                    //$stFiltro .= " AND evento_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                                    $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventosCalculados,$stFiltro);
                                    if ($rsEventosCalculados->getNumLinhas() > 0) {
                                        $boMultiploVinculo = true;
                                        break;
                                    }
                                    $stFiltro  = " AND cod_contrato = ".$rsContratosCGM->getCampo("cod_contrato");
                                    //$stFiltro .= " AND evento_complementar_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                                    $stFiltro .= " AND evento_complementar_calculado.cod_configuracao != 3";
                                    $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventoComplementarCalculadoParaRelatorio($rsEventoComplementarCalculado,$stFiltro);
                                    if ($rsEventoComplementarCalculado->getNumLinhas() > 0) {
                                        $boMultiploVinculo = true;
                                        break;
                                    }
                                    $stFiltro  = " AND cod_contrato = ".$rsContratosCGM->getCampo("cod_contrato");
                                    //$stFiltro .= " AND evento_rescisao_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                                    $stFiltro .= " AND evento_rescisao_calculado.desdobramento != 'D'";
                                    $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventoRescisaoCalculado($rsEventoRescisaoCalculado,$stFiltro);
                                    if ($rsEventoRescisaoCalculado->getNumLinhas() > 0) {
                                        $boMultiploVinculo = true;
                                        break;
                                    }
                                    $stFiltro  = " AND cod_contrato = ".$rsContratosCGM->getCampo("cod_contrato");
                                    //$stFiltro .= " AND evento_ferias_calculado.cod_evento = ".$rsEventoPrevidencia->getCampo("cod_evento");
                                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                                    $obTFolhaPagamentoEventoFeriasCalculado->recuperaEventosCalculados($rsEventoFeriasCalculado,$stFiltro);
                                    if ($rsEventoFeriasCalculado->getNumLinhas() > 0) {
                                        $boMultiploVinculo = true;
                                        break;
                                    }
                                    ####EVENTO CALCULADOS (SALÁRIO/COMPLEMENTAR/RESCISÃO/FÉRIAS) DE PREVIDÊNCIA
                                }
                                $rsContratosCGM->proximo();
                            }
                        }
                    }

                    if ( $rsPrevidencia->getNumLinhas() == 1) {
                        $stFiltro  = " AND desconto_externo_previdencia.timestamp <= ";
                        $stFiltro .= " (ultimotimestampperiodomovimentacao(".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                        $stFiltro .= " ,'".Sessao::getEntidade()."')::timestamp)";

                        require_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDescontoExternoPrevidencia.class.php";
                        $obTFolhaPagamentoDescontoExternoPrevidencia = new TFolhaPagamentoDescontoExternoPrevidencia;
                        $obTFolhaPagamentoDescontoExternoPrevidencia->setDado("cod_contrato", $rsContratos->getCampo("cod_contrato"));
                        $obTFolhaPagamentoDescontoExternoPrevidencia->setDado("vigencia"    , $rsPeriodoMovimentacao->getCampo("dt_inicial"));
                        $obTFolhaPagamentoDescontoExternoPrevidencia->recuperaRelacionamento ($rsDescontoExternoPrevidencia, $stFiltro);

                        if ($rsDescontoExternoPrevidencia->getNumLinhas() > 0) {
                            $boMultiploVinculo = TRUE;
                        }
                    }

                    if ($rsAssentamentoSEFIP->getNumLinhas() > 0 or $boMultiploVinculo) {
                        if ($boCompetencia13) {
                            if ($boMultiploVinculo) {
                                $nuValorDescontado = number_format($nuEventoDescontoCalculadoPrevidenciaDecimo, 2, '.', '');
                            }
                        } else {
                            $nuValorDescontado = number_format($nuEventoDescontoCalculadoPrevidencia, 2, '.', '');
                        }
                        Sessao::write("nuDescontoPrevidenciaS13", Sessao::read("nuDescontoPrevidenciaS13")+$nuEventoDescontoCalculadoPrevidencia);
                    } else {
                        $nuValorDescontado = 0;

                        #Contratos calculados que não possuem multiplos vinculos ou SALÁRIO maternidade
                        Sessao::write("nuDescontoPrevidenciaS13DemaisOcor", Sessao::read("nuDescontoPrevidenciaS13DemaisOcor")+$nuEventoDescontoCalculadoPrevidencia);
                    }
                    ######valor_descontado

                    ######ocorrencia
                    if ($boMultiploVinculo && $rsContratos->getCampo('cod_categoria') != 13 ) {
                        $stOcorrencia = "05";
                    } else {
                        $stOcorrencia = $rsContratos->getCampo("num_ocorrencia");
                    }
                    ######ocorrencia

                    ######remuneracao_base
                    $nuRemuneracaoBase = 0;

                    if ($arIncluidoMovimentacao[$rsContratos->getCampo("cod_contrato")]) {
                        $stFiltroMovSefip  = " AND cod_tipo = 2";
                        $stFiltroMovSefip .= " AND num_sefip IN ('O1','O2','R','Z2','Z3','Z4')";
                        $stFiltroMovSefip .= " AND assentamento_gerado_contrato_servidor.cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                        $obTPessoalAssentamentoMovSefipSaida->recuperaAfastamentoTemporariosSefip($rsAssentamentoMovSefip,$stFiltroMovSefip);

                        $nuRemuneracaoBase = 0;
                        if ( $rsPrevidencia->getNumLinhas() == 1 and $rsAssentamentoMovSefip->getNumLinhas() > 0) {
                            $nuRemuneracaoBase = $nuEventoBaseCalculadoPrevidencia;
                        }
                    }
                    ######remuneracao_base

                    ######base_calculo_13_22
                    $nuBaseCalculo1322 = $nuEventoBaseCalculadoRescisaoDesD;
                    ######base_calculo_13_22

                    ######base_calculo_13_23
                    $nuBaseCalculo1323 = 0;
                    if ( $rsPrevidencia->getNumLinhas() == 1 and $boDezembro and $rsRescisao->getNumLinhas() < 0 ) {
                        $nuBaseCalculo1323 = $nuEventoBaseCalculadoPrevidenciaDecimoDesD;
                    }
                    ######base_calculo_13_23

                    ######inscricao_tomador
                    if (in_array($request->get("inCodRecolhimento"),array(130,135,150,155,211,317,337,608)) and $arContratosAdidosCedidosTomador[$rsContratos->getCampo("cod_contrato")] != "") {
                        $inTipoInscricaoTomador = 1;
                    } else {
                        $inTipoInscricaoTomador = "";
                    }
                    ######inscricao_tomador

                    ######data_opcao
                    $dtOpcao = $rsContratos->getCampo("dt_opcao_fgts");

                    $nuDataAdmissao = $rsContratos->getCampo("dt_admissao");
                    $nuDataAdmissao = substr($nuDataAdmissao,4,4).substr($nuDataAdmissao,2,2).substr($nuDataAdmissao,0,2);

                    if ($dtOpcao == "") {
                        if ($nuDataAdmissao > 19881005) {
                            $dtOpcao = $rsContratos->getCampo("dt_admissao");
                        } else {
                            $dtOpcao = '05101988';
                        }
                    } else {
                        $nuDataOpcao = $rsContratos->getCampo("dt_opcao_fgts");
                        $nuDataOpcao = substr($nuDataOpcao,4,4).substr($nuDataOpcao,2,2).substr($nuDataOpcao,0,2);

                        if ($nuDataOpcao > $nuDataAdmissao) {
                            if ($nuDataOpcao <= 19881005) {
                                $dtOpcao = '05101988';
                            }
                        } else {
                            if ($nuDataOpcao > 19881005) {
                                $dtOpcao = $rsContratos->getCampo("dt_admissao");
                            } else {
                                $dtOpcao = '05101988';
                            }
                        }
                    }

                    ######data_opcao
                    //No caso da matricula, consta no layout que a informação NÃO deve constar em casos de trabalhadores
                    //com categoria = 06,13,14,15,16,17,18,22,23,24,25. Para os demais casos pode constar.
                    if (!in_array($rsContratos->getCampo("cod_categoria"),array(06,13,14,15,16,17,18,22,23,24,25))) {
                        $inRegistro = $rsContratos->getCampo("registro");
                    } else {
                        $inRegistro = "";
                    }

                    //Verifica se deve ser informado a data de admissão - Só deverá ser informada a data de admissão
                    //para contratos que possuirem uma das seguinte categorias (01,03,04,05,06,07,11,12,19,20,21,26)
                    if (in_array($rsContratos->getCampo("cod_categoria"),array(01,03,04,05,06,07,11,12,19,20,21,26))) {
                        $dtAdmissao = $rsContratos->getCampo("dt_admissao");
                        $dtAdmissaoFormatada = str_replace("-","",$rsContratos->getCampo("dt_admissao_n_formatado"));
                    } else {
                        $dtAdmissao = "";
                        $dtAdmissaoFormatada = "";
                    }

                    $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa();
                    $stContratoRescisao = " WHERE cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $obTPessoalContratoServidorCasoCausa->recuperaTodos($rsContratoRescisao,$stContratoRescisao);

                    $arRegistroTrabalhador[$inIndex]['tipo_registro']                                 = 30;
                    $arRegistroTrabalhador[$inIndex]['tipo_inscricao']                                = 1;
                    $arRegistroTrabalhador[$inIndex]['inscricao_empresa']                             = $inCNPJ;
                    $arRegistroTrabalhador[$inIndex]['tipo_inscricao_tomador']                        = $inTipoInscricaoTomador;
                    $arRegistroTrabalhador[$inIndex]['inscricao_tomador']                             = ( in_array($request->get("inCodRecolhimento"),array(130,135,150,155,211,317,337,608)) ) ? $arContratosAdidosCedidosTomador[$rsContratos->getCampo("cod_contrato")] : "";
                    $arRegistroTrabalhador[$inIndex]['pis_pasep']                                     = preg_replace("[A-Za-z.\-]","",$rsContratos->getCampo("servidor_pis_pasep"));
                    $arRegistroTrabalhador[$inIndex]['data_admissao']                                 = $dtAdmissao;
                    $arRegistroTrabalhador[$inIndex]['data_admissao_n_formatado']                     = $dtAdmissaoFormatada;
                    $arRegistroTrabalhador[$inIndex]['categoria_trabalhador']                         = $rsContratos->getCampo("cod_categoria");
                    $arRegistroTrabalhador[$inIndex]['nome_trabalhador']                              = removeAcentos($rsContratos->getCampo("nom_cgm"));
                    $arRegistroTrabalhador[$inIndex]['matricula_empregado']                           = $inRegistro;
                    $arRegistroTrabalhador[$inIndex]['numero_ctps']                                   = ( in_array($rsContratos->getCampo("cod_categoria"),array(1,2,3,4,6,7,26)) ) ? str_pad(trim($rsContratos->getCampo("numero")),7,"0",STR_PAD_LEFT) : "";
                    $arRegistroTrabalhador[$inIndex]['serie_ctps']                                    = ( in_array($rsContratos->getCampo("cod_categoria"),array(1,2,3,4,6,7,26)) ) ? str_pad(trim($rsContratos->getCampo("serie")),5,"0",STR_PAD_LEFT)  : "";
                    $arRegistroTrabalhador[$inIndex]['data_opcao']                                    = ( in_array($rsContratos->getCampo("cod_categoria"),array(1,3,4,5,6,7)) )    ? $dtOpcao : "";
                    $arRegistroTrabalhador[$inIndex]['data_nascimento']                               = ( in_array($rsContratos->getCampo("cod_categoria"),array(1,2,3,4,6,7,12,19,20,21,26)) ) ? $rsContratos->getCampo("dt_nascimento") : "";
                    $arRegistroTrabalhador[$inIndex]['cbo']                                           = "0".substr($rsContratos->getCampo("cbo"),0,strlen($rsContratos->getCampo("cbo"))-1);
                    $arRegistroTrabalhador[$inIndex]['remuneracao_sem_13']                            = ( !$boCompetencia13 ) ? str_replace(".","",number_format($nuRemuneracaoSem13,2,".","")) : "";
                    $arRegistroTrabalhador[$inIndex]['remuneracao_13']                                = ( !$boCompetencia13 ) ? ($rsContratoRescisao->getNumLinhas() == -1) ? str_replace(".","",number_format($nuRemuneracao13,2,".","")) : ""   : "";
                    $arRegistroTrabalhador[$inIndex]['classe_contribuicao']                           = "";
                    $arRegistroTrabalhador[$inIndex]['ocorrencia']                                    = ($stOcorrencia == 0) ? "" : str_pad($stOcorrencia,2,"0",STR_PAD_LEFT);                        
                    if ( $rsContratos->getCampo('cod_categoria') != 13 ){
                        $arRegistroTrabalhador[$inIndex]['valor_descontado']                          = str_replace(".","",number_format($nuValorDescontado,2,".",""));
                    }else{
                        $arRegistroTrabalhador[$inIndex]['valor_descontado']                          = 0;
                    }
                    $arRegistroTrabalhador[$inIndex]['remuneracao_base']                              = ( !$boCompetencia13 ) ? str_replace(".","",number_format($nuRemuneracaoBase,2,".","")) : "";

                    if (in_array($rsContratos->getCampo("cod_categoria"),array(1,2,4,6,7,12,13,19,20,21,26))) {
                        if ($boCompetencia13) {
                            $arRegistroTrabalhador[$inIndex]['base_calculo_13_22']                        = str_replace(".","",number_format($nuEventoBaseCalculadoPrevidenciaDecimoDesD,2,".",""));
                        } else {
                            if ( $rsContratos->getCampo('cod_categoria') != 13 ){
                                //Campo somente preenchido para contratos rescindidos
                                $arRegistroTrabalhador[$inIndex]['base_calculo_13_22']                        = ($rsContratoRescisao->getNumLinhas() == 1) ? str_replace(".","",number_format($nuEventoRescisaoCalculadoDesDecimo,2,".","")) : "";
                            }else{
                                $arRegistroTrabalhador[$inIndex]['base_calculo_13_22']                        = 0;
                            }
                        }
                    } else {
                        $arRegistroTrabalhador[$inIndex]['base_calculo_13_22'] = 0;
                    }

                    $arRegistroTrabalhador[$inIndex]['base_calculo_13_23']                            = ( !$boCompetencia13 ) ? str_replace(".","",number_format($nuBaseCalculo1323,2,".","")) : "";
                    $arRegistroTrabalhador[$inIndex]['brancos']                                       = "";
                    $arRegistroTrabalhador[$inIndex]['final']                                         = "*";

                    Sessao::write("inTotalServidoresArquivo", Sessao::read("inTotalServidoresArquivo")+1);

                    //Booleano para controlar contratos de categoria 13 e com miltiplas matriculas ja foi inserido no array
                    $boPularRegistro = false;
                    //Controlar contratos de categoria 13 e com miltiplas matriculas somando seus valores
                    //1946 e 1802 , 1738 e 1948
                    if ( $rsContratos->getCampo('cod_categoria') == 13 ){
                        $stPisPasepContratoAnterior = $rsContratos->getCampo('servidor_pis_pasep');
                        //Avança um registro para verificar se existe outra matricula para o mesmo servidor
                        if ( $rsContratos->proximo() == true ){
                            if ( $boRegistroRepetido == true ) {
                                $arRegistroTrabalhador[0]['remuneracao_sem_13'] = $arRegistroTrabalhador[0]['remuneracao_sem_13'] + $arAuxRegistroCategoria13[0]['remuneracao_sem_13'];
                                $arRegistroTrabalhador[0]['remuneracao_13']     = $arRegistroTrabalhador[0]['remuneracao_13']     + $arAuxRegistroCategoria13[0]['remuneracao_13'];
                                $arRegistroTrabalhador[0]['remuneracao_base']   = $arRegistroTrabalhador[0]['remuneracao_base']   + $arAuxRegistroCategoria13[0]['remuneracao_base'];
                                $arRegistroTrabalhador[0]['base_calculo_13_23'] = $arRegistroTrabalhador[0]['base_calculo_13_23'] + $arAuxRegistroCategoria13[0]['base_calculo_13_23'];
                                $boRegistroRepetido = false;
                                $boPularRegistro = false;
                            }else{
                                if ($stPisPasepContratoAnterior == $rsContratos->getCampo('servidor_pis_pasep') ) {
                                    $arAuxRegistroCategoria13 = $arRegistroTrabalhador;
                                    $boPularRegistro = true;
                                    $boRegistroRepetido = true;
                                }
                            }
                            //Volta para o registro corrente
                            $rsContratos->anterior();
                        }else{
                            //Verifica se o ultimo registro é o repetido
                            if ( $boRegistroRepetido == true ) {
                                $arRegistroTrabalhador[0]['remuneracao_sem_13'] = $arRegistroTrabalhador[0]['remuneracao_sem_13'] + $arAuxRegistroCategoria13[0]['remuneracao_sem_13'];
                                $arRegistroTrabalhador[0]['remuneracao_13']     = $arRegistroTrabalhador[0]['remuneracao_13']     + $arAuxRegistroCategoria13[0]['remuneracao_13'];
                                $arRegistroTrabalhador[0]['remuneracao_base']   = $arRegistroTrabalhador[0]['remuneracao_base']   + $arAuxRegistroCategoria13[0]['remuneracao_base'];
                                $arRegistroTrabalhador[0]['base_calculo_13_23'] = $arRegistroTrabalhador[0]['base_calculo_13_23'] + $arAuxRegistroCategoria13[0]['base_calculo_13_23'];
                            }
                            $boRegistroRepetido = false;
                            $boPularRegistro = false;
                        }
                    }

                    if ( $boPularRegistro == false ) {
                        addRegistroTrabalhador($obExportador,$arRegistroTrabalhador);
                    }

                    if (is_array($arMovimentacaoTrabalhador)) {
                        foreach ($arMovimentacaoTrabalhador as $inIndexTrab=>$arDados) {
                            if ($arDados["registro"] == $rsContratos->getCampo("registro")) {
                                addMovimentacaoTrabalhador($obExportador,array($arDados));
                                unset($arMovimentacaoTrabalhador[$inIndexTrab]);
                                reset($arMovimentacaoTrabalhador);
                            }
                        }
                    }
                }
                $rsContratos->proximo();
            }
            ##########REGISTRO DO TRABALHADOR
            ##########REGISTRO TOTALIZADOR DO ARQUIVO
            $inIndex = 0;
            $arFinal[$inIndex]['tipo_registro']                                 = 90;
            $arFinal[$inIndex]['marca']                                         = str_pad("9",51,"9");
            $arFinal[$inIndex]['brancos']                                       = "";
            $arFinal[$inIndex]['final']                                         = "*";
            $rsFinal = new RecordSet();
            $rsFinal->preenche($arFinal);
            $obExportador->roUltimoArquivo->addBloco($rsFinal);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("marca");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(51);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(306);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("final");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            ##########REGISTRO TOTALIZADOR DO ARQUIVO

            $inIndexArquivo++;
            $rsModalidades->proximo();
        }
        $obExportador->Show();
        Sessao::encerraExcecao();
        SistemaLegado::LiberaFrames();
    break;
}

function addMovimentacaoTrabalhador(&$obExportador,$arDados)
{
    $rsMovimentacaoTrabalhador = new RecordSet();
    $rsMovimentacaoTrabalhador->preenche($arDados);
    $obExportador->roUltimoArquivo->addBloco($rsMovimentacaoTrabalhador);
    unset($rsMovimentacaoTrabalhador);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_inscricao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inscricao_empresa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_inscricao_tomador");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inscricao_tomador");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("pis_pasep");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_admissao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("categoria_trabalhador");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_trabalhador");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(70);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_movimentacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_movimentacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicativo_recolhimento_fgts");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(225);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("final");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
}

function addRegistroTrabalhador(&$obExportador,$arDados)
{
    $rsRegistroTrabalhador = new RecordSet();
    $rsRegistroTrabalhador->preenche($arDados);
    $obExportador->roUltimoArquivo->addBloco($rsRegistroTrabalhador);
    unset($rsRegistroTrabalhador);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_inscricao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inscricao_empresa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_inscricao_tomador");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inscricao_tomador");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("pis_pasep");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_admissao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("categoria_trabalhador");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_trabalhador");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(70);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("matricula_empregado");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_ctps");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("serie_ctps");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_opcao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_nascimento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cbo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_sem_13");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_13");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("classe_contribuicao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ocorrencia");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_descontado");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_base");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("base_calculo_13_22");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("base_calculo_13_23");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(98);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("final");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
}

function separarDigito($stString)
{
    $inNumero = preg_replace( "/[^0-9a-zA-Z]/i","",$stString);
    $inDigito = $inNumero[strlen($inNumero)-1];
    $inNumero = substr($inNumero,0,strlen($inNumero)-1);

    return array($inNumero,$inDigito);
}

function removeAcentos($string)
{
    // assume $str esteja em UTF-8
    $acentos    = "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ";
    $semAcentos = "aaaaeeiooouucAAAAEEIOOOUUC";

    $keys = array();
    $values = array();
    preg_match_all('/./u', $acentos, $keys);
    preg_match_all('/./u', $semAcentos, $values);
    $mapping = array_combine($keys[0], $values[0]);

    return strtr($string, $mapping);
}

function processarFiltro(&$obTMapeamento)
{
    global $request;
    switch ($request->get('stTipoFiltro')) {
        case "contrato_todos":
        case "cgm_contrato_todos":
            foreach (Sessao::read('arContratos2') as $arContrato) {
                $stCodContrato .= $arContrato["cod_contrato"].",";
            }
            $stCodContrato = substr($stCodContrato,0,strlen($stCodContrato)-1);
            $stFiltro  = " AND contrato.cod_contrato IN (".$stCodContrato.")";
            break;
        case "lotacao":
            $obVOrganogramaOrgaoNivel = new VOrganogramaOrgaoNivel();
            foreach ($request->get('inCodLotacaoSelecionados') as $inCodLotacao) {
                $stCodLotacaoAux .= $inCodLotacao.',';
            }
            $stCodLotacaoAux = substr($stCodLotacaoAux,0,strlen($stCodLotacaoAux)-1);
            $stFiltroAux = " WHERE cod_orgao IN (".$stCodLotacaoAux.")";
            $obErro = $obVOrganogramaOrgaoNivel->recuperaTodos($rsNivelLotacao,$stFiltroAux,'',$boTransacao);
            //Buscando filhos de acordo com o estrutural
            if ( $rsNivelLotacao->getNumLinhas() > 0 ) {                
                foreach ($rsNivelLotacao->getElementos() as $orgaoNivel ) {
                    $stCodEstrutural .= $orgaoNivel['orgao_reduzido'].'%|';
                }
                $stCodEstrutural = substr($stCodEstrutural,0,strlen($stCodEstrutural)-1);
                $stFiltroAux = " WHERE orgao SIMILAR TO ('".$stCodEstrutural."')";
                $obErro = $obVOrganogramaOrgaoNivel->recuperaTodos($rsNivelLotacaoFilhos,$stFiltroAux,'',$boTransacao);                
                // atribuindo os cod_orgao dos niveis filhos
                foreach ( $rsNivelLotacaoFilhos->getElementos() as $orgaoNivelFilhos ) {                                
                    $stCodOrgao .= $orgaoNivelFilhos['cod_orgao'].',';
                }
            }else{                
                foreach ($request->get('inCodLotacaoSelecionados') as $inCodOrgao) {
                    $stCodOrgao .= $inCodOrgao.",";
                }
            }
            $stCodOrgao = substr($stCodOrgao,0,strlen($stCodOrgao)-1);
            $stFiltro  .= " AND contrato_servidor_orgao.cod_orgao in (".$stCodOrgao.") \n";
            break;
        case "local":
            foreach ($request->get('inCodLocalSelecionados') as $inCodLocal) {
                $stCodLocal .= $inCodLocal.",";
            }
            $stCodLocal = substr($stCodLocal,0,strlen($stCodLocal)-1);
            $inMes = $request->get('inCodMes');
            $inMes = str_pad($inMes, 2, "0", STR_PAD_LEFT);
            $dtCompetencia = $inMes."-".$request->get("inAno");
            $stFiltroPeriodo = " AND to_char(dt_final,'mm-yyyy') = '".$dtCompetencia."'";
            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltroPeriodo);
            $inCodPeriodoMovimentacao = $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');            
            $stJoin  = "    INNER JOIN (SELECT contrato_servidor_local.* 
                                        FROM pessoal.contrato_servidor_local
                                        INNER JOIN ( SELECT cod_contrato
                                                            ,cod_local
                                                            , MAX(timestamp) as timestamp
                                                        FROM pessoal.contrato_servidor_local
                                                        WHERE timestamp <= (ultimotimestampperiodomovimentacao(".$inCodPeriodoMovimentacao.",'".Sessao::getEntidade()."')::timestamp)
                                                        GROUP BY 1,2
                                                    )as max
                                            ON max.cod_contrato = contrato_servidor_local.cod_contrato
                                            AND max.cod_local = contrato_servidor_local.cod_local
                                            AND max.timestamp = contrato_servidor_local.timestamp
                            ) as contrato_servidor_local \n";
            $stJoin .= "       ON contrato.cod_contrato = contrato_servidor_local.cod_contrato         \n";
            $stJoin .= "      AND contrato_servidor_local.cod_local IN (".$stCodLocal.")               \n";                            
            $obTMapeamento->setDado("stJoin",$stJoin);
            break;
        case "atributos":
            $stJoin  = "     JOIN pessoal.atributo_contrato_servidor_valor                                      \n";
            $stJoin .= "       ON contrato.cod_contrato = atributo_contrato_servidor_valor.cod_contrato         \n";
            $stJoin .= "      AND atributo_contrato_servidor_valor.cod_atributo = ".$request->get("inCodAtributo")."   \n";
            $stJoin .= "      AND atributo_contrato_servidor_valor.valor = '".$request->get("Atributo_".$request->get("inCodAtributo")."_".$request->get("inCodCadastro"))."'   \n";
            $obTMapeamento->setDado("stJoin",$stJoin);
            break;
    }
    $stFiltro .= Sessao::read("stFiltroRegistroTrabalhadoresExtra");

    return $stFiltro;
}

function retornaContratosDoFiltro()
{
    $obTPessoalContratoServidor = new TPessoalContratoServidor();
    $stFiltro = processarFiltro($obTPessoalContratoServidor);
    $obTPessoalContratoServidor->recuperaContratosSEFIP($rsContratos,$stFiltro);
    $stCodContratos = "";
    while (!$rsContratos->eof()) {
        $stCodContratos .= $rsContratos->getCampo("cod_contrato").",";
        $rsContratos->proximo();
    }
    $stCodContratos = substr($stCodContratos,0,strlen($stCodContratos)-1);
    if (trim($stCodContratos) == "") {
        $stCodContratos = "null";
    }

    return $stCodContratos;
}

?>
