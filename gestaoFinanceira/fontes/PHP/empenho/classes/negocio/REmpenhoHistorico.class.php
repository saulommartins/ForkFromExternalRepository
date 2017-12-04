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
    * Classe de Regra de Negócio Histórico de Empenho
    * Data de Criação   : 30/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: luciano $
    $Date: 2007-02-06 09:55:26 -0200 (Ter, 06 Fev 2007) $

    * Casos de uso: uc-02.03.01
                    uc-02.03.03
*/

/*
$Log$
Revision 1.8  2007/02/06 11:55:26  luciano
#8281#

Revision 1.7  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"  );

class REmpenhoHistorico
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
    * @var Integer
    * @access Private
*/
var $inCodHistorico;
/**
    * @var Integer
    * @access Private
*/
var $inCodHistoricoInclusao;
/**
    * @var String
    * @access Private
*/
var $stNomHistorico;
/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                   = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio                   = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodHistorico($valor) { $this->inCodHistorico                = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodHistoricoInclusao($valor) { $this->inCodHistoricoInclusao        = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setNomHistorico($valor) { $this->stNomHistorico               = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getTransacao() { return $this->obTransacao;                            }
/**
     * @access Public
     * @param String $valor
*/
function getExercicio() { return $this->stExercicio;                            }
/**
     * @access Public
     * @param Integer $valor
*/
function getCodHistorico() { return $this->inCodHistorico;                         }
/**
     * @access Public
     * @param Integer $valor
*/
function getCodHistoricoInclusao() { return $this->inCodHistoricoInclusao;                 }
/**
     * @access Public
     * @param String $valor
*/
function getNomHistorico() { return $this->stNomHistorico;                         }

/**
    * Método Construtor
    * @access Private
*/
function REmpenhoHistorico()
{
    $this->setExercicio              	( Sessao::getExercicio()           );
    $this->setTransacao              	( new Transacao                );
}

/**
    * Busca proximo CodHistorico do Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
*/
function proximoCodHistorico($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoHistoricoEmpenho.class.php" );
    $obTEmpenhoHistoricoEmpenho  = new TEmpenhoHistoricoEmpenho;

    $obTEmpenhoHistoricoEmpenho->proximoCod( $inCodHistoricoInclusao , $boTransacao );
    $this->setCodHistoricoInclusao( $inCodHistoricoInclusao );
}

/**
    * Salva Historico no Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoHistoricoEmpenho.class.php" );
    $obTEmpenhoHistoricoEmpenho  = new TEmpenhoHistoricoEmpenho;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obTEmpenhoHistoricoEmpenho->setDado( "exercicio"     , $this->getExercicio()    );
        $obTEmpenhoHistoricoEmpenho->setDado( "nom_historico" , $this->getNomHistorico() );

        if ( $this->getCodHistorico() >= 0 and (!$this->getCodHistoricoInclusao())) {
            $obTEmpenhoHistoricoEmpenho->setDado("cod_historico", $this->getCodHistorico() );
            $this->listarNomeIgual($rsHistorico);

            if($rsHistorico->getNumLinhas()<=0)
                $obErro = $obTEmpenhoHistoricoEmpenho->alteracao( $boTransacao );
            else
                $obErro->setDescricao("Já existe uma descrição semelhante a informada!");

        } else {
            $this->listarNomeIgual($rsHistorico);
            $this->setCodHistorico( $this->getCodHistoricoInclusao() );
            $obTEmpenhoHistoricoEmpenho->setDado("cod_historico", $this->getCodHistorico() );

            if($rsHistorico->getNumLinhas()<=0)
                $obErro = $obTEmpenhoHistoricoEmpenho->inclusao( $boTransacao );
            else
                $obErro->setDescricao("Já existe uma descrição semelhante a informada!");
        }
       $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoHistoricoEmpenho );
    }

    return $obErro;
}

/**
    * Exclui dados de Histórico de Empenho
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoHistoricoEmpenho.class.php" );
    $obTEmpenhoHistoricoEmpenho  = new TEmpenhoHistoricoEmpenho;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obTEmpenhoHistoricoEmpenho->setDado("cod_historico", $this->getCodHistorico() );
        $obTEmpenhoHistoricoEmpenho->setDado("exercicio"    , $this->getExercicio()    );
        $obErro = $obTEmpenhoHistoricoEmpenho->exclusao( $boTransacao );
        if ($obErro->ocorreu()) {
            $obErro->setDescricao('Histórico não pode ser excluído porque está sendo utilizado.');
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoHistoricoEmpenho );
    }

    return $obErro;
}
/**
    * Lista todos os Historicos de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "cod_historico", $obTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoHistoricoEmpenho.class.php" );
    $obTEmpenhoHistoricoEmpenho  = new TEmpenhoHistoricoEmpenho;

    $stFiltro = "";

    if( $this->getCodHistorico() )
        $stFiltro .= " AND cod_historico = ". $this->getCodHistorico();
    if( $this->getExercicio() )
        $stFiltro .= " AND exercicio = '". $this->getExercicio() ."' ";
    if( $this->getNomHistorico() )
        $stFiltro .= " AND LOWER( nom_historico ) LIKE LOWER('%". $this->getNomHistorico() ."%') ";

    $stFiltro = ($stFiltro)?' WHERE cod_historico IS NOT NULL '.$stFiltro:'';

    $obErro = $obTEmpenhoHistoricoEmpenho->recuperaTodos( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Lista todos os Historicos de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNomeIgual(&$rsLista, $stOrder = "cod_historico", $obTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoHistoricoEmpenho.class.php" );
    $obTEmpenhoHistoricoEmpenho  = new TEmpenhoHistoricoEmpenho;

    $stFiltro = "";

    if( $this->getCodHistorico() )
        $stFiltro .= " AND cod_historico <> ". $this->getCodHistorico();
    if( $this->getExercicio() )
        $stFiltro .= " AND exercicio = '". $this->getExercicio() ."' ";
    if( $this->getNomHistorico() )
        $stFiltro .= " AND LOWER( nom_historico ) LIKE LOWER('". $this->getNomHistorico() ."') ";

    $stFiltro = ($stFiltro)?' WHERE cod_historico IS NOT NULL '.$stFiltro:'';

    $obErro = $obTEmpenhoHistoricoEmpenho->recuperaTodos( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoHistoricoEmpenho.class.php" );
    $obTEmpenhoHistoricoEmpenho  = new TEmpenhoHistoricoEmpenho;

    $obTEmpenhoHistoricoEmpenho->setDado( "cod_historico" , $this->getCodHistorico() );
    $obTEmpenhoHistoricoEmpenho->setDado( "exercicio"     , $this->getExercicio()    );

    $obErro = $obTEmpenhoHistoricoEmpenho->recuperaPorChave( $rsRecordSet, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->setNomHistorico  ( $rsRecordSet->getCampo("nom_historico") );
    }

    return $obErro;
}

}
