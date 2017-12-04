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
    * Página do Oculto de Inclusao/Alteracao de Lancamento Partida Dobrada
    * Data de Criação   : 19/10/2006

    * @author Analista      : Gelson Gonçalves
    * @author Desenvolvedor : Rodrigo Soares

    * @ignore

    * $Id: OCManterLancamentoPartidaDobrada.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.33
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeHistoricoPadrao.class.php"                        );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php"                        );

$stPrograma = "ManterLancamentoPartidaDobrada";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRContabilidadeHistoricoPadrao = new RContabilidadeHistoricoPadrao;
$obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
$obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;

switch ($_REQUEST['stCtrl']) {

 case "carregaDados":

    if ($_REQUEST['stAcao'] == "alterar") {

        include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php");
        $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
        $stFiltro  = "   AND l.exercicio = '".Sessao::getExercicio()."'";
        $stFiltro .= "   AND l.cod_entidade = ".$_REQUEST['cod_entidade'];
        $stFiltro .= "   AND l.tipo = '".$_REQUEST['tipo']."'";
        $stFiltro .= "   AND l.cod_lote = ".$_REQUEST['cod_lote'];
        $obTContabilidadeValorLancamento->recuperaLancamento($rsRecordSet, $stFiltro);

        $arValoresDebito  = array();
        $arValoresCredito = array();
        $arRegistro       = array();

        if ($rsRecordSet->getNumLinhas() > 0) {
            while (!$rsRecordSet->eof()) {
                if ($rsRecordSet->getCampo('tipo_valor') == 'C') {
                    $arRegistro['sequenciaCredito'     ] = $rsRecordSet->getCampo('sequencia');
                    $arRegistro['inCodContaCredito'    ] = $rsRecordSet->getCampo('cod_plano');
                    $arRegistro['stContaCredito'       ] = $rsRecordSet->getCampo('nom_conta');
                    $arRegistro['nuVlCredito'          ] = $rsRecordSet->getCampo('vl_lancamento');
                    $arRegistro['inCodHistoricoCredito'] = $rsRecordSet->getCampo('cod_historico');
                    $arRegistro['stNomHistoricoCredito'] = $rsRecordSet->getCampo('nom_historico');
                    $arRegistro['stComplementoCredito' ] = $rsRecordSet->getCampo('complemento');
                    $arRegistro['exercicio' ]            = $rsRecordSet->getCampo('exercicio');
                    $arValoresCredito[] = $arRegistro;
                } else {
                    $arRegistro['sequenciaDebito'     ] = $rsRecordSet->getCampo('sequencia');
                    $arRegistro['inCodContaDebito'    ] = $rsRecordSet->getCampo('cod_plano');
                    $arRegistro['stContaDebito'       ] = $rsRecordSet->getCampo('nom_conta');
                    $arRegistro['nuVlDebito'          ] = $rsRecordSet->getCampo('vl_lancamento');
                    $arRegistro['inCodHistoricoDebito'] = $rsRecordSet->getCampo('cod_historico');
                    $arRegistro['stNomHistoricoDebito'] = $rsRecordSet->getCampo('nom_historico');
                    $arRegistro['stComplementoDebito' ] = $rsRecordSet->getCampo('complemento');
                    $arRegistro['exercicio' ]           = $rsRecordSet->getCampo('exercicio');
                    $arValoresDebito[] = $arRegistro;
                }
                $arRegistro = "";
                $rsRecordSet->proximo();
            }

            Sessao::write('arValoresDebito' , $arValoresDebito);
            Sessao::write('arValoresCredito', $arValoresCredito);

            Sessao::write('arDebitosSalvos' , $arValoresDebito);
            Sessao::write('arCreditosSalvos' , $arValoresCredito);

            if (count($arValoresDebito) > 0) {
                $stJs .= montaListaDebito();
                $stJs .= 'f.stDebitos.value  = true;';
            }
            if (count($arValoresCredito) > 0) {
                $stJs .= montaListaCredito();
                $stJs .= 'f.stCreditos.value  = true;';
            }
        }
    } else {
        $stJs  = 'f.stDebitos.value  = false;';
        $stJs .= 'f.stCreditos.value  = false;';
    }

    echo $stJs;

 break;

 case "buscaContaDebito":
    if ($_REQUEST['inCodContaDebito'] != "") {
        $obRContabilidadePlanoContaAnalitica->setCodPlano( $_REQUEST['inCodContaDebito'] );
        $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
        $obRContabilidadePlanoContaAnalitica->consultar();
        $stNomContaDebito = $obRContabilidadePlanoContaAnalitica->getNomConta();
        if (!$stNomContaDebito) {
            $stJs .= 'f.inCodContaDebito.value = "";';
            $stJs .= 'f.inCodContaDebito.focus();';
            $stJs .= 'd.getElementById("stContaDebito").innerHTML = "&nbsp;";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inCodContaDebito"].")','form','erro','".Sessao::getId()."');";
        } else {
            $stJs  = 'd.getElementById("stContaDebito").innerHTML = "'.$stNomContaDebito.'";';
            $stJs .= 'f.stContaDebito.value = "'.$stNomContaDebito.'";';
        }
    } else $stJs .= 'd.getElementById("stContaDebito").innerHTML = "&nbsp;";';

    echo $stJs;

 break;

 case "buscaContaCredito":

    if ($_REQUEST['inCodContaCredito'] != "") {
        $obRContabilidadePlanoContaAnalitica->setCodPlano( $_REQUEST['inCodContaCredito'] );
        $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
        $obRContabilidadePlanoContaAnalitica->consultar();
        $stNomContaCredito = $obRContabilidadePlanoContaAnalitica->getNomConta();

        if (!$stNomContaCredito) {
            $stJs .= 'f.inCodContaCredito.value = "";';
            $stJs .= 'f.inCodContaCredito.focus();';
            $stJs .= 'd.getElementById("stContaCredito").innerHTML = "&nbsp;";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inCodContaCredito"].")','form','erro','".Sessao::getId()."');";
        } else {
            $stJs  = 'd.getElementById("stContaCredito").innerHTML = "'.$stNomContaCredito.'";';
            $stJs .= 'f.stContaCredito.value = "'.$stNomContaCredito.'";';
        }
    } else {
         $stJs = 'd.getElementById("stContaCredito").innerHTML = "&nbsp;";';
    }

    echo $stJs;

 break;

 case 'validaMes':   //ok

    if ($_REQUEST['inMesProcessamento'] != "" AND $_REQUEST['stDtLote'] != "") {
        $arData = array();
        $arData = explode('/', $_REQUEST['stDtLote']);

        if ($arData[2] == Sessao::getExercicio()) {
            if ($_REQUEST['inMesProcessamento'] != $arData[1]) {
                $stJs  = "alertaAviso('Data do lote fora do mês de processamento.','form','erro','".Sessao::getId()."');";
                $stJs .= "f.stDtLote.value = '';";
                $stJs .= "f.stDtLote.focus();";
            }
        } else {
            $stJs  = "alertaAviso('Data fora do exercício atual.','form','erro','".Sessao::getId()."');";
            $stJs .= "f.stDtLote.value = '';";
            $stJs .= "f.stDtLote.focus();";
        }
    } else {
        $stJs  = "alertaAviso('Informe a data do lote.','form','erro','".Sessao::getId()."');";
        $stJs .=  "f.stDtLote.focus();";
    }
    echo $stJs;

 break;

 case 'buscaDadosDebitosCreditos':

    include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php");
    $obTContabilidadeLancamento = new TContabilidadeLancamento;
    $obTContabilidadeLancamento->setDado("exercicio"    , $_REQUEST['stExercicio']   );
    $obTContabilidadeLancamento->setDado("cod_entidade" , $_REQUEST['inCodEntidade'] );
    $obTContabilidadeLancamento->setDado("stDtInicial"  , $_REQUEST['stDtLote']      );
    $obTContabilidadeLancamento->setDado("stDtFinal"    , $_REQUEST['stDtLote']      );
    $obTContabilidadeLancamento->setDado("cod_lote"     , $_REQUEST['inCodLote']     );
    $obTContabilidadeLancamento->relatorioDiario($rsRecordSet);

    $arRecordSet = array();
    $inCount = 0;

    while (!$rsRecordSet->eof()) {
        $arRecordSet[$inCount]['id']                     = $inCount;
        $arRecordSet[$inCount]['dt_lote']                = $rsRecordSet->getCampo('dt_lote');
        $arRecordSet[$inCount]['historico']              = $rsRecordSet->getCampo('historico');
        $arRecordSet[$inCount]['vl_lancamento_debito']   = $rsRecordSet->getCampo('vl_lancamento_debito');
        $arRecordSet[$inCount]['cod_estrutural_debito']  = $rsRecordSet->getCampo('cod_estrutural_debito');
        $arRecordSet[$inCount]['nom_conta_debito']       = $rsRecordSet->getCampo('nom_conta_debito');
        $arRecordSet[$inCount]['vl_lancamento_credito']  = $rsRecordSet->getCampo('vl_lancamento_credito');
        $arRecordSet[$inCount]['cod_estrutural_credito'] = $rsRecordSet->getCampo('cod_estrutural_credito');
        $arRecordSet[$inCount]['nom_conta_credito']      = $rsRecordSet->getCampo('nom_conta_credito');
        $inCount++;
        $rsRecordSet->proximo();
    }

    Sessao::write('arRecordSet',$arRecordSet);
    echo montaListaDebito(Sessao::read('arRecordSet'));
    echo montaListaCredito(Sessao::read('arRecordSet'));

 break;

 case 'buscaLancamentos':

    include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php" );
    $obTContabilidadeLote = new TContabilidadeLote;
    if ($_REQUEST['inCodEntidade']) {
        $stFiltro .= " WHERE lote.cod_entidade = ".$_REQUEST['inCodEntidade']."\n";
    }
    if ($_REQUEST['stDtLote']) {
        $stFiltro .= " AND lote.dt_lote = to_date('".$_REQUEST['stDtLote']."','dd/mm/yyyy') \n";
    }
    if ($_REQUEST['stNomLote']) {
        $stFiltro .= " AND lote.nom_lote like '".$_REQUEST['stNomLote']."' \n";
    }
    if ($_REQUEST['inCodLote']) {
        $stFiltro .= " AND lote.cod_lote = ".$_REQUEST['inCodLote']."\n";
    }
    $obTContabilidadeLote->recuperaTodos($rsRecordSet, $stFiltro, "");
    echo montaListaLotes($rsRecordSet);

 break;

 case "buscaHistoricoDebito":     //ok

        if ($_REQUEST["inCodHistoricoDebito"] != "") {
            include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadeHistoricoContabil.class.php");
            $obTContabilidadeHistoricoPadrao = new TContabilidadeHistoricoContabil;
            $stFiltro  = " WHERE cod_historico =  ".$_REQUEST["inCodHistoricoDebito"];
            $stFiltro .= "   AND exercicio     = '".Sessao::getExercicio()."'";
            $obTContabilidadeHistoricoPadrao->recuperaTodos($rsHistorico, $stFiltro);

            if ($rsHistorico->getNumLinhas() > 0) {
                $stJs .= 'd.getElementById("stNomHistoricoDebito").innerHTML = "'.$rsHistorico->getCampo('nom_historico').'";';
                if ($rsHistorico->getCampo('complemento')) {
                    $stJs .= 'f.stComplementoDebito.disabled=false;';
                    $stJs .= 'f.boComplementoDebito.value=true';
                } else {
                    $stJs .= 'f.stComplementoDebito.disabled=true;';
                    $stJs .= 'f.boComplementoDebito.value=false';
                }
            } else {
                $stJs .= "alertaAviso('Histórico inválido.','form','erro','".Sessao::getId()."');";
                $stJs .= 'f.inCodHistoricoDebito.value="";';
                $stJs .= 'd.getElementById("stNomHistoricoDebito").innerHTML = "&nbsp;";';
            }
        } else {
            $stJs = 'd.getElementById("stNomHistoricoDebito").innerHTML = "&nbsp;";';
        }

        echo $stJs;
    break;

 case "buscaHistoricoCredito":

        if ($_REQUEST["inCodHistoricoCredito"] != "") {
            include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadeHistoricoContabil.class.php");
            $obTContabilidadeHistoricoPadrao = new TContabilidadeHistoricoContabil;
            $stFiltro  = " WHERE cod_historico =  ".$_REQUEST["inCodHistoricoCredito"];
            $stFiltro .= "   AND exercicio     = '".Sessao::getExercicio()."'";
            $obTContabilidadeHistoricoPadrao->recuperaTodos($rsHistorico, $stFiltro);

            if ($rsHistorico->getNumLinhas() > 0) {
                $stJs  = 'd.getElementById("stNomHistoricoCredito").innerHTML = "'.$rsHistorico->getCampo('nom_historico').'";';
                if ($rsHistorico->getCampo('complemento')) {
                    $stJs .= 'f.stComplementoCredito.disabled=false;';
                    $stJs .= 'f.boComplementoCredito.value=true';
                } else {
                    $stJs .= 'f.stComplementoCredito.disabled=true;';
                    $stJs .= 'f.boComplementoCredito.value=false';
                }
            } else {
                $stJs .= "alertaAviso('Histórico inválido.','form','erro','".Sessao::getId()."');";
                $stJs .= 'f.inCodHistoricoCredito.value="";';
                $stJs .= 'd.getElementById("stNomHistoricoCredito").innerHTML = "&nbsp;";';
            }
        } else {
            $stJs = 'd.getElementById("stNomHistoricoCredito").innerHTML = "&nbsp;";';
        }

        echo $stJs;

    break;

 case 'incluirListaDebito':

    $boIncluir = true;
    if ($_REQUEST["inCodContaDebito"] != "") {
        if ($_REQUEST[ "nuVlDebito" ] != "0,00") {
            if ($_REQUEST[ "nuVlDebito" ] != "") {
                if ($_REQUEST[ "inCodHistoricoDebito" ] != "") {
                    $arRegistro = array();
                    $arValoresDebito = Sessao::read('arValoresDebito');
                    $arValoresCredito = Sessao::read('arValoresCredito');
                    if (count($arValoresCredito) > 0) {
                        foreach ($arValoresCredito as $key => $array) {
                            if ($array['inCodContaCredito'] == $_REQUEST["inCodContaDebito"]) {
                                $boIncluir = false;
                                $stJs = "alertaAviso('Conta já está sendo utilizada à Crédito.','form','erro','".Sessao::getId()."');";
                            }
                        }
                    }
                    if ($boIncluir) {
                        $inCodPlanoAlteracao = Sessao::read('inCodPlanoAlteracao');
                        if (count($arValoresDebito) > 0) {
                            foreach ($arValoresDebito as $key => $array) {
                                $stCod = $array['inCodContaDebito'];
                                if ($_REQUEST["inCodContaDebito"] == $stCod and $_REQUEST["inCodContaDebito"] != $inCodPlanoAlteracao) {
                                    $boIncluir = false;
                                    $stJs .= "alertaAviso('Conta já inclusa na lista.','form','erro','".Sessao::getId()."');";
                                    $stJs .= 'f.inCodHistoricoDebito.value = "";';
                                    $stJs .= 'f.inCodContaDebito.value = "";';
                                    $stJs .= 'd.getElementById("stContaDebito").innerHTML = "&nbsp;";';
                                    $stJs .= 'd.getElementById("stNomHistoricoDebito").innerHTML = "&nbsp;";';
                                    $stJs .= 'f.nuVlDebito.value = "";';
                                    $stJs .= 'f.stComplementoDebito.value = "";';
                                    $stJs .= 'f.inCodContaDebito.focus();';
                                }
                            }
                        }
                        include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadeHistoricoContabil.class.php");
                        $obTContabilidadeHistoricoContabil = new TContabilidadeHistoricoContabil;
                        $stFiltro  = " WHERE cod_historico =  ".$_REQUEST[ "inCodHistoricoDebito" ];
                        $stFiltro .= "   AND exercicio     = '".Sessao::getExercicio()."'";
                        $obTContabilidadeHistoricoContabil->recuperaTodos($rsHistorico, $stFiltro);

                        if ($boIncluir) {
                            $nuVlDebito = str_replace(".","",$_REQUEST[ "nuVlDebito" ]);
                            $nuVlDebito = str_replace(",",".",$nuVlDebito);
                            $nuVlDebito = str_replace("-","",$nuVlDebito);
                            $arRegistro['inCodContaDebito'    ] = $_REQUEST[ "inCodContaDebito"     ];
                            $arRegistro['stContaDebito'       ] = $_REQUEST[ "stContaDebito"        ];
                            $arRegistro['nuVlDebito'          ] = $nuVlDebito;
                            $arRegistro['inCodHistoricoDebito'] = $_REQUEST[ "inCodHistoricoDebito" ];
                            $arRegistro['stNomHistoricoDebito'] = $rsHistorico->getCampo('nom_historico');
                            $arRegistro['stComplementoDebito' ] = $_REQUEST[ "stComplementoDebito"  ];
                            $arRegistro['exercicio'           ] = Sessao::getExercicio();
                            $arTMP = array();
                            $inCount = 0;
                            if (count($arValoresDebito) > 0) {
                                foreach ($arValoresDebito as $arValue) {
                                    if ($arValue['inCodContaDebito'] != $inCodPlanoAlteracao) {
                                        $arTMP[$inCount]['sequenciaDebito']      = $arValue['sequenciaDebito'];
                                        $arTMP[$inCount]['inCodContaDebito']     = $arValue['inCodContaDebito'];
                                        $arTMP[$inCount]['stContaDebito']        = $arValue['stContaDebito'];
                                        $arTMP[$inCount]['nuVlDebito']           = $arValue['nuVlDebito'];
                                        $arTMP[$inCount]['inCodHistoricoDebito'] = $arValue['inCodHistoricoDebito'];
                                        $arTMP[$inCount]['stNomHistoricoDebito'] = $arValue['stNomHistoricoDebito'];
                                        $arTMP[$inCount]['stComplementoDebito']  = $arValue['stComplementoDebito'];
                                        $arTMP[$inCount]['exercicio']            = $arValue['exercicio'];
                                        $inCount++;
                                    }
                                }
                            }
                            $arTMP[$inCount]['sequenciaDebito']      = $_REQUEST['sequencia'];
                            $arTMP[$inCount]['inCodContaDebito']     = $arRegistro['inCodContaDebito'    ];
                            $arTMP[$inCount]['stContaDebito']        = $arRegistro['stContaDebito'       ];
                            $arTMP[$inCount]['nuVlDebito']           = $arRegistro['nuVlDebito'          ];
                            $arTMP[$inCount]['inCodHistoricoDebito'] = $arRegistro['inCodHistoricoDebito'];
                            $arTMP[$inCount]['stNomHistoricoDebito'] = $arRegistro['stNomHistoricoDebito'];
                            $arTMP[$inCount]['stComplementoDebito']  = $arRegistro['stComplementoDebito' ];
                            $arTMP[$inCount]['exercicio']            = $arRegistro['exercicio'           ];

                            Sessao::remove('arValoresDebito');
                            Sessao::remove('inCodPlanoAlteracao');
                            Sessao::write('arValoresDebito', $arTMP);
                            $stJs  = 'f.inCodHistoricoDebito.value = "";';
                            $stJs .= 'f.inCodContaDebito.value = "";';
                            $stJs .= 'd.getElementById("stContaDebito").innerHTML = "&nbsp;";';
                            $stJs .= 'd.getElementById("stNomHistoricoDebito").innerHTML = "&nbsp;";';
                            $stJs .= 'f.nuVlDebito.value = "";';
                            $stJs .= 'f.stComplementoDebito.value = "";';
                            $stJs .= 'f.inCodContaDebito.focus();';
                            $stJs .= 'f.stDebitos.value  = "true";';
                            $stJs .= 'f.btnIncluirDebito.value = "Incluir";';
                            $stJs .= 'f.btnLimparDebito.value = "Limpar";';
                            $stJs .= montaListaDebito();
                        }
                    }
                } else {
                    $stJs .= "alertaAviso('Informe o histórico do débito.','form','erro','".Sessao::getId()."');";
                }
            } else {
                $stJs .= "alertaAviso('Informe o valor do débito.','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "alertaAviso('Valor não pode ser 0,00.','form','erro','".Sessao::getId()."');";
        }
    } else {
         $stJs .= "alertaAviso('Informe a conta débito.','form','erro','".Sessao::getId()."');";
    }
    echo $stJs;
 break;

 case 'limparDebito':

    Sessao::remove('inCodPlanoAlteracao');
    $stJs  = 'f.inCodHistoricoDebito.value = "";';
    $stJs .= 'f.inCodContaDebito.value = "";';
    $stJs .= 'd.getElementById("stContaDebito").innerHTML = "&nbsp;";';
    $stJs .= 'd.getElementById("stNomHistoricoDebito").innerHTML = "&nbsp;";';
    $stJs .= 'f.nuVlDebito.value = "";';
    $stJs .= 'f.stComplementoDebito.value = "";';
    $stJs .= 'f.inCodContaDebito.focus();';
    echo $stJs;

 break;

 case 'limparCredito':

    Sessao::remove('inCodPlanoAlteracao');
    $stJs  = 'f.inCodHistoricoCredito.value = "";';
    $stJs .= 'f.inCodContaCredito.value = "";';
    $stJs .= 'd.getElementById("stContaCredito").innerHTML = "&nbsp;";';
    $stJs .= 'd.getElementById("stNomHistoricoCredito").innerHTML = "&nbsp;";';
    $stJs .= 'f.nuVlCredito.value = "";';
    $stJs .= 'f.stComplementoCredito.value = "";';
    $stJs .= 'f.inCodContaCredito.focus();';
    echo $stJs;

 break;

 case 'excluirListaDebito' :
    $arTEMP             = array();
    $arValoresDebito    = array();
    $arDebitosExcluidos = array();
    $arValores = Sessao::read('arValoresDebito');
    foreach ($arValores as $arRegistro) {
        if ($arRegistro['inCodContaDebito'] != $_REQUEST['id']) {
            $arTEMP['sequenciaDebito'     ] = $arRegistro[ "sequenciaDebito"      ];
            $arTEMP['inCodContaDebito'    ] = $arRegistro[ "inCodContaDebito"     ];
            $arTEMP['stContaDebito'       ] = $arRegistro[ "stContaDebito"        ];
            $arTEMP['nuVlDebito'          ] = $arRegistro[ "nuVlDebito"           ];
            $arTEMP['inCodHistoricoDebito'] = $arRegistro[ "inCodHistoricoDebito" ];
            $arTEMP['stNomHistoricoDebito'] = $arRegistro[ "stNomHistoricoDebito" ];
            $arTEMP['stComplementoDebito' ] = $arRegistro[ "stComplementoDebito"  ];
            $arValoresDebito[] = $arTEMP;
        }
    }

    if (count($arValoresDebito) == 0) {
        $stJs = 'f.stDebitos.value  = false;';
    }

    Sessao::write('arValoresDebito', $arValoresDebito);
    $stJs .= montaListaDebito();
    echo $stJs;
 break;

 case 'incluirListaCredito':

    $boIncluir = true;

    if ($_REQUEST["inCodContaCredito"] != "") {
        if ($_REQUEST[ "nuVlCredito" ] != "0,00") {
            if ($_REQUEST[ "nuVlCredito" ] != "") {
                if ($_REQUEST[ "inCodHistoricoCredito" ] != "") {
                    $arRegistro = array();
                    $arValoresCredito = Sessao::read('arValoresCredito');
                    $arValoresDebito = Sessao::read('arValoresDebito');
                    if (count($arValoresDebito) > 0) {
                        foreach ($arValoresDebito as $key => $array) {
                            if ($array['inCodContaDebito'] == $_REQUEST["inCodContaCredito"]) {
                                $boIncluir = false;
                                $stJs = "alertaAviso('Conta já está sendo utilizada à Débito.','form','erro','".Sessao::getId()."');";
                            }
                        }
                    }
                    if ($boIncluir) {
                        $inCodPlanoAlteracao = Sessao::read('inCodPlanoAlteracao');
                        if (count($arValoresCredito) > 0) {
                            foreach ($arValoresCredito as $key => $array) {
                                $stCod = $array['inCodContaCredito'];
                                if ($_REQUEST["inCodContaCredito"] == $stCod and $_REQUEST["inCodContaCredito"] != $inCodPlanoAlteracao) {
                                    $boIncluir = false;
                                    $stJs .= "alertaAviso('Conta já inclusa na lista.','form','erro','".Sessao::getId()."');";
                                    $stJs .= 'f.inCodHistoricoCredito.value = "";';
                                    $stJs .= 'f.inCodContaCredito.value = "";';
                                    $stJs .= 'd.getElementById("stContaCredito").innerHTML = "&nbsp;";';
                                    $stJs .= 'd.getElementById("stNomHistoricoCredito").innerHTML = "&nbsp;";';
                                    $stJs .= 'f.nuVlCredito.value = "";';
                                    $stJs .= 'f.stComplementoCredito.value = "";';
                                    $stJs .= 'f.inCodContaCredito.focus();';
                                }
                            }
                        }

                        include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadeHistoricoContabil.class.php");
                        $obTContabilidadeHistoricoContabil = new TContabilidadeHistoricoContabil;
                        $stFiltro  = " WHERE cod_historico =  ".$_REQUEST[ "inCodHistoricoCredito" ];
                        $stFiltro .= "   AND exercicio     = '".Sessao::getExercicio()."'";
                        $obTContabilidadeHistoricoContabil->recuperaTodos($rsHistorico, $stFiltro);

                        if ($boIncluir) {
                            $nuVlCredito = str_replace(".","",$_REQUEST[ "nuVlCredito" ]);
                            $nuVlCredito = str_replace(",",".",$nuVlCredito);
                            $nuVlCredito = str_replace("-","",$nuVlCredito);
                            $arRegistro['inCodContaCredito'    ] = $_REQUEST[ "inCodContaCredito"     ];
                            $arRegistro['stContaCredito'       ] = $_REQUEST[ "stContaCredito"        ];
                            $arRegistro['nuVlCredito'          ] = $nuVlCredito;
                            $arRegistro['inCodHistoricoCredito'] = $_REQUEST[ "inCodHistoricoCredito" ];
                            $arRegistro['stNomHistoricoCredito'] = $rsHistorico->getCampo('nom_historico');
                            $arRegistro['stComplementoCredito' ] = $_REQUEST[ "stComplementoCredito"  ];
                            $arRegistro['exercicio'           ] = Sessao::getExercicio();
                            $arTMP = array();
                            $inCount = 0;
                            if (count($arValoresCredito) > 0) {
                                foreach ($arValoresCredito as $arValue) {
                                    if ($arValue['inCodContaCredito'] != $inCodPlanoAlteracao) {
                                        $arTMP[$inCount]['sequenciaCredito']      = $arValue['sequenciaCredito'];
                                        $arTMP[$inCount]['inCodContaCredito']     = $arValue['inCodContaCredito'];
                                        $arTMP[$inCount]['stContaCredito']        = $arValue['stContaCredito'];
                                        $arTMP[$inCount]['nuVlCredito']           = $arValue['nuVlCredito'];
                                        $arTMP[$inCount]['inCodHistoricoCredito'] = $arValue['inCodHistoricoCredito'];
                                        $arTMP[$inCount]['stNomHistoricoCredito'] = $arValue['stNomHistoricoCredito'];
                                        $arTMP[$inCount]['stComplementoCredito']  = $arValue['stComplementoCredito'];
                                        $arTMP[$inCount]['exercicio']            = $arValue['exercicio'];
                                        $inCount++;
                                    }
                                }
                            }
                            $arTMP[$inCount]['sequenciaCredito']      = $_REQUEST['sequencia'];
                            $arTMP[$inCount]['inCodContaCredito']     = $arRegistro['inCodContaCredito'    ];
                            $arTMP[$inCount]['stContaCredito']        = $arRegistro['stContaCredito'       ];
                            $arTMP[$inCount]['nuVlCredito']           = $arRegistro['nuVlCredito'          ];
                            $arTMP[$inCount]['inCodHistoricoCredito'] = $arRegistro['inCodHistoricoCredito'];
                            $arTMP[$inCount]['stNomHistoricoCredito'] = $arRegistro['stNomHistoricoCredito'];
                            $arTMP[$inCount]['stComplementoCredito']  = $arRegistro['stComplementoCredito' ];
                            $arTMP[$inCount]['exercicio']             = $arRegistro['exercicio'            ];

                            Sessao::remove('arValoresCredito');
                            Sessao::remove('inCodPlanoAlteracao');
                            Sessao::write('arValoresCredito', $arTMP);
                            $stJs  = 'f.inCodHistoricoCredito.value = "";';
                            $stJs .= 'f.inCodContaCredito.value = "";';
                            $stJs .= 'd.getElementById("stContaCredito").innerHTML = "&nbsp;";';
                            $stJs .= 'd.getElementById("stNomHistoricoCredito").innerHTML = "&nbsp;";';
                            $stJs .= 'f.nuVlCredito.value = "";';
                            $stJs .= 'f.stComplementoCredito.value = "";';
                            $stJs .= 'f.inCodContaCredito.focus();';
                            $stJs .= 'f.stCreditos.value  = "true";';
                            $stJs .= 'f.btnIncluirCredito.value = "Incluir";';
                            $stJs .= 'f.btnLimparCredito.value = "Limpar";';
                            $stJs .= montaListaCredito();
                        }
                    }
                } else {
                    $stJs .= "alertaAviso('Informe o histórico do crédito.','form','erro','".Sessao::getId()."');";
                }
            } else {
                $stJs .= "alertaAviso('Informe o valor do crédito.','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "alertaAviso('Valor não pode ser 0,00.','form','erro','".Sessao::getId()."');";
        }
    } else {
         $stJs .= "alertaAviso('Informe a conta crédito.','form','erro','".Sessao::getId()."');";
    }

    echo $stJs;

  break;

 case 'excluirListaCredito' :
    $arTEMP = array();
    $arValoresCredito = array();
    $arCreditosExcluidos = array();
    $arValores = Sessao::read('arValoresCredito');
    foreach ($arValores as $arRegistro) {
        if ($arRegistro['inCodContaCredito'] != $_REQUEST['id']) {
            $arTEMP[ 'sequenciaCredito'      ] = $arRegistro[ "sequenciaCredito"      ];
            $arTEMP[ 'inCodContaCredito'     ] = $arRegistro[ "inCodContaCredito"     ];
            $arTEMP[ 'stContaCredito'        ] = $arRegistro[ "stContaCredito"        ];
            $arTEMP[ 'nuVlCredito'           ] = $arRegistro[ "nuVlCredito"           ];
            $arTEMP[ 'inCodHistoricoCredito' ] = $arRegistro[ "inCodHistoricoCredito" ];
            $arTEMP[ 'stNomHistoricoCredito' ] = $arRegistro[ "stNomHistoricoCredito" ];
            $arTEMP[ 'stComplementoCredito'  ] = $arRegistro[ "stComplementoCredito"  ];
            $arValoresCredito[] = $arTEMP;
        }
    }
    if (count($arValoresCredito) == 0) {
        $stJs = 'f.stCreditos.value  = false;';
    }
    Sessao::write('arValoresCredito', $arValoresCredito);
    $stJs .= montaListaCredito();
    echo $stJs;
 break;

 case "buscaProxLote":    // ok
    if ($_REQUEST['inCodEntidade'] != "") {
        $obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( 'M' );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->listar( $rsLote, 'cod_lote DESC LIMIT 1' );
        $inCodLote = $rsLote->getCampo('cod_lote')+1;
        $stJs  = 'f.cod_lote.value  = "'.$inCodLote.'";';
        $stJs .= 'f.inCodLote.value = "'.$inCodLote.'";';
    } else {
        $stJs = 'f.inCodLote.value = "";';
    }

    echo $stJs;
 break;

 case "validaLote":    //ok

    if ($_REQUEST['cod_lote'] != $_REQUEST['inCodLote']) {
        if ($_REQUEST['inCodLote'] != '' AND $_REQUEST['inCodEntidade'] != "") {
            include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php" );
            $obTContabilidadeLote = new TContabilidadeLote;
            $stFiltro  = " WHERE cod_lote     =  ".$_REQUEST['inCodLote'];
            $stFiltro .= "   AND exercicio    = '".Sessao::getExercicio()."'";
            $stFiltro .= "   AND cod_entidade =  ".$_REQUEST['inCodEntidade'];
            $stFiltro .= "   AND tipo         = 'M'";
            $obTContabilidadeLote->recuperaTodos($rsLote, $stFiltro);

            if ($rsLote->getNumLinhas() > 0) {
                $stJs  = "f.stNomLote.value = '".$rsLote->getCampo('nom_lote')."';";
                $stJs .= "f.stDtLote.value = '".$rsLote->getCampo('dt_lote')."';";

            } else {
                $stJs  = "alertaAviso('Lote informado inválido.','form','erro','".Sessao::getId()."');";
                $stJs .= "f.stNomLote.value = '';";
                $stJs .= "f.stDtLote.value = '';";
                $stJs .= "f.inCodLote.value = '';";
                $stJs .= "f.inCodLote.focus();";
            }
        } else {
            $stJs  = "alertaAviso('Informe a entidade.','form','erro','".Sessao::getId()."');";
            $stJs .= "f.inCodLote.value = '';";
            $stJs .= "f.stNomEntidade.focus();";
        }
    }

    echo $stJs;
 break;

 case "alterarListaDebito":

    $arValoresDebito  = array();
    $arRegistro       = array();

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche(Sessao::read('arValoresDebito'));
    $rsRecordSet->addFormatacao('nuVlDebito','NUMERIC_BR');

    while (!$rsRecordSet->eof()) {
        if ($rsRecordSet->getCampo('inCodContaDebito') == $_REQUEST['id']) {

            Sessao::write('inCodPlanoAlteracao', $rsRecordSet->getCampo('inCodContaDebito'));
            $stJs  = 'f.inCodHistoricoDebito.value = "'.$rsRecordSet->getCampo('inCodHistoricoDebito').'";';
            $stJs .= 'f.inCodContaDebito.value = "'.$rsRecordSet->getCampo('inCodContaDebito').'";';
            $stJs .= 'd.getElementById("stContaDebito").innerHTML = "'.$rsRecordSet->getCampo('stContaDebito').'";';
            $stJs .= 'd.getElementById("stNomHistoricoDebito").innerHTML = "'. $rsRecordSet->getCampo('stNomHistoricoDebito').'";';
            $stJs .= 'f.stContaDebito.value = "'.$rsRecordSet->getCampo('stContaDebito').'";';
            $stJs .= 'f.nuVlDebito.value = "'.$rsRecordSet->getCampo('nuVlDebito').'";';

            $stJs .= 'f.stComplementoDebito.value = "'.preg_replace('#\r?\n#','',$rsRecordSet->getCampo('stComplementoDebito')).'";';
            $stJs .= 'f.sequencia.value = "'.$rsRecordSet->getCampo('sequenciaDebito').'";';
            $stJs .= 'f.btnIncluirDebito.value = "Alterar";';
            $stJs .= 'f.btnLimparDebito.value = "Cancelar";';
        }
        $rsRecordSet->proximo();
    }

     echo  $stJs;
 break;

 case "alterarListaCredito":

    $arValoresCredito  = array();
    $arRegistro       = array();

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche(Sessao::read('arValoresCredito'));
    $rsRecordSet->addFormatacao('nuVlCredito','NUMERIC_BR');

    while (!$rsRecordSet->eof()) {
        if ($rsRecordSet->getCampo('inCodContaCredito') == $_REQUEST['id']) {
            Sessao::write('inCodPlanoAlteracao', $rsRecordSet->getCampo('inCodContaCredito'));
            $stJs  = 'f.inCodHistoricoCredito.value = "'.$rsRecordSet->getCampo('inCodHistoricoCredito').'";';
            $stJs .= 'f.inCodContaCredito.value = "'.$rsRecordSet->getCampo('inCodContaCredito').'";';
            $stJs .= 'd.getElementById("stContaCredito").innerHTML = "'.$rsRecordSet->getCampo('stContaCredito').'";';
            $stJs .= 'd.getElementById("stNomHistoricoCredito").innerHTML = "'. $rsRecordSet->getCampo('stNomHistoricoCredito').'";';
            $stJs .= 'f.stContaCredito.value = "'.$rsRecordSet->getCampo('stContaCredito').'";';
            $stJs .= 'f.nuVlCredito.value = "'.$rsRecordSet->getCampo('nuVlCredito').'";';
            $stJs .='f.stComplementoCredito.value="'.preg_replace('#\r?\n#','',$rsRecordSet->getCampo('stComplementoCredito')).'";';
            $stJs .= 'f.sequencia.value = "'.$rsRecordSet->getCampo('sequenciaCredito').'";';
            $stJs .= 'f.btnIncluirCredito.value = "Alterar";';
            $stJs .= 'f.btnLimparCredito.value = "Cancelar";';

        }
        $rsRecordSet->proximo();
    }
    echo $stJs;
 break;

 case "limparCampos":
    Sessao::remove('arValoresDebito');
    Sessao::remove('arValoresCredito');
    Sessao::remove('arDebitosSalvos');
    Sessao::remove('arCreditosSalvos');
    Sessao::remove('inCodPlanoAlteracao');

    $stJs  = "d.getElementById('spnListaCredito').innerHTML = '';";
    $stJs .= "d.getElementById('nuTotalCredito').innerHTML = '';";
    $stJs .= "d.getElementById('spnListaDebito').innerHTML = '';";
    $stJs .= "d.getElementById('nuTotalDebito').innerHTML = '';";
    $stJs .= 'f.inCodEntidade.value = "";';
    $stJs .= 'f.stNomEntidade.value = "";';
    $stJs .= 'f.inCodLote.value = "";';
    $stJs .= 'f.stNomLote.value = "";';
    $stJs .= 'f.stDtLote.value = "";';
    $stJs .= 'f.inCodHistoricoDebito.value = "";';
    $stJs .= 'f.inCodContaDebito.value = "";';
    $stJs .= 'd.getElementById("stContaDebito").innerHTML = "&nbsp;";';
    $stJs .= 'd.getElementById("stNomHistoricoDebito").innerHTML = "&nbsp;";';
    $stJs .= 'f.nuVlDebito.value = "";';
    $stJs .= 'f.stComplementoDebito.value = "";';
    $stJs .= 'f.inCodHistoricoCredito.value = "";';
    $stJs .= 'f.inCodContaCredito.value = "";';
    $stJs .= 'd.getElementById("stContaCredito").innerHTML = "&nbsp;";';
    $stJs .= 'd.getElementById("stNomHistoricoCredito").innerHTML = "&nbsp;";';
    $stJs .= 'f.nuVlCredito.value = "";';
    $stJs .= 'f.stComplementoCredito.value = "";';
    $stJs .= 'f.stCreditos.value  = "false";';
    $stJs .= 'f.stDebitos.value  = "false";';

    echo $stJs;

 break;

}

/* OK  */
function montaListaDebito()
{
    $obLista = new Lista;
    $rsListaDebito = new RecordSet;
    $rsListaDebito->preenche ( Sessao::read('arValoresDebito') );
    $rsListaDebito->addFormatacao( 'nuVlDebito', 'NUMERIC_BR');

    while (!$rsListaDebito->eof()) {
        $vlTotalDebito = str_replace('.','',$rsListaDebito->getCampo('nuVlDebito'));
        $vlTotalDebito = str_replace(',','.',$vlTotalDebito);
        $vlSomaDebito  = $vlSomaDebito + $vlTotalDebito;
        $rsListaDebito->proximo();
    }

    $vlTotalDebito = number_format($vlSomaDebito,2,',','.');
    $rsListaDebito->setPrimeiroElemento();

    $obLista = new Lista;
    $obLista->setTitulo('Dados da Lista');
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsListaDebito );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Conta");
    $obLista->ultimoCabecalho->setWidth( 45 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Histórico");
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[inCodContaDebito] - [stContaDebito]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[inCodHistoricoDebito] - [stNomHistoricoDebito]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuVlDebito" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('alterarListaDebito');" );
    $obLista->ultimaAcao->addCampo("","&id=[inCodContaDebito]&sequenciaDebito=[sequenciaDebito]&inCodContaDebito=[inCodContaDebito]");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('excluirListaDebito');" );
    $obLista->ultimaAcao->addCampo("","&id=[inCodContaDebito]");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs  = "d.getElementById('spnListaDebito').innerHTML = '".$stHTML."';\n";
    $stJs .= 'd.getElementById("nuTotalDebito").innerHTML = "'.$vlTotalDebito.'";';
    $stJs .= montaDiferenca();

    return $stJs;

}

/* OK */
function montaListaCredito()
{
    $obLista = new Lista;
    $rsListaCredito = new RecordSet;
    $rsListaCredito->preenche ( Sessao::read('arValoresCredito') );
    $rsListaCredito->addFormatacao( 'nuVlCredito', 'NUMERIC_BR');

    while (!$rsListaCredito->eof()) {
        $vlTotalCredito = str_replace('.','',$rsListaCredito->getCampo('nuVlCredito'));
        $vlTotalCredito = str_replace(',','.',$vlTotalCredito);
        $vlSomaCredito  = $vlSomaCredito + $vlTotalCredito;
        $rsListaCredito->proximo();
    }

    $vlTotalCredito = number_format($vlSomaCredito,2,',','.');
    $rsListaCredito->setPrimeiroElemento();

    $obLista = new Lista;
    $obLista->setTitulo('Dados da Lista');
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsListaCredito );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Conta");
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Histórico");
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[inCodContaCredito] - [stContaCredito]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[inCodHistoricoCredito] - [stNomHistoricoCredito]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuVlCredito" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('alterarListaCredito');" );
    $obLista->ultimaAcao->addCampo( "" , "&id=[inCodContaCredito]&sequenciaCredito=[sequenciaCredito]");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('excluirListaCredito');" );
    $obLista->ultimaAcao->addCampo("","&id=[inCodContaCredito]");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs  = "d.getElementById('spnListaCredito').innerHTML = '".$stHTML."';";
    $stJs .= 'd.getElementById("nuTotalCredito").innerHTML = "'.$vlTotalCredito.'";';
    $stJs .= montaDiferenca();

    return $stJs;

}

function montaDiferenca()
{
    $arCredito = array();
    $arCredito = Sessao::read('arValoresCredito');
    if (count($arCredito) > 0) {
        $rsListaCredito = new RecordSet;
        $rsListaCredito->preenche($arCredito);
        while (!$rsListaCredito->eof()) {
            $vlSomaCredito  = $vlSomaCredito + $rsListaCredito->getCampo('nuVlCredito');
            $rsListaCredito->proximo();
        }
    }

    $arDebito = array();
    $arDebito = Sessao::read('arValoresDebito');
    if (count($arDebito) > 0) {
        $rsListaDebito = new RecordSet;
        $rsListaDebito->preenche($arDebito);
        while (!$rsListaDebito->eof()) {
            $vlSomaDebito  = $vlSomaDebito + $rsListaDebito->getCampo('nuVlDebito');
            $rsListaDebito->proximo();
        }
    }

    $vlTotalDiferenca = $vlSomaCredito - $vlSomaDebito;
    $vlTotalDiferenca = number_format($vlTotalDiferenca,2,',','.');

    $stJs = 'd.getElementById("nuDiferenca").innerHTML = "'.$vlTotalDiferenca.'";';

    return $stJs;

}
