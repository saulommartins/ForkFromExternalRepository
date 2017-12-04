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
    * Classe de regra de negócio
    * Data de Criação   : 24/09/2004

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2008-01-07 16:53:33 -0200 (Seg, 07 Jan 2008) $

    * Casos de uso: uc-02.01.09
*/

/*
$Log$
Revision 1.10  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO   );
include_once( CAM_FW_PDF."RRelatorio.class.php"          );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"            );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoAnexo1Receita.class.php"     );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoAnexo1Despesa.class.php"     );

/**
    * Classe de regra de negócio

    * @author Desenvolvedor: Diego Barbosa Victoria
    * @author Analista: Jorge Ribarr
*/
class ROrcamentoRelatorioAnexo1 extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFOrcamentoAnexo1Receita;
/**
    * @var Object
    * @access Private
*/
var $obFOrcamentoAnexo1Despesa;
/**
    * @var Object
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Object
    * @access Private
*/
var $inCodDemDespesa;
/**
    * @var Object
    * @access Private
*/
var $inExercicio;
/**
    * @var Object
    * @access Private
*/
var $stFiltro;

/**
     * @access Public
     * @param Object $valor
*/
function setFOrcamentoAnexo1Receita($valor) { $this->obFOrcamentoAnexo1Receita  = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setFOrcamentoAnexo1Despesa($valor) { $this->obFOrcamentoAnexo1Despesa  = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodDemDespesa($valor) { $this->inCodDemDespesa           = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade                  = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setExercicio($valor) { $this->inExercicio                  = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setFiltro($valor) { $this->stFiltro                     = $valor; }

/**
     * @access Public
     * @return Object
*/
function getFOrcamentoAnexo1Receita() { return $this->obFOrcamentoAnexo1Receita ;  }
/**
     * @access Public
     * @return Object
*/
function getFOrcamentoAnexo1Despesa() { return $this->obFOrcamentoAnexo1Despesa ;  }
/**
     * @access Public
     * @return Object
*/
function getCodDemDespesa() { return $this->inCodDemDespesa;                    }
/**
     * @access Public
     * @return Object
*/
function getCodEntidade() { return $this->inCodEntidade;                    }
/**
     * @access Public
     * @return Object
*/
function getExercicio() { return $this->inExercicio;                    }
/**
     * @access Public
     * @return Object
*/
function getFiltro() { return $this->stFiltro;                       }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioAnexo1()
{
    $this->obFOrcamentoAnexo1Receita    = new FOrcamentoAnexo1Receita;
    $this->obFOrcamentoAnexo1Despesa    = new FOrcamentoAnexo1Despesa;
    $this->obROrcamentoDespesa          = new ROrcamentoDespesa;
    $this->obRRelatorio                 = new RRelatorio;
    $this->obROrcamentoDespesa->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm')         );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$arRecordSets , $stOrder = "")
{
    $nuSuperavitCapital = $nuDeficitCapital = $nuTotCorrente = $nuSuperavitCorrente = $nuDeficitCorrente = '';
    $stSuperavitCapital = $stDeficitCapital = $stSuperavitCorrente = $stDeficitCorrente = '';
    $arNivel1ReceitaCorrente = $arNivel1DespesaCorrente = $arNivel1ReceitaCapital = $arNivel1DespesaCapital = array();

    $this->obFOrcamentoAnexo1Receita->setDado('exercicio'    , $this->getExercicio()     );
    $this->obFOrcamentoAnexo1Receita->setDado('inCodEntidade', $this->getCodEntidade()   );
    $this->obFOrcamentoAnexo1Receita->setDado('stFiltro'     , $this->getFiltro()        );
    $obErro = $this->obFOrcamentoAnexo1Receita->recuperaTodos( $rsReceita );

    $this->obFOrcamentoAnexo1Despesa->setDado('exercicio'    , $this->getExercicio()     );
    $this->obFOrcamentoAnexo1Despesa->setDado('inCodEntidade', $this->getCodEntidade()   );
    $this->obFOrcamentoAnexo1Despesa->setDado('stFiltro'     , $this->getFiltro()        );
    $this->obFOrcamentoAnexo1Despesa->setDado('inNumOrgao'   , $this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() );
    $this->obFOrcamentoAnexo1Despesa->setDado('inNumUnidade' , $this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade() );
    $obErro = $this->obFOrcamentoAnexo1Despesa->recuperaTodos( $rsDespesa );

    //Receita
    while ( !$rsReceita->eof() ) {
        $stClassificacao    = $rsReceita->getCampo('classificacao');
        $arClassificacao    = preg_split("/[^a-zA-Z0-9]/",$stClassificacao);
        $inNivel            = $rsReceita->getCampo('nivel');

        if ($inNivel==1) {
            switch ((int) $arClassificacao[0]) {
                case 1: //Receita Corrente
                    $arNivel1ReceitaCorrente['DescricaoReceita'] = $rsReceita->getCampo('descricao');
                case 9: //
                    $arNivel1ReceitaCorrente['ValorReceita']        = $arNivel1ReceitaCorrente['ValorReceita'] + $rsReceita->getCampo('valor');
                    $arNivel2ReceitaCorrenteTmp['ValorReceita']     = $rsReceita->getCampo('valor');
                    $arNivel2ReceitaCorrenteTmp['DescricaoReceita'] = $rsReceita->getCampo('descricao');
                    $arNivel2ReceitaCorrente[] = $arNivel2ReceitaCorrenteTmp;
                break;
                case 2: //Receitas de Capital
                    $arNivel1ReceitaCapital['ValorReceita']     = $rsReceita->getCampo('valor');
                    $arNivel1ReceitaCapital['DescricaoReceita'] = $rsReceita->getCampo('descricao');
                break;
                case 7: //
                    $arNivel1ReceitaCorrente['ValorReceita']        = $arNivel1ReceitaCorrente['ValorReceita'] + $rsReceita->getCampo('valor');
                    $arNivel2ReceitaCorrenteTmp['ValorReceita']     = $rsReceita->getCampo('valor');
                    $arNivel2ReceitaCorrenteTmp['DescricaoReceita'] = $rsReceita->getCampo('descricao');
                    $arNivel2ReceitaCorrente[] = $arNivel2ReceitaCorrenteTmp;
            }
        } elseif ( $inNivel==2 && $rsReceita->getCampo('valor') ) {
            switch ((int) $arClassificacao[0]) {
                case 1: //Receita Corrente
                case 9: //
                    $arNivel2ReceitaCorrenteTmp['ValorReceita']     = $rsReceita->getCampo('valor');
                    $arNivel2ReceitaCorrenteTmp['DescricaoReceita'] = $rsReceita->getCampo('descricao');
                    $arNivel2ReceitaCorrente[] = $arNivel2ReceitaCorrenteTmp;
                break;
                case 2: //Receitas de Capital
                    $arNivel2ReceitaCapitalTmp['ValorReceita']     = $rsReceita->getCampo('valor');
                    $arNivel2ReceitaCapitalTmp['DescricaoReceita'] = $rsReceita->getCampo('descricao');
                    $arNivel2ReceitaCapital[] = $arNivel2ReceitaCapitalTmp;
                break;
                case 7: //Receitas de Capital
                    $arNivel2ReceitaCapitalTmp['ValorReceita']     = $rsReceita->getCampo('valor');
                    $arNivel2ReceitaCapitalTmp['DescricaoReceita'] = $rsReceita->getCampo('descricao');
                    $arNivel2ReceitaCapital[] = $arNivel2ReceitaCapitalTmp;
                break;
            }
        }
        $rsReceita->proximo();
    }
    //Despesa
    while ( !$rsDespesa->eof() ) {
        $stClassificacao    = $rsDespesa->getCampo('classificacao');
        $arClassificacao    = preg_split("/[^a-zA-Z0-9]/",$stClassificacao);
        $inNivel            = $rsDespesa->getCampo('nivel');
        if (((int) $rsDespesa->getCampo('conta')==$rsDespesa->getCampo('nivel')) && ((int) $arClassificacao[0]==9)) {
            $nuReservaContingencia = $nuReservaContingencia + $rsDespesa->getCampo('valor');
        }
        if ($inNivel==1) {
            switch ((int) $arClassificacao[0]) {
                case 3: //Despesa Corrente
                    $arNivel1DespesaCorrente['ValorDespesa']     = $rsDespesa->getCampo('valor');
                    $arNivel1DespesaCorrente['DescricaoDespesa'] = $rsDespesa->getCampo('descricao');
                break;
                case 4: //Despesas de Capital
                    $arNivel1DespesaCapital['ValorDespesa']     = $rsDespesa->getCampo('valor');
                    $arNivel1DespesaCapital['DescricaoDespesa'] = $rsDespesa->getCampo('descricao');
                break;
                case 7: //Reserva Legal do RPPS
                    $nuReservaLegal = $nuReservaLegal + $rsDespesa->getCampo('valor');
                break;

            }
        } elseif ( $inNivel==2 && $rsDespesa->getCampo('valor') ) {
            switch ((int) $arClassificacao[0]) {
                case 3: //Despesa Corrente
                    $arNivel2DespesaCorrenteTmp['ValorDespesa']     = $rsDespesa->getCampo('valor');
                    $arNivel2DespesaCorrenteTmp['DescricaoDespesa'] = $rsDespesa->getCampo('descricao');
                    $arNivel2DespesaCorrente[] = $arNivel2DespesaCorrenteTmp;
                break;
                case 4: //Despesas de Capital
                    $arNivel2DespesaCapitalTmp['ValorDespesa']     = $rsDespesa->getCampo('valor');
                    $arNivel2DespesaCapitalTmp['DescricaoDespesa'] = $rsDespesa->getCampo('descricao');
                    $arNivel2DespesaCapital[] = $arNivel2DespesaCapitalTmp;
                break;
            }
        }
        $rsDespesa->proximo();
    }

    //Corrente
    $inCountReceita = count($arNivel2ReceitaCorrente);
    $inCountDespesa = count($arNivel2DespesaCorrente);
    $inCountE       = ($inCountReceita > $inCountDespesa) ? $inCountReceita : $inCountDespesa;
    for ($inCount=0; $inCount<$inCountE; $inCount++) {
        $arCorrente[$inCount] = array(   'DescricaoReceita' => $arNivel2ReceitaCorrente[$inCount]['DescricaoReceita']
                                        ,'ValorReceita'     => $arNivel2ReceitaCorrente[$inCount]['ValorReceita']
                                        ,'DescricaoDespesa' => $arNivel2DespesaCorrente[$inCount]['DescricaoDespesa']
                                        ,'ValorDespesa'     => $arNivel2DespesaCorrente[$inCount]['ValorDespesa'] );
    }
    if ($arNivel1ReceitaCorrente['ValorReceita'] > $arNivel1DespesaCorrente['ValorDespesa']) {
        $stSuperavitCorrente = 'SUPERAVIT CORRENTE';
        $nuSuperavitCorrente = $arNivel1ReceitaCorrente['ValorReceita'] - $arNivel1DespesaCorrente['ValorDespesa'];
        $nuTotCorrente       = $arNivel1ReceitaCorrente['ValorReceita'];
    } elseif ($arNivel1ReceitaCorrente['ValorReceita'] < $arNivel1DespesaCorrente['ValorDespesa']) {
        $stDeficitCorrente = 'DEFICIT CORRENTE';
        $nuDeficitCorrente = $arNivel1ReceitaCorrente['ValorReceita'] - $arNivel1DespesaCorrente['ValorDespesa'];
        $nuTotCorrente     = $arNivel1DespesaCorrente['ValorDespesa'];
    }

    $inCountReceita = count($arNivel2ReceitaCapital);
    $inCountDespesa = count($arNivel2DespesaCapital);
    $inCountE       = ($inCountReceita > $inCountDespesa) ? $inCountReceita : $inCountDespesa;
    for ($inCount=0; $inCount<$inCountE; $inCount++) {
        $arCapital[$inCount] = array(    'DescricaoReceita' => $arNivel2ReceitaCapital[$inCount]['DescricaoReceita']
                                        ,'ValorReceita'     => $arNivel2ReceitaCapital[$inCount]['ValorReceita']
                                        ,'DescricaoDespesa' => $arNivel2DespesaCapital[$inCount]['DescricaoDespesa']
                                        ,'ValorDespesa'     => $arNivel2DespesaCapital[$inCount]['ValorDespesa'] );
    }
    if ($arNivel1ReceitaCapital['ValorReceita'] > $arNivel1DespesaCapital['ValorDespesa']) {
        $stSuperavirCapital = 'SUPERAVIT CAPITAL';
        $nuSuperavitCapital = $arNivel1ReceitaCapital['ValorReceita'] - $arNivel1DespesaCapital['ValorDespesa'];
        $nuTotCapital       = $arNivel1ReceitaCapital['ValorReceita'];
    } elseif ($arNivel1ReceitaCapital['ValorReceita'] < $arNivel1DespesaCapital['ValorDespesa']) {
        $stDeficitCapital = 'DEFICIT CAPITAL';
        $nuDeficitCapital = $arNivel1ReceitaCapital['ValorReceita'] - $arNivel1DespesaCapital['ValorDespesa'];
        if ($nuDeficitCapital < 0) {
            $nuDeficitCapital = $nuDeficitCapital * -1;
        }
        $nuTotCapital     = $arNivel1DespesaCapital['ValorDespesa'];
    }

    $rsNivel1Corrente = new RecordSet;
    $rsNivel1Corrente->preenche( array( array_merge( $arNivel1ReceitaCorrente, $arNivel1DespesaCorrente ) ) );
    
    $arCorrente[] = array(
        'DescricaoReceita' => $stDeficitCorrente, 
        'ValorReceita' => $nuDeficitCorrente, 
        'DescricaoDespesa' => $stSuperavitCorrente, 
        'ValorDespesa' => $nuSuperavitCorrente
    );

    $arCorrente[] = array(
        'DescricaoReceita' => 'TOTAIS', 
        'ValorReceita' => $nuTotCorrente, 
        'DescricaoDespesa' => 'TOTAIS', 
        'ValorDespesa' => $nuTotCorrente
    );

    $rsNivel2Corrente = new RecordSet;
    $rsNivel2Corrente->preenche( $arCorrente );

    $rsNivel1Capital = new RecordSet;
    $rsNivel1Capital->preenche( array( array_merge( $arNivel1ReceitaCapital, $arNivel1DespesaCapital ) ) );

    $arCapital[] = array(
        'DescricaoReceita' => $stDeficitCapital, 
        'ValorReceita' => $nuDeficitCapital, 
        'DescricaoDespesa' => $stSuperavitCapital, 
        'ValorDespesa' => $nuSuperavitCapital
    );

    $arCapital[] = array(
        'DescricaoReceita' => 'TOTAIS', 
        'ValorReceita' => $nuTotCapital, 
        'DescricaoDespesa' => 'TOTAIS', 
        'ValorDespesa' => $nuTotCapital
    );

    $rsNivel2Capital = new RecordSet;
    $rsNivel2Capital->preenche( $arCapital );

    $arResumo[0]['RESUMO']   = 'RECEITAS E DESPESAS CORRENTES';
    $arResumo[0]['RECEITAS'] = $arNivel1ReceitaCorrente['ValorReceita'];
    $arResumo[0]['DESPESAS'] = $arNivel1DespesaCorrente['ValorDespesa'];

    $arResumo[1]['RESUMO']   = 'RECEITAS E DESPESAS DE CAPITAL';
    $arResumo[1]['RECEITAS'] = $arNivel1ReceitaCapital['ValorReceita'];
    $arResumo[1]['DESPESAS'] = $arNivel1DespesaCapital['ValorDespesa'];

    $arResumo[2]['RESUMO']   = 'RESERVA LEGAL DO RPPS';
    $arResumo[2]['RECEITAS'] = '';
    $arResumo[2]['DESPESAS'] = $nuReservaLegal;

    $arResumo[3]['RESUMO']   = 'RESERVA DE CONTINGÊNCIA';
    $arResumo[3]['RECEITAS'] = '';
    $arResumo[3]['DESPESAS'] = $nuReservaContingencia;

    $arResumo[4]['RESUMO']   = '';
    $arResumo[4]['RECEITAS'] = '';
    $arResumo[4]['DESPESAS'] = '';

    $arResumo[5]['RESUMO']   = 'TOTAL';
    $arResumo[5]['RECEITAS'] = $arNivel1ReceitaCorrente['ValorReceita'] + $arNivel1ReceitaCapital['ValorReceita'] ;
    $arResumo[5]['DESPESAS'] = $arNivel1DespesaCorrente['ValorDespesa'] + $arNivel1DespesaCapital['ValorDespesa'] + $nuReservaContingencia + $nuReservaLegal;

    $arResumo[6]['RESUMO']   = '';
    $arResumo[6]['RECEITAS'] = '';
    $arResumo[6]['DESPESAS'] = '';

    $rsResumo = new RecordSet;
    $rsResumo->preenche($arResumo);

    $arRecordSets = array(   $rsNivel1Corrente
                            ,$rsNivel2Corrente

                            ,$rsNivel1Capital
                            ,$rsNivel2Capital

                            ,$rsResumo
                            );

    return $obErro;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSetBalanco(&$arRecordSets , $stOrder = "")
{
    $nuSuperavitCapital = $nuDeficitCapital = $nuTotCorrente = $nuSuperavitCorrente = $nuDeficitCorrente = '';
    $stSuperavitCapital = $stDeficitCapital = $stSuperavitCorrente = $stDeficitCorrente = '';

    $this->obFOrcamentoAnexo1Receita->setDado('exercicio'    , $this->getExercicio()     );
    $this->obFOrcamentoAnexo1Receita->setDado('inCodEntidade', $this->getCodEntidade()   );
    $this->obFOrcamentoAnexo1Receita->setDado('stFiltro'     , $this->getFiltro()        );
    $this->obFOrcamentoAnexo1Receita->setDado('stDataInicial', $this->obROrcamentoDespesa->obTPeriodo->getDataInicial() );
    $this->obFOrcamentoAnexo1Receita->setDado('stDataFinal'  , $this->obROrcamentoDespesa->obTPeriodo->getDataFinal() );
    $obErro = $this->obFOrcamentoAnexo1Receita->recuperaTodosBalanco( $rsReceita );

    $this->obFOrcamentoAnexo1Despesa->setDado('exercicio'    , $this->getExercicio()     );
    $this->obFOrcamentoAnexo1Despesa->setDado('inCodEntidade', $this->getCodEntidade()   );
    $this->obFOrcamentoAnexo1Despesa->setDado('stFiltro'     , $this->getFiltro()        );
    $this->obFOrcamentoAnexo1Despesa->setDado('inNumOrgao'   , $this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() );
    $this->obFOrcamentoAnexo1Despesa->setDado('inNumUnidade' , $this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade() );
    $this->obFOrcamentoAnexo1Despesa->setDado('inCodDemDespesa', $this->getCodDemDespesa());
    $this->obFOrcamentoAnexo1Despesa->setDado('stDataInicial', $this->obROrcamentoDespesa->obTPeriodo->getDataInicial() );
    $this->obFOrcamentoAnexo1Despesa->setDado('stDataFinal'  , $this->obROrcamentoDespesa->obTPeriodo->getDataFinal() );
    $obErro = $this->obFOrcamentoAnexo1Despesa->recuperaTodosBalanco( $rsDespesa );

    //Receita
    while ( !$rsReceita->eof() ) {
        $stClassificacao    = $rsReceita->getCampo('classificacao');
        $arClassificacao    = preg_split("/[^a-zA-Z0-9]/",$stClassificacao);
        $inNivel            = $rsReceita->getCampo('nivel');
        if ($inNivel==1) {
            switch ((int) $arClassificacao[0]) {
                case 1: //Receita Corrente
                    $arNivel1ReceitaCorrente['DescricaoReceita'] = $rsReceita->getCampo('descricao');
                    $arNivel1ReceitaCorrente['ValorReceita']     = $rsReceita->getCampo('valor');
                break;
                case 9: //
                    $arNivel1ReceitaCorrente['ValorReceita']        = $arNivel1ReceitaCorrente['ValorReceita'] + $rsReceita->getCampo('valor');
                    $arNivel2ReceitaCorrenteTmp['ValorReceita']     = $rsReceita->getCampo('valor');
                    $arNivel2ReceitaCorrenteTmp['DescricaoReceita'] = $rsReceita->getCampo('descricao');
                    $arNivel2ReceitaCorrente[] = $arNivel2ReceitaCorrenteTmp;
                break;
                case 7: //
                    $arNivel1ReceitaCorrente['ValorReceita']        = $arNivel1ReceitaCorrente['ValorReceita'] + $rsReceita->getCampo('valor');
                    $arNivel2ReceitaCorrenteTmp['ValorReceita']     = $rsReceita->getCampo('valor');
                    $arNivel2ReceitaCorrenteTmp['DescricaoReceita'] = $rsReceita->getCampo('descricao');
                    $arNivel2ReceitaCorrente[] = $arNivel2ReceitaCorrenteTmp;
                break;
                case 2: //Receitas de Capital
                    $arNivel1ReceitaCapital['ValorReceita']     = $rsReceita->getCampo('valor');
                    $arNivel1ReceitaCapital['DescricaoReceita'] = $rsReceita->getCampo('descricao');
                break;
            }
        } elseif ( $inNivel==2 && $rsReceita->getCampo('valor') ) {
            switch ((int) $arClassificacao[0]) {
                case 1: //Receita Corrente
                //case 9: //
                    $arNivel2ReceitaCorrenteTmp['ValorReceita']     = $rsReceita->getCampo('valor');
                    $arNivel2ReceitaCorrenteTmp['DescricaoReceita'] = $rsReceita->getCampo('descricao');
                    $arNivel2ReceitaCorrente[] = $arNivel2ReceitaCorrenteTmp;
                break;
                case 2: //Receitas de Capital
                    $arNivel2ReceitaCapitalTmp['ValorReceita']     = $rsReceita->getCampo('valor');
                    $arNivel2ReceitaCapitalTmp['DescricaoReceita'] = $rsReceita->getCampo('descricao');
                    $arNivel2ReceitaCapital[] = $arNivel2ReceitaCapitalTmp;
                break;
            }
        }
        $rsReceita->proximo();
    }

    //Despesa
    while ( !$rsDespesa->eof() ) {
        $stClassificacao    = $rsDespesa->getCampo('classificacao');
        $arClassificacao    = preg_split("/[^a-zA-Z0-9]/",$stClassificacao);
        $inNivel            = $rsDespesa->getCampo('nivel');
        if (((int) $rsDespesa->getCampo('conta')==$rsDespesa->getCampo('nivel')) && ((int) $arClassificacao[0]==9)) {
            $nuReservaContingencia = $nuReservaContingencia + $rsDespesa->getCampo('valor');
        }
        if ($inNivel==1) {
            switch ((int) $arClassificacao[0]) {
                case 3: //Despesa Corrente
                    $arNivel1DespesaCorrente['ValorDespesa']     = $rsDespesa->getCampo('valor');
                    $arNivel1DespesaCorrente['DescricaoDespesa'] = $rsDespesa->getCampo('descricao');
                break;
                case 4: //Despesas de Capital
                    $arNivel1DespesaCapital['ValorDespesa']     = $rsDespesa->getCampo('valor');
                    $arNivel1DespesaCapital['DescricaoDespesa'] = $rsDespesa->getCampo('descricao');
                break;
            }
        } elseif ( $inNivel==2 && $rsDespesa->getCampo('valor') ) {
            switch ((int) $arClassificacao[0]) {
                case 3: //Despesa Corrente
                    $arNivel2DespesaCorrenteTmp['ValorDespesa']     = $rsDespesa->getCampo('valor');
                    $arNivel2DespesaCorrenteTmp['DescricaoDespesa'] = $rsDespesa->getCampo('descricao');
                    $arNivel2DespesaCorrente[] = $arNivel2DespesaCorrenteTmp;
                break;
                case 4: //Despesas de Capital
                    $arNivel2DespesaCapitalTmp['ValorDespesa']     = $rsDespesa->getCampo('valor');
                    $arNivel2DespesaCapitalTmp['DescricaoDespesa'] = $rsDespesa->getCampo('descricao');
                    $arNivel2DespesaCapital[] = $arNivel2DespesaCapitalTmp;
                break;
            }
        }
        $rsDespesa->proximo();
    }

    //Corrente
    $inCountReceita = count($arNivel2ReceitaCorrente);
    $inCountDespesa = count($arNivel2DespesaCorrente);
    $inCountE       = ($inCountReceita > $inCountDespesa) ? $inCountReceita : $inCountDespesa;
    for ($inCount=0; $inCount<$inCountE; $inCount++) {
        $arCorrente[$inCount] = array(   'DescricaoReceita' => $arNivel2ReceitaCorrente[$inCount]['DescricaoReceita']
                                        ,'ValorReceita'     => $arNivel2ReceitaCorrente[$inCount]['ValorReceita']
                                        ,'DescricaoDespesa' => $arNivel2DespesaCorrente[$inCount]['DescricaoDespesa']
                                        ,'ValorDespesa'     => $arNivel2DespesaCorrente[$inCount]['ValorDespesa'] );
    }
    if ($arNivel1ReceitaCorrente['ValorReceita'] > $arNivel1DespesaCorrente['ValorDespesa']) {
        $stSuperavitCorrente = 'SUPERAVIT CORRENTE';
        $nuSuperavitCorrente = $arNivel1ReceitaCorrente['ValorReceita'] - $arNivel1DespesaCorrente['ValorDespesa'];
        $nuTotCorrente       = $arNivel1ReceitaCorrente['ValorReceita'];
    } elseif ($arNivel1ReceitaCorrente['ValorReceita'] < $arNivel1DespesaCorrente['ValorDespesa']) {
        $stDeficitCorrente = 'DEFICIT CORRENTE';
        $nuDeficitCorrente = $arNivel1ReceitaCorrente['ValorReceita'] - $arNivel1DespesaCorrente['ValorDespesa'];
        $nuTotCorrente     = $arNivel1DespesaCorrente['ValorDespesa'];
    }

    $inCountReceita = count($arNivel2ReceitaCapital);
    $inCountDespesa = count($arNivel2DespesaCapital);
    $inCountE       = ($inCountReceita > $inCountDespesa) ? $inCountReceita : $inCountDespesa;
    for ($inCount=0; $inCount<$inCountE; $inCount++) {
        $arCapital[$inCount] = array(    'DescricaoReceita' => $arNivel2ReceitaCapital[$inCount]['DescricaoReceita']
                                        ,'ValorReceita'     => $arNivel2ReceitaCapital[$inCount]['ValorReceita']
                                        ,'DescricaoDespesa' => $arNivel2DespesaCapital[$inCount]['DescricaoDespesa']
                                        ,'ValorDespesa'     => $arNivel2DespesaCapital[$inCount]['ValorDespesa'] );
    }
    if ($arNivel1ReceitaCapital['ValorReceita'] > $arNivel1DespesaCapital['ValorDespesa']) {
        $stSuperavirCapital = 'SUPERAVIT CAPITAL';
        $nuSuperavitCapital = $arNivel1ReceitaCapital['ValorReceita'] - $arNivel1DespesaCapital['ValorDespesa'];
        $nuTotCapital       = $arNivel1ReceitaCapital['ValorReceita'];
    } elseif ($arNivel1ReceitaCapital['ValorReceita'] < $arNivel1DespesaCapital['ValorDespesa']) {
        $stDeficitCapital = 'DEFICIT CAPITAL';
        $nuDeficitCapital = $arNivel1ReceitaCapital['ValorReceita'] - $arNivel1DespesaCapital['ValorDespesa'];
        if ($nuDeficitCapital < 0) {
            $nuDeficitCapital = $nuDeficitCapital * -1;
        }
        $nuTotCapital     = $arNivel1DespesaCapital['ValorDespesa'];
    }

    $rsNivel1Corrente = new RecordSet;
    $rsNivel1Corrente->preenche( array( array_merge( $arNivel1ReceitaCorrente, $arNivel1DespesaCorrente ) ) );

    $arCorrente[] = array(
        'DescricaoReceita' => $stDeficitCorrente, 
        'ValorReceita' => $nuDeficitCorrente, 
        'DescricaoDespesa' => $stSuperavitCorrente, 
        'ValorDespesa' => $nuSuperavitCorrente
    );

    $arCorrente[] = array(
        'DescricaoReceita' => 'TOTAIS', 
        'ValorReceita' => $nuTotCorrente, 
        'DescricaoDespesa' => 'TOTAIS', 
        'ValorDespesa' => $nuTotCorrente
    );

    $rsNivel2Corrente = new RecordSet;
    $rsNivel2Corrente->preenche( $arCorrente );

    $rsNivel1Capital = new RecordSet;
    $rsNivel1Capital->preenche( array( array_merge( $arNivel1ReceitaCapital, $arNivel1DespesaCapital ) ) );

    $arCapital[] = array(
        'DescricaoReceita' => $stDeficitCapital, 
        'ValorReceita' => $nuDeficitCapital, 
        'DescricaoDespesa' => $stSuperavitCapital, 
        'ValorDespesa' => $nuSuperavitCapital
    );

    $arCapital[] = array(
        'DescricaoReceita' => 'TOTAIS', 
        'ValorReceita' => $nuTotCapital, 
        'DescricaoDespesa' => 'TOTAIS', 
        'ValorDespesa' => $nuTotCapital
    );

    $rsNivel2Capital = new RecordSet;
    $rsNivel2Capital->preenche( $arCapital );

    $arResumo[0]['RESUMO']   = 'RECEITAS E DESPESAS CORRENTES';
    $arResumo[0]['RECEITAS'] = $arNivel1ReceitaCorrente['ValorReceita'];
    $arResumo[0]['DESPESAS'] = $arNivel1DespesaCorrente['ValorDespesa'];

    $arResumo[1]['RESUMO']   = 'RECEITAS E DESPESAS DE CAPITAL';
    $arResumo[1]['RECEITAS'] = $arNivel1ReceitaCapital['ValorReceita'];
    $arResumo[1]['DESPESAS'] = $arNivel1DespesaCapital['ValorDespesa'];

    $arResumo[2]['RESUMO']   = 'RESERVA DE CONTINGÊNCIA';
    $arResumo[2]['RECEITAS'] = '';
    $arResumo[2]['DESPESAS'] = $nuReservaContingencia;

    $arResumo[3]['RESUMO']   = '';
    $arResumo[3]['RECEITAS'] = '';
    $arResumo[3]['DESPESAS'] = '';

    $arResumo[4]['RESUMO']   = 'TOTAL';
    $arResumo[4]['RECEITAS'] = $arNivel1ReceitaCorrente['ValorReceita'] + $arNivel1ReceitaCapital['ValorReceita'] ;
    $arResumo[4]['DESPESAS'] = $arNivel1DespesaCorrente['ValorDespesa'] + $arNivel1DespesaCapital['ValorDespesa'] + $nuReservaContingencia;

    if ($arResumo[4]['RECEITAS'] > $arResumo[4]['DESPESAS']) {
        $arResumo[5]['RESUMO']   = 'SUPERÁVIT';
        $arResumo[5]['RECEITAS'] = '';
        $arResumo[5]['DESPESAS'] = bcsub($arResumo[4]['RECEITAS'],$arResumo[4]['DESPESAS'],4);
    } elseif ($arResumo[4]['RECEITAS'] < $arResumo[4]['DESPESAS']) {
        $arResumo[5]['RESUMO']   = 'DÉFICIT';
        $arResumo[5]['RECEITAS'] = bcsub($arResumo[4]['DESPESAS'],$arResumo[4]['RECEITAS'],4);
        $arResumo[5]['DESPESAS'] = '';
    } else {
        $arResumo[5]['RESUMO']   = 'SUPERÁVIT';
        $arResumo[5]['RECEITAS'] = 0;
        $arResumo[5]['DESPESAS'] = 0;
    }

    $arResumo[6]['RESUMO']   = 'TOTAL';
    $arResumo[6]['RECEITAS'] = bcadd($arResumo[4]['RECEITAS'],$arResumo[5]['RECEITAS'],4);
    $arResumo[6]['DESPESAS'] = bcadd($arResumo[4]['DESPESAS'],$arResumo[5]['DESPESAS'],4);

    $rsResumo = new RecordSet;
    $rsResumo->preenche($arResumo);

    $arRecordSets = array(   $rsNivel1Corrente
                            ,$rsNivel2Corrente

                            ,$rsNivel1Capital
                            ,$rsNivel2Capital

                            ,$rsResumo
                            );

    return $obErro;
}
}
