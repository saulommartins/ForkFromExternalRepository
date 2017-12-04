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
   /*
    * Arquivo de geracao do arquivo despesaTotalPessoalPL.txt
    * Data de Criação   : 20/01/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
    */

include_once( CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/FTCEMGDespesaTotalPessoalPL.class.php');
    
$arFiltros = Sessao::read('filtroRelatorio');

foreach($arDatasInicialFinal as $arPeriodo) {
    list($inDia, $inMes, $inAno) = explode('/',$arPeriodo['stDtInicial']);

    $obFTCEMGDespesaTotalPessoalPL = new FTCEMGDespesaTotalPessoalPL();
    $obFTCEMGDespesaTotalPessoalPL->setDado('cod_entidade', implode(',',$arFiltros['inCodEntidadeSelecionado']));
    $obFTCEMGDespesaTotalPessoalPL->setDado('dt_inicial', $arPeriodo['stDtInicial']);
    $obFTCEMGDespesaTotalPessoalPL->setDado('dt_final'  , $arPeriodo['stDtFinal']);

    //1 MES
    $arTemp[0]["bimestre"] = $inMes;

    //2 VENCIMENTOS E VANTAGENS FIXAS - SERVIDORES
    $stFiltro = " (conta_despesa.cod_estrutural like ''3.1.9.0%''
               AND conta_despesa.cod_estrutural not like ''3.1.9.0.01%''
               AND conta_despesa.cod_estrutural not like ''3.1.9.0.03%''
               AND conta_despesa.cod_estrutural not like ''3.1.9.0.09%''
               AND conta_despesa.cod_estrutural != ''3.1.9.0.11.74.00.00.00''
               AND conta_despesa.cod_estrutural != ''3.1.9.0.04.15.00.00.00''
               AND conta_despesa.cod_estrutural != ''3.1.9.0.13.00.00.00.00''
               AND conta_despesa.cod_estrutural != ''3.1.9.0.07.03.00.00.00''
               AND conta_despesa.cod_estrutural not like ''3.1.9.0.91%''
               AND conta_despesa.cod_estrutural != ''3.1.9.0.94.01.01.00.00''
               AND conta_despesa.cod_estrutural != ''3.1.9.0.94.01.02.00.00''
               AND conta_despesa.cod_estrutural not like ''3.1.9.0.92%''
               AND conta_despesa.cod_estrutural != ''3.1.9.0.16.04.00.00.00''
               AND conta_despesa.cod_estrutural not like ''3.1.9.0.34%'')";
    
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);    
    $arTemp[0]["nuVencimentosVantagens"] = $rsTemp->getCampo("valor");

    //3 APOSENTADORIAS E REFORMAS
    $stFiltro = " (conta_despesa.cod_estrutural like ''3.1.9.0.01%'')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuAposentadorias"] = $rsTemp->getCampo("valor");

    //4 PENSOES
    $stFiltro = " (conta_despesa.cod_estrutural like ''3.1.9.0.03%'')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuPensoes"] = $rsTemp->getCampo("valor");

    //5 SALARIO-FAMILIA
    $stFiltro = " (conta_despesa.cod_estrutural like ''3.1.9.0.09%'')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuSalarioFamilia"] = $rsTemp->getCampo("valor");

    //6 SUBSIDIOS
    $stFiltro = " (conta_despesa.cod_estrutural = ''3.1.9.0.11.74.00.00.00'')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuSubsidios"] = $rsTemp->getCampo("valor");

    //7 OBRIGAÇÕES PATRONAIS
    $stFiltro = " (conta_despesa.cod_estrutural = ''3.1.9.0.04.15.00.00.00''
                OR conta_despesa.cod_estrutural = ''3.1.9.0.13.00.00.00.00'')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuObrigacoesPatronais"] = $rsTemp->getCampo("valor");

    //8 REPASSE PATRONAL
    $stFiltro = " (conta_despesa.cod_estrutural = ''3.1.9.0.07.03.00.00.00''
                OR conta_despesa.cod_estrutural like ''3.1.9.1.13%'')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuRepassePatronal"] = $rsTemp->getCampo("valor");

    //9 SENTENCAS JUDICIAIS PESSOAL
    $stFiltro = " (conta_despesa.cod_estrutural like ''3.1.9.0.91%'')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuSentecasJudiciais"] = $rsTemp->getCampo("valor");

    //10 INDENIZACAO PARA DEMISSAO DE SERVIDORES/ EMPREGADOS
    $stFiltro = " (conta_despesa.cod_estrutural = ''3.1.9.0.94.01.01.00.00'')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuIndenizacaoDemissao"] = $rsTemp->getCampo("valor");

    //11 DESPESAS RELATIVAS A PROGRAMAS DE  DESLIGAMENTO VOLUNTARIO
    $stFiltro = " (conta_despesa.cod_estrutural = ''3.1.9.0.94.01.02.00.00'')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuDesligamentoVoluntario"] = $rsTemp->getCampo("valor");

    //12 SENTENCAS JUDICIAIS ANTERIORES
    $stFiltro = " (conta_despesa.cod_estrutural like ''3.1.9.0.92.91.00.00.00'')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuSentecasJudiciaisAnteriores"] = $rsTemp->getCampo("valor");
        
    //13 INATIVOS E PENSIONISTAS CUSTEIO PRÓPRIO -- fixar 0 (#20471)    
    $stFiltro = " (conta_despesa.cod_estrutural like ''3.1.9.0.01%'')
               AND despesa.cod_recurso = 1";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $arTemp[0]["nuInativosPensionistasCusteioProprio"] = 0;

    $stFiltro = " (conta_despesa.cod_estrutural like ''3.1.9.0.03%'')
               AND despesa.cod_recurso = 1";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuInativosPensionistasCusteioProprio"] += $rsTemp->getCampo("valor");
    $arTemp[0]["nuInativosPensionistasCusteioProprio"] = number_format($arTemp[0]["nuInativosPensionistasCusteioProprio"],2,'.','');

    //14 CONVOCACAO EXTRAORDINARIA
    $stFiltro = " (conta_despesa.cod_estrutural = ''3.1.9.0.16.04.00.00.00'')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuConvocacaoExtraordinaria"] = $rsTemp->getCampo("valor");

    //15 OUTRAS DESPESAS DE PESSOAL -- fixar 0 (#20471)
    $stFiltro = " (conta_despesa.cod_estrutural like ''3.1.9.0.34%'')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $arTemp[0]["nuOutrasDespesas"] = 0;

    //16 Indica se não há nada a declarar referente a Outras Despesas de Pessoal -- fixar S (#20471)
    $arTemp[0]["nadaDeclararPessoal"] = 'S';
    
    //17 DESPESAS DE EXERCICIOS ANTERIORES
    $stFiltro = " (conta_despesa.cod_estrutural like ''3.1.9.0.92%'')";
    $obFTCEMGDespesaTotalPessoalPL->setDado('filtro'      , $stFiltro);
    $obFTCEMGDespesaTotalPessoalPL->recuperaRelacionamento($rsTemp);
    $arTemp[0]["nuDespesasAnteriores"] = $rsTemp->getCampo("valor");    
    
    //18 
    $arTemp[0]["nuDespExercAntExcl"] = 0;
    
    //19 
    $arTemp[0]["nuCorApurMovel"] = 0;
    
    //20
    $arTemp[0]["nuDespesaCorres"] = 0;
    
    //21
    $arTemp[0]["nuDespesaCompet"] = 0;
       

    $rsArquivo = new recordset();
    $rsArquivo->preenche($arTemp);

    $obExportador->roUltimoArquivo->addBloco($rsArquivo);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('bimestre');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuVencimentosVantagens');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuAposentadorias');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuPensoes');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuSalarioFamilia');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuSubsidios');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuObrigacoesPatronais');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuRepassePatronal');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuSentecasJudiciais');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuIndenizacaoDemissao');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuDesligamentoVoluntario');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuSentecasJudiciaisAnteriores');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuInativosPensionistasCusteioProprio');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuConvocacaoExtraordinaria');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nuOutrasDespesas');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
     
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('nadaDeclararPessoal');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('CARACTER_ESPACOS_DIR');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

}
?>