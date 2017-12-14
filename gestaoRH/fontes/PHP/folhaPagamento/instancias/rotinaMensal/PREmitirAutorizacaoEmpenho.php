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
    * Processamento
    * Data de Criação: 19/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 31360 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-02 14:35:46 -0300 (Ter, 02 Out 2007) $

    * Casos de uso: uc-04.05.62
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once( CAM_GF_EMP_NEGOCIO . "REmpenhoAutorizacaoEmpenho.class.php");

//Define o nome dos arquivos PHP
$stPrograma = 'EmitirAutorizacaoEmpenho';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obErro = new Erro;

if (Sessao::read("origem") != "d") {

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenho.class.php");
    $obTFolhaPagamentoConfiguracaoEmpenho = new TFolhaPagamentoConfiguracaoEmpenho();
    $stFiltro = " WHERE vigencia <= to_date('".Sessao::read('dt_final')."','dd/mm/yyyy')
                    AND to_char(ultima_vigencia_competencia.vigencia,'yyyy') = '".Sessao::getExercicio()."'";
    $stOrdem  = " ORDER BY dt_vigencia DESC LIMIT 1";
    $obTFolhaPagamentoConfiguracaoEmpenho->recuperaVigencias($rsVigencia,$stFiltro,$stOrdem);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAutorizacaoEmpenho.class.php");
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenho();
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->setDado("cod_configuracao_autorizacao",Sessao::read("inCodConfiguracaoAutorizacao"));
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->setDado("exercicio",$rsVigencia->getCampo('exercicio'));
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->setDado("vigencia",$rsVigencia->getCampo('dt_vigencia'));
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->setDado("timestamp",$rsVigencia->getCampo('timestamp'));
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->recuperaConfiguracaoAutorizacaoEmpenho($rsConfiguracaoAutorizacaoEmpenho);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico.class.php");
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico();
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico->setDado("cod_configuracao_autorizacao",Sessao::read("inCodConfiguracaoAutorizacao"));
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico->setDado("exercicio",$rsVigencia->getCampo('exercicio'));
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico->setDado("timestamp",$rsVigencia->getCampo('timestamp'));
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico->recuperaPorChave($rsHistorico);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento.class.php");
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento();
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento->setDado("cod_configuracao_autorizacao",Sessao::read("inCodConfiguracaoAutorizacao"));
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento->setDado("exercicio",$rsVigencia->getCampo('exercicio'));
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento->setDado("timestamp",$rsVigencia->getCampo('timestamp'));
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento->recuperaPorChave($rsComplemento);

    $inNumCGM          = $rsConfiguracaoAutorizacaoEmpenho->getCampo("numcgm");
    $stDescricao       = $rsConfiguracaoAutorizacaoEmpenho->getCampo("descricao_item");
    $inCodHistorico    = $rsHistorico->getCampo("cod_historico");
    $stComplementoItem = $rsComplemento->getCampo("complemento_item");
    $inQuantidade      = 1;
}

$arImpAut = array();
foreach (Sessao::read("arEmissaoEmpenho") as $arEmissaoEmpenho) {
    if ($arEmissaoEmpenho["red_dotacao"] != "") {
        if (Sessao::read("origem") == "d") {
            $inNumCGM          = $arEmissaoEmpenho["numcgm"];
            $stDescricao       = $arEmissaoEmpenho["motivo_viagem"];
            $inCodHistorico    = 0;
            $stComplementoItem = 'Pagamento de Diárias';
            $inQuantidade      = $arEmissaoEmpenho["quantidade"];
        }

        $obAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;
        $obAutorizacaoEmpenho->boAutViaPatrimonial = false;
        $obAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
        $obAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( Sessao::getCodEntidade($boTransacao) );
        $obAutorizacaoEmpenho->obREmpenhoTipoEmpenho->setCodTipo( 0 );
        $obAutorizacaoEmpenho->obRCGM->setNumCGM( $inNumCGM );
        $obAutorizacaoEmpenho->obREmpenhoHistorico->setCodHistorico( $inCodHistorico );
        $obAutorizacaoEmpenho->setDescricao( $stDescricao );
        $obAutorizacaoEmpenho->setCodCategoria(1);
        $obAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $arEmissaoEmpenho["red_dotacao"] );
        $obAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arEmissaoEmpenho["rubrica_despesa"] );
        $obAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setCodConta( $arEmissaoEmpenho["cod_conta"] );
        $obAutorizacaoEmpenho->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
        $obErro = $obAutorizacaoEmpenho->listarMaiorData($rsMaiorData);
        if (!$obErro->ocorreu()) {
            $obAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeInicial( $rsMaiorData->getCampo('data_autorizacao') );
        } else {
            $obAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeInicial( date('d/m/Y') );
        }
        $obAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeFinal( '31/12/'.date('Y') );
        $obAutorizacaoEmpenho->obROrcamentoReserva->setDtInclusao( date('d/m/Y'));
        $obAutorizacaoEmpenho->obROrcamentoReserva->setVlReserva( $arEmissaoEmpenho["valor"] );
        $arOrgao   = explode("-",$arEmissaoEmpenho["orgao"]);
        $arUnidade = explode("-",$arEmissaoEmpenho["unidade"]);

        include_once(CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenho.class.php");
        $obTEmpenhoAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho();
        $stFiltro  = " WHERE exercicio = '".Sessao::getExercicio()."'";
        $stFiltro .= "   AND cod_entidade = ".Sessao::getCodEntidade($boTransacao);
        $obTEmpenhoAutorizacaoEmpenho->recuperaMaiorDataAutorizacao($rsAutorizacao,$stFiltro);
        if ($rsAutorizacao->getCampo("data_autorizacao") == "") {
            include_once(CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php");
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho();
            $stFiltro  = "   AND e.exercicio = '".Sessao::getExercicio()."'";
            $stFiltro .= "   AND e.cod_entidade = ".Sessao::getCodEntidade($boTransacao);
            $obTEmpenhoEmpenho->recuperaMaiorDataEmpenho($rsEmpenho,$stFiltro);

            if ($rsEmpenho->getCampo("dataempenho") == "") {
                $dtAutorizacao = "01/01/".Sessao::getExercicio();
            } else {
                $dtAutorizacao = $rsEmpenho->getCampo("dataempenho");
            }
        } else {
            $dtAutorizacao = $rsAutorizacao->getCampo("data_autorizacao");
        }

        $obAutorizacaoEmpenho->setDtAutorizacao( $dtAutorizacao );
        $obAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $arOrgao[0] );
        $obAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade( $arUnidade[0] );

        //atributo modalidade
        //array temporario para relação entre modalidade licitacao e atributo modalidade do empenho
        // colocado o valor 7, que é referente a Modalidade "Não Aplicável"
        $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '101' , 7 );

        //atributo tipo credor
        $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '103' , 1 );

        //atributo complementar
        $obAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( '100' , 2 );

        include_once( CAM_GA_ADM_MAPEAMENTO. "TUnidadeMedida.class.php");
        $obTAdministracaoUnidadeMedida = new TUnidadeMedida();
        $obTAdministracaoUnidadeMedida->setDado("cod_unidade",1);
        $obTAdministracaoUnidadeMedida->setDado("cod_grandeza",7);
        $obTAdministracaoUnidadeMedida->recuperaPorChave($rsUnidadeMedida);

        $obAutorizacaoEmpenho->addItemPreEmpenho();
        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomItem($stDescricao);
        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->setCodUnidade( $rsUnidadeMedida->getCampo("cod_unidade") );
        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->obRGrandeza->setCodGrandeza( $rsUnidadeMedida->getCampo("cod_grandeza") );
        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setSiglaUnidade( $rsUnidadeMedida->getCampo("simbolo") );
        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomUnidade( $rsUnidadeMedida->getCampo( 'nom_unidade' ) );
        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setQuantidade($inQuantidade);
        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setValorTotal($arEmissaoEmpenho["valor"]);
        $obAutorizacaoEmpenho->roUltimoItemPreEmpenho->setComplemento($stComplementoItem);
        $obAutorizacaoEmpenho->setCodEntidade(Sessao::getCodEntidade());

        $obErro = $obAutorizacaoEmpenho->incluir();

        /* Salvar assinaturas configuráveis se houverem */
        $arAssinaturas = Sessao::read('assinaturas');

        if (array_key_exists('selecionadas', $arAssinaturas)) {

            if ( isset($arAssinaturas) && count($arAssinaturas['selecionadas']) > 0 ) {
                include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenhoAssinatura.class.php" );
                $arAssinatura = $arAssinaturas['selecionadas'];

                $obTEmpenhoAutorizacaoEmpenhoAssinatura = new TEmpenhoAutorizacaoEmpenhoAssinatura;
                $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'exercicio', Sessao::getExercicio() );
                $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'cod_entidade', Sessao::getCodEntidade($boTransacao) );
                $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'cod_autorizacao', $obAutorizacaoEmpenho->inCodAutorizacao );
                $arPapel = $obTEmpenhoAutorizacaoEmpenhoAssinatura->arrayPapel();

                foreach ($arAssinatura as $arAssina) {
                    $stPapel = (isset($arAssina['papel'])) ? $arAssina['papel'] : 0;
                    $inNumAssina = (isset($arPapel[$stPapel])) ? $arPapel[$stPapel] : 1;
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'num_assinatura', $inNumAssina );
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'numcgm', $arAssina['inCGM'] );
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'cargo', $arAssina['stCargo'] );
                    $obErro = $obTEmpenhoAutorizacaoEmpenhoAssinatura->inclusao( $boTransacao );
                }
                unset($obTEmpenhoAutorizacaoEmpenhoAssinatura);
                // Limpa Sessao->assinaturas
                $arAssinaturas = array( 'disponiveis' => array(), 'papeis' => array(), 'selecionadas' => array() );
                Sessao::write('assinaturas', $arAssinaturas);
            }
        }

        if (Sessao::read("origem") == "d") {
            include_once(CAM_GRH_DIA_MAPEAMENTO."TDiariasDiariaEmpenho.class.php");
            $obTDiariasDiariaEmpenho = new TDiariasDiariaEmpenho();
            $obTDiariasDiariaEmpenho->setDado("cod_diaria",$arEmissaoEmpenho["cod_diaria"]);
            $obTDiariasDiariaEmpenho->setDado("cod_contrato",$arEmissaoEmpenho["cod_contrato"]);
            $obTDiariasDiariaEmpenho->setDado("timestamp",$arEmissaoEmpenho["timestamp"]);
            $obTDiariasDiariaEmpenho->setDado("exercicio",Sessao::getExercicio());
            $obTDiariasDiariaEmpenho->setDado("cod_entidade",Sessao::getCodEntidade($boTransacao));
            $obTDiariasDiariaEmpenho->setDado("cod_autorizacao",$obAutorizacaoEmpenho->inCodAutorizacao);
            $obTDiariasDiariaEmpenho->inclusao($boTransacao);
        }

        $arImpAut[] = array(   "inCodAutorizacao"	=> $obAutorizacaoEmpenho->getCodAutorizacao(),
                               "inCodPreEmpenho" 	=> $obAutorizacaoEmpenho->getCodPreEmpenho(),
                               "inCodEntidade" 	    => Sessao::getCodEntidade($boTransacao),
                               "stDtAutorizacao" 	=> $obAutorizacaoEmpenho->getDtAutorizacao(),
                               "inCodDespesa" 		=> $obAutorizacaoEmpenho->obROrcamentoDespesa->getCodDespesa() );
    }
}

if (count($arImpAut) === 0) {
    $obErro->setDescricao("Não foi gerada nenhuma autorização de empenho, é necessário configurar a lotação na configuração da autorização do empenho.");
}

if ( !$obErro->ocorreu() ) {
    Sessao::write('stImpressaoAutorizacao',$arImpAut);
    $stCaminho = CAM_GF_EMP_INSTANCIAS."autorizacao/OCRelatorioAutorizacao.php";
    $stCampos  =  "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inCodEntidade=".Sessao::getCodEntidade($boTransacao);
    SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );
    sistemaLegado::alertaAviso( $pgForm.'?'.Sessao::getId(), "Autorização Gerada com Sucesso.", "incluir", "aviso", Sessao::getId(), "../");
} else {
    sistemaLegado::alertaAviso( $pgForm.'?'.Sessao::getId(), $obErro->getDescricao(), "n_incluir", "erro", Sessao::getId(), "../");
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
