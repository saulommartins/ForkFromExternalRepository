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
    * Classe de Regra de Negócio Orcamento Previsao Orcamentaria
    * Data de Criação   : 11/08/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @package URBEM
    * @subpackage Regra

    $Revision: 30824 $
    $Name$
    $Author: lbbarreiro $
    $Date: 2008-04-07 10:06:52 -0300 (Seg, 07 Abr 2008) $

    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.6  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"        );

/**
    * Classe de Regra de Negócio Orcamento Previsao Orcamentaria
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Roberto Pawelski Rodrigues
*/
class ROrcamentoPrevisaoOrcamentaria
{
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var String
    * @access Private
*/
var $stExercicio;

/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao          = $valor; }

/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio          = $valor; }

/**
     * @access Public
     * @return Object
*/
function getTransacao() { return $this->obTransacao;          }

/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;          }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoPrevisaoOrcamentaria()
{
    $this->setTransacao                      ( new Transacao          );
    $this->setExercicio                      ( Sessao::getExercicio()     );
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoPrevisaoOrcamentaria.class.php"  );
    $obTOrcamentoPrevisaoOrcamentaria  = new TOrcamentoPrevisaoOrcamentaria;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoPrevisaoOrcamentaria->setDado( "exercicio" , $this->getExercicio() );
        $obErro = $obTOrcamentoPrevisaoOrcamentaria->inclusao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoPrevisaoOrcamentaria );
    }

    return $obErro;
}

/**
    * Exclui os dados do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoPrevisaoOrcamentaria.class.php"  );
    $obTOrcamentoPrevisaoOrcamentaria  = new TOrcamentoPrevisaoOrcamentaria;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoPrevisaoOrcamentaria->setDado( "exercicio" , $this->getExercicio() );
        $obErro = $obTOrcamentoPrevisaoOrcamentaria->exclusao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoPrevisaoOrcamentaria );
    }

    return $obErro;
}

/**
    * Executa um recuperaRelacionamento na classe Persistente Item
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "", $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoPrevisaoOrcamentaria.class.php"  );
    $obTOrcamentoPrevisaoOrcamentaria  = new TOrcamentoPrevisaoOrcamentaria;

    $stFiltro = "";
    if ( $this->getExercicio() ) {
        $stFiltro .= " WHERE exercicio = '".$this->getExercicio() . "' ";
    }
    $obErro = $obTOrcamentoPrevisaoOrcamentaria->recuperaTodos( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}
/**
    * Executa um recuperaPorChave na classe Persistente Item
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar(&$rsLista, $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoPrevisaoOrcamentaria.class.php"  );
    $obTOrcamentoPrevisaoOrcamentaria  = new TOrcamentoPrevisaoOrcamentaria;

    $obTOrcamentoPrevisaoOrcamentaria->setDado( "exercicio" , $this->getExercicio() );
    $obErro = $obTOrcamentoPrevisaoOrcamentaria->recuperaPorChave( $rsLista, $obTransacao );
    if ( !$obErro->ocorreu()) {
        $this->setExercicio( $rsLista->getCampo('exercicio') );
    }

    return $obErro;
}

}
