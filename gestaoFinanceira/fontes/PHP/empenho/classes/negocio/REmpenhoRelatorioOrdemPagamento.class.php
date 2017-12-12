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
    * Classe de Regra para Ordem de Pagamento do relatório
    * Data de Criação   : 18/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-12-18 17:28:18 -0200 (Ter, 18 Dez 2007) $

    * Casos de uso: uc-02.03.05
                    uc-02.03.22
                    uc-02.03.28
*/

/*
$Log$
Revision 1.16  2007/07/13 19:04:18  cako
Bug#9383#, Bug#9384#

Revision 1.15  2007/06/20 18:19:03  cako
Bug#9378#

Revision 1.14  2007/04/30 19:19:45  cako
implementação uc-02.03.28

Revision 1.13  2006/10/19 18:10:30  larocca
Bug #7264#

Revision 1.12  2006/09/28 09:52:29  eduardo
Bug #7060#

Revision 1.11  2006/07/07 13:52:06  jose.eduardo
Bug #6475#

Revision 1.10  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO);

/**
    * Classe de Regra para emissão do Plano de Contas com Banco/Recurso

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class REmpenhoRelatorioOrdemPagamento extends PersistenteRelatorio
{
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Integer
    * @access Private
*/
var $inCodOrdem;
/**
    * @var Boolean
    * @access Private
*/
var $boImplantado;

/**
     * @access public
     * @param string $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }
/**
     * @access public
     * @param string $valor
*/
function setExercicioEmpenho($valor) { $this->stExercicioEmpenho = $valor; }
/**
     * @access public
     * @param Integer $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade= $valor; }
/**
     * @access public
     * @param Integer $valor
*/
function setCodOrdem($valor) { $this->inCodOrdem   = $valor; }
/**
     * @access public
     * @param Boolean $valor
*/
function setImplantado($valor) { $this->boImplantado = $valor; }

/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio; }
/**
     * @access Public
     * @return String
*/
function getExercicioEmpenho() { return $this->stExercicioEmpenho; }
/**
     * @access Public
     * @return Integer
*/
function getCodEntidade() { return $this->inCodEntidade; }
/**
     * @access Public
     * @return Integer
*/
function getCodOrdem() { return $this->inCodOrdem;    }
/**
     * @access Public
     * @return Boolean
*/
function getImplantado() { return $this->boImplantado;  }

/**
    * Método Construtor
    * @access Private
*/
function REmpenhoRelatorioOrdemPagamento()
{
    parent::PersistenteRelatorio();
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$arRecordSet , $stOrder = "")
{
    include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoOrdemPagamento.class.php';
    include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoOrdemPagamentoRetencao.class.php';
    include_once CAM_GF_TES_NEGOCIO.'RTesourariaRelatorioRecibosExtra.class.php';
    $obTEmpenhoOrdemPagamento           = new TEmpenhoOrdemPagamento;
    $obTEmpenhoOrdemPagamentoRetencao   = new TEmpenhoOrdemPagamentoRetencao;
    $obRTesourariaRelatorioRecibosExtra = new RTesourariaRelatorioRecibosExtra;

    $arRecordSet = array();
    $stFiltro    = " AND   cod_entidade     IN (" . $this->inCodEntidade." )";
    $stFiltro   .= " AND   cod_ordem        = ".$this->inCodOrdem;
    $stFiltro   .= " AND   exercicio_ordem  = '" . $this->stExercicio."'";
    $obTEmpenhoOrdemPagamento->setDado( 'exercicio'         , $this->stExercicio   );
    $obTEmpenhoOrdemPagamento->setDado( 'cod_ordem'         , $this->inCodOrdem    );
    $obTEmpenhoOrdemPagamento->setDado( 'cod_entidade'      , $this->inCodEntidade );
    $obTEmpenhoOrdemPagamento->setDado( 'exercicio_empenho' , $this->stExercicioEmpenho   );

    // Faz o processo de buscar as assinaturas da ordem de pagamento para poder montar o array de assinaturas
    // para poder verificar se os cargos possuem pessoas. Assim a descrição abaixo da linha para assinatura é substituida
    // pelo nome e cargo
    include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamentoAssinatura.class.php";
    $obOPAssinatura = new TEmpenhoOrdemPagamentoAssinatura;
    $obOPAssinatura->setDado("cod_ordem" , $this->inCodOrdem);
    $obOPAssinatura->setDado("exercicio" , $this->stExercicio);
    $obOPAssinatura->setDado("cod_entidade", $this->inCodEntidade);
    $obOPAssinatura->recuperaAssinaturasOrdem( $rsAssinatura, "", " ORDER BY num_assinatura ", "" );
    $arPapel = $obOPAssinatura->arrayPapel();

    // Monta um array de assinatura contendo o papel como chave
    while (!$rsAssinatura->eof()) {
        foreach ($arPapel as $stChavePapel => $inCodPapel) {
            if ($inCodPapel == $rsAssinatura->getCampo('num_assinatura')) {
                $stChaveAssinatura = $stChavePapel;
                break;
            }
        }
        $arAssinatura[$stChaveAssinatura]['nome'] = $rsAssinatura->getCampo('nom_cgm');
        $arAssinatura[$stChaveAssinatura]['cargo'] = $rsAssinatura->getCampo('cargo');
        $rsAssinatura->proximo();
    }

    if ( trim($this->boImplantado) == 't' ) {
        $obErro = $obTEmpenhoOrdemPagamento->recuperaRelatorioRestos( $rsRecordSet, $stFiltro, $stOrder );
    } else {
        $obErro = $obTEmpenhoOrdemPagamento->recuperaRelatorioOP( $rsRecordSet, $stFiltro, $stOrder );
    }
    
    if (!$obErro->ocorreu()) {
        $obTEmpenhoOrdemPagamentoRetencao->setDado( 'exercicio'   , $this->stExercicio   );
        $obTEmpenhoOrdemPagamentoRetencao->setDado( 'cod_ordem'   , $this->inCodOrdem    );
        $obTEmpenhoOrdemPagamentoRetencao->setDado( 'cod_entidade', $this->inCodEntidade );
        $obErro = $obTEmpenhoOrdemPagamentoRetencao->recuperaRelacionamento( $rsRetencao);
    }

    if ( !$rsRecordSet->eof() && !$obErro->ocorreu() ) {
        $arLinha0 = array();
         //Linha0
        $arLinha0[0]['entidade']   = $rsRecordSet->getCampo('cod_entidade') . ' - ' . $rsRecordSet->getCampo('nom_entidade');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha0);
        $arRecordSet[0] = $rsNewRecord;

        //Bloco1
        $stNotificacao  = "Pague-se à ";
        $stNotificacao .= trim($rsRecordSet->getCampo('nom_cgm'));
        $stNotificacao .= " , inscrita no ";
        $stNotificacao .= $rsRecordSet->getCampo('cpfcnpj');
        $stNotificacao .= " sob número ";
        $stNotificacao .= $rsRecordSet->getCampo('cpf_cnpj');
        $stNotificacao .= " , ou à sua ordem a quantia de ";
        while ( !$rsRecordSet->eof() ) {
            $nuQuantia += $rsRecordSet->getCampo('vl_pagamento')-$rsRecordSet->getCampo('vl_anulado');
            $rsRecordSet->proximo();
        }
        $rsRecordSet->setPrimeiroElemento();

        $stNotificacao .= SistemaLegado::extenso($nuQuantia);

        $stComplemento = str_replace( chr(10) , "", $stNotificacao );
        $stComplemento = wordwrap( $stComplemento , 75, chr(13) );
        $arComplemento = explode( chr(13), $stComplemento );
        foreach ($arComplemento as $stComplemento) {
            $arNotificacao["2"] = $stComplemento;
            $arBloco1[] = $arNotificacao;
        }
        $arBloco1[] = array("2"=> "");
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arBloco1);
        $arRecordSet[1] = $rsNewRecord;

        //Bloco2
        $arBloco2 = array();
        $arBloco2[0]['2'] = $rsRecordSet->getCampo('nom_municipio').", ".SistemaLegado::dataExtenso($rsRecordSet->getCampo('dt_emissao'));
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arBloco2);
        $arRecordSet[2] = $rsNewRecord;

        //Bloco3
        $arBloco3[0]['2'] = "_______________________________";
        $arBloco3[0]['4'] = "_______________________________";
        $arBloco3[1]['2'] = ($arAssinatura['visto']['nome'] != "" ? $arAssinatura['visto']['nome'] : "Visto");
        $arBloco3[1]['4'] = ($arAssinatura['ordenador']['nome'] != "" ? $arAssinatura['ordenador']['nome'] : "Ordenador de Despesa");;
        $arBloco3[2]['2'] = ($arAssinatura['visto']['cargo'] != "" ? $arAssinatura['visto']['cargo'] : "");
        $arBloco3[2]['4'] = ($arAssinatura['ordenador']['cargo'] != "" ? $arAssinatura['ordenador']['cargo'] : "");
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arBloco3);
        $arRecordSet[3] = $rsNewRecord;

    }
    //Bloco4
    $stEmpenhoAtual = '';
    $inLinha = 0;
    while ( !$rsRecordSet->eof() ) {
        if ( $stEmpenhoAtual != $rsRecordSet->getCampo('cod_empenho').'/'.$rsRecordSet->getCampo('exercicio_empenho') ) {
            $stEmpenhoAtual               = $rsRecordSet->getCampo('cod_empenho').'/'.$rsRecordSet->getCampo('exercicio_empenho');
            $stDescricao = $rsRecordSet->getCampo('cod_empenho_relatorio').'/'.$rsRecordSet->getCampo('exercicio_empenho');
            if ($rsRecordSet->getCampo('dotacao_reduzida') != null && $rsRecordSet->getCampo('dotacao_reduzida') != '') {
                $stDescricao .= ' - '.$rsRecordSet->getCampo('dotacao_reduzida');
            }
            if ($rsRecordSet->getCampo('dotacao_formatada') != null && $rsRecordSet->getCampo('dotacao_formatada') != '') {
                $stDescricao .= ' - '.$rsRecordSet->getCampo('dotacao_formatada');
            }
            if ($rsRecordSet->getCampo('nom_conta') != null && $rsRecordSet->getCampo('nom_conta') != '') {
                $stDescricao .= ' - '.$rsRecordSet->getCampo('nom_conta');
            }
            $stDescricao = str_replace( chr(10) , "", $stDescricao );
            $stDescricao = wordwrap( $stDescricao, 80, chr(13) );
            $arDescricao = explode( chr(13), $stDescricao );
            $inCount = 0;
            foreach ($arDescricao as $stDescricao) {
                $arBloco4[ $inLinha++ ]['1'] .= $stDescricao;
                $inCount++;
            }
            if ( trim($this->boImplantado) == 't' ) {
                $arBloco4[ $inLinha++ ]['1'] .= substr('    Recurso: '.$rsRecordSet->getCampo('recurso_formatado'). ' / PAO: '.$rsRecordSet->getCampo('num_pao'),0,100);
            } else {
                $arBloco4[ $inLinha++ ]['1'] .= substr('    Recurso: '.$rsRecordSet->getCampo('recurso_formatado')." - ".$rsRecordSet->getCampo('nom_recurso'). ' / PAO: '.$rsRecordSet->getCampo('num_acao') . ' - '.$rsRecordSet->getCampo('nom_pao'),0,100);
            }
        }
        $arBloco4[ $inLinha   ]['1'] .= '    Liquidação: '. $rsRecordSet->getCampo('cod_nota_relatorio').'/'.$rsRecordSet->getCampo('exercicio_nota').' - '.$rsRecordSet->getCampo('dt_liquidacao');
        $vl_pagamento = number_format( $rsRecordSet->getCampo('vl_pagamento'), 2, "," ,"." );
        $vl_anulado   = number_format( $rsRecordSet->getCampo('vl_anulado')  , 2, "," ,"." );
        $arBloco4[ $inLinha   ]['2']  = $vl_pagamento;
        $arBloco4[ $inLinha++ ]['3']  = $vl_anulado;

        $stBanco = $rsRecordSet->getCampo('num_banco') . " - " . $rsRecordSet->getCampo('nom_banco');
        $stAgencia = $rsRecordSet->getCampo('num_agencia') . " - " . $rsRecordSet->getCampo('nom_agencia');
        $stContaCorrente = $rsRecordSet->getCampo('num_conta');
        $rsRecordSet->proximo();
    }
    
    
    if (!is_array($arBloco4)) {
        $arBloco4 = array();
    }
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arBloco4);
    $rsNewRecord->addFormatacao("4","NUMERIC_BR");
    $arRecordSet[4] = $rsNewRecord;
    $rsRecordSet->setPrimeiroElemento();
    //Bloco5
    $arBloco5[0][1] = "Total ";
    $arBloco5[0][2] = $nuQuantia;
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arBloco5);
    $rsNewRecord->addFormatacao("2","NUMERIC_BR");
    $arRecordSet[5] = $rsNewRecord;

    $arDadosBancoCredor = array();
    if ( $stBanco != '' AND $stAgencia != '' AND $stContaCorrente != '' ) {
        // Bloco Dados Bancários Credor
        $arDadosBancoCredor[0]['1'] = "Banco   : ".$stBanco;
        $arDadosBancoCredor[0]['2'] = "Agência : ".$stAgencia;
        $arDadosBancoCredor[0]['3'] = "Conta   : ".$stContaCorrente;
    
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arDadosBancoCredor);
        $arRecordSet['banco_credor'] = $rsNewRecord;

    }

    //Bloco6
    $arBloco6 = array();
    $stObservacao = str_replace( chr(10) , "", $rsRecordSet->getCampo('observacao') );
    $stObservacao = wordwrap( ($stObservacao) , 68, chr(13) );
    $arObservacao = explode( chr(13), $stObservacao );
    foreach ($arObservacao as $stObservacao) {
        $arObs[1] = $stObservacao;
        $arBloco6[] = $arObs;
    }

    $stNome = ($arAssinatura['tesoureiro']['nome'] != "" ? $arAssinatura['tesoureiro']['nome'] : "Tesoureiro");
    $stCargo = ($arAssinatura['tesoureiro']['cargo'] != "" ? $arAssinatura['tesoureiro']['cargo'] : "");
    $arBloco6[0][2] = '         Banco';
    $arBloco6[1][2] = '         ___________________________________________________';
    $arBloco6[2][2] = '         Documento Número                Cheque';
    $arBloco6[3][2] = '         _____________________       ___________________________';
    $arBloco6[4][2] = '         ___/___/___';
    $arBloco6[5][2] = '                        ______________________________________';
    $inLenNome = floor((120 - strlen($stNome))/2);
    $inLenCargo = floor((120 - strlen($stCargo))/2);
    $arBloco6[6][2] = str_repeat(" ", $inLenNome).$stNome;
    if ($stCargo != "") {
        $arBloco6[7][2] = str_repeat(" ", $inLenCargo).$stCargo;
    }

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arBloco6);
    $arRecordSet[6] = $rsNewRecord;

    //Bloco7
    $stDeclaracao = 'Declaro que recebi da '.$rsRecordSet->getCampo('nom_prefeitura').' a quantia de R$ ';
    $stDeclaracao .= number_format($nuQuantia,2,',','.').' ('.SistemaLegado::extenso($nuQuantia).' ).';
    $stDeclaracao = str_replace( chr(10), "", $stDeclaracao );
    $stDeclaracao = wordwrap( $stDeclaracao, 130, chr(13) );
    $arDeclaracao = explode( chr(13), $stDeclaracao );
    $inCount = 0;
    foreach ($arDeclaracao as $stDeclaracao) {
        $arBloco7[$inCount][1] = $stDeclaracao;
        $inCount++;
    }
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arBloco7);
    $arRecordSet[7] = $rsNewRecord;

    $inCount = 0;
    $arBloco8[$inCount++][1] = '';
    $arBloco8[$inCount++][1] = $rsRecordSet->getCampo('nom_municipio').' , __ de ________________________ de _______';
    $arBloco8[$inCount++][1] = '';
    $arBloco8[$inCount++][1] = '';
    $arBloco8[$inCount++][1] = '___________________________';
    $arBloco8[$inCount++][1] = 'Assinatura do credor';

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arBloco8);
    $arRecordSet[8] = $rsNewRecord;

  /**
         Retenções
                     **/
    if (!$rsRetencao->eof()) {  
        $inLinha = 0;
        // BLoco das Retenções
        while (!$rsRetencao->eof()) {
            $nuTlRetencao += $rsRetencao->getCampo('vl_retencao');
            $stNomConta = ($rsRetencao->getCampo('tipo') == 'O' ? $rsRetencao->getCampo('cod_receita') : $rsRetencao->getCampo('cod_plano')).' - '.$rsRetencao->getCampo('nom_conta');
            $stNomConta = str_replace( chr(10), "", $stNomConta );
            $stNomConta = wordwrap( $stNomConta, 90, chr(13) );
            $arNomConta = explode( chr(13), $stNomConta );
            if (isset($arNomConta[1])) {
                foreach ($arNomConta as $stNomConta) {
                    $arBloco9[$inLinha]['nom_conta'] = $stNomConta;
                    $arBloco9[$inLinha]['tipo'] = $rsRetencao->getCampo('tipo');
                    $arBloco9[$inLinha]['vl_retencao'] = '';
                    $inLinha++;
                }
                $arBloco9[$inLinha-1]['vl_retencao'] = number_format($rsRetencao->getCampo('vl_retencao'),'2',',','.');
                $arBloco9[$inLinha-1]['tipo'] = $rsRetencao->getCampo('tipo');
            } else {
                $arBloco9[$inLinha]['tipo'] = $rsRetencao->getCampo('tipo');
                $arBloco9[$inLinha]['nom_conta'] = $arNomConta[0];
                $arBloco9[$inLinha]['vl_retencao'] = number_format($rsRetencao->getCampo('vl_retencao'),'2',',','.');
            }
            $inLinha++;
            $rsRetencao->proximo();
        }
        $inCountExt = 0;
        $inCountOrc = 0;
        foreach ($arBloco9 as $item) {
            if ($item['tipo'] == 'O') {
                $arTmpRetOrc[$inCountOrc] = $item;
                $inCountOrc++;
            }
            if ($item['tipo'] == 'E') {
                $arTmpRetExt[$inCountExt] = $item;
                $inCountExt++;
            }
        }

        if ($arTmpRetOrc) {
            $rsNewRecord = new RecordSet;
            $rsNewRecord->preenche($arTmpRetOrc);
            $arRecordSet[9] = $rsNewRecord;
        }

        if ($arTmpRetExt) {
            $rsNewRecordb = new RecordSet;
            $rsNewRecordb->preenche($arTmpRetExt);
            $arRecordSet['9b'] = $rsNewRecordb;
        }

        //Bloco total da Retenção
        $arBloco10[0][1] = "Total Retenção";
        $arBloco10[0][2] = $nuTlRetencao;
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arBloco10);
        $rsNewRecord->addFormatacao("2","NUMERIC_BR");
        $arRecordSet[10] = $rsNewRecord;

        //Bloco Valor Liquido da OP
        $arBloco11[0][1] = "VALOR LÍQUIDO DA ORDEM DE PAGAMENTO";
        $arBloco11[0][2] = bcsub($nuQuantia,$nuTlRetencao,2);
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arBloco11);
        $rsNewRecord->addFormatacao("2","NUMERIC_BR");
        $arRecordSet[11] = $rsNewRecord;
    }
    //Bloco Data
    $arRecordSet['dt_ordem'] = $rsRecordSet->getCampo('dt_emissao');

    $obRTesourariaRelatorioRecibosExtra->setCodOrdem   ($this->inCodOrdem);
    $obRTesourariaRelatorioRecibosExtra->setExercicio  ($this->stExercicio);
    $obRTesourariaRelatorioRecibosExtra->setCodEntidade($this->inCodEntidade);
    $obRTesourariaRelatorioRecibosExtra->geraRecordSetOP($arRecordSetRecibos);

    $arRecordSet['arRecibosExtra'] = $arRecordSetRecibos;

}
}
