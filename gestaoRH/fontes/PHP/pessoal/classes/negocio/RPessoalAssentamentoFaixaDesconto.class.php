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
    * Classe de regra de negócio Pessoal AssentamentoFaixaDesconto
    * Data de Criação: 29/11/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.08

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoFaixaDesconto.class.php"         );

class RPessoalAssentamentoFaixaDesconto
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
var $inCodAssentamento;

/**
    * @access Private
    * @var Float
*/
var $inInicioIntervalo;
/**
    * @access Private
    * @var Float
*/
var $inFimIntervalo;
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
var $obTPessoalAssentamentoFaixaDesconto;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;

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
function setCodAssentamento($valor) { $this->inCodAssentamento        = $valor; }
/**
    * @access Public
    * @param Float $Valor
*/
function setInicioIntervalo($valor) { $this->inInicioIntervalo        = $valor; }
/**
    * @access Public
    * @param Float $Valor
*/
function setFimIntervalo($valor) { $this->inFimIntervalo            = $valor; }
/**
    * @access Public
    * @param Float $Valor
*/
function setPercentualDesc($valor) { $this->flPercentualDesc         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTPessoalAssentamentoFaixaDesconto($valor) { $this->obTPessoalAssentamentoFaixaDesconto        = $valor; }
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
function getCodAssentamento() { return $this->inCodAssentamento      ; }
/**
    * @access Public
    * @return Float
*/
function getInicioIntervalo() { return $this->inInicioIntervalo  ; }
/**
    * @access Public
    * @return Float
*/
function getFimIntervalo() { return $this->inFimIntervalo  ; }

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
function getTPessoalAssentamentoFaixaDesconto() { return $this->obTPessoalAssentamentoFaixaDesconto   ; }

/**
     * Método construtor
     * @access Private
*/
function RPessoalAssentamentoFaixaDesconto()
{
    $this->setTPessoalAssentamentoFaixaDesconto ( new TPessoalAssentamentoFaixaDesconto       );
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
//        $obErro = $this->excluirAssentamentoFaixaDesconto( $boTransacao );
        if ( $this->getFaixa() ) {
            if ( !$obErro->ocorreu() ) {
                $arFaixa = $this->getFaixa();
                $rsFaixa->preenche($arFaixa);
                while ( !$rsFaixa->eof() ) {
                    $this->obTPessoalAssentamentoFaixaDesconto->setDado("cod_assentamento"     , $this->getCodAssentamento()      );
                    $this->obTPessoalAssentamentoFaixaDesconto->setDado("valor_inicial"        , $rsFaixa->getCampo("inInicioIntervalo")   );
                    $this->obTPessoalAssentamentoFaixaDesconto->setDado("valor_final"          , $rsFaixa->getCampo("inFimIntervalo")     );
                    $this->obTPessoalAssentamentoFaixaDesconto->setDado("percentual_desconto"  , $rsFaixa->getCampo("flPercentualDesc") );
                    $this->obTPessoalAssentamentoFaixaDesconto->recuperaNow3($stNow3,$boTransacao);
                    $this->obTPessoalAssentamentoFaixaDesconto->setDado("timestamp"  , $stNow3);

                    $tmpComplementoCod   =  $this->obTPessoalAssentamentoFaixaDesconto->getCampoCod();
                    $this->obTPessoalAssentamentoFaixaDesconto->setComplementoChave('');
                    $this->obTPessoalAssentamentoFaixaDesconto->setCampoCod('cod_faixa');
                    $obErro = $this->obTPessoalAssentamentoFaixaDesconto->proximoCod( $inCodFaixa , $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $this->setCodFaixa( $inCodFaixa);
                        $this->obTPessoalAssentamentoFaixaDesconto->setDado("cod_faixa"     , $inCodFaixa );
                        $obErro = $this->obTPessoalAssentamentoFaixaDesconto->inclusao( $boTransacao );
                    }
                    $this->obTPessoalAssentamentoFaixaDesconto->setComplementoChave($tmpComplementoCampo);
                    $this->obTPessoalAssentamentoFaixaDesconto->setCampoCod($tmpComplementoCod);
                    $rsFaixa->proximo();
                }
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
function excluirAssentamentoFaixaDesconto($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stCodTPessoalAssentamentoFaixaDesconto = $this->obTPessoalAssentamentoFaixaDesconto->getCampoCod();
        $this->obTPessoalAssentamentoFaixaDesconto->setCampoCod("cod_assentamento" );
        $this->obTPessoalAssentamentoFaixaDesconto->setDado("cod_assentamento", $this->getCodAssentamento() );
        $obErro = $this->obTPessoalAssentamentoFaixaDesconto->exclusao( $boTransacao );
        $this->obTPessoalAssentamentoFaixaDesconto->setCampoCod( $stCodTPessoalAssentamentoFaixaDesconto  );
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
function listarAssentamentoFaixaDesconto(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if( $this->inCodFaixa )
        $stFiltro .= " cod_faixa = " . $this->inCodFaixa . " AND ";
    if( $this->stDescricao )
        $stFiltro .= " descricao = " . $this->stDescricao . " AND ";
    if( $this->inMascaraCodigo )
        $stFiltro .= " mascara_codigo = " . $this->inMascaraCodigo . " AND ";
    $stFiltro = '';
    $stOrder = ($stOrder)?$stOrder:" ORDER BY cod_faixa ";
    $obErro = $this->obTPessoalAssentamentoFaixaDesconto->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function consultarAssentamentoFaixaDesconto(&$rsRecordSet, $boTransacao = "")
{
    $this->obTPessoalAssentamentoFaixaDesconto->setDado( "cod_assentamento" , $this->getCodAssentamento() );
    if( $this->inCodAssentamento )
        $stFiltro = " and  pafd.cod_assentamento = '" . $this->inCodAssentamento . "'";
    $obErro = $this->obTPessoalAssentamentoFaixaDesconto->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
