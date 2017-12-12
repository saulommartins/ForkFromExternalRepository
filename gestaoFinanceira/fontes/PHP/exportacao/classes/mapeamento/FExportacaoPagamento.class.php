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
    * Classe de mapeamento da tabela FN_EXPORTACAO_PAGAMENTO
    * Data de Criação: 18/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.01
*/

/*
$Log$
Revision 1.8  2006/07/05 20:45:59  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FExportacaoPagamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FExportacaoPagamento()
{
    parent::Persistente();
    $this->setTabela('tcers.fn_exportacao_pagamento');

    $this->AddCampo('exercicio_empenho_cod_empenho'         ,'integer',false,''    ,false,false);
    $this->AddCampo('cod_ordem'                             ,'integer',false,''    ,false,false);
    $this->AddCampo('timestamp'                             ,'integer',false,''    ,false,false);
    $this->AddCampo('valor'                                 ,'numeric',false,''    ,false,false);
    $this->AddCampo('sinal_valor'                           ,'varchar',false,''    ,false,false);
    $this->AddCampo('observacao'                            ,'varchar',false,''    ,false,false);
    $this->AddCampo('codigo_operacao'                       ,'integer',false,''    ,false,false);
    $this->AddCampo('debito_codigo_conta_verificacao'       ,'integer',false,''    ,false,false);
    $this->AddCampo('debito_codigo_orgao_unid_orcamentaria' ,'integer',false,''    ,false,false);
    $this->AddCampo('credito_codigo_conta_verificacao'      ,'integer',false,''    ,false,false);
    $this->AddCampo('credito_codigo_orgao_unid_orcamentaria','integer',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "select                                                                   \n";
    $stSql .= "    lpad(tabela.exercicio_empenho,4,'0') as exercicio_empenho,           \n";
    $stSql .= "    lpad(tabela.cod_empenho,7,'0') as cod_empenho,                       \n";
    $stSql .= "    lpad(tabela.cod_entidade,2,'0') as cod_entidade,                     \n";
    $stSql .= "    cod_ordem                                      ,                     \n";
    $stSql .= "    vl_pago                                        ,                     \n";
    $stSql .= "    observacao                                     ,                     \n";
    $stSql .= "    to_char(data_pagamento,'dd/mm/yyyy') as data_pagamento,              \n";
    $stSql .= "    sinal_valor                                 ,                        \n";
    $stSql .= "    cod_operacao                                   ,                     \n";
    $stSql .= "    debito_codigo_conta_verificacao                ,                     \n";
    $stSql .= "    credito_codigo_conta_verificacao                                     \n";
    $stSql .= "  from " . $this->getTabela() . "(                                       \n";
    $stSql .= "                                 '".$this->getDado("stExercicio")    ."',\n";
    $stSql .= "                                 '".$this->getDado("stCodEntidades") ."',\n";
    $stSql .= "                                 '".$this->getDado("dtInicial")      ."',\n";
    $stSql .= "                                 '".$this->getDado("dtFinal")        ."',\n";
    $stSql .= "                                 ''                                      \n";
    $stSql .= "                                 ) as                                    \n";
    $stSql .= "        tabela(                                                          \n";
    $stSql .= "    exercicio_empenho                       char(4),                     \n";
    $stSql .= "    cod_empenho                             integer,                     \n";
    $stSql .= "    cod_entidade                            integer,                     \n";
    $stSql .= "    cod_ordem                               integer,                     \n";
    $stSql .= "    vl_pago                                 numeric,                     \n";
    $stSql .= "    observacao                              varchar,                     \n";
    $stSql .= "    data_pagamento                          date,                        \n";
    $stSql .= "    sinal_valor                             text,                        \n";
    $stSql .= "    cod_operacao                            integer,                     \n";
    $stSql .= "    debito_codigo_conta_verificacao         varchar,                     \n";
    $stSql .= "    credito_codigo_conta_verificacao        varchar,                     \n";
    $stSql .= "    oid                                     oid                          \n";
    $stSql .= "  )                                                                        ";

    return $stSql;
}

/**
 * Função para Exportação de dados do MANAD.
 *
 * @param  Object  $rsRecordSet Objeto RecordSet
 * @param  String  $stCondicao  String de condição do SQL (WHERE)
 * @param  Boolean $boTransacao
 * @return Object  Objeto Erro
 */
function recuperaDadosTransparencia(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro();
    $obConexao   = new Conexao();
    $rsRecordSet = new RecordSet();

    $stSql = $this->montaRecuperaTransparencia();
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

    return $obErro;
}

function montaRecuperaTransparencia()
{
    $stSql  = "select                                                                   \n";
    $stSql .= "    lpad(tabela.exercicio_empenho::varchar,4,'0') as exercicio_empenho,           \n";
    $stSql .= "    (tabela.exercicio_empenho || LPAD(tabela.cod_entidade::varchar,2,'0') || LPAD(tabela.cod_empenho::varchar,7,'0')) as cod_empenho  ,\n";
    #$stSql .= "    lpad(tabela.cod_empenho,7,'0') as cod_empenho,                       \n";
    $stSql .= "    lpad(tabela.cod_entidade::varchar,2,'0') as cod_entidade,                     \n";
    $stSql .= "    cod_ordem                                      ,                     \n";
    $stSql .= "    vl_pago                                        ,                     \n";
    $stSql .= "    observacao                                     ,                     \n";
    $stSql .= "    to_char(data_pagamento,'dd/mm/yyyy') as data_pagamento,              \n";
    $stSql .= "    sinal_valor                                 ,                        \n";
    $stSql .= "    cod_operacao                                   ,                     \n";
    $stSql .= "    debito_codigo_conta_verificacao                ,                     \n";
    $stSql .= "    credito_codigo_conta_verificacao                                     \n";
    $stSql .= "  from " . $this->getTabela() . "(                                       \n";
    $stSql .= "                                 '".$this->getDado("stExercicio")    ."',\n";
    $stSql .= "                                 '".$this->getDado("stCodEntidades") ."',\n";
    $stSql .= "                                 '".$this->getDado("dtInicial")      ."',\n";
    $stSql .= "                                 '".$this->getDado("dtFinal")        ."',\n";
    $stSql .= "                                 ''                                      \n";
    $stSql .= "                                 ) as                                    \n";
    $stSql .= "        tabela(                                                          \n";
    $stSql .= "    exercicio_empenho                       char(4),                     \n";
    $stSql .= "    cod_empenho                             integer,                     \n";
    $stSql .= "    cod_entidade                            integer,                     \n";
    $stSql .= "    cod_ordem                               integer,                     \n";
    $stSql .= "    vl_pago                                 numeric,                     \n";
    $stSql .= "    observacao                              varchar,                     \n";
    $stSql .= "    data_pagamento                          date,                        \n";
    $stSql .= "    sinal_valor                             text,                        \n";
    $stSql .= "    cod_operacao                            integer,                     \n";
    $stSql .= "    debito_codigo_conta_verificacao         varchar,                     \n";
    $stSql .= "    credito_codigo_conta_verificacao        varchar,                     \n";
    $stSql .= "    oid                                     oid                          \n";
    $stSql .= "  )                                                                        ";

    return $stSql;
}

/**
 * Função para Exportação de dados do MANAD.
 *
 * @param  Object  $rsRecordSet Objeto RecordSet
 * @param  String  $stCondicao  String de condição do SQL (WHERE)
 * @param  Boolean $boTransacao
 * @return Object  Objeto Erro
 */
function recuperaDadosMANAD(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro();
    $obConexao   = new Conexao();
    $rsRecordSet = new RecordSet();

    $stSql = $this->montaRecuperaDadosMANAD();
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

    return $obErro;
}

function montaRecuperaDadosMANAD()
{
    $stSql  = " SELECT 'L150' as reg                                                     \n";
    // $stSql .= "      , (tabela.exercicio || LPAD(tabela.cod_entidade::varchar,2,0) || LPAD(tabela.cod_empenho::varchar,7,0)) as nm_emp \n";
    $stSql .= "      , tabela.cod_empenho as nm_emp                                     \n";
    $stSql .= "      , 0 as nm_pgto                                                     \n"; // PENDENTE
    $stSql .= "      , to_char(data_pagamento,'dd/mm/yyyy') as dt_pgto                  \n";
    $stSql .= "      , replace(vl_pgto,'.',',') as vl_pgto                                                          \n";
    $stSql .= "      , CASE WHEN sinal_valor = '-'  THEN        \n";
    $stSql .= "                       'C'                     \n";
    $stSql .= "                 ELSE                                              \n";
    $stSql .= "                       'D'                     \n";
    $stSql .= "            END AS ind_deb_cred                                   \n";
    $stSql .= "      , observacao AS hist_pgto                                          \n";
    $stSql .= "      , debito_codigo_conta_verificacao AS cta_debito                    \n";
    $stSql .= "      , 0 AS cod_org_un_deb                                              \n"; // PENDENTE
    $stSql .= "      , credito_codigo_conta_verificacao AS cta_credito                  \n";
    $stSql .= "      , 0 AS cod_org_un_cre                                              \n"; // PENDENTE
    $stSql .= "      , lpad(tabela.cod_entidade::varchar,2,'0') as cod_entidade         \n";
    $stSql .= "      , cod_ordem                                                        \n";
    $stSql .= "      , sinal_valor                                                      \n";
    $stSql .= "      , cod_operacao                                                     \n";
    $stSql .= "  from " . $this->getTabela() . "(                                       \n";
    $stSql .= "                                 '".$this->getDado("stExercicio")    ."',\n";
    $stSql .= "                                 '".$this->getDado("stCodEntidades") ."',\n";
    $stSql .= "                                 '".$this->getDado("dtInicial")      ."',\n";
    $stSql .= "                                 '".$this->getDado("dtFinal")        ."',\n";
    $stSql .= "                                 ''                                      \n";
    $stSql .= "                                 ) as                                    \n";
    $stSql .= "        tabela(                                                          \n";
    $stSql .= "    exercicio                               char(4),                     \n";
    $stSql .= "    cod_empenho                             integer,                     \n";
    $stSql .= "    cod_entidade                            integer,                     \n";
    $stSql .= "    cod_ordem                               integer,                     \n";
    $stSql .= "    vl_pgto                                 numeric,                     \n";
    $stSql .= "    observacao                              varchar,                     \n";
    $stSql .= "    data_pagamento                          date,                        \n";
    $stSql .= "    sinal_valor                             text,                        \n";
    $stSql .= "    cod_operacao                            integer,                     \n";
    $stSql .= "    debito_codigo_conta_verificacao         varchar,                     \n";
    $stSql .= "    credito_codigo_conta_verificacao        varchar,                     \n";
    $stSql .= "    oid                                     oid                          \n";
    $stSql .= "  )                                                                        ";

    return $stSql;
}
}
