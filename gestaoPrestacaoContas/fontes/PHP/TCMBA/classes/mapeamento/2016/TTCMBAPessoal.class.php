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
/*
    * Página de Mapeamento - Exportação Arquivos TCM-BA- Pessoal.txt
    * Data de Criação:       05/10/2015
    * @author Analista:      Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos
    * 
    * $Id:$
*/
include_once CLA_PERSISTENTE;

class TTCMBAPessoal extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaPessoal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaPessoal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao,$obConexao );
        return $obErro;
    }

    public function montaRecuperaPessoal()
    {
        $stSql=" SELECT DISTINCT
                        1 AS tipo_registro
                        ,  ".$this->getDado('unidade_gestora')." AS unidade_gestora
                        , contrato_servidor.cod_cargo
                        , CASE WHEN tipos_atos_pessoal IS NOT NULL THEN                
                                tipos_atos_pessoal.tipo_ato::integer
                            ELSE
                                tipo_ato_pessoal.cod_tipo
                        END AS tipo_ato
                        , contrato.registro AS matricula
                        , contrato_servidor_nomeacao_posse.dt_admissao
                        , sw_cgm.nom_cgm AS nome_servidor
                        , sw_cgm_pessoa_fisica.cpf
                        , sw_cgm_pessoa_fisica.dt_nascimento
                        ,CASE WHEN tipo_ato_pessoal.cod_tipo NOT IN (4,7,22,24,26,32,23,13,14,15,25,35) THEN 
                                 norma.num_norma 
                             ELSE
                                 ''
                         END AS numero_ato
                        , norma.dt_publicacao as data_ato
                        , '' AS imprensa_oficial
                        , edital.cod_edital as numero_concurso
                        ,CASE WHEN tipo_ato_pessoal.cod_tipo = 4 THEN 
                                norma.num_norma 
                            ELSE
                                ''
                        END AS numero_ato
                        , '' AS reservado_tcm
                        , '' AS imprensa_oficial_ato                        
                        ,CASE WHEN de_para_tipo_cargo_tcmba.cod_tipo_cargo_tce = 2 THEN
                                1
                            ELSE
                                2
                        END AS cargo_efetivo
                        , '".$this->getDado('competencia')."' AS competencia
                        , '' AS fundamentacao_legal
                        , '' AS reservado_tcm2
                        , de_para_tipo_cargo_tcmba.cod_tipo_regime_tce AS tipo_regime
                        , CASE WHEN assentamento_assentamento.cod_motivo = 7 THEN
                                 assentamento_gerado.periodo_inicial
                        END AS data_efetivacao
                        ,CASE WHEN ( SELECT recuperarSituacaoDoContrato(contrato_servidor.cod_contrato
                                                                        , contrato_servidor_periodo.cod_periodo_movimentacao
                                                                        ,'".$this->getDado('entidades')."'
                                                                       ) 
                                    ) = 'A' 
                            THEN
                                1
                            ELSE
                                2
                        END AS indicador_acumulo_cargo
                        , '' AS orgao_entidade
                        ,CASE WHEN de_para_tipo_cargo_tcmba.cod_tipo_cargo_tce = 4 THEN
                                cargo.descricao
                            ELSE
                                ''
                        END AS funcao_servidor_temporario
                        , '' AS reservado_tcm3
                        , cargo.cod_cargo||' - '||cargo.descricao as funcao_desempenhada
                        , orgao_descricao.descricao
                        ,CASE WHEN adido_cedido.indicativo_onus = 'c' THEN
                                '1'
                            ELSE
                                '2'
                        END AS onus_cedente
                        , adido_cedido.cgm_cedente_cessionario||' - '||sw_cgm.nom_cgm AS nome_cedente
                        , '' AS numero_processo
                        , recuperaCargoAcumuladoServidor(contrato_servidor.cod_contrato
                                                        ,servidor_contrato_servidor.cod_servidor
                                                        ,'".$this->getDado('periodo_movimentacao')."'
                                                        ,'".$this->getDado('entidades')."'
                        ) AS nome_cargo_acumulado 
                        ,CASE WHEN tipo_ato_pessoal.cod_tipo <> 4 THEN
                                norma.dt_publicacao                
                        END AS data_publicacao
                        ,CASE WHEN tipo_ato_pessoal.cod_tipo = 4 THEN
                                norma.dt_publicacao
                        END AS data_publicacao_afastamento
                        , '' AS justificativa_contratacao
                        , CASE WHEN assentamento_assentamento.cod_motivo = 7 THEN
                                assentamento_gerado.periodo_final
                        END AS data_termino_contrato
                        , '' AS cargo_anterior
                        , '' AS processo_numero_tcm
                        , '' AS processo_digito_tcm
                        , '' AS processo_ano_tcm
                        ,CASE WHEN adido_cedido.indicativo_onus = 'c' THEN
                                cargo.cod_cargo||' - '||cargo.descricao
                        END AS cargo_origem_destino_cessao
                        , 'N' AS status_concurso
                        , 2 AS anterior_siga

                    FROM pessoal".$this->getDado('entidades').".contrato

               LEFT JOIN pessoal".$this->getDado('entidades').".contrato_pensionista
                      ON contrato_pensionista.cod_contrato = contrato.cod_contrato

               INNER JOIN pessoal".$this->getDado('entidades').".contrato_servidor
                      ON contrato.cod_contrato = contrato_servidor.cod_contrato

               INNER JOIN ultimo_contrato_servidor_nomeacao_posse('".$this->getDado('entidades')."',".$this->getDado('periodo_movimentacao').") as contrato_servidor_nomeacao_posse
                    ON contrato_servidor_nomeacao_posse.cod_contrato = contrato_servidor.cod_contrato

               INNER JOIN pessoal".$this->getDado('entidades').".servidor_contrato_servidor
                      ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato

              INNER JOIN pessoal".$this->getDado('entidades').".servidor
                     ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

             INNER JOIN sw_cgm
                     ON sw_cgm.numcgm = servidor.numcgm

             INNER JOIN sw_cgm_pessoa_fisica
                     ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

              INNER JOIN folhapagamento".$this->getDado('entidades').".contrato_servidor_periodo
                     ON contrato_servidor_periodo.cod_contrato = contrato.cod_contrato
 
              LEFT JOIN normas.norma
                     ON norma.cod_norma = contrato_servidor.cod_norma

              LEFT JOIN pessoal".$this->getDado('entidades').".assentamento_gerado_contrato_servidor
                     ON assentamento_gerado_contrato_servidor.cod_contrato = contrato.cod_contrato

              LEFT JOIN (SELECT assentamento_gerado.*
                         FROM pessoal".$this->getDado('entidades').".assentamento_gerado
                         INNER JOIN(SELECT MAX(max_assentamento_gerado.timestamp) as timestamp
                                         , max_assentamento_gerado.cod_assentamento_gerado                   
                                      FROM pessoal".$this->getDado('entidades').".assentamento_gerado as max_assentamento_gerado
                                      WHERE max_assentamento_gerado.timestamp <= (ultimoTimestampPeriodoMovimentacao('".$this->getDado('periodo_movimentacao')."'
                                                                                                                     ,'".$this->getDado('entidades')."')
                                                                                                                     )::timestamp
                                      GROUP BY max_assentamento_gerado.cod_assentamento_gerado
                                   ) as max_assentamento_gerado
                              ON max_assentamento_gerado.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                             AND max_assentamento_gerado.timestamp = assentamento_gerado.timestamp
                         )as assentamento_gerado
                     ON assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado
                    AND (TO_CHAR(assentamento_gerado.periodo_inicial,'yyyymm') <= '".$this->getDado('competencia')."'
                         AND 
                        (TO_CHAR(assentamento_gerado.periodo_final,'yyyymm') >= '".$this->getDado('competencia')."' ) OR assentamento_gerado.periodo_final is null)
    
            LEFT JOIN pessoal".$this->getDado('entidades').".assentamento_assentamento
                   ON assentamento_assentamento.cod_assentamento = assentamento_gerado.cod_assentamento                    

           LEFT JOIN pessoal".$this->getDado('entidades').".tcmba_assentamento_ato_pessoal
                   ON tcmba_assentamento_ato_pessoal.cod_assentamento = assentamento_assentamento.cod_assentamento

           LEFT JOIN tcmba.tipo_ato_pessoal
                   ON tipo_ato_pessoal.cod_tipo = tcmba_assentamento_ato_pessoal.cod_tipo_ato_pessoal

            LEFT JOIN concurso.candidato
                   ON candidato.numcgm = sw_cgm_pessoa_fisica.numcgm
 
            LEFT JOIN concurso.concurso_candidato
                   ON concurso_candidato.cod_candidato = candidato.cod_candidato
 
            LEFT JOIN concurso.edital
                   ON edital.cod_edital = concurso_candidato.cod_edital
 
            INNER JOIN pessoal".$this->getDado('entidades').".sub_divisao
                    ON sub_divisao.cod_sub_divisao = contrato_servidor.cod_sub_divisao
 
            LEFT JOIN pessoal".$this->getDado('entidades').".de_para_tipo_cargo_tcmba
                   ON de_para_tipo_cargo_tcmba.cod_sub_divisao = sub_divisao.cod_sub_divisao
 
            INNER JOIN ultimo_contrato_servidor_funcao('".$this->getDado('entidades')."',".$this->getDado('periodo_movimentacao').") as contrato_servidor_funcao
                   ON contrato_servidor_funcao.cod_contrato = contrato_servidor.cod_contrato
 
            INNER JOIN pessoal".$this->getDado('entidades').".cargo 
                   ON cargo.cod_cargo = contrato_servidor_funcao.cod_cargo
    
            INNER JOIN ultimo_contrato_servidor_orgao('".$this->getDado('entidades')."',".$this->getDado('periodo_movimentacao').") as contrato_servidor_orgao
                    ON contrato_servidor_orgao.cod_contrato = contrato_servidor.cod_contrato
 
            INNER JOIN organograma.orgao_descricao
                   ON orgao_descricao.cod_orgao = contrato_servidor_orgao.cod_orgao
            
            LEFT JOIN pessoal".$this->getDado('entidades').".adido_cedido
                   ON adido_cedido.cod_contrato = contrato_servidor.cod_contrato

            LEFT JOIN (SELECT * 
                       FROM tcmba.recuperaAtosDePessoalTCMBA('".$this->getDado('periodo_movimentacao')."'
                                                            , '".$this->getDado('competencia')."'
                                                            , '".$this->getDado('entidades')."'
                                                            )
                       AS resultado(
                                   tipo_ato     INTEGER
                                   ,cod_contrato INTEGER
                                   )
                      )as tipos_atos_pessoal
                   ON tipos_atos_pessoal.cod_contrato::integer = contrato.cod_contrato
            
            WHERE contrato_servidor_periodo.cod_periodo_movimentacao = '".$this->getDado('periodo_movimentacao')."'

            ORDER BY sw_cgm.nom_cgm
                            
            ";
        
        return $stSql;
    }
}
