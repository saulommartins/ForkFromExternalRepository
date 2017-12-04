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
* Página processamento para Relatório Definível de Servidor
* Data de Criação   : 06/03/2007

* @author Analista: Dagiane Vieira
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30860 $
$Name$
$Author: rgarbin $
$Date: 2008-03-10 11:58:23 -0300 (Seg, 10 Mar 2008) $

* Casos de uso: uc-04.04.48
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
// include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoPeriodoMovimentacao.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
// include_once CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoFolhaSituacao.class.php';
// include_once CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoPeriodoMovimentacao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'DefinivelServidor';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJS   = 'JS'.$stPrograma.'.js';

function removeAcentos($des_texto)
{
    $des_texto=mb_eregi_replace('[áàãâä]','a',$des_texto);
    $des_texto=mb_eregi_replace('[éèêë]' ,'e',$des_texto);
    $des_texto=mb_eregi_replace('[íìîï]' ,'i',$des_texto);
    $des_texto=mb_eregi_replace('[óòôõö]','o',$des_texto);
    $des_texto=mb_eregi_replace('[úùûü]' ,'u',$des_texto);
    $des_texto=mb_eregi_replace('[ñ]'    ,'n',$des_texto);
    $des_texto=mb_eregi_replace('[ç]'    ,'c',$des_texto);
    $des_texto=mb_eregi_replace('[ÁÀÃÂÄ]','A',$des_texto);
    $des_texto=mb_eregi_replace('[ÉÈÊË]' ,'E',$des_texto);
    $des_texto=mb_eregi_replace('[ÍÌÎÏÏ]','I',$des_texto);
    $des_texto=mb_eregi_replace('[ÓÒÔÕÖ]','O',$des_texto);
    $des_texto=mb_eregi_replace('[ÚÙÛÜ]' ,'U',$des_texto);
    $des_texto=mb_eregi_replace('[Ñ]'    ,'N',$des_texto);
    $des_texto=mb_eregi_replace('[Ç]'    ,'C',$des_texto);
    $des_texto=mb_eregi_replace('[/]'    ,'_',$des_texto);

    $des_texto=addslashes($des_texto);

    return $des_texto;
}

// Criando Array de referencia as colunas do SQL
$arReferenciaSQL = array();
$arReferenciaSQL['Endereço']                   = 'endereco';
$arReferenciaSQL['Bairro']                     = 'bairro';
$arReferenciaSQL['CEP']                        = 'cep';
$arReferenciaSQL['UF']                         = 'uf';
$arReferenciaSQL['Município']                  = 'municipio';
$arReferenciaSQL['Fone']                       = 'fone';
$arReferenciaSQL['Escolaridade']               = 'escolaridade';
$arReferenciaSQL['Nome']                       = 'nome';
$arReferenciaSQL['Salário Bruto']              = 'salario_bruto';
$arReferenciaSQL['Salário Líquido']            = 'salario_liquido';
$arReferenciaSQL['Descontos da Folha Salário'] = 'descontos_da_folha_salario';
$arReferenciaSQL['CPF']                        = 'cpf';
$arReferenciaSQL['RG']                         = 'rg';
$arReferenciaSQL['Banco']                      = 'banco';
$arReferenciaSQL['Agência']                    = 'agencia';
$arReferenciaSQL['Conta']                      = 'conta';
$arReferenciaSQL['Matrícula']                  = 'matricula';
$arReferenciaSQL['Lotação']                    = 'lotacao';
$arReferenciaSQL['Previdência']                = 'previdencia';
$arReferenciaSQL['Evento1 Quantidade']         = 'evento1_qte';
$arReferenciaSQL['Evento2 Quantidade']         = 'evento2_qte';
$arReferenciaSQL['Evento3 Quantidade']         = 'evento3_qte';
$arReferenciaSQL['Evento4 Quantidade']         = 'evento4_qte';
$arReferenciaSQL['Evento1 Valor']              = 'evento1_valor';
$arReferenciaSQL['Evento2 Valor']              = 'evento2_valor';
$arReferenciaSQL['Evento3 Valor']              = 'evento3_valor';
$arReferenciaSQL['Evento4 Valor']              = 'evento4_valor';

if ($_POST['stSituacao'] == 'pensionistas') {
    $arReferenciaSQL['Data Nascimento']             = 'data_nascimento';
    $arReferenciaSQL['Ocupação']                    = 'ocupacao';
    $arReferenciaSQL['CID']                         = 'cid';
    $arReferenciaSQL['Grau Parentesco']             = 'grau_parentesco';
    $arReferenciaSQL['Matrícula Gerador Benefício'] = 'matricula_gerador_beneficio';
    $arReferenciaSQL['Nome Gerador Benefício']      = 'nome_gerador_beneficio';
    $arReferenciaSQL['Número Benefício']            = 'numero_beneficio';
    $arReferenciaSQL['Processo']                    = 'processo';
    $arReferenciaSQL['Data Inclusão Processo']      = 'data_inclusao_processo';
    $arReferenciaSQL['Tipo Dependência']            = 'tipo_dependencia';
    $arReferenciaSQL['Percentual Pagamento Pensão'] = 'percentual_pagamento_pensao';
    $arReferenciaSQL['Data Início Benefício']       = 'data_inicio_beneficio';
    $arReferenciaSQL['Data Encerramento Benefício'] = 'data_encerramento_beneficio';
    $arReferenciaSQL['Motivo Encerramento']         = 'motivo_encerramento';
} else {
    $arReferenciaSQL['Data Nascimento']          = 'data_nascimento';
    $arReferenciaSQL['PIS/PASEP']                = 'pis_pasep';
    $arReferenciaSQL['Título de Eleitor']        = 'titulo_de_eleitor';
    $arReferenciaSQL['Seção do Título']          = 'secao_do_titulo';
    $arReferenciaSQL['Zona Título']              = 'zona_titulo';
    $arReferenciaSQL['CTPS']                     = 'ctps';
    $arReferenciaSQL['Série CTPS']               = 'serie_ctps';
    $arReferenciaSQL['Data Nomeação']            = 'data_nomeacao';
    $arReferenciaSQL['Data Posse']               = 'data_posse';
    $arReferenciaSQL['Data Admissão']            = 'data_admissao';
    $arReferenciaSQL['Data Rescisão']            = 'data_rescisao';
    $arReferenciaSQL['Causa Rescisão']           = 'causa_rescisao';
    $arReferenciaSQL['Tipo Admissão']            = 'tipo_admissao';
    $arReferenciaSQL['Vínculo Empregatício']     = 'vinculo_empregaticio';
    $arReferenciaSQL['Categoria']                = 'categoria';
    $arReferenciaSQL['Classif Agentes Nocivos']  = 'classif_agentes_nocivos';
    $arReferenciaSQL['Regime/Subdivisão Cargo']  = 'regime_subdivisao_cargo';
    $arReferenciaSQL['Cargo/Especialidade']      = 'cargo_especialidade';
    $arReferenciaSQL['Regime/Subdivisão Função'] = 'regime_subdivisao_funcao';
    $arReferenciaSQL['Função/Especialidade']     = 'funcao';
    $arReferenciaSQL['Horas Mensais']            = 'horas_mensais';
    $arReferenciaSQL['Horas Semanais']           = 'horas_semanais';
    $arReferenciaSQL['Padrão']                   = 'padrao';
    $arReferenciaSQL['Valor Padrão']             = 'valor_padrao';
    $arReferenciaSQL['Salário']                  = 'salario';
    $arReferenciaSQL['Forma Pagamento']          = 'forma_pagamento';
    $arReferenciaSQL['Local']                    = 'local';
    $arReferenciaSQL['Data Opção FGTS']          = 'data_opcao_fgts';
}

//Versão BIRT
$preview = new PreviewBirt(4,22,7);
$preview->setVersaoBirt('2.5.0');
$preview->setReturnURL( CAM_GRH_PES_INSTANCIAS.'relatorio/FLDefinivelServidor.php');
$preview->setTitulo('Relatório de Definível Servidor');
$preview->setNomeArquivo('definivelServidor');
$preview->setExportaExcel(true);
// $preview->setFormato('pdf');

# Seta string que guarda os titulos
$contador = 0;
$contadorEventoQte = 1;
$contadorEventoValor = 1;
$colunasVisiveis = '';
$stCodAtributos = '';
$boAgrupar = $_POST['boAgrupar'];
$boHideTotais = 'true';

$arCampos = Sessao::read('arCampos');

//periodo de movimentação
$inMesFinal = str_pad($_POST['inCodMes'], 2, 0, STR_PAD_LEFT);
$dtCompetenciaFinal = $inMesFinal.'/'.$_POST['inAno'];
$stFiltro = " AND to_char(dt_final,'mm/yyyy') = '".$dtCompetenciaFinal."'";
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentaco,$stFiltro);
$inCodPeriodoMovimentacao = $rsPeriodoMovimentaco->getCampo('cod_periodo_movimentacao');

//cria a tabela para adicionar os parametros
$dbCriaTabela = new dataBaseLegado;
$dbCriaTabela->abreBd();
$select =   "SELECT definivelServidor()";
$dbCriaTabela->abreSelecao($select);
$stNomeTabela = $dbCriaTabela->pegaCampo("definivelservidor");

if ((isset($arCampos)) && (count($arCampos)>0)) {
    foreach ($arCampos as $chave => $dados) {
        if (array_key_exists($dados['campo'], $arReferenciaSQL)) {
            $colunasVisiveis .= $arReferenciaSQL[$dados['campo']].',';
            $totalLarguraColunas += $dados['tamanho'];

            if (preg_match('/evento/', strtolower($dados['campo']))
             || $dados['campo'] == 'Salário Bruto'
             || $dados['campo'] == 'Salário Líquido'
             || $dados['campo'] == 'Descontos da Folha' ) {
                $boHideTotais = 'false';
                $boAgrupar = 'true';
            }
            if (preg_match('/data admissão/', strtolower($dados['campo']))) {
                $stDataAdmInicial = $dados['stDataInicial'];
                $stDataAdmFinal = $dados['stDataFinal'];
            }

            if (preg_match('/data nascimento/', strtolower($dados['campo']))) {
                if ($dados['stMes'] != '') {
                    $inMes = $dados['stMes'];
                } else {
                    $stDataNascInicial = $dados['stDataInicial'];
                    $stDataNascFinal = $dados['stDataFinal'];
                }
            }

            if (preg_match('/data nomeação/', strtolower($dados['campo']))) {
                $stDataNomInicial = $dados['stDataInicial'];
                $stDataNomFinal = $dados['stDataFinal'];
            }

            if (preg_match('/data opção fgts/', strtolower($dados['campo']))) {
                $stDataFgtsInicial = $dados['stDataInicial'];
                $stDataFgtsFinal = $dados['stDataFinal'];
            }

            if (preg_match('/data posse/', strtolower($dados['campo']))) {
                $stDataPosseInicial = $dados['stDataInicial'];
                $stDataPosseFinal = $dados['stDataFinal'];
            }

            if (preg_match('/data rescisão/', strtolower($dados['campo']))) {
                $stDataResInicial = $dados['stDataInicial'];
                $stDataResFinal = $dados['stDataFinal'];
            }

            if (preg_match('/evento/', strtolower($dados['campo']))) {
                $stEvento = $dados['cod_evento'].' - '.$dados['nom_evento'];

                $inCodCampoEvento = substr($dados['campo'], 6, 1);

                if (preg_match('/quantidade/', strtolower($dados['campo']))) {
                    $arCodEventos['Quantidade'][$inCodCampoEvento] = $dados['cod_evento'];
                    $preview->addParametro('legenda_evento'.$inCodCampoEvento.'_qte', $stEvento);
                    $contadorEventoQte++;
                } else {
                    $arCodEventos['Valor'][$inCodCampoEvento] = $dados['cod_evento'];
                    $preview->addParametro('legenda_evento'.$inCodCampoEvento.'_valor', $stEvento);
                    $contadorEventoValor++;
                }
            }
        } else {
            $contador++;
            $stCodAtributos .= $dados['codAtributo'].',';
            $colunasVisiveis .= 'atributo'.$contador.'*_*'.$dados['codAtributo'].',';
            $preview->addParametro('labelColuna'.$contador, $dados['campo']);
            $totalLarguraColunas += $dados['tamanho'];
        }
    }

    for ($inCount = 1; $inCount <= 4; $inCount++) {
        $inCodEventoQuantidade = $arCodEventos['Quantidade'][$inCount];
        $inCodEventoValor = $arCodEventos['Valor'][$inCount];
        if ($inCodEventoQuantidade != '') {
            $stCodEventosQte .= $inCodEventoQuantidade.',';
        } else {
            $stCodEventosQte .= '0,';
        }

        if ($inCodEventoValor != '') {
            $stCodEventosValor .= $inCodEventoValor.',';
        } else {
            $stCodEventosValor .= '0,';
        }

    }

    $stCodAtributos = substr($stCodAtributos, 0, -1);
    $colunasVisiveis = substr($colunasVisiveis, 0, -1);
    $stCodEventosQte = substr($stCodEventosQte, 0, -1);
    $stCodEventosValor = substr($stCodEventosValor, 0, -1);
}

# Seta Filtros
$stCodigos = '';
$inCodAtributo = 0;

switch ($_POST['stTipoFiltro']) {
    case 'contrato_rescisao':
    case 'contrato':
        $arContratos = Sessao::read('arContratos');
        foreach ($arContratos as $chave => $arContrato) {
            $stCodigos .= $arContrato['cod_contrato'].',';
        }
        $stCodigos = substr($stCodigos, 0, -1);
        break;

    case 'lotacao_grupo':
        $stCodigos = implode(',', $_POST['inCodLotacaoSelecionados']);
        break;

    case 'local_grupo':
        $stCodigos = implode(',', $_POST['inCodLocalSelecionados']);
        break;

    case 'reg_sub_fun_esp_grupo':
        $stCodigos = implode(',', $_POST['inCodSubDivisaoSelecionados']);
        break;

    case 'cargo_grupo':
        $stCodigos = implode(',', $_POST['inCodCargoSelecionados']);
        break;

    case 'funcao_grupo':
        $stCodigos = implode(',', $_POST['inCodFuncaoSelecionados']);
        break;

    case 'sub_divisao_funcao_grupo':
            $stCodigos = implode(',', $_POST['inCodSubDivisaoSelecionadosFunc']);
        break;

    case 'atributo_servidor_grupo':
    case 'atributo_pensionista_grupo':
        $inCodAtributo = $_POST['inCodAtributo'];
        $stNomeVariavel = 'Atributo_'.$inCodAtributo.'_'.$_POST['inCodCadastro'];

        if (is_array($_POST[$stNomeVariavel.'_Selecionados'])) {
            $arValorAtributos = $_POST[$stNomeVariavel.'_Selecionados'];
            $stCodigos = implode(',', $arValorAtributos);
            $boArray = 'true';
        } else {
            $stCodigos = $_POST[$stNomeVariavel];
            $boArray = 'false';
        }
        break;
}

# Começa a carregar a variavel que vai seta a ordernação
$stOrdenacao = '';
$cont = 1;
foreach ($_POST as $stCampo => $stValor) {
    if (strpos($stCampo,'boOrdenar') === 0) {
        $arOrdenar = explode('_',$stCampo);
        $stOrdemTemp = '';
        $stOrdemAtributoTemp = '';
        array_shift($arOrdenar);
        array_pop($arOrdenar);
        foreach ($arOrdenar as $stOrdenar) {
            $stOrdemTemp .= strtolower($stOrdenar).'_';
            $stOrdemAtributoTemp .= $stOrdenar.' ';
        }
        $stOrdemTemp = substr($stOrdemTemp,0,strlen($stOrdemTemp)-1);

        if (!array_key_exists(trim($stOrdemAtributoTemp), $arReferenciaSQL)) {
            $stOrdenacao .= 'atributo'.$cont.',';
            $cont++;
        } else {
            $stOrdemTemp = removeAcentos($stOrdemTemp);
            $stOrdenacao .= $stOrdemTemp.',';
        }
    }
}

$stOrdenacao = substr($stOrdenacao, 0, -1);

if (isset($_POST['inCodComplementar'])) {
    $str2 = (int) $_POST['inCodComplementar'];
    $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('inCodComplementar', '$str2', 'true', 'integer', 8 );\n";
} else {
    $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('inCodComplementar', '0', 'true', 'integer', 8 );\n";
}

if (isset($_POST['inCodConfiguracao'])) {
    $str3 = (int) $_POST['inCodConfiguracao'];
    $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('inCodConfiguracao', '$str3', 'true', 'integer', 10 );\n";
} else {
    $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('inCodConfiguracao', '0', 'true', 'integer', 10 );\n";
}

$preview->addParametro('entidade'            , trim(Sessao::getCodEntidade($boTransacao)));
$preview->addParametro('colunasVisiveis'     , $colunasVisiveis);
$preview->addParametro('larguraColunas'      , $totalLarguraColunas);
$preview->addParametro('boAgrupar'           , $boAgrupar);
$preview->addParametro('boHideTotais'        , $boHideTotais);
$preview->addParametro('boQuebrar'           , trim($_POST['boQuebrar']));
$preview->addParametro('stOrdenacao'         , $stOrdenacao);

$str7 = trim(Sessao::getEntidade());
$str4 = trim($_POST['stSituacao']);
$str5 = trim($_POST['stTipoFiltro']);

    if ($stDataAdmInicial) {
        $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values( 'dtDataAdmIni',   'data_admissao   >= to_date(''$stDataAdmInicial'', ''dd/mm/yyyy'')',    'false', 'date', 99);\n";
    }

    if ($stDataAdmFinal) {
        $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('dtDataAdmFin',   'data_admissao   <= to_date(''$stDataAdmFinal'',      ''dd/mm/yyyy'')',    'false', 'date', 99);\n";
    }

    if ($inMes) {
        $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('inMes',  'EXTRACT(month from data_nascimento)::INTEGER = ''$inMes''::INTEGER',    'false', 'date', 99);\n";
    }

    if ($stDataNascInicial) {
        $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('dtDataNascIni',  'data_nascimento >= to_date(''$stDataNascInicial'',   ''dd/mm/yyyy'')',    'false', 'date', 99);\n";
    }

    if ($stDataNascFinal) {
        $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('dtDataNascFin',  'data_nascimento <= to_date(''$stDataNascFinal'',     ''dd/mm/yyyy'')',    'false', 'date', 99);\n";
    }

    if ($stDataNomInicial) {
        $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('dtDataNomIni',   'data_nomeacao   >= to_date(''$stDataNomInicial'',    ''dd/mm/yyyy'')',    'false', 'date', 99);\n";
    }

    if ($stDataNomFinal) {
        $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('dtDataNomFin',   'data_nomeacao   <= to_date(''$stDataNomFinal'',      ''dd/mm/yyyy'')',    'false', 'date', 99);\n";
    }

    if ($stDataFgtsInicial) {
        $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('dtDataFgtsIni',  'data_opcao_fgts >= to_date('$stDataFgtsInicial'',   ''dd/mm/yyyy'')',    'false', 'date', 99);\n";
    }

    if ($stDataFgtsFinal) {
        $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('dtDataFgtsFin',  'data_opcao_fgts <= to_date(''$stDataFgtsFinal'',     ''dd/mm/yyyy'')',    'false', 'date', 99);\n";
    }

    if ($stDataResInicial) {
        $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('dtDataResIni',   'data_rescisao   >= to_date(''$stDataResInicial'',    ''dd/mm/yyyy'')',    'false', 'date', 99);\n";
    }

    if ($stDataResFinal) {
        $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('dtDataResFin',   'data_rescisao   <= to_date(''$stDataResFinal'',      ''dd/mm/yyyy'')',    'false', 'date', 99);\n";
    }
    if ($stDataPosseInicial) {
        $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('dtDataPosseIni', 'data_posse      >= to_date(''$stDataPosseInicial'',  ''dd/mm/yyyy'')',    'false', 'date', 99);\n";
    }
    if ($stDataPosseFinal) {
        $insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('dtDataPosseFin', 'data_posse      <= to_date(''$stDataPosseFinal'',    ''dd/mm/yyyy'')',    'false', 'date', 99);\n";
    }

$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('inCodPeriodoMovimentacao', '$inCodPeriodoMovimentacao', 'true', 'integer', 9  );\n";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('stEntidade',               '$str7',                     'true', 'varchar', 6  );\n";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('stSituacao',               '$str4',                     'true', 'varchar', 1  );\n";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('stTipoFiltro',             '$str5',                     'true', 'varchar', 2  );\n";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('stCodigos',                '$stCodigos',                'true', 'varchar', 3  );\n";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('stCodEventosValor',        '$stCodEventosValor',        'true', 'varchar', 12 );\n";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('stCodEventosQte',          '$stCodEventosQte',          'true', 'varchar', 11 );\n";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('inCodAtributo',            '$inCodAtributo',            'true', 'integer', 4  );\n";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('boArray',                  '$boArray',                  'true', 'boolean', 5  );\n";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('stCodAtributos',           '$stCodAtributos',           'true', 'varchar', 7  );\n";

if ( !$dbCriaTabela->executaSql($insert) ) {
    $stJs .= "alertaAviso('Erro ao criar tabela temporária','form','aviso','".Sessao::getId()."');";
    SistemaLegado::executaFrameOculto($stJs);
}

$jsOnload = "window.onunload = function sair() {
    executaFuncaoAjax('dropTmpTable','&stNomeTabela=$stNomeTabela',true);
};";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/URBEM/ajax.php';

$dbCriaTabela->limpaSelecao();
$dbCriaTabela->fechaBd();

$preview->addParametro('stNomeTabela'              , $stNomeTabela);

$preview->preview();

?>
