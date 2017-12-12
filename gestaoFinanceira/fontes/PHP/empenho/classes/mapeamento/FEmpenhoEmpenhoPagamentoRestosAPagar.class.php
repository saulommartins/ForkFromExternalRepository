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
    * Classe de mapeamento da tabela FN_EMPENHO_EMPENHO_PAGAMENTO_RESTOS_A_PAGAR
    * Data de Criação: 29/12/2004

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-08-28 11:40:39 -0300 (Seg, 28 Ago 2006) $

    * Caso de uso uc-02.03.23,uc-02.04.05
*/

/*
$Log$
Revision 1.11  2006/08/28 14:40:39  cleisson
Bug #6762#

Revision 1.10  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FEmpenhoEmpenhoPagamentoRestosAPagar extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FEmpenhoEmpenhoPagamentoRestosAPagar()
{
    parent::Persistente();
    $this->setTabela('EmpenhoPagamentoRPLiquidado');

    $this->AddCampo('exercicio'              ,'varchar',false,''    ,false,false);
    $this->AddCampo('valor'                  ,'numeric',false,'14.2',false,false);
    $this->AddCampo('complemento'            ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_lote'               ,'integer',false,''    ,false,false);
    $this->AddCampo('tipo_lote'              ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade'           ,'integer',false,''    ,false,false);
    $this->AddCampo('cod_nota'               ,'integer',false,''    ,false,false);
    $this->AddCampo('conta_pg'               ,'integer',false,''    ,false,false);
    $this->AddCampo('exerc_rp'               ,'varchar',false,''    ,false,false);
    $this->AddCampo('restos'                 ,'varchar',false,''    ,false,false);
    $this->AddCampo('exercicio_liquidacao'   ,'varchar',false,''    ,false,false);
}

function montaInsereLote()
{
    $stSql  = " SELECT  \n";
    $stSql .= "      contabilidade.fn_insere_lote( ";
    $stSql .= " '".$this->getDado('exercicio')."' ";
    $stSql .= " ,".$this->getDado('cod_entidade');
    $stSql .= " ,'".$this->getDado('tipo_lote')."' ";
    $stSql .= " ,'".$this->getDado('nom_lote')."' ";
    $stSql .= " ,'".$this->getDado('dt_lote')."' ";
    $stSql .= " ) as cod_lote \n";

    return $stSql ;
}

function montaExecutaFuncao()
{
    $stSql  = " SELECT  \n";
    $stSql .= " ".$this->getTabela()."('".$this->getDado("exercicio")               ."', \n";
    $stSql .= "                         ".$this->getDado("valor")                   ." , \n";
    $stSql .= "                        '".$this->getDado("complemento")             ."', \n";
    $stSql .= "                         ".$this->getDado("cod_lote")                ." , \n";
    $stSql .= "                        '".$this->getDado("tipo_lote")               ."', \n";
    $stSql .= "                         ".$this->getDado("cod_entidade")            ." , \n";
    $stSql .= "                         ".$this->getDado("cod_nota")                ." , \n";
    $stSql .= "                        '".$this->getDado("conta_pg")                ."', \n";
    $stSql .= "                        '".$this->getDado("exerc_rp")                ."', \n";
    $stSql .= "                        '".$this->getDado("restos")                  ."', \n";
    $stSql .= "                        '".$this->getDado("exercicio_liquidacao")    ."') \n";
    $stSql .= "                         as sequencia                                     \n";

    return $stSql;
}

function montaExecutaFuncaoTCEMS()
{
    $stSql  = " SELECT  \n";
    $stSql .= " EmpenhoPagamentoRPLiquidadoTCEMS('".$this->getDado("exercicio")     ."', \n";
    $stSql .= "                         ".$this->getDado("valor")                   ." , \n";
    $stSql .= "                        '".$this->getDado("complemento")             ."', \n";
    $stSql .= "                         ".$this->getDado("cod_lote")                ." , \n";
    $stSql .= "                        '".$this->getDado("tipo_lote")               ."', \n";
    $stSql .= "                         ".$this->getDado("cod_entidade")            ." , \n";
    $stSql .= "                         ".$this->getDado("cod_nota")                ." , \n";
    $stSql .= "                        '".$this->getDado("conta_pg")                ."', \n";
    $stSql .= "                        '".$this->getDado("exerc_rp")                ."', \n";
    $stSql .= "                        '".$this->getDado("restos")                  ."', \n";
    $stSql .= "                        '".$this->getDado("exercicio_liquidacao")    ."') \n";
    $stSql .= "                         as sequencia                                     \n";

    return $stSql;
}

/**
    * Executa funcao EmpenhoEmissao no banco de dados a partir do comando SQL montado no método montaExecutaFuncao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function executaFuncao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ( !$this->getDado("cod_lote") ) {
        $stSql = $this->montaInsereLote();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSetLote, $stSql, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setDado("cod_lote", $rsRecordSetLote->getCampo("cod_lote"));
        }
    }
    if ( !$obErro->ocorreu() ) {
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        if (Sessao::getExercicio() > '2012') {
            $stSql = $this->montaExecutaFuncaoTCEMS();
        } else {
            $stSql = $this->montaExecutaFuncao();
        }
        $stSql .= $stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setDado("sequencia", $rsRecordSet->getCampo("sequencia"));
        }
    }

    return $obErro;
}

/**
    * Método para executar montaRecuperaTipoRestosPagar
    * @access Private
    * @param Object $rsRecordSet
    * @param Object $boTransacao
    * @return Object $obErro
*/
function recuperaTipoRestosPagar(&$rsRecordSet, $stFiltro = "", $stOrder = "",$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $this->setDado( 'stFiltro', $stFiltro );
    $stSql = $this->montaRecuperaTipoRestosPagar();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTipoRestosPagar()
{
    $stSql  = "SELECT                                                                 \n";
    $stSql .= "     CASE WHEN parametro = 'cod_entidade_camara' THEN 'Legislativo'    \n";
    $stSql .= "          WHEN parametro = 'cod_entidade_rpps'   THEN 'RPPS'           \n";
    $stSql .= "          ELSE 'Executivo'                                             \n";
    $stSql .= "     END AS tipo_restos                                                \n";
    $stSql .= "     FROM                                                              \n";
    $stSql .= "       orcamento.entidade AS oe                                        \n";
    $stSql .= "       LEFT JOIN administracao.configuracao AS ac ON(                  \n";
    $stSql .= "            cod_modulo    = 8                AND                       \n";
    $stSql .= "            parametro  LIKE 'cod_entidade_%' AND                       \n";
    $stSql .= "            ac.exercicio  = oe.exercicio     AND                       \n";
    $stSql .= "            ac.valor      = oe.cod_entidade::VARCHAR                   \n";
    $stSql .= "       )                                                               \n";
    $stSql .= "     WHERE                                                             \n";
    $stSql .= "        oe.exercicio    = '".$this->getDado("exerc_rp")."'        AND  \n";
    $stSql .= "        oe.cod_entidade =  ".$this->getDado("cod_entidade")."          \n";

    return $stSql;
}

}
