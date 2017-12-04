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
    * Página de Processamento do Conferência SEFIP
    * Data de Criação : 02/04/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30829 $
    $Name$
    $Autor: $
    $Date: 2008-01-24 08:25:23 -0200 (Qui, 24 Jan 2008) $

    * Casos de uso: uc-04.08.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ConferenciaSEFIP";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->setDado("mes", $request->get("inCodMes") );
$obTFolhaPagamentoPeriodoMovimentacao->setDado("ano",$request->get("inAno") );
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao,$stFiltro);
$stCompetencia = substr($rsPeriodoMovimentacao->getCampo("dt_final"),3,strlen($rsPeriodoMovimentacao->getCampo("dt_final")));

$inCodAtributo = 0;
$inCodTipoAtributo = 0;

switch ($request->get("stTipoFiltro")) {
    case "contrato":
    case "cgm_contrato":
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stCodigos .= $arContrato["cod_contrato"].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
        break;
    case "local":
        $inCodLocalSelecionados = $request->get('inCodLocalSelecionados');
        $stCodigos = trim(implode(",",$inCodLocalSelecionados));
        break;
    case "lotacao":
        $inCodLotacaoSelecionados = $request->get('inCodLotacaoSelecionados');
        $stCodigos = trim(implode(",",$inCodLotacaoSelecionados));
        break;
    case "atributo_servidor_grupo":
    case "atributo_pensionista_grupo":
        $inCodAtributo = $request->get("inCodAtributo");
        $inCodCadastro = $request->get("inCodCadastro");
        $stNome = "Atributo_".$inCodAtributo."_".$inCodCadastro;
        $arDados = $request->get($stNome."_Selecionados");
        if ( is_array($arDados) ) {
            $stCodigos = $request->get($stNome."_Selecionados");
            $stCodigos = implode(",",$stCodigos);
            $boAtributoMultiplo = 1;
        } else {
            $stCodigos = $request->get($stNome);
            $stCodigos = pg_escape_string($stCodigos);
            $boAtributoMultiplo = 0;
        }

        //Recupera o nome e o tipo do atributo
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoCadastro.class.php");
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php");
        $obTAdministracaoAtributoDinamico = new TAdministracaoAtributoDinamico();

        $rsAtributoDinamico = new RecordSet();
        $obTAdministracaoAtributoDinamico->setDado("cod_modulo",   22);
        $obTAdministracaoAtributoDinamico->setDado("cod_cadastro", $inCodCadastro);
        $obTAdministracaoAtributoDinamico->setDado("cod_atributo", $inCodAtributo);
        $obTAdministracaoAtributoDinamico->recuperaPorChave($rsAtributoDinamico);

        $stNomeAtributo    = $rsAtributoDinamico->getCampo("nom_atributo");
        $inCodTipoAtributo = $rsAtributoDinamico->getCampo("cod_tipo");
        break;
}

//gestaoRH/fontes/RPT/IMA/report/design/conferenciaSEFIP.rptdesign
$preview = new PreviewBirt(4,40,1);
$preview->setVersaoBirt("2.5.0");
$preview->setReturnURL( CAM_GRH_IMA_INSTANCIAS."relatorio/FLConferenciaSEFIP.php");
$preview->setTitulo('Conferência SEFIP');
$preview->setNomeArquivo('conferenciaSEFIP');
$preview->addParametro("entidade", Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro('inCodPeriodoMovimentacao',$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
$preview->addParametro('stCompetencia',$stCompetencia);
$preview->addParametro('stTipoFiltro',$request->get("stTipoFiltro"));
$preview->addParametro('stCodigos',$stCodigos);
$preview->addParametro('inCodAtributo',$inCodAtributo);
$preview->addParametro('inCodTipoAtributo',$inCodTipoAtributo);
$preview->addParametro('inCodRecolhimento',$request->get("inCodRecolhimento"));

$preview->preview();
?>