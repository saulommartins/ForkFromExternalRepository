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
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 17/08/2014

    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    $Id: ANEXO27FUNDEFBBAAS.inc.php 60598 2014-11-03 13:12:44Z michel $
*/

include_once CAM_GPC_TCERN_MAPEAMENTO."TTRNAnexo27Fundefbbaas.class.php";
include_once CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php";

$obTEntidade = new TEntidade();
$obTMapeamento = new TTRNAnexo27Fundefbbaas();
$obTMapeamento->setDado( 'inBimestre' , $inBimestre );               

$codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::getExercicio());
$arEntidades = explode( ',', $inCodEntidade );

foreach ($arEntidades as $value) {
    $stFiltro = " WHERE nspname = 'pessoal_".$value."'";
    $obTEntidade->recuperaEsquemasCriados($rsEsquema,$stFiltro);
    if ($rsEsquema->getNumLinhas() > 0 || $codEntidadePrefeitura == $value)
        $arEsquemasEntidades[] = $value;
}

$arDtInicial = explode( '/', $stDataInicial );
$inMesInicial  = $arDtInicial[1];
$inAnoInicial  = $arDtInicial[2];

$arDataFinal = explode( '/', $stDataFinal );
$inMesFinal  = $arDataFinal[1];
$inAnoFinal  = $arDataFinal[2];

$arArquivo = array();

foreach ($arEsquemasEntidades as $inCodEntidade2) {    
    $stFiltro = " WHERE nspname = 'folhapagamento_".$inCodEntidade2."'";
    $obTEntidade->recuperaEsquemasCriados($rsEsquemas,$stFiltro);

    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("mesInicial" , $inMesInicial );
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("anoInicial" , $inAnoInicial );
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("mesFinal"   , $inMesFinal   );
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("anoFinal"   , $inAnoFinal   );

    $stEntidade = "";
    if ($rsEsquemas->getElementos() > 0 && $codEntidadePrefeitura != $inCodEntidade2) 
        $stEntidade = "_".$inCodEntidade2;
        
    // Busca a lista de eventos definidos na configuracao
    $stCampo   = "valor";
    $stTabela  = " administracao.configuracao ";
    $stFiltro  = " WHERE exercicio = '".Sessao::getExercicio()."'";
    $stFiltro .= "   AND parametro = 'remuneracao_base_fundef".$stEntidade."' ";
    $stFiltro .= "   AND cod_modulo = 49 ";

    $stFiltroEventos = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);
    
    if (strpos($stFiltroEventos, ',') !== false) {
        $arFiltroEventos = explode( ',', $stFiltroEventos );
    } else {
        $arFiltroEventos = array();
        if (strlen(trim($stFiltroEventos)) > 0) { //string contem somente um numero
            $arFiltroEventos[] = $stFiltroEventos;
        }
    }

    $arEventos = array();
    $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento();
    for($i=0;$i<count($arFiltroEventos);$i++){
        $obRFolhaPagamentoEvento->setCodEvento($arFiltroEventos[$i]);
        $obRFolhaPagamentoEvento->listarEvento($rsEventos);
        
        if($rsEventos->getNumLinhas()==1)
            $arEventos[] = $rsEventos->getCampo('codigo');
    }
    $stEventos = implode(",", $arEventos);
    
    $sessaoEntidade = str_replace('_', '', Sessao::getEntidade());
    Sessao::setEntidade(str_replace('_', '', $stEntidade));

    $stOrder = " ORDER BY cod_periodo_movimentacao DESC LIMIT 1 ";
    $obTFolhaPagamentoPeriodoMovimentacao->setTabela("folhapagamento".$stEntidade.".periodo_movimentacao");
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaIntervaloPeriodosMovimentacaoDaCompetencia($rsPeriodoMovimentacao, "", $stOrder);
    
    // Busca os codigos de lotacao definidos na configuracao
    $stCampo   = "valor";
    $stTabela  = "administracao.configuracao";
    $stFiltro  = " WHERE exercicio = '".Sessao::getExercicio()."'";
    $stFiltro .= "   AND parametro = 'lotacao_fundef".$stEntidade."' ";
    $stFiltro .= "   AND cod_modulo = 49 ";

    $inCodLotacao = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);
    $inCodLotacao = ($inCodLotacao) ? $inCodLotacao : '0';
    
    unset($arCargo);
    unset($arLotacao);
    unset($arFuncional);
    $arCargo = array();
    $arLotacao = array();
    $arFuncional = array();

    if ($rsPeriodoMovimentacao->getNumLinhas() > 0) {
        foreach ($rsPeriodoMovimentacao->getElementos() as $arPeriodoMovimentacao ) {
            $obTMapeamento->setDado('inCodEntidade'     , $inCodEntidade2   );
            $obTMapeamento->setDado('inCodMovimentacao' , $arPeriodoMovimentacao['cod_periodo_movimentacao'] );
            $obTMapeamento->setDado('inCodLotacao'      , $inCodLotacao     );
            $obTMapeamento->setDado('stEntidade'        , $stEntidade       );
            $obTMapeamento->setDado('stEventos'         , $stEventos        );
            
            $obTMapeamento->recuperaRelacionamento($rsRecordSet);

            while ( !$rsRecordSet->eof() ) {
                $arCargo[$rsRecordSet->getCampo('cod_cargo')] = $rsRecordSet->getCampo('descricao_cargo').";".$rsRecordSet->getCampo('cod_tipo_cargo');
                $arLotacao[$rsRecordSet->getCampo('codigo_lotacao')] = $rsRecordSet->getCampo('descricao_lotacao');
                $arFuncional[$rsRecordSet->getCampo('codigo_nivel_funcional')] = $rsRecordSet->getCampo('descricao_nivel_funcional');
                
                $rsRecordSet->proximo();
            }
            
            //Separar CARGO
            ksort($arCargo);
            foreach ($arCargo as $key=>$value) {
                $arArquivo['Cargo'][$key] = $value;
            }
            
            //Separar LOTAÇÃO
            ksort($arLotacao);
            foreach ($arLotacao as $key=>$value) {
                $arArquivo['Lotacao'][$key] = $value;
            }

            //Separar NÍVEL FUNCIONAL
            ksort($arFuncional);
            foreach ($arFuncional as $key=>$value) {
                $arArquivo['Nivel'][$key] = $value;
            }
            
            $rsRecordSet->setPrimeiroElemento();
            while ( !$rsRecordSet->eof() ) {
                unset($arTemp);
                $arTemp['matricula']                = $rsRecordSet->getCampo('matricula');
                $arTemp['cpf']                      = $rsRecordSet->getCampo('cpf');
                $arTemp['nome']                     = $rsRecordSet->getCampo('nome');
                $arTemp['id']                       = $rsRecordSet->getCampo('id');
                $arTemp['nr_titulo_eleitor']        = $rsRecordSet->getCampo('nr_titulo_eleitor');
                $arTemp['dt_nascimento']            = $rsRecordSet->getCampo('dt_nascimento');
                $arTemp['sexo']                     = $rsRecordSet->getCampo('sexo');
                $arTemp['grau_instrucao']           = $rsRecordSet->getCampo('grau_instrucao');
                $arArquivo['Pessoal'][] = $arTemp;
                
                unset($arTemp);
                $arTemp['matricula']                = $rsRecordSet->getCampo('matricula');
                $arTemp['cpf']                      = $rsRecordSet->getCampo('cpf');
                $arTemp['vinculo']                  = $rsRecordSet->getCampo('vinculo');
                $arTemp['situacaofuncional']        = $rsRecordSet->getCampo('situacaofuncional');
                $arTemp['cod_cargo']                = $rsRecordSet->getCampo('cod_cargo');
                $arTemp['codigo_nivel_funcional']   = $rsRecordSet->getCampo('codigo_nivel_funcional');
                $arTemp['codigo_lotacao']           = $rsRecordSet->getCampo('codigo_lotacao');
                $arTemp['forma_ingresso']           = $rsRecordSet->getCampo('forma_ingresso');
                $arTemp['dt_admissao']              = $rsRecordSet->getCampo('dt_admissao');
                $arTemp['forma_afastamento']        = $rsRecordSet->getCampo('forma_afastamento');
                $arTemp['dt_afastamento']           = $rsRecordSet->getCampo('dt_afastamento');
                $arTemp['vencimento_base']          = $rsRecordSet->getCampo('vencimento_base');
                $arTemp['total_outras_vantagens']   = $rsRecordSet->getCampo('outras_vantagens');
                $arTemp['inss']                     = $rsRecordSet->getCampo('inss');
                $arTemp['irrf']                     = $rsRecordSet->getCampo('irrf');
                $arTemp['total_outros_descontos']   = $rsRecordSet->getCampo('outros_descontos');
                $arArquivo['Folha'][] = $arTemp;

                $rsRecordSet->proximo();
            }
        }
    }
    
    Sessao::setEntidade($sessaoEntidade);
}

unset( $arTemp              );
unset( $arPessoal           );
unset( $arFolha             );
unset( $arCargoFiltrado     );
unset( $arLotacaoFiltrado   );
unset( $arFuncionalFiltrado );
unset( $arCargo             );
unset( $arLotacao           );
unset( $arFuncional         );

$arPessoal              = array();
$arFolha                = array();
$arCargoFiltrado        = array();
$arLotacaoFiltrado      = array();
$arFuncionalFiltrado    = array();
$arCargo                = array();
$arLotacao              = array();
$arFuncional            = array();
$countSequencial=2;

//Ajustar CARGO
if(is_array($arArquivo['Cargo']))
    $arCargo = $arArquivo['Cargo'];
ksort($arCargo);
foreach ($arCargo as $key=>$value) {
    unset($arTemp);
    $arDescTipoCargo = explode(';', $value);
    
    $arTemp['cod_cargo']        = $key;
    $arTemp['descricao_cargo']  = $arDescTipoCargo[0];
    $arTemp['cod_tipo_cargo']   = $arDescTipoCargo[1];
    $arTemp['registro']         = $countSequencial;
    $arCargoFiltrado[]          = $arTemp;
    $countSequencial++;
}

//Ajustar LOTAÇÃO
if(is_array($arArquivo['Lotacao']))
    $arLotacao = $arArquivo['Lotacao'];
ksort($arLotacao);
foreach ($arLotacao as $key=>$value) {
    unset($arTemp);
    $arTemp['codigo_lotacao']   = $key;
    $arTemp['descricao_lotacao']= $value;
    $arTemp['registro']         = $countSequencial;
    $arLotacaoFiltrado[]        = $arTemp;
    $countSequencial++;
}

//Ajustar NÍVEL FUNCIONAL
if(is_array($arArquivo['Nivel']))
    $arFuncional = $arArquivo['Nivel'];
ksort($arFuncional);
foreach ($arFuncional as $key=>$value) {
    unset($arTemp);
    $arTemp['codigo_nivel_funcional']   = $key;
    $arTemp['descricao_nivel_funcional']= $value;
    $arTemp['registro']                 = $countSequencial;
    $arFuncionalFiltrado[]              = $arTemp;
    $countSequencial++;
}

//RecordSet Cargo
$rsCargo = new recordset;
$rsCargo->preenche($arCargoFiltrado);

//RecordSet Lotacao
$rsLotacao = new recordset;
$rsLotacao->preenche($arLotacaoFiltrado);

//RecordSet Funcional
$rsFuncional = new recordset;
$rsFuncional->preenche($arFuncionalFiltrado);

//Ajustar Pessoal e Folha
for ( $i=0; $i<count($arArquivo['Pessoal']);$i++ ) {
    unset($arTemp);
    $arTemp['matricula']                = $arArquivo['Pessoal'][$i]['matricula'];
    $arTemp['cpf']                      = $arArquivo['Pessoal'][$i]['cpf'];
    $arTemp['nome']                     = $arArquivo['Pessoal'][$i]['nome'];
    $arTemp['id']                       = $arArquivo['Pessoal'][$i]['id'];
    $arTemp['nr_titulo_eleitor']        = $arArquivo['Pessoal'][$i]['nr_titulo_eleitor'];
    $arTemp['dt_nascimento']            = $arArquivo['Pessoal'][$i]['dt_nascimento'];
    $arTemp['sexo']                     = $arArquivo['Pessoal'][$i]['sexo'];
    $arTemp['grau_instrucao']           = $arArquivo['Pessoal'][$i]['grau_instrucao'];
    $arTemp['registro']                 = $countSequencial;
    $arPessoal[]                        = $arTemp;
    $countSequencial++;
    
    unset($arTemp);
    $arTemp['matricula']                = $arArquivo['Folha'][$i]['matricula'];
    $arTemp['cpf']                      = $arArquivo['Folha'][$i]['cpf'];
    $arTemp['vinculo']                  = $arArquivo['Folha'][$i]['vinculo'];
    $arTemp['situacaofuncional']        = $arArquivo['Folha'][$i]['situacaofuncional'];
    $arTemp['cod_cargo']                = $arArquivo['Folha'][$i]['cod_cargo'];
    $arTemp['codigo_nivel_funcional']   = $arArquivo['Folha'][$i]['codigo_nivel_funcional'];
    $arTemp['codigo_lotacao']           = $arArquivo['Folha'][$i]['codigo_lotacao'];
    $arTemp['forma_ingresso']           = $arArquivo['Folha'][$i]['forma_ingresso'];
    $arTemp['dt_admissao']              = $arArquivo['Folha'][$i]['dt_admissao'];
    $arTemp['forma_afastamento']        = $arArquivo['Folha'][$i]['forma_afastamento'];
    $arTemp['dt_afastamento']           = $arArquivo['Folha'][$i]['dt_afastamento'];
    $arTemp['vencimento_base']          = $arArquivo['Folha'][$i]['vencimento_base'];
    $arTemp['total_outras_vantagens']   = $arArquivo['Folha'][$i]['outras_vantagens'];
    $arTemp['inss']                     = $arArquivo['Folha'][$i]['inss'];
    $arTemp['irrf']                     = $arArquivo['Folha'][$i]['irrf'];
    $arTemp['total_outros_descontos']   = $arArquivo['Folha'][$i]['outros_descontos'];
    $arTemp['registro']                 = $countSequencial;
    $arFolha[]                          = $arTemp;
    $countSequencial++;
}

//RecordSet Pessoal
$rsPessoal = new recordset;
$rsPessoal->preenche($arPessoal);

//RecordSet Folha
$rsFolha = new recordset;
$rsFolha->preenche($arFolha);

//CARGO
$obExportador->roUltimoArquivo->addBloco($rsCargo);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]2");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cargo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_cargo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo_cargo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(87);

//LOTAÇÃO
$obExportador->roUltimoArquivo->addBloco($rsLotacao);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]3");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_lotacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_lotacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(89);

//NÍVEL FUNCIONAL
$obExportador->roUltimoArquivo->addBloco($rsFuncional);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]4");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_nivel_funcional");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_nivel_funcional");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(115);

$inCount=0;
foreach ($rsPessoal->arElementos as $arPessoal) {
    $inCount++;
    $stChave = $arPessoal['matricula'].$arPessoal['cpf'].$arPessoal['registro'];
    
    $rsBloco = 'rsBloco_'.$inCount;
    unset($$rsBloco);
    $$rsBloco = new RecordSet();
    $$rsBloco->preenche(array($arPessoal));
    
    //CADASTRO DE PESSOAL
    $obExportador->roUltimoArquivo->addBloco($$rsBloco);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]7");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("id");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nr_titulo_eleitor");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_nascimento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sexo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("grau_instrucao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

    //FOLHA DE PAGAMENTO
    foreach ($rsFolha->arElementos as $arFolha) {
        $stChave1 = $arFolha['matricula'].$arFolha['cpf'].($arFolha['registro']-1);
        
        if ($stChave === $stChave1) {
            $inCount++;
            
            $rsBloco = 'rsBloco_'.$inCount;
            unset($$rsBloco);
            $$rsBloco = new RecordSet();
            $$rsBloco->preenche(array($arFolha));
        
            $obExportador->roUltimoArquivo->addBloco($$rsBloco);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]8");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("matricula");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vinculo");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("situacaofuncional");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cargo");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_nivel_funcional");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_lotacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("forma_ingresso");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_admissao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("forma_afastamento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_afastamento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vencimento_base");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("total_outras_vantagens");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inss");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("irrf");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("total_outros_descontos");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("[]");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
        }
    }
}