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
    * Data de Criação: 02/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63106 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.4  2007/10/03 02:50:44  diego
Corrigindo formatação

Revision 1.3  2007/10/02 18:20:03  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.2  2007/10/01 04:41:09  diego
Correção na formatação de data

Revision 1.1  2007/08/09 01:05:49  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 02/08/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAPublicacaoLicitacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAPublicacaoLicitacao()
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
    $stSql = " 
            SELECT tabela.*
                  , ROW_NUMBER() OVER (PARTITION BY num_processo) AS sequencial

              FROM (

              SELECT 1 AS tipo_registro                                                
                     , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                     , licitacao.exercicio_processo::VARCHAR||LPAD(licitacao.cod_entidade::VARCHAR,2,'0')||LPAD(licitacao.cod_modalidade::VARCHAR,2,'0')||licitacao.cod_processo::VARCHAR AS num_processo
                     , TO_CHAR(publicacao_edital.data_publicacao,'dd/mm/yyyy') AS data_publicacao 
                     , sw_cgm.nom_cgm AS nome_veiculo                                   
                     , ".$this->getDado('exercicio')."::VARCHAR||LPAD(".$this->getDado('mes')."::VARCHAR,2,'0') AS competencia         

               FROM licitacao.edital

         INNER JOIN licitacao.publicacao_edital
                 ON publicacao_edital.num_edital = edital.num_edital
                AND publicacao_edital.exercicio = edital.exercicio

         INNER JOIN licitacao.veiculos_publicidade
                 ON veiculos_publicidade.numcgm = publicacao_edital.numcgm

         INNER JOIN sw_cgm
                 ON sw_cgm.numcgm = veiculos_publicidade.numcgm

         INNER JOIN licitacao.licitacao
                 ON licitacao.cod_licitacao = edital.cod_licitacao
                AND licitacao.cod_modalidade = edital.cod_modalidade
                AND licitacao.cod_entidade = edital.cod_entidade
                AND licitacao.exercicio = edital.exercicio_licitacao

              WHERE edital.exercicio = '".$this->getDado('exercicio')."'                
                AND publicacao_edital.data_publicacao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                AND edital.cod_entidade IN (".$this->getDado('entidades').")
              ) AS tabela

          ORDER BY num_processo
            ";

    return $stSql;
}

}
