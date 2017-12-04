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
    * Classe de mapeamento da tabela ORCAMENTO.CLASSIFICACAO_RECEITA
    * Data de Criação: 23/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.04
*/

/*
$Log$
Revision 1.7  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORCAMENTO.CLASSIFICACAO_RECEITA
  * Data de Criação: 23/07/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Marcelo B. Paulino

*/
class TOrcamentoContaReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoContaReceita()
{
    parent::Persistente();
    $this->setTabela('orcamento.conta_receita');

    $this->setCampoCod('cod_conta');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('exercicio','char',true,'04',true,false);
    $this->AddCampo('cod_conta','integer',true,'',true,false);
    $this->AddCampo('descricao','varchar',true,'',false,false);
    $this->AddCampo('cod_norma','integer',true,'',false,true);
    $this->AddCampo('cod_estrutural','varchar',true,'150',false,false);
}
function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT DISTINCT                                                         \n";
    $stSql .= "    D.cod_conta,                                                        \n";
    $stSql .= "    CD.cod_estrutural,                                                  \n";
    $stSql .= "    D.dt_criacao,                                                       \n";
    $stSql .= "    publico.fn_mascarareduzida(CD.cod_estrutural) AS mascara_reduzida,  \n";
    $stSql .= "    CD.descricao                                                        \n";
    $stSql .= "FROM                                                                    \n";
    $stSql .= "    orcamento.receita       AS  D,                                  \n";
    $stSql .= "    orcamento.conta_receita AS CD                                   \n";
    $stSql .= "WHERE                                                                   \n";
    $stSql .= "    D.cod_conta = CD.cod_conta AND                                      \n";
    $stSql .= "    D.exercicio = CD.exercicio                                          \n";

    return $stSql;
}

function montaRecuperaCodEstrutural()
{
    $stSql  = "SELECT DISTINCT                                                         \n";
    $stSql .= "    CD.cod_estrutural,                                                  \n";
    $stSql .= "    CD.cod_conta,                                                       \n";
    $stSql .= "    D.dt_criacao,                                                       \n";
    $stSql .= "    publico.fn_mascarareduzida(CD.cod_estrutural) AS mascara_reduzida,  \n";
    $stSql .= "    CD.descricao                                                        \n";
    $stSql .= "FROM                                                                    \n";
    $stSql .= "    orcamento.receita       AS  D,                                  \n";
    $stSql .= "    orcamento.conta_receita AS CD                                   \n";
    $stSql .= "WHERE                                                                   \n";
    $stSql .= "    D.exercicio = CD.exercicio                                          \n";

    return $stSql;
}

function recuperaDescricaoIRRF(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDescricaoIRRF().$stCondicao.$stOrdem;        
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDescricaoIRRF()
{
    $stSql = "  SELECT *
                FROM orcamento.conta_receita
                JOIN orcamento.receita
                    ON receita.exercicio    = conta_receita.exercicio
                    AND receita.cod_conta   = conta_receita.cod_conta
            ";
    return $stSql;
}

function recuperaListaIRRF(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaIRRF().$stCondicao.$stOrdem;        
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaIRRF()
{
    $stSql = "  SELECT * 
                FROM (
                    SELECT 
                            plano_analitica.cod_plano as cod_receita_irrf
                            ,plano_conta.cod_estrutural
                            ,plano_conta.nom_conta as descricao                                          

                    FROM contabilidade.plano_conta                                 
                                                            
                    INNER JOIN contabilidade.plano_analitica
                         ON plano_analitica.exercicio = plano_conta.exercicio
                        AND plano_analitica.cod_conta = plano_conta.cod_conta
                    
                    WHERE plano_conta.exercicio = '".$this->getDado('exercicio')."'

                    AND NOT EXISTS (SELECT 1 
                                    FROM orcamento.conta_receita
                                    where plano_conta.cod_conta = conta_receita.cod_conta
                                    AND plano_conta.exercicio = conta_receita.exercicio)

                    UNION
                    
                    SELECT 
                            receita.cod_receita as cod_receita_irrf
                            ,conta_receita.cod_estrutural
                            ,conta_receita.descricao
                    FROM orcamento.receita 
                    
                    INNER JOIN orcamento.conta_receita
                         ON conta_receita.cod_conta  = receita.cod_conta
                        AND conta_receita.exercicio = receita.exercicio
                    
                    WHERE conta_receita.exercicio = '".$this->getDado('exercicio')."'
                    
                    ) as foo
                    WHERE 1=1

            ";
    return $stSql;
}


}
