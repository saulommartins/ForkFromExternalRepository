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
    * Classe de mapeamento da tabela FN_ORCAMENTO_RELACAO_DESPESA
    * Data de Criação: 24/09/2004

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria
    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Anderson Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.01.18
*/

/*
$Log$
Revision 1.11  2006/11/20 21:37:37  gelson
Bug #7444#
Parte 1

Revision 1.10  2006/10/18 18:17:55  cako
Bug #7241#

Revision 1.9  2006/07/10 14:04:32  anasilvia
Bug #5140#

Revision 1.8  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FOrcamentoRelacaoDespesa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FOrcamentoRelacaoDespesa()
{
    parent::Persistente();
    $this->setTabela('orcamento.fn_relacao_despesa');

    $this->AddCampo('exercicio'            ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_despesa'          ,'integer',false,''    ,false,false);
    $this->AddCampo('cod_entidade'         ,'integer',false,''    ,false,false);
    $this->AddCampo('cod_programa'         ,'integer',false,''    ,false,false);
    $this->AddCampo('cod_conta'            ,'integer',false,''    ,false,false);
    $this->AddCampo('num_pao'              ,'integer',false,''    ,false,false);
    $this->AddCampo('num_orgao'            ,'integer',false,''    ,false,false);
    $this->AddCampo('num_unidade'          ,'integer',false,''    ,false,false);
    $this->AddCampo('cod_recurso'          ,'integer',false,''    ,false,false);
    $this->AddCampo('cod_funcao'           ,'integer',false,''    ,false,false);
    $this->AddCampo('cod_subfuncao'        ,'integer',false,''    ,false,false);
    $this->AddCampo('vl_original'          ,'numeric',false,'14.2',false,false);
    $this->AddCampo('dt_criacao'           ,'date'   ,false,''    ,false,false);
    $this->AddCampo('classificacao'        ,'varchar',false,''    ,false,false);
    $this->AddCampo('descricao'            ,'varchar',false,''    ,false,false);
    $this->AddCampo('num_recurso'          ,'varchar',false,''    ,false,false);
    $this->AddCampo('nom_recurso'          ,'varchar',false,''    ,false,false);
    $this->AddCampo('nom_orgao'            ,'varchar',false,''    ,false,false);
    $this->AddCampo('nom_unidade'          ,'varchar',false,''    ,false,false);
    $this->AddCampo('nom_funcao'           ,'varchar',false,''    ,false,false);
    $this->AddCampo('nom_subfuncao'        ,'varchar',false,''    ,false,false);
    $this->AddCampo('nom_programa'         ,'varchar',false,''    ,false,false);
    $this->AddCampo('nom_pao'              ,'varchar',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT *                                                                                                    \n";
    $stSql .= "       ,empenho.fn_saldo_dotacao( exercicio, cod_despesa ) as saldo_dotacao                              \n";
    $stSql .= " FROM ".$this->getTabela()."('".$this->getDado("exercicio")."','".$this->getDado("stFiltro")."', '".$this->getDado("inNumOrgao")."','".$this->getDado("inNumUnidade")."', '".$this->getDado("cod_recurso")."','".$this->getDado("stDestinacaoRecurso")."','".$this->getDado('cod_detalhamento')."') as retorno( \n";
    $stSql .= "     exercicio       char(4),                                                                                \n";
    $stSql .= "     cod_despesa     integer,                                                                                \n";
    $stSql .= "     cod_entidade    integer,                                                                                \n";
    $stSql .= "     cod_programa    integer,                                                                                \n";
    $stSql .= "     cod_conta       integer,                                                                                \n";
    $stSql .= "     num_pao         integer,                                                                                \n";
    $stSql .= "     num_orgao       integer,                                                                                \n";
    $stSql .= "     num_unidade     integer,                                                                                \n";
    $stSql .= "     cod_recurso     integer,                                                                                \n";
    $stSql .= "     cod_funcao      integer,                                                                                \n";
    $stSql .= "     cod_subfuncao   integer,                                                                                \n";
    $stSql .= "     vl_original     numeric,                                                                                \n";
    $stSql .= "     dt_criacao      date,                                                                                   \n";
    $stSql .= "     classificacao   varchar,                                                                                \n";
    $stSql .= "     descricao       varchar,                                                                                \n";
    $stSql .= "     num_recurso     varchar,                                                                                \n";
    $stSql .= "     nom_recurso     varchar,                                                                                \n";
    $stSql .= "     nom_orgao       varchar,                                                                                \n";
    $stSql .= "     nom_unidade     varchar,                                                                                \n";
    $stSql .= "     nom_funcao      varchar,                                                                                \n";
    $stSql .= "     nom_subfuncao   varchar,                                                                                \n";
    $stSql .= "     nom_programa    varchar,                                                                                \n";
    $stSql .= "     nom_pao         varchar,                                                                                \n";
    $stSql .= "     num_programa    varchar,                                                                                \n";
    $stSql .= "     num_acao        varchar                                                                                 \n";
    $stSql .= "     )                                                                                                       \n";

    return $stSql;
}

function consultaValorConta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaConsultaValorConta().$stFiltro.$stGroup.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    //echo $stSql;
    return $obErro;
}

function montaConsultaValorConta()
{
    $stQuebra = "\n";

    $stSql .= "SELECT coalesce(SUM(func.vl_original),0.00) as sum                                                     ".$stQuebra;
    $stSql .= "  FROM (SELECT despesa.cod_despesa, despesa.vl_original, conta_despesa.cod_estrutural AS classificacao ".$stQuebra;
    $stSql .= "          FROM orcamento.conta_despesa, orcamento.despesa                            ".$stQuebra;
    $stSql .= "         WHERE conta_despesa.cod_conta   = despesa.cod_conta                         ".$stQuebra;
    $stSql .= "           AND conta_despesa.exercicio   = despesa.exercicio                         ".$stQuebra;
    $stSql .= "           AND despesa.exercicio         ='".$this->getDado("exercicio")."'".$stQuebra;
    $stSql .= " ) AS func                                                                           ".$stQuebra;
    $stSql .= " WHERE                                                       ".$stQuebra;
    $stSql .= "     cod_despesa IS NOT NULL                                 ".$stQuebra;

    return $stSql;
}
}
