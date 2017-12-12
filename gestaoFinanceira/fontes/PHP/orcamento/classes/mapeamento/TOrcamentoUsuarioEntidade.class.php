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
    * Classe de mapeamento da tabela ORCAMENTO.USUARIO_UNIDADE
    * Data de Criação: 19/07/2004

    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.01.02
*/

/*
$Log$
Revision 1.8  2006/07/14 17:58:12  andre.almeida
Bug #6556#

Alterado scripts de NOT IN para NOT EXISTS.

Revision 1.7  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TOrcamentoUsuarioEntidade extends Persistente
{
function TOrcamentoUsuarioEntidade()
{
    parent::Persistente();
    $this->setTabela('orcamento.usuario_entidade');
    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,numcgm,cod_entidade');

    $this->AddCampo('exercicio'    , 'char'    , true, '4' , true ,  false );
    $this->AddCampo('numcgm'       , 'integer' , true,  '' , true ,  false );
    $this->AddCampo('cod_entidade' , 'integer' , true, '10', true ,  false );
}

function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
//    $stGroup .= "GROUP BY               ";
//    $stGroup .= "    OUE.cod_entidade,  ";
//    $stGroup .= "    OUE.numcgm         ";
    $stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stGroup.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT                                                   \n";
    $stSql .= "    OUE.cod_entidade,                                    \n";
    $stSql .= "    OUE.exercicio,                                       \n";
    $stSql .= "    OUE.numcgm                                           \n";
    $stSql .= "FROM                                                     \n";
    $stSql .= "    orcamento.usuario_entidade as OUE                    \n";
    $stSql .= "WHERE                                                    \n";
    $stSql .= "    OUE.cod_entidade is not null                \n";

    return $stSql;
}

/**
    * Seleciona membros disponíveis de acordo com o filtro
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $stFiltro Filtro da pesquisa
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaMembrosDisponiveis(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoDisponiveis().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta consulta para recuperar usuarios Disponiveis
    * @access Private
    * @return String $stSql
*/
function montaRecuperaRelacionamentoDisponiveis()
{
    $stSql .= "SELECT                                                   \n";
    $stSql .= "    U.*,                                                 \n";
    $stSql .= "    C.nom_cgm                                            \n";
    $stSql .= "FROM                                                     \n";
    $stSql .= "    administracao.usuario  AS U,                         \n";
    $stSql .= "    sw_cgm      AS C                                     \n";
    $stSql .= "WHERE                                                    \n";
    $stSql .= "    U.numcgm = C.numcgm                                  \n";

    $stSql .= "AND NOT EXISTS ( SELECT 1                                                     \n";
    $stSql .= "                   FROM orcamento.usuario_entidade oue                        \n";
    $stSql .= "                  WHERE oue.numcgm       = U.numcgm                           \n";
    $stSql .= "                    AND oue.cod_entidade = ".$this->getDado('cod_entidade')." \n";
    $stSql .= "                    AND oue.exercicio    = '".$this->getDado('exercicio')."'    \n";
    $stSql .= "               )                                                              \n";
//     $stSql .= "    U.numcgm              NOT IN                         \n";
//     $stSql .= "    (                                                    \n";
//     $stSql .= "    SELECT                                               \n";
//     $stSql .= "        numcgm                                           \n";
//     $stSql .= "    FROM                                                 \n";
//     $stSql .= "        ".$this->getTabela()."                           \n";
//     $stSql .= "    WHERE                                                \n";
//     $stSql .= "        cod_entidade = ".$this->getDado('cod_entidade')." \n";
//     $stSql .= "    AND exercicio    = ".$this->getDado('exercicio')   ." \n";
//     $stSql .= "    )                                                    \n";
    return $stSql;
}

/**
    * Seleciona membros selecionados acordo com o filtro
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $stFiltro Filtro da pesquisa
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaMembrosSelecionados(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoSelecionados().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta consulta para recuperar usuarios Selecionados
    * @access Private
    * @return String $stSql
*/
function montaRecuperaRelacionamentoSelecionados()
{
    $stSql .= "SELECT                                                   \n";
    $stSql .= "    U.*,                                                 \n";
    $stSql .= "    C.nom_cgm                                            \n";
    $stSql .= "FROM                                                     \n";
    $stSql .= "    administracao.usuario  AS U,                              \n";
    $stSql .= "    sw_cgm           AS C                               \n";
    $stSql .= "WHERE                                                    \n";
    $stSql .= "    U.numcgm = C.numcgm      AND                         \n";
    $stSql .= "    U.numcgm                 IN                          \n";
    $stSql .= "    ( SELECT                                             \n";
    $stSql .= "        numcgm                                           \n";
    $stSql .= "    FROM                                                 \n";
    $stSql .= "        ".$this->getTabela()."                           \n";
    $stSql .= "    WHERE                                                \n";
    $stSql .= "        cod_entidade = ".$this->getDado('cod_entidade')." \n";
    $stSql .= "    AND exercicio    = '".$this->getDado('exercicio')   ."' \n";
    $stSql .= "    )                                                    \n";

    return $stSql;
}

}
