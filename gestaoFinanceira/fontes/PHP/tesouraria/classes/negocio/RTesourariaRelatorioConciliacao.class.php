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
    * Regra para emissão de relatório
    * Data de Criação   : 15/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    * Casos de uso: uc-02.04.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO  );
include_once ( CAM_GF_TES_NEGOCIO ."RTesourariaConciliacao.class.php"               );

/**
    * Classe de Regra para emissão de relatório

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen
*/
class RTesourariaRelatorioConciliacao extends PersistenteRelatorio
{
/**
    * @var Nueric
    * @access Private
*/
var $nuSaldoTesouraria;
/**

    * @var Numeric
    * @access Private
*/
var $boAgrupar;

/**
     * @access Public
     * @param String $valor
*/
function setSaldoTesouraria($valor) { $this->nuSaldoTesouraria = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setAgrupar($valor) { $this->boAgrupar         = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setCC($valor) { $this->boCC = $valor; }
/*
    * @access Public
    * @return String
*/
function getSaldoTesouraria() { return $this->nuSaldoTesouraria; }
/*
    * @access Public
    * @return Boolean
*/
function getAgrupar() { return $this->boAgrupar;         }
/*
    * @access Public
    * @return Boolean
*/
function getCC() { return $this->boCC; }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaRelatorioConciliacao()
{
    parent::PersistenteRelatorio();
    $this->obRTesourariaConciliacao    = new RTesourariaConciliacao;

}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$arRecordSet , $stOrdem = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaConciliacao.class.php" );
    $obTTesourariaConciliacao   = new TTesourariaConciliacao;

    $inCount = 0;
    $arRecordSet = array();

    $obTTesourariaConciliacao->setDado("exercicio",$this->obRTesourariaConciliacao->obRContabilidadePlanoBanco->getExercicio());
    $obTTesourariaConciliacao->setDado("cod_plano",$this->obRTesourariaConciliacao->obRContabilidadePlanoBanco->getCodPlano());
    $obTTesourariaConciliacao->setDado("mes"      ,$this->obRTesourariaConciliacao->getMes());
    $obErro = $obTTesourariaConciliacao->recuperaCabecalhoConciliacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    $arLinha1[0]['movimentacao_conciliada'] = "MOVIMENTAÇÃO PENDENTE";

    $rsLista = new RecordSet();
    $rsListaPendente = new RecordSet();
    $rsListaManual = new RecordSet();

    /* Movimentação */
    $this->obRTesourariaConciliacao->listarMovimentacao($rsLista, '', '', $boTransacao);

    /* Pendências */
    $this->obRTesourariaConciliacao->setDataInicial(Sessao::read('stDtInicial'));
    $this->obRTesourariaConciliacao->setDataFinal(Sessao::read('stDtFinal'));
    $this->obRTesourariaConciliacao->listarMovimentacaoPendente($rsListaPendente, '', '', $boTransacao);

    /* Manual */
    $this->obRTesourariaConciliacao->addLancamentoManual();
    $this->obRTesourariaConciliacao->roUltimoLancamentoManual->listar( $rsListaManual, '', $boTransacao);

    /* Preenche RecordSet */

    /* Corrente */
    $arMovimentacao = $rsLista->getElementos();
    for ( $x = 0; $x<count($arMovimentacao); $x++ ) {
        if (!$arMovimentacao[$x]['conciliar']) {
            $nuSaldoContabilConciliado = bcadd( $nuSaldoContabilConciliado, $arMovimentacao[$x]['vl_lancamento'], 4);
        } else {
            if (substr($arMovimentacao[$x]['dt_conciliacao'],3,2) != $this->obRTesourariaConciliacao->getMes()) {
                $nuSaldoContabilConciliado = bcadd( $nuSaldoContabilConciliado, ($arMovimentacao[$x]['vl_lancamento']), 4 );
            }
        }

    }

    /* Pendencia */
    $arPendencia    = ( $rsListaPendente->getNumLinhas() > -1 ) ? $rsListaPendente->getElementos() : array();

    /* Manual */
    $arManual       = ( $rsListaManual->getNumLinhas() > -1 )   ? $rsListaManual->getElementos()   : array();

    $rsLista         = new RecordSet();
    $rsListaPendente = new RecordSet();
    $stExercicio = $this->obRTesourariaConciliacao->obRContabilidadePlanoBanco->getExercicio();

    // Agrupa bordero e receita
    if ($this->boAgrupar) {
        $inCount = 0;
        // Agrupa bordero e receita das pendencias
        for ( $x = 0 ; $x < count( $arPendencia ); $x++ ) {
            foreach ($arPendencia[$x] as $key => $value) {
                $arMovAgrupada[$inCount][$key] = $value;
            }
            $arMovAgrupada[$inCount][$key]['indices'] = $x;
            // Agrupa bordero
            if ($arPendencia[$x]['cod_bordero']) {
                $inCodBordero = $arPendencia[$x]['cod_bordero'];
                $stDescricao = "Pagamento de empenho(s) conforme Borderô nr. ".$inCodBordero."-".$arPendencia[$x]['cod_entidade']."/".$stExercicio;
                $arMovAgrupada[$inCount]['descricao'] = $stDescricao;
                $nuVlMovConciliada = 0;
                $nuVlMovNConciliada = 0;
                $stIndiceMovCinciliada  = "";
                $stIndiceMovNCinciliada = "";
                while ($arPendencia[$x]['cod_bordero'] == $inCodBordero) {
                    if ($arPendencia[$x]['conciliar']) {
                        $nuVlMovConciliada = bcadd( $nuVlMovConciliada, $arPendencia[$x]['vl_lancamento'], 4 );
                        $stIndiceMovCinciliada .= $x.",";
                    } else {
                        $nuVlMovNConciliada = bcadd( $nuVlMovNConciliada, $arPendencia[$x]['vl_lancamento'], 4 );
                        $stIndiceMovNCinciliada .= $x.",";
                    }
                    $x++;
                }
                $x--;
                if ($nuVlMovConciliada != 0) {
                    $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovConciliada;
                    $arMovAgrupada[$inCount]['conciliar']     = true;
                    $arMovAgrupada[$inCount]['indices']       = substr($stIndiceMovCinciliada,0,strlen($stIndiceMovCinciliada)-1);
                    $inCount++;
                }
                if ($nuVlMovNConciliada != 0) {
                    foreach ($arPendencia[$x] as $key => $value) {
                        $arMovAgrupada[$inCount][$key] = $value;
                    }
                    $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovNConciliada;
                    $arMovAgrupada[$inCount]['conciliar']     = false;
                    $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                    $arMovAgrupada[$inCount]['indices']       = substr($stIndiceMovNCinciliada,0,strlen($stIndiceMovNCinciliada)-1);
                    $inCount++;
                }
                $inCount--;
            }
            // Agrupa Arrecadacao
            if ($arPendencia[$x]['cod_receita']) {
                $inCodReceita = $arPendencia[$x]['cod_receita'];
                $stDescricao  = "Arrecadação da receita ".$inCodReceita."-".$arPendencia[$x]['cod_entidade']."/".$stExercicio;
                $arMovAgrupada[$inCount]['descricao'] = $stDescricao;
                $nuVlMovConciliada  = 0;
                $nuVlMovNConciliada = 0;
                while ($arPendencia[$x]['cod_receita'] == $inCodReceita) {
                    if( $arPendencia[$x]['conciliar'])
                        $nuVlMovConciliada = bcadd( $nuVlMovConciliada, $arPendencia[$x]['vl_lancamento']*(-1), 4 );
                    else
                        $nuVlMovNConciliada = bcadd( $nuVlMovNConciliada, $arPendencia[$x]['vl_lancamento']*(-1), 4 );
                    $x++;
                }
                $x--;
                if ($nuVlMovConciliada != 0) {
                    $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovConciliada;
                    $arMovAgrupada[$inCount]['conciliar']     = true;
                    $inCount++;
                }
                if ($nuVlMovNConciliada != 0) {
                    foreach ($arPendencia[$x] as $key => $value) {
                        $arMovAgrupada[$inCount][$key] = $value;
                    }
                    $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovNConciliada;
                    $arMovAgrupada[$inCount]['conciliar']     = false;
                    $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                    $inCount++;
                }
                $inCount--;
            }
            $inCount++;
        }

        // agrupa movimentacao do mes atual
        for ( $x = 0 ; $x < count( $arMovimentacao ); $x++ ) {
            foreach ($arMovimentacao[$x] as $key => $value) {
                $arMovAgrupada[$inCount][$key] = $value;
            }
            $arMovAgrupada[$inCount][$key]['indices'] = $x;
            // Agrupa bordero
            if ($arMovimentacao[$x]['cod_bordero']) {
                $inCodBordero = $arMovimentacao[$x]['cod_bordero'];
                $stDescricao = "Pagamento de empenho(s) conforme Borderô nr. ".$inCodBordero."-".$arMovimentacao[$x]['cod_entidade']."/".$stExercicio;
                $arMovAgrupada[$inCount]['descricao'] = $stDescricao;
                $nuVlMovConciliada = 0;
                $nuVlMovNConciliada = 0;
                $stIndiceMovCinciliada  = "";
                $stIndiceMovNCinciliada = "";
                while ($arMovimentacao[$x]['cod_bordero'] == $inCodBordero) {
                    if ($arMovimentacao[$x]['conciliar']) {
                        $nuVlMovConciliada = bcadd( $nuVlMovConciliada, $arMovimentacao[$x]['vl_lancamento'], 4 );
                        $stIndiceMovCinciliada .= $x.",";
                    } else {
                        $nuVlMovNConciliada = bcadd( $nuVlMovNConciliada, $arMovimentacao[$x]['vl_lancamento'], 4 );
                        $stIndiceMovNCinciliada .= $x.",";
                    }
                    $x++;
                }
                $x--;
                if ($nuVlMovConciliada != 0) {
                    $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovConciliada;
                    $arMovAgrupada[$inCount]['conciliar']     = true;
                    $arMovAgrupada[$inCount]['indices']       = substr($stIndiceMovCinciliada,0,strlen($stIndiceMovCinciliada)-1);
                    $inCount++;
                }
                if ($nuVlMovNConciliada != 0) {
                    foreach ($arMovimentacao[$x] as $key => $value) {
                        $arMovAgrupada[$inCount][$key] = $value;
                    }
                    $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovNConciliada;
                    $arMovAgrupada[$inCount]['conciliar']     = false;
                    $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                    $arMovAgrupada[$inCount]['indices']       = substr($stIndiceMovNCinciliada,0,strlen($stIndiceMovNCinciliada)-1);
                    $inCount++;
                }
                $inCount--;
            }
            // Agrupa Arrecadacao
            if ($arMovimentacao[$x]['cod_receita']) {
                $inCodReceita = $arMovimentacao[$x]['cod_receita'];
                $stDescricao  = "Arrecadação da receita ".$inCodReceita."-".$arMovimentacao[$x]['cod_entidade']."/".$stExercicio;
                $arMovAgrupada[$inCount]['descricao'] = $stDescricao;
                $nuVlMovConciliada  = 0;
                $nuVlMovNConciliada = 0;
                while ($arMovimentacao[$x]['cod_receita'] == $inCodReceita) {
                    if( $arMovimentacao[$x]['conciliar'])
                        $nuVlMovConciliada = bcadd( $nuVlMovConciliada, $arMovimentacao[$x]['vl_lancamento'], 4 );
                    else
                        $nuVlMovNConciliada = bcadd( $nuVlMovNConciliada, $arMovimentacao[$x]['vl_lancamento'], 4 );
                    $x++;
                }
                $x--;
                if ($nuVlMovConciliada != 0) {
                    $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovConciliada;
                    $arMovAgrupada[$inCount]['conciliar']     = true;
                    $inCount++;
                }
                if ($nuVlMovNConciliada != 0) {
                    foreach ($arMovimentacao[$x] as $key => $value) {
                        $arMovAgrupada[$inCount][$key] = $value;
                    }
                    $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovNConciliada;
                    $arMovAgrupada[$inCount]['conciliar']     = false;
                    $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                    $inCount++;
                }
                $inCount--;
            }
        $inCount++;
        }

        $arLista = $arMovAgrupada;
    } else {
        for ( $x = 0; $x<count( $arPendencia ); $x++ ) {

            if (!$arPendencia[$x]['conciliar']) {
                $nuSaldoContabilConciliado = bcadd( $nuSaldoContabilConciliado, ($arPendencia[$x]['vl_lancamento']*(-1)), 4 );
            } else {
                if (substr($arPendencia[$x]['dt_conciliacao'],3,2) != $this->obRTesourariaConciliacao->getMes()) {
                    $nuSaldoContabilConciliado = bcadd( $nuSaldoContabilConciliado, ($arPendencia[$x]['vl_lancamento']*(-1)), 4 );
                }
            }
        }
        $arLista =  array_merge($arMovimentacao, $arPendencia);
    }

    for ( $x = 0; $x<count( $arManual ); $x++ ) {
        $inSequencia = $arManual[$x]['sequencia'] - 1;
        $arManual[$x]['id'] = 'M'.$inSequencia;

        if ($arManual[$x]['conciliado'] == 't') {
            if (substr($arManual[$x]['dt_conciliacao'],3,2) != $this->obRTesourariaConciliacao->getMes()) {
                if(!$this->getCC()){
                	$arManual[$x]['vl_lancamento'] = bcsub( $nuSaldoContabilConciliado, $arManual[$x]['vl_lancamento'], 4 );
                }
            }
        } else {
            $nuSaldoContabilConciliado = bcsub( $nuSaldoContabilConciliado, $arManual[$x]['vl_lancamento'], 4 );
        }
    }

    $arLista = array_merge( $arLista, $arManual );

    if (count($arLista)) {
        sort($arLista);
    }
    $rsLista->preenche($arLista);
    $arLinha2['entradaTesouraria'] = array();
    $arLinha2['saidaTesouraria'] = array();
    $arLinha2['entradaBanco'] = array();
    $arLinha2['saidaBanco'] = array();
    while (!$rsLista->eof()) {
        $inMesConciliacao = substr($rsLista->getCampo('dt_conciliacao'),3,2);
        //regra: se nao estiver conciliado ou se a conciliacao nao e do mes corrente

        if ((!$rsLista->getCampo('conciliar') || $rsLista->getCampo('conciliado') == 'f') OR $inMesConciliacao != $this->obRTesourariaConciliacao->getMes()) {

            $nuVlLancamento = $rsLista->getCampo("vl_lancamento");
            // if (intval(substr($rsLista->getCampo("dt_lancamento"), 3, 2)) == $this->obRTesourariaConciliacao->getMes() OR $rsLista->getCampo('conciliar')) {
            if (intval(substr($rsLista->getCampo("dt_lancamento"), 3, 2)) != $this->obRTesourariaConciliacao->getMes() OR $rsLista->getCampo('conciliar')) {
                if ($rsLista->getCampo('tipo_movimentacao')) {
                    $nuVlLancamento = $nuVlLancamento*(-1);
                }
            }

            if (trim($rsLista->getCampo('ordem')) == "") {
                // tipo == C (entrada) | tipo == D (saida)
                if ($rsLista->getCampo('tipo_valor') == 'C') {
                    $stChave = 'entradaTesouraria';

                } else {
                    $stChave = 'saidaTesouraria';
                }
            // se não é uma movimentacao corrente do mes passado
            } else {
                if ($rsLista->getCampo('vl_lancamento') > 0) {
                    $stChave = 'entradaBanco';
                } else {
                    $stChave = 'saidaBanco';
                }
            }

            $inCount = count($arLinha2[$stChave]);
            $inCountAux = 0;
            // $nuSaldoContabilConciliado = bcadd($nuSaldoContabilConciliado,  $nuVlLancamento, 4);

            list($dia,$mes,$ano) = explode( '/', $rsLista->getCampo("dt_lancamento") );
            $arLinha2[$stChave][$inCount]['ordem']        = $ano.$mes.$dia;
            $arLinha2[$stChave][$inCount]['movimentacao'] = $rsLista->getCampo("dt_lancamento");
            $arLinha2[$stChave][$inCount]['valor']        = "";
            if($this->getCC()){
                $arLinha2[$stChave][$inCount]['cod_plano']    = $rsLista->getCampo("cod_plano");
                $boConciliado = $rsLista->getCampo("conciliado");
                if(!is_null($boConciliado)){
                    if($boConciliado=='f'){
                        $boConciliado = false;
                        $arLinha2[$stChave][$inCount]['manual']     = true;
                    }

                    $arLinha2[$stChave][$inCount]['conciliado']     = $boConciliado;
                    $arLinha2[$stChave][$inCount]['dt_conciliacao'] = $rsLista->getCampo("dt_conciliacao");
                }
            }

            $stNom = $rsLista->getCampo('descricao');
            if ( $rsLista->getCampo('tipo') == 'P' and trim($stNom) ) {
                if( !strstr( $stNom, "Borderô" ) AND trim($rsLista->getCampo('observacao')))
                    $stNom .= " - ".$rsLista->getCampo('observacao');
            }

            $stNom = str_replace(chr(10), "", $stNom);
            $stNom = str_replace(chr(13), " ", $stNom);

            $stNom = wordwrap( $stNom, 75, chr(13) );
            $arNom = explode( chr(13), $stNom );
            
            if($this->getCC()){
                foreach ($arNom as $stNom) {
                    if(count($arNom)>1){
                        $arLinha2[$stChave][$inCount]['descricao'][$inCountAux] = $stNom;
                        $inCountAux++;
                    }else{
                        $arLinha2[$stChave][$inCount]['descricao'] = $stNom;
                        $inCount++;
                    }
                }
                if(count($arNom)==1){
                    $inCount--;
                }
            }else{
                foreach ($arNom as $stNom) {
		            $arLinha2[$stChave][$inCount]['descricao'] = $stNom;
		            $inCount++;
                }
                $inCount--;
            }
            $arLinha2[$stChave][$inCount]['valor']        = number_format($nuVlLancamento, 2, ",", ".");
        }

        $rsLista->proximo();
    }

    $nuSaldoContabilConciliado = bcsub($this->nuSaldoTesouraria,$nuSaldoContabilConciliado,4);
    $nuSaldoContabilConciliado = number_format($nuSaldoContabilConciliado,2,',','.');

    $nuSaldoContabil = number_format($nuSaldoContabil,2,',','.');

    $arLinha2['total'][0]['descricao'] = "Saldo Conciliado";
    $arLinha2['total'][0]['valor']     = $nuSaldoContabilConciliado;

    $rsLista->setPrimeiroElemento();

    //Linha0
    $arLinha0[0]['descricao']   = "Entidade ";
    $arLinha0[0]['valor']       = $rsRecordSet->getCampo('cod_entidade') . ' - ' . $rsRecordSet->getCampo('nom_entidade');
    $arLinha0[1]['descricao']   = "Conta ";
    $arLinha0[1]['valor']       = $rsRecordSet->getCampo('cod_plano') . ' - ' . $rsRecordSet->getCampo('nom_conta');
    $arLinha0[2]['descricao']   = "Data do Extrato ";
    $arLinha0[2]['valor']       = $rsRecordSet->getCampo('dt_extrato');
    $arLinha0[3]['descricao']   = "Saldo do Extrato ";
    $arLinha0[3]['valor']       = number_format($rsRecordSet->getCampo('vl_extrato'),2,',','.');
    $mes = $rsRecordSet->getCampo('mes');
    $meses =  array(1 =>"Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
    $arLinha0[4]['descricao']   = "Mês de Conciliação ";
    $arLinha0[4]['valor']       = $mes . " - " . $meses[$mes];
    $arLinha0[5]['descricao']   = "Saldo Tesouraria ";
    $arLinha0[5]['valor']       = number_format($this->nuSaldoTesouraria,2,',','.');

    $this->obRTesourariaConciliacao->obRTesourariaAssinatura->setExercicio($this->obRTesourariaConciliacao->obRContabilidadePlanoBanco->getExercicio());
    $this->obRTesourariaConciliacao->obRTesourariaAssinatura->obROrcamentoEntidade->setCodigoEntidade($this->obRTesourariaConciliacao->obRTesourariaAssinatura->obROrcamentoEntidade->getCodigoEntidade());
    $this->obRTesourariaConciliacao->obRTesourariaAssinatura->setTipo('CO');
    $obErro = $this->obRTesourariaConciliacao->obRTesourariaAssinatura->listar($rsAssinat, '', $boTransacao);

    if (!$obErro->ocorreu()) {
        $arAssinatura = array();
        if ($rsAssinat->getNumLinhas()==1) {
            $arAssinatura[0]['assinatura2']   = str_pad(trim($rsAssinat->getCampo("nom_cgm")), 30, " ", STR_PAD_BOTH);
            $arAssinatura[1]['assinatura2']   = str_pad(trim($rsAssinat->getCampo("cargo")), 30, " ", STR_PAD_BOTH);
            $arAssinatura[2]['assinatura2']   = str_pad(trim($rsAssinat->getCampo("num_matricula")), 30, " ", STR_PAD_BOTH);
        }
        if ($rsAssinat->getNumLinhas()==2) {
            $arAssinatura[0]['assinatura1']   = str_pad(trim($rsAssinat->getCampo("nom_cgm")), 30, " ", STR_PAD_BOTH);
            $arAssinatura[1]['assinatura1']   = str_pad(trim($rsAssinat->getCampo("cargo")), 30, " ", STR_PAD_BOTH);
            $arAssinatura[2]['assinatura1']   = str_pad(trim($rsAssinat->getCampo("num_matricula")), 30, " ", STR_PAD_BOTH);
            $rsAssinat->proximo();
            $arAssinatura[0]['assinatura3']   = str_pad(trim($rsAssinat->getCampo("nom_cgm")), 30, " ", STR_PAD_BOTH);
            $arAssinatura[1]['assinatura3']   = str_pad(trim($rsAssinat->getCampo("cargo")), 30, " ", STR_PAD_BOTH);
            $arAssinatura[2]['assinatura3']   = str_pad(trim($rsAssinat->getCampo("num_matricula")), 30, " ", STR_PAD_BOTH);
        }
        if ($rsAssinat->getNumLinhas()==3) {
            $inCount = 1;
            while (!$rsAssinat->eof()) {
                $arAssinatura[0]['assinatura'.$inCount]   = str_pad(trim($rsAssinat->getCampo("nom_cgm")), 30, " ", STR_PAD_BOTH);
                $arAssinatura[1]['assinatura'.$inCount]   = str_pad(trim($rsAssinat->getCampo("cargo")), 30, " ", STR_PAD_BOTH);
                $arAssinatura[2]['assinatura'.$inCount]   = str_pad(trim($rsAssinat->getCampo("num_matricula")), 30, " ", STR_PAD_BOTH);
                $inCount++;
                $rsAssinat->proximo();
            }
        }
    }

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha0);
    $arRecordSet[0] = $rsNewRecord;

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha1);
    $arRecordSet[1] = $rsNewRecord;

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha2['entradaBanco']);
    $arRecordSet[2] = $rsNewRecord;

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha2['saidaBanco']);
    $arRecordSet[3] = $rsNewRecord;

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha2['entradaTesouraria']);
    $arRecordSet[4] = $rsNewRecord;

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha2['saidaTesouraria']);
    $arRecordSet[5] = $rsNewRecord;

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha2['total']);
    $arRecordSet[6] = $rsNewRecord;

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arAssinatura);
    $arRecordSet[7] = $rsNewRecord;

}

}
