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
    * Data de Criação: 17/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62823 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBAConvidados extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCMBAConvidados()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
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
        SELECT 1 AS tipo_registro
             , licitacao.exercicio || LPAD(licitacao.cod_entidade::VARCHAR,2,'0') || LPAD(licitacao.cod_modalidade::VARCHAR,2,'0')|| LPAD(licitacao.cod_licitacao::VARCHAR ,4,'0') AS num_processo
             , ".$this->getDado('inCodGestora')." AS unidade_gestora            
             , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL
                     THEN 1    
                     ELSE 2    
               END AS tipo_pessoa
            , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                     THEN sw_cgm_pessoa_fisica.cpf    
                       WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                         THEN sw_cgm_pessoa_juridica.cnpj    
                         ELSE ''    
               END AS documento
             , sw_cgm.nom_cgm AS convidado
             , participante.dt_inclusao AS dt_recebimento_convite
            , TO_CHAR(participante.dt_inclusao, 'yyyymm') AS competencia
     
          FROM licitacao.cotacao_licitacao
          
    INNER JOIN licitacao.licitacao
            ON licitacao.cod_licitacao  = cotacao_licitacao.cod_licitacao
           AND licitacao.cod_modalidade = cotacao_licitacao.cod_modalidade
           AND licitacao.cod_entidade   = cotacao_licitacao.cod_entidade
           AND licitacao.exercicio      = cotacao_licitacao.exercicio_licitacao
          
    INNER JOIN licitacao.participante
            ON participante.cod_licitacao  = cotacao_licitacao.cod_licitacao
           AND participante.cgm_fornecedor = cotacao_licitacao.cgm_fornecedor
           AND participante.cod_modalidade = cotacao_licitacao.cod_modalidade
           AND participante.exercicio      = cotacao_licitacao.exercicio_licitacao
           AND participante.cod_entidade   = cotacao_licitacao.cod_entidade
           
    INNER JOIN sw_cgm
            ON cotacao_licitacao.cgm_fornecedor = sw_cgm.numcgm
     
     LEFT JOIN sw_cgm_pessoa_fisica
            ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
     
     LEFT JOIN sw_cgm_pessoa_juridica
            ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm
     
         WHERE cotacao_licitacao.exercicio_licitacao = '".$this->getDado('stExercicio')."'
           AND participante.dt_inclusao BETWEEN TO_DATE('".$this->getDado('stDataInicial')."', 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('stDataFinal')."', 'dd/mm/yyyy')
           AND cotacao_licitacao.cod_entidade IN (".$this->getDado('stEntidades').")
           AND licitacao.cod_modalidade NOT IN (8, 9)
           
      GROUP BY num_processo
             , cotacao_licitacao.exercicio_licitacao    
             , cotacao_licitacao.cod_licitacao    
             , sw_cgm_pessoa_fisica.cpf    
             , sw_cgm_pessoa_juridica.cnpj    
             , sw_cgm_pessoa_fisica.numcgm    
             , sw_cgm.nom_cgm
             , dt_recebimento_convite
             , licitacao.exercicio_processo 
             , licitacao.cod_entidade 
             , licitacao.cod_modalidade 
             , licitacao.cod_processo
             , licitacao.cod_tipo_objeto
             , licitacao.registro_precos ";
    return $stSql;
}

}

?>