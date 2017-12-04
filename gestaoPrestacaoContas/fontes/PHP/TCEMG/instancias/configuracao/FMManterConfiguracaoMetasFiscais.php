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
  * Página de Formulario de Configuração de IDE
  * Data de Criação: 20/02/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore
  * $Id: FMManterConfiguracaoMetasFiscais.php 64322 2016-01-15 15:34:00Z jean $
  *
  * $Rev: 64322 $
  * $Author: jean $
  * $Date: 2016-01-15 13:34:00 -0200 (Fri, 15 Jan 2016) $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGMetasFiscais.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoMetasFiscais";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$rsTTCEMGMetasFiscais = new RecordSet();
$obTTCEMGMetasFiscais = new TTCEMGMetasFiscais();
$obTTCEMGMetasFiscais->setDado('exercicio',$request->get('inExercicio'));
$obTTCEMGMetasFiscais->recuperaValoresMetasFiscais($rsTTCEMGMetasFiscais);

if ($rsTTCEMGMetasFiscais->getNumLinhas() > 0) {
    $vlCorrenteReceitaTotal = $rsTTCEMGMetasFiscais->getCampo('valor_corrente_receita_total');
    $vlCorrenteReceitaPrimaria = $rsTTCEMGMetasFiscais->getCampo('valor_corrente_receita_primaria');
    $vlCorrenteDespesaTotal = $rsTTCEMGMetasFiscais->getCampo('valor_corrente_despesa_total');
    $vlCorrenteDespesaPrimaria = $rsTTCEMGMetasFiscais->getCampo('valor_corrente_despesa_primaria');
    $vlCorrenteResultadoPrimario = $rsTTCEMGMetasFiscais->getCampo('valor_corrente_resultado_primario');
    $vlCorrenteResultadoNominal = $rsTTCEMGMetasFiscais->getCampo('valor_corrente_resultado_nominal');
    $vlCorrenteDividaPublicaconsolidada = $rsTTCEMGMetasFiscais->getCampo('valor_corrente_divida_publica_consolidada');
    $vlCorrenteDividaConsolidadaLiquida = $rsTTCEMGMetasFiscais->getCampo('valor_corrente_divida_consolidada_liquida');

    $vlConstanteReceitaTotal = $rsTTCEMGMetasFiscais->getCampo('valor_constante_receita_total');
    $vlConstanteReceitaPrimaria = $rsTTCEMGMetasFiscais->getCampo('valor_constante_receita_primaria');
    $vlConstanteDespesaTotal = $rsTTCEMGMetasFiscais->getCampo('valor_constante_despesa_total');
    $vlConstanteDespesaPrimaria = $rsTTCEMGMetasFiscais->getCampo('valor_constante_despesa_primaria');
    $vlConstanteResultadoPrimario = $rsTTCEMGMetasFiscais->getCampo('valor_constante_resultado_primario');
    $vlConstanteResultadoNominal = $rsTTCEMGMetasFiscais->getCampo('valor_constante_resultado_nominal');
    $vlConstanteDividaPublicaConsolidada = $rsTTCEMGMetasFiscais->getCampo('valor_constante_divida_publica_consolidada');
    $vlConstanteDividaConsolidadaLiquida = $rsTTCEMGMetasFiscais->getCampo('valor_constante_divida_consolidada_liquida');

    $pcPIBReceitaTotal = $rsTTCEMGMetasFiscais->getCampo('percentual_pib_receita_total');
    $pcPIBReceitaPrimaria = $rsTTCEMGMetasFiscais->getCampo('percentual_pib_receita_primaria');
    $pcPIBDespesaTotal = $rsTTCEMGMetasFiscais->getCampo('percentual_pib_despesa_total');
    $pcPIBDespesaPrimaria = $rsTTCEMGMetasFiscais->getCampo('percentual_pib_despesa_primaria');
    $pcPIBResultadoPrimario = $rsTTCEMGMetasFiscais->getCampo('percentual_pib_resultado_primario');
    $pcPIBResultadoNominal = $rsTTCEMGMetasFiscais->getCampo('percentual_pib_resultado_nominal');
    $pcPIBDividaPublicaConsolidada = $rsTTCEMGMetasFiscais->getCampo('percentual_pib_divida_publica_consolidada');
    $pcPIBDividaConsolidadaLiquida = $rsTTCEMGMetasFiscais->getCampo('percentual_pib_divida_consolidada_liquida');

    if ($request->get('inExercicio') == '2016') {
        $vlCorrenteReceitaPrimariaAdv = $rsTTCEMGMetasFiscais->getCampo('valor_corrente_receita_primaria_adv');
        $vlCorrenteDespesaPrimariaGerada = $rsTTCEMGMetasFiscais->getCampo('valor_corrente_despesa_primaria_gerada');

        $vlConstanteReceitaDividaAdv = $rsTTCEMGMetasFiscais->getCampo('valor_constante_receita_primaria_adv');
        $vlConstanteDespesaPrimariaGerada = $rsTTCEMGMetasFiscais->getCampo('valor_constante_despesa_primaria_gerada');

        $pcPIBReceitaPrimariaAdv = $rsTTCEMGMetasFiscais->getCampo('percentual_pib_receita_primaria_adv');
        $pcPIBDespesaPrimariaAdv = $rsTTCEMGMetasFiscais->getCampo('percentual_pib_despesa_primaria_adv');
    }


} else {
    $vlCorrenteReceitaTotal = '0,00';
    $vlCorrenteReceitaPrimaria = '0,00';
    $vlCorrenteDespesaTotal = '0,00';
    $vlCorrenteDespesaPrimaria = '0,00';
    $vlCorrenteResultadoPrimario = '0,00';
    $vlCorrenteResultadoNominal = '0,00';
    $vlCorrenteDividaPublicaconsolidada = '0,00';
    $vlCorrenteDividaConsolidadaLiquida = '0,00';

    $vlConstanteReceitaTotal = '0,00';
    $vlConstanteReceitaPrimaria = '0,00';
    $vlConstanteDespesaTotal = '0,00';
    $vlConstanteDespesaPrimaria = '0,00';
    $vlConstanteResultadoPrimario = '0,00';
    $vlConstanteResultadoNominal = '0,00';
    $vlConstanteDividaPublicaConsolidada = '0,00';
    $vlConstanteDividaConsolidadaLiquida = '0,00';

    $pcPIBReceitaTotal = '0,000';
    $pcPIBReceitaPrimaria = '0,000';
    $pcPIBDespesaTotal = '0,000';
    $pcPIBDespesaPrimaria = '0,000';
    $pcPIBResultadoPrimario = '0,000';
    $pcPIBResultadoNominal = '0,000';
    $pcPIBDividaPublicaConsolidada = '0,000';
    $pcPIBDividaConsolidadaLiquida = '0,000';

    if ($request->get('inExercicio') == '2016') {
        $vlCorrenteReceitaPrimariaAdv = '0.00';
        $vlCorrenteDespesaPrimariaGerada = '0.00';

        $vlConstanteReceitaDividaAdv = '0.00';
        $vlConstanteDespesaPrimariaGerada = '0.00';

        $pcPIBReceitaPrimariaAdv = '0.00';
        $pcPIBDespesaPrimariaAdv = '0.00';
    }
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "stCtrl" );

$obHdnStExercicio = new Hidden();
$obHdnStExercicio->setName('stExercicio');
$obHdnStExercicio->setValue($request->get('inExercicio'));

//****************************************//
//Monta valores Correntes
//****************************************//
$obFlValorCorrenteReceitaTotal = new Numerico();
$obFlValorCorrenteReceitaTotal->setId('flValorCorrenteReceitaTotal');
$obFlValorCorrenteReceitaTotal->setName('flValorCorrenteReceitaTotal');
$obFlValorCorrenteReceitaTotal->setRotulo('Valor Corrente da Receita Total');
$obFlValorCorrenteReceitaTotal->setTitle('Informar o valor corrente da Meta Fiscal da Receita Total.');
$obFlValorCorrenteReceitaTotal->setDecimais(2);
$obFlValorCorrenteReceitaTotal->setMaxLength(15);
$obFlValorCorrenteReceitaTotal->setSize(17);
$obFlValorCorrenteReceitaTotal->setValue($vlCorrenteReceitaTotal);

$obFlValorCorrenteReceitaPrimaria = new Numerico();
$obFlValorCorrenteReceitaPrimaria->setId('flValorCorrenteReceitaPrimaria');
$obFlValorCorrenteReceitaPrimaria->setName('flValorCorrenteReceitaPrimaria');
$obFlValorCorrenteReceitaPrimaria->setRotulo('Valor Corrente da Receita Primaria');
$obFlValorCorrenteReceitaPrimaria->setTitle('Informar o valor corrente da Meta Fiscal da Receita Primária do ente.');
$obFlValorCorrenteReceitaPrimaria->setDecimais(2);
$obFlValorCorrenteReceitaPrimaria->setMaxLength(15);
$obFlValorCorrenteReceitaPrimaria->setSize(17);
$obFlValorCorrenteReceitaPrimaria->setValue($vlCorrenteReceitaPrimaria);

$obFlValorCorrenteDespesaTotal = new Numerico();
$obFlValorCorrenteDespesaTotal->setId('flValorCorrenteDespesaTotal');
$obFlValorCorrenteDespesaTotal->setName('flValorCorrenteDespesaTotal');
$obFlValorCorrenteDespesaTotal->setRotulo('Valor Corrente da Despesa Total');
$obFlValorCorrenteDespesaTotal->setTitle('Informar o valor corrente da Meta Fiscal da Despesa Total.');
$obFlValorCorrenteDespesaTotal->setDecimais(2);
$obFlValorCorrenteDespesaTotal->setMaxLength(15);
$obFlValorCorrenteDespesaTotal->setSize(17);
$obFlValorCorrenteDespesaTotal->setValue($vlCorrenteDespesaTotal);

$obFlValorCorrenteDespesaPrimaria = new Numerico();
$obFlValorCorrenteDespesaPrimaria->setId('flValorCorrenteDespesaPrimaria');
$obFlValorCorrenteDespesaPrimaria->setName('flValorCorrenteDespesaPrimaria');
$obFlValorCorrenteDespesaPrimaria->setRotulo('Valor Corrente da Despesa Primária');
$obFlValorCorrenteDespesaPrimaria->setTitle('Informar o valor corrente da Meta Fiscal da Despesa Primária.');
$obFlValorCorrenteDespesaPrimaria->setDecimais(2);
$obFlValorCorrenteDespesaPrimaria->setMaxLength(15);
$obFlValorCorrenteDespesaPrimaria->setSize(17);
$obFlValorCorrenteDespesaPrimaria->setValue($vlCorrenteDespesaPrimaria);

$obFlValorCorrenteResultadoPrimario = new Numerico();
$obFlValorCorrenteResultadoPrimario->setId('flValorCorrenteResultadoPrimario');
$obFlValorCorrenteResultadoPrimario->setName('flValorCorrenteResultadoPrimario');
$obFlValorCorrenteResultadoPrimario->setRotulo('Valor Corrente do Resultado Primário');
$obFlValorCorrenteResultadoPrimario->setTitle('Valor do resultado primário que correspondente a diferença entre a receita primária e despesa primária.');
$obFlValorCorrenteResultadoPrimario->setDecimais(2);
$obFlValorCorrenteResultadoPrimario->setMaxLength(15);
$obFlValorCorrenteResultadoPrimario->setSize(17);
$obFlValorCorrenteResultadoPrimario->setValue($vlCorrenteResultadoPrimario);

$obFlValorCorrenteResultadoNominal = new Numerico();
$obFlValorCorrenteResultadoNominal->setId('flValorCorrenteResultadoNominal');
$obFlValorCorrenteResultadoNominal->setName('flValorCorrenteResultadoNominal');
$obFlValorCorrenteResultadoNominal->setRotulo('Valor Corrente do Resultado Nominal');
$obFlValorCorrenteResultadoNominal->setTitle('Informar o valor corrente da Meta Fiscal esperada para o Resultado Nominal. <br/>
Representa a diferença entre o saldo da dívida fiscal líquida em 31 de dezembro de determinado ano em relação ao apurado em 31 de dezembro do ano anterior.
');
$obFlValorCorrenteResultadoNominal->setDecimais(2);
$obFlValorCorrenteResultadoNominal->setMaxLength(15);
$obFlValorCorrenteResultadoNominal->setSize(17);
$obFlValorCorrenteResultadoNominal->setValue($vlCorrenteResultadoNominal);

$obFlValorCorrenteDividaPublicaConsolidada = new Numerico();
$obFlValorCorrenteDividaPublicaConsolidada->setId('flValorCorrenteDividaPublicaConsolidada');
$obFlValorCorrenteDividaPublicaConsolidada->setName('flValorCorrenteDividaPublicaConsolidada');
$obFlValorCorrenteDividaPublicaConsolidada->setRotulo('Valor Corrente da Dívida Pública Consolidada');
$obFlValorCorrenteDividaPublicaConsolidada->setTitle('Informar o valor corrente da Meta Fiscal para a Dívida Pública Consolidada.');
$obFlValorCorrenteDividaPublicaConsolidada->setDecimais(2);
$obFlValorCorrenteDividaPublicaConsolidada->setMaxLength(15);
$obFlValorCorrenteDividaPublicaConsolidada->setSize(17);
$obFlValorCorrenteDividaPublicaConsolidada->setValue($vlCorrenteDividaPublicaconsolidada);

$obFlValorCorrenteDividaConsolidadaLiquida = new Numerico();
$obFlValorCorrenteDividaConsolidadaLiquida->setId('flValorCorrenteDividaConsolidadaLiquida');
$obFlValorCorrenteDividaConsolidadaLiquida->setName('flValorCorrenteDividaConsolidadaLiquida');
$obFlValorCorrenteDividaConsolidadaLiquida->setRotulo('Valor Corrente da Dívida Consolidada Líquida');
$obFlValorCorrenteDividaConsolidadaLiquida->setTitle('Informar o valor corrente da Meta Fiscal para a Dívida Consolidada Líquida do exercício.');
$obFlValorCorrenteDividaConsolidadaLiquida->setDecimais(2);
$obFlValorCorrenteDividaConsolidadaLiquida->setMaxLength(15);
$obFlValorCorrenteDividaConsolidadaLiquida->setSize(17);
$obFlValorCorrenteDividaConsolidadaLiquida->setValue($vlCorrenteDividaConsolidadaLiquida);

//

$obFlValorCorrenteReceitaPrimariaAdv = new Numerico();
$obFlValorCorrenteReceitaPrimariaAdv->setId('flValorCorrenteReceitaPrimariaAdv');
$obFlValorCorrenteReceitaPrimariaAdv->setName('flValorCorrenteReceitaPrimariaAdv');
$obFlValorCorrenteReceitaPrimariaAdv->setRotulo('Valor Corrente da Receita Primária Adv');
$obFlValorCorrenteReceitaPrimariaAdv->setTitle('Informar o valor corrente da Receita Primaria do exercício.');
$obFlValorCorrenteReceitaPrimariaAdv->setDecimais(2);
$obFlValorCorrenteReceitaPrimariaAdv->setMaxLength(15);
$obFlValorCorrenteReceitaPrimariaAdv->setSize(17);
$obFlValorCorrenteReceitaPrimariaAdv->setValue($vlCorrenteReceitaPrimariaAdv);

$obFlValorCorrenteDespesaPrimariaGerada = new Numerico();
$obFlValorCorrenteDespesaPrimariaGerada->setId('flValorCorrenteDespesaPrimariaGerada');
$obFlValorCorrenteDespesaPrimariaGerada->setName('flValorCorrenteDespesaPrimariaGerada');
$obFlValorCorrenteDespesaPrimariaGerada->setRotulo('Valor Corrente da Despesa Primária Gerada');
$obFlValorCorrenteDespesaPrimariaGerada->setTitle('Informar o valor corrente da Despesa Primaria Gerada do exercício.');
$obFlValorCorrenteDespesaPrimariaGerada->setDecimais(2);
$obFlValorCorrenteDespesaPrimariaGerada->setMaxLength(15);
$obFlValorCorrenteDespesaPrimariaGerada->setSize(17);
$obFlValorCorrenteDespesaPrimariaGerada->setValue($vlCorrenteDespesaPrimariaGerada);

//

//****************************************//
//Monta valores Constante
//****************************************//
$obFlValorConstanteReceitaTotal = new Numerico();
$obFlValorConstanteReceitaTotal->setId('flValorConstanteReceitaTotal');
$obFlValorConstanteReceitaTotal->setName('flValorConstanteReceitaTotal');
$obFlValorConstanteReceitaTotal->setRotulo('Valor Constante da Receita Total');
$obFlValorConstanteReceitaTotal->setTitle('Informar o valor constante da estimativa da Receita Total.');
$obFlValorConstanteReceitaTotal->setDecimais(2);
$obFlValorConstanteReceitaTotal->setMaxLength(15);
$obFlValorConstanteReceitaTotal->setSize(17);
$obFlValorConstanteReceitaTotal->setValue($vlConstanteReceitaTotal);

$obFlValorConstanteReceitaPrimaria = new Numerico();
$obFlValorConstanteReceitaPrimaria->setId('flValorConstanteReceitaPrimaria');
$obFlValorConstanteReceitaPrimaria->setName('flValorConstanteReceitaPrimaria');
$obFlValorConstanteReceitaPrimaria->setRotulo('Valor Constante da Receita Primaria');
$obFlValorConstanteReceitaPrimaria->setTitle('Informar o valor constante da estimativa da Receita Primária.');
$obFlValorConstanteReceitaPrimaria->setDecimais(2);
$obFlValorConstanteReceitaPrimaria->setMaxLength(15);
$obFlValorConstanteReceitaPrimaria->setSize(17);
$obFlValorConstanteReceitaPrimaria->setValue($vlConstanteReceitaPrimaria);

$obFlValorConstanteDespesaTotal = new Numerico();
$obFlValorConstanteDespesaTotal->setId('flValorConstanteDespesaTotal');
$obFlValorConstanteDespesaTotal->setName('flValorConstanteDespesaTotal');
$obFlValorConstanteDespesaTotal->setRotulo('Valor Constante da Despesa Total');
$obFlValorConstanteDespesaTotal->setTitle('Informar o valor constante da estimativa da Despesa Total.');
$obFlValorConstanteDespesaTotal->setDecimais(2);
$obFlValorConstanteDespesaTotal->setMaxLength(15);
$obFlValorConstanteDespesaTotal->setSize(17);
$obFlValorConstanteDespesaTotal->setValue($vlConstanteDespesaTotal);

$obFlValorConstanteDespesaPrimaria = new Numerico();
$obFlValorConstanteDespesaPrimaria->setId('flValorConstanteDespesaPrimaria');
$obFlValorConstanteDespesaPrimaria->setName('flValorConstanteDespesaPrimaria');
$obFlValorConstanteDespesaPrimaria->setRotulo('Valor Constante da Despesa Primária');
$obFlValorConstanteDespesaPrimaria->setTitle('Informar o valor constante estimado para as Despesas Primárias.');
$obFlValorConstanteDespesaPrimaria->setDecimais(2);
$obFlValorConstanteDespesaPrimaria->setMaxLength(15);
$obFlValorConstanteDespesaPrimaria->setSize(17);
$obFlValorConstanteDespesaPrimaria->setValue($vlConstanteDespesaPrimaria);

$obFlValorConstanteDespesaResultadoPrimario = new Numerico();
$obFlValorConstanteDespesaResultadoPrimario->setId('flValorConstanteDespesaResultadoPrimario');
$obFlValorConstanteDespesaResultadoPrimario->setName('flValorConstanteDespesaResultadoPrimario');
$obFlValorConstanteDespesaResultadoPrimario->setRotulo('Valor Constante do Resultado Primário');
$obFlValorConstanteDespesaResultadoPrimario->setTitle('Informar o valor constante estimado para o resultado primário que correspondente a diferença entre a receita primária e despesa primária.');
$obFlValorConstanteDespesaResultadoPrimario->setDecimais(2);
$obFlValorConstanteDespesaResultadoPrimario->setMaxLength(15);
$obFlValorConstanteDespesaResultadoPrimario->setSize(17);
$obFlValorConstanteDespesaResultadoPrimario->setValue($vlConstanteResultadoPrimario);

$obFlValorConstanteResultadoNominal = new Numerico();
$obFlValorConstanteResultadoNominal->setId('flValorConstanteResultadoNominal');
$obFlValorConstanteResultadoNominal->setName('flValorConstanteResultadoNominal');
$obFlValorConstanteResultadoNominal->setRotulo('Valor Constante do Resultado Nominal');
$obFlValorConstanteResultadoNominal->setTitle('Informar o valor constante esperado para o Resultado Nominal. <br/>
Representa a diferença entre o saldo da dívida fiscal líquida em 31 de dezembro de determinado ano em relação ao apurado em 31 de dezembro do ano anterior.');
$obFlValorConstanteResultadoNominal->setDecimais(2);
$obFlValorConstanteResultadoNominal->setMaxLength(15);
$obFlValorConstanteResultadoNominal->setSize(17);
$obFlValorConstanteResultadoNominal->setValue($vlConstanteResultadoNominal);

$obFlValorConstanteDividaPublicaConsolidada = new Numerico();
$obFlValorConstanteDividaPublicaConsolidada->setId('flValorConstanteDividaPublicaConsolidada');
$obFlValorConstanteDividaPublicaConsolidada->setName('flValorConstanteDividaPublicaConsolidada');
$obFlValorConstanteDividaPublicaConsolidada->setRotulo('Valor Constante da Dívida Pública Consolidada');
$obFlValorConstanteDividaPublicaConsolidada->setTitle('Informar o valor constante esperado para a Dívida Pública   Consolidada.');
$obFlValorConstanteDividaPublicaConsolidada->setDecimais(2);
$obFlValorConstanteDividaPublicaConsolidada->setMaxLength(15);
$obFlValorConstanteDividaPublicaConsolidada->setSize(17);
$obFlValorConstanteDividaPublicaConsolidada->setValue($vlConstanteDividaPublicaConsolidada);

$obFlValorConstanteDividaConsolidadaLiquida = new Numerico();
$obFlValorConstanteDividaConsolidadaLiquida->setId('flValorConstanteDividaConsolidadaLiquida');
$obFlValorConstanteDividaConsolidadaLiquida->setName('flValorConstanteDividaConsolidadaLiquida');
$obFlValorConstanteDividaConsolidadaLiquida->setRotulo('Valor Constante da Dívida Consolidada Líquida');
$obFlValorConstanteDividaConsolidadaLiquida->setTitle('Informar o valor constante esperado para a Dívida Pública Consolidada Líquida.');
$obFlValorConstanteDividaConsolidadaLiquida->setDecimais(2);
$obFlValorConstanteDividaConsolidadaLiquida->setMaxLength(15);
$obFlValorConstanteDividaConsolidadaLiquida->setSize(17);
$obFlValorConstanteDividaConsolidadaLiquida->setValue($vlConstanteDividaConsolidadaLiquida);

//

$obFlValorConstanteReceitaDividaAdv = new Numerico();
$obFlValorConstanteReceitaDividaAdv->setId('flValorConstanteReceitaDividaAdv');
$obFlValorConstanteReceitaDividaAdv->setName('flValorConstanteReceitaDividaAdv');
$obFlValorConstanteReceitaDividaAdv->setRotulo('Valor Constante da Receita Dívida');
$obFlValorConstanteReceitaDividaAdv->setTitle('Informar o valor constante de receita dívida adv.');
$obFlValorConstanteReceitaDividaAdv->setDecimais(2);
$obFlValorConstanteReceitaDividaAdv->setMaxLength(15);
$obFlValorConstanteReceitaDividaAdv->setSize(17);
$obFlValorConstanteReceitaDividaAdv->setValue($vlConstanteReceitaDividaAdv);

$obFlValorConstanteDespesaPrimariaGerada = new Numerico();
$obFlValorConstanteDespesaPrimariaGerada->setId('flValorConstanteDespesaPrimariaGerada');
$obFlValorConstanteDespesaPrimariaGerada->setName('flValorConstanteDespesaPrimariaGerada');
$obFlValorConstanteDespesaPrimariaGerada->setRotulo('Valor Constante da Despesa Primária Gerada');
$obFlValorConstanteDespesaPrimariaGerada->setTitle('Informar o valor constante da despesa primária gerada. ');
$obFlValorConstanteDespesaPrimariaGerada->setDecimais(2);
$obFlValorConstanteDespesaPrimariaGerada->setMaxLength(15);
$obFlValorConstanteDespesaPrimariaGerada->setSize(17);
$obFlValorConstanteDespesaPrimariaGerada->setValue($vlConstanteDespesaPrimariaGerada);

//

//****************************************//
//Monta Valores de PIB
//****************************************//
$obFlPercentualPIBReceitaTotal = new Porcentagem();
$obFlPercentualPIBReceitaTotal->setId('flPercentualPIBReceitaTotal');
$obFlPercentualPIBReceitaTotal->setName('flPercentualPIBReceitaTotal');
$obFlPercentualPIBReceitaTotal->setRotulo('Percentual do PIB da Receita Total');
$obFlPercentualPIBReceitaTotal->setTitle('Informar o valor percentual da Meta Fiscal da Receita Total em relação ao valor projetado do PIB do Estado de Minas Gerais, até um milésimo por cento (0,001%). <br/>
Formatação: 00,000 (Informar com três casas decimais).');
$obFlPercentualPIBReceitaTotal->setDecimais(3);
$obFlPercentualPIBReceitaTotal->setMaxLength(5);
$obFlPercentualPIBReceitaTotal->setSize(7);
$obFlPercentualPIBReceitaTotal->setValue($pcPIBReceitaTotal);

$obFlPercentualPIBReceitaPrimaria = new Porcentagem();
$obFlPercentualPIBReceitaPrimaria->setId('flPercentualPIBReceitaPrimaria');
$obFlPercentualPIBReceitaPrimaria->setName('flPercentualPIBReceitaPrimaria');
$obFlPercentualPIBReceitaPrimaria->setRotulo('Percentual do PIB da Receita Primaria');
$obFlPercentualPIBReceitaPrimaria->setTitle('Informar o valor percentual da Meta Fiscal da Receita Primária do ente em relação ao valor projetado do PIB do Estado de Minas Gerais, até um milésimo por cento (0,001%). <br/>
Formatação: 00,000 (Informar com três casas decimais).');
$obFlPercentualPIBReceitaPrimaria->setDecimais(3);
$obFlPercentualPIBReceitaPrimaria->setMaxLength(5);
$obFlPercentualPIBReceitaPrimaria->setSize(7);
$obFlPercentualPIBReceitaPrimaria->setValue($pcPIBReceitaPrimaria);

$obFlPercentualPIBDespesaTotal = new Porcentagem();
$obFlPercentualPIBDespesaTotal->setId('flPercentualPIBDespesaTotal');
$obFlPercentualPIBDespesaTotal->setName('flPercentualPIBDespesaTotal');
$obFlPercentualPIBDespesaTotal->setRotulo('Percentual do PIB da Despesa Total');
$obFlPercentualPIBDespesaTotal->setTitle('Informar o valor percentual da Meta Fiscal da Despesa Total em relação ao valor projetado do PIB do Estado de Minas Gerais, até um milésimo por cento (0,001%).<br/>
Formatação: 00,000 (Informar com três casas decimais).');
$obFlPercentualPIBDespesaTotal->setDecimais(3);
$obFlPercentualPIBDespesaTotal->setMaxLength(5);
$obFlPercentualPIBDespesaTotal->setSize(7);
$obFlPercentualPIBDespesaTotal->setValue($pcPIBDespesaTotal);

$obFlPercentualPIBDespesaPrimaria = new Porcentagem();
$obFlPercentualPIBDespesaPrimaria->setId('flPercentualPIBDespesaPrimaria');
$obFlPercentualPIBDespesaPrimaria->setName('flPercentualPIBDespesaPrimaria');
$obFlPercentualPIBDespesaPrimaria->setRotulo('Percentual do PIB da Despesa Primária');
$obFlPercentualPIBDespesaPrimaria->setTitle('Informar o valor percentual da Meta Fiscal estimado para a Despesa Primária em relação ao valor projetado do PIB do Estado de Minas Gerais, até um milésimo por cento (0,001%).<br/>
Formatação: 00,000 (Informar com três casas decimais).');
$obFlPercentualPIBDespesaPrimaria->setDecimais(3);
$obFlPercentualPIBDespesaPrimaria->setMaxLength(5);
$obFlPercentualPIBDespesaPrimaria->setSize(7);
$obFlPercentualPIBDespesaPrimaria->setValue($pcPIBDespesaPrimaria);

$obFlPercentualPIBResultadoPrimario = new Porcentagem();
$obFlPercentualPIBResultadoPrimario->setId('flPercentualPIBResultadoPrimario');
$obFlPercentualPIBResultadoPrimario->setName('flPercentualPIBResultadoPrimario');
$obFlPercentualPIBResultadoPrimario->setRotulo('Percentual do PIB do Resultado Primário');
$obFlPercentualPIBResultadoPrimario->setTitle('Informar o valor percentual do Resultado Primário estimado para o Resultado Nominal em relação ao valor projetado do PIB do Estado de Minas Gerais, até um milésimo por cento (0,001%).<br/>
Formatação: 00,000 (Informar com três casas decimais).');
$obFlPercentualPIBResultadoPrimario->setDecimais(3);
$obFlPercentualPIBResultadoPrimario->setMaxLength(5);
$obFlPercentualPIBResultadoPrimario->setSize(7);
$obFlPercentualPIBResultadoPrimario->setValue($pcPIBResultadoPrimario);

$obFlPercentualPIBResultadoNominal = new Porcentagem();
$obFlPercentualPIBResultadoNominal->setId('flPercentualPIBResultadoNominal');
$obFlPercentualPIBResultadoNominal->setName('flPercentualPIBResultadoNominal');
$obFlPercentualPIBResultadoNominal->setRotulo('Percentual do PIB do Resultado Nominal');
$obFlPercentualPIBResultadoNominal->setTitle('Informar o valor percentual da Meta Fiscal esperada para o Resultado Nominal em relação ao valor projetado do PIB do Estado de Minas Gerais, até um milésimo por cento (0,001%).<br/>
O Resultado Nominal representa a diferença entre o saldo da dívida fiscal líquida em 31 de dezembro de determinado ano em relação ao apurado em 31 de dezembro do ano anterior.<br/>
Formatação: 00,000 (Informar com três casas decimais).');
$obFlPercentualPIBResultadoNominal->setDecimais(3);
$obFlPercentualPIBResultadoNominal->setMaxLength(5);
$obFlPercentualPIBResultadoNominal->setSize(7);
$obFlPercentualPIBResultadoNominal->setValue($pcPIBResultadoNominal);

$obFlPercentualPIBDividaPublicaConsolidada = new Porcentagem();
$obFlPercentualPIBDividaPublicaConsolidada->setId('flPercentualPIBDividaPublicaConsolidada');
$obFlPercentualPIBDividaPublicaConsolidada->setName('flPercentualPIBDividaPublicaConsolidada');
$obFlPercentualPIBDividaPublicaConsolidada->setRotulo('Percentual do PIB da Dívida Pública Consolidada');
$obFlPercentualPIBDividaPublicaConsolidada->setTitle('Informar o valor percentual da Meta Fiscal esperada para a Dívida Pública Consolidada em relação ao valor projetado do PIB do Estado de Minas Gerais, até um milésimo por cento (0,001%).<br/>
Formatação: 00,000 (Informar com três casas decimais).');
$obFlPercentualPIBDividaPublicaConsolidada->setDecimais(3);
$obFlPercentualPIBDividaPublicaConsolidada->setMaxLength(5);
$obFlPercentualPIBDividaPublicaConsolidada->setSize(7);
$obFlPercentualPIBDividaPublicaConsolidada->setValue($pcPIBDividaPublicaConsolidada);

$obFlPercentualPIBDividaConsolidadaLiquida = new Porcentagem();
$obFlPercentualPIBDividaConsolidadaLiquida->setId('flPercentualPIBDividaConsolidadaLiquida');
$obFlPercentualPIBDividaConsolidadaLiquida->setName('flPercentualPIBDividaConsolidadaLiquida');
$obFlPercentualPIBDividaConsolidadaLiquida->setRotulo('Percentual do PIB da Dívida Consolidada Líquida');
$obFlPercentualPIBDividaConsolidadaLiquida->setTitle('Informar o valor percentual da Meta Fiscal esperada para a Dívida Consolidada Líquida do exercício financeiro em relação ao valor projetado do PIB do Estado de Minas Gerais, até um milésimo por cento (0,001%).<br/>
A Dívida Consolidada Líquida corresponde à dívida pública consolidada menos as deduções que compreendem o ativo disponível e os haveres financeiros, líquidos dos Restos a Pagar Processados.<br/>
Formatação: 00,000 (Informar com três casas decimais).');
$obFlPercentualPIBDividaConsolidadaLiquida->setDecimais(3);
$obFlPercentualPIBDividaConsolidadaLiquida->setMaxLength(5);
$obFlPercentualPIBDividaConsolidadaLiquida->setSize(7);
$obFlPercentualPIBDividaConsolidadaLiquida->setValue($pcPIBDividaConsolidadaLiquida);

//

$obFlPercentualPIBReceitaPrimariaAdv = new Porcentagem();
$obFlPercentualPIBReceitaPrimariaAdv->setId('flPercentualPIBReceitaPrimariaAdv');
$obFlPercentualPIBReceitaPrimariaAdv->setName('flPercentualPIBReceitaPrimariaAdv');
$obFlPercentualPIBReceitaPrimariaAdv->setRotulo('Percentual do PIB da Receita Primária Adv');
$obFlPercentualPIBReceitaPrimariaAdv->setTitle('Informar o valor percentual da Meta Fiscal esperada para a Dívida Consolidada Líquida do exercício financeiro em relação ao valor projetado do PIB do Estado de Minas Gerais, até um milésimo por cento (0,001%).<br/>
A Dívida Consolidada Líquida corresponde à dívida pública consolidada menos as deduções que compreendem o ativo disponível e os haveres financeiros, líquidos dos Restos a Pagar Processados.<br/>
Formatação: 00,000 (Informar com três casas decimais).');
$obFlPercentualPIBReceitaPrimariaAdv->setDecimais(3);
$obFlPercentualPIBReceitaPrimariaAdv->setMaxLength(5);
$obFlPercentualPIBReceitaPrimariaAdv->setSize(7);
$obFlPercentualPIBReceitaPrimariaAdv->setValue($pcPIBReceitaPrimariaAdv);

$obFlPercentualPIBDespesaPrimariaAdv = new Porcentagem();
$obFlPercentualPIBDespesaPrimariaAdv->setId('flPercentualPIBDespesaPrimariaAdv');
$obFlPercentualPIBDespesaPrimariaAdv->setName('flPercentualPIBDespesaPrimariaAdv');
$obFlPercentualPIBDespesaPrimariaAdv->setRotulo('Percentual do PIB da Despesa Primária Adv');
$obFlPercentualPIBDespesaPrimariaAdv->setTitle('Informar o valor percentual da Meta Fiscal esperada para a Dívida Consolidada Líquida do exercício financeiro em relação ao valor projetado do PIB do Estado de Minas Gerais, até um milésimo por cento (0,001%).<br/>
A Dívida Consolidada Líquida corresponde à dívida pública consolidada menos as deduções que compreendem o ativo disponível e os haveres financeiros, líquidos dos Restos a Pagar Processados.<br/>
Formatação: 00,000 (Informar com três casas decimais).');
$obFlPercentualPIBDespesaPrimariaAdv->setDecimais(3);
$obFlPercentualPIBDespesaPrimariaAdv->setMaxLength(5);
$obFlPercentualPIBDespesaPrimariaAdv->setSize(7);
$obFlPercentualPIBDespesaPrimariaAdv->setValue($pcPIBDespesaPrimariaAdv);

//

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm         ( $obForm );
$obFormulario->addHidden       ( $obHdnCtrl );
$obFormulario->addHidden       ( $obHdnAcao );
$obFormulario->addHidden       ( $obHdnStExercicio );
$obFormulario->setLarguraRotulo( 30 );
$obFormulario->setLarguraCampo ( 70 );
$obFormulario->addTitulo       ( "Detalhamento das Metas Fiscais (Valor Corrente)" );
$obFormulario->addComponente   ( $obFlValorCorrenteReceitaTotal );
$obFormulario->addComponente   ( $obFlValorCorrenteReceitaPrimaria );
$obFormulario->addComponente   ( $obFlValorCorrenteDespesaTotal );
$obFormulario->addComponente   ( $obFlValorCorrenteDespesaPrimaria );
$obFormulario->addComponente   ( $obFlValorCorrenteResultadoPrimario );
$obFormulario->addComponente   ( $obFlValorCorrenteResultadoNominal );
$obFormulario->addComponente   ( $obFlValorCorrenteDividaPublicaConsolidada );
$obFormulario->addComponente   ( $obFlValorCorrenteDividaConsolidadaLiquida );
//
$obFormulario->addComponente   ( $obFlValorCorrenteReceitaPrimariaAdv );
$obFormulario->addComponente   ( $obFlValorCorrenteDespesaPrimariaGerada );
//
$obFormulario->addTitulo       ( "Detalhamento das Metas Fiscais (Valor Constante)" );
$obFormulario->addComponente   ( $obFlValorConstanteReceitaTotal );
$obFormulario->addComponente   ( $obFlValorConstanteReceitaPrimaria );
$obFormulario->addComponente   ( $obFlValorConstanteDespesaTotal );
$obFormulario->addComponente   ( $obFlValorConstanteDespesaPrimaria );
$obFormulario->addComponente   ( $obFlValorConstanteDespesaResultadoPrimario );
$obFormulario->addComponente   ( $obFlValorConstanteResultadoNominal );
$obFormulario->addComponente   ( $obFlValorConstanteDividaPublicaConsolidada );
$obFormulario->addComponente   ( $obFlValorConstanteDividaConsolidadaLiquida );
//
$obFormulario->addComponente   ( $obFlValorConstanteReceitaDividaAdv );
$obFormulario->addComponente   ( $obFlValorConstanteDespesaPrimariaGerada );
//
$obFormulario->addTitulo       ( "Detalhamento das Metas Fiscais (Percentuais)" );
$obFormulario->addComponente   ( $obFlPercentualPIBReceitaTotal );
$obFormulario->addComponente   ( $obFlPercentualPIBReceitaPrimaria );
$obFormulario->addComponente   ( $obFlPercentualPIBDespesaTotal );
$obFormulario->addComponente   ( $obFlPercentualPIBDespesaPrimaria );
$obFormulario->addComponente   ( $obFlPercentualPIBResultadoPrimario );
$obFormulario->addComponente   ( $obFlPercentualPIBResultadoNominal );
$obFormulario->addComponente   ( $obFlPercentualPIBDividaPublicaConsolidada );
$obFormulario->addComponente   ( $obFlPercentualPIBDividaConsolidadaLiquida );
//
$obFormulario->addComponente   ( $obFlPercentualPIBReceitaPrimariaAdv );
$obFormulario->addComponente   ( $obFlPercentualPIBDespesaPrimariaAdv );
//

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
