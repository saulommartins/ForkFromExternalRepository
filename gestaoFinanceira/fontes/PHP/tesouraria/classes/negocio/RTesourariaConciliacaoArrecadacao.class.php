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
    * Classe de Regra para Conciliação de Arrecadacao
    * Data de Criação   : 23/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.19
*/

/*
$Log$
Revision 1.3  2006/07/05 20:38:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"                                    );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaArrecadacao.class.php"                                );

/**
    * Classe de Regra de Negócios Conciliação de Arrecadação
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RTesourariaConciliacaoArrecadacao
{
/*
    * @var Object
    * @access Private
*/
var $roRTesourariaConciliacao;
/*
    * @var Object
    * @access Private
*/
var $obRTesourariaArrecadacao;
/*
    * @var String
    * @access Private
*/
var $stTipoValor;
/*
    * @var String
    * @access Private
*/
var $stTipo;

/**
     * @access Public
     * @param String $valor
*/
function setTipoValor($valor) { $this->stTipoValor = $valor; }
/**

     * @access Public
     * @param String $valor
*/
function setTipo($valor) { $this->stTipo      = $valor; }

/*
    * @access Public
    * @return String
*/
function getTipoValor() { return $this->stTipoValor; }
/*
    * @access Public
    * @return String
*/
function getTipo() { return $this->stTipo;      }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaConciliacaoArrecadacao($roRTesourariaConciliacao)
{
    $this->obRTesourariaArrecadacao = new RTesourariaArrecadacao( new RTesourariaBoletim() );
    $this->roRTesourariaConciliacao = &$roRTesourariaConciliacao;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"                                   );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaConciliacaoLancamentoArrecadacao.class.php" );
    $obTransacao = new Transacao();
    $obTTesourariaConciliacaoArrecadacao = new TTesourariaConciliacaoLancamentoArrecadacao();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( !$obErro->ocorreu() ) {
            $obTTesourariaConciliacaoArrecadacao->setDado( "cod_plano"            , $this->obRTesourariaArrecadacao->obRContabilidadePlanoBanco->getCodPlano());
            $obTTesourariaConciliacaoArrecadacao->setDado( "exercicio"            , $this->obRTesourariaArrecadacao->obRContabilidadePlanoBanco->getExercicio());
            $obTTesourariaConciliacaoArrecadacao->setDado( "cod_arrecadacao"      , $this->obRTesourariaArrecadacao->getCodArrecadacao());
            $obTTesourariaConciliacaoArrecadacao->setDado( "timestamp_arrecadacao", $this->obRTesourariaArrecadacao->getTimestampArrecadacao());
            $obTTesourariaConciliacaoArrecadacao->setDado( "mes"                  , $this->roRTesourariaConciliacao->getMes());
            $obTTesourariaConciliacaoArrecadacao->setDado( "tipo_valor"           , $this->stTipoValor );
            $obTTesourariaConciliacaoArrecadacao->setDado( "tipo"                 , $this->stTipo      );
            $obErro = $obTTesourariaConciliacaoArrecadacao->inclusao( $boTransacao );
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaConciliacaoArrecadacao );

    return $obErro;
}

/**
    * Apaga os dados do banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"                                   );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaConciliacaoLancamentoArrecadacao.class.php" );
    $obTransacao      =  new Transacao;
    $obTTesourariaConciliacaoArrecadacao =  new TTesourariaConciliacaoLancamentoArrecadacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTTesourariaConciliacaoArrecadacao->setDado( "cod_plano", $this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getCodPlano());
        $obTTesourariaConciliacaoArrecadacao->setDado( "exercicio", $this->roRTesourariaConciliacao->obRContabilidadePlanoBanco->getExercicio());
        $obTTesourariaConciliacaoArrecadacao->setDado( "mes"      , $this->roRTesourariaConciliacao->getMes());
        $obErro = $obTTesourariaConciliacaoArrecadacao->exclusao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaConciliacaoArrecadacao );

    return $obErro;
}

/**
    * Método para salvar dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro de Transação
    * @return Object $obErro      Objeto de Erro
*/
function salvar($boTransacao = "")
{
    return new Erro();
}

/**
    * Executa um recuperaTodos da classe persistencia
    * @access Public
    * @param  Object $rsRecordSet
    * @param  String $stFiltro
    * @param  String $stOrder
    * @param  Object $boTransacao
    * @return Object $obErro
*/
function listar(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    if( $this->obRTesourariaArrecadacao->obRContabilidadePlanoBanco->getCodPlano() )
        $stFiltro .= " cod_plano = ".$this->obRTesourariaArrecadacao->obRContabilidadePlanoBanco->getCodPlano()." AND ";
    if( $this->obRTesourariaArrecadacao->obRContabilidadePlanoBanco->getExercicio() )
        $stFiltro .= " exercicio = '".$this->obRTesourariaArrecadacao->obRContabilidadePlanoBanco->getExercicio()." AND ";
    if( $this->roRTesourariaConciliacao->getMes() )
        $stFiltro .= " mes = ".$this->roRTesourariaConciliacao->getMes()." AND ";

    $stFiltro = ( $stFiltro ) ? " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $obErro = $obTTesourariaConciliacaoArrecadacao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
