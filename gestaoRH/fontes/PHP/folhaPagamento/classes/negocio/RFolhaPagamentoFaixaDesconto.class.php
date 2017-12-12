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
* Classe de regra de negócio para Pessoal-FaixaDesconto
* Data de Criação: 00/00/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Rafael Almeida

* @package URBEM
* @subpackage Regra

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

* Casos de uso: uc-04.05.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFaixaDesconto.class.php" );

class RFolhaPagamentoFaixaDesconto
{
/**
    * @access Private
    * @var Array
*/
var $arFaixa;
/**
    * @access Private
    * @var Integer
*/
var $inCodFaixa;
/**
    * @access Private
    * @var Integer
*/
var $inCodPrevidencia;

/**
    * @access Private
    * @var Float
*/
var $flSalarioInicial;
/**
    * @access Private
    * @var Float
*/
var $flSalarioFinal;
/**
    * @access Private
    * @var flPercentualDesc
*/
var $flPercentualDesc;
/**
    * @access Private
    * @var Object
*/
var $obUltimaFaixa;

/**
    * @access Private
    * @var Object
*/
var $obTFaixaDesconto;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $stTimestampPrevidencia;

/**
    * @access Public
    * @param Object $valor
*/
function setUltimaFaixa($valor) { $this->obUltimaFaixa       = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setFaixa($valor) { $this->arFaixa              = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodFaixa($valor) { $this->inCodFaixa              = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodPrevidencia($valor) { $this->inCodPrevidencia        = $valor; }
/**
    * @access Public
    * @param Float $Valor
*/
function setSalarioInicial($valor) { $this->flSalarioInicial        = $valor; }
/**
    * @access Public
    * @param Float $Valor
*/
function setSalarioFinal($valor) { $this->flSalarioFinal            = $valor; }
/**
    * @access Public
    * @param Float $Valor
*/
function setPercentualDesc($valor) { $this->flPercentualDesc         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTFaixaDesconto($valor) { $this->obTFaixaDesconto        = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTimestampPrevidencia($valor) { $this->stTimestampPrevidencia  = $valor; }
/**
    * @access Public
    * @return Array
*/
function getFaixa() { return $this->arFaixa            ; }
/**
    * @access Public
    * @return Integer
*/
function getCodFaixa() { return $this->inCodFaixa            ; }
/**
    * @access Public
    * @return Integer
*/
function getCodPrevidencia() { return $this->inCodPrevidencia      ; }
/**
    * @access Public
    * @return Float
*/
function getSalarioInicial() { return $this->flSalarioInicial  ; }
/**
    * @access Public
    * @return Float
*/
function getSalarioFinal() { return $this->flSalarioFinal  ; }

/**
    * @access Public
    * @return Float
*/
function getPercentualDesc() { return $this->flPercentualDesc  ; }
/**
    * @access Public
    * @return Object
*/
function getUltimaFaixa() { return $this->obUltimaFaixa   ; }
/**
    * @access Public
    * @return Object
*/
function getTFaixa() { return $this->obTFaixa   ; }
/**
    * @access Public
    * @return Object
*/
function getTFaixaDesconto() { return $this->obTFaixaDesconto   ; }
/**
    * @access Public
    * @return Object
*/
function getTimestampPrevidencia() { return $this->stTimestampPrevidencia   ; }
/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoFaixaDesconto()
{
    $this->setTFaixaDesconto ( new TFolhaPagamentoFaixaDesconto       );
    $this->obTransacao       = new Transacao;
}

/**
  * Instancia um novo objeto do tipo Faixa
  * @access Public
*/
function addFaixa()
{
    $this->setUltimaFaixa( new RPessoalDescontoFaixa );
}
/**
    * Adiciona o objeto do tipo Faixa ao array
    * @access Public
*/
function commitFaixa()
{
    $arElementos   = $this->getFaixa();
    $arElementos[] = $this->getUltimoFaixa();
    $this->setFaixa( $arElementos );
}

/**
    * Salva dados de Faixas de Desconto  no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarFaixas($boTransacao = "")
{
    $boFlagTransacao = false;
    $rsFaixa = new RecordSet;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $this->getFaixa() ) {
            $arFaixa = $this->getFaixa();
            $rsFaixa->preenche($arFaixa);
            while ( !$rsFaixa->eof() ) {
                $this->obTFaixaDesconto->setDado("cod_previdencia"      , $this->getCodPrevidencia()      );
                $this->obTFaixaDesconto->setDado("timestamp_previdencia", $this->getTimestampPrevidencia()      );
                $this->obTFaixaDesconto->setDado("valor_inicial"        , $rsFaixa->getCampo("flSalarioInicial")   );
                $this->obTFaixaDesconto->setDado("valor_final"          , $rsFaixa->getCampo("flSalarioFinal")     );
                $this->obTFaixaDesconto->setDado("percentual_desconto"  , $rsFaixa->getCampo("flPercentualDesc") );
                $obErro = $this->obTFaixaDesconto->proximoCod( $inCodFaixa , $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTFaixaDesconto->setDado("cod_faixa"     , $inCodFaixa );
                    $obErro = $this->obTFaixaDesconto->inclusao( $boTransacao );
                }
                $rsFaixa->proximo();
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/** Exclui registro do banco
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirFaixaDesconto($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stCodTFaixaDesconto = $this->obTFaixaDesconto->getCampoCod();
        $this->obTFaixaDesconto->setCampoCod("cod_previdencia" );
        $this->obTFaixaDesconto->setDado("cod_previdencia", $this->getCodPrevidencia() );
        $obErro = $this->obTFaixaDesconto->exclusao( $boTransacao );
        $this->obTFaixaDesconto->setCampoCod( $stCodTFaixaDesconto  );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarFaixaDesconto(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if( $this->inCodFaixa )
        $stFiltro .= " cod_faixa = " . $this->inCodFaixa . " AND ";
    if( $this->stDescricao )
        $stFiltro .= " descricao = " . $this->stDescricao . " AND ";
    if( $this->inMascaraCodigo )
        $stFiltro .= " mascara_codigo = " . $this->inMascaraCodigo . " AND ";
    $stFiltro = '';
    $stOrder = ($stOrder)?$stOrder:" ORDER BY cod_faixa ";
    $obErro = $this->obTFaixaDesconto->recuperaLista( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarFaixaDesconto(&$rsRecordSet, $boTransacao = "")
{
    $this->obTFaixaDesconto->setDado( "cod_previdencia" , $this->getCodPrevidencia() );
    if( $this->inCodPrevidencia )
        $stFiltro = " WHERE cod_previdencia = '" . $this->inCodPrevidencia . "'";
    $obErro = $this->obTFaixaDesconto->recuperaLista( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
