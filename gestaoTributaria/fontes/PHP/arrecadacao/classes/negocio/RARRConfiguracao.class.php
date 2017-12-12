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
    * Classe de Regra de Negócio ConfiguracaO ARRECADAÇÃO
    * Data de Criação   : 11/05/2005

    * @author Analista : Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * $Id: RARRConfiguracao.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

/*
$Log$
Revision 1.25  2007/09/25 14:49:01  vitor
Ticket#10246#

Revision 1.24  2007/07/19 15:43:03  cercato
Bug #9687#

Revision 1.23  2007/02/16 11:41:28  dibueno
Bug #8432#

Revision 1.22  2007/02/16 10:11:17  dibueno
Inclusão de opção de Baixa Manual Única

Revision 1.21  2006/10/23 17:41:08  fabio
adicionado grupo de credito para escrituracao de receita

Revision 1.20  2006/09/15 11:50:14  fabio
corrigidas tags de caso de uso

Revision 1.19  2006/09/15 10:48:45  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO . "RConfiguracaoConfiguracao.class.php");

/**
    * Classe de Regra de Negócio Configuracao Arrecadação
    * Data de Criação   : 30/08/2004
    * @author Analista : Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
*/
class RARRConfiguracao extends RConfiguracaoConfiguracao
{
/**
    * @var Array
    * @access Private
*/
var $arSuperSimples;

/**
    * @var RecordSet
    * @access Private
*/
var $rsRSSuperSimples;

/**
    * @var Float
    * @access Private
*/
var $flMinimoLancamentoAutomatico;

/**
    * @var Object
    * @access Private
*/
var $obTConfiguracao;
/**
    * @var Float
    * @access Private
*/
var $flValorMaximo;
/**
    * @var Boolean
    * @access Private
*/
var $botipoValor;
/**
    * @var Integer
    * @access Private
*/
var $inCodigoModulo;
/**
    * @var Integer
    * @access Private
*/
var $inAnoExercicio;
/**
    * @var sTRING
    * @access Private
*/
var $stBaixaManual;
/**
    * @var sTRING
    * @access Private
*/
var $stBaixaManualUnica;
/**
    * @var String
    * @access Private
*/
var $stFormaVerificacao;
/**
    * @var String
    * @access Private
*/
var $stValTransfImoveis;
/**
    * @var String
    * @access Private
*/
var $inCodFebraban;

/**
    * @var String
    * @access Private
*/
var $stSuspensao;

/**
    * @var Integer
    * @access Private
*/
var $inCodGrupoCreditoITBI;

/**
    * @var Integer
    * @access Private
*/
var $inCodGrupoCreditoIPTU;

/**
    * @var Integer
    * @access Private
*/
var $inCodGrupoCreditoEscrituracao;
/**
    * @var Integer
    * @access Private
*/
var $inCodGrupoNotaAvulsa;
/**
    * @var Integer
    * @access Private
*/
var $inMinimoLancamentoAutomatico;

/**
    * @var Integer
    * @access Private
*/
var $inGrupoDiferencaGeral;

/**
    * @var Integer
    * @access Private
*/
var $inGrupoDiferencaImob;

/**
    * @var Integer
    * @access Private
*/
var $inGrupoDiferencaEcon;

/**
    * @var Integer
    * @access Private
*/
var $inGrupoDiferencaAcrescimoGeral;

/**
    * @var Integer
    * @access Private
*/
var $inGrupoDiferencaAcrescimoImob;

/**
    * @var Integer
    * @access Private
*/
var $inGrupoDiferencaAcrescimoEcon;

/**
    * @var String
    * @access Private
*/
var $stEmissaoCarne;
/**
    * @var String
    * @access Private
*/
var $stEmissaoCarneIsento;
/**
    * @access Public
    * @param Integer $valor
*/
var $stFundLegal;
var $stCarneSecretaria;
var $stCarneDepartamento;
var $stCarneDam;
var $stNotaAvulsa;
var $inQtdViasNotaAvulsa;
var $inQtdMesDAVencida;

function setBaixaManualDAVencida($valor) { $this->inQtdMesDAVencida = $valor; }
function getBaixaManualDAVencida() { return $this->inQtdMesDAVencida; }
function setQtdViasNotaAvulsa($valor) { $this->inQtdViasNotaAvulsa = $valor; }
function getQtdViasNotaAvulsa() { return $this->inQtdViasNotaAvulsa; }
function setNotaAvulsa($valor) { $this->stNotaAvulsa = $valor; }
function getNotaAvulsa() { return $this->stNotaAvulsa; }
function setCodigoGrupoDiferencaAcrescimoEcon($valor) { $this->inGrupoDiferencaAcrescimoEcon = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setFundLegal($valor) { $this->stFundLegal = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoGrupoDiferencaAcrescimoImob($valor) { $this->inGrupoDiferencaAcrescimoImob = $valor; }

/**
    * @access Public
    * @param RecordSet $valor
*/
function setRSSuperSimples($valor) { $this->rsRSSuperSimples = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoGrupoDiferencaAcrescimoGeral($valor) { $this->inGrupoDiferencaAcrescimoGeral = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoGrupoDiferencaEcon($valor) { $this->inGrupoDiferencaEcon = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoGrupoDiferencaImob($valor) { $this->inGrupoDiferencaImob = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoGrupoDiferencaGeral($valor) { $this->inGrupoDiferencaGeral = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoGrupoCreditoITBI($valor) { $this->inCodGrupoCreditoITBI = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoGrupoCreditoIPTU($valor) { $this->inCodGrupoCreditoIPTU = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoGrupoCreditoEscrituracao($valor) { $this->inCodGrupoCreditoEscrituracao = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoGrupoNotaAvulsa($valor) { $this->inCodGrupoNotaAvulsa = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setSuspensao($valor) { $this->stSuspensao  = $valor; }

/**
    * @access Public
    * @param Object $valor
*/
function setTConfiguracao($valor) { $this->obTConfiguracao  = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setValorMaximo($valor) { $this->flValorMaximo        = $valor  ; }
/**
    * @access Public
    * @param String $valor
*/
function setBaixaManual($valor) { $this->stBaixaManual        = $valor  ; }
/**
    * @access Public
    * @param String $valor
*/
function setBaixaManualUnica($valor) { $this->stBaixaManualUnica = $valor  ; }
/**
    * @access Public
    * @param String $valor
*/
function setFormaVerificacao($valor) { $this->stFormaVerificacao   = $valor  ; }
/**
    * @access Public
    * @param String $valor
*/
function setValTransfImovel($valor) { $this->stValTransfImovel    = $valor  ; }
/**
    * @access Public
    * @param String $valor
*/
function setCodFebraban($valor) { $this->inCodFebraban        = $valor  ; }
/**
    * @access Public
    * @param  Integer $valor
*/
function setConvenioParcelamento($valor) { $this->inCodConvenioParcelamento  = $valor   ; }
/**
    * @access Public
    * @param Object $valor
*/
function setAnoExercicio($valor) { $this->inAnoExercicio   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setMinimoLancamentoAutomatico($valor) { $this->inMinimoLancamentoAutomatico = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setEmissaoCarne($valor) { $this->stEmissaoCarne  = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setEmissaoCarneIsento($valor) { $this->stEmissaoCarneIsento  = $valor; }

function setCarneSecretaria($valor) { $this->stCarneSecretaria = $valor; }
function setCarneDepartamento($valor) { $this->stCarneDepartamento = $valor; }
function setCarneDam($valor) { $this->stCarneDam = $valor; }

// GETTERES
/**
    * @access Public
    * @return RecordSet
*/
function getRSSuperSimples() { return $this->rsRSSuperSimples; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoGrupoDiferencaAcrescimoEcon() { return $this->inGrupoDiferencaAcrescimoEcon; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoGrupoDiferencaAcrescimoImob() { return $this->inGrupoDiferencaAcrescimoImob; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoGrupoDiferencaAcrescimoGeral() { return $this->inGrupoDiferencaAcrescimoGeral; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoGrupoDiferencaEcon() { return $this->inGrupoDiferencaEcon; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoGrupoDiferencaImob() { return $this->inGrupoDiferencaImob; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoGrupoDiferencaGeral() { return $this->inGrupoDiferencaGeral; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoGrupoCreditoITBI() { return $this->inCodGrupoCreditoITBI; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoGrupoCreditoIPTU() { return $this->inCodGrupoCreditoIPTU; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoGrupoCreditoEscrituracao() { return $this->inCodGrupoCreditoEscrituracao; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoGrupoNotaAvulsa() { return $this->inCodGrupoNotaAvulsa; }

/**
    * @access Public
    * @return String
*/
function getSuspensao() { return $this->stSuspensao; }

/**
    * @access Public
    * @return Object
*/
function getTConfiguracao() { return $this->obTConfiguracao       ; }
/**
    * @access Public
    * @return Integer
*/
function getValorMaximo() { return $this->flValorMaximo         ; }
/**
    * @access Public
    * @return String
*/
function getBaixaManual() { return $this->stBaixaManual         ; }
/**
    * @access Public
    * @return String
*/
function getBaixaManualUnica() { return $this->stBaixaManualUnica    ; }
/**
    * @access Public
    * @return String
*/
function getFormaVerificacao() { return $this->stFormaVerificacao    ; }
/**
    * @access Public
    * @return STRING
*/
function getValTransfImovel() { return $this->stValTransfImovel     ; }
/**
    * @access Public
    * @return STRING
*/
function getCodFebraban() { return $this->inCodFebraban         ; }
/**
    * @access Public
    * @return Integer
*/
function getConvenioParcelamento() { return $this->inCodConvenioParcelamento; }
/**
    * @access Public
    * @return Object
*/
function getAnoExercicio() { return $this->inAnoExercicio;   }
/**
    * @access Public
    * @return Object
*/
function getMinimoLancamentoAutomatico() { return $this->inMinimoLancamentoAutomatico; }

/**
    * @access Public
    * @return String
*/
function getEmissaoCarne() { return $this->stEmissaoCarne; }

/**
    * @access Public
    * @return String
*/
function getEmissaoCarneIsento() { return $this->stEmissaoCarneIsento; }

function getCarneSecretaria() { return $this->stCarneSecretaria; }
function getCarneDepartamento() { return $this->stCarneDepartamento; }
function getCarneDam() { return $this->stCarneDam; }
/**
    * @access Public
    * @return String
*/
function getFundLegal() { return $this->stFundLegal    ; }

/**
    * Método Construtor
    * @access Private
*/
function RARRConfiguracao()
{
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php" );
    include_once( CLA_TRANSACAO );
    parent::RConfiguracaoConfiguracao();
    $this->obTConfiguracao  = new TAdministracaoConfiguracao;
    $this->obTransacao      = new Transacao;
    $this->setCodModulo     ( 25 );
    $this->rsRSSuperSimples = new RecordSet;
}

function salvar($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        /* baixa manual */
        $this->setParametro ("baixa_manual");
        $this->setValor     ( $this->stBaixaManual );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        /* baixa manual Unica */
        $this->setParametro ("baixa_manual_unica");
        $this->setValor     ( $this->stBaixaManualUnica );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        /* baixa manual DA vencida */
        $this->setParametro ("baixa_manual_divida_vencida");
        $this->setValor     ( $this->inQtdMesDAVencida );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        /*valor_maximo*/
        $this->setParametro ("valor_maximo");
        $this->setValor     ( $this->flValorMaximo );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }
    /*valor minimo para lancamentos automaticos*/
        $this->setParametro ("minimo_lancamento_automatico");
        $this->setValor     ( $this->flMinimoLancamentoAutomatico );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }
    /*tipo_verificação*/
        $this->setParametro ("tipo_avaliacao");
        $this->setValor     ( $this->stFormaVerificacao );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

    /*tipo_valor
        $this->setParametro ("tipo_valor");
        $this->setValor     ( $this->stValTransfImovel );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }
        */

        //Convenio Parcelamento
        $this->setParametro ("convenio_parcelamento");
        $this->setValor     ( $this->inCodConvenioParcelamento );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

    //Ativar Suspensao
        $this->setParametro ("ativar_suspensao");
        $this->setValor     ( $this->stSuspensao );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        //Emitir Carne com ou sem CPF
        $this->setParametro ("emissao_cpf");
        $this->setValor     ( $this->stEmissaoCarne );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        //emitir carnê isento
        $this->setParametro ( "emitir_carne_isento" );
        $this->setValor     ( $this->stEmissaoCarneIsento );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }
        
        //Fundamentacao LEgal
        $this->setParametro ( "fundamentacao_legal" );
        $this->setValor     ( $this->stFundLegal );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }
        
        //valor minimo para fazer lancamento
        $this->setParametro ("minimo_lancamento_automatico");
        $this->setValor     ( $this->inMinimoLancamentoAutomatico );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        //carne secretaria
        $this->setParametro ( "carne_secretaria" );
        $this->setValor     ( $this->stCarneSecretaria );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        //carne departamento
        $this->setParametro ( "carne_departamento" );
        $this->setValor     ( $this->stCarneDepartamento );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        //carne Dam
        $this->setParametro ( "carne_dam" );
        $this->setValor     ( $this->stCarneDam );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        //nota avulsa
        $this->setParametro ( "nota_avulsa" );
        $this->setValor     ( $this->stNotaAvulsa );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        //qtd vias nota avulsa
        $this->setParametro ( "vias_nota_avulsa" );
        $this->setValor     ( $this->inQtdViasNotaAvulsa );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        //grupo_nota_avulsa
        $this->setParametro ("grupo_nota_avulsa");
        $this->setValor     ( $this->inCodGrupoNotaAvulsa );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        //codigo FEBRABAN
        $this->setCodModulo( 2 );
        $this->setParametro ("FEBRABAN");
        $this->setValor     ( $this->getCodFebraban() );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

    return $obErro;
}

function montaSuperSimples()
{
    $stSuperSimples = "{";
    foreach ($this->arSuperSimples as $stOrdEntrega) {
        $stSuperSimples .= $stOrdEntrega.",";
    }

    $stSuperSimples = substr( $stSuperSimples , 0, strlen($stSuperSimples) - 1 );

    return $stSuperSimples."}";
}

function addSuperSimples($stOrdem)
{
    $arOrdemEntrega = explode ( "/", $stOrdem );
    $this->arSuperSimples[] = '{'.trim( $arOrdemEntrega[0] ).', "'.trim( $arOrdemEntrega[1] ).'"}';
}

function montaRSSuperSimples($stSuperSimples)
{
    $stSuperSimples = preg_replace( "/[{}\"]/" , "", $stSuperSimples );
    $arSuperSimples = preg_split( "/,/" , $stSuperSimples );
    for ( $inCont = 0; $inCont < count( $arSuperSimples ); $inCont = $inCont + 2 ) {
        if (isset($arSuperSimples[$inCont]) && isset($arSuperSimples[$inCont + 1]) ) {
            $mtSuperSimples[] = array( "cod_grupo" => trim($arSuperSimples[$inCont]), "ano_exercicio" => trim($arSuperSimples[$inCont + 1]) );
        }
    }
    if (isset($mtSuperSimples)) {
        $this->rsRSSuperSimples->preenche($mtSuperSimples);
    }
}

function salvarGrupo($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //grupo_credito_itbi
        $this->setParametro ("grupo_credito_itbi");
        $this->setValor     ( $this->inCodGrupoCreditoITBI );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        //grupo de credito do IPTU
        $this->setParametro ("grupo_credito_iptu");
        $this->setValor     ( $this->getCodigoGrupoCreditoIPTU() );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        //escrituracao_receita
        $this->setParametro ("escrituracao_receita");
        $this->setValor     ( $this->inCodGrupoCreditoEscrituracao );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }
        //nota_avulsa
        $this->setParametro ("grupo_nota_avulsa");
        $this->setValor     ( $this->inCodGrupoNotaAvulsa );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        $this->setParametro ("grupo_diferenca_geral");
        $this->setValor     ( $this->getCodigoGrupoDiferencaGeral() );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        $this->setParametro ("grupo_diferenca_imob");
        $this->setValor     ( $this->getCodigoGrupoDiferencaImob() );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        $this->setParametro ("grupo_diferenca_econ");
        $this->setValor     ( $this->getCodigoGrupoDiferencaEcon() );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        $this->setParametro ("grupo_diferenca_acrescimo_geral");
        $this->setValor     ( $this->getCodigoGrupoDiferencaAcrescimoGeral() );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        $this->setParametro ("grupo_diferenca_acrescimo_imob");
        $this->setValor     ( $this->getCodigoGrupoDiferencaAcrescimoImob() );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        $this->setParametro ("grupo_diferenca_acrescimo_econ");
        $this->setValor     ( $this->getCodigoGrupoDiferencaAcrescimoEcon() );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }

        $this->setParametro ( "super_simples" );
        $this->setValor     ( $this->montaSuperSimples() );
        $this->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = parent::alterar( $boTransacao );
        } else {
            $obErro = parent::incluir( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

    return $obErro;
}
function consultarParamentro(&$stRetorno, $boTransacao = "" , $stParametro="")
{
    $this->setParametro ($stParametro);
    $obErro = parent::consultar ( $boTransacao );
    $stRetorno= $this->getValor();

    return $obErro;

}
function consultar($boTransacao = "")
{
    $this->setParametro ("baixa_manual");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setBaixaManual( $this->getValor() );

    $this->setParametro ("baixa_manual_divida_vencida");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setBaixaManualDAVencida( $this->getValor() );

    $this->setParametro ("valor_maximo");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setValorMaximo( $this->getValor() );

    $this->setParametro ("minimo_lancamento_automatico");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setMinimoLancamentoAutomatico( $this->getValor() );

    $this->setParametro ("tipo_avaliacao");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setFormaVerificacao( $this->getValor() );

/*
                $this->setParametro ("tipo_valor");
                $obErro = parent::consultar ( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->setValTransfImovel( $this->getValor() );
*/
                    //$this->setParametro ("cod_febraban");

    $this->setParametro ("FEBRABAN");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setCodFebraban( $this->getValor() );

    $this->setParametro ("convenio_parcelamento");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setConvenioParcelamento( $this->getValor() );

    $this->setParametro ("ativar_suspensao");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setSuspensao( $this->getValor() );

    $this->setParametro ("grupo_credito_itbi");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setCodigoGrupoCreditoITBI( $this->getValor() );

    $this->setParametro ("grupo_credito_iptu");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setCodigoGrupoCreditoIPTU( $this->getValor() );

    $this->setParametro ("escrituracao_receita");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setCodigoGrupoCreditoEscrituracao( $this->getValor() );

    $this->setParametro ("grupo_nota_avulsa");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setCodigoGrupoNotaAvulsa( $this->getValor() );

    $this->setParametro ("minimo_lancamento_automatico");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setMinimoLancamentoAutomatico( $this->getValor() );

    $this->setParametro ("grupo_diferenca_geral");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setCodigoGrupoDiferencaGeral( $this->getValor() );

    $this->setParametro ("grupo_diferenca_imob");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setCodigoGrupoDiferencaImob( $this->getValor() );

    $this->setParametro ("grupo_diferenca_econ");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setCodigoGrupoDiferencaEcon( $this->getValor() );

    $this->setParametro ("grupo_diferenca_acrescimo_geral");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setCodigoGrupoDiferencaAcrescimoGeral( $this->getValor() );

    $this->setParametro ("grupo_diferenca_acrescimo_imob");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setCodigoGrupoDiferencaAcrescimoImob( $this->getValor() );

    $this->setParametro ("grupo_diferenca_acrescimo_econ");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setCodigoGrupoDiferencaAcrescimoEcon( $this->getValor() );

    $this->setParametro ("baixa_manual_unica");
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setBaixaManualUnica( $this->getValor() );

    $this->setParametro( "super_simples" );
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->montaRSSuperSimples( $this->getValor() );

    $this->setParametro( "emissao_cpf" );
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setEmissaoCarne( $this->getValor() );

    $this->setParametro( "emitir_carne_isento" );
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setEmissaoCarneIsento( $this->getValor() );

    $this->setParametro( "fundamentacao_legal" );
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setFundLegal( $this->getValor() );
    
    $this->setNotaAvulsa( $this->getValor() );
    $this->setParametro( "carne_secretaria" );
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setCarneSecretaria( $this->getValor() );

    $this->setParametro( "carne_departamento" );
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setCarneDepartamento( $this->getValor() );

    $this->setParametro( "carne_dam" );
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setCarneDam( $this->getValor() );

    $this->setParametro( "nota_avulsa" );
    $obErro = parent::consultar ( $boTransacao );
    if( $obErro->ocorreu() )

        return $obErro;

    $this->setNotaAvulsa( $this->getValor() );

    $this->setParametro( "vias_nota_avulsa" );
    $obErro = parent::consultar ( $boTransacao );

    if( $obErro->ocorreu() )

        return $obErro;

    $this->setQtdViasNotaAvulsa( $this->getValor() );

    return $obErro;
}

/**
    * Recupera a Mascara de processo
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarMascaraProcesso(&$stMascaraProcesso , $boTransacao = "")
{
    $stFiltro  = " WHERE COD_MODULO = 5 AND parametro = 'mascara_processo' ";
    $stFiltro .= " AND  exercicio = '".$this->getAnoExercicio()."' ";
    $stOrdem   = " ORDER BY EXERCICIO DESC ";
    $obErro = $this->obTConfiguracao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $stMascaraProcesso = $rsRecordSet->getCampo( "valor" );
    }

    return $obErro;
}

/**
    * Recupera o valor maximo de limite do lançamento para cima ou para baixo
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarValorMaximo(&$nuValorMaximo , $boTransacao = "")
{
    $stFiltro  = " WHERE COD_MODULO = 25 AND parametro = 'valor_maximo' ";
    $stFiltro .= " AND  exercicio = '".$this->getAnoExercicio()."' ";
    $stOrdem   = " ORDER BY EXERCICIO DESC ";
    $obErro = $this->obTConfiguracao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $nuValorMaximo = $rsRecordSet->getCampo( "valor" );
    }

    return $obErro;
}

/**
    * Recupera o o Grupo de Credito para cadastro de Diferenca
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarGrupoCredito(&$inGrupoCredito , $boTransacao = "")
{
    $stFiltro  = " WHERE COD_MODULO = ".$this->getCodModulo()." AND parametro = '".$this->getParametro()."' ";
    $stFiltro .= " AND  exercicio = '".$this->getAnoExercicio()."' ";
    $stOrdem   = " ORDER BY EXERCICIO DESC ";
    $obErro = $this->obTConfiguracao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $inGrupoCredito = $rsRecordSet->getCampo( "valor" );
    }

    return $obErro;
}

function listaGrupoCredito($inCodModulo, $stParametro, $stValor = "", $boTransacao = "")
{
    $stFiltro  = " WHERE COD_MODULO = ".$inCodModulo." AND parametro = '".$stParametro."' ";
    if ( $stValor )
        $stFiltro .= " AND valor ='".$stValor."'";

    $stOrdem   = " ORDER BY EXERCICIO DESC ";
    $obErro = $this->obTConfiguracao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    if ( $rsRecordSet->Eof() )
        $boEncontrou = false;
    else
        $boEncontrou = true;

    return $boEncontrou;
}

} // fecha classe

?>
