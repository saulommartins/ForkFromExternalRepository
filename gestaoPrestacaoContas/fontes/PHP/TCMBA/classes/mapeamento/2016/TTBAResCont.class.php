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
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 19/10/2007

    * @author Analista: Gelson Wolvowski Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    $Id $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 19/10/2007

  * @author Analista: Gelson Wolvowski
  * @author Desenvolvedor: Henrique Girardi dos Santos

*/

class TTBAResCont extends Persistente
    {

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct () 
    {
        $this->setEstrutura( array() );
        $this->setEstruturaAuxiliar( array() );
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaDadosRescisaoContrato(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montarRecuperaDadosRescisaoContrato().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montarRecuperaDadosRescisaoContrato()
    {
        $stSql .= " SELECT 1 AS tipo_registro
                        , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                        , contrato.numero_contrato AS num_contrato
                        , rescisao_contrato.num_rescisao
                        , SUBSTR(TRIM(rescisao_contrato.motivo), 1, 50) AS motivo_rescisao
                        , TO_CHAR(rescisao_contrato.dt_rescisao, 'dd/mm/yyyy') AS dt_rescisao
                        , TO_CHAR(publicacao_rescisao_contrato.dt_publicacao, 'dd/mm/yyyy') AS dt_publicacao
                        , rescisao_contrato.vlr_multa
                        , rescisao_contrato.vlr_indenizacao
                        , TO_CHAR(rescisao_contrato.dt_rescisao, 'yyyymm') AS competencia
                        , 'N' AS exame_previo
                        , contrato.fundamentacao_legal AS artigo
                        , SUBSTR(TRIM(cgm_imprensa.nom_cgm), 1, 50) AS imprensa_oficial

                    FROM licitacao.contrato

              INNER JOIN licitacao.contrato_licitacao
                      ON contrato_licitacao.num_contrato = contrato.num_contrato
                     AND contrato_licitacao.exercicio = contrato.exercicio
                     AND contrato_licitacao.cod_entidade = contrato.cod_entidade

              INNER JOIN licitacao.rescisao_contrato
                      ON contrato.exercicio = rescisao_contrato.exercicio_contrato
                     AND contrato.cod_entidade = rescisao_contrato.cod_entidade
                     AND contrato.num_contrato = rescisao_contrato.num_contrato

              INNER JOIN licitacao.publicacao_rescisao_contrato
                      ON rescisao_contrato.num_contrato = publicacao_rescisao_contrato.num_contrato
                     AND rescisao_contrato.exercicio_contrato = publicacao_rescisao_contrato.exercicio_contrato
                     AND rescisao_contrato.cod_entidade = publicacao_rescisao_contrato.cod_entidade

               LEFT JOIN sw_cgm AS cgm_imprensa
                      ON publicacao_rescisao_contrato.cgm_imprensa = cgm_imprensa.numcgm

              INNER JOIN licitacao.licitacao
                      ON contrato_licitacao.cod_licitacao = licitacao.cod_licitacao
                     AND contrato_licitacao.cod_modalidade = licitacao.cod_modalidade
                     AND contrato_licitacao.cod_entidade = licitacao.cod_entidade
                     AND contrato_licitacao.exercicio = licitacao.exercicio

               LEFT JOIN licitacao.contrato_anulado
                      ON contrato.num_contrato = contrato_anulado.num_contrato
                     AND contrato.exercicio = contrato_anulado.exercicio
                     AND contrato.cod_entidade = contrato_anulado.cod_entidade     
                     
                   WHERE contrato_anulado.num_contrato IS NULL 
                     AND rescisao_contrato.dt_rescisao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
        ";
        return $stSql;
    }

}
