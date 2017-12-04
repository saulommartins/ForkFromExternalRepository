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
  * Layout exportação TCE-PE arquivo : ContribuicaoPrevidenciaria
  * Data de Criação: 18/11/2014

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Michel Teixeira
  *
  * @ignore
  * $Id: ContribuicaoPrevidenciaria.inc.php 60836 2014-11-18 15:31:02Z michel $
  * $Date: 2014-11-18 13:31:02 -0200 (Tue, 18 Nov 2014) $
  * $Author: michel $
  * $Rev: 60836 $
  *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEContribuicaoPrevidenciaria.class.php';
include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoPeriodoMovimentacao.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoPrevidencia.class.php';

$boTransacao = new Transacao();
$obTEntidade = new TEntidade();
$rsRecordSet = new RecordSet();

$stFiltro = " WHERE nspname = 'folhapagamento_".$inCodEntidade."'";
$obTEntidade->recuperaEsquemasCriados($rsEsquemas,$stFiltro, "", $boTransacao);

$arPrevidencia = array();
$arPrevidencia2 = array();
$inCount = 0;

//Backup de Competencia selecionada no Filtro
$inCodCompetencia2 = $inCodCompetencia;

//loop das competencias de Janeiro até Dezembro
for($inCodCompetencia=1;$inCodCompetencia<13;$inCodCompetencia++){
    $inTmInicial    = mktime(0,0,0,$inCodCompetencia,01,Sessao::getExercicio());
    $stDtInicial    = date  ('d/m/Y',$inTmInicial);
    $inTmFinal      = mktime(0,0,0,$inCodCompetencia+1,01,Sessao::getExercicio()) - 1;
    $stDtFinal      = date  ('d/m/Y',$inTmFinal);
    
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes", $inCodCompetencia     );
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano", Sessao::getExercicio());
    
    $codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, $stAno);
    
    if ($codEntidadePrefeitura == $inCodEntidade || $rsEsquemas->getNumLinhas() < 1) {
        $stEntidade = "";
    } else {
        $stEntidade = "_".$inCodEntidade;    
    }
    
    $stFiltro = " AND to_char(dt_final, 'mm/yyyy') = '".str_pad($inCodCompetencia,2,'0', STR_PAD_LEFT)."/".Sessao::getExercicio()."'";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao, $stFiltro, "", $boTransacao, "folhapagamento".$stEntidade);

    if ($rsPeriodoMovimentacao->getNumLinhas() > 0 &&($codEntidadePrefeitura == $inCodEntidade || $rsEsquemas->getNumLinhas() > 1)) {
        $inCodMovimentacao = 0;
        
        foreach ($rsPeriodoMovimentacao->getElementos() as $arPeriodoMovimentacao ) {
            $inCodMovimentacao = $arPeriodoMovimentacao['cod_periodo_movimentacao'];
        }
        
        //Recupera Previdencias
        $obTFolhaPagamentoPrevidencia = new TFolhaPagamentoPrevidencia();
        $obTFolhaPagamentoPrevidencia->setTabela('folhapagamento'.$stEntidade.'.previdencia');
        $obTFolhaPagamentoPrevidencia->recuperaTodos($rsPrevidencias, "", "", $boTransacao);
        
        foreach ($rsPrevidencias->getElementos() as $arCodPrevidencia ) {
            $obTTCEPEContribuicaoPrevidenciaria = new TTCEPEContribuicaoPrevidenciaria();
            $obTTCEPEContribuicaoPrevidenciaria->setDado('stEntidades'      , $stEntidade                                                           );
            $obTTCEPEContribuicaoPrevidenciaria->setDado('inCodEntidade'    , $inCodEntidade                                                        );
            $obTTCEPEContribuicaoPrevidenciaria->setDado('stMes'            , str_pad($inCodCompetencia,2,'0', STR_PAD_LEFT)                        );
            $obTTCEPEContribuicaoPrevidenciaria->setDado('stMesAno'         , str_pad($inCodCompetencia,2,'0', STR_PAD_LEFT).Sessao::getExercicio() );
            $obTTCEPEContribuicaoPrevidenciaria->setDado('inCodMovimentacao', $inCodMovimentacao                                                    );
            $obTTCEPEContribuicaoPrevidenciaria->setDado('stExercicio'      , Sessao::getExercicio()                                                );
            $obTTCEPEContribuicaoPrevidenciaria->setDado('stDataInicial'    , $stDtInicial                                                          );
            $obTTCEPEContribuicaoPrevidenciaria->setDado('stDataFinal'      , $stDtFinal                                                            );
            $obTTCEPEContribuicaoPrevidenciaria->setDado('inCodPrevidencia' , $arCodPrevidencia['cod_previdencia']                                  );
            $obTTCEPEContribuicaoPrevidenciaria->recuperaContribuicaoPrevidenciaria($rsRecordSet, "", "", $boTransacao);

            foreach ($rsRecordSet->getElementos() as $arRecordSet ) {
                $arPrevidencia[] = $arRecordSet;
            }
        }
    }
}

//Atribuindo valor inicial da Competência
$inCodCompetencia = $inCodCompetencia2;

//Soma Total do Regime/Vinculo por mês.
foreach ($arPrevidencia as $lsPrevidencia ) {
    $stChave = $lsPrevidencia['mes'].$lsPrevidencia['cod_regime_previdencia'].$lsPrevidencia['tipo_contribuicao'];
    if(isset($arPrevidencia2[$stChave])){
        $arPrevidencia2[ $stChave ][ 'base'         ] = $arPrevidencia2[ $stChave ][ 'base'         ] + $lsPrevidencia[ 'base'          ];
        $arPrevidencia2[ $stChave ][ 'retido'       ] = $arPrevidencia2[ $stChave ][ 'retido'       ] + $lsPrevidencia[ 'retido'        ];
        $arPrevidencia2[ $stChave ][ 'pago_direto'  ] = $arPrevidencia2[ $stChave ][ 'pago_direto'  ] + $lsPrevidencia[ 'pago_direto'   ];
        $arPrevidencia2[ $stChave ][ 'recolhido'    ] = $arPrevidencia2[ $stChave ][ 'recolhido'    ] + $lsPrevidencia[ 'recolhido'     ];
        $arPrevidencia2[ $stChave ][ 'familia'      ] = $arPrevidencia2[ $stChave ][ 'familia'      ] + $lsPrevidencia[ 'familia'       ];
    }else{
        $arPrevidencia2[$stChave] = $lsPrevidencia;
    }
}

ksort($arPrevidencia2);
unset($arPrevidencia);
$arPrevidencia = array();

foreach ($arPrevidencia2 as $arRecordSet ) {
    $arPrevidencia[ $inCount ][ 'mes'                   ] = $arRecordSet[ 'mes'                     ];
    $arPrevidencia[ $inCount ][ 'cod_regime_previdencia'] = $arRecordSet[ 'cod_regime_previdencia'  ];
    $arPrevidencia[ $inCount ][ 'tipo_contribuicao'     ] = $arRecordSet[ 'tipo_contribuicao'       ];
    $arPrevidencia[ $inCount ][ 'aliquota'              ] = number_format($arRecordSet[ 'aliquota'              ], 2, ',', '');
    $arPrevidencia[ $inCount ][ 'base'                  ] = number_format($arRecordSet[ 'base'                  ], 2, ',', '');
    $arPrevidencia[ $inCount ][ 'retido'                ] = number_format($arRecordSet[ 'retido'                ], 2, ',', '');
    $arPrevidencia[ $inCount ][ 'contabilizado'         ] = number_format($arRecordSet[ 'valor_contabilizado'   ], 2, ',', '');
    $arPrevidencia[ $inCount ][ 'pago_direto'           ] = number_format($arRecordSet[ 'pago_direto'           ], 2, ',', '');
    $arPrevidencia[ $inCount ][ 'recolhido'             ] = number_format($arRecordSet[ 'recolhido'             ], 2, ',', '');
    $arPrevidencia[ $inCount ][ 'familia'               ] = number_format($arRecordSet[ 'familia'               ], 2, ',', '');
    $arPrevidencia[ $inCount ][ 'dt_vencimento'         ] = $arRecordSet[ 'dt_vencimento'           ];
    $arPrevidencia[ $inCount ][ 'dt_repasse'            ] = $arRecordSet[ 'dt_repasse'              ];
    $inCount++;
}

$rsRecordSet = new RecordSet();
$rsRecordSet->preenche($arPrevidencia);

$obExportador->roUltimoArquivo->addBloco($rsRecordSet);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mes");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_regime_previdencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_contribuicao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("aliquota");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("base");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("retido");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("contabilizado");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("pago_direto");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("recolhido");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_vencimento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_repasse");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("reservado_tce");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
?>