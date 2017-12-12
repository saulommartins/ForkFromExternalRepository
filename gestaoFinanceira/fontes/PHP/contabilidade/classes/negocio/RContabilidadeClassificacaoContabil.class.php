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
    * Classe de Regra de Classificacao Contabil
    * Data de Criação   : 03/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-03-07 09:45:35 -0300 (Qua, 07 Mar 2007) $

    * Casos de uso: uc-02.02.01
*/

/*
$Log$
Revision 1.7  2007/03/07 12:45:35  rodrigo_sr
#7993#

Revision 1.6  2006/07/05 20:50:26  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                       );

/**
    * Classe de Regra de Classificacao Contabil
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RContabilidadeClassificacaoContabil
{
/**
    * @access Private
    * @var Integer
*/
var $inCodClassificacao;
/**
    * @access Private
    * @var String
*/
var $stNomClassificacao;
/**
    * @access Private
    * @var String
*/
var $stExercicio;

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodClassificacao($valor) { $this->inCodClassificacao = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setNomClassificacao($valor) { $this->stNomClassificacao = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setExercicio($valor) { $this->stExercicio        = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodClassificacao() { return $this->inCodClassificacao; }
/**
    * @access Public
    * @return String
*/
function getNomClassificacao() { return $this->stNomClassificacao;     }
/**
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;        }

/**
     * Método construtor
     * @access Public
*/
function RContabilidadeClassificacaoContabil()
{
    $this->obTransacao = new Transacao ;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeClassificacaoContabil.class.php");
    $obTContabilidadeClassificacaoContabil = new TContabilidadeClassificacaoContabil;

    $obTContabilidadeClassificacaoContabil->setDado( "cod_classificacao", $this->inCodClassificacao );
    $obTContabilidadeClassificacaoContabil->setDado( "exercicio"        , $this->stExercicio        );
    $obErro = $obTContabilidadeClassificacaoContabil->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stNomClassificacao = $rsRecordSet->getCampo( "nom_classificacao" );
    }

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
function listar(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeClassificacaoContabil.class.php");
    $obTContabilidadeClassificacaoContabil = new TContabilidadeClassificacaoContabil;

    if($this->inCodClassificacao)
        $stFiltro  = " cod_classificacao = "  . $this->inCodClassificacao  . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " exercicio = '"         . $this->stExercicio         . "' AND ";
    if($this->stNomClassificacao)
        $stFiltro .= " lower(nom_classificacao) like lower('%" . $this->stNomClassificacao . "%') AND ";
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ($stOrder) ? $stOrder : "cod_classificacao";
    $obErro = $obTContabilidadeClassificacaoContabil->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaUltimoExercicio na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarUltimoExercicio(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeClassificacaoContabil.class.php';
    $obTContabilidadeClassificacaoContabil = new TContabilidadeClassificacaoContabil;

    $stOrder = ($stOrder) ? $stOrder : " ORDER BY cod_classificacao";
    $obErro = $obTContabilidadeClassificacaoContabil->recuperaUltimoExercicio($rsRecordSet, "", $stOrder, $boTransacao);

    return $obErro;
}

/**
    * Incluir Classificao Contabil
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeClassificacaoContabil.class.php");
    $obTContabilidadeClassificacaoContabil = new TContabilidadeClassificacaoContabil;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $stFiltro  = " WHERE cod_classificacao = ".$this->getCodClassificacao();
        $stFiltro .= "   AND exercicio = '".$this->getExercicio()."'";
            $obErro = $obTContabilidadeClassificacaoContabil->recuperaTodos($rsClassificacaoContabil, $stFiltro,'',$boTransacao);
            if ( !$obErro->ocorreu() ) {
                if ( $rsClassificacaoContabil->eof() ) {

        $obTContabilidadeClassificacaoContabil->setDado( "cod_classificacao"  , $this->getCodClassificacao() );
        $obTContabilidadeClassificacaoContabil->setDado( "exercicio"          , $this->getExercicio() );
        $obTContabilidadeClassificacaoContabil->setDado( "nom_classificacao"  , $this->getNomClassificacao() );

        $obErro = $obTContabilidadeClassificacaoContabil->inclusao( $boTransacao );

                } else {
                    $obErro->setDescricao("Código ".$this->getCodClassificacao()." já cadastrado!");
                }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeClassificacaoContabil );
            }
    }

    return $obErro;
}

/**
    * Alterar Classificao Contabil
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeClassificacaoContabil.class.php");
    $obTContabilidadeClassificacaoContabil = new TContabilidadeClassificacaoContabil;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obTContabilidadeClassificacaoContabil->setDado( "cod_classificacao"  , $this->getCodClassificacao() );
        $obTContabilidadeClassificacaoContabil->setDado( "exercicio"          , $this->getExercicio() );
        $obTContabilidadeClassificacaoContabil->setDado( "nom_classificacao"  , $this->getNomClassificacao() );

        $obErro = $obTContabilidadeClassificacaoContabil->alteracao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeClassificacaoContabil );
    }

    return $obErro;
}

/**
    * Exclui Classificacao Contabil
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeClassificacaoContabil.class.php");
    $obTContabilidadeClassificacaoContabil = new TContabilidadeClassificacaoContabil;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obTContabilidadeClassificacaoContabil->setDado("cod_classificacao", $this->getCodClassificacao() );
        $obTContabilidadeClassificacaoContabil->setDado("exercicio"        , $this->getExercicio() );
        $obErro = $obTContabilidadeClassificacaoContabil->exclusao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeClassificacaoContabil );
    }

    return $obErro;
}

}
