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

class TTBABolsa extends Persistente
    {

    /**
        * Método Construtor
        * @access Private
    */
    public function TTBABolsa() {
      $this->setEstrutura( array() );
      $this->setEstruturaAuxiliar( array() );
      $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaBolsa(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaBolsa().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaBolsa()
    {
        $stSql = " SELECT 1 AS tipo_registro
                        , ( SELECT valor
                              FROM administracao.configuracao_entidade
                             WHERE cod_modulo = 45
                               AND parametro = 'tceba_codigo_unidade_gestora'
                               AND cod_entidade = '".$this->getDado('entidade')."'
                          ) AS unidade_gestora
                        , estagiario_estagio.numero_estagio AS num_bolsa
                        , estagiario_estagio.dt_inicio
                        , estagiario_estagio.dt_final AS dt_final_previsto
                        , estagiario_estagio.objetivos
                        , sw_cgm_pessoa_fisica.cpf
                        , sw_cgm.nom_cgm
                        , '' AS num_doe
                        , '' AS dt_publicacao
                        , 1 AS tipo_moeda
                        , '' AS num_bilhete
                        , estagiario_estagio_bolsa.vl_bolsa AS vl_mensal
                        , '' AS reservado_tcm
                        , estagiario_estagio.funcao
                        , CASE WHEN estagiario_estagio.dt_renovacao > estagiario_estagio.dt_final
                                THEN estagiario_estagio.dt_renovacao
                                ELSE estagiario_estagio.dt_final
                        END AS dt_final_efetivo
                        , area_conhecimento.descricao AS area_formacao
                        , instituicao_ensino.nom_cgm AS instituicao_ensino
                        , vw_orgao_nivel.orgao AS lotacao
                        , ".$this->getDado('periodo')." AS competencia

                    FROM estagio".$this->getDado('entidade_rh').".estagiario_estagio
              INNER JOIN sw_cgm_pessoa_fisica
                      ON sw_cgm_pessoa_fisica.numcgm = estagiario_estagio.cgm_estagiario
              INNER JOIN sw_cgm
                      ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
              INNER JOIN estagio".$this->getDado('entidade_rh').".estagiario_estagio_bolsa
                      ON estagiario_estagio_bolsa.cod_estagio = estagiario_estagio.cod_estagio
                     AND estagiario_estagio_bolsa.cod_curso = estagiario_estagio.cod_curso
                     AND estagiario_estagio_bolsa.cgm_estagiario = estagiario_estagio.cgm_estagiario
                     AND estagiario_estagio_bolsa.cgm_instituicao_ensino = estagiario_estagio.cgm_instituicao_ensino
                     AND estagiario_estagio_bolsa.timestamp = (SELECT MAX(timestamp)
                                                                 FROM estagio".$this->getDado('entidade_rh').".estagiario_estagio_bolsa AS tabela
                                                                WHERE tabela.cgm_instituicao_ensino = estagiario_estagio_bolsa.cgm_instituicao_ensino
                                                                  AND tabela.cgm_estagiario = estagiario_estagio_bolsa.cgm_estagiario
                                                                  AND tabela.cod_curso = estagiario_estagio_bolsa.cod_curso
                                                                  AND tabela.cod_estagio = estagiario_estagio_bolsa.cod_estagio
                                                               )
              INNER JOIN folhapagamento".$this->getDado('entidade_rh').".periodo_movimentacao
                      ON periodo_movimentacao.cod_periodo_movimentacao = estagiario_estagio_bolsa.cod_periodo_movimentacao
              INNER JOIN estagio".$this->getDado('entidade_rh').".curso
                      ON curso.cod_curso = estagiario_estagio.cod_curso
              INNER JOIN estagio".$this->getDado('entidade_rh').".area_conhecimento
                      ON area_conhecimento.cod_area_conhecimento = curso.cod_area_conhecimento
              INNER JOIN sw_cgm AS instituicao_ensino
                      ON instituicao_ensino.numcgm = estagiario_estagio.cgm_instituicao_ensino
              INNER JOIN organograma.vw_orgao_nivel
                      ON vw_orgao_nivel.cod_orgao = estagiario_estagio.cod_orgao

                   WHERE estagiario_estagio.dt_inicio BETWEEN periodo_movimentacao.dt_inicial AND periodo_movimentacao.dt_final
                     AND estagiario_estagio.dt_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')

        ";
        return $stSql;
    }

}
