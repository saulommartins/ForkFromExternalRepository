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
    * Data de Criação: 31/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63272 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoParticipante.class.php" );

/**
  *
  * Data de Criação: 31/07/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAParticipanteLicitacao extends TLicitacaoParticipante
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
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
    $stSql = "  SELECT licitacao.exercicio         
                     , licitacao.exercicio||LPAD(licitacao.cod_entidade::VARCHAR,2,'0')||LPAD(licitacao.cod_modalidade::VARCHAR,2,'0')||LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS cod_licitacao 
                     , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN 1 ELSE 2 END AS pf_pj_part       
                     , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN sw_cgm_pessoa_fisica.cpf       
                              WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL THEN sw_cgm_pessoa_juridica.cnpj       
                              ELSE ''       
                       END AS cpf_cnpj_part       
                      , 3 AS tipo_participante    -- fixado em participante comum      
                      , sw_cgm.nom_cgm AS nome_participante       
                      , pjco.cnpj AS cnpj_consorcio    
                      , ".$this->getDado('exercicio')."::VARCHAR||LPAD(".$this->getDado('mes')."::VARCHAR,2,'0') AS competencia       
                      , CASE WHEN (       
                                    SELECT count(*)       
                                      FROM licitacao.participante_documentos AS pado       
                                     WHERE participante.exercicio      = pado.exercicio       
                                       AND participante.cod_entidade   = pado.cod_entidade       
                                       AND participante.cod_modalidade = pado.cod_modalidade       
                                       AND participante.cod_licitacao  = pado.cod_licitacao       
                                       AND participante.cgm_fornecedor = pado.cgm_fornecedor       
                                  ) >= (       
                                            SELECT count(*)       
                                              FROM licitacao.licitacao_documentos AS lido       
                                             WHERE licitacao.exercicio      = lido.exercicio       
                                               AND licitacao.cod_entidade   = lido.cod_entidade       
                                               AND licitacao.cod_modalidade = lido.cod_modalidade       
                                               AND licitacao.cod_licitacao  = lido.cod_licitacao       
                                 ) THEN 'S'
                                   ELSE 'N'
                          END AS indicador_habilitacao
                        , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                        , 1 AS tipo_registro

                 FROM licitacao.licitacao

           INNER JOIN licitacao.participante
                   ON participante.exercicio      = licitacao.exercicio
                  AND participante.cod_entidade   = licitacao.cod_entidade
                  AND participante.cod_modalidade = licitacao.cod_modalidade
                  AND participante.cod_licitacao  = licitacao.cod_licitacao

           INNER JOIN licitacao.homologacao
                   ON homologacao.cod_licitacao  = licitacao.cod_licitacao
                  AND homologacao.cod_modalidade = licitacao.cod_modalidade
                  AND homologacao.cod_entidade   = licitacao.cod_entidade
                  AND homologacao.exercicio_licitacao = licitacao.exercicio

           INNER JOIN sw_cgm
                   ON sw_cgm.numcgm = participante.cgm_fornecedor

            LEFT JOIN sw_cgm_pessoa_fisica
                   ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm

            LEFT JOIN sw_cgm_pessoa_juridica       
                   ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm

            LEFT JOIN licitacao.participante_consorcio       
                   ON participante.exercicio      = participante_consorcio.exercicio       
                  AND participante.cod_entidade   = participante_consorcio.cod_entidade       
                  AND participante.cod_modalidade = participante_consorcio.cod_modalidade       
                  AND participante.cod_licitacao  = participante_consorcio.cod_licitacao       
                  AND participante.cgm_fornecedor = participante_consorcio.cgm_fornecedor       

            LEFT JOIN sw_cgm_pessoa_juridica as pjco  
                   ON participante_consorcio.cgm_fornecedor = pjco.numcgm    

                WHERE homologacao.cod_entidade IN (".$this->getDado('entidades').")              
                  AND homologacao.exercicio_licitacao = '".$this->getDado('exercicio')."'
                  AND TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                                            AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                  AND licitacao.cod_modalidade NOT IN (8,9)      

                GROUP BY licitacao.exercicio
                       , licitacao.cod_licitacao
                       , cpf_cnpj_part
                       , tipo_participante
                       , nome_participante
                       , pjco.cnpj
                       , competencia
                       , indicador_habilitacao
                       , unidade_gestora
                       , sw_cgm_pessoa_fisica.numcgm
                       , licitacao.cod_entidade
                       , licitacao.cod_modalidade

                ORDER BY cod_licitacao, unidade_gestora, cpf_cnpj_part ";                    
    return $stSql;
}

}

?>