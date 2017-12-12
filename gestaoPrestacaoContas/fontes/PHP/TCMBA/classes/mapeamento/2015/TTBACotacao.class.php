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
    * Extensão da Classe de mapeamento
    * Data de Criação: 18/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63145 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.3  2007/10/07 22:31:11  diego
Corrigindo formatação e informações

Revision 1.2  2007/10/02 18:17:17  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/09/21 01:47:58  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 18/09/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBACotacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBACotacao()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
    $this->setDado('exercicio', Sessao::getExercicio() );
}

function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosTribunal()
{
      $stSql = " SELECT 1 AS tipo_registro
                      , coli.exercicio_licitacao AS exercicio
                      ,coli.exercicio_licitacao||LPAD(coli.cod_entidade::VARCHAR,2,'0')||LPAD(coli.cod_modalidade::VARCHAR,2,'0')||LPAD(coli.cod_licitacao::VARCHAR,4,'0') AS cod_licitacao
                      , CASE WHEN pf.cpf IS NOT NULL THEN pf.cpf    
                             WHEN pj.cnpj IS NOT NULL THEN pj.cnpj    
                             ELSE ''    
                        END AS cpf_cnpj    
                      , CASE WHEN pf.numcgm IS NOT NULL THEN 1    
                             ELSE 2    
                        END AS pf_pj    
                      , cgm.nom_cgm    
                      , coli.cod_item    
                      , SUM(cofi.vl_cotacao) AS vl_cotacao    
                      , CASE WHEN coji.ordem = 1 THEN 1
                             ELSE 2
                        END AS status
                      , ".$this->getDado('unidade_gestora')." AS unidade_gestora   \n";
            if (trim($this->getDado('inMes'))) {
              $stSql .= ", '".$this->getDado('exercicio').$this->getDado('inMes')."' AS competencia     \n";
            }else {
              $stSql .= ", '' AS competencia  \n";
              }
              $stSql .= " FROM compras.cotacao_fornecedor_item  AS cofi    
             INNER JOIN compras.julgamento_item AS coji    
                     ON ( coji.exercicio      = cofi.exercicio    
                          AND coji.cod_cotacao    = cofi.cod_cotacao    
                          AND coji.cod_item       = cofi.cod_item    
                          AND coji.cgm_fornecedor = cofi.cgm_fornecedor    
                          AND coji.lote           = cofi.lote    
                        )    
             INNER JOIN licitacao.cotacao_licitacao   as coli    
                     ON ( cofi.exercicio      = coli.exercicio_cotacao    
                          AND cofi.cod_cotacao    = coli.cod_cotacao    
                          AND cofi.cod_item       = coli.cod_item    
                          AND cofi.cgm_fornecedor = coli.cgm_fornecedor    
                          AND cofi.lote           = coli.lote    
                        )    
             INNER JOIN sw_cgm  as cgm    
                     ON ( coli.cgm_fornecedor = cgm.numcgm )    
              LEFT JOIN sw_cgm_pessoa_fisica as pf    
                     ON ( cgm.numcgm = pf.numcgm )    
              LEFT JOIN sw_cgm_pessoa_juridica as pj    
                     ON ( cgm.numcgm = pj.numcgm )    
                  WHERE coli.exercicio_licitacao = '".$this->getDado('exercicio')."'
                    AND coli.cod_modalidade NOT IN (8,9)\n"; 
        if (trim($this->getDado('stEntidades'))) {
            $stSql .= " AND coli.cod_entidade IN (".$this->getDado('stEntidades').")              \n";
           }
        if (trim($this->getDado('inMes'))) {
            $stSql .= "  AND TO_CHAR( cofi.timestamp,'mm') = ('".$this->getDado('inMes')."')              \n";
           }    
            $stSql .= " GROUP BY coli.exercicio_licitacao 
                               , coli.cod_licitacao
                               , coli.cod_entidade
                               , coli.cod_modalidade
                               , pf.cpf    
                               , pj.cnpj    
                               , pf.numcgm    
                               , cgm.nom_cgm    
                               , coli.cod_item    
                               , coji.ordem    ";
    
        return $stSql;
}

}
