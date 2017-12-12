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
    * Classe de mapeamento da tabela FN_ExportacaoTCERS_EXPORTACAO_BALANCETE_RECEITA
    * Data de Criação: 01/03/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.01, uc-02.08.07
*/

/*
$Log$
Revision 1.10  2006/07/05 20:45:59  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FExportacaoTCERSExportacaoBalanceteReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FExportacaoTCERSExportacaoBalanceteReceita()
{
    parent::Persistente();
    $this->setTabela('tcers.fn_exportacao_balancete_receita');
}

function montaRecuperaDadosExportacao()
{
    $stSql  = "SELECT                                                               \n";
    $stSql .= "    replace(tabela.cod_estrutural,'.','') as cod_estrutural,         \n";
    $stSql .= "    replace(tabela.vl_original,'.','') as vl_original,               \n";
    $stSql .= "    replace(tabela.totalizado,'.','') as totalizado,                 \n";
    $stSql .= "    tabela.cod_recurso,                                              \n";
    $stSql .= "    tabela.descricao,                                                \n";
    $stSql .= "    tabela.tipo,                                                     \n";
    $stSql .= "    tabela.nivel,                                                    \n";
    $stSql .= "    tabela.cod_descricao                                             \n";
    $stSql .= "FROM                                                                 \n";
    $stSql .= " ".$this->getTabela()."('".$this->getDado("stExercicio")     ."',    \n";
    $stSql .= "                        '".$this->getDado("stCodEntidades")  ."',    \n";
    $stSql .= "                        '".$this->getDado("dtInicial")       ."',    \n";
    $stSql .= "                        '".$this->getDado("dtFinal")         ."')    \n";
    $stSql .= "AS tabela              (   cod_estrutural    varchar,                \n";
    $stSql .= "                           cod_recurso       integer,                \n";
    $stSql .= "                           descricao         varchar,                \n";
    $stSql .= "                           vl_original       numeric,                \n";
    $stSql .= "                           totalizado        numeric,                \n";
    $stSql .= "                           tipo              varchar,                \n";
    $stSql .= "                           nivel             integer,                \n";
    $stSql .= "                           cod_descricao     integer)                \n";
    $stSql .= "WHERE tabela.nivel<>0                                                \n";
    $stSql .= "ORDER BY tabela.cod_estrutural                                       \n";

    return $stSql;
}

/**
    * Executa funcao fn_exportacao_liquidacao no banco de dados a partir do comando SQL montado no método montaRecuperaDadosLiquidacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
