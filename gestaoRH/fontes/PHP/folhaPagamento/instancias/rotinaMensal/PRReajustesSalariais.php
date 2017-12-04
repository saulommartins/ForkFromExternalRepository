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
    * Arquivo de Processamento
    * Data de Criação: 27/09/2007

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 31031 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-03 15:39:19 -0300 (Seg, 03 Mar 2008) $

    * Casos de uso: uc-04.05.26
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$link = Sessao::read("link");
$stAcao = $request->get('stAcao');
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ReajustesSalariais";
$pgFilt      = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList      = "LS".$stPrograma.".php?stAcao=$stAcao";
$pgForm      = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc      = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul      = "OC".$stPrograma.".php?stAcao=$stAcao";
$pgJS        = "JS".$stPrograma.".js";

Sessao::setTrataExcecao(true);

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajuste.class.php");
$obTFolhaPagamentoReajuste = new TFolhaPagamentoReajuste();

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajusteAbsoluto.class.php");
$obTFolhaPagamentoReajusteAbsoluto = new TFolhaPagamentoReajusteAbsoluto();
$obTFolhaPagamentoReajusteAbsoluto->obTFolhaPagamentoReajuste = &$obTFolhaPagamentoReajuste;

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajustePercentual.class.php");
$obTFolhaPagamentoReajustePercentual = new TFolhaPagamentoReajustePercentual();
$obTFolhaPagamentoReajustePercentual->obTFolhaPagamentoReajuste = &$obTFolhaPagamentoReajuste;

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

if (Sessao::read("stReajuste") == "e") {
    include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php");
    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
    $obRFolhaPagamentoConfiguracao->consultar();
    $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stCodigo = str_pad(trim(Sessao::read("inCodigoEvento")),strlen($stMascaraEvento),"0",STR_PAD_LEFT);
    $stFiltroEvento  = " WHERE codigo = '".$stCodigo."'";
    $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltroEvento);

    switch (Sessao::read("inCodConfiguracao")) {
        case 0:
            $stOrigem = "C";
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementar.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoComplementar.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementarParcela.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculadoDependente.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoComplementar.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajusteRegistroEventoComplementar.class.php");

            $obTFolhaPagamentoRegistroEventoComplementar               = new TFolhaPagamentoRegistroEventoComplementar();
            $obTFolhaPagamentoUltimoRegistroEventoComplementar         = new TFolhaPagamentoUltimoRegistroEventoComplementar();
            $obTFolhaPagamentoRegistroEventoComplementarParcela        = new TFolhaPagamentoRegistroEventoComplementarParcela;
            $obTFolhaPagamentoEventoComplementarCalculado              = new TFolhaPagamentoEventoComplementarCalculado;
            $obTFolhaPagamentoEventoComplementarCalculadoDependente    = new TFolhaPagamentoEventoComplementarCalculadoDependente();
            $obTFolhaPagamentoLogErroCalculoComplementar               = new TFolhaPagamentoLogErroCalculoComplementar;
            $obTFolhaPagamentoReajusteRegistroEventoComplementar       = new TFolhaPagamentoReajusteRegistroEventoComplementar;

            $obTFolhaPagamentoRegistroEventoComplementarParcela->obTFolhaPagamentoUltimoRegistroEventoComplementar        = &$obTFolhaPagamentoUltimoRegistroEventoComplementar;
            $obTFolhaPagamentoLogErroCalculoComplementar->obTFolhaPagamentoUltimoRegistroEventoComplementar               = &$obTFolhaPagamentoUltimoRegistroEventoComplementar;
            $obTFolhaPagamentoEventoComplementarCalculado->obTFolhaPagamentoUltimoRegistroEventoComplementar              = &$obTFolhaPagamentoUltimoRegistroEventoComplementar;
            $obTFolhaPagamentoEventoComplementarCalculadoDependente->obTFolhaPagamentoEventoComplementarCalculado         = &$obTFolhaPagamentoEventoComplementarCalculado;
            $obTFolhaPagamentoReajusteRegistroEventoComplementar->obTFolhaPagamentoRegistroEventoComplementar             = &$obTFolhaPagamentoRegistroEventoComplementar;
            $obTFolhaPagamentoUltimoRegistroEventoComplementar->obTFolhaPagamentoRegistroEventoComplementar               = &$obTFolhaPagamentoRegistroEventoComplementar;
            break;
        case 1:
            $stOrigem = "S";
            include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php");
            include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoPeriodo.class.php"      );
            include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEvento.class.php"       );
            include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoParcela.class.php"      );
            include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php"             );
            include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php"            );
            include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculadoDependente.class.php"  );
            include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajusteRegistroEvento.class.php"     );

            $obTFolhaPagamentoRegistroEvento             = new TFolhaPagamentoRegistroEvento;
            $obTFolhaPagamentoRegistroEventoPeriodo      = new TFolhaPagamentoRegistroEventoPeriodo();
            $obTFolhaPagamentoUltimoRegistroEvento       = new TFolhaPagamentoUltimoRegistroEvento();
            $obTFolhaPagamentoRegistroEventoParcela      = new TFolhaPagamentoRegistroEventoParcela;
            $obTFolhaPagamentoLogErroCalculo             = new TFolhaPagamentoLogErroCalculo;
            $obTFolhaPagamentoEventoCalculado            = new TFolhaPagamentoEventoCalculado;
            $obTFolhaPagamentoEventoCalculadoDependente  = new TFolhaPagamentoEventoCalculadoDependente;
            $obTFolhaPagamentoReajusteRegistroEvento     = new TFolhaPagamentoReajusteRegistroEvento;

            $obTFolhaPagamentoRegistroEvento->obTFolhaPagamentoRegistroEventoPeriodo       = &$obTFolhaPagamentoRegistroEventoPeriodo;
            $obTFolhaPagamentoUltimoRegistroEvento->obTFolhaPagamentoRegistroEvento        = &$obTFolhaPagamentoRegistroEvento;
            $obTFolhaPagamentoRegistroEventoParcela->obTFolhaPagamentoUltimoRegistroEvento = &$obTFolhaPagamentoUltimoRegistroEvento;
            $obTFolhaPagamentoLogErroCalculo->obTFolhaPagamentoUltimoRegistroEvento        = &$obTFolhaPagamentoUltimoRegistroEvento;
            $obTFolhaPagamentoEventoCalculado->obTFolhaPagamentoUltimoRegistroEvento       = &$obTFolhaPagamentoUltimoRegistroEvento;
            $obTFolhaPagamentoEventoCalculadoDependente->obTFolhaPagamentoEventoCalculado  = &$obTFolhaPagamentoEventoCalculado;
            $obTFolhaPagamentoReajusteRegistroEvento->obTFolhaPagamentoRegistroEvento      = &$obTFolhaPagamentoRegistroEvento;
            break;
        case 2:
            $stOrigem = "F";
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoFerias.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoFerias.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoFeriasParcela.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculado.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculadoDependente.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoFerias.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajusteRegistroEventoFerias.class.php");

            $obTFolhaPagamentoRegistroEventoFerias            = new TFolhaPagamentoRegistroEventoFerias();
            $obTFolhaPagamentoUltimoRegistroEventoFerias      = new TFolhaPagamentoUltimoRegistroEventoFerias();
            $obTFolhaPagamentoRegistroEventoFeriasParcela     = new TFolhaPagamentoRegistroEventoFeriasParcela;
            $obTFolhaPagamentoEventoFeriasCalculado           = new TFolhaPagamentoEventoFeriasCalculado;
            $obTFolhaPagamentoEventoFeriasCalculadoDependente = new TFolhaPagamentoEventoFeriasCalculadoDependente;
            $obTFolhaPagamentoLogErroCalculoFerias            = new TFolhaPagamentoLogErroCalculoFerias;
            $obTFolhaPagamentoReajusteRegistroEventoFerias    = new TFolhaPagamentoReajusteRegistroEventoFerias;

            $obTFolhaPagamentoUltimoRegistroEventoFerias->obTFolhaPagamentoRegistroEventoFerias         = &$obTFolhaPagamentoRegistroEventoFerias;
            $obTFolhaPagamentoRegistroEventoFeriasParcela->obTFolhaPagamentoUltimoRegistroEventoFerias  = &$obTFolhaPagamentoUltimoRegistroEventoFerias;
            $obTFolhaPagamentoEventoFeriasCalculado->obTFolhaPagamentoUltimoRegistroEventoFerias        = &$obTFolhaPagamentoUltimoRegistroEventoFerias;
            $obTFolhaPagamentoEventoFeriasCalculadoDependente->obTFolhaPagamentoEventoFeriasCalculado   = &$obTFolhaPagamentoEventoFeriasCalculado;
            $obTFolhaPagamentoLogErroCalculoFerias->obTFolhaPagamentoUltimoRegistroEventoFerias         = &$obTFolhaPagamentoUltimoRegistroEventoFerias;
            $obTFolhaPagamentoReajusteRegistroEventoFerias->obTFolhaPagamentoRegistroEventoFerias       = &$obTFolhaPagamentoRegistroEventoFerias;
            break;
        case 3:
            $stOrigem = "D";
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoDecimo.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoDecimo.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoDecimoParcela.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculado.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculadoDependente.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoDecimo.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajusteRegistroEventoDecimo.class.php");

            $obTFolhaPagamentoRegistroEventoDecimo            = new TFolhaPagamentoRegistroEventoDecimo();
            $obTFolhaPagamentoUltimoRegistroEventoDecimo      = new TFolhaPagamentoUltimoRegistroEventoDecimo();
            $obTFolhaPagamentoRegistroEventoDecimoParcela     = new TFolhaPagamentoRegistroEventoDecimoParcela;
            $obTFolhaPagamentoEventoDecimoCalculado           = new TFolhaPagamentoEventoDecimoCalculado;
            $obTFolhaPagamentoEventoDecimoCalculadoDependente = new TFolhaPagamentoEventoDecimoCalculadoDependente;
            $obTFolhaPagamentoLogErroCalculoDecimo            = new TFolhaPagamentoLogErroCalculoDecimo;
            $obTFolhaPagamentoReajusteRegistroEventoDecimo    = new TFolhaPagamentoReajusteRegistroEventoDecimo;

            $obTFolhaPagamentoUltimoRegistroEventoDecimo->obTFolhaPagamentoRegistroEventoDecimo        = &$obTFolhaPagamentoRegistroEventoDecimo;
            $obTFolhaPagamentoRegistroEventoDecimoParcela->obTFolhaPagamentoUltimoRegistroEventoDecimo = &$obTFolhaPagamentoUltimoRegistroEventoDecimo;
            $obTFolhaPagamentoEventoDecimoCalculado->obTFolhaPagamentoUltimoRegistroEventoDecimo       = &$obTFolhaPagamentoUltimoRegistroEventoDecimo;
            $obTFolhaPagamentoEventoDecimoCalculadoDependente->obTFolhaPagamentoEventoDecimoCalculado  = &$obTFolhaPagamentoEventoDecimoCalculado;
            $obTFolhaPagamentoLogErroCalculoDecimo->obTFolhaPagamentoUltimoRegistroEventoDecimo        = &$obTFolhaPagamentoUltimoRegistroEventoDecimo;
            $obTFolhaPagamentoReajusteRegistroEventoDecimo->obTFolhaPagamentoRegistroEventoDecimo      = &$obTFolhaPagamentoRegistroEventoDecimo;
            break;
        case 4:
            $stOrigem = "R";
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoRescisao.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoRescisao.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoRescisaoParcela.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculado.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculadoDependente.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoRescisao.class.php");
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajusteRegistroEventoRescisao.class.php");

            $obTFolhaPagamentoRegistroEventoRescisao            = new TFolhaPagamentoRegistroEventoRescisao();
            $obTFolhaPagamentoUltimoRegistroEventoRescisao      = new TFolhaPagamentoUltimoRegistroEventoRescisao();
            $obTFolhaPagamentoRegistroEventoRescisaoParcela     = new TFolhaPagamentoRegistroEventoRescisaoParcela;
            $obTFolhaPagamentoEventoRescisaoCalculado           = new TFolhaPagamentoEventoRescisaoCalculado;
            $obTFolhaPagamentoEventoRescisaoCalculadoDependente = new TFolhaPagamentoEventoRescisaoCalculadoDependente;
            $obTFolhaPagamentoLogErroCalculoRescisao            = new TFolhaPagamentoLogErroCalculoRescisao;
            $obTFolhaPagamentoReajusteRegistroEventoRescisao    = new TFolhaPagamentoReajusteRegistroEventoRescisao;

            $obTFolhaPagamentoUltimoRegistroEventoRescisao->obTFolhaPagamentoRegistroEventoRescisao         = &$obTFolhaPagamentoRegistroEventoRescisao;
            $obTFolhaPagamentoRegistroEventoRescisaoParcela->obTFolhaPagamentoUltimoRegistroEventoRescisao  = &$obTFolhaPagamentoUltimoRegistroEventoRescisao;
            $obTFolhaPagamentoEventoRescisaoCalculado->obTFolhaPagamentoUltimoRegistroEventoRescisao        = &$obTFolhaPagamentoUltimoRegistroEventoRescisao;
            $obTFolhaPagamentoEventoRescisaoCalculadoDependente->obTFolhaPagamentoEventoRescisaoCalculado   = &$obTFolhaPagamentoEventoRescisaoCalculado;
            $obTFolhaPagamentoLogErroCalculoRescisao->obTFolhaPagamentoUltimoRegistroEventoRescisao         = &$obTFolhaPagamentoUltimoRegistroEventoRescisao;
            $obTFolhaPagamentoReajusteRegistroEventoRescisao->obTFolhaPagamentoRegistroEventoRescisao       = &$obTFolhaPagamentoRegistroEventoRescisao;
            break;
    }
} else {
    $stOrigem = "P";
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoNivelPadraoNivel.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPadraoPadrao.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajustePadraoPadrao.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalPensionista.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSalario.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajusteContratoServidorSalario.class.php");

    $obTFolhaPagamentoNivelPadraoNivel                = new TFolhaPagamentoNivelPadraoNivel();
    $obTFolhaPagamentoPadraoPadrao                    = new TFolhaPagamentoPadraoPadrao();
    $obTFolhaPagamentoReajustePadraoPadrao            = new TFolhaPagamentoReajustePadraoPadrao();
    $obTPessoalPensionista                            = new TPessoalPensionista();
    $obTPessoalContratoServidorSalario                = new TPessoalContratoServidorSalario();
    $obTFolhaPagamentoPeriodoMovimentacao             = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoReajusteContratoServidorSalario = new TFolhaPagamentoReajusteContratoServidorSalario();

    $obTFolhaPagamentoReajustePadraoPadrao->obTFolhaPagamentoPadraoPadrao                = &$obTFolhaPagamentoPadraoPadrao;
    $obTFolhaPagamentoReajusteContratoServidorSalario->obTPessoalContratoServidorSalario = &$obTPessoalContratoServidorSalario;
}
/*********************************
* Inclusão do reajuste
*********************************/
if (trim($stAcao) == "incluir") {
    $nuFaixaInicial       = (float) str_replace(",",".",str_replace(".","",Sessao::read("nuFaixaInicial")));
    $nuFaixaFinal         = (float) str_replace(",",".",str_replace(".","",Sessao::read("nuFaixaFinal")));
    $nuPercentualReajuste = (float) str_replace(',','.',str_replace(".","",Sessao::read("nuPercentualReajuste")));
    $nuValorReajuste      = (float) str_replace(',','.',str_replace(".","",Sessao::read("nuValorReajuste")));
    $stTipoReajuste       = Sessao::read("stTipoReajuste");

    $obTFolhaPagamentoReajuste->proximoCod($inCodReajuste);
    $obTFolhaPagamentoReajuste->setDado("cod_reajuste" , $inCodReajuste);
    $obTFolhaPagamentoReajuste->setDado("numcgm"       , Sessao::read("numCgm"));
    $obTFolhaPagamentoReajuste->setDado("dt_reajuste"  , Sessao::read("dtVigencia"));
    $obTFolhaPagamentoReajuste->setDado("faixa_inicial", $nuFaixaInicial);
    $obTFolhaPagamentoReajuste->setDado("faixa_final"  , $nuFaixaFinal);
    $obTFolhaPagamentoReajuste->setDado("origem"       , $stOrigem);

    $obTFolhaPagamentoReajuste->inclusao();

    if ($stTipoReajuste == 'v') {
        $obTFolhaPagamentoReajusteAbsoluto->setDado('valor', $nuValorReajuste);
        $obTFolhaPagamentoReajusteAbsoluto->inclusao();
    } else {
        $obTFolhaPagamentoReajustePercentual->setDado('valor', $nuPercentualReajuste);
        $obTFolhaPagamentoReajustePercentual->inclusao();
    }

    if (Sessao::read("stReajuste") == "p") {
        if (Sessao::read("inCodPadrao") != "") {
            $stFiltro = " AND padrao_padrao.cod_padrao = ".Sessao::read("inCodPadrao");
        } else {
            $stFiltro = " AND padrao_padrao.cod_padrao IN (".implode(",",Sessao::read("arPadroes")).")";
        }
        $obTFolhaPagamentoPadraoPadrao->recuperaRelacionamento($rsPadroes,$stFiltro);
        while (!$rsPadroes->eof()) {
            $stFiltroNivel = " AND FPNP.cod_padrao = ".$rsPadroes->getCampo("cod_padrao");
            $obTFolhaPagamentoNivelPadraoNivel->recuperaRelacionamento($rsNivelPadrao,$stFiltroNivel);

            if ($stTipoReajuste == 'v') {
                $nuValorNovo = $nuValorReajuste;
            } else {
                $nuValorNovo = $rsPadroes->getCampo("valor")+(($rsPadroes->getCampo("valor")*$nuPercentualReajuste)/100);
            }
            $obTFolhaPagamentoPadraoPadrao->setDado("cod_padrao",$rsPadroes->getCampo("cod_padrao"));
            $obTFolhaPagamentoPadraoPadrao->setDado("valor",$nuValorNovo);
            $obTFolhaPagamentoPadraoPadrao->setDado("cod_norma",$rsPadroes->getCampo("cod_norma"));
            $obTFolhaPagamentoPadraoPadrao->setDado("vigencia",Sessao::read("dtVigencia"));
            $obTFolhaPagamentoPadraoPadrao->inclusao();

            $obTFolhaPagamentoReajustePadraoPadrao->setDado("cod_reajuste", $inCodReajuste);
            $obTFolhaPagamentoReajustePadraoPadrao->inclusao();

            while (!$rsNivelPadrao->eof()) {
                if ($stTipoReajuste == 'v') {
                    $nuValorNovo = $nuValorReajuste;
                } else {
                    $nuValorNovo = $rsNivelPadrao->getCampo("valor")+(($rsNivelPadrao->getCampo("valor")*$nuPercentualReajuste)/100);
                }
                $obTFolhaPagamentoNivelPadraoNivel->setDado("cod_padrao",$rsNivelPadrao->getCampo("cod_padrao"));
                $obTFolhaPagamentoNivelPadraoNivel->setDado("cod_nivel_padrao",$rsNivelPadrao->getCampo("cod_nivel_padrao"));
                $obTFolhaPagamentoNivelPadraoNivel->setDado("descricao",$rsNivelPadrao->getCampo("descricao"));
                $obTFolhaPagamentoNivelPadraoNivel->setDado("valor",$nuValorNovo);
                $obTFolhaPagamentoNivelPadraoNivel->setDado("percentual",$rsNivelPadrao->getCampo("percentual"));
                $obTFolhaPagamentoNivelPadraoNivel->setDado("qtdmeses",$rsNivelPadrao->getCampo("qtdmeses"));
                $obTFolhaPagamentoNivelPadraoNivel->inclusao();
                $rsNivelPadrao->proximo();
            }
            $rsPadroes->proximo();
        }

        foreach (Sessao::read("arRegistros") as $arRegistro) {
            if (Sessao::read("stCadastro") == "p") {
                $stFiltro = " AND contrato_pensionista.cod_contrato = ".$arRegistro["cod_contrato"];
                $obTPessoalPensionista->recuperaRelacionamento($rsPensionista,$stFiltro);
                $arRegistro["cod_contrato"] = $rsPensionista->getCampo("cod_contrato_cedente");
            }

            $stFiltroSalario = " AND salario.cod_contrato = ".$arRegistro["cod_contrato"];
            $obTPessoalContratoServidorSalario->recuperaRelacionamento($rsSalario,$stFiltroSalario);

            if ($stTipoReajuste == 'v') {
                $nuSalario = $nuValorReajuste;
            } else {
                $nuSalario = $rsSalario->getCampo("salario")+(($rsSalario->getCampo("salario")*$nuPercentualReajuste)/100);
            }
            $obTPessoalContratoServidorSalario->setDado("cod_contrato",$rsSalario->getCampo("cod_contrato"));
            $obTPessoalContratoServidorSalario->setDado("salario",$nuSalario);
            $obTPessoalContratoServidorSalario->setDado("horas_mensais",$rsSalario->getCampo("horas_mensais"));
            $obTPessoalContratoServidorSalario->setDado("horas_semanais",$rsSalario->getCampo("horas_semanais"));
            $obTPessoalContratoServidorSalario->setDado("vigencia",Sessao::read("dtVigencia"));
            $obTPessoalContratoServidorSalario->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTPessoalContratoServidorSalario->setDado("reajuste",true);
            $obTPessoalContratoServidorSalario->inclusao();

            $obTFolhaPagamentoReajusteContratoServidorSalario->setDado("cod_reajuste", $inCodReajuste);
            $obTFolhaPagamentoReajusteContratoServidorSalario->inclusao();
        }
    } else {
        // Incluindo reajuste por evento
        foreach (Sessao::read("arRegistros") as $arRegistro) {
            switch (Sessao::read("inCodConfiguracao")) {
                case 0:
                    $obTFolhaPagamentoRegistroEventoComplementar->recuperaNow3($stNow);

                    $stFiltro  = " AND registro_evento_complementar.cod_contrato = ".$arRegistro["cod_contrato"];
                    $stFiltro .= " AND registro_evento_complementar.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND registro_evento_complementar.cod_evento = ".$rsEvento->getCampo("cod_evento");
                    $stFiltro .= " AND registro_evento_complementar.cod_complementar = ".Sessao::read("inCodComplementar");
                    $obTFolhaPagamentoRegistroEventoComplementar->recuperaRelacionamento($rsRegistroEvento,$stFiltro);

                    while (!$rsRegistroEvento->eof()) {
                        $obTFolhaPagamentoRegistroEventoComplementar->proximoCod($inCodRegistro);

                        if (Sessao::read("hdnEventoFixado") == "Quantidade") {
                            $nuValor = $rsRegistroEvento->getCampo("valor");
                            if ($stTipoReajuste == 'v') {
                                $nuQuantidade = $nuValorReajuste;
                            } else {
                                $nuQuantidade = $rsRegistroEvento->getCampo("quantidade") + (($rsRegistroEvento->getCampo("quantidade")*$nuPercentualReajuste)/100);
                            }
                        } else {
                            if ($stTipoReajuste == 'v') {
                                $nuValor = $nuValorReajuste;
                            } else {
                                $nuValor = $rsRegistroEvento->getCampo("valor") + (($rsRegistroEvento->getCampo("valor")*$nuPercentualReajuste)/100);
                            }
                            $nuQuantidade = $rsRegistroEvento->getCampo("quantidade");
                        }

                        $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_registro"     , $rsRegistroEvento->getCampo("cod_registro"));
                        $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_evento"       , $rsRegistroEvento->getCampo("cod_evento"));
                        $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_configuracao" , $rsRegistroEvento->getCampo("cod_configuracao"));
                        $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("timestamp"        , $rsRegistroEvento->getCampo("timestamp"));

                        $obTFolhaPagamentoReajusteRegistroEventoComplementar->setDado("cod_reajuste", $inCodReajuste);

                        $obTFolhaPagamentoEventoComplementarCalculadoDependente->exclusao();
                        $obTFolhaPagamentoEventoComplementarCalculado->exclusao();
                        $obTFolhaPagamentoLogErroCalculoComplementar->exclusao();
                        $obTFolhaPagamentoRegistroEventoComplementarParcela->exclusao();
                        $obTFolhaPagamentoReajusteRegistroEventoComplementar->exclusao();
                        $obTFolhaPagamentoUltimoRegistroEventoComplementar->exclusao();
                        // exclusão finalizada

                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_registro"            ,$inCodRegistro   );
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_contrato"            ,$arRegistro["cod_contrato"]   );
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_complementar"        ,Sessao::read("inCodComplementar"));
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_evento"              ,$rsEvento->getCampo("cod_evento"));
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("valor"                   ,$nuValor);
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("quantidade"              ,$nuQuantidade);
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_configuracao"        ,$rsRegistroEvento->getCampo("cod_configuracao"));
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("timestamp"               ,$stNow);
                        $obTFolhaPagamentoRegistroEventoComplementar->inclusao();

                        $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_registro"    ,$inCodRegistro);
                        $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("timestamp"         ,$stNow);
                        $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_evento"        ,$rsEvento->getCampo("cod_evento"));
                        $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_configuracao"  ,$rsRegistroEvento->getCampo("cod_configuracao"));
                        $obTFolhaPagamentoUltimoRegistroEventoComplementar->inclusao();

                        $obTFolhaPagamentoReajusteRegistroEventoComplementar->inclusao();

                        $rsRegistroEvento->proximo();
                    }
                    break;
                case 1:
                    $stFiltro  = " AND contrato.cod_contrato = ".$arRegistro["cod_contrato"];
                    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND evento.cod_evento = ".$rsEvento->getCampo("cod_evento");
                    $stFiltro .= " AND registro_evento.proporcional = false ";

                    $stCampoCod         = $obTFolhaPagamentoRegistroEvento->getCampoCod();
                    $stComplementoChave = $obTFolhaPagamentoRegistroEvento->getComplementoChave();
                    $obTFolhaPagamentoRegistroEvento->setCampoCod("cod_registro");
                    $obTFolhaPagamentoRegistroEvento->setComplementoChave('');
                    $obTFolhaPagamentoRegistroEvento->proximoCod($inCodRegistro);
                    $obTFolhaPagamentoRegistroEvento->setCampoCod($stCampoCod);
                    $obTFolhaPagamentoRegistroEvento->setComplementoChave($stComplementoChave);

                    $obTFolhaPagamentoRegistroEvento->setDado("desdobramento", true);
                    $obTFolhaPagamentoRegistroEvento->recuperaRegistrosDeEventos($rsRegistroEvento,$stFiltro);
                    $obTFolhaPagamentoRegistroEvento->recuperaNow3($stNow);

                    $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro",                $inCodRegistro                                       );
                    $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_contrato",                $arRegistro["cod_contrato"]   );
                    $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_periodo_movimentacao",    $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                    $obTFolhaPagamentoRegistroEventoPeriodo->inclusao();
                    if (Sessao::read("hdnEventoFixado") == "Quantidade") {
                        $nuValor = $rsRegistroEvento->getCampo("valor");
                        if ($stTipoReajuste == 'v') {
                            $nuQuantidade = $nuValorReajuste;
                        } else {
                            $nuQuantidade = $rsRegistroEvento->getCampo("quantidade") + (($rsRegistroEvento->getCampo("quantidade")*$nuPercentualReajuste)/100);
                        }
                    } else {
                        if ($stTipoReajuste == 'v') {
                            $nuValor = $nuValorReajuste;
                        } else {
                            $nuValor = $rsRegistroEvento->getCampo("valor") + (($rsRegistroEvento->getCampo("valor")*$nuPercentualReajuste)/100);
                        }
                        $nuQuantidade = $rsRegistroEvento->getCampo("quantidade");
                    }

                    $obTFolhaPagamentoReajusteRegistroEvento->setDado("cod_reajuste", $inCodReajuste);

                    $obTFolhaPagamentoUltimoRegistroEvento->setDado('cod_registro', "" );
                    $obTFolhaPagamentoUltimoRegistroEvento->setDado('timestamp',    "" );
                    $obTFolhaPagamentoUltimoRegistroEvento->setDado('cod_evento',   "" );

                    while (!$rsRegistroEvento->eof()) {
                        $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro", $rsRegistroEvento->getCampo("cod_registro"));
                        $obTFolhaPagamentoRegistroEventoParcela->exclusao();
                        $obTFolhaPagamentoLogErroCalculo->exclusao();
                        $obTFolhaPagamentoEventoCalculadoDependente->exclusao();
                        $obTFolhaPagamentoEventoCalculado->exclusao();
                        $obTFolhaPagamentoReajusteRegistroEvento->exclusao();
                        $obTFolhaPagamentoUltimoRegistroEvento->exclusao();

                        $rsRegistroEvento->proximo();
                    }
                    // exclusão finalizada
                    $rsRegistroEvento->setPrimeiroElemento();
                    $obTFolhaPagamentoRegistroEvento->setDado("cod_registro",$inCodRegistro);
                    $obTFolhaPagamentoRegistroEvento->setDado("cod_evento",$rsRegistroEvento->getCampo("cod_evento"));
                    $obTFolhaPagamentoRegistroEvento->setDado("timestamp",$stNow);
                    $obTFolhaPagamentoRegistroEvento->setDado("valor",$nuValor);
                    $obTFolhaPagamentoRegistroEvento->setDado("quantidade",$nuQuantidade);
                    $obTFolhaPagamentoRegistroEvento->setDado("proporcional",$rsRegistroEvento->getCampo("proporcional"));
                    $obTFolhaPagamentoRegistroEvento->setDado("automatico",$rsRegistroEvento->getCampo("automatico"));
                    $obTFolhaPagamentoRegistroEvento->inclusao();

                    $obTFolhaPagamentoUltimoRegistroEvento->setDado('cod_registro',     $inCodRegistro                             );
                    $obTFolhaPagamentoUltimoRegistroEvento->setDado('timestamp',        $stNow                                     );
                    $obTFolhaPagamentoUltimoRegistroEvento->setDado('cod_evento',       $rsRegistroEvento->getCampo("cod_evento")  );
                    $obTFolhaPagamentoUltimoRegistroEvento->inclusao();

                    $obTFolhaPagamentoReajusteRegistroEvento->inclusao();

                    break;
                case 2:
                    $stFiltro  = " AND registro_evento_ferias.cod_contrato = ".$arRegistro["cod_contrato"];
                    $stFiltro .= " AND registro_evento_ferias.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND registro_evento_ferias.cod_evento = ".$rsEvento->getCampo("cod_evento");
                    $obTFolhaPagamentoRegistroEventoFerias->recuperaRelacionamento($rsRegistroEvento,$stFiltro);

                    while (!$rsRegistroEvento->eof()) {
                        $obTFolhaPagamentoRegistroEventoFerias->proximoCod($inCodRegistro);

                        if (Sessao::read("hdnEventoFixado") == "Quantidade") {
                            $nuValor = $rsRegistroEvento->getCampo("valor");
                            if ($stTipoReajuste == 'v') {
                                $nuQuantidade = $nuValorReajuste;
                            } else {
                                $nuQuantidade = $rsRegistroEvento->getCampo("quantidade") + (($rsRegistroEvento->getCampo("quantidade")*$nuPercentualReajuste)/100);
                            }
                        } else {
                            if ($stTipoReajuste == 'v') {
                                $nuValor = $nuValorReajuste;
                            } else {
                                $nuValor = $rsRegistroEvento->getCampo("valor") + (($rsRegistroEvento->getCampo("valor")*$nuPercentualReajuste)/100);
                            }
                            $nuQuantidade = $rsRegistroEvento->getCampo("quantidade");
                        }

                        $obTFolhaPagamentoUltimoRegistroEventoFerias->setDado("cod_registro"  ,$rsRegistroEvento->getCampo("cod_registro"));
                        $obTFolhaPagamentoUltimoRegistroEventoFerias->setDado("cod_evento"    ,$rsRegistroEvento->getCampo("cod_evento"));
                        $obTFolhaPagamentoUltimoRegistroEventoFerias->setDado("desdobramento" ,$rsRegistroEvento->getCampo("desdobramento"));
                        $obTFolhaPagamentoUltimoRegistroEventoFerias->setDado("timestamp"     ,$rsRegistroEvento->getCampo("timestamp"));

                        $obTFolhaPagamentoReajusteRegistroEventoFerias->setDado("cod_reajuste", $inCodReajuste);

                        $obTFolhaPagamentoRegistroEventoFeriasParcela->exclusao();
                        $obTFolhaPagamentoEventoFeriasCalculadoDependente->exclusao();
                        $obTFolhaPagamentoEventoFeriasCalculado->exclusao();
                        $obTFolhaPagamentoLogErroCalculoFerias->exclusao();
                        $obTFolhaPagamentoReajusteRegistroEventoFerias->exclusao();
                        $obTFolhaPagamentoUltimoRegistroEventoFerias->exclusao();
                        // exclusão finalizada

                        $obTFolhaPagamentoRegistroEventoFerias->setDado("cod_registro"            ,$inCodRegistro);
                        $obTFolhaPagamentoRegistroEventoFerias->setDado("cod_evento"              ,$rsEvento->getCampo("cod_evento"));
                        $obTFolhaPagamentoRegistroEventoFerias->setDado("valor"                   ,$nuValor);
                        $obTFolhaPagamentoRegistroEventoFerias->setDado("quantidade"              ,$nuQuantidade);
                        $obTFolhaPagamentoRegistroEventoFerias->setDado("cod_contrato",$arRegistro["cod_contrato"]);
                        $obTFolhaPagamentoRegistroEventoFerias->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                        $obTFolhaPagamentoRegistroEventoFerias->setDado("desdobramento"           ,$rsRegistroEvento->getCampo("desdobramento"));
                        $obTFolhaPagamentoRegistroEventoFerias->inclusao();

                        $obTFolhaPagamentoUltimoRegistroEventoFerias->inclusao();

                        $obTFolhaPagamentoReajusteRegistroEventoFerias->inclusao();

                        $rsRegistroEvento->proximo();
                    }
                    break;
                case 3:
                    $stFiltro  = " AND registro_evento_decimo.cod_contrato = ".$arRegistro["cod_contrato"];
                    $stFiltro .= " AND registro_evento_decimo.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND registro_evento_decimo.cod_evento = ".$rsEvento->getCampo("cod_evento");
                    $obTFolhaPagamentoRegistroEventoDecimo->recuperaRelacionamento($rsRegistroEvento,$stFiltro);

                    while (!$rsRegistroEvento->eof()) {
                        $obTFolhaPagamentoRegistroEventoDecimo->proximoCod($inCodRegistro);

                        if (Sessao::read("hdnEventoFixado") == "Quantidade") {
                            $nuValor = $rsRegistroEvento->getCampo("valor");
                            if ($stTipoReajuste == 'v') {
                                $nuQuantidade = $nuValorReajuste;
                            } else {
                                $nuQuantidade = $rsRegistroEvento->getCampo("quantidade") + (($rsRegistroEvento->getCampo("quantidade")*$nuPercentualReajuste)/100);
                            }
                        } else {
                            if ($stTipoReajuste == 'v') {
                                $nuValor = $nuValorReajuste;
                            } else {
                                $nuValor = $rsRegistroEvento->getCampo("valor") + (($rsRegistroEvento->getCampo("valor")*$nuPercentualReajuste)/100);
                            }
                            $nuQuantidade = $rsRegistroEvento->getCampo("quantidade");
                        }

                        $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("cod_registro"  ,$rsRegistroEvento->getCampo("cod_registro"));
                        $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("cod_evento"    ,$rsRegistroEvento->getCampo("cod_evento"));
                        $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("desdobramento" ,$rsRegistroEvento->getCampo("desdobramento"));
                        $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("timestamp"     ,$rsRegistroEvento->getCampo("timestamp"));

                        $obTFolhaPagamentoReajusteRegistroEventoDecimo->setDado("cod_reajuste", $inCodReajuste);

                        $obTFolhaPagamentoRegistroEventoDecimoParcela->exclusao();
                        $obTFolhaPagamentoEventoDecimoCalculadoDependente->exclusao();
                        $obTFolhaPagamentoEventoDecimoCalculado->exclusao();
                        $obTFolhaPagamentoLogErroCalculoDecimo->exclusao();
                        $obTFolhaPagamentoReajusteRegistroEventoDecimo->exclusao();
                        $obTFolhaPagamentoUltimoRegistroEventoDecimo->exclusao();
                        // exclusão finalizada

                        $obTFolhaPagamentoRegistroEventoDecimo->setDado("cod_registro"            ,$inCodRegistro);
                        $obTFolhaPagamentoRegistroEventoDecimo->setDado("cod_evento"              ,$rsEvento->getCampo("cod_evento"));
                        $obTFolhaPagamentoRegistroEventoDecimo->setDado("valor"                   ,$nuValor);
                        $obTFolhaPagamentoRegistroEventoDecimo->setDado("quantidade"              ,$nuQuantidade);
                        $obTFolhaPagamentoRegistroEventoDecimo->setDado("cod_contrato",$arRegistro["cod_contrato"]);
                        $obTFolhaPagamentoRegistroEventoDecimo->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                        $obTFolhaPagamentoRegistroEventoDecimo->setDado("desdobramento"           ,$rsRegistroEvento->getCampo("desdobramento"));
                        $obTFolhaPagamentoRegistroEventoDecimo->inclusao();

                        $obTFolhaPagamentoUltimoRegistroEventoDecimo->inclusao();

                        $obTFolhaPagamentoReajusteRegistroEventoDecimo->inclusao();

                        $rsRegistroEvento->proximo();
                    }
                    break;
                case 4:
                    $stFiltro  = " AND registro_evento_rescisao.cod_contrato = ".$arRegistro["cod_contrato"];
                    $stFiltro .= " AND registro_evento_rescisao.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= " AND registro_evento_rescisao.cod_evento = ".$rsEvento->getCampo("cod_evento");
                    $obTFolhaPagamentoRegistroEventoRescisao->recuperaRelacionamento($rsRegistroEvento,$stFiltro);

                    while (!$rsRegistroEvento->eof()) {
                        $obTFolhaPagamentoRegistroEventoRescisao->proximoCod($inCodRegistro);

                        if (Sessao::read("hdnEventoFixado") == "Quantidade") {
                            $nuValor = $rsRegistroEvento->getCampo("valor");
                            if ($stTipoReajuste == 'v') {
                                $nuQuantidade = $nuValorReajuste;
                            } else {
                                $nuQuantidade = $rsRegistroEvento->getCampo("quantidade") + (($rsRegistroEvento->getCampo("quantidade")*$nuPercentualReajuste)/100);
                            }
                        } else {
                            if ($stTipoReajuste == 'v') {
                                $nuValor = $nuValorReajuste;
                            } else {
                                $nuValor = $rsRegistroEvento->getCampo("valor") + (($rsRegistroEvento->getCampo("valor")*$nuPercentualReajuste)/100);
                            }
                            $nuQuantidade = $rsRegistroEvento->getCampo("quantidade");
                        }

                        $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("cod_registro"  ,$rsRegistroEvento->getCampo("cod_registro"));
                        $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("cod_evento"    ,$rsRegistroEvento->getCampo("cod_evento"));
                        $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("desdobramento" ,$rsRegistroEvento->getCampo("desdobramento"));
                        $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("timestamp"     ,$rsRegistroEvento->getCampo("timestamp"));

                        $obTFolhaPagamentoReajusteRegistroEventoRescisao->setDado("cod_reajuste", $inCodReajuste);

                        $obTFolhaPagamentoRegistroEventoRescisaoParcela->exclusao();
                        $obTFolhaPagamentoEventoRescisaoCalculadoDependente->exclusao();
                        $obTFolhaPagamentoEventoRescisaoCalculado->exclusao();
                        $obTFolhaPagamentoLogErroCalculoRescisao->exclusao();
                        $obTFolhaPagamentoReajusteRegistroEventoRescisao->exclusao();
                        $obTFolhaPagamentoUltimoRegistroEventoRescisao->exclusao();
                        // exclusão finalizada

                        $obTFolhaPagamentoRegistroEventoRescisao->setDado("cod_registro"            ,$inCodRegistro);
                        $obTFolhaPagamentoRegistroEventoRescisao->setDado("cod_evento"              ,$rsEvento->getCampo("cod_evento"));
                        $obTFolhaPagamentoRegistroEventoRescisao->setDado("valor"                   ,$nuValor);
                        $obTFolhaPagamentoRegistroEventoRescisao->setDado("quantidade"              ,$nuQuantidade);
                        $obTFolhaPagamentoRegistroEventoRescisao->setDado("cod_contrato",$arRegistro["cod_contrato"]);
                        $obTFolhaPagamentoRegistroEventoRescisao->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                        $obTFolhaPagamentoRegistroEventoRescisao->setDado("desdobramento"           ,$rsRegistroEvento->getCampo("desdobramento"));
                        $obTFolhaPagamentoRegistroEventoRescisao->inclusao();

                        $obTFolhaPagamentoUltimoRegistroEventoRescisao->inclusao();

                        $obTFolhaPagamentoReajusteRegistroEventoRescisao->inclusao();

                        $rsRegistroEvento->proximo();
                    }
                    break;
            }
        }
    }

    // Assentamento
    if (Sessao::read("stCadastro") != "p") {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAssentamento.class.php");
        $obTPessoalAssentamentoAssentamento = new TPessoalAssentamentoAssentamento();
        $stFiltro  = " AND classificacao_assentamento.cod_tipo = 1";
        $stFiltro .= " AND assentamento_assentamento.cod_motivo = 8";
        $obTPessoalAssentamentoAssentamento->recuperaAssentamento($rsAssentamentoAssentamento,$stFiltro);

        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoContratoServidor.class.php" );
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php" );
        include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalRegistrarEventoPorAssentamento.class.php" );
        $obTPessoalAssentamentoGeradoContratoServidor = new TPessoalAssentamentoGeradoContratoServidor;
        $obTPessoalAssentamentoGerado = new TPessoalAssentamentoGerado;

        foreach (Sessao::read("arRegistros") as $arRegistro) {
            //gera timestamp
            $timestamp = date('Y-m-d H:i:s');

            $obTPessoalAssentamentoGeradoContratoServidor->proximoCod( $inCodAssentamentoGerado );

            $obTPessoalAssentamentoGeradoContratoServidor->setDado( "cod_assentamento_gerado" , $inCodAssentamentoGerado );
            $obTPessoalAssentamentoGeradoContratoServidor->setDado( "cod_contrato"            , $arRegistro["cod_contrato"] );
            $obTPessoalAssentamentoGeradoContratoServidor->inclusao( Sessao::getTransacao()->inTransacao );

            $obTPessoalAssentamentoGerado->setDado( "cod_assentamento_gerado" , $inCodAssentamentoGerado                                 );
            $obTPessoalAssentamentoGerado->setDado( "cod_assentamento"        , $rsAssentamentoAssentamento->getCampo("cod_assentamento"));
            $obTPessoalAssentamentoGerado->setDado( "periodo_inicial"         , Sessao::read("dtVigencia")                               );
            $obTPessoalAssentamentoGerado->setDado( "periodo_final"           , Sessao::read("dtVigencia")                               );
            $obTPessoalAssentamentoGerado->setDado( "automatico"              , true                                                     );
            $obTPessoalAssentamentoGerado->setDado( "observacao"              , Sessao::read("stObservacao")                             );
            $obTPessoalAssentamentoGerado->setDado( "timestamp"               , $timestamp                                               );
            $obTPessoalAssentamentoGerado->inclusao();

            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoNorma.class.php");
            $obTPessoalAssentamentoGeradoNorma = new TPessoalAssentamentoGeradoNorma();
            $obTPessoalAssentamentoGeradoNorma->setDado('cod_assentamento_gerado', $obTPessoalAssentamentoGerado->getDado('cod_assentamento_gerado'));
            $obTPessoalAssentamentoGeradoNorma->setDado('timestamp'              , $timestamp);
            $obTPessoalAssentamentoGeradoNorma->setDado('cod_norma'              , Sessao::read('inCodNorma'));
            $obTPessoalAssentamentoGeradoNorma->inclusao();
        }
    }
    // Assentamento
    $pgRetorno = $pgForm;
    $stMensagem = "Todos os reajustes foram realizados com sucesso.";
} else {
    /***************************
    * Exlusão do reajuste
    ***************************/
    $arRegistros = Sessao::read("arRegistrosExclusao");
    Sessao::write("arRegistros", $arRegistros);

    list($inCodReajusteExclusao, $stOrigemExclusao) = explode("*_*", Sessao::read("inCodReajuste"));

    foreach (Sessao::read("arRegistros") as $arRegistro) {
        // Excluindo o assentamento
        if (Sessao::read("stCadastro") != "p") {
            include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php" );
            include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoExcluido.class.php" );

            $obTPessoalAssentamentoGerado = new TPessoalAssentamentoGerado;
            $obTPessoalAssentamentoGeradoExcluido = new TPessoalAssentamentoGeradoExcluido;

            $stFiltro  = " AND classificacao_assentamento.cod_tipo = 1";
            $stFiltro .= " AND assentamento_assentamento.cod_motivo = 8";
            $stFiltro .= " AND assentamento_gerado_contrato_servidor.cod_contrato = ".$arRegistro["cod_contrato"];
            $obTPessoalAssentamentoGerado->recuperaAssentamentoGerado($rsAssentamentoGerado, $stFiltro, "timestamp DESC LIMIT 1");

            $obTPessoalAssentamentoGeradoExcluido->setDado( "cod_assentamento_gerado" , $rsAssentamentoGerado->getCampo("cod_assentamento_gerado") );
            $obTPessoalAssentamentoGeradoExcluido->setDado( "timestamp"               , $rsAssentamentoGerado->getCampo("timestamp") );
            $obTPessoalAssentamentoGeradoExcluido->setDado( "descricao"               , "Exclusão de assentamento gerado por consequência da exclusão do reajuste salarial." );
            $obTPessoalAssentamentoGeradoExcluido->inclusao();
        }
        // Excluindo o assentamento

        if (Sessao::read("stReajuste") == "p") {
            if (Sessao::read("stCadastro") == "p") {
                $stFiltro = " AND contrato_pensionista.cod_contrato = ".$arRegistro["cod_contrato"];
                $obTPessoalPensionista->recuperaRelacionamento($rsPensionista,$stFiltro);
                $arRegistro["cod_contrato"] = $rsPensionista->getCampo("cod_contrato_cedente");
            }

            $stFiltro = " WHERE cod_contrato = ".$arRegistro["cod_contrato"];
            $stFiltro .= "  AND cod_reajuste >= ".$inCodReajusteExclusao;
            $obTFolhaPagamentoReajusteContratoServidorSalario->recuperaTodos($rsReajusteContratoServidorSalario,$stFiltro);

            if ($rsReajusteContratoServidorSalario->getNumLinhas() != -1) {
                $stFiltro  = " WHERE cod_contrato = ".$arRegistro["cod_contrato"];
                $stFiltro .= "   AND timestamp >= '".$rsReajusteContratoServidorSalario->getCampo("timestamp")."'";
                $obTPessoalContratoServidorSalario->recuperaTodos($rsContratoServidorSalario, $stFiltro);

                while (!$rsContratoServidorSalario->eof()) {
                    $obTFolhaPagamentoReajusteContratoServidorSalario->setDado("cod_contrato", $arRegistro["cod_contrato"]);
                    $obTFolhaPagamentoReajusteContratoServidorSalario->setDado("cod_reajuste", $rsContratoServidorSalario->getCampo("cod_reajuste"));
                    $obTFolhaPagamentoReajusteContratoServidorSalario->setDado("timestamp"   , $rsContratoServidorSalario->getCampo("timestamp"));
                    $obTFolhaPagamentoReajusteContratoServidorSalario->exclusao();

                    $obTPessoalContratoServidorSalario->setDado("cod_contrato", $rsContratoServidorSalario->getCampo("cod_contrato"));
                    $obTPessoalContratoServidorSalario->setDado("timestamp"   , $rsContratoServidorSalario->getCampo("timestamp"));
                    $obTPessoalContratoServidorSalario->exclusao();

                    $rsContratoServidorSalario->proximo();
                }

                // Exclusão do padrão e níveis
                // Verifica se existe algum contrato para o padrao, caso não acha, excluir o reajuste do padrao, nivel e padrao
                $stFiltro  = " WHERE cod_reajuste >= ".$inCodReajusteExclusao;
                $stFiltro .= "   AND origem = 'P'";
                $obTFolhaPagamentoReajuste->recuperaTodos($rsReajuste, $stFiltro);

                while (!$rsReajuste->eof()) {
                    $stFiltro = "  AND cod_reajuste = ".$rsReajuste->getCampo("cod_reajuste");
                    $obTFolhaPagamentoReajusteContratoServidorSalario->setDado("padrao", $arRegistro["cod_padrao"]);
                    $obTFolhaPagamentoReajusteContratoServidorSalario->recuperaReajuste($rsReajusteContratoServidorSalario, $stFiltro);

                    if ($rsReajusteContratoServidorSalario->getNumLinhas() == -1) {
                        $stFiltro = " WHERE cod_padrao = ".$arRegistro["cod_padrao"];
                        $stFiltro .= " AND cod_reajuste = ".$rsReajuste->getCampo("cod_reajuste");
                        $obTFolhaPagamentoReajustePadraoPadrao->recuperaTodos($rsReajustePadraoPadrao,$stFiltro);

                        // Exclui reajuste e todos os niveis do padrao
                        while (!$rsReajustePadraoPadrao->eof()) {
                            $obTFolhaPagamentoPadraoPadrao->setDado("cod_padrao"          , $rsReajustePadraoPadrao->getCampo("cod_padrao"));
                            $obTFolhaPagamentoPadraoPadrao->setDado("timestamp"           , $rsReajustePadraoPadrao->getCampo("timestamp"));
                            $obTFolhaPagamentoReajustePadraoPadrao->setDado("cod_reajuste", $rsReajustePadraoPadrao->getCampo("cod_reajuste"));

                            $obTFolhaPagamentoReajustePadraoPadrao->exclusao();

                            $stFiltroNivel  = " AND FPNP.cod_padrao = ".$rsReajustePadraoPadrao->getCampo("cod_padrao");
                            $stFiltroNivel .= " AND FPNP.timestamp = '".$rsReajustePadraoPadrao->getCampo("timestamp")."'";
                            $obTFolhaPagamentoNivelPadraoNivel->recuperaRelacionamento($rsNivelPadraoNivel,$stFiltroNivel);

                            while (!$rsNivelPadraoNivel->eof()) {
                                $obTFolhaPagamentoNivelPadraoNivel->setDado("cod_padrao"      , $rsNivelPadraoNivel->getCampo("cod_padrao"));
                                $obTFolhaPagamentoNivelPadraoNivel->setDado("timestamp"       , $rsNivelPadraoNivel->getCampo("timestamp"));
                                $obTFolhaPagamentoNivelPadraoNivel->setDado("cod_nivel_padrao", $rsNivelPadraoNivel->getCampo("cod_nivel_padrao"));
                                $obTFolhaPagamentoNivelPadraoNivel->exclusao();

                                $rsNivelPadraoNivel->proximo();
                            }
                            $rsReajustePadraoPadrao->proximo();
                        }

                        // exclui o padrao
                        $rsReajustePadraoPadrao->setPrimeiroElemento();
                        while (!$rsReajustePadraoPadrao->eof()) {
                            $stFiltro  = " WHERE cod_padrao = ".$rsReajustePadraoPadrao->getCampo("cod_padrao");
                            $stFiltro .= " AND timestamp = '".$rsReajustePadraoPadrao->getCampo("timestamp")."'";
                            $obTFolhaPagamentoPadraoPadrao->recuperaTodos($rsPadraoPadrao,$stFiltro);

                            while (!$rsPadraoPadrao->eof()) {
                                $obTFolhaPagamentoPadraoPadrao->setDado("timestamp" , $rsPadraoPadrao->getCampo("timestamp"));
                                $obTFolhaPagamentoPadraoPadrao->exclusao();

                                $rsPadraoPadrao->proximo();
                            }
                            $rsReajustePadraoPadrao->proximo();
                        }
                    }

                    // Excluindo o reajuste
                    $stFiltro = " WHERE cod_reajuste = ".$rsReajuste->getCampo("cod_reajuste");
                    $obTFolhaPagamentoReajustePadraoPadrao->recuperaTodos($rsReajustePadraoPadrao, $stFiltro);
                    if ($rsReajustePadraoPadrao->getNumLinhas() == -1) {
                        $obTFolhaPagamentoReajusteContratoServidorSalario->recuperaTodos($rsReajusteContratoServidorSalario, $stFiltro);
                        if ($rsReajusteContratoServidorSalario->getNumLinhas() == -1) {
                            $obTFolhaPagamentoReajuste->setDado("cod_reajuste", $rsReajuste->getCampo("cod_reajuste"));

                            $obTFolhaPagamentoReajusteAbsoluto->exclusao();
                            $obTFolhaPagamentoReajustePercentual->exclusao();
                            $obTFolhaPagamentoReajuste->exclusao();
                        }
                    }
                    $rsReajuste->proximo();
                }
            }

        } else {
            switch (Sessao::read("inCodConfiguracao")) {
                case 0:
                    // Caso exista cadastro de registro de evento após o reajuste, excluir o registro
                    $stFiltro  = " WHERE registro_evento_complementar.cod_contrato = ".$arRegistro["cod_contrato"];
                    $stFiltro .= "   AND registro_evento_complementar.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= "   AND registro_evento_complementar.cod_evento = ".$rsEvento->getCampo("cod_evento");
                    $stFiltro .= "   AND registro_evento_complementar.cod_complementar = ".Sessao::read("inCodComplementar");
                    $stFiltro .= "   AND registro_evento_complementar.timestamp >= '".$arRegistro["timestamp"]."'";
                    $obTFolhaPagamentoRegistroEventoComplementar->recuperaTodos($rsRegistroEventoComplementar,$stFiltro);

                    $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_registro"      , "");
                    $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("timestamp"         , "");
                    $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_evento"        , "");
                    $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_configuracao"  , "");

                    while (!$rsRegistroEventoComplementar->eof()) {
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_registro"     , $rsRegistroEventoComplementar->getCampo("cod_registro"));
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_evento"       , $rsRegistroEventoComplementar->getCampo("cod_evento"));
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_configuracao" , $rsRegistroEventoComplementar->getCampo("cod_configuracao"));
                        $obTFolhaPagamentoRegistroEventoComplementar->setDado("timestamp"        , $rsRegistroEventoComplementar->getCampo("timestamp"));

                        $obTFolhaPagamentoReajusteRegistroEventoComplementar->exclusao();
                        $obTFolhaPagamentoEventoComplementarCalculadoDependente->exclusao();
                        $obTFolhaPagamentoEventoComplementarCalculado->exclusao();
                        $obTFolhaPagamentoLogErroCalculoComplementar->exclusao();
                        $obTFolhaPagamentoRegistroEventoComplementarParcela->exclusao();
                        $obTFolhaPagamentoUltimoRegistroEventoComplementar->exclusao();
                        $obTFolhaPagamentoRegistroEventoComplementar->exclusao();

                        $rsRegistroEventoComplementar->proximo();
                    }

                    $stFiltro  = " WHERE cod_reajuste >= ".$inCodReajusteExclusao;
                    $stFiltro .= "   AND origem = 'C'";
                    $obTFolhaPagamentoReajuste->recuperaTodos($rsReajuste, $stFiltro);

                    while (!$rsReajuste->eof()) {
                        $stFiltro = " WHERE cod_reajuste = ".$rsReajuste->getCampo("cod_reajuste");
                        $obTFolhaPagamentoReajusteRegistroEventoComplementar->recuperaTodos($rsReajusteRegistroEventoComplementar, $stFiltro);

                        if ($rsReajusteRegistroEventoComplementar->getNumLinhas() == -1) {
                            $obTFolhaPagamentoReajuste->setDado("cod_reajuste", $rsReajuste->getCampo("cod_reajuste"));

                            $obTFolhaPagamentoReajusteAbsoluto->exclusao();
                            $obTFolhaPagamentoReajustePercentual->exclusao();
                            $obTFolhaPagamentoReajuste->exclusao();
                        }

                        $rsReajuste->proximo();
                    }

                    // Inseri o ultimo registro de evento na tabela ultimo_registro_evento_complementar
                    $stFiltro  = " WHERE registro_evento_complementar.cod_contrato = ".$arRegistro["cod_contrato"];
                    $stFiltro .= "   AND registro_evento_complementar.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= "   AND registro_evento_complementar.cod_evento = ".$rsEvento->getCampo("cod_evento");
                    $stFiltro .= "   AND registro_evento_complementar.cod_complementar = ".Sessao::read("inCodComplementar");
                    $obTFolhaPagamentoRegistroEventoComplementar->recuperaTodos($rsRegistroEventoComplementar, $stFiltro, "  timestamp DESC limit 1");

                    $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_registro"      , $rsRegistroEventoComplementar->getCampo("cod_registro"));
                    $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("timestamp"         , $rsRegistroEventoComplementar->getCampo("timestamp"));
                    $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_evento"        , $rsRegistroEventoComplementar->getCampo("cod_evento"));
                    $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_configuracao"  , $rsRegistroEventoComplementar->getCampo("cod_configuracao"));
                    $obTFolhaPagamentoUltimoRegistroEventoComplementar->inclusao();
                    break;
                case 1:
                    // Caso exista cadastro de registro de evento após o reajuste, excluir o registro
                    $stFiltro  = " WHERE registro_evento_periodo.cod_contrato = ".$arRegistro["cod_contrato"];
                    $stFiltro .= "   AND registro_evento_periodo.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= "   AND registro_evento.cod_evento = ".$rsEvento->getCampo("cod_evento");
                    $stFiltro .= "   AND registro_evento.timestamp >= '".$arRegistro["timestamp"]."'";
                    $obTFolhaPagamentoRegistroEvento->recuperaTodosRegistrosEventos($rsRegistroEvento,$stFiltro);

                    $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_registro", "");
                    $obTFolhaPagamentoUltimoRegistroEvento->setDado("timestamp"   , "");
                    $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_evento"  , "");

                    while (!$rsRegistroEvento->eof()) {
                        $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro", $rsRegistroEvento->getCampo("cod_registro"));

                        $obTFolhaPagamentoRegistroEventoParcela->exclusao();
                        $obTFolhaPagamentoLogErroCalculo->exclusao();
                        $obTFolhaPagamentoEventoCalculadoDependente->exclusao();
                        $obTFolhaPagamentoEventoCalculado->exclusao();
                        $obTFolhaPagamentoUltimoRegistroEvento->exclusao();
                        $obTFolhaPagamentoReajusteRegistroEvento->exclusao();
                        $obTFolhaPagamentoRegistroEvento->exclusao();
                        $obTFolhaPagamentoRegistroEventoPeriodo->exclusao();

                        $rsRegistroEvento->proximo();
                    }

                    $stFiltro  = " WHERE cod_reajuste >= ".$inCodReajusteExclusao;
                    $stFiltro .= "   AND origem = 'S'";
                    $obTFolhaPagamentoReajuste->recuperaTodos($rsReajuste, $stFiltro);

                    while (!$rsReajuste->eof()) {
                        $stFiltro = " WHERE cod_reajuste = ".$rsReajuste->getCampo("cod_reajuste");
                        $obTFolhaPagamentoReajusteRegistroEvento->recuperaTodos($rsReajusteRegistroEvento, $stFiltro);

                        if ($rsReajusteRegistroEvento->getNumLinhas() == -1) {
                            $obTFolhaPagamentoReajuste->setDado("cod_reajuste", $rsReajuste->getCampo("cod_reajuste"));

                            $obTFolhaPagamentoReajusteAbsoluto->exclusao();
                            $obTFolhaPagamentoReajustePercentual->exclusao();
                            $obTFolhaPagamentoReajuste->exclusao();
                        }

                        $rsReajuste->proximo();
                    }

                    // Inseri o ultimo registro de evento na tabela ultimo_registro_evento
                    $stFiltro  = " WHERE registro_evento_periodo.cod_contrato = ".$arRegistro["cod_contrato"];
                    $stFiltro .= "   AND registro_evento_periodo.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= "   AND registro_evento.cod_evento = ".$rsEvento->getCampo("cod_evento");
                    $stFiltro .= "   AND registro_evento.proporcional = false ";
                    $obTFolhaPagamentoRegistroEvento->recuperaTodosRegistrosEventos($rsRegistroEvento,$stFiltro," ORDER BY timestamp DESC limit 1");

                    $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_registro", $rsRegistroEvento->getCampo("cod_registro"));
                    $obTFolhaPagamentoUltimoRegistroEvento->setDado("timestamp"   , $rsRegistroEvento->getCampo("timestamp"));
                    $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_evento"  , $rsRegistroEvento->getCampo("cod_evento"));
                    $obTFolhaPagamentoUltimoRegistroEvento->inclusao();
                    break;
                case 2:
                    $stFiltro  = " WHERE registro_evento_ferias.cod_contrato = ".$arRegistro["cod_contrato"];
                    $stFiltro .= "   AND registro_evento_ferias.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= "   AND registro_evento_ferias.cod_evento = ".$rsEvento->getCampo("cod_evento");
                    $stFiltro .= "   AND registro_evento_ferias.timestamp >= '".$arRegistro["timestamp"]."'";
                    $obTFolhaPagamentoRegistroEventoFerias->recuperaTodos($rsRegistroEventoFerias,$stFiltro);

                    $obTFolhaPagamentoUltimoRegistroEventoFerias->setDado("cod_registro"  , "");
                    $obTFolhaPagamentoUltimoRegistroEventoFerias->setDado("timestamp"     , "");
                    $obTFolhaPagamentoUltimoRegistroEventoFerias->setDado("cod_evento"    , "");
                    $obTFolhaPagamentoUltimoRegistroEventoFerias->setDado("desdobramento" , "");

                    while (!$rsRegistroEventoFerias->eof()) {
                        $obTFolhaPagamentoRegistroEventoFerias->setDado("cod_registro"  ,$rsRegistroEventoFerias->getCampo("cod_registro"));
                        $obTFolhaPagamentoRegistroEventoFerias->setDado("cod_evento"    ,$rsRegistroEventoFerias->getCampo("cod_evento"));
                        $obTFolhaPagamentoRegistroEventoFerias->setDado("desdobramento" ,$rsRegistroEventoFerias->getCampo("desdobramento"));
                        $obTFolhaPagamentoRegistroEventoFerias->setDado("timestamp"     ,$rsRegistroEventoFerias->getCampo("timestamp"));

                        $obTFolhaPagamentoReajusteRegistroEventoFerias->exclusao();
                        $obTFolhaPagamentoRegistroEventoFeriasParcela->exclusao();
                        $obTFolhaPagamentoEventoFeriasCalculadoDependente->exclusao();
                        $obTFolhaPagamentoEventoFeriasCalculado->exclusao();
                        $obTFolhaPagamentoLogErroCalculoFerias->exclusao();
                        $obTFolhaPagamentoUltimoRegistroEventoFerias->exclusao();
                        $obTFolhaPagamentoRegistroEventoFerias->exclusao();

                        $rsRegistroEventoFerias->proximo();
                    }

                    $stFiltro  = " WHERE cod_reajuste >= ".$inCodReajusteExclusao;
                    $stFiltro .= "   AND origem = 'F'";
                    $obTFolhaPagamentoReajuste->recuperaTodos($rsReajuste, $stFiltro);

                    while (!$rsReajuste->eof()) {
                        $stFiltro = " WHERE cod_reajuste = ".$rsReajuste->getCampo("cod_reajuste");
                        $obTFolhaPagamentoReajusteRegistroEventoFerias->recuperaTodos($rsReajusteRegistroEventoFerias, $stFiltro);

                        if ($rsReajusteRegistroEventoFerias->getNumLinhas() == -1) {
                            $obTFolhaPagamentoReajuste->setDado("cod_reajuste", $rsReajuste->getCampo("cod_reajuste"));

                            $obTFolhaPagamentoReajusteAbsoluto->exclusao();
                            $obTFolhaPagamentoReajustePercentual->exclusao();
                            $obTFolhaPagamentoReajuste->exclusao();
                        }

                        $rsReajuste->proximo();
                    }

                    $stFiltro  = " WHERE registro_evento_ferias.cod_contrato = ".$arRegistro["cod_contrato"];
                    $stFiltro .= "   AND registro_evento_ferias.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= "   AND registro_evento_ferias.cod_evento = ".$rsEvento->getCampo("cod_evento");
                    $obTFolhaPagamentoRegistroEventoFerias->recuperaTodos($rsRegistroEventoFerias,$stFiltro," ORDER BY timestamp DESC limit 1");

                    $obTFolhaPagamentoUltimoRegistroEventoFerias->setDado("cod_registro"  , $rsRegistroEventoFerias->getCampo("cod_registro"));
                    $obTFolhaPagamentoUltimoRegistroEventoFerias->setDado("timestamp"     , $rsRegistroEventoFerias->getCampo("timestamp"));
                    $obTFolhaPagamentoUltimoRegistroEventoFerias->setDado("cod_evento"    , $rsRegistroEventoFerias->getCampo("cod_evento"));
                    $obTFolhaPagamentoUltimoRegistroEventoFerias->setDado("desdobramento" , $rsRegistroEventoFerias->getCampo("desdobramento"));
                    $obTFolhaPagamentoUltimoRegistroEventoFerias->inclusao();
                    break;
                case 3:
                    $stFiltro  = " WHERE registro_evento_decimo.cod_contrato = ".$arRegistro["cod_contrato"];
                    $stFiltro .= "   AND registro_evento_decimo.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= "   AND registro_evento_decimo.cod_evento = ".$rsEvento->getCampo("cod_evento");
                    $stFiltro .= "   AND registro_evento_decimo.timestamp >= '".$arRegistro["timestamp"]."'";
                    $obTFolhaPagamentoRegistroEventoDecimo->recuperaTodos($rsRegistroEventoDecimo,$stFiltro);

                    $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("cod_registro"  , "");
                    $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("timestamp"     , "");
                    $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("cod_evento"    , "");
                    $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("desdobramento" , "");

                    while (!$rsRegistroEventoDecimo->eof()) {
                        $obTFolhaPagamentoRegistroEventoDecimo->setDado("cod_registro"  ,$rsRegistroEventoDecimo->getCampo("cod_registro"));
                        $obTFolhaPagamentoRegistroEventoDecimo->setDado("cod_evento"    ,$rsRegistroEventoDecimo->getCampo("cod_evento"));
                        $obTFolhaPagamentoRegistroEventoDecimo->setDado("desdobramento" ,$rsRegistroEventoDecimo->getCampo("desdobramento"));
                        $obTFolhaPagamentoRegistroEventoDecimo->setDado("timestamp"     ,$rsRegistroEventoDecimo->getCampo("timestamp"));

                        $obTFolhaPagamentoReajusteRegistroEventoDecimo->exclusao();
                        $obTFolhaPagamentoRegistroEventoDecimoParcela->exclusao();
                        $obTFolhaPagamentoEventoDecimoCalculadoDependente->exclusao();
                        $obTFolhaPagamentoEventoDecimoCalculado->exclusao();
                        $obTFolhaPagamentoLogErroCalculoDecimo->exclusao();
                        $obTFolhaPagamentoUltimoRegistroEventoDecimo->exclusao();
                        $obTFolhaPagamentoRegistroEventoDecimo->exclusao();

                        $rsRegistroEventoDecimo->proximo();
                    }

                    $stFiltro  = " WHERE cod_reajuste >= ".$inCodReajusteExclusao;
                    $stFiltro .= "   AND origem = 'D'";
                    $obTFolhaPagamentoReajuste->recuperaTodos($rsReajuste, $stFiltro);

                    while (!$rsReajuste->eof()) {
                        $stFiltro = " WHERE cod_reajuste = ".$rsReajuste->getCampo("cod_reajuste");
                        $obTFolhaPagamentoReajusteRegistroEventoDecimo->recuperaTodos($rsReajusteRegistroEventoDecimo, $stFiltro);

                        if ($rsReajusteRegistroEventoDecimo->getNumLinhas() == -1) {
                            $obTFolhaPagamentoReajuste->setDado("cod_reajuste", $rsReajuste->getCampo("cod_reajuste"));

                            $obTFolhaPagamentoReajusteAbsoluto->exclusao();
                            $obTFolhaPagamentoReajustePercentual->exclusao();
                            $obTFolhaPagamentoReajuste->exclusao();
                        }
                        $rsReajuste->proximo();
                    }

                    $stFiltro  = " WHERE registro_evento_decimo.cod_contrato = ".$arRegistro["cod_contrato"];
                    $stFiltro .= "   AND registro_evento_decimo.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= "   AND registro_evento_decimo.cod_evento = ".$rsEvento->getCampo("cod_evento");
                    $obTFolhaPagamentoRegistroEventoDecimo->recuperaTodos($rsRegistroEventoDecimo,$stFiltro," ORDER BY timestamp DESC limit 1");

                    $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("cod_registro"  , $rsRegistroEventoDecimo->getCampo("cod_registro"));
                    $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("timestamp"     , $rsRegistroEventoDecimo->getCampo("timestamp"));
                    $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("cod_evento"    , $rsRegistroEventoDecimo->getCampo("cod_evento"));
                    $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("desdobramento" , $rsRegistroEventoDecimo->getCampo("desdobramento"));
                    $obTFolhaPagamentoUltimoRegistroEventoDecimo->inclusao();
                    break;
                case 4:
                    $stFiltro  = " WHERE registro_evento_rescisao.cod_contrato = ".$arRegistro["cod_contrato"];
                    $stFiltro .= "   AND registro_evento_rescisao.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= "   AND registro_evento_rescisao.cod_evento = ".$rsEvento->getCampo("cod_evento");
                    $stFiltro .= "   AND registro_evento_rescisao.timestamp >= '".$arRegistro["cod_contrato"]."'";
                    $obTFolhaPagamentoRegistroEventoRescisao->recuperaTodos($rsRegistroEventoRescisao,$stFiltro);

                    $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("cod_registro"  , "");
                    $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("timestamp"     , "");
                    $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("cod_evento"    , "");
                    $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("desdobramento" , "");

                    while (!$rsRegistroEventoRescisao->eof()) {
                        $obTFolhaPagamentoRegistroEventoRescisao->setDado("cod_registro"  ,$rsRegistroEventoRescisao->getCampo("cod_registro"));
                        $obTFolhaPagamentoRegistroEventoRescisao->setDado("cod_evento"    ,$rsRegistroEventoRescisao->getCampo("cod_evento"));
                        $obTFolhaPagamentoRegistroEventoRescisao->setDado("desdobramento" ,$rsRegistroEventoRescisao->getCampo("desdobramento"));
                        $obTFolhaPagamentoRegistroEventoRescisao->setDado("timestamp"     ,$rsRegistroEventoRescisao->getCampo("timestamp"));

                        $obTFolhaPagamentoReajusteRegistroEventoRescisao->exclusao();
                        $obTFolhaPagamentoRegistroEventoRescisaoParcela->exclusao();
                        $obTFolhaPagamentoEventoRescisaoCalculadoDependente->exclusao();
                        $obTFolhaPagamentoEventoRescisaoCalculado->exclusao();
                        $obTFolhaPagamentoLogErroCalculoRescisao->exclusao();
                        $obTFolhaPagamentoUltimoRegistroEventoRescisao->exclusao();
                        $obTFolhaPagamentoRegistroEventoRescisao->exclusao();

                        $rsRegistroEventoRescisao->proximo();
                    }

                    $stFiltro  = " WHERE cod_reajuste >= ".$inCodReajusteExclusao;
                    $stFiltro .= "   AND origem = 'R'";
                    $obTFolhaPagamentoReajuste->recuperaTodos($rsReajuste, $stFiltro);

                    while (!$rsReajuste->eof()) {
                        $stFiltro = " WHERE cod_reajuste = ".$rsReajuste->getCampo("cod_reajuste");
                        $obTFolhaPagamentoReajusteRegistroEventoRescisao->recuperaTodos($rsReajusteRegistroEventoRescisao, $stFiltro);

                        if ($rsReajusteRegistroEventoRescisao->getNumLinhas() == -1) {
                            $obTFolhaPagamentoReajuste->setDado("cod_reajuste", $rsReajuste->getCampo("cod_reajuste"));

                            $obTFolhaPagamentoReajusteAbsoluto->exclusao();
                            $obTFolhaPagamentoReajustePercentual->exclusao();
                            $obTFolhaPagamentoReajuste->exclusao();
                        }

                        $rsReajuste->proximo();
                    }

                    $stFiltro  = " WHERE registro_evento_rescisao.cod_contrato = ".$arRegistro["cod_contrato"];
                    $stFiltro .= "   AND registro_evento_rescisao.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                    $stFiltro .= "   AND registro_evento_rescisao.cod_evento = ".$rsEvento->getCampo("cod_evento");
                    $obTFolhaPagamentoRegistroEventoRescisao->recuperaTodos($rsRegistroEventoRescisao,$stFiltro," ORDER BY timestamp DESC limit 1");

                    $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("cod_registro"  , $rsRegistroEventoRescisao->getCampo("cod_registro"));
                    $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("timestamp"     , $rsRegistroEventoRescisao->getCampo("timestamp"));
                    $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("cod_evento"    , $rsRegistroEventoRescisao->getCampo("cod_evento"));
                    $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("desdobramento" , $rsRegistroEventoRescisao->getCampo("desdobramento"));
                    $obTFolhaPagamentoUltimoRegistroEventoRescisao->inclusao();
                    break;
            }
        }
    }
    $pgRetorno = $pgForm;
    $stMensagem = "Todos os reajustes foram excluídos com sucesso.";
}
Sessao::encerraExcecao();
sistemaLegado::alertaAviso($pgRetorno,$stMensagem,$stAcao,"aviso",Sessao::getId(),"../");
?>