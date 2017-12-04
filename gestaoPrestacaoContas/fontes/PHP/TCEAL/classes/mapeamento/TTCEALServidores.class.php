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
    * Extensão da Classe de Mapeamento TTCEALServidores
    *
    *
    * Data de Criação: 06/06/2014
    *
    * @author: Jean Silva
    *
    $Id: TTCEALServidores.class.php 60564 2014-10-29 20:38:09Z carlos.silva $
    *
    *
    * @ignore
    *
*/
class TTCEALServidores extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALServidores()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaCredor.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaServidores(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaServidores().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        
        return $obErro;
    }

   public function montaRecuperaServidores()
    {
         $stSql = "
                   SELECT CASE WHEN codigo_ua <> '' THEN codigo_ua
                               ELSE '0000'
                          END AS codigo_ua_verdadeiro
                        , servidores.*
                     FROM (
                            SELECT ( SELECT PJ.cnpj
                                       FROM orcamento.entidade
                                 INNER JOIN sw_cgm_pessoa_juridica as PJ
                                         ON PJ.numcgm = entidade.numcgm
                                      WHERE entidade.exercicio = '".$this->getDado('exercicio')."'
                                        AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."
                                   ) AS cod_und_gestora
                                 , ( SELECT CASE WHEN configuracao_entidade.valor <> '' THEN valor ELSE '0000' END AS valor
                                       FROM administracao.configuracao_entidade
                                      WHERE configuracao_entidade.cod_modulo = 62
                                        AND configuracao_entidade.exercicio ='".$this->getDado('exercicio')."'
                                        AND configuracao_entidade.parametro like 'tceal_configuracao_unidade_autonoma'
                                        AND configuracao_entidade.cod_entidade =  ".$this->getDado('cod_entidade')."
                                   ) AS codigo_ua
                                 , ( SELECT SUM(valor)
                                       FROM recuperarEventosCalculados(1,'".$this->getDado('cod_periodo_movimentacao')."',servidores.cod_contrato,0,'','')
                                      WHERE natureza ='P'
                                   ) AS salario_bruto
                                 , ( SELECT SUM(valor) - ( SELECT SUM(valor)
                                                            FROM recuperarEventosCalculados(1,'".$this->getDado('cod_periodo_movimentacao')."',servidores.cod_contrato,0,'','')
                                                           WHERE natureza ='D'
                                                        )
                                       FROM recuperarEventosCalculados(1,'".$this->getDado('cod_periodo_movimentacao')."',servidores.cod_contrato,0,'','')
                                      WHERE natureza ='P'
                                   ) AS salario_liquido
                                 , *
                                 
                              FROM (
                                        SELECT contrato.registro as matricula
                                             , servidor_pensionista.*
                                          FROM pessoal".$this->getDado('entidade').".contrato
                                             , (
                                                -- Inicio consulta servidores (ativos, aposentados e rescindidos)
                                                   SELECT contrato_servidor.cod_contrato as cod_contrato
                                                        , sw_cgm_pessoa_fisica.cpf as cpf
                                                        , sw_cgm.nom_cgm as nome
                                                        , TO_CHAR(sw_cgm_pessoa_fisica.dt_nascimento,'dd/mm/yyyy') as data_nascimento
                                                        , servidor.nome_mae
                                                        , servidor.nome_pai
                                                        , COALESCE(regexp_replace(sw_cgm_pessoa_fisica.servidor_pis_pasep, '[.|\-]', '', 'g'),'00000000000') as pis_pasep
                                                        , servidor.nr_titulo_eleitor as titulo_eleitoral
                                                        , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_admissao::date,'dd/mm/yyyy') as dt_admissao
                                                        , de_para_tipo_cargo.cod_tipo_cargo_tce as cod_vinculo_empregaticio
                                                        , CASE WHEN previdencia.cod_regime_previdencia = 1 THEN 2
                                                               WHEN previdencia.cod_regime_previdencia = 2 THEN 1
                                                          END AS cod_regime_previdenciario
                                                        , CASE WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 5 THEN 1
                                                               WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 4 THEN 2
                                                               WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 7 THEN 3
                                                               WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 6 THEN 4
                                                               WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 9 THEN 5
                                                               WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 8 THEN 6
                                                               ELSE 7
                                                          END AS cod_escolaridade
                                                        , CASE WHEN adido_cedido.tipo_cedencia= 'a' THEN 2
                                                               WHEN adido_cedido.tipo_cedencia= 'c' THEN 1
                                                               ELSE 0
                                                          END AS sob_cessao
                                                        , cgm_cessionario.cnpj as cnpj_entidade
                                                        , cgm_cessionario.nom_fantasia as nome_entidade
                                                        , adido_cedido.dt_inicial as data_cessao
                                                        , adido_cedido.dt_final as data_retorno_cessao
                                                        , valor_consignada as margem_consignada
                                                        , (SELECT pc.codigo
                                                             FROM pessoal.cbo AS pc
                                                            WHERE pc.cod_cbo = ultimo_contrato_servidor_cargo_cbo.cod_cbo
                                                          ) AS cbo
                                                        , ultimo_contrato_servidor_cargo_cbo.cod_cargo
                                                        , (SELECT cod_cargo FROM pessoal.cargo WHERE cod_cargo = ultimo_contrato_servidor_funcao.cod_cargo) as cod_funcao
                                                        , REPLACE(vw_orgao_nivel.orgao,'.','') as cod_lotacao 
                                                      
                                                     FROM pessoal".$this->getDado('entidade').".contrato_servidor

                                               INNER JOIN pessoal".$this->getDado('entidade').".servidor_contrato_servidor
                                                       ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato

                                               INNER JOIN pessoal".$this->getDado('entidade').".servidor
                                                       ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor

                                               INNER JOIN sw_cgm
                                                       ON servidor.numcgm = sw_cgm.numcgm

                                               INNER JOIN sw_cgm_pessoa_fisica
                                                       ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                                               INNER JOIN pessoal".$this->getDado('entidade').".contrato_servidor_nomeacao_posse
                                                       ON contrato_servidor_nomeacao_posse.cod_contrato = contrato_servidor.cod_contrato

                                               INNER JOIN ultimo_contrato_servidor_orgao('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_servidor_orgao
                                                       ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_orgao.cod_contrato

                                               INNER JOIN organograma.vw_orgao_nivel
                                                       ON ultimo_contrato_servidor_orgao.cod_orgao = vw_orgao_nivel.cod_orgao

                                               INNER JOIN ultimo_contrato_servidor_nomeacao_posse('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_servidor_nomeacao_posse
                                                       ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_nomeacao_posse.cod_contrato

                                               INNER JOIN ultimo_contrato_servidor_funcao('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_servidor_funcao
                                                       ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_funcao.cod_contrato

                                               INNER JOIN ultimo_contrato_servidor_regime_funcao('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_servidor_regime_funcao
                                                       ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_regime_funcao.cod_contrato

                                               INNER JOIN ultimo_contrato_servidor_padrao('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_servidor_padrao
                                                       ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_padrao.cod_contrato

                                               INNER JOIN ultimo_contrato_servidor_salario('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_servidor_salario
                                                       ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_salario.cod_contrato

                                               INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_servidor_sub_divisao_funcao
                                                       ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_sub_divisao_funcao.cod_contrato

                                               INNER JOIN tceal.de_para_tipo_cargo
                                                       ON de_para_tipo_cargo.cod_sub_divisao = ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao
                                                      AND de_para_tipo_cargo.cod_entidade = ". $this->getDado('cod_entidade')."

                                               INNER JOIN ultimo_contrato_servidor_previdencia('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_servidor_previdencia
                                                       ON ultimo_contrato_servidor_previdencia.cod_contrato = contrato_servidor.cod_contrato
                                                      AND ultimo_contrato_servidor_previdencia.bo_excluido ='f'

                                               INNER JOIN folhapagamento".$this->getDado('entidade').".previdencia
                                                       ON previdencia.cod_previdencia  =  ultimo_contrato_servidor_previdencia.cod_previdencia

                                               INNER JOIN ultimo_contrato_servidor_cargo_cbo('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_servidor_cargo_cbo
                                                       ON ultimo_contrato_servidor_cargo_cbo.cod_cargo = contrato_servidor.cod_cargo

                                                LEFT JOIN pessoal".$this->getDado('entidade').".adido_cedido
                                                       ON adido_cedido.cod_contrato= contrato_servidor.cod_contrato

                                                LEFT JOIN sw_cgm_pessoa_juridica as cgm_cessionario
                                                       ON cgm_cessionario.numcgm = adido_cedido.cgm_cedente_cessionario

                                                LEFT JOIN pessoal".$this->getDado('entidade').".contrato_servidor_especialidade_cargo
                                                       ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato

                                                LEFT JOIN pessoal".$this->getDado('entidade').".especialidade
                                                       ON especialidade.cod_especialidade = contrato_servidor_especialidade_cargo.cod_especialidade

                                                LEFT JOIN ultimo_contrato_servidor_local('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_servidor_local
                                                       ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_local.cod_contrato

                                                LEFT JOIN organograma.local
                                                       ON local.cod_local = ultimo_contrato_servidor_local.cod_local

                                                LEFT JOIN ultimo_contrato_servidor_especialidade_funcao('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_servidor_especialidade_funcao
                                                       ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_especialidade_funcao.cod_contrato

                                                LEFT JOIN ultimo_contrato_servidor_caso_causa('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_servidor_caso_causa
                                                       ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_caso_causa.cod_contrato
                                                      AND ultimo_contrato_servidor_caso_causa.dt_rescisao <= to_date((select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('cod_periodo_movimentacao')."))::varchar, 'yyyy-mm-dd')
                                                
                                                LEFT JOIN 
                                                        (     SELECT registro_evento_periodo.cod_contrato
                                                                   , SUM(evento_calculado.valor) AS valor_consignada
                                                                FROM folhapagamento".$this->getDado('entidade').".evento
                                                                JOIN 
                                                                   ( SELECT unnest(string_to_array(valor, ',', ''))::integer AS valor FROM administracao.configuracao
                                                                      WHERE configuracao.cod_modulo = 62
                                                                        AND configuracao.parametro  = 'tceal_config_margem_consignada".$this->getDado('entidade')."'
                                                                        AND configuracao.exercicio  = '".$this->getDado('exercicio')."'
                                                                   ) AS config_consignada
                                                                  ON config_consignada.valor= evento.cod_evento
                                                                  
                                                                JOIN folhapagamento".$this->getDado('entidade').".registro_evento
                                                                  ON registro_evento.cod_evento=evento.cod_evento
                                                        
                                                                JOIN folhapagamento".$this->getDado('entidade').".registro_evento_periodo
                                                                  ON registro_evento_periodo.cod_periodo_movimentacao=".$this->getDado('cod_periodo_movimentacao')."
                                                                 AND registro_evento_periodo.cod_registro=registro_evento.cod_registro
                                                        
                                                                JOIN folhapagamento".$this->getDado('entidade').".evento_calculado
                                                                  ON evento_calculado.cod_evento=registro_evento.cod_evento
                                                                 AND evento_calculado.cod_registro=registro_evento.cod_registro
                                                                 
                                                            GROUP BY registro_evento_periodo.cod_contrato
                                                        ) AS evento_final
                                                       ON evento_final.cod_contrato=contrato_servidor.cod_contrato

                                                    WHERE ultimo_contrato_servidor_caso_causa.dt_rescisao IS NULL
                                                       OR TO_CHAR(ultimo_contrato_servidor_caso_causa.dt_rescisao,'yyyymmdd')::DATE >= (select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('cod_periodo_movimentacao').")::DATE)

                                                -- Fim consulta servidores (ativos, aposentados e rescindidos)

                                                    UNION

                                                -- Inicio consulta pensionista
                                                   SELECT contrato_pensionista.cod_contrato AS cod_contrato
                                                        , sw_cgm_pessoa_fisica.cpf AS cpf
                                                        , sw_cgm.nom_cgm as nome
                                                        , TO_CHAR(sw_cgm_pessoa_fisica.dt_nascimento,'dd/mm/yyyy') as data_nascimento
                                                        , 'Não Informado' as nome_mae
                                                        , 'Não Informado' as nome_pai
                                                        , COALESCE(regexp_replace(sw_cgm_pessoa_fisica.servidor_pis_pasep, '[.|\-]', '', 'g'),'00000000000') as pis_pasep
                                                        , servidor.nr_titulo_eleitor as titulo_eleitoral
                                                        , to_char(ultimo_contrato_pensionista_nomeacao_posse.dt_admissao::date, 'ddmmyyyy') as dt_admissao
                                                        , de_para_tipo_cargo.cod_tipo_cargo_tce as cod_vinculo_empregaticio
                                                        , CASE WHEN previdencia.cod_regime_previdencia = 1 THEN 2 WHEN previdencia.cod_regime_previdencia = 2 THEN 1 END AS cod_regime_previdenciario
                                                        , CASE WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 5 THEN 1
                                                                WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 4 THEN 2
                                                                WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 7 THEN 3
                                                                WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 6 THEN 4
                                                                WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 9 THEN 5
                                                                WHEN sw_cgm_pessoa_fisica.cod_escolaridade = 8 THEN 6
                                                                ELSE 7
                                                            END AS cod_escolaridade
                                                        , NULL AS sob_cessao
                                                        , '' as cnpj_entidade
                                                        , '' as nome_entidade
                                                        , NULL as data_cessao
                                                        , NULL as data_retorno_cessao
                                                        , valor_consignada as margem_consignada
                                                        , (SELECT pc.codigo
                                                             FROM pessoal.cbo AS pc
                                                            WHERE pc.cod_cbo = ultimo_contrato_servidor_cargo_cbo.cod_cbo
                                                          ) AS cbo
                                                        , ultimo_contrato_servidor_cargo_cbo.cod_cargo
                                                        , (SELECT cod_cargo FROM pessoal.cargo WHERE cod_cargo = ultimo_contrato_pensionista_funcao.cod_cargo) as cod_funcao
                                                        , REPLACE(vw_orgao_nivel.orgao,'.','') as cod_lotacao

                                                     FROM pessoal".$this->getDado('entidade').".contrato_pensionista

                                               INNER JOIN pessoal".$this->getDado('entidade').".pensionista
                                                       ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                                                      AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente

                                               INNER JOIN pessoal".$this->getDado('entidade').".contrato
                                                       ON contrato.cod_contrato = contrato_pensionista.cod_contrato

                                               INNER JOIN pessoal".$this->getDado('entidade').".contrato_servidor
                                                       ON contrato_servidor.cod_contrato = contrato.cod_contrato

                                               INNER JOIN pessoal".$this->getDado('entidade').".servidor_contrato_servidor
                                                       ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato

                                               INNER JOIN pessoal".$this->getDado('entidade').".servidor
                                                       ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

                                               INNER JOIN sw_cgm
                                                       ON sw_cgm.numcgm = pensionista.numcgm

                                               INNER JOIN sw_cgm_pessoa_fisica
                                                       ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                                               INNER JOIN ultimo_contrato_pensionista_orgao('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_pensionista_orgao
                                                       ON contrato_pensionista.cod_contrato = ultimo_contrato_pensionista_orgao.cod_contrato

                                               INNER JOIN organograma.vw_orgao_nivel
                                                       ON ultimo_contrato_pensionista_orgao.cod_orgao = vw_orgao_nivel.cod_orgao

                                               INNER JOIN ultimo_contrato_servidor_nomeacao_posse('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_pensionista_nomeacao_posse
                                                       ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_nomeacao_posse.cod_contrato

                                               INNER JOIN ultimo_contrato_servidor_funcao('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_pensionista_funcao
                                                       ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_funcao.cod_contrato

                                               INNER JOIN ultimo_contrato_servidor_regime_funcao('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_pensionista_regime_funcao
                                                       ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_regime_funcao.cod_contrato

                                               INNER JOIN ultimo_contrato_servidor_padrao('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_pensionista_padrao
                                                       ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_padrao.cod_contrato

                                               INNER JOIN ultimo_contrato_servidor_salario('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_pensionista_salario
                                                       ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_salario.cod_contrato

                                               INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_pensionista_sub_divisao_funcao
                                                       ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_sub_divisao_funcao.cod_contrato

                                               INNER JOIN tceal.de_para_tipo_cargo
                                                       ON de_para_tipo_cargo.cod_sub_divisao = ultimo_contrato_pensionista_sub_divisao_funcao.cod_sub_divisao_funcao
                                                      AND de_para_tipo_cargo.cod_entidade = ". $this->getDado('cod_entidade')."

                                               INNER JOIN ultimo_contrato_pensionista_previdencia('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_pensionista_previdencia
                                                       ON ultimo_contrato_pensionista_previdencia.cod_contrato = contrato_servidor.cod_contrato

                                               INNER JOIN folhapagamento".$this->getDado('entidade').".previdencia
                                                       ON previdencia.cod_previdencia  = ultimo_contrato_pensionista_previdencia.cod_previdencia

                                               INNER JOIN ultimo_contrato_servidor_cargo_cbo('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_servidor_cargo_cbo
                                                       ON ultimo_contrato_servidor_cargo_cbo.cod_cargo = contrato_servidor.cod_cargo

                                                LEFT JOIN pessoal".$this->getDado('entidade').".contrato_servidor_especialidade_cargo as contrato_pensionista_especialidade_cargo
                                                       ON pensionista.cod_contrato_cedente = contrato_pensionista_especialidade_cargo.cod_contrato

                                                LEFT JOIN pessoal".$this->getDado('entidade').".especialidade
                                                       ON especialidade.cod_especialidade = contrato_pensionista_especialidade_cargo.cod_especialidade

                                                LEFT JOIN ultimo_contrato_servidor_local('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_pensionista_local
                                                       ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_local.cod_contrato

                                                LEFT JOIN organograma.local
                                                       ON local.cod_local = ultimo_contrato_pensionista_local.cod_local

                                                LEFT JOIN ultimo_contrato_servidor_especialidade_funcao('".$this->getDado('entidade')."', '".$this->getDado('cod_periodo_movimentacao')."') as ultimo_contrato_pensionista_especialidade_funcao
                                                       ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_especialidade_funcao.cod_contrato
                                                            
                                                LEFT JOIN 
                                                        (     SELECT registro_evento_periodo.cod_contrato
                                                                   , SUM(evento_calculado.valor) AS valor_consignada
                                                                FROM folhapagamento".$this->getDado('entidade').".evento
                                                                JOIN 
                                                                   ( SELECT unnest(string_to_array(valor, ',', ''))::integer AS valor FROM administracao.configuracao
                                                                      WHERE configuracao.cod_modulo = 62
                                                                        AND configuracao.parametro  = 'tceal_config_margem_consignada".$this->getDado('entidade')."'
                                                                        AND configuracao.exercicio  = '".$this->getDado('exercicio')."'
                                                                   ) AS config_consignada
                                                                  ON config_consignada.valor= evento.cod_evento
                                                                  
                                                                JOIN folhapagamento".$this->getDado('entidade').".registro_evento
                                                                  ON registro_evento.cod_evento=evento.cod_evento
                                                        
                                                                JOIN folhapagamento".$this->getDado('entidade').".registro_evento_periodo
                                                                  ON registro_evento_periodo.cod_periodo_movimentacao = ".$this->getDado('cod_periodo_movimentacao')."
                                                                 AND registro_evento_periodo.cod_registro=registro_evento.cod_registro
                                                        
                                                                JOIN folhapagamento".$this->getDado('entidade').".evento_calculado
                                                                  ON evento_calculado.cod_evento=registro_evento.cod_evento
                                                                 AND evento_calculado.cod_registro=registro_evento.cod_registro
                                                            
                                                            GROUP BY registro_evento_periodo.cod_contrato
                                                        ) AS evento_final
                                                       ON evento_final.cod_contrato=contrato_servidor.cod_contrato

                                                    WHERE contrato_pensionista.dt_encerramento::date IS NULL
                                                       OR contrato_pensionista.dt_encerramento::date >= (select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('cod_periodo_movimentacao').")::date)

                                                -- Fim consulta pensionista
                                               ) as servidor_pensionista
                                         WHERE contrato.cod_contrato = servidor_pensionista.cod_contrato
                                   ) as servidores
                             WHERE ( substring(servidores.dt_admissao from 7 for 4)||substring(servidores.dt_admissao from 4 for 2)||substring(servidores.dt_admissao from 1 for 2))::integer < to_char((select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('cod_periodo_movimentacao').")::date), 'yyyymmdd' )::integer
                          
                          GROUP BY cod_contrato
                                 , cpf
                                 , nome
                                 , data_nascimento
                                 , nome_mae
                                 , nome_pai
                                 , pis_pasep
                                 , titulo_eleitoral
                                 , cod_vinculo_empregaticio
                                 , cod_regime_previdenciario
                                 , cod_escolaridade
                                 , sob_cessao
                                 , cnpj_entidade
                                 , nome_entidade
                                 , data_cessao
                                 , data_retorno_cessao
                                 , dt_admissao
                                 , salario_bruto
                                 , salario_liquido
                                 , margem_consignada
                                 , cbo
                                 , cod_cargo
                                 , cod_funcao
                                 , cod_lotacao
                                 , matricula
                          
                          ORDER BY nome
                                 , matricula
                          ) AS servidores
         ";
         
         return $stSql;
     }
}
?>
